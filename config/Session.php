<?php

function iniciar_sesion_segura()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    // La sesión mantiene al usuario autenticado y limita el acceso por rol.
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $https,
        'samesite' => 'Lax'
    ]);
    session_start();
}
