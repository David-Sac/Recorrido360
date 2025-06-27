<?php

namespace App\Http\Controllers;

use App\Models\Elemento;
use App\Models\Componente;
use App\Http\Requests\ElementoRequest;

class ElementoController extends Controller
{
    public function index()
    {
        $elementos = Elemento::with('componente')
                             ->latest()
                             ->paginate(10);
        return view('elementos.index', compact('elementos'));
    }

    public function create()
    {
        $componentes = Componente::pluck('titulo','id');
        return view('elementos.create', compact('componentes'));
    }

    public function store(ElementoRequest $request)
    {
        Elemento::create($request->validated());

        return redirect()->route('elementos.index')
                         ->with('success','Elemento creado correctamente');
    }

    public function edit(Elemento $elemento)
    {
        $componentes = Componente::pluck('titulo','id');
        return view('elementos.edit', compact('elemento','componentes'));
    }

    public function update(ElementoRequest $request, Elemento $elemento)
    {
        $elemento->update($request->validated());

        return redirect()->route('elementos.index')
                         ->with('warning','Elemento actualizado correctamente');
    }

    public function destroy(Elemento $elemento)
    {
        $elemento->delete();

        return redirect()->route('elementos.index')
                         ->with('error','Elemento eliminado');
    }
}
