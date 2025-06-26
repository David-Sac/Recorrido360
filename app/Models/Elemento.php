<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elemento extends Model
{
    protected $fillable = [
        'componente_id',
        'nombre',
        'tipo',
        'contenido',
    ];

    public function componente()
    {
        return $this->belongsTo(Componente::class);
    }
}
