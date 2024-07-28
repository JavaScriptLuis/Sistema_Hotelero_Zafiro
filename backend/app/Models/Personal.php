<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'user_id',
        'nombre',
        'paterno',
        'materno',
        'cedula',
        'foto',
        'telefono',
        'estado',
    ];

    public function relacionSucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
    public function relacionUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
