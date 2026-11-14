<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Case 3 DKP' }}</title>
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    <link rel="stylesheet" href="/build/{{ $manifest['resources/css/app.css']['file'] }}">
    <script type="module" src="/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
    @fluxAppearance
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 antialiased">
    {{ $slot }}
    @fluxScripts
</body>
</html>