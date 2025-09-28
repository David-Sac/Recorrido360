<?php

namespace App\Http\Controllers;

use App\Models\Elemento;
use App\Models\Hotspot;
use App\Models\Panorama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HotspotController extends Controller
{
    /** Toma el primer campo de ruta disponible del elemento. */
    private function elementoRawPath(?Elemento $e): ?string
    {
        if (!$e) return null;
        foreach ([$e->contenido ?? null, $e->media_path ?? null, $e->url ?? null] as $val) {
            if (is_string($val) && trim($val) !== '') return trim($val);
        }
        return null;
    }

    /** Construye una URL pública (/storage/...) a partir de una ruta cruda. */
    private function mediaUrlFromRaw(?string $raw): ?string
    {
        if (!$raw) return null;
        $c = str_replace('\\', '/', trim($raw));
        if (Str::startsWith($c, ['http://','https://'])) return $c;
        $c = ltrim($c, '/');
        $c = preg_replace('#^(public/|storage/)#i', '', $c);
        return Storage::disk('public')->url($c);
    }

    /** Serializa el Elemento para el frontend (incluye media_url). */
    private function elementoToArray(?Elemento $e): ?array
    {
        if (!$e) return null;

        return [
            'id'          => $e->id,
            'nombre'      => $e->nombre,
            'tipo'        => strtolower((string)$e->tipo),
            'contenido'   => $e->contenido,
            'media_path'  => $e->media_path ?? null,
            'url'         => $e->url ?? null,
            'descripcion' => $e->descripcion,
            'media_url'   => $this->mediaUrlFromRaw($this->elementoRawPath($e)),
        ];
    }

    /** Listado de hotspots de un panorama. */
    public function index(Panorama $panorama)
    {
        $panorama->load(['hotspots.elemento']);

        $elementos = Elemento::orderBy('nombre')
            ->get(['id','nombre'])
            ->map(fn($e) => ['id'=>$e->id, 'nombre'=>$e->nombre])
            ->values();

        $hotspots = $panorama->hotspots->map(function (Hotspot $h) {
            $posArr = collect(explode(' ', (string)$h->posicion))
                ->map(fn($v) => (float)$v)->values()->all();

            return [
                'id'              => $h->id,
                'posicion'        => $h->posicion,
                'posArr'          => $posArr,
                'elemento_id'     => $h->elemento?->id,
                'elemento_nombre' => $h->elemento?->nombre,
                'elemento'        => $this->elementoToArray($h->elemento),
            ];
        })->values();

        return view('hotspots.index', compact('panorama', 'elementos', 'hotspots'));
    }

    /** Crear hotspot. */
    public function store(Request $request, Panorama $panorama)
    {
        $data = $request->validate([
            'posicion'    => 'required|string',
            'elemento_id' => 'required|exists:elementos,id',
        ]);

        $data['panorama_id'] = $panorama->id;

        $hotspot = Hotspot::create($data)->load('elemento');

        return response()->json([
            'success'          => true,
            'id'               => $hotspot->id,
            'position'         => $hotspot->posicion,
            'elemento_id'      => $hotspot->elemento_id,
            'elemento_nombre'  => optional($hotspot->elemento)->nombre,
            'elemento'         => $this->elementoToArray($hotspot->elemento),
        ]);
    }

    /** Eliminar hotspot. */
    public function destroy(Hotspot $hotspot)
    {
        $hotspot->delete();
        return response()->json(['success' => true]);
    }
}
