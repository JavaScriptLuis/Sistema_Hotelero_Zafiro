<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movilidad extends Model
{
    use HasFactory;

    protected $fillable = [
       'cliente_id',
       'placa_vehiculo'
    ];
}
