<?php

namespace App\Http\Controllers;

use App\Models\Elemento;
use App\Models\Hotspot;
use App\Models\Panorama;
use Illuminate\Http\Request;

class HotspotController extends Controller
{
    // Crear un hotspot en el panorama dado
    public function store(Request $request, Panorama $panorama)
    {
        // 1) Validación de entrada
        $data = $request->validate([
            'posicion'    => 'required|string',
            'elemento_id' => 'required|exists:elementos,id',
        ]);

        // 2) Asociar al panorama
        $data['panorama_id'] = $panorama->id;

        // 3) Crear el registro
        $hotspot = Hotspot::create($data);

        // 4) Responder JSON con datos útiles para el frontend
        return response()->json([
            'success'          => true,
            'id'               => $hotspot->id,
            'position'         => $hotspot->posicion,
            'elemento_id'      => $hotspot->elemento_id,
            'elemento_nombre'  => optional($hotspot->elemento)->nombre,
        ]);
    }

    // Eliminar un hotspot
    public function destroy(Hotspot $hotspot)
    {
        $hotspot->delete();

        // Respuesta sencilla para confirmar al frontend
        return response()->json(['success' => true]);
    }
    // Mostrar listado de hotspots de un panorama
    // app/Http/Controllers/HotspotController.php
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

            $color = $h->color;
            if ($color) {
                $c = strtoupper(ltrim(trim($color), '#'));
                $color = preg_match('/^[0-9A-F]{6}$/', $c) ? "#{$c}" : null;
            }

            return [
                'id'              => $h->id,
                'posicion'        => $h->posicion,
                'posArr'          => $posArr,
                'color'           => $color, // o null
                'elemento_id'     => $h->elemento?->id,
                'elemento_nombre' => $h->elemento?->nombre,
            ];
        })->values();

        return view('hotspots.index', [
            'panorama'  => $panorama,
            'elementos' => $elementos,
            'hotspots'  => $hotspots,
        ]);
    }


}