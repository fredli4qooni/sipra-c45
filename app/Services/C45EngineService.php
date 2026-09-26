<?php

namespace App\Services;

class C45EngineService
{
    protected array $calculationLogs = [];
    protected int $ruleCounter = 1;
    protected array $extractedRules = [];

    /**
     * Compute Entropy of a dataset S for a given target attribute
     * Formula: Entropy(S) = sum( -p_i * log2(p_i) )
     */
    public static function calculateEntropy(array $samples, string $targetAttr = 'target'): float
    {
        $total = count($samples);
        if ($total === 0) {
            return 0.0;
        }

        // Count class distribution
        $classCounts = [];
        foreach ($samples as $sample) {
            $val = $sample[$targetAttr] ?? 'Unknown';
            $classCounts[$val] = ($classCounts[$val] ?? 0) + 1;
        }

        $entropy = 0.0;
        foreach ($classCounts as $class => $count) {
            if ($count > 0) {
                $p = $count / $total;
                $entropy -= $p * (log($p) / log(2)); // log2(p)
            }
        }

        return round($entropy, 4);
    }

    /**
     * Compute Information Gain and Gain Ratio for an attribute
     * Gain(S, A) = Entropy(S) - sum( (|S_v| / |S|) * Entropy(S_v) )
     * SplitInfo(S, A) = -sum( (|S_v| / |S|) * log2(|S_v| / |S|) )
     * GainRatio(S, A) = Gain(S, A) / SplitInfo(S, A)
     */
    public static function calculateGainAndRatio(array $samples, string $attribute, string $targetAttr = 'target'): array
    {
        $total = count($samples);
        if ($total === 0) {
            return ['gain' => 0.0, 'split_info' => 0.0, 'gain_ratio' => 0.0, 'subsets' => []];
        }

        $entropyS = self::calculateEntropy($samples, $targetAttr);

        // Partition samples by attribute values
        $subsets = [];
        foreach ($samples as $sample) {
            $attrVal = $sample[$attribute] ?? 'Undefined';
            $subsets[$attrVal][] = $sample;
        }

        $entropyWeightedSum = 0.0;
        $splitInfo = 0.0;
        $subsetStats = [];

        foreach ($subsets as $val => $subSamples) {
            $subCount = count($subSamples);
            $subRatio = $subCount / $total;
            $subEntropy = self::calculateEntropy($subSamples, $targetAttr);

            $entropyWeightedSum += $subRatio * $subEntropy;

            if ($subRatio > 0) {
                $splitInfo -= $subRatio * (log($subRatio) / log(2));
            }

            // Count distribution in this subset
            $classDist = [];
            foreach ($subSamples as $row) {
                $c = $row[$targetAttr] ?? 'Unknown';
                $classDist[$c] = ($classDist[$c] ?? 0) + 1;
            }

            $subsetStats[$val] = [
                'count' => $subCount,
                'entropy' => round($subEntropy, 4),
                'class_distribution' => $classDist,
            ];
        }

        $gain = round($entropyS - $entropyWeightedSum, 4);
        $splitInfo = round($splitInfo, 4);

        $gainRatio = ($splitInfo > 0.00001) ? round($gain / $splitInfo, 4) : 0.0;

        return [
            'entropy_s' => $entropyS,
            'gain' => max(0.0, $gain),
            'split_info' => $splitInfo,
            'gain_ratio' => max(0.0, $gainRatio),
            'subsets' => $subsetStats,
        ];
    }

    /**
     * Build Decision Tree recursively using C4.5 algorithm
     */
    public function buildTree(array $samples, array $attributes, string $targetAttr = 'target', int $depth = 1, string $nodePath = 'Root'): array
    {
        $total = count($samples);

        // 1. Base Case: Empty samples
        if ($total === 0) {
            return [
                'type' => 'leaf',
                'decision' => 'Risiko Sedang',
                'samples_count' => 0,
                'confidence' => 0.0,
                'distribution' => [],
            ];
        }

        // Get class distribution
        $classCounts = [];
        foreach ($samples as $sample) {
            $c = $sample[$targetAttr] ?? 'Unknown';
            $classCounts[$c] = ($classCounts[$c] ?? 0) + 1;
        }

        $majorityClass = array_keys($classCounts, max($classCounts))[0];
        $majorityCount = $classCounts[$majorityClass];
        $confidence = round(($majorityCount / $total) * 100, 2);

        // 2. Base Case: All samples belong to the same class (pure node)
        if (count($classCounts) === 1) {
            return [
                'type' => 'leaf',
                'decision' => $majorityClass,
                'samples_count' => $total,
                'confidence' => 100.0,
                'distribution' => $classCounts,
            ];
        }

        // 3. Base Case: No remaining attributes to split on or max depth reached
        if (empty($attributes) || $depth > 8) {
            return [
                'type' => 'leaf',
                'decision' => $majorityClass,
                'samples_count' => $total,
                'confidence' => $confidence,
                'distribution' => $classCounts,
            ];
        }

        // 4. Calculate Gain Ratio for all candidate attributes
        $bestAttribute = null;
        $bestGainRatio = -1.0;
        $attributeCalculations = [];

        foreach ($attributes as $attr) {
            $calc = self::calculateGainAndRatio($samples, $attr, $targetAttr);
            $attributeCalculations[$attr] = $calc;

            if ($calc['gain_ratio'] > $bestGainRatio) {
                $bestGainRatio = $calc['gain_ratio'];
                $bestAttribute = $attr;
            }
        }

        // Log calculation step for transparency / thesis analysis
        $this->calculationLogs[] = [
            'node_path' => $nodePath,
            'depth' => $depth,
            'samples_count' => $total,
            'entropy_parent' => self::calculateEntropy($samples, $targetAttr),
            'class_distribution' => $classCounts,
            'calculations' => $attributeCalculations,
            'best_attribute' => $bestAttribute,
            'best_gain_ratio' => $bestGainRatio,
        ];

        // 5. If best gain is zero or no gain can be achieved, return leaf
        if ($bestGainRatio <= 0.0 || $bestAttribute === null) {
            return [
                'type' => 'leaf',
                'decision' => $majorityClass,
                'samples_count' => $total,
                'confidence' => $confidence,
                'distribution' => $classCounts,
            ];
        }

        // 6. Branch on the best attribute values
        $remainingAttributes = array_values(array_diff($attributes, [$bestAttribute]));
        $branches = [];

        // Partition by best attribute values
        $partitions = [];
        foreach ($samples as $sample) {
            $val = $sample[$bestAttribute] ?? 'Undefined';
            $partitions[$val][] = $sample;
        }

        foreach ($partitions as $attrVal => $subSamples) {
            $childPath = ($nodePath === 'Root') ? "{$bestAttribute} = {$attrVal}" : "{$nodePath} AND {$bestAttribute} = {$attrVal}";
            $branches[$attrVal] = $this->buildTree($subSamples, $remainingAttributes, $targetAttr, $depth + 1, $childPath);
        }

        return [
            'type' => 'node',
            'attribute' => $bestAttribute,
            'gain_ratio' => $bestGainRatio,
            'samples_count' => $total,
            'distribution' => $classCounts,
            'branches' => $branches,
        ];
    }

    /**
     * Extract IF-THEN Classification Rules from Decision Tree
     */
    public function extractRules(array $treeNode, array $currentConditions = []): array
    {
        if ($treeNode['type'] === 'leaf') {
            $ruleCode = 'R' . $this->ruleCounter++;
            
            // Build readable IF-THEN string
            $conditionsStr = [];
            foreach ($currentConditions as $cond) {
                $conditionsStr[] = "{$cond['attribute']} = {$cond['value']}";
            }
            $conditionText = empty($conditionsStr) ? "TRUE" : implode(" AND ", $conditionsStr);
            $ruleText = "IF {$conditionText} THEN Risiko = {$treeNode['decision']}";

            $this->extractedRules[] = [
                'rule_code' => $ruleCode,
                'rule_text' => $ruleText,
                'conditions_json' => $currentConditions,
                'decision' => $treeNode['decision'],
                'confidence' => $treeNode['confidence'],
                'support_samples' => $treeNode['samples_count'],
            ];

            return $this->extractedRules;
        }

        $attribute = $treeNode['attribute'];
        foreach ($treeNode['branches'] as $value => $childNode) {
            $newConditions = $currentConditions;
            $newConditions[] = [
                'attribute' => $attribute,
                'operator' => '=',
                'value' => (string) $value,
            ];
            $this->extractRules($childNode, $newConditions);
        }

        return $this->extractedRules;
    }

    /**
     * Classify an input instance using Decision Tree
     */
    public static function predictWithTree(array $tree, array $instance): array
    {
        if ($tree['type'] === 'leaf') {
            return [
                'decision' => $tree['decision'],
                'confidence' => $tree['confidence'],
                'samples_count' => $tree['samples_count'],
            ];
        }

        $attr = $tree['attribute'];
        $val = $instance[$attr] ?? null;

        if ($val !== null && isset($tree['branches'][$val])) {
            return self::predictWithTree($tree['branches'][$val], $instance);
        }

        // If unknown branch, find branch with highest samples
        $largestBranch = null;
        $maxSamples = -1;
        foreach ($tree['branches'] as $branch) {
            if (($branch['samples_count'] ?? 0) > $maxSamples) {
                $maxSamples = $branch['samples_count'] ?? 0;
                $largestBranch = $branch;
            }
        }

        if ($largestBranch !== null) {
            return self::predictWithTree($largestBranch, $instance);
        }

        return [
            'decision' => 'Risiko Sedang',
            'confidence' => 50.0,
            'samples_count' => 0,
        ];
    }

    /**
     * Train complete C4.5 Model with Data Splitting and Evaluation
     */
    public function train(array $rawDataset, array $features = [], float $trainRatio = 0.8, string $targetAttr = 'target'): array
    {
        // 1. Preprocess Dataset
        $processedData = DataPreprocessingService::preprocessDataset($rawDataset);

        if (empty($features)) {
            $features = [
                'kategori_ipk',
                'kategori_ips',
                'kategori_sks',
                'kategori_kehadiran',
                'status_cuti',
                'sks_tidak_lulus',
            ];
        }

        // 2. Stratified / Random Train-Test Splitting
        shuffle($processedData);
        $totalSamples = count($processedData);
        $trainCount = (int) round($totalSamples * $trainRatio);
        
        $trainSet = array_slice($processedData, 0, $trainCount);
        $testSet = array_slice($processedData, $trainCount);

        // 3. Reset internal logs & state
        $this->calculationLogs = [];
        $this->ruleCounter = 1;
        $this->extractedRules = [];

        // 4. Build Tree
        $treeStructure = $this->buildTree($trainSet, $features, $targetAttr);

        // 5. Extract Rules
        $rules = $this->extractRules($treeStructure);

        // 6. Evaluate Model on Test Set (or Train Set if test is empty)
        $evalSet = !empty($testSet) ? $testSet : $trainSet;
        $evaluation = $this->evaluateModel($treeStructure, $evalSet, $targetAttr);

        return [
            'tree' => $treeStructure,
            'rules' => $rules,
            'calculation_logs' => $this->calculationLogs,
            'evaluation' => $evaluation,
            'total_training' => count($trainSet),
            'total_testing' => count($testSet),
            'features_used' => $features,
        ];
    }

    /**
     * Evaluate Model and calculate Confusion Matrix + Metrics
     */
    public function evaluateModel(array $tree, array $testSet, string $targetAttr = 'target'): array
    {
        $classes = ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'];
        
        // Initialize Confusion Matrix: matrix[actual][predicted]
        $matrix = [];
        foreach ($classes as $actual) {
            foreach ($classes as $pred) {
                $matrix[$actual][$pred] = 0;
            }
        }

        $correct = 0;
        $total = count($testSet);

        foreach ($testSet as $row) {
            $actual = $row[$targetAttr] ?? 'Risiko Sedang';
            $predictionResult = self::predictWithTree($tree, $row);
            $pred = $predictionResult['decision'] ?? 'Risiko Sedang';

            if (!in_array($actual, $classes)) {
                $actual = 'Risiko Sedang';
            }
            if (!in_array($pred, $classes)) {
                $pred = 'Risiko Sedang';
            }

            $matrix[$actual][$pred]++;

            if ($actual === $pred) {
                $correct++;
            }
        }

        $overallAccuracy = ($total > 0) ? round(($correct / $total) * 100, 2) : 0.0;

        // Calculate per-class Precision, Recall, Specificity, F1
        $classMetrics = [];
        $precisionSum = 0.0;
        $recallSum = 0.0;
        $specificitySum = 0.0;

        foreach ($classes as $c) {
            $tp = $matrix[$c][$c] ?? 0;
            $fp = 0;
            $fn = 0;
            $tn = 0;

            foreach ($classes as $otherActual) {
                foreach ($classes as $otherPred) {
                    if ($otherActual !== $c && $otherPred === $c) {
                        $fp += $matrix[$otherActual][$otherPred];
                    } elseif ($otherActual === $c && $otherPred !== $c) {
                        $fn += $matrix[$otherActual][$otherPred];
                    } elseif ($otherActual !== $c && $otherPred !== $c) {
                        $tn += $matrix[$otherActual][$otherPred];
                    }
                }
            }

            $p = ($tp + $fp > 0) ? round(($tp / ($tp + $fp)) * 100, 2) : 100.0;
            $r = ($tp + $fn > 0) ? round(($tp / ($tp + $fn)) * 100, 2) : 100.0;
            $s = ($tn + $fp > 0) ? round(($tn / ($tn + $fp)) * 100, 2) : 100.0;
            $f1 = ($p + $r > 0) ? round((2 * $p * $r) / ($p + $r), 2) : 0.0;

            $classMetrics[$c] = [
                'tp' => $tp,
                'fp' => $fp,
                'fn' => $fn,
                'tn' => $tn,
                'precision' => $p,
                'recall' => $r,
                'specificity' => $s,
                'f1_score' => $f1,
            ];

            $precisionSum += $p;
            $recallSum += $r;
            $specificitySum += $s;
        }

        $macroPrecision = round($precisionSum / count($classes), 2);
        $macroRecall = round($recallSum / count($classes), 2);
        $macroSpecificity = round($specificitySum / count($classes), 2);
        $macroF1 = ($macroPrecision + $macroRecall > 0) 
            ? round((2 * $macroPrecision * $macroRecall) / ($macroPrecision + $macroRecall), 2) 
            : 0.0;

        return [
            'accuracy' => $overallAccuracy,
            'precision' => $macroPrecision,
            'recall' => $macroRecall,
            'specificity' => $macroSpecificity,
            'f1_score' => $macroF1,
            'confusion_matrix' => $matrix,
            'per_class_metrics' => $classMetrics,
            'total_tested' => $total,
            'correct_count' => $correct,
        ];
    }
}
