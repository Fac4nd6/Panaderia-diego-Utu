<?php

function enviarCorreoTransaccional($destinatario, $nombre, $asunto, $html, $texto)
{
   // CONFIGURACIÓN DE BREVO
$apiKey = 'api';
$remitente = 'fac4nd6@gmail.com';
$nombreRemitente = 'Don Diego Panaderia';

    if ($apiKey === '' || !filter_var($remitente, FILTER_VALIDATE_EMAIL)) {
        error_log('Configuracion de la API de Brevo incompleta o invalida.');
        return false;
    }

    if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        error_log('Destinatario invalido; no se envio el correo transaccional.');
        return false;
    }

    $payload = json_encode([
        'sender' => [
            'name' => $nombreRemitente,
            'email' => $remitente,
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
        error_log('No se pudo preparar el correo para la API de Brevo.');
        return false;
    }

    if (!function_exists('curl_init')) {
        error_log('No se puede enviar correo: la extension PHP cURL no esta habilitada.');
        return false;
    }

    $curl = curl_init('https://api.brevo.com/v3/smtp/email');
    if ($curl === false) {
        error_log('No se pudo inicializar cURL para la API de Brevo.');
        return false;
    }

    $opcionesConfiguradas = curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'api-key: ' . $apiKey,
            'content-type: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
    ]);
    if (!$opcionesConfiguradas) {
        curl_close($curl);
        error_log('No se pudo configurar la solicitud HTTP para la API de Brevo.');
        return false;
    }

    $respuesta = curl_exec($curl);
    $estadoHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $errorCurl = curl_error($curl);
    curl_close($curl);

    if ($respuesta === false) {
        $detalle = str_replace($apiKey, '[redactado]', $errorCurl);
        $detalle = preg_replace('/[\r\n]+/', ' ', $detalle);
        error_log('Fallo de conexion con la API de Brevo: ' . $detalle);
        return false;
    }

    $datosRespuesta = json_decode($respuesta, true);
    if ($estadoHttp < 200 || $estadoHttp >= 300) {
        $codigoApi = is_array($datosRespuesta) && is_string($datosRespuesta['code'] ?? null)
            ? $datosRespuesta['code']
            : '';
        $mensajeApi = is_array($datosRespuesta) && is_string($datosRespuesta['message'] ?? null)
            ? $datosRespuesta['message']
            : '';
        $detalle = trim(implode(' ', array_filter([$codigoApi, $mensajeApi])));
        $detalle = str_replace(array_filter([$apiKey, $remitente, $destinatario]), '[redactado]', $detalle);
        $detalle = substr(preg_replace('/[\r\n\t]+/', ' ', $detalle), 0, 500);
        error_log('La API de Brevo rechazo el correo (HTTP ' . $estadoHttp . '). ' . $detalle);
        return false;
    }

    $messageId = is_array($datosRespuesta) && is_string($datosRespuesta['messageId'] ?? null)
        ? trim($datosRespuesta['messageId'])
        : '';
    if ($messageId === '') {
        error_log('Brevo respondio HTTP ' . $estadoHttp . ' sin un messageId; no se confirma la aceptacion del correo.');
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
