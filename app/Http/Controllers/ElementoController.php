<?php

namespace App\Http\Controllers;

use App\Models\Elemento;
use App\Models\Componente;
use App\Http\Requests\ElementoRequest;
use Illuminate\Support\Facades\Storage;

class ElementoController extends Controller
{
    public function index()
    {
        $elementos = Elemento::with('componente')->latest()->paginate(10);
        return view('elementos.index', compact('elementos'));
    }

    public function create()
    {
        $componentes = Componente::pluck('titulo','id');
        return view('elementos.create', compact('componentes'));
    }

    public function store(ElementoRequest $request)
    {
        $data = $request->validated();
        $data['meta'] = $data['meta'] ?? null;

        if (in_array($data['tipo'], ['imagen','video','audio'])) {
            if (empty($data['url']) && !$request->hasFile('media')) {
                return back()->withErrors(['url' => 'Provee una URL o sube un archivo.'])->withInput();
            }
        }

        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->store('elementos', 'public');
        }

        Elemento::create($data);

        return redirect()->route('elementos.index')->with('success','Elemento creado correctamente');
    }

    public function edit(Elemento $elemento)
    {
        $componentes = Componente::pluck('titulo','id');
        return view('elementos.edit', compact('elemento','componentes'));
    }

    public function update(ElementoRequest $request, Elemento $elemento)
    {
        $data = $request->validated();
        $data['meta'] = $data['meta'] ?? null;

        if (in_array($data['tipo'], ['imagen','video','audio'])) {
            if (empty($data['url']) && !$request->hasFile('media') && empty($elemento->media_path)) {
                return back()->withErrors(['url' => 'Provee una URL o sube un archivo.'])->withInput();
            }
        }

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('elementos', 'public');
            if (!empty($elemento->media_path) && Storage::disk('public')->exists($elemento->media_path)) {
                Storage::disk('public')->delete($elemento->media_path);
            }
            $data['media_path'] = $path;
        }

        $elemento->update($data);

        return redirect()->route('elementos.index')->with('warning','Elemento actualizado correctamente');
    }

    public function destroy(Elemento $elemento)
    {
        if (!empty($elemento->media_path) && Storage::disk('public')->exists($elemento->media_path)) {
            Storage::disk('public')->delete($elemento->media_path);
        }
        $elemento->delete();

        return redirect()->route('elementos.index')->with('error','Elemento eliminado');
    }
}
