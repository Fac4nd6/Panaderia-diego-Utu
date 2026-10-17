<?php
$titulo = 'Productos';
require __DIR__ . '/../header.php';
?>
<div class="title-row">
    <h1>Productos</h1>
    <a class="button" href="<?= url('/admin/productos/nuevo') ?>">Agregar producto</a>
</div>

<?php if ($error): ?>
    <p class="notice error"><?= h($error) ?></p>
<?php endif; ?>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td>
                        <img class="product-thumb" src="<?= url('/public/img/' . rawurlencode(basename($producto['imagen']))) ?>" alt="">
                    </td>
                    <td class="product-name-cell">
                        <strong><?= h($producto['nombre']) ?></strong>
                        <span class="product-state <?= (int) $producto['activo'] ? 'is-active' : 'is-inactive' ?>">
                            <?= (int) $producto['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>$<?= number_format((float) $producto['precio'], 2, ',', '.') ?></td>
                    <td><?= (int) $producto['stock'] ?></td>
                    <td class="actions product-actions">
                        <a class="product-action-edit" href="<?= url('/admin/productos/editar?id=' . (int) $producto['id']) ?>">Editar</a>
                        <form method="post" action="<?= url('/admin/productos/eliminar') ?>">
                            <input type="hidden" name="id" value="<?= (int) $producto['id'] ?>">
                            <button class="product-action-delete" type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../footer.php'; ?>
