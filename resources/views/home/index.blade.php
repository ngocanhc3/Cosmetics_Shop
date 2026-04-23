@extends('layouts.app')
@section('title','Cosme House')

@section('content')
@php
// Ưu tiên slides từ $banners; nếu không có dùng $heroSlides (fallback)
$slides = collect($banners ?? [])->map(function ($b) {
return [
'image' => $b->image ?? null,
'mobile_image' => $b->mobile_image ?? null,
'url' => $b->url ?: '#',
'title' => $b->title ?? '',
];
})->values()->all();

if (empty($slides)) {
$slides = $heroSlides ?? [
['image'=>null,'url'=>'#','title'=>'Banner 1'],
['image'=>null,'url'=>'#','title'=>'Banner 2'],
['image'=>null,'url'=>'#','title'=>'Banner 3'],
];
}
@endphp

{{-- ===== CUSTOM STYLES FOR HOMEPAGE ===== --}}
<style>
    :root {
        --primary: 248, 200, 220; /* #f8c8dc - hồng pastel */
        --primary-light: 253, 230, 240; /* #fde9f0 - hồng rất nhạt */
        --secondary: 236, 72, 153; /* #ec4899 - hồng đậm */
        --accent: 251, 191, 36; /* amber-400 */
        --neutral: 255, 255, 255; /* white */
        --text-dark: 17, 24, 39; /* gray-900 */
        --text-muted: 75, 85, 99; /* gray-600 */
    }

    .hero-section {
        background: linear-gradient(135deg, rgba(var(--primary), 0.05) 0%, rgba(var(--secondary), 0.03) 100%);
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(var(--primary), 0.1);
    }

    .section-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(var(--primary), 0.1);
        border-radius: 1.5rem;
        box-shadow: 0 8px 25px rgba(var(--primary), 0.08);
        transition: all 0.3s ease;
    }

    .section-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(var(--primary), 0.12);
    }

    .brand-item {
        transition: all 0.2s ease;
        border: 1px solid rgba(var(--primary), 0.2);
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(var(--primary-light), 0.1) 100%);
    }

    .brand-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(var(--primary), 0.15);
        border-color: rgb(var(--primary));
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .wave-container {
        position: relative;
        overflow: hidden;
    }

    .wave-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(var(--primary), 0.1), transparent);
        animation: wave 3s infinite;
    }

    @keyframes wave {
        0% { left: -100%; }
        50% { left: 100%; }
        100% { left: -100%; }
    }

    .flash-sale-badge {
        background: linear-gradient(135deg, rgb(var(--accent)) 0%, rgb(245, 158, 11) 100%);
        color: white;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: rgb(var(--text-dark));
        margin-bottom: 0.5rem;
    }

    .section-link {
        color: rgb(var(--primary));
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .section-link:hover {
        color: rgb(var(--secondary));
        text-decoration: underline;
    }

    .countdown-timer {
        background: linear-gradient(135deg, rgba(var(--primary), 0.1) 0%, rgba(var(--secondary), 0.1) 100%);
        color: rgb(var(--primary));
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .product-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
        }
    }
</style>

<section class="max-w-7xl mx-auto px-4 mt-6 space-y-12 relative">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute top-10 left-10 w-32 h-32 bg-sky-100 rounded-full blur-xl"></div>
        <div class="absolute top-40 right-20 w-24 h-24 bg-sky-100 rounded-full blur-xl"></div>
        <div class="absolute bottom-20 left-1/4 w-40 h-40 bg-sky-100 rounded-full blur-xl"></div>
        <div class="absolute bottom-40 right-10 w-28 h-28 bg-purple-100 rounded-full blur-xl"></div>
    </div>

    {{-- ========== HERO SLIDER ========== --}}
    <div x-data="heroCarousel({{ json_encode($slides) }})"
        x-init="init()"
        @mouseenter="pause()" @mouseleave="play()"
        @keydown.left.prevent="prev()" @keydown.right.prevent="next()"
        tabindex="0"
        class="hero-section focus:outline-none bg-gradient-to-r from-sky-50/30 to-pink-50/30">

        <div class="relative w-full h-0 pb-[36%] sm:pb-[28%]">
            <template x-for="(s,idx) in items" :key="idx">
                <a :href="s.url || '#'" class="absolute inset-0" x-show="i===idx" x-transition.opacity>
                    <picture>
                        <source media="(max-width: 640px)" :srcset="toUrl(s.mobile_image || s.image)">
                        <img :src="toUrl(s.image) || 'https://placehold.co/1600x576?text=Hero+Slide'"
                            :alt="s.title || ''"
                            class="w-full h-full object-cover">
                    </picture>
                </a>
            </template>
        </div>

        {{-- Dots --}}
        <div class="absolute bottom-4 left-0 right-0 flex items-center justify-center gap-2 z-20">
            <template x-for="k in items.length" :key="k">
                <button @click="go(k-1)" class="w-3 h-3 rounded-full transition-all duration-300 hover:scale-110"
                    :class="i===k-1 ? 'bg-white shadow-lg' : 'bg-white/60 hover:bg-white/80'"></button>
            </template>
        </div>

        {{-- Prev / Next (nằm trong banner) --}}
        <button @click="prev"
            class="absolute left-4 top-1/2 -translate-y-1/2 grid place-items-center w-12 h-12 rounded-full
                   bg-white/95 shadow-lg hover:bg-white hover:scale-105 transition-all duration-200 z-20
                   opacity-0 group-hover:opacity-100 sm:opacity-0 sm:group-hover:opacity-100">
            <i class="fa-solid fa-chevron-left text-gray-700"></i>
        </button>
        <button @click="next"
            class="absolute right-4 top-1/2 -translate-y-1/2 grid place-items-center w-12 h-12 rounded-full
                   bg-white/95 shadow-lg hover:bg-white hover:scale-105 transition-all duration-200 z-20
                   opacity-0 group-hover:opacity-100 sm:opacity-0 sm:group-hover:opacity-100">
            <i class="fa-solid fa-chevron-right text-gray-700"></i>
        </button>
    </div>

    {{-- ========== BRAND STRIP (lướt sóng) ========== --}}
    <div class="section-card p-6 wave-container bg-gradient-to-r from-sky-50/70 to-sky-100/70 border-sky-100/50">
        <h3 class="section-title text-center mb-4">Thương hiệu nổi tiếng</h3>
        <div class="flex items-center gap-4 overflow-x-auto snap-x snap-mandatory no-scrollbar"
            id="brandWave" data-wave=".js-brand">
            @forelse(($topBrands ?? []) as $b)
            @php
            $logo = $b->logo_url;
            @endphp
            <a href="{{ route('brand.show',$b->slug) }}"
                class="js-brand min-w-[160px] snap-start shrink-0 brand-item flex items-center gap-3 px-4 py-3
                          rounded-xl transition-all duration-200">
                @if($logo)
                <img src="{{ $logo }}" alt="{{ $b->name }}" class="w-12 h-12 object-contain rounded-lg"
                    onerror="this.onerror=null;this.src='https://placehold.co/60x60?text=IMG'">
                @else
                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-pink-100 to-pink-200 grid place-items-center font-bold text-sky-700">
                    {{ strtoupper(substr($b->name,0,1)) }}
                </div>
                @endif
                <span class="text-sm font-semibold text-gray-700">{{ $b->name }}</span>
            </a>
            @empty
            @for($i=0;$i<8;$i++)
                <div class="min-w-[160px] snap-start shrink-0 h-[64px] rounded-xl bg-gradient-to-r from-gray-100 to-gray-200 border border-gray-200">
                </div>
            @endfor
            @endforelse
        </div>
    </div>

    {{-- ========== FLASH SALE ========== --}}
    @if(($flashSale ?? collect())->count())
    <div class="section-card p-6 bg-gradient-to-r from-amber-50/70 to-cyan-50/70 border-sky-100/50">
        <div class="flex items-center justify-between mb-4">
            <h2 class="section-title flex items-center gap-2">
                <span class="flash-sale-badge px-3 py-1 rounded-full text-sm font-bold flex items-center gap-1">
                    <i class="fa-solid fa-bolt"></i> Flash Sale
                </span>
            </h2>
            <div x-data="countdown({{ now()->copy()->addHours(6)->timestamp }})"
                x-init="start()"
                class="countdown-timer px-4 py-2 rounded-lg text-sm font-semibold">
                <i class="fa-solid fa-clock mr-1"></i> Kết thúc sau: <span x-text="hhmmss" class="font-mono"></span>
            </div>
        </div>

        <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory no-scrollbar pb-2"
            id="flashWave" data-wave=".js-fs">
            @foreach($flashSale as $p)
            <div class="js-fs min-w-[200px] max-w-[200px] snap-start">
                <x-product-card :product="$p" />
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ========== GỢI Ý HÔM NAY (lướt sóng) ========== --}}
    @if(($suggested ?? collect())->count())
    <div class="section-card p-6 bg-gradient-to-r from-sky-50/70 to-sky-50/70 border-sky-100/50">
        <div class="flex items-center justify-between mb-4">
            <h2 class="section-title">Gợi ý hôm nay cho bạn</h2>
            <a href="{{ route('shop.index') }}" class="section-link text-sm">Xem tất cả <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>
        <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory no-scrollbar pb-2"
            id="suggestWave" data-wave=".js-sg">
            @foreach($suggested as $p)
            <div class="js-sg min-w-[200px] max-w-[200px] snap-start">
                <x-product-card :product="$p" />
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ========== LƯỚI “Sản phẩm mới” ========== --}}
    <div class="section-card p-6 bg-gradient-to-r from-green-50/70 to-teal-50/70 border-green-100/50">
        <div class="flex items-center justify-between mb-6">
            <h2 class="section-title">Sản phẩm mới nhất</h2>
            <a href="{{ route('shop.index') }}" class="section-link text-sm">Xem tất cả <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>
        <div class="product-grid">
            @forelse(($newProducts ?? []) as $p)
            <x-product-card :product="$p" />
            @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-400 mb-2">
                    <i class="fa-solid fa-box-open text-4xl"></i>
                </div>
                <p class="text-gray-500">Chưa có sản phẩm mới.</p>
            </div>
            @endforelse
        </div>
    </div>
    {{-- ====== FLAG + MODAL ====== --}}
    @php($__justLoggedIn = \Illuminate\Support\Facades\Session::has('just_logged_in'))
    <script>
        window.__JUST_LOGGED_IN__ = @json($__justLoggedIn);
    </script>

    @include('components.promo-modal', [
    'onlyWhenJustLoggedIn' => true,
    'posters' => [
    ['img'=>asset('images/promo/poster1.png'),'title'=>'SALE 50% – Skincare Hot','desc'=>'Giảm sâu cho bộ sưu tập chăm da bán chạy nhất.','cta'=>'Mua ngay','href'=>route('shop.sale')],
    ['img'=>asset('images/promo/poster2.png'),'title'=>'MUA 2 TẶNG 1 – Makeup','desc'=>'Săn deal son/phấn/cọ, số lượng có hạn.','cta'=>'Khám phá','href'=>route('shop.sale')],
    ['img'=>asset('images/promo/poster3.png'),'title'=>'Quay là trúng!','desc'=>'Thử vận may – nhận mã giảm giá tức thì.','cta'=>'Chơi ngay','href'=>route('spin.index')],
    ],
    ])

</section>


@push('scripts')
<script>
    /* ===== Hero carousel ===== */
    document.addEventListener('alpine:init', () => {
        Alpine.data('heroCarousel', (items) => ({
            items: Array.isArray(items) ? items : [],
            i: 0,
            timer: null,
            interval: 4500,

            toUrl(p) {
                if (!p) return '';
                p = String(p).trim().replace(/\\/g, '/');
                // đã là http(s) hoặc /storage
                if (/^https?:\/\//i.test(p)) return p;
                if (p.startsWith('/storage/')) return p;

                // chuẩn hoá đường dẫn lưu trong DB
                if (p.startsWith('public/')) p = p.replace(/^public\//, 'storage/');
                if (p.startsWith('storage/')) return '{{ url(' / ') }}/' + p;

                // còn lại: coi như trong storage/
                return '{{ url(' / ') }}/' + ('storage/' + p.replace(/^\/+/, ''));
            },

            init() {
                if (this.items.length > 1) this.play();
            },
            play() {
                this.pause();
                this.timer = setInterval(() => this.next(), this.interval);
            },
            pause() {
                if (this.timer) clearInterval(this.timer);
            },
            next() {
                this.i = (this.i + 1) % this.items.length;
            },
            prev() {
                this.i = (this.i - 1 + this.items.length) % this.items.length;
            },
            go(k) {
                this.i = k;
            }
        }));
    });

    /* ===== Countdown cho Flash Sale ===== */
    function countdown(targetTs) {
        return {
            hhmmss: '00:00:00',
            target: targetTs * 1000,
            timer: null,
            start() {
                const tick = () => {
                    const remain = this.target - Date.now();
                    if (remain <= 0) {
                        this.hhmmss = '00:00:00';
                        clearInterval(this.timer);
                        return;
                    }
                    const h = String(Math.floor(remain / 3600000)).padStart(2, '0');
                    const m = String(Math.floor(remain % 3600000 / 60000)).padStart(2, '0');
                    const s = String(Math.floor(remain % 60000 / 1000)).padStart(2, '0');
                    this.hhmmss = `${h}:${m}:${s}`;
                };
                tick();
                this.timer = setInterval(tick, 1000);
            }
        }
    }

    /* ===== Hiệu ứng “lướt sóng” – chỉ item hover + 2 hàng xóm ===== */
    function wave(group) {
        const selector = group.dataset.wave || '.card';
        const items = [...group.querySelectorAll(selector)];
        const shift = (el, dy) => el.style.transform = `translateY(${dy}px)`;
        const reset = (el) => el.style.transform = '';

        items.forEach((el, idx) => {
            el.addEventListener('mouseenter', () => {
                items.forEach(reset);
                shift(el, -8);
                if (items[idx - 1]) shift(items[idx - 1], -4);
                if (items[idx + 1]) shift(items[idx + 1], -4);
            });
            el.addEventListener('mouseleave', () => items.forEach(reset));
        });
    }

    /* Kích hoạt wave cho 3 cụm: Brand, Flash Sale, Gợi ý hôm nay */
    ['brandWave', 'flashWave', 'suggestWave'].forEach(id => {
        const el = document.getElementById(id);
        if (el) wave(el);
    });
</script>
@endpush

@endsection


