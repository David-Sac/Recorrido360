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

      {{-- Título --}}
      <h2 class="mt-2 text-2xl font-extrabold text-center text-gray-900">
        Crear cuenta
      </h2>

      {{-- Errores globales (opcional) --}}
      @if ($errors->any())
        <div class="px-3 py-2 text-sm rounded bg-rose-50 text-rose-700">
          <ul class="pl-5 space-y-1 list-disc">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Formulario --}}
      <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        {{-- Nombre --}}
        <div>
          <x-input-label for="name" value="Nombre completo" />
          <x-text-input id="name"
                        class="block w-full mt-1"
                        type="text"
                        name="name"
                        :value="old('name')"
                        placeholder="Ej: Juan Pérez"
                        required
                        autofocus
                        autocomplete="name" />
          <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- Correo --}}
        <div>
          <x-input-label for="email" value="Correo electrónico" />
          <x-text-input id="email"
                        class="block w-full mt-1"
                        type="email"
                        name="email"
                        :value="old('email')"
                        placeholder="tu@correo.com"
                        required
                        autocomplete="username" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Contraseña --}}
        <div>
          <x-input-label for="password" value="Contraseña" />
          <x-text-input id="password"
                        class="block w-full mt-1"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password" />
          <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres.</p>
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirmar contraseña --}}
        <div>
          <x-input-label for="password_confirmation" value="Confirmar contraseña" />
          <x-text-input id="password_confirmation"
                        class="block w-full mt-1"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password" />
          <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Acciones --}}
        <div class="flex items-center justify-between pt-2">
          <a href="{{ route('login') }}" class="text-sm text-green-600 hover:text-green-800">
            ¿Ya tienes una cuenta? Inicia sesión
          </a>
          <x-primary-button class="px-6 py-2">
            Registrarme
          </x-primary-button>
        </div>
      </form>

    </div>
  </div>
</x-guest-layout>
