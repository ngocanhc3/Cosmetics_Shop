<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use App\Models\Setting;
use App\Models\Category;
use App\Models\ProductReview;
use App\Models\Order;
use App\Observers\OrderObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1) Đăng ký Observer (bọc try để tránh lỗi DB)
        try {
            Order::observe(OrderObserver::class);
        } catch (\Throwable $e) {
            // bỏ qua nếu DB chưa sẵn sàng
        }

        // 2) Super-admin bypass
        Gate::before(function ($user, $ability) {
            return method_exists($user, 'hasRole') && $user->hasRole('super-admin') ? true : null;
        });

        // 3) Runtime settings (check DB + table)
        if ($this->canUseDatabase() && Schema::hasTable('settings')) {
            try {
                Setting::syncRuntime();
            } catch (\Throwable $e) {
                // bỏ qua lỗi
            }
        }

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // 4) View composer (bọc toàn bộ DB)
        View::composer('*', function ($view) {

            // Nếu chưa có DB → trả dữ liệu rỗng
            if (!$this->canUseDatabase()) {
                $view->with([
                    'headerCats' => collect(),
                    'megaTree' => collect(),
                    'wishlistCount' => 0,
                    'cartCount' => 0,
                    'pendingReviewsCount' => 0
                ]);
                return;
            }

            try {
                $headerCats = Cache::remember('headerCats:withChildren:v1', 3600, function () {
                    return Category::query()
                        ->select('id', 'name', 'slug')
                        ->whereNull('parent_id')->where('is_active', 1)
                        ->orderByRaw('COALESCE(sort_order,999999), name')
                        ->with(['children' => function ($q) {
                            $q->select('id', 'name', 'slug', 'parent_id')
                                ->where('is_active', 1)
                                ->orderByRaw('COALESCE(sort_order,999999), name')
                                ->with(['children' => function ($qq) {
                                    $qq->select('id', 'name', 'slug', 'parent_id')
                                        ->where('is_active', 1)
                                        ->orderByRaw('COALESCE(sort_order,999999), name');
                                }]);
                        }])->get();
                });

                $topCount   = 6;
                $megaTree   = $headerCats->take($topCount);
                $wishlistCount = (int) (is_array(session('wishlist')) ? count(session('wishlist')) : 0);
                $cartItems  = session('cart.items', []);
                $cartCount  = (int) collect($cartItems)->sum('qty');

                $pendingReviewsCount = ProductReview::where('approved', false)->count();

                $view->with(compact(
                    'headerCats',
                    'megaTree',
                    'wishlistCount',
                    'cartCount',
                    'pendingReviewsCount'
                ));

            } catch (\Throwable $e) {
                // fallback nếu query lỗi
                $view->with([
                    'headerCats' => collect(),
                    'megaTree' => collect(),
                    'wishlistCount' => 0,
                    'cartCount' => 0,
                    'pendingReviewsCount' => 0
                ]);
            }
        });
    }

    /**
     * Kiểm tra DB có dùng được không
     */
    private function canUseDatabase(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}