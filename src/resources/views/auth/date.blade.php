@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('auth/date.css') }}">
    
@endsection
@section('link')
  <div class="header__links">
    <a href="/">ホーム</a>
    <a href="/attendance">日付一覧</a>
    <form action="{{ route('logout') }}" method="POST">
        @csrf  
      <button type="submit" class="logout-button">ログアウト</button>


        {{-- <a href="{{ route('dateList') }}">日付一覧</a> --}}
    </form>    
  </div>
@endsection

@section('content')
 <form class="header__wrap" action="/the-date" method="post">
    @csrf
    <button class="date__change-button" name="prevDate"><</button>
    <input type="hidden" name="displayDate" value=>
    <p class="header__text"></p>
    <button class="date__change-button" name="nextDate">></button>
 </form>

 <div class="table__wrap">
    <table class="attendance__table">
      <tr class="table__row">
        <th class="table__header">名前</th>
        <th class="table__header">勤務開始</th>
        <th class="table__header">勤務終了</th>
        <th class="table__header">休憩時間</th>
        <th class="table__header">勤務時間</th>
      </tr>
      @foreach ($attendances as $attendance)
        <tr class="table__row">
          <td class="table__item">{{ $attendance->user->name }}</td>
          <td class="table__item">{{ $attendance->start_work }}</td>
          <td class="table__item">{{ $attendance->end_work }}</td>
          <td class="table__item">
            @foreach ($attendance->workBreaks as $break)
                {{ $break->start_break }} - {{ $break->end_break }}<br>
            @endforeach
          </td>
          <td class="table__item">
          @php
						$workTime = $attendance->end_work ? \Carbon\Carbon::parse($attendance->end_work)->diffInMinutes($attendance->start_work) : 0;
						$breakTime = $attendance->workBreaks->sum(function ($break) {
    					return $break->end_break ? \Carbon\Carbon::parse($break->end_break)->diffInMinutes($break->start_break) : 0;
    					});
    					$totalWorkTime = $workTime - $breakTime;
    					$hours = floor($totalWorkTime / 60);
    					$minutes = $totalWorkTime % 60;
						@endphp
            {{ $hours }}時間{{ $minutes }}分
          </td>
        </tr>
      @endforeach
    </table> 
 </form>      
@endsection
