<!DOCTYPE html>
<html lang="es" class="min-h-dvh dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>
            @hasSection ('title')
                @yield('title') - {{ env("APP_NAME") }}
            @else
                {{ env("APP_NAME") }}
            @endif
        </title>

        @vite('resources/css/app.css')
    </head>
    <body class="min-h-dvh">
        @yield("content")
    </body>
</html>
