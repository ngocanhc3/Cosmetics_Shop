@extends('admin.layouts.app')
@section('title','Mã vận chuyển')

@section('content')
@if(session('ok'))
<div class="alert alert-success mb-3" data-auto-dismiss="3000">{{ session('ok') }}</div>
@endif

{{-- Toolbar --}}
<div class="toolbar">
    <div class="toolbar-title">
        <i class="fa-solid fa-ticket text-sky-500 mr-2"></i>
        Quản lý mã vận chuyển
    </div>
    <div class="toolbar-actions">
        <a href="{{ route('admin.shipvouchers.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Tạo mã
        </a>
    </div>
</div>

@php
$qs = request()->except('page','status');
$cur = request('status');
@endphp

{{-- Tabs lọc nhanh --}}
<div class="card p-2 mb-3">
    <div class="flex flex-wrap gap-2">

        @php $active = ($cur===null || $cur===''); $count = (int)($counts['all'] ?? 0); @endphp
        <a href="{{ route('admin.shipvouchers.index', $qs) }}"
            class="btn btn-ghost btn-sm {{ $active ? 'bg-sky-600 text-white hover:bg-sky-600' : '' }}">
            Tất cả
            <span class="ml-1 text-xs rounded-full px-1.5 {{ $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $count }}
            </span>
        </a>

        @php $active = ($cur==='running'); $count = (int)($counts['running'] ?? 0); @endphp
        <a href="{{ route('admin.shipvouchers.index', array_merge($qs,['status'=>'running'])) }}"
            class="btn btn-ghost btn-sm {{ $active ? 'bg-sky-600 text-white hover:bg-sky-600' : '' }}">
            <i class="fa-solid fa-circle-play text-xs"></i> Đang diễn ra
            <span class="ml-1 text-xs rounded-full px-1.5 {{ $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $count }}
            </span>
        </a>

        @php $active = ($cur==='expired'); $count = (int)($counts['expired'] ?? 0); @endphp
        <a href="{{ route('admin.shipvouchers.index', array_merge($qs,['status'=>'expired'])) }}"
            class="btn btn-ghost btn-sm {{ $active ? 'bg-sky-600 text-white hover:bg-sky-600' : '' }}">
            <i class="fa-solid fa-clock text-xs"></i> Hết hạn
            <span class="ml-1 text-xs rounded-full px-1.5 {{ $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $count }}
            </span>
        </a>

        @php $active = ($cur==='active'); $count = (int)($counts['active'] ?? 0); @endphp
        <a href="{{ route('admin.shipvouchers.index', array_merge($qs,['status'=>'active'])) }}"
            class="btn btn-ghost btn-sm {{ $active ? 'bg-sky-600 text-white hover:bg-sky-600' : '' }}">
            <i class="fa-solid fa-toggle-on text-xs"></i> Đang bật
            <span class="ml-1 text-xs rounded-full px-1.5 {{ $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $count }}
            </span>
        </a>

        @php $active = ($cur==='inactive'); $count = (int)($counts['inactive'] ?? 0); @endphp
        <a href="{{ route('admin.shipvouchers.index', array_merge($qs,['status'=>'inactive'])) }}"
            class="btn btn-ghost btn-sm {{ $active ? 'bg-sky-600 text-white hover:bg-sky-600' : '' }}">
            <i class="fa-solid fa-toggle-off text-xs"></i> Đang tắt
            <span class="ml-1 text-xs rounded-full px-1.5 {{ $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $count }}
            </span>
        </a>
    </div>
</div>

{{-- Bộ lọc chi tiết --}}
<div class="card p-3 mb-3">
    <form method="get" class="grid md:grid-cols-5 gap-2 items-center">
        <div class="md:col-span-2">
            <input class="form-control" name="q" value="{{ request('q') }}"
                placeholder="🔍  Tìm theo mã / tên…">
        </div>

        <select class="form-control" name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="active"   @selected(request('status')==='active')>Đang bật</option>
            <option value="inactive" @selected(request('status')==='inactive')>Đang tắt</option>
            <option value="running"  @selected(request('status')==='running')>Đang diễn ra</option>
            <option value="expired"  @selected(request('status')==='expired')>Hết hạn</option>
        </select>

        <select class="form-control" disabled>
            <option selected>🚚 Mã vận chuyển</option>
        </select>

        <div class="flex items-center gap-2">
            <button class="btn btn-soft btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
            <a href="{{ route('admin.shipvouchers.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- Bảng --}}
<div class="card table-wrap p-0">
    <table class="table-admin">
        <thead>
            <tr>
                <th style="width:56px">#</th>
                <th style="width:22%">Mã / Tên</th>
                <th>Giảm</th>
                <th>Giới hạn</th>
                <th>Áp dụng</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th class="col-actions">Thao tác</th>
            </tr>
        </thead>

        <tbody>
            @forelse($items as $i => $v)
            @php
                $now = now();

                if ($v->isRunning()) {
                    $timeBadge = 'Đang diễn ra';
                    $timeCls   = 'badge-green';
                } elseif ($v->end_at && $v->end_at < $now) {
                    $timeBadge = 'Hết hạn';
                    $timeCls   = 'badge-red';
                } else {
                    $timeBadge = 'Chưa bắt đầu';
                    $timeCls   = 'badge-amber';
                }

                if ($v->discount_type === 'percent') {
                    $giam = rtrim(rtrim(number_format($v->amount, 2, '.', ''), '0'), '.') . '%';
                    if ($v->max_discount) {
                        $giam .= ' (tối đa ' . number_format($v->max_discount) . '₫)';
                    }
                } else {
                    $giam = number_format($v->amount) . '₫';
                }

                $startTxt = $v->start_at ? $v->start_at->format('d/m/Y H:i') : '—';
                $endTxt   = $v->end_at   ? $v->end_at->format('d/m/Y H:i')   : '—';

                $usedCount = (int)($usageCounts[$v->id] ?? 0);
                $usageLimit = $v->usage_limit ?? null;
                $usedPct = $usageLimit ? min(100, round($usedCount / $usageLimit * 100)) : 0;
            @endphp

            <tr class="hover:bg-sky-50/30 transition-colors">
                <td class="text-slate-400 text-xs">
                    {{ ($items->currentPage()-1)*$items->perPage() + $i + 1 }}
                </td>

                <td>
                    <div class="font-mono font-bold text-sky-700 tracking-wide">{{ $v->code }}</div>
                    <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $v->title }}</div>
                </td>

                <td>
                    <span class="font-semibold text-emerald-700">{{ $giam }}</span>
                </td>

                <td class="text-xs">
                    {{-- Progress bar lượt dùng --}}
                    <div class="flex items-center gap-1 mb-1">
                        <span class="text-slate-500">Dùng:</span>
                        <span class="font-semibold">{{ $usedCount }}</span>
                        <span class="text-slate-400">/ {{ $usageLimit ?? '∞' }}</span>
                    </div>
                    @if($usageLimit)
                    <div class="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-sky-400 transition-all"
                             style="width: {{ $usedPct }}%"></div>
                    </div>
                    @endif
                    <div class="text-slate-400 mt-1">Mỗi user: {{ $v->per_user_limit ?? '∞' }}</div>
                </td>

                <td class="text-xs">
                    @if($v->min_order)
                        <div>Đơn từ <b>{{ number_format($v->min_order) }}₫</b></div>
                    @endif
                    @if($v->carriers)
                        <div class="text-slate-500">🚚 {{ implode(', ', (array)$v->carriers) }}</div>
                    @endif
                </td>

                <td class="text-xs text-slate-500">
                    <div>{{ $startTxt }}</div>
                    <div class="text-slate-300">↓</div>
                    <div>{{ $endTxt }}</div>
                </td>

                <td>
                    <div class="flex items-center gap-1 flex-wrap">
                        <span class="badge {{ $v->is_active ? 'badge-green' : 'badge-red' }}">
                            {{ $v->is_active ? 'Bật' : 'Tắt' }}
                        </span>
                        <span class="badge {{ $timeCls }}">{{ $timeBadge }}</span>
                    </div>
                </td>

                <td class="col-actions">
                    <a class="btn btn-table btn-outline" href="{{ route('admin.shipvouchers.edit',$v) }}">
                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                    </a>

                    <form action="{{ route('admin.shipvouchers.toggle',$v) }}" method="post" class="inline">
                        @csrf @method('PATCH')
                        <label class="sv-switch" title="Bật/Tắt mã">
                            <input type="checkbox" {{ $v->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="sv-slider"></span>
                        </label>
                    </form>

                    <form class="inline" action="{{ route('admin.shipvouchers.destroy',$v) }}"
                        method="post" onsubmit="return confirm('Xoá mã {{ $v->code }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-table btn-danger">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="flex flex-col items-center py-10 text-slate-400">
                        <i class="fa-solid fa-ticket text-4xl mb-3 text-slate-200"></i>
                        <p class="text-sm">Chưa có mã vận chuyển nào.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination + summary --}}
<div class="flex items-center justify-between mt-2">
    <div class="text-sm text-slate-500">
        @if($items->total() > 0)
        Hiển thị
        <b>{{ ($items->currentPage()-1)*$items->perPage()+1 }}</b>
        –
        <b>{{ ($items->currentPage()-1)*$items->perPage()+$items->count() }}</b>
        / {{ $items->total() }} mã
        @endif
    </div>
    <div class="pagination">{{ $items->onEachSide(1)->links() }}</div>
</div>

{{-- Styles --}}
<style>
    .sv-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
        vertical-align: middle;
    }
    .sv-switch input { display: none; }
    .sv-slider {
        position: absolute;
        inset: 0;
        background: #e5e7eb;
        border-radius: 999px;
        transition: .2s;
    }
    .sv-slider:before {
        content: "";
        position: absolute;
        width: 18px;
        height: 18px;
        left: 2px;
        top: 2px;
        background: #fff;
        border-radius: 999px;
        box-shadow: 0 1px 2px rgba(0,0,0,.2);
        transition: .2s;
    }
    .sv-switch input:checked + .sv-slider { background: #0ea5e9; }
    .sv-switch input:checked + .sv-slider:before { transform: translateX(18px); }

    /* Row hover */
    tbody tr { transition: background .15s; }
</style>

<script>
    document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
        const t = +el.getAttribute('data-auto-dismiss') || 3000;
        setTimeout(() => {
            el.classList.add('alert--hide');
            setTimeout(() => el.remove(), 350);
        }, t);
    });
</script>
@endsection