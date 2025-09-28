<div class="space-y-5">
  <div>
    <label class="block mb-1 text-sm font-medium text-slate-700">Título</label>
    <input type="text" name="titulo"
           value="{{ old('titulo', $recorrido->titulo ?? '') }}"
           class="w-full rounded-md border-slate-300 focus:border-emerald-400 focus:ring-emerald-400"
           required>
    @error('titulo')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  <div>
    <label class="block mb-1 text-sm font-medium text-slate-700">Descripción</label>
    <textarea name="descripcion" rows="4"
              class="w-full rounded-md border-slate-300 focus:border-emerald-400 focus:ring-emerald-400"
              placeholder="Breve descripción del recorrido…">{{ old('descripcion', $recorrido->descripcion ?? '') }}</textarea>
    @error('descripcion')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  <div class="flex items-center gap-3">
    <input id="publicado" type="checkbox" name="publicado" value="1"
           @checked(old('publicado', $recorrido->publicado ?? false))
           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
    <label for="publicado" class="text-sm text-slate-700">Publicado</label>
  </div>
</div>
