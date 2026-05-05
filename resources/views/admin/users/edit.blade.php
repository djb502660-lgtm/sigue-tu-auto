<x-admin.monitor.layout>
    <div class="max-w-3xl">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-slate-200">
            <div class="p-6">
                    <h3 class="font-semibold text-slate-800 mb-4">Editar cuenta</h3>
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-600">Nombre</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                                class="mt-1 block w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-600">Correo</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                                class="mt-1 block w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                            @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-semibold text-slate-600">Rol</label>
                            <select id="role" name="role" required
                                class="mt-1 block w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="usuario" @selected(old('role', $user->role) === 'usuario')>Usuario</option>
                                <option value="mantenimiento" @selected(old('role', $user->role) === 'mantenimiento')>Mantenimiento</option>
                                <option value="administrador" @selected(old('role', $user->role) === 'administrador')>Administrador</option>
                            </select>
                            @error('role') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-slate-600">Nueva contrasena (opcional)</label>
                            <input id="password" name="password" type="password"
                                class="mt-1 block w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                            @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-600">Confirmar nueva contrasena</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="mt-1 block w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" />
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancelar</a>
                            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</x-admin.monitor.layout>
