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
- API transaccional de Brevo mediante HTTPS y cURL.
- XAMPP para ejecutar Apache y MySQL localmente.

## Requisitos

- XAMPP con Apache, MySQL y PHP 8.1 o posterior.
- Extensiones PHP `mysqli`, `fileinfo` y `curl` habilitadas.
- Un navegador web.
- Una cuenta de Brevo y un remitente validado para probar los correos.

## Instalacion en XAMPP

1. Copia la carpeta del proyecto dentro de la carpeta `htdocs` de XAMPP y conserva el nombre `DonDiego-Panaderia-Final-simple`.
2. Abre el panel de XAMPP e inicia Apache y MySQL.
3. Entra a phpMyAdmin desde `http://localhost/phpmyadmin`.
4. En phpMyAdmin, abre la pestaña **Importar**, selecciona `database/don_diego.sql` y ejecútalo. El archivo crea la base `don_diego_simple`, sus tablas y productos iniciales.
5. Revisa `config/Database.php`. La configuracion inicial usa el servidor `localhost`, el usuario `root`, contraseña vacia y la base `don_diego_simple`. Cambia usuario o contraseña si tu instalacion de MySQL usa otros datos.

## Configuracion del correo

Configura estas variables de entorno en el proceso que ejecuta Apache/PHP. La clave API y la dirección remitente no deben guardarse en el código fuente.

```sh
export BREVO_API_KEY=TU_API_KEY
export BREVO_SENDER_EMAIL=TU_CORREO_REMITENTE_VALIDADO
export BREVO_SENDER_NAME="Don Diego Panaderia"
```

La API Key se envía en el encabezado HTTPS `api-key` a la API transaccional de Brevo. Asegurate de que las variables esten disponibles para Apache, no solo para una terminal.

## Ejecucion y comprobacion

Abre `http://localhost/DonDiego-Panaderia-Final-simple/`. Debe mostrarse el catalogo. Prueba crear un pedido y luego registra una cuenta para comprobar el inicio de sesion. Para probar la verificacion y recuperacion de cuenta, configura primero la API de Brevo y revisa el correo recibido.

El rol de una cuenta nueva es `cliente`. Para habilitar las pantallas de administracion, cambia su rol desde phpMyAdmin usando el correo con el que te registraste:

```sql
UPDATE usuarios SET rol = 'admin' WHERE email = 'TU_CORREO';
```

## Autores

Facundo Leites, Thiago Cano, Constanza Ortiz, Matias Hernandez
