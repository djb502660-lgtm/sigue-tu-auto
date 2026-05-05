<x-admin.monitor.layout>
    <div class="space-y-6">
        <div class="grid sm:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-slate-500">Cuentas totales</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['users'] }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-slate-500">Administradores</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['admins'] }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-slate-500">Mantenimiento</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['maintenance'] }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-slate-500">Usuarios</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['users_role'] }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <p class="text-xs text-slate-500">Eventos hoy</p>
                <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['events_today'] }}</p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Actividad reciente</h3>
            <div class="space-y-3">
                @forelse($recentEvents as $event)
                    <article class="border border-slate-100 rounded-lg px-3 py-2">
                        <p class="text-sm text-slate-800">
                            <span class="font-semibold uppercase text-xs text-slate-500">{{ $event->category }}</span>
                            - {{ $event->event }}
                        </p>
                        <p class="text-sm text-slate-600">{{ $event->description ?? 'Sin descripcion' }}</p>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $event->created_at->format('d/m/Y H:i') }} · {{ $event->actor?->email ?? 'sistema' }}
                        </p>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay eventos para monitorear.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin.monitor.layout>
