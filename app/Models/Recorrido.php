<?php

// app/Models/Recorrido.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recorrido extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'publicado',
        'meta',
        'created_by',
    ];

    protected $casts = [
        'publicado' => 'boolean',
        'meta'      => 'array',
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Asegúrate de que tu pivote use 'orden' como columna
    public function panoramas()
    {
        return $this->belongsToMany(Panorama::class, 'recorrido_panorama')
                    ->withPivot('orden')
                    ->orderBy('recorrido_panorama.orden');
        // En Laravel 11 también vale: ->orderByPivot('orden')
    }
}
