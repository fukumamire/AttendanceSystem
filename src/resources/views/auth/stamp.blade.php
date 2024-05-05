@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('auth/stamp.css') }}">
@endsection

@section('link')
    <div class="header__links">
        <a href="/">ホーム</a>
        <a href="/attendance">日付一覧</a>
        <form class="logout-button" action="{{ route('logout') }}" method="POST">
          @csrf  
        <button type="submit" class="logout-button">ログアウト</button>
        </form>

        {{-- <a href="{{ route('dateList') }}">日付一覧</a> --}}
        
    </div>
@endsection


@section('content')
<div class="stamp__content">
  @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
  @endif

  @if (session('error'))
    <div class="alert-danger">
        {{ session('error') }}
    </div>
  @endif
  
  <div class="welcome-message">
  {{ Auth::user()->name }}
  
  さんお疲れ様です！
  </div>

  <table class="buttons">
    <tr>
      <td>
        <form class="inner-items-upper" action="/start-work" method="POST">
          @csrf
          @if(!$hasAttendanceToday ?? '')
            <button class="form__item-button" type="submit" name="start_work">勤務開始</button>
          @else
            <button class="form__item-button" type="submit" name="start_work" disabled>勤務開始</button>
          @endif
        </form>
      </td>
      <td>
        <form class="inner-items-upper" action="/end-work" method="POST">
          @csrf
          <button type="submit">勤務終了</button>
        </form>
      </td>
    </tr>

    <tr>
      <td>
        <form class="inner-items-lower" action="/start-break" method="POST">
          @csrf
          <button type="submit">休憩開始</button>
        </form>
      </td>
      <td>
        <form class="inner-items-lower" action="/end-break" method="POST">
          <input type="hidden" name="break_id"  value="{{ $break_id ?? '' }}">

          @csrf
          <button type="submit">休憩終了</button>
        </form>
      </td>
    </tr>
  </table>  
</div>
@endsection