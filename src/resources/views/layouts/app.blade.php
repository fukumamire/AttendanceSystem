<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atte</title>
  <link rel="stylesheet" href="{{ asset('auth/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('auth/common.css') }}">
  @yield('css')
</head>
<body>
  <header class="
  header">
    <div class="header__inner">
      <h1 class="header__heading">Atte</h1>  
    </div>
    @yield('link')
  </header>

  <main>
    @yield('content')
  </main>
  
  <footer class="footer">
    <div class="footer__inner">
      <h3>Atte,inc</h3>
    </div>
  </footer>
</body>