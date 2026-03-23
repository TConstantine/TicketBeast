<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TicketBeast')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.App = {
            stripePublicKey: '{{ config("services.stripe.key") }}'
        }
    </script>
</head>
<body>
    <div id="app">
        @yield('body')
    </div>

    @stack('beforeScripts')
    @stack('afterScripts')
</body>
</html>