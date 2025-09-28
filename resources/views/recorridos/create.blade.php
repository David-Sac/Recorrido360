<x-app-layout :show-footer="false">
  <x-admin.toolbar
    title="Nuevo recorrido"
    :back="route('recorridos.index')"
    backLabel="Volver a recorridos"
    :breadcrumbs="[
      ['label'=>'Dashboard','url'=>route('dashboard')],
      ['label'=>'Recorridos','url'=>route('recorridos.index')],
      ['label'=>'Nuevo']
    ]"
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

      <form method="POST" action="{{ route('recorridos.store') }}" class="overflow-hidden bg-white border rounded-lg shadow-sm border-slate-200">
        @csrf
        <div class="p-5 space-y-5">
          @include('recorridos._form', ['recorrido' => null])

          <div class="flex justify-end gap-2 pt-2">
            <x-ui.btn-ghost href="{{ route('recorridos.index') }}">Cancelar</x-ui.btn-ghost>
            <x-ui.btn-primary type="submit">Guardar</x-ui.btn-primary>
          </div>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
