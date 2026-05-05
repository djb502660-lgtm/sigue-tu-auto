<x-admin.monitor.layout>
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between gap-3 mb-4">
            <h3 class="font-semibold text-slate-800">Asignacion de roles</h3>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                Gestionar cuentas
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-600">
                        <th class="px-3 py-2 text-left font-semibold">Nombre</th>
                        <th class="px-3 py-2 text-left font-semibold">Correo</th>
                        <th class="px-3 py-2 text-left font-semibold">Rol</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b border-slate-100">
                            <td class="px-3 py-2">{{ $user->name }}</td>
                            <td class="px-3 py-2">{{ $user->email }}</td>
                            <td class="px-3 py-2 capitalize">{{ $user->role }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-admin.monitor.layout>
