<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">Componentes</h2>
  </x-slot>

  {{-- Alpine data para controlar el modal --}}
  <main x-data="{ showModal: false, deleteUrl: '' }" class="py-6 max-w-7xl mx-auto px-4">

    {{-- Título y botón Nuevo --}}
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-gray-900">LISTA DE COMPONENTES</h1>
      <a href="{{ route('componentes.create') }}"
         class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
        <!-- SVG “+” -->
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-5 h-5 mr-2 inline-block"
             fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo Componente
      </a>
    </div>

    {{-- Alertas centradas (inyecta el mensaje de session) --}}
    <x-alert type="success"/>
    <x-alert type="warning"/>
    <x-alert type="error"/>

    {{-- Tabla --}}
    <div class="bg-white shadow rounded-lg overflow-visible">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Título
            </th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
              Imagen
            </th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($componentes as $c)
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">{{ $c->titulo }}</td>
              <td class="px-6 py-4 text-center">
                @if($c->imagen_path)
                  <img src="{{ asset('storage/'.$c->imagen_path) }}"
                       alt="{{ $c->titulo }}"
                       class="mx-auto h-20 w-20 object-cover rounded">
                @else
                  <span class="text-gray-400">Sin imagen</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="inline-flex items-center space-x-4">
                  <!-- Editar -->
                  <a href="{{ route('componentes.edit', $c) }}"
                     class="text-blue-600 hover:text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11 5h6m-3-3v6m5.5 2.5l-7.086 7.086a1 1 0 01-1.414 0L6 15l3.5-3.5L16.5 14.5z"/>
                    </svg>
                  </a>
                  <!-- Borrar (abre modal) -->
                  <button 
                    @click="deleteUrl='{{ route('componentes.destroy', $c) }}'; showModal = true"
                    class="text-red-600 hover:text-red-800"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-1 12a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7m5-4h4"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                No hay componentes. ¡Crea uno nuevo arriba!
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-4">
      {{ $componentes->links() }}
    </div>

    {{-- Modal de confirmación teletransportado al <body> --}}
    <template x-teleport="body">
      <div 
        x-show="showModal" 
        x-transition
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
          <h3 class="text-lg font-semibold mb-4">¿Eliminar Componente?</h3>
          <p class="mb-6">Esta acción no se puede deshacer.</p>
          <div class="flex justify-center space-x-4">
            <button 
              @click="showModal = false"
              class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
            >
              Cancelar
            </button>
            <form :action="deleteUrl" method="POST">
              @csrf @method('DELETE')
              <button 
                type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
              >
                Eliminar
              </button>
            </form>
          </div>
        </div>
      </div>
    </template>

  </main>
</x-app-layout>
