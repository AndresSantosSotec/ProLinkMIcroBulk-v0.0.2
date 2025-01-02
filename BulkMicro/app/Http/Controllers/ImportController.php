<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ClientesImport;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    /**
     * Mostrar la vista de carga de archivo Excel y la lista de clientes.
     */
    public function index()
    {
        try {
            // Obtener todos los clientes
            $clientes = Cliente::all();
            return view('upload', compact('clientes'));
        } catch (\Exception $e) {
            Log::error('Error al cargar los datos: ' . $e->getMessage());
            return redirect()->back()->withErrors('Error al cargar los datos: ' . $e->getMessage());
        }
    }

    /**
     * Procesar el archivo Excel cargado e importar datos.
     */
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $import = new ClientesImport();
            Excel::import($import, $request->file('file'));

            $importedData = $import->getImportedData();

            foreach ($importedData as $data) {
                Cliente::updateOrCreate(
                    ['id' => $data['id']], // Ajusta el campo de identificación único según tu modelo
                    $data
                );
            }

            return response()->json(['message' => 'Datos importados exitosamente!', 'status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error al procesar el archivo: ' . $e->getMessage());
            return response()->json(['message' => 'Error al procesar el archivo: ' . $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    /**
     * Insertar datos validados manualmente en la base de datos.
     */
    public function insertValidatedData(Request $request)
    {
        $data = $request->input('data');

        if (empty($data)) {
            return response()->json(['message' => 'No se proporcionaron datos válidos.', 'status' => 'error'], 422);
        }

        try {
            DB::table('clientes')->insert($data);
            return response()->json(['message' => 'Datos insertados correctamente.', 'total' => count($data), 'status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error al insertar los datos: ' . $e->getMessage());
            return response()->json(['message' => 'Error al insertar los datos.', 'error' => $e->getMessage(), 'status' => 'error'], 500);
        }
    }

    /**
     * Insertar un cliente manualmente (opcional).
     */
    public function insertCliente(array $data)
    {
        try {
            Cliente::create($data);
            return response()->json(['message' => 'Cliente insertado exitosamente'], 201);
        } catch (\Exception $e) {
            Log::error('Error al insertar cliente: ' . $e->getMessage());
            return response()->json(['message' => 'Error al insertar cliente', 'error' => $e->getMessage()], 500);
        }
    }
}