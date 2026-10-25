<?php

require_once __DIR__ . '/../config/Session.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Url.php';
require_once __DIR__ . '/../models/Usuario.php';

iniciar_sesion_segura();
$usuarioModel = new Usuario($conn);
$accion = $_GET['accion'] ?? 'login';
$error = '';
$mensaje = '';
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$csrfReenvio = $_SESSION['csrf_reenvio'] ??= bin2hex(random_bytes(32));
$verificacionCompletada = false;

// Cerrar sesión y volver al inicio de sesión.
if ($accion === 'logout') {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . url('/login'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'login') {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $usuario = $usuarioModel->iniciarSesion($email, $password);

        if ($usuario && !(int) $usuario['email_verificado']) {
            $error = 'Debes verificar tu correo antes de ingresar. Puedes solicitar otro enlace de verificacion.';
        } elseif ($usuario) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = (int) $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['rol'];

            $destino = in_array($usuario['rol'], ['admin', 'empleado'], true)
                ? '/admin/pedidos'
                : '/productos';
            header('Location: ' . url($destino));
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos.';
        }
    }

    if ($accion === 'register') {
        $nombre = trim($_POST['nombre'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (
            $nombre === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($password) < 8 ||
            strlen($password) > 72
        ) {
            $error = 'Ingresa un nombre, un correo valido y una contrasena de 8 a 72 caracteres.';
        } else {
            $tokenVerificacion = bin2hex(random_bytes(32));
            $usuario = $usuarioModel->registrar(
                $nombre,
                $email,
                $password,
                hash('sha256', $tokenVerificacion)
            );

            if ($usuario) {
                require_once __DIR__ . '/../config/Mailer.php';
                $enlace = url_absoluta(
                    '/verificar-correo?token=' . rawurlencode($tokenVerificacion)
                );
                $enviado = enviarCorreoVerificacion($email, $nombre, $enlace);
                $mensaje = $enviado
                    ? 'Tu cuenta fue creada. Revisa tu correo para verificar la direccion y poder ingresar.'
                    : 'Tu cuenta fue creada, pero no pudimos enviar el correo. Revisa la configuracion SMTP o solicita otro enlace.';
            } else {
                $error = 'Ese correo ya esta registrado o no se pudo crear la cuenta.';
            }
        }
    }

    if ($accion === 'reenviar') {
        $csrfEnviado = $_POST['csrf_token'] ?? '';
        $csrfValido = is_string($csrfEnviado)
            && hash_equals($_SESSION['csrf_reenvio'], $csrfEnviado);
        $_SESSION['csrf_reenvio'] = bin2hex(random_bytes(32));
        $csrfReenvio = $_SESSION['csrf_reenvio'];

        if ($csrfValido) {
            $email = strtolower(trim($_POST['email'] ?? ''));
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $pendiente = $usuarioModel->buscarPendienteVerificacion($email);

                if ($pendiente) {
                    $tokenVerificacion = bin2hex(random_bytes(32));
                    $expira = date('Y-m-d H:i:s', time() + 86400);
                    if ($usuarioModel->actualizarTokenVerificacion(
                        (int) $pendiente['id'],
                        hash('sha256', $tokenVerificacion)
                    )) {
                        require_once __DIR__ . '/../config/Mailer.php';
                        $enlace = url_absoluta(
                            '/verificar-correo?token=' . rawurlencode($tokenVerificacion)
                        );
                        enviarCorreoVerificacion($email, $pendiente['nombre'], $enlace);
                    }
                }
            }
        } else {
            $error = 'La solicitud vencio. Recarga la pagina e intenta nuevamente.';
        }

        $mensaje = 'Si la direccion corresponde a una cuenta pendiente, recibiras un enlace si se puede enviar ahora. Revisa tu correo en un minuto.';
    }

    if ($accion === 'verificar') {
        $verificacionCompletada = preg_match('/\A[a-f0-9]{64}\z/i', $token)
            && $usuarioModel->verificarCorreo(hash('sha256', $token));

        if ($verificacionCompletada) {
            $mensaje = 'Correo verificado correctamente. Ya puedes ingresar.';
        } else {
            $error = 'El enlace no es valido, vencio o ya fue utilizado. Solicita uno nuevo.';
        }
    }

    if ($accion === 'recuperar') {
        $email = strtolower(trim($_POST['email'] ?? ''));

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $usuario = $usuarioModel->buscarPorEmail($email);

            if ($usuario) {
                require_once __DIR__ . '/../config/Mailer.php';

                // El enlace contiene el token original; la base guarda solo su hash.
                $token = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $token);
                $expira = date('Y-m-d H:i:s', time() + 3600);
                $tokenGuardado = $usuarioModel->guardarTokenRecuperacion(
                    (int) $usuario['id'],
                    $tokenHash,
                    $expira
                );

                if ($tokenGuardado) {
                    $enlace = url_absoluta(
                        '/restablecer-contrasena?token=' . rawurlencode($token)
                    );
                    enviarCorreoRecuperacion($email, $usuario['nombre'], $enlace);
                }
            }
        }

        // El mismo mensaje evita revelar si una dirección está registrada.
        $mensaje = 'Si el correo esta registrado, recibiras un enlace para restablecer la contrasena.';
    }

    if ($accion === 'restablecer') {
        $password = $_POST['password'] ?? '';

        if (strlen($password) < 8 || strlen($password) > 72) {
            $error = 'La contrasena debe tener entre 8 y 72 caracteres.';
        } else {
            $usuario = $usuarioModel->buscarTokenRecuperacion(hash('sha256', $token));
            if ($usuario && $usuarioModel->actualizarPassword((int) $usuario['id'], $password)) {
                header('Location: ' . url('/login?restablecida=1'));
                exit;
            }

            $error = 'El enlace no es valido o ya vencio. Solicita uno nuevo.';
        }
    }
}

$tokenValido = $accion === 'restablecer' && $token !== ''
    ? $usuarioModel->buscarTokenRecuperacion(hash('sha256', $token)) !== null
    : false;

if ($accion === 'verificar' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $tokenValido = preg_match('/\A[a-f0-9]{64}\z/i', $token)
        && $usuarioModel->buscarTokenVerificacion(hash('sha256', $token)) !== null;
    if (!$tokenValido) {
        $error = 'El enlace no es valido, vencio o ya fue utilizado. Solicita uno nuevo.';
    }
}

$vistas = [
    'login' => 'login',
    'register' => 'register',
    'verificar' => 'verificar',
    'reenviar' => 'reenviar',
    'recuperar' => 'recuperar',
    'restablecer' => 'restablecer'
];

if (!isset($vistas[$accion])) {
    http_response_code(404);
    exit('Pagina no encontrada.');
}

require __DIR__ . '/../views/auth/' . $vistas[$accion] . '.php';
