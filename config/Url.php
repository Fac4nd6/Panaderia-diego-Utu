<?php

// Convierte rutas internas conocidas a páginas del router PHP.
if (!function_exists('url')) {
    function url($path = '')
    {
        $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
        $projectRoot = realpath(__DIR__ . '/..');
        $basePath = '';

        if ($documentRoot && $projectRoot && strpos($projectRoot, $documentRoot) === 0) {
            $basePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($projectRoot, strlen($documentRoot)));
        }

        $parts = parse_url($path);
        $route = '/' . trim($parts['path'] ?? '', '/');
        $routes = [
            '/' => 'productos',
            '/productos' => 'productos',
            '/pedidos/crear' => 'pedido-crear',
            '/pedidos' => 'pedido',
            '/login' => 'login',
            '/registro' => 'registro',
            '/verificar-correo' => 'verificar-correo',
            '/reenviar-verificacion' => 'reenviar-verificacion',
            '/recuperar-contrasena' => 'recuperar',
            '/restablecer-contrasena' => 'restablecer',
            '/logout' => 'logout',
            '/admin/pedidos' => 'admin-pedidos',
            '/admin/pedidos/estado' => 'admin-pedido-estado',
            '/admin/estadisticas' => 'admin-estadisticas',
            '/admin/productos' => 'admin-productos',
            '/admin/productos/nuevo' => 'producto-nuevo',
            '/admin/productos/editar' => 'producto-editar',
            '/admin/productos/guardar' => 'producto-guardar',
            '/admin/productos/eliminar' => 'producto-eliminar'
        ];

        $base = rtrim($basePath, '/');
        if (isset($routes[$route])) {
            $query = 'page=' . rawurlencode($routes[$route]);
            if (!empty($parts['query'])) {
                $query .= '&' . $parts['query'];
            }
            return $base . '/index.php?' . $query;
        }

        // Las rutas fuera del mapa son archivos estáticos, como CSS e imágenes.
        $url = $base . ($route === '/' ? '/' : $route);
        return $url . (empty($parts['query']) ? '' : '?' . $parts['query']);
    }
}

if (!function_exists('url_absoluta')) {
    function url_absoluta($path = '')
    {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $protocolo . '://' . $host . url($path);
    }
}
