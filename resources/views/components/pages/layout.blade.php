@props(['page'])
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        <title>{{ $page->title ?? 'Page Title' }}</title>
    </title>
    <link
        rel="shortcut icon"
        href="assets/images/favicon.png"
        type="image/x-icon"
    />
    @livewireStyles
    @vite(['resources/css/app.css'])
</head>
<body>
<x-chord::component component="header.index" />
{{ $slot }}
@livewireScripts
</body>
</html>
