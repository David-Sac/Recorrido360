<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elemento extends Model
{
    protected $fillable = [
        'componente_id',
        'nombre',
        'tipo',         // datos | imagen | video | audio | otro
        'contenido',
        'titulo',
        'descripcion',
        'url',
        'media_path',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function getSourceUrlAttribute(): ?string
    {
        if (!empty($this->url)) {
            return $this->url;
        }
        if (!empty($this->media_path)) {
            return asset('storage/'.$this->media_path);
        }
        return null;
    }

    public function componente()
    {
        return $this->belongsTo(\App\Models\Componente::class);
    }
}
