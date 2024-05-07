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
        
    </div>
@endsection

@section('content')
    <form class="header__wrap" action="{{ route('the-date') }}" method="post">
        @csrf
        <button class="date__change-button" name="prevDate"><</button>
        <input type="hidden" name="displayDate" value="{{ $displayDate }}">
        <p class="header__text">{{ $displayDate->format('Y-m-d') }}</p>
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
        </table>    
@endsection