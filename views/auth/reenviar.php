<?php
$error = $error ?? '';
$mensaje = $mensaje ?? '';
$csrfReenvio = $csrfReenvio ?? '';
$titulo = 'Reenviar verificacion';
require __DIR__ . '/../header.php';
?>
<section class="auth">
	<h1>Reenviar verificacion</h1>
	<?php if ($error): ?><p class="notice error"><?= h($error) ?></p><?php endif; ?>
	<?php if ($mensaje): ?><p class="notice"><?= h($mensaje) ?></p><?php endif; ?>
	<form class="stack" method="post" action="<?= url('/reenviar-verificacion') ?>">
		<input type="hidden" name="csrf_token" value="<?= h($csrfReenvio) ?>">
		<label>Correo electronico<input type="email" name="email" autocomplete="email" required></label>
		<button class="button" type="submit">Enviar enlace</button>
	</form>
	<p><a href="<?= url('/login') ?>">Volver a ingresar</a></p>
</section>
<?php require __DIR__ . '/../footer.php'; ?>