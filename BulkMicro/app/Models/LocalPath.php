<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalPath extends Model
{
    use HasFactory;

    protected $table = 'local_paths'; // Tabla asociada al modelo

    /**
     * Los atributos que se pueden asignar en masa.
     *
     * @var array
     */
    protected $fillable = [
        'config_type', // Tipo de configuración: asociados, captaciones, colocaciones
        'path',        // Ruta de la carpeta local
        'file_name',   // Archivo asociado para migración automática
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
