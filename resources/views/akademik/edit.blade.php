@extends('layouts.app')

@section('title', 'Ubah Data Nilai Akademik')
@section('subtitle', 'Perbarui riwayat parameter nilai mahasiswa semester ' . $akademik->semester)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i data-lucide="pencil" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Ubah Data Akademik</h3>
                    <p class="text-xs text-slate-500 font-mono">{{ $akademik->mahasiswa->nim ?? '' }} - {{ $akademik->mahasiswa->nama ?? '' }}</p>
                </div>
            </div>
            <a href="{{ route('admin.akademik.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('admin.akademik.update', $akademik) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Semester -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Semester <span class="text-rose-500">*</span></label>
                    <select name="semester" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('semester', $akademik->semester) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Tahun Akademik -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Akademik <span class="text-rose-500">*</span></label>
                    <input type="text" name="tahun_akademik" value="{{ old('tahun_akademik', $akademik->tahun_akademik) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- IPS -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">IPS Semester Ini <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="4.00" name="ips" value="{{ old('ips', $akademik->ips) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- IPK -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">IPK Kumulatif <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="4.00" name="ipk" value="{{ old('ipk', $akademik->ipk) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- SKS Semester -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">SKS Semester <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" max="30" name="sks_semester" value="{{ old('sks_semester', $akademik->sks_semester) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- SKS Total -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">SKS Total</label>
                    <input type="number" min="0" max="160" name="sks_total" value="{{ old('sks_total', $akademik->sks_total) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- SKS Gagal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">SKS Gagal</label>
                    <input type="number" min="0" max="60" name="sks_tidak_lulus" value="{{ old('sks_tidak_lulus', $akademik->sks_tidak_lulus) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kehadiran -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Persentase Kehadiran (%) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" min="0" max="100.0" name="persentase_kehadiran" value="{{ old('persentase_kehadiran', $akademik->persentase_kehadiran) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- Status Cuti -->
                <div class="flex items-center pt-6">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="status_cuti" value="1" {{ old('status_cuti', $akademik->status_cuti) ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-xs font-medium text-slate-700">Status Cuti Akademik</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ route('admin.akademik.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 shadow-xs transition flex items-center">
                    <i data-lucide="save" class="w-4 h-4 mr-1.5"></i>
                    Perbarui Rekam Nilai
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
