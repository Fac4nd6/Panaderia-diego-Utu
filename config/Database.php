<?php

// Abre la conexión principal con MySQL.
$host = 'localhost';
$user = 'root';
$password = '';
// Esta copia usa su propia base y no altera la base original.
$database = 'don_diego_simple';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli(
    $host,
    $user,
    $password,
    $database
);

if ($conn->connect_error) {

    error_log($conn->connect_error);
    http_response_code(503);

    exit('No se pudo conectar con la base don_diego_simple. Importa database/don_diego.sql y vuelve a intentar.');

}

$conn->set_charset('utf8mb4');

// Una base existente sin tablas suele indicar que falta importar el SQL del proyecto.
$tablas = $conn->query("SHOW TABLES LIKE 'productos'");
if (!$tablas || $tablas->num_rows === 0) {
    http_response_code(503);
    exit('La base don_diego_simple esta vacia. Importa database/don_diego.sql y vuelve a intentar.');
}