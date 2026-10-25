<?php

require_once __DIR__ . '/../vendor/phpmailer/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/SMTP.php';

require_once __DIR__ . '/datos.php';

function enviarCorreoTransaccional($destinatario, $nombre, $asunto, $html, $texto)
{
    global $smtpHost, $smtpPort, $smtpUsername, $smtpPassword, $smtpFrom, $smtpFromName;

    if (!$smtpHost || !$smtpUsername || !$smtpPassword || !$smtpFrom) {
        error_log('Completa la configuracion SMTP en config/datos.php.');
        return false;
    }

    $correo = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $correo->isSMTP();
        $correo->Host = $smtpHost;
        $correo->SMTPAuth = true;
        $correo->Username = $smtpUsername;
        $correo->Password = $smtpPassword;
        $correo->Port = (int) $smtpPort;
        $correo->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $correo->CharSet = 'UTF-8';
        $correo->setFrom($smtpFrom, $smtpFromName);
        $correo->addAddress($destinatario, $nombre);
        $correo->isHTML(true);
        $correo->Subject = $asunto;
        $correo->Body = $html;
        $correo->AltBody = $texto;
        $correo->send();

        return true;
    } catch (PHPMailer\PHPMailer\Exception $error) {
        error_log('No se pudo enviar el correo transaccional.');
        return false;
    }
}

function enviarCorreoRecuperacion($destinatario, $nombre, $enlace)
{
    $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    $enlaceSeguro = htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8');
    $html = '<p>Hola ' . $nombreSeguro . ',</p>'
        . '<p>Usa el siguiente enlace para elegir una nueva contrasena. Es valido durante una hora.</p>'
        . '<p><a href="' . $enlaceSeguro . '">Restablecer contrasena</a></p>'
        . '<p>Si no solicitaste este cambio, puedes ignorar este correo.</p>';

    return enviarCorreoTransaccional(
        $destinatario,
        $nombre,
        'Restablecer contrasena',
        $html,
        "Hola {$nombre}, usa este enlace para elegir una nueva contrasena (valido durante una hora):\n\n{$enlace}"
    );
}

function enviarCorreoVerificacion($destinatario, $nombre, $enlace)
{
    $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    $enlaceSeguro = htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8');
    $html = '<div style="max-width:560px;margin:24px auto;padding:32px;border:1px solid #eadfd2;'
        . 'border-top:5px solid #d94f4f;font-family:Arial,sans-serif;color:#3d342d">'
        . '<p style="margin:0 0 8px;color:#9c312d;font-size:12px;font-weight:bold;letter-spacing:1px">DON DIEGO PANADERIA</p>'
        . '<h1 style="margin:0 0 20px;font-size:24px">Confirma tu correo</h1>'
        . '<p>Hola ' . $nombreSeguro . ', gracias por registrarte. Confirma tu direccion de correo para activar tu cuenta.</p>'
        . '<p style="margin:28px 0"><a href="' . $enlaceSeguro . '" style="display:inline-block;padding:14px 22px;'
        . 'background:#d94f4f;color:#fff;text-decoration:none;font-weight:bold;border-radius:4px">Verificar mi cuenta</a></p>'
        . '<p style="font-size:13px">El enlace vence en 24 horas. Si no creaste esta cuenta, ignora este correo.</p>'
        . '<p style="font-size:13px">Si el boton no funciona, copia este enlace en tu navegador:<br>'
        . '<a href="' . $enlaceSeguro . '" style="color:#9c312d;word-break:break-all">' . $enlaceSeguro . '</a></p>'
        . '</div>';
    $texto = "Hola {$nombre}, confirma tu correo para activar tu cuenta Don Diego. El enlace vence en 24 horas:\n\n{$enlace}\n\nSi no creaste esta cuenta, ignora este correo.";

    return enviarCorreoTransaccional(
        $destinatario,
        $nombre,
        'Verifica tu correo | Don Diego Panaderia',
        $html,
        $texto
    );
}
