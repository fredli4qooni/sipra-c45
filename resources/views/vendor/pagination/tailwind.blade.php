@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 font-sans">
        {{-- Results Counter Text --}}
        <div>
            <span>
                Menampilkan 
                <strong class="font-mono font-bold text-slate-800">{{ $paginator->firstItem() ?? 0 }}</strong> 
                - 
                <strong class="font-mono font-bold text-slate-800">{{ $paginator->lastItem() ?? 0 }}</strong> 
                dari 
                <strong class="font-mono font-bold text-slate-900">{{ $paginator->total() }}</strong> 
                data
            </span>
        </div>

        {{-- Pagination Nav Links --}}
        <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Halaman Sebelumnya" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-300 bg-slate-50/80 border border-slate-200/60 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman Sebelumnya" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 hover:border-slate-300 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-slate-400 select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center justify-center min-w-[32px] h-8 px-2.5 rounded-lg text-xs font-mono font-bold text-white bg-brand-600 border border-brand-600 shadow-xs">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-[32px] h-8 px-2.5 rounded-lg text-xs font-mono font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 hover:border-slate-300 transition shadow-2xs" aria-label="Ke Halaman {{ $page }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman Berikutnya" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 hover:text-slate-900 hover:border-slate-300 transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="Halaman Berikutnya" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-300 bg-slate-50/80 border border-slate-200/60 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </span>
            @endif
        </nav>
    </div>
@elseif ($paginator->total() > 0)
    <div class="text-xs text-slate-500 font-sans">
        <span>
            Menampilkan 
            <strong class="font-mono font-bold text-slate-800">{{ $paginator->firstItem() ?? 1 }}</strong> 
            - 
            <strong class="font-mono font-bold text-slate-800">{{ $paginator->lastItem() ?? $paginator->total() }}</strong> 
            dari 
            <strong class="font-mono font-bold text-slate-900">{{ $paginator->total() }}</strong> 
            data
        </span>
    </div>
@endif
