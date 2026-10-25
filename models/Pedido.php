<?php

class Pedido
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Guarda el pedido, sus detalles y el descuento de stock como una sola operación.
    public function crear($nombre, $telefono, $fecha, $cantidades)
    {
        $this->db->begin_transaction();

        try {
            $lineas = [];
            $total = 0.0;
            $consultaProducto = $this->db->prepare(
                'SELECT id, nombre, precio, stock
                 FROM productos
                 WHERE id = ? AND activo = 1
                 FOR UPDATE'
            );

            foreach ($cantidades as $id => $cantidad) {
                $id = (int) $id;
                $cantidad = filter_var($cantidad, FILTER_VALIDATE_INT);
                if ($id < 1 || $cantidad === false || $cantidad < 1 || $cantidad > 99) {
                    continue;
                }

                $consultaProducto->bind_param('i', $id);
                $consultaProducto->execute();
                $producto = $consultaProducto->get_result()->fetch_assoc();
                if (!$producto || (int) $producto['stock'] < $cantidad) {
                    $nombreProducto = $producto['nombre'] ?? 'un producto';
                    throw new RuntimeException('Stock insuficiente para ' . $nombreProducto);
                }

                $subtotal = (float) $producto['precio'] * $cantidad;
                $lineas[] = [
                    'id' => $id,
                    'nombre' => $producto['nombre'],
                    'cantidad' => $cantidad,
                    'precio' => (float) $producto['precio'],
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
            $consultaProducto->close();

            if (!$lineas) {
                throw new RuntimeException('Selecciona al menos un producto.');
            }

            $crearPedido = $this->db->prepare(
                "INSERT INTO pedidos (nombre_cliente, telefono, fecha_retiro, estado, total)
                 VALUES (?, ?, ?, 'pendiente', ?)"
            );
            $crearPedido->bind_param('sssd', $nombre, $telefono, $fecha, $total);
            $crearPedido->execute();
            $pedidoId = $crearPedido->insert_id;
            $crearPedido->close();

            $crearDetalle = $this->db->prepare(
                'INSERT INTO pedido_detalles
                 (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $descontarStock = $this->db->prepare(
                'UPDATE productos SET stock = stock - ? WHERE id = ?'
            );

            foreach ($lineas as $linea) {
                $crearDetalle->bind_param(
                    'iisidd',
                    $pedidoId,
                    $linea['id'],
                    $linea['nombre'],
                    $linea['cantidad'],
                    $linea['precio'],
                    $linea['subtotal']
                );
                $crearDetalle->execute();

                $descontarStock->bind_param('ii', $linea['cantidad'], $linea['id']);
                $descontarStock->execute();
            }

            $crearDetalle->close();
            $descontarStock->close();
            $this->db->commit();

            return ['id' => $pedidoId];
        } catch (Throwable $error) {
            $this->db->rollback();
            return ['error' => $error->getMessage()];
        }
    }

    // Devuelve pedidos y aplica los límites de fecha que haya elegido el personal.
    public function listar($desde = '', $hasta = '')
    {
        $sql = 'SELECT id, nombre_cliente, telefono, fecha_retiro, fecha_pedido, estado, total
                FROM pedidos WHERE 1 = 1';
        $tipos = '';
        $valores = [];

        if ($desde !== '') {
            $sql .= ' AND fecha_retiro >= ?';
            $tipos .= 's';
            $valores[] = $desde;
        }
        if ($hasta !== '') {
            $sql .= ' AND fecha_retiro <= ?';
            $tipos .= 's';
            $valores[] = $hasta;
        }

        $sql .= ' ORDER BY fecha_retiro, id DESC';
        $stmt = $this->db->prepare($sql);
        if ($tipos !== '') {
            $stmt->bind_param($tipos, ...$valores);
        }
        $stmt->execute();
        $pedidos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $pedidos;
    }

    // Los detalles conservan nombre y precio aunque cambie el catálogo después.
    public function obtener($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM pedidos WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $pedido = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$pedido) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT cantidad, precio_unitario, subtotal, nombre_producto AS nombre
             FROM pedido_detalles
             WHERE pedido_id = ?'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $pedido['detalles'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $pedido;
    }

    public function actualizarEstado($id, $estado)
    {
        $estados = ['pendiente', 'en_preparacion', 'listo', 'entregado'];
        if (!in_array($estado, $estados, true)) {
            return false;
        }

        $stmt = $this->db->prepare('UPDATE pedidos SET estado = ? WHERE id = ?');
        $stmt->bind_param('si', $estado, $id);
        $actualizado = $stmt->execute();
        $stmt->close();

        return $actualizado;
    }

    public function estadisticas()
    {
        $resultado = $this->db->query(
            "SELECT COUNT(*) AS total,
                    SUM(estado = 'pendiente') AS pendientes,
                    SUM(estado = 'entregado') AS entregados,
                    COALESCE(SUM(total), 0) AS ventas
             FROM pedidos"
        );

        return $resultado->fetch_assoc();
    }
}
