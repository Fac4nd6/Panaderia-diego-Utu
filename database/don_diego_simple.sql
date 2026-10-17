-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 17-10-2026 a las 06:51:19
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `don_diego_simple`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre_cliente` varchar(150) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `fecha_retiro` date NOT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','en_preparacion','listo','entregado') NOT NULL DEFAULT 'pendiente',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `nombre_cliente`, `telefono`, `fecha_retiro`, `fecha_pedido`, `estado`, `total`) VALUES
(1, 'Facundo', '092155738', '2026-10-15', '2026-10-01 12:11:13', 'pendiente', 1200.00),
(2, 'Facundo', '092155738', '2026-10-18', '2026-10-01 12:11:37', 'pendiente', 3600.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalles`
--

CREATE TABLE `pedido_detalles` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `producto_id` int(10) UNSIGNED NOT NULL,
  `nombre_producto` varchar(150) NOT NULL,
  `cantidad` smallint(5) UNSIGNED NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `pedido_detalles`
--

INSERT INTO `pedido_detalles` (`id`, `pedido_id`, `producto_id`, `nombre_producto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, 'Alfajores', 10, 120.00, 1200.00),
(2, 2, 1, 'Alfajores', 30, 120.00, 3600.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `imagen` varchar(255) NOT NULL DEFAULT 'alfajores.avif',
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `activo`) VALUES
(1, 'Alfajores', 'Alfajores artesanales.', 120.00, 60, 'alfajores.avif', 1),
(2, 'Empanadas', 'Empanadas caseras.', 130.00, 100, 'empanadas.avif', 1),
(3, 'Brownie', 'Brownie artesanal de chocolate.', 180.00, 100, 'brownie.avif', 1),
(4, 'Muffins', 'Muffins artesanales.', 150.00, 100, 'muffins.avif', 1),
(5, 'Torta de chocolate', 'Torta artesanal de chocolate.', 950.00, 20, 'torta-chocolate.avif', 1),
(6, 'Sandwiches', 'Sandwiches preparados artesanalmente.', 180.00, 100, 'sandwiches.avif', 1),
(7, 'Postre', 'Postre artesanal.', 450.00, 30, 'postre.avif', 1),
(8, 'Arrollado', 'Arrollado artesanal.', 650.00, 20, 'arrollado.avif', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('cliente','empleado','admin') NOT NULL DEFAULT 'cliente',
  `token_recuperacion` char(64) DEFAULT NULL,
  `token_expira` datetime DEFAULT NULL,
  `email_verificado` tinyint(1) NOT NULL DEFAULT 1,
  `token_verificacion` char(64) DEFAULT NULL,
  `token_verificacion_expira` datetime DEFAULT NULL,
  `ultimo_reenvio_verificacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `token_recuperacion`, `token_expira`, `email_verificado`, `token_verificacion`, `token_verificacion_expira`, `ultimo_reenvio_verificacion`) VALUES
(1, 'Facundo Leites', 'fac4nd6@gmail.com', '$2y$10$kHAuJaIWbHiW6Y9NczC7cuBRfhduWEuHaSPNhhc0ay/rNNo9SCI0q', 'cliente', NULL, NULL, 1, NULL, NULL, NULL),
(2, 'Administrador Demo', 'admin.demo@dondiego.test', '$2y$10$n11f4Y6YAYsqOw/LRex9UeUb1g./D0/DfmxRex7O5FuVXG.Uy0lqO', 'admin', NULL, NULL, 1, NULL, NULL, NULL),
(3, 'Cliente Demo', 'cliente.demo@dondiego.test', '$2y$10$HCADPA8ZA/WuYEtya5NbVOVWmOLfTCf2SP48RWWkYElSSIL4T04eC', 'cliente', NULL, NULL, 1, NULL, NULL, NULL),
(4, 'Empleado Demo', 'empleado.demo@dondiego.test', '$2y$10$qKBuI0yrjpDc4tm4Lnxrq..paCQ2S9V5ySzAyG9s00BpxsXjQ.uI2', 'empleado', NULL, NULL, 1, NULL, NULL, NULL),
(7, 'Facundo Leites', 'facu@gmail.com', '$2y$10$zamti94kWftDznYKUpJONOav0qGWw2FwkPvfKAvrdKK9OKozNTTXW', 'cliente', NULL, NULL, 0, '5ea50df971b4ed1816eb78e68480429f3d9621c9aeedefaa22e4c9fcfa35897f', '2026-10-17 14:39:27', '2026-10-16 14:39:27');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detalle_pedido` (`pedido_id`),
  ADD KEY `idx_detalle_producto` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedido_detalles`
--
ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
