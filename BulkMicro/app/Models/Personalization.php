<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personalization extends Model
{
    use HasFactory;

    protected $fillable = ['nombre_tipo', 'intervalo_horas', 'notificaciones_email'];
}
