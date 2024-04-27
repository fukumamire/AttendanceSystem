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

// ログインページ
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest');


// // 会員登録ページ
// Route::get('/register', function () {
//     return view('auth.register');
// });
// 便宜　打刻ページの表示
Route::get('/', function () {
    return view('auth.stamp');
});

// // ログアウト　２０２４年４月１０日現在不要　FortifyServiceProviderのregisterメソッド があるため、コメントアウト
// Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
//     ->name('logout');


//ログアウト２０２４年４月１０日時点ではミドルウェアを使用していないのでコメントOFF
// Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
//     ->middleware('auth')
//     ->name('logout');

//便宜　日付一覧
Route::get('/attendance', function () {
    return view('auth.date');
});



//勤務時間関係
Route::post('/start-work', [AttendanceController::class, 'startWork'])->middleware('auth');
Route::post('/end-work', [AttendanceController::class, 'endWork'])->middleware('auth');
Route::post('/start-break', [AttendanceController::class, 'startBreak'])->middleware('auth');
Route::post('/end-break', [AttendanceController::class, 'endBreak'])->middleware('auth');
