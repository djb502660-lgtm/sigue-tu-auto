<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mis ordenes de servicio
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white border border-slate-200 shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <form method="GET" action="{{ route('consulta') }}" class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-500">
                            Consulta por folio o placas. Solo se muestran tus ordenes.
                        </p>
                        <div class="flex items-center gap-2">
                            <input
                                type="text"
                                name="q"
                                value="{{ $term }}"
                                placeholder="Buscar..."
                                class="rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            />
                            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white border border-slate-200 shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-600">
                                    <th class="px-3 py-2 text-left font-semibold">Folio</th>
                                    <th class="px-3 py-2 text-left font-semibold">Vehiculo</th>
                                    <th class="px-3 py-2 text-left font-semibold">Placas</th>
                                    <th class="px-3 py-2 text-left font-semibold">Estado</th>
                                    <th class="px-3 py-2 text-left font-semibold">Ingreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr class="border-b border-slate-100">
                                        <td class="px-3 py-2 font-semibold text-slate-800">{{ $order->folio_number }}</td>
                                        <td class="px-3 py-2">{{ $order->vehicle?->brand }} {{ $order->vehicle?->model }}</td>
                                        <td class="px-3 py-2">{{ $order->vehicle?->plate }}</td>
                                        <td class="px-3 py-2">{{ $order->status?->name ?? 'Sin estado' }}</td>
                                        <td class="px-3 py-2">{{ optional($order->entry_date)->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-8 text-center text-slate-500">
                                            No tienes ordenes registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
