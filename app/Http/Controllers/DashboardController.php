<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use App\Models\DataAkademik;
use App\Models\C45Model;
use App\Models\C45Rule;
use App\Models\Prediksi;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Entry router gateway: redirects to specific dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isProdi() || $user->isDosenPa()) {
            return redirect()->route('prodi.dashboard');
        } elseif ($user->isMahasiswa()) {
            return redirect()->route('mahasiswa.dashboard');
        }

        abort(403, 'Peran pengguna tidak valid.');
    }

    /**
     * Dashboard view for Admin Akademik
     */
    public function adminDashboard()
    {
        $totalMahasiswa = Mahasiswa::count();
        $totalAktif = Mahasiswa::where('status_mahasiswa', 'Aktif')->count();
        $totalDataAkademik = DataAkademik::count();
        $totalModelC45 = C45Model::count();
        $activeModel = C45Model::active()->first();
        $totalPrediksi = Prediksi::count();
        $totalUsers = User::count();

        // Count risk statistics from latest predictions or academic records
        $totalRisikoRendah = DataAkademik::where('label_risiko_aktual', 'Risiko Rendah')->count();
        $totalRisikoSedang = DataAkademik::where('label_risiko_aktual', 'Risiko Sedang')->count();
        $totalRisikoTinggi = DataAkademik::where('label_risiko_aktual', 'Risiko Tinggi')->count();

        $recentMahasiswas = Mahasiswa::with('latestAkademik')->latest()->take(5)->get();
        $recentPrediksis = Prediksi::with('mahasiswa')->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalMahasiswa',
            'totalAktif',
            'totalDataAkademik',
            'totalModelC45',
            'activeModel',
            'totalPrediksi',
            'totalUsers',
            'totalRisikoRendah',
            'totalRisikoSedang',
            'totalRisikoTinggi',
            'recentMahasiswas',
            'recentPrediksis'
        ));
    }

    /**
     * Dashboard view for Pihak Prodi / Dosen PA
     */
    public function prodiDashboard()
    {
        $user = Auth::user();
        $totalMahasiswa = Mahasiswa::count();
        $activeModel = C45Model::active()->first();
        $totalPrediksi = Prediksi::count();

        // Risk Breakdown
        $totalRisikoRendah = DataAkademik::where('label_risiko_aktual', 'Risiko Rendah')->count();
        $totalRisikoSedang = DataAkademik::where('label_risiko_aktual', 'Risiko Sedang')->count();
        $totalRisikoTinggi = DataAkademik::where('label_risiko_aktual', 'Risiko Tinggi')->count();

        // Advisee specific metrics for Dosen PA
        $myBimbinganTotal = 0;
        $myBimbinganHighRisk = 0;
        $myBimbinganPendingIntervention = 0;

        if ($user->isDosenPa()) {
            $myBimbinganTotal = Mahasiswa::where('dosen_pa_id', $user->id)->count();
            $myBimbinganHighRisk = DataAkademik::whereHas('mahasiswa', fn($q) => $q->where('dosen_pa_id', $user->id))
                ->where('label_risiko_aktual', 'Risiko Tinggi')
                ->count();
            $myBimbinganPendingIntervention = DataAkademik::whereHas('mahasiswa', fn($q) => $q->where('dosen_pa_id', $user->id))
                ->where(function($q) {
                    $q->where('label_risiko_aktual', 'Risiko Tinggi')
                      ->orWhere('ipk', '<', 2.75)
                      ->orWhere('persentase_kehadiran', '<', 75.0);
                })
                ->where(function($q) {
                    $q->whereNull('status_intervensi')
                      ->orWhere('status_intervensi', 'Belum Ditindaklanjuti');
                })->count();
        }

        // High-risk students requiring immediate attention
        $highRiskStudents = DataAkademik::with(['mahasiswa.dosenPa'])
            ->where('label_risiko_aktual', 'Risiko Tinggi')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.prodi', compact(
            'totalMahasiswa',
            'activeModel',
            'totalPrediksi',
            'totalRisikoRendah',
            'totalRisikoSedang',
            'totalRisikoTinggi',
            'highRiskStudents',
            'myBimbinganTotal',
            'myBimbinganHighRisk',
            'myBimbinganPendingIntervention'
        ));
    }

    /**
     * Dashboard view for Mahasiswa (Personalized Early Warning Portal)
     */
    public function mahasiswaDashboard()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['dataAkademiks', 'latestAkademik', 'latestPrediksi'])
            ->where('user_id', $user->id)
            ->orWhere('nim', $user->nim_nip)
            ->first();

        $latestAkademik = $mahasiswa?->latestAkademik;
        $latestPrediksi = $mahasiswa?->latestPrediksi;
        $riwayatAkademik = $mahasiswa?->dataAkademiks ?? collect();

        return view('dashboard.mahasiswa', compact(
            'mahasiswa',
            'latestAkademik',
            'latestPrediksi',
            'riwayatAkademik'
        ));
    }

    /**
     * Display Help Center & User Guide (Pusat Bantuan & FAQ)
     */
    public function bantuan() 
    {
        $activeModel = C45Model::active()->first();
        return view('bantuan.index', compact('activeModel'));
    }
}
