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
    $now = Carbon::now();
    $couponId = DB::table('coupons')->insertGetId([
        'code' => $code,
        'name' => 'Admin Coupon '.$i,
        'description' => 'Discount for admin '.$i,
        'discount_type' => 'percent',
        'discount_value' => 10,
        'max_discount' => 50000,
        'min_order_total' => 0,
        'starts_at' => $now,
        'ends_at' => $now->copy()->addDays(30),
        'is_active' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    DB::table('user_coupons')->insert([
        'user_id' => $uid,
        'coupon_id' => $couponId,
        'times' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
    echo "Inserted $code\n";
}
?>
