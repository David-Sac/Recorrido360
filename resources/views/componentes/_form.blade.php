{{-- resources/views/componentes/_form.blade.php --}}
<div class="space-y-4">
  {{-- Título --}}
  <div>
    <x-input-label for="titulo" :value="__('Título')" />
    <x-text-input id="titulo"
                  name="titulo"
                  type="text"
                  class="mt-1 block w-full"
                  :value="old('titulo', $componente->titulo ?? '')"
                  required />
    <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
  </div>

  {{-- Descripción --}}
  <div>
    <x-input-label for="descripcion" :value="__('Descripción')" />
    <textarea id="descripcion"
              name="descripcion"
              class="mt-1 block w-full border rounded"
              rows="3">{{ old('descripcion', $componente->descripcion ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
  </div>

  {{-- Imagen con preview --}}
  <div
    x-data="{
      preview: '{{ isset($componente) && $componente->imagen_path ? asset('storage/'.$componente->imagen_path) : '' }}'
    }"
    class="space-y-2"
  >
    <x-input-label for="imagen_path" :value="__('Imagen')" />
    <input
      id="imagen_path"
      name="imagen_path"
      type="file"
      accept="image/*"
      @change="
        const file = $event.target.files[0];
        if (!file) { preview = ''; return; }
        const reader = new FileReader();
        reader.onload = e => preview = e.target.result;
        reader.readAsDataURL(file);
      "
      class="mt-1 block w-full border rounded"
      {{ empty($componente) ? 'required' : '' }}
    />
    <x-input-error :messages="$errors->get('imagen_path')" class="mt-2" />

    {{-- Vista previa --}}
    <template x-if="preview">
      <img
        :src="preview"
        alt="Vista previa"
        class="mt-4 mx-auto h-32 w-32 object-cover rounded shadow" />
      >
    </template>
  </div>
</div>
