<x-app-layout>
    <x-slot name="head">
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Activa drag & drop
            const lista = document.getElementById('lista-orden');
            if (lista) new Sortable(lista, { animation: 150, ghostClass: 'bg-yellow-50' });

            // Guardar orden
            const btn = document.getElementById('btn-guardar-orden');
            if (btn) {
                btn.addEventListener('click', () => {
                    const ids = Array.from(document.querySelectorAll('#lista-orden [data-id]'))
                        .map(li => li.dataset.id);

                    fetch(@json(route('recorridos.reorder', $recorrido)), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token())
                        },
                        body: JSON.stringify({ orden: ids })
                    })
                    .then(r => r.ok ? location.reload() : Promise.reject())
                    .catch(() => alert('No se pudo guardar el orden'));
                });
            }
        });
        </script>
    </x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-800">Editar recorrido</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl px-4 mx-auto">
            @if (session('status'))
                <div class="px-4 py-3 mb-4 rounded-lg bg-emerald-50 text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="px-4 py-3 mb-4 rounded-lg bg-rose-50 text-rose-700">
                    <ul class="pl-5 space-y-1 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('recorridos.update', $recorrido) }}" class="card">
                @csrf @method('PUT')
                <div class="space-y-4 card-body">
                    @include('recorridos._form', ['recorrido' => $recorrido])
                    <div class="flex justify-between">
                        <a href="{{ route('recorridos.index') }}" class="btn btn-secondary">Volver</a>
                        <button class="btn btn-primary" type="submit">Actualizar</button>
                    </div>
                </div>
            </form>

            {{-- Orden de panoramas (drag & drop) --}}
            <div class="mt-6 card">
                <div class="card-body">
                    <h3 class="mb-3 text-lg font-semibold">Orden de panoramas</h3>

                    <ul id="lista-orden" class="space-y-2">
                        @forelse($recorrido->panoramas as $p)
                            <li class="flex items-center justify-between gap-3 p-2 rounded bg-slate-50" data-id="{{ $p->id }}">
                                <div class="flex items-center gap-3">
                                    @if(!empty($p->imagen_path))
                                        <img src="{{ asset('storage/'.$p->imagen_path) }}" class="object-cover w-20 h-12 rounded" alt="">
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ $p->nombre }}</div>
                                        <div class="text-xs text-slate-500">ID {{ $p->id }}</div>
                                    </div>
                                </div>

                                <form action="{{ route('recorridos.detach', [$recorrido, $p]) }}" method="POST"
                                      onsubmit="return confirm('¿Quitar panorama del recorrido?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Quitar</button>
                                </form>
                            </li>
                        @empty
                            <li class="p-3 text-sm text-center text-slate-500">Este recorrido aún no tiene panoramas.</li>
                        @endforelse
                    </ul>

                    <div class="mt-3 text-right">
                        <button id="btn-guardar-orden" class="btn btn-primary">Guardar orden</button>
                    </div>
                </div>
            </div>

            {{-- Panoramas disponibles (añadir) --}}
            <div class="mt-6 card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold">Añadir panoramas</h3>
                        <form method="GET" action="{{ route('recorridos.edit', $recorrido) }}">
                            <input type="text" name="q" value="{{ request('q') }}" class="w-64 px-3 py-2 border rounded"
                                   placeholder="Buscar por nombre...">
                        </form>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        @forelse($disponibles as $p)
                            <div class="flex items-center justify-between gap-3 p-2 rounded bg-slate-50">
                                <div class="flex items-center gap-3">
                                    @if(!empty($p->imagen_path))
                                        <img src="{{ asset('storage/'.$p->imagen_path) }}" class="object-cover w-20 h-12 rounded" alt="">
                                    @endif
                                    <div class="font-medium">{{ $p->nombre }}</div>
                                </div>
                                <form action="{{ route('recorridos.attach', [$recorrido, $p]) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Añadir</button>
                                </form>
                            </div>
                        @empty
                            <div class="p-3 text-sm text-center text-slate-500 md:col-span-2">
                                No hay panoramas disponibles para añadir.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
