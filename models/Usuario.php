<?php

class Usuario
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Busca la cuenta por correo y verifica su contraseña cifrada.
    public function iniciarSesion($email, $password)
    {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, password, rol, email_verificado
             FROM usuarios
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $usuario && password_verify($password, $usuario['password'])
            ? $usuario
            : null;
    }

    public function registrar($nombre, $email, $password, $tokenVerificacion)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (
                nombre, email, password, rol, email_verificado,
                token_verificacion, token_verificacion_expira, ultimo_reenvio_verificacion
               ) VALUES (?, ?, ?, 'cliente', 0, ?, DATE_ADD(NOW(), INTERVAL 1 DAY), NOW())"
        );
        $stmt->bind_param('ssss', $nombre, $email, $hash, $tokenVerificacion);

        // La dirección es única; un correo duplicado devuelve false al controlador.
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $usuario = [
            'id' => $stmt->insert_id,
            'nombre' => $nombre
        ];
        $stmt->close();

        return $usuario;
    }

    // Consultas y cambios del proceso de recuperación de contraseña.
    public function buscarPorEmail($email)
    {
        $stmt = $this->db->prepare(
            'SELECT id, nombre
             FROM usuarios
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $usuario;
    }

    public function guardarTokenRecuperacion($id, $tokenHash, $expira)
    {
        $stmt = $this->db->prepare(
            'UPDATE usuarios
             SET token_recuperacion = ?, token_expira = ?
             WHERE id = ?'
        );
        $stmt->bind_param('ssi', $tokenHash, $expira, $id);
        $guardado = $stmt->execute();
        $stmt->close();

        return $guardado;
    }

    public function buscarTokenRecuperacion($tokenHash)
    {
        $stmt = $this->db->prepare(
            'SELECT id
             FROM usuarios
             WHERE token_recuperacion = ? AND token_expira > NOW()
             LIMIT 1'
        );
        $stmt->bind_param('s', $tokenHash);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $usuario;
    }

    public function actualizarPassword($id, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            'UPDATE usuarios
             SET password = ?, token_recuperacion = NULL, token_expira = NULL
             WHERE id = ?'
        );
        $stmt->bind_param('si', $hash, $id);
        $actualizado = $stmt->execute();
        $stmt->close();

        return $actualizado;
    }

    public function buscarPendienteVerificacion($email)
    {
        $stmt = $this->db->prepare(
            'SELECT id, nombre, email
             FROM usuarios
             WHERE email = ? AND email_verificado = 0
             LIMIT 1'
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $usuario;
    }

    public function actualizarTokenVerificacion($id, $tokenHash)
    {
        $stmt = $this->db->prepare(
            'UPDATE usuarios
             SET token_verificacion = ?, token_verificacion_expira = DATE_ADD(NOW(), INTERVAL 1 DAY),
                 ultimo_reenvio_verificacion = NOW()
             WHERE id = ? AND email_verificado = 0
               AND (ultimo_reenvio_verificacion IS NULL
                    OR ultimo_reenvio_verificacion <= DATE_SUB(NOW(), INTERVAL 1 MINUTE))'
        );
        $stmt->bind_param('si', $tokenHash, $id);
        $actualizado = $stmt->execute() && $stmt->affected_rows === 1;
        $stmt->close();

        return $actualizado;
    }

    public function buscarTokenVerificacion($tokenHash)
    {
        $stmt = $this->db->prepare(
            'SELECT id
             FROM usuarios
             WHERE token_verificacion = ? AND token_verificacion_expira > NOW()
               AND email_verificado = 0
             LIMIT 1'
        );
        $stmt->bind_param('s', $tokenHash);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $usuario;
    }

    public function verificarCorreo($tokenHash)
    {
        $stmt = $this->db->prepare(
            'UPDATE usuarios
             SET email_verificado = 1, token_verificacion = NULL,
                 token_verificacion_expira = NULL
             WHERE token_verificacion = ? AND token_verificacion_expira > NOW()
               AND email_verificado = 0'
        );
        $stmt->bind_param('s', $tokenHash);
        $verificado = $stmt->execute() && $stmt->affected_rows === 1;
        $stmt->close();

        return $verificado;
    }
}
