<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = auth()->user()->role;
            if (in_array($role, ['asmen', 'it support'])) {
                return redirect()->intended('/admin');
            }
            return redirect()->intended('/' . $role);
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    });
});


// POS Kasir Layar Sentuh (Tanpa Login Password, cukup pilih nama Florist)
Route::prefix('pos')->name('pos.')->group(function () {
    // Halaman Pilih Florist
    Route::get('/login', function () {
        return view('pos.login');
    })->name('login');
    
    // Proses Pilih Florist
    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $request->validate(['florist_name' => 'required|string']);
        session()->put('pos_florist', $request->florist_name);
        return redirect()->route('pos.index');
    })->name('login.post');

    // Proses Ganti Florist / Logout
    Route::post('/logout', function () {
        session()->forget('pos_florist');
        return redirect()->route('pos.login');
    })->name('logout');

    // Rute POS Utama
    // Rute POS Utama
    Route::middleware([\App\Http\Middleware\CheckPosFlorist::class])->group(function () {
        Route::get('/', [\App\Http\Controllers\PosController::class, 'index'])->name('index');
        Route::get('/kiosk', [\App\Http\Controllers\PosController::class, 'kiosk'])->name('kiosk');
        Route::get('/catalog', [\App\Http\Controllers\PosController::class, 'catalog'])->name('catalog');
        Route::get('/materials/{type}', [\App\Http\Controllers\PosController::class, 'materials'])->name('materials');
        Route::get('/custom', [\App\Http\Controllers\PosController::class, 'custom'])->name('custom');
        
        Route::post('/cart/add', [\App\Http\Controllers\PosController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/add-material', [\App\Http\Controllers\PosController::class, 'addMaterialToCart'])->name('cart.add-material');
        Route::post('/cart/add-custom', [\App\Http\Controllers\PosController::class, 'addCustomToCart'])->name('cart.add-custom');
        
        Route::post('/cart/update', [\App\Http\Controllers\PosController::class, 'updateCart'])->name('cart.update');
        Route::post('/cart/remove', [\App\Http\Controllers\PosController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/cart/clear', [\App\Http\Controllers\PosController::class, 'clearCart'])->name('cart.clear');
        Route::post('/store', [\App\Http\Controllers\PosController::class, 'store'])->name('store');
    });
});

// API Ongkir & Promo (Public or API accessible)
Route::get('/api/calculate-ongkir', [\App\Http\Controllers\OrderController::class, 'calculateOngkir'])->name('api.ongkir');
Route::get('/api/check-promo', [\App\Http\Controllers\OrderController::class, 'checkPromo'])->name('api.promo');
Route::get('/api/check-new-orders', [\App\Http\Controllers\OrderController::class, 'checkNewOrders'])->name('api.new_orders');

// Print Receipt (Publicly accessible for customers and POS)
Route::get('/orders/{id}/print', [\App\Http\Controllers\OrderController::class, 'printReceipt'])->name('orders.print');

Route::middleware('auth')->group(function () {
    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // Katalog Produk (Bisa diakses semua role)
    Route::get('/katalog', [\App\Http\Controllers\CatalogController::class, 'index'])->name('catalog.index');

    // Custom Bucket Builder
    Route::get('/custom-bucket', [\App\Http\Controllers\CustomBucketController::class, 'index'])->name('custom.index');
    Route::get('/custom-bucket/drafts', [\App\Http\Controllers\CustomBucketController::class, 'drafts'])->name('custom.drafts');
    Route::post('/custom-bucket/store', [\App\Http\Controllers\CustomBucketController::class, 'store'])->name('custom.store');

    // Pesanan Offline & Checkout
    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/checkout/{product_id}', [\App\Http\Controllers\OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
    
    // Pesanan Online (Marketing)
    Route::get('/orders/online', [\App\Http\Controllers\OrderController::class, 'createOnline'])->name('orders.online.create');
    Route::post('/orders/online', [\App\Http\Controllers\OrderController::class, 'storeOnline'])->name('orders.online.store');
    
    // Order Detail & Actions
    Route::get('/orders/{id}', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::put('/orders/{id}/florist-notes', [\App\Http\Controllers\OrderController::class, 'updateFloristNotes'])->name('orders.updateFloristNotes');
    
    // Dapur Florist (Kitchen)
    Route::get('/kitchen', [\App\Http\Controllers\OrderController::class, 'kitchen'])->name('kitchen.index');
    
    // API Polling untuk Notifikasi Dapur
    Route::get('/api/check-new-orders', function (\Illuminate\Http\Request $request) {
        $lastCheck = (int) $request->query('last_check', time() - 60);
        $lastCheckTime = \Carbon\Carbon::createFromTimestamp($lastCheck);
        
        $hasNew = \App\Models\Order::where('status', 'pending')
            ->where('created_at', '>', $lastCheckTime)
            ->exists();
            
        $hasPending = \App\Models\Order::where('status', 'pending')->exists();
            
        return response()->json([
            'has_new' => $hasNew,
            'has_pending' => $hasPending
        ]);
    })->name('api.check-new-orders');
    
    // Order Revision / Histori
    Route::get('/orders/{id}/revision', [\App\Http\Controllers\OrderRevisionController::class, 'editComponents'])->name('orders.revision.edit');
    Route::post('/orders/{id}/components', [\App\Http\Controllers\OrderRevisionController::class, 'storeComponent'])->name('orders.revision.storeComponent');
    Route::delete('/orders/{id}/components/{componentId}', [\App\Http\Controllers\OrderRevisionController::class, 'deleteComponent'])->name('orders.revision.deleteComponent');

    // Payments
    Route::post('/orders/{id}/payments', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    Route::put('/payments/{id}/verify', [\App\Http\Controllers\PaymentController::class, 'verify'])->name('payments.verify');
    Route::put('/payments/{id}/upload-proof', [\App\Http\Controllers\PaymentController::class, 'uploadProof'])->name('payments.upload_proof');

    // Order Images
    Route::post('/orders/{id}/images', [\App\Http\Controllers\OrderImageController::class, 'store'])->name('orders.images.store');
    Route::delete('/order-images/{id}', [\App\Http\Controllers\OrderImageController::class, 'destroy'])->name('orders.images.destroy');



    Route::middleware('role:admin,asmen,it support')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DashboardController::class, 'admin'])->name('dashboard');
        
        // Settings
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        Route::resource('promos', \App\Http\Controllers\Admin\PromoController::class)->except(['show']);

        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('materials', \App\Http\Controllers\Admin\MaterialController::class);
        Route::resource('stocks', \App\Http\Controllers\Admin\StockController::class)->only(['index', 'create', 'store']);
        
        // Audit Logs
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
        
        // Database Backup
        Route::get('/backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/run', [\App\Http\Controllers\Admin\BackupController::class, 'runBackup'])->name('backups.run');
        Route::get('/backups/download/{file}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{file}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('/backups/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('backups.restore');
        
        // Reports
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    });

    Route::middleware('role:marketing')->prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DashboardController::class, 'marketing'])->name('dashboard');
    });

    Route::middleware('role:marketing,admin,asmen,it support')->prefix('marketing')->name('marketing.')->group(function () {
        Route::resource('products', \App\Http\Controllers\Marketing\ProductController::class);
    });
    Route::get('/florist', [\App\Http\Controllers\DashboardController::class, 'florist'])->middleware('role:florist')->name('florist.dashboard');
    Route::get('/owner', [\App\Http\Controllers\DashboardController::class, 'owner'])->middleware('role:owner')->name('owner.dashboard');
});