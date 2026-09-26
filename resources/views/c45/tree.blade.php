@extends('layouts.app')

@section('title', 'Visualisasi Pohon Keputusan C4.5')
@section('subtitle', 'Diagram alur keputusan interaktif algoritma Data Mining C4.5')

@section('content')
<div class="space-y-6">

    <!-- Model Switcher & Toolbar Header -->
    <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Struktur Hierarki Pohon Keputusan</h3>
            <p class="text-xs text-slate-500">
                Model Acuan: <strong class="text-brand-700 font-semibold">{{ $c45->nama_model ?? 'Tidak ada model' }}</strong> 
                (Akurasi: <span class="text-brand-700 font-bold">{{ $c45->accuracy ?? 0 }}%</span> • {{ count($c45->rules ?? []) }} Aturan Klasifikasi)
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Model Switcher -->
            <form method="GET" action="{{ auth()->user()->isAdmin() ? route('admin.tree.show') : route('prodi.tree.show') }}" class="flex items-center">
                <select name="c45" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    @foreach($allModels as $m)
                        <option value="{{ $m->id }}" {{ ($c45 && $c45->id === $m->id) ? 'selected' : '' }}>
                            {{ $m->nama_model }} ({{ $m->accuracy }}% {{ $m->is_active ? '• Aktif' : '' }})
                        </option>
                    @endforeach
                </select>
            </form>

            <a href="{{ auth()->user()->isAdmin() ? route('admin.rules.index', ['c45' => $c45?->id]) : route('prodi.rules.index', ['c45' => $c45?->id]) }}" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center">
                <i data-lucide="list-tree" class="w-4 h-4 mr-1.5 text-amber-600"></i>
                Buka Aturan IF-THEN
            </a>
        </div>
    </div>

    @if(!$c45 || empty($treeArray))
        <div class="bg-white p-8 rounded-xl border border-slate-200/80 shadow-xs text-center text-slate-400 space-y-2">
            <i data-lucide="git-commit" class="w-12 h-12 mx-auto text-slate-300"></i>
            <p class="text-sm">Belum ada struktur pohon keputusan yang tersedia. Silakan latih model terlebih dahulu.</p>
        </div>
    @else
        <!-- Visual Decision Tree Canvas Card -->
        <div class="bg-white p-6 rounded-xl border border-slate-200/80 shadow-xs space-y-4">
            
            <!-- Clean Responsive Toolbar & Legend Bar -->
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <!-- Left Title & Subtitle -->
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center flex-shrink-0 border border-brand-200/60">
                        <i data-lucide="git-merge" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Diagram Alur Keputusan (Interactive Tree)</h4>
                        <p class="text-[11px] text-slate-400">Drag kanvas untuk menggeser • Scroll wheel untuk zoom</p>
                    </div>
                </div>

                <!-- Right Controls & Encapsulated Legend Toolbar -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Legend Chips in a clean horizontal container -->
                    <div class="hidden sm:flex items-center gap-1.5 p-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px]">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1"></span> Root
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-white text-slate-700 font-bold border border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-1"></span> Uji
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-900 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span> Rendah
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1"></span> Sedang
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-rose-100 text-rose-900 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1"></span> Tinggi
                        </span>
                    </div>

                    <!-- Divider -->
                    <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-1.5">
                        <button type="button" onclick="zoomInTree()" title="Perbesar (Zoom In)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                            <i data-lucide="zoom-in" class="w-4 h-4"></i>
                        </button>
                        <button type="button" onclick="zoomOutTree()" title="Perkecil (Zoom Out)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                            <i data-lucide="zoom-out" class="w-4 h-4"></i>
                        </button>
                        <button type="button" onclick="fitTreeToView()" title="Pusatkan Tampilan" class="px-3 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center whitespace-nowrap">
                            <i data-lucide="maximize-2" class="w-3.5 h-3.5 mr-1 text-slate-500"></i>
                            Pusatkan
                        </button>
                        <button type="button" onclick="toggleTreeMode()" id="toggleViewBtn" class="px-3.5 h-8 rounded-lg bg-brand-50 hover:bg-brand-100 border border-brand-200 text-xs font-bold text-brand-700 transition flex items-center whitespace-nowrap">
                            <i data-lucide="list" class="w-3.5 h-3.5 mr-1.5"></i>
                            Mode Teks
                        </button>
                    </div>
                </div>
            </div>

            @php
                // Format tree to clean JSON for D3 Hierarchical Engine
                if (!function_exists('formatTreeForD3')) {
                    function formatTreeForD3($node, $branchName = null) {
                        if ($node['type'] === 'leaf') {
                            return [
                                'name' => $node['decision'],
                                'type' => 'leaf',
                                'decision' => $node['decision'],
                                'confidence' => $node['confidence'] ?? 100,
                                'samples' => $node['samples_count'] ?? 0,
                                'branch' => $branchName,
                            ];
                        }

                        $children = [];
                        if (!empty($node['branches'])) {
                            foreach ($node['branches'] as $branchVal => $childNode) {
                                $children[] = formatTreeForD3($childNode, (string) $branchVal);
                            }
                        }

                        return [
                            'name' => $node['attribute'] ?? 'Node',
                            'type' => 'node',
                            'attribute' => $node['attribute'] ?? 'Node',
                            'gain_ratio' => $node['gain_ratio'] ?? 0,
                            'samples' => $node['samples_count'] ?? 0,
                            'branch' => $branchName,
                            'children' => $children,
                        ];
                    }
                }

                $d3DataJson = json_encode(formatTreeForD3($treeArray));
            @endphp

            <!-- 1. D3 SVG INTERACTIVE VECTOR TREE CANVAS -->
            <div id="d3TreeCanvasWrapper" class="w-full bg-slate-50/70 rounded-xl border border-slate-200 overflow-hidden relative cursor-grab active:cursor-grabbing" style="height: 600px;">
                <svg id="decisionTreeSvg" class="w-full h-full"></svg>
            </div>

            <!-- 2. TEXT/INDENT OUTLINE VIEW (Alternative View) -->
            <div id="textOutlineContainer" class="hidden p-6 bg-slate-50/70 rounded-xl border border-slate-200 space-y-4">
                @php
                    if (!function_exists('renderTreeNode')) {
                        function renderTreeNode($node, $depth = 1) {
                            if ($node['type'] === 'leaf') {
                                $decision = $node['decision'];
                                $colorClass = match($decision) {
                                    'Risiko Rendah' => 'bg-brand-50 border-brand-200 text-brand-900',
                                    'Risiko Sedang' => 'bg-amber-50 border-amber-200 text-amber-900',
                                    default => 'bg-rose-50 border-rose-200 text-rose-900',
                                };
                                $badgeColor = match($decision) {
                                    'Risiko Rendah' => 'bg-brand-600 text-white',
                                    'Risiko Sedang' => 'bg-amber-500 text-white',
                                    default => 'bg-rose-600 text-white',
                                };

                                echo '<div class="inline-flex items-center space-x-2.5 p-3 rounded-lg border shadow-xs ' . $colorClass . ' my-1 bg-white">';
                                echo '  <div class="w-6 h-6 rounded-md flex items-center justify-center font-bold text-xs ' . $badgeColor . '"><i data-lucide="check" class="w-3.5 h-3.5"></i></div>';
                                echo '  <div>';
                                echo '    <p class="text-xs font-bold uppercase tracking-wider">' . e($decision) . '</p>';
                                echo '    <p class="text-[10px] text-slate-500">Confidence: ' . $node['confidence'] . '% (' . $node['samples_count'] . ' sampel)</p>';
                                echo '  </div>';
                                echo '</div>';
                                return;
                            }

                            echo '<div class="space-y-3 my-2">';
                            echo '  <div class="inline-flex items-center space-x-2.5 px-4 py-2.5 rounded-lg bg-white border border-slate-200 text-slate-900 shadow-xs">';
                            echo '    <span class="w-2.5 h-2.5 rounded-full bg-brand-500"></span>';
                            echo '    <span class="text-xs font-extrabold uppercase tracking-wide">UJI: ' . str_replace('_', ' ', strtoupper($node['attribute'])) . '</span>';
                            echo '    <span class="text-[10px] px-2 py-0.5 rounded-md bg-brand-50 text-brand-700 border border-brand-200 font-mono font-bold">Gain Ratio: ' . $node['gain_ratio'] . '</span>';
                            echo '    <span class="text-[10px] text-slate-400">(' . $node['samples_count'] . ' data)</span>';
                            echo '  </div>';

                            echo '  <div class="pl-6 border-l-2 border-dashed border-brand-300 space-y-4 pt-1">';
                            foreach ($node['branches'] as $val => $child) {
                                echo '    <div class="relative">';
                                echo '      <div class="flex items-center space-x-2 mb-1.5">';
                                echo '        <i data-lucide="corner-down-right" class="w-3.5 h-3.5 text-brand-600 inline"></i>';
                                echo '        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-white text-slate-800 border border-slate-200 shadow-xs">' . e($val) . '</span>';
                                echo '      </div>';
                                echo '      <div class="pl-4">';
                                renderTreeNode($child, $depth + 1);
                                echo '      </div>';
                                echo '    </div>';
                            }
                            echo '  </div>';
                            echo '</div>';
                        }
                    }
                @endphp

                <div class="tree-root">
                    {!! renderTreeNode($treeArray) !!}
                </div>
            </div>

        </div>
    @endif

</div>

@push('scripts')
<!-- D3.js v7 for Pixel-Perfect, Interactive Vector Tree Rendering -->
<script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
<script>
    const treeData = {!! $d3DataJson ?? '{}' !!};
    let svg, g, zoomBehavior;
    const nodeWidth = 200;
    const nodeHeight = 88;

    document.addEventListener('DOMContentLoaded', () => {
        if (!treeData || Object.keys(treeData).length === 0) return;
        renderD3Tree();
    });

    function renderD3Tree() {
        const container = document.getElementById('d3TreeCanvasWrapper');
        const width = container.clientWidth || 1000;
        const height = container.clientHeight || 600;

        d3.select("#decisionTreeSvg").selectAll("*").remove();

        svg = d3.select("#decisionTreeSvg")
            .attr("width", "100%")
            .attr("height", "100%")
            .attr("viewBox", [0, 0, width, height]);

        // Define SVG Arrowhead Marker
        const defs = svg.append("defs");
        defs.append("marker")
            .attr("id", "arrow")
            .attr("viewBox", "0 -5 10 10")
            .attr("refX", 8)
            .attr("refY", 0)
            .attr("markerWidth", 6)
            .attr("markerHeight", 6)
            .attr("orient", "auto")
            .append("path")
            .attr("d", "M0,-5L10,0L0,5")
            .attr("fill", "#334155");

        g = svg.append("g");

        // Zoom & Pan handler
        zoomBehavior = d3.zoom()
            .scaleExtent([0.3, 2.0])
            .on("zoom", (event) => {
                g.attr("transform", event.transform);
            });

        svg.call(zoomBehavior);

        // Compute D3 Tree Layout
        const hierarchyRoot = d3.hierarchy(treeData);
        const treeLayout = d3.tree()
            .nodeSize([nodeWidth + 60, nodeHeight + 100]);

        treeLayout(hierarchyRoot);

        // 1. Draw Links / Connecting Curves
        const linkGenerator = d3.linkVertical()
            .x(d => d.x)
            .y(d => d.y);

        g.selectAll(".tree-link")
            .data(hierarchyRoot.links())
            .enter()
            .append("path")
            .attr("class", "tree-link")
            .attr("d", d => {
                const sourceY = d.source.y + (nodeHeight / 2);
                const targetY = d.target.y - (nodeHeight / 2);
                return `M${d.source.x},${sourceY} C${d.source.x},${(sourceY + targetY)/2} ${d.target.x},${(sourceY + targetY)/2} ${d.target.x},${targetY}`;
            })
            .attr("fill", "none")
            .attr("stroke", "#334155")
            .attr("stroke-width", 2)
            .attr("marker-end", "url(#arrow)");

        // 2. Draw Branch Value Badges (Floating on curve)
        const branchGroups = g.selectAll(".branch-label-group")
            .data(hierarchyRoot.descendants().filter(d => d.parent && d.data.branch))
            .enter()
            .append("g")
            .attr("class", "branch-label-group")
            .attr("transform", d => {
                const midX = (d.parent.x + d.x) / 2;
                const midY = ((d.parent.y + (nodeHeight / 2)) + (d.y - (nodeHeight / 2))) / 2;
                return `translate(${midX}, ${midY})`;
            });

        branchGroups.append("foreignObject")
            .attr("x", -60)
            .attr("y", -14)
            .attr("width", 120)
            .attr("height", 28)
            .html(d => `
                <div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 100%;">
                    <span style="background: #ffffff; color: #0f172a; border: 1.5px solid #94a3b8; font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 9999px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); font-family: 'Plus Jakarta Sans', sans-serif; white-space: nowrap;">
                        ${d.data.branch}
                    </span>
                </div>
            `);

        // 3. Draw Nodes (Cards with Full Styling & No Text Truncation)
        const nodeGroups = g.selectAll(".tree-node")
            .data(hierarchyRoot.descendants())
            .enter()
            .append("g")
            .attr("class", "tree-node")
            .attr("transform", d => `translate(${d.x - (nodeWidth / 2)}, ${d.y - (nodeHeight / 2)})`);

        nodeGroups.append("foreignObject")
            .attr("width", nodeWidth)
            .attr("height", nodeHeight)
            .html(d => {
                const data = d.data;

                if (data.type === 'leaf') {
                    let bg, border, text, dot, badgeBg;
                    if (data.decision === 'Risiko Rendah') {
                        bg = '#ecfdf5'; border = '#10b981'; text = '#065f46'; dot = '#059669'; badgeBg = '#10b981';
                    } else if (data.decision === 'Risiko Sedang') {
                        bg = '#fffbeb'; border = '#f59e0b'; text = '#92400e'; dot = '#d97706'; badgeBg = '#f59e0b';
                    } else {
                        bg = '#fff1f2'; border = '#f43f5e'; text = '#9f1239'; dot = '#e11d48'; badgeBg = '#f43f5e';
                    }

                    return `
                        <div style="width: 100%; height: 100%; background: ${bg}; border: 2px solid ${border}; border-radius: 10px; padding: 8px 12px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 2px 6px rgba(0,0,0,0.06); font-family: 'Plus Jakarta Sans', sans-serif;">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                                <span style="width: 8px; height: 8px; border-radius: 9999px; background: ${dot}; display: inline-block;"></span>
                                <span style="font-size: 13px; font-weight: 800; color: ${text}; text-transform: uppercase;">${data.decision}</span>
                            </div>
                            <div style="font-size: 11px; font-weight: 600; color: #475569; margin-top: 2px;">
                                Confidence: <strong style="color: #0f172a;">${data.confidence}%</strong>
                            </div>
                            <div style="font-size: 10px; color: #64748b; margin-top: 1px;">
                                (${data.samples} sampel data)
                            </div>
                        </div>
                    `;
                }

                const isRoot = !d.parent;
                const bg = isRoot ? '#fef3c7' : '#f8fafc';
                const border = isRoot ? '#d97706' : '#64748b';
                const titleColor = isRoot ? '#78350f' : '#0f172a';
                const rootBadge = isRoot ? '<span style="font-size: 9px; font-weight: 800; letter-spacing: 0.05em; background: #fef08a; color: #854d0e; padding: 1px 5px; border-radius: 4px; margin-right: 4px; border: 1px solid #fde047;">ROOT</span>' : '';
                const rawAttr = data.attribute || 'Atribut';
                const formattedAttr = rawAttr.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

                return `
                    <div style="width: 100%; height: 100%; background: ${bg}; border: 2px solid ${border}; border-radius: 10px; padding: 8px 12px; display: flex; flex-direction: column; justify-content: center; align-items: center; box-shadow: 0 2px 6px rgba(0,0,0,0.06); font-family: 'Plus Jakarta Sans', sans-serif;">
                        <div style="font-size: 12px; font-weight: 800; color: ${titleColor}; text-align: center; line-height: 1.2;">
                            ${rootBadge}${formattedAttr}?
                        </div>
                        <div style="font-size: 11px; font-weight: 700; color: #059669; background: #ffffff; padding: 1px 8px; border-radius: 6px; border: 1px solid #e2e8f0; margin-top: 4px;">
                            Gain: ${data.gain_ratio}
                        </div>
                        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
                            (${data.samples} data)
                        </div>
                    </div>
                `;
            });

        // Center tree initially
        fitTreeToView();
    }

    function fitTreeToView() {
        if (!svg || !g) return;
        const container = document.getElementById('d3TreeCanvasWrapper');
        const width = container.clientWidth || 1000;
        const height = container.clientHeight || 600;

        const bounds = g.node().getBBox();
        if (bounds.width === 0 || bounds.height === 0) return;

        const dx = bounds.width;
        const dy = bounds.height;
        const x = bounds.x + (dx / 2);
        const y = bounds.y + (dy / 2);

        const scale = Math.max(0.4, Math.min(1.2, 0.85 / Math.max(dx / width, dy / height)));
        const translate = [width / 2 - scale * x, 60 - scale * bounds.y];

        svg.transition()
            .duration(500)
            .call(zoomBehavior.transform, d3.zoomIdentity.translate(translate[0], translate[1]).scale(scale));
    }

    function zoomInTree() {
        if (svg) svg.transition().duration(250).call(zoomBehavior.scaleBy, 1.25);
    }

    function zoomOutTree() {
        if (svg) svg.transition().duration(250).call(zoomBehavior.scaleBy, 0.8);
    }

    function toggleTreeMode() {
        const canvas = document.getElementById('d3TreeCanvasWrapper');
        const textOutline = document.getElementById('textOutlineContainer');
        const btn = document.getElementById('toggleViewBtn');

        if (canvas.classList.contains('hidden')) {
            canvas.classList.remove('hidden');
            textOutline.classList.add('hidden');
            btn.innerHTML = '<i data-lucide="list" class="w-3.5 h-3.5 mr-1"></i> Mode Teks';
            renderD3Tree();
        } else {
            canvas.classList.add('hidden');
            textOutline.classList.remove('hidden');
            btn.innerHTML = '<i data-lucide="git-merge" class="w-3.5 h-3.5 mr-1"></i> Mode Pohon Grafis';
        }
        if (window.lucide) lucide.createIcons();
    }

    window.addEventListener('resize', () => {
        renderD3Tree();
    });
</script>
@endpush
@endsection
