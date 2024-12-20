<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalPath;
use App\Models\AwsBucket;

class ConfigurationController extends Controller
{
    /**
     * Guardar configuración.
     */
    public function saveConfiguration(Request $request)
    {
        $request->validate([
            'configType' => 'required|string|in:local,aws',
            'configStep' => 'required|string|in:asociados,captaciones,colocaciones',
        ]);

        $configType = $request->configType;
        $configStep = $request->configStep;

        if ($configType === 'local') {
            $request->validate([
                'path' => 'required|string|max:255',
                'file_name' => 'nullable|string|max:255',
            ]);

            $description = "Configuración para $configStep en carpeta local.";

            LocalPath::updateOrCreate(
                ['config_type' => $configStep],
                [
                    'path' => $request->path,
                    'file_name' => $request->file_name,
                    'description' => $description,
                ]
            );

            return response()->json(['message' => 'Configuración de ruta local guardada correctamente.']);
        }

        if ($configType === 'aws') {
            $request->validate([
                'bucket_path' => 'required|string|max:255',
                'access_key' => 'required|string|max:255',
                'secret_key' => 'required|string|max:255',
                'file_name' => 'required|string|max:255',
            ]);

            $description = "Configuración para $configStep en bucket AWS.";

            AwsBucket::updateOrCreate(
                ['config_type' => $configStep],
                [
                    'bucket_path' => $request->bucket_path,
                    'access_key' => $request->access_key,
                    'secret_key' => $request->secret_key,
                    'file_name' => $request->file_name,
                    'description' => $description,
                ]
            );

            return response()->json(['message' => 'Configuración de bucket AWS guardada correctamente.']);
        }

        return response()->json(['error' => 'Configuración no válida.'], 400);
    }
}
