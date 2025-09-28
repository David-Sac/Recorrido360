<?php

namespace App\Http\Controllers;

use App\Models\Elemento;
use App\Models\Componente;
use App\Http\Requests\ElementoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ElementoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $elementos = Elemento::with('componente')
            ->when($q !== '', fn($qq) => $qq->where('nombre','like',"%{$q}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

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
        $tipo = $data['tipo'];

        if ($tipo === 'datos') {
            if (empty($data['contenido'])) {
                return back()->withErrors(['contenido' => 'El contenido es obligatorio en tipo "datos".'])->withInput();
            }
            $data['media_path'] = null;
        } else {
            // MEDIA: archivo obligatorio
            if (!$request->hasFile('media')) {
                return back()->withErrors(['media' => 'Debes seleccionar un archivo.'])->withInput();
            }

            // Validar mimetypes por tipo
            $this->validateMimeForType($request, $tipo);

            $data['media_path'] = $request->file('media')->store('elementos', 'public');
            $data['contenido']  = null; // no aplica en media
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
        $tipo = $data['tipo'];

        if ($tipo === 'datos') {
            if (empty($data['contenido'])) {
                return back()->withErrors(['contenido' => 'El contenido es obligatorio en tipo "datos".'])->withInput();
            }
            // si tenía archivo previo y ahora pasa a datos, lo borramos
            if (!empty($elemento->media_path) && Storage::disk('public')->exists($elemento->media_path)) {
                Storage::disk('public')->delete($elemento->media_path);
            }
            $data['media_path'] = null;
        } else {
            // MEDIA: si no hay archivo nuevo y tampoco existe uno previo, error
            if (!$request->hasFile('media') && empty($elemento->media_path)) {
                return back()->withErrors(['media' => 'Debes seleccionar un archivo.'])->withInput();
            }

            // Si suben archivo nuevo, reemplaza anterior
            if ($request->hasFile('media')) {
                $this->validateMimeForType($request, $tipo);

                $path = $request->file('media')->store('elementos', 'public');

                if (!empty($elemento->media_path) && Storage::disk('public')->exists($elemento->media_path)) {
                    Storage::disk('public')->delete($elemento->media_path);
                }
                $data['media_path'] = $path;
            }

            $data['contenido'] = null; // no aplica en media
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

    // -----------------------
    // Validación de mimetypes
    // -----------------------
    private function validateMimeForType(Request $request, string $tipo): void
    {
        $rules = match ($tipo) {
            'imagen' => ['media' => 'file|mimes:jpg,jpeg,png,webp,avif|max:20480'], // 20MB
            'video'  => ['media' => 'file|mimetypes:video/mp4,video/webm,video/ogg|max:102400'], // 100MB
            'audio'  => ['media' => 'file|mimetypes:audio/mpeg,audio/mp3,audio/ogg,audio/wav|max:51200'], // 50MB
            default  => ['media' => 'file|max:102400'], // "otro"
        };
        $request->validate($rules);
    }
}
