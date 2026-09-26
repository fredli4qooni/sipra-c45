@extends('layouts.app')

@section('title', 'Data Mining Decision Tree C4.5')
@section('subtitle', 'Daftar model klasifikasi risiko akademik & riwayat pengujian algoritma')

@section('content')
<div class="space-y-6">

    <!-- Active Model Ribbon Banner -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-brand-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500 mr-1.5 animate-ping"></span>
                    Model Klasifikasi Aktif Sistem
                </span>
            </div>
            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                {{ $activeModel->nama_model ?? 'Belum ada model yang diaktifkan' }}
            </h3>
            <p class="text-xs text-slate-500">
                @if($activeModel)
                    Dilatih pada {{ $activeModel->train_date->format('d F Y H:i') }} WIB • Split Ratio: <strong>{{ $activeModel->split_ratio }}</strong> • Total Dataset: <strong>{{ $activeModel->total_training_samples + $activeModel->total_testing_samples }} sampel</strong>
                @else
                    Silakan latih model baru dengan dataset riwayat akademik untuk mengaktifkan mesin prediksi.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($activeModel)
                <div class="p-3.5 rounded-2xl bg-brand-50/70 border border-brand-200/80 text-center min-w-[130px]">
                    <p class="text-[10px] text-brand-700 uppercase font-bold tracking-wider">Akurasi Pengujian</p>
                    <p class="text-2xl font-black text-brand-800">{{ $activeModel->accuracy }}%</p>
                    <span class="text-[10px] text-brand-600 font-medium">F1-Score: {{ $activeModel->f1_score }}%</span>
                </div>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.c45.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 shadow-xs hover:shadow-sm transition">
                    <i data-lucide="play" class="w-4 h-4 mr-1.5"></i>
                    Latih Model C4.5 Baru
                </a>
            @endif
        </div>
    </div>

    <!-- Models Table List Card -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-2.5">
                <i data-lucide="history" class="w-4 h-4 text-brand-600"></i>
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Riwayat Pelatihan Model C4.5</h4>
            </div>
            <span class="text-xs text-slate-500 font-medium">Total: {{ $models->total() }} Model</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-semibold">Nama Model</th>
                        <th class="pb-3 font-semibold">Waktu Training</th>
                        <th class="pb-3 font-semibold">Split Ratio</th>
                        <th class="pb-3 font-semibold">Sampel (Latih/Uji)</th>
                        <th class="pb-3 font-semibold">Akurasi</th>
                        <th class="pb-3 font-semibold">Presisi</th>
                        <th class="pb-3 font-semibold">Recall</th>
                        <th class="pb-3 font-semibold">F1-Score</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($models as $model)
                        <tr class="hover:bg-slate-50/80 transition {{ $model->is_active ? 'bg-brand-50/30' : '' }}">
                            <td class="py-3.5">
                                <span class="font-bold text-slate-900 block">{{ $model->nama_model }}</span>
                                <span class="text-[11px] text-slate-500">{{ Str::limit($model->deskripsi, 40) }}</span>
                            </td>
                            <td class="py-3.5 text-slate-600">{{ $model->train_date->format('d/m/Y H:i') }}</td>
                            <td class="py-3.5 font-mono text-slate-600 font-semibold">{{ $model->split_ratio }}</td>
                            <td class="py-3.5 text-slate-600">{{ $model->total_training_samples }} / {{ $model->total_testing_samples }}</td>
                            <td class="py-3.5 font-bold text-brand-700">{{ $model->accuracy }}%</td>
                            <td class="py-3.5 text-slate-700">{{ $model->precision }}%</td>
                            <td class="py-3.5 text-slate-700">{{ $model->recall }}%</td>
                            <td class="py-3.5 text-slate-700">{{ $model->f1_score }}%</td>
                            <td class="py-3.5">
                                @if($model->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 mr-1.5"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        Arsip
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 text-right space-x-1">
                                @php
                                    $showRoute = auth()->user()->isAdmin() ? route('admin.c45.show', $model) : route('prodi.c45.show', $model);
                                @endphp
                                <a href="{{ $showRoute }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition inline-flex items-center">
                                    Inspeksi ➜
                                </a>

                                @if(auth()->user()->isAdmin() && !$model->is_active)
                                    <form method="POST" action="{{ route('admin.c45.activate', $model) }}" class="inline">
                                        @csrf
                                        <button type="submit" title="Jadikan Model Aktif" class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-semibold text-[11px] border border-brand-200 transition">
                                            Aktifkan
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.c45.destroy', $model) }}" class="inline" onsubmit="return confirm('Hapus model C4.5 ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus" class="p-1 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-600 inline-flex transition">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">Belum ada model C4.5 yang dilatih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $models->links() }}
        </div>
    </div>

</div>
@endsection
