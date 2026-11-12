<?php

function enviarCorreoTransaccional($destinatario, $nombre, $asunto, $html, $texto)
{
    $apiKey = getenv('BREVO_API_KEY') ?: '';
    $senderEmail = getenv('BREVO_SENDER_EMAIL') ?: 'fac4nd6@gmail.com';
    $senderName = getenv('BREVO_SENDER_NAME') ?: 'Don Diego Panaderia';

    if (
        $apiKey === '' ||
        !filter_var($senderEmail, FILTER_VALIDATE_EMAIL) ||
        !filter_var($destinatario, FILTER_VALIDATE_EMAIL)
    ) {
        error_log('Configura BREVO_API_KEY y una dirección remitente válida de Brevo.');
        return false;
    }

    if (!function_exists('curl_init')) {
        error_log('La extensión cURL de PHP es necesaria para enviar correos con Brevo.');
        return false;
    }

    $payload = json_encode([
        'sender' => [
            'name' => $senderName,
            'email' => $senderEmail,
        ],
        'to' => [[
            'email' => $destinatario,
            'name' => $nombre,
        ]],
        'subject' => $asunto,
        'htmlContent' => $html,
        'textContent' => $texto,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($payload === false) {
        error_log('No se pudo preparar el correo transaccional para Brevo.');
        return false;
    }

    $curl = curl_init('https://api.brevo.com/v3/smtp/email');
    if ($curl === false) {
        error_log('No se pudo iniciar la solicitud a la API de Brevo.');
        return false;
    }

    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'api-key: ' . $apiKey,
            'content-type: application/json',
        ],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
    ]);

    $respuesta = curl_exec($curl);
    $codigoHttp = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);

    if ($respuesta === false || $codigoHttp < 200 || $codigoHttp >= 300) {
        error_log('No se pudo enviar el correo transaccional con la API de Brevo (HTTP ' . $codigoHttp . ').');
        return false;
    }

    return true;
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
