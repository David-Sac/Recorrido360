<?php
namespace App\Http\Controllers;

use App\Models\Recorrido;
use Illuminate\Support\Str;
use App\Http\Requests\StoreRecorridoRequest;
use App\Http\Requests\UpdateRecorridoRequest;

class RecorridoController extends Controller
{
    public function index()
    {
        $recorridos = Recorrido::orderByDesc('created_at')->paginate(12);
        return view('recorridos.index', compact('recorridos'));
    }

    public function create()
    {
        return view('recorridos.create');
    }

    public function store(StoreRecorridoRequest $request)
    {
        $data = $request->validated();

        // Normaliza checkbox
        $data['publicado']  = (bool) ($data['publicado'] ?? false);

        // ✅ guarda autor
        $data['created_by'] = auth()->id();

        $recorrido = Recorrido::create($data);

        return redirect()
            ->route('recorridos.edit', $recorrido)
            ->with('status', 'Recorrido creado correctamente.');
    }


    public function show(Recorrido $recorrido)
    {
        // Si quieres mostrar su secuencia, puedes eager-load: panoramas()
        return view('recorridos.show', compact('recorrido'));
    }

    public function edit(Recorrido $recorrido)
    {
        return view('recorridos.edit', compact('recorrido'));
    }

    public function update(UpdateRecorridoRequest $request, Recorrido $recorrido)
    {
        $data = $request->validated();
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
}
