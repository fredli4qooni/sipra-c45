@extends('layouts.app')

@section('title', 'Prediksi Massal (Batch Excel)')
@section('subtitle', 'Klasifikasi risiko akademik seluruh mahasiswa sekaligus menggunakan dataset spreadsheet')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Active Model Banner -->
    <div class="p-4 rounded-2xl bg-brand-50 border border-brand-200 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-xs">
                <i data-lucide="cpu" class="w-4 h-4"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900">Model C4.5: {{ $activeModel->nama_model ?? 'Model Default' }}</p>
                <p class="text-[10px] text-brand-800">Akurasi: <strong>{{ $activeModel->accuracy ?? 0 }}%</strong></p>
            </div>
        </div>
    </div>

    <!-- Batch Upload Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                    <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Unggah Berkas Excel Prediksi</h3>
                    <p class="text-xs text-slate-500">Sistem akan mengklasifikasikan setiap baris mahasiswa menggunakan Decision Tree C4.5</p>
                </div>
            </div>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Kembali
            </a>
        </div>

        @php
            $batchStoreAction = auth()->user()->isAdmin() ? route('admin.prediksi.batch.store') : route('prodi.prediksi.batch.store');
        @endphp

        <form method="POST" action="{{ $batchStoreAction }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Pilih File Spreadsheet (.xlsx, .xls, .csv) <span class="text-rose-500">*</span></label>
                <input 
                    type="file" 
                    name="file" 
                    required 
                    accept=".xlsx,.xls,.csv"
                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer bg-slate-50 border border-slate-200 rounded-xl p-2"
                >
                @error('file') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Template Download Helper Box -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-2">
                <div class="flex items-center space-x-2 text-slate-900 font-bold">
                    <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
                    <span>Ketentuan Format Berkas:</span>
                </div>
                <p>1. Berkas spreadsheet harus memiliki kolom: <strong>NIM, Nama Mahasiswa, Semester, IPS, IPK, SKS Semester, SKS Tidak Lulus, Kehadiran (%), Status Cuti</strong>.</p>
                <p>2. Anda dapat mengunduh format template yang sudah disesuaikan di bawah ini.</p>
                <div class="pt-1">
                    <a href="{{ route('admin.akademik.template') }}" class="inline-flex items-center text-brand-700 hover:underline font-bold">
                        <i data-lucide="download" class="w-3.5 h-3.5 mr-1"></i>
                        Unduh Template Excel Prediksi (.xlsx)
                    </a>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 shadow-xs transition flex items-center">
                    <i data-lucide="play" class="w-4 h-4 mr-2"></i>
                    Mulai Eksekusi Prediksi Massal
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
