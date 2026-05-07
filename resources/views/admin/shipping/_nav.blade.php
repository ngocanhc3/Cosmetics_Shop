{{-- resources/views/admin/shipping/_nav.blade.php --}}
<nav class="mb-5">
    <div id="shipTabs"
        class="relative inline-flex gap-1 rounded-2xl border border-slate-200 bg-slate-50 p-1 shadow-sm">

        {{-- Indicator di chuyển mượt --}}
        <span id="shipTabIndicator"
            class="pointer-events-none absolute top-1 left-1 z-0 h-[36px] rounded-xl
                   bg-white ring-1 ring-sky-200 shadow-md
                   transition-all duration-300 ease-out"></span>

        <a href="{{ route('admin.shipping.carriers.index') }}"
            class="ship-tab relative z-[1] overflow-hidden px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-medium
                   transition-[color,transform] duration-200 hover:scale-[1.03]
                   {{ request()->routeIs('admin.shipping.carriers.*') ? 'is-active text-sky-700' : 'text-slate-500' }}">
            <i class="fa-solid fa-truck text-xs"></i>
            <span>Đơn vị</span>
        </a>

        <a href="{{ route('admin.shipping.zones.index') }}"
            class="ship-tab relative z-[1] overflow-hidden px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-medium
                   transition-[color,transform] duration-200 hover:scale-[1.03]
                   {{ request()->routeIs('admin.shipping.zones.*') ? 'is-active text-sky-700' : 'text-slate-500' }}">
            <i class="fa-solid fa-location-dot text-xs"></i>
            <span>Khu vực</span>
        </a>

        <a href="{{ route('admin.shipping.rates.index') }}"
            class="ship-tab relative z-[1] overflow-hidden px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-medium
                   transition-[color,transform] duration-200 hover:scale-[1.03]
                   {{ request()->routeIs('admin.shipping.rates.*') ? 'is-active text-sky-700' : 'text-slate-500' }}">
            <i class="fa-solid fa-scale-balanced text-xs"></i>
            <span>Biểu phí</span>
        </a>

        <a href="{{ route('admin.shipvouchers.index') }}"
            class="ship-tab relative z-[1] overflow-hidden px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-medium
                   transition-[color,transform] duration-200 hover:scale-[1.03]
                   {{ request()->routeIs('admin.shipvouchers.*') ? 'is-active text-sky-700' : 'text-slate-500' }}">
            <i class="fa-solid fa-ticket text-xs"></i>
            <span>Mã voucher</span>
        </a>
    </div>
</nav>

@once
@push('scripts')
<style>
    .ship-tab {
        background-color: transparent !important;
    }

    .ship-tab:hover {
        background-color: transparent !important;
    }

    /* Active tab đậm hơn */
    .ship-tab.is-active {
        font-weight: 600;
    }

    /* Ripple */
    .ship-tab .ripple {
        position: absolute;
        border-radius: 9999px;
        background: rgba(14, 165, 233, .15);
        transform: scale(0);
        animation: ship-ripple .6s ease-out forwards;
        pointer-events: none;
    }

    @keyframes ship-ripple {
        to {
            transform: scale(2.6);
            opacity: 0;
        }
    }
</style>

<script>
    (() => {
        const wrap = document.getElementById('shipTabs');
        if (!wrap) return;

        const indicator = document.getElementById('shipTabIndicator');
        const tabs = [...wrap.querySelectorAll('.ship-tab')];

        function moveIndicator(el, { animate = true } = {}) {
            const r  = el.getBoundingClientRect();
            const rw = wrap.getBoundingClientRect();
            indicator.classList.toggle('transition-none', !animate);
            indicator.style.width     = r.width + 'px';
            indicator.style.transform = `translateX(${r.left - rw.left}px)`;
        }

        const active = tabs.find(t => t.classList.contains('is-active')) || tabs[0];

        tabs.forEach((t, i) => t.addEventListener('click', () => {
            try { sessionStorage.setItem('shipTabPrevIndex', i); } catch (e) {}
        }));

        const prevIdx = parseInt(sessionStorage.getItem('shipTabPrevIndex') ?? '-1', 10);
        if (!isNaN(prevIdx) && prevIdx >= 0 && tabs[prevIdx]) {
            moveIndicator(tabs[prevIdx], { animate: false });
            requestAnimationFrame(() => moveIndicator(active, { animate: true }));
        } else {
            moveIndicator(active, { animate: true });
        }

        tabs.forEach(t => {
            t.addEventListener('mouseenter', (e) => {
                const d      = Math.max(t.clientWidth, t.clientHeight);
                const circle = document.createElement('span');
                circle.className = 'ripple';
                circle.style.width = circle.style.height = d + 'px';
                const rect = t.getBoundingClientRect();
                circle.style.left = (e.clientX - rect.left - d / 2) + 'px';
                circle.style.top  = (e.clientY - rect.top  - d / 2) + 'px';
                t.appendChild(circle);
                setTimeout(() => circle.remove(), 600);
                moveIndicator(t);
            });
            t.addEventListener('mouseleave', () => moveIndicator(active));
        });
    })();
</script>
@endpush
@endonce