<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('coupons')) {
            return;
        }

        Schema::table('coupons', function (Blueprint $table) {
            if (! Schema::hasColumn('coupons', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            if (! Schema::hasColumn('coupons', 'discount_value')) {
                $table->unsignedBigInteger('discount_value')->nullable()->after('discount_type');
            }

            if (! Schema::hasColumn('coupons', 'min_order_total')) {
                $table->unsignedBigInteger('min_order_total')->nullable()->after('max_discount');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('coupons')) {
            return;
        }

        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('coupons', 'discount_value')) {
                $table->dropColumn('discount_value');
            }

            if (Schema::hasColumn('coupons', 'min_order_total')) {
                $table->dropColumn('min_order_total');
            }
        });
    }
};
