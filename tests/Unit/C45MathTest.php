<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\C45EngineService;

class C45MathTest extends TestCase
{
    public function test_entropy_on_pure_dataset(): void
    {
        // Dataset with only 1 class -> Entropy must be 0
        $pureData = [
            ['target' => 'Risiko Rendah'],
            ['target' => 'Risiko Rendah'],
            ['target' => 'Risiko Rendah'],
            ['target' => 'Risiko Rendah'],
        ];

        $entropy = C45EngineService::calculateEntropy($pureData, 'target');
        $this->assertEquals(0.0, $entropy);
    }

    public function test_entropy_on_50_50_dataset(): void
    {
        // 50:50 distribution between 2 classes -> Entropy must be 1.0000
        $equalData = [
            ['target' => 'Risiko Rendah'],
            ['target' => 'Risiko Rendah'],
            ['target' => 'Risiko Tinggi'],
            ['target' => 'Risiko Tinggi'],
        ];

        $entropy = C45EngineService::calculateEntropy($equalData, 'target');
        $this->assertEquals(1.0, $entropy);
    }

    public function test_gain_and_ratio_calculation(): void
    {
        $samples = [
            ['kategori_ipk' => 'Rendah', 'target' => 'Risiko Tinggi'],
            ['kategori_ipk' => 'Rendah', 'target' => 'Risiko Tinggi'],
            ['kategori_ipk' => 'Tinggi', 'target' => 'Risiko Rendah'],
            ['kategori_ipk' => 'Tinggi', 'target' => 'Risiko Rendah'],
        ];

        $res = C45EngineService::calculateGainAndRatio($samples, 'kategori_ipk', 'target');
        
        $this->assertEquals(1.0, $res['gain']);
        $this->assertGreaterThan(0.0, $res['gain_ratio']);
    }

    public function test_tree_construction_and_rule_extraction(): void
    {
        $engine = new C45EngineService();
        $dataset = [
            ['kategori_ipk' => 'Tinggi', 'kategori_kehadiran' => 'Baik', 'target' => 'Risiko Rendah'],
            ['kategori_ipk' => 'Tinggi', 'kategori_kehadiran' => 'Baik', 'target' => 'Risiko Rendah'],
            ['kategori_ipk' => 'Rendah', 'kategori_kehadiran' => 'Kurang', 'target' => 'Risiko Tinggi'],
            ['kategori_ipk' => 'Rendah', 'kategori_kehadiran' => 'Kurang', 'target' => 'Risiko Tinggi'],
            ['kategori_ipk' => 'Cukup', 'kategori_kehadiran' => 'Cukup', 'target' => 'Risiko Sedang'],
        ];

        $tree = $engine->buildTree($dataset, ['kategori_ipk', 'kategori_kehadiran'], 'target');
        $this->assertIsArray($tree);

        $rules = $engine->extractRules($tree);
        $this->assertNotEmpty($rules);
        $this->assertArrayHasKey('rule_text', $rules[0]);
    }

    public function test_deterministic_shuffle_reproducibility(): void
    {
        $input = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        
        $shuffle1 = C45EngineService::deterministicShuffle($input, 42);
        $shuffle2 = C45EngineService::deterministicShuffle($input, 42);
        $shuffleDifferentSeed = C45EngineService::deterministicShuffle($input, 99);

        // Same seed must produce identical permutation
        $this->assertSame($shuffle1, $shuffle2);

        // Different seed must produce different permutation
        $this->assertNotSame($shuffle1, $shuffleDifferentSeed);

        // All elements must still be present
        sort($shuffle1);
        $this->assertSame($input, $shuffle1);
    }

    public function test_stratified_split_preserves_class_proportions(): void
    {
        $dataset = [];
        for ($i = 0; $i < 40; $i++) {
            $dataset[] = ['id' => $i, 'target' => 'Risiko Rendah'];
        }
        for ($i = 40; $i < 60; $i++) {
            $dataset[] = ['id' => $i, 'target' => 'Risiko Sedang'];
        }
        for ($i = 60; $i < 70; $i++) {
            $dataset[] = ['id' => $i, 'target' => 'Risiko Tinggi'];
        }

        // 80% train / 20% test
        $split = C45EngineService::stratifiedSplit($dataset, 0.8, 'target', 42);

        $trainCounts = array_count_values(array_column($split['train'], 'target'));
        $testCounts = array_count_values(array_column($split['test'], 'target'));

        // 40 * 0.8 = 32 train, 8 test
        $this->assertEquals(32, $trainCounts['Risiko Rendah']);
        $this->assertEquals(8, $testCounts['Risiko Rendah']);

        // 20 * 0.8 = 16 train, 4 test
        $this->assertEquals(16, $trainCounts['Risiko Sedang']);
        $this->assertEquals(4, $testCounts['Risiko Sedang']);

        // 10 * 0.8 = 8 train, 2 test
        $this->assertEquals(8, $trainCounts['Risiko Tinggi']);
        $this->assertEquals(2, $testCounts['Risiko Tinggi']);

        // Same seed produces identical splits
        $split2 = C45EngineService::stratifiedSplit($dataset, 0.8, 'target', 42);
        $this->assertSame($split, $split2);
    }

    public function test_train_reproducibility_with_identical_seed(): void
    {
        $engine = new C45EngineService();

        $dataset = [
            ['ipk' => 3.8, 'kategori_ipk' => 'Tinggi', 'kategori_kehadiran' => 'Baik', 'status_cuti' => false, 'label_risiko_aktual' => 'Risiko Rendah'],
            ['ipk' => 3.6, 'kategori_ipk' => 'Tinggi', 'kategori_kehadiran' => 'Baik', 'status_cuti' => false, 'label_risiko_aktual' => 'Risiko Rendah'],
            ['ipk' => 3.5, 'kategori_ipk' => 'Tinggi', 'kategori_kehadiran' => 'Cukup', 'status_cuti' => false, 'label_risiko_aktual' => 'Risiko Rendah'],
            ['ipk' => 3.1, 'kategori_ipk' => 'Cukup', 'kategori_kehadiran' => 'Baik', 'status_cuti' => false, 'label_risiko_aktual' => 'Risiko Rendah'],
            ['ipk' => 2.8, 'kategori_ipk' => 'Cukup', 'kategori_kehadiran' => 'Cukup', 'status_cuti' => false, 'label_risiko_aktual' => 'Risiko Sedang'],
            ['ipk' => 2.5, 'kategori_ipk' => 'Cukup', 'kategori_kehadiran' => 'Kurang', 'status_cuti' => false, 'label_risiko_aktual' => 'Risiko Sedang'],
            ['ipk' => 2.3, 'kategori_ipk' => 'Rendah', 'kategori_kehadiran' => 'Cukup', 'status_cuti' => false, 'label_risiko_aktual' => 'Risiko Sedang'],
            ['ipk' => 1.8, 'kategori_ipk' => 'Rendah', 'kategori_kehadiran' => 'Kurang', 'status_cuti' => true, 'label_risiko_aktual' => 'Risiko Tinggi'],
            ['ipk' => 1.5, 'kategori_ipk' => 'Rendah', 'kategori_kehadiran' => 'Kurang', 'status_cuti' => true, 'label_risiko_aktual' => 'Risiko Tinggi'],
            ['ipk' => 1.2, 'kategori_ipk' => 'Rendah', 'kategori_kehadiran' => 'Kurang', 'status_cuti' => false, 'label_risiko_aktual' => 'Risiko Tinggi'],
        ];

        $features = ['kategori_ipk', 'kategori_kehadiran', 'status_cuti'];

        $res1 = $engine->train($dataset, $features, 0.8, 'label_risiko_aktual', 42);
        $res2 = $engine->train($dataset, $features, 0.8, 'label_risiko_aktual', 42);

        // Accuracy and metrics must be identical
        $this->assertEquals($res1['evaluation']['accuracy'], $res2['evaluation']['accuracy']);
        $this->assertEquals($res1['evaluation']['precision'], $res2['evaluation']['precision']);
        $this->assertEquals($res1['evaluation']['recall'], $res2['evaluation']['recall']);

        // Tree structure and rules must be identical
        $this->assertSame(json_encode($res1['tree']), json_encode($res2['tree']));
        $this->assertSame(count($res1['rules']), count($res2['rules']));
        $this->assertSame($res1['rules'][0]['rule_text'], $res2['rules'][0]['rule_text']);
        $this->assertEquals(42, $res1['random_seed']);
    }
}
