<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataAkademik;
use App\Models\User;
use App\Models\C45Model;
use App\Models\C45Rule;
use App\Services\C45EngineService;

class C45ModelSeeder extends Seeder
{
    /**
     * Run database seeds to create default trained C4.5 model
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $records = DataAkademik::with('mahasiswa')->get()->map(function ($row) {
            return [
                'id' => $row->id,
                'nim' => $row->mahasiswa->nim ?? '',
                'nama' => $row->mahasiswa->nama ?? '',
                'semester' => $row->semester,
                'ipk' => (float) $row->ipk,
                'ips' => (float) $row->ips,
                'sks_semester' => (int) $row->sks_semester,
                'sks_total' => (int) $row->sks_total,
                'sks_tidak_lulus' => (int) $row->sks_tidak_lulus,
                'persentase_kehadiran' => (float) $row->persentase_kehadiran,
                'status_cuti' => (bool) $row->status_cuti,
                'label_risiko_aktual' => $row->label_risiko_aktual,
            ];
        })->toArray();

        if (count($records) >= 5) {
            $engine = new C45EngineService();
            $features = [
                'kategori_ipk',
                'kategori_ips',
                'kategori_sks',
                'kategori_kehadiran',
                'status_cuti',
                'sks_tidak_lulus',
            ];

            $trainResult = $engine->train($records, $features, 0.8);

            $c45Model = C45Model::updateOrCreate(
                ['nama_model' => 'Model Baseline C4.5 Skripsi (UIN RIL)'],
                [
                    'deskripsi' => 'Model pohon keputusan awal berbasis dataset akademik 2021/2022.',
                    'train_date' => now(),
                    'split_ratio' => '80:20',
                    'total_training_samples' => $trainResult['total_training'],
                    'total_testing_samples' => $trainResult['total_testing'],
                    'target_attribute' => 'label_risiko_aktual',
                    'features_used' => $trainResult['features_used'],
                    'accuracy' => $trainResult['evaluation']['accuracy'],
                    'precision' => $trainResult['evaluation']['precision'],
                    'recall' => $trainResult['evaluation']['recall'],
                    'specificity' => $trainResult['evaluation']['specificity'],
                    'f1_score' => $trainResult['evaluation']['f1_score'],
                    'confusion_matrix_data' => $trainResult['evaluation'],
                    'entropy_gain_calculations' => $trainResult['calculation_logs'],
                    'tree_structure_json' => json_encode($trainResult['tree']),
                    'is_active' => true,
                    'created_by' => $admin?->id,
                ]
            );

            // Delete old rules for this model and recreate
            C45Rule::where('model_id', $c45Model->id)->delete();
            foreach ($trainResult['rules'] as $ruleData) {
                C45Rule::create([
                    'model_id' => $c45Model->id,
                    'rule_code' => $ruleData['rule_code'],
                    'rule_text' => $ruleData['rule_text'],
                    'conditions_json' => $ruleData['conditions_json'],
                    'decision' => $ruleData['decision'],
                    'confidence' => $ruleData['confidence'],
                    'support_samples' => $ruleData['support_samples'],
                ]);
            }
        }
    }
}
