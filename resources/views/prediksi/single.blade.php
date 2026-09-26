@extends('layouts.app')

@section('title', 'Simulasi Prediksi Risiko Tunggal')
@section('subtitle', 'Klasifikasi risiko akademik mahasiswa secara instan menggunakan Decision Tree C4.5')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Active Model Banner -->
    <div class="p-4 rounded-2xl bg-brand-50 border border-brand-200 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-xs">
                <i data-lucide="cpu" class="w-4 h-4"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900">Model Acuan: {{ $activeModel->nama_model ?? 'Model Default' }}</p>
                <p class="text-[10px] text-brand-800">Akurasi Pengujian: <strong>{{ $activeModel->accuracy ?? 0 }}%</strong> • {{ count($activeModel->rules ?? []) }} Aturan Klasifikasi</p>
            </div>
        </div>
        <a href="{{ auth()->user()->isAdmin() ? route('admin.c45.index') : route('prodi.c45.index') }}" class="text-xs font-bold text-brand-700 hover:underline">
            Ganti Model ➜
        </a>
    </div>

    <!-- Prediction Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Formulir Parameter Prediksi</h3>
                    <p class="text-xs text-slate-500">Pilih mahasiswa terdaftar atau masukkan parameter simulasi manual</p>
                </div>
            </div>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Kembali
            </a>
        </div>

        @php
            $storeAction = auth()->user()->isAdmin() ? route('admin.prediksi.single.store') : route('prodi.prediksi.single.store');
        @endphp

        <form method="POST" action="{{ $storeAction }}" class="space-y-5">
            @csrf

            <!-- Quick Auto-Fill Selector -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <label class="block text-xs font-bold text-brand-700 uppercase tracking-wider flex items-center">
                    <i data-lucide="user-search" class="w-4 h-4 mr-1.5"></i>
                    Pilih Data Mahasiswa (Auto-Fill Otomatis)
                </label>
                <select id="mhs-selector" onchange="autoFillStudentData(this)" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">-- Pilih Mahasiswa atau Isi Bebas di Bawah --</option>
                    @foreach($mahasiswas as $mhs)
                        @php $akd = $mhs->latestAkademik; @endphp
                        <option 
                            value="{{ $mhs->id }}"
                            data-nim="{{ $mhs->nim }}"
                            data-nama="{{ $mhs->nama }}"
                            data-semester="{{ $akd->semester ?? 4 }}"
                            data-ips="{{ $akd->ips ?? 3.00 }}"
                            data-ipk="{{ $akd->ipk ?? 3.00 }}"
                            data-sks="{{ $akd->sks_semester ?? 20 }}"
                            data-tidaklulus="{{ $akd->sks_tidak_lulus ?? 0 }}"
                            data-kehadiran="{{ $akd->persentase_kehadiran ?? 85.0 }}"
                            data-cuti="{{ ($akd->status_cuti ?? false) ? '1' : '0' }}"
                        >
                            {{ $mhs->nim }} - {{ $mhs->nama }} (IPK: {{ $akd->ipk ?? '-' }}, Kehadiran: {{ $akd->persentase_kehadiran ?? '-' }}%)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Hidden input for mahasiswa_id -->
            <input type="hidden" name="mahasiswa_id" id="mahasiswa_id" value="{{ old('mahasiswa_id') }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NIM -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">NIM Mahasiswa</label>
                    <input type="text" name="nim" id="nim" value="{{ old('nim') }}" placeholder="contoh: 2271020052" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 font-mono transition">
                </div>

                <!-- Nama -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Mahasiswa <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" value="{{ old('nama_mahasiswa') }}" required placeholder="Nama lengkap mahasiswa" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @error('nama_mahasiswa') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Semester -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Semester Saat Ini <span class="text-rose-500">*</span></label>
                    <select name="semester" id="semester" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('semester', 4) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- IPS -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">IPS Terakhir <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="4.00" name="ips" id="ips" value="{{ old('ips', '3.20') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- IPK -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">IPK Kumulatif <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" max="4.00" name="ipk" id="ipk" value="{{ old('ipk', '3.15') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- SKS Semester -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">SKS Diambil <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" max="30" name="sks_semester" id="sks_semester" value="{{ old('sks_semester', 20) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- SKS Tidak Lulus -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">SKS Gagal (D/E) <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" max="60" name="sks_tidak_lulus" id="sks_tidak_lulus" value="{{ old('sks_tidak_lulus', 0) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>

                <!-- Persentase Kehadiran -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kehadiran (%) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" min="0" max="100.0" name="persentase_kehadiran" id="persentase_kehadiran" value="{{ old('persentase_kehadiran', 88.0) }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                </div>
            </div>

            <!-- Status Cuti -->
            <div class="pt-1">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="status_cuti" id="status_cuti" value="1" {{ old('status_cuti') ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-xs font-medium text-slate-700">Status Mahasiswa Sedang Mengambil Cuti Akademik</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xs transition flex items-center">
                    <i data-lucide="brain-circuit" class="w-4 h-4 mr-2"></i>
                    Jalankan Prediksi Risiko C4.5
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    function autoFillStudentData(selectElem) {
        const option = selectElem.options[selectElem.selectedIndex];
        if (!option.value) return;

        document.getElementById('mahasiswa_id').value = option.value;
        document.getElementById('nim').value = option.dataset.nim || '';
        document.getElementById('nama_mahasiswa').value = option.dataset.nama || '';
        document.getElementById('semester').value = option.dataset.semester || 4;
        document.getElementById('ips').value = option.dataset.ips || 3.00;
        document.getElementById('ipk').value = option.dataset.ipk || 3.00;
        document.getElementById('sks_semester').value = option.dataset.sks || 20;
        document.getElementById('sks_tidak_lulus').value = option.dataset.tidaklulus || 0;
        document.getElementById('persentase_kehadiran').value = option.dataset.kehadiran || 85.0;
        document.getElementById('status_cuti').checked = (option.dataset.cuti === '1');
    }
</script>
@endpush
@endsection
