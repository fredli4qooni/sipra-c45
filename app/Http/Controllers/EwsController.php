<?php

namespace App\Http\Controllers;

use App\Models\DataAkademik;
use App\Models\Mahasiswa;
use App\Models\C45Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EwsController extends Controller
{
    /**
     * Early Warning System (EWS) Alert Center
     */
    public function index(Request $request)
    {
        $baseQuery = DataAkademik::with(['mahasiswa', 'dosenPa'])
            ->where(function ($q) {
                $q->where('label_risiko_aktual', 'Risiko Tinggi')
                  ->orWhere('ipk', '<', 2.75)
                  ->orWhere('persentase_kehadiran', '<', 75.0)
                  ->orWhere('sks_tidak_lulus', '>=', 6)
                  ->orWhere('status_cuti', true);
            });

        $query = clone $baseQuery;

        if ($semester = $request->input('semester')) {
            $query->where('semester', $semester);
        }

        if ($statusIntervensi = $request->input('status_intervensi')) {
            $query->where('status_intervensi', $statusIntervensi);
        }

        if ($angkatan = $request->input('angkatan')) {
            $query->whereHas('mahasiswa', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            });
        }

        $alertList = $query->orderBy('ipk', 'asc')->paginate(12)->withQueryString();

        // High priority counts
        $criticalCount = (clone $baseQuery)->where('label_risiko_aktual', 'Risiko Tinggi')->count();
        $attendanceRiskCount = (clone $baseQuery)->where('persentase_kehadiran', '<', 75.0)->count();
        $gpaRiskCount = (clone $baseQuery)->where('ipk', '<', 2.75)->count();
        $cutiCount = (clone $baseQuery)->where('status_cuti', true)->count();

        // Intervention status counts
        $pendingInterventionCount = (clone $baseQuery)->where(function($q) {
            $q->whereNull('status_intervensi')
              ->orWhere('status_intervensi', 'Belum Ditindaklanjuti');
        })->count();

        $completedInterventionCount = (clone $baseQuery)->where('status_intervensi', 'Selesai / Teratasi')->count();

        $activeModel = C45Model::active()->first();

        return view('ews.index', compact(
            'alertList',
            'criticalCount',
            'attendanceRiskCount',
            'gpaRiskCount',
            'cutiCount',
            'pendingInterventionCount',
            'completedInterventionCount',
            'activeModel'
        ));
    }

    /**
     * Update counseling / academic intervention status by Dosen PA or Admin
     */
    public function updateIntervention(Request $request, DataAkademik $dataAkademik)
    {
        $validated = $request->validate([
            'status_intervensi' => ['required', 'string', 'in:Belum Ditindaklanjuti,Dijadwalkan Bimbingan,Sedang Bimbingan,Selesai / Teratasi'],
            'tindakan_intervensi' => ['nullable', 'string', 'max:150'],
            'catatan_intervensi' => ['nullable', 'string', 'max:1000'],
            'tanggal_intervensi' => ['nullable', 'date'],
        ]);

        $dataAkademik->update([
            'status_intervensi' => $validated['status_intervensi'],
            'tindakan_intervensi' => $validated['tindakan_intervensi'] ?? $dataAkademik->tindakan_intervensi,
            'catatan_intervensi' => $validated['catatan_intervensi'] ?? $dataAkademik->catatan_intervensi,
            'tanggal_intervensi' => $validated['tanggal_intervensi'] ?? now(),
            'dosen_pa_id' => Auth::id() ?? $dataAkademik->dosen_pa_id,
        ]);

        return redirect()->back()->with('success', "Status intervensi & bimbingan akademik untuk {$dataAkademik->mahasiswa->nama} (NIM: {$dataAkademik->mahasiswa->nim}) berhasil diperbarui!");
    }
}
