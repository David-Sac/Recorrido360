<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-800">Recorridos</h2>
    </x-slot>

    <div class="py-6">
        <div class="px-4 mx-auto max-w-7xl">
        @if (session('status'))
            <div class="px-4 py-3 mb-4 rounded-lg bg-emerald-50 text-emerald-700">
            {{ session('status') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <div class="text-slate-600">Total: {{ $recorridos->total() }}</div>
            <a href="{{ route('recorridos.create') }}" class="btn btn-primary">Nuevo recorrido</a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($recorridos as $r)
            <div class="card">
                <div class="flex items-center justify-between card-header">
                <span>{{ $r->titulo }}</span>
                <span class="badge {{ $r->publicado ? 'badge-green' : 'badge-slate' }}">
                    {{ $r->publicado ? 'Publicado' : 'Borrador' }}
                </span>
                </div>
                <div class="card-body">
                <p class="mb-3 text-sm text-slate-600 line-clamp-3">{{ $r->descripcion }}</p>
                <div class="flex gap-2">
                    <a class="btn btn-secondary btn-sm" href="{{ route('recorridos.edit', $r) }}">Editar</a>
                    <form method="POST" action="{{ route('recorridos.destroy', $r) }}"
                        onsubmit="return confirm('¿Eliminar este recorrido?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
                    </form>
                </div>
                </div>
            </div>
            @empty
            <div class="text-slate-500">No hay recorridos aún.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $recorridos->links() }}</div>
        </div>
    </div>
    </x-app-layout>
