<?php

namespace App\Http\Controllers;

use App\Models\Hotspot;
use App\Models\Panorama;
use Illuminate\Http\Request;

class HotspotController extends Controller
{
    public function store(Request $request, Panorama $panorama)
    {
        $data = $request->validate([
            'elemento_id' => 'required|exists:elementos,id',  // ← validamos elemento_id
            'posicion'    => 'required|string',
        ]);

        $data['panorama_id'] = $panorama->id;
        Hotspot::create($data);

        return response()->json(['success' => true]);
    }

    public function destroy(Hotspot $hotspot)
    {
        $hotspot->delete();
        return back()->with('error','Hotspot eliminado');
    }

    public function index(Panorama $panorama)
    {
        // Carga la relación con su elemento (si la tienes) y hotspots
        $panorama->load('hotspots.elemento');
        return view('hotspots.index', compact('panorama'));
    }

}
