<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ColumnConfiguration;
use Illuminate\Support\Facades\Schema;

class ColumnConfigurationController extends Controller
{
    // ============================
    // Funciones para Clientes (Asociados)
    // ============================

    /**
     * Mostrar columnas configurables para la tabla 'clientes'.
     */
    public function showColumns()
    {
        return $this->showTableColumns('clientes', 1, 'id_asociado', 'components.tablas.Asotb');
    }


    /**
     * Editar columna de clientes.
     */
    public function editColumn($columnName)
    {
        // Buscar la configuración de la columna
        $columnConfig = ColumnConfiguration::where('column_name', $columnName)
            ->where('id_tipo', 1) // Clientes (ID tipo correspondiente)
            ->first();

        // Si no existe configuración, inicializa valores por defecto
        $column = [
            'column_name' => $columnName,
            'excel_column_name' => $columnConfig->excel_column_name ?? '',
            'column_number' => $columnConfig->column_number ?? '',
        ];

        // Definir $tipo para la vista
        $tipo = 'clientes';

        // Pasar datos a la vista
        return view('components.tablas.Operaciones.AsoEdit', compact('column', 'tipo'));
    }
    /**
     * Actualizar o crear una configuración para una columna en la tabla 'clientes'.
     */
    /**
     * Actualizar o crear una configuración para una columna en la tabla 'clientes'.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateColumn(Request $request)
    {
        // Validar los datos enviados
        $validatedData = $request->validate([
            'columnName' => 'required|string|max:100',
            'excelColumnName' => 'required|string|max:100',
            'columnNumber' => 'required|integer|min:0',
        ]);

        // Crear o actualizar la configuración
        $this->upsertColumnConfiguration($validatedData, 1);

        // Redirigir con mensaje de éxito
        return redirect()->route('columnas.clientes')->with('success', 'Columna actualizada correctamente.');
    }

    /**
     * Eliminar una configuración de columna en la tabla 'clientes'.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteColumn(Request $request)
    {
        $validatedData = $request->validate([
            'columnName' => 'required|string|max:100',
        ]);

        ColumnConfiguration::where('column_name', $validatedData['columnName'])
            ->where('id_tipo', 1)
            ->delete();

        return redirect()->route('columnas.clientes')->with('success', 'Columna eliminada correctamente.');
    }

    // ============================
    // Funciones para Captaciones
    // ============================

    /**
     * Mostrar columnas configurables para la tabla 'captaciones'.
     */
    public function showCaptacionesColumns()
    {
        return $this->showTableColumns('captaciones', 2, 'id_captacion', 'components.tablas.Captb');
    }
    /**
     * Edicion de columnas y caotaciones por medio de la vista
     */
    public function editColumnCaptaciones($columnName)
    {
        // Buscar la configuración de la columna
        $columnConfig = ColumnConfiguration::where('column_name', $columnName)
            ->where('id_tipo', 2) // Captaciones
            ->first();

        // Si no existe configuración, inicializa valores por defecto
        $column = [
            'column_name' => $columnName,
            'excel_column_name' => $columnConfig->excel_column_name ?? '',
            'column_number' => $columnConfig->column_number ?? '',
        ];

        // Definir $tipo para la vista
        $tipo = 'captaciones';

        // Pasar datos a la vista
        return view('components.tablas.Operaciones.CapEdit', compact('column', 'tipo'));
    }

    /**
     * Actualizar o crear una configuración para una columna en la tabla 'captaciones'.
     */
    public function updateCaptacionesColumn(Request $request)
    {
        // Validar los datos enviados
        $validatedData = $request->validate([
            'columnName' => 'required|string|max:100',
            'excelColumnName' => 'required|string|max:100',
            'columnNumber' => 'required|integer|min:0',
        ]);

        // Crear o actualizar la configuración
        $this->upsertColumnConfiguration($validatedData, 2);

        // Redirigir con mensaje de éxito
        return redirect()->route('columnas.captaciones')->with('success', 'Columna actualizada correctamente.');
    }

    /**
     * Eliminar una configuración de columna en la tabla 'captaciones'.
     */
    public function deleteCaptacionesColumn(Request $request)
    {
        $validatedData = $request->validate([
            'columnName' => 'required|string|max:100',
        ]);

        ColumnConfiguration::where('column_name', $validatedData['columnName'])
            ->where('id_tipo', 2)
            ->delete();

        return redirect()->route('columnas.captaciones')->with('success', 'Columna eliminada correctamente.');
    }
    // ============================
    // Funciones para Colocaciones
    // ============================

    /**
     * Mostrar columnas configurables para la tabla 'colocaciones'.
     */
    public function showColocacionesColumns()
    {
        return $this->showTableColumns('colocaciones', 3, 'id_colocacion', 'components.tablas.Coltb');
    }
    /**
     * Funcion de codigo para el edit
     */
    public function editColumnColocaciones($columnName)
    {
        // Buscar la configuración de la columna
        $columnConfig = ColumnConfiguration::where('column_name', $columnName)
            ->where('id_tipo', 3) // Colocaciones
            ->first();

        // Si no existe configuración, inicializa valores por defecto
        $column = [
            'column_name' => $columnName,
            'excel_column_name' => $columnConfig->excel_column_name ?? '',
            'column_number' => $columnConfig->column_number ?? '',
        ];

        // Definir $tipo para la vista
        $tipo = 'colocaciones';

        // Pasar datos a la vista
        return view('components.tablas.Operaciones.ColEdit', compact('column', 'tipo'));
    }
    /**
     * Actualizar o crear una configuración para una columna en la tabla 'colocaciones'.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateColocacionesColumn(Request $request)
    {
        // Validar los datos enviados
        $validatedData = $request->validate([
            'columnName' => 'required|string|max:100',
            'excelColumnName' => 'required|string|max:100',
            'columnNumber' => 'required|integer|min:0',
        ]);

        // Crear o actualizar la configuración
        $this->upsertColumnConfiguration($validatedData, 3);

        // Redirigir con mensaje de éxito
        return redirect()->route('columnas.colocaciones')->with('success', 'Columna actualizada correctamente.');
    }

    /**
     * Eliminar una configuración de columna en la tabla 'colocaciones'.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteColocacionesColumn(Request $request)
    {
        $validatedData = $request->validate([
            'columnName' => 'required|string|max:100',
        ]);

        ColumnConfiguration::where('column_name', $validatedData['columnName'])
            ->where('id_tipo', 3)
            ->delete();

        return redirect()->route('columnas.colocaciones')->with('success', 'Columna eliminada correctamente.');
    }
    // ============================
    // Métodos Auxiliares
    // ============================

    /**
     * Mostrar columnas configurables de una tabla específica.
     *
     * @param string $tableName Nombre de la tabla en la base de datos.
     * @param int $idTipo Identificador del tipo de tabla.
     * @param string $excludedField Campo a excluir de las columnas.
     * @param string $view Nombre de la vista que se renderizará.
     * @return \Illuminate\View\View
     */
    private function showTableColumns($tableName, $idTipo, $excludedField, $view)
    {
        // Excluir campos específicos (como 'id', 'created_at', 'updated_at')
        $excludedColumns = [$excludedField, 'created_at', 'updated_at'];

        // Filtrar las columnas de la tabla excluyendo las indicadas
        $allTableColumns = array_filter(
            Schema::getColumnListing($tableName),
            fn($column) => !in_array($column, $excludedColumns)
        );

        // Obtener las columnas configuradas en la base de datos
        $configuredColumns = ColumnConfiguration::where('id_tipo', $idTipo)->get();

        // Combinar las columnas
        $allColumns = $this->combineColumns($allTableColumns, $configuredColumns);

        // Retornar la vista con los datos
        return view($view, ['allColumns' => $allColumns]);
    }


    /**
     * Actualizar o crear una configuración de columna.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $idTipo Identificador del tipo de tabla.
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleColumnUpdate(Request $request, $idTipo)
    {
        // Validar datos del formulario
        $validatedData = $this->validateColumnData($request);

        // Crear o actualizar la configuración
        $this->upsertColumnConfiguration($validatedData, $idTipo);

        return response()->json(['success' => true]);
    }

    /**
     * Eliminar una configuración de columna.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $idTipo Identificador del tipo de tabla.
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleColumnDelete(Request $request, $idTipo)
    {
        $validatedData = $request->validate([
            'columnName' => 'required|string|max:100',
        ]);

        ColumnConfiguration::where('column_name', $validatedData['columnName'])
            ->where('id_tipo', $idTipo)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Combinar columnas de la tabla con las configuradas en la base de datos.
     */
    private function combineColumns($allTableColumns, $configuredColumns)
    {
        return array_map(function ($column) use ($configuredColumns) {
            $configured = $configuredColumns->firstWhere('column_name', $column);
            return [
                'id' => $configured->id ?? null,
                'column_name' => $column,
                'excel_column_name' => $configured->excel_column_name ?? '',
                'column_number' => $configured->column_number ?? '',
                'is_configured' => $configured ? true : false,
            ];
        }, $allTableColumns);
    }

    /**
     * Validar datos del formulario de actualización o creación de columna.
     */
    private function validateColumnData(Request $request)
    {
        return $request->validate([
            'columnName' => 'required|string|max:100',
            'excelColumnName' => 'required|string|max:100',
            'columnNumber' => 'required|integer',
        ]);
    }

    /**
     * Crear o actualizar una configuración de columna en la base de datos.
     */
    private function upsertColumnConfiguration($validatedData, $idTipo)
    {
        $columnConfig = ColumnConfiguration::where('column_name', $validatedData['columnName'])
            ->where('id_tipo', $idTipo)
            ->first();

        if ($columnConfig) {
            $columnConfig->excel_column_name = $validatedData['excelColumnName'];
            $columnConfig->column_number = $validatedData['columnNumber'];
            $columnConfig->save();
        } else {
            ColumnConfiguration::create([
                'id_tipo' => $idTipo,
                'column_name' => $validatedData['columnName'],
                'excel_column_name' => $validatedData['excelColumnName'],
                'column_number' => $validatedData['columnNumber'],
            ]);
        }
    }
}
