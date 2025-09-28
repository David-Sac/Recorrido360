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
        Recuperar contraseña
      </h2>

      <p class="text-sm text-gray-600">
        Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
      </p>

      {{-- Estado de la sesión (mensaje de “enlace enviado”) --}}
      <x-auth-session-status class="mb-4" :status="session('status')" />

      {{-- Formulario --}}
      <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

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
                        autofocus />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Acciones --}}
        <div class="flex items-center justify-between pt-2">
          <a href="{{ route('login') }}" class="text-sm text-green-600 hover:text-green-800">
            Volver a iniciar sesión
          </a>
          <x-primary-button class="px-6 py-2">
            Enviar enlace
          </x-primary-button>
        </div>
      </form>

    </div>
  </div>
</x-guest-layout>
