<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model đại diện cho biểu phí vận chuyển của từng hãng theo khu vực.
 * Mỗi biểu phí gắn với một carrier và một zone, định nghĩa mức phí
 * dựa trên trọng lượng, giá trị đơn hàng và thời gian giao dự kiến.
 *
 * @author Nguyen Thanh Nga
 */
class ShippingRate extends Model
{
    /** Các trường được phép gán hàng loạt */
    protected $fillable = [
        'carrier_id', 'zone_id', 'name',
        'min_weight', 'max_weight',
        'min_total', 'max_total',
        'base_fee', 'per_kg_fee',
        'etd_min_days', 'etd_max_days',
        'enabled'
    ];

    /** Ép kiểu tự động: enabled thành boolean, trọng lượng thành float */
    protected $casts = [
        'enabled'    => 'boolean',
        'min_weight' => 'float',
        'max_weight' => 'float',
    ];

    /**
     * Quan hệ ngược: biểu phí thuộc về một hãng vận chuyển (ShippingCarrier).
     */
    public function carrier()
    {
        return $this->belongsTo(ShippingCarrier::class);
    }

    /**
     * Quan hệ ngược: biểu phí thuộc về một khu vực vận chuyển (ShippingZone).
     */
    public function zone()
    {
        return $this->belongsTo(ShippingZone::class);
    }
}