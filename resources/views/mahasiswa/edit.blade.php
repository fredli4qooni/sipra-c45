@extends('layouts.app')

@section('title', 'Ubah Data Mahasiswa: ' . $mahasiswa->nama)
@section('subtitle', 'Perbarui informasi data pokok mahasiswa')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i data-lucide="user-pen" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Edit Data Mahasiswa</h3>
                    <p class="text-xs text-slate-500 font-mono">NIM: {{ $mahasiswa->nim }}</p>
                </div>
            </div>
            <a href="{{ route('admin.mahasiswa.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('admin.mahasiswa.update', $mahasiswa) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NIM -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">NIM Mahasiswa <span class="text-rose-500">*</span></label>
                    <input type="text" name="nim" value="{{ old('nim', $mahasiswa->nim) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('nim') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nama -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('nama') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Angkatan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Angkatan <span class="text-rose-500">*</span></label>
                    <input type="number" name="angkatan" value="{{ old('angkatan', $mahasiswa->angkatan) }}" required min="2015" max="2030" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="jenis_kelamin" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        <option value="L" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $mahasiswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Jalur Masuk -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jalur Masuk</label>
                    <select name="jalur_masuk" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        <option value="SPAN-PTKIN" {{ old('jalur_masuk', $mahasiswa->jalur_masuk) == 'SPAN-PTKIN' ? 'selected' : '' }}>SPAN-PTKIN</option>
                        <option value="UM-PTKIN" {{ old('jalur_masuk', $mahasiswa->jalur_masuk) == 'UM-PTKIN' ? 'selected' : '' }}>UM-PTKIN</option>
                        <option value="SNBP" {{ old('jalur_masuk', $mahasiswa->jalur_masuk) == 'SNBP' ? 'selected' : '' }}>SNBP</option>
                        <option value="SNBT" {{ old('jalur_masuk', $mahasiswa->jalur_masuk) == 'SNBT' ? 'selected' : '' }}>SNBT</option>
                        <option value="Mandiri" {{ old('jalur_masuk', $mahasiswa->jalur_masuk) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Status Mahasiswa -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Mahasiswa <span class="text-rose-500">*</span></label>
                    <select name="status_mahasiswa" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        <option value="Aktif" {{ old('status_mahasiswa', $mahasiswa->status_mahasiswa) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Cuti" {{ old('status_mahasiswa', $mahasiswa->status_mahasiswa) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="Lulus" {{ old('status_mahasiswa', $mahasiswa->status_mahasiswa) == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="Drop Out" {{ old('status_mahasiswa', $mahasiswa->status_mahasiswa) == 'Drop Out' ? 'selected' : '' }}>Drop Out</option>
                    </select>
                </div>

                <!-- Email Pribadi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Mahasiswa</label>
                    <input type="email" name="email" value="{{ old('email', $mahasiswa->email) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ route('admin.mahasiswa.index') }}" class="px-4 py-2.5 rounded-lg bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 shadow-xs transition flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-1.5"></i>
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
