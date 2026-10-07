<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriRisiko;

class KategoriRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $presets = KategoriRisiko::getDefaultPresets();

        foreach ($presets as $kode => $data) {
            KategoriRisiko::updateOrCreate(
                ['kode' => $kode],
                $data
            );
        }
    }
}
