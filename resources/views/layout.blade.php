<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <style>
        :root { --bg: #f6f7f9; --card: #fff; --text: #111827; --muted: #6b7280; --accent: #111827; --accent-text: #fff; --border: #e5e7eb; }
        @media (prefers-color-scheme: dark) {
            :root { --bg: #0b0d12; --card: #151922; --text: #f3f4f6; --muted: #9ca3af; --accent: #f3f4f6; --accent-text: #111827; --border: #262b36; }
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 16px; background: var(--bg); color: var(--text); font: 15px/1.5 system-ui, -apple-system, "Segoe UI", sans-serif; }
        main { width: 100%; max-width: 380px; padding: 32px 28px; background: var(--card); border: 1px solid var(--border); border-radius: 14px; text-align: center; }
        h1 { margin: 0 0 8px; font-size: 18px; }
        p { margin: 0 0 24px; color: var(--muted); overflow-wrap: anywhere; }
        strong { color: var(--text); font-weight: 600; }
        a.button { display: inline-block; padding: 10px 18px; border-radius: 8px; background: var(--accent); color: var(--accent-text); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <main>
        @yield('content')
    </main>
</body>
</html>
