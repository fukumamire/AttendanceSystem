<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\WorkBreak;
use App\Models\User;

class AttendanceController extends Controller
{
    public function showStampPage()
    {
        // ユーザーが認証されているかを確認
        if (!Auth::check()) {
            return redirect('login');
        }

        $today = Carbon::today();
        $hasAttendanceToday = Attendance::where('user_id', Auth::id())
            ->whereDate('start_work', $today)
            ->exists();

        // ユーザーが今日の出席記録に終了記録を登録しているかどうかを確認
        $hasEndWorkToday = Attendance::where('user_id', Auth::id())
            ->whereDate('start_work', $today)
            ->whereNotNull('end_work')
            ->exists();

        // ユーザーが今日休憩開始時間を登録しているかどうかを確認
        $hasBreakToday = WorkBreak::where('user_id', Auth::id())
            ->whereDate('start_break', $today)
            ->exists();

        // ユーザーが今日の休憩終了時間を登録しているかどうかを確認
        $hasEndBreakToday = WorkBreak::where('user_id', Auth::id())
            ->whereDate('start_break', $today)
            ->whereNotNull('end_break')
            ->exists();

        // 休憩終了ボタンが押下されたかどうかをセッションから取得
        $isEndBreak = session('is_end_break', false);

        // nullの場合、falseにして勤務開始ボタンの有効化
        $hasAttendanceToday = $hasAttendanceToday ?? false;
        $hasEndWorkToday = $hasEndWorkToday ?? false;
        $hasBreakToday = $hasBreakToday ?? false;
        $hasEndBreakToday = $hasEndBreakToday ?? false;

        return view('auth.stamp', compact('hasAttendanceToday', 'hasEndWorkToday', 'hasBreakToday', 'hasEndBreakToday', 'isEndBreak'));
    }


    public function startWork(Request $request)
    {
        // ユーザーが認証されているかを確認
        if (!Auth::check()) {
            return redirect('login');
        }

        // 現在の日付を取得
        $today = Carbon::today();

        // ユーザーの最新の出席記録を取得
        $latestAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('start_work', $today)
            ->latest()
            ->first();

        // ユーザーが既に今日の出席記録を作成していない場合にのみ、新しい出席記録を作成
        if (!$latestAttendance) {
            // Attendanceモデルの新しいインスタンスを作成
            $attendance = new Attendance;

            // 必要なデータを設定
            $attendance->user_id = Auth::id(); // 現在認証されているユーザーのIDを設定
            $attendance->start_work = now(); // 現在の時間を開始時間として設定（now()ヘルパ関数を使用）

            // データベースに保存
            $attendance->save();

            // 成功メッセージを表示して、リダイレクト
            return redirect()->back()->with('success', '勤務開始時間を記録しました。');
        } else {
            // 既に出席記録が存在する場合、エラーメッセージを表示して、リダイレクト
            return redirect()->back()->with('error', '既に出勤済みです。');
        }
    }


    public function endWork()
    {
        $attendance = Attendance::where('user_id', Auth::id())->latest()->first();
        if ($attendance) {
            $attendance->end_work = Carbon::now();
            $attendance->save();

            return redirect('/')->with('success', '勤務終了しました');
        } else {
            return redirect('/')->with('error', '勤務開始または休憩終了ボタンを押してください。');
        }
    }
    // 休憩開始
    public function startBreak(Request $request)
    {
        // ユーザーの最新の出席記録を取得
        $attendance = Attendance::where('user_id', Auth::id())->latest()->first();

        // 出席記録が存在しない場合はエラーメッセージを表示してリダイレクト
        if (!$attendance) {
            return redirect('/')->with('error', '勤務開始時間が記録されていません。勤務開始ボタンを押下してください');
        }

        // 新しい休憩記録を作成
        $break = new WorkBreak();
        $break->user_id = Auth::id();
        $break->attendance_id = $attendance->id;
        $break->start_break = Carbon::now();
        $break->save();

        // 休憩開始フラグをセッションに保存
        $request->session()->put('is_break', true);

        return redirect()->back()->with('success', '休憩開始しました');
    }


    // 休憩終了
    public function endBreak(Request $request)
    {
        // ユーザーの最新の出席記録を取得
        $latestAttendance = Attendance::where('user_id', Auth::id())->latest()->first();

        // 出席記録が存在しない場合はエラーメッセージを表示してリダイレクト
        if (!$latestAttendance) {
            return redirect('/')->with('error', '勤務開始時間が記録されていません。勤務開始ボタンを押下してください');
        }

        // 最新の休憩記録を取得
        $latestBreak = WorkBreak::where('attendance_id', $latestAttendance->id)
            ->whereNull('end_break')
            ->latest()
            ->first();

        // 休憩記録が存在しない場合はエラーメッセージを表示してリダイレクト
        if (!$latestBreak) {
            return redirect('/')->with('error', '休憩開始ボタンを押下してください');
        }

        // 休憩終了時間を設定して保存
        $latestBreak->end_break = Carbon::now();
        $latestBreak->save();

        // 休憩開始フラグをセッションから削除
        $request->session()->forget('is_break');

        // ユーザーが休憩を終了した後にのみ勤務終了ボタンを有効化
        $request->session()->put('is_end_break', true);

        return redirect()->back()->with('success', '休憩終了しました');
    }

    public function calculateDailyWorkTime()
    {
        // 現在の日付を取得
        $today = Carbon::today();

        // ユーザーの出席記録を取得
        $attendances = Attendance::where('user_id', Auth::id())
            ->whereDate('start_work', $today)
            ->get();

        // 勤務時間の合計を初期化
        $totalWorkTime = 0;

        foreach ($attendances as $attendance) {
            // 勤務時間と休憩時間を計算
            $workTime = $attendance->end_work->diffInMinutes($attendance->start_work);
            $breakTime = $attendance->end_break ? $attendance->end_break->diffInMinutes($attendance->start_break) : 0;

            // 勤務時間から休憩時間を引いて、合計勤務時間に加算
            $totalWorkTime += $workTime - $breakTime;
        }

        // 合計勤務時間を時間と分に変換
        $hours = floor($totalWorkTime / 60);
        $minutes = $totalWorkTime % 60;

        return view('Auth.date', compact('hours', 'minutes'));
    }

    //勤務記録を取得し日付一覧whereDateメソッドを使用して特定の日付に一致する出勤記録（date.blade.php)に表示させる
    public function attendanceList()
    {
        $displayDate = Carbon::now()->toDateString();

        // 特定の日付に一致する出席記録を取得
        $attendances = Attendance::with('user')
            ->whereDate('start_work', $displayDate)
            ->orderBy('start_work', 'desc')
            ->get();

        return view('auth.date', compact('attendances'));
    }
}
