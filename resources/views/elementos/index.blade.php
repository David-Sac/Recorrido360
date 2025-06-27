{{-- resources/views/elementos/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Elementos</h2>
  </x-slot>

  <main x-data="{ showModal:false, deleteUrl:'' }"
        class="py-6 max-w-7xl mx-auto px-4">

    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold">LISTA DE ELEMENTOS</h1>
      <a href="{{ route('elementos.create') }}"
         class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
        + Nuevo Elemento
      </a>
    </div>

    <x-alert type="success"/>
    <x-alert type="warning"/>
    <x-alert type="error"/>

    <div class="bg-white shadow rounded-lg overflow-visible">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Componente</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contenido</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($elementos as $e)
            <tr>
              <td class="px-6 py-4 whitespace-nowrap">{{ $e->componente->titulo }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ $e->nombre }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($e->tipo) }}</td>
              <td class="px-6 py-4 whitespace-nowrap max-w-xs truncate">{{ $e->contenido }}</td>
              <td class="px-6 py-4 text-center">
                <div class="inline-flex items-center space-x-4">
                  {{-- Editar --}}
                  <a href="{{ route('elementos.edit', $e) }}"
                     class="text-blue-600 hover:text-blue-800">
                    ✎
                  </a>
                  {{-- Borrar abre modal --}}
                  <button @click="deleteUrl='{{ route('elementos.destroy', $e) }}'; showModal=true"
                          class="text-red-600 hover:text-red-800">
                    🗑
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                No hay elementos. ¡Crea uno nuevo arriba!
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $elementos->links() }}
    </div>

    {{-- Modal de confirmación --}}
    <template x-teleport="body">
      <div x-show="showModal" x-transition
           class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
          <h3 class="text-lg font-semibold mb-4">¿Eliminar Elemento?</h3>
          <p class="mb-6">Esta acción no se puede deshacer.</p>
          <div class="flex justify-center space-x-4">
            <button @click="showModal=false"
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
              Cancelar
            </button>
            <form :action="deleteUrl" method="POST">
              @csrf @method('DELETE')
              <button type="submit"
                      class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Eliminar
              </button>
            </form>
          </div>
        </div>
      </div>
    </template>

  </main>
</x-app-layout>
