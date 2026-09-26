@extends('layouts.app')

@section('title', 'Prediksi Massal (Batch Excel)')
@section('subtitle', 'Klasifikasi risiko akademik seluruh mahasiswa sekaligus menggunakan dataset spreadsheet')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Active Model Banner -->
    <div class="p-4 rounded-xl bg-brand-50 border border-brand-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-lg bg-brand-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                <i data-lucide="cpu" class="w-4 h-4"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-900">Pohon Keputusan Aktif: {{ $activeModel->nama_model ?? 'Model Default' }}</p>
                <p class="text-[11px] text-brand-800">
                    Akurasi: <strong>{{ $activeModel->accuracy ?? 0 }}%</strong> • Total Aturan: <strong>{{ $activeModel ? count($activeModel->rules) : 0 }} Rules</strong>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @php
                $templateRoute = auth()->user()->isAdmin() ? route('admin.prediksi.batch.template') : route('prodi.prediksi.batch.template');
            @endphp
            <a href="{{ $templateRoute }}" class="px-3.5 py-1.5 rounded-lg bg-white border border-brand-300 text-brand-700 hover:bg-brand-50 text-xs font-bold transition flex items-center shadow-2xs">
                <i data-lucide="download" class="w-3.5 h-3.5 mr-1.5"></i>
                Unduh Template (.xlsx)
            </a>
        </div>
    </div>

    <!-- Batch Upload Card -->
    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center shrink-0">
                    <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Unggah Berkas Spreadsheet Prediksi</h3>
                    <p class="text-xs text-slate-500">Mendukung format .xlsx, .xls, dan .csv dengan pemetaan kolom otomatis</p>
                </div>
            </div>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                Kembali
            </a>
        </div>

        @php
            $batchStoreAction = auth()->user()->isAdmin() ? route('admin.prediksi.batch.store') : route('prodi.prediksi.batch.store');
        @endphp

        <form id="batchUploadForm" method="POST" action="{{ $batchStoreAction }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Interactive File Drop Zone -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Pilih File Spreadsheet (.xlsx, .xls, .csv) <span class="text-rose-500">*</span></label>
                
                <div id="dropZone" class="relative border-2 border-dashed border-slate-300 hover:border-brand-500 rounded-xl p-6 sm:p-8 text-center bg-slate-50/50 hover:bg-brand-50/20 transition cursor-pointer group">
                    <input 
                        type="file" 
                        name="file" 
                        id="fileInput" 
                        required 
                        accept=".xlsx,.xls,.csv"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                    >
                    
                    <div id="uploadPrompt" class="space-y-2">
                        <div class="w-12 h-12 rounded-lg bg-white border border-slate-200 text-slate-400 group-hover:text-brand-600 group-hover:border-brand-300 flex items-center justify-center mx-auto transition shadow-2xs">
                            <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">
                                Klik untuk memilih berkas <span class="text-slate-400 font-normal">atau seret ke sini</span>
                            </p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Format file: Microsoft Excel (.xlsx, .xls) atau CSV (Maksimal 10 MB)</p>
                        </div>
                    </div>

                    <!-- Selected File Live Preview (hidden by default) -->
                    <div id="filePreview" class="hidden items-center justify-between p-3.5 rounded-lg bg-white border border-brand-200 shadow-2xs">
                        <div class="flex items-center space-x-3 text-left">
                            <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center shrink-0">
                                <i data-lucide="file-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p id="fileName" class="text-xs font-bold text-slate-900 truncate max-w-xs sm:max-w-md">-</p>
                                <p id="fileDetails" class="text-[10px] text-slate-500">-</p>
                            </div>
                        </div>
                        <button type="button" id="removeFileBtn" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2 py-1 rounded hover:bg-rose-50 transition relative z-20">
                            Ganti Berkas
                        </button>
                    </div>
                </div>

                @error('file') <p class="text-[11px] text-rose-600 mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Format Specifications & Helper Accordion -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-slate-900 font-bold text-xs">
                        <i data-lucide="info" class="w-4 h-4 text-brand-600"></i>
                        <span>Spesifikasi Kolom & Ketentuan Berkas</span>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400">9 Kolom Utama</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1 text-[11px]">
                    <div class="p-2 rounded-lg bg-white border border-slate-200">
                        <span class="font-bold text-slate-800 block">1. NIM</span>
                        <span class="text-[10px] text-slate-500">Nomor Induk Mahasiswa</span>
                    </div>
                    <div class="p-2 rounded-lg bg-white border border-slate-200">
                        <span class="font-bold text-slate-800 block">2. Nama Mahasiswa</span>
                        <span class="text-[10px] text-slate-500">Nama Lengkap</span>
                    </div>
                    <div class="p-2 rounded-lg bg-white border border-slate-200">
                        <span class="font-bold text-slate-800 block">3. Semester</span>
                        <span class="text-[10px] text-slate-500">Angka 1 s/d 14</span>
                    </div>
                    <div class="p-2 rounded-lg bg-white border border-slate-200">
                        <span class="font-bold text-slate-800 block">4. IPS</span>
                        <span class="text-[10px] text-slate-500">Rentang 0.00 - 4.00</span>
                    </div>
                    <div class="p-2 rounded-lg bg-white border border-slate-200">
                        <span class="font-bold text-slate-800 block">5. IPK</span>
                        <span class="text-[10px] text-slate-500">Rentang 0.00 - 4.00</span>
                    </div>
                    <div class="p-2 rounded-lg bg-white border border-slate-200">
                        <span class="font-bold text-slate-800 block">6. SKS Semester</span>
                        <span class="text-[10px] text-slate-500">Beban SKS (contoh: 22)</span>
                    </div>
                    <div class="p-2 rounded-lg bg-white border border-slate-200">
                        <span class="font-bold text-slate-800 block">7. SKS Tidak Lulus</span>
                        <span class="text-[10px] text-slate-500">Jumlah SKS gagal (0, 3, dst)</span>
                    </div>
                    <div class="p-2 rounded-lg bg-white border border-slate-200">
                        <span class="font-bold text-slate-800 block">8. Kehadiran (%)</span>
                        <span class="text-[10px] text-slate-500">Presensi (0 s/d 100)</span>
                    </div>
                    <div class="p-2 rounded-lg bg-white border border-slate-200">
                        <span class="font-bold text-slate-800 block">9. Status Cuti</span>
                        <span class="text-[10px] text-slate-500">'Ya' atau 'Tidak'</span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-500 pt-1 leading-relaxed">
                    Sistem mendeteksi header secara cerdas. Jika terdapat baris data yang keliru (misal IPK &gt; 4.00), sistem akan tetap memproses baris yang valid dan menyajikan laporan log baris yang dilewati secara rinci.
                </p>
            </div>

            <!-- Submit Button & Action Controls -->
            <div class="pt-2 flex items-center justify-end space-x-3 border-t border-slate-100">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.prediksi.index') : route('prodi.prediksi.index') }}" class="px-4 py-2.5 rounded-lg bg-slate-100 text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                    Batal
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2.5 rounded-lg text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xs transition flex items-center">
                    <span id="btnIcon"><i data-lucide="play" class="w-4 h-4 mr-2"></i></span>
                    <span id="btnText">Mulai Eksekusi Prediksi Massal</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('fileInput');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileDetails = document.getElementById('fileDetails');
        const removeFileBtn = document.getElementById('removeFileBtn');
        const form = document.getElementById('batchUploadForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');

        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                fileName.textContent = file.name;
                const sizeKb = (file.size / 1024).toFixed(1);
                const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                const sizeStr = file.size > 1024 * 1024 ? `${sizeMb} MB` : `${sizeKb} KB`;
                fileDetails.textContent = `Ukuran: ${sizeStr} • Siap diproses`;

                uploadPrompt.classList.add('hidden');
                filePreview.classList.remove('hidden');
                filePreview.classList.add('flex');
            }
        });

        removeFileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            fileInput.value = '';
            filePreview.classList.add('hidden');
            filePreview.classList.remove('flex');
            uploadPrompt.classList.remove('hidden');
        });

        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            btnIcon.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            btnText.textContent = 'Sedang Memproses Prediksi C4.5...';
        });
    });
</script>
@endsection
