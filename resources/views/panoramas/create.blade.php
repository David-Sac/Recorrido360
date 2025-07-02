{{-- resources/views/panoramas/create.blade.php --}}
<x-app-layout>

  <x-slot name="header">
    <h2 class="font-semibold text-xl">Crear Panorama</h2>
  </x-slot>

  <main x-data="simplePreview()" class="py-6 max-w-3xl mx-auto px-4 space-y-8">
    {{-- Flash alerts --}}
    <x-alert type="success"/>
    <x-alert type="warning"/>
    <x-alert type="error"/>

    <form @submit.prevent="submit()" enctype="multipart/form-data"
          class="bg-white p-6 rounded-lg shadow space-y-6">
      @csrf

      {{-- Nombre --}}
      <div>
        <x-input-label for="nombre" :value="__('Nombre')" />
        <x-text-input id="nombre"
                      name="nombre"
                      type="text"
                      class="mt-1 block w-full"
                      x-model="form.nombre"
                      required />
      </div>

      {{-- Componente asociado --}}
      <div>
        <x-input-label for="componente_id" :value="__('Componente asociado')" />
        <select id="componente_id"
                name="componente_id"
                class="mt-1 block w-full border rounded"
                x-model="form.componente_id"
                required>
          <option value="">— Selecciona componente —</option>
          @foreach($componentes as $id => $titulo)
            <option value="{{ $id }}">{{ $titulo }}</option>
          @endforeach
        </select>
      </div>

      {{-- Input de archivo --}}
      <div>
        <x-input-label for="imagen_path" :value="__('Imagen 360° (jpg/png)')" />
        <input id="imagen_path"
               name="imagen_path"
               type="file"
               accept="image/jpeg,image/png"
               class="mt-1 block w-full"
               @change="onFileChange"
               required />
      </div>

      {{-- Previsualización estática --}}
      <div x-show="previewUrl" class="mt-4">
        <h3 class="font-medium mb-2">Previsualización</h3>
        <img :src="previewUrl"
             alt="Previsualización"
             class="w-full rounded border" />
      </div>

      {{-- Botón Guardar --}}
      <div class="flex justify-end">
        <button type="submit"
                :disabled="saving"
                class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50">
          <span x-text="saving ? 'Guardando…' : 'Guardar'"></span>
        </button>
      </div>
    </form>
  </main>

  {{-- Alpine.js --}}
  <x-slot name="scripts">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
      function simplePreview() {
        return {
          form: {
            nombre: '',
            componente_id: '',
            imagen_path: null
          },
          previewUrl: null,
          saving: false,

          onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.form.imagen_path = file;
            // Genera URL temporal para mostrarla en <img>
            this.previewUrl = URL.createObjectURL(file);
          },

          async submit() {
            this.saving = true;
            const data = new FormData();
            data.append('nombre', this.form.nombre);
            data.append('componente_id', this.form.componente_id);
            data.append('imagen_path', this.form.imagen_path);

            try {
              const res = await fetch('{{ route("panoramas.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: data
              });
              if (!res.ok) throw new Error('Error al guardar panorama');
              // Redirige al índice con mensaje de éxito
              window.location = '{{ route("panoramas.index") }}';
            } catch (err) {
              alert(err.message);
            } finally {
              this.saving = false;
            }
          }
        }
      }
    </script>
  </x-slot>

</x-app-layout>
