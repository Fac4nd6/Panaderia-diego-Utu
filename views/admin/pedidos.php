<?php
$titulo = 'Pedidos';
require __DIR__ . '/../header.php';
$nombreEstado = [
    'pendiente' => 'Pendiente',
    'en_preparacion' => 'En preparación',
    'listo' => 'Listo',
    'entregado' => 'Entregado'
];
?>
<h1>Pedidos</h1>

<form method="get" action="<?= url('/admin/pedidos') ?>" class="filter">
    <label>Desde<input type="date" name="desde" value="<?= h($desde) ?>"></label>
    <label>Hasta<input type="date" name="hasta" value="<?= h($hasta) ?>"></label>
    <button class="button" type="submit">Filtrar</button>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Pedido</th>
                <th>Cliente</th>
                <th>Retiro</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Actualizar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><a href="<?= url('/pedidos?id=' . (int) $pedido['id']) ?>">#<?= (int) $pedido['id'] ?></a></td>
                    <td><?= h($pedido['nombre_cliente']) ?><br><small><?= h($pedido['telefono']) ?></small></td>
                    <td><?= h($pedido['fecha_retiro']) ?></td>
                    <td>$<?= number_format((float) $pedido['total'], 2, ',', '.') ?></td>
                    <td><?= h($nombreEstado[$pedido['estado']] ?? $pedido['estado']) ?></td>
                    <td>
                        <form method="post" action="<?= url('/admin/pedidos/estado') ?>">
                            <input type="hidden" name="pedido_id" value="<?= (int) $pedido['id'] ?>">
                            <select name="estado" aria-label="Estado pedido <?= (int) $pedido['id'] ?>">
                                <?php foreach ($nombreEstado as $valor => $etiqueta): ?>
                                    <option value="<?= h($valor) ?>" <?= $pedido['estado'] === $valor ? 'selected' : '' ?>><?= h($etiqueta) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="button small" type="submit">Guardar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$pedidos): ?>
                <tr><td colspan="6">No hay pedidos para ese periodo.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../footer.php'; ?>
