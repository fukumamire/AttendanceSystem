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