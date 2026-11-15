ALTER TABLE `pedido_detalles`
  DROP FOREIGN KEY `fk_detalle_producto`;

ALTER TABLE `pedido_detalles`
  MODIFY `producto_id` int(10) UNSIGNED DEFAULT NULL;

ALTER TABLE `pedido_detalles`
  ADD CONSTRAINT `fk_detalle_producto`
  FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
  ON DELETE SET NULL;