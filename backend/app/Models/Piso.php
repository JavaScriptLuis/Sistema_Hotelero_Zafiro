<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piso extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'nombre',
        'estado',
    ];

    public function relacionSucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
