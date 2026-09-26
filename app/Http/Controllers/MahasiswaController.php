<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['latestAkademik', 'dosenPa']);

        // Search by NIM or Name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter by Angkatan
        if ($angkatan = $request->input('angkatan')) {
            $query->where('angkatan', $angkatan);
        }

        // Filter by Status
        if ($status = $request->input('status_mahasiswa')) {
            $query->where('status_mahasiswa', $status);
        }

        // Filter by Dosen PA
        if ($dosenPaId = $request->input('dosen_pa_id')) {
            if ($dosenPaId === 'unassigned') {
                $query->whereNull('dosen_pa_id');
            } else {
                $query->where('dosen_pa_id', $dosenPaId);
            }
        }

        $mahasiswas = $query->orderBy('nim', 'asc')->paginate(10)->withQueryString();
        $angkatans = Mahasiswa::select('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');
        $dosenPas = User::where('role', 'dosen_pa')->orderBy('name')->get();

        return view('mahasiswa.index', compact('mahasiswas', 'angkatans', 'dosenPas'));
    }

    /**
     * Show form for creating a new student
     */
    public function create()
    {
        $dosenPas = User::where('role', 'dosen_pa')->orderBy('name')->get();
        return view('mahasiswa.create', compact('dosenPas'));
    }

    /**
     * Store a newly created student in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:30', 'unique:mahasiswas,nim'],
            'nama' => ['required', 'string', 'max:150'],
            'dosen_pa_id' => ['nullable', 'exists:users,id'],
            'angkatan' => ['required', 'integer', 'min:2018', 'max:' . (date('Y') + 1)],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'jalur_masuk' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string'],
            'tinggal_dengan' => ['nullable', 'string', 'max:50'],
            'status_mahasiswa' => ['required', 'in:Aktif,Cuti,Lulus,Drop Out,Non-Aktif'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar dalam sistem.',
            'nama.required' => 'Nama mahasiswa wajib diisi.',
            'angkatan.required' => 'Tahun angkatan wajib dipilih.',
            'dosen_pa_id.exists' => 'Dosen PA yang dipilih tidak valid.',
        ]);

        Mahasiswa::create($validated);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Data mahasiswa ' . $validated['nama'] . ' berhasil ditambahkan.');
    }

    /**
     * Display student detail
     */
    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['dataAkademiks', 'prediksis.model', 'latestPrediksi', 'dosenPa']);
        return view('mahasiswa.show', compact('mahasiswa'));
    }

    /**
     * Show form for editing student
     */
    public function edit(Mahasiswa $mahasiswa)
    {
        $dosenPas = User::where('role', 'dosen_pa')->orderBy('name')->get();
        return view('mahasiswa.edit', compact('mahasiswa', 'dosenPas'));
    }

    /**
     * Update student record
     */
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:30', 'unique:mahasiswas,nim,' . $mahasiswa->id],
            'nama' => ['required', 'string', 'max:150'],
            'dosen_pa_id' => ['nullable', 'exists:users,id'],
            'angkatan' => ['required', 'integer', 'min:2018', 'max:' . (date('Y') + 1)],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'jalur_masuk' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string'],
            'tinggal_dengan' => ['nullable', 'string', 'max:50'],
            'status_mahasiswa' => ['required', 'in:Aktif,Cuti,Lulus,Drop Out,Non-Aktif'],
        ]);

        $mahasiswa->update($validated);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Data mahasiswa ' . $mahasiswa->nama . ' berhasil diperbarui.');
    }

    /**
     * Remove student record
     */
    public function destroy(Mahasiswa $mahasiswa)
    {
        $nama = $mahasiswa->nama;
        $mahasiswa->delete();

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Data mahasiswa ' . $nama . ' berhasil dihapus.');
    }
}
