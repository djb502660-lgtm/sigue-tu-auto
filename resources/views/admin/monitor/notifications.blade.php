<x-admin.monitor.layout>
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
        <h3 class="font-semibold text-slate-800 mb-4">Notificaciones</h3>
        <p class="text-sm text-slate-500 mb-4">
            Eventos recientes del sistema, incluyendo consultas de usuarios y cambios operativos.
        </p>
        <div class="space-y-3">
            @forelse($notifications as $notification)
                <article class="border border-slate-100 rounded-lg px-3 py-2">
                    <p class="text-sm font-medium text-slate-800">{{ $notification->event }}</p>
                    <p class="text-sm text-slate-600">{{ $notification->description ?? 'Sin descripcion' }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $notification->created_at->format('d/m/Y H:i') }} · {{ $notification->actor?->email ?? 'sistema' }}
                    </p>
                </article>
            @empty
                <p class="text-sm text-slate-500">No hay notificaciones disponibles.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $notifications->links() }}</div>
    </div>
</x-admin.monitor.layout>
