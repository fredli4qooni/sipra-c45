<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\DataPreprocessingService;

class DataPreprocessingTest extends TestCase
{
    public function test_ipk_categorization(): void
    {
        $this->assertEquals('Rendah', DataPreprocessingService::categorizeIpk(2.40));
        $this->assertEquals('Rendah', DataPreprocessingService::categorizeIpk(2.74));
        $this->assertEquals('Cukup', DataPreprocessingService::categorizeIpk(2.75));
        $this->assertEquals('Cukup', DataPreprocessingService::categorizeIpk(3.25));
        $this->assertEquals('Tinggi', DataPreprocessingService::categorizeIpk(3.26));
        $this->assertEquals('Tinggi', DataPreprocessingService::categorizeIpk(4.00));
    }

    public function test_kehadiran_categorization(): void
    {
        $this->assertEquals('Kurang', DataPreprocessingService::categorizeKehadiran(65.0));
        $this->assertEquals('Kurang', DataPreprocessingService::categorizeKehadiran(74.9));
        $this->assertEquals('Cukup', DataPreprocessingService::categorizeKehadiran(75.0));
        $this->assertEquals('Cukup', DataPreprocessingService::categorizeKehadiran(85.0));
        $this->assertEquals('Baik', DataPreprocessingService::categorizeKehadiran(85.1));
        $this->assertEquals('Baik', DataPreprocessingService::categorizeKehadiran(100.0));
    }

    public function test_sks_categorization(): void
    {
        $this->assertEquals('Kurang', DataPreprocessingService::categorizeSks(16));
        $this->assertEquals('Cukup', DataPreprocessingService::categorizeSks(18));
        $this->assertEquals('Cukup', DataPreprocessingService::categorizeSks(21));
        $this->assertEquals('Sangat Baik', DataPreprocessingService::categorizeSks(24));
    }
}
