<?php
$tokenValido = $tokenValido ?? false;
$token = $token ?? '';
$verificacionCompletada = $verificacionCompletada ?? false;
$mensaje = $mensaje ?? '';
$error = $error ?? '';
$titulo = 'Verificar correo';
require __DIR__ . '/../header.php';
?>
<section class="auth">
	<h1>Verificar correo</h1>
	<?php if ($verificacionCompletada): ?>
		<p class="notice"><?= h($mensaje) ?></p>
		<p><a href="<?= url('/login') ?>">Ingresar</a></p>
	<?php elseif ($tokenValido): ?>
		<p>Confirma tu direccion de correo para activar tu cuenta.</p>
		<form class="stack" method="post" action="<?= url('/verificar-correo') ?>">
			<input type="hidden" name="token" value="<?= h($token) ?>">
			<button class="button" type="submit">Verificar mi cuenta</button>
		</form>
	<?php else: ?>
		<p class="notice error"><?= h($error) ?></p>
		<p><a href="<?= url('/reenviar-verificacion') ?>">Solicitar otro enlace</a></p>
	<?php endif; ?>
</section>
<?php require __DIR__ . '/../footer.php'; ?>