<?php
$titulo = 'Crear pedido';
require __DIR__ . '/header.php';
?>
<h1>Crear pedido</h1>

<?php if ($error): ?>
    <p class="notice error" role="alert"><?= h($error) ?></p>
<?php endif; ?>

<form method="post" action="<?= url('/pedidos/crear') ?>" class="stack">
    <div class="fields">
        <label>
            Nombre
            <input name="nombre" maxlength="150" required value="<?= h($_POST['nombre'] ?? '') ?>">
        </label>
        <label>
            Telefono
            <input name="telefono" type="tel" maxlength="30" required value="<?= h($_POST['telefono'] ?? '') ?>">
        </label>
        <label>
            Fecha de retiro
            <input name="fecha_retiro" type="date" min="<?= date('Y-m-d') ?>" required value="<?= h($_POST['fecha_retiro'] ?? '') ?>">
        </label>
    </div>

    <h2>Productos</h2>
    <div class="order-list">
        <?php foreach ($productos as $producto): ?>
            <label class="order-line">
                <span>
                    <?= h($producto['nombre']) ?>
                    <small>$<?= number_format((float) $producto['precio'], 2, ',', '.') ?> · stock <?= (int) $producto['stock'] ?></small>
                </span>
                <input
                    aria-label="Cantidad de <?= h($producto['nombre']) ?>"
                    type="number"
                    min="0"
                    max="99"
                    name="cantidad[<?= (int) $producto['id'] ?>]"
                    value="<?= h($_POST['cantidad'][$producto['id']] ?? 0) ?>"
                >
            </label>
        <?php endforeach; ?>
    </div>

    <button class="button" type="submit">Confirmar pedido</button>
</form>

<?php require __DIR__ . '/footer.php'; ?>
