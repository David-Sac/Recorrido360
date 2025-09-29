<x-app-layout :show-footer="false">
  {{-- TOOLBOX unificado --}}
  <x-ui.toolbox
    :title="'Editar panorama'"
    :subtitle="'Panorama: ' . $panorama->nombre"
    :back="route('panoramas.index')"
    backLabel="Volver a panoramas"
  >
    {{-- Atajo a gestión de hotspots --}}
    <x-ui.btn-secondary href="{{ route('panoramas.hotspots.index', $panorama) }}">
      Gestionar hotspots
    </x-ui.btn-secondary>
  </x-ui.toolbox>

  <div class="py-6">
    <div class="max-w-3xl px-4 mx-auto">

      {{-- Alerts --}}
      @foreach (['success'=>'emerald','warning'=>'amber','error'=>'rose'] as $k=>$c)
        @if (session($k))
          <div class="px-4 py-3 mb-4 rounded-lg bg-{{ $c }}-50 text-{{ $c }}-700">{{ session($k) }}</div>
        @endif
      @endforeach

      @if ($errors->any())
        <div class="px-4 py-3 mb-4 rounded-lg bg-rose-50 text-rose-700">
          <ul class="pl-5 space-y-1 list-disc">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Formulario (solo datos del panorama) --}}
      <form action="{{ route('panoramas.update', $panorama) }}"
            method="POST" enctype="multipart/form-data"
            class="overflow-hidden bg-white border rounded-lg shadow-sm border-slate-200">
        @csrf @method('PUT')

        <div class="p-5 space-y-6">
          @include('panoramas._form', ['panorama' => $panorama, 'componentes' => $componentes])

          <div class="flex justify-between pt-2">
            <x-ui.btn-ghost href="{{ route('panoramas.index') }}">Volver</x-ui.btn-ghost>
            <x-ui.btn-primary type="submit">Actualizar</x-ui.btn-primary>
          </div>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
