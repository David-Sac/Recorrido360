<x-app-layout :show-footer="false">
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-slate-800">Elementos</h2>
  </x-slot>

  <div class="py-6">
    <div class="max-w-6xl px-4 mx-auto">
      @foreach (['success'=>'emerald','warning'=>'amber','error'=>'rose'] as $k=>$c)
        @if (session($k))
          <div class="px-4 py-3 mb-4 rounded-lg bg-{{ $c }}-50 text-{{ $c }}-700">{{ session($k) }}</div>
        @endif
      @endforeach

      <div class="flex items-center justify-between mb-4">
        <form method="GET" action="{{ route('elementos.index') }}">
          <input type="text" name="q" value="{{ request('q') }}"
                 placeholder="Buscar por nombre..."
                 class="w-64 px-3 py-2 border rounded">
        </form>
        <a href="{{ route('elementos.create') }}" class="px-3 py-2 text-white rounded bg-slate-800">+ Nuevo elemento</a>
      </div>

      <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
          <colgroup>
            <col> {{-- Nombre --}}
            <col> {{-- Tipo --}}
            <col> {{-- Componente --}}
            <col style="width:12rem"> {{-- Acciones --}}
          </colgroup>
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nombre</th>
              <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tipo</th>
              <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Componente</th>
              <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($elementos as $el)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-slate-800">{{ $el->nombre }}</div>
                  @if($el->media_path)
                    <div class="text-xs text-slate-500 truncate max-w-[260px]">
                      {{ '/storage/'.$el->media_path }}
                    </div>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs rounded bg-slate-100 text-slate-700">{{ $el->tipo }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $el->componente?->titulo ?? '—' }}</td>
                <td class="px-6 py-4 text-right">
                  <div class="inline-flex items-center justify-end w-full gap-2">
                    <a href="{{ route('elementos.edit', $el) }}"
                       class="px-3 py-1 text-white rounded shrink-0 bg-sky-600 hover:bg-sky-700">Editar</a>
                    <form action="{{ route('elementos.destroy', $el) }}" method="POST" class="shrink-0"
                          onsubmit="return confirm('¿Eliminar elemento? Esta acción no se puede deshacer.');">
                      @csrf @method('DELETE')
                      <button type="submit" class="px-3 py-1 text-white rounded bg-rose-600 hover:bg-rose-700">Eliminar</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-6 py-6 text-sm text-center text-slate-500">No hay elementos.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">
        {{ $elementos->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
