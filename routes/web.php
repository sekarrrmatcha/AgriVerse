<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PraktikumController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik (bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Halaman setelah login (butuh autentikasi)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/forum', [ThreadController::class, 'index'])->name('forum.index');
    Route::get('/forum/create', [ThreadController::class, 'create'])->name('forum.create');
    Route::post('/forum', [ThreadController::class, 'store'])->name('forum.store');
    Route::get('/forum/{thread}', [ThreadController::class, 'show'])->name('forum.show');
    Route::post('/forum/{thread}/reply', [ReplyController::class, 'store'])->name('forum.reply');

    Route::middleware('user.prodi')->group(function () {
        Route::get('/semester/{semester}', [SemesterController::class, 'show'])->name('semester.show');
        Route::get('/matakuliah/{matakuliah}', [MatakuliahController::class, 'show'])->name('matakuliah.show');

        Route::get('/materi/{materi}', [MateriController::class, 'show'])->name('materi.show');
        Route::post('/materi/{materi}/toggle', [MateriController::class, 'toggle'])->name('materi.toggle');

        Route::get('/praktikum/{praktikum}', [PraktikumController::class, 'show'])->name('praktikum.show');
        Route::post('/praktikum/{praktikum}/langkah', [PraktikumController::class, 'updateLangkah'])->name('praktikum.langkah');
        Route::post('/praktikum/{praktikum}/toggle', [PraktikumController::class, 'toggle'])->name('praktikum.toggle');
    });
});
Route::get('/debug', function () {
    return [
        'app_url' => config('app.url'),
        'url' => url('/login'),
        'route' => route('login'),
        'secure' => request()->isSecure(),
        'scheme' => request()->getScheme(),
        'x_forwarded_proto' => request()->header('X-Forwarded-Proto'),
    ];
});
