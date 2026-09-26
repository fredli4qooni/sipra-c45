<?php

namespace App\Http\Controllers;

use App\Models\DataAkademik;
use App\Models\Mahasiswa;
use App\Services\DataPreprocessingService;
use App\Services\SpreadsheetService;
use Illuminate\Http\Request;

class DataAkademikController extends Controller
{
    /**
     * Display a listing of academic records
     */
    public function index(Request $request)
    {
        $query = DataAkademik::with('mahasiswa');

        // Search by NIM or Nama
        if ($search = $request->input('search')) {
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter by Semester
        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }

        // Filter by Risiko
        if ($risiko = $request->input('label_risiko_aktual')) {
            $query->where('label_risiko_aktual', $risiko);
        }

        $akademiks = $query->orderBy('semester', 'asc')->orderBy('mahasiswa_id', 'asc')->paginate(10)->withQueryString();
        $semesters = DataAkademik::select('semester')->distinct()->orderBy('semester')->pluck('semester');

        return view('akademik.index', compact('akademiks', 'semesters'));
    }

    /**
     * Show form for creating a new academic record
     */
    public function create(Request $request)
    {
        $mahasiswas = Mahasiswa::orderBy('nim', 'asc')->get();
        $selectedMahasiswaId = $request->input('mahasiswa_id');

        return view('akademik.create', compact('mahasiswas', 'selectedMahasiswaId'));
    }

    /**
     * Store a newly created academic record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswas,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'tahun_akademik' => ['nullable', 'string', 'max:30'],
            'ips' => ['required', 'numeric', 'min:0', 'max:4.00'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4.00'],
            'sks_semester' => ['required', 'integer', 'min:0', 'max:30'],
            'sks_total' => ['required', 'integer', 'min:0', 'max:160'],
            'sks_tidak_lulus' => ['required', 'integer', 'min:0', 'max:60'],
            'persentase_kehadiran' => ['required', 'numeric', 'min:0', 'max:100.00'],
            'status_cuti' => ['nullable', 'boolean'],
            'label_risiko_aktual' => ['nullable', 'string', 'in:Risiko Rendah,Risiko Sedang,Risiko Tinggi'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'mahasiswa_id.required' => 'Pilih mahasiswa terlebih dahulu.',
            'semester.required' => 'Semester wajib diisi.',
            'ips.required' => 'Nilai IPS wajib diisi.',
            'ipk.required' => 'Nilai IPK wajib diisi.',
            'persentase_kehadiran.required' => 'Persentase kehadiran wajib diisi.',
        ]);

        $validated['status_cuti'] = $request->boolean('status_cuti');

        // Check if record for this student and semester already exists
        $exists = DataAkademik::where('mahasiswa_id', $validated['mahasiswa_id'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Data akademik untuk mahasiswa ini pada semester ' . $validated['semester'] . ' sudah ada.');
        }

        $akademik = new DataAkademik($validated);
        $akademik->calculateCategories();

        if (empty($akademik->label_risiko_aktual)) {
            $akademik->label_risiko_aktual = DataPreprocessingService::determineHeuristicRisk(
                $akademik->ipk,
                $akademik->persentase_kehadiran,
                $akademik->sks_tidak_lulus,
                $akademik->status_cuti
            );
        }

        $akademik->label_do_aktual = ($akademik->status_cuti || $akademik->ipk < 2.50) ? 'Berisiko' : 'Tidak Berisiko';
        $akademik->save();

        return redirect()->route('admin.akademik.index')
            ->with('success', 'Data akademik semester ' . $akademik->semester . ' berhasil disimpan.');
    }

    /**
     * Show form for editing an academic record
     */
    public function edit(DataAkademik $akademik)
    {
        $akademik->load('mahasiswa');
        return view('akademik.edit', compact('akademik'));
    }

    /**
     * Update academic record
     */
    public function update(Request $request, DataAkademik $akademik)
    {
        $validated = $request->validate([
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'tahun_akademik' => ['nullable', 'string', 'max:30'],
            'ips' => ['required', 'numeric', 'min:0', 'max:4.00'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4.00'],
            'sks_semester' => ['required', 'integer', 'min:0', 'max:30'],
            'sks_total' => ['required', 'integer', 'min:0', 'max:160'],
            'sks_tidak_lulus' => ['required', 'integer', 'min:0', 'max:60'],
            'persentase_kehadiran' => ['required', 'numeric', 'min:0', 'max:100.00'],
            'status_cuti' => ['nullable', 'boolean'],
            'label_risiko_aktual' => ['nullable', 'string', 'in:Risiko Rendah,Risiko Sedang,Risiko Tinggi'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $validated['status_cuti'] = $request->boolean('status_cuti');

        $akademik->fill($validated);
        $akademik->calculateCategories();
        $akademik->label_do_aktual = ($akademik->status_cuti || $akademik->ipk < 2.50) ? 'Berisiko' : 'Tidak Berisiko';
        $akademik->save();

        return redirect()->route('admin.akademik.index')
            ->with('success', 'Data akademik berhasil diperbarui.');
    }

    /**
     * Remove academic record
     */
    public function destroy(DataAkademik $akademik)
    {
        $akademik->delete();
        return redirect()->route('admin.akademik.index')
            ->with('success', 'Data akademik berhasil dihapus.');
    }

    /**
     * Download Excel import template
     */
    public function downloadTemplate()
    {
        return SpreadsheetService::downloadTemplate();
    }

    /**
     * Import Excel/CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [
            'file.required' => 'File Excel / CSV wajib diunggah.',
            'file.mimes' => 'Format file harus berupa .xlsx, .xls, atau .csv',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            $result = SpreadsheetService::importData($request->file('file'));

            return redirect()->route('admin.akademik.index')
                ->with('success', "Proses impor selesai! Total {$result['success_count']} data berhasil diproses (Dilewati: {$result['skipped_count']}).");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export academic records to Excel
     */
    public function export()
    {
        return SpreadsheetService::exportData();
    }
}
