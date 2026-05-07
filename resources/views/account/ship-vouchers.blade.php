@extends('layouts.app')
@section('title','Mã vận chuyển')

@section('content')
<style>
    /* ====== Card vận chuyển - cải tiến UI/UX ====== */
    .gcard {
        position: relative;
        border-radius: 1rem;
        padding: 1px;
        background: linear-gradient(135deg, #bae6fd, #7dd3fc, #38bdf8);
        overflow: visible;
        isolation: isolate;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .gcard:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(14, 165, 233, .18);
    }

    .gcard>.inner {
        border-radius: inherit;
        background: rgba(255, 255, 255, .97);
        backdrop-filter: saturate(140%) blur(8px);
        overflow: visible;
    }

    .badge-x {
        position: absolute;
        right: 0;
        top: 0;
        transform: translate(12px, -12px) rotate(-12deg);
        width: 36px;
        height: 36px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
        color: #fff;
        background: linear-gradient(135deg, #38bdf8, #0ea5e9);
        box-shadow: 0 0 0 3px #fff, 0 10px 22px rgba(14, 165, 233, .35);
        z-index: 40;
        pointer-events: none;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .2rem .55rem;
        border-radius: .6rem;
        font-size: .72rem;
        background: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
        font-weight: 500;
    }

    /* Progress bar */
    .usage-bar {
        height: 5px;
        border-radius: 999px;
        background: #e0f2fe;
        overflow: hidden;
        margin-top: 6px;
    }

    .usage-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #38bdf8, #0ea5e9);
        transition: width .4s ease;
    }

    .mini-toast {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        background: #111827;
        color: #fff;
        border-radius: 12px;
        padding: 10px 18px;
        font-size: .875rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .2);
        opacity: 0;
        z-index: 9999;
    }

    /* Code highlight */
    .voucher-code {
        font-family: 'Courier New', monospace;
        letter-spacing: .08em;
        background: #f0f9ff;
        border: 1px dashed #7dd3fc;
        border-radius: .4rem;
        padding: .1rem .4rem;
        font-size: .95rem;
        color: #0284c7;
        font-weight: 800;
    }
</style>

<div class="max-w-6xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black tracking-tight mb-1">🚚 Mã vận chuyển</h1>
            <p class="text-gray-500 text-sm">Các mã giảm phí ship bạn đã nhận được.</p>
        </div>
        @if(($items ?? collect())->count())
        <span class="px-3 py-1 rounded-full bg-sky-50 text-sky-700 text-sm font-semibold border border-sky-200">
            {{ $items->count() }} mã
        </span>
        @endif
    </div>

    @if(($items ?? collect())->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $it)
        @php
            $isPercent = strtolower($it->discount_type ?? '') === 'percent';
            $valueStr  = $isPercent
                ? (rtrim(rtrim(number_format($it->amount, 2), '0'), '.') . '%')
                : number_format((float)$it->amount, 0, ',', '.') . 'đ';
            $maxStr = $it->max_discount
                ? ('Tối đa ' . number_format((float)$it->max_discount, 0, ',', '.') . 'đ')
                : null;
            $minStr = $it->min_order
                ? ('Đơn từ ' . number_format((float)$it->min_order, 0, ',', '.') . 'đ')
                : 'Không yêu cầu';

            $now      = now();
            $start    = $it->start_at ? \Carbon\Carbon::parse($it->start_at) : null;
            $end      = $it->end_at   ? \Carbon\Carbon::parse($it->end_at)   : null;
            $statusOk = (int)$it->is_active === 1
                && (!$start || $start->lte($now))
                && (!$end   || $end->gte($now));

            $times   = max(1, (int)($it->times ?? 1));
            $used    = max(0, (int)($it->used_count ?? 0));
            $left    = max(0, $times - $used);
            $usedPct = $times > 0 ? round($used / $times * 100) : 0;

            $uid      = 'sv' . ($loop->index ?? 0) . '_' . $it->id;
            $regions  = collect(json_decode($it->regions  ?? '[]', true) ?: []);
            $carriers = collect(json_decode($it->carriers ?? '[]', true) ?: []);
        @endphp

        <div class="gcard shadow-sm">
            @if($times > 1)
                <div class="badge-x" title="Số lần lưu">×{{ $times }}</div>
            @endif

            <div class="inner p-5">
                <div class="flex gap-4">

                    {{-- Icon vé xếp lớp --}}
                    <div class="relative w-12 h-12 shrink-0 mt-1">
                        @for($i = 0; $i < min(3, $times); $i++)
                        <svg width="48" height="48" viewBox="0 0 48 48"
                            class="absolute top-0 left-0 drop-shadow-sm"
                            style="transform: translate({{ $i*4 }}px, {{ -$i*4 }}px) rotate({{ -$i*2 }}deg)">
                            <defs>
                                <linearGradient id="g{{ $uid }}{{ $i }}" x1="0" x2="1" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#7dd3fc"/>
                                    <stop offset="100%" stop-color="#38bdf8"/>
                                </linearGradient>
                            </defs>
                            <path d="M8 10 h32 a2 2 0 0 1 2 2 v6 a4 4 0 0 0 0 12 v6 a2 2 0 0 1-2 2 H8 a2 2 0 0 1-2-2 v-6 a4 4 0 0 0 0-12 v-6 a2 2 0 0 1 2-2 z"
                                fill="url(#g{{ $uid }}{{ $i }})"/>
                            <path d="M16 10 v28 M32 10 v28"
                                stroke="rgba(255,255,255,.7)" stroke-width="2" stroke-dasharray="4 4"/>
                        </svg>
                        @endfor
                    </div>

                    <div class="min-w-0 flex-1">

                        {{-- Code + trạng thái --}}
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="voucher-code">{{ $it->code }}</span>
                            @if($statusOk)
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ Hiệu lực
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                    Hết hạn
                                </span>
                            @endif
                        </div>

                        {{-- Mô tả giảm giá --}}
                        <div class="text-sm text-gray-700 font-semibold mb-2">
                            {{ $isPercent ? 'Giảm' : 'Trừ' }} {{ $valueStr }} phí ship
                            @if($maxStr) <span class="font-normal text-gray-500">• {{ $maxStr }}</span>@endif
                        </div>

                        {{-- Điều kiện --}}
                        <div class="text-xs text-gray-500 mb-2">{{ $minStr }}</div>

                        {{-- Carriers / Regions chips --}}
                        @if($carriers->count() || $regions->count())
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            @if($carriers->count())
                                <span class="chip">🚚 {{ $carriers->take(2)->implode(', ') }}@if($carriers->count()>2) +{{ $carriers->count()-2 }}@endif</span>
                            @endif
                            @if($regions->count())
                                <span class="chip">📍 {{ $regions->take(2)->implode(', ') }}@if($regions->count()>2) +{{ $regions->count()-2 }}@endif</span>
                            @endif
                        </div>
                        @endif

                        {{-- Progress bar số lần dùng --}}
                        <div class="mb-1">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>Đã dùng: <b class="text-gray-600">{{ $used }}</b> / {{ $times }}</span>
                                @if($left > 0)
                                    <span class="text-emerald-600 font-semibold">Còn {{ $left }}</span>
                                @else
                                    <span class="text-red-400 font-semibold">Hết lượt</span>
                                @endif
                            </div>
                            <div class="usage-bar">
                                <div class="usage-bar-fill" style="width: {{ $usedPct }}%"></div>
                            </div>
                        </div>

                        {{-- Thời gian --}}
                        @if($start || $end)
                        <div class="text-xs text-gray-400 mt-2 mb-3">
                            @if($start)📅 {{ $start->format('d/m/Y') }}@endif
                            @if($end) → {{ $end->format('d/m/Y') }}@endif
                        </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex gap-2 mt-3">
                            <button
                                onclick="navigator.clipboard.writeText('{{ $it->code }}'); miniToast('✓ Đã copy mã {{ $it->code }}');"
                                class="flex-1 px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm font-medium transition text-center">
                                Copy mã
                            </button>
                            @if($statusOk && $left > 0)
                            <a href="{{ route('checkout.index') }}"
                                class="flex-1 px-3 py-2 rounded-lg bg-sky-500 hover:bg-sky-600 text-white text-sm font-semibold transition text-center">
                                Dùng ngay
                            </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @else
    {{-- Empty state --}}
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-20 h-20 rounded-full bg-sky-50 flex items-center justify-center mb-4 text-4xl">🚚</div>
        <h3 class="text-lg font-bold text-gray-700 mb-1">Chưa có mã vận chuyển</h3>
        <p class="text-gray-400 text-sm mb-6">Các mã giảm phí ship sẽ xuất hiện ở đây sau khi bạn nhận được.</p>
        <a href="{{ route('shop.index') }}"
            class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-semibold text-sm transition">
            Khám phá sản phẩm
        </a>
    </div>
    @endif
</div>

<script>
    function miniToast(text) {
        const el = document.createElement('div');
        el.className = 'mini-toast';
        el.textContent = text;
        document.body.appendChild(el);
        el.animate([
            { opacity: 0, transform: 'translateX(-50%) translateY(10px)' },
            { opacity: 1, transform: 'translateX(-50%) translateY(0)' }
        ], { duration: 180, fill: 'forwards' });
        setTimeout(() => {
            el.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 220, fill: 'forwards' })
              .onfinish = () => el.remove();
        }, 1500);
    }
</script>
@endsection