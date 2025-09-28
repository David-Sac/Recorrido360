<?php

namespace App\Http\Controllers;

use App\Models\Elemento;
use App\Models\Hotspot;
use App\Models\Panorama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;   // 👈 AÑADIR
use Illuminate\Support\Str;               // 👈 AÑADIR

class HotspotController extends Controller
{
    // Crear un hotspot en el panorama dado
    public function store(Request $request, Panorama $panorama)
    {
        $data = $request->validate([
            'posicion'    => 'required|string',
            'elemento_id' => 'required|exists:elementos,id',
        ]);
        $data['panorama_id'] = $panorama->id;

        $hotspot = Hotspot::create($data)->load('elemento');

        // ✅ Construir media_url del elemento
        $mediaUrl = null;
        if ($hotspot->elemento) {
            $c = trim((string) ($hotspot->elemento->contenido ?? ''));
            if ($c !== '') {
                if (Str::startsWith($c, ['http://','https://'])) {
                    $mediaUrl = $c;
                } else {
                    // normaliza: quita backslashes y 'public/' si existe
                    $c = str_replace('\\','/', $c);
                    $c = ltrim(preg_replace('#^public/#', '', $c), '/');
                    $mediaUrl = Storage::url($c); // => /storage/...
                }
            }
        }

        return response()->json([
            'success'          => true,
            'id'               => $hotspot->id,
            'position'         => $hotspot->posicion,
            'elemento_id'      => $hotspot->elemento_id,
            'elemento_nombre'  => optional($hotspot->elemento)->nombre,
            'elemento'         => $hotspot->elemento ? [
                'id'          => $hotspot->elemento->id,
                'nombre'      => $hotspot->elemento->nombre,
                'tipo'        => strtolower($hotspot->elemento->tipo),
                'contenido'   => $hotspot->elemento->contenido,
                'descripcion' => $hotspot->elemento->descripcion,
                'media_url'   => $mediaUrl,               // 👈 ENVÍA LA URL LISTA
            ] : null,
        ]);
    }

    public function destroy(Hotspot $hotspot)
    {
        $hotspot->delete();
        return response()->json(['success' => true]);
    }

    // Mostrar listado con media_url ya resuelta
    public function index(Panorama $panorama)
    {
        $panorama->load(['hotspots.elemento']);

        $elementos = Elemento::orderBy('nombre')
            ->get(['id','nombre'])
            ->map(fn($e) => ['id'=>$e->id, 'nombre'=>$e->nombre])
            ->values();

        $hotspots = $panorama->hotspots->map(function ($h) {
            $posArr = collect(explode(' ', (string)$h->posicion))
                ->map(fn($v) => (float)$v)->values()->all();

            $e = $h->elemento;
            $mediaUrl = null;
            if ($e) {
                $c = trim((string) ($e->contenido ?? ''));
                if ($c !== '') {
                    if (Str::startsWith($c, ['http://','https://'])) {
                        $mediaUrl = $c;
                    } else {
                        $c = str_replace('\\','/', $c);
                        $c = ltrim(preg_replace('#^public/#', '', $c), '/');
                        $mediaUrl = Storage::url($c); // => /storage/...
                    }
                }
            }

            return [
                'id'              => $h->id,
                'posicion'        => $h->posicion,
                'posArr'          => $posArr,
                'elemento_id'     => $e?->id,
                'elemento_nombre' => $e?->nombre,
                'elemento'        => $e ? [
                    'id'          => $e->id,
                    'nombre'      => $e->nombre,
                    'tipo'        => strtolower($e->tipo),
                    'contenido'   => $e->contenido,
                    'descripcion' => $e->descripcion,
                    'media_url'   => $mediaUrl,  // 👈 AQUÍ TAMBIÉN
                ] : null,
            ];
        })->values();

        return view('hotspots.index', compact('panorama','elementos','hotspots'));
    }
}
