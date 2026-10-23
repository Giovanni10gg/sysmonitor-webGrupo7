<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulacion extends Model
{
    protected $table = 'simulaciones';

    protected $fillable = [
        'algoritmo',
        'promedio_espera',
        'promedio_retorno'
    ];
}
