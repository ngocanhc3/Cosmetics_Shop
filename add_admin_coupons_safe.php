<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$admin = DB::table('users')->where('email','admin@cosme.local')->first();
if (!$admin) {
    echo "Admin user not found\n";
    exit;
}
$uid = $admin->id;

for ($i = 1; $i <= 10; $i++) {
    $code = 'ADMIN' . $i;
    $now  = Carbon::now();
    
    // 1. Get or Create Coupon
    $coupon = DB::table('coupons')->where('code', $code)->first();
    if ($coupon) {
        $couponId = $coupon->id;
        echo "Coupon $code exists (ID: $couponId)\n";
    } else {
        $couponId = DB::table('coupons')->insertGetId([
            'code'            => $code,
            'name'            => "Admin Coupon $i",
            'description'    => "Discount for admin $i",
            'discount_type'   => 'percent',
            'discount_value'  => 10,
            'max_discount'    => 50000,
            'min_order_total' => 0,
            'starts_at'       => $now,
            'ends_at'         => $now->copy()->addDays(30),
            'is_active'       => 1,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
        echo "Inserted $code (ID: $couponId)\n";
    }

    // 2. Assign to user if not already assigned
    $assignment = DB::table('user_coupons')
        ->where('user_id', $uid)
        ->where('coupon_id', $couponId)
        ->first();
    
    if ($assignment) {
        echo "  -> Already assigned to user $uid (Times: {$assignment->times})\n";
        // Ensure times > 0 so it shows up
        if ($assignment->times <= 0) {
            DB::table('user_coupons')
                ->where('id', $assignment->id)
                ->update(['times' => 1]);
            echo "  -> Updated times to 1\n";
        }
    } else {
        DB::table('user_coupons')->insert([
            'user_id'   => $uid,
            'coupon_id' => $couponId,
            'times'     => 1,
            'created_at'=> $now,
            'updated_at'=> $now,
        ]);
        echo "  -> Assigned to user $uid\n";
    }
}
?>
