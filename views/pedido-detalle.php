<?php
$titulo = 'Pedido';
$nombreEstado = [
    'pendiente' => 'Pendiente',
    'en_preparacion' => 'En preparación',
    'listo' => 'Listo',
    'entregado' => 'Entregado'
];
require __DIR__ . '/header.php';
?>
<?php if (!$pedido): ?>
    <h1>Pedido no encontrado</h1>
    <p>Revisa el numero del pedido e intenta nuevamente.</p>
<?php else: ?>
    <h1>Pedido #<?= (int) $pedido['id'] ?></h1>
    <p>Estado: <strong><?= h($nombreEstado[$pedido['estado']] ?? $pedido['estado']) ?></strong></p>
    <p>Retiro: <?= h($pedido['fecha_retiro']) ?> · <?= h($pedido['nombre_cliente']) ?> · <?= h($pedido['telefono']) ?></p>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedido['detalles'] as $detalle): ?>
                <tr>
                    <td><?= h($detalle['nombre']) ?></td>
                    <td><?= (int) $detalle['cantidad'] ?></td>
                    <td>$<?= number_format((float) $detalle['subtotal'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="total">Total: $<?= number_format((float) $pedido['total'], 2, ',', '.') ?></p>
<?php endif; ?>

<p><a href="<?= url('/productos') ?>">Volver al catalogo</a></p>
<?php require __DIR__ . '/footer.php'; ?>
