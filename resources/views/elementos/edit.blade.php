<x-app-layout >
  <x-slot name="head">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </x-slot>

  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-slate-800">Editar elemento</h2>
  </x-slot>

  <div class="py-6">
    <div class="max-w-3xl px-4 mx-auto">
      @foreach (['success'=>'emerald','warning'=>'amber'] as $k=>$c)
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

      <form method="POST" action="{{ route('elementos.update', $elemento) }}" enctype="multipart/form-data" class="card">
        @csrf @method('PUT')
        <div class="space-y-4 card-body">
          @include('elementos._form', ['componentes' => $componentes, 'elemento' => $elemento])
          <div class="flex justify-between">
            <a href="{{ route('elementos.index') }}" class="btn btn-secondary">Volver</a>
            <button class="btn btn-primary" type="submit">Actualizar</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
