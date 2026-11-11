<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../models/Producto.php';

iniciar_sesion_segura();

// El catálogo público solo muestra productos habilitados.
$productos = (new Producto($conn))->obtenerActivos();

require __DIR__ . '/../views/catalogo.php';
