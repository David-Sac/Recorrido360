<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      {{-- Logo y título --}}
      <div class="flex items-center">
        <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">
          Ecomuseo Llacta Amaru
        </a>
      </div>

      {{-- Enlaces desktop --}}
      <div class="hidden sm:flex sm:items-center sm:space-x-6">
        @guest
          <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">Login</a>
          <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-800">Registro</a>
        @else
          <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800">Intranet</a>

          <div class="relative" x-data="{ openDrop: false }">
            <button @click="openDrop = !openDrop"
                    class="flex items-center space-x-2 text-gray-600 hover:text-gray-800 focus:outline-none">
              <span>{{ Auth::user()->name }}</span>
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div x-show="openDrop" @click.away="openDrop = false"
                 class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg py-1">
              <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Perfil</a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                  Cerrar sesión
                </button>
              </form>
            </div>
          </div>
        @endguest
      </div>

      {{-- Botón mobile --}}
      <div class="flex items-center sm:hidden">
        <button @click="open = !open" class="p-2 rounded-md focus:outline-none">
          <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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

  {{-- Menú mobile --}}
  <div x-show="open" class="sm:hidden border-t border-gray-200">
    <div class="px-2 py-3 space-y-1">
      @guest
        <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Login</a>
        <a href="{{ route('register') }}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Registro</a>
      @else
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Intranet</a>
        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Perfil</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full text-left px-4 py-2 text-gray-600 hover:bg-gray-100">
            Cerrar sesión
          </button>
        </form>
      @endguest
    </div>
  </div>
</nav>
