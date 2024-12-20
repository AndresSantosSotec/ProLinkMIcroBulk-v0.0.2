<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function convertExcelToJson(Request $request)
    {
        try {
            // Validar el archivo
            $request->validate([
                'file' => 'required|file|mimes:xls,xlsx|max:20480',
            ]);

            $file = $request->file('file')->getRealPath();

            // Cargar el archivo Excel
            $spreadsheet = IOFactory::load($file);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            if (empty($sheetData) || !isset($sheetData[0])) {
                return response()->json(['message' => 'El archivo está vacío o tiene un formato inválido.'], 422);
            }

            // Obtener encabezados y normalizarlos
            $headers = array_map('trim', array_shift($sheetData));
            $normalizedHeaders = array_map(fn($h) => $this->normalizeHeader($h), $headers);

            // Procesar filas
            $data = [];
            $errors = [];

            foreach ($sheetData as $index => $row) {
                try {
                    $normalizedRow = array_combine($normalizedHeaders, array_values($row));
                    $data[] = $this->validateAndTransformRow($normalizedRow);
                } catch (\Exception $e) {
                    $errors[] = "Fila " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'validData' => $data,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            Log::error("Error al procesar archivo: {$e->getMessage()}");
            return response()->json(['message' => 'Error interno al procesar el archivo.'], 500);
        }
    }

    // Validar y transformar cada fila
    private function validateAndTransformRow($row)
    {
        $transformed = [];
        foreach ($row as $key => $value) {
            switch ($key) {
                case 'Codigo_Cliente':
                    $transformed[$key] = $value ?: 'GEN-' . uniqid();
                    break;
                case 'Edad':
                    if (!is_numeric($value)) {
                        throw new \Exception("La edad debe ser un número.");
                    }
                    $transformed[$key] = (int)$value;
                    break;
                case 'Fecha_Nacimiento':
                    $transformed[$key] = $this->formatDate($value);
                    break;
                default:
                    $transformed[$key] = trim($value);
            }
        }
        return $transformed;
    }

    // Normalizar nombres de encabezados (sin espacios, tildes, etc.)
    private function normalizeHeader($header)
    {
        $header = preg_replace('/\s+/', '_', $header); // Reemplaza espacios con _
        $header = iconv('UTF-8', 'ASCII//TRANSLIT', $header); // Elimina acentos
        return preg_replace('/[^A-Za-z0-9_]/', '', $header); // Remueve caracteres especiales
    }

    // Formatear fechas al formato Y-m-d
    private function formatDate($date)
    {
        if (!$date) return null;

        $parsed = \DateTime::createFromFormat('d/m/Y', $date) ?: \DateTime::createFromFormat('Y-m-d', $date);
        if (!$parsed) {
            throw new \Exception("Fecha no válida: {$date}");
        }
        return $parsed->format('Y-m-d');
    }

    public function insertValidatedData(Request $request)
    {
        try {
            $data = $request->input('data');
            if (empty($data)) {
                return response()->json(['message' => 'No se proporcionaron datos válidos.'], 422);
            }

            DB::table('clientes')->insert($data);
            return response()->json(['message' => 'Datos insertados correctamente.', 'total' => count($data)]);
        } catch (\Exception $e) {
            Log::error("Error al insertar datos: {$e->getMessage()}");
            return response()->json(['message' => 'Error al insertar los datos.'], 500);
        }
    }
}
