<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    @php
        $siteName = \App\Models\Setting::get('site_name', 'Sʜɪᴍᴇᴋᴀ');
    @endphp
    <title>{{ $siteName }}</title>
    <script>window.__SITE_NAME__ = '{{ $siteName }}';</script>
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
        background: #fff0f5;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    ">
        <div style="
            font-size: 1.75rem;
            font-weight: 700;
            color: #e91e63;
            font-family: 'Playfair Display', serif;
            margin-bottom: 1.5rem;
            letter-spacing: 0.05em;
        ">Sʜɪᴍᴇᴋᴀ</div>
        <div style="
            width: 40px;
            height: 40px;
            border: 3px solid #ffc1e3;
            border-top-color: #e91e63;
            border-radius: 50%;
            animation: preloader-spin 0.7s linear infinite;
        "></div>
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
