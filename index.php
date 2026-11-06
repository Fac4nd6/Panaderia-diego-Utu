<?php

require_once __DIR__ . '/config/Session.php';
iniciar_sesion_segura();

// Cada página pública apunta a su controlador y, si corresponde, a una acción.
$paginas = [
    'productos' => ['CatalogoController.php', null],
    'pedido-crear' => ['PedidoController.php', 'crear'],
    'pedido' => ['PedidoController.php', 'confirmacion'],
    'login' => ['AuthController.php', 'login'],
    'registro' => ['AuthController.php', 'register'],
    'verificar-correo' => ['AuthController.php', 'verificar'],
    'reenviar-verificacion' => ['AuthController.php', 'reenviar'],
    'recuperar' => ['AuthController.php', 'recuperar'],
    'restablecer' => ['AuthController.php', 'restablecer'],
    'logout' => ['AuthController.php', 'logout'],
    'admin-pedidos' => ['PedidoController.php', 'listar'],
    'admin-pedido-estado' => ['PedidoController.php', 'estado'],
    'admin-estadisticas' => ['PedidoController.php', 'estadisticas'],
    'admin-productos' => ['ProductoController.php', 'listar'],
    'producto-nuevo' => ['ProductoController.php', 'nuevo'],
    'producto-editar' => ['ProductoController.php', 'editar'],
    'producto-guardar' => ['ProductoController.php', 'guardar'],
    'producto-eliminar' => ['ProductoController.php', 'eliminar']
];

// El catálogo es la entrada principal cuando no se indica una página.
$pagina = $_GET['page'] ?? 'productos';
if (!isset($paginas[$pagina])) {
    http_response_code(404);
    exit('Pagina no encontrada.');
}

[$controlador, $accion] = $paginas[$pagina];
if ($accion !== null) {
    $_GET['accion'] = $accion;
}

// El controlador prepara los datos y elige la vista según la acción.
require __DIR__ . '/controllers/' . $controlador;
