<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="/storage/logo.png">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @routes
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
