<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>{{ config('app.name', 'Sʜɪᴍᴇᴋᴀ') }}</title>
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
    <!-- Preloader -->
    <div id="preloader" style="
        position: fixed;
        inset: 0;
        background: linear-gradient(135deg, #0f172a, #1e3a5f);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    ">
        <div style="
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255,255,255,0.1);
            border-top-color: #ec4899;
            border-radius: 50%;
            animation: preloader-spin 0.8s linear infinite;
        "></div>
        <p style="
            margin-top: 1.5rem;
            color: rgba(255,255,255,0.7);
            font-size: 0.875rem;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        ">Loading...</p>
    </div>
    <style>
        @keyframes preloader-spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <div id="app"></div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                var preloader = document.getElementById('preloader');
                if (preloader) {
                    preloader.style.opacity = '0';
                    preloader.style.visibility = 'hidden';
                    setTimeout(function() { preloader.remove(); }, 500);
                }
            }, 600);
        });
    </script>
</body>
</html>
