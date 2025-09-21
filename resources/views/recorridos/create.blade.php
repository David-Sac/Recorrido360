<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-slate-800">Nuevo recorrido</h2>
    </x-slot>

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

        <form method="POST" action="{{ route('recorridos.store') }}" class="card">
            @csrf
            <div class="space-y-4 card-body">
            @include('recorridos._form', ['recorrido' => null])
            <div class="flex justify-end gap-2">
                <a href="{{ route('recorridos.index') }}" class="btn btn-secondary">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
            </div>
        </form>
        </div>
    </div>
</x-app-layout>
