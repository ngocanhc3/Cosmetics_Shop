<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$uid = 3;

$hasTimes         = Schema::hasColumn('user_coupons', 'times');
$hasCouponId      = Schema::hasColumn('user_coupons', 'coupon_id');
$hasCouponCode    = Schema::hasColumn('user_coupons', 'coupon_code');
$hasDescription   = Schema::hasColumn('coupons', 'description');
$hasDiscountValue = Schema::hasColumn('coupons', 'discount_value');
$hasMinOrderTotal = Schema::hasColumn('coupons', 'min_order_total');

$couponColumns = [
    'c.id as coupon_id',
    'c.code',
    'c.name',
    $hasDescription ? 'c.description' : DB::raw('NULL as description'),
    'c.discount_type',
    $hasDiscountValue ? 'c.discount_value' : DB::raw('COALESCE(c.percent, c.amount) as discount_value'),
    'c.max_discount',
    $hasMinOrderTotal ? 'c.min_order_total' : DB::raw('COALESCE(c.min_subtotal, 0) as min_order_total'),
    'c.starts_at',
    'c.ends_at',
    'c.is_active',
];

$q = DB::table('user_coupons as uc')
    ->join('coupons as c', function ($j) use ($hasCouponId, $hasCouponCode) {
        if ($hasCouponId) {
            $j->on('c.id', '=', 'uc.coupon_id');
            if ($hasCouponCode) {
                $j->orOn('c.code', '=', 'uc.coupon_code');
            }
        } elseif ($hasCouponCode) {
            $j->on('c.code', '=', 'uc.coupon_code');
        }
    })
    ->where('uc.user_id', $uid);

if ($hasTimes) {
    $q->select(array_merge($couponColumns, ['uc.times']))
        ->where('uc.times', '>', 0);
} else {
    $q->select(array_merge($couponColumns, [DB::raw('COUNT(*) as times')]))
        ->groupBy('c.id', 'c.code', 'c.name', 'c.discount_type', 'c.max_discount', 'c.starts_at', 'c.ends_at', 'c.is_active');
}

$coupons = $q->orderBy('starts_at', 'desc')->get();

echo "Query returned " . $coupons->count() . " coupons.\n";
foreach($coupons as $c) {
    echo " - {$c->code} (ID: {$c->coupon_id})\n";
}
?>
