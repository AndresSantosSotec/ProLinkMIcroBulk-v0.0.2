<?php
// Configuración de conexión a la base de datos (PostgreSQL)
$host = '127.0.0.1';       // Host de la base de datos
$dbname = 'bd_bulk';       // Nombre de la base de datos
$username = 'postgres';    // Usuario de la base de datos
$password = '1234';        // Contraseña de la base de datos
$port = '5432';            // Puerto de PostgreSQL

try {
    // Conexión a la base de datos con PDO para PostgreSQL
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener las columnas de la tabla `clientes`
    $query = $pdo->prepare("
        SELECT column_name
        FROM information_schema.columns
        WHERE table_name = 'clientes'
        AND table_schema = 'public'
    ");
    $query->execute();
    $columns = $query->fetchAll(PDO::FETCH_COLUMN); // Obtener solo los nombres de las columnas
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>