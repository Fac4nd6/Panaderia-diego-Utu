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

    public function eliminar($id)
    {
        $stmt = $this->db->prepare('DELETE FROM productos WHERE id = ?');
        $stmt->bind_param('i', $id);
        $eliminado = $stmt->execute();
        $stmt->close();

        return $eliminado;
    }
}
