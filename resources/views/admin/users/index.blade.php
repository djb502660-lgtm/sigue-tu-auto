<x-admin.monitor.layout>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
        <div class="p-6 text-slate-800">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <h3 class="font-semibold text-slate-800">Gestion de cuentas</h3>
                        <a href="{{ route('admin.users.create') }}"
                            class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Nueva cuenta
                        </a>
                    </div>
                    @if (session('status'))
                        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->has('delete'))
                        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
                            {{ $errors->first('delete') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-600">
                                    <th class="px-3 py-2 text-left font-semibold">Nombre</th>
                                    <th class="px-3 py-2 text-left font-semibold">Correo</th>
                                    <th class="px-3 py-2 text-left font-semibold">Rol</th>
                                    <th class="px-3 py-2 text-left font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="border-b border-slate-100">
                                        <td class="px-3 py-2">{{ $user->name }}</td>
                                        <td class="px-3 py-2">{{ $user->email }}</td>
                                        <td class="px-3 py-2 capitalize">{{ $user->role }}</td>
                                        <td class="px-3 py-2">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                    class="text-emerald-700 hover:text-emerald-900 font-medium">
                                                    Editar
                                                </a>
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                    onsubmit="return confirm('¿Eliminar esta cuenta?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-700 hover:text-rose-900 font-medium">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-6 text-center text-slate-500">
                                            No hay cuentas registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
        </div>
    </div>
</x-admin.monitor.layout>
