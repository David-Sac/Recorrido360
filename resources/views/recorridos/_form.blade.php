@php
    $r = $recorrido;
@endphp

<div>
    <label class="block mb-1 text-sm font-medium text-slate-700">Título</label>
    <input type="text" name="titulo" value="{{ old('titulo', $r->titulo ?? '') }}"
            class="w-full rounded-xl border-slate-300 focus:border-emerald-400 focus:ring-emerald-400" required>
</div>

<div>
    <label class="block mb-1 text-sm font-medium text-slate-700">Slug (opcional)</label>
    <input type="text" name="slug" value="{{ old('slug', $r->slug ?? '') }}"
            class="w-full rounded-xl border-slate-300 focus:border-emerald-400 focus:ring-emerald-400"
            placeholder="se-generara-automatico-si-lo-dejas-vacio">
</div>

<div>
    <label class="block mb-1 text-sm font-medium text-slate-700">Descripción</label>
    <textarea name="descripcion" rows="4"
                class="w-full rounded-xl border-slate-300 focus:border-emerald-400 focus:ring-emerald-400">{{ old('descripcion', $r->descripcion ?? '') }}</textarea>
</div>

<div class="flex items-center gap-3">
    <input id="publicado" type="checkbox" name="publicado" value="1"
            @checked(old('publicado', $r->publicado ?? false))
            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
    <label for="publicado" class="text-sm text-slate-700">Publicado</label>
</div>
