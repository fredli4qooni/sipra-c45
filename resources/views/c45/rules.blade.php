@extends('layouts.app')

@section('title', 'Aturan Klasifikasi IF-THEN')
@section('subtitle', 'Daftar aturan logika yang diekstraksi dari pohon keputusan C4.5')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Rules Explorer (IF - THEN)</h3>
                <p class="text-xs text-slate-500">
                    Model: <strong class="text-brand-700">{{ $c45->nama_model ?? 'Tidak ada model' }}</strong> • Total: <strong class="text-slate-900 font-mono">{{ $rules->total() }} Aturan</strong>
                </p>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.tree.show', ['c45' => $c45?->id]) : route('prodi.tree.show', ['c45' => $c45?->id]) }}" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center">
                    <i data-lucide="git-merge" class="w-4 h-4 mr-1.5 text-brand-600"></i>
                    Buka Pohon Keputusan
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ auth()->user()->isAdmin() ? route('admin.rules.index') : route('prodi.rules.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
            @if($c45)
                <input type="hidden" name="c45" value="{{ $c45->id }}">
            @endif

            <!-- Search -->
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari kondisi aturan (contoh: IPK = Rendah)..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition"
                >
            </div>

            <!-- Filter Keputusan -->
            <div class="flex items-center space-x-2">
                <select name="decision" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <option value="">Semua Keputusan</option>
                    <option value="Risiko Rendah" {{ request('decision') === 'Risiko Rendah' ? 'selected' : '' }}>Risiko Rendah</option>
                    <option value="Risiko Sedang" {{ request('decision') === 'Risiko Sedang' ? 'selected' : '' }}>Risiko Sedang</option>
                    <option value="Risiko Tinggi" {{ request('decision') === 'Risiko Tinggi' ? 'selected' : '' }}>Risiko Tinggi</option>
                </select>
                @if(request()->hasAny(['search', 'decision']))
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.rules.index', ['c45' => $c45?->id]) : route('prodi.rules.index', ['c45' => $c45?->id]) }}" class="p-2 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-900 transition">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Rules Table Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-100">
                        <th class="pb-3 font-semibold w-16">Kode</th>
                        <th class="pb-3 font-semibold">Aturan Logika Keputusan</th>
                        <th class="pb-3 font-semibold">Keputusan</th>
                        <th class="pb-3 font-semibold text-center">Confidence</th>
                        <th class="pb-3 font-semibold text-center">Dukungan Sampel</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rules as $rule)
                        <tr class="hover:bg-slate-50/80 transition font-mono">
                            <td class="py-3.5 font-bold text-amber-700">{{ $rule->rule_code }}</td>
                            <td class="py-3.5 font-sans text-slate-800 text-xs">
                                @php
                                    $formatted = str_replace(
                                        ['IF ', ' AND ', ' THEN '],
                                        ['<strong class="text-purple-700 font-bold">IF</strong> ', ' <strong class="text-teal-700 font-bold">AND</strong> ', ' <strong class="text-amber-700 font-bold">THEN</strong> '],
                                        e($rule->rule_text)
                                    );
                                @endphp
                                {!! $formatted !!}
                            </td>
                            <td class="py-3.5 font-sans">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                    @if($rule->decision === 'Risiko Rendah') bg-brand-50 text-brand-700 border border-brand-200/60
                                    @elseif($rule->decision === 'Risiko Sedang') bg-amber-50 text-amber-700 border border-amber-200/60
                                    @else bg-rose-50 text-rose-700 border border-rose-200/60
                                    @endif">
                                    {{ $rule->decision }}
                                </span>
                            </td>
                            <td class="py-3.5 text-center text-slate-900 font-sans font-bold">{{ $rule->confidence }}%</td>
                            <td class="py-3.5 text-center text-slate-500 font-sans">{{ $rule->support_samples }} sampel</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Tidak ada aturan yang sesuai dengan filter pencarian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-xs text-slate-500">
            <span>Menampilkan {{ $rules->firstItem() ?? 0 }} - {{ $rules->lastItem() ?? 0 }} dari {{ $rules->total() }} aturan</span>
            <div>{{ $rules->links() }}</div>
        </div>
    </div>

</div>
@endsection
