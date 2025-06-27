<?php

namespace App\Http\Controllers;

use App\Models\Componente;
use App\Http\Requests\ComponenteRequest;
use Illuminate\Support\Facades\Storage;

class ComponenteController extends Controller
{
    // Listado
    public function index()
    {
        $componentes = Componente::latest()->paginate(10);
        return view('componentes.index', compact('componentes'));
    }

    // Formulario de creación
    public function create()
    {
        return view('componentes.create');
    }

    // Guardar nuevo
    public function store(ComponenteRequest $request)
    {
        $data = $request->validated();

        // Manejar subida de imagen
        if ($path = $request->file('imagen_path')?->store('componentes','public')) {
            $data['imagen_path'] = $path;
        }

        $data['created_by'] = auth()->id();
        Componente::create($data);

        return redirect()->route('componentes.index')
                        ->with('success','Componente creado correctamente');

    }

    // Ver detalle (opcional)
    public function show(Componente $componente)
    {
        return view('componentes.show', compact('componente'));
    }

    // Formulario de edición
    public function edit(Componente $componente)
    {
        return view('componentes.edit', compact('componente'));
    }

    // Actualizar existente
    public function update(ComponenteRequest $request, Componente $componente)
    {
        $data = $request->validated();

        if ($file = $request->file('imagen_path')) {
            // Borrar imagen antigua si existe
            Storage::disk('public')->delete($componente->imagen_path);
            $data['imagen_path'] = $file->store('componentes','public');
        }

        $componente->update($data);

        return redirect()->route('componentes.index')
                        ->with('warning','Componente actualizado correctamente');

    }

    // Eliminar
    public function destroy(Componente $componente)
    {
        Storage::disk('public')->delete($componente->imagen_path);
        $componente->delete();

        return redirect()->route('componentes.index')
                        ->with('error','Componente eliminado correctamente');

    }
}
