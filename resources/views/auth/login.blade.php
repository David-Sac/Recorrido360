{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
  {{-- Contenedor centrado en toda la pantalla --}}
  <div class="flex items-center justify-center min-h-screen bg-gray-50">
    {{-- Tarjeta blanca con sombra --}}
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg">
      
      {{-- Logo arriba centrado --}}
      <div class="flex justify-center">
        <a href="{{ url('/') }}">
          <img src="{{ asset('images/logo.png') }}" alt="Logo Ecomuseo" class="h-12">
        </a>
      </div>

      {{-- Título del form --}}
      <h2 class="mt-2 text-center text-2xl font-extrabold text-gray-900">
        Iniciar sesión
      </h2>

      {{-- Estado de la sesión (éxito / error general) --}}
      <x-auth-session-status class="mb-4" :status="session('status')" />

      {{-- Formulario --}}
      <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        {{-- Email --}}
        <div>
          <x-input-label for="email" :value="__('Correo electrónico')" />
          <x-text-input id="email"
                        class="block mt-1 w-full"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required autofocus
                        autocomplete="username" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div>
          <x-input-label for="password" :value="__('Contraseña')" />
          <x-text-input id="password"
                        class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password" />
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center">
          <input id="remember_me"
                 type="checkbox"
                 name="remember"
                 class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" />
          <label for="remember_me" class="ml-2 block text-sm text-gray-700">
            {{ __('Recuérdame') }}
          </label>
        </div>

        {{-- Forgot & Submit --}}
        <div class="flex items-center justify-between">
          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}"
               class="text-sm text-green-600 hover:text-green-800">
              {{ __('¿Olvidaste tu contraseña?') }}
            </a>
          @endif

          <x-primary-button class="px-6 py-2">
            {{ __('Entrar') }}
          </x-primary-button>
        </div>
      </form>
    </div>
  </div>
</x-guest-layout>
