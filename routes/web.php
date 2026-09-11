<?php

use App\Http\Controllers\Admin\ArrangementServiceController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CustomBucketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Marketing\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderImageController;
use App\Http\Controllers\OrderRevisionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Middleware\CheckPosFlorist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Push Notification Routes
Route::post('/api/push/subscribe', [PushNotificationController::class, 'subscribe'])->middleware('auth');
Route::post('/api/push/unsubscribe', [PushNotificationController::class, 'unsubscribe'])->middleware('auth');
Route::post('/api/push/notify-florist', [PushNotificationController::class, 'notifyFlorist'])->middleware(['auth', 'role:admin,asmen']);

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = auth()->user();
            $role = $user->role;

            if (in_array($role, ['asmen', 'it support'])) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/'.$role);
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    });
});

Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/login', function () {
        return view('pos.login');
    })->name('login');

    Route::post('/login', [PosController::class, 'login'])->name('login.post');
    Route::post('/logout', [PosController::class, 'logout'])->name('logout');

    Route::middleware([CheckPosFlorist::class])->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/kiosk', [PosController::class, 'kiosk'])->name('kiosk');
        Route::get('/catalog', [PosController::class, 'catalog'])->name('catalog');
        Route::get('/materials/{type}', [PosController::class, 'materials'])->name('materials');
        Route::get('/custom', [PosController::class, 'custom'])->name('custom');

        Route::post('/cart/add', [PosController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/add-variant-product', [PosController::class, 'addVariantProductToCart'])->name('cart.add-variant-product');
        Route::post('/cart/add-material', [PosController::class, 'addMaterialToCart'])->name('cart.add-material');
        Route::post('/cart/add-custom', [PosController::class, 'addCustomToCart'])->name('cart.add-custom');
        Route::post('/cart/add-multiple-materials', [PosController::class, 'addMultipleMaterialsToCart'])->name('cart.add-multiple-materials');

        Route::post('/cart/update', [PosController::class, 'updateCart'])->name('cart.update');
        Route::post('/cart/remove', [PosController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/cart/clear', [PosController::class, 'clearCart'])->name('cart.clear');

        Route::post('/store', [PosController::class, 'store'])->name('store');
    });
});

// API Ongkir & Promo
Route::get('/api/calculate-ongkir', [OrderController::class, 'calculateOngkir'])->name('api.ongkir');
Route::get('/api/check-promo', [OrderController::class, 'checkPromo'])->name('api.promo');
Route::get('/api/check-new-orders', [OrderController::class, 'checkNewOrders'])->name('api.new_orders');

// Print Receipt
Route::get('/orders/{id}/print', [OrderController::class, 'printReceipt'])->name('orders.print');

Route::middleware('auth')->group(function () {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');

    Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');

    Route::get('/custom-bucket', [CustomBucketController::class, 'index'])->name('custom.index');
    Route::get('/custom-bucket/drafts', [CustomBucketController::class, 'drafts'])->name('custom.drafts');
    Route::post('/custom-bucket/store', [CustomBucketController::class, 'store'])->name('custom.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/checkout/{product_id}', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    Route::get('/orders/online', [OrderController::class, 'createOnline'])->name('orders.online.create');
    Route::post('/orders/online', [OrderController::class, 'storeOnline'])->name('orders.online.store');
    Route::get('/orders/online/{id}/edit', [OrderController::class, 'editOnline'])->name('orders.online.edit');
    Route::put('/orders/online/{id}', [OrderController::class, 'updateOnline'])->name('orders.online.update');

    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::put('/orders/{id}/florist-notes', [OrderController::class, 'updateFloristNotes'])->name('orders.updateFloristNotes');

    Route::get('/kitchen', [OrderController::class, 'kitchen'])->name('kitchen.index');

    Route::get('/orders/{id}/revision', [OrderRevisionController::class, 'editComponents'])->name('orders.revision.edit');
    Route::post('/orders/{id}/components', [OrderRevisionController::class, 'storeComponent'])->name('orders.revision.storeComponent');
    Route::delete('/orders/{id}/components/{componentId}', [OrderRevisionController::class, 'deleteComponent'])->name('orders.revision.deleteComponent');

    Route::post('/orders/{id}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::put('/payments/{id}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::put('/payments/{id}/upload-proof', [PaymentController::class, 'uploadProof'])->name('payments.upload_proof');

    Route::post('/orders/{id}/images', [OrderImageController::class, 'store'])->name('orders.images.store');
    Route::delete('/order-images/{id}', [OrderImageController::class, 'destroy'])->name('orders.images.destroy');

    Route::middleware('role:admin,asmen,it support')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::resource('promos', PromoController::class)->except(['show']);
        Route::resource('categories', CategoryController::class);
        Route::resource('materials', MaterialController::class);
        Route::resource('arrangement-services', ArrangementServiceController::class)->except(['show']);
        Route::resource('stocks', StockController::class)->only(['index', 'create', 'store']);

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/run', [BackupController::class, 'runBackup'])->name('backups.run');
        Route::get('/backups/download/{file}', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{file}', [BackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('/backups/restore', [BackupController::class, 'restore'])->name('backups.restore');

        Route::get('/orders/export/excel', [OrderController::class, 'exportExcel'])->name('orders.export.excel');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::middleware('role:marketing')->prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/', [DashboardController::class, 'marketing'])->name('dashboard');
    });

    Route::middleware('role:marketing,admin,asmen,it support')->prefix('marketing')->name('marketing.')->group(function () {
        Route::resource('products', ProductController::class);
    });

    Route::get('/florist', [DashboardController::class, 'florist'])->middleware('role:florist')->name('florist.dashboard');
    Route::get('/owner', [DashboardController::class, 'owner'])->middleware('role:owner')->name('owner.dashboard');
});
