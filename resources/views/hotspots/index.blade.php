<x-app-layout>

  <x-slot name="head">
    {{-- A-Frame + Alpine --}}
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  </x-slot>

  <x-slot name="header">
    <h2 class="font-semibold text-xl">Hotspots de “{{ $panorama->nombre }}”</h2>
  </x-slot>

  <main
    x-data="hotspotManager()"
    x-init="init()"
    class="py-6 max-w-4xl mx-auto px-4 space-y-6"
  >

    {{-- Botones + / cancelar --}}
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Visor 360°</h1>
      <div>
        <button
          @click="startAdd()"
          class="px-3 py-1 bg-green-600 text-white rounded mr-2"
        >＋ Añadir</button>
        <button
          @click="cancelAdd()"
          class="px-3 py-1 bg-gray-400 text-white rounded"
        >✕ Cancelar</button>
      </div>
    </div>

    {{-- Escena 360° --}}
    <div class="relative h-96 bg-black rounded-lg overflow-hidden">
      <a-scene x-ref="scene" embedded style="height:100%;">
        <a-sky
          src="{{ asset('storage/'.$panorama->imagen_path) }}"
          rotation="0 -100 0"
        ></a-sky>
        <a-camera wasd-controls-enabled="false" look-controls="true">
          <a-cursor rayOrigin="mouse" material="color: white; shader: flat"></a-cursor>
        </a-camera>

        {{-- Hotspots existentes (– borra) --}}
        <template x-for="h in hotspots" :key="h.id">
          <a-image
            :position="h.posicion"
            src="{{ asset('images/hotspot-icon.png') }}"
            look-at="#camera"
            scale="0.5 0.5 0.5"
            @click="deleteHotspot(h.id)"
          ></a-image>
        </template>

        {{-- Punto provisional (+ confirma) --}}
        <template x-if="adding && newPos">
          <a-image
            :position="newPos"
            src="{{ asset('images/hotspot-icon-add.png') }}"
            look-at="#camera"
            scale="0.5 0.5 0.5"
            @click="confirmAdd()"
          ></a-image>
        </template>
      </a-scene>
    </div>

    {{-- Tabla de Hotspots --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Elemento</th>
            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Posición</th>
            <th class="px-6 py-3"></th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <template x-for="h in hotspots" :key="h.id">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap" x-text="h.elemento_nombre"></td>
              <td class="px-6 py-4 whitespace-nowrap" x-text="h.posicion"></td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <button
                  @click="deleteHotspot(h.id)"
                  class="px-2 py-1 bg-red-500 text-white rounded text-sm"
                >−</button>
              </td>
            </tr>
          </template>
          <template x-if="hotspots.length===0">
            <tr>
              <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                No hay hotspots aún.
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

  </main>

  <x-slot name="scripts">
  <script>
    function hotspotManager(){
      return {
        hotspots: @json($hotspots->map(fn($h)=>[
          'id'             => $h->id,
          'posicion'       => $h->posicion,
          'elemento_nombre'=> $h->elemento->nombre,
        ])},
        adding: false,
        newPos: null,

        init(){
          this.$refs.scene.addEventListener('click', e => {
            if (!this.adding) return;
            const inter = e.detail.intersection;
            if (!inter) return;
            // capturo la nueva posición
            this.newPos = [inter.point.x,inter.point.y,inter.point.z]
                            .map(n=>n.toFixed(2)).join(' ');
          });
        },

        startAdd(){
          this.adding = true;
          this.newPos = null;
        },
        cancelAdd(){
          this.adding = false;
          this.newPos = null;
        },

        confirmAdd(){
          if (!this.newPos) return;
          fetch(`{{ url("panoramas/{$panorama->id}/hotspots") }}`, {
            method:'POST',
            headers:{
              'Content-Type':'application/json',
              'X-CSRF-TOKEN':'{{ csrf_token() }}'
            },
            body: JSON.stringify({
              elemento_id: null,   // o abre un modal para seleccionar elemento
              posicion:    this.newPos
            })
          }).then(() => location.reload());
        },

        deleteHotspot(id){
          if (!confirm('¿Eliminar hotspot?')) return;
          fetch(`/hotspots/${id}`, {
            method:'DELETE',
            headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
          }).then(() => location.reload());
        }
      }
    }
  </script>
  </x-slot>

</x-app-layout>
