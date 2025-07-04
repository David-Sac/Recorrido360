<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panorama extends Model
{
    protected $fillable = [
        'nombre',
        'imagen_path',
        'created_by',
        'componente_id',
    ];

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function componente()
    {
    return $this->belongsTo(Componente::class);
    }
}
