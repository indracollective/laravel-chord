@props(['page'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://use.typekit.net/ins2wgm.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ $page->title ?? 'Page Title' }}</title>
</head>
<body class="bg-gray-50">
<x-chord::site.partials.header />
{{ $slot }}
</body>
</html>
