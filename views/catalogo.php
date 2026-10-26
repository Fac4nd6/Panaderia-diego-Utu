<?php
$titulo = 'Catalogo';
require __DIR__ . '/header.php';
?>
<h1>Catalogo</h1>
<div class="product-grid">
	<?php foreach ($productos as $producto): ?>
		<article class="product">
			<img src="<?= url('/public/img/' . rawurlencode(basename($producto['imagen']))) ?>" alt="<?= h($producto['nombre']) ?>">
			<h2><?= h($producto['nombre']) ?></h2>
			<p><?= h($producto['descripcion']) ?></p>
			<div class="product-meta">
				<strong class="product-price">$<?= number_format((float) $producto['precio'], 2, ',', '.') ?></strong>
				<span class="product-stock"><?= (int) $producto['stock'] ?> disponibles</span>
			</div>
		</article>
	<?php endforeach; ?>
</div>
<p><a class="button" href="<?= url('/pedidos/crear') ?>">Crear pedido</a></p>
<?php require __DIR__ . '/footer.php'; ?>
