<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            DefaultUserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            // ProductImageSeeder::class, // nếu có
            OrderSeeder::class,
            CouponSeeder::class,
            SettingSeeder::class,
            SampleOrdersSeeder::class,
            OneOrderSeeder::class,
            PaymentMethodSeeder::class,
            WheelSeeder::class,
        ]);
    }
}
