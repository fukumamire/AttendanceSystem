@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('auth/stamp.css') }}">
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
<div class="stamp__content">
  @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger">
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
        <form class="inner-items-upper">
          @csrf
          <button type="submit">勤務開始</button>
        </form>
      </td>
      <td>
        <form class="inner-items-upper">
          @csrf
          <button type="submit">勤務終了</button>
        </form>
      </td>
    </tr>

    <tr>
      <td>
        <form class="inner-items-lower">
          @csrf
          <button type="submit">休憩開始</button>
        </form>
      </td>
      <td>
        <form class="inner-items-lower">
          @csrf
          <button type="submit">休憩終了</button>
        </form>
      </td>
    </tr>
  </table>  
</div>
@endsection