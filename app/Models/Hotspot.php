<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    protected $fillable = [
        'panorama_id',
        'componente_id',
        'posicion',
    ];

    public function panorama()
    {
        return $this->belongsTo(Panorama::class);
    }

    public function componente()
    {
        return $this->belongsTo(Componente::class);
    }
}
