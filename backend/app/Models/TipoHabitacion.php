<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    use HasFactory;
    
    protected $table = 'tipo_habitacions';
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_fijo',
        'precio_decremento',
        'estado',
    ];


}
