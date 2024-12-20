<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwsBucket extends Model
{
    use HasFactory;

    protected $table = 'aws_buckets'; // Tabla asociada al modelo

    /**
     * Los atributos que se pueden asignar en masa.
     *
     * @var array
     */
    protected $fillable = [
        'config_type', // Tipo de configuración: asociados, captaciones, colocaciones
        'bucket_path', // Ruta completa del bucket
        'file_name',   // Archivo asociado para migración automática
        'access_key',  // Llave de acceso
        'secret_key',  // Llave secreta
        'region',      // Región del bucket (opcional)
        'description', // Descripción opcional
    ];

    /**
     * Obtener la descripción legible del tipo de configuración.
     *
     * @return string
     */
    public function getConfigTypeNameAttribute()
    {
        return match ($this->config_type) {
            '1' => 'Asociados',
            '2' => 'Captaciones',
            '3' => 'Colocaciones',
            default => 'Desconocido',
        };
    }
}
