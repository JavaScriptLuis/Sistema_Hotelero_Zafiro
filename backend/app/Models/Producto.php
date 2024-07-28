<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'nombre',
        'descripcion',
        'precio_venta',
        'precio_compra',
        'estado',
    ];

    public function relacionSucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
