<?php

// Abre la conexión principal con MySQL.
$host = 'localhost';
$user = 'root';
$password = '';
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

    exit('No se pudo conectar con la base Datos. ');

}

$conn->set_charset('utf8mb4');
