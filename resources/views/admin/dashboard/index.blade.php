@extends('admin.layouts.app')
@section('title','Tổng quan')

@section('content')

{{-- ===== THEME-LOCAL CSS (không dùng @apply) ===== --}}
<style>
    :root {
        --ink: 15, 23, 42;
        /* slate-900 */
        --muted: 100, 116, 139;
        /* slate-500 */
        --border: 226, 232, 240;
        /* slate-200 */
        --ring: 248, 200, 220;
        /* pink-200 */
        --brand: 248, 200, 220;
        /* pink-200 - hồng pastel */
        --brand2: 253, 230, 240;
        /* pink-100 */
        --sky: 14, 165, 233;
        /* sky-500 */
        --emerald: 16, 185, 129;
        /* emerald-500 */
    }

    .glass {
        background: rgba(255, 255, 255, .95);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(var(--border), 0.8);
    }

    .card {
        border: 1px solid rgba(var(--border), 1);
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,250,252,0.8) 100%);
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(var(--brand), .08), 0 1px 8px rgba(0,0,0,.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(var(--brand), .12), 0 4px 12px rgba(0,0,0,.08);
    }

    .tile {
        border: 1px solid rgba(var(--border), 1);
        border-radius: 1.5rem;
        background: linear-gradient(135deg, rgba(var(--brand), .08) 0%, rgba(var(--brand2), .06) 100%);
        box-shadow: 0 8px 24px rgba(var(--brand), .06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .tile:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(var(--brand), .1);
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: .375rem;
        border-radius: 999px;
        padding: .25rem .5rem;
        font-size: .75rem;
        font-weight: 600;
        border: 1px solid rgba(var(--border), 1);
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(4px);
    }

    .chip-dot {
        width: .5rem;
        height: .5rem;
        border-radius: 999px;
        background: rgb(var(--brand));
        box-shadow: 0 0 8px rgba(var(--brand), 0.4);
    }

    .soft-table thead th {
        color: rgba(var(--muted), 1);
        font-weight: 600;
        background: rgba(var(--brand), 0.05);
        border-bottom: 1px solid rgba(var(--border), 1);
    }

    .soft-table tbody tr {
        border-bottom: 1px solid rgba(var(--border), 0.5);
        transition: background-color 0.2s ease;
    }

    .soft-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(var(--brand), .02) 0%, rgba(var(--brand2), .04) 100%);
    }

    .kpi-number {
        letter-spacing: -.01em;
        background: linear-gradient(135deg, rgb(var(--brand)) 0%, rgb(var(--brand2)) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .btn-primary {
        background: linear-gradient(135deg, rgb(var(--brand)) 0%, rgb(var(--brand2)) 100%);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(var(--brand), 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(var(--brand), 0.4);
    }
</style>

{{-- ===== HEADER ===== --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Tổng quan</h1>
        <p class="text-slate-500 mt-1">Ảnh chụp nhanh hiệu suất bán hàng & tồn kho.</p>
    </div>
    <div class="flex items-center gap-2">
        <input id="dateRange"
            class="glass rounded-lg border border-pink-200/70 px-3 py-2 text-sm shadow-sm focus:outline-none"
            placeholder="Chọn khoảng ngày" />
    </div>
</div>

{{-- ===== KPI ROW ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="tile p-4">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-600">Doanh thu hôm nay</span>
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-pink-100 text-pink-700">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                    <path d="M7 9h10M7 13h6"></path>
                </svg>
            </span>
        </div>
        <div class="mt-1 text-3xl font-bold kpi-number">{{ number_format($todayRevenue ?? 0) }}₫</div>
    </div>

    <div class="tile p-4">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-600">Doanh thu tháng</span>
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-pink-200 text-pink-700">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 12h18"></path>
                    <path d="M3 6h18"></path>
                    <path d="M3 18h18"></path>
                </svg>
            </span>
        </div>
        <div class="mt-1 text-3xl font-bold kpi-number">{{ number_format($monthRevenue ?? 0) }}₫</div>
    </div>

    <div class="tile p-4">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-600">Số đơn tháng</span>
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-pink-300 text-pink-800">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 7h18M5 7l1 12h12l1-12"></path>
                    <path d="M9 7V5a3 3 0 016 0v2"></path>
                </svg>
            </span>
        </div>
        <div class="mt-1 text-3xl font-bold kpi-number">{{ number_format($ordersCount ?? 0) }}</div>
    </div>

    <div class="tile p-4">
        <div class="flex items-center justify-between">
            <span class="text-sm text-slate-600">AOV (giá trị TB)</span>
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-pink-400 text-pink-900">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 12h18"></path>
                    <path d="M12 3v18"></path>
                </svg>
            </span>
        </div>
        <div class="mt-1 text-3xl font-bold kpi-number">{{ number_format($aov ?? 0) }}₫</div>
    </div>
</div>

{{-- ===== CHARTS ROW 1 ===== --}}
<div class="grid lg:grid-cols-3 gap-4 mt-5">
    {{-- Revenue 14d --}}
    <div class="card p-5 lg:col-span-2 min-w-0">
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold">Doanh thu 14 ngày</div>
                <div class="text-xs text-slate-500">Đã lọc các đơn đã thanh toán & hoàn tất</div>
            </div>
            <span class="chip bg-pink-50 text-pink-700"><span class="chip-dot"></span> realtime</span>
        </div>
        <div class="relative mt-4 h-[340px]">
            <canvas id="revChart" class="absolute inset-0 w-full h-full"></canvas>
        </div>
    </div>

    {{-- Status Donut --}}
    <div class="card p-5 min-w-0">
        <div class="font-semibold">Tỉ lệ trạng thái đơn (tháng)</div>

        <div class="grid grid-cols-5 gap-4 mt-3">
            <div class="col-span-3">
                <div class="relative h-[260px]">
                    <canvas id="statusChart" class="absolute inset-0 w-full h-full"></canvas>
                </div>
            </div>

            <div class="col-span-2">
                @php
                $colors = ['#f43f5e','#06b6d4','#22c55e','#f59e0b','#8b5cf6','#64748b','#ef4444','#14b8a6','#10b981','#eab308'];
                $i = 0;
                @endphp
                <ul class="space-y-1.5">
                    @forelse(($statusAgg ?? []) as $label => $val)
                    <li class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full"
                                style="background-color: {{ $colors[$i % count($colors)] }}"></span>
                            <span class="text-slate-600">{{ ucwords(str_replace('_',' ', $label ?: 'unknown')) }}</span>
                        </div>
                        <span class="font-medium">{{ number_format($val) }}</span>
                    </li>
                    @php $i++; @endphp
                    @empty
                    <li class="text-slate-500 text-sm">Chưa có dữ liệu.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ===== CHARTS ROW 2 ===== --}}
<div class="grid lg:grid-cols-3 gap-4 mt-5">
    {{-- Payment --}}
    <div class="card p-5 min-w-0">
        <div class="font-semibold">Kênh thanh toán (tháng)</div>
        <div class="relative mt-4 h-[260px]">
            <canvas id="payChart" class="absolute inset-0 w-full h-full"></canvas>
        </div>
    </div>

    {{-- Top products --}}
    <div class="card p-5 lg:col-span-2 min-w-0">
        <div class="flex items-center justify-between">
            <div class="font-semibold">Top sản phẩm bán chạy (14 ngày)</div>
            <span class="text-xs text-slate-400">Theo SL & doanh thu</span>
        </div>
        <div class="overflow-x-auto mt-3">
            <table class="soft-table w-full text-sm">
                <thead>
                    <tr>
                        <th class="py-2">Sản phẩm</th>
                        <th class="w-24 text-right">SL</th>
                        <th class="w-32 text-right">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($topProducts ?? []) as $row)
                    @php $displayName = $row->name ?? $row->product_name_snapshot ?? '—'; @endphp
                    <tr>
                        <td class="py-2">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-50 text-pink-600 font-semibold">
                                    {{ mb_substr($displayName,0,1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium leading-5 line-clamp-1">{{ $displayName }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-right">{{ number_format($row->qty ?? 0) }}</td>
                        <td class="text-right font-medium">{{ number_format($row->total ?? 0) }}₫</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-slate-500">Chưa có dữ liệu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== CATEGORIES + LOW STOCK ===== --}}
<div class="grid lg:grid-cols-3 gap-4 mt-5">
    {{-- Categories --}}
    <div class="card p-5 min-w-0">
        <div class="font-semibold">Ngành hàng nổi bật (14 ngày)</div>
        @php
        $catMax = 0;
        if(isset($categoryAgg) && $categoryAgg instanceof \Illuminate\Support\Collection && $categoryAgg->count()){
        $catMax = max($categoryAgg->pluck('total')->toArray());
        }
        if ($catMax <= 0) $catMax=1;
            @endphp

            <div class="mt-3 space-y-3">
            @forelse(($categoryAgg ?? collect()) as $c)
            @php $pct = min(100, round(($c->total / $catMax) * 100)); @endphp
            <div>
                <div class="flex items-center justify-between text-sm">
                    <div class="font-medium">{{ $c->name }}</div>
                    <div class="text-slate-500">{{ number_format($c->total) }}₫</div>
                </div>
                <div class="mt-1 h-2 w-full overflow-hidden rounded-full bg-pink-50">
                    <div class="h-2 rounded-full"
                        style="width: {{ $pct }}%; background: linear-gradient(90deg, rgb(var(--brand)) 0%, rgb(var(--brand2)) 100%);"></div>
                </div>
            </div>
            @empty
            <div class="text-sm text-slate-500">Chưa có dữ liệu.</div>
            @endforelse
    </div>
</div>

{{-- Low stock --}}
<div class="card p-5 lg:col-span-2 min-w-0">
    <div class="flex items-center justify-between">
        <div class="font-semibold">Cảnh báo tồn kho thấp</div>
        <span class="chip bg-amber-50 text-amber-700">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            {{ $lowStockCount ?? 0 }} biến thể
        </span>
    </div>

    @if(($lowStockItems ?? collect())->count())
    <div class="mt-3 overflow-x-auto">
        <table class="soft-table w-full text-sm">
            <thead>
                <tr>
                    <th class="py-2">Sản phẩm</th>
                    <th>SKU</th>
                    <th class="w-24 text-right">SL</th>
                    <th class="w-28 text-right">Ngưỡng</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockItems as $it)
                <tr>
                    <td class="py-2">{{ $it->product_name }}</td>
                    <td class="text-slate-600">{{ $it->sku }}</td>
                    <td class="text-right text-pink-600 font-medium">{{ $it->qty }}</td>
                    <td class="text-right text-slate-500">{{ $it->min_qty ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="mt-3 text-sm text-slate-500">Tuyệt vời! Hiện tại không có biến thể nào dưới ngưỡng.</div>
    @endif
</div>
</div>

@endsection

@push('scripts')
<script>
    // Date range (nếu flatpickr đã include ở layout)
    if (window.flatpickr) {
        flatpickr('#dateRange', {
            mode: 'range',
            dateFormat: 'd/m/Y'
        });
    }

    var revLabels = @json($revLabels ?? []);
    var revSeries = @json($revSeries ?? []);
    var statusAgg = @json((object)($statusAgg ?? []));
    var payAgg = @json((object)($payAgg ?? []));

    if (window.Chart) {
        // === Revenue Area ===
        const rctx = document.getElementById('revChart').getContext('2d');
        const grad = rctx.createLinearGradient(0, 0, 0, 350);
        grad.addColorStop(0, 'rgba(244,63,94,.28)');
        grad.addColorStop(1, 'rgba(244,63,94,.03)');

        new Chart(rctx, {
            type: 'line',
            data: {
                labels: revLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: revSeries,
                    borderColor: 'rgb(244,63,94)',
                    backgroundColor: grad,
                    tension: .35,
                    fill: true,
                    pointRadius: 2.5,
                    pointHoverRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // === Status Donut ===
        const sctx = document.getElementById('statusChart').getContext('2d');
        const stLabs = Object.keys(statusAgg);
        const stData = Object.values(statusAgg);
        const stColors = ['#f43f5e', '#06b6d4', '#22c55e', '#f59e0b', '#8b5cf6', '#64748b', '#ef4444', '#14b8a6', '#10b981', '#eab308'];

        const centerText = {
            id: 'centerText',
            beforeDraw(chart) {
                if (!stData.length) return;
                let total = stData.reduce((a, b) => a + b, 0);
                const ctx = chart.ctx;
                const meta = chart.getDatasetMeta(0);
                if (!meta || !meta.data || !meta.data[0]) return;
                const {
                    x,
                    y
                } = meta.data[0];
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = 'rgba(var(--ink),1)';
                ctx.font = '700 18px ui-sans-serif,system-ui';
                ctx.fillText(total, x, y);
                ctx.restore();
            }
        };

        new Chart(sctx, {
            type: 'doughnut',
            data: {
                labels: stLabs,
                datasets: [{
                    data: stData,
                    backgroundColor: stLabs.map((_, i) => stColors[i % stColors.length]),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            },
            plugins: [centerText]
        });

        // === Payment Bar ===
        const pctx = document.getElementById('payChart').getContext('2d');
        const pLab = Object.keys(payAgg);
        const pVal = Object.values(payAgg);

        new Chart(pctx, {
            type: 'bar',
            data: {
                labels: pLab,
                datasets: [{
                    label: 'Đơn',
                    data: pVal,
                    backgroundColor: 'rgba(59,130,246,.18)',
                    borderColor: 'rgb(59,130,246)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    maxBarThickness: 42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
</script>
@endpush