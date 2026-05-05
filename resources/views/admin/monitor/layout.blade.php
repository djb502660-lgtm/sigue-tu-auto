<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel de monitoreo administrativo
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-[240px,1fr] gap-6">
                <aside class="bg-white border border-slate-200 rounded-xl shadow-sm p-3 h-fit">
                    <p class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Menu admin</p>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.monitor.dashboard') }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.monitor.dashboard') ? 'bg-emerald-100 text-emerald-800' : 'text-slate-700 hover:bg-slate-100' }}">
                            Inicio monitoreo
                        </a>
                        <a href="{{ route('admin.monitor.configuration') }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.monitor.configuration') ? 'bg-emerald-100 text-emerald-800' : 'text-slate-700 hover:bg-slate-100' }}">
                            Configuracion
                        </a>
                        <a href="{{ route('admin.monitor.account') }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.monitor.account') ? 'bg-emerald-100 text-emerald-800' : 'text-slate-700 hover:bg-slate-100' }}">
                            Cuenta
                        </a>
                        <a href="{{ route('admin.monitor.role-assignment') }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.monitor.role-assignment') ? 'bg-emerald-100 text-emerald-800' : 'text-slate-700 hover:bg-slate-100' }}">
                            Asignacion de roles
                        </a>
                        <a href="{{ route('admin.monitor.history') }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.monitor.history') ? 'bg-emerald-100 text-emerald-800' : 'text-slate-700 hover:bg-slate-100' }}">
                            Historial
                        </a>
                        <a href="{{ route('admin.monitor.notifications') }}"
                            class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.monitor.notifications') ? 'bg-emerald-100 text-emerald-800' : 'text-slate-700 hover:bg-slate-100' }}">
                            Notificaciones
                        </a>
                    </nav>
                </aside>

                <section>
                    {{ $slot }}
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
