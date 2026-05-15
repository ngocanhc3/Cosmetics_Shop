<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;


class CouponController extends Controller
{
    public function index(Request $r)
    {
        $uid = $r->user()->id;

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

        $groupColumns = [
            'c.id',
            'c.code',
            'c.name',
            $hasDescription ? 'c.description' : DB::raw('NULL'),
            'c.discount_type',
            $hasDiscountValue ? 'c.discount_value' : DB::raw('COALESCE(c.percent, c.amount)'),
            'c.max_discount',
            $hasMinOrderTotal ? 'c.min_order_total' : DB::raw('COALESCE(c.min_subtotal, 0)'),
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
                ->groupBy($groupColumns);
        }

        if (Schema::hasColumn('coupons', 'is_public')) {
            $publicCoupons = DB::table('coupons as c')
                ->where('c.is_active', 1)
                ->where('c.is_public', 1)
                ->select(array_merge($couponColumns, [DB::raw('1 as times')]));

            $publicCoupons->whereNotExists(function ($q2) use ($uid, $hasCouponId, $hasCouponCode) {
                $q2->select(DB::raw('1'))
                    ->from('user_coupons as uc2')
                    ->where('uc2.user_id', $uid)
                    ->where(function ($w) use ($hasCouponId, $hasCouponCode) {
                        if ($hasCouponId) {
                            $w->whereRaw('uc2.coupon_id = c.id');
                        }
                        if ($hasCouponCode) {
                            if ($hasCouponId) {
                                $w->orWhereRaw('uc2.coupon_code = c.code');
                            } else {
                                $w->whereRaw('uc2.coupon_code = c.code');
                            }
                        }
                    });
            });

            $q = $q->unionAll($publicCoupons);
        }

        $coupons = $q->orderBy('starts_at', 'desc')->get();
        return view('account.coupons.index', compact('coupons'));
    }
}
