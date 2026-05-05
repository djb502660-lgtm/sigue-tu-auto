<x-admin.monitor.layout>
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
        <h3 class="font-semibold text-slate-800 mb-4">Historial del sistema</h3>
        <div class="space-y-3">
            @forelse($events as $event)
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
                <p class="text-sm text-slate-500">No hay historial para mostrar.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $events->links() }}</div>
    </div>
</x-admin.monitor.layout>
