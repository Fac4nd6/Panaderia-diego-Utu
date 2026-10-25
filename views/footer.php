	</main>
<footer class="site-footer">
	<div class="footer-inner">
		<div class="footer-about">
			<a class="footer-brand" href="<?= url('/productos') ?>">
				<img src="<?= url('/public/img/logo.avif') ?>" alt="">
				<span>Don Diego</span>
			</a>
			<p>Panaderia artesanal para compartir. Explora el catalogo y prepara tu pedido para retirar.</p>
		</div>
		<div class="footer-links">
			<h2>Explora</h2>
			<nav class="footer-nav" aria-label="Enlaces del pie de pagina">
				<a href="<?= url('/productos') ?>">Catalogo</a>
				<a href="<?= url('/pedidos/crear') ?>">Hacer pedido</a>
				<a href="<?= url('/login') ?>">Ingresar</a>
			</nav>
		</div>
		<div class="footer-bottom">
			<span>Panaderia Don Diego</span>
			<span>&copy; <?= date('Y') ?> Don Diego</span>
		</div>
	</div>
</footer>
</body>
</html>
