<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Iniciar sesion</h2>
        <p class="text-sm text-slate-500 mt-1">Ingresa para administrar ordenes de servicio.</p>
    </div>

    <x-auth-session-status class="mb-4 text-sm text-emerald-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Correo electronico')" class="text-sm font-semibold text-slate-600" />
            <x-text-input id="email" class="block mt-1 w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Contrasena')" class="text-sm font-semibold text-slate-600" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500/30" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-slate-600 hover:text-slate-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500" href="{{ route('password.request') }}">
                    {{ __('Olvidaste tu contrasena?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 !normal-case !text-sm !tracking-normal !rounded-xl !bg-emerald-600 hover:!bg-emerald-700 focus:!bg-emerald-700 focus:!ring-emerald-500">
                {{ __('Entrar') }}
            </x-primary-button>
        </div>

        <p class="text-sm text-slate-500 mt-5 text-center">
            Si necesitas acceso, solicita tu cuenta al administrador.
        </p>
    </form>
</x-guest-layout>
