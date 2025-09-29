@php
  $e = $elemento ?? null;
@endphp

<div x-data="{
      tipo: @js(old('tipo', $e->tipo ?? 'datos')),
      acceptFor(t) {
        if (t === 'imagen') return 'image/*';
        if (t === 'video')  return 'video/mp4,video/webm,video/ogg';
        if (t === 'audio')  return 'audio/mpeg,audio/wav,audio/ogg,audio/mp3';
        return '*/*'; // otro
      }
    }" class="space-y-5">

  {{-- Componente --}}
  <div>
    <label class="block text-sm font-medium text-slate-700">Componente</label>
    <select name="componente_id"
            class="w-full mt-1 rounded-md border-slate-300 focus:border-emerald-400 focus:ring-emerald-400">
      <option value="">— Sin componente —</option>
      @foreach($componentes as $id => $label)
        <option value="{{ $id }}" @selected(old('componente_id', $e->componente_id ?? '') == $id)>{{ $label }}</option>
      @endforeach
    </select>
    @error('componente_id')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  <div class="grid gap-4 md:grid-cols-2">
    <div>
      <label class="block text-sm font-medium text-slate-700">Nombre *</label>
      <input name="nombre"
             value="{{ old('nombre', $e->nombre ?? '') }}"
             required
             class="w-full mt-1 rounded-md border-slate-300 focus:border-emerald-400 focus:ring-emerald-400">
      @error('nombre')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700">Tipo *</label>
      <select name="tipo" x-model="tipo" required
              class="w-full mt-1 rounded-md border-slate-300 focus:border-emerald-400 focus:ring-emerald-400">
        <option value="datos">Datos (texto)</option>
        <option value="imagen">Imagen</option>
        <option value="video">Video</option>
        <option value="audio">Audio</option>
        <option value="otro">Otro</option>
      </select>
      @error('tipo')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium text-slate-700">Descripción</label>
    <textarea name="descripcion" rows="3"
              class="w-full mt-1 rounded-md border-slate-300 focus:border-emerald-400 focus:ring-emerald-400">{{ old('descripcion', $e->descripcion ?? '') }}</textarea>
    @error('descripcion')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  {{-- DATOS --}}
  <div x-show="tipo === 'datos'" x-cloak>
    <label class="block text-sm font-medium text-slate-700">Contenido *</label>
    <textarea name="contenido" rows="6"
              class="w-full mt-1 rounded-md border-slate-300 focus:border-emerald-400 focus:ring-emerald-400"
              placeholder="Texto a mostrar...">{{ old('contenido', $e->contenido ?? '') }}</textarea>
    @error('contenido')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  {{-- MEDIA (archivo obligatorio para imagen/video/audio/otro) --}}
  <div x-show="tipo !== 'datos'" x-cloak>
    <label class="block text-sm font-medium text-slate-700">Archivo *</label>
    <input type="file" name="media"
           class="w-full mt-1 rounded-md border-slate-300 focus:border-emerald-400 focus:ring-emerald-400"
           :accept="acceptFor(tipo)">
    @error('media')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror

    @if(!empty($e?->media_path))
      <div class="p-3 mt-3 rounded bg-slate-50">
        <div class="text-sm font-medium text-slate-700">Archivo actual:</div>
        <div class="mt-2">
          @php $src = asset('storage/'.$e->media_path); @endphp
          @if(($e->tipo ?? '') === 'imagen')
            <img src="{{ $src }}" alt="" class="rounded max-h-64">
          @elseif(($e->tipo ?? '') === 'video')
            <video src="{{ $src }}" controls class="rounded max-h-64"></video>
          @elseif(($e->tipo ?? '') === 'audio')
            <audio src="{{ $src }}" controls></audio>
          @else
            <a href="{{ $src }}" target="_blank" class="underline break-all text-sky-600">{{ $src }}</a>
          @endif
        </div>
        <p class="mt-1 text-xs text-slate-500">Si seleccionas un archivo nuevo, reemplazará al anterior.</p>
      </div>
    @endif
  </div>
</div>
