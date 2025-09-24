<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'imagen_path',
        'created_by',
    ];

    public function elementos()
    {
        return $this->hasMany(Elemento::class);
    }

    public function panoramas()
    {
        return $this->hasMany(Panorama::class);
    }

    // reemplaza la relación hotspots()
    public function hotspots()
    {
        return $this->hasManyThrough(
            \App\Models\Hotspot::class,   // modelo final
            \App\Models\Panorama::class,  // modelo intermedio
            'componente_id',              // FK en panoramas -> componentes.id
            'panorama_id',                // FK en hotspots   -> panoramas.id
            'id',                         // PK en componentes
            'id'                          // PK en panoramas
        );
}


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
