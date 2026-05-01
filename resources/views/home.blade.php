<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sigue tu Auto | Inicio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-100 text-slate-800">
    <div class="min-h-screen">
        <header class="bg-slate-900 text-white border-b border-white/10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1-1V6a1 1 0 00-1-1h-1m-1 1a1 1 0 011-1h2a1 1 0 011 1v10a1 1 0 01-1 1h-1m-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold">Sigue tu Auto</p>
                        <p class="text-xs text-slate-400">Sistema de seguimiento de ordenes</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @auth
                        <a
                            href="{{ route('sistema') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 text-white px-4 py-2 text-sm font-semibold hover:bg-emerald-600 transition"
                        >
                            Ir al sistema
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 text-white px-4 py-2 text-sm font-semibold hover:bg-white/20 transition"
                        >
                            Iniciar sesion
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 text-white px-4 py-2 text-sm font-semibold hover:bg-emerald-600 transition"
                        >
                            Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
            <section class="rounded-3xl bg-gradient-to-r from-emerald-600 via-emerald-700 to-slate-900 text-white p-6 sm:p-10 shadow-lg">
                <div class="grid md:grid-cols-2 gap-8 items-center">
                    <div>
                        <p class="text-emerald-100 text-sm font-semibold uppercase tracking-wide">Bienvenido</p>
                        <h1 class="mt-2 text-3xl sm:text-4xl font-bold leading-tight">
                            Controla tu taller con una pagina simple y llamativa
                        </h1>
                        <p class="mt-4 text-emerald-50/90 text-sm sm:text-base">
                            Registra ordenes de servicio, actualiza estados y consulta avances del vehiculo en un solo lugar.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            @auth
                                <a
                                    href="{{ route('sistema') }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-white text-emerald-700 px-5 py-2.5 text-sm font-semibold hover:bg-emerald-50 transition"
                                >
                                    Ir al sistema
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-white text-emerald-700 px-5 py-2.5 text-sm font-semibold hover:bg-emerald-50 transition"
                                >
                                    Entrar al panel
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <article class="rounded-2xl bg-white/15 border border-white/20 p-4">
                            <p class="text-xs text-emerald-100">Ordenes</p>
                            <p class="text-2xl font-bold mt-1">Rapidas</p>
                        </article>
                        <article class="rounded-2xl bg-white/15 border border-white/20 p-4">
                            <p class="text-xs text-emerald-100">Consulta</p>
                            <p class="text-2xl font-bold mt-1">En vivo</p>
                        </article>
                        <article class="rounded-2xl bg-white/15 border border-white/20 p-4 col-span-2">
                            <p class="text-xs text-emerald-100">Comunicacion</p>
                            <p class="text-2xl font-bold mt-1">Cliente + Taller</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="mt-8 grid sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h2 class="font-semibold text-slate-800">1. Registra</h2>
                    <p class="mt-2 text-sm text-slate-500">Captura cliente, vehiculo y trabajo solicitado en minutos.</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h2 class="font-semibold text-slate-800">2. Actualiza</h2>
                    <p class="mt-2 text-sm text-slate-500">Cambia el estado de la orden y manten el proceso claro.</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h2 class="font-semibold text-slate-800">3. Consulta</h2>
                    <p class="mt-2 text-sm text-slate-500">El cliente revisa avances desde el asistente virtual.</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
