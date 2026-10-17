<?php
$titulo = 'Ingresar';
require __DIR__ . '/../header.php';
?>
<section class="auth">
	<h1>Ingresar</h1>
	<?php if ($error): ?><p class="notice error"><?= h($error) ?></p><?php endif; ?>
	<?php if ($mensaje): ?><p class="notice"><?= h($mensaje) ?></p><?php endif; ?>
	<?php if (isset($_GET['restablecida'])): ?><p class="notice">Contrasena actualizada. Ya puedes ingresar.</p><?php endif; ?>
	<form class="stack" method="post" action="<?= url('/login') ?>">
		<label>Correo electronico<input type="email" name="email" autocomplete="email" required></label>
		<label>Contrasena<input type="password" name="password" autocomplete="current-password" required></label>
		<button class="button" type="submit">Ingresar</button>
	</form>
	<p><a href="<?= url('/registro') ?>">Crear cuenta</a> · <a href="<?= url('/recuperar-contrasena') ?>">Olvide mi contrasena</a> · <a href="<?= url('/reenviar-verificacion') ?>">Reenviar verificacion</a></p>
</section>
<?php require __DIR__ . '/../footer.php'; ?>
