<?php
$titulo = empty($producto['id']) ? 'Agregar producto' : 'Editar producto';
require __DIR__ . '/../header.php';
?>
<h1><?= h($titulo) ?></h1>

<?php if ($error): ?>
    <p class="notice error"><?= h($error) ?></p>
<?php endif; ?>

<form class="stack narrow" method="post" action="<?= url('/admin/productos/guardar') ?>" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="guardar">
    <input type="hidden" name="id" value="<?= (int) ($producto['id'] ?? 0) ?>">

    <label>
        Nombre
        <input name="nombre" maxlength="150" required value="<?= h($producto['nombre'] ?? '') ?>">
    </label>
    <label>
        Descripcion
        <textarea name="descripcion" rows="3"><?= h($producto['descripcion'] ?? '') ?></textarea>
    </label>
    <div class="fields">
        <label>
            Precio
            <input type="number" name="precio" min="0" step="0.01" required value="<?= h($producto['precio'] ?? '') ?>">
        </label>
        <label>
            Stock
            <input type="number" name="stock" min="0" step="1" required value="<?= h($producto['stock'] ?? 0) ?>">
        </label>
    </div>
    <div class="product-image-field">
        <label>
            Imagen del producto
            <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp,image/avif" aria-describedby="product-image-help" <?= empty($producto['id']) ? 'required' : '' ?>>
        </label>
        <small id="product-image-help">JPG, PNG, WEBP o AVIF. Maximo 5 MB.</small>
        <?php if (!empty($producto['imagen'])): ?>
            <div class="product-image-current">
                <img src="<?= url('/public/img/' . rawurlencode(basename($producto['imagen']))) ?>" alt="Imagen actual de <?= h($producto['nombre'] ?? 'producto') ?>">
                <span>Imagen actual</span>
            </div>
        <?php endif; ?>
    </div>
    <button class="button" type="submit">Guardar producto</button>
</form>

<?php require __DIR__ . '/../footer.php'; ?>
