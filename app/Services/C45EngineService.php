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
     * Human readable Indonesian label for C4.5 attributes
     */
    public static function getAttributeLabel(string $attr): string
    {
        return match ($attr) {
            'kategori_ipk' => 'Indeks Prestasi Kumulatif (IPK)',
            'kategori_ips' => 'Indeks Prestasi Semester (IPS)',
            'kategori_sks' => 'Beban SKS Semester',
            'kategori_kehadiran' => 'Tingkat Kehadiran Kuliah',
            'status_cuti' => 'Status Cuti Akademik',
            'sks_tidak_lulus' => 'Mata Kuliah Mengulang (SKS Tidak Lulus)',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tinggal_dengan' => 'Tempat Tinggal Mahasiswa',
            default => ucwords(str_replace('_', ' ', $attr)),
        };
    }

    /**
     * Trace step-by-step decision traversal path for Explainable AI (XAI)
     */
    public static function traceDecisionPath(array $tree, array $inputParams): array
    {
        if (empty($tree)) {
            return [
                'has_trace' => false,
                'steps' => [],
                'terminal' => null,
                'rule_text' => null,
                'narratives' => [],
                'summary_explanation' => 'Struktur pohon keputusan tidak tersedia.',
            ];
        }

        // Ensure all categorical values exist
        $categories = [
            'kategori_ipk' => $inputParams['kategori_ipk'] ?? (isset($inputParams['ipk']) ? DataPreprocessingService::categorizeIpk((float)$inputParams['ipk']) : null),
            'kategori_ips' => $inputParams['kategori_ips'] ?? (isset($inputParams['ips']) ? DataPreprocessingService::categorizeIps((float)$inputParams['ips']) : null),
            'kategori_sks' => $inputParams['kategori_sks'] ?? (isset($inputParams['sks_semester']) ? DataPreprocessingService::categorizeSks((int)$inputParams['sks_semester']) : null),
            'kategori_kehadiran' => $inputParams['kategori_kehadiran'] ?? (isset($inputParams['persentase_kehadiran']) ? DataPreprocessingService::categorizeKehadiran((float)$inputParams['persentase_kehadiran']) : null),
            'status_cuti' => isset($inputParams['status_cuti']) 
                ? (($inputParams['status_cuti'] === true || $inputParams['status_cuti'] === 1 || $inputParams['status_cuti'] === 'Ya' || $inputParams['status_cuti'] === 'true') ? 'Ya' : 'Tidak') 
                : 'Tidak',
            'sks_tidak_lulus' => isset($inputParams['sks_tidak_lulus']) 
                ? (((int)$inputParams['sks_tidak_lulus'] > 0 || $inputParams['sks_tidak_lulus'] === 'Ada') ? 'Ada' : 'Tidak Ada') 
                : 'Tidak Ada',
            'jenis_kelamin' => $inputParams['jenis_kelamin'] ?? null,
            'tinggal_dengan' => $inputParams['tinggal_dengan'] ?? null,
        ];

        $steps = [];
        $currentNode = $tree;
        $depth = 1;
        $conditionsText = [];
        $narratives = [];

        while ($currentNode && ($currentNode['type'] ?? '') !== 'leaf') {
            $attr = $currentNode['attribute'] ?? null;
            if (!$attr) break;

            $chosenVal = $categories[$attr] ?? null;
            $rawDisplay = match($attr) {
                'kategori_ipk' => isset($inputParams['ipk']) ? "IPK " . number_format((float)$inputParams['ipk'], 2) : "Kategori " . $chosenVal,
                'kategori_ips' => isset($inputParams['ips']) ? "IPS " . number_format((float)$inputParams['ips'], 2) : "Kategori " . $chosenVal,
                'kategori_sks' => isset($inputParams['sks_semester']) ? $inputParams['sks_semester'] . " SKS" : "Kategori " . $chosenVal,
                'kategori_kehadiran' => isset($inputParams['persentase_kehadiran']) ? number_format((float)$inputParams['persentase_kehadiran'], 1) . "% Kehadiran" : "Kategori " . $chosenVal,
                'status_cuti' => ($chosenVal === 'Ya') ? 'Mahasiswa Mengajukan Cuti' : 'Mahasiswa Aktif Perkuliahan',
                'sks_tidak_lulus' => isset($inputParams['sks_tidak_lulus']) && is_numeric($inputParams['sks_tidak_lulus']) ? $inputParams['sks_tidak_lulus'] . " SKS Mengulang" : "Status " . $chosenVal,
                default => (string) $chosenVal,
            };

            $branches = $currentNode['branches'] ?? [];
            $branchKeys = array_keys($branches);
            $isFallback = false;
            $nextNode = null;

            if ($chosenVal !== null && isset($branches[$chosenVal])) {
                $nextNode = $branches[$chosenVal];
            } else {
                // Unknown branch fallback: branch with highest samples
                $maxSamples = -1;
                foreach ($branches as $bKey => $bNode) {
                    if (($bNode['samples_count'] ?? 0) > $maxSamples) {
                        $maxSamples = $bNode['samples_count'] ?? 0;
                        $nextNode = $bNode;
                        $chosenVal = (string) $bKey;
                        $isFallback = true;
                    }
                }
            }

            $attrLabel = self::getAttributeLabel($attr);
            $conditionsText[] = "{$attrLabel} = '{$chosenVal}'";
            $narratives[] = "Simpul #" . $depth . " (" . ($depth === 1 ? 'Root Node' : 'Internal Node') . "): Algoritma memeriksa atribut <strong>{$attrLabel}</strong>. Nilai mahasiswa tergolong <strong>{$chosenVal}</strong> ({$rawDisplay}), mengarahkan alur ke cabang <em>{$chosenVal}</em>.";

            $steps[] = [
                'step' => $depth,
                'depth' => $depth,
                'type' => 'node',
                'attribute' => $attr,
                'attribute_label' => $attrLabel,
                'category_value' => $chosenVal,
                'raw_value' => $rawDisplay,
                'available_branches' => $branchKeys,
                'is_fallback' => $isFallback,
                'gain_ratio' => $currentNode['gain_ratio'] ?? null,
                'samples_count' => $currentNode['samples_count'] ?? null,
                'distribution' => $currentNode['distribution'] ?? [],
            ];

            $currentNode = $nextNode;
            $depth++;

            if ($depth > 15) break; // safety guard
        }

        $terminal = null;
        if ($currentNode && ($currentNode['type'] ?? '') === 'leaf') {
            $terminal = [
                'step' => $depth,
                'depth' => $depth,
                'type' => 'leaf',
                'decision' => $currentNode['decision'] ?? 'Risiko Sedang',
                'confidence' => $currentNode['confidence'] ?? 0.0,
                'samples_count' => $currentNode['samples_count'] ?? 0,
                'distribution' => $currentNode['distribution'] ?? [],
            ];
            $narratives[] = "Simpul Daun (Terminal Leaf): Alur berakhir pada daun keputusan <strong>{$terminal['decision']}</strong> dengan tingkat keyakinan (confidence) <strong>{$terminal['confidence']}%</strong>, didukung oleh <strong>{$terminal['samples_count']} data sampel latih</strong>.";
        }

        $ruleText = !empty($conditionsText)
            ? "IF " . implode(" AND ", $conditionsText) . " THEN Keputusan = " . ($terminal['decision'] ?? 'Risiko Sedang')
            : null;

        return [
            'has_trace' => !empty($steps) && $terminal !== null,
            'steps' => $steps,
            'terminal' => $terminal,
            'rule_text' => $ruleText,
            'narratives' => $narratives,
        ];
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
     * Deterministic Array Shuffle using PHP 8.2+ Randomizer with Mt19937 engine
     *
     * Guarantees 100% reproducible ordering without polluting global RNG state.
     *
     * @param array $items
     * @param int $seed
     * @return array
     */
    public static function deterministicShuffle(array $items, int $seed): array
    {
        if (count($items) <= 1) {
            return array_values($items);
        }

        if (class_exists(\Random\Randomizer::class) && class_exists(\Random\Engine\Mt19937::class)) {
            $randomizer = new \Random\Randomizer(new \Random\Engine\Mt19937($seed));
            return $randomizer->shuffleArray(array_values($items));
        }

        // Fallback Fisher-Yates with 32-bit LCG
        $items = array_values($items);
        $s = $seed;
        $count = count($items);
        for ($i = $count - 1; $i > 0; $i--) {
            $s = (1664525 * $s + 1013904223) & 0xFFFFFFFF;
            $j = (int) floor((($s >> 1) / 0x7FFFFFFF) * ($i + 1));
            $temp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $temp;
        }

        return $items;
    }

    /**
     * Stratified Train-Test Dataset Splitting with Seeded Determinism
     *
     * Preserves class distribution proportions across train and test sets,
     * preventing skewed evaluation metrics or class starvation.
     *
     * @param array $dataset
     * @param float $trainRatio (e.g. 0.8 for 80:20)
     * @param string $targetAttr
     * @param int $seed
     * @return array{train: array, test: array}
     */
    public static function stratifiedSplit(array $dataset, float $trainRatio = 0.8, string $targetAttr = 'target', int $seed = 42): array
    {
        $total = count($dataset);
        if ($total === 0) {
            return ['train' => [], 'test' => []];
        }

        if ($trainRatio >= 1.0) {
            return [
                'train' => array_values($dataset),
                'test' => [],
            ];
        }

        if ($trainRatio <= 0.0) {
            return [
                'train' => [],
                'test' => array_values($dataset),
            ];
        }

        // Group dataset by target class attribute
        $grouped = [];
        foreach ($dataset as $row) {
            $class = (string) ($row[$targetAttr] ?? 'Unknown');
            $grouped[$class][] = $row;
        }

        $trainSet = [];
        $testSet = [];

        // Sort keys to maintain strict cross-platform ordering before processing
        ksort($grouped);

        foreach ($grouped as $class => $items) {
            $classCount = count($items);
            
            // Deterministically shuffle samples of this class using seed mixed with class identifier
            $classSeed = abs($seed + crc32($class));
            $shuffledItems = self::deterministicShuffle($items, $classSeed);

            // Calculate train count for this class
            $trainCount = (int) round($classCount * $trainRatio);

            // If class has at least 2 samples, ensure at least 1 sample goes to train and 1 to test
            if ($classCount >= 2 && $trainRatio > 0.0 && $trainRatio < 1.0) {
                if ($trainCount === 0) {
                    $trainCount = 1;
                } elseif ($trainCount === $classCount) {
                    $trainCount = $classCount - 1;
                }
            }

            $trainPart = array_slice($shuffledItems, 0, $trainCount);
            $testPart = array_slice($shuffledItems, $trainCount);

            $trainSet = array_merge($trainSet, $trainPart);
            $testSet = array_merge($testSet, $testPart);
        }

        // Deterministically shuffle the combined train and test sets
        $finalTrainSet = self::deterministicShuffle($trainSet, $seed);
        $finalTestSet = self::deterministicShuffle($testSet, $seed + 1);

        return [
            'train' => $finalTrainSet,
            'test' => $finalTestSet,
        ];
    }

    /**
     * Train complete C4.5 Model with Data Splitting and Evaluation
     */
    public function train(array $rawDataset, array $features = [], float $trainRatio = 0.8, string $targetAttr = 'target', int $seed = 42): array
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

        // 2. Deterministic Stratified Train-Test Splitting
        $splitResult = self::stratifiedSplit($processedData, $trainRatio, $targetAttr, $seed);
        $trainSet = $splitResult['train'];
        $testSet = $splitResult['test'];

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
        $evaluation['random_seed'] = $seed;
        $evaluation['split_strategy'] = 'Stratified (Seeded)';

        return [
            'tree' => $treeStructure,
            'rules' => $rules,
            'calculation_logs' => $this->calculationLogs,
            'evaluation' => $evaluation,
            'total_training' => count($trainSet),
            'total_testing' => count($testSet),
            'features_used' => $features,
            'random_seed' => $seed,
            'split_strategy' => 'Stratified (Seeded)',
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
