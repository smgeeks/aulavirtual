=== RVCFDI para Woocommerce ===
Contributors: realvirtualmx,garizmendi
Tags: CFDI, Factura Electronica Mexico, Autofacturacion
Requires at least: 4.7.3
Tested up to: 6.2
Stable tag: trunk
License: GNU GENERAL PUBLIC LICENSE 3.0
License URI: https://www.gnu.org/licenses/gpl.html

El plugin RVCFDI para WooCommerce es una herramienta que se integra
con RV Factura Electronica Web y te permite llevar a cabo el proceso
facturacion electronica de tus ventas realizadas a tus clientes con
WooCommerce en tu sitio web.

== Description ==
El plugin RVCFDI para WooCommerce es una herramienta que se integra
con RV Factura Electronica Web y te permite llevar a cabo el proceso
facturacion electronica de tus ventas realizadas a tus clientes con
WooCommerce en tu sitio web, almacenando tus CFDI en RV Factura Electronica Web
con la posibilidad de administrarlos tambien en tu panel de
administracion del plugin en Wordpress.
Ventajas del plugin

1- Consulta de facturas emitidas

2- Cancelacion de facturas

3- Acuse de cancelacion

4- Descarga del PDF y XML

5- Autofacturacion para el cliente

6- Autocarga de datos de facturacion

7.- Factura global

8.- Reporte en excel

9.- Conexion opcional a un servicio externo de consulta de pedidos

== Installation ==
INSTALACION Y CONFIGURACION DEL PLUGIN
PASO 1
Haz clic en Plugins del menu izquierdo de tu Wordpress. Luego, haz clic en
Añadir nuevo y en la barra de busqueda escribe RVCFDI o rvcfdi. Por ultimo,
haz clic en Instalar ahora.
PASO 2
Activa el plugin haciendo clic en Activar.
PASO 3
Haz clic en RVCFDI del menu izquierdo de tu Wodrpress y se abrira el panel
de administracion del plugin. En la seccion Mi Cuenta ingresa tu RFC, Usuario
y Clave Cifrada obtenidos previamente desde el sistema de facturacion
RV Factura Electronica Web en la seccion RVCFDI para WooCommerce > Datos de acceso
ubicada en el menu izquierdo del mismo.
PASO 4
Ir a la seccion Configuracion para personalizar el plugin y establecer su
estilo visual y funcionamiento.
Al finalizar, pulsa el boton Guardar.
PASO 5
Haz que tus clientes puedan ver el modulo de facturacion electronica en tu
sitio web.
Para ello, crea una nueva pagina en Paginas > Añadir nueva y escribe el
shortcode [rvcfdi_woocommerce_formulario]. Por ultimo, guarda los
cambios.

PANEL DE ADMINISTRACION DEL PLUGIN EN WORDPRESS
En tu panel de administracion del plugin dispones de seis secciones:
1. Mi Cuenta.
En donde podras iniciar sesion con tu cuenta del sistema de facturacion
2. Configuracion.
En donde puedes configurar el plugin siempre que lo desees.
3. CFDI Emitidos.
En donde podras administrar tus CFDI emitidos con el plugin
utilizando las siguientes opciones:
- Descargar XML
- Descargar PDF
- Enviar
- Cancelar
- Acuse de cancelacion
- Descargar Reporte en Excel
- Filtrar

4. Factura Global.
En donde podras emitir una factura global de tus ventas no facturadas.
5. Centro de Integracion.
En donde podras conectar el plugin a tu servicio externo para consultar un pedido y llevar a cabo el proceso de emision de CFDI correspondiente en la vista del cliente en tu sitio web.
6. Soporte Tecnico.
En donde podras encontrar los recursos para brindarte la ayuda que
necesites.
7. Preguntas Frecuentes.
En donde podras conocer las respuestas a las preguntas mas comunes.

== Changelog ==
8.1.0 Mejoras internas en la emision de CFDI.
8.0.9 Mejoras internas en la emision de CFDI.
8.0.8 Mejoras internas en la emision de CFDI.
8.0.7 Mejoras internas en la emision de factura global.
8.0.6 Mejoras internas del funcionamiento del sistema.
8.0.5 Se agrego la compatibilidad del impuesto IVA Exento.
8.0.4 Se agrego una nueva zona horaria del tiempo del centro para los municipios del país que utilizan el horario de verano.
8.0.3 Se agrego la posibilidad de gestionar desde el panel de administracion del plugin los datos fiscales de los clientes que previamente ya hayan dado de alta esa informacion en sus cuentas de usuario.
8.0.2 Se agrego la posibilidad de configurar la informacion global por defecto para CFDI 4.0 de facturas a PUBLICO EN GENERAL con RFC genérico nacional.
8.0.1 Mejoras del funcionamiento del sistema.
8.0.0 Se agrego la compatibilidad para emitir CFDI 4.0. Se reorganizaron los modulos de sistema para un mejor manejo del plugin por parte del usuario.
7.0.14 Se actualizo el catalogo de regimen fiscal.
7.0.13 Se actualizo el catalogo de regimen fiscal.
7.0.12 Se implemento el nuevo esquema de cancelacion 2022 para CFDI.
7.0.11 Se agregaron mejoras generales para el funcionamiento interno del sistema. 
7.0.10 Se agregaron mejoras generales para el funcionamiento interno del sistema en el apartado de factura global.
7.0.9 Se agregaron mejoras generales para el funcionamiento interno del sistema. 
7.0.8 Se agrego la posibilidad de reconocer la leyenda TASA CERO como impuesto IVA para factura global.
7.0.7 Se agrego la posibilidad de cargar pedidos desde un archivo CSV para emitir factura global.
7.0.6 Se agregaron mejoras generales para el funcionamiento interno del sistema.
7.0.5 Se agregaron mejoras generales para el funcionamiento interno del sistema.
7.0.4 Se agregaron mejoras generales para el funcionamiento interno del sistema.
7.0.3 Se agregaron mejoras generales para el funcionamiento interno del sistema.
7.0.2 Se agregaron mejoras generales para el funcionamiento interno del sistema.
7.0.1 Se agrego la posibilidad de que el usuario pueda configurar que se muestre o no el domicilio del cliente en la facturacion. 
6.9.1 Se agrego la posibilidad de que el usuario pueda configurar que se pueda emitir el CFDI de pedidos cuyo estado sea Procesando o Completado.
6.9 Se corrigio un error interno del proceso de generacion de vista previa con la zona horaria establecida en la seccion Configuracion.
6.8 Se agrego la posibilidad de establecer una zona horaria en la seccion Configuracion para que la fecha de emision de los CFDI y Facturas Globales se establezca correctamente y corresponda al huso horario de la dirección fiscal del Emisor.
6.7 Se mejoro el funcionamiento interno de algunos procesos que el plugin ejecuta para su correcto rendimiento.
6.6 Se mejoro el proceso interno de calculo y verificacion cuando el plugin contempla impuestos incluidos en los precios de los articulos de un pedido. 
6.5 Se corrigio otro error interno cuando el plugin contempla impuestos incluidos en los precios de los articulos de un pedido. 
6.4 Se corrigio un error interno cuando el plugin contempla impuestos incluidos en los precios de los articulos de un pedido.
6.3 Se actualizaron las credenciales para el ambiente de pruebas.
6.2 Se agregaron opciones de configuracion para que el plugin recalcule todas las cantidades del pedido a partir del total del mismo (detalles en la seccion de Configuracion). Util en aquellos casos en que las cantidades del pedido no cuadran entre si.
6.1 Se corrigio un error en el funcionamiento interno del proceso de emision de CFDI con respecto a la fecha de emision que se toma del dispositivo que se este utilizando y evitar asi incompatibilidad de zonas horarias.
6.0 Se aplicaron mejoras internas para el funcionamiento del sistema.
5.9 Se agregaron opciones de configuracion para el comportamiento de los campos Uso CFDI, Forma De Pago y Metodo de Pago, asi como otros aspectos para el modulo de facturacion para clientes.
5.8 Se mejoro la lectura interna de impuestos para Factura Global para que en caso de que el impuesto tenga por nombre iva o ieps (minusculas), sean convertidos a IVA e IEPS (mayusculas).
5.7 Se aplicaron mejoras internas para el funcionamiento del sistema.
5.6 Se modifico el proceso interno de lectura de impuestos de un pedido para que la emision de CFDI soporte varios impuestos repetidos con diferentes tasas.
5.5 Se agrego la posibilidad de conectar nuestro plugin a un sevicio externo de consulta de pedidos que el usuario disponga para llevar a cabo la emision de CFDI correspondiente en la vista del cliente en tu sitio web.
5.4 Se optmizo la consulta de pedidos en la seccion Factura Global.
5.3 Se mejoraron detalles visuales generales.
5.2 Se mejoraron aspectos visuales en la seccion Configuracion y se agrego en ella una vista previa en tiempo real del estilo que el usuario define del modulo de facturacion para clientes.
5.1 Se agrego la posibilidad de descargar un reporte de CFDI Emitidos en un archivo de Excel.
5.0 Se agrego la posibilidad de emitir una factura global desde el panel de administracion del plugin.
4.1 Se mejoro el funcionamiento interno del proceso de emision de CFDI para que la fecha de emision se tome del dispositivo que se este utilizando y evitar asi incompatibilidad de zonas horarias.
4.0 Se corrigio un error interno que en algunos casos provocaba un error al activar el plugin.
3.9 Se aplicaron mejoras a los procesos que el plugin ejecuta internamente para su correcto funcionamiento.
3.8 Se mejoro el funcionamiento interno de algunos procesos que el plugin ejecuta para su correcto rendimiento.
3.7 Se agrego la posibilidad de permitir la emision de CFDI sin importar el estado de un pedido en caso de que el usuario asi lo desee.
3.6 Se corrigio un error interno al utilizar la precision decimal para los importes de los impuestos aplicables a un CFDI por parte de un pedido.
3.5 Se agrego la posiblidad de definir una precision decimal para redondear los valores correspondientes a los atriculos de un pedido desde WooCommerce.
3.4 Se agrego la posibilidad de permitir o no la emision de CFDI si la fecha en que un pedido fue completado esta fuera del mes actual. Se agrego la posibilidad de definir el metodo de pago por defecto.
3.3 Se agregaron mejoras para el rendimiento del sistema.
3.0 Se agrego la compatibilidad para emitir CFDI 3.3 unicamente. La emision de CFDI 3.2 ya no existe. Se agrego la posibilidad de seleccionar el idioma Español e Ingles. Se agregaron mas opciones de configuracion del plugin. Se agregaron mejoras visuales.
2.1 Se agregaron mejoras internas para la correcta visualizacion de informacion en el formato XML y PDF de un CFDI emitido.
2.0 Se agrego la seccion Preguntas Frecuentes para aclarar diversas dudas. Ahora es posible utilizar el plugin para realizar pruebas de emision de CFDI.
1.9 Se agrego la version del plugin para poder visualizarla en el titulo del panel de administracion.
1.8 Se mejoraron diversos aspectos visuales de la interfaz grafica y funcionamiento del plugin.
1.7 Se corrigieron errores y se agregaron mejoras, asi como la posibilidad de guardar y recuperar la configuracion del plugin en el sistema de facturacion automaticamente al establecer RFC, Usuario y Clave Cifrada en el plugin.
1.6 Se agrego la posibilidad de visualizar el estado de la cuenta del emisor en el panel de administracion al guardar la configuracion del plugin.
1.5 Se corrigieron algunos comportamientos del sistema y se implementaron validaciones para el tratamiento de la informacion.
1.4 Se corrigieron errores generales de diseño.
1.3 Se mejoro el proceso para verificar si un pedido ya fue facturado o no.
1.2 Se corrigieron errores que interferian con el funcionamiento del sitio web hecho en Wordpress.
1.1 Se agregaron nuevos parametros de configuracion de colores en el panel de administracion del plugin.