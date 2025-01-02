<?php

namespace App\Imports;

use App\Models\Cliente;
use App\Models\ColumnConfiguration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientesImport implements ToModel, WithHeadingRow
{
    private $columnMappings;
    private $importedData = [];

    public function __construct()
    {
        // Obtener mapeo de columnas desde la base de datos
        $this->columnMappings = ColumnConfiguration::where('id_tipo', 1)
            ->pluck('column_name', 'excel_column_name')
            ->toArray();
    }

    /**
     * Convertir una fila del Excel en un modelo Cliente.
     *
     * @param array $row
     * @return Cliente|null
     */
    public function model(array $row)
    {
        try {
            $mappedData = [];
            foreach ($this->columnMappings as $excelColumn => $dbColumn) {
                $value = $row[strtolower($excelColumn)] ?? null;

                // Validar y procesar valores según tipo
                $mappedData[$dbColumn] = $this->validateAndProcessValue($dbColumn, $value);
            }

            $this->importedData[] = $mappedData;

            return new Cliente($mappedData);
        } catch (\Exception $e) {
            Log::error("Error procesando fila: {$e->getMessage()}");
            return null; // Manejar filas inválidas si es necesario
        }
    }

    /**
     * Obtener los datos importados.
     *
     * @return array
     */
    public function getImportedData()
    {
        return $this->importedData;
    }

    /**
     * Validar y procesar valores según el campo de la base de datos.
     *
     * @param string $column
     * @param mixed $value
     * @return mixed
     */
    private function validateAndProcessValue($column, $value)
    {
        switch ($column) {
            case 'Fecha_Nacimiento':
            case 'Fecha_Apertura':
            case 'Fecha_Actualizacion_Ive':
            case 'Fecha_Ingreso_Laboral':
            case 'Fecha_Inicio_Negocio':
                return $this->parseDate($value);

            case 'Alto_Riesgo':
            case 'Negocio_Propio':
            case 'Persona_PEP':
            case 'Persona_CPE':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case 'Edad':
            case 'Id_Reple':
                return is_numeric($value) ? (int)$value : null;

            case 'Ingresos_Laborales':
            case 'Ingresos_Negocio_Propio':
            case 'Ingresos_Remesas':
            case 'Monto_Otros_Ingresos':
            case 'Monto_Ingresos':
            case 'Monto_Egresos':
                return is_numeric($value) ? (float)$value : null;

            default:
                return $this->sanitizeString($value);
        }
    }

    /**
     * Convertir fechas de formato d/m/Y a Y-m-d.
     *
     * @param string|null $value
     * @return string|null
     */
    private function parseDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Fecha inválida: {$value}");
            return null;
        }
    }

    /**
     * Sanitizar cadenas para evitar datos inválidos.
     *
     * @param string|null $value
     * @return string|null
     */
    private function sanitizeString($value)
    {
        return $value ? trim($value) : null;
    }
}