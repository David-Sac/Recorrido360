@php
  // Variables esperadas:
  // - $componentes: array [id => titulo]
  // - $elemento: App\Models\Elemento|null
  $e = $elemento ?? null;
@endphp

<div x-data="{ 
      tipo: @js(old('tipo', $e->tipo ?? 'datos')),
      acceptFor(t) {
        if (t === 'imagen') return 'image/*';
        if (t === 'video')  return 'video/mp4,video/webm';
        if (t === 'audio')  return 'audio/mpeg,audio/wav,audio/mp3';
        return '';
      }
    }" 
    class="space-y-4">

  {{-- Componente --}}
  <div>
    <label class="block text-sm font-medium">Componente</label>
    <select name="componente_id" class="w-full px-3 py-2 border rounded">
      @foreach($componentes as $id => $label)
        <option value="{{ $id }}" @selected(old('componente_id', $e->componente_id ?? '') == $id)>{{ $label }}</option>
      @endforeach
    </select>
    @error('componente_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  <div class="grid gap-4 md:grid-cols-2">
    {{-- Nombre --}}
    <div>
      <label class="block text-sm font-medium">Nombre</label>
      <input name="nombre" value="{{ old('nombre', $e->nombre ?? '') }}" class="w-full px-3 py-2 border rounded">
      @error('nombre')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    {{-- Tipo --}}
    <div>
      <label class="block text-sm font-medium">Tipo</label>
      <select name="tipo" x-model="tipo" class="w-full px-3 py-2 border rounded">
        <option value="datos">Datos (texto)</option>
        <option value="imagen">Imagen</option>
        <option value="video">Video (mp4/webm)</option>
        <option value="audio">Audio (mp3/wav)</option>
        <option value="otro">Otro</option>
      </select>
      @error('tipo')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
  </div>

  <div class="grid gap-4 md:grid-cols-2">
    {{-- Título --}}
    <div>
      <label class="block text-sm font-medium">Título</label>
      <input name="titulo" value="{{ old('titulo', $e->titulo ?? '') }}" class="w-full px-3 py-2 border rounded">
      @error('titulo')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    {{-- URL remota --}}
    <div x-show="['imagen','video','audio'].includes(tipo)">
      <label class="block text-sm font-medium">URL remota (opcional)</label>
      <input name="url" value="{{ old('url', $e->url ?? '') }}" class="w-full px-3 py-2 border rounded" placeholder="https://...">
      @error('url')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
  </div>

  {{-- Descripción --}}
  <div>
    <label class="block text-sm font-medium">Descripción</label>
    <textarea name="descripcion" rows="3" class="w-full px-3 py-2 border rounded">{{ old('descripcion', $e->descripcion ?? '') }}</textarea>
    @error('descripcion')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  {{-- Archivo local (solo multimedia) --}}
  <div x-show="['imagen','video','audio'].includes(tipo)">
    <label class="block text-sm font-medium">Archivo local (opcional)</label>
    <input type="file" name="media" class="w-full px-3 py-2 border rounded" 
           :accept="acceptFor(tipo)">
    @if(!empty($e?->media_path))
      <p class="mt-1 text-xs text-gray-500">Actual: {{ $e->media_path }}</p>
    @endif
    @error('media')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    <p class="mt-1 text-xs text-gray-500">Puedes usar URL o subir archivo. Si subes uno nuevo, reemplaza al anterior.</p>
  </div>

  {{-- Contenido libre (datos/otro) --}}
  <div x-show="['datos','otro'].includes(tipo)">
    <label class="block text-sm font-medium">Contenido</label>
    <textarea name="contenido" rows="4" class="w-full px-3 py-2 border rounded">{{ old('contenido', $e->contenido ?? '') }}</textarea>
    @error('contenido')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>
</div>
