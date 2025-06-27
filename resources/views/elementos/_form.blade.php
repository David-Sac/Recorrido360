{{-- resources/views/elementos/_form.blade.php --}}
<div class="space-y-4">

  {{-- Componente --}}
  <div>
    <x-input-label for="componente_id" :value="__('Componente')" />
    <select id="componente_id" name="componente_id"
            class="mt-1 block w-full border rounded">
      @foreach($componentes as $id => $titulo)
        <option value="{{ $id }}"
          {{ old('componente_id', $elemento->componente_id ?? '') == $id ? 'selected' : '' }}>
          {{ $titulo }}
        </option>
      @endforeach
    </select>
    <x-input-error :messages="$errors->get('componente_id')" class="mt-2" />
  </div>

  {{-- Nombre --}}
  <div>
    <x-input-label for="nombre" :value="__('Nombre')" />
    <x-text-input id="nombre" name="nombre" type="text"
                  class="mt-1 block w-full"
                  :value="old('nombre', $elemento->nombre ?? '')"
                  required />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
  </div>

  {{-- Tipo --}}
  <div>
    <x-input-label for="tipo" :value="__('Tipo')" />
    <select id="tipo" name="tipo" class="mt-1 block w-full border rounded">
      @foreach(['datos','video','imagen','audio','otro'] as $tipo)
        <option value="{{ $tipo }}"
          {{ old('tipo', $elemento->tipo ?? '') == $tipo ? 'selected' : '' }}>
          {{ ucfirst($tipo) }}
        </option>
      @endforeach
    </select>
    <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
  </div>

  {{-- Contenido --}}
  <div>
    <x-input-label for="contenido" :value="__('Contenido')" />
    <textarea id="contenido" name="contenido"
              class="mt-1 block w-full border rounded"
              rows="4">{{ old('contenido', $elemento->contenido ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('contenido')" class="mt-2" />
  </div>

</div>
