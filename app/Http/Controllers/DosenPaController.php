<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DosenPaController extends Controller
{
    /**
     * Display a listing of Dosen Pembimbing Akademik (PA)
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'dosen_pa')
            ->withCount('mahasiswaBimbingan')
            ->withCount(['mahasiswaBimbingan as mahasiswa_risiko_tinggi_count' => function ($q) {
                $q->whereHas('latestAkademik', function ($sq) {
                    $sq->where('label_risiko_aktual', 'Risiko Tinggi');
                });
            }]);

        // Search by name, NIP/NIDN, or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim_nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $dosenPas = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        // Summary metrics
        $totalDosen = User::where('role', 'dosen_pa')->count();
        $totalAktif = User::where('role', 'dosen_pa')->where('status', 'active')->count();
        $totalBimbingan = Mahasiswa::whereNotNull('dosen_pa_id')->count();

        return view('dosen.index', compact('dosenPas', 'totalDosen', 'totalAktif', 'totalBimbingan'));
    }

    /**
     * Show the form for creating a new Dosen PA
     */
    public function create()
    {
        return view('dosen.create');
    }

    /**
     * Store a newly created Dosen PA in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'nim_nip' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:25'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'Nama lengkap beserta gelar dosen wajib diisi.',
            'email.required' => 'Email dosen wajib diisi.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'password.required' => 'Kata sandi awal wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'nim_nip' => $validated['nim_nip'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => 'dosen_pa',
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.dosen.index')
            ->with('success', "Akun Dosen PA {$validated['name']} berhasil ditambahkan.");
    }

    /**
     * Display the specified Dosen PA and list of supervised students
     */
    public function show(User $dosen)
    {
        if (!$dosen->isDosenPa()) {
            abort(404, 'Pengguna bukan merupakan Dosen PA.');
        }

        $mahasiswaBimbingan = $dosen->mahasiswaBimbingan()
            ->with('latestAkademik')
            ->orderBy('nim', 'asc')
            ->paginate(15);

        $totalAdvisees = $dosen->mahasiswaBimbingan()->count();
        $highRiskAdvisees = $dosen->mahasiswaBimbingan()
            ->whereHas('latestAkademik', fn($q) => $q->where('label_risiko_aktual', 'Risiko Tinggi'))
            ->count();

        return view('dosen.show', compact('dosen', 'mahasiswaBimbingan', 'totalAdvisees', 'highRiskAdvisees'));
    }

    /**
     * Show the form for editing the specified Dosen PA
     */
    public function edit(User $dosen)
    {
        if (!$dosen->isDosenPa()) {
            abort(404, 'Pengguna bukan merupakan Dosen PA.');
        }

        return view('dosen.edit', compact('dosen'));
    }

    /**
     * Update the specified Dosen PA in storage
     */
    public function update(Request $request, User $dosen)
    {
        if (!$dosen->isDosenPa()) {
            abort(404, 'Pengguna bukan merupakan Dosen PA.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'nim_nip' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($dosen->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:25'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'Nama lengkap dosen wajib diisi.',
            'email.required' => 'Email dosen wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'nim_nip' => $validated['nim_nip'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $dosen->update($updateData);

        return redirect()->route('admin.dosen.index')
            ->with('success', "Data Dosen PA {$dosen->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified Dosen PA from storage
     */
    public function destroy(User $dosen)
    {
        if (!$dosen->isDosenPa()) {
            abort(404, 'Pengguna bukan merupakan Dosen PA.');
        }

        $name = $dosen->name;
        $adviseeCount = $dosen->mahasiswaBimbingan()->count();

        // Safely detach advisees so their student records remain intact
        Mahasiswa::where('dosen_pa_id', $dosen->id)->update(['dosen_pa_id' => null]);

        $dosen->delete();

        $msg = "Akun Dosen PA {$name} berhasil dihapus.";
        if ($adviseeCount > 0) {
            $msg .= " Sebanyak {$adviseeCount} mahasiswa bimbingan telah dilepaskan dan dapat ditugaskan ke Dosen PA lain.";
        }

        return redirect()->route('admin.dosen.index')->with('success', $msg);
    }
}
