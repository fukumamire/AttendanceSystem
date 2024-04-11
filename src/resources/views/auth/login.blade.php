@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('auth/login.css') }}">
@endsection

@section('content')
<div class="login__content">
  <div class="login-form__heading">
    <h2>ログイン</h2>
  </div>
  <form class="form" action="/login" method="post">
    @csrf
    <div class="form__group">
      <input type="email" name="email" placeholder="メールアドレス"  value="{{ old('email') }}" required  />
      @error('email')
        <div class="alert alert-danger">{{ $errors->first('email') }}</div>
      @enderror
    </div>
    <div class="form__group">
      <input type="password" name="password" placeholder="パスワード" required>
      @error('password')
        <div class="alert alert-danger">{{ $errors->first('password') }}</div>
      @enderror
    </div>
    <div class="form__button">
      <button type="submit" class="login-button">ログイン</button>
    </div>
  </form>
  <div class="register-link">
    <p>アカウントをお持ちでない方はこちらから</p>
    <a href="/register">会員登録</a>
  </div>
</div>
@endsection