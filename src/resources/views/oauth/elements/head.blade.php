<head>
    <meta charset="UTF-8" />
</head>

<head>
    <meta charset="UTF-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noｰindex, no-follow">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/img/favicon.ico">
    <title>@yield('pageTitle') | 認可システム</title>
    <script src="{{ mix('/js/web/app.js') }}"></script>
    <link type="text/css" href="/css/web/app.css" rel="stylesheet">
</head>