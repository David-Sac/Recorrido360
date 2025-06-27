{{-- resources/views/panoramas/_form.blade.php --}}
<div class="space-y-4">
  {{-- Nombre --}}
  <div>
    <x-input-label for="nombre" :value="__('Nombre')" />
    <x-text-input id="nombre"
                  name="nombre"
                  type="text"
                  class="mt-1 block w-full"
                  :value="old('nombre', $panorama->nombre ?? '')"
                  required />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
  </div>

  {{-- Selector de componente padre --}}
  <div>
    <x-input-label for="componente_id" :value="__('Componente asociado')" />
    <select id="componente_id"
            name="componente_id"
            class="mt-1 block w-full border rounded"
            required>
      <option value="">— Selecciona componente —</option>
      @foreach($componentes as $id => $titulo)
        <option value="{{ $id }}"
          {{ old('componente_id', $panorama->componente_id ?? '') == $id ? 'selected' : '' }}>
          {{ $titulo }}
        </option>
      @endforeach
    </select>
    <x-input-error :messages="$errors->get('componente_id')" class="mt-2" />
  </div>

  {{-- Campo para subir la imagen 360° --}}
  <div>
    <x-input-label for="imagen_path" :value="__('Imagen 360° (jpg/png)')" />
    <input id="imagen_path"
           name="imagen_path"
           type="file"
           accept="image/jpeg,image/png"
           class="mt-1 block w-full border rounded p-2"
           {{ isset($panorama) ? '' : 'required' }} />
    @if(isset($panorama) && $panorama->imagen_path)
      <p class="mt-1 text-sm text-gray-500">Imagen actual:</p>
      <img src="{{ asset('storage/'.$panorama->imagen_path) }}"
           alt="Panorama"
           class="h-24 w-auto mt-1 rounded border" />
    @endif
    <x-input-error :messages="$errors->get('imagen_path')" class="mt-2" />
  </div>
</div>
