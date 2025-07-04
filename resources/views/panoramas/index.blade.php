{{-- resources/views/panoramas/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Panoramas 360°</h2>
  </x-slot>

  <main class="py-6 max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold">LISTA DE PANORAMAS</h1>
      <a href="{{ route('panoramas.create') }}"
         class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
        + Nuevo Panorama
      </a>
    </div>

    <x-alert type="success"/>
    <x-alert type="warning"/>
    <x-alert type="error"/>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($panoramas as $p)
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <img src="{{ asset('storage/'.$p->imagen_path) }}"
               alt="{{ $p->nombre }}"
               class="h-40 w-full object-cover">
          <div class="p-4">
            <h3 class="text-lg font-semibold">{{ $p->nombre }}</h3>
            <div class="mt-4 flex justify-between">
              <a href="{{ route('panoramas.edit', $p) }}"
                 class="text-blue-600 hover:underline">Editar</a>
              {{-- NUEVO: Gestionar Hotspots --}}
              <a href="{{ route('panoramas.hotspots.index', $p) }}"
                class="text-green-600 hover:underline">
                ⚑ Gestionar Hotspots
              </a>
              <form action="{{ route('panoramas.destroy', $p) }}" method="POST"
                    onsubmit="return confirm('Eliminar panorama?')" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <p class="col-span-3 text-center text-gray-500">No hay panoramas aún.</p>
      @endforelse
    </div>

    <div class="mt-6">
      {{ $panoramas->links() }}
    </div>
  </main>
</x-app-layout>
