<?php

namespace App\Http\Controllers;

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
            'elemento_id' => 'nullable|exists:elementos,id',
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
            // devolver también el nombre del elemento para la tabla
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
    public function index(Panorama $panorama)
    {
        $hotspots  = $panorama->hotspots()->with('elemento')->get();
        $elementos = $panorama->componente->elementos()->select('id','nombre')->get();
        return view('hotspots.index', compact('panorama','hotspots','elementos'));
    }

}