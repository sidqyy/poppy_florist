<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Material;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Observers\AuditObserver;
use App\Services\AuditService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function ($event) {
            AuditService::log('Login ke sistem', null, null, $event->user->id);
        });

        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                AuditService::log('Logout dari sistem', null, null, $event->user->id);
            }
        });

        // Register Audit Observers
        Order::observe(AuditObserver::class);
        Product::observe(AuditObserver::class);
        Material::observe(AuditObserver::class);
        Category::observe(AuditObserver::class);
        User::observe(AuditObserver::class);

        // Global Notifications for Navbar
        View::composer('layouts.navbar', function ($view) {
            $notifications = [];
            $unreadCount = 0;

            if (auth()->check()) {
                $role = auth()->user()->role;

                if (in_array($role, ['florist', 'admin', 'owner'])) {
                    $pendingOrders = Order::where('status', 'pending')->count();
                    if ($pendingOrders > 0) {
                        $notifications[] = [
                            'icon' => 'fa-clipboard-list',
                            'color' => 'text-blue-500',
                            'bg' => 'bg-blue-100',
                            'title' => 'Pesanan Menunggu',
                            'message' => "Ada $pendingOrders pesanan belum dirangkai.",
                            'time' => 'Baru saja',
                            'link' => route('kitchen.index'),
                        ];
                        $unreadCount++;
                    }

                    $lowStock = Material::where('stock', '<', 10)->count();
                    if ($lowStock > 0) {
                        $notifications[] = [
                            'icon' => 'fa-triangle-exclamation',
                            'color' => 'text-red-500',
                            'bg' => 'bg-red-100',
                            'title' => 'Peringatan Stok',
                            'message' => "Ada $lowStock bahan baku yang menipis.",
                            'time' => 'Penting',
                            'link' => ($role === 'admin') ? route('admin.materials.index') : '#',
                        ];
                        $unreadCount++;
                    }
                }

                if ($role === 'marketing' || $role === 'admin') {
                    $drafts = Product::where('availability', 'custom')->where('is_active', false)->count();
                    if ($drafts > 0) {
                        $notifications[] = [
                            'icon' => 'fa-file-signature',
                            'color' => 'text-purple-500',
                            'bg' => 'bg-purple-100',
                            'title' => 'Draft Custom',
                            'message' => "Ada $drafts draf buket custom tersimpan.",
                            'time' => 'Info',
                            'link' => route('custom.drafts'),
                        ];
                        $unreadCount++;
                    }
                }
            }

            $notifHash = md5(json_encode($notifications));
            $view->with('notifications', $notifications)->with('unreadCount', $unreadCount)->with('notifHash', $notifHash);
        });
    }
}
