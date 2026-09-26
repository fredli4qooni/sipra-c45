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
}
