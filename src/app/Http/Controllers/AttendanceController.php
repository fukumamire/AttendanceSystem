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
    // 勤務開始　日を跨いだ時点で翌日の出勤操作に切り替える 
    public function startWork()
    {
        // // ユーザーが認証されているかどうかを確認
        // if (!Auth::check()) {
        //     // ユーザーが認証されていない場合、ログイン画面にリダイレクト
        //     return redirect('/login')->with('error', 'ログインしてください。');
        // }
        try {
            // 現在の日付を取得
            $today = Carbon::today();

            // ユーザーの最新の出席記録を取得
            $latestAttendance = Attendance::where('user_id', Auth::id())->latest()->first();

            // 最新の出席記録が存在し、その日付が今日と同じでない場合、新しい出席記録を作成
            if (!$latestAttendance || $latestAttendance->start_work->toDateString() !== $today->toDateString()) {
                $attendance = new Attendance();
                $attendance->user_id = Auth::id();
                $attendance->start_work = Carbon::now();
                $attendance->save();
            } else {
            // 既に出勤記録が存在する場合の処理
                return redirect('/')->with('error', '既に出勤記録が存在します');
            }
            return redirect('/')->with('status', '勤務開始しました');
        } catch (\Exception $e) {
            Log::error('データベース操作中にエラーが発生しました: ' . $e->getMessage());
            return redirect('/')->with('error', '勤務開始に失敗しました。管理者に連絡してください。');
        }
    }

    public function endWork()
    {
        $attendance = Attendance::where('user_id', Auth::id())->latest()->first();
        if ($attendance) {
            $attendance->end_work = Carbon::now();
            $attendance->save();

            return redirect('/')->with('status', '勤務終了しました');
        } else {
            return redirect('/')->with('error', '勤務開始または休憩終了ボタンを押してください。');
        }
    }

    public function startBreak()
    {
        $attendance = Attendance::where('user_id', Auth::id())->latest()->first();
        if ($attendance) {
            $break = new WorkBreak();
            $break->attendance_id = $attendance->id;
            $break->start_break = Carbon::now();
            $break->save();

            return redirect('/')->with('status', '休憩開始しました');
        } else {
            return redirect('/')->with('error', '勤務開始時間が記録されていません。勤務開始ボタンを押下してください');
        }
    }

    public function endBreak()
    {
        // ユーザーの最新の出席記録を取得
        $latestAttendance = Attendance::where('user_id', Auth::id())->latest()->first();


        if (!$latestAttendance) {
            return redirect('/')->with('error', '勤務開始時間が記録されていません。勤務開始ボタンを押下してください');
        }

        // 最新の休憩記録を取得
        $latestBreak = WorkBreak::where('attendance_id', $latestAttendance->id)
            ->whereNull('end_break')
            ->latest()
            ->first();

        // 最新の休憩記録が存在する場合、休憩終了時間を記録

        if ($latestBreak) {
            $latestBreak->end_break = Carbon::now();
            $latestBreak->save();

            return redirect('/')->with('status', '休憩終了しました');
        } else {
            return redirect('/')->with('error', '休憩開始ボタンを押下してください');
        }

        // $latestBreak = WorkBreak::where('attendance_id', Attendance::where('user_id', Auth::id())->latest()->first()->id)
        //     ->whereNull('end_break')
        //     ->latest()
        //     ->first();

        // if ($latestBreak) {
        //     $latestBreak->end_break = Carbon::now();
        //     $latestBreak->save();

        //     return redirect('/')->with('status', '休憩終了しました');
        // } else {
        //     return redirect('/')->with('error', '休憩開始ボタンを押下してください');
        // }
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
