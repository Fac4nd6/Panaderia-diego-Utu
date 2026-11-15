<?php

class Producto
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Consultas usadas por el catálogo, los pedidos y la administración.
    public function obtenerActivos()
    {
        $resultado = $this->db->query(
            'SELECT id, nombre, descripcion, precio, stock, imagen
             FROM productos
             WHERE activo = 1
             ORDER BY nombre'
        );

        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerTodos()
    {
        $resultado = $this->db->query(
            'SELECT id, nombre, descripcion, precio, stock, imagen, activo
             FROM productos
             ORDER BY nombre'
        );

        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, descripcion, precio, stock, imagen, activo
             FROM productos
             WHERE id = ?'
        );
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $producto = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $producto;
    }

    // Un ID actualiza un registro; cero crea uno nuevo.
    public function guardar($id, $nombre, $descripcion, $precio, $stock, $imagen)
    {
        if ($id > 0) {
            $stmt = $this->db->prepare(
                'UPDATE productos
                 SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ?
                 WHERE id = ?'
            );
            $stmt->bind_param('ssdisi', $nombre, $descripcion, $precio, $stock, $imagen, $id);
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO productos (nombre, descripcion, precio, stock, imagen, activo)
                 VALUES (?, ?, ?, ?, ?, 1)'
            );
            $stmt->bind_param('ssdis', $nombre, $descripcion, $precio, $stock, $imagen);
        }

        $guardado = $stmt->execute();
        $stmt->close();

        return $guardado;
    }

    public function cambiarEstado($id, $activo)
    {
        if ($id < 1 || !in_array($activo, [0, 1], true)) {
            return false;
        }

        $stmt = $this->db->prepare('UPDATE productos SET activo = ? WHERE id = ?');
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('ii', $activo, $id);
        $actualizado = $stmt->execute();
        $stmt->close();

        return $actualizado;
    }

    public function eliminarDefinitivamente($id)
    {
        if ($id < 1) {
            return false;
        }

        $this->db->begin_transaction();
        try {
            $consulta = $this->db->prepare('SELECT imagen FROM productos WHERE id = ? FOR UPDATE');
            if (!$consulta) {
                throw new RuntimeException('No se pudo consultar el producto.');
            }
            $consulta->bind_param('i', $id);
            if (!$consulta->execute()) {
                $consulta->close();
                throw new RuntimeException('No se pudo consultar el producto.');
            }
            $producto = $consulta->get_result()->fetch_assoc();
            $consulta->close();

            if (!$producto) {
                $this->db->rollback();
                return false;
            }

            $borrar = $this->db->prepare('DELETE FROM productos WHERE id = ?');
            if (!$borrar) {
                throw new RuntimeException('No se pudo preparar el borrado.');
            }
            $borrar->bind_param('i', $id);
            if (!$borrar->execute()) {
                $borrar->close();
                throw new RuntimeException('No se pudo borrar el producto.');
            }
            $borrar->close();

            if (!$this->db->commit()) {
                throw new RuntimeException('No se pudo confirmar el borrado.');
            }

            return $producto['imagen'];
        } catch (Throwable $error) {
            $this->db->rollback();
            return false;
        }
    }

    public function imagenEnUso($imagen)
    {
        $stmt = $this->db->prepare('SELECT id FROM productos WHERE imagen = ? LIMIT 1');
        if (!$stmt) {
            return true;
        }

        $stmt->bind_param('s', $imagen);
        if (!$stmt->execute()) {
            $stmt->close();
            return true;
        }
        $enUso = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $enUso;
    }
}
