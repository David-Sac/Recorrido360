<x-app-layout :show-footer="false">
  {{-- TOOLBOX unificada --}}
  <x-ui.toolbox
    :title="'Editar componente'"
    :subtitle="$componente->titulo"
    :back="route('componentes.index')"
    backLabel="Volver al listado"
  />

  <main class="py-6">
    <div class="max-w-3xl px-4 mx-auto">
      <form action="{{ route('componentes.update', $componente) }}" method="POST" enctype="multipart/form-data"
            class="p-6 space-y-6 bg-white border shadow-sm rounded-xl border-slate-200">
        @csrf @method('PUT')
        @include('componentes._form')

        <div class="flex justify-end gap-2">
          <x-ui.btn-ghost href="{{ route('componentes.index') }}">Cancelar</x-ui.btn-ghost>
          <x-ui.btn-primary type="submit">Actualizar</x-ui.btn-primary>
        </div>
      </form>
    </div>
  </main>
</x-app-layout>
