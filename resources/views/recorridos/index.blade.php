<x-app-layout :show-footer="false">
  <x-admin.toolbar
    title="Recorridos"
    :back="route('dashboard')"
    backLabel="Volver al dashboard"
    :breadcrumbs="[
      ['label'=>'Dashboard','url'=>route('dashboard')],
      ['label'=>'Recorridos']
    ]"
  >
    <x-ui.btn-primary href="{{ route('recorridos.create') }}">+ Nuevo recorrido</x-ui.btn-primary>
  </x-admin.toolbar>

  <div class="py-6">
    <div class="px-4 mx-auto max-w-7xl">
      @if (session('status'))
        <div class="px-4 py-3 mb-4 rounded-lg bg-emerald-50 text-emerald-700">
          {{ session('status') }}
        </div>
      @endif

      <div class="mb-4 text-sm text-slate-600">
        Total: <span class="font-medium">{{ $recorridos->total() }}</span>
      </div>

      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($recorridos as $r)
          <div class="overflow-hidden bg-white border rounded-lg shadow-sm border-slate-200">
            <div class="flex items-center justify-between px-4 py-3 border-b bg-slate-50 border-slate-200">
              <span class="font-semibold truncate text-slate-900">{{ $r->titulo }}</span>
              <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                  {{ $r->publicado ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                {{ $r->publicado ? 'Publicado' : 'Borrador' }}
              </span>
            </div>

            <div class="p-4">
              <p class="mb-4 text-sm text-slate-600 line-clamp-3">{{ $r->descripcion }}</p>

              <div class="flex flex-wrap gap-2">
                <x-ui.btn-secondary href="{{ route('recorridos.edit', $r) }}">Editar</x-ui.btn-secondary>

                <form method="POST" action="{{ route('recorridos.destroy', $r) }}"
                      onsubmit="return confirm('¿Eliminar este recorrido?')">
                  @csrf @method('DELETE')
                  <button type="submit"
                          class="inline-flex items-center px-3 py-1.5 rounded-md bg-rose-600 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-400">
                    Eliminar
                  </button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="text-slate-500">No hay recorridos aún.</div>
        @endforelse
      </div>

      <div class="mt-6">
        {{ $recorridos->links() }}
      </div>
    </div>
  </div>
</x-app-layout>
