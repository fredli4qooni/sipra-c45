<?php

namespace App\Http\Controllers;

use App\Models\DataAkademik;
use App\Models\Mahasiswa;
use App\Models\C45Model;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EwsController extends Controller
{
    /**
     * Early Warning System (EWS) Alert Center
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $baseQuery = DataAkademik::with(['mahasiswa.dosenPa', 'dosenPa'])
            ->where(function ($q) {
                $q->where('label_risiko_aktual', 'Risiko Tinggi')
                  ->orWhere('ipk', '<', 2.75)
                  ->orWhere('persentase_kehadiran', '<', 75.0)
                  ->orWhere('sks_tidak_lulus', '>=', 6)
                  ->orWhere('status_cuti', true);
            });

        // Calculate Advisee vs All count for Dosen PA
        $myBimbinganCount = (clone $baseQuery)->whereHas('mahasiswa', function ($q) use ($user) {
            $q->where('dosen_pa_id', $user->id);
        })->count();

        $allAlertCount = (clone $baseQuery)->count();

        // Scope handling: default to 'bimbingan_saya' for Dosen PA, 'semua' for others
        $scope = $request->input('scope', $user->isDosenPa() ? 'bimbingan_saya' : 'semua');

        $query = clone $baseQuery;

        if ($user->isDosenPa() && $scope === 'bimbingan_saya') {
            $query->whereHas('mahasiswa', function ($q) use ($user) {
                $q->where('dosen_pa_id', $user->id);
            });
        }

        // Filter by specific Dosen PA (Admin / Prodi filter)
        if ($dosenPaId = $request->input('dosen_pa_id')) {
            if ($dosenPaId === 'unassigned') {
                $query->whereHas('mahasiswa', fn($q) => $q->whereNull('dosen_pa_id'));
            } else {
                $query->whereHas('mahasiswa', fn($q) => $q->where('dosen_pa_id', $dosenPaId));
            }
        }

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

        // Metrics breakdown calculated from scoped query or base query
        $metricsBase = ($user->isDosenPa() && $scope === 'bimbingan_saya')
            ? (clone $baseQuery)->whereHas('mahasiswa', fn($q) => $q->where('dosen_pa_id', $user->id))
            : (clone $baseQuery);

        $criticalCount = (clone $metricsBase)->where('label_risiko_aktual', 'Risiko Tinggi')->count();
        $attendanceRiskCount = (clone $metricsBase)->where('persentase_kehadiran', '<', 75.0)->count();
        $gpaRiskCount = (clone $metricsBase)->where('ipk', '<', 2.75)->count();
        $cutiCount = (clone $metricsBase)->where('status_cuti', true)->count();

        // Intervention status counts
        $pendingInterventionCount = (clone $metricsBase)->where(function($q) {
            $q->whereNull('status_intervensi')
              ->orWhere('status_intervensi', 'Belum Ditindaklanjuti');
        })->count();

        $completedInterventionCount = (clone $metricsBase)->where('status_intervensi', 'Selesai / Teratasi')->count();

        $activeModel = C45Model::active()->first();
        $dosenPas = User::where('role', 'dosen_pa')->orderBy('name')->get();

        return view('ews.index', compact(
            'alertList',
            'criticalCount',
            'attendanceRiskCount',
            'gpaRiskCount',
            'cutiCount',
            'pendingInterventionCount',
            'completedInterventionCount',
            'activeModel',
            'scope',
            'myBimbinganCount',
            'allAlertCount',
            'dosenPas'
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
