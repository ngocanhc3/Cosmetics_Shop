{{-- components/header/mega-menu.blade.php --}}
@php
// Nhận dữ liệu từ include
$roots = $tree ?? collect();
$roots = $roots instanceof \Illuminate\Support\Collection
? $roots->values()->take(6)
: collect($roots)->values()->take(6);
@endphp

<ul class="flex items-center gap-4 whitespace-nowrap">
    @foreach($roots as $cat)
    {{-- pb-2 = hover bridge để không bị “rơi” hover khi di chuyển xuống panel --}}
    <li class="relative group pb-2">
        <a href="{{ route('category.show', $cat->slug) }}"
            class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-pink-50 hover:text-brand-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-pink-200">
            {{ $cat->name }}
            @if(!empty($cat->children) && count($cat->children))
            <i class="fa-solid fa-chevron-down text-[10px] opacity-70"></i>
            @endif
        </a>

        {{-- MEGA DROPDOWN --}}
        @if(!empty($cat->children) && count($cat->children))
        <div
            class="absolute left-0 top-full mt-3 w-[min(360px,calc(100vw-2rem))] bg-white border border-pink-100 rounded-[28px] shadow-card p-4
                 opacity-0 invisible translate-y-2 transition-all duration-200 ease-out
                 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0
                 z-[80]">
            <div class="space-y-4">
                @foreach($cat->children as $child)
                <div class="space-y-2">
                    <a href="{{ route('category.show', $child->slug) }}"
                        class="block text-sm font-semibold text-slate-900 hover:text-brand-600 transition">
                        {{ $child->name }}
                    </a>

                    @if(!empty($child->children) && count($child->children))
                    <ul class="space-y-1 ml-2 border-l border-pink-100 pl-3">
                        @foreach($child->children as $gchild)
                        <li>
                            <a href="{{ route('category.show', $gchild->slug) }}"
                                class="block rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition">
                                {{ $gchild->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </li>
    @endforeach
</ul>
