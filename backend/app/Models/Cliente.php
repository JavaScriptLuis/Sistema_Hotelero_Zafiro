<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'sucursal_id', 'nombre', 'paterno', 'materno', 'cedula', 'telefono', 'estado', 'nit'];

    public function relacionSucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
    public function relacionUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function relacionMovilidad()
    {
        return $this->hasOne(Movilidad::class, 'cliente_id');
    }
}
