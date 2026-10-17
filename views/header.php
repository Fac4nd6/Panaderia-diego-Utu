<?php
require_once __DIR__ . '/../config/Url.php';

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$titulo = $titulo ?? 'Panaderia Don Diego';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titulo) ?> | Don Diego</title>
    <link rel="stylesheet" href="<?= url('/public/css/style.css') ?>">
</head>
<body>
<header class="site-header">
    <a class="brand" href="<?= url('/') ?>"><img src="<?= url('/public/img/logo.avif') ?>" alt="">Don Diego</a>
    <nav aria-label="Navegacion principal">
        <a href="<?= url('/productos') ?>">Catalogo</a>
        <a href="<?= url('/pedidos/crear') ?>">Hacer pedido</a>
        <?php if (in_array($_SESSION['usuario_rol'] ?? '', ['admin', 'empleado'], true)): ?>
            <a href="<?= url('/admin/pedidos') ?>">Pedidos</a>
        <?php endif; ?>
        <?php if (($_SESSION['usuario_rol'] ?? '') === 'admin'): ?>
            <a href="<?= url('/admin/productos') ?>">Productos</a>
            <a href="<?= url('/admin/estadisticas') ?>">Estadisticas</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <a href="<?= url('/logout') ?>">Salir</a>
        <?php else: ?>
            <a href="<?= url('/login') ?>">Ingresar</a>
        <?php endif; ?>
    </nav>
</header>
<main class="page">
