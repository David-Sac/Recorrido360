{{-- resources/views/hotspots/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Hotspots de: {{ $panorama->nombre }}</h2>
  </x-slot>

  <main class="py-6 max-w-6xl mx-auto px-4 space-y-8">
    <a href="{{ route('panoramas.index') }}"
       class="text-gray-600 hover:underline mb-4 inline-block">
      ← Volver a Panoramas
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Columna izquierda: visor 360° --}}
      <section x-data="hotspotManager()" x-init="init()" class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Visor 360°</h3>
        <div class="relative" style="height:400px;">
          <a-scene x-ref="scene" embedded style="height:100%;">
            <a-sky src="{{ asset('storage/'.$panorama->imagen_path) }}"
                   rotation="0 -100 0"></a-sky>
            <a-camera wasd-controls-enabled="false" look-controls="true">
              <a-cursor rayOrigin="mouse" material="color:white;shader:flat"></a-cursor>
            </a-camera>

            {{-- Puntos de hotspots existentes --}}
            <template x-for="h in hotspots" :key="h.id">
              <a-image :position="h.position"
                       src="{{ asset('images/hotspot-icon.png') }}"
                       look-at="#camera"
                       scale="0.5 0.5 0.5"></a-image>
            </template>
          </a-scene>

          {{-- Mensaje de instrucciones cuando estamos en modo añadir --}}
          <div x-show="mode==='placing'"
               class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 text-white text-lg">
            Haz clic para colocar el hotspot
          </div>
        </div>

        {{-- Botón para entrar/salir de modo colocar hotspot --}}
        <div class="mt-4">
          <button @click="toggleMode()"
                  class="px-4 py-2 rounded text-white"
                  :class="mode==='placing' ? 'bg-red-600' : 'bg-green-600'">
            <template x-if="mode==='placing'">Cancelar</template>
            <template x-if="mode!=='placing'">+ Añadir Hotspot</template>
          </button>
        </div>
      </section>

      {{-- Columna derecha: listado de hotspots --}}
      <section class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Listado de Hotspots</h3>
        <template x-if="hotspots.length">
          <ul class="space-y-3">
            <template x-for="h in hotspots" :key="h.id">
              <li class="flex justify-between items-center border-b pb-2">
                <div>
                  <strong>ID:</strong> <span x-text="h.id"></span><br>
                  <strong>Posición:</strong> <span x-text="h.position"></span>
                </div>
                <button @click="remove(h.id)"
                        class="text-red-600 hover:underline">
                  Eliminar
                </button>
              </li>
            </template>
          </ul>
        </template>
        <template x-if="!hotspots.length">
          <p class="text-gray-500">No hay hotspots aún.</p>
        </template>
      </section>
    </div>
  </main>

  <x-slot name="scripts">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js" defer></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
      function hotspotManager() {
        return {
          mode: 'view',  // 'view' ó 'placing'
          hotspots: @json($panorama->hotspots->map(fn($h)=>['id'=>$h->id,'position'=>$h->posicion])),
          
          init() {
            this.$refs.scene.addEventListener('click', this.onSceneClick.bind(this));
          },

          toggleMode() {
            this.mode = this.mode==='placing' ? 'view' : 'placing';
          },

          async onSceneClick(evt) {
            if (this.mode!=='placing') return;
            const inter = evt.detail.intersection;
            if (!inter) return;
            const pos = [inter.point.x, inter.point.y, inter.point.z]
                          .map(n=>n.toFixed(2)).join(' ');

            // Llamada al backend para crear
            const res = await fetch(
              `{{ url("panoramas/{$panorama->id}/hotspots") }}`, {
                method: 'POST',
                headers: {
                  'Content-Type':'application/json',
                  'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({ posicion: pos, elemento_id: null })
              }
            );
            const json = await res.json();
            if (json.success) {
              this.hotspots.push({ id: json.id, position: pos });
              this.mode = 'view';
            } else {
              alert('Error al crear hotspot');
            }
          },

          async remove(id) {
            if (!confirm('Eliminar este hotspot?')) return;
            const res = await fetch(`/hotspots/${id}`, {
              method:'DELETE',
              headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
            });
            if (res.ok) {
              this.hotspots = this.hotspots.filter(h=>h.id!==id);
            } else {
              alert('Error al eliminar');
            }
          }
        }
      }
    </script>
  </x-slot>
</x-app-layout>
