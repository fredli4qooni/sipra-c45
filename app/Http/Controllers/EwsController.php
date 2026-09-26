<?php

namespace App\Http\Controllers;

use App\Models\DataAkademik;
use App\Models\Mahasiswa;
use App\Models\C45Model;
use Illuminate\Http\Request;

class EwsController extends Controller
{
    /**
     * Early Warning System (EWS) Alert Center
     */
    public function index(Request $request)
    {
        $query = DataAkademik::with('mahasiswa')
            ->where(function ($q) {
                $q->where('label_risiko_aktual', 'Risiko Tinggi')
                  ->orWhere('ipk', '<', 2.75)
                  ->orWhere('persentase_kehadiran', '<', 75.0)
                  ->orWhere('sks_tidak_lulus', '>=', 6)
                  ->orWhere('status_cuti', true);
            });

        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }

        if ($angkatan = $request->input('angkatan')) {
            $query->whereHas('mahasiswa', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            });
        }

        $alertList = $query->orderBy('ipk', 'asc')->paginate(12)->withQueryString();

        // High priority counts
        $criticalCount = DataAkademik::where('label_risiko_aktual', 'Risiko Tinggi')->count();
        $attendanceRiskCount = DataAkademik::where('persentase_kehadiran', '<', 75.0)->count();
        $gpaRiskCount = DataAkademik::where('ipk', '<', 2.75)->count();
        $cutiCount = DataAkademik::where('status_cuti', true)->count();

        $activeModel = C45Model::active()->first();

        return view('ews.index', compact(
            'alertList',
            'criticalCount',
            'attendanceRiskCount',
            'gpaRiskCount',
            'cutiCount',
            'activeModel'
        ));
    }
}
