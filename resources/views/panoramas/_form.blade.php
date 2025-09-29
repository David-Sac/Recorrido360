@php
  /** @var \App\Models\Panorama|null $panorama */
  $hotspotCount = isset($panorama) ? ($panorama->hotspots_count ?? $panorama->hotspots()->count()) : 0;
@endphp

<div
  x-data="{
    preview: @js(isset($panorama) && $panorama->imagen_path ? asset('storage/'.$panorama->imagen_path) : ''),
    hasHotspots: {{ $hotspotCount }},
    showConfirm: false,
    resetHotspots: false,
    candidateFile: null,
    onFileChange(e) {
      const f = e.target.files?.[0];
      if (!f) return;
      if (this.hasHotspots > 0) {
        this.candidateFile = f;
        this.showConfirm = true;
      } else {
        this.applyFile(f);
      }
    },
    applyFile(f) {
      this.preview = URL.createObjectURL(f);
      // Marcar que se deben resetear hotspots si existían
      this.resetHotspots = (this.hasHotspots > 0);
    },
    confirmChange() {
      if (this.candidateFile) this.applyFile(this.candidateFile);
      this.showConfirm = false;
    },
    cancelChange() {
      this.candidateFile = null;
      this.$refs.file.value = '';
      this.showConfirm = false;
    }
  }"
  class="space-y-6"
>
  {{-- Info: este componente puede tener más de un panorama --}}
  <div class="p-3 text-xs rounded bg-slate-50 text-slate-600">
    Un <strong>componente</strong> puede tener <strong>más de un panorama</strong>. Usa nombres claros para diferenciarlos.
  </div>

  {{-- Aviso si ya hay hotspots --}}
  @if($hotspotCount > 0)
    <div class="p-3 text-sm border rounded bg-amber-50 text-amber-800 border-amber-200">
      Este panorama tiene <strong>{{ $hotspotCount }}</strong> hotspot{{ $hotspotCount === 1 ? '' : 's' }}.
      Si cambias la <strong>imagen 360°</strong>, se <strong>eliminarán</strong> al guardar.
    </div>
  @endif

  {{-- Botón a gestión de hotspots (también aquí por pedido) --}}
  @isset($panorama)
    <div>
      <x-ui.btn-secondary href="{{ route('panoramas.hotspots.index', $panorama) }}">
        Gestionar hotspots
      </x-ui.btn-secondary>
    </div>
  @endisset

  {{-- Nombre --}}
  <div>
    <x-input-label for="nombre" :value="__('Nombre')" />
    <x-text-input id="nombre"
                  name="nombre"
                  type="text"
                  class="block w-full mt-1"
                  :value="old('nombre', $panorama->nombre ?? '')"
                  required />
    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
  </div>

  {{-- Componente asociado --}}
  <div>
    <x-input-label for="componente_id" :value="__('Componente asociado')" />
    <select id="componente_id" name="componente_id"
            class="block w-full mt-1 border rounded"
            required>
      <option value="">— Selecciona componente —</option>
      @foreach($componentes as $id => $titulo)
        <option value="{{ $id }}" {{ old('componente_id', $panorama->componente_id ?? '') == $id ? 'selected' : '' }}>
          {{ $titulo }}
        </option>
      @endforeach
    </select>
    <x-input-error :messages="$errors->get('componente_id')" class="mt-2" />
  </div>

  {{-- Imagen 360° --}}
  <div>
    <x-input-label for="imagen_path" :value="__('Imagen 360° (JPG/PNG)')" />
    <input id="imagen_path"
           x-ref="file"
           name="imagen_path"
           type="file"
           accept="image/jpeg,image/png"
           class="block w-full p-2 mt-1 border rounded"
           @change="onFileChange"
           {{ isset($panorama) ? '' : 'required' }} />
    <x-input-error :messages="$errors->get('imagen_path')" class="mt-2" />

    {{-- preview --}}
    <template x-if="preview">
      <img :src="preview" alt="Previsualización" class="w-auto h-24 mt-3 border rounded">
    </template>

    {{-- hidden flag para el backend --}}
    <input type="hidden" name="reset_hotspots" :value="resetHotspots ? 1 : 0">
  </div>

  {{-- Modal de confirmación al cambiar imagen con hotspots --}}
  <template x-if="showConfirm">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" x-transition.opacity>
      <div class="w-full max-w-md p-5 bg-white shadow-lg rounded-xl" x-transition>
        <h3 class="text-lg font-semibold text-slate-900">Cambiar imagen 360°</h3>
        <p class="mt-2 text-sm text-slate-700">
          Este panorama tiene <strong>{{ $hotspotCount }}</strong> hotspot{{ $hotspotCount === 1 ? '' : 's' }}.
          Si continúas, al guardar se <strong>eliminarán todos</strong>.
        </p>
        <div class="flex justify-end gap-2 mt-4">
          <x-ui.btn-secondary type="button" @click="cancelChange()">Cancelar</x-ui.btn-secondary>
          <x-ui.btn-primary type="button" @click="confirmChange()">Sí, cambiar imagen</x-ui.btn-primary>
        </div>
      </div>
    </div>
  </template>
</div>
