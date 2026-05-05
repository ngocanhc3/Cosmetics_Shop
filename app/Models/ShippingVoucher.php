<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Model đại diện cho mã giảm phí vận chuyển.
 * Hỗ trợ giảm theo số tiền cố định hoặc phần trăm,
 * giới hạn thời gian, số lượt dùng và đối tượng áp dụng.
 *
 * @author Nguyen Thanh Nga
 */
class ShippingVoucher extends Model
{
    protected $table   = 'shipping_vouchers';
    protected $guarded = [];

    /** Ép kiểu tự động cho các trường đặc biệt */
    protected $casts = [
        'regions'        => 'array',
        'carriers'       => 'array',
        'is_active'      => 'boolean',
        'start_at'       => 'datetime',
        'end_at'         => 'datetime',
        'usage_limit'    => 'integer',
        'per_user_limit' => 'integer',
        'amount'         => 'integer',
        'max_discount'   => 'integer',
        'min_order'      => 'integer',
    ];

    /**
     * Scope: lọc các voucher đang còn hiệu lực.
     * Điều kiện: is_active = 1, chưa hết hạn, đã đến thời điểm bắt đầu.
     */
    public function scopeValid(Builder $q): Builder
    {
        $now = Carbon::now();
        return $q->where('is_active', 1)
            ->where(fn($qq) => $qq->whereNull('start_at')->orWhere('start_at', '<=', $now))
            ->where(fn($qq) => $qq->whereNull('end_at')->orWhere('end_at', '>=', $now));
    }

    /**
     * Scope: lọc voucher áp dụng được cho một user cụ thể.
     * Voucher không gắn user_id thì ai cũng dùng được.
     */
    public function scopeForUser(Builder $q, $user): Builder
    {
        if (!$user) return $q;
        return $q->where(fn($qq) => $qq->whereNull('user_id')->orWhere('user_id', $user->id));
    }

    /**
     * Quan hệ 1-nhiều: một voucher có nhiều lượt sử dụng.
     */
    public function usages()
    {
        return $this->hasMany(ShippingVoucherUsage::class);
    }

    /**
     * Trả về chuỗi mô tả mức giảm giá dạng đọc được.
     * Ví dụ: "Giảm 20% tối đa 50.000đ" hoặc "Giảm 30.000đ".
     */
    public function discountText(): string
    {
        if ($this->discount_type === 'percent') {
            $txt = "Giảm {$this->amount}%";
            if ($this->max_discount)
                $txt .= ' tối đa ' . number_format($this->max_discount, 0, ',', '.') . 'đ';
            return $txt;
        }
        return 'Giảm ' . number_format($this->amount, 0, ',', '.') . 'đ';
    }

    /**
     * Kiểm tra voucher có đang chạy thực tế không.
     * Kết hợp cả is_active, start_at và end_at.
     */
    public function isRunning(): bool
    {
        $now = now();
        $ok  = !$this->start_at || $this->start_at <= $now;
        $ok  = $ok && (!$this->end_at || $this->end_at >= $now);
        return $this->is_active && $ok;
    }
}