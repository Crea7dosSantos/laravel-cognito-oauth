<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mypage</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset('js/mypage/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/mypage/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app"></div>
</body>

</html>