<x-app-layout :show-footer="false">
  {{-- TOOLBOX unificada --}}
  <x-ui.toolbox
    title="Componentes"
    :subtitle="'Total: ' . $componentes->total()"
    :back="route('dashboard')"
    backLabel="Dashboard"
  >
    <x-ui.btn-primary href="{{ route('componentes.create') }}">
      <svg class="w-4 h-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16" />
      </svg>
      Nuevo componente
    </x-ui.btn-primary>
  </x-ui.toolbox>

  <main x-data="{ showModal:false, deleteUrl:'' }" class="py-6">
    <div class="px-4 mx-auto max-w-7xl">
      {{-- Alertas (si las usas) --}}
      <x-alert type="success"/>
      <x-alert type="warning"/>
      <x-alert type="error"/>

      {{-- Tabla --}}
      <div class="overflow-x-auto bg-white border shadow-sm rounded-xl border-slate-200">
        <table class="min-w-full text-sm">
          <thead class="text-xs tracking-wider text-left uppercase text-slate-500 bg-slate-50">
            <tr>
              <th class="px-6 py-3">Título</th>
              <th class="px-6 py-3 text-center">Imagen</th>
              <th class="px-6 py-3 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            @forelse ($componentes as $c)
              <tr>
                <td class="px-6 py-4">
                  <div class="font-medium text-slate-900">{{ $c->titulo }}</div>
                  @if($c->descripcion)
                    <div class="mt-0.5 text-slate-500 line-clamp-1">{{ $c->descripcion }}</div>
                  @endif
                </td>
                <td class="px-6 py-4 text-center">
                  @if($c->imagen_path)
                    <img src="{{ asset('storage/'.$c->imagen_path) }}" alt="{{ $c->titulo }}"
                         class="object-cover w-16 h-16 mx-auto rounded-md shadow-sm">
                  @else
                    <span class="text-slate-400">Sin imagen</span>
                  @endif
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="inline-flex items-center gap-2">
                    <x-ui.btn-secondary href="{{ route('componentes.edit', $c) }}" class="px-2 py-1 text-xs">
                      Editar
                    </x-ui.btn-secondary>

                    <x-ui.btn-ghost class="px-2 py-1 text-xs text-rose-600 border-rose-300 hover:bg-rose-50"
                      @click="deleteUrl='{{ route('componentes.destroy', $c) }}'; showModal=true">
                      Eliminar
                    </x-ui.btn-ghost>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-6 py-10 text-center text-slate-500">
                  No hay componentes. Crea el primero con “Nuevo componente”.
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
    </div>

    {{-- Modal de confirmación (teleport) --}}
    <template x-teleport="body">
      <div x-show="showModal" x-transition.opacity
           class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div x-show="showModal" x-transition
             class="w-full max-w-sm p-6 bg-white shadow-lg rounded-xl">
          <div class="flex items-start justify-between">
            <h3 class="text-lg font-semibold">Eliminar componente</h3>
            <button class="text-slate-400 hover:text-slate-600" @click="showModal=false">✕</button>
          </div>
          <p class="mt-3 text-sm text-slate-600">
            ¿Seguro que deseas eliminar este componente? Esta acción no se puede deshacer.
          </p>
          <div class="flex justify-end gap-2 mt-6">
            <x-ui.btn-secondary class="px-3 py-1.5" @click="showModal=false">Cancelar</x-ui.btn-secondary>
            <form x-bind:action="deleteUrl" method="POST">
              @csrf @method('DELETE')
              <x-ui.btn-primary class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700">Eliminar</x-ui.btn-primary>
            </form>
          </div>
        </div>
      </div>
    </template>
  </main>
</x-app-layout>
