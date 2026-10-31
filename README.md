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
- cURL para enviar correos por la API transaccional de Brevo.
- XAMPP para ejecutar Apache y MySQL localmente.

## Requisitos

- XAMPP con Apache, MySQL y PHP 8.1 o posterior.
- Extensiones PHP `mysqli` y `fileinfo` habilitadas.
- Extension PHP `curl` habilitada para el envio de correo.
- Un navegador web.
- Una cuenta de Brevo solo si se quieren probar los correos.

## Instalacion en XAMPP

1. Copia la carpeta del proyecto dentro de la carpeta `htdocs` de XAMPP y conserva el nombre `DonDiego-Panaderia-Final-simple`.
2. Abre el panel de XAMPP e inicia Apache y MySQL.
3. Entra a phpMyAdmin desde `http://localhost/phpmyadmin`.
4. En phpMyAdmin, abre la pestaña **Importar**, selecciona `database/don_diego.sql` y ejecútalo. El archivo crea la base `don_diego_simple`, sus tablas y productos iniciales.
5. Revisa `config/Database.php`. La configuracion inicial usa el servidor `localhost`, el usuario `root`, contraseña vacia y la base `don_diego_simple`. Cambia usuario o contraseña si tu instalacion de MySQL usa otros datos.

## Configuracion del correo

La configuracion actual del envio de correos esta implementada directamente en `config/Mailer.php`. El proyecto usa Brevo y realiza una llamada HTTPS al endpoint `https://api.brevo.com/v3/smtp/email` con la clave API y el remitente definidos dentro del archivo:

```php
$apiKey = 'TU_CLAVE_API_DE_BREVO';
$remitente = 'tu-correo-verificado@dominio.com';
$nombreRemitente = 'Don Diego Panaderia';
```

En este momento no se leen variables de entorno de Apache ni de `.env`; por eso debes editar `config/Mailer.php` para cambiar la clave y el correo remitente. La clave debe ser la API key completa de Brevo, no la clave SMTP ni la contraseña de la cuenta. El correo remitente debe ser una direccion verificada en Brevo para que el envio funcione correctamente. No compartas ni publiques esa informacion dentro del repositorio.

Si quieres probar el servicio sin pasar por el registro, reemplaza el destinatario por una direccion que controles y ejecuta:

```sh
php -r 'require "config/Mailer.php"; $ok = enviarCorreoTransaccional("DESTINATARIO_DE_PRUEBA", "Prueba", "Prueba de correo", "<p>Prueba independiente</p>", "Prueba independiente"); echo $ok ? "Aceptado por Brevo\n" : "Fallo; revisa el log de PHP\n"; exit($ok ? 0 : 1);'
```

Un resultado exitoso requiere una respuesta 2xx con `messageId`, que confirma que Brevo acepto el mensaje para procesarlo; no garantiza que llegue a la bandeja de entrada. Si falla, el log indica si fue por configuracion, conexion, autenticacion, limites o rechazo de la API, sin imprimir la clave. Despues puedes probar registro/verificacion, reenvio y recuperacion desde la aplicacion. El restablecimiento de contrasena consume el enlace enviado durante la recuperacion; no genera un correo adicional.

## Ejecucion y comprobacion

El rol de una cuenta nueva es `cliente`. Para habilitar las pantallas de administracion, cambia su rol desde phpMyAdmin usando el correo con el que te registraste:

```sql
UPDATE usuarios SET rol = 'admin' WHERE email = 'TU_CORREO';
```

## Autores

Los nombres de los integrantes no estan documentados en los archivos del proyecto.
