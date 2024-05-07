<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\WorkBreak;

class AttendanceController extends Controller
{
    public function showStampPage()
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $today = Carbon::today();
        $hasAttendanceToday = Attendance::where('user_id', Auth::id())
            ->whereDate('start_work', $today)
            ->exists();

        $hasEndWorkToday = Attendance::where('user_id', Auth::id())
            ->whereDate('start_work', $today)
            ->whereNotNull('end_work')
            ->exists();

        $hasBreakToday = WorkBreak::where('user_id', Auth::id())
            ->whereDate('start_break', $today)
            ->exists();

        $hasEndBreakToday = WorkBreak::where('user_id', Auth::id())
            ->whereDate('start_break', $today)
            ->whereNotNull('end_break')
            ->exists();

        // 勤務開始ボタンの有効化条件を調整
        $canStartWork = !$hasAttendanceToday;

        // 勤務終了ボタンの有効化条件を調整
        // 勤務開始後に有効化または休憩時間があれば休憩終了後に有効化
        $canEndWork = $hasAttendanceToday && (!$hasBreakToday || ($hasBreakToday && $hasEndBreakToday));

        // 休憩開始ボタンの有効化条件を調整
        // 勤務開始後にのみ有効化され、休憩終了後にのみ再度有効化される
        $canStartBreak = $hasAttendanceToday && !$hasBreakToday && session('is_end_break', true);

        // 休憩終了ボタンの有効化条件を調整
        // 休憩開始後にのみ有効化される
        $canEndBreak = session('is_break', false);

        return view('auth.stamp', compact('hasAttendanceToday', 'hasEndWorkToday', 'hasBreakToday', 'hasEndBreakToday', 'canStartWork', 'canEndWork', 'canStartBreak', 'canEndBreak'));
    }

    public function startWork(Request $request)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $today = Carbon::today();
        $attendance = Attendance::firstOrNew([
            'user_id' => Auth::id(),
            'start_work' => $today,
        ]);

        if (!$attendance->exists) {
            $attendance->start_work = now();
            $attendance->save();
            return redirect()->back()->with('success', '勤務開始時間を記録しました。');
        }

        return redirect()->back()->with('error', '既に出勤済みです。');
    }

    public function endWork()
    {
        $attendance = Attendance::where('user_id', Auth::id())->latest()->first();
        if ($attendance) {
            $attendance->end_work = Carbon::now();
            $attendance->save();
            return redirect('/')->with('success', '勤務終了しました');
        }

        return redirect('/')->with('error', '勤務開始または休憩終了ボタンを押してください。');
    }

    public function startBreak(Request $request)
    {
        $attendance = Attendance::where('user_id', Auth::id())->latest()->first();
        if (!$attendance) {
            return redirect('/')->with('error', '勤務開始時間が記録されていません。勤務開始ボタンを押下してください');
        }

        $break = new WorkBreak();
        $break->user_id = Auth::id();
        $break->attendance_id = $attendance->id;
        $break->start_break = Carbon::now();
        $break->save();

        // 休憩開始時に、is_breakセッションをtrueに設定
        $request->session()->put('is_break', true);

        $request->session()->put('is_end_break', false);

        return redirect()->back()->with('success', '休憩開始しました');
    }

    public function endBreak(Request $request)
    {
        $latestAttendance = Attendance::where('user_id', Auth::id())->latest()->first();
        if (!$latestAttendance) {
            return redirect('/')->with('error', '勤務開始時間が記録されていません。勤務開始ボタンを押下してください');
        }

        $latestBreak = WorkBreak::where('attendance_id', $latestAttendance->id)
            ->whereNull('end_break')
            ->latest()
            ->first();

        if ($latestBreak) {
            $latestBreak->end_break = Carbon::now();
            $latestBreak->save();

            // 休憩終了時に、is_breakセッションをfalseにリセット
            $request->session()->put('is_break', false);

            $request->session()->put('is_end_break', true);

            return redirect()->back()->with('success', '休憩終了しました');
        }

        return redirect('/')->with('error', '休憩開始ボタンを押下してください');
    }

    public function calculateDailyWorkTime()
    {
        $today = Carbon::today();
        $attendances = Attendance::where('user_id', Auth::id())
            ->whereDate('start_work', $today)
            ->get();

        $totalWorkTime = $attendances->sum(function ($attendance) {
            $workTime = $attendance->end_work->diffInMinutes($attendance->start_work);
            $breakTime = $attendance->end_break ? $attendance->end_break->diffInMinutes($attendance->start_break) : 0;
            return $workTime - $breakTime;
        });

        $hours = floor($totalWorkTime / 60);
        $minutes = $totalWorkTime % 60;

        return view('Auth.date', compact('hours', 'minutes'));
    }

    public function attendanceList(Request $request)
    {
        $displayDate = $request->input('displayDate', Carbon::now()->toDateString());
        $attendances = Attendance::with('user')
            ->whereDate('start_work', $displayDate)
            ->orderBy('start_work', 'desc')
            ->get();

        return view('auth.date', compact('attendances', 'displayDate'));
    }
}
