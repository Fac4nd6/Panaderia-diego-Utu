# Don Diego Panaderia

## Descripcion

Don Diego Panaderia es un sitio web para consultar productos de panaderia y gestionar pedidos. El proyecto permite que el negocio mantenga su catalogo y organice la preparacion y entrega de los pedidos.

## Funcionalidades principales

- Catalogo publico con productos, precios e imagenes.
- Pedidos sin necesidad de crear una cuenta, con fecha de retiro y cantidades.
- Descuento de stock al crear un pedido.
- Registro, inicio de sesion, verificacion de correo y recuperacion de contrasena.
- Panel para consultar pedidos y actualizar sus estados.
- Administracion de productos e imagenes y consulta de estadisticas.
- Roles de cliente, empleado y administrador.

No incluye carrito de compras.

## Tecnologias

- PHP y MySQL/MariaDB.
- HTML y CSS.
- PHPMailer para enviar correos por SMTP de Brevo.
- XAMPP para ejecutar Apache y MySQL localmente.

## Requisitos

- XAMPP con Apache, MySQL y PHP 8.1 o posterior.
- Extensiones PHP `mysqli` y `fileinfo` habilitadas.
- Un navegador web.
- Una cuenta de Brevo solo si se quieren probar los correos.

## Instalacion en XAMPP

1. Copia la carpeta del proyecto dentro de la carpeta `htdocs` de XAMPP y conserva el nombre `DonDiego-Panaderia-Final-simple`.
2. Abre el panel de XAMPP e inicia Apache y MySQL.
3. Entra a phpMyAdmin desde `http://localhost/phpmyadmin`.
4. En phpMyAdmin, abre la pestaña **Importar**, selecciona `database/don_diego_simple.sql` y ejecútalo. El archivo crea la base `don_diego_simple`, sus tablas y productos iniciales.
5. Revisa `config/Database.php`. La configuracion inicial usa el servidor `localhost`, el usuario `root`, contraseña vacia y la base `don_diego_simple`. Cambia usuario o contraseña si tu instalacion de MySQL usa otros datos.

## Configuracion del correo

Completa `config/datos.php` con los datos SMTP de tu cuenta de Brevo. No compartas ese archivo ni publiques sus credenciales.

```php
<?php

$smtpHost = 'smtp-relay.brevo.com';
$smtpPort = 587;
$smtpUsername = 'TU_USUARIO_SMTP';
$smtpPassword = 'TU_CLAVE_SMTP';
$smtpFrom = 'TU_CORREO_REMITENTE';
$smtpFromName = 'Don Diego Panaderia';
```

Usa tu usuario y clave SMTP de Brevo, y un correo remitente habilitado en esa cuenta. El sitio utiliza SMTP; no es necesario instalar PHPMailer porque ya esta incluido en `vendor/phpmailer`.

## Ejecucion y comprobacion

Abre `http://localhost/DonDiego-Panaderia-Final-simple/`. Debe mostrarse el catalogo. Prueba crear un pedido y luego registra una cuenta para comprobar el inicio de sesion. Para probar la verificacion y recuperacion de cuenta, configura primero Brevo y revisa el correo recibido.

El rol de una cuenta nueva es `cliente`. Para habilitar las pantallas de administracion, cambia su rol desde phpMyAdmin usando el correo con el que te registraste:

```sql
UPDATE usuarios SET rol = 'admin' WHERE email = 'TU_CORREO';
```

## Autores

Los nombres de los integrantes son: Facundo Leites, Thiago Cano, Constanza Ortiz, Matias Hernandez
