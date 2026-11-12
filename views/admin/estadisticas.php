<?php
$titulo = 'Estadisticas';
require __DIR__ . '/../header.php';
?>
<h1>Estadisticas</h1>

<div class="stats">
    <article>
        <span>Pedidos</span>
        <strong><?= (int) $estadisticas['total'] ?></strong>
    </article>
    <article>
        <span>Pendientes</span>
        <strong><?= (int) $estadisticas['pendientes'] ?></strong>
    </article>
    <article>
        <span>Entregados</span>
        <strong><?= (int) $estadisticas['entregados'] ?></strong>
    </article>
    <article>
        <span>Ventas registradas</span>
        <strong>$<?= number_format((float) $estadisticas['ventas'], 2, ',', '.') ?></strong>
    </article>
</div>

<?php require __DIR__ . '/../footer.php'; ?>
