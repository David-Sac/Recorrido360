<?php

namespace App\Http\Controllers;

use App\Models\Elemento;
use Illuminate\Http\Request;

use App\Models\Panorama;
use App\Models\Componente;              // ← Importa Componente
use App\Http\Requests\PanoramaRequest;
use Illuminate\Support\Facades\Storage;

class PanoramaController extends Controller
{
    public function index()
    {
        $panoramas = Panorama::latest()->paginate(10);
        return view('panoramas.index', compact('panoramas'));
    }
    public function create()
    {
        // Para el select padre
        $componentes = Componente::pluck('titulo','id');
        // Inicialmente vacío — lo irás cargando dinámicamente o mostrando vacío
        $elementos   = [];

        return view('panoramas.create', compact('componentes','elementos'));
    }


    public function store(PanoramaRequest $request)
    {
        $data = $request->validated();
        $data['componente_id'] = $request->componente_id;
        $data['created_by']    = auth()->id();

        // Guarda la imagen en storage/app/public/panoramas
        if ($file = $request->file('imagen_path')) {
            $path = $file->store('panoramas', 'public');
            $data['imagen_path'] = $path;
        }

        $panorama = Panorama::create($data);

        return redirect()
            ->route('panoramas.index')
            ->with('success', 'Panorama creado correctamente');
    }



    public function edit(Panorama $panorama)
    {
        $componentes = Componente::pluck('titulo','id');
        return view('panoramas.edit', compact('panorama','componentes'));
    }

    public function update(PanoramaRequest $request, Panorama $panorama)
    {
        $data = $request->validated();
        $data['componente_id'] = $request->componente_id;

        if ($file = $request->file('imagen_path')) {
            // Usar $panorama (singular), no $panoramas
            Storage::disk('public')->delete($panorama->imagen_path);
            $data['imagen_path'] = $file->store('panoramas','public');
        }

        $panorama->update($data);

        return redirect()->route('panoramas.index')
                         ->with('warning','Panorama actualizado correctamente');
    }

    public function destroy(Panorama $panorama)
    {
        Storage::disk('public')->delete($panorama->imagen_path);
        $panorama->delete();

        return redirect()->route('panoramas.index')
                         ->with('error','Panorama eliminado');
    }

    // app/Http/Controllers/PanoramaController.php

    public function previewUpload(Request $request)
    {
        // Validamos sólo la imagen
        $request->validate([
        'imagen' => 'required|image|mimes:jpg,jpeg,png|max:10240'
        ]);

        // Guardamos en public/temp
        $path = $request->file('imagen')->store('temp','public');

        // Devolvemos la URL completa
        return response()->json([
        'url' => asset('storage/'.$path)
        ]);
    }

}
