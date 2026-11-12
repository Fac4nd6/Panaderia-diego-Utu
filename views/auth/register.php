<?php
$titulo = 'Registro';
require __DIR__ . '/../header.php';
?>
<section class="auth">
	<h1>Crear cuenta</h1>
	<?php if ($error): ?><p class="notice error"><?= h($error) ?></p><?php endif; ?>
	<?php if ($mensaje): ?><p class="notice"><?= h($mensaje) ?></p><?php endif; ?>
	<form class="stack" method="post" action="<?= url('/registro') ?>">
		<label>Nombre<input name="nombre" maxlength="150" autocomplete="name" required value="<?= h($_POST['nombre'] ?? '') ?>"></label>
		<label>Correo electronico<input type="email" name="email" autocomplete="email" required value="<?= h($_POST['email'] ?? '') ?>"></label>
		<label>Contrasena<input type="password" name="password" minlength="8" autocomplete="new-password" required></label>
		<button class="button" type="submit">Registrarme</button>
	</form>
	<p><a href="<?= url('/login') ?>">Ya tengo cuenta</a> · <a href="<?= url('/reenviar-verificacion') ?>">Reenviar verificacion</a></p>
</section>
<?php require __DIR__ . '/../footer.php'; ?>
