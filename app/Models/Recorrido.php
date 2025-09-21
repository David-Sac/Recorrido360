<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recorrido extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'estado_publicacion',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function panoramas()
    {
        return $this->belongsToMany(Panorama::class)
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('recorrido_panorama.order');
    }
}
