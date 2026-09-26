<?php

namespace App\Http\Controllers;

use App\Models\DataAkademik;
use App\Models\Mahasiswa;
use App\Models\C45Model;
use App\Services\SpreadsheetService;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display report filters and preview table
     */
    public function index(Request $request)
    {
        $query = DataAkademik::with('mahasiswa');

        // Filter by Angkatan
        if ($angkatan = $request->input('angkatan')) {
            $query->whereHas('mahasiswa', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            });
        }

        // Filter by Semester
        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }

        // Filter by Status Risiko
        if ($risiko = $request->input('label_risiko_aktual')) {
            $query->where('label_risiko_aktual', $risiko);
        }

        // Filter by Jalur Masuk
        if ($jalur = $request->input('jalur_masuk')) {
            $query->whereHas('mahasiswa', function ($q) use ($jalur) {
                $q->where('jalur_masuk', $jalur);
            });
        }

        $records = $query->orderBy('semester', 'asc')->orderBy('mahasiswa_id', 'asc')->paginate(15)->withQueryString();

        // Statistical summary for the filtered report
        $totalRecords = $query->count();
        $totalRendah = (clone $query)->where('label_risiko_aktual', 'Risiko Rendah')->count();
        $totalSedang = (clone $query)->where('label_risiko_aktual', 'Risiko Sedang')->count();
        $totalTinggi = (clone $query)->where('label_risiko_aktual', 'Risiko Tinggi')->count();

        $angkatans = Mahasiswa::select('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');
        $semesters = DataAkademik::select('semester')->distinct()->orderBy('semester')->pluck('semester');
        $activeModel = C45Model::active()->first();

        return view('laporan.index', compact(
            'records',
            'angkatans',
            'semesters',
            'totalRecords',
            'totalRendah',
            'totalSedang',
            'totalTinggi',
            'activeModel'
        ));
    }

    /**
     * Official Printable Report with Kop Surat UIN Raden Intan Lampung
     */
    public function print(Request $request)
    {
        $query = DataAkademik::with('mahasiswa');

        if ($angkatan = $request->input('angkatan')) {
            $query->whereHas('mahasiswa', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            });
        }

        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }

        if ($risiko = $request->input('label_risiko_aktual')) {
            $query->where('label_risiko_aktual', $risiko);
        }

        $records = $query->orderBy('semester', 'asc')->orderBy('mahasiswa_id', 'asc')->get();

        $totalRecords = $records->count();
        $totalRendah = $records->where('label_risiko_aktual', 'Risiko Rendah')->count();
        $totalSedang = $records->where('label_risiko_aktual', 'Risiko Sedang')->count();
        $totalTinggi = $records->where('label_risiko_aktual', 'Risiko Tinggi')->count();

        $activeModel = C45Model::active()->first();

        return view('laporan.print', compact(
            'records',
            'totalRecords',
            'totalRendah',
            'totalSedang',
            'totalTinggi',
            'activeModel'
        ));
    }

    /**
     * Export filtered report into Excel
     */
    public function exportExcel(Request $request)
    {
        $query = DataAkademik::with('mahasiswa');

        if ($angkatan = $request->input('angkatan')) {
            $query->whereHas('mahasiswa', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            });
        }

        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }

        if ($risiko = $request->input('label_risiko_aktual')) {
            $query->where('label_risiko_aktual', $risiko);
        }

        return SpreadsheetService::exportData($query);
    }
}
