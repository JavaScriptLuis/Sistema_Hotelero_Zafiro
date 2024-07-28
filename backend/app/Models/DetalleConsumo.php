<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleConsumo extends Model
{
    use HasFactory;


    protected $fillable = [
        'consumo_id',
        'producto_id',
        'cantidad',
        'precio',
        'estado',
        'metodo_pago',
    ];
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
