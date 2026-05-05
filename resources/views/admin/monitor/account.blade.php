<x-admin.monitor.layout>
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
        <h3 class="font-semibold text-slate-800">Cuenta</h3>
        <p class="text-sm text-slate-600 mt-2">
            Datos de la cuenta administradora conectada.
        </p>
        <div class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
            <div class="rounded-lg border border-slate-200 p-3">
                <p class="text-xs text-slate-500">Nombre</p>
                <p class="font-medium text-slate-800">{{ auth()->user()->name }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-3">
                <p class="text-xs text-slate-500">Correo</p>
                <p class="font-medium text-slate-800">{{ auth()->user()->email }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-3">
                <p class="text-xs text-slate-500">Rol</p>
                <p class="font-medium text-slate-800">{{ auth()->user()->role }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 p-3">
                <p class="text-xs text-slate-500">Ultimo acceso</p>
                <p class="font-medium text-slate-800">{{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</x-admin.monitor.layout>
