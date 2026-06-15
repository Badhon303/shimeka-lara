<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Glow & Glam') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $entry = $manifest['resources/js/app.js'] ?? null;
    @endphp
    @if($entry)
        @foreach(($entry['css'] ?? []) as $css)
            <link rel="stylesheet" href="/build/{{ $css }}">
        @endforeach
        <script type="module" src="/build/{{ $entry['file'] }}"></script>
    @endif
</head>
<body>
    <div id="app"></div>
</body>
</html>
