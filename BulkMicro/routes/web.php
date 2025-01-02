<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CargasController;
use App\Http\Controllers\ColumnConfigurationController;
use App\Http\Controllers\PersonalizationController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\ImportController;


// Ruta principal siempre el dashboard
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
});

// ------------------- Rutas de Configuración de Columnas -------------------

// Clientes (Asociados)
Route::prefix('columnas-clientes')->group(function () {
    Route::get('/', [ColumnConfigurationController::class, 'showColumns'])->name('columnas.clientes');
    Route::post('/update', [ColumnConfigurationController::class, 'updateColumn'])->name('update-column');
    Route::post('/delete', [ColumnConfigurationController::class, 'deleteColumn'])->name('delete-column');
    Route::get('/editar/{column}', [ColumnConfigurationController::class, 'editColumn'])->name('columnas.clientes.editar');
});

// Captaciones
Route::prefix('columnas-captaciones')->group(function () {
    Route::get('/', [ColumnConfigurationController::class, 'showCaptacionesColumns'])->name('columnas.captaciones');
    Route::get('/editar/{column}', [ColumnConfigurationController::class, 'editColumnCaptaciones'])->name('columnas.captaciones.editar');
    Route::post('/update', [ColumnConfigurationController::class, 'updateCaptacionesColumn'])->name('update-column-captaciones');
    Route::post('/delete', [ColumnConfigurationController::class, 'deleteCaptacionesColumn'])->name('delete-column-captaciones');
});


// Colocaciones
Route::prefix('columnas-colocaciones')->group(function () {
    Route::get('/', [ColumnConfigurationController::class, 'showColocacionesColumns'])->name('columnas.colocaciones');
    Route::get('/editar/{column}', [ColumnConfigurationController::class, 'editColumnColocaciones'])->name('columnas.colocaciones.editar');
    Route::post('/update', [ColumnConfigurationController::class, 'updateColocacionesColumn'])->name('update-column-colocaciones');
    Route::post('/delete', [ColumnConfigurationController::class, 'deleteColocacionesColumn'])->name('delete-column-colocaciones');
});



// ------------------- Rutas de Personalización -------------------
Route::prefix('personalization')->group(function () {
    Route::get('/view/{tipo}', [PersonalizationController::class, 'loadView']);
    Route::post('/{tipo}', [PersonalizationController::class, 'saveData']);
    Route::get('/{tipo}', [PersonalizationController::class, 'getData']);
    Route::delete('/{tipo}', [PersonalizationController::class, 'deleteData']);
});

// ------------------- Rutas de Cargas -------------------
Route::prefix('cargas')->group(function () {
    Route::get('/{step}', [CargasController::class, 'getCargasByStep']);
});

// ------------------- Subidas y Configuraciones -------------------
Route::prefix('upload')->group(function () {
    Route::post('/', [UploadController::class, 'store'])->name('upload.store');
    Route::post('/config/aws', [UploadController::class, 'saveAwsConfig'])->name('config.aws');
    Route::post('/config/local', [UploadController::class, 'saveLocalConfig'])->name('config.local');
});


// Otras rutas
Route::get('/column-configurations', [ColumnConfigurationController::class, 'index'])->name('column-configurations.index');

// Rutas para guardar configuraciones de Asociados, Captaciones y Colocaciones
Route::post('/save-configuration', [ConfigurationController::class, 'saveConfiguration'])->name('configuration.save');

// Opcionales: rutas para cargar las configuraciones existentes (si son necesarias)
Route::get('/get-configuration/{configType}/{configStep}', [ConfigurationController::class, 'getConfiguration'])->name('configuration.get');

// *--Rutas para realizar las importaciones de las carga de las bases de datos--*

//--Ruta para la realziacion de la importacion de clientes 
Route::get('/upload', [ImportController::class, 'index'])->name('upload.excel');
Route::post('/upload-excel', [ImportController::class, 'uploadExcel'])->name('upload.excel.file');
Route::post('/insert-validated-data', [ImportController::class, 'insertValidatedData'])->name('insert-validated-data'); // Asegúrate de este nombre
