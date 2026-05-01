<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Crear cuenta</h2>
        <p class="text-sm text-slate-500 mt-1">Registra un usuario para acceder al panel.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nombre')" class="text-sm font-semibold text-slate-600" />
            <x-text-input id="name" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo electronico')" class="text-sm font-semibold text-slate-600" />
            <x-text-input id="email" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Contrasena')" class="text-sm font-semibold text-slate-600" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar contrasena')" class="text-sm font-semibold text-slate-600" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-slate-600 hover:text-slate-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500" href="{{ route('login') }}">
                {{ __('Ya tienes cuenta?') }}
            </a>

            <x-primary-button class="ms-4 !normal-case !text-sm !tracking-normal !rounded-xl !bg-emerald-600 hover:!bg-emerald-700 focus:!bg-emerald-700 focus:!ring-emerald-500">
                {{ __('Registrarme') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
