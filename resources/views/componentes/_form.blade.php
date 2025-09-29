{{-- Form de Componentes --}}
<div class="space-y-6">
  {{-- Título --}}
  <div>
    <x-input-label for="titulo" :value="__('Título')" />
    <x-text-input id="titulo" name="titulo" type="text"
                  class="block w-full mt-1"
                  :value="old('titulo', $componente->titulo ?? '')"
                  required />
    <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
  </div>

  {{-- Descripción --}}
  <div>
    <x-input-label for="descripcion" :value="__('Descripción')" />
    <textarea id="descripcion" name="descripcion" rows="3"
              class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-emerald-400 focus:ring-emerald-400">{{ old('descripcion', $componente->descripcion ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
  </div>

  {{-- Imagen con preview --}}
  <div
    x-data="{ preview: '{{ isset($componente) && $componente->imagen_path ? asset('storage/'.$componente->imagen_path) : '' }}' }"
    class="space-y-2"
  >
    <x-input-label for="imagen_path" :value="__('Imagen')" />
    <input id="imagen_path" name="imagen_path" type="file" accept="image/*"
           class="block w-full mt-1 border-gray-300 rounded-md shadow-sm"
           @change="
              const f = $event.target.files[0];
              if(!f){ preview=''; return; }
              const r = new FileReader();
              r.onload = e => preview = e.target.result;
              r.readAsDataURL(f);
           "
           {{ empty($componente) ? 'required' : '' }}>
    <x-input-error :messages="$errors->get('imagen_path')" class="mt-2" />

    <template x-if="preview">
      <img :src="preview" alt="Vista previa"
           class="object-cover w-32 h-32 mx-auto mt-2 rounded-md shadow-sm">
    </template>
  </div>
</div>
