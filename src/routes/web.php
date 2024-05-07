<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\AttendanceController;

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


// 会員登録ページ
Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('resister');;

// ログインページ
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');



// ログアウトのルート
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


// 打刻ページの表示
Route::get('/', function () {
    return view('auth.stamp');
})->middleware('auth');



//日付一覧
Route::get('/attendance', function () {
    return view('auth.date');
})->middleware('auth');



//勤務時間関係
Route::get('/', [AttendanceController::class, 'showStampPage'])->middleware('auth');
Route::post('/start-work', [AttendanceController::class, 'startWork'])->middleware('auth');
Route::post('/end-work', [AttendanceController::class, 'endWork'])->middleware('auth');
Route::post('/start-break', [AttendanceController::class, 'startBreak'])->middleware('auth');
Route::post('/end-break', [AttendanceController::class, 'endBreak'])->middleware('auth');

Route::post('/the-date', [AttendanceController::class, 'attendanceList'])->name('the-date');
