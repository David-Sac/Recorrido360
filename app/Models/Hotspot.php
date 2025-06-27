<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotspot extends Model
{
    protected $fillable = [
        'panorama_id',
        'elemento_id',   // ← cambiamos aquí
        'posicion',
    ];

    public function elemento()
    {
        return $this->belongsTo(Elemento::class);
    }

    public function panorama()
    {
        return $this->belongsTo(Panorama::class);
    }
}
