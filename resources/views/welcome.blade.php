{{-- resources/views/welcome.blade.php --}}
<x-guest-layout>

  {{-- Imagen Principal --}}
  <section class="relative h-screen bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/ecomuseo_entry.jpg') }}');">
  <div class="absolute inset-0 bg-black bg-opacity-40"></div>

  <div class="relative z-10 flex flex-col items-center justify-center h-full text-white text-center px-4">
    <h1 class="text-4xl md:text-6xl font-bold">Explora el Ecomuseo Llacta Amaru en 360°</h1>
    <p class="mt-4 text-lg md:text-xl">Descubre nuestra historia, patrimonio y naturaleza desde tu navegador.</p>
    <div class="mt-6 space-x-4">
      <a href="#demo" class="px-6 py-3 bg-green-600 hover:bg-green-700 rounded text-white font-semibold">Ver demo</a>
      <a href="{{ route('register') }}" class="px-6 py-3 border border-white hover:bg-white hover:text-green-700 rounded text-white font-semibold">Regístrate</a>
    </div>
  </div>
</section>


  {{-- Previsualización del demo --}}
    <section id="demo" class="bg-gray-100 py-16">
    <div class="max-w-4xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-6">Prueba nuestro recorrido</h2>
        <div class="relative">
            <a-scene embedded style="height: 500px;">
                <a-camera fov="50" wasd-controls-enabled="false" look-controls="true"></a-camera>
                <a-sky src="{{ asset('images/360_1.jpg') }}" rotation="0 -100 0"></a-sky>
            </a-scene>

            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-60 text-white px-3 py-1 text-sm rounded">
            Arrastra para mirar alrededor
            </div>

        </div>
    </div>
    </section>

    <!-- Cómo funciona -->
    <section class="py-16">
    <div class="max-w-3xl mx-auto text-center space-y-12">
        <h2 class="text-3xl font-bold">¿Cómo funciona?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="space-y-4"><Icon step1 /><h3>Paso 1</h3><p>Elige un recorrido</p></div>
        <div class="space-y-4"><Icon step2 /><h3>Paso 2</h3><p>Explora hotspots</p></div>
        <div class="space-y-4"><Icon step3 /><h3>Paso 3</h3><p>Consulta información</p></div>
        </div>
    </div>
    </section>

    {{-- Galería de componentes como carousel --}}
    <section class="py-16 bg-gray-100">
    <div class="max-w-screen-xl mx-auto px-4">

        <h2 class="text-3xl font-bold text-center mb-8">Nuestros Componentes</h2>

        <div 
        
        class="relative overflow-hidden"
        >
        {{-- Slides --}}
        <template x-for="(slide, i) in slides" :key="i">
            <div 
            x-show="index === i" 
            class="transition-opacity duration-500"
            >
            <img 
                :src="slide.image" 
                alt="" 
                class="w-full h-64 object-cover rounded-lg shadow-md"
            >
            <h4 
                class="mt-4 text-center text-xl font-semibold text-gray-800"
                x-text="slide.title"
            ></h4>
            </div>
        </template>

        {{-- Controles --}}
        <button 
            @click="index = index === 0 ? slides.length - 1 : index - 1" 
            class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 p-2 rounded-full shadow"
        >
            <!-- Icono flecha izquierda -->
            <svg class="h-6 w-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <button 
            @click="index = index === slides.length -1 ? 0 : index + 1" 
            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-75 hover:bg-opacity-100 p-2 rounded-full shadow"
        >
            <!-- Icono flecha derecha -->
            <svg class="h-6 w-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        </div>

    </div>
    </section>


</x-guest-layout>
