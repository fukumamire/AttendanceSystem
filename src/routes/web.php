<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ログインページ
Route::get('/login', function () {
    return view('auth.login');
});


// // 会員登録ページ
// Route::get('/register', function () {
//     return view('auth.register');
// });
// 便宜　打刻ページの表示
Route::get('/', function () {
    return view('auth.stamp');
});


Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
