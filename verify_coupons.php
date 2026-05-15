<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$admin = DB::table('users')->where('email','admin@cosme.local')->first();
if (!$admin) { echo "User not found\n"; exit; }
$uid = $admin->id;

$res = DB::table('user_coupons as uc')
    ->join('coupons as c','c.id','=','uc.coupon_id')
    ->where('uc.user_id', $uid)
    ->select('c.code', 'uc.times')
    ->get();

echo "User $uid coupons:\n";
foreach($res as $r) {
    echo " - {$r->code} (Times: {$r->times})\n";
}
if ($res->isEmpty()) {
    echo "No coupons found for user $uid.\n";
}
?>
