<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\FeaturePageController;
use App\Http\Controllers\HomeContentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleDashboardController;
use App\Http\Controllers\Cms\SettingController;
use App\Http\Controllers\Cms\VirtualRoomController;
use App\Http\Controllers\Cms\VirtualBookPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lang/{locale}', [HomeController::class, 'switchLocale'])->name('locale.switch');
Route::post('/api/chat', [ChatController::class, 'getBotResponse'])->name('api.chat');

// Static pages
Route::view('/disclaimer', 'pages.disclaimer')->name('disclaimer');

// Public feature pages
Route::get('/halaman/{feature}/{pageNum?}', [FeaturePageController::class, 'publicShow'])
    ->where('pageNum', '[0-9]+')
    ->name('feature.page');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [RoleDashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/admin', [RoleDashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

    Route::get('/dashboard/pegawai', [RoleDashboardController::class, 'pegawai'])
        ->middleware('role:pegawai')
        ->name('dashboard.pegawai');

    Route::get('/dashboard/umum', [RoleDashboardController::class, 'umum'])
        ->middleware('role:umum')
        ->name('dashboard.umum');

    Route::get('/dashboard/pelajar-mahasiswa', [RoleDashboardController::class, 'pelajar'])
        ->middleware('role:pelajar_mahasiswa')
        ->name('dashboard.pelajar');

    Route::get('/dashboard/instansi-swasta', [RoleDashboardController::class, 'instansi'])
        ->middleware('role:instansi_swasta')
        ->name('dashboard.instansi');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('/profile/activity', [ProfileController::class, 'activity'])->name('profile.activity');
    Route::delete('/profile/activity/logout-others', [ProfileController::class, 'logoutOtherBrowserSessions'])->name('profile.activity.logout-others');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CMS Home Content Editor
    Route::middleware('role:admin')->prefix('cms/home')->name('cms.home.')->group(function () {
        Route::get('/', [HomeContentController::class, 'edit'])->name('edit');
        Route::post('/', [HomeContentController::class, 'update'])->name('update');
    });

    // CMS Settings
    Route::middleware('role:admin')->prefix('cms/settings')->name('cms.settings.')->group(function () {
        Route::post('/rte-upload', [SettingController::class, 'uploadRteMedia'])->name('rte.upload');

        Route::get('/footer', [SettingController::class, 'editFooter'])->name('footer.edit');
        Route::put('/footer', [SettingController::class, 'updateFooter'])->name('footer.update');
        Route::get('/disclaimer', [SettingController::class, 'editDisclaimer'])->name('disclaimer.edit');
        Route::put('/disclaimer', [SettingController::class, 'updateDisclaimer'])->name('disclaimer.update');
    });

    // CMS Features
    Route::middleware('role:admin')->prefix('cms/features')->name('cms.features.')->group(function () {
        Route::get('/', [FeatureController::class, 'index'])->name('index');
        Route::post('/', [FeatureController::class, 'store'])->name('store');
        Route::get('/{feature}', [FeatureController::class, 'show'])->name('show');
        Route::put('/{feature}', [FeatureController::class, 'update'])->name('update');
        Route::delete('/{feature}', [FeatureController::class, 'destroy'])->name('destroy');
        Route::put('/{feature}/content', [FeatureController::class, 'updateContent'])->name('update-content');
        Route::put('/{feature}/sub', [FeatureController::class, 'updateSub'])->name('update-sub');
        Route::delete('/{feature}/sub', [FeatureController::class, 'destroySub'])->name('destroy-sub');

        // Feature Pages (multi-page content)
        Route::get('/{feature}/pages', [FeaturePageController::class, 'index'])->name('pages.index');
        Route::post('/{feature}/pages', [FeaturePageController::class, 'store'])->name('pages.store');
        Route::get('/{feature}/pages/{page}', [FeaturePageController::class, 'show'])->name('pages.show');
        Route::put('/{feature}/pages/{page}', [FeaturePageController::class, 'update'])->name('pages.update');
        Route::delete('/{feature}/pages/{page}', [FeaturePageController::class, 'destroy'])->name('pages.destroy');

        // Page Sections
        Route::post('/{feature}/pages/{page}/sections', [FeaturePageController::class, 'storeSection'])->name('pages.sections.store');
        Route::put('/{feature}/pages/{page}/sections/{section}', [FeaturePageController::class, 'updateSection'])->name('pages.sections.update');
        Route::delete('/{feature}/pages/{page}/sections/{section}', [FeaturePageController::class, 'destroySection'])->name('pages.sections.destroy');

        // Virtual Room 360 Feature (yang lama)
        Route::get('/{feature}/virtual-rooms', [VirtualRoomController::class, 'index'])->name('virtual_rooms.index');
        Route::get('/{feature}/virtual-rooms/create', [VirtualRoomController::class, 'create'])->name('virtual_rooms.create');
        Route::post('/{feature}/virtual-rooms', [VirtualRoomController::class, 'store'])->name('virtual_rooms.store');
        Route::get('/{feature}/virtual-rooms/{room}/edit', [VirtualRoomController::class, 'edit'])->name('virtual_rooms.edit');
        Route::put('/{feature}/virtual-rooms/{room}', [VirtualRoomController::class, 'update'])->name('virtual_rooms.update');
        Route::delete('/{feature}/virtual-rooms/{room}', [VirtualRoomController::class, 'destroy'])->name('virtual_rooms.destroy');

        // Virtual 3D Rooms Feature (yang baru - 4 dinding 1 pintu)
        Route::get('/{feature}/virtual-3d-rooms', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'index'])->name('virtual_3d_rooms.index');
        Route::get('/{feature}/virtual-3d-rooms/create', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'create'])->name('virtual_3d_rooms.create');
        Route::post('/{feature}/virtual-3d-rooms', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'store'])->name('virtual_3d_rooms.store');
        Route::get('/{feature}/virtual-3d-rooms/{room}/edit', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'edit'])->name('virtual_3d_rooms.edit');
        Route::put('/{feature}/virtual-3d-rooms/{room}', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'update'])->name('virtual_3d_rooms.update');
        Route::delete('/{feature}/virtual-3d-rooms/{room}', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'destroy'])->name('virtual_3d_rooms.destroy');
        
        // Media Management untuk Virtual 3D Rooms
        Route::post('/{feature}/virtual-3d-rooms/{room}/media', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'uploadMedia'])->name('virtual_3d_rooms.media.store');
        Route::put('/{feature}/virtual-3d-rooms/{room}/media/{media}', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'updateMediaPosition'])->name('virtual_3d_rooms.media.update');
        Route::delete('/{feature}/virtual-3d-rooms/{room}/media/{media}', [App\Http\Controllers\Cms\Virtual3dRoomController::class, 'deleteMedia'])->name('virtual_3d_rooms.media.destroy');

        // Virtual Book Pages (Pameran Virtual Buku)
        Route::get('/{feature}/virtual-book-pages', [VirtualBookPageController::class, 'index'])->name('virtual_book_pages.index');
        Route::get('/{feature}/virtual-book-pages/create', [VirtualBookPageController::class, 'create'])->name('virtual_book_pages.create');
        Route::post('/{feature}/virtual-book-pages', [VirtualBookPageController::class, 'store'])->name('virtual_book_pages.store');
        Route::get('/{feature}/virtual-book-pages/{virtualBookPage}/edit', [VirtualBookPageController::class, 'edit'])->name('virtual_book_pages.edit');
        Route::get('/{feature}/virtual-book-pages/{virtualBookPage}', [VirtualBookPageController::class, 'show'])->name('virtual_book_pages.show');
        Route::put('/{feature}/virtual-book-pages/{virtualBookPage}', [VirtualBookPageController::class, 'update'])->name('virtual_book_pages.update');
        Route::delete('/{feature}/virtual-book-pages/{virtualBookPage}', [VirtualBookPageController::class, 'destroy'])->name('virtual_book_pages.destroy');
        Route::put('/{feature}/virtual-book-pages-settings', [VirtualBookPageController::class, 'updateSettings'])->name('virtual_book_pages.settings');
    });
});

require __DIR__.'/auth.php';

// Public feature pages by path (e.g., /pameran/tetap) - must be last
Route::get('/{path}', [FeaturePageController::class, 'publicShowByPath'])
    ->where('path', '.+')
    ->name('feature.path');
