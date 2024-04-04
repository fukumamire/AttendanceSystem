@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('auth/stamp.css') }}">
@endsection

@section('link')
    <div class="header__links">
        <a href="/">ホーム</a>
        <a href="">日付一覧</a>
        <a href="">ログアウト</a>
        {{-- <a href="{{ route('logout') }}">ログアウト</a> --}}
        {{-- <a href="{{ route('dateList') }}">日付一覧</a> --}}
        
    </div>
@endsection

@section('content')
<div class="stamp__content">
  <div class="welcome-message">
  {{-- {{ Auth::user()->name }} --}}
  
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
    {{-- <form action="{{ route('startWork') }}" method="post">
        @csrf
        <button type="submit">勤務開始</button>
    </form>
    <form action="{{ route('endWork') }}" method="post">
        @csrf
        <button type="submit">勤務終了時間</button>
    </form>
    <form action="{{ route('startBreak') }}" method="post">
        @csrf
        <button type="submit">休憩開始</button>
    </form>
    <form action="{{ route('endBreak') }}" method="post">
        @csrf
        <button type="submit">休憩終了</button>
    </form> --}}
</table>

</div>
@endsection