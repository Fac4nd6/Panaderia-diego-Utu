<?php
$titulo = 'Restablecer contrasena';
require __DIR__ . '/../header.php';
?>
<section class="auth">
	<h1>Nueva contrasena</h1>
	<?php if ($error): ?><p class="notice error"><?= h($error) ?></p><?php endif; ?>
	<?php if ($tokenValido): ?>
		<form class="stack" method="post" action="<?= url('/restablecer-contrasena') ?>">
			<input type="hidden" name="token" value="<?= h($token) ?>">
			<label>Contrasena nueva<input type="password" name="password" minlength="8" autocomplete="new-password" required></label>
			<button class="button" type="submit">Guardar contrasena</button>
		</form>
	<?php else: ?>
		<p>El enlace no es valido o ya vencio.</p>
		<a class="button" href="<?= url('/recuperar-contrasena') ?>">Solicitar otro enlace</a>
	<?php endif; ?>
</section>
<?php require __DIR__ . '/../footer.php'; ?>
