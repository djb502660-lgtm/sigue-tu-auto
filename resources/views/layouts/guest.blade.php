<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sigue tu Auto | Acceso</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr;
        }

        .auth-visual {
            display: none;
        }

        @media (min-width: 1024px) {
            .auth-shell {
                grid-template-columns: 1fr 1fr;
            }

            .auth-visual {
                display: flex;
            }
        }
    </style>
</head>
<body class="font-sans bg-slate-100 text-slate-800 antialiased">
    <div class="auth-shell">
        <section class="auth-visual bg-gradient-to-br from-emerald-600 via-emerald-700 to-slate-900 p-12 text-white">
            <div class="max-w-md self-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-8">
                    <span class="w-10 h-10 rounded-xl bg-white/20 grid place-items-center">
                        <span class="w-3 h-3 rounded-full bg-white"></span>
                    </span>
                    <span class="font-semibold tracking-wide">Sigue tu Auto</span>
                </a>
                <h1 class="text-3xl font-bold leading-tight">Acceso seguro para el panel de servicio</h1>
                <p class="mt-4 text-emerald-50/90">
                    Gestiona ordenes, actualiza estados y da seguimiento al cliente desde una sola plataforma.
                </p>
            </div>
        </section>

        <section class="flex items-center justify-center p-5 sm:p-8">
            <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-7">
                <div class="lg:hidden mb-6">
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                        Sigue tu Auto
                    </a>
                </div>
                {{ $slot }}
            </div>
        </section>
    </div>
</body>
</html>
