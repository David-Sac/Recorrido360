<nav x-data="{ open: false }" class="w-full bg-green-800 text-white">
  {{-- Contenedor centrado hasta un max-width razonable --}}
  <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">

      {{-- 1. LOGO a la izquierda --}}
      <div class="flex-shrink-0">
        <a href="{{ url('/') }}" class="flex items-center">
          <img class="h-10 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo Ecomuseo">
        </a>
      </div>

      {{-- 2. NAV LINKS (ocultos en móvil) --}}
      <div class="hidden md:flex md:space-x-8">
        <a href="{{ url('/') }}" class="hover:text-gray-200">Inicio</a>
        <a href="{{ url('/') }}" class="hover:text-gray-200">Panoramas</a>
        <a href="{{ url('/') }}" class="hover:text-gray-200">Componentes</a>
        <a href="{{ url('/') }}" class="hover:text-gray-200">Acerca</a>
      </div>

      {{-- 3. BOTONES Login / Register o Dropdown de usuario (ocultos en móvil) --}}
      <div class="hidden md:flex md:items-center md:space-x-4">
        @guest
          <a href="{{ route('login') }}"
             class="px-4 py-2 bg-white text-indigo-800 rounded hover:bg-gray-100">
            Login
          </a>
          <a href="{{ route('register') }}"
             class="px-4 py-2 border border-white rounded hover:bg-white hover:text-indigo-800">
            Register
          </a>
        @else
          <div class="relative" x-data="{ openDrop: false }">
            <button @click="openDrop = !openDrop"
                    class="flex items-center px-4 py-2 bg-white text-indigo-800 rounded hover:bg-gray-100 focus:outline-none">
              <span>{{ Auth::user()->name }}</span>
              <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div x-show="openDrop" @click.away="openDrop = false"
                 class="absolute right-0 mt-2 w-48 bg-white text-indigo-800 rounded shadow-lg py-1">
              <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-gray-100">Dashboard</a>
              <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">Perfil</a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">
                  Cerrar sesión
                </button>
              </form>
            </div>
          </div>
        @endguest
      </div>

      {{-- 4. HAMBURGUESA (visible solo en móvil) --}}
      <div class="md:hidden flex items-center">
        <button @click="open = !open" class="p-2 focus:outline-none">
          <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
            <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round"
                  stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
            <path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round"
                  stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

    </div>
  </div>

  {{-- MENÚ MÓVIL --}}
  <div x-show="open" class="md:hidden bg-green-700">
    <div class="px-2 pt-2 pb-3 space-y-1">
      <a href="{{ url('/') }}" class="block px-3 py-2 hover:bg-indigo-600 rounded">Inicio</a>
      <a href="{{ url('/') }}" class="block px-3 py-2 hover:bg-indigo-600 rounded">Panoramas</a>
      <a href="{{ url('/') }}" class="block px-3 py-2 hover:bg-indigo-600 rounded">Componentes</a>
      <a href="{{ url('/') }}" class="block px-3 py-2 hover:bg-indigo-600 rounded">Acerca</a>

      @guest
        <a href="{{ route('login') }}" class="block mt-2 px-3 py-2 bg-white text-indigo-800 rounded">Login</a>
        <a href="{{ route('register') }}" class="block px-3 py-2 border border-white rounded hover:bg-white hover:text-indigo-800">Register</a>
      @else
        <a href="{{ route('dashboard') }}" class="block mt-2 px-3 py-2 bg-white text-indigo-800 rounded">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full text-left px-3 py-2 hover:bg-indigo-600 rounded">
            Cerrar sesión
          </button>
        </form>
      @endguest
    </div>
  </div>
</nav>
