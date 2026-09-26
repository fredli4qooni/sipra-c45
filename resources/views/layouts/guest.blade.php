<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk ke Sistem') — SIPRA-C4.5 UIN Raden Intan Lampung</title>

    <!-- Google Fonts: Outfit (Display & Headings) + Plus Jakarta Sans (Body) + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                borderRadius: {
                    'none': '0px',
                    'xs': '2px',
                    'sm': '3px',
                    'DEFAULT': '4px',
                    'md': '6px',
                    'lg': '8px',
                    'xl': '10px',
                    '2xl': '12px',
                    '3xl': '14px',
                    'full': '9999px',
                },
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
        }
        h1, h2, h3, h4, h5, h6, .font-heading, .font-display {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.025em;
        }
    </style>
</head>
<body class="min-h-full bg-[#f8fafc] text-slate-800 antialiased selection:bg-brand-100 selection:text-brand-900 flex flex-col justify-between p-4 sm:p-6 lg:p-8">

    @yield('content')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
