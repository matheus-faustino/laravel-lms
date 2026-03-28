<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            text-align: center;
        }

        .code {
            font-size: 6rem;
            font-weight: bold;
            margin: 0;
            color: #ccc;
        }

        .message {
            font-size: 1.25rem;
            margin: 0.5rem 0 2rem;
        }

        a {
            color: #333;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
