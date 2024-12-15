<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JWTAuthController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::middleware([VerifyCsrfToken::class, 'auth'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware([VerifyCsrfToken::class, 'auth', 'verified'])->name('dashboard');

Route::middleware([VerifyCsrfToken::class, 'auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Auth::routes();

Route::middleware([VerifyCsrfToken::class, 'auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin');
});

// Api routes - with token
Route::prefix('/api')->middleware([JwtMiddleware::class])->group(function () {
    Route::get('me', [JWTAuthController::class, 'me'])->name('me');
    Route::post('logout', [JWTAuthController::class, 'logout'])->name('logout');
});

// Api routes - without token
Route::prefix('/api')->group(function () {
    Route::post('register', [JWTAuthController::class, 'register']);
    Route::post('login', [JWTAuthController::class, 'login']);
});
