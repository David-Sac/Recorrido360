{{-- resources/views/panoramas/index.blade.php --}}
<x-app-layout :show-footer="false">
  <x-ui.toolbox
    title="Panoramas 360°"
    :subtitle="'Total: ' . $panoramas->total()"
    :back="route('dashboard')"
    backLabel="Dashboard"
  >
    <x-ui.btn-primary href="{{ route('panoramas.create') }}">
      <svg class="w-4 h-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16" />
      </svg>
      Nuevo panorama
    </x-ui.btn-primary>
  </x-ui.toolbox>

  <div class="py-6">
    <div class="px-4 mx-auto max-w-7xl">
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($panoramas as $p)
          <div class="overflow-hidden bg-white border rounded-lg shadow-sm border-slate-200">
            <div class="aspect-[16/9] bg-slate-100">
              @if($p->imagen_path)
                <img src="{{ asset('storage/'.$p->imagen_path) }}"
                     alt="{{ $p->nombre }}"
                     class="object-cover w-full h-full">
              @endif
            </div>
            <div class="p-4">
              <h3 class="font-semibold text-slate-900">{{ $p->nombre }}</h3>

              <div class="flex flex-wrap items-center gap-2 mt-4">
                <x-ui.btn-secondary href="{{ route('panoramas.edit', $p) }}" class="btn-sm">Editar</x-ui.btn-secondary>
                <x-ui.btn-secondary href="{{ route('panoramas.hotspots.index', $p) }}" class="btn-sm">
                  Gestionar hotspots
                </x-ui.btn-secondary>

                <form action="{{ route('panoramas.destroy', $p) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar panorama?')">
                  @csrf @method('DELETE')
                  <x-ui.btn-ghost class="btn-sm text-rose-600 border-rose-300 hover:bg-rose-50">
                    Eliminar
                  </x-ui.btn-ghost>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="p-6 text-center bg-white border rounded-lg col-span-full text-slate-500 border-slate-200">
            No hay panoramas aún. Crea el primero con “Nuevo panorama”.
          </div>
        @endforelse
      </div>

      <div class="mt-6">
        {{ $panoramas->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
