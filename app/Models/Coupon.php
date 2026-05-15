<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Coupon extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'apply_scope',     // order | item | shipping
        'discount_type',   // percent | fixed | free_shipping
        'percent',
        'amount',
        'min_subtotal',
        'max_discount',
        'shipping_cap',
        'usage_limit',
        'per_user_limit',
        'first_order_only',
        'require_logged_in',
        'stacking',
        'exclude_sale_items',
        'starts_at',
        'ends_at',
        'is_active',
    ];
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_active' => 'bool',
        'min_subtotal' => 'int',
        'max_discount' => 'int',
        'percent' => 'int',
        'amount' => 'int',
        'usage_limit' => 'int',
        'per_user_limit' => 'int',
        'first_order_only' => 'bool',
        'require_logged_in' => 'bool',
        'exclude_sale_items' => 'bool',
        'target_brands'     => 'array',
        'target_categories' => 'array',
        'target_products'   => 'array',
    ];

    // ===== Relations (nếu có bảng, tự nhận; không có thì vẫn chạy bình thường) =====
    public function targets(): HasMany
    {
        return $this->hasMany(CouponTarget::class);
    }
    public function codes(): HasMany
    {
        return $this->hasMany(CouponCode::class);
    }
    public function uses(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }
    public function redemptions()
    {
        return $this->hasMany(\App\Models\CouponRedemption::class);
    }
    protected static function booted()
    {
        static::saving(function ($m) {
            if (isset($m->code) && $m->code) {
                $m->code = strtoupper($m->code);
            }
        });
    }

    // ===== “Bắc cầu” schema cũ -> thuộc tính mới (để không phải sửa DB ngay) =====
    public function getAmountAttribute($v): int
    {
        if (!is_null($v)) return (int)$v;
        // schema cũ: discount_type=fixed + discount_value
        if (($this->attributes['discount_type'] ?? null) === 'fixed') {
            return (int)($this->attributes['discount_value'] ?? 0);
        }
        return 0;
    }

    public function getPercentAttribute($v): float
    {
        if (!is_null($v)) return (float)$v;
        // schema cũ: discount_type=percent + discount_value
        if (($this->attributes['discount_type'] ?? null) === 'percent') {
            return (float)($this->attributes['discount_value'] ?? 0);
        }
        return 0.0;
    }

    public function getDiscountValueAttribute($v)
    {
        if (!is_null($v)) return $v;
        if (($this->attributes['discount_type'] ?? '') === 'percent') {
            return (float)($this->attributes['percent'] ?? 0);
        }
        return (int)($this->attributes['amount'] ?? 0);
    }

    public function getMinOrderTotalAttribute($v): int
    {
        if (!is_null($v)) return (int)$v;
        return (int)($this->attributes['min_subtotal'] ?? 0);
    }

    public function getAppliedToAttribute($v): string
    {
        if (!is_null($v)) return $v;
        if (($this->attributes['apply_scope'] ?? 'order') === 'order') {
            return 'order';
        }

        if (! Schema::hasTable('coupon_targets')) {
            return 'product';
        }

        if ($this->relationLoaded('targets') && $this->targets->isNotEmpty()) {
            return (string)$this->targets->first()->target_type;
        }

        $firstTarget = $this->targets()->first();
        if ($firstTarget) {
            return (string)$firstTarget->target_type;
        }

        return 'product';
    }

    public function getUsageLimitPerUserAttribute($v): int
    {
        if (!is_null($v)) return (int)$v;
        return (int)($this->attributes['per_user_limit'] ?? 0);
    }

    public function getMinSubtotalAttribute($v): int
    {
        if (!is_null($v)) return (int)$v;
        return (int)($this->attributes['min_order_total'] ?? 0);
    }

    public function getApplyScopeAttribute($v): string
    {
        if (!is_null($v)) return (string)$v;
        $applied = $this->attributes['applied_to'] ?? 'order';
        return $applied === 'order' ? 'order' : 'item';
    }

    // ===== State helpers =====
    public function isCurrentlyActive(): bool
    {
        if (!(bool)($this->attributes['is_active'] ?? true)) return false;
        $now = now();
        $start = $this->starts_at ? \Illuminate\Support\Carbon::parse($this->starts_at) : null;
        $end   = $this->ends_at   ? \Illuminate\Support\Carbon::parse($this->ends_at)   : null;
        if ($start && $now->lt($start)) return false;
        if ($end && $now->gt($end))     return false;
        return true;
    }
}
