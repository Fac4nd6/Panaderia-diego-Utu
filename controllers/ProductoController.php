<?php

require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Url.php';
require_once __DIR__ . '/../models/Producto.php';

iniciar_sesion_segura();
if (($_SESSION['usuario_rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso restringido al administrador.');
}

$productoModel = new Producto($conn);
$accion = $_POST['accion'] ?? $_GET['accion'] ?? 'listar';
$error = '';

// Validar y guardar los datos recibidos del formulario.
if ($accion === 'guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = filter_var($_POST['precio'] ?? '', FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT);
    $productoActual = $id > 0 ? $productoModel->obtenerPorId($id) : null;
    $imagen = basename($productoActual['imagen'] ?? '');
    $producto = compact('id', 'nombre', 'descripcion', 'precio', 'stock', 'imagen');

    if ($nombre === '' || $precio === false || $precio < 0 || $stock === false || $stock < 0) {
        $error = 'Revisa nombre, precio y stock.';
        require __DIR__ . '/../views/admin/producto-form.php';
        exit;
    }

    $archivo = $_FILES['imagen'] ?? null;
    $errorCarga = $archivo['error'] ?? UPLOAD_ERR_NO_FILE;
    $rutaSubida = null;

    if ($errorCarga === UPLOAD_ERR_NO_FILE) {
        if ($id === 0) {
            $error = 'Selecciona una imagen para el producto.';
        }
    } elseif ($errorCarga !== UPLOAD_ERR_OK) {
        $error = 'No se pudo recibir la imagen. Intenta con un archivo de hasta 5 MB.';
    } elseif (($archivo['size'] ?? 0) > 5 * 1024 * 1024) {
        $error = 'La imagen debe pesar menos de 5 MB.';
    } else {
        $tiposPermitidos = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/avif' => 'avif'
        ];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tipoImagen = $finfo->file($archivo['tmp_name']);

        if (!isset($tiposPermitidos[$tipoImagen])) {
            $error = 'El archivo debe ser una imagen JPG, PNG, WEBP o AVIF.';
        } else {
            $nombreArchivo = 'producto_' . bin2hex(random_bytes(16)) . '.' . $tiposPermitidos[$tipoImagen];
            $rutaSubida = __DIR__ . '/../public/img/' . $nombreArchivo;

            if (!move_uploaded_file($archivo['tmp_name'], $rutaSubida)) {
                $rutaSubida = null;
                $error = 'No se pudo guardar la imagen. Revisa los permisos de public/img.';
            } else {
                $imagen = $nombreArchivo;
                $producto['imagen'] = $imagen;
            }
        }
    }

    if ($error !== '') {
        require __DIR__ . '/../views/admin/producto-form.php';
        exit;
    }

    if (!$productoModel->guardar($id, $nombre, $descripcion, $precio, $stock, $imagen)) {
        if ($rutaSubida !== null && is_file($rutaSubida)) {
            unlink($rutaSubida);
        }
        $error = 'No se pudo guardar el producto.';
        require __DIR__ . '/../views/admin/producto-form.php';
        exit;
    }

    header('Location: ' . url('/admin/productos'));
    exit;
}

// Un producto enlazado a pedidos históricos no se elimina.
if ($accion === 'eliminar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$productoModel->eliminar((int) ($_POST['id'] ?? 0))) {
        $error = 'No se puede eliminar un producto incluido en pedidos existentes.';
    }
}

// Mostrar el formulario vacío o cargar los datos del producto a editar.
if ($accion === 'nuevo' || $accion === 'editar') {
    $producto = $accion === 'editar'
        ? $productoModel->obtenerPorId((int) ($_GET['id'] ?? 0))
        : null;
    require __DIR__ . '/../views/admin/producto-form.php';
    exit;
}

$productos = $productoModel->obtenerTodos();
require __DIR__ . '/../views/admin/productos.php';
