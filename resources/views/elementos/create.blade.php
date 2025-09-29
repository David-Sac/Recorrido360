<x-app-layout :show-footer="false">
  <x-slot name="head">
    {{-- Alpine (si no lo llevas en Vite) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </x-slot>

  {{-- TOOLBOX --}}
  <x-ui.toolbox
    title="Nuevo elemento"
    :back="route('elementos.index')"
    backLabel="Volver al listado"
  />

  <div class="py-6">
    <div class="max-w-3xl px-4 mx-auto">
      @if ($errors->any())
        <div class="px-4 py-3 mb-4 rounded-lg bg-rose-50 text-rose-700">
          <ul class="pl-5 space-y-1 list-disc">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('elementos.store') }}" enctype="multipart/form-data"
            class="p-6 space-y-6 bg-white border shadow-sm rounded-xl border-slate-200">
        @csrf
        @include('elementos._form', ['componentes' => $componentes])

        <div class="flex justify-end gap-2">
          <x-ui.btn-ghost href="{{ route('elementos.index') }}">Cancelar</x-ui.btn-ghost>
          <x-ui.btn-primary type="submit">Guardar</x-ui.btn-primary>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
