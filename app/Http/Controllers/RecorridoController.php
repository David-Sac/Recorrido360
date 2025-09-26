<?php

namespace App\Http\Controllers;

use App\Models\Recorrido;
use App\Models\Panorama;
use Illuminate\Http\Request;

class RecorridoController extends Controller
{
    // LISTA con búsqueda y contador
    public function index(Request $request)
    {
        $recorridos = Recorrido::query()
            ->withCount('panoramas')
            ->when($request->filled('search'), fn($q) =>
                $q->where('titulo', 'like', '%'.$request->search.'%')
            )
            ->orderByDesc('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('recorridos.index', compact('recorridos'));
    }

    public function create()
    {
        return view('recorridos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => ['required','string','max:255'],
            'descripcion' => ['nullable','string'],
            'publicado'   => ['sometimes','boolean'],
        ]);

        $data['publicado']  = (bool) ($data['publicado'] ?? false);
        $data['created_by'] = auth()->id();

        $recorrido = Recorrido::create($data);

        return redirect()
            ->route('recorridos.edit', $recorrido)
            ->with('status', 'Recorrido creado correctamente.');
    }

    public function edit(Recorrido $recorrido, Request $request)
    {
        // Carga panoramas ya anexados, ordenados por el pivot 'orden'
        $recorrido->load(['panoramas' => function($q){
            $q->withPivot('orden')->orderBy('recorrido_panorama.orden');
        }]);

        // Panoramas disponibles (excluye los usados)
        $usados = $recorrido->panoramas->pluck('id')->all();

        $disponibles = Panorama::query()
            ->when($request->filled('q'), fn($q) =>
                $q->where('nombre','like','%'.$request->q.'%')   // usa 'nombre'
            )
            ->whereNotIn('id', $usados)
            ->latest('id')
            ->take(40)
            ->get(['id','nombre','imagen_path']);               // usa 'nombre'

        return view('recorridos.edit', compact('recorrido','disponibles'));
    }

    public function update(Request $request, Recorrido $recorrido)
    {
        $data = $request->validate([
            'titulo'      => ['required','string','max:255'],
            'descripcion' => ['nullable','string'],
            'publicado'   => ['sometimes','boolean'],
        ]);
        $data['publicado'] = (bool) ($data['publicado'] ?? false);

        $recorrido->update($data);

        return redirect()
            ->route('recorridos.edit', $recorrido)
            ->with('status', 'Recorrido actualizado.');
    }

    public function destroy(Recorrido $recorrido)
    {
        $recorrido->delete();
        return redirect()
            ->route('recorridos.index')
            ->with('status', 'Recorrido eliminado.');
    }

    // -------------------------
    // ACCIONES EXTRA DE INTERFAZ
    // -------------------------

    // ➕ Adjuntar un panorama (al final del orden)
    public function attachPanorama(Recorrido $recorrido, Panorama $panorama)
    {
        $max = $recorrido->panoramas()->max('recorrido_panorama.orden') ?? 0;
        $recorrido->panoramas()->syncWithoutDetaching([
            $panorama->id => ['orden' => $max + 1]
        ]);

        return back()->with('status','Panorama añadido');
    }

    // ➖ Quitar panorama
    public function detachPanorama(Recorrido $recorrido, Panorama $panorama)
    {
        $recorrido->panoramas()->detach($panorama->id);
        return back()->with('status','Panorama quitado');
    }

    // ↕️ Guardar reordenamiento (drag & drop)
    public function reorder(Request $request, Recorrido $recorrido)
    {
        $data = $request->validate([
            'orden' => ['required','array'], // ej: ["12","9","15"]
        ]);

        foreach (array_values($data['orden']) as $pos => $panoramaId) {
            $recorrido->panoramas()->updateExistingPivot($panoramaId, [
                'orden' => $pos + 1
            ]);
        }

        return response()->json(['success' => true]);
    }
}
