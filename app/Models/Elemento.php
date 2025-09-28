<?php
// app/Models/Elemento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elemento extends Model
{
    protected $fillable = [
        'componente_id',
        'nombre',
        'tipo',          // datos | imagen | video | audio | otro
        'descripcion',
        'contenido',     // solo para tipo "datos" (y "otro" si quieres texto)
        'media_path',    // archivo subido (storage/elementos/...)
    ];

    public function componente()
    {
        return $this->belongsTo(\App\Models\Componente::class);
    }
}
