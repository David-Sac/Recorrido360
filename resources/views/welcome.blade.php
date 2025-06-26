{{-- resources/views/welcome.blade.php --}}
<x-guest-layout>
  {{-- Incluir tu navegación personalizada --}}
  @include('layouts.navigation')

  {{-- Hero section --}}
  <div class="relative bg-white overflow-hidden w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">
      <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:pb-28 xl:pb-32">
        <main class="mt-10 w-full sm:mt-12 md:mt-16 lg:mt-20 xl:mt-28">
          <div class="sm:text-center lg:text-left">
            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
              <span class="block xl:inline">Bienvenido al</span>
              <span class="block text-indigo-600 xl:inline">Ecomuseo Llacta Amaru</span>
            </h1>
            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg md:mt-5 md:text-xl max-w-3xl">
              Explora nuestros recorridos virtuales 360°, descubre la riqueza cultural y natural de la Amazonía Peruana desde tu navegador.
            </p>
            <div class="mt-5 sm:mt-8 sm:flex sm:justify-start">
              <div class="rounded-md shadow">
                <a href="{{ route('dashboard') }}"
                   class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                  Ir a Intranet
                </a>
              </div>
              <div class="mt-3 sm:mt-0 sm:ml-3">
                <a href="{{ route('register') }}"
                   class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg md:px-10">
                  Registrarse
                </a>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
    {{-- Aquí podrías añadir una imagen de fondo o A-Frame teaser, etc. --}}
  </div>

  {{-- Footer sencillo --}}
  <footer class="bg-gray-50">
    <div class="w-full py-8 px-4 overflow-hidden sm:px-6 lg:px-8">
      <p class="text-center text-base text-gray-400">
        &copy; {{ date('Y') }} Ecomuseo Llacta Amaru. Todos los derechos reservados.
      </p>
    </div>
  </footer>
</x-guest-layout>
