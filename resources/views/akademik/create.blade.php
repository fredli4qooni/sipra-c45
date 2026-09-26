@extends('layouts.app')

@section('title', 'Tambah Data Nilai Akademik')
@section('subtitle', 'Entri rekam riwayat IPS, IPK, SKS, dan kehadiran mahasiswa per semester')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Formulir Rekam Akademik</h3>
                    <p class="text-xs text-slate-500">Pilih mahasiswa dan masukkan parameter performa studi</p>
                </div>
            </div>
            <a href="{{ route('admin.akademik.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('admin.akademik.store') }}" class="space-y-4">
            @csrf

            <!-- Pilih Mahasiswa -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Mahasiswa <span class="text-rose-500">*</span></label>
                <select name="mahasiswa_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">-- Pilih Mahasiswa --</option>
                    @foreach($mahasiswas as $mhs)
                        <option value="{{ $mhs->id }}" {{ (old('mahasiswa_id', request('mahasiswa_id')) == $mhs->id) ? 'selected' : '' }}>
                            {{ $mhs->nim }} - {{ $mhs->nama }} (Angkatan {{ $mhs->angkatan }})
                        </option>
                    @endforeach
                </select>
                @error('mahasiswa_id') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Semester -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Semester <span class="text-rose-500">*</span></label>
                    <select name="semester" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Tahun Akademik -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Akademik <span class="text-rose-500">*</span></label>
                    <input type="text" name="tahun_akademik" value="{{ old('tahun_akademik', '2023/2024 Genap') }}" required placeholder="contoh: 2023/2024 Ganjil" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- IPS -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Indeks Prestasi Semester (IPS) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="4.00" name="ips" value="{{ old('ips', '3.25') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- IPK -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Indeks Prestasi Kumulatif (IPK) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="4.00" name="ipk" value="{{ old('ipk', '3.30') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- SKS Semester -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">SKS Semester <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" max="30" name="sks_semester" value="{{ old('sks_semester', 20) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- SKS Total -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">SKS Kumulatif</label>
                    <input type="number" min="0" max="160" name="sks_total" value="{{ old('sks_total', 80) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- SKS Tidak Lulus -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">SKS Gagal (D/E)</label>
                    <input type="number" min="0" max="60" name="sks_tidak_lulus" value="{{ old('sks_tidak_lulus', 0) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kehadiran -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Persentase Kehadiran (%) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" min="0" max="100.0" name="persentase_kehadiran" value="{{ old('persentase_kehadiran', 88.0) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- Status Cuti -->
                <div class="flex items-center pt-6">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="status_cuti" value="1" {{ old('status_cuti') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-xs font-medium text-slate-700">Mahasiswa Sedang Mengambil Cuti Akademik</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ route('admin.akademik.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xs transition flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-1.5"></i>
                    Simpan Rekam Nilai
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
