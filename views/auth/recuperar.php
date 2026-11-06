<?php
$titulo = 'Recuperar contrasena';
require __DIR__ . '/../header.php';
?>
<section class="auth">
	<h1>Recuperar contrasena</h1>
	<?php if ($mensaje): ?><p class="notice"><?= h($mensaje) ?></p><?php endif; ?>
	<form class="stack" method="post" action="<?= url('/recuperar-contrasena') ?>">
		<label>Correo electronico<input type="email" name="email" autocomplete="email" required></label>
		<button class="button" type="submit">Enviar enlace</button>
	</form>
	<p><a href="<?= url('/login') ?>">Volver a ingresar</a></p>
</section>
<?php require __DIR__ . '/../footer.php'; ?>
