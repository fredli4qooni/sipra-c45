<?php

namespace App\Http\Controllers;

use App\Models\C45Model;
use App\Models\C45Rule;
use App\Models\DataAkademik;
use App\Services\C45EngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class C45Controller extends Controller
{
    /**
     * Display a listing of trained C4.5 models
     */
    public function index()
    {
        $models = C45Model::with(['creator', 'rules'])->latest()->paginate(10);
        $activeModel = C45Model::active()->first();
        $totalDataset = DataAkademik::count();

        return view('c45.index', compact('models', 'activeModel', 'totalDataset'));
    }

    /**
     * Show form to train a new C4.5 model
     */
    public function create()
    {
        $totalDataset = DataAkademik::count();
        $availableFeatures = [
            'kategori_ipk' => 'Kategori IPK (Rendah / Cukup / Tinggi)',
            'kategori_ips' => 'Kategori IPS (Rendah / Cukup / Tinggi)',
            'kategori_sks' => 'Kategori SKS Diambil (Kurang / Cukup / Sangat Baik)',
            'kategori_kehadiran' => 'Kategori Kehadiran (Kurang / Cukup / Baik)',
            'status_cuti' => 'Status Cuti (Ya / Tidak)',
            'sks_tidak_lulus' => 'SKS Tidak Lulus (Ada / Tidak Ada)',
        ];

        return view('c45.create', compact('totalDataset', 'availableFeatures'));
    }

    /**
     * Execute C4.5 Training process and store model
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_model' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'split_ratio' => ['required', 'in:80:20,70:30,90:10,100:0'],
            'features' => ['required', 'array', 'min:2'],
        ], [
            'nama_model.required' => 'Nama model wajib diisi.',
            'features.required' => 'Pilih minimal 2 fitur atribut untuk pohon keputusan.',
            'features.min' => 'Pilih minimal 2 fitur atribut.',
        ]);

        $totalDataset = DataAkademik::count();
        if ($totalDataset < 5) {
            return back()->withInput()->with('error', 'Dataset akademik terlalu sedikit (' . $totalDataset . ' baris). Tambahkan minimal 5 data untuk proses training.');
        }

        // Fetch raw academic dataset with student relations
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

        // Parse Split Ratio
        $ratioMap = [
            '80:20' => 0.8,
            '70:30' => 0.7,
            '90:10' => 0.9,
            '100:0' => 1.0,
        ];
        $trainRatio = $ratioMap[$validated['split_ratio']] ?? 0.8;

        DB::beginTransaction();

        try {
            $engine = new C45EngineService();
            $trainResult = $engine->train($records, $validated['features'], $trainRatio);

            // Deactivate other models if this is chosen to be active or first model
            $isFirstModel = (C45Model::count() === 0);
            $isActive = $request->boolean('is_active', $isFirstModel);

            if ($isActive) {
                C45Model::query()->update(['is_active' => false]);
            }

            // Create C45Model record
            $c45Model = C45Model::create([
                'nama_model' => $validated['nama_model'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'train_date' => now(),
                'split_ratio' => $validated['split_ratio'],
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
                'is_active' => $isActive,
                'created_by' => Auth::id(),
            ]);

            // Save extracted IF-THEN rules
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

            DB::commit();

            return redirect()->route('admin.c45.show', $c45Model)
                ->with('success', "Model '{$c45Model->nama_model}' berhasil dilatih! Akurasi: {$c45Model->accuracy}%, Total Aturan: " . count($trainResult['rules']));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal melatih model C4.5: ' . $e->getMessage());
        }
    }

    /**
     * Show detail of a trained C4.5 model with evaluation logs
     */
    public function show(C45Model $c45)
    {
        $c45->load(['rules', 'creator']);
        $treeArray = json_decode($c45->tree_structure_json, true) ?? [];
        $evaluation = $c45->confusion_matrix_data ?? [];
        $calculationLogs = $c45->entropy_gain_calculations ?? [];

        return view('c45.show', compact('c45', 'treeArray', 'evaluation', 'calculationLogs'));
    }

    /**
     * Activate a model for default prediction
     */
    public function activate(C45Model $c45)
    {
        C45Model::query()->update(['is_active' => false]);
        $c45->update(['is_active' => true]);

        return back()->with('success', "Model '{$c45->nama_model}' sekarang aktif digunakan untuk seluruh prediksi sistem.");
    }

    /**
     * Delete a trained model
     */
    public function destroy(C45Model $c45)
    {
        $name = $c45->nama_model;
        $c45->delete();

        return redirect()->route('admin.c45.index')
            ->with('success', "Model '{$name}' berhasil dihapus.");
    }
}
