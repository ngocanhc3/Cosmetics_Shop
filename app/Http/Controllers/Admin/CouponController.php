<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CouponController extends Controller
{
    /** Danh sách + lọc cơ bản (ADMIN) */
    public function index(Request $r)
    {
        $now = now();

        // --- Nhận filters từ query ---
        $filters = [
            'keyword' => trim((string) $r->input('keyword', '')),
            'status'  => trim((string) $r->input('status', '')),   // active|inactive|ongoing|expired|''
            'type'    => trim((string) $r->input('type', '')),     // percent|fixed|''
        ];

        // --- Builder danh sách ---
        $q = Coupon::query();

        if ($filters['keyword'] !== '') {
            $kw = $filters['keyword'];
            $q->where(function ($qq) use ($kw) {
                $qq->where('code', 'like', "%{$kw}%")
                    ->orWhere('name', 'like', "%{$kw}%");
            });
        }

        if ($filters['type'] !== '') {
            $q->where('discount_type', $filters['type']);
        }

        switch ($filters['status']) {
            case 'active':
                $q->where('is_active', true);
                break;
            case 'inactive':
                $q->where('is_active', false);
                break;
            case 'ongoing':
                $q->where(function ($qq) use ($now) {
                    $qq->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })->where(function ($qq) use ($now) {
                    $qq->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                });
                break;
            case 'expired':
                $q->whereNotNull('ends_at')->where('ends_at', '<', $now);
                break;
        }

        // --- Đếm số lượt dùng (coupon_usages) ---
        $usageSub = DB::table('coupon_usages')
            ->select('coupon_id', DB::raw('COUNT(*) as redemptions_count'))
            ->groupBy('coupon_id');

        $coupons = $q->leftJoinSub($usageSub, 'u', function ($join) {
            $join->on('u.coupon_id', '=', 'coupons.id');
        })
            ->select('coupons.*', DB::raw('COALESCE(u.redemptions_count,0) as redemptions_count'))
            ->orderByDesc('coupons.created_at')
            ->paginate(20)
            ->withQueryString();

        // --- Counts cho tabs ---
        $countQ = Coupon::query();
        $counts = [
            'all'      => (clone $countQ)->count(),
            'active'   => (clone $countQ)->where('is_active', true)->count(),
            'inactive' => (clone $countQ)->where('is_active', false)->count(),
            'ongoing'  => (clone $countQ)
                ->where(function ($qq) use ($now) {
                    $qq->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })
                ->where(function ($qq) use ($now) {
                    $qq->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                })
                ->count(),
            'expired'  => (clone $countQ)->whereNotNull('ends_at')->where('ends_at', '<', $now)->count(),
        ];

        // 👉 Quan trọng: trả về view Admin
        return view('admin.coupons.index', compact('coupons', 'counts', 'filters'));
    }

    /** Form tạo */
    public function create()
    {
        $preselected = [];
        return view('admin.coupons.create', compact('preselected'));
    }

    /** Lưu mới */
    public function store(Request $r)
    {
        $data = $this->validateForm($r);
        $payload = $this->mapToPayload($data);

        $coupon = Coupon::create($payload);
        $this->syncTargets($coupon, $data['applies_to'], $data['applies_to_ids'] ?? []);

        return redirect()->route('admin.coupons.index')->with('ok', 'Tạo mã giảm giá thành công!');
    }

    /** Form sửa */
    public function edit(Coupon $coupon)
    {
        $existingIds = [];
        if (Schema::hasTable('coupon_targets')) {
            $existingIds = $coupon->targets()->pluck('target_id')->all();
        }

        $preselected = collect(old('applies_to_ids', $existingIds))
            ->map(fn($id) => (string) $id)->values()->all();

        return view('admin.coupons.edit', compact('coupon', 'preselected'));
    }

    /** Cập nhật */
    public function update(Request $r, Coupon $coupon)
    {
        $data = $this->validateForm($r, $coupon->id);
        $payload = $this->mapToPayload($data);

        $coupon->update($payload);
        $this->syncTargets($coupon, $data['applies_to'], $data['applies_to_ids'] ?? []);

        return redirect()->route('admin.coupons.edit', $coupon)->with('ok', 'Cập nhật mã giảm giá thành công!');
    }

    /** Xoá */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('ok', 'Đã xoá mã giảm giá.');
    }

    /** API TomSelect targets */
    public function targets(Request $r)
    {
        $type = $r->get('type');
        abort_unless(in_array($type, ['brand', 'category', 'product']), 422, 'Loại không hợp lệ');

        $ids  = (array) $r->input('ids', []);
        $q    = trim((string) $r->get('q', ''));
        $per  = max(1, min((int) $r->get('per', 20), 50));
        $page = max(1, (int) $r->get('page', 1));

        $builder = match ($type) {
            'brand'    => Brand::query()->select('id', 'name'),
            'category' => Category::query()->select('id', 'name'),
            'product'  => Product::query()->select('id', 'name'),
        };

        if (!empty($ids)) {
            $items = $builder->whereIn('id', $ids)->limit(200)->get();
            return response()->json([
                'results' => $items->map(fn($m) => ['value' => (string) $m->id, 'text' => $m->name])->values(),
            ]);
        }

        if ($q === '') return response()->json(['results' => []]);

        $items = $builder->where('name', 'like', "%{$q}%")
            ->orderBy('name')->forPage($page, $per)->get();

        return response()->json([
            'results' => $items->map(fn($m) => ['value' => (string) $m->id, 'text' => $m->name])->values(),
        ]);
    }

    /** Bật / tắt */
    public function toggle(Coupon $coupon)
    {
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();
        return back()->with('ok', $coupon->is_active ? 'Đã bật mã.' : 'Đã tắt mã.');
    }

    // =================== Helpers ===================

    protected function validateForm(Request $r, $ignoreId = null): array
    {
        return $r->validate([
            'code'               => ['required', 'string', 'max:64', Rule::unique('coupons', 'code')->ignore($ignoreId)],
            'name'               => ['nullable', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'is_active'          => ['required', Rule::in(['0', '1', 0, 1])],

            'type'               => ['required', Rule::in(['percent', 'fixed'])],
            'value'              => ['required', 'string'],

            'max_discount'       => ['nullable', 'string'],
            'min_order_value'    => ['nullable', 'string'],

            'usage_limit'        => ['nullable', 'integer', 'min:0'],
            'per_customer_limit' => ['nullable', 'integer', 'min:0'],

            'applies_to'         => ['required', Rule::in(['order', 'category', 'brand', 'product'])],
            'applies_to_ids'     => ['required_if:applies_to,category,brand,product', 'array'],
            'applies_to_ids.*'   => ['integer'],

            'start_at'           => ['nullable', 'date'],
            'end_at'             => ['nullable', 'date', 'after_or_equal:start_at'],
        ]);
    }

    /** Map dữ liệu form sang cột DB (đúng schema hiện tại) */
    protected function mapToPayload(array $data): array
    {
        $percent = $this->toFloat($data['value']);
        $fixed   = $this->toInt($data['value']);

        $payload = [
            'code'                 => strtoupper(trim($data['code'])),
            'name'                 => $data['name'] ?? null,
            'is_active'            => (bool) $data['is_active'],

            'discount_type'        => $data['type'],
            'percent'              => $data['type'] === 'percent' ? max(0, min(100, $percent)) : null,
            'amount'               => $data['type'] === 'fixed' ? $fixed : null,
            'max_discount'         => $this->toInt($data['max_discount'] ?? null),
            'min_subtotal'         => $this->toInt($data['min_order_value'] ?? null),

            'usage_limit'          => isset($data['usage_limit']) ? (int) $data['usage_limit'] : null,
            'per_user_limit'       => isset($data['per_customer_limit']) ? (int) $data['per_customer_limit'] : null,

            'apply_scope'          => $data['applies_to'] === 'order' ? 'order' : 'item',

            'starts_at'            => !empty($data['start_at']) ? Carbon::parse($data['start_at']) : null,
            'ends_at'              => !empty($data['end_at'])   ? Carbon::parse($data['end_at'])   : null,
        ];

        if (Schema::hasColumn('coupons', 'description')) {
            $payload['description'] = $data['description'] ?? null;
        }

        return $payload;
    }

    protected function syncTargets(Coupon $coupon, string $appliesTo, array $ids): void
    {
        if (! Schema::hasTable('coupon_targets')) {
            return;
        }

        $coupon->targets()->delete();

        if ($appliesTo === 'order') {
            return;
        }

        $targetIds = collect($ids)
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        foreach ($targetIds as $targetId) {
            $coupon->targets()->create([
                'target_type' => $appliesTo,
                'target_id' => $targetId,
                'is_excluded' => false,
            ]);
        }
    }

    protected function toInt($v): ?int
    {
        if ($v === null || $v === '') return null;
        $s = preg_replace('/\D+/', '', (string) $v);
        return $s === '' ? 0 : (int) $s;
    }

    protected function toFloat($v): float
    {
        $s = str_replace(',', '.', (string) $v);
        $s = preg_replace('/[^0-9.]/', '', $s);
        if ($s === '' || $s === '.') return 0.0;
        return (float) $s;
    }
}
