<?php


use App\Http\Controllers\ChirpController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('chirps', ChirpController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

// 認証が必要なルートグループ内に追加
Route::middleware('auth')->group(function () {
    // 既存のルート...
    
    // フォロー関連のルート
    Route::post('/users/{user}/follow', [App\Http\Controllers\FollowController::class, 'follow'])
        ->name('users.follow');
    // フォロー解除のルート
    Route::post('/users/{user}/unfollow', [App\Http\Controllers\FollowController::class, 'unfollow'])
        ->name('users.unfollow');
});

require __DIR__.'/auth.php';
