<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-slate-800">Elementos</h2>
  </x-slot>

  <div class="py-6">
    <div class="max-w-6xl px-4 mx-auto">
      @if (session('success'))
        <div class="px-4 py-3 mb-4 rounded-lg bg-emerald-50 text-emerald-700">
          {{ session('success') }}
        </div>
      @endif
      @if (session('warning'))
        <div class="px-4 py-3 mb-4 rounded-lg bg-amber-50 text-amber-700">
          {{ session('warning') }}
        </div>
      @endif
      @if (session('error'))
        <div class="px-4 py-3 mb-4 rounded-lg bg-rose-50 text-rose-700">
          {{ session('error') }}
        </div>
      @endif

      <div class="flex items-center justify-between mb-4">
        <form method="GET" action="{{ route('elementos.index') }}">
          <input type="text" name="q" value="{{ request('q') }}"
                 placeholder="Buscar por nombre o título..."
                 class="w-64 px-3 py-2 border rounded">
        </form>
        <a href="{{ route('elementos.create') }}" class="px-3 py-2 text-white rounded bg-slate-800">+ Nuevo elemento</a>
      </div>

<div class="overflow-x-auto bg-white rounded-lg shadow">
  <table class="min-w-full divide-y divide-gray-200">
    {{-- Fuerza ancho fijo para la columna de Acciones --}}
    <colgroup>
      <col> {{-- Nombre --}}
      <col> {{-- Tipo --}}
      <col> {{-- Título --}}
      <col> {{-- Componente --}}
      <col style="width:12rem"> {{-- Acciones --}}
    </colgroup>

    <thead class="bg-gray-50">
      <tr>
        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nombre</th>
        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tipo</th>
        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Título</th>
        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Componente</th>
        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Acciones</th>
      </tr>
    </thead>

    <tbody class="bg-white divide-y divide-gray-200">
      @forelse($elementos as $el)
        <tr>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="font-medium text-slate-800">{{ $el->nombre }}</div>
            @if($el->url || $el->media_path)
              <div class="text-xs text-slate-500 truncate max-w-[260px]">
                {{ $el->url ?? $el->media_path }}
              </div>
            @endif
          </td>

          <td class="px-6 py-4 whitespace-nowrap">
            <span class="px-2 py-1 text-xs rounded bg-slate-100 text-slate-700">{{ $el->tipo }}</span>
          </td>

          <td class="px-6 py-4 whitespace-nowrap">{{ $el->titulo ?? '—' }}</td>

          <td class="px-6 py-4 whitespace-nowrap">{{ $el->componente?->titulo ?? '—' }}</td>

          {{-- ACCIONES (no se encoge y siempre visibles) --}}
          <td class="px-6 py-4 text-right">
            <div class="inline-flex items-center justify-end w-full gap-2">
              <a href="{{ route('elementos.edit', $el) }}"
                 class="px-3 py-1 text-white rounded shrink-0 bg-sky-600 hover:bg-sky-700">Editar</a>

              <form action="{{ route('elementos.destroy', $el) }}" method="POST" class="shrink-0"
                    onsubmit="return confirm('¿Eliminar elemento? Esta acción no se puede deshacer.');">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-3 py-1 text-white rounded bg-rose-600 hover:bg-rose-700">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-6 py-6 text-sm text-center text-slate-500">No hay elementos.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>


      <div class="mt-4">
        {{ $elementos->withQueryString()->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
