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

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
