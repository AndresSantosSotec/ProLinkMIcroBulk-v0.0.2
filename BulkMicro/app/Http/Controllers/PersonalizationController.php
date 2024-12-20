<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonalizationController extends Controller
{
    /**
     * Obtener personalización por tipo.
     *
     * @param string $tipo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData($tipo)
    {
        try {
            // Verificar si el tipo es válido
            $allowedTypes = ['Asociados', 'Captaciones', 'Colocaciones'];
            if (!in_array($tipo, $allowedTypes)) {
                return response()->json(['error' => 'Tipo de personalización no válido'], 400);
            }

            // Buscar personalización
            $personalization = DB::table('personalizations')->where('tipo', $tipo)->first();
            if (!$personalization) {
                return response()->json(['error' => 'Configuración no encontrada para el tipo ' . $tipo], 404);
            }

            return response()->json($personalization);
        } catch (\Exception $e) {
            Log::error("Error al obtener la personalización para el tipo {$tipo}: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Guardar o actualizar personalización por tipo.
     *
     * @param Request $request
     * @param string $tipo
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveData(Request $request, $tipo)
    {
        try {
            // Validar tipo permitido
            $allowedTypes = ['Asociados', 'Captaciones', 'Colocaciones'];
            if (!in_array($tipo, $allowedTypes)) {
                return response()->json(['error' => 'Tipo de personalización no válido'], 400);
            }

            // Validar los datos
            $data = $request->validate([
                'intervalo_horas' => 'required|integer|min:1',
                'notificaciones_email' => 'required|boolean',
            ]);

            // Guardar o actualizar
            DB::table('personalizations')->updateOrInsert(
                ['tipo' => $tipo],
                [
                    'intervalo_horas' => $data['intervalo_horas'],
                    'notificaciones_email' => $data['notificaciones_email'],
                    'updated_at' => now(),
                ]
            );

            return response()->json(['message' => 'Personalización guardada correctamente']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Datos no válidos', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Error al guardar la personalización para el tipo {$tipo}: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
}
