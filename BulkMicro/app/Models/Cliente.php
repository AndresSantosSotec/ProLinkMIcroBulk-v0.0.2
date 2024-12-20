<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $primaryKey = 'id_asociado';

    public $timestamps = true;

    protected $fillable = [
        'Codigo_Cliente',
        'Actualizacion',
        'Nombre1',
        'Nombre2',
        'Nombre3',
        'Apellido1',
        'Apellido2',
        'ApellidoCasada',
        'Celular',
        'Telefono',
        'Genero',
        'Fecha_Apertura',
        'Tipo_Cliente',
        'Fecha_Nacimiento',
        'Fecha_Actualizacion_Ive',
        'Alto_Riesgo',
        'Id_Reple',
        'Dpi',
        'Pasaporte',
        'Licencia',
        'Nit',
        'Departamento',
        'Municipio',
        'Pais',
        'Estado_Civil',
        'Depto_Nacimiento',
        'Muni_Nacimiento',
        'Pais_Nacimiento',
        'Nacionalidad',
        'Ocupacion',
        'Profesion',
        'Correo_Electronico',
        'Actividad_Economica',
        'Rubro',
        'SubRubro',
        'Direccion',
        'Zona_domicilio',
        'Pais_Domicilio',
        'Depto_Domicilio',
        'Muni_Domicilio',
        'Relacion_Dependencia',
        'Nombre_Relacion_Dependencia',
        'Ingresos_Laborales',
        'Mondea_Ingreso_Laboral',
        'Fecha_Ingreso_Laboral',
        'Negocio_Propio',
        'Nombre_Negocio',
        'Fecha_Inicio_Negocio',
        'Ingresos_Negocio_Propio',
        'Moneda_Negocio_Propio',
        'Ingresos_Remesas',
        'Monto_Otros_Ingresos',
        'Otros_Ingresos',
        'Monto_Ingresos',
        'Moneda_Ingresos',
        'Rango_Ingresos',
        'Monto_Egresos',
        'Moneda_Egresos',
        'Rango_Egresos',
        'Act_Economica_Relacion_Dependencia',
        'Act_Economica_Negocio',
        'Edad',
        'Cooperativa',
        'Condicion_Vivienda',
        'Puesto',
        'Direccion_Laboral',
        'Zona_Laboral',
        'Depto_Laboral',
        'Muni_Laboral',
        'Telefono_Laboral',
        'Persona_PEP',
        'Persona_CPE',
        'Categoria',
    ];
}
