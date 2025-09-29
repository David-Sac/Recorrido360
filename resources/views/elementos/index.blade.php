<x-app-layout :show-footer="false">
  <x-slot name="head">
    {{-- Alpine para el modal (si no lo llevas en Vite) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </x-slot>

  {{-- TOOLBOX --}}
  <x-ui.toolbox
    title="Elementos"
    :subtitle="'Total: ' . $elementos->total()"
    :back="route('dashboard')"
    backLabel="Dashboard"
  >
    <x-ui.btn-primary href="{{ route('elementos.create') }}">
      <svg class="w-4 h-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16" />
      </svg>
      Nuevo elemento
    </x-ui.btn-primary>
  </x-ui.toolbox>

  <main x-data="{ showModal:false, deleteUrl:'' }" class="py-6">
    <div class="max-w-6xl px-4 mx-auto">

      {{-- Filtro / búsqueda --}}
      <form method="GET" action="{{ route('elementos.index') }}" class="mb-4">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Buscar por nombre…"
               class="w-64 px-3 py-2 rounded-md border-slate-300 focus:border-emerald-400 focus:ring-emerald-400">
      </form>

      {{-- Tabla --}}
      <div class="overflow-x-auto bg-white border shadow-sm rounded-xl border-slate-200">
        <table class="min-w-full text-sm">
          <colgroup>
            <col> {{-- Nombre --}}
            <col class="w-32"> {{-- Tipo --}}
            <col class="w-60"> {{-- Componente --}}
            <col class="w-48"> {{-- Acciones --}}
          </colgroup>

          <thead class="text-xs tracking-wider text-left uppercase text-slate-500 bg-slate-50">
            <tr>
              <th class="px-6 py-3">Nombre</th>
              <th class="px-6 py-3">Tipo</th>
              <th class="px-6 py-3">Componente</th>
              <th class="px-6 py-3 text-center">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-200">
            @forelse($elementos as $el)
              <tr>
                <td class="px-6 py-4">
                  <div class="font-medium text-slate-900">{{ $el->nombre }}</div>
                  @if($el->media_path)
                    <div class="mt-0.5 text-xs text-slate-500 truncate max-w-[320px]">
                      {{ '/storage/'.$el->media_path }}
                    </div>
                  @endif
                </td>

                <td class="px-6 py-4">
                  <span class="inline-flex items-center rounded-full bg-slate-100 text-slate-700 px-2 py-0.5 text-xs font-medium">
                    {{ $el->tipo }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  {{ $el->componente?->titulo ?? '—' }}
                </td>

                <td class="px-6 py-4 text-center">
                  <div class="inline-flex items-center gap-2">
                    <x-ui.btn-secondary href="{{ route('elementos.edit', $el) }}" class="px-2 py-1 text-xs">
                      Editar
                    </x-ui.btn-secondary>

                    <x-ui.btn-ghost class="px-2 py-1 text-xs text-rose-600 border-rose-300 hover:bg-rose-50"
                      @click="deleteUrl='{{ route('elementos.destroy', $el) }}'; showModal=true">
                      Eliminar
                    </x-ui.btn-ghost>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                  No hay elementos. Crea el primero con “Nuevo elemento”.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="mt-4">
        {{ $elementos->withQueryString()->links() }}
      </div>
    </div>

    {{-- Modal de confirmación (teleport) --}}
    <template x-teleport="body">
      <div x-show="showModal" x-transition.opacity
           class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div x-show="showModal" x-transition
             class="w-full max-w-sm p-6 bg-white shadow-lg rounded-xl">
          <div class="flex items-start justify-between">
            <h3 class="text-lg font-semibold">Eliminar elemento</h3>
            <button class="text-slate-400 hover:text-slate-600" @click="showModal=false">✕</button>
          </div>
          <p class="mt-3 text-sm text-slate-600">
            ¿Seguro que deseas eliminar este elemento? Esta acción no se puede deshacer.
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
