<!DOCTYPE html>
<html lang="es" class="h-100">
<meta charset="UTF-8">
<title>
    @hasSection ('title')
        @yield('title') - {{ env("APP_NAME") }}
    @else
        {{ env("APP_NAME") }}
    @endif
</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<!--TODO: bundle assets -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<body class="h-100">
<div class="container h-100 d-block">
    @yield("content")
</div>
</body>
</html>
