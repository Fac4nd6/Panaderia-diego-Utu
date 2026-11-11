<?php

require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Url.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Producto.php';

iniciar_sesion_segura();
$pedidoModel = new Pedido($conn);
$accion = $_GET['accion'] ?? 'crear';
$error = '';

// El cliente puede pedir sin crear una cuenta.
if ($accion === 'crear') {
    $productos = (new Producto($conn))->obtenerActivos();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $fecha = $_POST['fecha_retiro'] ?? '';
        $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha);

        if (
            $nombre === '' ||
            $telefono === '' ||
            !$fechaValida ||
            $fechaValida->format('Y-m-d') !== $fecha ||
            $fecha < date('Y-m-d')
        ) {
            $error = 'Completa nombre, telefono y una fecha de retiro valida (hoy o posterior).';
        } else {
            $resultado = $pedidoModel->crear($nombre, $telefono, $fecha, $_POST['cantidad'] ?? []);

            if (isset($resultado['id'])) {
                $_SESSION['ultimo_pedido_id'] = (int) $resultado['id'];
                header('Location: ' . url('/pedidos'));
                exit;
            }

            $error = $resultado['error'];
        }
    }

    require __DIR__ . '/../views/pedido-crear.php';
    exit;
}

// El cliente ve la confirmación de su último pedido; el personal puede consultar por ID.
if ($accion === 'confirmacion') {
    $esPersonal = in_array($_SESSION['usuario_rol'] ?? '', ['admin', 'empleado'], true);
    $pedidoId = $esPersonal
        ? (int) ($_GET['id'] ?? 0)
        : (int) ($_SESSION['ultimo_pedido_id'] ?? 0);
    $pedido = $pedidoId > 0 ? $pedidoModel->obtener($pedidoId) : null;

    http_response_code($pedido ? 200 : 404);
    require __DIR__ . '/../views/pedido-detalle.php';
    exit;
}

// El resto de acciones de este controlador pertenece al personal.
if (!in_array($_SESSION['usuario_rol'] ?? '', ['admin', 'empleado'], true)) {
    http_response_code(403);
    exit('Acceso restringido al personal.');
}

// Actualizar el estado de un pedido existente.
if ($accion === 'estado' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedidoModel->actualizarEstado((int) ($_POST['pedido_id'] ?? 0), $_POST['estado'] ?? '');
    header('Location: ' . url('/admin/pedidos'));
    exit;
}

// Las estadísticas son una función exclusiva del administrador.
if ($accion === 'estadisticas') {
    if (($_SESSION['usuario_rol'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Acceso restringido al administrador.');
    }

    $estadisticas = $pedidoModel->estadisticas();
    require __DIR__ . '/../views/admin/estadisticas.php';
    exit;
}

// Los filtros inválidos se ignoran para evitar consultas con fechas incorrectas.
$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) {
    $desde = '';
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
    $hasta = '';
}

$pedidos = $pedidoModel->listar($desde, $hasta);
require __DIR__ . '/../views/admin/pedidos.php';
