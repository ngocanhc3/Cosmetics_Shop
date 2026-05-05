<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model ghi lại lịch sử mỗi lượt sử dụng mã giảm phí vận chuyển.
 * Dùng để đếm số lần dùng và kiểm tra giới hạn per_user_limit.
 *
 * @author Nguyen Thanh Nga
 */
class ShippingVoucherUsage extends Model
{
    protected $table   = 'shipping_voucher_usages';
    protected $guarded = [];

    /**
     * Quan hệ ngược: mỗi lượt dùng thuộc về một ShippingVoucher.
     * Dùng shipping_voucher_id làm khoá ngoại.
     */
    public function voucher()
    {
        return $this->belongsTo(ShippingVoucher::class, 'shipping_voucher_id');
    }
}