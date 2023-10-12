<?php
/*
Plugin Name: Facturación - RVCFDI para WooCommerce
Plugin URI: https://realvirtual.com.mx/factura-electronica-cfdi-wordpress-woocommerce/
Description: Conecta tu tienda WooCommerce con RealVirtual para que tus clientes puedan facturar sus compras.
Version: 8.1.0
Author: Gustavo Arizmendi
Author URI: https://profiles.wordpress.org/garizmendi/
Text Domain: rvcfdi-para-woocommerce
Domain Path: /languages/
License:     GPL2

RVCFDI para WooCommerce is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
RVCFDI para WooCommerce is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with RVCFDI para WooCommerce. If not, see https://www.gnu.org/licenses/gpl.html.
*/

$versionPlugin = '8.1.0';
$sistema = 'RVCFDI';
$nombreSistemaMenu = 'Facturación RVCFDI';
$nombreSistema = 'Facturación - RVCFDI para WooCommerce';
$nombreSistemaAsociado = 'RV Factura Electrónica Web';
$urlSistemaAsociado = 'https://comprobante.realvirtual.com.mx/comprobante_digital/';
$sitioOficialSistema = 'https://realvirtual.com.mx/';
$idiomaRVLFECFDI = 'ES';

if($sistema == 'LFECFDI')
{
	$nombreSistemaMenu = 'Facturación LFECFDI';
	$nombreSistema = 'Facturación - LFECFDI para WooCommerce';
	$nombreSistemaAsociado = 'LasFacturasElectrónicas.com';
	$urlSistemaAsociado = 'https://secure.lasfacturaselectronicas.com/lfe-misfacturas/';
	$sitioOficialSistema = 'https://lasfacturaselectronicas.com/';
}

$urlServicio = 'https://utils.realvirtual.com.mx/api/data';

require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_cuenta.php');
require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_configuracion.php');
require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_centrointegracion.php');
require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_pedido.php');
require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_cliente.php');
require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_emisor.php');
require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_metodopago.php');
require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_cfdi.php');
require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_configuracion_bayer.php');
require_once(plugin_dir_path( __FILE__ ).'recursos/realvirtual_woocommerce_complementos.php');

add_action('init', 'realvirtual_woocommerce_cargar_scripts');
add_action('admin_menu', 'realvirtual_woocommerce_back_end');
add_shortcode(strtolower($sistema).'_woocommerce_formulario', 'realvirtual_woocommerce_front_end');
add_shortcode(strtolower($sistema).'_woocommerce_formulario_receptor', 'realvirtual_woocommerce_front_end_receptor');

if(!(has_action('woocommerce_new_order', 'rvutils_woocommerce_enviar_pedido_creado')))
	add_action('woocommerce_new_order', 'rvutils_woocommerce_enviar_pedido_creado');

if(!(has_action('woocommerce_order_status_changed', 'rvcfdi_woocommerce_order_status_changed')))
	add_action('woocommerce_order_status_changed', 'rvcfdi_woocommerce_order_status_changed');

function creacion_base_datos()
{
	global $wpdb;
	
	$table_name = $wpdb->prefix.'realvirtual_datosfiscales';
	$charset_collate = $wpdb->get_charset_collate();
	
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id_user varchar(255) NOT NULL DEFAULT '',
		rfc varchar(20) NOT NULL DEFAULT '',
		razon_social text DEFAULT '',
		uso_cfdi varchar(255) NOT NULL DEFAULT '',
		forma_pago varchar(255) NOT NULL DEFAULT '',
		metodo_pago varchar(255) NOT NULL DEFAULT '',
		domicilio_fiscal varchar(255) NOT NULL DEFAULT '',
		regimen_fiscal varchar(255) NOT NULL DEFAULT '') $charset_collate;";
	
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

function realvirtual_woocommerce_cargar_scripts()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	wp_enqueue_script('realvirtual_woocommerce_script', plugin_dir_url(__FILE__).'/assets/realvirtual_woocommerce.js', array('jquery'), '30.33', true);
	wp_register_style('realvirtual_woocommerce_style', plugin_dir_url(__FILE__).'assets/realvirtual_woocommerce.css', array(), '2.6', 'all');
	wp_localize_script('realvirtual_woocommerce_script', 'myAjax', array( 'ajaxurl' => admin_url('admin-ajax.php')));
    wp_enqueue_style('realvirtual_woocommerce_style');
	wp_enqueue_script('jquery');
	wp_enqueue_script('realvirtual_woocommerce_script');
	
	//$inline_js = 'if(typeof(idiomaRVLFECFDI) == "undefined") var idiomaRVLFECFDI="'.$idiomaRVLFECFDI.'";';
    //wp_add_inline_script('realvirtual_woocommerce_script', $inline_js);
	
	wp_enqueue_script(
    'realvirtual_woocommerce_verificar_idioma_frontend',
    plugin_dir_url(__FILE__).'/assets/verificador_idioma_frontend_realvirtual_woocommerce.js',
    array('jquery'),
    '1',
    true
  );
  
  wp_enqueue_script(
    'realvirtual_woocommerce_verificar_idioma_backend',
    plugin_dir_url(__FILE__).'/assets/verificador_idioma_backend_realvirtual_woocommerce.js',
    array('jquery'),
    '1',
    true
  );
}

function realvirtual_woocommerce_back_end()
{
	global $sistema, $nombreSistemaMenu, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$idiomaRVLFECFDI = ($configuracion['idioma'] != '') ? $configuracion['idioma'] : 'ES';
	
	add_menu_page($sistema, $nombreSistemaMenu, 'manage_woocommerce', 'realvirtual_woo_dashboard', 'realvirtual_woocommerce_dashboard', plugin_dir_url( __FILE__ ).'/assets/realvirtual_woocommerce.png');
	add_submenu_page('realvirtual_woo_dashboard', (($idiomaRVLFECFDI == 'ES') ? 'Mi Cuenta' : 'My Account'), (($idiomaRVLFECFDI == 'ES') ? 'Mi Cuenta' : 'My Account'), 'manage_woocommerce', 'realvirtual_woo_cuenta', 'realvirtual_woocommerce_menu_cuenta');
	add_submenu_page('realvirtual_woo_dashboard', (($idiomaRVLFECFDI == 'ES') ? 'Configuración' : 'Settings'), (($idiomaRVLFECFDI == 'ES') ? 'Configuración' : 'Settings'), 'manage_woocommerce', 'realvirtual_woo_configuracion', 'realvirtual_woocommerce_menu_configuracion');
	
	if($cuenta['rfc'] == 'MCO701113C5A')
	{
		add_submenu_page('realvirtual_woo_dashboard', (($idiomaRVLFECFDI == 'ES') ? 'Configuración Bayer' : 'Configuración Bayer'), (($idiomaRVLFECFDI == 'ES') ? 'Configuración Bayer' : 'Configuración Bayer'), 'manage_woocommerce', 'realvirtual_woo_configuracionbayer', 'realvirtual_woocommerce_configuracion_bayer');
	}
	
	add_submenu_page('realvirtual_woo_dashboard', (($idiomaRVLFECFDI == 'ES') ? 'Facturación' : 'Invoicing'), (($idiomaRVLFECFDI == 'ES') ? 'Facturación' : 'Invoicing'), 'manage_woocommerce', 'realvirtual_woo_facturacion', 'realvirtual_woocommerce_menu_facturacion');
	add_submenu_page('realvirtual_woo_dashboard', (($idiomaRVLFECFDI == 'ES') ? 'Centro de Integración' : 'Integration Center'), (($idiomaRVLFECFDI == 'ES') ? 'Centro de Integración' : 'Integration Center'), 'manage_woocommerce', 'realvirtual_woo_integracion', 'realvirtual_woocommerce_menu_integracion');
	add_submenu_page('realvirtual_woo_dashboard', (($idiomaRVLFECFDI == 'ES') ? 'Mis Licencias' : 'My Licenses'), (($idiomaRVLFECFDI == 'ES') ? 'Mis Licencias' : 'My Licenses'), 'manage_woocommerce', 'realvirtual_woo_licencias', 'realvirtual_woocommerce_menu_licencias');
	add_submenu_page('realvirtual_woo_dashboard', (($idiomaRVLFECFDI == 'ES') ? 'Ayuda' : 'Help'), '<font color="#f0d21d">'.(($idiomaRVLFECFDI == 'ES') ? 'Ayuda' : 'Help').'</font>', 'manage_woocommerce', 'realvirtual_woo_soporte', 'realvirtual_woocommerce_menu_soporte');
	add_submenu_page('realvirtual_woo_dashboard', (($idiomaRVLFECFDI == 'ES') ? 'Complementos' : 'Addons'), '<font color="#64c900">'.(($idiomaRVLFECFDI == 'ES') ? 'Complementos' : 'Addons').'</font>', 'manage_woocommerce', 'realvirtual_woo_complementos', 'realvirtual_woocommerce_complementos');
	remove_submenu_page('realvirtual_woo_dashboard','realvirtual_woo_dashboard');
}

function realvirtual_woocommerce_menu_licencias()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $versionPlugin, $idiomaRVLFECFDI;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		echo ($idiomaRVLFECFDI == 'ES') ? 'No se puede obtener la información porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.' : 'The information can not be obtained because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.';
		wp_die();
	}
	
	$filtro = '||||||';
	$datosVentas = RealVirtualWooCommerceCFDI::obtenerVentas($cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $filtro, $sistema, $urlSistemaAsociado, $idiomaRVLFECFDI);
	$VigenciaTimbrado = $datosVentas->TIMBRES_FOLIOS;
	
	$datosLicencias = RealVirtualWooCommerceCuenta::obtenerLicencias($cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
	$TIM_FechaInicio = $datosVentas->FECHA_INICIO;
	$TIM_FechaFin = $datosVentas->FECHA_FIN;
	$TIM_DiasRestantes = $datosVentas->DIAS_RESTANTES;
	$TIM_CONTRATADOS = $datosVentas->CONTRATADOS;
	$TIM_EMITIDOS = $datosVentas->EMITIDOS;
	$TIM_DISPONIBLES = $datosVentas->DISPONIBLES;
	$TIM_CANCELADOS = $datosVentas->CANCELADOS;
	
	$PL01_Existe = $datosLicencias->PL01_Existe;
	$PL01_Codigo = $datosLicencias->PL01_Codigo;
	$PL01_Nombre = $datosLicencias->PL01_Nombre;
	$PL01_FechaInicio = $datosLicencias->PL01_FechaInicio;
	$PL01_FechaFin = $datosLicencias->PL01_FechaFin;
	$PL01_DiasRestantes = $datosLicencias->PL01_DiasRestantes;
	$PL01_TiempoContratado = $datosLicencias->PL01_TiempoContratado;
	
	$PL02_Existe = $datosLicencias->PL02_Existe;
	$PL02_Codigo = $datosLicencias->PL02_Codigo;
	$PL02_Nombre = $datosLicencias->PL02_Nombre;
	$PL02_FechaInicio = $datosLicencias->PL02_FechaInicio;
	$PL02_FechaFin = $datosLicencias->PL02_FechaFin;
	$PL02_DiasRestantes = $datosLicencias->PL02_DiasRestantes;
	$PL02_TiempoContratado = $datosLicencias->PL02_TiempoContratado;
	
	$PL03_Existe = $datosLicencias->PL03_Existe;
	$PL03_Codigo = $datosLicencias->PL03_Codigo;
	$PL03_Nombre = $datosLicencias->PL03_Nombre;
	$PL03_FechaInicio = $datosLicencias->PL03_FechaInicio;
	$PL03_FechaFin = $datosLicencias->PL03_FechaFin;
	$PL03_DiasRestantes = $datosLicencias->PL03_DiasRestantes;
	$PL03_TiempoContratado = $datosLicencias->PL03_TiempoContratado;
	
	$WS01_Existe = $datosLicencias->WS01_Existe;
	$WS01_Codigo = $datosLicencias->WS01_Codigo;
	$WS01_Nombre = $datosLicencias->WS01_Nombre;
	$WS01_FechaInicio = $datosLicencias->WS01_FechaInicio;
	$WS01_FechaFin = $datosLicencias->WS01_FechaFin;
	$WS01_DiasRestantes = $datosLicencias->WS01_DiasRestantes;
	$WS01_TiempoContratado = $datosLicencias->WS01_TiempoContratado;
	
	$WS02_Existe = $datosLicencias->WS02_Existe;
	$WS02_Codigo = $datosLicencias->WS02_Codigo;
	$WS02_Nombre = $datosLicencias->WS02_Nombre;
	$WS02_FechaInicio = $datosLicencias->WS02_FechaInicio;
	$WS02_FechaFin = $datosLicencias->WS02_FechaFin;
	$WS02_DiasRestantes = $datosLicencias->WS02_DiasRestantes;
	$WS02_TiempoContratado = $datosLicencias->WS02_TiempoContratado;
	
	$WS03_Existe = $datosLicencias->WS03_Existe;
	$WS03_Codigo = $datosLicencias->WS03_Codigo;
	$WS03_Nombre = $datosLicencias->WS03_Nombre;
	$WS03_FechaInicio = $datosLicencias->WS03_FechaInicio;
	$WS03_FechaFin = $datosLicencias->WS03_FechaFin;
	$WS03_DiasRestantes = $datosLicencias->WS03_DiasRestantes;
	$WS03_TiempoContratado = $datosLicencias->WS03_TiempoContratado;
	
	$WS04_Existe = $datosLicencias->WS04_Existe;
	$WS04_Codigo = $datosLicencias->WS04_Codigo;
	$WS04_Nombre = $datosLicencias->WS04_Nombre;
	$WS04_FechaInicio = $datosLicencias->WS04_FechaInicio;
	$WS04_FechaFin = $datosLicencias->WS04_FechaFin;
	$WS04_DiasRestantes = $datosLicencias->WS04_DiasRestantes;
	$WS04_TiempoContratado = $datosLicencias->WS04_TiempoContratado;
	
	?>
		<style>
		.card_complementos {
		  box-shadow: 0 0 6px 0 rgba(0,0,0,0.2);
		  transition: 0.2s;
		  width: 100%;
		}

		.card_complementos:hover {
		  box-shadow: 0 0 16px 0 rgba(0,0,0,0.2);
		}

		.container_complementos {
		  padding: 2px 16px;
		  background-color: white;
		}

		.footer_complementos {
		   background-color: #f7f7f7;
		   color: black;
		   text-align: center;
		   border-top: 1px solid #ddd;
		   padding: 15px;
		}
		</style>
		<br/>
		<div style="background-color:#ffffff; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
        <font color="#000000" size="5"><b><?php echo esc_html($nombreSistema); ?></b></font><font color="#505050" size="2" style="font-style: italic;"><?php echo '&nbsp; '.($idiomaRVLFECFDI == 'ES' ? 'versión ' : 'version ').esc_html($versionPlugin); ?></font>
		<br/><br/>
		<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Mis Licencias':'My Licenses';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Vigencia de las licencias de uso del plugin de facturación':'Validity of the licenses to use the billing plugin';?></font></label>
		</div>
		<br/>
		<div>
			<table border="0">
				<tr>
					<td style="width:20%; padding: 15px; <?php echo ($sistema == 'RVCFDI') ? '' : 'display:none;'; ?>">
						<div class="card_complementos">
							<div class="container_complementos">
									<h3><b>Timbrado Ilimitado</b><br/><font color="#007095" size="2">Servicio Principal</font></h3>
									<p><b>Vigencia: </b><?php echo 'Del '.$TIM_FechaInicio.' al '.$TIM_FechaFin; ?>
									<br/><b>Días Restantes: </b><?php echo $TIM_DiasRestantes; ?>
									<br/><b><?php echo ($TIM_DiasRestantes > 0) ? '<font color="#599B00" size="3">VIGENTE</font>' : '<font color="#E42800" size="3">LICENCIA EXPIRADA</font>';?></b>
									</p>
							</div>
							<div class="footer_complementos" <?php echo ($TIM_DiasRestantes > 0) ? 'style="background-color:#58a329;"' : 'style="background-color:#d90c0c;"'; ?>>
								<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank">
									<input type="button" <?php echo ($TIM_DiasRestantes > 0) ? 'style="background-color:#417607;"' : 'style="background-color:#890000;"'; ?> class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Renovar Licencia':'Renew License';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px; <?php echo ($sistema == 'LFECFDI') ? '' : 'display:none;'; ?>">
						<div class="card_complementos">
							<div class="container_complementos">
									<h3><b>Folios</b><br/><font color="#007095" size="2">Servicio Principal</font></h3>
									<p><b>Contratados: </b><?php echo $TIM_CONTRATADOS; ?>
									<br/><b>Emitidos: </b><?php echo $TIM_EMITIDOS; ?>
									<br/><b>Cancelados: </b><?php echo $TIM_CANCELADOS; ?>
									<br/><b>Disponibles: </b><?php echo $TIM_DISPONIBLES; ?>
									<br/><b><?php echo ($TIM_DISPONIBLES > 0) ? '<font color="#599B00" size="3">FOLIOS DISPONIBLES</font>' : '<font color="#E42800" size="3">FOLIOS AGOTADOS</font>';?></b>
									</p>
							</div>
							<div class="footer_complementos" <?php echo ($TIM_DISPONIBLES > 0) ? 'style="background-color:#58a329;"' : 'style="background-color:#d90c0c;"'; ?>>
								<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank">
									<input type="button" <?php echo ($TIM_DISPONIBLES > 0) ? 'style="background-color:#417607;"' : 'style="background-color:#890000;"'; ?> class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Comprar Folios':'Buy Sheets';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px; <?php echo ($PL01_Existe == '1') ? '' : 'display:none;'; ?>">
						<div class="card_complementos">
							<div class="container_complementos">
									<h3><b><?php echo $PL01_Codigo.' - '.$PL01_Nombre; ?></b><br/><font color="#007095" size="2">Complemento</font></h3>
									<p><b>Vigencia: </b><?php echo 'Del '.$PL01_FechaInicio.' al '.$PL01_FechaFin; ?>
									<br/><b>Tiempo Contratado: </b><?php echo $PL01_TiempoContratado; ?>
									<br/><b>Días Restantes: </b><?php echo $PL01_DiasRestantes; ?>
									<br/><b><?php echo ($PL01_DiasRestantes > 0) ? '<font color="#599B00" size="3">VIGENTE</font>' : '<font color="#E42800" size="3">LICENCIA EXPIRADA</font>';?></b>
									</p>
							</div>
							<div class="footer_complementos" <?php echo ($PL01_DiasRestantes > 0) ? 'style="background-color:#58a329;"' : 'style="background-color:#d90c0c;"'; ?>>
								<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank">
									<input type="button" <?php echo ($PL01_DiasRestantes > 0) ? 'style="background-color:#417607;"' : 'style="background-color:#890000;"'; ?> style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Renovar Licencia':'Renew License';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px; <?php echo ($PL02_Existe == '1') ? '' : 'display:none;'; ?>">
						<div class="card_complementos">
							<div class="container_complementos">
									<h3><b><?php echo $PL02_Codigo.' - '.$PL02_Nombre; ?></b><br/><font color="#007095" size="2">Complemento</font></h3>
									<p><b>Vigencia: </b><?php echo 'Del '.$PL02_FechaInicio.' al '.$PL02_FechaFin; ?>
									<br/><b>Tiempo Contratado: </b><?php echo $PL02_TiempoContratado; ?>
									<br/><b>Días Restantes: </b><?php echo $PL02_DiasRestantes; ?>
									<br/><b><?php echo ($PL02_DiasRestantes > 0) ? '<font color="#599B00" size="3">VIGENTE</font>' : '<font color="#E42800" size="3">LICENCIA EXPIRADA</font>';?></b>
									</p>
							</div>
							<div class="footer_complementos" <?php echo ($PL02_DiasRestantes > 0) ? 'style="background-color:#58a329;"' : 'style="background-color:#d90c0c;"'; ?>>
								<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank">
									<input type="button" <?php echo ($PL02_DiasRestantes > 0) ? 'style="background-color:#417607;"' : 'style="background-color:#890000;"'; ?> style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Renovar Licencia':'Renew License';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px; <?php echo ($PL03_Existe == '1') ? '' : 'display:none;'; ?>">
						<div class="card_complementos">
							<div class="container_complementos">
									<h3><b><?php echo $PL03_Codigo.' - '.$PL03_Nombre; ?></b><br/><font color="#007095" size="2">Complemento</font></h3>
									<p><b>Vigencia: </b><?php echo 'Del '.$PL03_FechaInicio.' al '.$PL03_FechaFin; ?>
									<br/><b>Tiempo Contratado: </b><?php echo $PL03_TiempoContratado; ?>
									<br/><b>Días Restantes: </b><?php echo $PL03_DiasRestantes; ?>
									<br/><b><?php echo ($PL03_DiasRestantes > 0) ? '<font color="#599B00" size="3">VIGENTE</font>' : '<font color="#E42800" size="3">LICENCIA EXPIRADA</font>';?></b>
									</p>
							</div>
							<div class="footer_complementos" <?php echo ($PL03_DiasRestantes > 0) ? 'style="background-color:#58a329;"' : 'style="background-color:#d90c0c;"'; ?>>
								<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank">
									<input type="button" <?php echo ($PL03_DiasRestantes > 0) ? 'style="background-color:#417607;"' : 'style="background-color:#890000;"'; ?> style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Renovar Licencia':'Renew License';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px; <?php echo ($WS01_Existe == '1') ? '' : 'display:none;'; ?>">
						<div class="card_complementos">
							<div class="container_complementos">
									<h3><b><?php echo $WS01_Codigo.' - '.$WS01_Nombre; ?></b><br/><font color="#007095" size="2">Complemento</font></h3>
									<p><b>Vigencia: </b><?php echo 'Del '.$WS01_FechaInicio.' al '.$WS01_FechaFin; ?>
									<br/><b>Tiempo Contratado: </b><?php echo $WS01_TiempoContratado; ?>
									<br/><b>Días Restantes: </b><?php echo $WS01_DiasRestantes; ?>
									<br/><b><?php echo ($WS01_DiasRestantes > 0) ? '<font color="#599B00" size="3">VIGENTE</font>' : '<font color="#E42800" size="3">LICENCIA EXPIRADA</font>';?></b>
									</p>
							</div>
							<div class="footer_complementos" <?php echo ($WS01_DiasRestantes > 0) ? 'style="background-color:#58a329;"' : 'style="background-color:#d90c0c;"'; ?>>
								<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank">
									<input type="button" <?php echo ($WS01_DiasRestantes > 0) ? 'style="background-color:#417607;"' : 'style="background-color:#890000;"'; ?> style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Renovar Licencia':'Renew License';?>" />
								</a>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td style="width:20%; padding: 15px; <?php echo ($WS02_Existe == '1') ? '' : 'display:none;'; ?>">
						<div class="card_complementos">
							<div class="container_complementos">
									<h3><b><?php echo $WS02_Codigo.' - '.$WS02_Nombre; ?></b><br/><font color="#007095" size="2">Complemento</font></h3>
									<p><b>Vigencia: </b><?php echo 'Del '.$WS02_FechaInicio.' al '.$WS02_FechaFin; ?>
									<br/><b>Tiempo Contratado: </b><?php echo $WS02_TiempoContratado; ?>
									<br/><b>Días Restantes: </b><?php echo $WS02_DiasRestantes; ?>
									<br/><b><?php echo ($WS02_DiasRestantes > 0) ? '<font color="#599B00" size="3">VIGENTE</font>' : '<font color="#E42800" size="3">LICENCIA EXPIRADA</font>';?></b>
									</p>
							</div>
							<div class="footer_complementos" <?php echo ($WS02_DiasRestantes > 0) ? 'style="background-color:#58a329;"' : 'style="background-color:#d90c0c;"'; ?>>
								<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank">
									<input type="button" <?php echo ($WS02_DiasRestantes > 0) ? 'style="background-color:#417607;"' : 'style="background-color:#890000;"'; ?> style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Renovar Licencia':'Renew License';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px; <?php echo ($WS03_Existe == '1') ? '' : 'display:none;'; ?>">
						<div class="card_complementos">
							<div class="container_complementos">
									<h3><b><?php echo $WS03_Codigo.' - '.$WS03_Nombre; ?></b><br/><font color="#007095" size="2">Complemento</font></h3>
									<p><b>Vigencia: </b><?php echo 'Del '.$WS03_FechaInicio.' al '.$WS03_FechaFin; ?>
									<br/><b>Tiempo Contratado: </b><?php echo $WS03_TiempoContratado; ?>
									<br/><b>Días Restantes: </b><?php echo $WS03_DiasRestantes; ?>
									<br/><b><?php echo ($WS03_DiasRestantes > 0) ? '<font color="#599B00" size="3">VIGENTE</font>' : '<font color="#E42800" size="3">LICENCIA EXPIRADA</font>';?></b>
									</p>
							</div>
							<div class="footer_complementos" <?php echo ($WS03_DiasRestantes > 0) ? 'style="background-color:#58a329;"' : 'style="background-color:#d90c0c;"'; ?>>
								<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank">
									<input type="button" <?php echo ($WS03_DiasRestantes > 0) ? 'style="background-color:#417607;"' : 'style="background-color:#890000;"'; ?> style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Renovar Licencia':'Renew License';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px; <?php echo ($WS04_Existe == '1') ? '' : 'display:none;'; ?>">
						<div class="card_complementos">
							<div class="container_complementos">
									<h3><b><?php echo $WS04_Codigo.' - '.$WS04_Nombre; ?></b><br/><font color="#007095" size="2">Complemento</font></h3>
									<p><b>Vigencia: </b><?php echo 'Del '.$WS04_FechaInicio.' al '.$WS04_FechaFin; ?>
									<br/><b>Tiempo Contratado: </b><?php echo $WS04_TiempoContratado; ?>
									<br/><b>Días Restantes: </b><?php echo $WS04_DiasRestantes; ?>
									<br/><b><?php echo ($WS04_DiasRestantes > 0) ? '<font color="#599B00" size="3">VIGENTE</font>' : '<font color="#E42800" size="3">LICENCIA EXPIRADA</font>';?></b>
									</p>
							</div>
							<div class="footer_complementos" <?php echo ($WS04_DiasRestantes > 0) ? 'style="background-color:#58a329;"' : 'style="background-color:#d90c0c;"'; ?>>
								<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank">
									<input type="button" <?php echo ($WS04_DiasRestantes > 0) ? 'style="background-color:#417607;"' : 'style="background-color:#890000;"'; ?> style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Renovar Licencia':'Renew License';?>" />
								</a>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	<?php
}

function realvirtual_woocommerce_configuracion_bayer()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $versionPlugin, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad();
	
	?>
		<br/>
		<div style="background-color:#ffffff; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
        <font color="#000000" size="5"><b><?php echo esc_html($nombreSistema); ?></b></font><font color="#505050" size="2" style="font-style: italic;"><?php echo '&nbsp; '.($idiomaRVLFECFDI == 'ES' ? 'versión ' : 'version ').esc_html($versionPlugin); ?></font>
		</div>
		<br/>
		<div>
			<form id="realvirtual_woocommerce_configuracion_bayer" method="post" style="background-color: #FFFFFF; padding: 20px;">
				<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'Configuración BAYER':'BAYER Configuration';?></b></font></label>
				<br/><br/>
				<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Reporte Facturación':'Reporte Facturación';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Establece la configuración para el reporte de Facturación.':'Establece la configuración para el reporte de Facturación.';?></font></label>
				<br/><br/>
				<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Encabezado':'Encabezado';?></b></font></label>
				<br/><br/>
				<div>
					<label><font color="#000000">Clase Documento</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_c_clase_documento" name="rvcfdi_bayer_facturacion_c_clase_documento" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_c_clase_documento']); ?>">
					<br/><br/>
					<label><font color="#000000">Sociedad</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_c_sociedad" name="rvcfdi_bayer_facturacion_c_sociedad" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_c_sociedad']); ?>">
					<br/><br/>
					<label><font color="#000000">Moneda</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_c_moneda" name="rvcfdi_bayer_facturacion_c_moneda" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_c_moneda']); ?>">
					<br/><br/>
					<label><font color="#000000">Tc.Cab.Doc.</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_c_tc_cab_doc" name="rvcfdi_bayer_facturacion_c_tc_cab_doc" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_c_tc_cab_doc']); ?>">
					<br/><br/>
				</div>	
				<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Detalle':'Detalle';?></b></font></label>
				<br/><br/>
				<div>
					<label><font color="#000000">Cuenta</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_cuenta" name="rvcfdi_bayer_facturacion_p_cuenta" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_cuenta']); ?>">
					<br/><br/>
					<label><font color="#000000">División</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_division" name="rvcfdi_bayer_facturacion_p_division" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_division']); ?>">
					<br/><br/>
					<label><font color="#000000">CeBe</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_ce_be" name="rvcfdi_bayer_facturacion_p_ce_be" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_ce_be']); ?>">
					<br/><br/>
					<label><font color="#000000">Texto</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_texto" name="rvcfdi_bayer_facturacion_p_texto" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_texto']); ?>">
					<br/><br/>
					<label><font color="#000000">País Destinatario</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_pais_destinatario" name="rvcfdi_bayer_facturacion_p_pais_destinatario" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_pais_destinatario']); ?>">
					<br/><br/>
					<label><font color="#000000">Linea de Producto</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_linea_de_producto" name="rvcfdi_bayer_facturacion_p_linea_de_producto" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_linea_de_producto']); ?>">
					<br/><br/>
					<label><font color="#000000">Grupo de Producto</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_grupo_de_producto" name="rvcfdi_bayer_facturacion_p_grupo_de_producto" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_grupo_de_producto']); ?>">
					<br/><br/>
					<label><font color="#000000">Centro</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_centro" name="rvcfdi_bayer_facturacion_p_centro" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_centro']); ?>">
					<br/><br/>
					<label><font color="#000000">Cliente</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_cliente" name="rvcfdi_bayer_facturacion_p_cliente" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_cliente']); ?>">
					<br/><br/>
					<label><font color="#000000">Organiz. Ventas</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_organiz_ventas" name="rvcfdi_bayer_facturacion_p_organiz_ventas" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_organiz_ventas']); ?>">
					<br/><br/>
					<label><font color="#000000">Canal Distrib.</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_canal_distrib" name="rvcfdi_bayer_facturacion_p_canal_distrib" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_canal_distrib']); ?>">
					<br/><br/>
					<label><font color="#000000">Zona de Ventas</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_zoha_de_ventas" name="rvcfdi_bayer_facturacion_p_zoha_de_ventas" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_zoha_de_ventas']); ?>">
					<br/><br/>
					<label><font color="#000000">Oficina Ventas</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_oficina_ventas" name="rvcfdi_bayer_facturacion_p_oficina_ventas" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_oficina_ventas']); ?>">
					<br/><br/>
					<label><font color="#000000">Ramo</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_ramo" name="rvcfdi_bayer_facturacion_p_ramo" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_ramo']); ?>">
					<br/><br/>
					<label><font color="#000000">Grupo</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_grupo" name="rvcfdi_bayer_facturacion_p_grupo" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_grupo']); ?>">
					<br/><br/>
					<label><font color="#000000">Gr. Vendedores</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_gr_vendedores" name="rvcfdi_bayer_facturacion_p_gr_vendedores" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_gr_vendedores']); ?>">
					<br/><br/>
					<label><font color="#000000">Atributo 1 Sector</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_atributo_1_sector" name="rvcfdi_bayer_facturacion_p_atributo_1_sector" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_atributo_1_sector']); ?>">
					<br/><br/>
					<label><font color="#000000">Atributo 2 Sector</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_atributo_2_sector" name="rvcfdi_bayer_facturacion_p_atributo_2_sector" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_atributo_2_sector']); ?>">
					<br/><br/>
					<label><font color="#000000">Clase Factura</font></label><br/>
					<input type="text" id="rvcfdi_bayer_facturacion_p_clase_factura" name="rvcfdi_bayer_facturacion_p_clase_factura" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_facturacion_p_clase_factura']); ?>">
					<br/><br/>
				</div>
				<br/>
				<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Reporte Financiero':'Reporte Financiero';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Establece la configuración para el reporte Financiero.':'Establece la configuración para el reporte Financiero.';?></font></label>
				<br/><br/>
				<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Encabezado':'Encabezado';?></b></font></label>
				<br/><br/>
				<div>
					<label><font color="#000000">Clase de Documento</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_c_clase_de_documento" name="rvcfdi_bayer_financiero_c_clase_de_documento" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_c_clase_de_documento']); ?>">
					<br/><br/>
					<label><font color="#000000">Sociedad</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_c_sociedad" name="rvcfdi_bayer_financiero_c_sociedad" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_c_sociedad']); ?>">
					<br/><br/>
					<label><font color="#000000">Moneda</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_c_moneda" name="rvcfdi_bayer_financiero_c_moneda" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_c_moneda']); ?>">
					<br/><br/>
					<label><font color="#000000">T.Xt.Cab.Doc.</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_c_t_xt_cab_doc" name="rvcfdi_bayer_financiero_c_t_xt_cab_doc" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_c_t_xt_cab_doc']); ?>">
					<br/><br/>
					<label><font color="#000000">Cuenta Bancaria</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_c_cuenta_bancaria" name="rvcfdi_bayer_financiero_c_cuenta_bancaria" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_c_cuenta_bancaria']); ?>">
					<br/><br/>
					<label><font color="#000000">Texto</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_c_texto" name="rvcfdi_bayer_financiero_c_texto" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_c_texto']); ?>">
					<br/><br/>
					<label><font color="#000000">División</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_c_division" name="rvcfdi_bayer_financiero_c_division" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_c_division']); ?>">
					<br/><br/>
					<label><font color="#000000">CeBe</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_c_cebe" name="rvcfdi_bayer_financiero_c_cebe" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_c_cebe']); ?>">
					<br/><br/>
					<label><font color="#000000">Cliente</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_c_cliente" name="rvcfdi_bayer_financiero_c_cliente" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_c_cliente']); ?>">
					<br/><br/>
				</div>	
				<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Detalle':'Detalle';?></b></font></label>
				<br/><br/>
				<div>
					<label><font color="#000000">Cuenta</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_cuenta" name="rvcfdi_bayer_financiero_p_cuenta" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_cuenta']); ?>">
					<br/><br/>
					<label><font color="#000000">Ind. Impuestos</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_ind_impuestos" name="rvcfdi_bayer_financiero_p_ind_impuestos" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_ind_impuestos']); ?>">
					<br/><br/>
					<label><font color="#000000">División</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_division" name="rvcfdi_bayer_financiero_p_division" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_division']); ?>">
					<br/><br/>
					<label><font color="#000000">Texto</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_texto" name="rvcfdi_bayer_financiero_p_texto" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_texto']); ?>">
					<br/><br/>
					<label><font color="#000000">CeBe</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_cebe" name="rvcfdi_bayer_financiero_p_cebe" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_cebe']); ?>">
					<br/><br/>
					<label><font color="#000000">País Destinatario</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_pais_destinatario" name="rvcfdi_bayer_financiero_p_pais_destinatario" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_pais_destinatario']); ?>">
					<br/><br/>
					<label><font color="#000000">Línea de Producto</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_linea_de_producto" name="rvcfdi_bayer_financiero_p_linea_de_producto" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_linea_de_producto']); ?>">
					<br/><br/>
					<label><font color="#000000">Grupo de Producto</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_grupo_de_proudcto" name="rvcfdi_bayer_financiero_p_grupo_de_proudcto" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_grupo_de_proudcto']); ?>">
					<br/><br/>
					<label><font color="#000000">Centro</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_centro" name="rvcfdi_bayer_financiero_p_centro" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_centro']); ?>">
					<br/><br/>
					<label><font color="#000000">Artículo</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_articulo" name="rvcfdi_bayer_financiero_p_articulo" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_articulo']); ?>">
					<br/><br/>
					<label><font color="#000000">Zona de Ventas</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_zona_de_ventas" name="rvcfdi_bayer_financiero_p_zona_de_ventas" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_zona_de_ventas']); ?>">
					<br/><br/>
					<label><font color="#000000">Material</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_material" name="rvcfdi_bayer_financiero_p_material" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_material']); ?>">
					<br/><br/>
					<label><font color="#000000">Atributo 2 Sector</font></label><br/>
					<input type="text" id="rvcfdi_bayer_financiero_p_atributo_2_sector" name="rvcfdi_bayer_financiero_p_atributo_2_sector" style="width:25%" value="<?php echo esc_html($configuracion['rvcfdi_bayer_financiero_p_atributo_2_sector']); ?>">
					<br/><br/>
				</div>
				<br/><br/>
				<center>
				<div>
					<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_enviar_configuracion_bayer"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
					<img id="cargandoConfiguracionBayer" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
				</div>
				</center>
			</form>
		</div>
		
		<div id="ventanaModalConfiguracionBayer" class="modalConfiguracion">
			<div class="modal-contentConfiguracion">
				<span id="closeConfiguracionBayer" class="closeConfiguracion">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalConfiguracionBayer"></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalConfiguracionBayer"></div>
					</font>
					<br/>
					<input type="button" style="background-color:#e94700;" class="boton" id="botonModalConfiguracionBayer" value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Aceptar':'Accept';?>" />
				</center>
			</div>
		</div>
    <?php
}

function realvirtual_woocommerce_menu_cuenta()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $versionPlugin, $idiomaRVLFECFDI;
	
	?>
		<br/>
		<div style="background-color:#ffffff; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
        <font color="#000000" size="5"><b><?php echo esc_html($nombreSistema); ?></b></font><font color="#505050" size="2" style="font-style: italic;"><?php echo '&nbsp; '.($idiomaRVLFECFDI == 'ES' ? 'versión ' : 'version ').esc_html($versionPlugin); ?></font>
		</div>
		<br/>
		<div>
        <?php
			realvirtual_woocommerce_cuenta();
        ?>
		</div>
	<?php
}

function realvirtual_woocommerce_complementos()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $versionPlugin, $idiomaRVLFECFDI;
	
	?>
		<style>
		.card_complementos {
		  box-shadow: 0 0 6px 0 rgba(0,0,0,0.2);
		  transition: 0.2s;
		  width: 100%;
		}

		.card_complementos:hover {
		  box-shadow: 0 0 16px 0 rgba(0,0,0,0.2);
		}

		.container_complementos {
		  padding: 2px 16px;
		  background-color: white;
		}

		.footer_complementos {
		   background-color: #f7f7f7;
		   color: black;
		   text-align: center;
		   border-top: 1px solid #ddd;
		   padding: 15px;
		}
		</style>
		<br/>
		<div style="background-color:#ffffff; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
        <font color="#000000" size="5"><b><?php echo esc_html($nombreSistema); ?></b></font><font color="#505050" size="2" style="font-style: italic;"><?php echo '&nbsp; '.($idiomaRVLFECFDI == 'ES' ? 'versión ' : 'version ').esc_html($versionPlugin); ?></font>
		<br/><br/>
		<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Complementos':'Addons';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Mejora tu experiencia de facturación adquiriendo los complementos que ofrecemos':'Improve your billing experience by purchasing the add-ons we offer';?></font></label>
		</div>
		<br/>
		<div>
			<table border="0">
				<tr>
					<td style="width:20%; padding: 15px;">
						<div class="card_complementos">
							<div class="container_complementos">
								<center>
									<h3><b>FACTURA GLOBAL</b><br/><font color="#007095" size="2">PL01</font></h3>
									<img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_facturaglobal_img.png"); ?>" alt="Avatar" style="width:90px; height:80px;">
									<br/><br/>
									<p>Emisión de Factura Global de tus pedidos no facturados. Filtros de búsqueda de pedidos para y detalle de pedidos que ya fueron facturados.</p>
									<br/><br/>
								</center>
							</div>
							<div class="footer_complementos">
								<a href="<?php echo esc_url('https://realvirtual.com.mx/producto/pl01-factura-global-cfdi-por-csv/'); ?>" target="_blank">
									<input type="button" style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Comprar':'Purchase';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px;">
						<div class="card_complementos">
							<div class="container_complementos">
								<center>
									<h3><b>FACTURACIÓN DE PEDIDOS DESDE EL PANEL DE ADMINISTRACIÓN DEL PLUGIN</b><br/><font color="#007095" size="2">PL02</font></h3>
									<img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_facturacionpedidosinterno_img.png"); ?>" alt="Avatar" style="width:90px; height:80px;">
									<br/>
									<p>Factura tus pedidos desde el panel de administración del plugin a través del mismo formulario que tus clientes pueden utilizar desde tu sitio web.</p>
								</center>
							</div>
							<div class="footer_complementos">
								<a href="<?php echo esc_url('https://realvirtual.com.mx/producto/pl02-facturacion-de-pedidos-en-backend-woocomerce/'); ?>" target="_blank">
									<input type="button" style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Comprar':'Purchase';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px;">
						<div class="card_complementos">
							<div class="container_complementos">
								<center>
									<h3><b>FACTURACIÓN AUTOMÁTICA DE PEDIDOS</b><br/><font color="#007095" size="2">PL03</font></h3>
									<img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_facturacionautomaticapedidos_img.png"); ?>" alt="Avatar" style="width:90px; height:70px;">
									<br/><br/>
									<p>Factura un pedido automáticamente cuando su estatus cambie a Completado.</p>
									<br/><br/>
								</center>
							</div>
							<div class="footer_complementos">
								<a href="<?php echo esc_url('https://realvirtual.com.mx/producto/pl03-facturacion-automatica-de-pedidos/'); ?>" target="_blank">
									<input type="button" style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Comprar':'Purchase';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px;">
						<div class="card_complementos">
							<div class="container_complementos">
								<center>
									<h3><b>CONSULTA DE PEDIDOS DESDE UN SERVICIO EXTERNO</b><br/><font color="#007095" size="2">WS01</font></h3>
									<img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_wsconsultapedidos_img.png"); ?>" alt="Avatar" style="width:90px; height:80px;">
									<br/>
									<p>Permite que tus clientes puedan facturar pedidos que provienen desde una fuente diferente a WooCommerce. El plugin se conectará al API Rest que definas para recibir la información.</p>
								</center>
							</div>
							<div class="footer_complementos">
								<a href="<?php echo esc_url('https://realvirtual.com.mx/producto/pl04-portal-de-facturacion-con-consulta-a-servicio-externo/'); ?>" target="_blank">
									<input type="button" style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Comprar':'Purchase';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px;">
						<div class="card_complementos">
							<div class="container_complementos">
								<center>
									<h3><b>ENVÍO DE PEDIDO AL SER CREADO A UN SERVICIO EXTERNO</b><br/><font color="#007095" size="2">WS02</font></h3>
									<img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_enviarpedido_img.png"); ?>" alt="Avatar" style="width:90px; height:70px;">
									<br/>
									<p>Envía automáticamente a tu API Rest todo los datos de un pedido al momento de ser creado por un cliente. El plugin se conectará al API Rest que definas para enviar la información.</p>
									<br/>
								</center>
							</div>
							<div class="footer_complementos">
								<a href="<?php echo esc_url('https://realvirtual.com.mx/producto/ws02-automatizacion-para-woocommerce-envio-de-pedido-a-webservice-externo/'); ?>" target="_blank">
									<input type="button" style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Comprar':'Purchase';?>" />
								</a>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td style="width:20%; padding: 15px;">
						<div class="card_complementos">
							<div class="container_complementos">
								<center>
									<h3><b>ENVÍO DE PEDIDO AL CAMBIAR DE ESTADO A UN SERVICIO EXTERNO</b><br/><font color="#007095" size="2">WS03</font></h3>
									<img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_enviarpedido_img.png"); ?>" alt="Avatar" style="width:90px; height:70px;">
									<br/>
									<p>Envía automáticamente a tu API Rest el XML timbrado de un pedido al momento de cambiar de estado. El plugin se conectará al API Rest que definas para enviar la información.</p>
									<br/>
								</center>
							</div>
							<div class="footer_complementos">
								<a href="<?php echo esc_url('https://realvirtual.com.mx/producto/ws03-automatizacion-para-woocommerce-envio-de-pedido-al-cambiar-estado-a-webservice-externo/'); ?>" target="_blank">
									<input type="button" style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Comprar':'Purchase';?>" />
								</a>
							</div>
						</div>
					</td>
					<td style="width:20%; padding: 15px;">
						<div class="card_complementos">
							<div class="container_complementos">
								<center>
									<h3><b>ENVÍO DE XML DEL PEDIDO A UN SERVICIO EXTERNO AL EMITIR CFDI</b><br/><font color="#007095" size="2" >WS04</font></h3>
									<img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_envioxml_img.png"); ?>" alt="Avatar" style="width:90px; height:80px;">
									<br/>
									<p>Envía automáticamente a tu API Rest el XML timbrado de un pedido al momento de ser facturado por un cliente. El plugin se conectará al API Rest que definas para enviar la información.</p>
								</center>
							</div>
							<div class="footer_complementos">
								<a href="<?php echo esc_url('https://realvirtual.com.mx/producto/ws04-automatizacion-para-woocommerce-envio-de-xml-del-pedido-al-cambiar-estado-a-webservice-externo/'); ?>" target="_blank">
									<input type="button" style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Comprar':'Purchase';?>" />
								</a>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	<?php
}

function realvirtual_woocommerce_menu_integracion()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $versionPlugin, $idiomaRVLFECFDI;
	
	$default_tab = null;
	$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
  
	?>
		<style>
		.tooltip {
		  position: relative;
		  display: inline-block;
		  border-bottom: 1px black;
		}

		.tooltip .tooltiptext {
		  visibility: hidden;
		  width: 700px;
		  background-color: #555;
		  color: #fff;
		  text-align: left;
		  border-radius: 6px;
		  padding: 10px 10px 10px 10px;
		  position: absolute;
		  z-index: 1;
		  left: 50%;
		  margin-left: 10px;
		  opacity: 0;
		  transition: opacity 0.3s;
		}

		.tooltip .tooltiptext::after {
		  content: "";
		  position: absolute;
		  margin-left: -5px;
		  border-width: 5px;
		  border-style: solid;
		  border-color: #555 transparent transparent transparent;
		}

		.tooltip:hover .tooltiptext {
		  visibility: visible;
		  opacity: 1;
		}


		.tooltip.right .tooltiptext{
			top: -5px;
			left: 110%;
		}
		.tooltip.right .tooltiptext::after{
			margin-top: -5px;
			top: 50%;
			right: 100%;
			border-color: transparent #2E2E2E transparent transparent;
		}
		</style>
		<div class="wrap">
			<br/>
			<div style="background-color:#ffffff; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
			<font color="#000000" size="5"><b><?php echo esc_html($nombreSistema); ?></b></font><font color="#505050" size="2" style="font-style: italic;"><?php echo '&nbsp; '.($idiomaRVLFECFDI == 'ES' ? 'versión ' : 'version ').esc_html($versionPlugin); ?></font>
			<br/><br/>
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Centro de Integración':'Integration Center';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'En esta sección podrás conectar el plugin de facturación con tu punto de venta a través de un API Rest con el que cuente tu sistema para buscar en él los pedidos que se desean facturar o realizar otras acciones.':'In this section you can connect the billing plugin with your point of sale through a Rest API that your system has to search it for the orders you want to bill or perform other actions.';?></font></label>
			</div>
			<br/>
			<nav class="nav-tab-wrapper">
				<a href="?page=realvirtual_woo_integracion&tab=consultarPedidos" class="nav-tab <?php if($tab==='consultarPedidos' || $tab == null):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Consultar Pedidos' : 'Get Orders'; ?></a>
				<a href="?page=realvirtual_woo_integracion&tab=enviarPedidosCrear" class="nav-tab <?php if($tab==='enviarPedidosCrear'):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Enviar Pedidos al ser Creados' : 'Send Orders when Created'; ?></a>
				<a href="?page=realvirtual_woo_integracion&tab=enviarPedidos" class="nav-tab <?php if($tab==='enviarPedidos'):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Enviar Pedidos al cambiar de Estado' : 'Send Orders when changing State'; ?></a>
				<a href="?page=realvirtual_woo_integracion&tab=enviarXml" class="nav-tab <?php if($tab==='enviarXml'):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Enviar XML al facturar Pedidos' : 'Send XML when invoicing Orders'; ?></a>
			</nav>
			<div class="tab-content">
				<?php
					if($tab == 'consultarPedidos' || $tab == null)
						realvirtual_woocommerce_integracion_consultarPedidos();
					else if($tab == 'enviarPedidosCrear')
						realvirtual_woocommerce_integracion_enviarPedidosCrear();
					else if($tab == 'enviarPedidos')
						realvirtual_woocommerce_integracion_enviarPedidos();
					else if($tab == 'enviarXml')
						realvirtual_woocommerce_integracion_enviarXml();
				?>
			</div>
			
			<div id="ventanaModalCentroIntegracion" class="modalConfiguracion">
				<div class="modal-contentConfiguracion">
					<span id="closeCentroIntegracion" class="closeConfiguracion">&times;</span>
					<br/>
					<center>
						<font color="#000000" size="5"><b>
							<div id="tituloModalCentroIntegracion"></div>
						</b></font>
						<br/>
						<font color="#000000" size="3">
							<div id="textoModalCentroIntegracion"></div>
						</font>
						<br/>
						<input type="button" style="background-color:#e94700;" class="boton" id="botonModalCentroIntegracion" value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Aceptar':'Accept';?>" />
					</center>
				</div>
			</div>
		</div>
	<?php
}

function realvirtual_woocommerce_integracion_enviarXml()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	$complementos = RealVirtualWooCommerceComplementos::configuracionEntidad();
	?>
		<form id="realvirtual_woocommerce_ci_enviarXml" method="post" style="background-color: #FFFFFF; padding: 20px;">
		<div style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Enviar XML al facturar Pedidos' : 'Send XML when invoicing Orders';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Establece la conexión a tu API Rest para enviar el archivo XML en Base64 de un pedido cuando se timbre su CFDI.':'Establish the connection to your Rest API to send the XML file in Base64 of an order when its CFDI is issued.';?></font></label>
			<br/>
			<?php
				if($complementos['wsEnviarXMLTimbrado'] != '1')
				{
					$avisoTitulo = ($idiomaRVLFECFDI == 'ES') ? 'ESTE MÓDULO NO ESTÁ DISPONIBLE' : 'THIS MODULE IS NOT AVAILABLE';
					$avisoTitulo = '<label><font color="#dc0000" size="4"><b>'.$avisoTitulo.'</b></font></label>';
					$avisoMensaje = ($idiomaRVLFECFDI == 'ES') ? 'Estimado usuario, realiza la compra de este módulo para poder utilizarlo. Ve a la sección <b>Complementos</b> del plugin de facturación para realizar la compra de este módulo y conoce todos los complementos que ofrecemos.<br/>A continuación, podrás observar el módulo pero su funcionalidad estará deshabilitada.' : 'Dear user, make the purchase of this module to be able to use it. Go to the <b>Add-ons</b> section of the billing plugin to purchase this module and learn about all the add-ons we offer.<br/>Next, you will be able to see the module but its functionality will be disabled.';
					$avisoMensaje = '<label><font color="#000000" size="3">'.$avisoMensaje.'</font></label>';
					$avisoCompleto = '<br/><div style="background-color:#f3bfbf; padding: 15px;">'.$avisoTitulo.'<br/>'.$avisoMensaje.'</div>';
					echo $avisoCompleto;
				}
			?>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>REQUISITOS:</b><br/>Capacidad de adecuar tu servicio a nuestro estándar para ser compatible.':'<b>REQUIREMENTS:</b><br/>Ability to adapt your service to our standard to be compatible.';?></font></label>
			<br/><br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>OBJETIVO:</b><br/>Permitir que el plugin pueda conectarse con tu servicio para que se envíen a él los XML de los pedidos cuando se timbren sus CFDI.':'<b>OBJECTIVE:</b><br/>Allow the plugin to connect with your service to send it the XML of the orders when their CFDIs are issued.';?></font></label>
			<br/><br/>
			<label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo deseas que el plugin realice el envío de XML?':'How do you want the plugin to send XML?';?></b></font></label>
			<br/><br/>
			<select id="realvirtual_woocommerce_ci_enviarXml_formaConsulta" name="realvirtual_woocommerce_ci_enviarXml_formaConsulta" style="max-width:100%">
				<?php 
					$tipo_consulta = $configuracion['ci_enviarXml_tipo_consulta'];
					
					if($tipo_consulta == '0')
					{
					?>
						<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin no realizará envíos de XML a ningún servicio':'The plugin will not send XML to any service'; ?></option>
					<?php 
					}
					else
					{
					?>
						<option value="0"><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin no realizará envíos de XML a ningún servicio':'The plugin will not send XML to any service'; ?></option>
					<?php
					}
					if($tipo_consulta == '1')
					{
					?>
						<option value="1" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin enviará el XML a mi servicio cuando se timbre el CFDI de un pedido':'The plugin will send the XML to my service when the CFDI of an order is issued'; ?></option>
					<?php
					}								
					else
					{
					?>
						<option value="1"><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin enviará el XML a mi servicio cuando se timbre el CFDI de un pedido':'The plugin will send the XML to my service when the CFDI of an order is issued'; ?></option>
					<?php
					}
					?>
			</select>
			<br/><br/><br/>
			<div width="100%" id="realvirtual_woocommerce_ci_enviarXml_restoFormulario" hidden>
				<label><font color="#35a200" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'PASO 1. ' : 'STEP 1. ';?></b></font></label><label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Configura la conexión del plugin con tu servicio' : 'Configure the plugin connection with your service';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si recibes parámetros especiales (tokens, claves de acceso, etc.) en tu servicio, envíalos por GET especificándolos en la URL de tu servicio, independientemente del Tipo de Solicitud GET o POST que hayas seleccionado.' : 'If you receive special parameters (tokens, access keys, etc.) in your service, send them by GET specifying them in the URL of your service, regardless of the GET or POST Request Type you have selected.';?></font></label>
				<br/><br/>
				<table width="95%">
					<tr>
						<td style="width:10%" hidden>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo Conexión':'* Connection Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarXml_tipoConexion" name="realvirtual_woocommerce_ci_enviarXml_tipoConexion">
								<?php 
									$tipo_conexion = $configuracion['ci_enviarXml_tipo_conexion'];
									
									if($tipo_conexion == 'WS')
									{
									?>
										<option value="WS" selected hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="WS" hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php
									}
									if($tipo_conexion == 'PHP')
									{
									?>
										<option value="PHP" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="PHP"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td style="width:7%">
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo Solicitud':'* Request Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarXml_tipoSolicitud" name="realvirtual_woocommerce_ci_enviarXml_tipoSolicitud" style="width:120px">
								<?php 
									$tipo_solicitud = $configuracion['ci_enviarXml_tipo_solicitud'];
									
									if($tipo_solicitud == 'GET')
									{
									?>
										<option value="GET" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="GET"><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php
									
									}
									if($tipo_solicitud == 'POST')
									{
									?>
										<option value="POST" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="POST"><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* URL':'* URL';?></font></label>
							<br/>
							<input type="text" style="width:50%" id="realvirtual_woocommerce_ci_enviarXml_url" name="realvirtual_woocommerce_ci_enviarXml_url" value="<?php echo esc_html($configuracion['ci_enviarXml_url']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'URL del servicio':'Service URL';?>">
						</td>
					</tr>
				</table>
				<br/><br/>
				<label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Necesitas enviarlos a un segundo servicio? Configura la conexión del plugin con el segundo servicio' : 'Do you need to send them to a second service? Configure the plugin connection with the second service';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si recibes parámetros especiales (tokens, claves de acceso, etc.) en tu servicio, envíalos por GET especificándolos en la URL de tu servicio, independientemente del Tipo de Solicitud GET o POST que hayas seleccionado.' : 'If you receive special parameters (tokens, access keys, etc.) in your service, send them by GET specifying them in the URL of your service, regardless of the GET or POST Request Type you have selected.';?></font></label>
				<br/><br/>
				<table width="95%">
					<tr>
						<td style="width:10%" hidden>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Tipo Conexión':'Connection Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarXml_tipoConexion2" name="realvirtual_woocommerce_ci_enviarXml_tipoConexion2">
								<?php 
									$tipo_conexion = $configuracion['ci_enviarXml_tipo_conexion2'];
									
									if($tipo_conexion == 'WS')
									{
									?>
										<option value="WS" selected hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="WS" hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php
									}
									if($tipo_conexion == 'PHP')
									{
									?>
										<option value="PHP" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="PHP"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td style="width:7%">
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Tipo Solicitud':'Request Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarXml_tipoSolicitud2" name="realvirtual_woocommerce_ci_enviarXml_tipoSolicitud2" style="width:120px">
								<?php 
									$tipo_solicitud = $configuracion['ci_enviarXml_tipo_solicitud2'];
									
									if($tipo_solicitud == 'GET')
									{
									?>
										<option value="GET" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="GET"><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php
									
									}
									if($tipo_solicitud == 'POST')
									{
									?>
										<option value="POST" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="POST"><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'URL':'URL';?></font></label>
							<br/>
							<input type="text" style="width:50%" id="realvirtual_woocommerce_ci_enviarXml_url2" name="realvirtual_woocommerce_ci_enviarXml_url2" value="<?php echo esc_html($configuracion['ci_enviarXml_url2']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'URL del servicio':'Service URL';?>">
						</td>
					</tr>
				</table>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? '<b>NOTA: </b>Si no deseas enviar tus pedidos a un segundo servicio, sólo deja en blanco la URL del segundo servicio.' : '<b>NOTE: </b>If you do not want to send your orders to a second service, just leave the URL of the second service blank.';?></font></label>
				<br/><br/>
				<label><font color="#35a200" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'PASO 2. ' : 'STEP 2. ';?></b></font></label><label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Actualiza en tu servicio lo necesario' : 'Update your service as necessary';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Actualiza en tu servicio lo necesario para que reciba y lea correctamente la información del pedido que se enviará en formato JSON.' : 'Update in your service what is necessary so that it receives and correctly reads the information of the order that will be sent in JSON format.';?></b></font></label>
				<br/><br/>
				<label>{</label>
				<br/>
					<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"ID"</label></font><label> : <font color="#c33434" size="2">"001"</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Corresponde al número del pedido.':'Corresponds to the order number.';?></font></span>
					<br/>
					<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"XMLBase64"</label></font><label> : <font color="#c33434" size="2">"cadenaXMLBase64"</font></label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Corresponde al XML en Base64.':'Corresponds to XML in Base64.';?></font></span>
					<br/>
				<label>}</label>
				<br/>
			</div>
		</div>
		<br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_ci_enviarXml_botonGuardar"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargando_ci_enviarXml" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
		
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var accionEnviarXml_formaConsulta = document.getElementById('realvirtual_woocommerce_ci_enviarXml_formaConsulta').value;
				
				if(accionEnviarXml_formaConsulta == '0')
				{
					$( "#realvirtual_woocommerce_ci_enviarXml_restoFormulario" ).hide("slow", function()
					{
						  
					});
				}
				else
				{
					$( "#realvirtual_woocommerce_ci_enviarXml_restoFormulario" ).show("slow", function()
					{
						
					});
				}
				
				$('#realvirtual_woocommerce_ci_enviarXml_formaConsulta').change(function(event)
				{
					var accionEnviarXml_formaConsulta = document.getElementById('realvirtual_woocommerce_ci_enviarXml_formaConsulta').value;
					
					if(accionEnviarXml_formaConsulta == '0')
					{
						$( "#realvirtual_woocommerce_ci_enviarXml_restoFormulario" ).hide("slow", function()
						{
							  
						});
					}
					else
					{
						$( "#realvirtual_woocommerce_ci_enviarXml_restoFormulario" ).show("slow", function()
						{
							
						});
					}
				});
			});
		</script>
	<?php
}

function realvirtual_woocommerce_integracion_enviarPedidos()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	$complementos = RealVirtualWooCommerceComplementos::configuracionEntidad();
	$estadosOrden = wc_get_order_statuses();
	
	$tipo_consulta = $configuracion['ci_enviarPedidos_tipo_consulta'];
	
	$comboBoxEstadosOrden = '';
	
	$nombreEstado = ($idiomaRVLFECFDI == 'ES') ? 'El plugin no realizará envíos de pedidos a ningún servicio':'The plugin will not send orders to any service';
	if($tipo_consulta == '0')
		$comboBoxEstadosOrden .= '<option value="0" selected>'.$nombreEstado.'</option>';
	else
		$comboBoxEstadosOrden .= '<option value="0">'.$nombreEstado.'</option>';
	
	foreach($estadosOrden as $key => $value)
	{
		$idEstado = str_replace("wc-", "", $key);
		
		$nombreEstado = '';
		
		if($idEstado == 'pending')
			$nombreEstado = ($idiomaRVLFECFDI == 'ES') ? 'Pendiente' : 'Pending';
		else if($idEstado == 'processing')
			$nombreEstado = ($idiomaRVLFECFDI == 'ES') ? 'Procesando' : 'Processing';
		else if($idEstado == 'on-hold')
			$nombreEstado = ($idiomaRVLFECFDI == 'ES') ? 'En espera' : 'On hold';
		else if($idEstado == 'completed')
			$nombreEstado = ($idiomaRVLFECFDI == 'ES') ? 'Completado' : 'Completed';
		else if($idEstado == 'cancelled')
			$nombreEstado = ($idiomaRVLFECFDI == 'ES') ? 'Cancelado' : 'Canceled';
		else if($idEstado == 'refunded')
			$nombreEstado = ($idiomaRVLFECFDI == 'ES') ? 'Reembolsado' : 'Refunded';
		else if($idEstado == 'failed')
			$nombreEstado = ($idiomaRVLFECFDI == 'ES') ? 'Fallido' : 'Failed';
		else
			$nombreEstado = $value;
		
		if($nombreEstado != '')
		{
			if($tipo_consulta == $idEstado)
				$comboBoxEstadosOrden .= '<option value="'.$idEstado.'" selected>'.$nombreEstado.'</option>';
			else
				$comboBoxEstadosOrden .= '<option value="'.$idEstado.'">'.$nombreEstado.'</option>';
		}	
	}
	
	?>
		<form id="realvirtual_woocommerce_ci_enviarPedidos" method="post" style="background-color: #FFFFFF; padding: 20px;">
		<div style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Enviar Pedidos al cambiar de Estado' : 'Send Orders when changing State';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Establece la conexión a tu API Rest para enviar información de un pedido cuando su estado cambie.':'Establish the connection to your API Rest to send information about an order when its status changes.';?></font></label>
			<br/>
			<?php
				if($complementos['wsEnviarPedidosEstado'] != '1')
				{
					$avisoTitulo = ($idiomaRVLFECFDI == 'ES') ? 'ESTE MÓDULO NO ESTÁ DISPONIBLE' : 'THIS MODULE IS NOT AVAILABLE';
					$avisoTitulo = '<label><font color="#dc0000" size="4"><b>'.$avisoTitulo.'</b></font></label>';
					$avisoMensaje = ($idiomaRVLFECFDI == 'ES') ? 'Estimado usuario, realiza la compra de este módulo para poder utilizarlo. Ve a la sección <b>Complementos</b> del plugin de facturación para realizar la compra de este módulo y conoce todos los complementos que ofrecemos.<br/>A continuación, podrás observar el módulo pero su funcionalidad estará deshabilitada.' : 'Dear user, make the purchase of this module to be able to use it. Go to the <b>Add-ons</b> section of the billing plugin to purchase this module and learn about all the add-ons we offer.<br/>Next, you will be able to see the module but its functionality will be disabled.';
					$avisoMensaje = '<label><font color="#000000" size="3">'.$avisoMensaje.'</font></label>';
					$avisoCompleto = '<br/><div style="background-color:#f3bfbf; padding: 15px;">'.$avisoTitulo.'<br/>'.$avisoMensaje.'</div>';
					echo $avisoCompleto;
				}
			?>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>REQUISITOS:</b><br/>Capacidad de adecuar tu servicio a nuestro estándar para ser compatible.':'<b>REQUIREMENTS:</b><br/>Ability to adapt your service to our standard to be compatible.';?></font></label>
			<br/><br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>OBJETIVO:</b><br/>Permitir que el plugin pueda conectarse con tu servicio para que se envíen a él los pedidos cuando cambien de estado.':'<b>OBJECTIVE:</b><br/>Allow the plugin to connect with your service so that orders are sent to it when they change status.';?></font></label>
			<br/><br/>
			<label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Qué estado debe tener el pedido para ser enviado a tu servicio?':'What state must the order have to be sent to your service?';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Cuando un pedido cambie al estado que selecciones a continuación, se enviará a tu servicio.':'When an order changes to the status you select below, it will be sent to your service.';?></font></label>
			<br/><br/>
			<select id="realvirtual_woocommerce_ci_enviarPedidos_formaConsulta" name="realvirtual_woocommerce_ci_enviarPedidos_formaConsulta" style="max-width:100%">
				<?php
					echo $comboBoxEstadosOrden;
				?>
			</select>
			<br/><br/><br/>
			<div width="100%" id="realvirtual_woocommerce_ci_enviarPedidos_restoFormulario" hidden>
				<label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Configura la conexión del plugin con tu servicio' : 'Configure the plugin connection with your service';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si recibes parámetros especiales (tokens, claves de acceso, etc.) en tu servicio, envíalos por GET especificándolos en la URL de tu servicio, independientemente del Tipo de Solicitud GET o POST que hayas seleccionado.' : 'If you receive special parameters (tokens, access keys, etc.) in your service, send them by GET specifying them in the URL of your service, regardless of the GET or POST Request Type you have selected.';?></font></label>
				<br/><br/>
				<table width="95%">
					<tr>
						<td style="width:10%" hidden>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo Conexión':'* Connection Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarPedidos_tipoConexion" name="realvirtual_woocommerce_ci_enviarPedidos_tipoConexion">
								<?php 
									$tipo_conexion = $configuracion['ci_enviarPedidos_tipo_conexion'];
									
									if($tipo_conexion == 'WS')
									{
									?>
										<option value="WS" selected hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="WS" hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php
									}
									if($tipo_conexion == 'PHP')
									{
									?>
										<option value="PHP" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="PHP"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td style="width:7%">
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo Solicitud':'* Request Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarPedidos_tipoSolicitud" name="realvirtual_woocommerce_ci_enviarPedidos_tipoSolicitud" style="width:120px">
								<?php 
									$tipo_solicitud = $configuracion['ci_enviarPedidos_tipo_solicitud'];
									
									if($tipo_solicitud == 'GET')
									{
									?>
										<option value="GET" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="GET"><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php
									
									}
									if($tipo_solicitud == 'POST')
									{
									?>
										<option value="POST" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="POST"><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* URL':'* URL';?></font></label>
							<br/>
							<input type="text" style="width:50%" id="realvirtual_woocommerce_ci_enviarPedidos_url" name="realvirtual_woocommerce_ci_enviarPedidos_url" value="<?php echo esc_html($configuracion['ci_enviarPedidos_url']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'URL del servicio':'Service URL';?>">
						</td>
					</tr>
				</table>
				<br/><br/>
				<label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Necesitas enviarlos a un segundo servicio? Configura la conexión del plugin con el segundo servicio' : 'Do you need to send them to a second service? Configure the plugin connection with the second service';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si recibes parámetros especiales (tokens, claves de acceso, etc.) en tu servicio, envíalos por GET especificándolos en la URL de tu servicio, independientemente del Tipo de Solicitud GET o POST que hayas seleccionado.' : 'If you receive special parameters (tokens, access keys, etc.) in your service, send them by GET specifying them in the URL of your service, regardless of the GET or POST Request Type you have selected.';?></font></label>
				<br/><br/>
				<table width="95%">
					<tr>
						<td style="width:10%" hidden>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo Conexión':'* Connection Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarPedidos_tipoConexion2" name="realvirtual_woocommerce_ci_enviarPedidos_tipoConexion2">
								<?php 
									$tipo_conexion = $configuracion['ci_enviarPedidos_tipo_conexion2'];
									
									if($tipo_conexion == 'WS')
									{
									?>
										<option value="WS" selected hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="WS" hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php
									}
									if($tipo_conexion == 'PHP')
									{
									?>
										<option value="PHP" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="PHP"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td style="width:7%">
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Tipo Solicitud':'Request Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarPedidos_tipoSolicitud2" name="realvirtual_woocommerce_ci_enviarPedidos_tipoSolicitud2" style="width:120px">
								<?php 
									$tipo_solicitud = $configuracion['ci_enviarPedidos_tipo_solicitud2'];
									
									if($tipo_solicitud == 'GET')
									{
									?>
										<option value="GET" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="GET"><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php
									
									}
									if($tipo_solicitud == 'POST')
									{
									?>
										<option value="POST" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="POST"><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'URL':'URL';?></font></label>
							<br/>
							<input type="text" style="width:50%" id="realvirtual_woocommerce_ci_enviarPedidos_url2" name="realvirtual_woocommerce_ci_enviarPedidos_url2" value="<?php echo esc_html($configuracion['ci_enviarPedidos_url2']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'URL del servicio':'Service URL';?>">
						</td>
					</tr>
				</table>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? '<b>NOTA: </b>Si no deseas enviar tus pedidos a un segundo servicio, sólo deja en blanco la URL del segundo servicio.' : '<b>NOTE: </b>If you do not want to send your orders to a second service, just leave the URL of the second service blank.';?></font></label>
			</div>
		</div>
		<br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_ci_enviarPedidos_botonGuardar"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargando_ci_enviarPedidos" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
		
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var accionActualizacionPedidos_tipo_consulta = document.getElementById('realvirtual_woocommerce_ci_enviarPedidos_formaConsulta').value;
				
				if(accionActualizacionPedidos_tipo_consulta == '0')
				{
					$( "#realvirtual_woocommerce_ci_enviarPedidos_restoFormulario" ).hide("slow", function()
					{
						  
					});
				}
				else
				{
					$( "#realvirtual_woocommerce_ci_enviarPedidos_restoFormulario" ).show("slow", function()
					{
						
					});
				}
				
				$('#realvirtual_woocommerce_ci_enviarPedidos_formaConsulta').change(function(event)
				{
					var accionActualizacionPedidos_tipo_consulta = document.getElementById('realvirtual_woocommerce_ci_enviarPedidos_formaConsulta').value;
					
					if(accionActualizacionPedidos_tipo_consulta == '0')
					{
						$( "#realvirtual_woocommerce_ci_enviarPedidos_restoFormulario" ).hide("slow", function()
						{
							  
						});
					}
					else
					{
						$( "#realvirtual_woocommerce_ci_enviarPedidos_restoFormulario" ).show("slow", function()
						{
							
						});
					}
				});
			});
		</script>
	<?php
}

function realvirtual_woocommerce_integracion_enviarPedidosCrear()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	$complementos = RealVirtualWooCommerceComplementos::configuracionEntidad();
	?>
		<form id="realvirtual_woocommerce_ci_enviarPedidosCrear" method="post" style="background-color: #FFFFFF; padding: 20px;">
		<div style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Enviar Pedidos al ser Creados' : 'Send Orders when Created';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Establece la conexión a tu API Rest para enviar información de un pedido cuando sea creado.':'Establish the connection to your API Rest to send information about an order when it is created.';?></font></label>
			<br/>
			<?php
				if($complementos['wsEnviarPedidosCreado'] != '1')
				{
					$avisoTitulo = ($idiomaRVLFECFDI == 'ES') ? 'ESTE MÓDULO NO ESTÁ DISPONIBLE' : 'THIS MODULE IS NOT AVAILABLE';
					$avisoTitulo = '<label><font color="#dc0000" size="4"><b>'.$avisoTitulo.'</b></font></label>';
					$avisoMensaje = ($idiomaRVLFECFDI == 'ES') ? 'Estimado usuario, realiza la compra de este módulo para poder utilizarlo. Ve a la sección <b>Complementos</b> del plugin de facturación para realizar la compra de este módulo y conoce todos los complementos que ofrecemos.<br/>A continuación, podrás observar el módulo pero su funcionalidad estará deshabilitada.' : 'Dear user, make the purchase of this module to be able to use it. Go to the <b>Add-ons</b> section of the billing plugin to purchase this module and learn about all the add-ons we offer.<br/>Next, you will be able to see the module but its functionality will be disabled.';
					$avisoMensaje = '<label><font color="#000000" size="3">'.$avisoMensaje.'</font></label>';
					$avisoCompleto = '<br/><div style="background-color:#f3bfbf; padding: 15px;">'.$avisoTitulo.'<br/>'.$avisoMensaje.'</div>';
					echo $avisoCompleto;
				}
			?>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>REQUISITOS:</b><br/>Capacidad de adecuar tu servicio a nuestro estándar para ser compatible.':'<b>REQUIREMENTS:</b><br/>Ability to adapt your service to our standard to be compatible.';?></font></label>
			<br/><br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>OBJETIVO:</b><br/>Permitir que el plugin pueda conectarse con tu servicio para que se envíen a él los pedidos cuando sean creados.':'<b>OBJECTIVE:</b><br/>Allow the plugin to connect with your service so that orders are sent to it when they are created.';?></font></label>
			<br/><br/>
			<label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo deseas que el plugin realice el envío de pedidos a tu servicio?':'How you want the plugin to send orders to your service?';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Cuando un pedido sea creado, se enviará a tu servicio.':'When an order is created, it will be sent to your service.';?></font></label>
			<br/><br/>
			<select id="realvirtual_woocommerce_ci_enviarPedidosCrear_formaConsulta" name="realvirtual_woocommerce_ci_enviarPedidosCrear_formaConsulta" style="max-width:100%">
				<?php
					$tipo_consulta = $configuracion['ci_enviarPedidosCrear_tipo_consulta'];
									
					if($tipo_consulta == '0')
					{
					?>
						<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin no enviará ningún pedido a ningún servicio':'The plugin will not send any request to any service'; ?></option>
					<?php 
					}
					else
					{
					?>
						<option value="0"><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin no enviará ningún pedido a ningún servicio':'The plugin will not send any request to any service'; ?></option>
					<?php
					
					}
					if($tipo_consulta == '1')
					{
					?>
						<option value="1" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin enviará pedidos a mi servicio cuando sean creados.':'The plugin will send orders to my service when they are created'; ?></option>
					<?php 
					}
					else
					{
					?>
						<option value="1"><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin enviará pedidos a mi servicio cuando sean creados':'The plugin will send orders to my service when they are created'; ?></option>
					<?php 
					}
					?>
				?>
			</select>
			<br/><br/><br/>
			<div width="100%" id="realvirtual_woocommerce_ci_enviarPedidosCrear_restoFormulario" hidden>
				<label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Configura la conexión del plugin con tu servicio' : 'Configure the plugin connection with your service';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si recibes parámetros especiales (tokens, claves de acceso, etc.) en tu servicio, envíalos por GET especificándolos en la URL de tu servicio, independientemente del Tipo de Solicitud GET o POST que hayas seleccionado.' : 'If you receive special parameters (tokens, access keys, etc.) in your service, send them by GET specifying them in the URL of your service, regardless of the GET or POST Request Type you have selected.';?></font></label>
				<br/><br/>
				<table width="95%">
					<tr>
						<td style="width:10%" hidden>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo Conexión':'* Connection Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarPedidosCrear_tipoConexion" name="realvirtual_woocommerce_ci_enviarPedidosCrear_tipoConexion">
								<?php 
									$tipo_conexion = $configuracion['ci_enviarPedidosCrear_tipo_conexion'];
									
									if($tipo_conexion == 'WS')
									{
									?>
										<option value="WS" selected hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="WS" hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php
									}
									if($tipo_conexion == 'PHP')
									{
									?>
										<option value="PHP" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="PHP"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td style="width:7%">
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo Solicitud':'* Request Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarPedidosCrear_tipoSolicitud" name="realvirtual_woocommerce_ci_enviarPedidosCrear_tipoSolicitud" style="width:120px">
								<?php 
									$tipo_solicitud = $configuracion['ci_enviarPedidosCrear_tipo_solicitud'];
									
									if($tipo_solicitud == 'GET')
									{
									?>
										<option value="GET" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="GET"><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php
									
									}
									if($tipo_solicitud == 'POST')
									{
									?>
										<option value="POST" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="POST"><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* URL':'* URL';?></font></label>
							<br/>
							<input type="text" style="width:50%" id="realvirtual_woocommerce_ci_enviarPedidosCrear_url" name="realvirtual_woocommerce_ci_enviarPedidosCrear_url" value="<?php echo esc_html($configuracion['ci_enviarPedidosCrear_url']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'URL del servicio':'Service URL';?>">
						</td>
					</tr>
				</table>
				<br/><br/>
				<label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Necesitas enviarlos a un segundo servicio? Configura la conexión del plugin con el segundo servicio' : 'Do you need to send them to a second service? Configure the plugin connection with the second service';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si recibes parámetros especiales (tokens, claves de acceso, etc.) en tu servicio, envíalos por GET especificándolos en la URL de tu servicio, independientemente del Tipo de Solicitud GET o POST que hayas seleccionado.' : 'If you receive special parameters (tokens, access keys, etc.) in your service, send them by GET specifying them in the URL of your service, regardless of the GET or POST Request Type you have selected.';?></font></label>
				<br/><br/>
				<table width="95%">
					<tr>
						<td style="width:10%" hidden>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Tipo Conexión':'Connection Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarPedidosCrear_tipoConexion2" name="realvirtual_woocommerce_ci_enviarPedidosCrear_tipoConexion2">
								<?php 
									$tipo_conexion = $configuracion['ci_enviarPedidosCrear_tipo_conexion2'];
									
									if($tipo_conexion == 'WS')
									{
									?>
										<option value="WS" selected hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="WS" hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
									<?php
									}
									if($tipo_conexion == 'PHP')
									{
									?>
										<option value="PHP" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="PHP"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td style="width:7%">
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Tipo Solicitud':'Request Type';?></font></label>
							<br/>
							<select id="realvirtual_woocommerce_ci_enviarPedidosCrear_tipoSolicitud2" name="realvirtual_woocommerce_ci_enviarPedidosCrear_tipoSolicitud2" style="width:120px">
								<?php 
									$tipo_solicitud = $configuracion['ci_enviarPedidosCrear_tipo_solicitud2'];
									
									if($tipo_solicitud == 'GET')
									{
									?>
										<option value="GET" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="GET"><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
									<?php
									
									}
									if($tipo_solicitud == 'POST')
									{
									?>
										<option value="POST" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									else
									{
									?>
										<option value="POST"><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
									<?php 
									}
									?>
							</select>
						</td>
						<td>
							<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'URL':'URL';?></font></label>
							<br/>
							<input type="text" style="width:50%" id="realvirtual_woocommerce_ci_enviarPedidosCrear_url2" name="realvirtual_woocommerce_ci_enviarPedidosCrear_url2" value="<?php echo esc_html($configuracion['ci_enviarPedidosCrear_url2']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'URL del servicio':'Service URL';?>">
						</td>
					</tr>
				</table>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? '<b>NOTA: </b>Si no deseas enviar tus pedidos a un segundo servicio, sólo deja en blanco la URL del segundo servicio.' : '<b>NOTE: </b>If you do not want to send your orders to a second service, just leave the URL of the second service blank.';?></font></label>
			</div>
		</div>
		<br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_ci_enviarPedidosCrear_botonGuardar"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargando_ci_enviarPedidosCrear" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
		
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var accionActualizacionPedidosCrear_tipo_consulta = document.getElementById('realvirtual_woocommerce_ci_enviarPedidosCrear_formaConsulta').value;
				
				if(accionActualizacionPedidosCrear_tipo_consulta == '0')
				{
					$( "#realvirtual_woocommerce_ci_enviarPedidosCrear_restoFormulario" ).hide("slow", function()
					{
						  
					});
				}
				else
				{
					$( "#realvirtual_woocommerce_ci_enviarPedidosCrear_restoFormulario" ).show("slow", function()
					{
						
					});
				}
				
				$('#realvirtual_woocommerce_ci_enviarPedidosCrear_formaConsulta').change(function(event)
				{
					var accionActualizacionPedidosCrear_tipo_consulta = document.getElementById('realvirtual_woocommerce_ci_enviarPedidosCrear_formaConsulta').value;
					
					if(accionActualizacionPedidosCrear_tipo_consulta == '0')
					{
						$( "#realvirtual_woocommerce_ci_enviarPedidosCrear_restoFormulario" ).hide("slow", function()
						{
							  
						});
					}
					else
					{
						$( "#realvirtual_woocommerce_ci_enviarPedidosCrear_restoFormulario" ).show("slow", function()
						{
							
						});
					}
				});
			});
		</script>
	<?php
}

function realvirtual_woocommerce_integracion_consultarPedidos()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	$complementos = RealVirtualWooCommerceComplementos::configuracionEntidad();
	?>
		<form id="realvirtual_woocommerce_ci_consultarPedidos" method="post" style="background-color: #FFFFFF; padding: 20px;">
		<div style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Consultar Pedidos':'Get Orders';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Establece la conexión a tu API Rest para buscar en él los pedidos que se desean facturar.':'Establish the connection to your API Rest to search it for the orders that you want to invoice.';?></font></label>
			<br/>
			<?php
				if($complementos['wsObtenerPedidosExternos'] != '1')
				{
					$avisoTitulo = ($idiomaRVLFECFDI == 'ES') ? 'ESTE MÓDULO NO ESTÁ DISPONIBLE' : 'THIS MODULE IS NOT AVAILABLE';
					$avisoTitulo = '<label><font color="#dc0000" size="4"><b>'.$avisoTitulo.'</b></font></label>';
					$avisoMensaje = ($idiomaRVLFECFDI == 'ES') ? 'Estimado usuario, realiza la compra de este módulo para poder utilizarlo. Ve a la sección <b>Complementos</b> del plugin de facturación para realizar la compra de este módulo y conoce todos los complementos que ofrecemos.<br/>A continuación, podrás observar el módulo pero su funcionalidad estará deshabilitada.' : 'Dear user, make the purchase of this module to be able to use it. Go to the <b>Add-ons</b> section of the billing plugin to purchase this module and learn about all the add-ons we offer.<br/>Next, you will be able to see the module but its functionality will be disabled.';
					$avisoMensaje = '<label><font color="#000000" size="3">'.$avisoMensaje.'</font></label>';
					$avisoCompleto = '<br/><div style="background-color:#f3bfbf; padding: 15px;">'.$avisoTitulo.'<br/>'.$avisoMensaje.'</div>';
					echo $avisoCompleto;
				}
			?>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>REQUISITOS:</b><br/>Capacidad de adecuar tu servicio a nuestro estándar para ser compatible.':'<b>REQUIREMENTS:</b><br/>Ability to adapt your service to our standard to be compatible.';?></font></label>
			<br/><br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>OBJETIVO:</b><br/>Permitir que el plugin pueda conectarse con tu servicio para que a través de él se busquen los pedidos<br/>que el cliente desea facturar desde el módulo de facturación en el sitio web de tu empresa u organización.':'<b>OBJECTIVE:</b><br/>Allow the plugin to connect with your service so that through it the orders<br/>that the customer wants to invoice from the invoicing module can be found on the website of your company or organization.';?></font></label>
			<br/><br/>
			<label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo deseas que el plugin realice la búsqueda de pedidos?':'How do you want the plugin to search for orders?';?></b></font></label>
			<br/><br/>
			<select id="realvirtual_woocommerce_ci_consultarPedidos_formaConsulta" name="realvirtual_woocommerce_ci_consultarPedidos_formaConsulta" style="max-width:100%">
				<?php 
					$tipo_consulta = $configuracion['ci_consultarPedidos_tipo_consulta'];
					
					if($tipo_consulta == '1')
					{
					?>
						<option value="1" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin buscará los pedidos sólo a través de WooCommerce':'The plugin will search for orders only through WooCommerce'; ?></option>
					<?php
					}								
					else
					{
					?>
						<option value="1"><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin buscará los pedidos sólo a través de WooCommerce':'The plugin will search for orders only through WooCommerce'; ?></option>
					<?php
					}
					if($tipo_consulta == '0')
					{
					?>
						<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin buscará los pedidos sólo a través de mi servicio':'The plugin will search for orders only through my service'; ?></option>
					<?php 
					}
					else
					{
					?>
						<option value="0"><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin buscará los pedidos sólo a través de mi servicio':'The plugin will search for orders only through my service'; ?></option>
					<?php
					}
					if($tipo_consulta == '2')
					{
					?>
						<option value="2" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin buscará los pedidos a través de WooCommerce y de mi servicio':'The plugin will search for orders through WooCommerce and my service'; ?></option>
					<?php
					}
					else
					{
					?>
						<option value="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'El plugin buscará los pedidos a través de WooCommerce y de mi servicio':'The plugin will search for orders through WooCommerce and my service'; ?></option>
					<?php
					}
					?>
			</select>
			<br/><br/><br/>
			<div width="100%" id="realvirtual_woocommerce_ci_consultarPedidos_restoFormulario" hidden>
			<label><font color="#35a200" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'PASO 1. ' : 'STEP 1. ';?></b></font></label><label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Configura la conexión del plugin con tu servicio' : 'Configure the plugin connection with your service';?></b></font></label>
			<br/><br/>
			<table width="95%">
				<tr>
					<td style="width:10%" hidden>
						<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo Conexión':'* Connection Type';?></font></label>
						<br/>
						<select id="realvirtual_woocommerce_ci_consultarPedidos_tipoConexion" name="realvirtual_woocommerce_ci_consultarPedidos_tipoConexion">
							<?php 
								$tipo_conexion = $configuracion['ci_consultarPedidos_tipo_conexion'];
								
								if($tipo_conexion == 'WS')
								{
								?>
									<option value="WS" selected hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
								<?php 
								}
								else
								{
								?>
									<option value="WS" hidden><?php echo($idiomaRVLFECFDI == 'ES') ? 'Servicio Web (SOAP)':'Web service (SOAP)'; ?></option>
								<?php
								}
								if($tipo_conexion == 'PHP')
								{
								?>
									<option value="PHP" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
								<?php 
								}
								else
								{
								?>
									<option value="PHP"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Archivo PHP':'PHP File'; ?></option>
								<?php 
								}
								?>
						</select>
					</td>
					<td style="width:7%">
						<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo Solicitud':'* Request Type';?></font></label>
						<br/>
						<select id="realvirtual_woocommerce_ci_consultarPedidos_tipoSolicitud" name="realvirtual_woocommerce_ci_consultarPedidos_tipoSolicitud" style="width:120px">
							<?php 
								$tipo_solicitud = $configuracion['ci_consultarPedidos_tipo_solicitud'];
								
								if($tipo_solicitud == 'GET')
								{
								?>
									<option value="GET" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
								<?php 
								}
								else
								{
								?>
									<option value="GET"><?php echo($idiomaRVLFECFDI == 'ES') ? 'GET':'GET'; ?></option>
								<?php
								
								}
								if($tipo_solicitud == 'POST')
								{
								?>
									<option value="POST" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
								<?php 
								}
								else
								{
								?>
									<option value="POST"><?php echo($idiomaRVLFECFDI == 'ES') ? 'POST':'POST'; ?></option>
								<?php 
								}
								?>
						</select>
					</td>
					<td>
						<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* URL':'* URL';?></font></label>
						<br/>
						<input type="text" style="width:50%" id="realvirtual_woocommerce_ci_consultarPedidos_url" name="realvirtual_woocommerce_ci_consultarPedidos_url" value="<?php echo esc_html($configuracion['ci_consultarPedidos_url']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'URL del servicio':'Service URL';?>">
					</td>
				</tr>
			</table>
			<br/><br/>
			<label><font color="#35a200" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'PASO 2. ' : 'STEP 2. ';?></b></font></label><label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Indica al plugin los parámetros que recibe tu servicio' : 'Tell the plugin the parameters that your service receives';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Indica al plugin a qué parámetros de tu servicio enviar los datos que por defecto son solicitados al cliente en el módulo de facturación de tu sitio web' : 'Tell the plugin to which parameters of your service to send the data that by default are requested from the customer in the invoicing module of your website';?></font></label>
			<br/><br/>
			<table width="45%">
				<tr>
					<td style="width:20%">
						<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Enviar el valor del campo <b>Número de Pedido</b> al parámetro':'Send the value of the <b>Order Number</b> field to the parameter';?></font></label>
					</td>
					<td style="width:20%">
						<input type="text" style="width:100%" id="realvirtual_woocommerce_ci_consultarPedidos_numero_de_pedido" name="realvirtual_woocommerce_ci_consultarPedidos_numero_de_pedido" value="<?php echo esc_html($configuracion['ci_consultarPedidos_nombre_parametro_numeropedido']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Nombre del parámetro en tu servicio':'Parameter name in your service';?>">
					</td>
				</tr>
				<tr>
					<td>
						<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Enviar el valor del campo <b>Monto</b> al parámetro':'Send the value of the <b>Amount</b> field to the parameter';?></font></label>
					</td>
					<td>
						<input type="text" style="width:100%" id="realvirtual_woocommerce_ci_consultarPedidos_monto" name="realvirtual_woocommerce_ci_consultarPedidos_monto" value="<?php echo esc_html($configuracion['ci_consultarPedidos_nombre_parametro_monto']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Nombre del parámetro en tu servicio':'Parameter name in your service';?>">
					</td>
				</tr>
			</table>
			<br/><br/>
			<label><font color="#35a200" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'PASO 3. ' : 'STEP 3. ';?></b></font></label><label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Indica al plugin los parámetros adicionales que recibe tu servicio' : 'Tell the plugin the additional parameters your service receives';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Si tu servicio recibe más parámetros obligatorios, puedes definir y activar los siguientes campos para que se muestren en el módulo de facturación de tu sitio web y el cliente ingrese en ellos los datos de forma obligatoria.':'If your service receives more mandatory parameters, you can define and activate the following fields so that they are displayed in the invoicing module of your website and the customer enters the data in a mandatory way.';?></font></label>
			<br/><br/>
			<table width="100%">
				<tr>
					<td>
						<label><font color="#000000" size="3"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'CAMPO 1':'FIELD 1';?></b></font></label>
					</td>
				</tr>
				<tr>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Estado':'* Status';?></font></label>
									<br/>
									<select id="realvirtual_woocommerce_ci_consultarPedidos_activadoCampo1" name="realvirtual_woocommerce_ci_consultarPedidos_activadoCampo1" style="width:95%">
										<?php 
											$parametro_extra1_estado = $configuracion['ci_consultarPedidos_parametro_extra1_estado'];
											
											if($parametro_extra1_estado == '1')
											{
											?>
												<option value="1" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Activado':'Enabled'; ?></option>
											<?php 
											}
											else
											{
											?>
												<option value="1"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Activado':'Enabled'; ?></option>
											<?php
											}
											if($parametro_extra1_estado == '0')
											{
											?>
												<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Desactivado':'Disabled'; ?></option>
											<?php 
											}
											else
											{
											?>
												<option value="0"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Desactivado':'Disabled'; ?></option>
											<?php 
											}
											?>
									</select>
								</td>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo de datos':'* Data type';?></font></label>
									<br/>
									<select id="realvirtual_woocommerce_ci_consultarPedidos_tipoCampo1" name="realvirtual_woocommerce_ci_consultarPedidos_tipoCampo1" style="width:95%">
										<?php 
											$parametro_extra1_tipo = $configuracion['ci_consultarPedidos_parametro_extra1_tipo'];
											
											if($parametro_extra1_tipo == 'tipo_texto')
											{
											?>
												<option value="tipo_texto" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Texto':'Text'; ?></option>
											<?php 
											}
											else
											{
											?>
												<option value="tipo_texto"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Texto':'Text'; ?></option>
											<?php
											}
											if($parametro_extra1_tipo == 'tipo_fecha')
											{
											?>
												<option value="tipo_fecha" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Fecha':'Date'; ?></option>
											<?php 
											}
											else
											{
											?>
												<option value="tipo_fecha"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Fecha':'Date'; ?></option>
											<?php 
											}
											?>
									</select>
								</td>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Nombre del campo en el módulo de facturación':'* Name of the field in the invoicing module';?></font></label>
									<br/>
									<input type="text" style="width:98%" id="realvirtual_woocommerce_ci_consultarPedidos_nombreCampo1" name="realvirtual_woocommerce_ci_consultarPedidos_nombreCampo1" value="<?php echo esc_html($configuracion['ci_consultarPedidos_parametro_extra1_nombrevisual']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Nombre visual del campo en la interfaz de usuario':'Visual name of the field in the user interface';?>">
								</td>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* ¿A qué parámetro en tu servicio se enviará el valor del <b>CAMPO 1</b>?':'* To which parameter in your service will the value of <b>FIELD 1</b> be sent?';?></font></label>
									<br/>
									<input type="text" style="width:95%" id="realvirtual_woocommerce_ci_consultarPedidos_parametroCampo1" name="realvirtual_woocommerce_ci_consultarPedidos_parametroCampo1" value="<?php echo esc_html($configuracion['ci_consultarPedidos_parametro_extra1_nombreinterno']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Nombre del parámetro en tu servicio':'Parameter name in your service';?>">
								</td>
				</tr>
				<tr>
					<td>
						<br/>
						<label><font color="#000000" size="3"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'CAMPO 2':'FIELD 2';?></b></font></label>
					</td>
				</tr>
				<tr>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Estado':'* Status';?></font></label>
									<br/>
									<select id="realvirtual_woocommerce_ci_consultarPedidos_activadoCampo2" name="realvirtual_woocommerce_ci_consultarPedidos_activadoCampo2" style="width:95%">
										<?php 
											$parametro_extra2_estado = $configuracion['ci_consultarPedidos_parametro_extra2_estado'];
											
											if($parametro_extra2_estado == '1')
											{
											?>
												<option value="1" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Activado':'Enabled'; ?></option>
											<?php
											}											
											else
											{
											?>
												<option value="1"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Activado':'Enabled'; ?></option>
											<?php
											}
											if($parametro_extra2_estado == '0')
											{
											?>
												<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Desactivado':'Disabled'; ?></option>
											<?php 
											}
											else
											{
											?>
												<option value="0"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Desactivado':'Disabled'; ?></option>
											<?php 
											}
											?>
									</select>
								</td>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Tipo de datos':'* Data type';?></font></label>
									<br/>
									<select id="realvirtual_woocommerce_ci_consultarPedidos_tipoCampo2" name="realvirtual_woocommerce_ci_consultarPedidos_tipoCampo2" style="width:95%">
										<?php 
											$parametro_extra2_tipo = $configuracion['ci_consultarPedidos_parametro_extra2_tipo'];
											
											if($parametro_extra2_tipo == 'tipo_texto')
											{
											?>
												<option value="tipo_texto" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Texto':'Text'; ?></option>
											<?php 
											}
											else
											{
											?>
												<option value="tipo_texto"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Texto':'Text'; ?></option>
											<?php
											}
											if($parametro_extra2_tipo == 'tipo_fecha')
											{
											?>
												<option value="tipo_fecha" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Fecha':'Date'; ?></option>
											<?php
											}											
											else
											{
											?>
												<option value="tipo_fecha"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Fecha':'Date'; ?></option>
											<?php 
											}
											?>
									</select>
								</td>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Nombre del campo en el módulo de facturación':'* Name of the field in the invoicing module';?></font></label>
									<br/>
									<input type="text" style="width:98%" id="realvirtual_woocommerce_ci_consultarPedidos_nombreCampo2" name="realvirtual_woocommerce_ci_consultarPedidos_nombreCampo2" value="<?php echo esc_html($configuracion['ci_consultarPedidos_parametro_extra2_nombrevisual']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Nombre visual del campo en la interfaz de usuario':'Visual name of the field in the user interface';?>">
								</td>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* ¿A qué parámetro en tu servicio se enviará el valor del <b>CAMPO 2</b>?':'* To which parameter in your service will the value of <b>FIELD 2</b> be sent?';?></font></label>
									<br/>
									<input type="text" style="width:95%" id="realvirtual_woocommerce_ci_consultarPedidos_parametroCampo2" name="realvirtual_woocommerce_ci_consultarPedidos_parametroCampo2" value="<?php echo esc_html($configuracion['ci_consultarPedidos_parametro_extra2_nombreinterno']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Nombre del parámetro en tu servicio':'Parameter name in your service';?>">
								</td>
				</tr>
			</table>
			<br/><br/>
			<label><font color="#35a200" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'PASO 4. ' : 'STEP 4. ';?></b></font></label><label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Actualiza la estructura de la respuesta de tu servicio' : 'Update the structure of your service response';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'La estructura de la respuesta de tu servicio al plugin debe ser bajo el siguiente estándar en formato JSON con la finalidad de asegurar su compatibilidad.':'The structure of the response of your service to the plugin must be under the following standard in JSON format in order to ensure its compatibility.';?></font></label>
			<br/><br/>
			<label>{</label>
			<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"status"</label></font><label> : <font color="#1a4be3" size="2">true</font>,</label><label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. <b>True</b> indica éxito y <b>False</b> cualquier problema con el pedido consultado.':'<b>Required</b>. <b>True</b> indicates success and <b>False</b> indicates any problem with the requested order.';?></font></label></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"message"</label></font><label> : <font color="#c33434" size="2">""</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b> si <b>status</b> = <b>False</b> para mostrar el mensaje al usuario. Si <b>status</b> = <b>True</b>, este dato no se utiliza.':'<b>Required</b> if <b>status</b> = <b>False</b> to display the message to the user. If <b>status</b> = <b>True</b>, this data is not used.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"ID"</label></font><label> : <font color="#c33434" size="2">"001"</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde al número del pedido.':'<b>Required</b>. Corresponds to the order number.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"Subtotal"</label></font><label> : <font color="#dc7a13" size="2">100</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde al subtotal del pedido.':'<b>Required</b>. Corresponds to the order subtotal.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"Descuento"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al descuento del pedido. Si existe, entonces se utiliza.':'<b>Optional</b>. Corresponds to the order discount. If it exists, then it is used.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"Total"</label></font><label> : <font color="#dc7a13" size="2">116</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde al total del pedido.':'<b>Required</b>. Corresponds to the order total.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"IVA"</label></font><label> : <font color="#dc7a13" size="2">16</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al IVA total del pedido. Es <b>requerido</b> si algún artículo utiliza este impuesto.':'<b>Optional</b>. Corresponds to the total VAT of the order. It is <b>required</b> if any item uses this tax.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"IEPS"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al IEPS total del pedido. Es <b>requerido</b> si algún artículo utiliza este impuesto.':'<b>Optional</b>. Corresponds to the total IEPS of the order. It is <b>required</b> if any item uses this tax.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"IVARetenido"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al IVA Retenido total del pedido. Es <b>requerido</b> si algún artículo utiliza este impuesto.':'<b>Optional</b>. Corresponds to the total VAT Withheld of the order. It is <b>required</b> if any item uses this tax.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"IEPSRetenido"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al IEPS Retenido total del pedido. Es <b>requerido</b> si algún artículo utiliza este impuesto.':'<b>Optional</b>. Corresponds to the total IEPS Withheld of the order. It is <b>required</b> if any item uses this tax.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"ISR"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al ISR total del pedido. Es <b>requerido</b> si algún artículo utiliza este impuesto.':'<b>Optional</b>. Corresponds to the total ISR of the order. It is <b>required</b> if any item uses this tax.';?></font></span>
				<br/>
				<span style="margin-left:1em"><font color="#7837d6" size="2"><label>"Articulo"</label></font><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Debe contener al menos un elemento.':'Required. Must contain at least one element.';?></font></span>
				<br/>
				<span style="margin-left:1em"><label>[</label></span>
				<br/>
					<span style="margin-left:2em"><label>{</label>
					<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"LineID"</label></font><label> : <font color="#c33434" size="2">"ART_001"</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde al No. Identificación del artículo.':'<b>Required</b>. Corresponds to the article ID Identification.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"ClaveProdServ"</label></font><label> : <font color="#c33434" size="2">"01010101"</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde a la ClaveProdServ del catálogo oficial del SAT.':'<b>Required</b>. Corresponds to the ClaveProdServ of the SAT official catalog.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"ClaveUnidad"</label></font><label> : <font color="#c33434" size="2">"ZZ"</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde a la ClaveUnidad del catálogo oficial del SAT.':'<b>Required</b>. Corresponds to the ClaveUnidad of the SAT official catalog.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"Cantidad"</label></font><label> : <font color="#dc7a13" size="2">1.00</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde a la cantidad del artículo.':'<b>Required</b>. It corresponds to the quantity of the article.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"Descripcion"</label></font><label> : <font color="#c33434" size="2">"Articulo de pruebas"</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde a la descripción del artículo.':'<b>Required</b>. Corresponds to the article description.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"PrecioUnitario"</label></font><label> : <font color="#dc7a13" size="2">100</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde al precio unitario del artículo.':'<b>Required</b>. Corresponds to the article unit price.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"Descuento"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde al descuento del artículo. Si el artículo no tiene descuento, el valor de este dato debe ser cero.':'<b>Required</b>. Corresponds to the article discount. If the article does not have a discount, the value of this data must be zero.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"Total"</label></font><label> : <font color="#dc7a13" size="2">116</font></label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido</b>. Corresponde al total del artículo.':'<b>Required</b>. Corresponds to the article total.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"IVA"</label></font><label> : <font color="#dc7a13" size="2">16</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al IVA del artículo.':'<b>Optional</b>. Corresponds to the total VAT of the article.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"IEPS"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al IEPS del artículo.':'<b>Optional</b>. Corresponds to the total IEPS of the article.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"IVARetenido"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al IVA Retenido del artículo.':'<b>Optional</b>. Corresponds to the total VAT Withheld of the article.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"IEPSRetenido"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al IEPS Retenido del artículo.':'<b>Optional</b>. Corresponds to the total IEPS Withheld of the article.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"ISR"</label></font><label> : <font color="#dc7a13" size="2">0</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Opcional</b>. Corresponde al ISR del artículo.':'<b>Optional</b>. Corresponds to the total ISR of the article.';?></font></span>
						<br/>
						<span style="margin-left:3em"><font color="#7837d6" size="2"><label>"ObjetoImpuesto"</label></font><label> : <font color="#c33434" size="2">"01"</font>,</label><font color="#8c8c8c" size="2" style="font-style: italic;"></span><span style="margin-left:2em"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<b>Requerido en CFDI 4.0</b>. Clave del Objeto de Impuesto del artículo para CFDI 4.0.':'<b>Required for CFDI 4.0</b>. Article Tax Object Code for CFDI 4.0.';?></font></span>
						<br/>
						</span>
					<span style="margin-left:2em"><label>}</label></span>
					</span>
					<br/>
				<span style="margin-left:1em"><label>]</label></span>
			<br/>
			<label>}</label>
			<br/>
			</div>
		</div>
		<br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_ci_consultarPedidos_botonGuardar"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargando_ci_consultarPedidos" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
		
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var ci_formaConsulta = document.getElementById('realvirtual_woocommerce_ci_consultarPedidos_formaConsulta').value;
				
				if(ci_formaConsulta == '1')
					{
						$( "#realvirtual_woocommerce_ci_consultarPedidos_restoFormulario" ).hide("slow", function()
						{
							  
						});
					}
					else
					{
						$( "#realvirtual_woocommerce_ci_consultarPedidos_restoFormulario" ).show("slow", function()
						{
							
						});
					}
				
				$('#realvirtual_woocommerce_ci_consultarPedidos_formaConsulta').change(function(event)
				{
					var ci_formaConsulta = document.getElementById('realvirtual_woocommerce_ci_consultarPedidos_formaConsulta').value;
					
					if(ci_formaConsulta == '1')
					{
						$( "#realvirtual_woocommerce_ci_consultarPedidos_restoFormulario" ).hide("slow", function()
						{
							  
						});
					}
					else
					{
						$( "#realvirtual_woocommerce_ci_consultarPedidos_restoFormulario" ).show("slow", function()
						{
							
						});
					}
				});
			});
		</script>
	<?php
}

function realvirtual_woocommerce_menu_configuracion()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $versionPlugin, $idiomaRVLFECFDI;
	
	$default_tab = null;
	$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
  
	?>
		<style>
		.tooltip {
		  position: relative;
		  display: inline-block;
		  border-bottom: 1px black;
		}

		.tooltip .tooltiptext {
		  visibility: hidden;
		  width: 700px;
		  background-color: #555;
		  color: #fff;
		  text-align: left;
		  border-radius: 6px;
		  padding: 10px 10px 10px 10px;
		  position: absolute;
		  z-index: 1;
		  left: 50%;
		  margin-left: 10px;
		  opacity: 0;
		  transition: opacity 0.3s;
		}

		.tooltip .tooltiptext::after {
		  content: "";
		  position: absolute;
		  margin-left: -5px;
		  border-width: 5px;
		  border-style: solid;
		  border-color: #555 transparent transparent transparent;
		}

		.tooltip:hover .tooltiptext {
		  visibility: visible;
		  opacity: 1;
		}


		.tooltip.right .tooltiptext{
			top: -5px;
			left: 110%;
		}
		.tooltip.right .tooltiptext::after{
			margin-top: -5px;
			top: 50%;
			right: 100%;
			border-color: transparent #2E2E2E transparent transparent;
		}
		</style>
		<div class="wrap">
			<br/>
			<div style="background-color:#ffffff; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
			<font color="#000000" size="5"><b><?php echo esc_html($nombreSistema); ?></b></font><font color="#505050" size="2" style="font-style: italic;"><?php echo '&nbsp; '.($idiomaRVLFECFDI == 'ES' ? 'versión ' : 'version ').esc_html($versionPlugin); ?></font>
			<br/><br/>
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Configuración':'Settings';?></b></font></label>
			</div>
			<br/>
			<nav class="nav-tab-wrapper">
			  <a href="?page=realvirtual_woo_configuracion&tab=general" class="nav-tab <?php if($tab==='general' || $tab == null):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'General' : 'General'; ?></a>
			  <a href="?page=realvirtual_woo_configuracion&tab=productos" class="nav-tab <?php if($tab==='productos'):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Productos' : 'Products'; ?></a>
			  <a href="?page=realvirtual_woo_configuracion&tab=envios" class="nav-tab <?php if($tab==='envios'):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Envíos (Shipping)' : 'Shipping'; ?></a>
			  <a href="?page=realvirtual_woo_configuracion&tab=impuestos" class="nav-tab <?php if($tab==='impuestos'):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Impuestos' : 'Taxes'; ?></a>
			  <a href="?page=realvirtual_woo_configuracion&tab=reglasModuloClientes" class="nav-tab <?php if($tab==='reglasModuloClientes'):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Reglas del módulo de facturación para clientes' : 'Rules of the billing module for customers'; ?></a>
			  <a href="?page=realvirtual_woo_configuracion&tab=estiloModuloClientes" class="nav-tab <?php if($tab==='estiloModuloClientes'):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Aspecto visual del módulo de facturación para clientes' : 'Visual appearance of the billing module for customers'; ?></a>
			  <a href="?page=realvirtual_woo_configuracion&tab=ajustesAvanzados" class="nav-tab <?php if($tab==='ajustesAvanzados'):?>nav-tab-active<?php endif; ?>"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ajustes Avanzados' : 'Advanced Settings'; ?></a>
			  <a href="?page=realvirtual_woo_configuracion&tab=idioma" class="nav-tab <?php if($tab==='idioma'):?>nav-tab-active<?php endif; ?>">Idioma</a>
			</nav>
			<div class="tab-content">
				<?php
					if($tab == 'general' || $tab == null)
						realvirtual_woocommerce_configuracion_general();
					else if($tab == 'productos')
						realvirtual_woocommerce_configuracion_productos();
					else if($tab == 'envios')
						realvirtual_woocommerce_configuracion_envios();
					else if($tab == 'impuestos')
						realvirtual_woocommerce_configuracion_impuestos();
					else if($tab == 'reglasModuloClientes')
						realvirtual_woocommerce_configuracion_reglasModuloClientes();
					else if($tab == 'estiloModuloClientes')
						realvirtual_woocommerce_configuracion_estiloModuloClientes();
					else if($tab == 'ajustesAvanzados')
						realvirtual_woocommerce_configuracion_ajustesAvanzados();
					else if($tab == 'idioma')
						realvirtual_woocommerce_configuracion_idioma();
				?>
			</div>
			<div id="ventanaModalConfiguracion" class="modalConfiguracion">
				<div class="modal-contentConfiguracion">
					<span class="closeConfiguracion">&times;</span>
					<br/>
					<center>
						<font color="#000000" size="5"><b>
							<div id="tituloModalConfiguracion"></div>
						</b></font>
						<br/>
						<font color="#000000" size="3">
							<div id="textoModalConfiguracion"></div>
						</font>
						<br/>
						<input type="button" style="background-color:#e94700;" class="boton" id="botonModalConfiguracion" value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Aceptar':'Accept';?>" />
					</center>
				</div>
			</div>
		</div>
	<?php
}

function realvirtual_woocommerce_configuracion_general()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<form id="realvirtual_woocommerce_configuracion_general" method="post" style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Emisión de CFDI y Factura Global - General':'Issuance of CFDI and Global Invoice - General';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Establece la configuración para la emisión de CFDI.':'Set the configuration for the CFDI Issue.';?></font></label>
			<br/><br/>
			<div>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Versión de CFDI':'* CFDI version';?></font></label><br/>
					<select id="version_cfdi" name="version_cfdi" style="width:10%">
					<?php 
						$version_cfdi = $configuracion['version_cfdi'];
						
						/*if($version_cfdi == '3.3')
						{
						?>
							<option value="3.3" selected>3.3</option>
						<?php 
						}
						else
						{
						?>
							<option value="3.3">3.3</option>
						<?php 
						}*/
						
						if($version_cfdi == '4.0')
						{
						?>
							<option value="4.0" selected>4.0</option>
						<?php 
						}
						else
						{
						?>
							<option value="4.0">4.0</option>
						<?php 
						}
					?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Serie de facturación':'* Issue Serie';?></font></label><br/>
				<input type="text" id="serie" name="serie" style="width:10%" value="<?php echo esc_html($configuracion['serie']); ?>">
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Régimen fiscal del Emisor':'* Issuer fiscal regime';?></font></label><br/>
				<select id="regimen_fiscal" name="regimen_fiscal" style="width:30%">
				<?php 
					$regimen_fiscal = $configuracion['regimen_fiscal'];
					
					if($regimen_fiscal == '601')
					{
					?>
						<option value="601" selected>601 - General de Ley Personas Morales</option>
					<?php 
					}
					else
					{
					?>
						<option value="601">601 - General de Ley Personas Morales</option>
					<?php 
					}
					
					if($regimen_fiscal == '603')
					{
					?>
						<option value="603" selected>603 - Personas Morales con Fines no Lucrativos</option>
					<?php 
					}
					else
					{
					?>
						<option value="603">603 - Personas Morales con Fines no Lucrativos</option>
					<?php 
					}
					
					if($regimen_fiscal == '605')
					{
					?>
						<option value="605" selected>605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>
					<?php 
					}
					else
					{
					?>
						<option value="605">605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>
					<?php 
					}
					
					if($regimen_fiscal == '606')
					{
					?>
						<option value="606" selected>606 - Arrendamiento</option>
					<?php 
					}
					else
					{
					?>
						<option value="606">606 - Arrendamiento</option>
					<?php 
					}
					
					if($regimen_fiscal == '607')
					{
					?>
						<option value="607" selected>607 - Régimen de Enajenación o Adquisición de Bienes</option>
					<?php 
					}
					else
					{
					?>
						<option value="607">607 - Régimen de Enajenación o Adquisición de Bienes</option>
					<?php 
					}
					
					if($regimen_fiscal == '608')
					{
					?>
						<option value="608" selected>608 - Demás ingresos</option>
					<?php 
					}
					else
					{
					?>
						<option value="608">608 - Demás ingresos</option>
					<?php 
					}
					
					if($regimen_fiscal == '609')
					{
					?>
						<option value="609" selected>609 - Consolidación</option>
					<?php 
					}
					else
					{
					?>
						<option value="609">609 - Consolidación</option>
					<?php 
					}
					
					if($regimen_fiscal == '610')
					{
					?>
						<option value="610" selected>610 - Residentes en el Extranjero sin Establecimiento Permanente en México</option>
					<?php 
					}
					else
					{
					?>
						<option value="610">610 - Residentes en el Extranjero sin Establecimiento Permanente en México</option>
					<?php 
					}
					
					if($regimen_fiscal == '611')
					{
					?>
						<option value="611" selected>611 - Ingresos por Dividendos (socios y accionistas)</option>
					<?php 
					}
					else
					{
					?>
						<option value="611">611 - Ingresos por Dividendos (socios y accionistas)</option>
					<?php 
					}
					
					if($regimen_fiscal == '612')
					{
					?>
						<option value="612" selected>612 - Personas Físicas con Actividades Empresariales y Profesionales</option>
					<?php 
					}
					else
					{
					?>
						<option value="612">612 - Personas Físicas con Actividades Empresariales y Profesionales</option>
					<?php 
					}
					
					if($regimen_fiscal == '614')
					{
					?>
						<option value="614" selected>614 - Ingresos por intereses</option>
					<?php 
					}
					else
					{
					?>
						<option value="614">614 - Ingresos por intereses</option>
					<?php 
					}
					
					if($regimen_fiscal == '615')
					{
					?>
						<option value="615" selected>615 - Régimen de los ingresos por obtención de premios</option>
					<?php 
					}
					else
					{
					?>
						<option value="615">615 - Régimen de los ingresos por obtención de premios</option>
					<?php 
					}
					
					if($regimen_fiscal == '616')
					{
					?>
						<option value="616" selected>616 - Sin obligaciones fiscales</option>
					<?php 
					}
					else
					{
					?>
						<option value="616">616 - Sin obligaciones fiscales</option>
					<?php 
					}
					
					if($regimen_fiscal == '620')
					{
					?>
						<option value="620" selected>620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>
					<?php 
					}
					else
					{
					?>
						<option value="620">620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>
					<?php 
					}
					
					if($regimen_fiscal == '621')
					{
					?>
						<option value="621" selected>621 - Incorporación Fiscal</option>
					<?php 
					}
					else
					{
					?>
						<option value="621">621 - Incorporación Fiscal</option>
					<?php 
					}
					
					if($regimen_fiscal == '622')
					{
					?>
						<option value="622" selected>622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>
					<?php 
					}
					else
					{
					?>
						<option value="622">622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>
					<?php 
					}
					
					if($regimen_fiscal == '623')
					{
					?>
						<option value="623" selected>623 - Opcional para Grupos de Sociedades</option>
					<?php 
					}
					else
					{
					?>
						<option value="623">623 - Opcional para Grupos de Sociedades</option>
					<?php 
					}
					
					if($regimen_fiscal == '624')
					{
					?>
						<option value="624" selected>624 - Coordinados</option>
					<?php 
					}
					else
					{
					?>
						<option value="624">624 - Coordinados</option>
					<?php 
					}
					
					if($regimen_fiscal == '625')
					{
					?>
						<option value="625" selected>625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>
					<?php 
					}
					else
					{
					?>
						<option value="625">625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>
					<?php 
					}
					
					if($regimen_fiscal == '626')
					{
					?>
						<option value="626" selected>626 - Régimen Simplificado de Confianza</option>
					<?php 
					}
					else
					{
					?>
						<option value="626">626 - Régimen Simplificado de Confianza</option>
					<?php 
					}
					
					if($regimen_fiscal == '628')
					{
					?>
						<option value="628" selected>628 - Hidrocarburos</option>
					<?php 
					}
					else
					{
					?>
						<option value="628">628 - Hidrocarburos</option>
					<?php 
					}
					
					if($regimen_fiscal == '629')
					{
					?>
						<option value="629" selected>629 - De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales</option>
					<?php 
					}
					else
					{
					?>
						<option value="629">629 - De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales</option>
					<?php 
					}
					
					if($regimen_fiscal == '630')
					{
					?>
						<option value="630" selected>630 - Enajenación de acciones en bolsa de valores</option>
					<?php 
					}
					else
					{
					?>
						<option value="630">630 - Enajenación de acciones en bolsa de valores</option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Moneda':'* Currency';?></font></label><br/>
				<select id="moneda" name="moneda" style="width:10%;">
				<?php 
					$moneda = $configuracion['moneda'];
					
					if($moneda == 'MXN')
					{
					?>
						<option value="MXN" selected>MXN - Pesos</option>
					<?php 
					}
					else
					{
					?>
						<option value="MXN">MXN - Pesos</option>
					<?php 
					}
					
					if($moneda == 'USD')
					{
					?>
						<option value="USD" selected>USD - Dolar</option>
					<?php 
					}
					else
					{
					?>
						<option value="USD">USD - Dolar</option>
					<?php 
					}
					
					if($moneda == 'EUR')
					{
					?>
						<option value="EUR" selected>EUR - Euro</option>
					<?php 
					}
					else
					{
					?>
						<option value="EUR">EUR - Euro</option>
					<?php 
					}
				?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'La moneda seleccionada se utilizará por defecto en un CFDI':'The selected currency will be used by default in a CFDI';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Tipo de Cambio':'* Exchange Rate';?></font></label><br/>
				<input type="text" id="tipo_cambio" name="tipo_cambio" style="width:10%;" value="<?php echo esc_html($configuracion['tipo_cambio']); ?>">
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Tipo de cambio para la moneda seleccionada':'Exchange rate for the selected currency';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Clave Confirmación':'Confirmation Code';?></font></label><br/>
				<input type="text" id="clave_confirmacion" name="clave_confirmacion" style="width:10%;" value="<?php echo esc_html($configuracion['clave_confirmacion']); ?>">
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'En caso de que el total de un CFDI sea mayor a $2,000,000.00, solicítanos la Clave de Confirmación<br/>para que pueda ser emitido. De lo contrario, deja vacío este campo.':'In case the total of a CFDI is greater than $ 2,000,000.00, ask us<br/>for the Confirmation Code for the CFDI to be issued. Otherwise, leave this field empty.';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Exportación':'* Export';?></font></label><br/>
					<select id="exportacion_cfdi" name="exportacion_cfdi" style="width:10%">
					<?php 
						$exportacion_cfdi = $configuracion['exportacion_cfdi'];
						
						if($exportacion_cfdi == '01')
						{
						?>
							<option value="01" selected>01 - No Aplica</option>
						<?php 
						}
						else
						{
						?>
							<option value="01">01 - No Aplica</option>
						<?php 
						}
						
						if($exportacion_cfdi == '02')
						{
						?>
							<option value="02" selected>02 - Definitiva</option>
						<?php 
						}
						else
						{
						?>
							<option value="02">02 - Definitiva</option>
						<?php 
						}
						
						if($exportacion_cfdi == '03')
						{
						?>
							<option value="03" selected>03 - Temporal</option>
						<?php 
						}
						else
						{
						?>
							<option value="03">03 - Temporal</option>
						<?php 
						}
						
						if($exportacion_cfdi == '04')
						{
						?>
							<option value="04" selected>04 - Definitiva con clave distinta a A1 o cuando no existe enajenación en términos del CFF</option>
						<?php 
						}
						else
						{
						?>
							<option value="04">04 - Definitiva con clave distinta a A1 o cuando no existe enajenación en términos del CFF</option>
						<?php 
						}
					?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Para CFDI 4.0 se debe registrar la clave con la que se identifica si el<br/>comprobante ampara una operación de exportación.'
						:
						'For CFDI 4.0, the key with which it is identified if the<br/>voucher covers an export operation must be registered.';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Fac. Atr. Adquiriente':'Fac. Atr. Adquiriente';?></font></label><br/>
				<input type="text" id="facAtrAdquirente" name="facAtrAdquirente" style="width:10%;" value="<?php echo esc_html($configuracion['facAtrAdquirente']); ?>">
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Para CFDI 4.0 se debe registrar el número de operación proporcionado por el<br/>
						SAT cuando se trate de un comprobante a través del adquirente<br/> 
						de los productos o servicios siempre que la respuesta del<br/> 
						servicio sea en sentido positivo, conforme a la Resolución<br/> 
						Miscelánea Fiscal vigente.'
						:
						'For CFDI 4.0 the transaction number provided by the<br/> 
						SAT in the case of a voucher through the acquirer<br/> 
						of the products or services provided that the response of the<br/> 
						service is positive, in accordance with Resolution<br/> 
						Current Tax Miscellaneous.';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Observación en el CFDI de un pedido':'Observation in the Order CFDI';?></font></label><br/>
				<input type="text" id="observacion" name="observacion" style="width:30%;" value="<?php echo esc_html($configuracion['observacion']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por ejemplo: No. Pedido [numero_pedido]':'For example: No. Order [numero_pedido]';?>">
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'La observación aparecerá en el formato PDF del CFDI de un pedido. Puedes dejar vacío este campo.<br/>Si deseas que aparezca el número del pedido en la observación que establezcas,<br/>escribe <b>[numero_pedido]</b> en cualquier parte de la observación.':'The observation will appear in the PDF format of the order CFDI. You can leave this field empty.<br/>If you want the order number to appear in the observation,<br/>then enter <b>[order_number]</b> anywhere in the observation.';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Zona Horaria del Emisor':'* Issuer Time Zone';?></font></label><br/>
				<select id="huso_horario" name="huso_horario" style="width:30%;">
					<?php 
					$huso_horario = $configuracion['huso_horario'];
					
					if($huso_horario == 'America/Tijuana')
					{
					?>
						<option value="America/Tijuana" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-08:00 Tiempo del Noroeste) Baja California': '(UTC-08:00 Tiempo del Noroeste) Baja California'?></option>
					<?php 
					}
					else
					{
					?>
						<option value="America/Tijuana"><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-08:00 Tiempo del Noroeste) Baja California': '(UTC-08:00 Tiempo del Noroeste) Baja California'?></option>
					<?php 
					}
					
					if($huso_horario == 'America/Mazatlan')
					{
					?>
						<option value="America/Mazatlan" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-07:00 Tiempo del Pacífico) Chihuahua, La Paz, Mazatlán': '(UTC-07:00 Tiempo del Pacífico) Chihuahua, La Paz, Mazatlán'?></option>
					<?php 
					}
					else
					{
					?>
						<option value="America/Mazatlan"><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-07:00 Tiempo del Pacífico) Chihuahua, La Paz, Mazatlán': '(UTC-07:00 Tiempo del Pacífico) Chihuahua, La Paz, Mazatlán'?></option>
					<?php 
					}
					
					if($huso_horario == 'America/Mexico_City')
					{
					?>
						<option value="America/Mexico_City" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-06:00 Tiempo del Centro) Guadalajara, Ciudad de México, Monterrey': '(UTC-06:00 Tiempo del Centro) Guadalajara, Ciudad de México, Monterrey'?></option>
					<?php 
					}
					else
					{
					?>
						<option value="America/Mexico_City"><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-06:00 Tiempo del Centro) Guadalajara, Ciudad de México, Monterrey': '(UTC-06:00 Tiempo del Centro) Guadalajara, Ciudad de México, Monterrey'?></option>
					<?php 
					}
					
					if($huso_horario == 'America/Mexico_City_HorarioVerano')
					{
					?>
						<option value="America/Mexico_City_HorarioVerano" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-06:00 Tiempo del Centro) Municipios que usan horario de verano': '(UTC-06:00 Tiempo del Centro) Municipios que usan horario de verano'?></option>
					<?php 
					}
					else
					{
					?>
						<option value="America/Mexico_City_HorarioVerano"><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-06:00 Tiempo del Centro) Municipios que usan horario de verano': '(UTC-06:00 Tiempo del Centro) Municipios que usan horario de verano'?></option>
					<?php 
					}
					
					if($huso_horario == 'America/Cancun')
					{
					?>
						<option value="America/Cancun" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-05:00 Tiempo del Sureste) Quintana Roo': '(UTC-05:00 Tiempo del Sureste) Quintana Roo'?></option>
					<?php 
					}
					else
					{
					?>
						<option value="America/Cancun"><?php echo ($idiomaRVLFECFDI == 'ES') ? '(UTC-05:00 Tiempo del Sureste) Quintana Roo': '(UTC-05:00 Tiempo del Sureste) Quintana Roo'?></option>
					<?php 
					}
					?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Establece la zona horaria que corresponde al código postal de tu dirección fiscal. De esta manera, la fecha de emisión de tus CFDI se establecerá correctamente al corresponder al huso horario de tu dirección fiscal.':'Set the time zone that corresponds to the postal code of your fiscal address. In this way, the date of issuance of your CFDI will be correctly established as it corresponds to the time zone of your fiscal address.';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Mostrar la dirección del cliente en la facturación':"* Show customer's address on billing";?></font></label><br/>
				<select id="domicilio_receptor" name="domicilio_receptor" style="width:30%;">
					<?php 
					$domicilio_receptor = $configuracion['domicilio_receptor'];
					
					if($domicilio_receptor == '0')
					{
					?>
						<option value="0" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No (Por defecto)': 'No (By default)'?></option>
					<?php 
					}
					else
					{
					?>
						<option value="0"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No (Por defecto)': 'No (By default)'?></option>
					<?php 
					}
					
					if($domicilio_receptor == '1')
					{
					?>
						<option value="1" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí': 'Yes'?></option>
					<?php 
					}
					else
					{
					?>
						<option value="1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí': 'Yes'?></option>
					<?php 
					}
					?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'La dirección del cliente aparecerá en el proceso de facturación en el módulo de facturación para clientes y en el formato PDF del CFDI. Por disposición oficial del SAT en el archivo XML del CFDI emitido esta información no existirá.':"The customer's address will appear in the billing process in the customer billing module and in the CFDI PDF format. By official provision of the SAT in the XML file of the CFDI issued, this information will not exist.";?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Precisión decimal':'* Decimal precision';?></font></label><br/>
				<select id="precision_decimal" name="precision_decimal" style="width:10%;">
				<?php 
					$precision_decimal = $configuracion['precision_decimal'];
					
					if($precision_decimal == '2')
					{
					?>
						<option value="2" selected>2</option>
					<?php 
					}
					else
					{
					?>
						<option value="2">2</option>
					<?php 
					}
					
					if($precision_decimal == '3')
					{
					?>
						<option value="3" selected>3</option>
					<?php 
					}
					else
					{
					?>
						<option value="3">3</option>
					<?php 
					}
					
					if($precision_decimal == '4')
					{
					?>
						<option value="4" selected>4</option>
					<?php 
					}
					else
					{
					?>
						<option value="4">4</option>
					<?php 
					}
					
					if($precision_decimal == '5')
					{
					?>
						<option value="5" selected>5</option>
					<?php 
					}
					else
					{
					?>
						<option value="5">5</option>
					<?php 
					}
					
					if($precision_decimal == '6')
					{
					?>
						<option value="6" selected>6</option>
					<?php 
					}
					else
					{
					?>
						<option value="6">6</option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Información Global para CFDI 4.0 (Facturación normal de un pedido)':'Global Information for CFDI 4.0 (Normal billing of an order)';?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Establece la Información Global que se utilizará cuando se emita CFDI 4.0 (Facturación normal de un pedido) a un receptor con RFC <b>XAXX010101000</b> y razón social <b>PUBLICO EN GENERAL</b>.':'Establishes the Global Information that will be used when CFDI 4.0 (Normal billing of an order) is issued to a recipient with RFC <b>XAXX010101000</b> and company name <b>PUBLICO EN GENERAL</b>';?></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Esta configuración no aplica para la emisión de una Factura Global. Sólo aplica en facturas normales de un pedido emitidas a PUBLICO EN GENERAL.':'This configuration does not apply to the issuance of a Global Invoice. It only applies to normal invoices for an order issued to the GENERAL PUBLIC.';?></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si deseas emitir una Factura Global con su propia Información Global, ve a la sección <b>Facturación > Factura Global</b>.':'If you wish to issue a Global Invoice with your own Global Information, go to the <b>Billing > Global Invoice</b> section.';?></font></label>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Periodicidad':'* Periodicity';?></font></label><br/>
				<select id="informacionGlobal_periodicidad" name="informacionGlobal_periodicidad" style="width:30%;">
				<?php 
					$informacionGlobal_periodicidad = $configuracion['informacionGlobal_periodicidad'];
					
					if($informacionGlobal_periodicidad == '01')
					{
					?>
						<option value="01" selected>01 - Diario</option>
					<?php 
					}
					else
					{
					?>
						<option value="01">01 - Diario</option>
					<?php 
					}
					
					if($informacionGlobal_periodicidad == '02')
					{
					?>
						<option value="02" selected>02 - Semanal</option>
					<?php 
					}
					else
					{
					?>
						<option value="02">02 - Semanal</option>
					<?php 
					}
					
					if($informacionGlobal_periodicidad == '03')
					{
					?>
						<option value="03" selected>03 - Quincenal</option>
					<?php 
					}
					else
					{
					?>
						<option value="03">03 - Quincenal</option>
					<?php 
					}
					
					if($informacionGlobal_periodicidad == '04')
					{
					?>
						<option value="04" selected>04 - Mensual</option>
					<?php 
					}
					else
					{
					?>
						<option value="04">04 - Mensual</option>
					<?php 
					}
					
					if($informacionGlobal_periodicidad == '05')
					{
					?>
						<option value="05" selected>05 - Bimestral</option>
					<?php 
					}
					else
					{
					?>
						<option value="05">05 - Bimestral</option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Meses':'* Months';?></font></label><br/>
				<select id="informacionGlobal_meses" name="informacionGlobal_meses" style="width:30%;">
				<?php 
					$informacionGlobal_meses = $configuracion['informacionGlobal_meses'];
					
					if($informacionGlobal_meses == '0')
					{
					?>
						<option value="0" selected>Mes en curso al momento de emitir el CFDI</option>
					<?php 
					}
					else
					{
					?>
						<option value="0">Mes en curso al momento de emitir el CFDI</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '01')
					{
					?>
						<option value="01" selected>01 - Enero</option>
					<?php 
					}
					else
					{
					?>
						<option value="01">01 - Enero</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '02')
					{
					?>
						<option value="02" selected>02 - Febrero</option>
					<?php 
					}
					else
					{
					?>
						<option value="02">02 - Febrero</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '03')
					{
					?>
						<option value="03" selected>03 - Marzo</option>
					<?php 
					}
					else
					{
					?>
						<option value="03">03 - Marzo</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '04')
					{
					?>
						<option value="04" selected>04 - Abril</option>
					<?php 
					}
					else
					{
					?>
						<option value="04">04 - Abril</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '05')
					{
					?>
						<option value="05" selected>05 - Mayo</option>
					<?php 
					}
					else
					{
					?>
						<option value="05">05 - Mayo</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '06')
					{
					?>
						<option value="06" selected>06 - Junio</option>
					<?php 
					}
					else
					{
					?>
						<option value="06">06 - Junio</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '07')
					{
					?>
						<option value="07" selected>07 - Julio</option>
					<?php 
					}
					else
					{
					?>
						<option value="07">07 - Julio</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '08')
					{
					?>
						<option value="08" selected>08 - Agosto</option>
					<?php 
					}
					else
					{
					?>
						<option value="08">08 - Agosto</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '09')
					{
					?>
						<option value="09" selected>09 - Septiembre</option>
					<?php 
					}
					else
					{
					?>
						<option value="09">09 - Septiembre</option>
					<?php 
					}
				
					if($informacionGlobal_meses == '10')
					{
					?>
						<option value="10" selected>10 - Octubre</option>
					<?php 
					}
					else
					{
					?>
						<option value="10">10 - Octubre</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '11')
					{
					?>
						<option value="11" selected>11 - Noviembre</option>
					<?php 
					}
					else
					{
					?>
						<option value="11">11 - Noviembre</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '12')
					{
					?>
						<option value="12" selected>12 - Diciembre</option>
					<?php 
					}
					else
					{
					?>
						<option value="12">12 - Diciembre</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '13')
					{
					?>
						<option value="13" selected>13 - Enero-Febrero</option>
					<?php 
					}
					else
					{
					?>
						<option value="13">13 - Enero-Febrero</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '14')
					{
					?>
						<option value="14" selected>14 - Marzo-Abril</option>
					<?php 
					}
					else
					{
					?>
						<option value="14">14 - Marzo-Abril</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '15')
					{
					?>
						<option value="15" selected>15 - Mayo-Junio</option>
					<?php 
					}
					else
					{
					?>
						<option value="15">15 - Mayo-Junio</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '16')
					{
					?>
						<option value="16" selected>16 - Julio-Agosto</option>
					<?php 
					}
					else
					{
					?>
						<option value="16">16 - Julio-Agosto</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '17')
					{
					?>
						<option value="17" selected>17 - Septiembre-Octubre</option>
					<?php 
					}
					else
					{
					?>
						<option value="17">17 - Septiembre-Octubre</option>
					<?php 
					}
					
					if($informacionGlobal_meses == '18')
					{
					?>
						<option value="18" selected>18 - Noviembre-Diciembre</option>
					<?php 
					}
					else
					{
					?>
						<option value="18">18 - Noviembre-Diciembre</option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Año':'* Year';?></font></label><br/>
				<select id="informacionGlobal_año" name="informacionGlobal_año" style="width:30%;">
				<?php 
					$informacionGlobal_año = $configuracion['informacionGlobal_año'];
					
					if($informacionGlobal_año == '0')
					{
					?>
						<option value="0" selected>Año en curso al momento de emitir del CFDI</option>
					<?php 
					}
					else
					{
					?>
						<option value="0">Año en curso al momento de emitir del CFDI</option>
					<?php 
					}
				?>
				</select>
			</div>
			<br/><br/>
			<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
			<br/><br/>
			<div>
				<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_enviar_configuracion_general"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
				<img id="cargandoConfiguracionGeneral" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
			</div>
		</form>
	<?php
}

function realvirtual_woocommerce_configuracion_productos()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<form id="realvirtual_woocommerce_configuracion_productos" method="post" style="background-color: #FFFFFF; padding: 20px;">
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Emisión de CFDI - Productos':'CFDI Issue - Products';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Establece la configuración para la emisión de CFDI.':'Set the configuration for the CFDI Issue.';?></font></label>
		<br/><br/>
		<div>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Clave Servicio':'* Service Code';?></font></label><br/>
			<input type="text" id="clave_servicio" style="width:10%; text-transform: uppercase;" name="clave_servicio" value="<?php echo esc_html($configuracion['clave_servicio']); ?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los productos de un CFDI tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>clave_servicio</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the products of a CFDI will have the value you enter. This field is required.<br/>Add an attribute with the <b>clave_servicio</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
			<br/>
			<br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Clave Unidad':'* Unit Code';?></font></label><br/>
			<input type="text" id="clave_unidad" style="width:10%; text-transform: uppercase;" name="clave_unidad" value="<?php echo esc_html($configuracion['clave_unidad']); ?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los productos de un CFDI tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>clave_unidad</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the products of a CFDI will have the value you enter. This field is required.<br/>Add an attribute with the <b>clave_unidad</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Unidad de Medida': 'Unit of Measurement';?></font></label><br/>
			<input type="text" id="unidad_medida" name="unidad_medida" style="width:10%;" value="<?php echo esc_html($configuracion['unidad_medida']); ?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los productos de un CFDI tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>unidad_medida</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the products of a CFDI will have the value you enter. This field is required.<br/>Add an attribute with the <b>unidad_medida</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Clave de Identificación':'Identification Code';?></font></label><br/>
			<input type="text" id="clave_producto" name="clave_producto" style="width:10%;" value="<?php echo esc_html($configuracion['clave_producto']); ?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los productos de un CFDI tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>clave_identificacion</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the products of a CFDI will have the value you enter. This field is required.<br/>Add an attribute with the <b>clave_identificacion</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Objeto de Impuesto':'* Tax Object';?></font></label><br/>
			<select id="objeto_imp_producto" name="objeto_imp_producto" style="width:20%">
				<?php 
					$objeto_imp_producto = $configuracion['objeto_imp_producto'];
					
					if($objeto_imp_producto == '01')
					{
					?>
						<option value="01" selected>01 - No objeto de impuesto</option>
					<?php 
					}
					else
					{
					?>
						<option value="01">01 - No objeto de impuesto</option>
					<?php 
					}
					
					if($objeto_imp_producto == '02')
					{
					?>
						<option value="02" selected>02 - Sí objeto de impuesto</option>
					<?php 
					}
					else
					{
					?>
						<option value="02">02 - Sí objeto de impuesto</option>
					<?php 
					}
					
					if($objeto_imp_producto == '03')
					{
					?>
						<option value="03" selected>03 - Sí objeto del impuesto y no obligado al desglose</option>
					<?php 
					}
					else
					{
					?>
						<option value="03">03 - Sí objeto del impuesto y no obligado al desglose</option>
					<?php 
					}
					
					if($objeto_imp_producto == '04')
					{
					?>
						<option value="04" selected>04 - Sí objeto del impuesto y no causa impuesto</option>
					<?php 
					}
					else
					{
					?>
						<option value="04">04 - Sí objeto del impuesto y no causa impuesto</option>
					<?php 
					}
				?>
			</select>
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los productos de un CFDI 4.0 tendrán por defecto el valor que ingreses. Este campo es obligatorio para CFDI 4.0.<br/>Agrega un atributo con el Slug/Referencia <b>objeto_impuesto</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente. Sólo se aceptan los valores 01, 02 y 03 correspondientes a las claves de este catálogo.':'By default, the products of a CFDI 4.0 will have the value you enter. This field is required for CFDI 4.0.<br/>Add an attribute with the <b>objeto_impuesto</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically. Only the values ​​01, 02 and 03 corresponding to the keys of this catalog are accepted.';?></span>
			</div>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No. Pedimento (Información Aduanera)':'No. Requirement (Customs Information)';?></font></label><br/>
			<input type="text" id="numero_pedimento" name="numero_pedimento" style="width:20%;" value="<?php echo esc_html($configuracion['numero_pedimento']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'No. Pedimento en formato 00  00  0000  0000000':'No. Requirement in the format 00  00  0000  0000000';?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los productos de un CFDI tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>numero_pedimento</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the products of a CFDI will have the value you enter. This field is required.<br/>Add an attribute with the <b>numero_pedimento</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
		</div>
		<br/><br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_enviar_configuracion_productos"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargandoConfiguracionProductos" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
	<?php
}
function realvirtual_woocommerce_configuracion_envios()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<form id="realvirtual_woocommerce_configuracion_envios" method="post" style="background-color: #FFFFFF; padding: 20px;">
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Emisión de CFDI - Envío (Shipping)':'CFDI Issue - Shipping';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Establece la configuración para la emisión de CFDI.':'Set the configuration for the CFDI Issue.';?></font></label>
			<br/><br/>
			<div>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* ¿Utilizas conceptos de envío en tus pedidos?':'* Do you use shipping concepts in your orders?';?></font></label><br/>
			<select id="config_principal_shipping" name="config_principal_shipping" style="width:30%">
			<?php 
				$config_principal_shipping = $configuracion['config_principal_shipping'];
				
				if($config_principal_shipping == '0')
				{
				?>
					<option value="0" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No (No se incluirán los conceptos de envío como conceptos del CFDI)':'No (Shipping concepts will not be included as CFDI concepts)';?></option>
				<?php 
				}
				else
				{
				?>
					<option value="0"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No (No se incluirán los conceptos de envío como conceptos del CFDI)':'No (Shipping concepts will not be included as CFDI concepts)';?></option>
				<?php 
				}
				
				if($config_principal_shipping == '1')
				{
				?>
					<option value="1" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí (Se incluirán los conceptos de envío como conceptos del CFDI)':'Yes (Shipping concepts will be included as CFDI concepts)';?></option>
				<?php 
				}
				else
				{
				?>
					<option value="1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí (Se incluirán los conceptos de envío como conceptos del CFDI)':'Yes (Shipping concepts will be included as CFDI concepts)';?></option>
				<?php 
				}
			?>
			</select>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Clave Servicio':'* Service Code';?></font></label><br/>
			<input type="text" id="clave_servicio_shipping" style="width:10%; text-transform: uppercase;" name="clave_servicio_shipping" value="<?php echo esc_html($configuracion['clave_servicio_shipping']); ?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los conceptos de envío (shipping) serán productos de un CFDI y tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>clave_servicio_shipping</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the shipping concepts will be products of a CFDI and will have the value you enter. This field is required.<br/>Add an attribute with the <b>clave_servicio_shipping</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
			<br/>
			<br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Clave Unidad':'* Unit Code';?></font></label><br/>
			<input type="text" id="clave_unidad_shipping" style="width:10%; text-transform: uppercase;" name="clave_unidad_shipping" value="<?php echo esc_html($configuracion['clave_unidad_shipping']); ?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los conceptos de envío (shipping) serán productos de un CFDI y tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>clave_unidad_shipping</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the shipping concepts will be products of a CFDI and will have the value you enter. This field is required.<br/>Add an attribute with the <b>clave_unidad_shipping</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Unidad de Medida': 'Unit of Measurement';?></font></label><br/>
			<input type="text" id="unidad_medida_shipping" name="unidad_medida_shipping" style="width:10%;" value="<?php echo esc_html($configuracion['unidad_medida_shipping']); ?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los conceptos de envío (shipping) serán productos de un CFDI y tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>unidad_medida_shipping</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the shipping concepts will be products of a CFDI and will have the value you enter. This field is required.<br/>Add an attribute with the <b>unidad_medida_shipping</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Clave de Identificación':'Identification Code';?></font></label><br/>
			<input type="text" id="clave_producto_shipping" name="clave_producto_shipping" style="width:10%;" value="<?php echo esc_html($configuracion['clave_producto_shipping']); ?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los conceptos de envío (shipping) serán productos de un CFDI y tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>clave_identificacion_shipping</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the shipping concepts will be products of a CFDI and will have the value you enter. This field is required.<br/>Add an attribute with the <b>clave_identificacion_shipping</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Objeto de Impuesto':'* Tax Object';?></font></label><br/>
				<select id="objeto_imp_shipping" name="objeto_imp_shipping" style="width:20%">
				<?php 
					$objeto_imp_shipping = $configuracion['objeto_imp_shipping'];
					
					if($objeto_imp_shipping == '01')
					{
					?>
						<option value="01" selected>01 - No objeto de impuesto</option>
					<?php 
					}
					else
					{
					?>
						<option value="01">01 - No objeto de impuesto</option>
					<?php 
					}
					
					if($objeto_imp_shipping == '02')
					{
					?>
						<option value="02" selected>02 - Sí objeto de impuesto</option>
					<?php 
					}
					else
					{
					?>
						<option value="02">02 - Sí objeto de impuesto</option>
					<?php 
					}
					
					if($objeto_imp_shipping == '03')
					{
					?>
						<option value="03" selected>03 - Sí objeto del impuesto y no obligado al desglose</option>
					<?php 
					}
					else
					{
					?>
						<option value="03">03 - Sí objeto del impuesto y no obligado al desglose</option>
					<?php 
					}
					
					if($objeto_imp_shipping == '04')
					{
					?>
						<option value="04" selected>04 - Sí objeto del impuesto y no causa impuesto</option>
					<?php 
					}
					else
					{
					?>
						<option value="04">04 - Sí objeto del impuesto y no causa impuesto</option>
					<?php 
					}
				?>
			</select>
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Para CFDI 4.0 los conceptos de envío (shipping) serán productos de un CFDI y tendrán por defecto el valor que ingreses. Este campo es obligatorio para CFDI 4.0.<br/>Agrega un atributo con el Slug/Referencia <b>objeto_impuesto_shipping</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente. Sólo se aceptan los valores 01, 02 y 03 correspondientes a las claves de este catálogo.':'For CFDI 4.0, shipping concepts will be products of a CFDI and will have the value you enter by default. This field is required for CFDI 4.0.<br/>Add an attribute with the <b>objeto_impuesto_shipping</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically. Only the values ​​01, 02 and 03 corresponding to the keys of this catalog are accepted.';?></span>
			</div>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No. Pedimento (Información Aduanera)':'No. Requirement (Customs Information)';?></font></label><br/>
			<input type="text" id="numero_pedimento_shipping" name="numero_pedimento_shipping" style="width:20%;" value="<?php echo esc_html($configuracion['numero_pedimento_shipping']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'No. Pedimento en formato 00  00  0000  0000000':'No. Requirement in the format 00  00  0000  0000000';?>">
			<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
			  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Los conceptos de envío (shipping) serán productos de un CFDI y tendrán por defecto el valor que ingreses. Este campo es obligatorio.<br/>Agrega un atributo con el Slug/Referencia <b>numero_pedimento_shipping</b> en <b>Productos > Atributos</b> de Wordpress<br/>y añádelo a cada uno de tus productos especificando un valor para que el plugin lo utilice automáticamente.':'By default, the shipping concepts will be products of a CFDI and will have the value you enter. This field is required.<br/>Add an attribute with the <b>numero_pedimento_shipping</b> Slug/Reference in <b>Products > Attributes</b> of Wordpress<br/>and add it to each of your products specifying a value for the plugin to use automatically.';?></span>
			</div>
		</div>
		<br/><br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_enviar_configuracion_envios"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargandoConfiguracionEnvios" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
	<?php
}
function realvirtual_woocommerce_configuracion_reglasModuloClientes()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<form id="realvirtual_woocommerce_configuracion_reglasModuloClientes" method="post" style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Reglas y condiciones del módulo de facturación para clientes':'Rules and conditions of the customer invoicing module';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Define las reglas y condiciones del módulo de facturación que utilizan tus clientes.':'Define the rules and conditions of the invoicing module that your customers use.';?></font></label>
			<br/><br/>
			<div>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Estado del pedido para emitir CFDI':'* Status order to issue CFDI';?></font></label><br/>
				<select id="estado_orden" name="estado_orden" style="width:20%;">
				<?php 
					$estado_orden = $configuracion['estado_orden'];
					
					if($estado_orden == 'no-especificado')
					{
					?>
						<option value="no-especificado" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No especificado (no se permitirá la emisión de CFDI)':'Not specified (CFDI issue will not be allowed)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="no-especificado"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No especificado (no se permitirá la emisión de CFDI)':'Not specified (CFDI issue will not be allowed)';?></option>
					<?php 
					}
					
					if($estado_orden == 'cualquier-estado')
					{
					?>
						<option value="cualquier-estado" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cualquier estado':'Any state';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="cualquier-estado"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cualquier estado':'Any state';?></option>
					<?php 
					}
					
					if($estado_orden == 'cualquier-estado-excepto')
					{
					?>
						<option value="cualquier-estado-excepto" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Todos excepto Pendiente de pago, Cancelado, Reembolsado y Fallido':'All except Pending payment, Canceled, Refunded and Failed';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="cualquier-estado-excepto"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Todos excepto Pendiente de pago, Cancelado, Reembolsado y Fallido':'All except Pending payment, Canceled, Refunded and Failed';?></option>
					<?php 
					}
					
					if($estado_orden == 'processing')
					{
					?>
						<option value="processing" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando (recomendado)':'Processing (recommended)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="processing"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando (recomendado)':'Processing (recommended)';?></option>
					<?php 
					}
					
					if($estado_orden == 'completed')
					{
					?>
						<option value="completed" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Completado':'Completed';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="completed"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Completado':'Completed';?></option>
					<?php 
					}
					
					if($estado_orden == 'processing-completed')
					{
					?>
						<option value="processing-completed" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando o Completado':'Processing or Completed';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="processing-completed"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando o Completado':'Processing or Completed';?></option>
					<?php 
					}
					
					if($estado_orden == 'personalizado-1')
					{
					?>
						<option value="personalizado-1" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 1 (Slug personalizado-1)':'Personalized State 1 (Slug personalizado-1)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="personalizado-1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 1 (Slug personalizado-1)':'Personalized State 1 (Slug personalizado-1)';?></option>
					<?php 
					}
					
					if($estado_orden == 'personalizado-2')
					{
					?>
						<option value="personalizado-2" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 2 (Slug personalizado-2)':'Personalized State 2 (Slug personalizado-2)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="personalizado-2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 2 (Slug personalizado-2)':'Personalized State 2 (Slug personalizado-2)';?></option>
					<?php 
					}
					
					if($estado_orden == 'personalizado-3')
					{
					?>
						<option value="personalizado-3" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 3 (Slug personalizado-3)':'Personalized State 3 (Slug personalizado-3)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="personalizado-3"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 3 (Slug personalizado-3)':'Personalized State 3 (Slug personalizado-3)';?></option>
					<?php 
					}
				?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si seleccionas un estado personalizado, éste debes agregarlo previamente en WooCommerce<br/>sin importar el nombre pero con el Slug indicado para que pueda funcionar, ya que el plugin<br/>sólo trabaja con el Slug y el nombre es indiferente y nunca es utilizado.':'If you select a custom status, you must first add it in WooCommerce<br/>regardless of the name but with the Slug indicated so that it can work,<br/>since the plugin only works with the Slug and the name is indifferent and is never used.';?></span>
				</div>
				<br/><br/>
			</div>
			<div>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Estado del pedido para emitir CFDI tras cancelación':'* Status order to issue CFDI after cancellation';?></font></label><br/>
				<select id="estado_orden_refacturacion" name="estado_orden_refacturacion" style="width:30%;">
				<?php 
					$estado_orden_refacturacion = $configuracion['estado_orden_refacturacion'];
					
					if($estado_orden_refacturacion == 'no-especificado')
					{
					?>
						<option value="no-especificado" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No especificado (no se permitirá la emisión de CFDI)':'Not specified (CFDI issue will not be allowed)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="no-especificado"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No especificado (no se permitirá la emisión de CFDI)':'Not specified (CFDI issue will not be allowed)';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'cualquier-estado')
					{
					?>
						<option value="cualquier-estado" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cualquier estado':'Any state';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="cualquier-estado"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cualquier estado':'Any state';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'pending')
					{
					?>
						<option value="pending" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Pendiente de pago':'Pending';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="pending"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Pendiente de pago':'Pending';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'processing')
					{
					?>
						<option value="processing" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando':'Processing';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="processing"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando':'Processing';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'on-hold')
					{
					?>
						<option value="on-hold" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'En espera':'On hold';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="on-hold"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'En espera':'On hold';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'completed')
					{
					?>
						<option value="completed" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Completado':'Completed';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="completed"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Completado':'Completed';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'canceled')
					{
					?>
						<option value="canceled" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cancelado':'Canceled';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="canceled"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cancelado':'Canceled';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'refunded')
					{
					?>
						<option value="refunded" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Reembolsado':'Refunded';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="refunded"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Reembolsado':'Refunded';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'failed')
					{
					?>
						<option value="failed" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Fallido':'Failed';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="failed"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Fallido':'Failed';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'personalizado-1')
					{
					?>
						<option value="personalizado-1" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 1 (Slug personalizado-1)':'Personalized State 1 (Slug personalizado-1)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="personalizado-1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 1 (Slug personalizado-1)':'Personalized State 1 (Slug personalizado-1)';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'personalizado-2')
					{
					?>
						<option value="personalizado-2" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 2 (Slug personalizado-2)':'Personalized State 2 (Slug personalizado-2)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="personalizado-2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 2 (Slug personalizado-2)':'Personalized State 2 (Slug personalizado-2)';?></option>
					<?php 
					}
					
					if($estado_orden_refacturacion == 'personalizado-3')
					{
					?>
						<option value="personalizado-3" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 3 (Slug personalizado-3)':'Personalized State 3 (Slug personalizado-3)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="personalizado-3"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 3 (Slug personalizado-3)':'Personalized State 3 (Slug personalizado-3)';?></option>
					<?php 
					}
				?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si cancelas todos los CFDI emitidos de un pedido, puedes permitir a tu cliente volver a<br/>emitir el CFDI si el estado del pedido coincide con el que establezcas.<br/>Si seleccionas un estado personalizado, éste debes agregarlo previamente en WooCommerce<br/>sin importar el nombre pero con el Slug indicado para que pueda funcionar, ya que el plugin<br/>sólo trabaja con el Slug y el nombre es indiferente y nunca es utilizado.':'If you cancel all CFDIs generated from an order, you can allow your customer to return to<br/>issue the CFDI if the order status matches the status you set.<br/>If you select a custom status, you must first add it in WooCommerce<br/>regardless of the name but with the Slug indicated so that it can work,<br/>since the plugin only works with the Slug and the name is indifferent and is never used.';?></span>
				</div>
				<br/><br/>
			</div>
			<div>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Facturación de pedidos pagados fuera del mes actual':'* Billing for orders paid outside the current month';?></font></label><br/>
				<select id="pedido_mes_actual" name="pedido_mes_actual" style="width:50%;">
				<?php 
					$pedido_mes_actual = $configuracion['pedido_mes_actual'];
					
					if($pedido_mes_actual == 'si')
					{
					?>
						<option value="si" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Facturar pedidos pagados en cualquier fecha':'Invoice orders paid on any date';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="si"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Facturar pedidos pagados en cualquier fecha':'Invoice orders paid on any date';?></option>
					<?php 
					}
					
					if($pedido_mes_actual == 'no')
					{
					?>
						<option value="no" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Solo facturar pedidos pagados dentro del mes actual':'Only invoice orders paid within the current month';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="no"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Solo facturar pedidos pagados dentro del mes actual':'Only invoice orders paid within the current month';?></option>
					<?php 
					}
				?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Esta validación sólo aplica para pedidos que tengan el estado <b>Completado</b>.':'This validation only applies to orders that have the status <b>Completed</b>.';?></span>
				</div>
				<br/><br/>
			</div>
			<div>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Uso CFDI por defecto para el Cliente': '* CFDI Use by default for the Customer';?></font></label><br/>
				<select id="uso_cfdi" name="uso_cfdi" style="width:30%;">
				<?php 
					$uso_cfdi = $configuracion['uso_cfdi'];
					
					if($uso_cfdi == 'G01')
					{
					?>
						<option value="G01" selected>G01 - Adquisición de mercancías</option>
					<?php 
					}
					else
					{
					?>
						<option value="G01">G01 - Adquisición de mercancías</option>
					<?php 
					}
					
					if($uso_cfdi == 'G02')
					{
					?>
						<option value="G02" selected>G02 - Devoluciones, descuentos o bonificaciones</option>
					<?php 
					}
					else
					{
					?>
						<option value="G02">G02 - Devoluciones, descuentos o bonificaciones</option>
					<?php 
					}
					
					if($uso_cfdi == 'G03')
					{
					?>
						<option value="G03" selected>G03 - Gastos en general</option>
					<?php 
					}
					else
					{
					?>
						<option value="G03">G03 - Gastos en general</option>
					<?php 
					}
					
					if($uso_cfdi == 'I01')
					{
					?>
						<option value="I01" selected>I01 - Construcciones</option>
					<?php 
					}
					else
					{
					?>
						<option value="I01">I01 - Construcciones</option>
					<?php 
					}
					
					if($uso_cfdi == 'I02')
					{
					?>
						<option value="I02" selected>I02 - Mobiliario y equipo de oficina por inversiones</option>
					<?php 
					}
					else
					{
					?>
						<option value="I02">I02 - Mobiliario y equipo de oficina por inversiones</option>
					<?php 
					}
					
					if($uso_cfdi == 'I03')
					{
					?>
						<option value="I03" selected>I03 - Equipo de transporte</option>
					<?php 
					}
					else
					{
					?>
						<option value="I03">I03 - Equipo de transporte</option>
					<?php 
					}
					
					if($uso_cfdi == 'I04')
					{
					?>
						<option value="I04" selected>I04 - Equipo de cómputo y accesorios</option>
					<?php 
					}
					else
					{
					?>
						<option value="I04">I04 - Equipo de cómputo y accesorios</option>
					<?php 
					}
					
					if($uso_cfdi == 'I05')
					{
					?>
						<option value="I05" selected>I05 - Dados, troqueles, moldes, matrices y herramental</option>
					<?php 
					}
					else
					{
					?>
						<option value="I05">I05 - Dados, troqueles, moldes, matrices y herramental</option>
					<?php 
					}
					
					if($uso_cfdi == 'I06')
					{
					?>
						<option value="I06" selected>I06 - Comunicaciones telefónicas</option>
					<?php 
					}
					else
					{
					?>
						<option value="I06">I06 - Comunicaciones telefónicas</option>
					<?php 
					}
					
					if($uso_cfdi == 'I07')
					{
					?>
						<option value="I07" selected>I07 - Comunicaciones satelitales</option>
					<?php 
					}
					else
					{
					?>
						<option value="I07">I07 - Comunicaciones satelitales</option>
					<?php 
					}
					
					if($uso_cfdi == 'I08')
					{
					?>
						<option value="I08" selected>I08 - Otra maquinaria y equipo</option>
					<?php 
					}
					else
					{
					?>
						<option value="I08">I08 - Otra maquinaria y equipo</option>
					<?php 
					}
					
					if($uso_cfdi == 'D01')
					{
					?>
						<option value="D01" selected>D01 - Honorarios médicos, dentales y gastos hospitalarios</option>
					<?php 
					}
					else
					{
					?>
						<option value="D01">D01 - Honorarios médicos, dentales y gastos hospitalarios</option>
					<?php 
					}
					
					if($uso_cfdi == 'D02')
					{
					?>
						<option value="D02" selected>D02 - Gastos médicos por incapacidad o discapacidad</option>
					<?php 
					}
					else
					{
					?>
						<option value="D02">D02 - Gastos médicos por incapacidad o discapacidad</option>
					<?php 
					}
					
					if($uso_cfdi == 'D03')
					{
					?>
						<option value="D03" selected>D03 - Gastos funerales</option>
					<?php 
					}
					else
					{
					?>
						<option value="D03">D03 - Gastos funerales</option>
					<?php 
					}
					
					if($uso_cfdi == 'D04')
					{
					?>
						<option value="D04" selected>D04 - Donativos</option>
					<?php 
					}
					else
					{
					?>
						<option value="D04">D04 - Donativos</option>
					<?php 
					}
					
					if($uso_cfdi == 'D05')
					{
					?>
						<option value="D05" selected>D05 - Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>
					<?php 
					}
					else
					{
					?>
						<option value="D05">D05 - Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>
					<?php 
					}
					
					if($uso_cfdi == 'D06')
					{
					?>
						<option value="D06" selected>D06 - Aportaciones voluntarias al SAR</option>
					<?php 
					}
					else
					{
					?>
						<option value="D06">D06 - Aportaciones voluntarias al SAR</option>
					<?php 
					}
					
					if($uso_cfdi == 'D07')
					{
					?>
						<option value="D07" selected>D07 - Primas por seguros de gastos médicos</option>
					<?php 
					}
					else
					{
					?>
						<option value="D07">D07 - Primas por seguros de gastos médicos</option>
					<?php 
					}
					
					if($uso_cfdi == 'D08')
					{
					?>
						<option value="D08" selected>D08 - Gastos de transportación escolar obligatoria</option>
					<?php 
					}
					else
					{
					?>
						<option value="D08">D08 - Gastos de transportación escolar obligatoria</option>
					<?php 
					}
					
					if($uso_cfdi == 'D09')
					{
					?>
						<option value="D09" selected>D09 - Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones</option>
					<?php 
					}
					else
					{
					?>
						<option value="D09">D09 - Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones</option>
					<?php 
					}
					
					if($uso_cfdi == 'D10')
					{
					?>
						<option value="D10" selected>D10 - Pagos por servicios educativos (colegiaturas)</option>
					<?php 
					}
					else
					{
					?>
						<option value="D10">D10 - Pagos por servicios educativos (colegiaturas)</option>
					<?php 
					}
					
					/*if($uso_cfdi == 'P01')
					{
					?>
						<option value="P01" selected>P01 - Por definir (Sólo CFDI 3.3)</option>
					<?php 
					}
					else
					{
					?>
						<option value="P01">P01 - Por definir (Sólo CFDI 3.3)</option>
					<?php 
					}*/
					
					if($uso_cfdi == 'S01')
					{
					?>
						<option value="S01" selected>S01 - Sin efectos fiscales (Sólo CFDI 4.0)</option>
					<?php 
					}
					else
					{
					?>
						<option value="S01">S01 - Sin efectos fiscales (Sólo CFDI 4.0)</option>
					<?php 
					}
					
					if($uso_cfdi == 'CP01')
					{
					?>
						<option value="CP01" selected>CP01 - Pagos (Sólo CFDI 4.0)</option>
					<?php 
					}
					else
					{
					?>
						<option value="CP01">CP01 - Pagos (Sólo CFDI 4.0)</option>
					<?php 
					}
					
					if($uso_cfdi == 'CN01')
					{
					?>
						<option value="CN01" selected>CN01 - Nómina (Sólo CFDI 4.0)</option>
					<?php 
					}
					else
					{
					?>
						<option value="CN01">CN01 - Nómina (Sólo CFDI 4.0)</option>
					<?php 
					}
				?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si el cliente que va a facturar su pedido registró previamente sus datos fiscales en su cuenta de usuario de la tienda virtual, entonces el Uso CFDI por defecto será el que el cliente registró.':'If the customer who is going to bill their order previously registered their tax data in their virtual store user account, then the CFDI Use by default will be the one that the customer registered.';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Permitir al cliente seleccionar el Uso CFDI':'* Allow customer to select the CFDI Use';?></font></label><br/>
				<select id="uso_cfdi_seleccionar" name="uso_cfdi_seleccionar" style="width:30%;">
				<?php 
					$uso_cfdi_seleccionar = $configuracion['uso_cfdi_seleccionar'];
					
					if($uso_cfdi_seleccionar == 'si')
					{
					?>
						<option value="si" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí permitir (recomendado)':'Yes allow  (recommended)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="si"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí permitir (recomendado)':'Yes allow  (recommended)';?></option>
					<?php 
					}
					
					if($uso_cfdi_seleccionar == 'no')
					{
					?>
						<option value="no" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir, pero mostrar este campo en pantalla':'Do not allow, but show this field on screen';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="no"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir, pero mostrar este campo en pantalla':'Do not allow, but show this field on screen';?></option>
					<?php 
					}
					
					if($uso_cfdi_seleccionar == 'noOcultar')
					{
					?>
						<option value="noOcultar" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir y no mostrar este campo en pantalla':'Do not allow and do not show this field on screen';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="noOcultar"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir y no mostrar este campo en pantalla':'Do not allow and do not show this field on screen';?></option>
					<?php 
					}
				?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si el cliente que va a facturar su pedido registró previamente sus datos fiscales en su cuenta de usuario de la tienda virtual, entonces el Uso CFDI por defecto será el que el cliente registró.':'If the customer who is going to bill their order previously registered their tax data in their virtual store user account, then the CFDI Use by default will be the one that the customer registered.';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Forma de pago por defecto':'* Default Payment way';?></font></label><br/>
				<select id="metodo_pago" name="metodo_pago" style="width:30%;">
				<?php 
					$metodo_pago = $configuracion['metodo_pago'];
					
					if($metodo_pago == '01')
					{
					?>
						<option value="01" selected>01 - Efectivo</option>
					<?php 
					}
					else
					{
					?>
						<option value="01">01 - Efectivo</option>
					<?php 
					}
					
					if($metodo_pago == '02')
					{
					?>
						<option value="02" selected>02 - Cheque nominativo</option>
					<?php 
					}
					else
					{
					?>
						<option value="02">02 - Cheque nominativo</option>
					<?php 
					}
					
					if($metodo_pago == '03')
					{
					?>
						<option value="03" selected>03 - Transferencia electrónica de fondos</option>
					<?php 
					}
					else
					{
					?>
						<option value="03">03 - Transferencia electrónica de fondos</option>
					<?php 
					}
					
					if($metodo_pago == '04')
					{
					?>
						<option value="04" selected>04 - Tarjeta de crédito</option>
					<?php 
					}
					else
					{
					?>
						<option value="04">04 - Tarjeta de crédito</option>
					<?php 
					}
					
					if($metodo_pago == '05')
					{
					?>
						<option value="05" selected>05 - Monedero electrónico</option>
					<?php 
					}
					else
					{
					?>
						<option value="05">05 - Monedero electrónico</option>
					<?php 
					}
					
					if($metodo_pago == '06')
					{
					?>
						<option value="06" selected>06 - Dinero electrónico</option>
					<?php 
					}
					else
					{
					?>
						<option value="06">06 - Dinero electrónico</option>
					<?php 
					}
					
					if($metodo_pago == '08')
					{
					?>
						<option value="08" selected>08 - Vales de despensa</option>
					<?php 
					}
					else
					{
					?>
						<option value="08">08 - Vales de despensa</option>
					<?php 
					}
					
					if($metodo_pago == '12')
					{
					?>
						<option value="12" selected>12 - Dación en pago</option>
					<?php 
					}
					else
					{
					?>
						<option value="12">12 - Dación en pago</option>
					<?php 
					}
					
					if($metodo_pago == '13')
					{
					?>
						<option value="13" selected>13 - Pago por subrogación</option>
					<?php 
					}
					else
					{
					?>
						<option value="13">13 - Pago por subrogación</option>
					<?php 
					}
					
					if($metodo_pago == '14')
					{
					?>
						<option value="14" selected>14 - Pago por consignación</option>
					<?php 
					}
					else
					{
					?>
						<option value="14">14 - Pago por consignación</option>
					<?php 
					}
					
					if($metodo_pago == '15')
					{
					?>
						<option value="15" selected>15 - Condonación</option>
					<?php 
					}
					else
					{
					?>
						<option value="15">15 - Condonación</option>
					<?php 
					}
					
					if($metodo_pago == '17')
					{
					?>
						<option value="17" selected>17 - Compensación</option>
					<?php 
					}
					else
					{
					?>
						<option value="17">17 - Compensación</option>
					<?php 
					}
					
					if($metodo_pago == '23')
					{
					?>
						<option value="23" selected>23 - Novación</option>
					<?php 
					}
					else
					{
					?>
						<option value="23">23 - Novación</option>
					<?php 
					}
					
					if($metodo_pago == '24')
					{
					?>
						<option value="24" selected>24 - Confusión</option>
					<?php 
					}
					else
					{
					?>
						<option value="24">24 - Confusión</option>
					<?php 
					}
					
					if($metodo_pago == '25')
					{
					?>
						<option value="25" selected>25 - Remisión de deuda</option>
					<?php 
					}
					else
					{
					?>
						<option value="25">25 - Remisión de deuda</option>
					<?php 
					}
					
					if($metodo_pago == '26')
					{
					?>
						<option value="26" selected>26 - Prescripción o caducidad</option>
					<?php 
					}
					else
					{
					?>
						<option value="26">26 - Prescripción o caducidad</option>
					<?php 
					}
					
					if($metodo_pago == '27')
					{
					?>
						<option value="27" selected>27 - A satisfacción del acreedor</option>
					<?php 
					}
					else
					{
					?>
						<option value="27">27 - A satisfacción del acreedor</option>
					<?php 
					}
					
					if($metodo_pago == '28')
					{
					?>
						<option value="28" selected>28 - Tarjeta de débito</option>
					<?php 
					}
					else
					{
					?>
						<option value="28">28 - Tarjeta de débito</option>
					<?php 
					}
					
					if($metodo_pago == '29')
					{
					?>
						<option value="29" selected>29 - Tarjeta de servicios</option>
					<?php 
					}
					else
					{
					?>
						<option value="29">29 - Tarjeta de servicios</option>
					<?php 
					}
					
					if($metodo_pago == '30')
					{
					?>
						<option value="30" selected>30 - Aplicación de anticipos</option>
					<?php 
					}
					else
					{
					?>
						<option value="30">30 - Aplicación de anticipos</option>
					<?php 
					}
					
					if($metodo_pago == '31')
					{
					?>
						<option value="31" selected>31 - Intermediario pagos</option>
					<?php 
					}
					else
					{
					?>
						<option value="31">31 - Intermediario pagos</option>
					<?php 
					}
					
					if($metodo_pago == '99')
					{
					?>
						<option value="99" selected>99 - Por definir</option>
					<?php 
					}
					else
					{
					?>
						<option value="99">99 - Por definir</option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Permitir al cliente seleccionar la forma de pago':'* Allow customer to select the payment way';?></font></label><br/>
				<select id="metodo_pago_seleccionar" name="metodo_pago_seleccionar" style="width:30%;">
				<?php 
					$metodo_pago_seleccionar = $configuracion['metodo_pago_seleccionar'];
					
					if($metodo_pago_seleccionar == 'no')
					{
					?>
						<option value="no" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir, pero mostrar este campo en pantalla (recomendado)':'Do not allow, but show this field on screen (recommended)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="no"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir, pero mostrar este campo en pantalla (recomendado)':'Do not allow, but show this field on screen (recommended)';?></option>
					<?php 
					}
					
					if($metodo_pago_seleccionar == 'noOcultar')
					{
					?>
						<option value="noOcultar" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir y no mostrar este campo en pantalla':'Do not allow and do not show this field on screen (recommended)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="noOcultar"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir y no mostrar este campo en pantalla':'Do not allow and do not show this field on screen (recommended)';?></option>
					<?php 
					}
					
					if($metodo_pago_seleccionar == 'si')
					{
					?>
						<option value="si" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí permitir':'Yes allow';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="si"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí permitir':'Yes allow';?></option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Método de pago por defecto':'* Default Payment Method';?></font></label><br/>
				<select id="metodo_pago33" name="metodo_pago33" style="width:30%;">
				<?php 
					$metodo_pago33 = $configuracion['metodo_pago33'];
					
					if($metodo_pago33 == 'PUE')
					{
					?>
						<option value="PUE" selected>PUE - Pago en una sola exhibición</option>
					<?php 
					}
					else
					{
					?>
						<option value="PUE">PUE - Pago en una sola exhibición</option>
					<?php 
					}
					
					if($metodo_pago33 == 'PPD')
					{
					?>
						<option value="PPD" selected>PPD - Pago en parcialidades o diferido</option>
					<?php 
					}
					else
					{
					?>
						<option value="PPD">PPD - Pago en parcialidades o diferido</option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Permitir al cliente seleccionar el método de pago':'* Allow customer to select the payment method';?></font></label><br/>
				<select id="metodo_pago_seleccionar33" name="metodo_pago_seleccionar33" style="width:30%;">
				<?php 
					$metodo_pago_seleccionar33 = $configuracion['metodo_pago_seleccionar33'];
					
					if($metodo_pago_seleccionar33 == 'no')
					{
					?>
						<option value="no" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir, pero mostrar este campo en pantalla (recomendado)':'Do not allow, but show this field on screen (recommended)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="no"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir, pero mostrar este campo en pantalla (recomendado)':'Do not allow, but show this field on screen (recommended)';?></option>
					<?php 
					}
					
					if($metodo_pago_seleccionar33 == 'noOcultar')
					{
					?>
						<option value="noOcultar" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir y no mostrar este campo en pantalla':'Do not allow and do not show this field on screen (recommended)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="noOcultar"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No permitir y no mostrar este campo en pantalla':'Do not allow and do not show this field on screen (recommended)';?></option>
					<?php 
					}
					
					if($metodo_pago_seleccionar33 == 'si')
					{
					?>
						<option value="si" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí permitir':'Yes allow';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="si"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí permitir':'Yes allow';?></option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Conceptos especiales de envío':'* Special shipping concepts';?></font></label><br/>
				<select id="conceptos_especiales_envio" name="conceptos_especiales_envio" style="width:60%;">
				<?php 
					$conceptos_especiales_envio = $configuracion['conceptos_especiales_envio'];
					
					if($conceptos_especiales_envio == 'no')
					{
					?>
						<option value="no" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No contemplar los envíos como conceptos en el CFDI cuando tengan valor de $0.00 (recomendado)':'Do not consider shipments as concepts in the CFDI when they have a value of $0.00 (recommended)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="no"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No contemplar los envíos como conceptos en el CFDI cuando tengan valor de $0.00 (recomendado)':'Do not consider shipments as concepts in the CFDI when they have a value of $0.00 (recommended)';?></option>
					<?php 
					}
					
					if($conceptos_especiales_envio == 'si')
					{
					?>
						<option value="si" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Contemplar los envíos como conceptos en el CFDI aunque tengan valor de $0.00 (no recomendado)':'Contemplate shipments as concepts in the CFDI although they have a value of $0.00 (not recommended)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="si"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Contemplar los envíos como conceptos en el CFDI aunque tengan valor de $0.00 (no recomendado)':'Contemplate shipments as concepts in the CFDI although they have a value of $0.00 (not recommended)';?></option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Título':'Title';?></font></label><br/>
				<input type="text" id="titulo" name="titulo" value="<?php echo esc_html($configuracion['titulo']); ?>" style="width:30%;">
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Texto descriptivo':'Descriptive text';?></font></label><br/>
				<input type="text" id="descripcion" name="descripcion" value="<?php echo esc_html($configuracion['descripcion']); ?>" style="width:30%;">
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Mostrar mensaje personalizado en pantalla al cliente cuando ocurra un error al emitir el CFDI':'* Show on-screen personalized message to the customer when an error occurs when generating the CFDI';?></font></label><br/>
				<select id="mostrarMensajeErrorCliente" name="mostrarMensajeErrorCliente" style="width:30%;">
				<?php 
					$mostrarMensajeErrorCliente = $configuracion['mostrarMensajeErrorCliente'];
					
					if($mostrarMensajeErrorCliente == 'si')
					{
					?>
						<option value="si" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí, mostrar mensaje personalizado':'Yes, show custom message';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="si"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí, mostrar mensaje personalizado':'Yes, show custom message';?></option>
					<?php 
					}
					
					if($mostrarMensajeErrorCliente == 'no')
					{
					?>
						<option value="no" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No, mostrar el error original':'No, show the original error';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="no"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No, mostrar el error original':'No, show the original error';?></option>
					<?php 
					}
				?>
				</select>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'E-mail a donde se enviará el error original detallado de emisión del CFDI cuando ocurra un error.':'E-mail a donde se enviará el error original detallado de emisión del CFDI cuando ocurra un error';?></font></label><br/>
				<input type="text" id="emailNotificacionErrorModuloClientes" name="emailNotificacionErrorModuloClientes" value="<?php echo esc_html($configuracion['emailNotificacionErrorModuloClientes']); ?>" style="width:30%;" />
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Mensaje personalizado en pantalla al cliente cuando ocurra un error al emitir el CFDI':'On-screen personalized message to the customer when an error occurs when generating the CFDI';?></font></label><br/>
				<input type="text" id="mensajeErrorCliente" name="mensajeErrorCliente" value="<?php if(esc_html($configuracion['mensajeErrorCliente']) == "") { echo "Estimado cliente, encontramos un error en la generación de esta factura, hemos notificado al administrador y se le dará seguimiento en breve."; } else { echo esc_html($configuracion['mensajeErrorCliente']); } ?>" style="width:60%;" />
			</div>
		<br/><br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_enviar_configuracion_reglasModuloClientes"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargandoConfiguracionReglasModuloClientes" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
	<?php
}
function realvirtual_woocommerce_configuracion_estiloModuloClientes()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<form id="realvirtual_woocommerce_configuracion_estiloModuloClientes" method="post" style="background-color: #FFFFFF; padding: 20px;">
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Aspecto visual del módulo de facturación para clientes':'Visual appearance of the customer invoicing module';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Personaliza la apariencia del módulo de facturación que utilizan tus clientes. Puedes seleccionar colores utilizando las paletas de colores o ingresando los códigos hexadecimales.':'Customize the appearance of the invoicing module that your customers use. You can select colors using color palettes or by entering hexadecimal codes.';?></font></label>
			<br/><br/>
			<div>
				<br/>
				<table width="100%">
					<tr>
						<td style="width:30%">
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color de fondo en encabezado':'Header background color';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_fondo_encabezado_hexadecimal" name="color_fondo_encabezado_hexadecimal" value="<?php echo esc_html($configuracion['color_fondo_encabezado']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_fondo_encabezado" name="color_fondo_encabezado" value="<?php echo esc_html($configuracion['color_fondo_encabezado']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color de texto en encabezado':'Header text color';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_texto_encabezado_hexadecimal" name="color_texto_encabezado_hexadecimal" value="<?php echo esc_html($configuracion['color_texto_encabezado']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_texto_encabezado" name="color_texto_encabezado" value="<?php echo esc_html($configuracion['color_texto_encabezado']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color de fondo en formulario':'Form background color';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_fondo_formulario_hexadecimal" name="color_fondo_formulario_hexadecimal" value="<?php echo esc_html($configuracion['color_fondo_formulario']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_fondo_formulario" name="color_fondo_formulario" value="<?php echo esc_html($configuracion['color_fondo_formulario']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color de texto en formulario':'Form text color';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_texto_formulario_hexadecimal" name="color_texto_formulario_hexadecimal" value="<?php echo esc_html($configuracion['color_texto_formulario']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_texto_formulario" name="color_texto_formulario" value="<?php echo esc_html($configuracion['color_texto_formulario']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color de texto en campos del formulario':'Text color in form fields';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_texto_controles_formulario_hexadecimal" name="color_texto_controles_formulario_hexadecimal" value="<?php echo esc_html($configuracion['color_texto_controles_formulario']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_texto_controles_formulario" name="color_texto_controles_formulario" value="<?php echo esc_html($configuracion['color_texto_controles_formulario']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color de botones':'Button color';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_boton_hexadecimal" name="color_boton_hexadecimal" value="<?php echo esc_html($configuracion['color_boton']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_boton" name="color_boton" value="<?php echo esc_html($configuracion['color_boton']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color de texto en botones':'Text color on buttons';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_texto_boton_hexadecimal" name="color_texto_boton_hexadecimal" value="<?php echo esc_html($configuracion['color_texto_boton']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_texto_boton" name="color_texto_boton" value="<?php echo esc_html($configuracion['color_texto_boton']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color del botón "Descargar Vista Previa"':'"Download Preview" button color';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_boton_hexadecimal_vistaprevia" name="color_boton_hexadecimal_vistaprevia" value="<?php echo esc_html($configuracion['color_boton_vistaprevia']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_boton_vistaprevia" name="color_boton_vistaprevia" value="<?php echo esc_html($configuracion['color_boton_vistaprevia']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color de texto del botón "Descargar Vista Previa"':'"Download Preview" button text color';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_texto_boton_hexadecimal_vistaprevia" name="color_texto_boton_hexadecimal_vistaprevia" value="<?php echo esc_html($configuracion['color_texto_boton_vistaprevia']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_texto_boton_vistaprevia" name="color_texto_boton_vistaprevia" value="<?php echo esc_html($configuracion['color_texto_boton_vistaprevia']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color del botón "Generar CFDI"':'"Generate CFDI" button color';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_boton_hexadecimal_generarcfdi" name="color_boton_hexadecimal_generarcfdi" value="<?php echo esc_html($configuracion['color_boton_generarcfdi']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_boton_generarcfdi" name="color_boton_generarcfdi" value="<?php echo esc_html($configuracion['color_boton_generarcfdi']); ?>">
							<br/><br/>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Color de texto del botón "Generar CFDI"':'"Generate CFDI" button text color';?></font></label><br/>
							<input type="text" style="width:60%;" id="color_texto_boton_hexadecimal_generarcfdi" name="color_texto_boton_hexadecimal_generarcfdi" value="<?php echo esc_html($configuracion['color_texto_boton_generarcfdi']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Valor hexadecimal con símbolo #':'Hexadecimal value with symbol #';?>">
							<input type="color" style="width:50px;height:20px; border:none;" id="color_texto_boton_generarcfdi" name="color_texto_boton_generarcfdi" value="<?php echo esc_html($configuracion['color_texto_boton_generarcfdi']); ?>">
						</td>
						<td style="width:30%">
							<center>
							<div style="width: 95%;background:#f9f9f9;">
							<br/><br/>
							<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'VISTA PREVIA DEL MÓDULO PARA CLIENTES':'PREVIEW OF THE MODULE FOR CUSTOMERS';?></b></font></label><br/>
							<br/>
							<font color="#848484" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Las siguientes pantallas son una vista previa de ejemplo del aspecto que tiene módulo de facturación para clientes con la configuración de estilos visuales definida en este apartado.':'The following screens are an example preview of what the customer billing module looks like with the configuration of visual styles defined in this section.';?></font>
							<br/><br/><br/>
							<font color="#848484" size="4"><label id="config_test_titulo"><?php echo $configuracion['titulo']; ?></label></font>
							<br/>
							<font color="#848484" size="2"><label id="config_test_descripcion"><?php echo $configuracion['descripcion']; ?></label></font>
							<br/><br/>
							<div style="width: 70%;">
								<div id="config_test_fondo_encabezado" style="background:<?php echo $configuracion['color_fondo_encabezado']; ?>; height: 80px; line-height: 20px; margin-bottom: 0px;">
									<br/>
									<font color="<?php echo $configuracion['color_texto_encabezado']?>" size="6"><label id="config_test_texto_encabezado1"><?php echo (($idiomaRVLFECFDI == 'ES') ? 'Paso 1/4':'Step 1/4'); ?></label></font>
									<br/>
									<font color="<?php echo $configuracion['color_texto_encabezado']?>" size="4"><label id="config_test_texto_encabezado2"><?php echo (($idiomaRVLFECFDI == 'ES') ? 'Identificar pedido':'Identify order'); ?></label></font>
								</div>
								
								<div id="config_test_fondo_formulario" style="background-color:<?php echo $configuracion['color_fondo_formulario']; ?>;">
									<br/>
									<p><font color="<?php echo $configuracion['color_texto_formulario'];?>" size="2"><label id="config_test_texto_formulario1"><?php echo (($idiomaRVLFECFDI == 'ES') ? 'Ingresa el número de pedido y el monto':'Enter the order number and the amount'); ?></label></font></p>
									<form>
										<div class="rowPaso1">
											<font color="<?php echo $configuracion['color_texto_formulario']; ?>"><label id="config_test_texto_formulario2"><?php echo (($idiomaRVLFECFDI == 'ES') ? '* Número de pedido':'* Order number'); ?></label></font>
											<input type="text" style="color: <?php echo $configuracion['color_texto_controles_formulario']; ?>;" id="config_test_texto_campo_formulario1" value="0000" />
											<br/>
											<font color="<?php echo $configuracion['color_texto_formulario']; ?>"><label id="config_test_texto_formulario3"><?php echo (($idiomaRVLFECFDI == 'ES') ? '* Monto':'* Amount'); ?></label></font>
											<input type="text" style="color: <?php echo $configuracion['color_texto_controles_formulario']; ?>;" id="config_test_texto_campo_formulario2" value="0.00" />
										</div>
										<br/>
										<div>
											<input id="config_test_fondo_boton" type="boton" style="height:30px; width:90px; font-size : 12px; margin: 0 auto; padding: 0; display: inline-block; line-height: 10px; text-align: center; background-color: <?php echo $configuracion['color_boton']; ?>; color:<?php echo $configuracion['color_texto_boton']; ?>;" class="boton" value="<?php echo (($idiomaRVLFECFDI == 'ES') ? 'Siguiente':'Next'); ?>" />
										</div>
									</form>
									<br/>
								</div>
								<br/><br/>
							</div>
							</div>
							</center>
						</td>
						<td style="width:30%">
							<center>
							<div style="width: 95%;background:#f9f9f9;">
							<br/><br/>
							<div style="width: 70%;">
								<div id="config_test_fondo_encabezado2" style="background:<?php echo $configuracion['color_fondo_encabezado']; ?>; height: 80px; line-height: 20px; margin-bottom: 0px;">
									<br/>
									<font color="<?php echo $configuracion['color_texto_encabezado']?>" size="6"><label id="config_test_texto_encabezado21"><?php echo (($idiomaRVLFECFDI == 'ES') ? 'Paso 3/4':'Step 3/4'); ?></label></font>
									<br/>
									<font color="<?php echo $configuracion['color_texto_encabezado']?>" size="4"><label id="config_test_texto_encabezado22"><?php echo (($idiomaRVLFECFDI == 'ES') ? 'Verificar datos del CFDI':'Check CFDI data'); ?></label></font>
								</div>
								
								<div id="config_test_fondo_formulario2" style="background-color:<?php echo $configuracion['color_fondo_formulario']; ?>;">
									<form>
										<table width="97%">
											<tr>
												<td>	
													<font color="<?php echo $configuracion['color_texto_formulario']?>"><b><div style="text-align: left;"><label id="config_test_texto_formulario21">No. Pedido</label> <font color="#d40000">99999</font></div></b></font>
												</td>
											</tr>
											<tr>
												<td style="vertical-align:top; width:50%; text-align: left;">
													<font color="<?php echo $configuracion['color_texto_formulario'];?>" size="2"><div><font size="3"><label id="config_test_texto_formulario22"><b>EMISOR</b><br/>XIA190128J61<br/>Empresa Emisora</label></font><br/><font size="2" color="#757575" style="font-style: italic;">correo@empresa.com</font></div></font>
												</td>
												<td style="vertical-align:top; width:50%; text-align: left;">
													<font color="<?php echo $configuracion['color_texto_formulario'];?>" size="2"><div><font size="3"><label id="config_test_texto_formulario23"><b>RECEPTOR</b><br/>XAXX010101000<br/>Cliente Receptor</label></font><br/><font size="2" color="#757575" style="font-style: italic;">correo@cliente.com</font></div></font>
												</td>
											</tr>
										</table>
										<table width="97%">
											<tr>
												<td style="vertical-align:top; width:60%; text-align: left;">
													<font color="<?php echo $configuracion['color_texto_formulario'];?>" size="2"><label id="config_test_texto_formulario24">Forma de pago</label></font>
													<font color="<?php echo $configuracion['color_texto_formulario'];?>" size="2">
														<select id="config_test_paso_3_metodos_pago" style="width: 90%; color: <?php echo $configuracion['color_texto_controles_formulario'];?>;" '.''.'>
															<option>Opción 1</option>
															<option>Opción 2</option>
														</select>
													</font>
												</td>
												<td style="vertical-align:top; width:40%; text-align: left;">
													<font color="<?php echo $configuracion['color_texto_formulario'];?>" size="2"><label id="config_test_texto_formulario25">Uso CFDI</label>
														<select id="config_test_paso_3_uso_cfdi" style="width: 90%; color: <?php echo $configuracion['color_texto_controles_formulario'];?>;" '.''.'>
															<option>Opción 1</option>
															<option>Opción 2</option>
														</select>
													</font>
												</td>
											</tr>
											<tr>
												<td style="vertical-align:top; width:60%; text-align: left;">
													<font color="<?php echo $configuracion['color_texto_formulario'];?>" size="2"><label id="config_test_texto_formulario26">Método de pago</label>
														<select id="config_test_paso_3_metodos_pago33" style="width: 90%; color: <?php echo $configuracion['color_texto_controles_formulario'];?>;" '.''.'>
															<option>Opción 1</option>
															<option>Opción 2</option>
														</select>
													</font>
												</td>
											</tr>
										</table>
										<font color="<?php echo $configuracion['color_texto_formulario'];?>" size="2">
											<div style="width: 97%;" id="config_test_texto_formulario27">
												<table border="1" style="border-collapse: collapse; background-color:#FFFFFF; border-color:#dedede;" width="100%">
													<thead>
														<tr>
															<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Articulo</b></td>
															<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Costo</b></td>
															<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Cantidad</b></td>
															<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Descuento</b></td>
															<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Total</b></td>
														</tr>
													</thead>
													<tbody>
														<tr>
														  <td style="text-align:left; border-color: #dedede; background-color: #fbfbfb">Ejemplo</td>
														  <td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">$1.00</td>
														  <td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">1.00</td>
														  <td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">$0.00</td>
														  <td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">$1.00</td>
														</tr>
													</tbody>
												</table>
											</div>
										</font>
										<font color="<?php echo $configuracion['color_texto_formulario'];?>" size="2">
											<div id="config_test_texto_formulario28">
												<table id="config_test_fondo_formulario3" border="0" style="background-color:<?php echo $configuracion['color_fondo_formulario'];?>;" width="100%">
													<tbody>
														<tr><td style="text-align:right; width:80%;"><b>SUBTOTAL</b></td><td style="text-align:right; width:20%;">$1.00</td></tr>
														<tr><td style="text-align:right; width:80%;"><b>DESCUENTO</b></td><td style="text-align:right; width:20%;">$0.00</td></tr>
														<tr><td style="text-align:right; width:80%;"><b>SUBTOTAL NETO</b></td><td style="text-align:right; width:20%;">$1.00</td></tr>
														<tr><td style="text-align:right; width:80%;"><b>IVA 16.000000%</b></td><td style="text-align:right; width:20%;">$0.16</td></tr>
														<tr><td style="text-align:right; width:80%;"><b>TOTAL</b></td><td style="text-align:right; width:20%;">$1.16</td></tr>
													</tbody>
												</table>
											</div>
										</font>
										<br/>
										<div>
											<input type="button" style="height:30px; width:90px; font-size : 12px; margin: 0 auto; padding: 0; display: inline-block; line-height: 10px; text-align: center; background-color: <?php echo $configuracion['color_boton'];?>; color:<?php echo $configuracion['color_texto_boton'];?>;" class="boton" id="config_test_fondo_boton21" value="Regresar" />
											<input type="button" style="height:30px; width:130px; font-size : 12px; margin: 0 auto; padding: 0; display: inline-block; line-height: 10px; text-align: center; background-color: <?php echo $configuracion['color_boton_vistaprevia'];?>; color:<?php echo $configuracion['color_texto_boton_vistaprevia'];?>;" class="boton" id="config_test_fondo_boton22" value="Descargar Vista Previa" />
											<input type="button" style="height:30px; width:100px; font-size : 12px; margin: 0 auto; padding: 0; display: inline-block; line-height: 10px; text-align: center; background-color: <?php echo $configuracion['color_boton_generarcfdi'];?>; color:<?php echo $configuracion['color_texto_boton_generarcfdi'];?>;" class="boton" id="config_test_fondo_boton23" value="Generar CFDI" />
										</div>
									</form>
									<br/>
								</div>
								<br/><br/>
							</div>
							</div>
							</center>
						</td>
					</tr>
				</table>
			</div>
		<br/><br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_enviar_configuracion_estiloModuloClientes"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargandoConfiguracionEstiloModuloClientes" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				/*var valor = document.getElementById('uso_cfdi_seleccionar').value;
				if(valor == 'noOcultar')
				{
					document.getElementById('config_test_texto_formulario25').style.visibility = 'hidden';
					document.getElementById('config_test_paso_3_uso_cfdi').style.visibility = 'hidden';
					document.getElementById('config_test_paso_3_uso_cfdi').disabled = true;
				}
				else if(valor == 'no')
				{
					document.getElementById('config_test_texto_formulario25').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_uso_cfdi').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_uso_cfdi').disabled = true;
				}
				else if(valor == 'si')
				{
					document.getElementById('config_test_texto_formulario25').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_uso_cfdi').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_uso_cfdi').disabled = false;
				}
				
				valor = document.getElementById('metodo_pago_seleccionar').value;
					
				if(valor == 'noOcultar')
				{
					document.getElementById('config_test_texto_formulario24').style.visibility = 'hidden';
					document.getElementById('config_test_paso_3_metodos_pago').style.visibility = 'hidden';
					document.getElementById('config_test_paso_3_metodos_pago').disabled = true;
				}
				else if(valor == 'no')
				{
					document.getElementById('config_test_texto_formulario24').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_metodos_pago').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_metodos_pago').disabled = true;
				}
				else if(valor == 'si')
				{
					document.getElementById('config_test_texto_formulario24').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_metodos_pago').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_metodos_pago').disabled = false;
				}
				
				valor = document.getElementById('metodo_pago_seleccionar33').value;
					
				if(valor == 'noOcultar')
				{
					document.getElementById('config_test_texto_formulario26').style.visibility = 'hidden';
					document.getElementById('config_test_paso_3_metodos_pago33').style.visibility = 'hidden';
					document.getElementById('config_test_paso_3_metodos_pago33').disabled = true;
				}
				else if(valor == 'no')
				{
					document.getElementById('config_test_texto_formulario26').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_metodos_pago33').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_metodos_pago33').disabled = true;
				}
				else if(valor == 'si')
				{
					document.getElementById('config_test_texto_formulario26').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_metodos_pago33').style.visibility = 'visible';
					document.getElementById('config_test_paso_3_metodos_pago33').disabled = false;
				}
					
				$("#uso_cfdi_seleccionar").change(function(){
					var valor = document.getElementById('uso_cfdi_seleccionar').value;
					
					if(valor == 'noOcultar')
					{
						document.getElementById('config_test_texto_formulario25').style.visibility = 'hidden';
						document.getElementById('config_test_paso_3_uso_cfdi').style.visibility = 'hidden';
						document.getElementById('config_test_paso_3_uso_cfdi').disabled = true;
					}
					else if(valor == 'no')
					{
						document.getElementById('config_test_texto_formulario25').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_uso_cfdi').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_uso_cfdi').disabled = true;
					}
					else if(valor == 'si')
					{
						document.getElementById('config_test_texto_formulario25').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_uso_cfdi').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_uso_cfdi').disabled = false;
					}
				});
				
				$("#metodo_pago_seleccionar").change(function(){
					var valor = document.getElementById('metodo_pago_seleccionar').value;
					
					if(valor == 'noOcultar')
					{
						document.getElementById('config_test_texto_formulario24').style.visibility = 'hidden';
						document.getElementById('config_test_paso_3_metodos_pago').style.visibility = 'hidden';
						document.getElementById('config_test_paso_3_metodos_pago').disabled = true;
					}
					else if(valor == 'no')
					{
						document.getElementById('config_test_texto_formulario24').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_metodos_pago').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_metodos_pago').disabled = true;
					}
					else if(valor == 'si')
					{
						document.getElementById('config_test_texto_formulario24').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_metodos_pago').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_metodos_pago').disabled = false;
					}
				});
				
				$("#metodo_pago_seleccionar33").change(function(){
					var valor = document.getElementById('metodo_pago_seleccionar33').value;
					
					if(valor == 'noOcultar')
					{
						document.getElementById('config_test_texto_formulario26').style.visibility = 'hidden';
						document.getElementById('config_test_paso_3_metodos_pago33').style.visibility = 'hidden';
						document.getElementById('config_test_paso_3_metodos_pago33').disabled = true;
					}
					else if(valor == 'no')
					{
						document.getElementById('config_test_texto_formulario26').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_metodos_pago33').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_metodos_pago33').disabled = true;
					}
					else if(valor == 'si')
					{
						document.getElementById('config_test_texto_formulario26').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_metodos_pago33').style.visibility = 'visible';
						document.getElementById('config_test_paso_3_metodos_pago33').disabled = false;
					}
				});
				
				$("#titulo").change(function(){
					var valor = document.getElementById('titulo').value;
					document.getElementById('config_test_titulo').innerHTML = valor;
				});
				
				$("#descripcion").change(function(){
					var valor = document.getElementById('descripcion').value;
					document.getElementById('config_test_descripcion').innerHTML = valor;
				});
				*/
				$("#color_fondo_encabezado_hexadecimal").change(function(){
					var valor = document.getElementById('color_fondo_encabezado_hexadecimal').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_fondo_encabezado_hexadecimal').value = '#FFFFFF';
						document.getElementById('color_fondo_encabezado').value = '#FFFFFF';
						document.getElementById('config_test_fondo_encabezado').style.backgroundColor = '#FFFFFF';
						document.getElementById('config_test_fondo_encabezado2').style.backgroundColor = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_fondo_encabezado').value = valor;
						document.getElementById('config_test_fondo_encabezado').style.backgroundColor = valor;
						document.getElementById('config_test_fondo_encabezado2').style.backgroundColor = valor;
					}
				});
				
				$("#color_fondo_encabezado").change(function(){
					var valor = document.getElementById('color_fondo_encabezado').value;
					document.getElementById('color_fondo_encabezado_hexadecimal').value = valor;
					document.getElementById('config_test_fondo_encabezado').style.backgroundColor = valor;
					document.getElementById('config_test_fondo_encabezado2').style.backgroundColor = valor;
				});
				
				$("#color_texto_encabezado_hexadecimal").change(function(){
					var valor = document.getElementById('color_texto_encabezado_hexadecimal').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_texto_encabezado_hexadecimal').value = '#FFFFFF';
						document.getElementById('color_texto_encabezado').value = '#FFFFFF';
						document.getElementById('config_test_texto_encabezado1').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_encabezado2').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_encabezado21').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_encabezado22').style.color = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_texto_encabezado').value = valor;
						document.getElementById('config_test_texto_encabezado1').style.color = valor;
						document.getElementById('config_test_texto_encabezado2').style.color = valor;
						document.getElementById('config_test_texto_encabezado21').style.color = valor;
						document.getElementById('config_test_texto_encabezado22').style.color = valor;
					}
				});
				
				$("#color_texto_encabezado").change(function(){
					var valor = document.getElementById('color_texto_encabezado').value;
					document.getElementById('color_texto_encabezado_hexadecimal').value = valor;
					document.getElementById('config_test_texto_encabezado1').style.color = valor;
					document.getElementById('config_test_texto_encabezado2').style.color = valor;
					document.getElementById('config_test_texto_encabezado21').style.color = valor;
					document.getElementById('config_test_texto_encabezado22').style.color = valor;
				});
				
				$("#color_fondo_formulario_hexadecimal").change(function(){
					var valor = document.getElementById('color_fondo_formulario_hexadecimal').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_fondo_formulario_hexadecimal').value = '#FFFFFF';
						document.getElementById('color_fondo_formulario').value = '#FFFFFF';
						document.getElementById('config_test_fondo_formulario').style.backgroundColor = '#FFFFFF';
						document.getElementById('config_test_fondo_formulario2').style.backgroundColor = '#FFFFFF';
						document.getElementById('config_test_fondo_formulario3').style.backgroundColor = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_fondo_formulario').value = valor;
						document.getElementById('config_test_fondo_formulario').style.backgroundColor = valor;
						document.getElementById('config_test_fondo_formulario2').style.backgroundColor = valor;
						document.getElementById('config_test_fondo_formulario3').style.backgroundColor = valor;
					}
				});
				
				$("#color_fondo_formulario").change(function(){
					var valor = document.getElementById('color_fondo_formulario').value;
					document.getElementById('color_fondo_formulario_hexadecimal').value = valor;
					document.getElementById('config_test_fondo_formulario').style.backgroundColor = valor;
					document.getElementById('config_test_fondo_formulario2').style.backgroundColor = valor;
					document.getElementById('config_test_fondo_formulario3').style.backgroundColor = valor;
				});
				
				$("#color_texto_formulario_hexadecimal").change(function(){
					var valor = document.getElementById('color_texto_formulario_hexadecimal').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_texto_formulario_hexadecimal').value = '#FFFFFF';
						document.getElementById('color_texto_formulario').value = '#FFFFFF';
						document.getElementById('config_test_texto_formulario1').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario2').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario3').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario21').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario22').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario23').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario24').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario25').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario26').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario27').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_formulario28').style.color = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_texto_formulario').value = valor;
						document.getElementById('config_test_texto_formulario1').style.color = valor;
						document.getElementById('config_test_texto_formulario2').style.color = valor;
						document.getElementById('config_test_texto_formulario3').style.color = valor;
						document.getElementById('config_test_texto_formulario21').style.color = valor;
						document.getElementById('config_test_texto_formulario22').style.color = valor;
						document.getElementById('config_test_texto_formulario23').style.color = valor;
						document.getElementById('config_test_texto_formulario24').style.color = valor;
						document.getElementById('config_test_texto_formulario25').style.color = valor;
						document.getElementById('config_test_texto_formulario26').style.color = valor;
						document.getElementById('config_test_texto_formulario27').style.color = valor;
						document.getElementById('config_test_texto_formulario28').style.color = valor;
					}
				});
				
				$("#color_texto_formulario").change(function(){
					var valor = document.getElementById('color_texto_formulario').value;
					document.getElementById('color_texto_formulario_hexadecimal').value = valor;
					document.getElementById('config_test_texto_formulario1').style.color = valor;
					document.getElementById('config_test_texto_formulario2').style.color = valor;
					document.getElementById('config_test_texto_formulario3').style.color = valor;
					document.getElementById('config_test_texto_formulario21').style.color = valor;
					document.getElementById('config_test_texto_formulario22').style.color = valor;
					document.getElementById('config_test_texto_formulario23').style.color = valor;
					document.getElementById('config_test_texto_formulario24').style.color = valor;
					document.getElementById('config_test_texto_formulario25').style.color = valor;
					document.getElementById('config_test_texto_formulario26').style.color = valor;
					document.getElementById('config_test_texto_formulario27').style.color = valor;
					document.getElementById('config_test_texto_formulario28').style.color = valor;
				});
				
				$("#color_texto_controles_formulario_hexadecimal").change(function(){
					var valor = document.getElementById('color_texto_controles_formulario_hexadecimal').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_texto_controles_formulario_hexadecimal').value = '#FFFFFF';
						document.getElementById('color_texto_controles_formulario').value = '#FFFFFF';
						document.getElementById('config_test_texto_campo_formulario1').style.color = '#FFFFFF';
						document.getElementById('config_test_texto_campo_formulario2').style.color = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_texto_controles_formulario').value = valor;
						document.getElementById('config_test_texto_campo_formulario1').style.color = valor;
						document.getElementById('config_test_texto_campo_formulario2').style.color = valor;
					}
				});
				
				$("#color_texto_controles_formulario").change(function(){
					var valor = document.getElementById('color_texto_controles_formulario').value;
					document.getElementById('color_texto_controles_formulario_hexadecimal').value = valor;
					document.getElementById('config_test_texto_campo_formulario1').style.color = valor;
					document.getElementById('config_test_texto_campo_formulario2').style.color = valor;
				});
				
				$("#color_boton_hexadecimal").change(function(){
					var valor = document.getElementById('color_boton_hexadecimal').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_boton_hexadecimal').value = '#FFFFFF';
						document.getElementById('color_boton').value = '#FFFFFF';
						document.getElementById('config_test_fondo_boton').style.backgroundColor = '#FFFFFF';
						document.getElementById('config_test_fondo_boton21').style.backgroundColor = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_boton').value = valor;
						document.getElementById('config_test_fondo_boton').style.backgroundColor = valor;
						document.getElementById('config_test_fondo_boton21').style.backgroundColor = valor;
					}
				});
				
				$("#color_boton").change(function(){
					var valor = document.getElementById('color_boton').value;
					document.getElementById('color_boton_hexadecimal').value = valor;
					document.getElementById('config_test_fondo_boton').style.backgroundColor = valor;
					document.getElementById('config_test_fondo_boton21').style.backgroundColor = valor;
				});
				
				$("#color_texto_boton_hexadecimal").change(function(){
					var valor = document.getElementById('color_texto_boton_hexadecimal').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_texto_boton_hexadecimal').value = '#FFFFFF';
						document.getElementById('color_texto_boton').value = '#FFFFFF';
						document.getElementById('config_test_fondo_boton').style.color = '#FFFFFF';
						document.getElementById('config_test_fondo_boton21').style.color = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_texto_boton').value = valor;
						document.getElementById('config_test_fondo_boton').style.color = valor;
						document.getElementById('config_test_fondo_boton21').style.color = valor;
					}
				});
				
				$("#color_texto_boton").change(function(){
					var valor = document.getElementById('color_texto_boton').value;
					document.getElementById('color_texto_boton_hexadecimal').value = valor;
					document.getElementById('config_test_fondo_boton').style.color = valor;
					document.getElementById('config_test_fondo_boton21').style.color = valor;
				});
				
				$("#color_boton_hexadecimal_vistaprevia").change(function(){
					var valor = document.getElementById('color_boton_hexadecimal_vistaprevia').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_boton_hexadecimal_vistaprevia').value = '#FFFFFF';
						document.getElementById('color_boton_vistaprevia').value = '#FFFFFF';
						document.getElementById('config_test_fondo_boton22').style.backgroundColor = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_boton_vistaprevia').value = valor;
						document.getElementById('config_test_fondo_boton22').style.backgroundColor = valor;
					}
				});
				
				$("#color_boton_vistaprevia").change(function(){
					var valor = document.getElementById('color_boton_vistaprevia').value;
					document.getElementById('color_boton_hexadecimal_vistaprevia').value = valor;
					document.getElementById('config_test_fondo_boton22').style.backgroundColor = valor;
				});
				
				$("#color_texto_boton_hexadecimal_vistaprevia").change(function(){
					var valor = document.getElementById('color_texto_boton_hexadecimal_vistaprevia').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_texto_boton_hexadecimal_vistaprevia').value = '#FFFFFF';
						document.getElementById('color_texto_boton_vistaprevia').value = '#FFFFFF';
						document.getElementById('config_test_fondo_boton22').style.color = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_texto_boton_vistaprevia').value = valor;
						document.getElementById('config_test_fondo_boton22').style.color = valor;
					}
				});
				
				$("#color_texto_boton_vistaprevia").change(function(){
					var valor = document.getElementById('color_texto_boton_vistaprevia').value;
					document.getElementById('color_texto_boton_hexadecimal_vistaprevia').value = valor;
					document.getElementById('config_test_fondo_boton22').style.color = valor;
				});
				
				$("#color_boton_hexadecimal_generarcfdi").change(function(){
					var valor = document.getElementById('color_boton_hexadecimal_generarcfdi').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_boton_hexadecimal_generarcfdi').value = '#FFFFFF';
						document.getElementById('color_boton_generarcfdi').value = '#FFFFFF';
						document.getElementById('config_test_fondo_boton23').style.backgroundColor = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_boton_generarcfdi').value = valor;
						document.getElementById('config_test_fondo_boton23').style.backgroundColor = valor;
					}
				});
				
				$("#color_boton_generarcfdi").change(function(){
					var valor = document.getElementById('color_boton_generarcfdi').value;
					document.getElementById('color_boton_hexadecimal_generarcfdi').value = valor;
					document.getElementById('config_test_fondo_boton23').style.backgroundColor = valor;
				});
				
				$("#color_texto_boton_hexadecimal_generarcfdi").change(function(){
					var valor = document.getElementById('color_texto_boton_hexadecimal_generarcfdi').value;
					
					re = /(^#[0-9A-F]{6}$)/i;
	   
					if (re.test(valor) == false)
					{
						mostrarVentanaConfiguracion2('<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, ingresa un valor hexadecimal válido.':'Please, enter a valid hexadecimal value.';?>');
						document.getElementById('color_texto_boton_hexadecimal_generarcfdi').value = '#FFFFFF';
						document.getElementById('color_texto_boton_generarcfdi').value = '#FFFFFF';
						document.getElementById('config_test_fondo_boton23').style.color = '#FFFFFF';
					}
					else
					{
						document.getElementById('color_texto_boton_generarcfdi').value = valor;
						document.getElementById('config_test_fondo_boton23').style.color = valor;
					}
				});
				
				$("#color_texto_boton_generarcfdi").change(function(){
					var valor = document.getElementById('color_texto_boton_generarcfdi').value;
					document.getElementById('color_texto_boton_hexadecimal_generarcfdi').value = valor;
					document.getElementById('config_test_fondo_boton23').style.color = valor;
				});
				
				var modalConfiguracion2 = document.getElementById('ventanaModalConfiguracion');
				var spanConfiguracion2 = document.getElementsByClassName('closeConfiguracion')[0];
				var botonConfiguracion2 = document.getElementById('botonModalConfiguracion');
				
				function mostrarVentanaConfiguracion2(texto)
				{
					modalConfiguracion2.style.display = "block";
					document.getElementById('tituloModalConfiguracion').innerHTML = '<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice';?>';
					document.getElementById('textoModalConfiguracion').innerHTML = texto;
				}
				
				botonConfiguracion2.onclick = function()
				{
					modalConfiguracion2.style.display = "none";
					document.getElementById('tituloModalConfiguracion').innerHTML = '';
					document.getElementById('textoModalConfiguracion').innerHTML = '';
				}
				
				spanConfiguracion2.onclick = function()
				{
					modalConfiguracion2.style.display = "none";
					document.getElementById('tituloModalConfiguracion').innerHTML = '';
					document.getElementById('textoModalConfiguracion').innerHTML = '';
				}
			});
		</script>
	<?php
}
function realvirtual_woocommerce_configuracion_ajustesAvanzados()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$complementos = RealVirtualWooCommerceComplementos::configuracionEntidad();
	
	?>
		<form id="realvirtual_woocommerce_configuracion_ajustesAvanzados" method="post" style="background-color: #FFFFFF; padding: 20px;">
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ajustes avanzados':'Advanced settings';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ajustes avanzados del funcionamiento del plugin de facturación.':'Advanced settings for invoicing plugin operation.';?></font></label>
			<br/><br/>
			<label><font color="#505050" size="3"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Facturación en el sitio web':'Invoicing on the website';?></font></b></label>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* ¿Cómo utilizará el plugin los montos de los pedidos de WooCommerce para su facturación?':'* How will the plugin use WooCommerce order amounts for invoicing?';?></font></label><br/>
				<select id="manejo_impuestos_pedido" name="manejo_impuestos_pedido" style="width:30%;">
				<?php 
					$manejo_impuestos_pedido = $configuracion['manejo_impuestos_pedido'];
					
					if($manejo_impuestos_pedido == '0')
					{
					?>
						<option value="0" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '<b>(Por defecto)</b> Usará los montos del pedido original.':'<b>(By default)</b>You will use the amounts from the original order.';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="0"><?php echo ($idiomaRVLFECFDI == 'ES') ? '<b>(Por defecto)</b> Usará los montos del pedido original.':'<b>(By default)</b>You will use the amounts from the original order.';?></option>
					<?php 
					}
					
					if($manejo_impuestos_pedido == '4')
					{
					?>
						<option value="4" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Recalculará montos intentando que cuadren con el total del pedido (Los descuentos serán eliminados).':'It will recalculate amounts trying to match the order total (Discounts will be eliminated).';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="4"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Recalculará montos intentando que cuadren con el total del pedido (Los descuentos serán eliminados).':'It will recalculate amounts trying to match the order total (Discounts will be eliminated).';?></option>
					<?php 
					}
					
					if($manejo_impuestos_pedido == '5')
					{
					?>
						<option value="5" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Recalculará montos alterando el total del pedido, el cual puede resultar menor o mayor al original (Los descuentos serán eliminados).':'It will recalculate amounts by altering the total of the order, which may be less or greater than the original (discounts will be eliminated).';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="5"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Recalculará montos alterando el total del pedido, el cual puede resultar menor o mayor al original (Los descuentos serán eliminados).':'It will recalculate amounts by altering the total of the order, which may be less or greater than the original (discounts will be eliminated).';?></option>
					<?php 
					}
					
					if($manejo_impuestos_pedido == '1')
					{
					?>
						<option value="1" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 16% del importe de cada artículo en pedidos sin impuestos. No funciona con otros impuestos diferentes al IVA 16%':'It will calculate the VAT 16% of the amount of each article in orders without taxes. It does not work with taxes other than VAT 16%';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 16% del importe de cada artículo en pedidos sin impuestos. No funciona con otros impuestos diferentes al IVA 16%':'It will calculate the VAT 16% of the amount of each article in orders without taxes. It does not work with taxes other than VAT 16%';?></option>
					<?php 
					}
					
					if($manejo_impuestos_pedido == '2')
					{
					?>
						<option value="2" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 8% del importe de cada artículo en pedidos sin impuestos. No funciona con otros impuestos diferentes al IVA 8%':'It will calculate the VAT 8% of the amount of each article in orders without taxes. It does not work with taxes other than VAT 8%';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 8% del importe de cada artículo en pedidos sin impuestos. No funciona con otros impuestos diferentes al IVA 8%':'It will calculate the VAT 8% of the amount of each article in orders without taxes. It does not work with taxes other than VAT 8%';?></option>
					<?php 
					}
					
					if($manejo_impuestos_pedido == '3')
					{
					?>
						<option value="3" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 0% del importe de cada artículo en pedidos sin impuestos. No funciona con otros impuestos diferentes al IVA 0%':'It will calculate the VAT 0% of the amount of each article in orders without taxes. It does not work with taxes other than VAT 0%';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="3"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 0% del importe de cada artículo en pedidos sin impuestos. No funciona con otros impuestos diferentes al IVA 0%':'It will calculate the VAT 0% of the amount of each article in orders without taxes. It does not work with taxes other than VAT 0%';?></option>
					<?php 
					}
				?>
				</select>
				<br/>
				<label id="manejo_impuestos_pedido_seleccionado"></label>
			<br/><br/>
			<label><font color="#505050" size="3"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Factura Global':'Global Invoicing';?></font></b></label>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* ¿Cómo utilizará el plugin los montos de los pedidos de WooCommerce para la factura global?':'* How will the plugin use WooCommerce order amounts for the global invoice?';?></font></label><br/>
				<select id="manejo_impuestos_pedido_facturaGlobal" name="manejo_impuestos_pedido_facturaGlobal" style="width:30%;">
				<?php 
					$manejo_impuestos_pedido_facturaGlobal = $configuracion['manejo_impuestos_pedido_facturaGlobal'];
					
					if($manejo_impuestos_pedido_facturaGlobal == '0')
					{
					?>
						<option value="0" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '<b>(Por defecto)</b> Usará los montos del pedido original.':'<b>(By default)</b>You will use the amounts from the original order.';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="0"><?php echo ($idiomaRVLFECFDI == 'ES') ? '<b>(Por defecto)</b> Usará los montos del pedido original.':'<b>(By default)</b>You will use the amounts from the original order.';?></option>
					<?php 
					}
					
					if($manejo_impuestos_pedido_facturaGlobal == '1')
					{
					?>
						<option value="1" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 16% a partir del total de cada pedido. No funciona con otros impuestos diferentes al IVA 16%':'It will calculate the VAT 16% of the total of each order. It does not work with taxes other than VAT 16%';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 16% a partir del total de cada pedido. No funciona con otros impuestos diferentes al IVA 16%':'It will calculate the VAT 16% of the total of each order. It does not work with taxes other than VAT 16%';?></option>
					<?php 
					}
					
					if($manejo_impuestos_pedido_facturaGlobal == '2')
					{
					?>
						<option value="2" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 8% a partir del total de cada pedido. No funciona con otros impuestos diferentes al IVA 8%':'It will calculate the VAT 8% of the total of each order. It does not work with taxes other than VAT 8%';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 8% a partir del total de cada pedido. No funciona con otros impuestos diferentes al IVA 8%':'It will calculate the VAT 8% of the total of each order. It does not work with taxes other than VAT 8%';?></option>
					<?php 
					}
					
					if($manejo_impuestos_pedido_facturaGlobal == '3')
					{
					?>
						<option value="3" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 0% a partir del total de cada pedido. No funciona con otros impuestos diferentes al IVA 0%':'It will calculate the VAT 0% of the total of each order. It does not work with taxes other than VAT 0%';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="3"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Calculará el IVA 0% a partir del total de cada pedido. No funciona con otros impuestos diferentes al IVA 0%':'It will calculate the VAT 0% of the total of each order. It does not work with taxes other than VAT 0%';?></option>
					<?php 
					}
				?>
				</select>
				<br/>
				<label id="manejo_impuestos_pedido_facturaGlobal_seleccionado"></label>
				<input type="text" id="manejo_impuestos_pedido_facturaGlobal_texto" hidden>
			<br/><br/>
			<label><font color="#505050" size="3"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Facturación Automática':'Automatic Invoicing';?></font></b></label>
			<?php
				if($complementos['emisionCFDIAutomatico'] != '1')
				{
					$avisoTitulo = ($idiomaRVLFECFDI == 'ES') ? 'FACTURACIÓN AUTOMÁTICA NO ESTÁ DISPONIBLE' : 'AUTOMATIC BILLING IS NOT AVAILABLE';
					$avisoTitulo = '<label><font color="#dc0000" size="3"><b>'.$avisoTitulo.'</b></font></label>';
					$avisoMensaje = ($idiomaRVLFECFDI == 'ES') ? 'Estimado usuario, realiza la compra de esta funcionalidad para poder utilizarla. Ve a la sección <b>Complementos</b> del plugin de facturación para realizar la compra de esta funcionalidad y conoce todos los complementos que ofrecemos.<br/><br/>A continuación, podrás observar esta característica pero su funcionalidad estará deshabilitada.' : 'Dear user, make the purchase of this functionality to be able to use it. Go to the <b>Add-ons</b> section of the billing plugin to purchase this functionality and learn about all the add-ons we offer.<br/><br/>Next, you will be able to see the feature but its functionality will be disabled.';
					$avisoMensaje = '<label><font color="#000000" size="2">'.$avisoMensaje.'</font></label>';
					$avisoCompleto = '<br/><br/><div style="background-color:#f3bfbf; padding: 15px; width:40%;">'.$avisoTitulo.'<br/>'.$avisoMensaje.'</div><br/>';
					echo $avisoCompleto;
				}
			?>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Estado del pedido para emitir CFDI automáticamente':'* Status of the order to issue CFDI automatically';?></font></label><br/>
				<select id="estado_orden_cfdi_automatico" name="estado_orden_cfdi_automatico" style="width:30%;">
				<?php 
					$estado_orden_cfdi_automatico = $configuracion['estado_orden_cfdi_automatico'];
					
					if($estado_orden_cfdi_automatico == 'no-especificado')
					{
					?>
						<option value="no-especificado" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No especificado (no se emitirá el CFDI automáticamente)':'Not specified (the CFDI will not be issued automatically)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="no-especificado"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No especificado (no se emitirá el CFDI automáticamente)':'Not specified (the CFDI will not be issued automatically)';?></option>
					<?php 
					}
					
					if($estado_orden_cfdi_automatico == 'cualquier-estado-excepto')
					{
					?>
						<option value="cualquier-estado-excepto" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Todos excepto Pendiente de pago, Cancelado, Reembolsado y Fallido':'All except Pending payment, Canceled, Refunded and Failed';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="cualquier-estado-excepto"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Todos excepto Pendiente de pago, Cancelado, Reembolsado y Fallido':'All except Pending payment, Canceled, Refunded and Failed';?></option>
					<?php 
					}
					
					if($estado_orden_cfdi_automatico == 'processing')
					{
					?>
						<option value="processing" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando (recomendado)':'Processing (recommended)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="processing"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando':'Processing';?></option>
					<?php 
					}
					
					if($estado_orden_cfdi_automatico == 'completed')
					{
					?>
						<option value="completed" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Completado':'Completed';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="completed"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Completado':'Completed';?></option>
					<?php 
					}
					
					if($estado_orden_cfdi_automatico == 'processing-completed')
					{
					?>
						<option value="processing-completed" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando o Completado':'Processing or Completed';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="processing-completed"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando o Completado':'Processing or Completed';?></option>
					<?php 
					}
					
					if($estado_orden_cfdi_automatico == 'personalizado-1')
					{
					?>
						<option value="personalizado-1" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 1 (Slug personalizado-1)':'Personalized State 1 (Slug personalizado-1)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="personalizado-1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 1 (Slug personalizado-1)':'Personalized State 1 (Slug personalizado-1)';?></option>
					<?php 
					}
					
					if($estado_orden_cfdi_automatico == 'personalizado-2')
					{
					?>
						<option value="personalizado-2" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 2 (Slug personalizado-2)':'Personalized State 2 (Slug personalizado-2)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="personalizado-2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 2 (Slug personalizado-2)':'Personalized State 2 (Slug personalizado-2)';?></option>
					<?php 
					}
					
					if($estado_orden_cfdi_automatico == 'personalizado-3')
					{
					?>
						<option value="personalizado-3" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 3 (Slug personalizado-3)':'Personalized State 3 (Slug personalizado-3)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="personalizado-3"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 3 (Slug personalizado-3)':'Personalized State 3 (Slug personalizado-3)';?></option>
					<?php 
					}
				?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Para que esta característica funcione, es necesario que tus clientes registren sus datos fiscales en sus cuentas de usuario de tu tienda virtual, esto con el objetivo de que el plugin pueda utilizar esos datos automáticamente al emitir el CFDI. Ve a la sección <b>Ayuda > Guía del Módulo de Datos Fiscales</b> para leer nuestra guía de configuración de esta característica.<br/><br/>Si seleccionas un estado personalizado, éste debes agregarlo previamente en WooCommerce<br/>sin importar el nombre pero con el Slug indicado para que pueda funcionar, ya que el plugin<br/>sólo trabaja con el Slug y el nombre es indiferente y nunca es utilizado.':'For this feature to work, it is necessary for your customers to register their tax data in their user accounts of your virtual store, so that the plugin can use this data automatically when issuing the CFDI. Go to the <b>Help > Fiscal Data Module Guide</b> section to read our configuration guide for this feature.<br/><br/>If you select a custom status, you must first add it in WooCommerce<br/>regardless of the name but with the Slug indicated so that it can work,<br/>since the plugin only works with the Slug and the name is indifferent and is never used.';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Enviar notificación por correo cuando ocurra un error al emitir el CFDI':'* Send notification by mail when an error occurs when issuing the CFDI';?></font></label><br/>
				<select id="notificar_error_cfdi_automatico" name="notificar_error_cfdi_automatico" style="width:30%;">
				<?php 
					$notificar_error_cfdi_automatico = $configuracion['notificar_error_cfdi_automatico'];
					
					if($notificar_error_cfdi_automatico == '1')
					{
					?>
						<option value="1" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Enviar notificación al cliente y al administrador':'Send notification to client and administrator';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Enviar notificación al cliente y al administrador':'Send notification to client and administrator';?></option>
					<?php 
					}
					
					if($notificar_error_cfdi_automatico == '2')
					{
					?>
						<option value="2" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sólo enviar notificación al cliente':'Only send notification to client';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sólo enviar notificación al cliente':'Only send notification to client';?></option>
					<?php 
					}
					
					if($notificar_error_cfdi_automatico == '3')
					{
					?>
						<option value="3" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sólo enviar notificación al administrador':'Only send notification to administrator';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="3"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sólo enviar notificación al administrador':'Only send notification to administrator';?></option>
					<?php 
					}
					
					if($notificar_error_cfdi_automatico == '0')
					{
					?>
						<option value="0" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No enviar notificaciones':'Do not send notifications';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="0"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No enviar notificaciones':'Do not send notifications';?></option>
					<?php 
					}
				?>
				</select>
				<div class="tooltip right"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
				  <span class="tooltiptext"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Las notificaciones se enviarán al correo electrónico del cliente de un pedido en cuestión y también al especificado en el campo debajo con el error original detallado.':'Notifications will be sent to the email of the customer of an order in question and also the one specified in the field below with the detailed original error.';?></span>
				</div>
				<br/><br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'E-mail a donde se enviará el error original detallado de emisión del CFDI cuando ocurra un error.':'E-mail a donde se enviará el error original detallado de emisión del CFDI cuando ocurra un error';?></font></label><br/>
				<input type="text" id="emailNotificacionErrorAutomatico" name="emailNotificacionErrorAutomatico" value="<?php echo esc_html($configuracion['emailNotificacionErrorAutomatico']); ?>" style="width:30%;" />
				<br/><br/>
				<br/>
			<br/>
			<div hidden>
			<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Complemento del CFDI':'CFDI Complement';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Permite incluir un complemento en los CFDI que tus clientes generen desde tu sitio web. El cliente ingresará los datos de un complemento en la emisión del CFDI.':'It allows to include a complement in the CFDI that your clients generate from your website. The client will enter the data of a complement in the issuance of the CFDI.';?></font></label>
			<br/><br/>
			<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Complemento':'* Complement';?></font></label><br/>
				<select id="complementoCFDI" name="complementoCFDI" style="width:30%;">
				<?php 
					$complementoCFDI = $configuracion['complementoCFDI'];
					
					if($complementoCFDI == 'ninguno')
					{
					?>
						<option value="ninguno" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ninguno (Por defecto)':'None (By default)';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="ninguno"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ninguno (Por defecto)':'None (By default)';?></option>
					<?php 
					}
					
					if($complementoCFDI == 'iedu')
					{
					?>
						<option value="iedu" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'IEDU':'IEDU';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="iedu"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'IEDU':'IEDU';?></option>
					<?php 
					}
				?>
				</select>
			<br/>
			<div width="100%" id="complementoCFDI_iedu_configuracion" hidden>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Nivel educativo por defecto':'* Default educational level';?></font></label><br/>
				<select id="complementoCFDI_iedu_configuracion_nivel" name="complementoCFDI_iedu_configuracion_nivel" style="width:30%;">
				<?php 
					$complementoCFDI_iedu_configuracion_nivel = $configuracion['complementoCFDI_iedu_configuracion_nivel'];
					
					if($complementoCFDI_iedu_configuracion_nivel == 'Preescolar')
					{
					?>
						<option value="Preescolar" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Preescolar':'Preescolar';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="Preescolar"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Preescolar':'Preescolar';?></option>
					<?php 
					}
					
					if($complementoCFDI_iedu_configuracion_nivel == 'Primaria')
					{
					?>
						<option value="Primaria" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Primaria':'Primaria';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="Primaria"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Primaria':'Primaria';?></option>
					<?php 
					}
					
					if($complementoCFDI_iedu_configuracion_nivel == 'Secundaria')
					{
					?>
						<option value="Secundaria" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Secundaria':'Secundaria';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="Secundaria"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Secundaria':'Secundaria';?></option>
					<?php 
					}
					
					if($complementoCFDI_iedu_configuracion_nivel == 'Profesional técnico')
					{
					?>
						<option value="Profesional técnico" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Profesional técnico':'Profesional técnico';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="Profesional técnico"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Profesional técnico':'Profesional técnico';?></option>
					<?php 
					}
					
					if($complementoCFDI_iedu_configuracion_nivel == 'Bachillerato o su equivalente')
					{
					?>
						<option value="Bachillerato o su equivalente" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Bachillerato o su equivalente':'Bachillerato o su equivalente';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="Bachillerato o su equivalente"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Bachillerato o su equivalente':'Bachillerato o su equivalente';?></option>
					<?php 
					}
				?>
				</select>
				<br/>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Clave validéz oficial por defecto':'* Official valid password by default';?></font></label><br/>
				<input type="text" id="complementoCFDI_iedu_configuracion_autRVOE" name="complementoCFDI_iedu_configuracion_autRVOE" value="<?php echo esc_html($configuracion['complementoCFDI_iedu_configuracion_autRVOE']); ?>" style="width:30%;">
			</div>
			<br/>
			</div>
		<br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_enviar_configuracion_ajustesAvanzados"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargandoConfiguracionAjustesAvanzados" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var modalConfiguracion2 = document.getElementById('ventanaModalConfiguracion');
				var spanConfiguracion2 = document.getElementsByClassName('closeConfiguracion')[0];
				var botonConfiguracion2 = document.getElementById('botonModalConfiguracion');
				
				function mostrarVentanaConfiguracion2(texto)
				{
					modalConfiguracion2.style.display = "block";
					document.getElementById('tituloModalConfiguracion').innerHTML = '<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice';?>';
					document.getElementById('textoModalConfiguracion').innerHTML = texto;
				}
				
				botonConfiguracion2.onclick = function()
				{
					modalConfiguracion2.style.display = "none";
					document.getElementById('tituloModalConfiguracion').innerHTML = '';
					document.getElementById('textoModalConfiguracion').innerHTML = '';
				}
				
				spanConfiguracion2.onclick = function()
				{
					modalConfiguracion2.style.display = "none";
					document.getElementById('tituloModalConfiguracion').innerHTML = '';
					document.getElementById('textoModalConfiguracion').innerHTML = '';
				}
				
				document.getElementById('manejo_impuestos_pedido_seleccionado').innerHTML = '<font color="#1d8400" size="2">' + $("#manejo_impuestos_pedido option:selected").text() + '</font>';
					
				$('#manejo_impuestos_pedido').change(function(event)
				{
					document.getElementById('manejo_impuestos_pedido_seleccionado').innerHTML = '<font color="#1d8400" size="2">' + $("#manejo_impuestos_pedido option:selected").text() + '</font>';
				});
				
				document.getElementById('manejo_impuestos_pedido_facturaGlobal_seleccionado').innerHTML = '<font color="#1d8400" size="2">' + $("#manejo_impuestos_pedido_facturaGlobal option:selected").text() + '</font>';
				document.getElementById('manejo_impuestos_pedido_facturaGlobal_texto').value = $("#manejo_impuestos_pedido_facturaGlobal option:selected").text();
					
				$('#manejo_impuestos_pedido_facturaGlobal').change(function(event)
				{
					document.getElementById('manejo_impuestos_pedido_facturaGlobal_seleccionado').innerHTML = '<font color="#1d8400" size="2">' + $("#manejo_impuestos_pedido_facturaGlobal option:selected").text() + '</font>';
					document.getElementById('manejo_impuestos_pedido_facturaGlobal_texto').value = $("#manejo_impuestos_pedido_facturaGlobal option:selected").text();
				});
				
				$('#complementoCFDI').change(function(event)
				{
					var complementoCFDI = document.getElementById('complementoCFDI').value;
					
					if(complementoCFDI == 'ninguno')
					{
						$( "#complementoCFDI_iedu_configuracion" ).hide("slow", function()
						{
							  
						});
					}
					else if(complementoCFDI == 'iedu')
					{
						$( "#complementoCFDI_iedu_configuracion" ).show("slow", function()
						{
							
						});
					}
				});
				
				let complementoCFDI = document.getElementById('complementoCFDI').value;
				
				if(complementoCFDI == 'ninguno')
				{
						$( "#complementoCFDI_iedu_configuracion" ).hide("slow", function()
						{
							  
						});
				}
				else if(complementoCFDI == 'iedu')
				{
					$( "#complementoCFDI_iedu_configuracion" ).show("slow", function()
					{
							
					});
				}
			});
		</script>
	<?php
}

function realvirtual_woocommerce_configuracion_idioma()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<form id="realvirtual_woocommerce_configuracion_idioma" method="post" style="background-color: #FFFFFF; padding: 20px;">
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'Idioma':'Language';?></b></font></label>
			<br/><br/>
			<div>
				<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Idioma del plugin':'* Plugin language';?></font></label><br/>
				<select id="idioma" name="idioma" style="width:10%">
				<?php 
					//$idiomaRVLFECFDI = $configuracion['idioma'];
					
					if($idiomaRVLFECFDI == 'ES')
					{
					?>
						<option value="ES" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Español':'Spanish';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="ES"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Español':'Spanish';?></option>
					<?php 
					}
					
					if($idiomaRVLFECFDI == 'EN')
					{
					?>
						<option value="EN" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Inglés':'English';?></option>
					<?php 
					}
					else
					{
					?>
						<option value="EN"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Inglés':'English';?></option>
					<?php 
					}
				?>
				</select>
				<br/>
			</div>
		<br/><br/>
		<label><font color="#505050" size="2" style="font-style: italic;"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:';?></b><br/><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '1) Para la emisión de CFDI con el plugin es necesario haber configurado previamente todos tus datos en la sección <b>Mi Cuenta</b> del sistema de facturación':'1) For the issue of CFDI with the plugin it is necessary to have previously configured all your data in the <b>My Account</b> section of';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '.':' system.';?><br/><?php echo ($idiomaRVLFECFDI == 'ES') ? '2) Al pulsar el botón Guardar, tu configuración se guardará tanto en tu Wordpress como de manera interna en':'2) When you press the Save button, your settings will be saved both in your Wordpress and internally in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '. Así, en caso de extravío o siempre que actualices este plugin e ingreses tus datos de acceso en la sección <b>Mi Cuenta</b>, se recuperará tu configuración automáticamente.':' system. So, in case of loss or whenever you update this plugin and enter your access data in the <b>My Account</b> section, your settings will be automatically retrieved.';?></font></label>
		<br/><br/>
		<div>
			<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_enviar_configuracion_idioma"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
			<img id="cargandoConfiguracionIdioma" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
		</div>
		</form>
	<?php
}

function realvirtual_woocommerce_configuracion_impuestos()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<form id="realvirtual_woocommerce_configuracion_impuestos" method="post" style="background-color: #FFFFFF; padding: 20px;">
		
		
		<label><font color="#000000" size="4"><b>Impuestos</b></font></label>
		<br/>
		<div>
			<label><font color="#000000">El plugin de facturación lee la información de los impuestos existentes en tus pedidos de WooCommerce cuando se desea realizar la emisión CFDI.<br/>A continuación, te enlistamos los impuestos que son reconocidos y cómo asegurar su compatibilidad con el plugin de facturación.<br/>No olvides al final de la siguiente lista ver las consideraciones finales de los impuestos aplicados a los productos de tu tienda.</font></label><br/>
			<br/>
			<div style="background-color:#dbdbdb63;padding: 20px;">
			<label><font color="#d1330f" size="4"><b>IVA</b></font></label>
			<br/>
			<label><font color="#000000">Para utilizar el impuesto IVA, se debe agregar con el nombre <b><font color="#d1330f" size="2">IVA</font></b> en el catálogo de <b>Impuestos</b> de WooCommerce en la clase de impuestos<br/>por defecto <b>Tarifas estándar</b> o en cualquier otra clase de impuestos personalizada que hayas creado.</font></label><br/>
			<br/>
			<dd>
			<label><font color="#000000" size="3"><b>Consideraciones:</b></font></label>
			<br/>
			- El nombre del impuesto debe ser <b><font color="#d1330f" size="2">IVA</font></b> en mayúsculas y sin ninguna otra letra, símbolo, número o caracter. De lo contrario no será reconocido.
			<br/>
			- Las tasas compatibles son <b>16%</b>, <b>8%</b> y <b>0%</b>.
			<br/>
			</dd>
			</div><br/>
			<div style="background-color:#dbdbdb63;padding: 20px;">
			<label><font color="#d1330f" size="4"><b>IVA EXENTO</b></font></label>
			<br/>
			<label><font color="#000000">Para utilizar el impuesto IVA Exento, se debe agregar con el nombre <b><font color="#d1330f" size="2">IVA EXENTO</font></b> en el catálogo de <b>Impuestos</b> de WooCommerce en la clase de impuestos<br/>por defecto <b>Tarifas estándar</b> o en cualquier otra clase de impuestos personalizada que hayas creado.</font></label><br/>
			<br/>
			<dd>
			<label><font color="#000000" size="3"><b>Consideraciones:</b></font></label>
			<br/>
			- El nombre del impuesto debe ser <b><font color="#d1330f" size="2">IVA EXENTO</font></b> en mayúsculas y sin ninguna otra letra, símbolo, número o caracter. De lo contrario no será reconocido.
			<br/>
			- La tasa debe ser <b>0%</b> para que no afecte los cálculos en el pedido de WooCommerce.
			<br/>
			- La tasa no será contemplada en la emisión del CFDI debido a que se trata de un impuesto exento.
			<br/>
			</dd>
			</div><br/>
			<div style="background-color:#dbdbdb63;padding: 20px;">
			<label><font color="#d1330f" size="4"><b>IVA RETENIDO</b></font></label>
			<br/>
			<label><font color="#000000">Para utilizar el impuesto IVA Retenido, se debe agregar con el nombre <b><font color="#d1330f" size="2">IVA RETENIDO</font></b> en el catálogo de <b>Impuestos</b> de WooCommerce en la clase de impuestos<br/>por defecto <b>Tarifas estándar</b> o en cualquier otra clase de impuestos personalizada que hayas creado.</font></label><br/>
			<br/>
			<dd>
			<label><font color="#000000" size="3"><b>Consideraciones:</b></font></label>
			<br/>
			- El nombre del impuesto debe ser <b><font color="#d1330f" size="2">IVA RETENIDO</font></b> en mayúsculas y sin ninguna otra letra, símbolo, número o caracter. De lo contrario no será reconocido.
			<br/>
			- La tasa compatible debe ser un valor mayor o igual a <b>0%</b> y menor o igual a <b>16%</b>.
			<br/>
			</dd>
			</div><br/>
			<div style="background-color:#dbdbdb63;padding: 20px;">
			<label><font color="#d1330f" size="4"><b>IEPS</b></font></label>
			<br/>
			<label><font color="#000000">Para utilizar el impuesto IEPS, se debe agregar con el nombre <b><font color="#d1330f" size="2">IEPS</font></b> en el catálogo de <b>Impuestos</b> de WooCommerce en la clase de impuestos<br/>por defecto <b>Tarifas estándar</b> o en cualquier otra clase de impuestos personalizada que hayas creado.</font></label><br/>
			<br/>
			<dd>
			<label><font color="#000000" size="3"><b>Consideraciones:</b></font></label>
			<br/>
			- El nombre del impuesto debe ser <b><font color="#d1330f" size="2">IEPS</font></b> en mayúsculas y sin ninguna otra letra, símbolo, número o caracter. De lo contrario no será reconocido.
			<br/>
			- Las tasas compatibles son <b>0%</b>, <b>3%</b>, <b>6%</b>, <b>7%</b>, <b>8%</b>, <b>9%</b> <b>25%</b>, <b>26.50%</b>, <b>30%</b>, <b>30.40%</b>, <b>50%</b>, <b>53%</b> y <b>160%</b>.
			<br/>
			</dd>
			</div><br/>
			<div style="background-color:#dbdbdb63;padding: 20px;">
			<label><font color="#d1330f" size="4"><b>IEPS RETENIDO</b></font></label>
			<br/>
			<label><font color="#000000">Para utilizar el impuesto IEPS Retenido, se debe agregar con el nombre <b><font color="#d1330f" size="2">IEPS RETENIDO</font></b> en el catálogo de <b>Impuestos</b> de WooCommerce en la clase de impuestos<br/>por defecto <b>Tarifas estándar</b> o en cualquier otra clase de impuestos personalizada que hayas creado.</font></label><br/>
			<br/>
			<dd>
			<label><font color="#000000" size="3"><b>Consideraciones:</b></font></label>
			<br/>
			- El nombre del impuesto debe ser <b><font color="#d1330f" size="2">IEPS RETENIDO</font></b> en mayúsculas y sin ninguna otra letra, símbolo, número o caracter. De lo contrario no será reconocido.
			<br/>
			- Las tasas compatibles son <b>6%</b>, <b>7%</b>, <b>8%</b>, <b>9%</b> <b>25%</b>, <b>26.50%</b>, <b>30%</b>, <b>30.40%</b>, <b>50%</b>, <b>53%</b> y <b>160%</b>.
			<br/>
			</dd>
			</div><br/>
			<div style="background-color:#dbdbdb63;padding: 20px;">
			<label><font color="#d1330f" size="4"><b>ISR</b></font></label>
			<br/>
			<label><font color="#000000">Para utilizar el impuesto ISR, se debe agregar con el nombre <b><font color="#d1330f" size="2">ISR</font></b> en el catálogo de <b>Impuestos</b> de WooCommerce en la clase de impuestos<br/>por defecto <b>Tarifas estándar</b> o en cualquier otra clase de impuestos personalizada que hayas creado.</font></label><br/>
			<br/>
			<dd>
			<label><font color="#000000" size="3"><b>Consideraciones:</b></font></label>
			<br/>
			- El nombre del impuesto debe ser <b><font color="#d1330f" size="2">ISR</font></b> en mayúsculas y sin ninguna otra letra, símbolo, número o caracter. De lo contrario no será reconocido.
			<br/>
			- La tasa compatible debe ser un valor mayor o igual a <b>0%</b> y menor o igual a <b>35%</b>.
			<br/>
			</dd>
			</div><br/>
			<div style="background-color:#dbdbdb63;padding: 20px;">
			<label><font color="#d1330f" size="4"><b>ISH</b></font></label>
			<br/>
			<label><font color="#000000">Para utilizar el impuesto local ISH, se debe agregar con el nombre <b><font color="#d1330f" size="2">ISH</font></b> en el catálogo de <b>Impuestos</b> de WooCommerce en la clase de impuestos<br/>por defecto <b>Tarifas estándar</b> o en cualquier otra clase de impuestos personalizada que hayas creado.</font></label><br/>
			<br/>
			<dd>
			<label><font color="#000000" size="3"><b>Consideraciones:</b></font></label>
			<br/>
			- El nombre del impuesto debe ser <b><font color="#d1330f" size="2">ISH</font></b> en mayúsculas y sin ninguna otra letra, símbolo, número o caracter. De lo contrario no será reconocido.
			<br/>
			- La tasa debe corresponder a dicho impuesto en tu región.
			<br/>
			</dd>
			</div><br/>
			<div style="background-color:#dbdbdb63;padding: 20px;">
			<label><font color="#d1330f" size="4"><b>CONSIDERACIONES FINALES</b></font></label>
			<dd>
			<br/>
			- WooCommerce no almacena la tasa de un impuesto en cada pedido creado, por lo que el plugin obtiene este dato desde del catálogo de <b>Impuestos</b> de WooCommerce.<br/>
			Si elimina o edita un impuesto afectará a los pedidos que lo utilizaron previamente y el plugin no podrá encontrar la tasa del impuesto en cuestión.<br/>
			Si esto ocurre, el plugin intentará calcular la tasa al momento de la emisión del CFDI mostrando una advertencia al cliente para que se comunique con el administrador de la tienda virtual y se le confirme si las tasas están bien o no, pudiendo generar o no el CFDI.<br/>
			La única solución ante un pedido defectuoso por este caso, es editarlo agregando de nuevo los impuestos y recalculando las cantidades o en el peor de los casos, volverlo a elaborar. 
			<br/><br/>
			- Un producto no puede tener dos veces o más el mismo impuesto trasladado o retenido a pesar de que la tasa sea diferente.<br/>
			Por ejemplo, un artículo puede tener IVA Trasladado e IVA Retenido a la vez, pero no puede tener dos o más veces el IVA Trasladado aunque sus tasas sean diferentes.<br/>
			Si a nivel pedido en WooCommerce no respeta lo anterior para un artículo, nuestro plugin sólo considerará la primera aparición del mismo tipo de impuesto ignorando sus repeticiones posteriores.
			<br/>
			</dd>
			</div><br/>
		</div>



		</form>
	
	<?php
}

function realvirtual_woocommerce_menu_soporte()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $versionPlugin, $idiomaRVLFECFDI;
	
	$default_tab = null;
	$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
  
	?>
		<div class="wrap">
			<br/>
			<div style="background-color:#ffffff; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
			<font color="#000000" size="5"><b><?php echo esc_html($nombreSistema); ?></b></font><font color="#505050" size="2" style="font-style: italic;"><?php echo '&nbsp; '.($idiomaRVLFECFDI == 'ES' ? 'versión ' : 'version ').esc_html($versionPlugin); ?></font>
			<br/><br/>
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ayuda':'Help';?></b></font></label>
			</div>
			<br/>
			<nav class="nav-tab-wrapper">
				<a href="?page=realvirtual_woo_soporte&tab=guiainiciorapido" class="nav-tab <?php if($tab==='guiainiciorapido' || $tab == null):?>nav-tab-active<?php endif; ?>">Guía de Inicio Rápido</a>
				<a href="?page=realvirtual_woo_soporte&tab=guiadatosfiscales" class="nav-tab <?php if($tab==='guiadatosfiscales'):?>nav-tab-active<?php endif; ?>">Guía del Módulo de Datos Fiscales de Clientes</a>
				<a href="?page=realvirtual_woo_soporte&tab=soporte" class="nav-tab <?php if($tab==='soporte'):?>nav-tab-active<?php endif; ?>">Soporte Técnico</a>
				<a href="?page=realvirtual_woo_soporte&tab=preguntas" class="nav-tab <?php if($tab==='preguntas'):?>nav-tab-active<?php endif; ?>">Preguntas Frecuentes</a>
			</nav>
			<div class="tab-content">
				<?php
					if($tab == 'guiainiciorapido' || $tab == null)
						realvirtual_woocommerce_guiainiciorapido();
					else if($tab == 'guiadatosfiscales')
						realvirtual_woocommerce_guiadatosfiscales();
					else if($tab == 'soporte')
						realvirtual_woocommerce_soporte();
					else if($tab == 'preguntas')
						realvirtual_woocommerce_preguntas();
				?>
			</div>
		</div>
	<?php
}

function realvirtual_woocommerce_menu_facturacion()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $versionPlugin, $idiomaRVLFECFDI;
	
	$default_tab = null;
	$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
  
	?>
		<div class="wrap">
			<br/>
			<div style="background-color:#ffffff; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
			<font color="#000000" size="5"><b><?php echo esc_html($nombreSistema); ?></b></font><font color="#505050" size="2" style="font-style: italic;"><?php echo '&nbsp; '.($idiomaRVLFECFDI == 'ES' ? 'versión ' : 'version ').esc_html($versionPlugin); ?></font>
			<br/><br/>
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Facturación':'Invoicing';?></b></font></label>
			</div>
			<br/>
			<nav class="nav-tab-wrapper">
			  <a href="?page=realvirtual_woo_facturacion&tab=ventas" class="nav-tab <?php if($tab==='ventas' || $tab == null):?>nav-tab-active<?php endif; ?>">CFDI Emitidos</a>
			  <a href="?page=realvirtual_woo_facturacion&tab=datosFiscalesClientes" class="nav-tab <?php if($tab==='datosFiscalesClientes'):?>nav-tab-active<?php endif; ?>">Datos Fiscales de Clientes</a>
			  <a href="?page=realvirtual_woo_facturacion&tab=facturarCFDI" class="nav-tab <?php if($tab==='facturarCFDI'):?>nav-tab-active<?php endif; ?>">Facturar Pedido</a>
			  <a href="?page=realvirtual_woo_facturacion&tab=facturaGlobal" class="nav-tab <?php if($tab==='facturaGlobal'):?>nav-tab-active<?php endif; ?>">Factura Global</a>
			</nav>
			<div class="tab-content">
				<?php
					if($tab == 'ventas' || $tab == null)
						realvirtual_woocommerce_ventas();
					else if($tab == 'datosFiscalesClientes')
						realvirtual_woocommerce_datosFiscalesClientes();
					else if($tab == 'facturaGlobal')
						realvirtual_woocommerce_facturaglobal();
					else if($tab == 'facturarCFDI')
						realvirtual_woocommerce_facturarpedido();
				?>
			</div>
		</div>
	<?php
}

function realvirtual_woocommerce_facturarpedido()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$complementos = RealVirtualWooCommerceComplementos::configuracionEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		echo ($idiomaRVLFECFDI == 'ES') ? 'No se puede abrir esta sección porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.' : 'Cannot open this section because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.';
		wp_die();
	}
	
	?>
		<div style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Facturar Pedido':'Invoice Order';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'En esta sección podrás emitir el CFDI de un pedido a través del mismo formulario que tus clientes pueden utilizar desde tu sitio web.':'In this section you can issue the CFDI of an order through the same form that your customers can use from your website.';?></font></label>
			<br/>
			<?php
				if($complementos['facturacionDashboard'] != '1')
				{
					$avisoTitulo = ($idiomaRVLFECFDI == 'ES') ? 'ESTE MÓDULO NO ESTÁ DISPONIBLE' : 'THIS MODULE IS NOT AVAILABLE';
					$avisoTitulo = '<label><font color="#dc0000" size="4"><b>'.$avisoTitulo.'</b></font></label>';
					$avisoMensaje = ($idiomaRVLFECFDI == 'ES') ? 'Estimado usuario, realiza la compra de este módulo para poder utilizarlo. Ve a la sección <b>Complementos</b> del plugin de facturación para realizar la compra de este módulo y conoce todos los complementos que ofrecemos.<br/>A continuación, podrás observar el módulo pero su funcionalidad estará deshabilitada.' : 'Dear user, make the purchase of this module to be able to use it. Go to the <b>Add-ons</b> section of the billing plugin to purchase this module and learn about all the add-ons we offer.<br/>Next, you will be able to see the module but its functionality will be disabled.';
					$avisoMensaje = '<label><font color="#000000" size="3">'.$avisoMensaje.'</font></label>';
					$avisoCompleto = '<br/><div style="background-color:#f3bfbf; padding: 15px;">'.$avisoTitulo.'<br/>'.$avisoMensaje.'</div>';
					echo $avisoCompleto;
				}
			?>
			<br/>
			<center>
			<div style="background-color: #FFFFFF; padding: 20px; width: 100%">
				<table border="0" style="background-color:#f0f0ef; width:100%; height:100%;">
					<tr>
						<td style="width:40%;vertical-align: top;">
						<?php
							realvirtual_woocommerce_facturarpedido_pedidos();
						?>
						</td>
						<td style="width:60%;vertical-align: top;">
						<div style="background-color: #FFFFFF; width:100%; height:100%;">
						<?php
							realvirtual_woocommerce_front_end(1);
						?>
						</div>
						</td>
					</tr>
				</table>
			</div>
			</center>
		</div>
	<?php
}

function realvirtual_woocommerce_facturarpedido_pedidos()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$complementos = RealVirtualWooCommerceComplementos::configuracionEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		echo ($idiomaRVLFECFDI == 'ES') ? 'No se puede abrir esta sección porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.' : 'Cannot open this section because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.';
		wp_die();
	}
	
	?>
		<div style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#35a200" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'PASO 1: ' : 'STEP 1: ';?></b></font></label><label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Encuentra tu pedido' : 'Find your order';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Si lo requieres, puedes buscar el pedido de WooCommerce que deseas facturar.':'If you require, you can search the WooCommerce order you want to invoice.';?></font></label>
			<br/><br/>
			<table width="100%">
				<tr>
					<td>
						<table>
							<tr>
								<td style="width:36%">
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Fecha Inicial':'* Initial Date';?></font></label>
								</td>
								<td>
									<input type="date" id="fp_fechaInicial" name="fp_fechaInicial" value="<?php echo date("Y-m-01", strtotime(date("Y-m-d")));?>">
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td style="width:36%">
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Fecha Final':'* Final Date';?></font></label>
								</td>
								<td>
									<input type="date" id="fp_fechaFinal" name="fp_fechaFinal" value="<?php echo date("Y-m-t", strtotime(date("Y-m-d")));?>">
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td style="width:20%">
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Estado':'* Status';?></font></label>
								</td>
								<td>
									<select id="fp_estado_orden" name="fp_estado_orden" style="width:74%">
										<option value=""><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cualquier estado':'Any state';?></option>
										<option value="processing"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando':'Processing';?></option>
										<option value="completed" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Completado':'Completed';?></option>
										<option value="personalizado-1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 1 (Slug personalizado-1)':'Personalized State 1 (Slug personalizado-1)';?></option>
										<option value="personalizado-2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 2 (Slug personalizado-2)':'Personalized State 2 (Slug personalizado-2)';?></option>
										<option value="personalizado-3"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 3 (Slug personalizado-3)':'Personalized State 3 (Slug personalizado-3)';?></option>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<div>
							<input type="button" style="background-color:#e94700;" class="boton" id="boton_buscar_facturarpedidos"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Buscar':'Search';?>" />
							<img id="cargandoBuscarFacturarPedidos" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
						</div>
					</td>
				</tr>
			</table>
			<br/>
			<div style="width: 100%; height: 270px; overflow-y: scroll; border:1px solid #c9c9c9;">
			<table border="1" style="border-collapse: collapse; background-color:#FFFFFF; border-color:#a54107;" width="100%">
				<thead>
					<tr>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Pedido' : 'Order'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Fecha' : 'Date'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Estado Pedido' : 'Order Status'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Total' : 'Total'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Estado CFDI' : 'CFDI Status'; ?></font></td>
					</tr>
				</thead>
				<tbody id="catalogoPedidos_facturarpedidos"></tbody>
			</table>
			</div>
		</div>
		<input type="hidden" id="pedidosJSON_facturarpedidos" name="pedidosJSON_facturarpedidos" value="">
		
		<div id="ventanaModalFacturarPedidos" class="modalVentas">
			<div class="modal-contentVentas">
				<span id="closeFacturarPedidos" class="closeVentas">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalFacturarPedidos"></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalFacturarPedidos"></div>
					</font>
					<br/>
					<input type="button" style="background-color:#e94700;" class="boton" id="botonModalFacturarPedidos" value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Aceptar':'Accept';?>" />
				</center>
			</div>
		</div>
		
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				BuscarPedidosFacturacion();
				
				var NUMERO_ORDEN = '';
				var TOTAL_PEDIDO = '';
				
				$("#catalogoPedidos_facturarpedidos tr").click(function()
				{ 
					$(this).addClass('selected').siblings().removeClass('selected');    
					NUMERO_ORDEN = $(this).find('td:first-child').html();
					TOTAL_PEDIDO = $(this).find('td:nth-child(2)').html();
					
					console.log('Pedido seleccionado: ' + NUMERO_ORDEN);
					
					var numero_pedido = document.getElementById("numero_pedido");
					
					if(typeof(numero_pedido) != 'undefined' && numero_pedido != null)
					{
						numero_pedido.value = NUMERO_ORDEN;
						
						var monto_pedido = document.getElementById("monto_pedido");
						
						if(typeof(monto_pedido) != 'undefined' && monto_pedido != null)
							monto_pedido.value = TOTAL_PEDIDO;
					}
				});
				
				$('#boton_buscar_facturarpedidos').click(function(event)
				{
					BuscarPedidosFacturacion();
				});
				
				function BuscarPedidosFacturacion()
				{
					let fechaInicial = document.getElementById('fp_fechaInicial').value;
					let fechaFinal = document.getElementById('fp_fechaFinal').value;
					var estado_orden = document.getElementById('fp_estado_orden').value;
					
					/*if(estado_orden == '')
					{
						mostrarVentanaFacturarPedidos('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un estado de pedido.':'Select an order state.';?>');
						return;
					}*/
					
					document.getElementById('catalogoPedidos_facturarpedidos').innerHTML = '';
					document.getElementById('pedidosJSON_facturarpedidos').value = '';
							
					document.getElementById('cargandoBuscarFacturarPedidos').style.visibility = 'visible';
					
					data = 
					{
						action  				: 'realvirtual_woocommerce_buscar_facturarpedidos',
						fechaInicial			: fechaInicial,
						fechaFinal				: fechaFinal,
						estadoOrden				: estado_orden
					}
					
					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoBuscarFacturarPedidos').style.visibility = 'hidden';
						var response = JSON.parse(response);
						
						if(response.success == false)
						{
							mostrarVentanaFacturarPedidos(response.message);
							return;
						}
						else
						{
							document.getElementById('catalogoPedidos_facturarpedidos').innerHTML = response.pedidosHTML;
							document.getElementById('pedidosJSON_facturarpedidos').value = response.pedidosJSON;
							
							$("#catalogoPedidos_facturarpedidos tr").click(function()
							{ 
								$(this).addClass('selected').siblings().removeClass('selected');    
								NUMERO_ORDEN = $(this).find('td:first-child').html();
								TOTAL_PEDIDO = $(this).find('td:nth-child(2)').html();
								
								console.log('Pedido seleccionado: ' + NUMERO_ORDEN);
								
								var numero_pedido = document.getElementById("numero_pedido");
								
								if(typeof(numero_pedido) != 'undefined' && numero_pedido != null)
								{
									numero_pedido.value = NUMERO_ORDEN;
									
									var monto_pedido = document.getElementById("monto_pedido");
									
									if(typeof(monto_pedido) != 'undefined' && monto_pedido != null)
										monto_pedido.value = TOTAL_PEDIDO;
								}
							});
						}
					});
				}
				
				var modalFacturarPedidos = document.getElementById('ventanaModalFacturarPedidos');
				var spanFacturarPedidos = document.getElementById('closeFacturarPedidos');
				var botonFacturarPedidos = document.getElementById('botonModalFacturarPedidos');
					
				function mostrarVentanaFacturarPedidos(texto)
				{
					modalFacturarPedidos.style.display = "block";
					document.getElementById('tituloModalFacturarPedidos').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Aviso' : 'Notice'; ?>';
					document.getElementById('textoModalFacturarPedidos').innerHTML = texto;
				}
					
				botonFacturarPedidos.onclick = function()
				{
					modalFacturarPedidos.style.display = "none";
					document.getElementById('tituloModalFacturarPedidos').innerHTML = '';
					document.getElementById('textoModalFacturarPedidos').innerHTML = '';
				}
				
				spanFacturarPedidos.onclick = function()
				{
					modalFacturarPedidos.style.display = "none";
					document.getElementById('tituloModalFacturarPedidos').innerHTML = '';
					document.getElementById('textoModalFacturarPedidos').innerHTML = '';
				}
				
				window.onclick = function(event)
				{
					if (event.target == modalFacturarPedidos)
					{
						modalFacturarPedidos.style.display = "none";
						document.getElementById('textoModalFacturarPedidos').innerHTML = '';
					}
				}
			});
		</script>
	<?php
}

function realvirtual_woocommerce_dashboard()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $versionPlugin, $idiomaRVLFECFDI;
	
	?>
		<br/>
		<div style="background-color:#ffffff; padding-top: 20px; padding-right: 20px; padding-bottom: 5px; padding-left: 20px;">
        <font color="#000000" size="5"><b><?php echo esc_html($nombreSistema); ?></b></font><font color="#505050" size="2" style="font-style: italic;"><?php echo '&nbsp; '.($idiomaRVLFECFDI == 'ES' ? 'versión ' : 'version ').esc_html($versionPlugin); ?></font>
		
        <?php
			if(isset($_GET['tab']))
				$opcion = $_GET['tab'];
			else
				$opcion = 'cuenta';
		?>

        <h2>
			<font color="#000000" size="4"><a href="?page=realvirtual_woo_dashboard&tab=cuenta" style="text-decoration: <?php echo $opcion == 'cuenta' ? 'underline' : 'none'; ?>;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Mi Cuenta' : 'My Account'; ?></a>&nbsp;|</font>
            <font color="#000000" size="4"><a href="?page=realvirtual_woo_dashboard&tab=configuracion" style="text-decoration: <?php echo $opcion == 'configuracion' ? 'underline' : 'none'; ?>;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Configuración' : 'Configuration'; ?></a>&nbsp;|</font>
			<font color="#000000" size="4"><a href="?page=realvirtual_woo_dashboard&tab=ventas" style="text-decoration: <?php echo $opcion == 'ventas' ? 'underline' : 'none'; ?>;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'CFDI Emitidos' : 'Generated CFDI'; ?></a>&nbsp;|</font>
			<font color="#000000" size="4"><a href="?page=realvirtual_woo_dashboard&tab=facturaGlobal" style="text-decoration: <?php echo $opcion == 'facturaGlobal' ? 'underline' : 'none'; ?>;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Factura Global' : 'Global Invoice'; ?></a>&nbsp;|</font>
			<font color="#000000" size="4"><a href="?page=realvirtual_woo_dashboard&tab=centroIntegracion" style="text-decoration: <?php echo $opcion == 'centroIntegracion' ? 'underline' : 'none'; ?>;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Centro de Integración' : 'Integration Center'; ?></a>&nbsp;|</font>
			<font color="#000000" size="4"><a href="?page=realvirtual_woo_dashboard&tab=soporte" style="text-decoration: <?php echo $opcion == 'soporte' ? 'underline' : 'none'; ?>;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Soporte Técnico' : 'Support'; ?></a>&nbsp;|</font>
			<font color="#000000" size="4"><a href="?page=realvirtual_woo_dashboard&tab=preguntas" style="text-decoration: <?php echo $opcion == 'preguntas' ? 'underline' : 'none'; ?>;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Preguntas Frecuentes' : 'FAQ'; ?></a></font>
        </h2>
		</div>
		<br/>
		<div>
        <?php
			if($opcion == 'ventas')
                realvirtual_woocommerce_ventas();
			else if($opcion == 'cuenta')
                realvirtual_woocommerce_cuenta();
			else if($opcion == 'configuracion')
                realvirtual_woocommerce_configuracion();
			else if($opcion == 'soporte')
				realvirtual_woocommerce_soporte();
			else if($opcion == 'preguntas')
				realvirtual_woocommerce_preguntas();
			else if($opcion == 'facturaGlobal')
				realvirtual_woocommerce_facturaglobal();
			else if($opcion == 'centroIntegracion')
				realvirtual_woocommerce_centrointegracion();
        ?>
		</div>
	<?php
}

function realvirtual_woocommerce_datosFiscalesClientes()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		echo ($idiomaRVLFECFDI == 'ES') ? 'No se pueden obtener los Datos Fiscales de Clientes porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.' : 'Fiscal Data of Clients can not be obtained because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.';
		wp_die();
	}
	
	$datosFiscalesClientes = obtenerDatosFiscalesClientes();
	$datosFiscalesClientesHTML = '';
	
	foreach ($datosFiscalesClientes as $fila) 
	{
		$datosFiscalesClientesHTML .= '<tr>
			<td style="display:none;">'.$fila->id_user.'</td>
			<td style="display:none;">'.$fila->rfc.'</td>
			<td style="display:none;">'.$fila->razon_social.'</td>
			<td style="display:none;">'.$fila->domicilio_fiscal.'</td>
			<td style="display:none;">'.$fila->regimen_fiscal.'</td>
			<td style="display:none;">'.$fila->uso_cfdi.'</td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->id_user.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->rfc.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->razon_social.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->domicilio_fiscal.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->regimen_fiscal.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->uso_cfdi.'</font></td>
			</tr>';
	}
	
	?>
		<div style="background-color: #FFFFFF; padding: 20px;">
		<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Datos Fiscales de Clientes' : 'Fiscal Data of Clients'; ?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'En esta sección podrás gestionar los datos fiscales de tus clientes que hayan dado de alta esta información previamente en sus cuentas de usuario.':'In this section you can manage the tax data of your clients who have previously registered this information in their user accounts.';?></font></label>
		<br/><br/>
		
		<center>
		<table border="0" style="background-color:#e94700;" width="95%">
			<tr>
				<td style="width: 1px;white-space: nowrap;">
					<button style="background-color:#f75e14;" class="botonVentas" id="datosFiscalesClientes_boton_editar"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_update.gif"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Editar' : 'Edit'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="datosFiscalesClientes_boton_eliminar"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cancelar.gif"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Eliminar' : 'Delete'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="datosFiscalesClientes_boton_refresh"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_refresh.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2"></font></button>
					<img id="cargandoDatosFiscalesClientes" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando_ventas.gif"); ?>" alt="Cargando" height="16" width="16" style="visibility: hidden;">
				</td>
			</tr>
		</table>
		<table border="1" style="border-collapse: collapse; background-color:#FFFFFF; border-color:#a54107;" width="95%">
			<thead>
				<tr>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'ID Cliente' : 'Customer ID'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'RFC' : 'RFC'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Razón Social' : 'Business name'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Código Postal' : 'Postal Code'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Régimen Fiscal' : 'Fiscal Regime'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Uso CFDI' : 'CFDI Use'; ?></font></td>
				</tr>
			</thead>
			<tbody id="catalogoDatosFiscalesClientes"><?php echo $datosFiscalesClientesHTML; ?></tbody>
		</table>
		</center>
		<br/>
		</div>
		
		<div id="ventanaModalDatosFiscalesClientes" class="modalVentas">
			<div class="modal-contentVentas">
				<span id="closeModalDatosFiscalesClientes" class="closeVentas">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalDatosFiscalesClientes"></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalDatosFiscalesClientes"></div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalDatosFiscalesClientes" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Aceptar':'Accept';?>" />
				</center>
			</div>
		</div>
		
		<div id="ventanaModalDatosFiscalesClientes_Eliminar" class="modalCancelar">
			<div class="modal-contentCancelar">
				<span id="closeModalDatosFiscalesClientes_Eliminar" class="closeCancelar">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalDatosFiscalesClientes_Eliminar"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice';?></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalDatosFiscalesClientes_Eliminar"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Se eliminarán los datos fiscales del cliente. Si deseas que el cliente vuelva a tener datos fiscales, será necesario que el cliente los vuelva a registrar en su cuenta de usuario. ¿Deseas continuar?':"The client's fiscal data will be deleted. If you want the client to have tax data again, it will be necessary for the client to register them again in their user account. Do you want to continue?";?></div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalDatosFiscalesClientes_Eliminar_Si" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Sí':'Yes';?>" />
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalDatosFiscalesClientes_Eliminar_No" value="No" />
				</center>
			</div>
		</div>
		
		<div id="ventanaModalDatosFiscalesClientes_Editar" class="modalCancelar">
			<div class="modal-contentCancelar">
				<span id="closeModalDatosFiscalesClientes_Editar" class="closeCancelar">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalDatosFiscalesClientes_Cancelar"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Editar':'Edit';?></div>
					</b></font>
					<br/><br/>
					<font color="#000000" size="3">
						<div class="rowFiltrar">
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* RFC':'* RFC'; ?></font></label>
							<input type="text" id="txt_ModalDatosFiscalesClientes_rfc" name="txt_ModalDatosFiscalesClientes_rfc" value="">
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Razón Social':'* Business Name'; ?></font></label>
							<input type="text" id="txt_ModalDatosFiscalesClientes_razonsocial" name="txt_ModalDatosFiscalesClientes_razonsocial" value="">
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Código Postal':'* Postal Code'; ?></font></label>
							<input type="text" id="txt_ModalDatosFiscalesClientes_codigopostal" name="txt_ModalDatosFiscalesClientes_codigopostal" value="">
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Régimen Fiscal':'* Fiscal Regime'; ?></font></label>
							<select id="combo_ModalDatosFiscalesClientes_regimenfiscal" name="combo_ModalDatosFiscalesClientes_regimenfiscal">
								<option value="601">601 - General de Ley Personas Morales</option>
								<option value="603">603 - Personas Morales con Fines no Lucrativos</option>
								<option value="605">605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>
								<option value="606">606 - Arrendamiento</option>
								<option value="607">607 - Régimen de Enajenación o Adquisición de Bienes</option>
								<option value="608">608 - Demás ingresos</option>
								<option value="609">609 - Consolidación</option>
								<option value="610">610 - Residentes en el Extranjero sin Establecimiento Permanente en México</option>
								<option value="611">611 - Ingresos por Dividendos (socios y accionistas)</option>
								<option value="612">612 - Personas Físicas con Actividades Empresariales y Profesionales</option>
								<option value="614">614 - Ingresos por intereses</option>
								<option value="615">615 - Régimen de los ingresos por obtención de premios</option>
								<option value="616">616 - Sin obligaciones fiscales</option>
								<option value="620">620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>
								<option value="621">621 - Incorporación Fiscal</option>
								<option value="622">622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>
								<option value="623">623 - Opcional para Grupos de Sociedades</option>
								<option value="624">624 - Coordinados</option>
								<option value="625">625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>
								<option value="626">626 - Régimen Simplificado de Confianza</option>
								<option value="628">628 - Hidrocarburos</option>
								<option value="629">629 - De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales</option>
								<option value="630">630 - Enajenación de acciones en bolsa de valores</option>
							</select>
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Uso CFDI':'* CFDI Use'; ?></font></label>
							<select id="combo_ModalDatosFiscalesClientes_usocfdi" name="combo_ModalDatosFiscalesClientes_usocfdi">
								<option value="G01">G01 - Adquisición de mercancías</option>
								<option value="G02">G02 - Devoluciones, descuentos o bonificaciones</option>
								<option value="G03">G03 - Gastos en general</option>
								<option value="I01">I01 - Construcciones</option>
								<option value="I02">I02 - Mobiliario y equipo de oficina por inversiones</option>
								<option value="I03">I03 - Equipo de transporte</option>
								<option value="I04">I04 - Equipo de cómputo y accesorios</option>
								<option value="I05">I05 - Dados, troqueles, moldes, matrices y herramental</option>
								<option value="I06">I06 - Comunicaciones telefónicas</option>
								<option value="I07">I07 - Comunicaciones satelitales</option>
								<option value="I08">I08 - Otra maquinaria y equipo</option>
								<option value="D01">D01 - Honorarios médicos, dentales y gastos hospitalarios</option>
								<option value="D02">D02 - Gastos médicos por incapacidad o discapacidad</option>
								<option value="D03">D03 - Gastos funerales</option>
								<option value="D04">D04 - Donativos</option>
								<option value="D05">D05 - Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>
								<option value="D06">D06 - Aportaciones voluntarias al SAR</option>
								<option value="D07">D07 - Primas por seguros de gastos médicos</option>
								<option value="D08">D08 - Gastos de transportación escolar obligatoria</option>
								<option value="D09">D09 - Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones</option>
								<option value="D10">D10 - Pagos por servicios educativos (colegiaturas)</option>
								<!--<option value="P01">P01 - Por definir (Sólo CFDI 3.3)</option>-->
								<option value="S01">S01 - Sin efectos fiscales (Sólo CFDI 4.0)</option>
								<option value="CP01">CP01 - Pagos (Sólo CFDI 4.0)</option>
								<option value="CN01">CN01 - Nómina (Sólo CFDI 4.0)</option>
							</select>
						</div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalDatosFiscalesClientes_Editar_No" value="Cerrar" />
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalDatosFiscalesClientes_Editar_Si" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save';?>" />
				</center>
			</div>
		</div>
		
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				let ID_USUARIO = '';
				let RFC = '';
				let RAZON_SOCIAL = '';
				let CODIGO_POSTAL = '';
				let REGIMEN_FISCAL = '';
				let USO_CFDI = '';
				
				$("#catalogoDatosFiscalesClientes tr").click(function()
				{
					$(this).addClass('selected').siblings().removeClass('selected');    
					ID_USUARIO = $(this).find('td:first-child').html();
					RFC = $(this).find('td:nth-child(2)').html();
					RAZON_SOCIAL = $(this).find('td:nth-child(3)').html();
					CODIGO_POSTAL = $(this).find('td:nth-child(4)').html();
					REGIMEN_FISCAL = $(this).find('td:nth-child(5)').html();
					USO_CFDI = $(this).find('td:nth-child(6)').html();
				});
				
				$('#botonModalDatosFiscalesClientes_Eliminar_Si').click(function(event)
				{
					document.getElementById('cargandoDatosFiscalesClientes').style.visibility = 'visible';
					
					data =
					{
						action  			: 'realvirtual_woocommerce_eliminar_datosfiscales_cliente',
						ID_USUARIO   		: ID_USUARIO,
						IDIOMA				:  '<?php echo $idiomaRVLFECFDI; ?>'
					}

					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoDatosFiscalesClientes').style.visibility = 'hidden';
						var response = JSON.parse(response);
						
						if(response.success == true)
						{
							document.getElementById('ventanaModalDatosFiscalesClientes_Eliminar').style.display = "none";
							document.getElementById('catalogoDatosFiscalesClientes').innerHTML = response.datosFiscalesClientesHTML;
							$("#catalogoDatosFiscalesClientes tr").click(function()
							{
								$(this).addClass('selected').siblings().removeClass('selected');    
								ID_USUARIO = $(this).find('td:first-child').html();
								RFC = $(this).find('td:nth-child(2)').html();
								RAZON_SOCIAL = $(this).find('td:nth-child(3)').html();
								CODIGO_POSTAL = $(this).find('td:nth-child(4)').html();
								REGIMEN_FISCAL = $(this).find('td:nth-child(5)').html();
								USO_CFDI = $(this).find('td:nth-child(6)').html();
							});
							
							ID_USUARIO = '';
						}
						
						mostrarVentanaDatosFiscalesClientes(response.message);
						
					});
				});
				
				$('#botonModalDatosFiscalesClientes_Editar_Si').click(function(event)
				{
					document.getElementById('cargandoDatosFiscalesClientes').style.visibility = 'visible';
					
					data =
					{
						action  			: 'realvirtual_woocommerce_editar_datosfiscales_cliente',
						ID_USUARIO   		: ID_USUARIO,
						RFC   				: document.getElementById('txt_ModalDatosFiscalesClientes_rfc').value,
						RAZON_SOCIAL   		: document.getElementById('txt_ModalDatosFiscalesClientes_razonsocial').value,
						CODIGO_POSTAL   	: document.getElementById('txt_ModalDatosFiscalesClientes_codigopostal').value,
						REGIMEN_FISCAL   	: document.getElementById('combo_ModalDatosFiscalesClientes_regimenfiscal').value,
						USO_CFDI   			: document.getElementById('combo_ModalDatosFiscalesClientes_usocfdi').value,
						IDIOMA				:  '<?php echo $idiomaRVLFECFDI; ?>'
					}

					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoDatosFiscalesClientes').style.visibility = 'hidden';
						var response = JSON.parse(response);
						
						if(response.success == true)
						{
							document.getElementById('ventanaModalDatosFiscalesClientes_Editar').style.display = "none";
							document.getElementById('catalogoDatosFiscalesClientes').innerHTML = response.datosFiscalesClientesHTML;
							$("#catalogoDatosFiscalesClientes tr").click(function()
							{
								$(this).addClass('selected').siblings().removeClass('selected');    
								ID_USUARIO = $(this).find('td:first-child').html();
								RFC = $(this).find('td:nth-child(2)').html();
								RAZON_SOCIAL = $(this).find('td:nth-child(3)').html();
								CODIGO_POSTAL = $(this).find('td:nth-child(4)').html();
								REGIMEN_FISCAL = $(this).find('td:nth-child(5)').html();
								USO_CFDI = $(this).find('td:nth-child(6)').html();
							});
							
							ID_USUARIO = '';
						}
						
						mostrarVentanaDatosFiscalesClientes(response.message);
						
					});
				});
				
				$('#datosFiscalesClientes_boton_refresh').click(function(event)
				{
					document.getElementById('cargandoDatosFiscalesClientes').style.visibility = 'visible';
					
					data =
					{
						action  			: 'realvirtual_woocommerce_buscar_datosfiscales_cliente',
						IDIOMA				:  '<?php echo $idiomaRVLFECFDI; ?>'
					}

					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoDatosFiscalesClientes').style.visibility = 'hidden';
						var response = JSON.parse(response);
						
						if(response.success == true)
						{
							document.getElementById('catalogoDatosFiscalesClientes').innerHTML = response.datosFiscalesClientesHTML;
							
							$("#catalogoDatosFiscalesClientes tr").click(function()
							{
								$(this).addClass('selected').siblings().removeClass('selected');    
								ID_USUARIO = $(this).find('td:first-child').html();
								RFC = $(this).find('td:nth-child(2)').html();
								RAZON_SOCIAL = $(this).find('td:nth-child(3)').html();
								CODIGO_POSTAL = $(this).find('td:nth-child(4)').html();
								REGIMEN_FISCAL = $(this).find('td:nth-child(5)').html();
								USO_CFDI = $(this).find('td:nth-child(6)').html();
							});
							
							ID_USUARIO = '';
						}
						
						//mostrarVentanaDatosFiscalesClientes(response.message);
					});
				});
				
				$('#datosFiscalesClientes_boton_eliminar').click(function(event)
				{
					if(ID_USUARIO == '')
					{
						mostrarVentanaDatosFiscalesClientes('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un cliente.':'Select a customer.';?>');
						return;
					}
					
					mostrarVentanaEliminarDatosFiscales();
				});
				
				$('#datosFiscalesClientes_boton_editar').click(function(event)
				{
					if(ID_USUARIO == '')
					{
						mostrarVentanaDatosFiscalesClientes('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un cliente.':'Select a customer.';?>');
						return;
					}
					
					document.getElementById('txt_ModalDatosFiscalesClientes_rfc').value = RFC;
					document.getElementById('txt_ModalDatosFiscalesClientes_razonsocial').value = RAZON_SOCIAL;
					document.getElementById('txt_ModalDatosFiscalesClientes_codigopostal').value = CODIGO_POSTAL;
					document.getElementById('combo_ModalDatosFiscalesClientes_regimenfiscal').value = REGIMEN_FISCAL;
					document.getElementById('combo_ModalDatosFiscalesClientes_usocfdi').value = USO_CFDI;
					
					mostrarVentanaEditarDatosFiscales();
				});
				
				let modalDatosFiscalesClientes = document.getElementById('ventanaModalDatosFiscalesClientes');
				let spanDatosFiscalesClientes = document.getElementById('closeModalDatosFiscalesClientes');
				let botonDatosFiscalesClientes = document.getElementById('botonModalDatosFiscalesClientes');
				
				let modalDatosFiscalesClientes_Eliminar = document.getElementById('ventanaModalDatosFiscalesClientes_Eliminar');
				let spanDatosFiscalesClientes_Eliminar = document.getElementById('closeModalDatosFiscalesClientes_Eliminar');
				//let botonDatosFiscalesClientes_Eliminar_Si = document.getElementById('botonModalDatosFiscalesClientes_Eliminar_Si');
				let botonDatosFiscalesClientes_Eliminar_No = document.getElementById('botonModalDatosFiscalesClientes_Eliminar_No');
				
				let modalDatosFiscalesClientes_Editar = document.getElementById('ventanaModalDatosFiscalesClientes_Editar');
				let spanDatosFiscalesClientes_Editar = document.getElementById('closeModalDatosFiscalesClientes_Editar');
				//let botonDatosFiscalesClientes_Editar_Si = document.getElementById('botonModalDatosFiscalesClientes_Editar_Si');
				let botonDatosFiscalesClientes_Editar_No = document.getElementById('botonModalDatosFiscalesClientes_Editar_No');
				
				function mostrarVentanaDatosFiscalesClientes(texto)
				{
					modalDatosFiscalesClientes.style.display = "block";
					document.getElementById('tituloModalDatosFiscalesClientes').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Aviso' : 'Notice'; ?>';
					document.getElementById('textoModalDatosFiscalesClientes').innerHTML = texto;
				}
				
				botonDatosFiscalesClientes.onclick = function()
				{
					modalDatosFiscalesClientes.style.display = "none";
					document.getElementById('tituloModalDatosFiscalesClientes').innerHTML = '';
					document.getElementById('textoModalDatosFiscalesClientes').innerHTML = '';
				}
				
				spanDatosFiscalesClientes.onclick = function()
				{
					modalDatosFiscalesClientes.style.display = "none";
					document.getElementById('tituloModalDatosFiscalesClientes').innerHTML = '';
					document.getElementById('textoModalDatosFiscalesClientes').innerHTML = '';
				}
				
				function mostrarVentanaEliminarDatosFiscales()
				{
					modalDatosFiscalesClientes_Eliminar.style.display = "block";
				}
				
				/*botonDatosFiscalesClientes_Eliminar_Si.onclick = function()
				{
					modalDatosFiscalesClientes_Eliminar.style.display = "none";
				}*/
				
				botonDatosFiscalesClientes_Eliminar_No.onclick = function()
				{
					modalDatosFiscalesClientes_Eliminar.style.display = "none";
				}
				
				spanDatosFiscalesClientes_Eliminar.onclick = function()
				{
					modalDatosFiscalesClientes_Eliminar.style.display = "none";
				}
				
				function mostrarVentanaEditarDatosFiscales()
				{
					modalDatosFiscalesClientes_Editar.style.display = "block";
				}
				
				/*botonDatosFiscalesClientes_Editar_Si.onclick = function()
				{
					modalDatosFiscalesClientes_Editar.style.display = "none";
				}*/
				
				botonDatosFiscalesClientes_Editar_No.onclick = function()
				{
					modalDatosFiscalesClientes_Editar.style.display = "none";
				}
				
				spanDatosFiscalesClientes_Editar.onclick = function()
				{
					modalDatosFiscalesClientes_Editar.style.display = "none";
				}
			});
		</script>
	<?php
}

function realvirtual_woocommerce_ventas()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		/*$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se pueden obtener las ventas realizadas porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.' : 'Sales can not be obtained because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);*/
		echo ($idiomaRVLFECFDI == 'ES') ? 'No se pueden obtener los CFDI emitidos porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.' : 'Generated CFDI can not be obtained because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.';
		wp_die();
	}
	
	$filtro = '||||||';
	$datosVentas = RealVirtualWooCommerceCFDI::obtenerVentas($cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $filtro, $sistema, $urlSistemaAsociado, $idiomaRVLFECFDI);
	
	?>
		<div style="background-color: #FFFFFF; padding: 20px;">
		<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'CFDI Emitidos' : 'Generated CFDI'; ?></b></font></label>
		<br/>
		<font color="#000000" size="2"><div id="timbres_folios"><?php echo ($datosVentas->TIMBRES_FOLIOS); ?></div></font>
		<br/>
		<div style="text-align: right;"><font color="#000000" size="2"><label id="total_cfdi_ventas"><?php echo ($datosVentas->success == true) ? esc_html($datosVentas->TOTAL_CFDI) : esc_html(0) ?><?php echo ($idiomaRVLFECFDI == 'ES') ? ' CFDI encontrados' : ' CFDI found'; ?></label><label id="ingresos_ventas" style="padding-left: 3em;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ingresos:':'Income:'; ?> <b>$<?php echo esc_html($datosVentas->INGRESOS); ?></label></b><label id="iva_ventas" style="padding-left: 3em;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'IVA:':'IVA (VAT):'; ?> <b>$<?php echo esc_html($datosVentas->IVA); ?></label></b><label id="total_ventas" style="padding-left: 3em; padding-right: 3em;">Total: <b>$<?php echo esc_html($datosVentas->TOTAL); ?></label></b></font></div>
		
		<center>
		<table border="0" style="background-color:#e94700;" width="95%">
			<tr>
				<td style="width: 1px;white-space: nowrap;">
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_xml"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_xml.PNG"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Descargar XML' : 'Download XML'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_pdf"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_pdf.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Descargar PDF' : 'Download PDF'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_enviar"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_enviar.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Enviar' : 'Send'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_cancelar"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cancelar.gif"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Cancelar' : 'Cancel'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_acuse"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_acuse.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Acuse de cancelación' : 'Cancellation acknowledgment'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_reporte_excel"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_excel.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Reporte Excel' : 'Excel Report'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_reporte_BAYER_facturacion"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_grid.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Reporte BAYER Facturación' : 'Reporte BAYER Facturación'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_reporte_BAYER_financiero"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_grid.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Reporte BAYER Financiero' : 'Reporte BAYER Financiero'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_reporte_TIBA"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_excel.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Reporte TIBA' : 'TIBA Report'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_filtrar"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_filtrar.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2">&nbsp;<?php echo($idiomaRVLFECFDI == 'ES') ? 'Filtrar' : 'Filter'; ?></font></button>
					<button style="background-color:#f75e14;" class="botonVentas" id="ventas_boton_refresh"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_refresh.png"); ?>" height="16" width="16"><font color="#FFFFFF" size="2"></font></button>
					<img id="cargandoVentas" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando_ventas.gif"); ?>" alt="Cargando" height="16" width="16" style="visibility: hidden;">
				</td>
			</tr>
		</table>
		<table border="1" style="border-collapse: collapse; background-color:#FFFFFF; border-color:#a54107;" width="95%">
			<thead>
				<tr>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Versión' : 'Version'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Pedido' : 'Order'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Folio' : 'Folio'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Folio Fiscal (UUID)' : 'Fiscal Folio (UUID)'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Fecha Emisión' : 'Generate Date'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Cliente' : 'Customer'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Ingresos' : 'Income'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'IVA' : 'IVA (VAT)'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'IEPS' : 'IEPS'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Total' : 'Total'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Moneda' : 'Currency'; ?></font></td>
					<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Estado' : 'Status'; ?></font></td>
				</tr>
			</thead>
			<tbody id="catalogoFacturas"><?php echo $datosVentas->VENTAS; ?></tbody>
		</table>
		</center>
		<br/>
		<font color="#505050" size="2" style="font-style: italic;"><b><?php echo($idiomaRVLFECFDI == 'ES') ? 'NOTAS:':'NOTES:'; ?></b><br/><br/><?php echo($idiomaRVLFECFDI == 'ES') ? '1) Puedes administrar tus CFDI directamente en':'1) You can manage your CFDI directly in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a>.<br/><?php echo($idiomaRVLFECFDI == 'ES') ? '2) Por defecto sólo se muestran los CFDI emitidos en el año actual.<br/>3) Pulsa el botón Filtrar para consultar los CFDI que desees.':'2) By default only the generated CFDIs in the current year are displayed.<br/>3) Press the Filter button to consult the CFDIs you want.';?><br/><br/> 
		</div>
		
		<div id="ventanaModalVentas" class="modalVentas">
			<div class="modal-contentVentas">
				<span class="closeVentas">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalVentas"></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalVentas"></div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalVentas" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Aceptar':'Accept';?>" />
				</center>
			</div>
		</div>
		
		<!--<div id="ventanaModalCancelar" class="modalCancelar">
			<div class="modal-contentCancelar">
				<span class="closeCancelar">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalCancelar"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice';?></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalCancelar"><?php echo($idiomaRVLFECFDI == 'ES') ? '¿Estás seguro de cancelar este CFDI?':'Are you sure to cancel this CFDI?';?></div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalCancelarSi" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Sí':'Yes';?>" />
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalCancelarNo" value="No" />
				</center>
			</div>
		</div>-->
		
		<div id="ventanaModalCancelar" class="modalCancelar">
			<div class="modal-contentCancelar">
				<span class="closeCancelar">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalCancelar"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Cancelar CFDI':'Cancel CFDI';?></div>
					</b></font>
					<br/><br/>
					<font color="#000000" size="3">
						<div class="rowFiltrar">
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Motivo':'* Reason'; ?></font></label>
							<select id="cancelacion_detalle_motivo" name="cancelacion_detalle_motivo">
								<option value="01">01 - Comprobante emitido con errores con relación</option>
								<option value="02">02 - Comprobante emitido con errores sin relación</option>
								<option value="03" selected>03 - No se llevó a cabo la operación</option>
								<option value="04">04 - Operación nominativa relacionada en una factura global</option>
							</select>
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Folio Sustitución':'Replacement Folio'; ?></font></label>
							<input type="text" id="cancelacion_detalle_foliosustitucion" name="cancelacion_detalle_foliosustitucion" value="">
						</div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalCancelarNo" value="Cerrar" />
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalCancelarSi" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Cancelar CFDI':'Cancel CFDI';?>" />
				</center>
			</div>
		</div>
		
		<div id="ventanaModalReporteExcel" class="modalCancelar">
			<div class="modal-contentCancelar">
				<span id="closeModalReporteExcel" class="closeCancelar">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalReporteExcel"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice';?></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalReporteExcel"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Se descargará el reporte de CFDI Emitidos en un archivo de Excel en base al filtro de búsqueda definido en la opción <b>Filtrar</b>. El proceso puede demorar unos segundos dependiendo del volúmen de CFDI en el reporte. ¿Deseas continuar?':'The CFDI Issued report will be downloaded into an Excel file based on the search filter defined in the <b>Filter</b> option. The process may take a few seconds depending on the volume of CFDI in the report. Do you want to continue?';?></div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalReporteExcelSi" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Sí':'Yes';?>" />
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalReporteExcelNo" value="No" />
				</center>
			</div>
		</div>
		
		<div id="ventanaModalReporteExcelTIBA" class="modalCancelar">
			<div class="modal-contentCancelar">
				<span id="closeModalReporteExcelTIBA" class="closeCancelar">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalReporteExcelTIBA"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice';?></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalReporteExcelTIBA"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Se descargará el reporte de CFDI Emitidos en un archivo Excel en base al filtro de búsqueda definido en la opción <b>Filtrar</b>. El proceso puede demorar unos segundos dependiendo del volúmen de CFDI en el reporte. ¿Deseas continuar?':'The CFDI Issued report will be downloaded into an Excel file based on the search filter defined in the <b>Filter</b> option. The process may take a few seconds depending on the volume of CFDI in the report. Do you want to continue?';?></div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalReporteExcelTIBASi" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Sí':'Yes';?>" />
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalReporteExcelTIBANo" value="No" />
				</center>
			</div>
		</div>
		
		<div id="ventanaModalReporteBAYERFacturacion" class="modalCancelar">
			<div class="modal-contentCancelar">
				<span id="closeModalReporteBAYERFacturacion" class="closeCancelar">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5">
						<b>
							<div id="tituloModalReporteBAYERFacturacion"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Reporte BAYER Facturación':'Reporte BAYER Facturación';?></div>
						</b>
					</font>
					<br/>
					<label><font color="#000000" size="3"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Establece el rango de fechas de consulta para generar el reporte.':'Establece el rango de fechas de consulta para generar el reporte.';?></font></label>
					<br/>
					<br/>
					<table width="60%">
						<tr>
							<td>
								<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Fecha Inicial':'* Initial Date';?></font></label>
							</td>
							<td>
								<select id="fg_dia_inicio_bayer_facturacion" name="fg_dia_inicio_bayer_facturacion">
									<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Día':'Day'; ?></option>
									<?php 
										for($i = 1; $i <= 31; $i++)
										{
											if($i < 10)
											{
												?>
													<option value="<?php echo esc_html("0".$i); ?>"><?php echo esc_html("0".$i); ?></option>
												<?php 
											}
											else
											{
												?>
													<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
												<?php 
											}
										}
									?>
								</select>
								<select id="fg_mes_inicio_bayer_facturacion" name="fg_mes_inicio_bayer_facturacion">
									<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Mes':'Month'; ?></option>
									<option value="01"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Enero':'January'; ?></option>
									<option value="02"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Febrero':'February'; ?></option>
									<option value="03"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Marzo':'March'; ?></option>
									<option value="04"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Abril':'April'; ?></option>
									<option value="05"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Mayo':'May'; ?></option>
									<option value="06"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Junio':'June'; ?></option>
									<option value="07"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Julio':'July'; ?></option>
									<option value="08"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Agosto':'August'; ?></option>
									<option value="09"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Septiembre':'September'; ?></option>
									<option value="10"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Octubre':'October'; ?></option>
									<option value="11"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Noviembre':'November'; ?></option>
									<option value="12"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Diciembre':'December'; ?></option>
								</select>
								<select id="fg_año_inicio_bayer_facturacion" name="fg_año_inicio_bayer_facturacion">
									<?php
										$añoInicial = 2017;
										$añoActual = date("Y");
														
										for($i = $añoInicial; $i <= $añoActual; $i++)
														
										if($i == date("Y"))
										{
										?>
											<option value="<?php echo esc_html($i); ?>" selected><?php echo esc_html($i); ?></option>
										<?php 
										}
										else
										{
										?>
											<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
										<?php 
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Fecha Final':'* Final Date';?></font></label>
							</td>
							<td>
								<select id="fg_dia_fin_bayer_facturacion" name="fg_dia_fin_bayer_facturacion">
									<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Día':'Day'; ?></option>
									<?php 
										for($i = 1; $i <= 31; $i++)
										{
											if($i < 10)
											{
												?>
													<option value="<?php echo esc_html("0".$i); ?>"><?php echo esc_html("0".$i); ?></option>
												<?php 
											}
											else
											{
												?>
													<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
												<?php 
											}
										}
									?>
								</select>
								<select id="fg_mes_fin_bayer_facturacion" name="fg_mes_fin_bayer_facturacion">
									<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Mes':'Month'; ?></option>
									<option value="01"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Enero':'January'; ?></option>
									<option value="02"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Febrero':'February'; ?></option>
									<option value="03"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Marzo':'March'; ?></option>
									<option value="04"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Abril':'April'; ?></option>
									<option value="05"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Mayo':'May'; ?></option>
									<option value="06"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Junio':'June'; ?></option>
									<option value="07"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Julio':'July'; ?></option>
									<option value="08"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Agosto':'August'; ?></option>
									<option value="09"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Septiembre':'September'; ?></option>
									<option value="10"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Octubre':'October'; ?></option>
									<option value="11"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Noviembre':'November'; ?></option>
									<option value="12"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Diciembre':'December'; ?></option>
								</select>
								<select id="fg_año_fin_bayer_facturacion" name="fg_año_fin_bayer_facturacion">
									<?php
										$añoInicial = 2017;
										$añoActual = date("Y");
														
										for($i = $añoInicial; $i <= $añoActual; $i++)
														
										if($i == date("Y"))
										{
										?>
											<option value="<?php echo esc_html($i); ?>" selected><?php echo esc_html($i); ?></option>
										<?php 
										}
										else
										{
										?>
											<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
										<?php 
										}
									?>
								</select>
							</td>
						</tr>
					</table>
					<br/>
					<font color="#000000" size="2">
						<div id="textoModalReporteBAYERFacturacion"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Se descargará el reporte de Facturación en un archivo de texto (.txt) en base al filtro de búsqueda definido. El proceso puede demorar unos segundos dependiendo del volúmen de datos en el reporte. ¿Deseas continuar?':'Se descargará el reporte de Facturación en un archivo de texto (.txt) en base al filtro de búsqueda definido. El proceso puede demorar unos segundos dependiendo del volúmen de datos en el reporte. ¿Deseas continuar?';?></div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalReporteBAYERFacturacionSi" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Sí':'Yes';?>" />
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalReporteBAYERFacturacionNo" value="No" />
				</center>
			</div>
		</div>
		
		<div id="ventanaModalReporteBAYERFinanciero" class="modalCancelar">
			<div class="modal-contentCancelar">
				<span id="closeModalReporteBAYERFinanciero" class="closeCancelar">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5">
						<b>
							<div id="tituloModalReporteBAYERFinanciero"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Reporte BAYER Financiero':'Reporte BAYER Financiero';?></div>
						</b>
					</font>
					<br/>
					<label><font color="#000000" size="3"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Establece el rango de fechas de consulta para generar el reporte.':'Establece el rango de fechas de consulta para generar el reporte.';?></font></label>
					<br/>
					<br/>
					<table width="60%">
						<tr>
							<td>
								<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Fecha Inicial':'* Initial Date';?></font></label>
							</td>
							<td>
								<select id="fg_dia_inicio_bayer_financiero" name="fg_dia_inicio_bayer_financiero">
									<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Día':'Day'; ?></option>
									<?php 
										for($i = 1; $i <= 31; $i++)
										{
											if($i < 10)
											{
												?>
													<option value="<?php echo esc_html("0".$i); ?>"><?php echo esc_html("0".$i); ?></option>
												<?php 
											}
											else
											{
												?>
													<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
												<?php 
											}
										}
									?>
								</select>
								<select id="fg_mes_inicio_bayer_financiero" name="fg_mes_inicio_bayer_financiero">
									<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Mes':'Month'; ?></option>
									<option value="01"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Enero':'January'; ?></option>
									<option value="02"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Febrero':'February'; ?></option>
									<option value="03"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Marzo':'March'; ?></option>
									<option value="04"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Abril':'April'; ?></option>
									<option value="05"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Mayo':'May'; ?></option>
									<option value="06"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Junio':'June'; ?></option>
									<option value="07"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Julio':'July'; ?></option>
									<option value="08"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Agosto':'August'; ?></option>
									<option value="09"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Septiembre':'September'; ?></option>
									<option value="10"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Octubre':'October'; ?></option>
									<option value="11"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Noviembre':'November'; ?></option>
									<option value="12"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Diciembre':'December'; ?></option>
								</select>
								<select id="fg_año_inicio_bayer_financiero" name="fg_año_inicio_bayer_financiero">
									<?php
										$añoInicial = 2017;
										$añoActual = date("Y");
														
										for($i = $añoInicial; $i <= $añoActual; $i++)
														
										if($i == date("Y"))
										{
										?>
											<option value="<?php echo esc_html($i); ?>" selected><?php echo esc_html($i); ?></option>
										<?php 
										}
										else
										{
										?>
											<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
										<?php 
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Fecha Inicial':'* Initial Date';?></font></label>
							</td>
							<td>
								<select id="fg_dia_fin_bayer_financiero" name="fg_dia_fin_bayer_financiero">
									<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Día':'Day'; ?></option>
									<?php 
										for($i = 1; $i <= 31; $i++)
										{
											if($i < 10)
											{
												?>
													<option value="<?php echo esc_html("0".$i); ?>"><?php echo esc_html("0".$i); ?></option>
												<?php 
											}
											else
											{
												?>
													<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
												<?php 
											}
										}
									?>
								</select>
								<select id="fg_mes_fin_bayer_financiero" name="fg_mes_fin_bayer_financiero">
									<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Mes':'Month'; ?></option>
									<option value="01"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Enero':'January'; ?></option>
									<option value="02"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Febrero':'February'; ?></option>
									<option value="03"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Marzo':'March'; ?></option>
									<option value="04"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Abril':'April'; ?></option>
									<option value="05"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Mayo':'May'; ?></option>
									<option value="06"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Junio':'June'; ?></option>
									<option value="07"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Julio':'July'; ?></option>
									<option value="08"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Agosto':'August'; ?></option>
									<option value="09"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Septiembre':'September'; ?></option>
									<option value="10"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Octubre':'October'; ?></option>
									<option value="11"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Noviembre':'November'; ?></option>
									<option value="12"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Diciembre':'December'; ?></option>
								</select>
								<select id="fg_año_fin_bayer_financiero" name="fg_año_fin_bayer_financiero">
									<?php
										$añoInicial = 2017;
										$añoActual = date("Y");
														
										for($i = $añoInicial; $i <= $añoActual; $i++)
														
										if($i == date("Y"))
										{
										?>
											<option value="<?php echo esc_html($i); ?>" selected><?php echo esc_html($i); ?></option>
										<?php 
										}
										else
										{
										?>
											<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
										<?php 
										}
									?>
								</select>
							</td>
						</tr>
					</table>
					<br/>
					<font color="#000000" size="2">
						<div id="textoModalReporteBAYERFinanciero"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Se descargará el reporte de Facturación en un archivo de Excel (.xlsx) en base al filtro de búsqueda definido. El proceso puede demorar unos segundos dependiendo del volúmen de datos en el reporte. ¿Deseas continuar?':'Se descargará el reporte de Facturación en un archivo de texto (.txt) en base al filtro de búsqueda definido. El proceso puede demorar unos segundos dependiendo del volúmen de datos en el reporte. ¿Deseas continuar?';?></div>
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalReporteBAYERFinancieroSi" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Sí':'Yes';?>" />
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalReporteBAYERFinancieroNo" value="No" />
				</center>
			</div>
		</div>
		
		<div id="ventanaModalFiltrar" class="modalFiltrar">
			<div class="modal-contentFiltrar">
				<span class="closeFiltrar">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalFiltrar"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Filtrar':'Filter';?></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div class="rowFiltrar">
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Año': '* Year';?></font></label>
							<select id="año_ventas" name="año_ventas">
							<?php
								$añoInicial = 2017;
								$añoActual = date("Y");
								
								for($i = $añoInicial; $i <= $añoActual; $i++)
								
								if($i == date("Y"))
								{
								?>
									<option value="<?php echo esc_html($i); ?>" selected><?php echo esc_html($i); ?></option>
								<?php 
								}
								else
								{
								?>
									<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
								<?php 
								}
							?>
							</select>
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Mes':'* Month'; ?></font></label>
							<select id="mes_ventas" name="mes_ventas">
								<option value="01"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Enero':'January'; ?></option>
								<option value="02"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Febrero':'February'; ?></option>
								<option value="03"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Marzo':'March'; ?></option>
								<option value="04"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Abril':'April'; ?></option>
								<option value="05"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Mayo':'May'; ?></option>
								<option value="06"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Junio':'June'; ?></option>
								<option value="07"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Julio':'July'; ?></option>
								<option value="08"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Agosto':'August'; ?></option>
								<option value="09"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Septiembre':'September'; ?></option>
								<option value="10"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Octubre':'October'; ?></option>
								<option value="11"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Noviembre':'November'; ?></option>
								<option value="12"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Diciembre':'December'; ?></option>
								<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Todo el año':'All year'; ?></option>
							</select>
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Estado':'* Status'; ?></font></label>
							<select id="estado_ventas" name="estado_ventas">
								<option value="1"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Vigentes':'Current'; ?></option>
								<option value="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Cancelados':'Canceled'; ?></option>
								<option value="3"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Por Cancelar':'Pending Cancellation'; ?></option>
								<option value="" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Vigentes, Cancelados y Por Cancelar':'Current, Canceled and Pending Cancellation'; ?></option>
							</select>
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Versión':'* Version'; ?></font></label>
							<select id="version_ventas" name="version_ventas">
								<option value="4.0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? '4.0':'4.0'; ?></option>
								<option value="3.3"><?php echo($idiomaRVLFECFDI == 'ES') ? '3.3':'3.3'; ?></option>
								<option value="3.2"><?php echo($idiomaRVLFECFDI == 'ES') ? '3.2':'3.2'; ?></option>
								<option value=""><?php echo($idiomaRVLFECFDI == 'ES') ? 'Todas':'All'; ?></option>
							</select>
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? '* Fuente de Emisión':'* Generation Source'; ?></font></label>
							<select id="fuente_ventas" name="fuente_ventas">
								<option value="1"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Emitidos a través del plugin '.$nombreSistema :'Generated through the '.$nombreSistema.' plugin'; ?></option>
								<option value="0"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Emitidos a través del sistema principal '.$nombreSistemaAsociado :'Generated through the '.$nombreSistemaAsociado.' official system'; ?></option>
								<option value="" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Emitidos en ambos sistemas':'Generated on both systems'; ?></option>
							</select>
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Número de Pedido':'Order Number'; ?></font></label>
							<input type="text" id="numero_pedido_ventas" name="numero_pedido_ventas" value="" placeholder="Sin símbolo #">
							<br/>
							<label><font color="#000000"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Cliente':'Customer'; ?></font></label>
							<input type="text" id="cliente_ventas" name="cliente_ventas" value="">
						</div>	
					</font>
					<br/>
					<input type="button" style="background-color:#f75e14;" class="boton" id="botonModalFiltrar" value="<?php echo($idiomaRVLFECFDI == 'ES') ? 'Aceptar':'Accept';?>" />
				</center>
			</div>
		</div>
				
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var urlSistemaAsociado = '<?php echo esc_url($urlSistemaAsociado); ?>';
				var nombreSistemaAsociado = '<?php echo esc_html($nombreSistemaAsociado); ?>';
				var urlSistemaAsociado = '<?php echo esc_url($urlSistemaAsociado); ?>';
				var RFC_EMISOR = '<?php echo RealVirtualWooCommerceCuenta::cuentaEntidad()["rfc"]; ?>';
				var USUARIO_EMISOR = '<?php echo RealVirtualWooCommerceCuenta::cuentaEntidad()["usuario"]; ?>';
				var CLAVE_EMISOR = '<?php echo RealVirtualWooCommerceCuenta::cuentaEntidad()["clave"]; ?>';
				var CFDI_ID = '';
				var CFDI_UUID = '';
				var RECEPTOR_EMAIL = '';
				var EMISOR_ID = '';
				var CFDI_ESTADO = '';
				
				if(RFC_EMISOR == "LCO160722HA6")
				{
					$("#ventas_boton_reporte_TIBA").show();
				}
				else
				{
					$("#ventas_boton_reporte_TIBA").hide();
				}
				
				if(RFC_EMISOR == "MCO701113C5A"/* || RFC_EMISOR == "XIA190128J61"*/)
				{
					$("#ventas_boton_reporte_BAYER_facturacion").show();
					$("#ventas_boton_reporte_BAYER_financiero").show();
				}
				else
				{
					$("#ventas_boton_reporte_BAYER_facturacion").hide();
					$("#ventas_boton_reporte_BAYER_financiero").hide();
				}
				
				$("#catalogoFacturas tr").click(function()
				{ 
					$(this).addClass('selected').siblings().removeClass('selected');    
					CFDI_ID = $(this).find('td:first-child').html();
					CFDI_UUID = $(this).find('td:nth-child(2)').html();
					RECEPTOR_EMAIL = $(this).find('td:nth-child(4)').html();
					EMISOR_ID = $(this).find('td:nth-child(5)').html();
					CFDI_ESTADO = $(this).find('td:nth-child(6)').html();
				});
				
				$('#ventas_boton_xml').click(function(event)
				{
					if(CFDI_ID == '')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un CFDI.':'Select a CFDI.';?>');
						return;
					}
					
					location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarXML&CFDI_ID=' + CFDI_ID + '&IDIOMA=' + '<?php echo $idiomaRVLFECFDI; ?>';
				});
				
				$('#ventas_boton_pdf').click(function(event)
				{
					if(CFDI_ID == '')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un CFDI.':'Select a CFDI.';?>');
						return;
					}
					
					location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarPDF33&CFDI_ID=' + CFDI_ID + '&IDIOMA=' + '<?php echo $idiomaRVLFECFDI; ?>';
				});
				
				$('#ventas_boton_reporte_excel').click(function(event)
				{
					mostrarVentanaReporteExcel();
				});
				
				$('#botonModalReporteExcelSi').click(function(event)
				{
					var año = document.getElementById('año_ventas').value;
					var mes = document.getElementById('mes_ventas').value;
					var estado = document.getElementById('estado_ventas').value;
					var numero_pedido = document.getElementById('numero_pedido_ventas').value;
					var cliente = document.getElementById('cliente_ventas').value;
					var fuente = document.getElementById('fuente_ventas').value;
					var version = document.getElementById('version_ventas').value;
					
					location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarReporteExcel&'	
														+ 'ANIO=' + año + '&MES=' + mes + '&ESTADO=' + estado + '&NUMERO_PEDIDO=' + numero_pedido 
														+ '&RFC_EMISOR=' + RFC_EMISOR + '&USUARIO_EMISOR=' + USUARIO_EMISOR + '&CLAVE_EMISOR=' + CLAVE_EMISOR
														+ '&CLIENTE=' + cliente + '&FUENTE=' + fuente + '&VERSION=' + version + '&IDIOMA=' + '<?php echo $idiomaRVLFECFDI; ?>';
				});
				
				$('#ventas_boton_reporte_TIBA').click(function(event)
				{
					mostrarVentanaReporteExcelTIBA();
				});
				
				$('#ventas_boton_reporte_BAYER_facturacion').click(function(event)
				{
					mostrarVentanaReporteBAYERFacturacion();
				});
				
				$('#ventas_boton_reporte_BAYER_financiero').click(function(event)
				{
					mostrarVentanaReporteBAYERFinanciero();
				});
				
				$('#botonModalReporteExcelTIBASi').click(function(event)
				{
					var año = document.getElementById('año_ventas').value;
					var mes = document.getElementById('mes_ventas').value;
					var estado = document.getElementById('estado_ventas').value;
					var numero_pedido = document.getElementById('numero_pedido_ventas').value;
					var cliente = document.getElementById('cliente_ventas').value;
					var fuente = document.getElementById('fuente_ventas').value;
					var version = document.getElementById('version_ventas').value;
					
					location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarReporteExcelTIBA&'	
														+ 'ANIO=' + año + '&MES=' + mes + '&ESTADO=' + estado + '&NUMERO_PEDIDO=' + numero_pedido 
														+ '&RFC_EMISOR=' + RFC_EMISOR + '&USUARIO_EMISOR=' + USUARIO_EMISOR + '&CLAVE_EMISOR=' + CLAVE_EMISOR
														+ '&CLIENTE=' + cliente + '&FUENTE=' + fuente + '&VERSION=' + version + '&IDIOMA=' + '<?php echo $idiomaRVLFECFDI; ?>';
				});
				
				$('#botonModalReporteBAYERFacturacionSi').click(function(event)
				{
					var rvcfdi_bayer_facturacion_c_clase_documento = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_c_clase_documento"]); ?>';
					var rvcfdi_bayer_facturacion_c_sociedad = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_c_sociedad"]); ?>';
					var rvcfdi_bayer_facturacion_c_moneda = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_c_moneda"]); ?>';
					var rvcfdi_bayer_facturacion_c_tc_cab_doc = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_c_tc_cab_doc"]); ?>';
					var rvcfdi_bayer_facturacion_p_cuenta = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_cuenta"]); ?>';
					var rvcfdi_bayer_facturacion_p_division = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_division"]); ?>';
					var rvcfdi_bayer_facturacion_p_ce_be = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_ce_be"]); ?>';
					var rvcfdi_bayer_facturacion_p_texto = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_texto"]); ?>';
					var rvcfdi_bayer_facturacion_p_pais_destinatario = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_pais_destinatario"]); ?>';
					var rvcfdi_bayer_facturacion_p_linea_de_producto = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_linea_de_producto"]); ?>';
					var rvcfdi_bayer_facturacion_p_grupo_de_producto = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_grupo_de_producto"]); ?>';
					var rvcfdi_bayer_facturacion_p_centro = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_centro"]); ?>';
					var rvcfdi_bayer_facturacion_p_cliente = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_cliente"]); ?>';
					var rvcfdi_bayer_facturacion_p_organiz_ventas = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_organiz_ventas"]); ?>';
					var rvcfdi_bayer_facturacion_p_canal_distrib = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_canal_distrib"]); ?>';
					var rvcfdi_bayer_facturacion_p_zoha_de_ventas = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_zoha_de_ventas"]); ?>';
					var rvcfdi_bayer_facturacion_p_oficina_ventas = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_oficina_ventas"]); ?>';
					var rvcfdi_bayer_facturacion_p_ramo = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_ramo"]); ?>';
					var rvcfdi_bayer_facturacion_p_grupo = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_grupo"]); ?>';
					var rvcfdi_bayer_facturacion_p_gr_vendedores = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_gr_vendedores"]); ?>';
					var rvcfdi_bayer_facturacion_p_atributo_1_sector = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_atributo_1_sector"]); ?>';
					var rvcfdi_bayer_facturacion_p_atributo_2_sector = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_atributo_2_sector"]); ?>';
					var rvcfdi_bayer_facturacion_p_clase_factura = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_facturacion_p_clase_factura"]); ?>';
					
					var fg_dia_inicio_bayer_facturacion = document.getElementById('fg_dia_inicio_bayer_facturacion').value;
					var fg_mes_inicio_bayer_facturacion = document.getElementById('fg_mes_inicio_bayer_facturacion').value;
					var fg_año_inicio_bayer_facturacion = document.getElementById('fg_año_inicio_bayer_facturacion').value;
					var fg_dia_fin_bayer_facturacion = document.getElementById('fg_dia_fin_bayer_facturacion').value;
					var fg_mes_fin_bayer_facturacion = document.getElementById('fg_mes_fin_bayer_facturacion').value;
					var fg_año_fin_bayer_facturacion = document.getElementById('fg_año_fin_bayer_facturacion').value;
					
					data = 
					{
						action : 'realvirtual_woocommerce_bayer_reporte_facturacion',
						rvcfdi_bayer_facturacion_c_clase_documento : rvcfdi_bayer_facturacion_c_clase_documento,
						rvcfdi_bayer_facturacion_c_sociedad : rvcfdi_bayer_facturacion_c_sociedad,
						rvcfdi_bayer_facturacion_c_moneda : rvcfdi_bayer_facturacion_c_moneda,
						rvcfdi_bayer_facturacion_c_tc_cab_doc : rvcfdi_bayer_facturacion_c_tc_cab_doc, 
						rvcfdi_bayer_facturacion_p_cuenta : rvcfdi_bayer_facturacion_p_cuenta,
						rvcfdi_bayer_facturacion_p_division : rvcfdi_bayer_facturacion_p_division,
						rvcfdi_bayer_facturacion_p_ce_be : rvcfdi_bayer_facturacion_p_ce_be,
						rvcfdi_bayer_facturacion_p_texto : rvcfdi_bayer_facturacion_p_texto,
						rvcfdi_bayer_facturacion_p_pais_destinatario : rvcfdi_bayer_facturacion_p_pais_destinatario,
						rvcfdi_bayer_facturacion_p_linea_de_producto : rvcfdi_bayer_facturacion_p_linea_de_producto,
						rvcfdi_bayer_facturacion_p_grupo_de_producto : rvcfdi_bayer_facturacion_p_grupo_de_producto,
						rvcfdi_bayer_facturacion_p_centro : rvcfdi_bayer_facturacion_p_centro,
						rvcfdi_bayer_facturacion_p_cliente : rvcfdi_bayer_facturacion_p_cliente,
						rvcfdi_bayer_facturacion_p_organiz_ventas : rvcfdi_bayer_facturacion_p_organiz_ventas,
						rvcfdi_bayer_facturacion_p_canal_distrib : rvcfdi_bayer_facturacion_p_canal_distrib,
						rvcfdi_bayer_facturacion_p_zoha_de_ventas : rvcfdi_bayer_facturacion_p_zoha_de_ventas,
						rvcfdi_bayer_facturacion_p_oficina_ventas : rvcfdi_bayer_facturacion_p_oficina_ventas,
						rvcfdi_bayer_facturacion_p_ramo : rvcfdi_bayer_facturacion_p_ramo,
						rvcfdi_bayer_facturacion_p_grupo : rvcfdi_bayer_facturacion_p_grupo,
						rvcfdi_bayer_facturacion_p_gr_vendedores : rvcfdi_bayer_facturacion_p_gr_vendedores,
						rvcfdi_bayer_facturacion_p_atributo_1_sector : rvcfdi_bayer_facturacion_p_atributo_1_sector,
						rvcfdi_bayer_facturacion_p_atributo_2_sector : rvcfdi_bayer_facturacion_p_atributo_2_sector,
						rvcfdi_bayer_facturacion_p_clase_factura : rvcfdi_bayer_facturacion_p_clase_factura,
						fg_dia_inicio_bayer_facturacion : fg_dia_inicio_bayer_facturacion,
						fg_mes_inicio_bayer_facturacion : fg_mes_inicio_bayer_facturacion,
						fg_año_inicio_bayer_facturacion : fg_año_inicio_bayer_facturacion,
						fg_dia_fin_bayer_facturacion : fg_dia_fin_bayer_facturacion,
						fg_mes_fin_bayer_facturacion : fg_mes_fin_bayer_facturacion,
						fg_año_fin_bayer_facturacion : fg_año_fin_bayer_facturacion,
						idioma : '<?php echo $idiomaRVLFECFDI; ?>'
					};
					
					$.post(myAjax.ajaxurl, data, function(response)
					{
						if(!response.success)
						{
							mostrarVentanaVentas(response.message);
							return false;
						}
						else
						{
							mostrarVentanaVentas('Reporte de Facturación descargado con éxito.');
							archivo = atob(response.archivo);
							archivo = archivo.replace(/\\"/g, '"');
							
							var element = document.createElement('a');
							element.setAttribute('href', 'data:text/plain;charset=utf-8,' + unescape(encodeURIComponent(archivo)));
							element.setAttribute('download', 'Reporte_Facturacion.txt');
							element.style.display = 'none';
							document.body.appendChild(element);
							element.click();
							document.body.removeChild(element);
							
							return true;
						}
					}, 'json');
					
					return false;
				});
				
				$('#botonModalReporteBAYERFinancieroSi').click(function(event)
				{
					var rvcfdi_bayer_financiero_c_clase_de_documento = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_c_clase_de_documento"]); ?>';
					var rvcfdi_bayer_financiero_c_sociedad = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_c_sociedad"]); ?>';
					var rvcfdi_bayer_financiero_c_moneda = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_c_moneda"]); ?>';
					var rvcfdi_bayer_financiero_c_t_xt_cab_doc = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_c_t_xt_cab_doc"]); ?>';
					var rvcfdi_bayer_financiero_c_cuenta_bancaria = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_c_cuenta_bancaria"]); ?>';
					var rvcfdi_bayer_financiero_c_texto = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_c_texto"]); ?>';
					var rvcfdi_bayer_financiero_c_division = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_c_division"]); ?>';
					var rvcfdi_bayer_financiero_c_cebe = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_c_cebe"]); ?>';
					var rvcfdi_bayer_financiero_c_cliente = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_c_cliente"]); ?>';
					var rvcfdi_bayer_financiero_p_cuenta = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_cuenta"]); ?>';
					var rvcfdi_bayer_financiero_p_ind_impuestos = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_ind_impuestos"]); ?>';
					var rvcfdi_bayer_financiero_p_division = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_division"]); ?>';
					var rvcfdi_bayer_financiero_p_texto = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_texto"]); ?>';
					var rvcfdi_bayer_financiero_p_cebe = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_cebe"]); ?>';
					var rvcfdi_bayer_financiero_p_pais_destinatario = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_pais_destinatario"]); ?>';
					var rvcfdi_bayer_financiero_p_linea_de_producto = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_linea_de_producto"]); ?>';
					var rvcfdi_bayer_financiero_p_grupo_de_proudcto = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_grupo_de_proudcto"]); ?>';
					var rvcfdi_bayer_financiero_p_centro = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_centro"]); ?>';
					var rvcfdi_bayer_financiero_p_articulo = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_articulo"]); ?>';
					var rvcfdi_bayer_financiero_p_zona_de_ventas = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_zona_de_ventas"]); ?>';
					var rvcfdi_bayer_financiero_p_material = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_material"]); ?>';
					var rvcfdi_bayer_financiero_p_atributo_2_sector = '<?php echo (RealVirtualWooCommerceConfiguracionBayer::configuracionEntidad()["rvcfdi_bayer_financiero_p_atributo_2_sector"]); ?>';
					
					var fg_dia_inicio_bayer_financiero = document.getElementById('fg_dia_inicio_bayer_financiero').value;
					var fg_mes_inicio_bayer_financiero = document.getElementById('fg_mes_inicio_bayer_financiero').value;
					var fg_año_inicio_bayer_financiero = document.getElementById('fg_año_inicio_bayer_financiero').value;
					var fg_dia_fin_bayer_financiero = document.getElementById('fg_dia_fin_bayer_financiero').value;
					var fg_mes_fin_bayer_financiero = document.getElementById('fg_mes_fin_bayer_financiero').value;
					var fg_año_fin_bayer_financiero = document.getElementById('fg_año_fin_bayer_financiero').value;
					
					data = 
					{
						action : 'realvirtual_woocommerce_bayer_reporte_financiero',
						rvcfdi_bayer_financiero_c_clase_de_documento : rvcfdi_bayer_financiero_c_clase_de_documento,
						rvcfdi_bayer_financiero_c_sociedad : rvcfdi_bayer_financiero_c_sociedad,
						rvcfdi_bayer_financiero_c_moneda : rvcfdi_bayer_financiero_c_moneda,
						rvcfdi_bayer_financiero_c_t_xt_cab_doc : rvcfdi_bayer_financiero_c_t_xt_cab_doc, 
						rvcfdi_bayer_financiero_c_cuenta_bancaria : rvcfdi_bayer_financiero_c_cuenta_bancaria,
						rvcfdi_bayer_financiero_c_texto : rvcfdi_bayer_financiero_c_texto,
						rvcfdi_bayer_financiero_c_division : rvcfdi_bayer_financiero_c_division,
						rvcfdi_bayer_financiero_c_cebe : rvcfdi_bayer_financiero_c_cebe,
						rvcfdi_bayer_financiero_c_cliente : rvcfdi_bayer_financiero_c_cliente,
						rvcfdi_bayer_financiero_p_cuenta : rvcfdi_bayer_financiero_p_cuenta,
						rvcfdi_bayer_financiero_p_ind_impuestos : rvcfdi_bayer_financiero_p_ind_impuestos,
						rvcfdi_bayer_financiero_p_division : rvcfdi_bayer_financiero_p_division,
						rvcfdi_bayer_financiero_p_texto : rvcfdi_bayer_financiero_p_texto,
						rvcfdi_bayer_financiero_p_cebe : rvcfdi_bayer_financiero_p_cebe,
						rvcfdi_bayer_financiero_p_pais_destinatario : rvcfdi_bayer_financiero_p_pais_destinatario,
						rvcfdi_bayer_financiero_p_linea_de_producto : rvcfdi_bayer_financiero_p_linea_de_producto,
						rvcfdi_bayer_financiero_p_grupo_de_proudcto : rvcfdi_bayer_financiero_p_grupo_de_proudcto,
						rvcfdi_bayer_financiero_p_centro : rvcfdi_bayer_financiero_p_centro,
						rvcfdi_bayer_financiero_p_articulo : rvcfdi_bayer_financiero_p_articulo,
						rvcfdi_bayer_financiero_p_zona_de_ventas : rvcfdi_bayer_financiero_p_zona_de_ventas,
						rvcfdi_bayer_financiero_p_material : rvcfdi_bayer_financiero_p_material,
						rvcfdi_bayer_financiero_p_atributo_2_sector : rvcfdi_bayer_financiero_p_atributo_2_sector,
						fg_dia_inicio_bayer_financiero : fg_dia_inicio_bayer_financiero,
						fg_mes_inicio_bayer_financiero : fg_mes_inicio_bayer_financiero,
						fg_año_inicio_bayer_financiero : fg_año_inicio_bayer_financiero,
						fg_dia_fin_bayer_financiero : fg_dia_fin_bayer_financiero,
						fg_mes_fin_bayer_financiero : fg_mes_fin_bayer_financiero,
						fg_año_fin_bayer_financiero : fg_año_fin_bayer_financiero,
						idioma : '<?php echo $idiomaRVLFECFDI; ?>'
					};
					
					$.post(myAjax.ajaxurl, data, function(response)
					{
						if(!response.success)
						{
							mostrarVentanaVentas(response.message);
							return false;
						}
						else
						{
							mostrarVentanaVentas('Reporte Financiero descargado con éxito.');
							archivo = atob(response.archivo);
							archivo = archivo.replace(/\\"/g, '"');
							
							var element = document.createElement('a');
							element.setAttribute('href', 'data:text/plain;charset=utf-8,' + unescape(encodeURIComponent(archivo)));
							element.setAttribute('download', 'Reporte_Financiero.txt');
							element.style.display = 'none';
							document.body.appendChild(element);
							element.click();
							document.body.removeChild(element);
							
							return true;
						}
					}, 'json');
					
					return false;
				});
				
				$('#ventas_boton_enviar').click(function(event)
				{
					if(CFDI_ID == '')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un CFDI.':'Select a CFDI.';?>');
						return;
					}
					
					if(RECEPTOR_EMAIL == '')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'No se puede enviar el CFDI porque el cliente no tiene correo electrónico especificado. Por favor, revisa los datos del cliente en '.$nombreSistemaAsociado : 'The CFDI can not be sent because the customer does not have the specified email. Please review customer data in '.$nombreSistemaAsociado; ?>');
						return;
					}
					
					document.getElementById('cargandoVentas').style.visibility = 'visible';
					
					data =
					{
						action  			: 'realvirtual_woocommerce_enviar',
						CFDI_ID   			: CFDI_ID,
						EMISOR_ID   		: EMISOR_ID,
						RECEPTOR_EMAIL   	: RECEPTOR_EMAIL,
						IDIOMA				:  '<?php echo $idiomaRVLFECFDI; ?>'
					}

					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoVentas').style.visibility = 'hidden';
						var response = JSON.parse(response);
						mostrarVentanaVentas(response.message);
					});
				});
				
				$('#ventas_boton_cancelar').click(function(event)
				{
					if(CFDI_UUID == '')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un CFDI.':'Select a CFDI.';?>');
						return;
					}
					
					if(CFDI_ESTADO != 'Vigente')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Este CFDI ya ha sido cancelado.':'This CFDI has already been canceled.';?>');
						return;
					}
					
					mostrarVentanaCancelar();
				});
				
				$('#botonModalCancelarSi').click(function(event)
				{
					var motivo = document.getElementById('cancelacion_detalle_motivo').value;
					var folioSustitucion = document.getElementById('cancelacion_detalle_foliosustitucion').value;
					
					document.getElementById('cargandoVentas').style.visibility = 'visible';
					
					data =
					{
						action  			: 'realvirtual_woocommerce_cancelar',
						CFDI_UUID   		: CFDI_UUID,
						EMISOR_ID   		: EMISOR_ID,
						IDIOMA				:  '<?php echo $idiomaRVLFECFDI; ?>',
						MOTIVO				: motivo,
						FOLIOSUSTITUCION	: folioSustitucion
					}

					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoVentas').style.visibility = 'hidden';
						var response = JSON.parse(response);
						mostrarVentanaVentas(response.message);
						
						var año = '';
						var mes = '';
						var estado = '';
						var numero_pedido = '';
						var cliente = '';
						
						document.getElementById('cargandoVentas').style.visibility = 'visible';
						
						data =
						{
							action  			: 'realvirtual_woocommerce_filtrar',
							AÑO   				: año,
							MES   				: mes,
							ESTADO   			: estado,
							NUMERO_PEDIDO   	: numero_pedido,
							CLIENTE   			: cliente,
							IDIOMA				: '<?php echo $idiomaRVLFECFDI; ?>'
						}

						$.post(myAjax.ajaxurl, data, function(response)
						{
							document.getElementById('cargandoVentas').style.visibility = 'hidden';
							var response = JSON.parse(response);
							
							if(response.success == false)
							{
								mostrarVentanaVentas(response.message);
								return;
							}
							else
							{
								document.getElementById('catalogoFacturas').innerHTML = response.ventas;
								document.getElementById('total_cfdi_ventas').innerHTML = response.total_cfdi + '<?php echo($idiomaRVLFECFDI == 'ES') ? ' CFDI encontrados.':' CFDI found.'; ?>';
								document.getElementById('ingresos_ventas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Ingresos: ':'Income: '; ?>' + '<b>$' + response.ingresos;
								document.getElementById('iva_ventas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IVA: ' : 'IVA (VAT): '; ?>' + '<b>$' + response.iva;
								document.getElementById('total_ventas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Total: ' : 'Total: '; ?>' + '<b>$' + response.total;
								document.getElementById('timbres_folios').innerHTML = response.TIMBRES_FOLIOS;
								
								$("#catalogoFacturas tr").click(function()
								{ 
									$(this).addClass('selected').siblings().removeClass('selected');    
									CFDI_ID = $(this).find('td:first-child').html();
									CFDI_UUID = $(this).find('td:nth-child(2)').html();
									RECEPTOR_EMAIL = $(this).find('td:nth-child(4)').html();
									EMISOR_ID = $(this).find('td:nth-child(5)').html();
									CFDI_ESTADO = $(this).find('td:nth-child(6)').html();
								});
							}
						});
					});
				});
				
				$('#ventas_boton_acuse').click(function(event)
				{
					if(CFDI_UUID == '')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un CFDI.':'Select a CFDI.';?>');
						return;
					}
					
					if(CFDI_ESTADO != 'Cancelado')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Este CFDI aún no ha sido cancelado.':'This CFDI has not yet been canceled.';?>');
						return;
					}
					
					location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarAcuse&CFDI_UUID=' + CFDI_UUID + '&IDIOMA=' + <?php echo $idiomaRVLFECFDI; ?>;
				});
				
				$('#ventas_boton_filtrar').click(function(event)
				{
					mostrarVentanaFiltrar();
				});
				
				$('#botonModalFiltrar').click(function(event)
				{
					var año = document.getElementById('año_ventas').value;
					var mes = document.getElementById('mes_ventas').value;
					var estado = document.getElementById('estado_ventas').value;
					var numero_pedido = document.getElementById('numero_pedido_ventas').value;
					var cliente = document.getElementById('cliente_ventas').value;
					var fuente = document.getElementById('fuente_ventas').value;
					var version = document.getElementById('version_ventas').value;
					
					if(año == '')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un año.':'Select a year.';?>');
						return;
					}
					
					if(mes == '')
					{
						mostrarVentanaVentas('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un mes.':'Select a month.';?>');
						return;
					}
					
					document.getElementById('cargandoVentas').style.visibility = 'visible';
					
					data =
					{
						action  			: 'realvirtual_woocommerce_filtrar',
						AÑO   				: año,
						MES   				: mes,
						ESTADO   			: estado,
						NUMERO_PEDIDO   	: numero_pedido,
						CLIENTE   			: cliente,
						FUENTE				: fuente,
						VERSION 			: version,
						IDIOMA				: '<?php echo $idiomaRVLFECFDI; ?>'
					}

					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoVentas').style.visibility = 'hidden';
						var response = JSON.parse(response);
						
						if(response.success == false)
						{
							mostrarVentanaVentas(response.message);
							return;
						}
						else
						{
							document.getElementById('catalogoFacturas').innerHTML = response.ventas;
							document.getElementById('total_cfdi_ventas').innerHTML = response.total_cfdi + '<?php echo($idiomaRVLFECFDI == 'ES') ? ' CFDI encontrados.': ' CFDI found.';?>';
							document.getElementById('ingresos_ventas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Ingresos: ':'Income: ';?>' + '<b>$' + response.ingresos;
							document.getElementById('iva_ventas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IVA: ':'IVA (VAT): ';?>' + '<b>$' + response.iva;
							document.getElementById('total_ventas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Total: ':'Total: ';?>' + '<b>$' + response.total;
							document.getElementById('timbres_folios').innerHTML = response.TIMBRES_FOLIOS;
							
							$("#catalogoFacturas tr").click(function()
							{ 
								$(this).addClass('selected').siblings().removeClass('selected');    
								CFDI_ID = $(this).find('td:first-child').html();
								CFDI_UUID = $(this).find('td:nth-child(2)').html();
								RECEPTOR_EMAIL = $(this).find('td:nth-child(4)').html();
								EMISOR_ID = $(this).find('td:nth-child(5)').html();
								CFDI_ESTADO = $(this).find('td:nth-child(6)').html();
							});
						}
					});
				});
				
				$('#ventas_boton_refresh').click(function(event)
				{
					var año = '';
					var mes = '';
					var estado = '';
					var numero_pedido = '';
					var cliente = '';
					var fuente = '';
					var version = '';
					
					document.getElementById('cargandoVentas').style.visibility = 'visible';
					
					data =
					{
						action  			: 'realvirtual_woocommerce_filtrar',
						AÑO   				: año,
						MES   				: mes,
						ESTADO   			: estado,
						NUMERO_PEDIDO   	: numero_pedido,
						CLIENTE   			: cliente,
						FUENTE				: fuente,
						VERSION				: version,
						IDIOMA				: '<?php echo $idiomaRVLFECFDI; ?>'
					}

					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoVentas').style.visibility = 'hidden';
						var response = JSON.parse(response);
						
						if(response.success == false)
						{
							mostrarVentanaVentas(response.message);
							return;
						}
						else
						{
							document.getElementById('catalogoFacturas').innerHTML = response.ventas;
							document.getElementById('total_cfdi_ventas').innerHTML = response.total_cfdi + '<?php echo($idiomaRVLFECFDI == 'ES') ? ' CFDI encontrados.': ' CFDI found.';?>';
							document.getElementById('ingresos_ventas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Ingresos: ':'Income: ';?>' + '<b>$' + response.ingresos;
							document.getElementById('iva_ventas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IVA: ':'IVA (VAT): ';?>' + '<b>$' + response.iva;
							document.getElementById('total_ventas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Total: ':'Total: ';?>' + '<b>$' + response.total;
							document.getElementById('timbres_folios').innerHTML = response.TIMBRES_FOLIOS;
							
							$("#catalogoFacturas tr").click(function()
							{ 
								$(this).addClass('selected').siblings().removeClass('selected');    
								CFDI_ID = $(this).find('td:first-child').html();
								CFDI_UUID = $(this).find('td:nth-child(2)').html();
								RECEPTOR_EMAIL = $(this).find('td:nth-child(4)').html();
								EMISOR_ID = $(this).find('td:nth-child(5)').html();
								CFDI_ESTADO = $(this).find('td:nth-child(6)').html();
							});
						}
					});
				});
				
				var modalVentas = document.getElementById('ventanaModalVentas');
				var spanVentas = document.getElementsByClassName('closeVentas')[0];
				var botonVentas = document.getElementById('botonModalVentas');
				
				var modalFiltrar = document.getElementById('ventanaModalFiltrar');
				var spanFiltrar = document.getElementsByClassName('closeFiltrar')[0];
				var botonFiltrar = document.getElementById('botonModalFiltrar');
				
				var modalCancelar = document.getElementById('ventanaModalCancelar');
				var spanCancelar = document.getElementsByClassName('closeCancelar')[0];
				var botonCancelar = document.getElementById('botonModalCancelarSi');
				var botonCancelar2 = document.getElementById('botonModalCancelarNo');
				
				var modalReporteExcel = document.getElementById('ventanaModalReporteExcel');
				var spanReporteExcel = document.getElementById('closeModalReporteExcel');
				var botonReporteExcel = document.getElementById('botonModalReporteExcelSi');
				var botonReporteExcel2 = document.getElementById('botonModalReporteExcelNo');
				
				var modalReporteExcelTIBA = document.getElementById('ventanaModalReporteExcelTIBA');
				var spanReporteExcelTIBA = document.getElementById('closeModalReporteExcelTIBA');
				var botonReporteExcelTIBA = document.getElementById('botonModalReporteExcelTIBASi');
				var botonReporteExcelTIBA2 = document.getElementById('botonModalReporteExcelTIBANo');
				
				var modalReporteBAYERFacturacion = document.getElementById('ventanaModalReporteBAYERFacturacion');
				var spanReporteBAYERFacturacion = document.getElementById('closeModalReporteBAYERFacturacion');
				var botonReporteBAYERFacturacion = document.getElementById('botonModalReporteBAYERFacturacionSi');
				var botonReporteBAYERFacturacion2 = document.getElementById('botonModalReporteBAYERFacturacionNo');
				
				var modalReporteBAYERFinanciero = document.getElementById('ventanaModalReporteBAYERFinanciero');
				var spanReporteBAYERFinanciero = document.getElementById('closeModalReporteBAYERFinanciero');
				var botonReporteBAYERFinanciero = document.getElementById('botonModalReporteBAYERFinancieroSi');
				var botonReporteBAYERFinanciero2 = document.getElementById('botonModalReporteBAYERFinancieroNo');
				
				function mostrarVentanaVentas(texto)
				{
					modalVentas.style.display = "block";
					document.getElementById('tituloModalVentas').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Aviso' : 'Notice'; ?>';
					document.getElementById('textoModalVentas').innerHTML = texto;
				}
				
				botonVentas.onclick = function()
				{
					modalVentas.style.display = "none";
					document.getElementById('tituloModalVentas').innerHTML = '';
					document.getElementById('textoModalVentas').innerHTML = '';
				}
				
				spanVentas.onclick = function()
				{
					modalVentas.style.display = "none";
					document.getElementById('tituloModalVentas').innerHTML = '';
					document.getElementById('textoModalVentas').innerHTML = '';
				}
				
				function mostrarVentanaFiltrar()
				{
					modalFiltrar.style.display = "block";
				}
				
				botonFiltrar.onclick = function()
				{
					modalFiltrar.style.display = "none";
				}
				
				spanFiltrar.onclick = function()
				{
					modalFiltrar.style.display = "none";
				}
				
				function mostrarVentanaCancelar()
				{
					modalCancelar.style.display = "block";
				}
				
				botonCancelar.onclick = function()
				{
					modalCancelar.style.display = "none";
				}
				
				botonCancelar2.onclick = function()
				{
					modalCancelar.style.display = "none";
				}
				
				spanCancelar.onclick = function()
				{
					modalCancelar.style.display = "none";
				}
				
				function mostrarVentanaReporteExcel()
				{
					modalReporteExcel.style.display = "block";
				}
				
				botonReporteExcel.onclick = function()
				{
					modalReporteExcel.style.display = "none";
				}
				
				botonReporteExcel2.onclick = function()
				{
					modalReporteExcel.style.display = "none";
				}
				
				spanReporteExcel.onclick = function()
				{
					modalReporteExcel.style.display = "none";
				}
				
				function mostrarVentanaReporteExcelTIBA()
				{
					modalReporteExcelTIBA.style.display = "block";
				}
				
				botonReporteExcelTIBA.onclick = function()
				{
					modalReporteExcelTIBA.style.display = "none";
				}
				
				botonReporteExcelTIBA2.onclick = function()
				{
					modalReporteExcelTIBA.style.display = "none";
				}
				
				spanReporteExcelTIBA.onclick = function()
				{
					modalReporteExcelTIBA.style.display = "none";
				}
				
				function mostrarVentanaReporteBAYERFacturacion()
				{
					modalReporteBAYERFacturacion.style.display = "block";
				}
				
				botonReporteBAYERFacturacion.onclick = function()
				{
					modalReporteBAYERFacturacion.style.display = "none";
				}
				
				botonReporteBAYERFacturacion2.onclick = function()
				{
					modalReporteBAYERFacturacion.style.display = "none";
				}
				
				spanReporteBAYERFacturacion.onclick = function()
				{
					modalReporteBAYERFacturacion.style.display = "none";
				}
				
				function mostrarVentanaReporteBAYERFinanciero()
				{
					modalReporteBAYERFinanciero.style.display = "block";
				}
				
				botonReporteBAYERFinanciero.onclick = function()
				{
					modalReporteBAYERFinanciero.style.display = "none";
				}
				
				botonReporteBAYERFinanciero2.onclick = function()
				{
					modalReporteBAYERFinanciero.style.display = "none";
				}
				
				spanReporteBAYERFinanciero.onclick = function()
				{
					modalReporteBAYERFinanciero.style.display = "none";
				}
				
				window.onclick = function(event)
				{
					if (event.target == modalVentas)
					{
						modalVentas.style.display = "none";
						document.getElementById('textoModalVentas').innerHTML = '';
					}
					
					if (event.target == modalFiltrar)
					{
						modalFiltrar.style.display = "none";
					}
					
					if(event.target == modalCancelar)
					{
						modalCancelar.style.display = "none";
					}
					
					if(event.target == modalReporteExcel)
					{
						modalReporteExcel.style.display = "none";
					}
					
					if(event.target == modalReporteExcelTIBA)
					{
						modalReporteExcelTIBA.style.display = "none";
					}
				}
			});
		</script>
	<?php
}

add_action('wp_ajax_realvirtual_woocommerce_enviar', 'realvirtual_woocommerce_enviar_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_enviar', 'realvirtual_woocommerce_enviar_callback');

function realvirtual_woocommerce_enviar_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
	
	$CFDI_ID = sanitize_text_field($_POST['CFDI_ID']);
	update_post_meta($post->ID, 'CFDI_ID', $CFDI_ID);
	
	$EMISOR_ID = sanitize_text_field($_POST['EMISOR_ID']);
	update_post_meta($post->ID, 'EMISOR_ID', $EMISOR_ID);
	
	$RECEPTOR_EMAIL = sanitize_text_field($_POST['RECEPTOR_EMAIL']);
	update_post_meta($post->ID, 'RECEPTOR_EMAIL', $RECEPTOR_EMAIL);
	
	$CFDI_ID 			= $_POST['CFDI_ID'];
	$EMISOR_ID 			= $_POST['EMISOR_ID'];
	$RECEPTOR_EMAIL 	= $_POST['RECEPTOR_EMAIL'];
	$idiomaRVLFECFDI 			= $_POST['IDIOMA'];
	
	if(!intval($CFDI_ID))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El ID del CFDI no es válido.':'The CFDI ID is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!intval($EMISOR_ID))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El ID del Emisor no es válido.':'The Issuer ID is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!filter_var($RECEPTOR_EMAIL, FILTER_VALIDATE_EMAIL))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El correo electrónico del Receptor tiene un formato inválido.':'The Receiver email has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}

	$opcion = 'EnviarCFDI';
	
	$parametros = array
	(
		'OPCION' => $opcion,
		'CFDI_ID' => $CFDI_ID,
		'EMISOR_ID' => $EMISOR_ID,
		'RECEPTOR_EMAIL' => $RECEPTOR_EMAIL,
		'IDIOMA' => $idiomaRVLFECFDI
	);
	
	$params = array
	(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => $parametros,
		'cookies' => array()
	);
			
	try
	{
		$response = wp_remote_post($urlSistemaAsociado.'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php', $params);
				
		if(is_array($response))
		{
			$header = $response['headers'];
			$body = $response['body'];
		}
	}
	catch(Exception $e)
	{
		print('Exception occured: ' . $e->getMessage());
	}
	
	$respuestaEnvio = json_decode($body);
	
	if($respuestaEnvio->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $respuestaEnvio->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'message' => $respuestaEnvio->message
		);
	}
		
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_cancelar', 'realvirtual_woocommerce_cancelar_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_cancelar', 'realvirtual_woocommerce_cancelar_callback');

function realvirtual_woocommerce_cancelar_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$CFDI_UUID = sanitize_text_field($_POST['CFDI_UUID']);
	update_post_meta($post->ID, 'CFDI_UUID', $CFDI_UUID);
	
	$EMISOR_ID = sanitize_text_field($_POST['EMISOR_ID']);
	update_post_meta($post->ID, 'EMISOR_ID', $EMISOR_ID);
	
	$MOTIVO = sanitize_text_field($_POST['MOTIVO']);
	update_post_meta($post->ID, 'MOTIVO', $MOTIVO);
	
	$FOLIOSUSTITUCION = sanitize_text_field($_POST['FOLIOSUSTITUCION']);
	update_post_meta($post->ID, 'FOLIOSUSTITUCION', $FOLIOSUSTITUCION);
	
	$CFDI_UUID 			= $_POST['CFDI_UUID'];
	$EMISOR_ID 			= $_POST['EMISOR_ID'];
	$EMISOR_RFC 		= $cuenta['rfc'];
	$SISTEMA 			= $sistema;
	$idiomaRVLFECFDI				= $_POST['IDIOMA'];
	$MOTIVO 			= $_POST['MOTIVO'];
	$FOLIOSUSTITUCION 			= $_POST['FOLIOSUSTITUCION'];
	
	if($MOTIVO == '' || $MOTIVO == null)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => 'Selecciona un <b>Motivo</b> de cancelación.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($MOTIVO == '01' && ($FOLIOSUSTITUCION == '' || $FOLIOSUSTITUCION == null))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => 'El campo <b>Folio Sustitución</b> no puede ser vacío. Se debe ingresar un UUID válido en el campo <b>Folio Sustitución</b> cuando la clave del <b>Motivo</b> seleccionado es "01".'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	/*if($FOLIOSUSTITUCION != '')
	{
		$patron = '/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/gi';
		if(preg_match($patron, $FOLIOSUSTITUCION) == false)
		{
			$respuesta = array
			(
				'success' => false,
				'message' => 'El UUID ingresado en el campo <b>Folio Sustitución</b> tiene un formato inválido.'
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
	}*/
	
	if(!($CFDI_UUID != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El UUID del CFDI no es válido.':'The CFDI UUID is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!intval($EMISOR_ID))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El ID del Emisor no es válido.':'The Issuer ID is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/^([A-Z]|&|Ñ){3,4}[0-9]{2}[0-1][0-9][0-3][0-9]([A-Z]|[0-9]){2}([0-9]|A){1}$/", $EMISOR_RFC))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El RFC del Emisor tiene un formato inválido.':'The Issuer RFC has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$opcion = 'CancelarCFDI';
	
	$parametros = array
	(
		'OPCION' => $opcion,
		'CFDI_UUID' => $CFDI_UUID,
		'EMISOR_ID' => $EMISOR_ID,
		'EMISOR_RFC' => $EMISOR_RFC,
		'SISTEMA' => $SISTEMA,
		'IDIOMA' => $idiomaRVLFECFDI,
		'MOTIVO' => $MOTIVO,
		'FOLIOSUSTITUCION' => $FOLIOSUSTITUCION
	);
	
	$params = array
	(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => $parametros,
		'cookies' => array()
	);
			
	try
	{
		$response = wp_remote_post($urlSistemaAsociado.'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php', $params);
				
		if(is_array($response))
		{
			$header = $response['headers'];
			$body = $response['body'];
		}
	}
	catch(Exception $e)
	{
		print('Exception occured: ' . $e->getMessage());
	}
	
	$respuestaCancelacion = json_decode($body);
	
	if($respuestaCancelacion->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $respuestaCancelacion->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'message' => $respuestaCancelacion->message
		);
	}
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_filtrar', 'realvirtual_woocommerce_filtrar_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_filtrar', 'realvirtual_woocommerce_filtrar_callback');

function realvirtual_woocommerce_filtrar_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$AÑO = sanitize_text_field($_POST['AÑO']);
	update_post_meta($post->ID, 'AÑO', $AÑO);
	
	$MES = sanitize_text_field($_POST['MES']);
	update_post_meta($post->ID, 'MES', $MES);
	
	$ESTADO = sanitize_text_field($_POST['ESTADO']);
	update_post_meta($post->ID, 'ESTADO', $ESTADO);
	
	$NUMERO_PEDIDO = sanitize_text_field($_POST['NUMERO_PEDIDO']);
	update_post_meta($post->ID, 'NUMERO_PEDIDO', $NUMERO_PEDIDO);
	
	$CLIENTE = sanitize_text_field($_POST['CLIENTE']);
	update_post_meta($post->ID, 'CLIENTE', $CLIENTE);
	
	$FUENTE = sanitize_text_field($_POST['FUENTE']);
	update_post_meta($post->ID, 'FUENTE', $FUENTE);
	
	$VERSION = sanitize_text_field($_POST['VERSION']);
	update_post_meta($post->ID, 'VERSION', $VERSION);
	
	$AÑO 				= $_POST['AÑO'];
	$MES 				= $_POST['MES'];
	$ESTADO 			= $_POST['ESTADO'];
	$NUMERO_PEDIDO 		= $_POST['NUMERO_PEDIDO'];
	$CLIENTE 			= $_POST['CLIENTE'];
	$FUENTE 			= $_POST['FUENTE'];
	$VERSION 			= $_POST['VERSION'];
	$idiomaRVLFECFDI	= $_POST['IDIOMA'];
	
	$fechaDesde = '';
	$fechaHasta = '';
	
	if($MES > '0')
	{
		$fechaDesde = $AÑO.'-'.$MES.'-01';
		$fecha = new DateTime($fechaDesde); 
		$fechaHasta = $fecha->format('Y-m-t');
	}
	else if($MES == '0')
	{
		$fechaDesde = $AÑO.'-01-01';
		$fechaHasta = $AÑO.'-12-31';
	}
	
	$filtro = $fechaDesde.'|'.$fechaHasta.'|'.$ESTADO.'|'.$NUMERO_PEDIDO.'|'.$CLIENTE.'|'.$FUENTE.'|'.$VERSION;
	$datosVentas = RealVirtualWooCommerceCFDI::obtenerVentas($cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $filtro, $sistema, $urlSistemaAsociado, $idiomaRVLFECFDI);
	
	if($datosVentas->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $datosVentas->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'ventas' => $datosVentas->VENTAS,
			'total_cfdi' => $datosVentas->TOTAL_CFDI,
			'ingresos' => $datosVentas->INGRESOS,
			'iva' => $datosVentas->IVA,
			'total' => $datosVentas->TOTAL,
			'TIMBRES_FOLIOS' => $datosVentas->TIMBRES_FOLIOS
		);
	}
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function realvirtual_woocommerce_cuenta()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	?>
		<form id="realvirtual_woocommerce_cuenta" method="post" style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Mi Cuenta' : 'My Account'; ?></b></font></label>
			<br/><br/>
			<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Accede con tu cuenta' : 'Login with your account'; ?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ingresa los datos de acceso a tu cuenta que obtienes en ':'Enter the access data to your account that you get in '; ?><a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a> <?php echo ($idiomaRVLFECFDI == 'ES') ? 'en la sección <b>"'.esc_html($nombreSistema).' > Datos de acceso".</b>':'in the <b>"'.esc_html($nombreSistema).' > Access Data"</b> section.';?></font></label>
			<br/><br/>
			<div>
				<table>
					<tr>
						<td>
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* RFC':'* RFC';?></font></label>
							<br/>
							<input type="text" style="width:100%" id="rfc" name="rfc" value="<?php echo esc_html($cuenta['rfc']); ?>">
						</td>
						<td>	
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Usuario': '* User';?></font></label>
							<br/>
							<input type="text" style="width:100%" id="usuario" name="usuario" value="<?php echo esc_html($cuenta['usuario']); ?>">
						</td>
						<td style="width:60%">	
							<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Clave Cifrada':'* Coded Key';?></font></label>
							<br/>
							<input type="text" style="width:100%" id="clave" name="clave" value="<?php echo esc_html($cuenta['clave']); ?>">
						</td>
					</tr>
				</table>
			</div>
			<input type="hidden" id="micuenta_sistema" name="micuenta_sistema" value="<?php echo $sistema; ?>">
			<div>
				<input type="button" style="background-color:#e94700;" class="boton" id="realvirtual_woocommerce_enviar_cuenta"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Acceder':'Log In';?>" />
				<img id="cargandoCuenta" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
			</div>
			<br/><br/>
			<div style="width:30%;background-color:#ececec; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Crea una cuenta' : 'Create an account'; ?></b></font></label>
				<br/>
				<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si no tienes una cuenta, crea una directamente en el portal del sistema principal de facturación ' :'If you do not have an account, create one directly on the main billing system portal '; ?><a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a>.</font></label>
				<br/>
				<a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank">
					<input type="button" style="background-color:#627580;" class="boton" id="realvirtual_woocommerce_crear_cuenta"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Crear una cuenta':'Crate an account';?>" />
				</a>
			</div>
			<br/>
			<div style="width:30%;background-color:#27343c; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<label><font color="#FFFFFF" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Probar como Invitado' : 'Try as a Guest'; ?></b></font></label>
				<br/>
				<label><font color="#FFFFFF" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Prueba el plugin como invitado utilizando nuestra cuenta de pruebas para que conozcas su funcionamiento y todas las posibilidades que te ofrecemos.' :'Try the plugin as a guest using our test account so that you know how it works and all the possibilities we offer you.'; ?></font></label>
				<br/>
				<input type="button" style="background-color:#289235;" class="boton" id="realvirtual_woocommerce_cuenta_pruebas"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Probar gratis':'Try for free';?>" />
			</div>
		</form>
		
		<div id="ventanaModalCuenta" class="modalCuenta">
			<div class="modal-contentCuenta">
				<span class="closeCuenta">&times;</span>
				<br/>
					<center><font color="#000000" size="5"><b>
						<div id="tituloModalCuenta"></div>
					</b></font></center>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalCuenta"></div>
					</font>
					<br/>
					<center><input type="button" style="background-color:#e94700;" class="boton" id="botonModalCuenta" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Aceptar':'Accept';?>" /></center>
			</div>
		</div>
    <?php
}

function realvirtual_woocommerce_facturaglobal()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$complementos = RealVirtualWooCommerceComplementos::configuracionEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		echo ($idiomaRVLFECFDI == 'ES') ? 'No se puede abrir esta sección porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.' : 'Cannot open this section because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.';
		wp_die();
	}
	
	?>
	
	<style>
		.tooltip {
			position: relative;
			display: inline-block;
			border-bottom: 1px black;
		}
		.tooltip .tiptext {
			visibility: hidden;
			width: 500px;
			background-color: #555;
			color: #fff;
			text-align: center;
			border-radius: 6px;
			padding: 10px 10px 10px 10px;
			position: absolute;
			z-index: 1;
			box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
		}
		.tooltip .tiptext::after {
			content: "";
			position: absolute;
			border-width: 5px;
			border-style: solid;
			border-color: #555 transparent transparent transparent;
		}
		.tooltip:hover .tiptext {
			visibility: visible;
		}
		.tooltip.left .tiptext{
			top: -20px;
			right: 110%;
		}
		.tooltip.left .tiptext::after{
			margin-top: -5px;
			top: 50%;
			left: 100%;
			border-color: transparent transparent transparent #2E2E2E;
		}
		
		.tooltip2 {
				  position: relative;
				  display: inline-block;
				  border-bottom: 1px black;
				}
		.tooltip2 .tooltiptext2 {
				  visibility: hidden;
				  width: 300px;
				  background-color: #555;
				  color: #fff;
				  text-align: left;
				  border-radius: 6px;
				  padding: 10px 10px 10px 10px;
				  position: absolute;
				  z-index: 1;
				  left: 50%;
				  margin-left: 10px;
				  opacity: 0;
				  transition: opacity 0.3s;
				}
		.tooltip2 .tooltiptext2::after {
				  content: "";
				  position: absolute;
				  margin-left: -5px;
				  border-width: 5px;
				  border-style: solid;
				  border-color: #555 transparent transparent transparent;
				}
		.tooltip2:hover .tooltiptext2 {
				  visibility: visible;
				  opacity: 1;
				}
		.tooltip2.top2 .tooltiptext2{
			margin-left: -150px;
			bottom: 150%;
			left: 50%;
		}
		.tooltip2.top2 .tooltiptext2::after{
			margin-left: -5px;
			top: 100%;
			left: 50%;
			border-color: #2E2E2E transparent transparent transparent;
		}
	</style>
	
		<div style="background-color: #FFFFFF; padding: 20px;">
			<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Factura Global':'Global Invoice'; ?></b></font></label>
			<label><font color="#505050" size="3"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? ' versión '.$configuracion['version_cfdi'] :' version '.$configuracion['version_cfdi']; ?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'En esta sección podrás emitir una factura global de todos tus pedidos que no han sido facturados a través del plugin, ya sea desde esta sección o desde la vista del cliente en tu sitio web.':'In this section you can issue a global invoice for all your orders that have not been invoiced through the plugin, either from this section or from the customer view on your website.';?></font></label>
			<br/>
			<?php
				if($complementos['facturaGlobal'] != '1')
				{
					$avisoTitulo = ($idiomaRVLFECFDI == 'ES') ? 'ESTE MÓDULO NO ESTÁ DISPONIBLE' : 'THIS MODULE IS NOT AVAILABLE';
					$avisoTitulo = '<label><font color="#dc0000" size="4"><b>'.$avisoTitulo.'</b></font></label>';
					$avisoMensaje = ($idiomaRVLFECFDI == 'ES') ? 'Estimado usuario, realiza la compra de este módulo para poder utilizarlo. Ve a la sección <b>Complementos</b> del plugin de facturación para realizar la compra de este módulo y conoce todos los complementos que ofrecemos.<br/>A continuación, podrás observar el módulo pero su funcionalidad estará deshabilitada.' : 'Dear user, make the purchase of this module to be able to use it. Go to the <b>Add-ons</b> section of the billing plugin to purchase this module and learn about all the add-ons we offer.<br/>Next, you will be able to see the module but its functionality will be disabled.';
					$avisoMensaje = '<label><font color="#000000" size="3">'.$avisoMensaje.'</font></label>';
					$avisoCompleto = '<br/><div style="background-color:#f3bfbf; padding: 15px;">'.$avisoTitulo.'<br/>'.$avisoMensaje.'</div>';
					echo $avisoCompleto;
				}
			?>
			<br/>
			<label><font color="#35a200" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'PASO 1: ' : 'STEP 1: ';?></b></font></label><label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Búsqueda de pedidos' : 'Order search';?></b></font></label>
			<br/>
			<select id="facturaglobal_formaConsulta" name="facturaglobal_formaConsulta" style="max-width:100%">
				<option value="0" selected><?php echo($idiomaRVLFECFDI == 'ES') ? 'Buscar pedidos de WooCommerce':'Search WooCommerce Orders'; ?></option>
				<option value="1"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Cargar pedidos desde archivo CSV':'Upload orders from CSV file'; ?></option>
			</select>
			<br/>
			<br/>
			<div width="100%" id="facturaglobal_formulario1">
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Establece el rango de fechas y el estado de los pedidos que deseas considerar en la factura global.':'Set the date range and status of the orders you want to consider in the global invoice.';?></font></label>
			<br/>
			<table width="100%">
				<tr>
					<td style="width:9%">
						<table>
							<tr>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Fecha Inicial':'* Initial Date';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<input type="date" id="fg_fechaInicial" name="fg_fechaInicial" value="<?php echo date("Y-m-01", strtotime(date("Y-m-d")));?>">
								</td>
							</tr>
						</table>
					</td>
					<td style="width:9%">
						<table>
							<tr>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Fecha Final':'* Final Date';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<input type="date" id="fg_fechaFinal" name="fg_fechaFinal" value="<?php echo date("Y-m-t", strtotime(date("Y-m-d")));?>">
								</td>
							</tr>
						</table>
					</td>
					<td style="width:20%">
						<table>
							<tr>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Estado':'* Status';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_estado_orden" name="fg_estado_orden">
										<option value=""><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cualquier estado':'Any state';?></option>
										<option value="processing"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Procesando':'Processing';?></option>
										<option value="completed" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Completado':'Completed';?></option>
										<option value="personalizado-1"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 1 (Slug personalizado-1)':'Personalized State 1 (Slug personalizado-1)';?></option>
										<option value="personalizado-2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 2 (Slug personalizado-2)':'Personalized State 2 (Slug personalizado-2)';?></option>
										<option value="personalizado-3"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estado Personalizado 3 (Slug personalizado-3)':'Personalized State 3 (Slug personalizado-3)';?></option>
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:13.5%">
						<table>
							<tr>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'* Método de pago':'* Payment method';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_metodo_pago_orden" name="fg_metodo_pago_orden">
									<?php 
										$metodosPagoInstalados = WC()->payment_gateways->get_available_payment_gateways(); //WC()->payment_gateways->payment_gateways();
										$metodosPagoInstaladosID = array();
										?>
										<option value=""><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cualquier método de pago':'Any payment method';?></option>
										<?php
										foreach($metodosPagoInstalados as $metodo)
										{
										?>
											<option value="<?php echo $metodo->id; ?>"><?php echo $metodo->title; ?></option>
										<?php 
										}
									?>
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:20%">
						<table>
							<tr>
								<td>
									<label><font color="#000000" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Excluir los pedidos':'Exclude orders';?></font></label>
								</td>
							</tr>
							<tr>
								<td style="width:30%">
									<input type="text" style="width:96%" id="fg_numeros_pedidos_excluir" name="fg_numeros_pedidos_excluir" value="" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Sin # y separado por comas. Ejem: 11,22 o 44':'No # and comma separated. E.g: 11,22 o 44';?>">
								</td>
							</tr>
						</table>
					</td>
					<td>
						<div>
							<input type="button" style="background-color:#e94700;" class="boton" id="boton_buscar_facturaglobal"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Buscar':'Search';?>" />
							<img id="cargandoBuscarFacturaGlobal" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
						</div>
					</td>
				</tr>
			</table>
			</div>
			<div width="100%" id="facturaglobal_formulario2" hidden>
				<center>
					<label><font color="#505050" size="4"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Si tienes pedidos en un archivo CSV puedes subirlo aquí.':'If you have orders in a CSV file you can upload it here.';?></font></label>
					<br/><br/>
					<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'El contenido del archivo CSV consiste en 5 columnas (No. Pedido, Total, Subtotal, Importe IVA, Porcentaje Tasa IVA) respetando el orden mostrado.<br/>Todos los datos deben ser númericos, excepto el No. Pedido que puede ser numérico o texto.<br/>Sólo debe existir información de los pedidos y no debe haber encabezados con el nombre de las columnas.':'The content of the CSV file consists of 5 columns (Order No., Total, Subtotal, VAT Amount, VAT Rate Percentage) respecting the order shown.<br/>All data must be numeric, except the Order No. which can be numeric or text.<br/>There should only be information about the orders and there should be no headings with the name of the columns.';?></font></label>
					<br/><br/>
					<table width="40%" style="background-color:#f0f0f1; border: 1px solid #c3c4c7; border-collapse: collapse;">
						<tr>
							<td style="padding-top: 10px;
							  padding-bottom: 10px;
							  padding-left: 10px;
							  padding-right: 2px;">
								<input type="file" name="facturaglobal_archivo" id="facturaglobal_archivo" accept=".csv"/>
							</td>
							<td style="padding-top: 10px;
							  padding-bottom: 10px;
							  padding-left: 2px;
							  padding-right: 10px;">
								<input type="button" style="background-color:#e94700;" class="boton" id="boton_csv_facturaglobal" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Leer desde CSV':'Leer desde CSV';?>" />
								<img id="cargandoBuscarFacturaGlobal2" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
							</td>
						</tr>
					</table>
				</center>
			</div>
			<br/>
			<div style="text-align: right; width:95%"><font color="#000000" size="2"><label id="fg_total_pedidos"><?php echo ($idiomaRVLFECFDI == 'ES') ? '0 pedidos encontrados' : '0 orders found'; ?></label><label id="fg_total_subtotal" style="padding-left: 3em;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Subtotal:':'Subtotal:'; ?> <b>$0.00</label></b><label id="fg_total_descuento" style="padding-left: 3em;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Descuento:':'Discount:'; ?> <b>$0.00</label></b><label id="fg_total_iva" style="padding-left: 3em;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'IVA:':'IVA (VAT):'; ?> <b>$0.00</label></b><label id="fg_total_ieps" style="padding-left: 3em;"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'IEPS:':'IEPS:'; ?> <b>$0.00</label></b><label id="fg_total_total" style="padding-left: 3em; padding-right: 3em;">Total: <b>$0.00</label></b></font></div>
		
			<div style="width: 95%; height: 270px; overflow-y: scroll; border:1px solid #c9c9c9;">
			<table border="1" style="border-collapse: collapse; background-color:#FFFFFF; border-color:#a54107;" width="100%">
				<thead>
					<tr>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Pedido' : 'Order'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Fecha' : 'Date'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Estado' : 'Status'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Método Pago' : 'Payment Method'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Moneda' : 'Currency'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Subtotal' : 'Subtotal'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Descuento' : 'Discount'; ?></font></td>
						<?php
							echo($configuracion['version_cfdi'] == '4.0') ? '<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2">Obj.Imp.</font></td>' : '';
						?>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'IVA' : 'VAT'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'IEPS' : 'IEPS'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Total' : 'Total'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Estado CFDI' : 'CFDI Estatus'; ?></font></td>
						<td style="text-align:center; border-color: #a54107; background-color: #e94700; padding: 5px;"><font color="#FFFFFF" size="2"><?php echo($idiomaRVLFECFDI == 'ES') ? 'Advertencias' : 'Warnings'; ?></font></td>
					</tr>
				</thead>
				<tbody id="catalogoPedidos_facturaglobal"></tbody>
			</table>
			</div>
			<br/>
			<label><font color="#35a200" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ?'PASO 2: ' : 'STEP 2: ';?></b></font></label><label><font color="#505050" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Generar Factura Global' : 'Generate Global Invoice';?></b></font></label>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Establece los siguientes datos de la factura global.':'Set the following global invoice data.';?></font></label>
			<br/>
			<table width="<?php echo ($configuracion['version_cfdi'] == '4.0') ? '73%' : '47%'; ?>">
				<tr>
					<td style="width:6%">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Serie':'* Serie';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<input style="width:100%" type="text" id="fg_serie" name="fg_serie"value="<?php echo esc_html($configuracion['serie']); ?>">
								</td>
							</tr>
						</table>
					</td>
					<td style="width:21%">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Forma de pago':'* Payment way';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_forma_pago" name="fg_forma_pago">
									<?php 
										$forma_pago = $configuracion['metodo_pago'];
										
										if($forma_pago == '01')
										{
										?>
											<option value="01" selected>01 - Efectivo</option>
										<?php 
										}
										else
										{
										?>
											<option value="01">01 - Efectivo</option>
										<?php 
										}
										
										if($forma_pago == '02')
										{
										?>
											<option value="02" selected>02 - Cheque nominativo</option>
										<?php 
										}
										else
										{
										?>
											<option value="02">02 - Cheque nominativo</option>
										<?php 
										}
										
										if($forma_pago == '03')
										{
										?>
											<option value="03" selected>03 - Transferencia electrónica de fondos</option>
										<?php 
										}
										else
										{
										?>
											<option value="03">03 - Transferencia electrónica de fondos</option>
										<?php 
										}
										
										if($forma_pago == '04')
										{
										?>
											<option value="04" selected>04 - Tarjeta de crédito</option>
										<?php 
										}
										else
										{
										?>
											<option value="04">04 - Tarjeta de crédito</option>
										<?php 
										}
										
										if($forma_pago == '05')
										{
										?>
											<option value="05" selected>05 - Monedero electrónico</option>
										<?php 
										}
										else
										{
										?>
											<option value="05">05 - Monedero electrónico</option>
										<?php 
										}
										
										if($forma_pago == '06')
										{
										?>
											<option value="06" selected>06 - Dinero electrónico</option>
										<?php 
										}
										else
										{
										?>
											<option value="06">06 - Dinero electrónico</option>
										<?php 
										}
										
										if($forma_pago == '08')
										{
										?>
											<option value="08" selected>08 - Vales de despensa</option>
										<?php 
										}
										else
										{
										?>
											<option value="08">08 - Vales de despensa</option>
										<?php 
										}
										
										if($forma_pago == '12')
										{
										?>
											<option value="12" selected>12 - Dación en pago</option>
										<?php 
										}
										else
										{
										?>
											<option value="12">12 - Dación en pago</option>
										<?php 
										}
										
										if($forma_pago == '13')
										{
										?>
											<option value="13" selected>13 - Pago por subrogación</option>
										<?php 
										}
										else
										{
										?>
											<option value="13">13 - Pago por subrogación</option>
										<?php 
										}
										
										if($forma_pago == '14')
										{
										?>
											<option value="14" selected>14 - Pago por consignación</option>
										<?php 
										}
										else
										{
										?>
											<option value="14">14 - Pago por consignación</option>
										<?php 
										}
										
										if($forma_pago == '15')
										{
										?>
											<option value="15" selected>15 - Condonación</option>
										<?php 
										}
										else
										{
										?>
											<option value="15">15 - Condonación</option>
										<?php 
										}
										
										if($forma_pago == '17')
										{
										?>
											<option value="17" selected>17 - Compensación</option>
										<?php 
										}
										else
										{
										?>
											<option value="17">17 - Compensación</option>
										<?php 
										}
										
										if($forma_pago == '23')
										{
										?>
											<option value="23" selected>23 - Novación</option>
										<?php 
										}
										else
										{
										?>
											<option value="23">23 - Novación</option>
										<?php 
										}
										
										if($forma_pago == '24')
										{
										?>
											<option value="24" selected>24 - Confusión</option>
										<?php 
										}
										else
										{
										?>
											<option value="24">24 - Confusión</option>
										<?php 
										}
										
										if($forma_pago == '25')
										{
										?>
											<option value="25" selected>25 - Remisión de deuda</option>
										<?php 
										}
										else
										{
										?>
											<option value="25">25 - Remisión de deuda</option>
										<?php 
										}
										
										if($forma_pago == '26')
										{
										?>
											<option value="26" selected>26 - Prescripción o caducidad</option>
										<?php 
										}
										else
										{
										?>
											<option value="26">26 - Prescripción o caducidad</option>
										<?php 
										}
										
										if($forma_pago == '27')
										{
										?>
											<option value="27" selected>27 - A satisfacción del acreedor</option>
										<?php 
										}
										else
										{
										?>
											<option value="27">27 - A satisfacción del acreedor</option>
										<?php 
										}
										
										if($forma_pago == '28')
										{
										?>
											<option value="28" selected>28 - Tarjeta de débito</option>
										<?php 
										}
										else
										{
										?>
											<option value="28">28 - Tarjeta de débito</option>
										<?php 
										}
										
										if($forma_pago == '29')
										{
										?>
											<option value="29" selected>29 - Tarjeta de servicios</option>
										<?php 
										}
										else
										{
										?>
											<option value="29">29 - Tarjeta de servicios</option>
										<?php 
										}
										
										if($forma_pago == '30')
										{
										?>
											<option value="30" selected>30 - Aplicación de anticipos</option>
										<?php 
										}
										else
										{
										?>
											<option value="30">30 - Aplicación de anticipos</option>
										<?php 
										}
										
										if($forma_pago == '31')
										{
										?>
											<option value="31" selected>31 - Intermediario pagos</option>
										<?php 
										}
										else
										{
										?>
											<option value="31">31 - Intermediario pagos</option>
										<?php 
										}
										
										if($forma_pago == '99')
										{
										?>
											<option value="99" selected>99 - Por definir</option>
										<?php 
										}
										else
										{
										?>
											<option value="99">99 - Por definir</option>
										<?php 
										}
									?>
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:5%">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Moneda':'* Currency';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_moneda" name="fg_moneda">
									<?php 
										$moneda = $configuracion['moneda'];
										
										if($moneda == 'MXN')
										{
										?>
											<option value="MXN" selected>MXN - Pesos</option>
										<?php 
										}
										else
										{
										?>
											<option value="MXN">MXN - Pesos</option>
										<?php 
										}
										
										if($moneda == 'USD')
										{
										?>
											<option value="USD" selected>USD - Dolar</option>
										<?php 
										}
										else
										{
										?>
											<option value="USD">USD - Dolar</option>
										<?php 
										}
										
										if($moneda == 'EUR')
										{
										?>
											<option value="EUR" selected>EUR - Euro</option>
										<?php 
										}
										else
										{
										?>
											<option value="EUR">EUR - Euro</option>
										<?php 
										}
									?>
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:14%">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Tipo de Cambio':'* Exchange Rate';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="fg_tipo_cambio" name="fg_tipo_cambio" value="<?php echo esc_html($configuracion['tipo_cambio']); ?>" placeholder="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ingresa un valor numérico':'Enter a numeric value';?>">
								</td>
							</tr>
						</table>
					</td>
					<td style="width:5%">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Precisión decimal':'* Decimal precision';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_precision_decimal" name="fg_precision_decimal">
									<?php 
										$precision_decimal = $configuracion['precision_decimal'];
										
										if($precision_decimal == '2')
										{
										?>
											<option value="2" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '2 decimales':'2 decimals';?></option>
										<?php 
										}
										else
										{
										?>
											<option value="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? '2 decimales':'2 decimals';?></option>
										<?php 
										}
										
										if($precision_decimal == '3')
										{
										?>
											<option value="3" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '3 decimales':'3 decimals';?></option>
										<?php 
										}
										else
										{
										?>
											<option value="3"><?php echo ($idiomaRVLFECFDI == 'ES') ? '3 decimales':'3 decimals';?></option>
										<?php 
										}
										
										if($precision_decimal == '4')
										{
										?>
											<option value="4" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '4 decimales':'4 decimals';?></option>
										<?php 
										}
										else
										{
										?>
											<option value="4"><?php echo ($idiomaRVLFECFDI == 'ES') ? '4 decimales':'4 decimals';?></option>
										<?php 
										}
										
										if($precision_decimal == '5')
										{
										?>
											<option value="5" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '5 decimales':'5 decimals';?></option>
										<?php 
										}
										else
										{
										?>
											<option value="5"><?php echo ($idiomaRVLFECFDI == 'ES') ? '5 decimales':'5 decimals';?></option>
										<?php 
										}
										
										if($precision_decimal == '6')
										{
										?>
											<option value="6" selected><?php echo ($idiomaRVLFECFDI == 'ES') ? '6 decimales':'6 decimals';?></option>
										<?php 
										}
										else
										{
										?>
											<option value="6"><?php echo ($idiomaRVLFECFDI == 'ES') ? '6 decimales':'6 decimals';?></option>
										<?php 
										}
									?>
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:5%; <?php echo ($configuracion['version_cfdi'] == '4.0') ? '' : 'display:none;'; ?>">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Periodicidad':'* Periodicity';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_periodicidad" name="fg_periodicidad">
										<option value="01">01 - Diario</option>
										<option value="02">02 - Semanal</option>
										<option value="03">03 - Quincenal</option>
										<option value="04" selected>04 - Mensual</option>
										<option value="05">05 - Bimestral</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:5%; <?php echo ($configuracion['version_cfdi'] == '4.0') ? '' : 'display:none;'; ?>">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Mes':'* Month';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_meses" name="fg_meses">
									<?php
										if(date("m") == '01')
										{
											?>
												<option value="01" selected>01 - Enero</option>
											<?php
										}
										else
										{
											?>
												<option value="01">01 - Enero</option>
											<?php
										}
										
										if(date("m") == '02')
										{
											?>
												<option value="02" selected>02 - Febrero</option>
											<?php
										}
										else
										{
											?>
												<option value="02">02 - Febrero</option>
											<?php
										}
										
										if(date("m") == '03')
										{
											?>
												<option value="03" selected>03 - Marzo</option>
											<?php
										}
										else
										{
											?>
												<option value="03">03 - Marzo</option>
											<?php
										}
										
										if(date("m") == '04')
										{
											?>
												<option value="04" selected>04 - Abril</option>
											<?php
										}
										else
										{
											?>
												<option value="04">04 - Abril</option>
											<?php
										}
										
										if(date("m") == '05')
										{
											?>
												<option value="05" selected>05 - Mayo</option>
											<?php
										}
										else
										{
											?>
												<option value="05">05 - Mayo</option>
											<?php
										}
										
										if(date("m") == '06')
										{
											?>
												<option value="06" selected>06 - Junio</option>
											<?php
										}
										else
										{
											?>
												<option value="06">06 - Junio</option>
											<?php
										}
										
										if(date("m") == '07')
										{
											?>
												<option value="07" selected>07 - Julio</option>
											<?php
										}
										else
										{
											?>
												<option value="07">07 - Julio</option>
											<?php
										}
										
										if(date("m") == '08')
										{
											?>
												<option value="08" selected>08 - Agosto</option>
											<?php
										}
										else
										{
											?>
												<option value="08">08 - Agosto</option>
											<?php
										}
										
										if(date("m") == '09')
										{
											?>
												<option value="09" selected>09 - Septiembre</option>
											<?php
										}
										else
										{
											?>
												<option value="09">09 - Septiembre</option>
											<?php
										}
										
										if(date("m") == '10')
										{
											?>
												<option value="10" selected>10 - Octubre</option>
											<?php
										}
										else
										{
											?>
												<option value="10">10 - Octubre</option>
											<?php
										}
										
										if(date("m") == '11')
										{
											?>
												<option value="11" selected>11 - Noviembre</option>
											<?php
										}
										else
										{
											?>
												<option value="11">11 - Noviembre</option>
											<?php
										}
										
										if(date("m") == '12')
										{
											?>
												<option value="12" selected>12 - Diciembre</option>
											<?php
										}
										else
										{
											?>
												<option value="12">12 - Diciembre</option>
											<?php
										}
										?>
										
										<option value="13">13 - Enero-Febrero</option>
										<option value="14">14 - Marzo-Abril</option>
										<option value="15">15 - Mayo-Junio</option>
										<option value="16">16 - Julio-Agosto</option>
										<option value="17">17 - Septiembre-Octubre</option>
										<option value="18">18 - Noviembre-Diciembre</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:5%; <?php echo ($configuracion['version_cfdi'] == '4.0') ? '' : 'display:none;'; ?>">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Año':'* Year';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_año" name="fg_año">
									<?php
										$time = strtotime("-2 year", time());
										$añoInicial = date("Y", $time);
										$añoActual = date("Y");
										
										for($i = $añoInicial; $i <= $añoActual; $i++)
										{
											if($i == date("Y"))
											{
											?>
												<option value="<?php echo esc_html($i); ?>" selected><?php echo esc_html($i); ?></option>
											<?php 
											}
											else
											{
											?>
												<option value="<?php echo esc_html($i); ?>"><?php echo esc_html($i); ?></option>
											<?php 
											}
										}
									?>
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:5%; <?php echo ($configuracion['version_cfdi'] == '4.0') ? '' : 'display:none;'; ?>">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Ajustes Especiales de Impuestos':'* Special Tax Adjustments';?></font></label>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_impuestosAntiguos" name="fg_impuestosAntiguos">
										<option value="1" selected>Considerar el impuesto 'IMPUESTO' como 'IVA' para los pedidos que tengan dicho impuesto.</option>
										<option value="2">Considerar el impuesto 'IMPUESTO' como 'IEPS' para los pedidos que tengan dicho impuesto.</option>
										<option value="0">Considerar el impuesto 'IMPUESTO' como 'IMPUESTO' siempre. No será posible la emisión de la factura global.</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
					
					<td style="display:none;">
						<table>
							<tr>
								<td>
									<label><font color="#000000"><?php echo ($idiomaRVLFECFDI == 'ES') ? '* Recalcular Impuestos y Subtotales':'* Recalculate Taxes and Subtotals';?></font></label>
									<div class="tooltip2 top2"><img src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_information.png"); ?>" height="16" width="16">
									  <span class="tooltiptext2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Esta opción permite recalcular los impuestos y subtotales a partir del total de cada pedido. Únicamente es útil en caso de que tengas problemas para emitir tu factura global. El uso de esta opción es bajo responsabilidad del usuario. Para más información, ir a la sección <b>Preguntas Frecuentes</b>.':'This option allows you to recalculate taxes and subtotals from the total of each order. It is only useful in case you have trouble issuing your global invoice. The use of this option is under the responsibility of the user. For more information, go to the <b>FAQ</b> section.';?></span>
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<select id="fg_recalcular_impuestos" name="fg_recalcular_impuestos">
										<option value="0" selected><?php echo ($idiomaRVLFECFDI == 'ES') ?'No, no recalcular impuestos ni subtotales (recomendado)':'No, do not recalculate taxes or subtotals (recommended)';?></option>
										<option value="1"><?php echo ($idiomaRVLFECFDI == 'ES') ?'Sí, recalcular impuestos y subtotales a partir del total de cada pedido':'Yes, recalculate taxes and subtotals from the total of each order';?></option>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br/>
			<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ?'<font color="#d10000"><b>NOTAS:</b></font><br/>- Únicamente entrarán en la factura global los pedidos mostrados en la tabla anterior cuyo <b>Estado CFDI</b> es <b>No Facturado</b>.<br/>- Los montos de cada pedido serán utilizados de la siguiente manera: <b>'.$configuracion['manejo_impuestos_pedido_facturaGlobal_texto'].'</b> <i>(Puedes modificar esto en la sección Configuración)</i>.' : '<font color="#d10000"><b>NOTES:</b></font><br/>- Only the orders shown in the previous table whose <b>CFDI Status</b> is <b>No Facturado</b> will enter the global invoice.<br/>- The amounts of each order will be used as follows: <b> '.$configuration['manejo_impuestos_pedido_facturaGlobal_texto'].'</b> <i>(You can modify this in the Configuration section)</i>.';?></font></label>
			<br/>
			<table>
				<tr>
					<td>
						<div>
							<input type="button" style="background-color:#9c9c9c;" class="boton" id="boton_vistaprevia_facturaglobal"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Descargar Vista Previa':'Download Preview';?>" />
							<img id="cargandoVistaPreviaFacturaGlobal" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
						</div>
					</td>
					<td>
						<div>
							<input type="button" style="background-color:#e94700;" class="boton" id="boton_timbrar_facturaglobal"  value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Generar Factura Global':'Generate Global Invoice';?>" />
							<img id="cargandoTimbrarFacturaGlobal" src="<?php echo esc_url(plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif"); ?>" alt="Cargando" height="32" width="32" style="visibility: hidden;">
						</div>
					</td>
				</tr>
			</table>
			<input type="hidden" id="fg_version" name="fg_version" value="<?php echo esc_html($configuracion['version_cfdi']); ?>">
			<input type="hidden" id="fg_subtotal" name="fg_subtotal" value="">
			<input type="hidden" id="fg_descuento" name="fg_descuento" value="">
			<input type="hidden" id="fg_total" name="fg_total" value="">
			<input type="hidden" id="fg_subtotal_noFacturado" name="fg_subtotal_noFacturado" value="">
			<input type="hidden" id="fg_descuento_noFacturado" name="fg_descuento_noFacturado" value="">
			<input type="hidden" id="fg_total_noFacturado" name="fg_total_noFacturado" value="">
			<input type="hidden" id="pedidosJSON" name="pedidosJSON" value="">
		</div>

		<div id="ventanaModalFacturaGlobal" class="modalVentas">
			<div class="modal-contentVentas">
				<span id="closeFacturaGlobal" class="closeVentas">&times;</span>
				<br/>
					<center><font color="#000000" size="5"><b>
						<div id="tituloModalFacturaGlobal"></div>
					</b></font></center>
					<br/>
					<font color="#000000" size="3">
						<center><div id="textoModalFacturaGlobal"></div></center>
					</font>
					<br/>
					<center><input type="button" style="background-color:#e94700;" class="boton" id="botonModalFacturaGlobal" value="<?php echo ($idiomaRVLFECFDI == 'ES') ?'Aceptar':'Accept';?>" /></center>
			</div>
		</div>
		
		<div id="ventanaModalTimbrarFacturaGlobal" class="modalVentas">
			<div class="modal-contentVentas">
				<span id="closeTimbrarFacturaGlobal" class="closeVentas">&times;</span>
				<br/>
				<center>
					<font color="#000000" size="5"><b>
						<div id="tituloModalTimbrarFacturaGlobal"><?php echo(($idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice');?></div>
					</b></font>
					<br/>
					<font color="#000000" size="3">
						<div id="textoModalTimbrarFacturaGlobal"><?php echo(($idiomaRVLFECFDI == 'ES') ? '¿Deseas generar esta Factura Global?':'Do you want to generate this Global Invoice?');?></div>
					</font>
					<br/>
					<input type="button" style="background-color:#e94700;" class="boton" id="botonModalTimbrarFacturaGlobalSi" value="<?php echo (($idiomaRVLFECFDI == 'ES') ? 'Sí':'Yes'); ?>" />
					<input type="button" style="background-color:#e94700;" class="boton" id="botonModalTimbrarFacturaGlobalNo" value="No" />
				</center>
			</div>
		</div>
		
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var facturaglobal_formaConsulta = document.getElementById('facturaglobal_formaConsulta').value;
				
				if(facturaglobal_formaConsulta == '0')
				{
					$( "#facturaglobal_formulario1" ).show("slow", function()
					{
						  
					});
					$( "#facturaglobal_formulario2" ).hide("slow", function()
					{
						  
					});
				}
				else
				{
					$( "#facturaglobal_formulario1" ).hide("slow", function()
					{
						  
					});
					$( "#facturaglobal_formulario2" ).show("slow", function()
					{
						  
					});
				}
				
				$('#facturaglobal_formaConsulta').change(function(event)
				{
					var facturaglobal_formaConsulta = document.getElementById('facturaglobal_formaConsulta').value;
					
					if(facturaglobal_formaConsulta == '0')
					{
						$( "#facturaglobal_formulario1" ).show("slow", function()
						{
							  
						});
						$( "#facturaglobal_formulario2" ).hide("slow", function()
						{
							  
						});
					}
					else
					{
						$( "#facturaglobal_formulario1" ).hide("slow", function()
						{
							  
						});
						$( "#facturaglobal_formulario2" ).show("slow", function()
						{
							  
						});
					}
				});
				
				$('#fg_fechaInicial').change(function(event)
				{
					let fechaInicial = document.getElementById('fg_fechaInicial').value;
					let [año1, mes1, dia1] = fechaInicial.split('-');
					
					let fechaFinal = document.getElementById('fg_fechaFinal').value;
					let [año2, mes2, dia2] = fechaFinal.split('-');
					
					var obj_fechaInicio = new Date(fechaInicial).getTime();
					var obj_fechaFin = new Date(fechaFinal).getTime();

					var tiempoDiferencia = obj_fechaFin - obj_fechaInicio;

					var diasDiferencia = tiempoDiferencia/(1000*60*60*24);
					
					document.getElementById('fg_año').value = año1;
					
					if(mes1 == mes2)
					{
						if(diasDiferencia == 0)
							document.getElementById('fg_periodicidad').value = '01';
						else if(diasDiferencia >= 1 && diasDiferencia <= 7)
							document.getElementById('fg_periodicidad').value = '02';
						else if(diasDiferencia >= 7 && diasDiferencia <= 15)
							document.getElementById('fg_periodicidad').value = '03';
						else
							document.getElementById('fg_periodicidad').value = '04';
						
						document.getElementById('fg_meses').value = mes1;
					}
					else
					{
						if(mes1 == '01' && mes2 == '02')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '13';
						}
						else if(mes1 == '03' && mes2 == '04')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '14';
						}
						else if(mes1 == '05' && mes2 == '06')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '15';
						}
						else if(mes1 == '07' && mes2 == '08')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '16';
						}
						else if(mes1 == '09' && mes2 == '10')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '17';
						}
						else if(mes1 == '11' && mes2 == '12')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '18';
						}
					}
				});
				
				$('#fg_fechaFinal').change(function(event)
				{
					let fechaInicial = document.getElementById('fg_fechaInicial').value;
					let [año1, mes1, dia1] = fechaInicial.split('-');
					
					let fechaFinal = document.getElementById('fg_fechaFinal').value;
					let [año2, mes2, dia2] = fechaFinal.split('-');
					
					var obj_fechaInicio = new Date(fechaInicial).getTime();
					var obj_fechaFin = new Date(fechaFinal).getTime();

					var tiempoDiferencia = obj_fechaFin - obj_fechaInicio;

					var diasDiferencia = tiempoDiferencia/(1000*60*60*24);
					
					document.getElementById('fg_año').value = año1;
					
					if(mes1 == mes2)
					{
						if(diasDiferencia == 0)
							document.getElementById('fg_periodicidad').value = '01';
						else if(diasDiferencia >= 1 && diasDiferencia <= 7)
							document.getElementById('fg_periodicidad').value = '02';
						else if(diasDiferencia >= 7 && diasDiferencia <= 15)
							document.getElementById('fg_periodicidad').value = '03';
						else
							document.getElementById('fg_periodicidad').value = '04';
						
						document.getElementById('fg_meses').value = mes1;
					}
					else
					{
						if(mes1 == '01' && mes2 == '02')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '13';
						}
						else if(mes1 == '03' && mes2 == '04')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '14';
						}
						else if(mes1 == '05' && mes2 == '06')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '15';
						}
						else if(mes1 == '07' && mes2 == '08')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '16';
						}
						else if(mes1 == '09' && mes2 == '10')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '17';
						}
						else if(mes1 == '11' && mes2 == '12')
						{
							document.getElementById('fg_periodicidad').value = '05';
							document.getElementById('fg_meses').value = '18';
						}
					}
				});
				
				var NUMERO_ORDEN = '';
				var CLIENTE_NOMBRE = '';
				var FECHA_CREACION = '';
				var ESTADO_PEDIDO = '';
				var TOTAL_PEDIDO = '';
				
				$("#catalogoPedidos_facturaglobal tr").click(function()
				{ 
					$(this).addClass('selected').siblings().removeClass('selected');    
					NUMERO_ORDEN = $(this).find('td:first-child').html();
					CLIENTE_NOMBRE = $(this).find('td:nth-child(2)').html();
					FECHA_CREACION = $(this).find('td:nth-child(4)').html();
					ESTADO_PEDIDO = $(this).find('td:nth-child(5)').html();
					TOTAL_PEDIDO = $(this).find('td:nth-child(6)').html();
				});
				
				$('#boton_csv_facturaglobal').click(function(event)
				{
					document.getElementById('cargandoBuscarFacturaGlobal2').style.visibility = 'visible';
					
					var csvCargado = '';
					var fileInput = document.getElementById('facturaglobal_archivo');

					if(fileInput.files.length != 1)
					{
						document.getElementById('cargandoBuscarFacturaGlobal2').style.visibility = 'hidden';
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Seleccione un archivo CSV.':'Select a CSV file.';?>');
						return;
					}

					var file = fileInput.files[0];
					var textType = /csv.*/;
					
					var fileExtension = fileInput.files[0].name.split('.').pop();
					
					if (fileExtension == 'csv') 
					{
						var reader = new FileReader();
						
						reader.onload = function(e) 
						{
							var content = reader.result;
							csvCargado = content;
							
							data = 
							{
								action  				: 'realvirtual_woocommerce_leercsv_facturaglobal',
								csv						: csvCargado,
								versionCFDI				: document.getElementById('fg_version').value
							}
							
							$.post(myAjax.ajaxurl, data, function(response)
							{
								document.getElementById('cargandoBuscarFacturaGlobal2').style.visibility = 'hidden';
								var response = JSON.parse(response);
								
								if(response.success == false)
								{
									mostrarVentanaFacturaGlobal(response.message);
									return;
								}
								else
								{
									document.getElementById('catalogoPedidos_facturaglobal').innerHTML = response.pedidosHTML;
									document.getElementById('fg_total_pedidos').innerHTML = response.total_pedidos + '<?php echo($idiomaRVLFECFDI == 'ES') ? ' pedidos encontrados.': ' orders found.';?>';
									document.getElementById('fg_total_subtotal').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Subtotal: ':'Subtotal: ';?>' + '<b>$' + response.total_subtotal;
									document.getElementById('fg_total_descuento').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Descuento: ':'Discount: ';?>' + '<b>$' + response.total_descuento;
									document.getElementById('fg_total_iva').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IVA: ':'IVA (VAT): ';?>' + '<b>$' + response.total_iva;
									document.getElementById('fg_total_ieps').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IEPS: ':'IEPS: ';?>' + '<b>$' + response.total_ieps;
									document.getElementById('fg_total_total').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Total: ':'Total: ';?>' + '<b>$' + response.total_total;
									document.getElementById('fg_subtotal').value = response.pedidos_total_subtotal;
									document.getElementById('fg_descuento').value = response.pedidos_total_descuento;
									document.getElementById('fg_total').value = response.pedidos_total_total;
									document.getElementById('fg_subtotal_noFacturado').value = response.total_subtotal_noFacturado;
									document.getElementById('fg_descuento_noFacturado').value = response.total_descuento_noFacturado;
									document.getElementById('fg_total_noFacturado').value = response.total_total_noFacturado;
									document.getElementById('pedidosJSON').value = response.pedidosJSON;
									
									$("#catalogoPedidos_facturaglobal tr").click(function()
									{ 
										$(this).addClass('selected').siblings().removeClass('selected');    
										NUMERO_ORDEN = $(this).find('td:first-child').html();
										CLIENTE_NOMBRE = $(this).find('td:nth-child(2)').html();
										FECHA_CREACION = $(this).find('td:nth-child(4)').html();
										ESTADO_PEDIDO = $(this).find('td:nth-child(5)').html();
										TOTAL_PEDIDO = $(this).find('td:nth-child(6)').html();
									});
								}
							});
						}
						
						reader.readAsText(file);	
					}
					else
					{
						csvCargado = '';
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'No es posible leer el archivo. El formato del archivo debe ser CSV.':'Unable to read the file. The file format must be CSV.';?>');
						document.getElementById('cargandoBuscarFacturaGlobal2').style.visibility = 'hidden';
					}
				});
				
				$('#boton_buscar_facturaglobal').click(function(event)
				{
					var fechaInicial = document.getElementById('fg_fechaInicial').value;
					var fechaFinal = document.getElementById('fg_fechaFinal').value;
					var estado_orden = document.getElementById('fg_estado_orden').value;
					var metodo_pago_orden = document.getElementById('fg_metodo_pago_orden').value;
					var numeros_pedidos_excluir = document.getElementById('fg_numeros_pedidos_excluir').value;
					
					if(fechaInicial == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Establece la fecha inicial.':'Set the initial date.';?>');
						return;
					}
					
					if(fechaFinal == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Establece la fecha final.':'Set the final date.';?>');
						return;
					}
					
					/*if(estado_orden == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un estado de pedido.':'Select an order state.';?>');
						return;
					}
					
					if(metodo_pago_orden == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Selecciona un método de pago de pedido.':'Select an order payment method.';?>');
						return;
					}*/
					
					document.getElementById('catalogoPedidos_facturaglobal').innerHTML = '';
					document.getElementById('fg_total_pedidos').innerHTML = '0 <?php echo($idiomaRVLFECFDI == 'ES') ? ' pedidos encontrados.': ' orders found.';?>';
					document.getElementById('fg_total_subtotal').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Subtotal: ':'Subtotal: ';?>' + '<b>$0.00';
					document.getElementById('fg_total_descuento').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Descuento: ':'Discount: ';?>' + '<b>$0.00';
					document.getElementById('fg_total_iva').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IVA: ':'IVA (VAT): ';?>' + '<b>$0.00';
					document.getElementById('fg_total_ieps').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IEPS: ':'IEPS: ';?>' + '<b>$0.00';
					document.getElementById('fg_total_total').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Total: ':'Total: ';?>' + '<b>$0.00';
					document.getElementById('fg_subtotal').value = '';
					document.getElementById('fg_descuento').value = '';
					document.getElementById('fg_total').value = '';
					document.getElementById('fg_subtotal_noFacturado').value = '';
					document.getElementById('fg_descuento_noFacturado').value = '';
					document.getElementById('fg_total_noFacturado').value = '';
					document.getElementById('pedidosJSON').value = '';
							
					document.getElementById('cargandoBuscarFacturaGlobal').style.visibility = 'visible';
					
					data = 
					{
						action  				: 'realvirtual_woocommerce_buscar_facturaglobal',
						fechaInicial			: fechaInicial,
						fechaFinal				: fechaFinal,
						estadoOrden				: estado_orden,
						metodoPagoOrden			: metodo_pago_orden,
						numerosPedidosExcluir	: numeros_pedidos_excluir,
						versionCFDI				: document.getElementById('fg_version').value
					}
					
					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoBuscarFacturaGlobal').style.visibility = 'hidden';
						var response = JSON.parse(response);
						
						if(response.success == false)
						{
							mostrarVentanaFacturaGlobal(response.message);
							return;
						}
						else
						{
							document.getElementById('catalogoPedidos_facturaglobal').innerHTML = response.pedidosHTML;
							document.getElementById('fg_total_pedidos').innerHTML = response.total_pedidos + '<?php echo($idiomaRVLFECFDI == 'ES') ? ' pedidos encontrados.': ' orders found.';?>';
							document.getElementById('fg_total_subtotal').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Subtotal: ':'Subtotal: ';?>' + '<b>$' + response.total_subtotal;
							document.getElementById('fg_total_descuento').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Descuento: ':'Discount: ';?>' + '<b>$' + response.total_descuento;
							document.getElementById('fg_total_iva').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IVA: ':'IVA (VAT): ';?>' + '<b>$' + response.total_iva;
							document.getElementById('fg_total_ieps').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IEPS: ':'IEPS: ';?>' + '<b>$' + response.total_ieps;
							document.getElementById('fg_total_total').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Total: ':'Total: ';?>' + '<b>$' + response.total_total;
							document.getElementById('fg_subtotal').value = response.pedidos_total_subtotal;
							document.getElementById('fg_descuento').value = response.pedidos_total_descuento;
							document.getElementById('fg_total').value = response.pedidos_total_total;
							document.getElementById('fg_subtotal_noFacturado').value = response.total_subtotal_noFacturado;
							document.getElementById('fg_descuento_noFacturado').value = response.total_descuento_noFacturado;
							document.getElementById('fg_total_noFacturado').value = response.total_total_noFacturado;
							document.getElementById('pedidosJSON').value = response.pedidosJSON;
							
							$("#catalogoPedidos_facturaglobal tr").click(function()
							{ 
								$(this).addClass('selected').siblings().removeClass('selected');    
								NUMERO_ORDEN = $(this).find('td:first-child').html();
								CLIENTE_NOMBRE = $(this).find('td:nth-child(2)').html();
								FECHA_CREACION = $(this).find('td:nth-child(4)').html();
								ESTADO_PEDIDO = $(this).find('td:nth-child(5)').html();
								TOTAL_PEDIDO = $(this).find('td:nth-child(6)').html();
							});
						}
					});
				});
				
				var modalFacturaGlobal = document.getElementById('ventanaModalFacturaGlobal');
				var spanFacturaGlobal = document.getElementById('closeFacturaGlobal');
				var botonFacturaGlobal = document.getElementById('botonModalFacturaGlobal');
					
				function mostrarVentanaFacturaGlobal(texto)
				{
					modalFacturaGlobal.style.display = "block";
					document.getElementById('tituloModalFacturaGlobal').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Aviso' : 'Notice'; ?>';
					document.getElementById('textoModalFacturaGlobal').innerHTML = texto;
				}
					
				botonFacturaGlobal.onclick = function()
				{
					modalFacturaGlobal.style.display = "none";
					document.getElementById('tituloModalFacturaGlobal').innerHTML = '';
					document.getElementById('textoModalFacturaGlobal').innerHTML = '';
				}
					
				spanFacturaGlobal.onclick = function()
				{
					modalFacturaGlobal.style.display = "none";
					document.getElementById('tituloModalFacturaGlobal').innerHTML = '';
					document.getElementById('textoModalFacturaGlobal').innerHTML = '';
				}
				
				var modalTimbrarFacturaGlobal = document.getElementById('ventanaModalTimbrarFacturaGlobal');
				var spanTimbrarFacturaGlobal = document.getElementById('closeTimbrarFacturaGlobal');
				var botonModalTimbrarFacturaGlobalSi = document.getElementById('botonModalTimbrarFacturaGlobalSi');
				var botonModalTimbrarFacturaGlobalNo = document.getElementById('botonModalTimbrarFacturaGlobalNo');
					
				function mostrarVentanaTimbrarFacturaGlobal()
				{
					modalTimbrarFacturaGlobal.style.display = "block";
				}
				
				botonModalTimbrarFacturaGlobalSi.onclick = function()
				{
					modalTimbrarFacturaGlobal.style.display = "none";
				}
				
				botonModalTimbrarFacturaGlobalNo.onclick = function()
				{
					modalTimbrarFacturaGlobal.style.display = "none";
				}
					
				spanTimbrarFacturaGlobal.onclick = function()
				{
					modalTimbrarFacturaGlobal.style.display = "none";
				}
				
				window.onclick = function(event)
				{
					if (event.target == modalFacturaGlobal)
					{
						modalFacturaGlobal.style.display = "none";
						document.getElementById('textoModalFacturaGlobal').innerHTML = '';
					}
					else if (event.target == modalTimbrarFacturaGlobal)
					{
						modalTimbrarFacturaGlobal.style.display = "none";
						document.getElementById('textoModalTimbrarFacturaGlobal').innerHTML = '';
					}
				}
				
				$('#boton_timbrar_facturaglobal').click(function(event)
				{
					var pedidosJSON = document.getElementById('pedidosJSON').value;
					
					if(pedidosJSON == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'No ha realizado la búsqueda de pedidos en el PASO 1.':'You have not searched for orders in the STEP 1.';?>');
						return;
					}
					
					mostrarVentanaTimbrarFacturaGlobal();
				});
				
				$('#botonModalTimbrarFacturaGlobalSi').click(function(event)
				{
					var pedidosJSON = document.getElementById('pedidosJSON').value;
					var fg_version = document.getElementById('fg_version').value;
					var fg_serie = document.getElementById('fg_serie').value;
					var fg_forma_pago = document.getElementById('fg_forma_pago').value;
					var fg_moneda = document.getElementById('fg_moneda').value;
					var fg_tipo_cambio = document.getElementById('fg_tipo_cambio').value;
					var fg_subtotal = document.getElementById('fg_subtotal').value;
					var fg_descuento = document.getElementById('fg_descuento').value;
					var fg_total = document.getElementById('fg_total').value;
					var fg_subtotal_noFacturado = document.getElementById('fg_subtotal_noFacturado').value;
					var fg_descuento_noFacturado = document.getElementById('fg_descuento_noFacturado').value;
					var fg_total_noFacturado = document.getElementById('fg_total_noFacturado').value;
					var fg_precision_decimal = document.getElementById('fg_precision_decimal').value;
					var fg_periodicidad = document.getElementById('fg_periodicidad').value;
					var fg_meses = document.getElementById('fg_meses').value;
					var fg_año = document.getElementById('fg_año').value;
					var fg_impuestosAntiguos = document.getElementById('fg_impuestosAntiguos').value;
					
					var fecha_actual = new Date();
					var año = fecha_actual.getFullYear();
					var mes = fecha_actual.getMonth() + 1;
					mes = ((mes < 10) ? '0' : '') + mes;
					var dia = fecha_actual.getDate();
					dia = ((dia < 10) ? '0' : '') + dia;
					var hora = fecha_actual.getHours();
					hora = ((hora < 10) ? '0' : '') + hora;
					var minutos = fecha_actual.getMinutes();
					minutos = ((minutos < 10) ? '0' : '') + minutos;
					var segundos = fecha_actual.getSeconds();
					segundos = ((segundos < 10) ? '0' : '') + segundos;

					var fg_fecha_emision = año + "-" + mes + "-" + dia + "T" + hora + ":" + minutos + ":" + segundos;
					
					var fg_recalcular_impuestos = document.getElementById('fg_recalcular_impuestos').value;
		
					if(fg_forma_pago == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'La Forma de Pago no puede ser vacía.':'The Payment Way cannot be empty.';?>');
						return;
					}
					
					if(fg_recalcular_impuestos == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Recalcular impuestos y subtotales no puede ser vacío.':'Recalculate taxes and subtotals cannot be empty.';?>');
						return;
					}
					
					if(fg_moneda == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'La Moneda no puede ser vacía.':'The Currency cannot be empty.';?>');
						return;
					}
					
					if(fg_tipo_cambio == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'El Tipo de Cambio no puede ser vacío.':'The Exchange Rate cannot be empty.';?>');
						return;
					}
					
					if(fg_subtotal_noFacturado == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'El Subtotal no puede ser vacío.':'The Subtotal cannot be empty.';?>');
						return;
					}
					
					if(fg_descuento_noFacturado == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'El Descuento no puede ser vacío.':'The Discount cannot be empty.';?>');
						return;
					}
					
					if(fg_total_noFacturado == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'El Total no puede ser vacío.':'The Total cannot be empty.';?>');
						return;
					}
					
					if(fg_precision_decimal == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'La Precisión Decimal no puede ser vacía.':'The Decimal Precision cannot be empty.';?>');
						return;
					}
					
					document.getElementById('cargandoTimbrarFacturaGlobal').style.visibility = 'visible';
					document.getElementById('boton_vistaprevia_facturaglobal').disabled = true;
					document.getElementById('boton_timbrar_facturaglobal').disabled = true;
					
					data = 
					{
						action  			: 'realvirtual_woocommerce_timbrar_facturaglobal',
						fg_version			: fg_version,
						fg_serie			: fg_serie,
						fg_forma_pago		: fg_forma_pago,
						fg_moneda			: fg_moneda,
						fg_tipo_cambio		: fg_tipo_cambio,
						fg_recalcular_impuestos : fg_recalcular_impuestos,
						fg_fecha_emision	: fg_fecha_emision,
						fg_subtotal			: fg_subtotal_noFacturado,
						fg_descuento		: fg_descuento_noFacturado,
						fg_total			: fg_total_noFacturado,
						fg_precision_decimal: fg_precision_decimal,
						pedidosJSON			: pedidosJSON,
						fg_periodicidad		: fg_periodicidad,
						fg_meses			: fg_meses,
						fg_año				: fg_año,
						fg_impuestosAntiguos : fg_impuestosAntiguos
					}
					
					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoTimbrarFacturaGlobal').style.visibility = 'hidden';
						document.getElementById('boton_vistaprevia_facturaglobal').disabled = false;
						document.getElementById('boton_timbrar_facturaglobal').disabled = false;
						
						var response = JSON.parse(response);
						
						if(response.success == false)
						{
							mostrarVentanaFacturaGlobal(response.message);
							return;
						}
						else
						{
							document.getElementById('catalogoPedidos_facturaglobal').innerHTML = '';
							document.getElementById('fg_total_pedidos').innerHTML = '0 <?php echo($idiomaRVLFECFDI == 'ES') ? ' pedidos encontrados.': ' orders found.';?>';
							document.getElementById('fg_total_subtotal').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Subtotal: ':'Subtotal: ';?>' + '<b>$0.00';
							document.getElementById('fg_total_descuento').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Descuento: ':'Discount: ';?>' + '<b>$0.00';
							document.getElementById('fg_total_iva').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IVA: ':'IVA (VAT): ';?>' + '<b>$0.00';
							document.getElementById('fg_total_ieps').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'IEPS: ':'IEPS: ';?>' + '<b>$0.00';
							document.getElementById('fg_total_total').innerHTML = '<?php echo($idiomaRVLFECFDI == 'ES') ? 'Total: ':'Total: ';?>' + '<b>$0.00';
							document.getElementById('fg_subtotal').value = '';
							document.getElementById('fg_descuento').value = '';
							document.getElementById('fg_total').value = '';
							document.getElementById('fg_subtotal_noFacturado').value = '';
							document.getElementById('fg_descuento_noFacturado').value = '';
							document.getElementById('fg_total_noFacturado').value = '';
							document.getElementById('pedidosJSON').value = '';
							
							mostrarVentanaFacturaGlobal(response.message);
							return;
						}
					});
				});
				
				$('#boton_vistaprevia_facturaglobal').click(function(event)
				{
					var pedidosJSON = document.getElementById('pedidosJSON').value;
					var fg_version = document.getElementById('fg_version').value;
					var fg_serie = document.getElementById('fg_serie').value;
					var fg_forma_pago = document.getElementById('fg_forma_pago').value;
					var fg_moneda = document.getElementById('fg_moneda').value;
					var fg_tipo_cambio = document.getElementById('fg_tipo_cambio').value;
					var fg_recalcular_impuestos = document.getElementById('fg_recalcular_impuestos').value;
					var fg_subtotal = document.getElementById('fg_subtotal').value;
					var fg_descuento = document.getElementById('fg_descuento').value;
					var fg_total = document.getElementById('fg_total').value;
					var fg_subtotal_noFacturado = document.getElementById('fg_subtotal_noFacturado').value;
					var fg_descuento_noFacturado = document.getElementById('fg_descuento_noFacturado').value;
					var fg_total_noFacturado = document.getElementById('fg_total_noFacturado').value;
					var fg_precision_decimal = document.getElementById('fg_precision_decimal').value;
					var fg_periodicidad = document.getElementById('fg_periodicidad').value;
					var fg_meses = document.getElementById('fg_meses').value;
					var fg_año = document.getElementById('fg_año').value;
					var fg_impuestosAntiguos = document.getElementById('fg_impuestosAntiguos').value;
					var fecha_actual = new Date();
					var fg_fecha_emision = fecha_actual.toISOString().substring(0,19);
		
					if(pedidosJSON == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'No ha realizado la búsqueda de pedidos en el PASO 1.':'You have not searched for orders in the STEP 1.';?>');
						return;
					}
		
					if(fg_forma_pago == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'La Forma de Pago no puede ser vacía.':'The Payment Way cannot be empty.';?>');
						return;
					}
					
					if(fg_moneda == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'La Moneda no puede ser vacía.':'The Currency cannot be empty.';?>');
						return;
					}
					
					if(fg_tipo_cambio == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'El Tipo de Cambio no puede ser vacío.':'The Exchange Rate cannot be empty.';?>');
						return;
					}
					
					if(fg_recalcular_impuestos == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'Recalcular impuestos y subtotales no puede ser vacío.':'Recalculate taxes and subtotals cannot be empty.';?>');
						return;
					}
					
					if(fg_subtotal_noFacturado == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'El Subtotal no puede ser vacío.':'The Subtotal cannot be empty.';?>');
						return;
					}
					
					if(fg_descuento_noFacturado == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'El Descuento no puede ser vacío.':'The Discount cannot be empty.';?>');
						return;
					}
					
					if(fg_total_noFacturado == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'El Total no puede ser vacío.':'The Total cannot be empty.';?>');
						return;
					}
					
					if(fg_precision_decimal == '')
					{
						mostrarVentanaFacturaGlobal('<?php echo($idiomaRVLFECFDI == 'ES') ? 'La Precisión Decimal no puede ser vacía.':'The Decimal Precision cannot be empty.';?>');
						return;
					}
					
					document.getElementById('cargandoVistaPreviaFacturaGlobal').style.visibility = 'visible';
					document.getElementById('boton_vistaprevia_facturaglobal').disabled = true;
					document.getElementById('boton_timbrar_facturaglobal').disabled = true;
					
					data = 
					{
						action  			: 'realvirtual_woocommerce_vistaprevia_facturaglobal',
						fg_version			: fg_version,
						fg_serie			: fg_serie,
						fg_forma_pago		: fg_forma_pago,
						fg_moneda			: fg_moneda,
						fg_tipo_cambio		: fg_tipo_cambio,
						fg_recalcular_impuestos : fg_recalcular_impuestos,
						fg_fecha_emision	: fg_fecha_emision,
						fg_subtotal			: fg_subtotal_noFacturado,
						fg_descuento		: fg_descuento_noFacturado,
						fg_total			: fg_total_noFacturado,
						fg_precision_decimal: fg_precision_decimal,
						pedidosJSON			: pedidosJSON,
						fg_periodicidad		: fg_periodicidad,
						fg_meses			: fg_meses,
						fg_año				: fg_año,
						fg_impuestosAntiguos : fg_impuestosAntiguos
					}
					
					$.post(myAjax.ajaxurl, data, function(response)
					{
						document.getElementById('cargandoVistaPreviaFacturaGlobal').style.visibility = 'hidden';
						document.getElementById('boton_vistaprevia_facturaglobal').disabled = false;
						document.getElementById('boton_timbrar_facturaglobal').disabled = false;
						
						var response = JSON.parse(response);
						
						if(response.success == false)
						{
							mostrarVentanaFacturaGlobal(response.message);
							return;
						}
						else
						{
							mensaje = response.message;
							CFDI_PDF = response.CFDI_PDF;
							
							if(mensaje == '')
							{
								location.href = '<?php echo $urlSistemaAsociado;?>' + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarVistaPreviaFacturaGlobal&CFDI_PDF=' + CFDI_PDF + '&IDIOMA=' + idiomaRVLFECFDI;
							}
							else
							{
								mostrarVentanaFacturaGlobal(message);
								return;
							}
						}
					});
				});
			});
		</script>
	<?php
}

function realvirtual_woocommerce_soporte()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<div style="background-color: #FFFFFF; padding: 20px;">
		<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Soporte Técnico':'Support';?></b></font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo implementar el Módulo de CFDI para Clientes?':'How to implement the CFDI Module for Customers?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Inserta en tu sitio el módulo que permite a tus clientes emitir el CFDI de sus pedidos.':'Insert on your site the module that allows your customers to issue the CFDI of their orders.';?>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Crea una nueva página en tu sitio e ingresa el siguiente shortcode para mostrar el <b>Módulo de Emisión de CFDI para Clientes</b>':'Create a new page in your website and enter the next shortcode to show the <b>Customer CFDI Issue Module</b> in your website.';?><br/><b>[<?php echo esc_html(strtolower($sistema)); ?>_woocommerce_formulario]</b>
		</font></label>
		<br/><br/>
		
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo implementar el Módulo de Registro de Datos Fiscales para Clientes?':'How to implement the Customer Tax Data Registration Module?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Inserta en tu sitio el módulo que permite a tus clientes registrar sus datos fiscales para una emisión de CFDI 4.0 más automatizada en el módulo de emisión de CFDI para clientes y/o para la emisión de CFDI automática previamente configurada.':'Insert on your site the module that allows your clients to register their fiscal data for a more automated CFDI 4.0 issuance in the CFDI issuance module for clients and/or for the previously configured automatic CFDI issuance.';?>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Crea una nueva página en tu sitio e ingresa el siguiente shortcode para mostrar el <b>Módulo de Registro de Datos Fiscales para Clientes</b>':'Create a new page in your website and enter the next shortcode to show the <b>Customer Tax Data Registration Module</b> in your website.';?><br/><b>[<?php echo esc_html(strtolower($sistema)); ?>_woocommerce_formulario_receptor]</b>
		</font></label>
		<br/><br/>
		
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Catálogo Clave Servicio':'Service Code Catalog';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si necesitas ver el catálogo oficial del SAT de Claves de Servicio, por favor haz clic <a href="https://realvirtual.com.mx/catalogo-de-productos-y-servicios-cfdi-3-3/" target="_blank"><b>aquí</b></a>. Si necesitas consultar el valor a ingresar en el campo Clave Servicio en la sección <b>Configuración</b>, por favor haz clic <a href="https://realvirtual.com.mx/herramienta-para-la-consulta-de-claves-de-productos-y-servicios/" target="_blank"><b>aquí</b></a>. En caso de no existir la clave correspondiente, ingresa el valor <b>01010101</b>. Te pedimos verlo directamente con tu contador.' : 'If you need to see the official SAT catalog of Service Codes, please click <a href="https://realvirtual.com.mx/catalogo-de-productos-y-servicios-cfdi-3-3/" target="_blank"><b>here</b></a>. If you need to consult the value to enter for the field Service Code in <b>Configuration</b> section, please click <a href="https://realvirtual.com.mx/herramienta-para-la-consulta-de-claves-de-productos-y-servicios/" target="_blank"><b>here</b></a>. In case the corresponding code does not exist, enter the value <b>01010101</b>. We suggest you to see it directly with your accountant.';?>
		</font></label>
		<br/><br/>
		
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Catálogo Clave Unidad':'Unit Code Catalog';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Si necesitas ver el catálogo oficial del SAT de Claves de Unidad, por favor haz clic <a href="https://realvirtual.com.mx/catalogo-clave-unidad/" target="_blank"><b>aquí</b></a>. Si necesitas consultar el valor a ingresar en el campo Clave Unidad en la sección <b>Configuración</b>, por favor haz clic <a href="https://realvirtual.com.mx/herramienta-para-la-consulta-de-claves-de-unidad/" target="_blank"><b>aquí</b></a>. En caso de no existir la clave correspondiente, ingresa el valor <b>ZZ</b>. Te pedimos verlo directamente con tu contador.' : 'If you need to see the official SAT catalog of Unit Codes, please click <a href="https://realvirtual.com.mx/catalogo-clave-unidad/" target="_blank"><b>here</b></a>. If you need to consult the value to enter for the field Unit Code in <b>Configuration</b> section, please click <a href="https://realvirtual.com.mx/herramienta-para-la-consulta-de-claves-de-unidad/" target="_blank"><b>here</b></a>. In case the corresponding code does not exist, enter the value <b>ZZ</b>. We suggest you to see it directly with your accountant.';?>
		</font></label>
		<br/><br/>
		
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Manual de Usuario':'User Manual';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Consulta nuestro manual de usuario para conocer todo acerca de':'Consult our user manual to know everything about';?> <b><?php echo esc_html($nombreSistema); ?></b>.
		</font></label>
		<br/><br/>
		<a href="<?php echo esc_url($urlSistemaAsociado); ?><?php echo ($sistema == 'RVCFDI') ? esc_html('plugin_rv') : esc_html('plugin_lfe'); ?>/<?php echo ($sistema == 'RVCFDI') ? (($idiomaRVLFECFDI == 'ES')?'Manual-RVCFDI_para_WooCommerce.pdf':'Manual-RVCFDI_para_WooCommerce_EN.pdf') : (($idiomaRVLFECFDI == 'ES')?'Manual-LFECFDI_para_WooCommerce.pdf':'Manual-LFECFDI_para_WooCommerce_EN.pdf') ?>" target="_blank"><input type="button" style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ver el Manual':'See the Manual';?>" /></a>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Chat en Línea':'Chat Online';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Estimado usuario, nuestro servicio de Soporte Técnico no tiene costo.
		Estás a punto de entrar a nuestro Sitio Oficial para utilizar nuestro servicio de Chat en Línea.
		Cuando contactes con un asesor, por favor menciona que utilizas el plugin':'Dear user, our Technical Support service has no cost.
		You are about to enter our Official Site to use our Online Chat service.
		When you contacting an advisor, please mention that you use the';?> <b><?php echo esc_html($nombreSistema); ?></b> <?php echo ($idiomaRVLFECFDI == 'ES') ? 'seguido de tu solicitud para poder ubicar fácilmente el sistema que utilizas de entre varios que manejamos y poder brindarte un mejor servicio.':' plugin followed by your request to be able to easily locate the system that you use from several that we handle and to be able to offer you a better service.';?>
		</font></label>
		<br/><br/>
		<a href="<?php echo esc_url($sitioOficialSistema); ?>" target="_blank"><input type="button" style="background-color:#e94700;" class="boton" value="<?php echo ($idiomaRVLFECFDI == 'ES') ? 'Ir al Chat':'Go to Chat';?>" /></a>
		</div>
	<?php
}

function realvirtual_woocommerce_preguntas()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<div style="background-color: #FFFFFF; padding: 20px;overflow-y: scroll; height:100%;">
		<div style="height:auto;">
		<label><font color="#e94700" size="5"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Preguntas Frecuentes':'FAQ';?></b></font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo puedo mostrar el módulo de facturación para mis clientes en mi sitio web?':'How can I display the CFDI Issue module for my clients on my website?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Añade una nueva página en tu wordpress e ingresa el shortcode':'Add a new page in your wordpress and enter the shortcode';?> <font color="#000000" size="2"><b>[<?php echo esc_html(strtolower($sistema)); ?>_woocommerce_formulario]</b></font> <?php echo ($idiomaRVLFECFDI == 'ES') ? 'para mostrar el módulo de facturación para clientes en tu sitio web.':'to show the CFDI issue module for customers in your website.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo puedo mostrar el módulo de registro de datos fiscales para mis clientes en mi sitio web?':'How can I display the tax data registration module for my clients on my website?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Añade una nueva página en tu wordpress e ingresa el shortcode':'Add a new page in your wordpress and enter the shortcode';?> <font color="#000000" size="2"><b>[<?php echo esc_html(strtolower($sistema)); ?>_woocommerce_formulario_receptor]</b></font> <?php echo ($idiomaRVLFECFDI == 'ES') ? 'para mostrar el módulo de registro de datos fiscales para clientes en tu sitio web.':'to show the tax data registration module for clients in your website.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Puedo realizar pruebas con el plugin?':'Can I do tests with the plugin?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí, dispones de un ambiente de pruebas del plugin. Para ello, ingrese los siguientes datos de acceso en la sección <b>Mi Cuenta</b> del plugin.':
		'Yes, you have a plugin testing environment. To use it, enter the following access data in the <b>My Account</b> section of the plugin.';?>
		<br/><br/>
		<b>RFC:</b> XIA190128J61
		<br/><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Usuario:':'User:';?></b> <?php echo ($sistema == 'RVCFDI') ? esc_html('PRUEBASRV') : esc_html('PRUEBASLFE'); ?>
		<br/><b><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Clave cifrada:':'Coded Key:';?></b> ff68f569f16179394aa8d02ccbc761497e42beafc956ab17ab6c7c539649d392
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Puedo utilizar este plugin en otra plataforma que no sea wordpress?':'Can I use this plugin in another platform other than wordpress?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'No, el plugin únicamente es compatible con wordpress. Además, es necesario tener el plugin woocommerce para poder funcionar correctamente.':'No, the plugin is compatible with wordpress only. In addition, you need to have the woocommerce plugin in order to the plugin works correctly.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Puedo emitir CFDI desde el panel de administración del plugin en wordpress?':'Can I issue CFDI from the plugin administration panel in wordpress?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Únicamente se puede emitir una factura global de tus pedidos no facturados. Esto es posible en la sección <b>Factura Global</b> del plugin.':'Only a global invoice can be issued for your non-invoiced orders. This is possible in the <b>Global Invoice</b> section of the plugin.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Puedo administrar mis CFDI emitidos tanto en el panel de administración del plugin en wordpress como en el sistema de facturación':'Can I manage my CFDI issued both in the administration panel of the plugin in wordpress and in the ';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '?':' official system?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí, es posible administrar los CFDI emitidos en ambas plataformas.':'Yes, it is posible to manage the Issued CFDI in both platforms.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿El cliente puede ver una vista previa del CFDI en formato PDF antes de emitirlo?':'Can the customer see a preview of the CFDI in PDF format before issue it?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí, el cliente puede ver una vista previa del CFDI antes de emitirlo.':'Yes, the customer can see a preview of the CFDI before issue it.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo personalizo mi logotipo para que aparezca en la versión PDF de los CFDI emitidos?':'How do I customize my logo to appear in the PDF version of the CFDI issued?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Esta configuración se realiza directamente en el sistema de facturación':'You can do this directly in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a> <?php echo ($idiomaRVLFECFDI == 'ES') ? 'en la sección <b>Mi Cuenta > Logotipo</b>.':' system in the <b>My Account > Logo</b> section.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Dónde cargo mi Certificado de Sello Digital, Llave Privada y Contraseña para poder emitir CFDI?':'Where do I charge my Digital Seal Certificate, Private Key and Password in order to issue CFDI?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Esta configuración se realiza directamente en el sistema de facturación':'You can do this directly in';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a> <?php echo ($idiomaRVLFECFDI == 'ES') ? 'en la sección <b>Mi Cuenta > Certificados</b>.':' system in the <b>My Account > Certificates</b> section.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Qué puedo hacer si aún no tengo cuenta en el sistema de facturación':'What can I do if I do not have an account yet on the';?> <a href="<?php echo esc_url($urlSistemaAsociado); ?>" target="_blank"><b><?php echo esc_html($nombreSistemaAsociado); ?></b></a><?php echo ($idiomaRVLFECFDI == 'ES') ? '?':' system?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Por favor, vaya a la sección <b>Soporte Técnico</b> de este plugin y contacte a un asesor de ventas utilizando nuestro servicio de Chat en Línea para recibir información en un horario de 9 AM a 7 PM (Tiempo de la Ciudad de México).':'Plase, go to the <b>Support</b> section of this plugin and contact a sales consultant using our Online Chat service to receive information from 9 AM to 7 PM (Mexico City Time).'; ?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿El plugin cumple con todos los requisitos que el SAT establece para la correcta estructura de un CFDI con valor fiscal?':'Does the plugin comply with all the requirements that the SAT establishes for the correct structure of a CFDI with fiscal value?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí, el plugin se encuentra actualizado para emitir CFDI que cumpla con la estructura establecida por el SAT. Además, ante cualquier cambio establecido por el SAT actualizaremos el plugin y será posible actualizarlo a la versión más reciente desde tu wordpress.':'Yes, the plugin is updated to issue CFDI that complies with the structure established by the SAT. In addition, before any changes established by the SAT we will update the plugin and it will be possible to update it to the most recent version from your wordpress.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿El plugin soporta varios impuestos en un mismo pedido?':'Does the plugin support multiple taxes in the same order?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Sí. Es necesario considerar que WooCommerce no almacena la tasa de un impuesto en cada pedido. Por tal motivo, el plugin obtiene este dato desde la sección <b>Impuestos</b> de WooCommerce. Esto significa que hay que tener cuidado en caso de eliminar (no será posible obtener la tasa original y el plugin intentará calcularla por su parte) o editar (aparecerá otra tasa diferente a la que se aplicó originalmente a un pedido) la tasa de un impuesto. Únicamente en el caso de que el plugin intente calcular la tasa debido a que el impuesto fue eliminado en <b>Impuestos</b>, aparecerá una advertencia al cliente para que se comunique con el administrador de la tienda virtual y se le confirme si las tasas están bien o no, pudiendo generar o no el CFDI. La única solución ante un pedido defectuoso por este caso, es editarlo agregando de nuevo los impuestos y recalculando las cantidades o en el peor de los casos, volverlo a elaborar. Por último, es necesario considerar que un artículo no puede tener dos veces o más el mismo impuesto trasladado o retenido a pesar de que la tasa sea diferente. Por ejemplo, un artículo puede tener IVA Trasladado e IVA Retenido a la vez, pero no puede tener dos o más veces el IVA Trasladado aunque sus tasas sean diferentes. Si a nivel pedido en WooCommerce no respeta lo anterior para un artículo, nuestro plugin sólo considerará la primera aparición del mismo tipo de impuesto ignorando sus repeticiones posteriores. Por último, los nombres obligatorios que deben tener los impuestos en WooCommerce para que nuestro plugin pueda identificarlos son <b>IVA</b>, <b>IEPS</b>, <b>IVA RETENIDO</b>, <b>IEPS RETENIDO</b>, <b>ISR</b> y/o <b>ISH</b>.':'Yes. It is necessary to consider that WooCommerce does not store the tax rate on each order. For this reason, the plugin obtains this data from the <b>Taxes</b> section of WooCommerce. This means that you have to be careful in case of deletion (it will not be possible to obtain the original rate and the plugin will try to calculate it for you) or edit (a different rate will appear than the one originally applied to an order). Only in case the plugin attempts to calculate the tax due to the tax being removed in <b>Taxes</b>, a warning will appear to the customer to communicate with the administrator of the virtual store and confirm whether the rates are good or not, and may or may not generate the CFDI.The only solution to a defective order in this case, is to edit it by adding the taxes again and recalculating the amounts or, in the worst case, to elaborate it again. Finally, it is necessary to consider that an article cannot have twice or more the same tax transferred or withheld despite the fact that the rate is different. For example, an item can have Transfer VAT and Withheld VAT at the same time, but it cannot have Transfer Tax VAT two or more times even if their rates are different. If at the WooCommerce order level you do not respect the above for an article, our plugin will only consider the first appearance of the same type of tax, ignoring its subsequent repetitions. Finally, the mandatory names that taxes must have in WooCommerce for our plugin to identify them are <b>IVA</b>, <b>IEPS</b>, <b>IVA RETENIDO</b>, <b>IEPS RETENIDO</b>, <b>ISR</b> and/or <b>ISH</b>.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo es el proceso de actualización del plugin?':'How is the plugin update process?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'Cuando se encuentra disponible una actualización del plugin, se puede realizar la actualización del mismo a través de la sección <b>Plugins</b> del panel de administración de Wordpress. Una vez actualizado el plugin, por motivos de seguridad de tu cuenta será necesario ingresar nuevamente el RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>. Al guardar estos cambios, se iniciará sesión y automáticamente se obtendrá desde nuestra base de datos tu configuración del plugin (preferencias y estilos visuales) y se guardará internamente en Wordpress. Llegado a este punto, el proceso de actualización del plugin ha finalizado.':'When a plugin update is available, you can update the plugin through the <b>Plugins</b> section of the Wordpress admin panel. Once the plugin has been updated, for security reasons, it will be necessary to enter the RFC, Username and Encrypted Password again in the <b>My Account</b> section. By saving these changes, you will be logged in and your plugin configuration (preferences and visual styles) will be automatically obtained from our database and will be saved internally in Wordpress. At this point, the plugin update process is complete.';?>
		</font></label>
		<br/><br/>
		<label><font color="#000000" size="4"><b><?php echo ($idiomaRVLFECFDI == 'ES') ? '¿Cómo funciona la opción para recalcular impuestos y subtotales en una factura global?':'How the option to recalculate taxes and subtotals works on a global invoice?';?></b></font></label>
		<br/>
		<label><font color="#505050" size="2"><?php echo ($idiomaRVLFECFDI == 'ES') ? 'En ocasiones, algunos pedidos contienen cantidades calculadas de forma incorrecta para el Subtotal y los Impuestos. En este caso, te recomendamos corregir esta información directamente en tus pedidos en WooCommerce. Sin embargo, puedes activar la opción para recalcular los impuestos y subtotales de tus pedidos automáticamente al emitir la factura global seleccionando que sí deseas que dicho comportamiento suceda. Al activar esta opción, se realizará de manera interna el cálculo de los impuestos y subtotales a partir del total de cada pedido. En caso de existir un descuento en algún pedido, éste se descartará, ya que al calcular a partir del total los impuestos y subtotales, sería imposible adivinar el descuento. El proceso para calcular los impuestos a partir del total consiste en fórmulas matemáticas que consideran la tasa de los impuestos aplicables en el pedido provenientes de WooCommerce. Por último, conociendo el monto total de los impuestos aplicables en el pedido se calcula el subtotal del mismo. El uso de esta opción sólo es útil cuando no se pueda emitir una factura global por cantidades calculadas incorrectamente en los pedidos y su uso es bajo responsabilidad del usuario.':'Sometimes some orders contain incorrectly calculated amounts for Subtotal and Tax. In this case, we recommend that you correct this information directly in your WooCommerce orders. However, you can activate the option to recalculate the taxes and subtotals of your orders automatically when issuing the global invoice by selecting that you do want this behavior to happen. When activating this option, the calculation of taxes and subtotals will be carried out internally from the total of each order. If there is a discount on any order, it will be discarded, since when calculating the taxes and subtotals from the total, it would be impossible to guess the discount. The process for calculating taxes from the total consists of mathematical formulas that consider the rate of applicable taxes on the order from WooCommerce. Finally, knowing the total amount of applicable taxes in the order, the subtotal thereof is calculated. The use of this option is only useful when it is not possible to issue a global invoice for quantities calculated incorrectly in the orders and its use is under the responsibility of the user..';?>
		</font></label>
		<br/><br/>
		</div>
		</div>
	<?php
}

function realvirtual_woocommerce_guiainiciorapido()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<style>
			.sombra
			{
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.7);
			}
		</style>
		<div style="background-color: #FFFFFF; padding-top: 20px; padding-right: 60px; padding-bottom: 20px; padding-left: 20px;">
			<div style="background-color: #e94700; height: 100px; width: 100%; padding-top: 50px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<table style="width:100%;">
					<tr>
						<td style="text-align: center;">
							<label><font color="#ffffff" style="font-size: 240%; font-family: Arial,sans-serif;">
								Guía de Inicio Rápido
							</font></label>
						</td>
					</tr>
					<tr>
						<td style="text-align: center;">
							<br/>
							<label><font color="#ffffff" style="font-size: 160%; font-family: Arial,sans-serif;">
								Configura todo lo necesario para que tus clientes puedan emitir CFDI desde tu sitio web
							</font></label>
						</td>
					</tr>
				</table>
			</div>
			<div style="text-align: left; background-color: #ffffff; height: 410px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<table style="width:100%;">
					<tr>
						<td style="text-align: left; width:50%;">
							<div style="width:90%;background-color: #ffffff;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
									1. Inicia Sesión
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									En la sección <b>Mi Cuenta</b> ingresa los datos de acceso de tu cuenta. Puedes obtenerlos en el sistema <b>RV Factura Electrónica Web</b> en la sección <b>RVCFDI para WooCommerce > Datos de acceso</b> ubicada en el menú izquierdo del mismo.
									<br/><br/>
									Si lo deseas, puedes activar el modo de pruebas. El plugin iniciará sesión automáticamente con una cuenta propia de pruebas.
								</font></label>
							</div>
						</td>
						<td style="text-align: left;">
							<div style="width:40%;background-color: #ffffff;padding-top: 0px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'/imagenes/guia1.png'); ?>" width="650" height="400" class="sombra">
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="text-align: left; background-color: #f0f0f1; height: 240px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<div style="width:90%;background-color: #f0f0f1;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
					<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
						2. Personaliza el plugin
					</font></label>
					<br/><br/>
					<font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
						<label>
							En la sección <b>Configuración</b> podrás personalizar el plugin y su funcionamiento para la emisión de CFDI.
						<br/><br/>
						▪ Podrás configurar tus datos de facturación, el aspecto visual del módulo de facturación para clientes en tu sitio web, así como el comportamiento del mismo configurando algunas reglas.
						<br/><br/>
						▪ Podrás realizar ajustes avanzados. Por ejemplo, podrás establecer si se emitirá automáticamente el CFDI de un pedido cuando su estado cambie a Completado.
						<br/><br/>
						▪ Podrás seleccionar el idioma del plugin.
						</label>
					</font>
				</div>
			</div>
			<div style="background-color: #ffffff; height: 300px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<table style="width:90%;">
					<tr>
						<td style="text-align: left; width:50%;">
							<div style="width:90%;background-color: #ffffff;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
									3. Activa el módulo de facturación para tus clientes en tu sitio web
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									Haz que tus clientes puedan ver el módulo de facturación electrónica en tu sitio web. Para ello, crea o edita una página en la sección <b>Páginas</b> de tu Wordpress y escribe el shortcode <b>[rvcfdi_woocommerce_formulario]</b> y guarda los cambios. 
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									Una vez finalizado, el módulo de facturación ya será visible para tus clientes en la página que modificaste y ya podrán emitir el CFDI de sus pedidos.
								</font></label>
							</div>
						</td>
						<td style="text-align: left;">
							<div style="width:40%;background-color: #ffffff;padding-top: 0px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'/imagenes/guia2.png'); ?>" width="350" height="280" class="sombra">
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="text-align: left; background-color: #f0f0f1; height: 160px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<div style="width:90%;background-color: #f0f0f1;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
					<label><font color="#e94700" style="font-size: 200%; font-family: Arial,sans-serif;">
						Módulo de Facturación para Clientes
					</font></label>
					<br/><br/>
					<font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
						<label>
							Tus clientes visualizarán el módulo de facturación en tu sitio web en donde podrán emitir el CFDI de sus pedidos.
							<br/><br/>
							El proceso de emisión de CFDI consta de 4 pasos, los cuales te explicamos a continuación.
						</label>
					</font>
				</div>
			</div>
			<div style="background-color: #ffffff; height: 290px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<table style="width:90%;">
					<tr>
						<td style="text-align: left; width:50%;">
							<div style="width:90%;background-color: #ffffff;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
									PASO 1
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									El cliente debe ingresar el número de pedido y el monto.
								</font></label>
							</div>
						</td>
						<td style="text-align: left;">
							<div style="width:40%;background-color: #ffffff;padding-top: 0px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'/imagenes/guia3.png'); ?>" width="430" height="280" class="sombra">
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="background-color: #f0f0f1; height: 410px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<table style="width:90%;">
					<tr>
						<td style="text-align: left; width:50%;">
							<div style="width:90%;background-color: #f0f0f1;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
									PASO 2
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									El cliente debe ingresar sus datos fiscales para la 
									emisión del CFDI. El cliente se 
									registrará o actualizará con los 
									datos ingresados en este paso en el sistema principal
									<b>RV Factura Electrónica Web</b> al 
									emitir el CFDI.
								</font></label>
							</div>
						</td>
						<td style="text-align: left;">
							<div style="width:40%;background-color: #f0f0f1;padding-top: 0px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'/imagenes/guia4.png'); ?>" width="490" height="400" class="sombra">
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="background-color: #ffffff; height: 710px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<table style="width:90%;">
					<tr>
						<td style="text-align: left; width:50%;">
							<div style="width:90%;background-color: #ffffff;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
									PASO 3
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									El cliente podrá confirmar la información del CFDI a emitir.
									<br/><br/>
									▪ Podrá ver la información del <b>emisor</b> y el <b>receptor</b>, el detalle de los <b>artículos</b> y sus <b>impuestos</b>, así como el <b>subtotal</b> y el <b>total</b> final.
									<br/><br/>
									▪ Podrá seleccionar el <b>método de pago</b>, la <b>forma de pago</b> y el <b>uso de cfdi</b> únicamente si has permitido este comportamiento en la configuración del plugin.
									<br/><br/>
									▪ Podrá descargar la vista previa del CFDI en formato PDF antes de emitirlo.
									<br/><br/>
									▪ Podrá emitir el CFDI del pedido.
								</font></label>
							</div>
						</td>
						<td style="text-align: left;">
							<div style="width:40%;background-color: #ffffff;padding-top: 0px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'/imagenes/guia5.png'); ?>" width="600" height="700" class="sombra">
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div style="background-color: #f0f0f1; height: 290px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<table style="width:90%;">
					<tr>
						<td style="text-align: left; width:50%;">
							<div style="width:90%;background-color: #f0f0f1;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
									PASO 4
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									Una vez emitido el CFDI el cliente recibe en su correo electrónico el CFDI en formato XML y PDF. También podrá realizar la descarga de estos archivos
									en este paso final.
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									Gracias por leer esta guía de inicio rápido.
								</font></label>
							</div>
						</td>
						<td style="text-align: left;">
							<div style="width:40%;background-color: #f0f0f1;padding-top: 0px;padding-right: 30px;padding-bottom: 10px;padding-left: 30px;">
								<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'/imagenes/guia6.png'); ?>" width="430" height="280" class="sombra">
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		
	<?php
}

function realvirtual_woocommerce_guiadatosfiscales()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	?>
		<style>
			.sombra
			{
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.7);
			}
		</style>
		<div style="background-color: #FFFFFF; padding-top: 20px; padding-right: 60px; padding-bottom: 20px; padding-left: 20px;">
			<div style="background-color: #e94700; height: 100px; width: 100%; padding-top: 50px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<table style="width:100%;">
					<tr>
						<td style="text-align: center;">
							<label><font color="#ffffff" style="font-size: 240%; font-family: Arial,sans-serif;">
								Guía del Módulo de Datos Fiscales para Clientes
							</font></label>
						</td>
					</tr>
					<tr>
						<td style="text-align: center;">
							<br/>
							<label><font color="#ffffff" style="font-size: 160%; font-family: Arial,sans-serif;">
								Configura todo lo necesario para que tus clientes puedan registrar sus datos fiscales en sus cuentas de usuario de tu tienda virtual.
							</font></label>
						</td>
					</tr>
				</table>
			</div>
			<div style="text-align: left; background-color: #f0f0f1; height: 580px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<div style="width:90%;background-color: #f0f0f1;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
					<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
						¿En qué consiste?
					</font></label>
					<br/><br/>
					<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
						Al activar el <b>Módulo de Datos Fiscales para Clientes</b> permitirás a tus clientes visualizar un formulario que podrán utilizar para guardar sus datos fiscales, mismos que serán asociados a sus cuentas de usuario de tu tienda virtual.
						<br/>
						De esta forma, estos datos fiscales serán utilizados por defecto al emitir automáticamente el CFDI de sus pedidos cuando estos cambien a un estado específico.
						<br/>
						Igualmente, estos datos serán cargados en el formulario del <b>Paso 2</b> del <b>Módulo de Facturación para Clientes</b> siempre y cuando el cliente que lo utilice haya iniciado sesión.
					</font></label>
					<br/>
					<br/>
					<label>
						<font color="#006dc6" style="font-size: 130%; font-family: Arial,sans-serif;">
							<b>IMPORTANTE:</b>
						</font>
						<font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
							<br/></br>
							▪ Para que esta característica funcione correctamente, el módulo de datos fiscales debe ser mostrado al cliente en algún lugar de la configuración de su cuenta de usuario que tiene en tu tienda virtual.
							<br/></br>
							▪ Esto es debido a que cuando un cliente guarda sus datos fiscales a través del módulo, sus datos fiscales se relacionan a su cuenta de usuario internamente en una base de datos.
							<br/></br>
							▪ De esta manera, cuando se emita automáticamente el CFDI de un pedido el plugin identificará al cliente del mismo y podrá obtener los datos fiscales que tenga registrados para usarlos en la emisión del CFDI.
							</br></br>
							▪ Como sugerencia, el módulo de datos fiscales podrías mostrarlo dentro de la sección <b>Mi Cuenta</b> o <b>Mi Perfil</b> de tu tienda virtual, para que tus clientes inicien sesión y al ingresar a la sección mencionada, puedan encontrar el módulo de datos fiscales.
							</br></br>
							▪ En caso de que el cliente no tenga datos fiscales registrados y la función de emisión de CFDI automática de un pedido esté activa, entonces se usarán los datos fiscales correspondientes al RFC genérico XAXX010101000.
							</br></br>
							▪ En caso de que el cliente no tenga datos fiscales registrados y use el <b>Módulo de Facturación para Clientes</b>, entonces el formulario del <b>Paso 2</b> estará en blanco para capturar manualmente los datos fiscales.
							</br></br>
							▪ Los datos fiscales ingresados en el <b>Módulo de Facturación para Clientes</b> no causa una actualización de los datos fiscales existentes en el <b>Módulo de Datos Fiscales para Clientes</b>.
						</font>
					</label>
				</div>
			</div>
			<div style="text-align: left; background-color: #ffffff; height: 210px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<div style="width:90%;background-color: #ffffff;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
					<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
						¿Cómo activo el Módulo de Datos Fiscales para Clientes?
					</font></label>
					<br/><br/>
					<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
						Crea o edita una página en la sección <b>Páginas</b> de tu Wordpress y escribe el shortcode <b>[rvcfdi_woocommerce_formulario_receptor]</b> y guarda los cambios. 
					</font></label>
					<br/><br/>
					<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
						Recuerda los puntos importantes a considerar ya mencionados para que este módulo funcione correctamente. 
					</font></label>
					<br/><br/>
					<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
						Una vez finalizado, el módulo de datos fiscales ya será visible para tus clientes en la página que modificaste y ya podrán guardar sus datos fiscales que quedarán asociados a su cuenta de usuario de tu tienda virtual.
					</font></label>
				</div>
			</div>
			<div style="background-color: #f0f0f1; height: 470px; width: 100%; padding-top: 20px; padding-right: 20px; padding-bottom: 20px; padding-left: 20px;">
				<table style="width:90%;">
					<tr>
						<td style="text-align: left; width:50%;">
							<div style="width:90%;background-color: #f0f0f1;padding-top: 30px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<label><font color="#000000" style="font-size: 200%; font-family: Arial,sans-serif;">
									Una vez activado...
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									Tu cliente ya podrá visualizar el módulo de datos fiscales dentro de la sección de configuración de su cuenta de usuario en tu tienda virtual. Por ejemplo, dentro de una sección <b>Mi Cuenta</b> o <b>Mi Perfil</b>.
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									El cliente debe ingresar sus datos fiscales para la emisión de CFDI y guardar los cambios.
								</font></label>
								<br/><br/>
								<label><font color="#000000" style="font-size: 130%; font-family: Arial,sans-serif;">
									El <b>Uso CFDI</b> que el cliente seleccione en este módulo será utilizado por defecto en el <b>Módulo de Facturación para Clientes</b> independientemente de la configuración para el campo <b>Uso CFDI</b> que hayas establecido en <b>Configuración > Reglas del módulo de facturación para clientes</b>.
								</font></label>
							</div>
						</td>
						<td style="text-align: left;">
							<div style="width:40%;background-color: #f0f0f1;padding-top: 0px;padding-right: 30px;padding-bottom: 30px;padding-left: 30px;">
								<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'/imagenes/guia_receptor1.png'); ?>" width="700" height="450" class="sombra">
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		
	<?php
}

add_action('wp_ajax_realvirtual_woocommerce_buscar_facturarpedidos', 'realvirtual_woocommerce_buscar_facturarpedidos_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_buscar_facturarpedidos', 'realvirtual_woocommerce_buscar_facturarpedidos_callback');

function realvirtual_woocommerce_buscar_facturarpedidos_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post, $idiomaRVLFECFDI;
	
	$fechaInicial = sanitize_text_field($_POST['fechaInicial']);
	update_post_meta($post->ID, 'fechaInicial', $fechaInicial);
	
	$fechaFinal = sanitize_text_field($_POST['fechaFinal']);
	update_post_meta($post->ID, 'fechaFinal', $fechaFinal);
	
	$estadoOrden = sanitize_text_field($_POST['estadoOrden']);
	update_post_meta($post->ID, 'estadoOrden', $estadoOrden);
	
	$fechaInicial				= $_POST['fechaInicial'];
	$fechaFinal					= $_POST['fechaFinal'];
	$estadoOrden				= $_POST['estadoOrden'];
	
	$fechaDesde = $fechaInicial;
	$fechaHasta = $fechaFinal;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();	
	$datosPedidos = RealVirtualWooCommercePedido::obtenerPedidosFacturacion($fechaDesde, $fechaHasta, $estadoOrden, $configuracion['precision_decimal'], $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
	if($datosPedidos->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $datosPedidos->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'pedidosHTML' => $datosPedidos->pedidosHTML,
			'pedidosJSON' => $datosPedidos->pedidosJSON,
			'pedidos' => $datosPedidos->pedidos
		);
	}
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_leercsv_facturaglobal', 'realvirtual_woocommerce_leercsv_facturaglobal_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_leercsv_facturaglobal', 'realvirtual_woocommerce_leercsv_facturaglobal_callback');

function realvirtual_woocommerce_leercsv_facturaglobal_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post, $idiomaRVLFECFDI;
	
	$csv = sanitize_text_field($_POST['csv']);
	update_post_meta($post->ID, 'csv', $csv);
	
	$versionCFDI = sanitize_text_field($_POST['versionCFDI']);
	update_post_meta($post->ID, 'versionCFDI', $versionCFDI);
	
	$csv = $_POST['csv'];
	$versionCFDI = $_POST['versionCFDI'];
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();	
	$datosPedidos = RealVirtualWooCommercePedido::obtenerPedidosCSV($csv, $configuracion['precision_decimal'], $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI, $versionCFDI);
	
	if($datosPedidos->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $datosPedidos->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'pedidosHTML' => $datosPedidos->pedidosHTML,
			'pedidosJSON' => $datosPedidos->pedidosJSON,
			'pedidos' => $datosPedidos->pedidos,
			'total_pedidos' => $datosPedidos->total_pedidos,
			'total_subtotal' => $datosPedidos->total_subtotal,
			'total_descuento' => $datosPedidos->total_descuento,
			'total_iva' => $datosPedidos->total_iva,
			'total_ieps' => $datosPedidos->total_ieps,
			'total_otros_impuestos' => $datosPedidos->total_otros_impuestos,
			'total_total' => $datosPedidos->total_total,
			'pedidos_total_subtotal' => $datosPedidos->pedidos_total_subtotal,
			'pedidos_total_descuento' => $datosPedidos->pedidos_total_descuento,
			'pedidos_total_iva' => $datosPedidos->pedidos_total_iva,
			'pedidos_total_ieps' => $datosPedidos->pedidos_total_ieps,
			'pedidos_total_total' => $datosPedidos->pedidos_total_total,
			'filtroBusqueda_fechaDesde' => $datosPedidos->fechaDesde,
			'filtroBusqueda_fechaHasta' => $datosPedidos->fechaHasta,
			'filtroBusqueda_estadoOrden' => $datosPedidos->estadoOrden,
			'filtroBusqueda_metodoPagoOrden' => $datosPedidos->metodoPagoOrden,
			'filtroBusqueda_numerosPedidosExcluir' => $datosPedidos->numerosPedidosExcluir,
			'total_subtotal_noFacturado' => $datosPedidos->total_subtotal_noFacturado,
			'total_descuento_noFacturado' => $datosPedidos->total_descuento_noFacturado,
			'total_total_noFacturado' => $datosPedidos->total_total_noFacturado
		);
	}
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_buscar_facturaglobal', 'realvirtual_woocommerce_buscar_facturaglobal_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_buscar_facturaglobal', 'realvirtual_woocommerce_buscar_facturaglobal_callback');

function realvirtual_woocommerce_buscar_facturaglobal_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post, $idiomaRVLFECFDI;
	
	$fechaInicial = sanitize_text_field($_POST['fechaInicial']);
	update_post_meta($post->ID, 'fechaInicial', $fechaInicial);
	
	$fechaFinal = sanitize_text_field($_POST['fechaFinal']);
	update_post_meta($post->ID, 'fechaFinal', $fechaFinal);
	
	$estadoOrden = sanitize_text_field($_POST['estadoOrden']);
	update_post_meta($post->ID, 'estadoOrden', $estadoOrden);
	
	$metodoPagoOrden = sanitize_text_field($_POST['metodoPagoOrden']);
	update_post_meta($post->ID, 'metodoPagoOrden', $metodoPagoOrden);
	
	$numerosPedidosExcluir = sanitize_text_field($_POST['numerosPedidosExcluir']);
	update_post_meta($post->ID, 'numerosPedidosExcluir', $numerosPedidosExcluir);
	
	$versionCFDI = sanitize_text_field($_POST['versionCFDI']);
	update_post_meta($post->ID, 'versionCFDI', $versionCFDI);
	
	$fechaInicial				= $_POST['fechaInicial'];
	$fechaFinal					= $_POST['fechaFinal'];
	$estadoOrden				= $_POST['estadoOrden'];
	$metodoPagoOrden			= $_POST['metodoPagoOrden'];
	$numerosPedidosExcluir		= $_POST['numerosPedidosExcluir'];
	$versionCFDI		= $_POST['versionCFDI'];
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();	
	$datosPedidos = RealVirtualWooCommercePedido::obtenerPedidos($fechaInicial, $fechaFinal, $estadoOrden, $metodoPagoOrden, $numerosPedidosExcluir, $configuracion['precision_decimal'], $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI, $versionCFDI);
	
	if($datosPedidos->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $datosPedidos->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'pedidosHTML' => $datosPedidos->pedidosHTML,
			'pedidosJSON' => $datosPedidos->pedidosJSON,
			'pedidos' => $datosPedidos->pedidos,
			'total_pedidos' => $datosPedidos->total_pedidos,
			'total_subtotal' => $datosPedidos->total_subtotal,
			'total_descuento' => $datosPedidos->total_descuento,
			'total_iva' => $datosPedidos->total_iva,
			'total_ieps' => $datosPedidos->total_ieps,
			'total_otros_impuestos' => $datosPedidos->total_otros_impuestos,
			'total_total' => $datosPedidos->total_total,
			'pedidos_total_subtotal' => $datosPedidos->pedidos_total_subtotal,
			'pedidos_total_descuento' => $datosPedidos->pedidos_total_descuento,
			'pedidos_total_iva' => $datosPedidos->pedidos_total_iva,
			'pedidos_total_ieps' => $datosPedidos->pedidos_total_ieps,
			'pedidos_total_total' => $datosPedidos->pedidos_total_total,
			'filtroBusqueda_fechaDesde' => $datosPedidos->fechaDesde,
			'filtroBusqueda_fechaHasta' => $datosPedidos->fechaHasta,
			'filtroBusqueda_estadoOrden' => $datosPedidos->estadoOrden,
			'filtroBusqueda_metodoPagoOrden' => $datosPedidos->metodoPagoOrden,
			'filtroBusqueda_numerosPedidosExcluir' => $datosPedidos->numerosPedidosExcluir,
			'total_subtotal_noFacturado' => $datosPedidos->total_subtotal_noFacturado,
			'total_descuento_noFacturado' => $datosPedidos->total_descuento_noFacturado,
			'total_total_noFacturado' => $datosPedidos->total_total_noFacturado
		);
	}
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_timbrar_facturaglobal', 'realvirtual_woocommerce_timbrar_facturaglobal_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_timbrar_facturaglobal', 'realvirtual_woocommerce_timbrar_facturaglobal_callback');

function realvirtual_woocommerce_timbrar_facturaglobal_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post, $idiomaRVLFECFDI;
	
	$fg_serie = sanitize_text_field($_POST['fg_serie']);
	update_post_meta($post->ID, 'fg_serie', $fg_serie);
	
	$fg_forma_pago = sanitize_text_field($_POST['fg_forma_pago']);
	update_post_meta($post->ID, 'fg_forma_pago', $fg_forma_pago);
	
	$fg_moneda = sanitize_text_field($_POST['fg_moneda']);
	update_post_meta($post->ID, 'fg_moneda', $fg_moneda);
	
	$fg_tipo_cambio = sanitize_text_field($_POST['fg_tipo_cambio']);
	update_post_meta($post->ID, 'fg_tipo_cambio', $fg_tipo_cambio);
	
	$fg_recalcular_impuestos = sanitize_text_field($_POST['fg_recalcular_impuestos']);
	update_post_meta($post->ID, 'fg_recalcular_impuestos', $fg_recalcular_impuestos);
	
	$fg_fecha_emision = sanitize_text_field($_POST['fg_fecha_emision']);
	update_post_meta($post->ID, 'fg_fecha_emision', $fg_fecha_emision);
	
	$fg_precision_decimal = sanitize_text_field($_POST['fg_precision_decimal']);
	update_post_meta($post->ID, 'fg_precision_decimal', $fg_precision_decimal);
	
	$fg_subtotal = sanitize_text_field($_POST['fg_subtotal']);
	update_post_meta($post->ID, 'fg_subtotal', $fg_subtotal);
	
	$fg_descuento = sanitize_text_field($_POST['fg_descuento']);
	update_post_meta($post->ID, 'fg_descuento', $fg_descuento);
	
	$fg_total = sanitize_text_field($_POST['fg_total']);
	update_post_meta($post->ID, 'fg_total', $fg_total);
	
	$pedidosJSON = sanitize_text_field($_POST['pedidosJSON']);
	update_post_meta($post->ID, 'pedidosJSON', $pedidosJSON);
	
	$fg_version = sanitize_text_field($_POST['fg_version']);
	update_post_meta($post->ID, 'fg_version', $fg_version);
	
	$fg_periodicidad = sanitize_text_field($_POST['fg_periodicidad']);
	update_post_meta($post->ID, 'fg_periodicidad', $fg_periodicidad);
	
	$fg_meses = sanitize_text_field($_POST['fg_meses']);
	update_post_meta($post->ID, 'fg_meses', $fg_meses);
	
	$fg_año = sanitize_text_field($_POST['fg_año']);
	update_post_meta($post->ID, 'fg_año', $fg_año);
	
	$fg_impuestosAntiguos = sanitize_text_field($_POST['fg_impuestosAntiguos']);
	update_post_meta($post->ID, 'fg_impuestosAntiguos', $fg_impuestosAntiguos);
	
	$fg_serie				= trim($_POST['fg_serie']);
	$fg_forma_pago			= trim($_POST['fg_forma_pago']);
	$fg_moneda				= trim($_POST['fg_moneda']);
	$fg_tipo_cambio			= trim($_POST['fg_tipo_cambio']);
	$fg_recalcular_impuestos = trim($_POST['fg_recalcular_impuestos']);
	$fg_precision_decimal	= trim($_POST['fg_precision_decimal']);
	$pedidosJSON        	= trim($_POST['pedidosJSON']);
	$fg_fecha_emision		= trim($_POST['fg_fecha_emision']);
	$fg_version				= trim($_POST['fg_version']);
	$fg_periodicidad		= trim($_POST['fg_periodicidad']);
	$fg_meses				= trim($_POST['fg_meses']);
	$fg_año					= trim($_POST['fg_año']);
	$fg_impuestosAntiguos	= trim($_POST['fg_impuestosAntiguos']);
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();	
	
	if((!isset($configuracion['manejo_impuestos_pedido_facturaGlobal'])) || is_null($configuracion['manejo_impuestos_pedido_facturaGlobal']) || $configuracion['manejo_impuestos_pedido_facturaGlobal'] == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Establezca cómo utilizará el plugin los montos de los pedidos para la factura global en la sección <b>Configuración</b> y vuelva a abrir la sección <b>Factura Global</b> para aplicar los cambios.' : 'Set how the plugin will use the order amounts for the global invoice in the <b>Configuration</b> section and reopen the <b>Global Invoice</b> section to apply the changes.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$resultado = RealVirtualWooCommerceCFDI::generarFacturaGlobal
	(
		$cuenta['rfc'],
		$cuenta['usuario'],
		$cuenta['clave'],
		$fg_serie,
		$fg_forma_pago,
		$fg_moneda,
		$fg_tipo_cambio,
		$pedidosJSON,
		$fg_subtotal,
		$fg_descuento,
		$fg_total,
		$configuracion['regimen_fiscal'],
		$configuracion['clave_confirmacion'],
		$fg_precision_decimal,
		$sistema,
		$fg_fecha_emision,
		$urlSistemaAsociado,
		$idiomaRVLFECFDI,
		//$fg_recalcular_impuestos
		$configuracion['manejo_impuestos_pedido_facturaGlobal'],
		$configuracion['huso_horario'],
		$fg_version, 
		$configuracion['exportacion_cfdi'],
		$fg_periodicidad,
		$fg_meses,
		$fg_año,
		$fg_impuestosAntiguos
	);
	
	$respuesta = array
	(
		'success' => $resultado->success,
		'message' => $resultado->message
	);
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_vistaprevia_facturaglobal', 'realvirtual_woocommerce_vistaprevia_facturaglobal_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_vistaprevia_facturaglobal', 'realvirtual_woocommerce_vistaprevia_facturaglobal_callback');

function realvirtual_woocommerce_vistaprevia_facturaglobal_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post, $idiomaRVLFECFDI;
	
	$fg_serie = sanitize_text_field($_POST['fg_serie']);
	update_post_meta($post->ID, 'fg_serie', $fg_serie);
	
	$fg_forma_pago = sanitize_text_field($_POST['fg_forma_pago']);
	update_post_meta($post->ID, 'fg_forma_pago', $fg_forma_pago);
	
	$fg_moneda = sanitize_text_field($_POST['fg_moneda']);
	update_post_meta($post->ID, 'fg_moneda', $fg_moneda);
	
	$fg_tipo_cambio = sanitize_text_field($_POST['fg_tipo_cambio']);
	update_post_meta($post->ID, 'fg_tipo_cambio', $fg_tipo_cambio);
	
	$fg_recalcular_impuestos = sanitize_text_field($_POST['fg_recalcular_impuestos']);
	update_post_meta($post->ID, 'fg_recalcular_impuestos', $fg_recalcular_impuestos);
	
	$fg_fecha_emision = sanitize_text_field($_POST['fg_fecha_emision']);
	update_post_meta($post->ID, 'fg_fecha_emision', $fg_fecha_emision);
	
	$fg_precision_decimal = sanitize_text_field($_POST['fg_precision_decimal']);
	update_post_meta($post->ID, 'fg_precision_decimal', $fg_precision_decimal);
	
	$fg_subtotal = sanitize_text_field($_POST['fg_subtotal']);
	update_post_meta($post->ID, 'fg_subtotal', $fg_subtotal);
	
	$fg_descuento = sanitize_text_field($_POST['fg_descuento']);
	update_post_meta($post->ID, 'fg_descuento', $fg_descuento);
	
	$fg_total = sanitize_text_field($_POST['fg_total']);
	update_post_meta($post->ID, 'fg_total', $fg_total);
	
	$pedidosJSON = sanitize_text_field($_POST['pedidosJSON']);
	update_post_meta($post->ID, 'pedidosJSON', $pedidosJSON);
	
	$fg_version = sanitize_text_field($_POST['fg_version']);
	update_post_meta($post->ID, 'fg_version', $fg_version);
	
	$fg_periodicidad = sanitize_text_field($_POST['fg_periodicidad']);
	update_post_meta($post->ID, 'fg_periodicidad', $fg_periodicidad);
	
	$fg_meses = sanitize_text_field($_POST['fg_meses']);
	update_post_meta($post->ID, 'fg_meses', $fg_meses);
	
	$fg_año = sanitize_text_field($_POST['fg_año']);
	update_post_meta($post->ID, 'fg_año', $fg_año);
	
	$fg_impuestosAntiguos = sanitize_text_field($_POST['fg_impuestosAntiguos']);
	update_post_meta($post->ID, 'fg_impuestosAntiguos', $fg_impuestosAntiguos);
	
	$fg_serie				= trim($_POST['fg_serie']);
	$fg_forma_pago			= trim($_POST['fg_forma_pago']);
	$fg_moneda				= trim($_POST['fg_moneda']);
	$fg_tipo_cambio			= trim($_POST['fg_tipo_cambio']);
	$fg_recalcular_impuestos = trim($_POST['fg_recalcular_impuestos']);
	$fg_precision_decimal	= trim($_POST['fg_precision_decimal']);
	$pedidosJSON        	= trim($_POST['pedidosJSON']);
	$fg_fecha_emision		= trim($_POST['fg_fecha_emision']);
	$fg_version				= trim($_POST['fg_version']);
	$fg_periodicidad		= trim($_POST['fg_periodicidad']);
	$fg_meses				= trim($_POST['fg_meses']);
	$fg_año					= trim($_POST['fg_año']);
	$fg_impuestosAntiguos	= trim($_POST['fg_impuestosAntiguos']);
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();	
	
	if((!isset($configuracion['manejo_impuestos_pedido_facturaGlobal'])) || is_null($configuracion['manejo_impuestos_pedido_facturaGlobal']) || $configuracion['manejo_impuestos_pedido_facturaGlobal'] == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Establezca cómo utilizará el plugin los montos de los pedidos para la factura global en la sección <b>Configuración</b> y vuelva a abrir la sección <b>Factura Global</b> para aplicar los cambios.' : 'Set how the plugin will use the order amounts for the global invoice in the <b>Configuration</b> section and reopen the <b>Global Invoice</b> section to apply the changes.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$resultado = RealVirtualWooCommerceCFDI::generarVistaPreviaFacturaGlobal
	(
		$cuenta['rfc'],
		$cuenta['usuario'],
		$cuenta['clave'],
		$fg_serie,
		$fg_forma_pago,
		$fg_moneda,
		$fg_tipo_cambio,
		$pedidosJSON,
		$fg_subtotal,
		$fg_descuento,
		$fg_total,
		$configuracion['regimen_fiscal'],
		$configuracion['clave_confirmacion'],
		$fg_precision_decimal,
		$sistema,
		$fg_fecha_emision,
		$urlSistemaAsociado,
		$idiomaRVLFECFDI,
		//$fg_recalcular_impuestos
		$configuracion['manejo_impuestos_pedido_facturaGlobal'],
		$configuracion['huso_horario'],
		$fg_version,
		$configuracion['exportacion_cfdi'],
		$fg_periodicidad,
		$fg_meses,
		$fg_año,
		$fg_impuestosAntiguos
	);
	
	if($resultado->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $resultado->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'message' => $resultado->message,
			'CFDI_PDF' => $resultado->CFDI_PDF
		);
	}
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_guardar_cuenta', 'realvirtual_woocommerce_guardar_cuenta_callback');

function realvirtual_woocommerce_guardar_cuenta_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
	
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$rfc = sanitize_text_field($_POST['rfc']);
	update_post_meta($post->ID, 'rfc', $rfc);
	
	$usuario = sanitize_text_field($_POST['usuario']);
	update_post_meta($post->ID, 'usuario', $usuario);
	
	$clave = sanitize_text_field($_POST['clave']);
	update_post_meta($post->ID, 'clave', $clave);
	
	if(!preg_match("/^([A-Z]|&|Ñ){3,4}[0-9]{2}[0-1][0-9][0-3][0-9]([A-Z]|[0-9]){2}([0-9]|A){1}$/", $_POST['rfc']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El RFC tiene un formato inválido.':'The RFC has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/^[a-zA-Z0-9\s\#\$\+\%\(\)\[\]\*\¡\!\=\\/\&\.\,\;\:\-\_\ñ\á\é\í\ó\ú\Á\É\Í\Ó\Ú\Ñ]*$/", $_POST['usuario']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El usuario tiene un formato inválido.':'The user has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/^[0-9a-zA-Z]+$/", $_POST['clave']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La clave cifrada tiene un formato inválido.':'The coded key has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $cuenta = array
	(
        'rfc'      	=> $_POST['rfc'],
        'usuario'   => $_POST['usuario'],
        'clave'     => $_POST['clave']
    );
	
    $guardado = RealVirtualWooCommerceCuenta::guardarCuenta($cuenta, $_POST['rfc'], $_POST['usuario'], $_POST['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);

	$respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message,
	   'EMISOR_RENOVACION' => $guardado->EMISOR_RENOVACION,
	   'EMISOR_VIGENCIA' => $guardado->EMISOR_VIGENCIA,
	   'EMISOR_ESTADO' => $guardado->EMISOR_ESTADO,
	   'EMISOR_TIPO_USUARIO' => $guardado->EMISOR_TIPO_USUARIO,
	   'sistema' => $sistema
    );
	
	if($guardado->ESTADO_ORDEN != '')
	{
		$color_boton_vistaprevia = trim($guardado->COLOR_BOTON_VISTAPREVIA);
		$color_texto_boton_vistaprevia = trim($guardado->COLOR_TEXTO_BOTON_VISTAPREVIA);
		$color_boton_generarcfdi = trim($guardado->COLOR_BOTON_GENERARCFDI);
		$color_texto_boton_generarcfdi = trim($guardado->COLOR_TEXTO_BOTON_GENERARCFDI);
		
		if(!isset($color_boton_vistaprevia) || is_null($color_boton_vistaprevia) || $color_boton_vistaprevia == '')
			$color_boton_vistaprevia = trim($guardado->COLOR_BOTON);
		if(!isset($color_texto_boton_vistaprevia) || is_null($color_texto_boton_vistaprevia) || $color_texto_boton_vistaprevia == '')
			$color_texto_boton_vistaprevia = trim($guardado->COLOR_TEXTO_BOTON);
		if(!isset($color_boton_generarcfdi) || is_null($color_boton_generarcfdi) || $color_boton_generarcfdi == '')
			$color_boton_generarcfdi = trim($guardado->COLOR_BOTON);
		if(!isset($color_texto_boton_generarcfdi) || is_null($color_texto_boton_generarcfdi) || $color_texto_boton_generarcfdi == '')
			$color_texto_boton_generarcfdi = trim($guardado->COLOR_TEXTO_BOTON);
		
		$configuracion = array
		(
			'serie'       							=> trim($guardado->SERIE),
			'estado_orden'       					=> trim($guardado->ESTADO_ORDEN),
			'titulo'       							=> trim($guardado->TITULO),
			'descripcion'   						=> trim($guardado->DESCRIPCION),
			'color_fondo_encabezado'      			=> trim($guardado->COLOR_FONDO_ENCABEZADO),
			'color_texto_encabezado'       			=> trim($guardado->COLOR_TEXTO_ENCABEZADO),
			'color_fondo_formulario'      			=> trim($guardado->COLOR_FONDO_FORMULARIO),
			'color_texto_formulario'       			=> trim($guardado->COLOR_TEXTO_FORMULARIO),
			'color_texto_controles_formulario'      => trim($guardado->COLOR_TEXTO_CONTROLES_FORMULARIO),
			'color_boton'       					=> trim($guardado->COLOR_BOTON),
			'color_texto_boton'       				=> trim($guardado->COLOR_TEXTO_BOTON),
			'estado_orden_refacturacion'       		=> trim($guardado->ESTADO_ORDEN_REFACTURACION),
			'version_cfdi'							=> trim($guardado->VERSION_CFDI),
			'metodo_pago'							=> trim($guardado->METODO_PAGO),
			'metodo_pago_seleccionar'				=> trim($guardado->METODO_PAGO_SELECCIONAR),
			'idioma' 								=> trim($guardado->IDIOMA),
			'uso_cfdi'								=> trim($guardado->USO_CFDI),
			'uso_cfdi_seleccionar'					=> trim($guardado->USO_CFDI_SELECCIONAR),
			'clave_servicio'						=> trim($guardado->CLAVE_SERVICIO),
			'clave_unidad'							=> trim($guardado->CLAVE_UNIDAD),
			'unidad_medida'							=> trim($guardado->UNIDAD_MEDIDA),
			'regimen_fiscal'						=> trim($guardado->REGIMEN_FISCAL),
			'clave_producto'						=> trim($guardado->CLAVE_PRODUCTO),
			'clave_confirmacion'					=> trim($guardado->CLAVE_CONFIRMACION),
			'numero_pedimento'						=> trim($guardado->NUMERO_PEDIMENTO),
			'moneda'								=> trim($guardado->MONEDA),
			'tipo_cambio'							=> trim($guardado->TIPO_CAMBIO),
			'observacion'							=> trim($guardado->OBSERVACION),
			'precision_decimal'						=> trim($guardado->PRECISION_DECIMAL),
			'pedido_mes_actual'						=> trim($guardado->PEDIDO_MES_ACTUAL),
			'metodo_pago33'							=> trim($guardado->METODO_PAGO33),
			'metodo_pago_seleccionar33'				=> trim($guardado->METODO_PAGO_SELECCIONAR33),
			'conceptos_especiales_envio'			=> trim($guardado->CONCEPTOS_ESPECIALES_ENVIO),
			'manejo_impuestos_pedido'				=> trim($guardado->MANEJO_IMPUESTOS_PEDIDO),
			'color_boton_vistaprevia'       		=> $color_boton_vistaprevia,
			'color_texto_boton_vistaprevia'       	=> $color_texto_boton_vistaprevia,
			'color_boton_generarcfdi'       		=> $color_boton_generarcfdi,
			'color_texto_boton_generarcfdi'       	=> $color_texto_boton_generarcfdi,
			'clave_servicio_shipping'				=> trim($guardado->CLAVE_SERVICIO_SHIPPING),
			'clave_unidad_shipping'					=> trim($guardado->CLAVE_UNIDAD_SHIPPING),
			'unidad_medida_shipping'				=> trim($guardado->UNIDAD_MEDIDA_SHIPPING),
			'clave_producto_shipping'				=> trim($guardado->CLAVE_PRODUCTO_SHIPPING),
			'numero_pedimento_shipping'				=> trim($guardado->NUMERO_PEDIMENTO_SHIPPING),
			'config_principal_shipping'				=> trim($guardado->CONFIG_PRINCIPAL_SHIPPING),
			'huso_horario'							=> trim($guardado->HUSO_HORARIO),
			'domicilio_receptor'					=> trim($guardado->DOMICILIO_RECEPTOR),
			'mostrarMensajeErrorCliente'			=> trim($guardado->MOSTRAR_MENSAJE_ERROR_CLIENTE),
			'mensajeErrorCliente'					=> trim($guardado->MENSAJE_ERROR_CLIENTE),
			'complementoCFDI'						=> trim($guardado->COMPLEMENTO_CFDI),
			'complementoCFDI_iedu_configuracion_nivel' => trim($guardado->COMPLEMENTO_CFDI_IEDU_CONFIGURACION_NIVEL),
			'complementoCFDI_iedu_configuracion_autRVOE' => trim($guardado->COMPLEMENTO_CFDI_IEDU_CONFIGURACION_AUTRVOE),
			'manejo_impuestos_pedido_facturaGlobal' => trim($guardado->MANEJO_IMPUESTOS_PEDIDO_FACTURAGLOBAL),
			'manejo_impuestos_pedido_facturaGlobal_texto' => trim($guardado->MANEJO_IMPUESTOS_PEDIDO_FACTURAGLOBAL_TEXTO),
			'exportacion_cfdi' 						=> trim($guardado->EXPORTACION_CFDI),
			'facAtrAdquirente' 						=> trim($guardado->FACATRADQUIRIENTE),
			'objeto_imp_producto' 					=> trim($guardado->OBJETO_IMP_PRODUCTO),
			'objeto_imp_shipping' 					=> trim($guardado->OBJETO_IMP_SHIPPING),
			'estado_orden_cfdi_automatico' 			=> trim($guardado->ESTADO_ORDEN_CFDI_AUTOMATICO),
			'notificar_error_cfdi_automatico' 		=> trim($guardado->NOTIFICAR_ERROR_CFDI_AUTOMATICO),
			'informacionGlobal_periodicidad' 		=> trim($guardado->INFORMACIONGLOBAL_PERIODICIDAD),
			'informacionGlobal_meses' 				=> trim($guardado->INFORMACIONGLOBAL_MESES),
			'informacionGlobal_año' 				=> trim($guardado->INFORMACIONGLOBAL_AÑO)
		);

		$guardadoConfiguracion = RealVirtualWooCommerceConfiguracion::guardarConfiguracionLocal($configuracion, $idiomaRVLFECFDI);
	}
	
	if($guardado->URL != '')
	{
		$configuracionIntegracion = array
		(
			'ci_consultarPedidos_tipo_conexion'       					=> trim($guardado->TIPO_CONEXION),
			'ci_consultarPedidos_tipo_solicitud'       					=> trim($guardado->TIPO_SOLICITUD),
			'ci_consultarPedidos_url'       							=> trim($guardado->URL),
			'ci_consultarPedidos_nombre_parametro_numeropedido'   		=> trim($guardado->NOMBRE_PARAMETRO_NUMEROPEDIDO),
			'ci_consultarPedidos_nombre_parametro_monto'      			=> trim($guardado->NOMBRE_PARAMETRO_MONTO),
			'ci_consultarPedidos_parametro_extra1_tipo'       			=> trim($guardado->PARAMETRO_EXTRA1_TIPO),
			'ci_consultarPedidos_parametro_extra1_nombrevisual'      	=> trim($guardado->PARAMETRO_EXTRA1_NOMBREVISUAL),
			'ci_consultarPedidos_parametro_extra1_nombreinterno'       	=> trim($guardado->PARAMETRO_EXTRA1_NOMBREINTERNO),
			'ci_consultarPedidos_parametro_extra1_estado'      			=> trim($guardado->PARAMETRO_EXTRA1_ESTADO),
			'ci_consultarPedidos_parametro_extra2_tipo'       			=> trim($guardado->PARAMETRO_EXTRA2_TIPO),
			'ci_consultarPedidos_parametro_extra2_nombrevisual'       	=> trim($guardado->PARAMETRO_EXTRA2_NOMBREVISUAL),
			'ci_consultarPedidos_parametro_extra2_nombreinterno'       	=> trim($guardado->PARAMETRO_EXTRA2_NOMBREINTERNO),
			'ci_consultarPedidos_parametro_extra2_estado'				=> trim($guardado->PARAMETRO_EXTRA2_ESTADO),
			'ci_consultarPedidos_tipo_consulta'							=> trim($guardado->TIPO_CONSULTA),
			'ci_enviarPedidos_tipo_conexion'							=> trim($guardado->ENVIARPEDIDOS_TIPO_CONEXION),
			'ci_enviarPedidos_tipo_solicitud'							=> trim($guardado->ENVIARPEDIDOS_TIPO_SOLICITUD),
			'ci_enviarPedidos_url'										=> trim($guardado->ENVIARPEDIDOS_URL),
			'ci_enviarPedidos_tipo_conexion2'							=> trim($guardado->ENVIARPEDIDOS_TIPO_CONEXION2),
			'ci_enviarPedidos_tipo_solicitud2'							=> trim($guardado->ENVIARPEDIDOS_TIPO_SOLICITUD2),
			'ci_enviarPedidos_url2'										=> trim($guardado->ENVIARPEDIDOS_URL2),
			'ci_enviarPedidos_tipo_consulta'							=> trim($guardado->ENVIARPEDIDOS_TIPO_CONSULTA),
			'ci_enviarXml_tipo_conexion'								=> trim($guardado->ENVIARXML_TIPO_CONEXION),
			'ci_enviarXml_tipo_solicitud'								=> trim($guardado->ENVIARXML_TIPO_SOLICITUD),
			'ci_enviarXml_url'											=> trim($guardado->ENVIARXML_URL),
			'ci_enviarXml_tipo_conexion2'								=> trim($guardado->ENVIARXML_TIPO_CONEXION2),
			'ci_enviarXml_tipo_solicitud2'								=> trim($guardado->ENVIARXML_TIPO_SOLICITUD2),
			'ci_enviarXml_url2'											=> trim($guardado->ENVIARXML_URL2),
			'ci_enviarXml_tipo_consulta'								=> trim($guardado->ENVIARXML_TIPO_CONSULTA),
			'ci_enviarPedidosCrear_tipo_conexion'						=> trim($guardado->ENVIARPEDIDOSCREAR_TIPO_CONEXION),
			'ci_enviarPedidosCrear_tipo_solicitud'						=> trim($guardado->ENVIARPEDIDOSCREAR_TIPO_SOLICITUD),
			'ci_enviarPedidosCrear_url'									=> trim($guardado->ENVIARPEDIDOSCREAR_URL),
			'ci_enviarPedidosCrear_tipo_conexion2'						=> trim($guardado->ENVIARPEDIDOSCREAR_TIPO_CONEXION2),
			'ci_enviarPedidosCrear_tipo_solicitud2'						=> trim($guardado->ENVIARPEDIDOSCREAR_TIPO_SOLICITUD2),
			'ci_enviarPedidosCrear_url2'								=> trim($guardado->ENVIARPEDIDOSCREAR_URL2),
			'ci_enviarPedidosCrear_tipo_consulta'						=> trim($guardado->ENVIARPEDIDOSCREAR_TIPO_CONSULTA)
		);

		$guardadoIntegracion = RealVirtualWooCommerceCentroIntegracion::guardarConfiguracionLocal($configuracionIntegracion, $idiomaRVLFECFDI);
	}
	
	if($guardado->FacturaGlobal != '')
	{
		$configuracionComplementos = array
		(
			'facturaGlobal' 					=> trim($guardado->FacturaGlobal),
			'facturacionDashboard' 				=> trim($guardado->FacturacionPedidosInterno),
			'wsObtenerPedidosExternos' 			=> trim($guardado->WSRecuperarPedidos),
			'wsEnviarPedidosEstado' 			=> trim($guardado->WSEnviarPedidoEstado),
			'wsEnviarPedidosCreado' 			=> trim($guardado->WSEnviarPedidoCreado),
			'wsEnviarXMLTimbrado' 				=> trim($guardado->WSEnviarXMLPedido),
			'emisionCFDIAutomatico' 			=> trim($guardado->FacturacionAutomaticaPedidos)
		);

		$guardadoComplementos = RealVirtualWooCommerceComplementos::guardarConfiguracionLocal($configuracionComplementos, $idiomaRVLFECFDI);
	}
	
	//Creación de la base de datos
	creacion_base_datos();
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_guardar_configuracion_general', 'realvirtual_woocommerce_guardar_configuracion_general_callback');
add_action('wp_ajax_realvirtual_woocommerce_guardar_configuracion_productos', 'realvirtual_woocommerce_guardar_configuracion_productos_callback');
add_action('wp_ajax_realvirtual_woocommerce_guardar_configuracion_envios', 'realvirtual_woocommerce_guardar_configuracion_envios_callback');
add_action('wp_ajax_realvirtual_woocommerce_guardar_configuracion_reglasModuloClientes', 'realvirtual_woocommerce_guardar_configuracion_reglasModuloClientes_callback');
add_action('wp_ajax_realvirtual_woocommerce_guardar_configuracion_estiloModuloClientes', 'realvirtual_woocommerce_guardar_configuracion_estiloModuloClientes_callback');
add_action('wp_ajax_realvirtual_woocommerce_guardar_configuracion_ajustesAvanzados', 'realvirtual_woocommerce_guardar_configuracion_ajustesAvanzados_callback');
add_action('wp_ajax_realvirtual_woocommerce_guardar_configuracion_idioma', 'realvirtual_woocommerce_guardar_configuracion_idioma_callback');

function realvirtual_woocommerce_guardar_configuracion_general_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;

	$serie = sanitize_text_field($_POST['serie']);
	update_post_meta($post->ID, 'serie', $serie);
	
	$version_cfdi = sanitize_text_field($_POST['version_cfdi']);
	update_post_meta($post->ID, 'version_cfdi', $version_cfdi);
	
	$regimen_fiscal = sanitize_text_field($_POST['regimen_fiscal']);
	update_post_meta($post->ID, 'regimen_fiscal', $regimen_fiscal);
	
	$clave_confirmacion = sanitize_text_field($_POST['clave_confirmacion']);
	update_post_meta($post->ID, 'clave_confirmacion', $clave_confirmacion);
	
	$moneda = sanitize_text_field($_POST['moneda']);
	update_post_meta($post->ID, 'moneda', $moneda);
	
	$tipo_cambio = sanitize_text_field($_POST['tipo_cambio']);
	update_post_meta($post->ID, 'tipo_cambio', $tipo_cambio);
	
	$observacion = sanitize_text_field($_POST['observacion']);
	update_post_meta($post->ID, 'observacion', $observacion);
	
	$precision_decimal = sanitize_text_field($_POST['precision_decimal']);
	update_post_meta($post->ID, 'precision_decimal', $precision_decimal);
	
	$huso_horario = sanitize_text_field($_POST['huso_horario']);
	update_post_meta($post->ID, 'huso_horario', $huso_horario);
	
	$domicilio_receptor = sanitize_text_field($_POST['domicilio_receptor']);
	update_post_meta($post->ID, 'domicilio_receptor', $domicilio_receptor);
	
	$exportacion_cfdi = sanitize_text_field($_POST['exportacion_cfdi']);
	update_post_meta($post->ID, 'exportacion_cfdi', $exportacion_cfdi);
	
	$facAtrAdquirente = sanitize_text_field($_POST['facAtrAdquirente']);
	update_post_meta($post->ID, 'facAtrAdquirente', $facAtrAdquirente);
	
	$informacionGlobal_periodicidad = sanitize_text_field($_POST['informacionGlobal_periodicidad']);
	update_post_meta($post->ID, 'informacionGlobal_periodicidad', $informacionGlobal_periodicidad);
	
	$informacionGlobal_meses = sanitize_text_field($_POST['informacionGlobal_meses']);
	update_post_meta($post->ID, 'informacionGlobal_meses', $informacionGlobal_meses);
	
	$informacionGlobal_año = sanitize_text_field($_POST['informacionGlobal_año']);
	update_post_meta($post->ID, 'informacionGlobal_año', $informacionGlobal_año);
	
	if(!preg_match("/^[a-zA-Z0-9\s\#\$\+\%\(\)\[\]\*\¡\!\=\\/\&\.\,\;\:\-\_\ñ\á\é\í\ó\ú\Á\É\Í\Ó\Ú\Ñ]*$/", $_POST['serie']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La serie tiene un formato inválido.':'The serie has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($version_cfdi == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona una versión de CFDI.':'Select a CFDI version.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($regimen_fiscal == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona el régimen fiscal por defecto.':'Select the default fiscal regime.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($moneda == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona la moneda por defecto.':'Select the default currency.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($tipo_cambio == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ingresa el tipo de cambio por defecto.':'Enter the default exchange rate.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($precision_decimal == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona la precisión decimal.':'Select the decimal precision.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(is_numeric($tipo_cambio) == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El tipo de cambio debe ser un valor numérico.':'The exchange rate must be a numeric value.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(is_numeric($precision_decimal) == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La precisión decimal debe ser un valor numérico.':'The decimal precision must be a numeric value.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($huso_horario == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Zona horaria</b>." : "Select an option for <b>Time zone</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($domicilio_receptor == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Mostrar la dirección del cliente en la facturación</b>." : "Select an option for <b>Show customer's address on billing</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($domicilio_receptor == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Mostrar la dirección del cliente en la facturación</b>." : "Select an option for <b>Show customer's address on billing</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($informacionGlobal_periodicidad == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Periodicidad</b>." : "Select an option for <b>Periodicity</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($informacionGlobal_meses == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Meses</b>." : "Select an option for <b>Months</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($informacionGlobal_año == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Año</b>." : "Select an option for <b>Year</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($informacionGlobal_periodicidad == '05' && $regimen_fiscal != '621')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Cuando el valor del campo <b>Periodicidad</b> es <b>05 - Bimestral</b> el valor del campo <b>Régimen fiscal del Emisor</b> debe ser <b>621 - Incorporación Fiscal</b>." : "When the value of the <b>Periodicity</b> field is <b>05 - Bimestral</b>, the value of the <b>Issuer fiscal regime</b> field must be <b>621 - Incorporación Fiscal</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($informacionGlobal_periodicidad == '05' && $informacionGlobal_meses != '13' && $informacionGlobal_meses != '14' && $informacionGlobal_meses != '15' && $informacionGlobal_meses != '16'
				&& $informacionGlobal_meses != '17' && $informacionGlobal_meses != '18')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Cuando el valor del campo <b>Periodicidad</b> es <b>05 - Bimestral</b> el valor del campo <b>Meses</b> debe ser <b>13 - Enero-Febrero</b>, <b>14 - Marzo-Abril</b>, <b>15 - Mayo-Junio</b>, <b>16 - Julio-Agosto</b>, <b>17 - Septiembre-Octubre</b> o <b>18 - Noviembre-Diciembre</b>." : "When the value of the <b>Periodicity</b> field is <b>05 - Bimestral</b>, the value of the <b>Months</b> field must be <b>13 - Enero-Febrero</b>, <b>14 - Marzo-Abril</b>, <b>15 - Mayo-Junio</b>, <b>16 - Julio-Agosto</b>, <b>17 - Septiembre-Octubre</b> o <b>18 - Noviembre-Diciembre</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($informacionGlobal_periodicidad == '05' && ($informacionGlobal_meses == '13' || $informacionGlobal_meses == '14' || $informacionGlobal_meses == '15' || $informacionGlobal_meses == '16'
				|| $informacionGlobal_meses == '17' || $informacionGlobal_meses == '18'))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Cuando el valor del campo <b>Periodicidad</b> es diferente de <b>05 - Bimestral</b> el valor del campo <b>Meses</b> no puede ser <b>13 - Enero-Febrero</b>, <b>14 - Marzo-Abril</b>, <b>15 - Mayo-Junio</b>, <b>16 - Julio-Agosto</b>, <b>17 - Septiembre-Octubre</b> o <b>18 - Noviembre-Diciembre</b>." : "When the value of the <b>Periodicity</b> field is different from <b>05 - Bimestral</b>, the value of the <b>Months</b> field cannot be <b>13 - Enero-Febrero</b>, <b>14 - Marzo-Abril</b>, <b>15 - Mayo-Junio</b>, <b>16 - Julio-Agosto</b>, <b>17 - Septiembre-Octubre</b> o <b>18 - Noviembre-Diciembre</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $configuracion = array
	(
		'serie'       							=> $_POST['serie'],
		'version_cfdi'							=> $_POST['version_cfdi'],
		'regimen_fiscal'						=> $_POST['regimen_fiscal'],
		'moneda'								=> $_POST['moneda'],
		'tipo_cambio'							=> $_POST['tipo_cambio'],
		'observacion'							=> $_POST['observacion'],
		'precision_decimal'						=> $_POST['precision_decimal'],
		'huso_horario'							=> $_POST['huso_horario'],
		'domicilio_receptor'					=> $_POST['domicilio_receptor'],
		'exportacion_cfdi'						=> $_POST['exportacion_cfdi'],
		'facAtrAdquirente'						=> $_POST['facAtrAdquirente'],
		'informacionGlobal_periodicidad'		=> $_POST['informacionGlobal_periodicidad'],
		'informacionGlobal_meses'				=> $_POST['informacionGlobal_meses'],
		'informacionGlobal_año'					=> $_POST['informacionGlobal_año']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceConfiguracion::guardarConfiguracionGeneral($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function realvirtual_woocommerce_guardar_configuracion_productos_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;

	$clave_servicio = sanitize_text_field($_POST['clave_servicio']);
	update_post_meta($post->ID, 'clave_servicio', $clave_servicio);
	
	$clave_unidad = sanitize_text_field($_POST['clave_unidad']);
	update_post_meta($post->ID, 'clave_unidad', $clave_unidad);
	
	$unidad_medida = sanitize_text_field($_POST['unidad_medida']);
	update_post_meta($post->ID, 'unidad_medida', $unidad_medida);
	
	$clave_producto = sanitize_text_field($_POST['clave_producto']);
	update_post_meta($post->ID, 'clave_producto', $clave_producto);
	
	$numero_pedimento = sanitize_text_field($_POST['numero_pedimento']);
	update_post_meta($post->ID, 'numero_pedimento', $numero_pedimento);
	
	$objeto_imp_producto = sanitize_text_field($_POST['objeto_imp_producto']);
	update_post_meta($post->ID, 'objeto_imp_producto', $objeto_imp_producto);
	
	if($clave_servicio == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ingresa la clave servicio por defecto.':'Enter the default service code.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($clave_unidad == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ingresa la clave unidad por defecto.':'Enter the default unit code.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($numero_pedimento != '')
	{
		if(!preg_match("/[0-9]{2}  [0-9]{2}  [0-9]{4}  [0-9]{7}/i", $_POST['numero_pedimento']))
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? 'El No. Pedimento tiene un formato inválido.<br/><br/>El formato válido es <b>00  00  0000  0000000</b>, el cual se explica a continuación: Últimos 2 dígitos del año de validación seguidos por dos espacios, 2 dígitos  de  la  aduana  de  despacho  seguidos  por  dos  espacios,  4 dígitos del número de la patente seguidos por dos espacios, 1 dígito que corresponde al último dígito del año en curso, salvo que se trate de un pedimento consolidado, iniciado en el año inmediato anterior o del pedimento original de unarectificación, seguido de 6 dígitos de la numeración progresiva por aduana.':'The No. Requirement has an invalid format.<br/><br/>The valid format is <b>0000 0000 0000000</b>, which is explained below: Last 2 digits of the validation year followed by two 2 digits of the office of despatch followed by two spaces, 4 digits of the patent number followed by two spaces, 1 digit corresponding to the last digit of the current year, unless it is a consolidated request, initiated in the immediately preceding year or the original request for unarectification, followed by 6 digits of progressive numbering by customs.'
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
	}
	
    $configuracion = array
	(
		'clave_servicio'						=> $_POST['clave_servicio'],
		'clave_unidad'							=> $_POST['clave_unidad'],
		'unidad_medida'							=> $_POST['unidad_medida'],
		'clave_producto'						=> $_POST['clave_producto'],
		'numero_pedimento'						=> $_POST['numero_pedimento'],
		'objeto_imp_producto'					=> $_POST['objeto_imp_producto']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceConfiguracion::guardarConfiguracionProductos($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function realvirtual_woocommerce_guardar_configuracion_envios_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;

	$clave_servicio_shipping = sanitize_text_field($_POST['clave_servicio_shipping']);
	update_post_meta($post->ID, 'clave_servicio_shipping', $clave_servicio_shipping);
	
	$clave_unidad_shipping = sanitize_text_field($_POST['clave_unidad_shipping']);
	update_post_meta($post->ID, 'clave_unidad_shipping', $clave_unidad_shipping);
	
	$unidad_medida_shipping = sanitize_text_field($_POST['unidad_medida_shipping']);
	update_post_meta($post->ID, 'unidad_medida_shipping', $unidad_medida_shipping);
	
	$clave_producto_shipping = sanitize_text_field($_POST['clave_producto_shipping']);
	update_post_meta($post->ID, 'clave_producto_shipping', $clave_producto_shipping);
	
	$numero_pedimento_shipping = sanitize_text_field($_POST['numero_pedimento_shipping']);
	update_post_meta($post->ID, 'numero_pedimento_shipping', $numero_pedimento_shipping);
	
	$config_principal_shipping = sanitize_text_field($_POST['config_principal_shipping']);
	update_post_meta($post->ID, 'config_principal_shipping', $config_principal_shipping);
	
	$objeto_imp_shipping = sanitize_text_field($_POST['objeto_imp_shipping']);
	update_post_meta($post->ID, 'objeto_imp_shipping', $objeto_imp_shipping);
	
	if($config_principal_shipping == '1')
	{
		if($clave_servicio_shipping == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ingresa la clave servicio del concepto de envío (shipping) por defecto.':'Enter the default service code for the shipping concept.'
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($clave_unidad_shipping == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ingresa la clave unidad del concepto de envío (shipping) por defecto.':'Enter the default unit code for the shipping concept.'
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($numero_pedimento_shipping != '')
		{
			if(!preg_match("/[0-9]{2}  [0-9]{2}  [0-9]{4}  [0-9]{7}/i", $_POST['numero_pedimento_shipping']))
			{
				$respuesta = array
				(
					'success' => false,
					'message' => ($idiomaRVLFECFDI == 'ES') ? 'El No. Pedimento del concepto de envío (shipping) tiene un formato inválido.<br/><br/>El formato válido es <b>00  00  0000  0000000</b>, el cual se explica a continuación: Últimos 2 dígitos del año de validación seguidos por dos espacios, 2 dígitos  de  la  aduana  de  despacho  seguidos  por  dos  espacios,  4 dígitos del número de la patente seguidos por dos espacios, 1 dígito que corresponde al último dígito del año en curso, salvo que se trate de un pedimento consolidado, iniciado en el año inmediato anterior o del pedimento original de unarectificación, seguido de 6 dígitos de la numeración progresiva por aduana.':'The No. Requirement shipping concept has an invalid format.<br/><br/>The valid format is <b>0000 0000 0000000</b>, which is explained below: Last 2 digits of the validation year followed by two 2 digits of the office of despatch followed by two spaces, 4 digits of the patent number followed by two spaces, 1 digit corresponding to the last digit of the current year, unless it is a consolidated request, initiated in the immediately preceding year or the original request for unarectification, followed by 6 digits of progressive numbering by customs.'
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
		}
	}
	
    $configuracion = array
	(
		'clave_servicio_shipping'				=> $_POST['clave_servicio_shipping'],
		'clave_unidad_shipping'					=> $_POST['clave_unidad_shipping'],
		'unidad_medida_shipping'				=> $_POST['unidad_medida_shipping'],
		'clave_producto_shipping'				=> $_POST['clave_producto_shipping'],
		'numero_pedimento_shipping'				=> $_POST['numero_pedimento_shipping'],
		'config_principal_shipping'				=> $_POST['config_principal_shipping'],
		'objeto_imp_shipping'					=> $_POST['objeto_imp_shipping']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceConfiguracion::guardarConfiguracionEnvios($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function realvirtual_woocommerce_guardar_configuracion_reglasModuloClientes_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;

	$estado_orden = sanitize_text_field($_POST['estado_orden']);
	update_post_meta($post->ID, 'estado_orden', $estado_orden);
	
	$titulo = sanitize_text_field($_POST['titulo']);
	update_post_meta($post->ID, 'titulo', $titulo);
	
	$descripcion = sanitize_text_field($_POST['descripcion']);
	update_post_meta($post->ID, 'descripcion', $descripcion);
	
	$estado_orden_refacturacion = sanitize_text_field($_POST['estado_orden_refacturacion']);
	update_post_meta($post->ID, 'estado_orden_refacturacion', $estado_orden_refacturacion);
	
	$uso_cfdi = sanitize_text_field($_POST['uso_cfdi']);
	update_post_meta($post->ID, 'uso_cfdi', $uso_cfdi);
	
	$uso_cfdi_seleccionar = sanitize_text_field($_POST['uso_cfdi_seleccionar']);
	update_post_meta($post->ID, 'uso_cfdi_seleccionar', $uso_cfdi_seleccionar);
	
	$metodo_pago = sanitize_text_field($_POST['metodo_pago']);
	update_post_meta($post->ID, 'metodo_pago', $metodo_pago);
	
	$metodo_pago_seleccionar = sanitize_text_field($_POST['metodo_pago_seleccionar']);
	update_post_meta($post->ID, 'metodo_pago_seleccionar', $metodo_pago_seleccionar);
	
	$metodo_pago33 = sanitize_text_field($_POST['metodo_pago33']);
	update_post_meta($post->ID, 'metodo_pago33', $metodo_pago33);
	
	$metodo_pago_seleccionar33 = sanitize_text_field($_POST['metodo_pago_seleccionar33']);
	update_post_meta($post->ID, 'metodo_pago_seleccionar33', $metodo_pago_seleccionar33);
	
	$conceptos_especiales_envio = sanitize_text_field($_POST['conceptos_especiales_envio']);
	update_post_meta($post->ID, 'conceptos_especiales_envio', $conceptos_especiales_envio);
	
	$pedido_mes_actual = sanitize_text_field($_POST['pedido_mes_actual']);
	update_post_meta($post->ID, 'pedido_mes_actual', $pedido_mes_actual);
	
	$mostrarMensajeErrorCliente = sanitize_text_field($_POST['mostrarMensajeErrorCliente']);
	update_post_meta($post->ID, 'mostrarMensajeErrorCliente', $mostrarMensajeErrorCliente);
	
	$mensajeErrorCliente = sanitize_text_field($_POST['mensajeErrorCliente']);
	update_post_meta($post->ID, 'mensajeErrorCliente', $mensajeErrorCliente);
	
	$emailNotificacionErrorModuloClientes = sanitize_text_field($_POST['emailNotificacionErrorModuloClientes']);
	update_post_meta($post->ID, 'emailNotificacionErrorModuloClientes', $emailNotificacionErrorModuloClientes);
	
	if(!preg_match("/^[a-zA-Z0-9\s\#\$\+\%\(\)\[\]\*\¡\!\=\\/\&\.\,\;\:\-\_\ñ\á\é\í\ó\ú\Á\É\Í\Ó\Ú\Ñ]*$/", $_POST['estado_orden']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El estado del pedido para permitir facturación tiene un formato inválido.':'The order status to allow CFDI issue has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/^[a-zA-Z0-9\s\#\$\+\%\(\)\[\]\*\¡\!\=\\/\&\.\,\;\:\-\_\ñ\á\é\í\ó\ú\Á\É\Í\Ó\Ú\Ñ]*$/", $_POST['titulo']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El título tiene un formato inválido.':'The title has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/^[a-zA-Z0-9\s\#\$\+\%\(\)\[\]\*\¡\!\=\\/\&\.\,\;\:\-\_\ñ\á\é\í\ó\ú\Á\É\Í\Ó\Ú\Ñ]*$/", $_POST['descripcion']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El texto descriptivo tiene un formato inválido.':'The descriptive text has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/^[a-zA-Z0-9\s\#\$\+\%\(\)\[\]\*\¡\!\=\\/\&\.\,\;\:\-\_\ñ\á\é\í\ó\ú\Á\É\Í\Ó\Ú\Ñ]*$/", $_POST['estado_orden_refacturacion']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El estado del pedido para permitir refacturación tras cancelación tiene un formato inválido.':'The order status to allow CFDI issue after cancellation has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($metodo_pago == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona una forma de pago por defecto.':'Select a default payment way.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($metodo_pago_seleccionar == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Establece si el cliente podrá o no seleccionar la forma de pago.':'Sets whether or not the customer can select the payment way.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($metodo_pago33 == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona una forma de pago por defecto.':'Select a default payment way.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($metodo_pago_seleccionar33 == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Establece si el cliente podrá o no seleccionar la forma de pago.':'Sets whether or not the customer can select the payment way.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($conceptos_especiales_envio == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona el comportamiento de los conceptos especiales.':'Select the behavior of special concepts.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($pedido_mes_actual == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Establece si el cliente podrá o no emitir CFDI de un pedido completado que no corresponde al mes actual.':'It establishes whether or not the client can issue CFDI of a completed order that does not correspond to the current month.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($uso_cfdi == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona un uso CFDI por defecto.':'Select a default CFDI use.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($uso_cfdi_seleccionar == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Establece si el cliente podrá o no seleccionar el uso CFDI.':'Sets wheter or not the customer can select the CFDI use.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($mostrarMensajeErrorCliente == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Mostrar mensaje personalizado al cliente...</b>." : "Select an option for <b>Show custom message to customer...</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($mostrarMensajeErrorCliente == 'si')
	{
		if($mensajeErrorCliente == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Ingresa un texto para <b>Mensaje personalizado para el cliente...</b>." : "Enter text for <b>Personalized message for the customer...</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($emailNotificacionErrorModuloClientes == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Ingrese un correo electrónico para el campo <b>E-mail a donde se enviará el error...</b>." : "Enter an email for the <b>Email to which the error will be sent...</b> field."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
	}
	
    $configuracion = array
	(
		'estado_orden'       					=> $_POST['estado_orden'],
		'titulo'       							=> $_POST['titulo'],
		'descripcion'   						=> $_POST['descripcion'],
		'estado_orden_refacturacion'			=> $_POST['estado_orden_refacturacion'],
		'metodo_pago'							=> $_POST['metodo_pago'],
		'metodo_pago_seleccionar'				=> $_POST['metodo_pago_seleccionar'],
		'metodo_pago33'							=> $_POST['metodo_pago33'],
		'metodo_pago_seleccionar33'				=> $_POST['metodo_pago_seleccionar33'],
		'uso_cfdi'								=> $_POST['uso_cfdi'],
		'uso_cfdi_seleccionar'					=> $_POST['uso_cfdi_seleccionar'],
		'pedido_mes_actual'						=> $_POST['pedido_mes_actual'],
		'conceptos_especiales_envio'			=> $_POST['conceptos_especiales_envio'],
		'mostrarMensajeErrorCliente'			=> $_POST['mostrarMensajeErrorCliente'],
		'mensajeErrorCliente'					=> $_POST['mensajeErrorCliente'],
		'emailNotificacionErrorModuloClientes'	=> $_POST['emailNotificacionErrorModuloClientes']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceConfiguracion::guardarConfiguracionReglasModuloClientes($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function realvirtual_woocommerce_guardar_configuracion_estiloModuloClientes_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;

	$color_fondo_encabezado_hexadecimal = sanitize_text_field($_POST['color_fondo_encabezado_hexadecimal']);
	update_post_meta($post->ID, 'color_fondo_encabezado_hexadecimal', $color_fondo_encabezado_hexadecimal);
	
	$color_texto_encabezado_hexadecimal = sanitize_text_field($_POST['color_texto_encabezado_hexadecimal']);
	update_post_meta($post->ID, 'color_texto_encabezado_hexadecimal', $color_texto_encabezado_hexadecimal);
	
	$color_fondo_formulario_hexadecimal = sanitize_text_field($_POST['color_fondo_formulario_hexadecimal']);
	update_post_meta($post->ID, 'color_fondo_formulario_hexadecimal', $color_fondo_formulario_hexadecimal);
	
	$color_texto_formulario_hexadecimal = sanitize_text_field($_POST['color_texto_formulario_hexadecimal']);
	update_post_meta($post->ID, 'color_texto_formulario_hexadecimal', $color_texto_formulario_hexadecimal);
	
	$color_texto_controles_formulario_hexadecimal = sanitize_text_field($_POST['color_texto_controles_formulario_hexadecimal']);
	update_post_meta($post->ID, 'color_texto_controles_formulario_hexadecimal', $color_texto_controles_formulario_hexadecimal);
	
	$color_boton_hexadecimal = sanitize_text_field($_POST['color_boton_hexadecimal']);
	update_post_meta($post->ID, 'color_boton_hexadecimal', $color_boton_hexadecimal);
	
	$color_texto_boton_hexadecimal = sanitize_text_field($_POST['color_texto_boton_hexadecimal']);
	update_post_meta($post->ID, 'color_texto_boton_hexadecimal', $color_texto_boton_hexadecimal);
	
	$color_boton_hexadecimal_vistaprevia = sanitize_text_field($_POST['color_boton_hexadecimal_vistaprevia']);
	update_post_meta($post->ID, 'color_boton_hexadecimal_vistaprevia', $color_boton_hexadecimal_vistaprevia);
	
	$color_texto_boton_hexadecimal_vistaprevia = sanitize_text_field($_POST['color_texto_boton_hexadecimal_vistaprevia']);
	update_post_meta($post->ID, 'color_texto_boton_hexadecimal_vistaprevia', $color_texto_boton_hexadecimal_vistaprevia);
	
	$color_boton_hexadecimal_generarcfdi = sanitize_text_field($_POST['color_boton_hexadecimal_generarcfdi']);
	update_post_meta($post->ID, 'color_boton_hexadecimal_generarcfdi', $color_boton_hexadecimal_generarcfdi);
	
	$color_texto_boton_hexadecimal_generarcfdi = sanitize_text_field($_POST['color_texto_boton_hexadecimal_generarcfdi']);
	update_post_meta($post->ID, 'color_texto_boton_hexadecimal_generarcfdi', $color_texto_boton_hexadecimal_generarcfdi);
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_fondo_encabezado_hexadecimal']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color de fondo en encabezado" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Header background color" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_texto_encabezado_hexadecimal']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color de texto en encabezado" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Header text color" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_fondo_formulario_hexadecimal']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color de fondo en formulario" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Form background color" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_texto_formulario_hexadecimal']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color de texto en formulario" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Form text color" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_texto_controles_formulario_hexadecimal']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color de texto en campos del formulario" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Text color in form fields" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_boton_hexadecimal']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color de botones" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Button color" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_texto_boton_hexadecimal']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color de texto en botones" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Text color on buttons" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_boton_hexadecimal_vistaprevia']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color del botón Descargar Vista Previa" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Download Preview button color" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_texto_boton_hexadecimal_vistaprevia']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color de texto del botón Descargar Vista Previa" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Download Preview button text color" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_boton_hexadecimal_generarcfdi']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color del botón Generar CFDI" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Generate CFDI button color" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/(^#[0-9A-F]{6}$)/i", $_POST['color_texto_boton_hexadecimal_generarcfdi']))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El valor del campo "Color de texto del botón Generar CFDI" tiene un formato inválido. Por favor, ingresa un valor hexadecimal que represente el color deseado.':'The value of the "Generate CFDI button text color" field has an invalid format. Plase, enter a hexadecimal value representing the desired color.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $configuracion = array
	(
		'color_fondo_encabezado'      			=> $_POST['color_fondo_encabezado_hexadecimal'],
		'color_texto_encabezado'       			=> $_POST['color_texto_encabezado_hexadecimal'],
		'color_fondo_formulario'      			=> $_POST['color_fondo_formulario_hexadecimal'],
		'color_texto_formulario'       			=> $_POST['color_texto_formulario_hexadecimal'],
		'color_texto_controles_formulario'      => $_POST['color_texto_controles_formulario_hexadecimal'],
		'color_boton'       					=> $_POST['color_boton_hexadecimal'],
		'color_texto_boton'       				=> $_POST['color_texto_boton_hexadecimal'],
		'color_boton_vistaprevia'       		=> $_POST['color_boton_hexadecimal_vistaprevia'],
		'color_texto_boton_vistaprevia'       	=> $_POST['color_texto_boton_hexadecimal_vistaprevia'],
		'color_boton_generarcfdi'       		=> $_POST['color_boton_hexadecimal_generarcfdi'],
		'color_texto_boton_generarcfdi'       	=> $_POST['color_texto_boton_hexadecimal_generarcfdi']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceConfiguracion::guardarConfiguracionEstiloModuloClientes($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function realvirtual_woocommerce_guardar_configuracion_ajustesAvanzados_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;

	$manejo_impuestos_pedido = sanitize_text_field($_POST['manejo_impuestos_pedido']);
	update_post_meta($post->ID, 'manejo_impuestos_pedido', $manejo_impuestos_pedido);
	
	$manejo_impuestos_pedido_facturaGlobal = sanitize_text_field($_POST['manejo_impuestos_pedido_facturaGlobal']);
	update_post_meta($post->ID, 'manejo_impuestos_pedido_facturaGlobal', $manejo_impuestos_pedido_facturaGlobal);
	
	$manejo_impuestos_pedido_facturaGlobal_texto = sanitize_text_field($_POST['manejo_impuestos_pedido_facturaGlobal_texto']);
	update_post_meta($post->ID, 'manejo_impuestos_pedido_facturaGlobal_texto', $manejo_impuestos_pedido_facturaGlobal_texto);
	
	$estado_orden_cfdi_automatico = sanitize_text_field($_POST['estado_orden_cfdi_automatico']);
	update_post_meta($post->ID, 'estado_orden_cfdi_automatico', $estado_orden_cfdi_automatico);
	
	$notificar_error_cfdi_automatico = sanitize_text_field($_POST['notificar_error_cfdi_automatico']);
	update_post_meta($post->ID, 'notificar_error_cfdi_automatico', $notificar_error_cfdi_automatico);
	
	$complementoCFDI = sanitize_text_field($_POST['complementoCFDI']);
	update_post_meta($post->ID, 'complementoCFDI', $complementoCFDI);
	
	$complementoCFDI_iedu_configuracion_nivel = sanitize_text_field($_POST['complementoCFDI_iedu_configuracion_nivel']);
	update_post_meta($post->ID, 'complementoCFDI_iedu_configuracion_nivel', $complementoCFDI_iedu_configuracion_nivel);
	
	$complementoCFDI_iedu_configuracion_autRVOE = sanitize_text_field($_POST['complementoCFDI_iedu_configuracion_autRVOE']);
	update_post_meta($post->ID, 'complementoCFDI_iedu_configuracion_autRVOE', $complementoCFDI_iedu_configuracion_autRVOE);
	
	$emailNotificacionErrorAutomatico = sanitize_text_field($_POST['emailNotificacionErrorAutomatico']);
	update_post_meta($post->ID, 'emailNotificacionErrorAutomatico', $emailNotificacionErrorAutomatico);
	
	if($manejo_impuestos_pedido == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>¿Cómo utilizará el plugin los datos de los pedidos de WooCommerce para su facturación?</b>." : "Select an option for <b>How will the plugin use WooCommerce order data for invoicing?</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($manejo_impuestos_pedido_facturaGlobal == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Factura Global - ¿Cómo utilizará el plugin los datos de los pedidos de WooCommerce para la factura global?</b>." : "Select an option for <b>Global Invoice - How will the plugin use WooCommerce order data for the global invoice?</b>."
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $configuracion = array
	(
		'manejo_impuestos_pedido'				=> $_POST['manejo_impuestos_pedido'],
		'manejo_impuestos_pedido_facturaGlobal' => $_POST['manejo_impuestos_pedido_facturaGlobal'],
		'manejo_impuestos_pedido_facturaGlobal_texto' => $_POST['manejo_impuestos_pedido_facturaGlobal_texto'],
		'estado_orden_cfdi_automatico' => $_POST['estado_orden_cfdi_automatico'],
		'notificar_error_cfdi_automatico' => $_POST['notificar_error_cfdi_automatico'],
		'complementoCFDI' => $_POST['complementoCFDI'],
		'complementoCFDI_iedu_configuracion_nivel' => $_POST['complementoCFDI_iedu_configuracion_nivel'],
		'complementoCFDI_iedu_configuracion_autRVOE' => $_POST['complementoCFDI_iedu_configuracion_autRVOE'],
		'emailNotificacionErrorAutomatico' => $_POST['emailNotificacionErrorAutomatico']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceConfiguracion::guardarConfiguracionAjustesAvanzados($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function realvirtual_woocommerce_guardar_configuracion_idioma_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;

	$idiomaRVLFECFDI = sanitize_text_field($_POST['idioma']);
	update_post_meta($post->ID, 'idioma', $idiomaRVLFECFDI);
	
	if($idiomaRVLFECFDI == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona el idioma del plugin.':'Select the plugin language.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $configuracion = array
	(
		'idioma' 								=> $_POST['idioma']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceConfiguracion::guardarConfiguracionIdioma($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_ci_consultarPedidos_guardar', 'realvirtual_woocommerce_ci_consultarPedidos_guardar_callback');

function realvirtual_woocommerce_ci_consultarPedidos_guardar_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post, $idiomaRVLFECFDI;

	$ci_consultarPedidos_tipo_conexion = sanitize_text_field($_POST['ci_consultarPedidos_tipo_conexion']);
	update_post_meta($post->ID, 'ci_consultarPedidos_tipo_conexion', $ci_consultarPedidos_tipo_conexion);
	
	$ci_consultarPedidos_tipo_solicitud = sanitize_text_field($_POST['ci_consultarPedidos_tipo_solicitud']);
	update_post_meta($post->ID, 'ci_consultarPedidos_tipo_solicitud', $ci_consultarPedidos_tipo_solicitud);
	
	$ci_consultarPedidos_url = sanitize_text_field($_POST['ci_consultarPedidos_url']);
	update_post_meta($post->ID, 'ci_consultarPedidos_url', $ci_consultarPedidos_url);
	
	$ci_consultarPedidos_nombre_parametro_numeropedido = sanitize_text_field($_POST['ci_consultarPedidos_nombre_parametro_numeropedido']);
	update_post_meta($post->ID, 'ci_consultarPedidos_nombre_parametro_numeropedido', $ci_consultarPedidos_nombre_parametro_numeropedido);
	
	$ci_consultarPedidos_nombre_parametro_monto = sanitize_text_field($_POST['ci_consultarPedidos_nombre_parametro_monto']);
	update_post_meta($post->ID, 'ci_consultarPedidos_nombre_parametro_monto', $ci_consultarPedidos_nombre_parametro_monto);
	
	$ci_consultarPedidos_parametro_extra1_tipo = sanitize_text_field($_POST['ci_consultarPedidos_parametro_extra1_tipo']);
	update_post_meta($post->ID, 'ci_consultarPedidos_parametro_extra1_tipo', $ci_consultarPedidos_parametro_extra1_tipo);
	
	$ci_consultarPedidos_parametro_extra1_nombrevisual = sanitize_text_field($_POST['ci_consultarPedidos_parametro_extra1_nombrevisual']);
	update_post_meta($post->ID, 'ci_consultarPedidos_parametro_extra1_nombrevisual', $ci_consultarPedidos_parametro_extra1_nombrevisual);
	
	$ci_consultarPedidos_parametro_extra1_nombreinterno = sanitize_text_field($_POST['ci_consultarPedidos_parametro_extra1_nombreinterno']);
	update_post_meta($post->ID, 'ci_consultarPedidos_parametro_extra1_nombreinterno', $ci_consultarPedidos_parametro_extra1_nombreinterno);
	
	$ci_consultarPedidos_parametro_extra1_estado = sanitize_text_field($_POST['ci_consultarPedidos_parametro_extra1_estado']);
	update_post_meta($post->ID, 'ci_consultarPedidos_parametro_extra1_estado', $ci_consultarPedidos_parametro_extra1_estado);
	
	$ci_consultarPedidos_parametro_extra2_tipo = sanitize_text_field($_POST['ci_consultarPedidos_parametro_extra2_tipo']);
	update_post_meta($post->ID, 'ci_consultarPedidos_parametro_extra2_tipo', $ci_consultarPedidos_parametro_extra2_tipo);
	
	$ci_consultarPedidos_parametro_extra2_nombrevisual = sanitize_text_field($_POST['ci_consultarPedidos_parametro_extra2_nombrevisual']);
	update_post_meta($post->ID, 'ci_consultarPedidos_parametro_extra2_nombrevisual', $ci_consultarPedidos_parametro_extra2_nombrevisual);
	
	$ci_consultarPedidos_parametro_extra2_nombreinterno = sanitize_text_field($_POST['ci_consultarPedidos_parametro_extra2_nombreinterno']);
	update_post_meta($post->ID, 'ci_consultarPedidos_parametro_extra2_nombreinterno', $ci_consultarPedidos_parametro_extra2_nombreinterno);
	
	$ci_consultarPedidos_parametro_extra2_estado = sanitize_text_field($_POST['ci_consultarPedidos_parametro_extra2_estado']);
	update_post_meta($post->ID, 'ci_consultarPedidos_parametro_extra2_estado', $ci_consultarPedidos_parametro_extra2_estado);
	
	$ci_consultarPedidos_tipo_consulta = sanitize_text_field($_POST['ci_consultarPedidos_tipo_consulta']);
	update_post_meta($post->ID, 'ci_consultarPedidos_tipo_consulta', $ci_consultarPedidos_tipo_consulta);
	
	if($ci_consultarPedidos_tipo_consulta != '1')
	{
		if($ci_consultarPedidos_tipo_consulta == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción en <b>¿Cómo deseas que el plugin realice la búsqueda de pedidos?</b>.":"Select an option in <b>How do you want the plugin to search for orders?</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_consultarPedidos_tipo_conexion == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Conexión</b>.":"Select the <b>Connection Type</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_consultarPedidos_tipo_solicitud == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Solicitud</b>.":"Select the <b>Request Type</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_consultarPedidos_url == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Ingresa la <b>URL</b> de tu servicio.":"Enter the <b>URL</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_consultarPedidos_nombre_parametro_numeropedido == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Ingresa el <b>nombre del parámetro</b> en tu servicio al que se enviará el <b>número de pedido</b>.":"Enter the <b>parameter name</b> in your service to which the <b>order number</b> will be sent."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_consultarPedidos_nombre_parametro_monto == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Ingresa el <b>nombre del parámetro</b> en tu servicio al que se enviará el <b>monto del pedido</b>.":"Enter the <b>parameter name</b> in your service to which the <b>order amount</b> will be sent."	
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_consultarPedidos_parametro_extra1_estado == '1')
		{
			if($ci_consultarPedidos_parametro_extra1_tipo == '1')
			{
				$respuesta = array
				(
					'success' => false,
					'message' => ($idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 1</b> está activado pero faltan datos obligatorios.<br/><br/>Selecciona el <b>Tipo de datos</b> para el <b>CAMPO 1</b>.":"<b>FIELD 1</b> is activated but mandatory data is missing.<br/><br/>Select the <b>Data type</b> for the <b>FIELD 1</b>."
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
			
			if($ci_consultarPedidos_parametro_extra1_nombrevisual == '1')
			{
				$respuesta = array
				(
					'success' => false,
					'message' => ($idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 1</b> está activado pero faltan datos obligatorios.<br/><br/>Ingresa el <b>Nombre del campo en el módulo de facturación</b> para el <b>CAMPO 1</b>.":"<b>FIELD 1</b> is activated but mandatory data is missing.<br/><br/>Enter the <b>Name of the field in the invoicing module</b> for the <b>FIELD 1</b>."
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
			
			if($ci_consultarPedidos_parametro_extra1_nombreinterno == '1')
			{
				$respuesta = array
				(
					'success' => false,
					'message' => ($idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 1</b> está activado pero faltan datos obligatorios.<br/><br/>Ingresa el <b>nombre del parámetro</b> en tu servicio al que se enviará el valor del <b>CAMPO 1</b>.":"<b>FIELD 1</b> is activated but mandatory data is missing.<br/><br/>Enter the <b>parameter name</b> in your service to which the <b>FIELD 1</b> value will be sent."
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
		}
		
		if($ci_consultarPedidos_parametro_extra2_estado == '1')
		{
			if($ci_consultarPedidos_parametro_extra2_tipo == '1')
			{
				$respuesta = array
				(
					'success' => false,
					'message' => ($idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 2</b> está activado pero faltan datos obligatorios.<br/><br/>Selecciona el <b>Tipo de datos</b> para el <b>CAMPO 2</b>.":"<b>FIELD 2</b> is activated but mandatory data is missing.<br/><br/>Select the <b>Data type</b> for the <b>FIELD 2</b>."
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
			
			if($ci_consultarPedidos_parametro_extra2_nombrevisual == '1')
			{
				$respuesta = array
				(
					'success' => false,
					'message' => ($idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 2</b> está activado pero faltan datos obligatorios.<br/><br/>Ingresa el <b>Nombre del campo en el módulo de facturación</b> para el <b>CAMPO 2</b>.":"<b>FIELD 2</b> is activated but mandatory data is missing.<br/><br/>Enter the <b>Name of the field in the invoicing module</b> for the <b>FIELD 2</b>."	
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
			
			if($ci_consultarPedidos_parametro_extra2_nombreinterno == '1')
			{
				$respuesta = array
				(
					'success' => false,
					'message' => ($idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 2</b> está activado pero faltan datos obligatorios.<br/><br/>Ingresa el <b>nombre del parámetro</b> en tu servicio al que se enviará el valor del <b>CAMPO 2</b>.":"<b>FIELD 2</b> is activated but mandatory data is missing.<br/><br/>Enter the <b>parameter name</b> in your service to which the <b>FIELD 2</b> value will be sent."	
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
		}
	}
	
    $configuracion = array
	(
		'ci_consultarPedidos_tipo_conexion'       					=> $_POST['ci_consultarPedidos_tipo_conexion'],
		'ci_consultarPedidos_tipo_solicitud'       					=> $_POST['ci_consultarPedidos_tipo_solicitud'],
		'ci_consultarPedidos_url'       							=> $_POST['ci_consultarPedidos_url'],
		'ci_consultarPedidos_nombre_parametro_numeropedido'   		=> $_POST['ci_consultarPedidos_nombre_parametro_numeropedido'],
		'ci_consultarPedidos_nombre_parametro_monto'      			=> $_POST['ci_consultarPedidos_nombre_parametro_monto'],
		'ci_consultarPedidos_parametro_extra1_tipo'       			=> $_POST['ci_consultarPedidos_parametro_extra1_tipo'],
		'ci_consultarPedidos_parametro_extra1_nombrevisual'      	=> $_POST['ci_consultarPedidos_parametro_extra1_nombrevisual'],
		'ci_consultarPedidos_parametro_extra1_nombreinterno'       	=> $_POST['ci_consultarPedidos_parametro_extra1_nombreinterno'],
		'ci_consultarPedidos_parametro_extra1_estado'     			=> $_POST['ci_consultarPedidos_parametro_extra1_estado'],
		'ci_consultarPedidos_parametro_extra2_tipo'       			=> $_POST['ci_consultarPedidos_parametro_extra2_tipo'],
		'ci_consultarPedidos_parametro_extra2_nombrevisual'       	=> $_POST['ci_consultarPedidos_parametro_extra2_nombrevisual'],
		'ci_consultarPedidos_parametro_extra2_nombreinterno'		=> $_POST['ci_consultarPedidos_parametro_extra2_nombreinterno'],
		'ci_consultarPedidos_parametro_extra2_estado'				=> $_POST['ci_consultarPedidos_parametro_extra2_estado'],
		'ci_consultarPedidos_tipo_consulta'							=> $_POST['ci_consultarPedidos_tipo_consulta']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceCentroIntegracion::guardarConfiguracionConsultarPedidos($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_ci_enviarPedidos_guardar', 'realvirtual_woocommerce_ci_enviarPedidos_guardar_callback');

function realvirtual_woocommerce_ci_enviarPedidos_guardar_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post, $idiomaRVLFECFDI;

	$ci_enviarPedidos_tipo_consulta = sanitize_text_field($_POST['ci_enviarPedidos_tipo_consulta']);
	update_post_meta($post->ID, 'ci_enviarPedidos_tipo_consulta', $ci_enviarPedidos_tipo_consulta);
	
	$ci_enviarPedidos_tipo_conexion = sanitize_text_field($_POST['ci_enviarPedidos_tipo_conexion']);
	update_post_meta($post->ID, 'ci_enviarPedidos_tipo_conexion', $ci_enviarPedidos_tipo_conexion);
	
	$ci_enviarPedidos_tipo_solicitud = sanitize_text_field($_POST['ci_enviarPedidos_tipo_solicitud']);
	update_post_meta($post->ID, 'ci_enviarPedidos_tipo_solicitud', $ci_enviarPedidos_tipo_solicitud);
	
	$ci_enviarPedidos_url = sanitize_text_field($_POST['ci_enviarPedidos_url']);
	update_post_meta($post->ID, 'ci_enviarPedidos_url', $ci_enviarPedidos_url);
	
	$ci_enviarPedidos_tipo_conexion2 = sanitize_text_field($_POST['ci_enviarPedidos_tipo_conexion2']);
	update_post_meta($post->ID, 'ci_enviarPedidos_tipo_conexion2', $ci_enviarPedidos_tipo_conexion2);
	
	$ci_enviarPedidos_tipo_solicitud2 = sanitize_text_field($_POST['ci_enviarPedidos_tipo_solicitud2']);
	update_post_meta($post->ID, 'ci_enviarPedidos_tipo_solicitud2', $ci_enviarPedidos_tipo_solicitud2);
	
	$ci_enviarPedidos_url2 = sanitize_text_field($_POST['ci_enviarPedidos_url2']);
	update_post_meta($post->ID, 'ci_enviarPedidos_url2', $ci_enviarPedidos_url2);
	
	if($ci_enviarPedidos_tipo_consulta != '0')
	{
		if($ci_enviarPedidos_tipo_consulta == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción en <b>¿Qué estado debe tener el pedido para ser enviado a tu servicio?</b>.":"Select an option in <b>What state must the order have to be sent to your service?</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_enviarPedidos_tipo_conexion == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Conexión</b>.":"Select the <b>Connection Type</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_enviarPedidos_tipo_solicitud == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Solicitud</b>.":"Select the <b>Request Type</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_enviarPedidos_url == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Ingresa la <b>URL</b> de tu servicio.":"Enter the <b>URL</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
	}
	
    $configuracion = array
	(
		'ci_enviarPedidos_tipo_conexion'    => $_POST['ci_enviarPedidos_tipo_conexion'],
		'ci_enviarPedidos_tipo_solicitud'   => $_POST['ci_enviarPedidos_tipo_solicitud'],
		'ci_enviarPedidos_url'       		=> $_POST['ci_enviarPedidos_url'],
		'ci_enviarPedidos_tipo_conexion2'    => $_POST['ci_enviarPedidos_tipo_conexion2'],
		'ci_enviarPedidos_tipo_solicitud2'   => $_POST['ci_enviarPedidos_tipo_solicitud2'],
		'ci_enviarPedidos_url2'       		=> $_POST['ci_enviarPedidos_url2'],
		'ci_enviarPedidos_tipo_consulta'	=> $_POST['ci_enviarPedidos_tipo_consulta']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceCentroIntegracion::guardarConfiguracionEnviarPedidos($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_ci_enviarPedidosCrear_guardar', 'realvirtual_woocommerce_ci_enviarPedidosCrear_guardar_callback');

function realvirtual_woocommerce_ci_enviarPedidosCrear_guardar_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post, $idiomaRVLFECFDI;

	$ci_enviarPedidosCrear_tipo_consulta = sanitize_text_field($_POST['ci_enviarPedidosCrear_tipo_consulta']);
	update_post_meta($post->ID, 'ci_enviarPedidosCrear_tipo_consulta', $ci_enviarPedidosCrear_tipo_consulta);
	
	$ci_enviarPedidosCrear_tipo_conexion = sanitize_text_field($_POST['ci_enviarPedidosCrear_tipo_conexion']);
	update_post_meta($post->ID, 'ci_enviarPedidosCrear_tipo_conexion', $ci_enviarPedidosCrear_tipo_conexion);
	
	$ci_enviarPedidosCrear_tipo_solicitud = sanitize_text_field($_POST['ci_enviarPedidosCrear_tipo_solicitud']);
	update_post_meta($post->ID, 'ci_enviarPedidosCrear_tipo_solicitud', $ci_enviarPedidosCrear_tipo_solicitud);
	
	$ci_enviarPedidosCrear_url = sanitize_text_field($_POST['ci_enviarPedidosCrear_url']);
	update_post_meta($post->ID, 'ci_enviarPedidosCrear_url', $ci_enviarPedidosCrear_url);
	
	$ci_enviarPedidosCrear_tipo_conexion2 = sanitize_text_field($_POST['ci_enviarPedidosCrear_tipo_conexion2']);
	update_post_meta($post->ID, 'ci_enviarPedidosCrear_tipo_conexion2', $ci_enviarPedidosCrear_tipo_conexion2);
	
	$ci_enviarPedidosCrear_tipo_solicitud2 = sanitize_text_field($_POST['ci_enviarPedidosCrear_tipo_solicitud2']);
	update_post_meta($post->ID, 'ci_enviarPedidosCrear_tipo_solicitud2', $ci_enviarPedidosCrear_tipo_solicitud2);
	
	$ci_enviarPedidosCrear_url2 = sanitize_text_field($_POST['ci_enviarPedidosCrear_url2']);
	update_post_meta($post->ID, 'ci_enviarPedidosCrear_url2', $ci_enviarPedidosCrear_url2);
	
	if($ci_enviarPedidosCrear_tipo_consulta != '0')
	{
		if($ci_enviarPedidosCrear_tipo_consulta == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción en <b>¿Qué estado debe tener el pedido para ser enviado a tu servicio?</b>.":"Select an option in <b>What state must the order have to be sent to your service?</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_enviarPedidosCrear_tipo_conexion == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Conexión</b>.":"Select the <b>Connection Type</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_enviarPedidosCrear_tipo_solicitud == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Solicitud</b>.":"Select the <b>Request Type</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_enviarPedidosCrear_url == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Ingresa la <b>URL</b> de tu servicio.":"Enter the <b>URL</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
	}
	
    $configuracion = array
	(
		'ci_enviarPedidosCrear_tipo_conexion'    	=> $_POST['ci_enviarPedidosCrear_tipo_conexion'],
		'ci_enviarPedidosCrear_tipo_solicitud'   	=> $_POST['ci_enviarPedidosCrear_tipo_solicitud'],
		'ci_enviarPedidosCrear_url'       			=> $_POST['ci_enviarPedidosCrear_url'],
		'ci_enviarPedidosCrear_tipo_conexion2'   	=> $_POST['ci_enviarPedidosCrear_tipo_conexion2'],
		'ci_enviarPedidosCrear_tipo_solicitud2'  	=> $_POST['ci_enviarPedidosCrear_tipo_solicitud2'],
		'ci_enviarPedidosCrear_url2'       			=> $_POST['ci_enviarPedidosCrear_url2'],
		'ci_enviarPedidosCrear_tipo_consulta'		=> $_POST['ci_enviarPedidosCrear_tipo_consulta']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceCentroIntegracion::guardarConfiguracionEnviarPedidosCrear($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_ci_enviarXml_guardar', 'realvirtual_woocommerce_ci_enviarXml_guardar_callback');

function realvirtual_woocommerce_ci_enviarXml_guardar_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post, $idiomaRVLFECFDI;

	$ci_enviarXml_tipo_conexion = sanitize_text_field($_POST['ci_enviarXml_tipo_conexion']);
	update_post_meta($post->ID, 'ci_enviarXml_tipo_conexion', $ci_enviarXml_tipo_conexion);
	
	$ci_enviarXml_tipo_solicitud = sanitize_text_field($_POST['ci_enviarXml_tipo_solicitud']);
	update_post_meta($post->ID, 'ci_enviarXml_tipo_solicitud', $ci_enviarXml_tipo_solicitud);
	
	$ci_enviarXml_url = sanitize_text_field($_POST['ci_enviarXml_url']);
	update_post_meta($post->ID, 'ci_enviarXml_url', $ci_enviarXml_url);
	
	$ci_enviarXml_tipo_conexion2 = sanitize_text_field($_POST['ci_enviarXml_tipo_conexion2']);
	update_post_meta($post->ID, 'ci_enviarXml_tipo_conexion2', $ci_enviarXml_tipo_conexion2);
	
	$ci_enviarXml_tipo_solicitud2 = sanitize_text_field($_POST['ci_enviarXml_tipo_solicitud2']);
	update_post_meta($post->ID, 'ci_enviarXml_tipo_solicitud2', $ci_enviarXml_tipo_solicitud2);
	
	$ci_enviarXml_url2 = sanitize_text_field($_POST['ci_enviarXml_url2']);
	update_post_meta($post->ID, 'ci_enviarXml_url2', $ci_enviarXml_url2);
	
	$ci_enviarXml_tipo_consulta = sanitize_text_field($_POST['ci_enviarXml_tipo_consulta']);
	update_post_meta($post->ID, 'ci_enviarXml_tipo_consulta', $ci_enviarXml_tipo_consulta);
	
	if($ci_enviarXml_tipo_consulta != '0')
	{
		if($ci_enviarXml_tipo_consulta == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona una opción en <b>¿Cómo deseas que el plugin realice la búsqueda de pedidos?</b>.":"Select an option in <b>How do you want the plugin to search for orders?</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_enviarXml_tipo_conexion == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Conexión</b>.":"Select the <b>Connection Type</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_enviarXml_tipo_solicitud == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Solicitud</b>.":"Select the <b>Request Type</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($ci_enviarXml_url == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? "Ingresa la <b>URL</b> de tu servicio.":"Enter the <b>URL</b>."
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
	}
	
    $configuracion = array
	(
		'ci_enviarXml_tipo_conexion'    => $_POST['ci_enviarXml_tipo_conexion'],
		'ci_enviarXml_tipo_solicitud'   => $_POST['ci_enviarXml_tipo_solicitud'],
		'ci_enviarXml_url'       		=> $_POST['ci_enviarXml_url'],
		'ci_enviarXml_tipo_conexion2'   => $_POST['ci_enviarXml_tipo_conexion2'],
		'ci_enviarXml_tipo_solicitud2'  => $_POST['ci_enviarXml_tipo_solicitud2'],
		'ci_enviarXml_url2'       		=> $_POST['ci_enviarXml_url2'],
		'ci_enviarXml_tipo_consulta'	=> $_POST['ci_enviarXml_tipo_consulta']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceCentroIntegracion::guardarConfiguracionEnviarXml($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function realvirtual_woocommerce_front_end($numeroPedido = 0)
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $sistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$configuracionIntegracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$idiomaRVLFECFDI = ($configuracion['idioma'] != '') ? $configuracion['idioma'] : 'ES';
	
	//echo'<script type="text/javascript">if(typeof(idiomaRVLFECFDI) == "undefined")
    //var idiomaRVLFECFDI="'.$idiomaRVLFECFDI.'";</script>';
	
	$opcionesMetodoPago = '';
	$metodo_pago = $configuracion['metodo_pago'];
	$metodosPagoHabilitado = '';
	$metodosPagoHabilitadoLB = ($idiomaRVLFECFDI == 'ES') ? 'Forma de pago':'Payment way';
	
	$opcionesMetodoPago33 = '';
	$metodo_pago33 = $configuracion['metodo_pago33'];
	$metodosPagoHabilitado33 = '';
	$metodosPagoHabilitado33LB = ($idiomaRVLFECFDI == 'ES') ? 'Método de pago':'Payment Method';
	
	$opcionesUsoCFDI = '';
	$uso_cfdi = $configuracion['uso_cfdi'];
	$usoCFDIHabilitado = '';
	$usoCFDIHabilitadoLB = ($idiomaRVLFECFDI == 'ES') ? 'Uso CFDI':'CFDI Use';
	
	$opcionesRegimenFiscalReceptor = '';
	
	if($configuracion['uso_cfdi_seleccionar'] == 'no')
		$usoCFDIHabilitado = 'disabled';
	if($configuracion['uso_cfdi_seleccionar'] == 'noOcultar')
	{
		$usoCFDIHabilitado = 'hidden';
		$usoCFDIHabilitadoLB = '';
	}
	
	if($configuracion['metodo_pago_seleccionar'] == 'no')
		$metodosPagoHabilitado = 'disabled';
	if($configuracion['metodo_pago_seleccionar'] == 'noOcultar')
	{
		$metodosPagoHabilitado = 'hidden';
		$metodosPagoHabilitadoLB = '';
	}
	
	if($configuracion['metodo_pago_seleccionar33'] == 'no')
		$metodosPagoHabilitado33 = 'disabled';
	if($configuracion['metodo_pago_seleccionar33'] == 'noOcultar')
	{
		$metodosPagoHabilitado33 = 'hidden';
		$metodosPagoHabilitado33LB = '';
	}
	
	if($metodo_pago == '01')
		$opcionesMetodoPago .= '<option value="01" selected>01 - Efectivo</option>';
	else
		$opcionesMetodoPago .= '<option value="01">01 - Efectivo</option>';
	
	if($metodo_pago == '02')
		$opcionesMetodoPago .= '<option value="02" selected>02 - Cheque nominativo</option>';
	else
		$opcionesMetodoPago .= '<option value="02">02 - Cheque nominativo</option>';
											
	if($metodo_pago == '03')
		$opcionesMetodoPago .= '<option value="03" selected>03 - Transferencia electrónica de fondos</option>';
	else
		$opcionesMetodoPago .= '<option value="03">03 - Transferencia electrónica de fondos</option>';
											
	if($metodo_pago == '04')
		$opcionesMetodoPago .= '<option value="04" selected>04 - Tarjeta de crédito</option>';
	else
		$opcionesMetodoPago .= '<option value="04">04 - Tarjeta de crédito</option>';
											
	if($metodo_pago == '05')
		$opcionesMetodoPago .= '<option value="05" selected>05 - Monedero electrónico</option>';
	else
		$opcionesMetodoPago .= '<option value="05">05 - Monedero electrónico</option>';
											
	if($metodo_pago == '06')
		$opcionesMetodoPago .= '<option value="06" selected>06 - Dinero electrónico</option>';
	else
		$opcionesMetodoPago .= '<option value="06">06 - Dinero electrónico</option>';
											
	if($metodo_pago == '08')
		$opcionesMetodoPago .= '<option value="08" selected>08 - Vales de despensa</option>';
	else
		$opcionesMetodoPago .= '<option value="08">08 - Vales de despensa</option>';
											
	if($metodo_pago == '12')
		$opcionesMetodoPago .= '<option value="12" selected>12 - Dación de pago</option>';
	else
		$opcionesMetodoPago .= '<option value="12">12 - Dación de pago</option>';

	if($metodo_pago == '13')
		$opcionesMetodoPago .= '<option value="13" selected>13 - Pago por subrogación</option>';
	else
		$opcionesMetodoPago .= '<option value="13">13 - Pago por subrogación</option>';
											
	if($metodo_pago == '14')
		$opcionesMetodoPago .= '<option value="14" selected>14 - Pago por consignación</option>';
	else
		$opcionesMetodoPago .= '<option value="14">14 - Pago por consignación</option>';
	
	if($metodo_pago == '15')
		$opcionesMetodoPago .= '<option value="15" selected>15 - Condonación</option>';
	else
		$opcionesMetodoPago .= '<option value="15">15 - Condonación</option>';
	
	if($metodo_pago == '17')
		$opcionesMetodoPago .= '<option value="17" selected>17 - Compensación</option>';
	else
		$opcionesMetodoPago .= '<option value="17">17 - Compensación</option>';
	
	if($metodo_pago == '23')
		$opcionesMetodoPago .= '<option value="23" selected>23 - Novación</option>';
	else
		$opcionesMetodoPago .= '<option value="23">23 - Novación</option>';
	
	if($metodo_pago == '24')
		$opcionesMetodoPago .= '<option value="24" selected>24 - Confusión</option>';
	else
		$opcionesMetodoPago .= '<option value="24">24 - Confusión</option>';
	
	if($metodo_pago == '25')
		$opcionesMetodoPago .= '<option value="25" selected>25 - Remisión de deuda</option>';
	else
		$opcionesMetodoPago .= '<option value="25">25 - Remisión de deuda</option>';
	
	if($metodo_pago == '26')
		$opcionesMetodoPago .= '<option value="26" selected>26 - Prescripción o caducidad</option>';
	else
		$opcionesMetodoPago .= '<option value="26">26 - Prescripción o caducidad</option>';
	
	if($metodo_pago == '27')
		$opcionesMetodoPago .= '<option value="27" selected>27 - A satisfacción del acreedor</option>';
	else
		$opcionesMetodoPago .= '<option value="27">27 - A satisfacción del acreedor</option>';
	
	if($metodo_pago == '28')
		$opcionesMetodoPago .= '<option value="28" selected>28 - Tarjeta de débito</option>';
	else
		$opcionesMetodoPago .= '<option value="28">28 - Tarjeta de débito</option>';
	
	if($metodo_pago == '29')
		$opcionesMetodoPago .= '<option value="29" selected>29 - Tarjeta de servicios</option>';
	else
		$opcionesMetodoPago .= '<option value="29">29 - Tarjeta de servicios</option>';
	
	if($metodo_pago == '30')
		$opcionesMetodoPago .= '<option value="30" selected>30 - Aplicación de anticipos</option>';
	else
		$opcionesMetodoPago .= '<option value="30">30 - Aplicación de anticipos</option>';
	
	if($metodo_pago == '31')
		$opcionesMetodoPago .= '<option value="31" selected>31 - Intermediario pagos</option>';
	else
		$opcionesMetodoPago .= '<option value="31">31 - Intermediario pagos</option>';
	
	if($metodo_pago == '99')
		$opcionesMetodoPago .= '<option value="99" selected>99 - Por definir</option>';
	else
		$opcionesMetodoPago .= '<option value="99">99 - Por definir</option>';
	
	if($metodo_pago33 == 'PUE')
		$opcionesMetodoPago33 .= '<option value="PUE" selected>PUE - Pago en una sola exhibición</option>';
	else
		$opcionesMetodoPago33 .= '<option value="PUE">PUE - Pago en una sola exhibición</option>';
	
	if($metodo_pago33 == 'PPD')
		$opcionesMetodoPago33 .= '<option value="PPD" selected>PPD - Pago en parcialidades o diferido</option>';
	else
		$opcionesMetodoPago33 .= '<option value="PPD">PPD - Pago en parcialidades o diferido</option>';
	
	if($uso_cfdi == 'G01')
		$opcionesUsoCFDI .= '<option value="G01" selected>G01 - Adquisición de mercancías</option>';
	else
		$opcionesUsoCFDI .= '<option value="G01">G01 - Adquisición de mercancías</option>';
	
	if($uso_cfdi == 'G02')
		$opcionesUsoCFDI .= '<option value="G02" selected>G02 - Devoluciones, descuentos o bonificaciones</option>';
	else
		$opcionesUsoCFDI .= '<option value="G02">G02 - Devoluciones, descuentos o bonificaciones</option>';
	
	if($uso_cfdi == 'G03')
		$opcionesUsoCFDI .= '<option value="G03" selected>G03 - Gastos en general</option>';
	else
		$opcionesUsoCFDI .= '<option value="G03">G03 - Gastos en general</option>';
	
	if($uso_cfdi == 'I01')
		$opcionesUsoCFDI .= '<option value="I01" selected>I01 - Construcciones</option>';
	else
		$opcionesUsoCFDI .= '<option value="I01">I01 - Construcciones</option>';
	
	if($uso_cfdi == 'I02')
		$opcionesUsoCFDI .= '<option value="I02" selected>I02 - Mobiliario y equipo de oficina por inversiones</option>';
	else
		$opcionesUsoCFDI .= '<option value="I02">I02 - Mobiliario y equipo de oficina por inversiones</option>';
	
	if($uso_cfdi == 'I03')
		$opcionesUsoCFDI .= '<option value="I03" selected>I03 - Equipo de transporte</option>';
	else
		$opcionesUsoCFDI .= '<option value="I03">I03 - Equipo de transporte</option>';
	
	if($uso_cfdi == 'I04')
		$opcionesUsoCFDI .= '<option value="I04" selected>I04 - Equipo de cómputo y accesorios</option>';
	else
		$opcionesUsoCFDI .= '<option value="I04">I04 - Equipo de cómputo y accesorios</option>';
	
	if($uso_cfdi == 'I05')
		$opcionesUsoCFDI .= '<option value="I05" selected>I05 - Dados, troqueles, moldes, matrices y herramental</option>';
	else
		$opcionesUsoCFDI .= '<option value="I05">I05 - Dados, troqueles, moldes, matrices y herramental</option>';
	
	if($uso_cfdi == 'I06')
		$opcionesUsoCFDI .= '<option value="I06" selected>I06 - Comunicaciones telefónicas</option>';
	else
		$opcionesUsoCFDI .= '<option value="I06">I06 - Comunicaciones telefónicas</option>';
	
	if($uso_cfdi == 'I07')
		$opcionesUsoCFDI .= '<option value="I07" selected>I07 - Comunicaciones satelitales</option>';
	else
		$opcionesUsoCFDI .= '<option value="I07">I07 - Comunicaciones satelitales</option>';
	
	if($uso_cfdi == 'I08')
		$opcionesUsoCFDI .= '<option value="I08" selected>I08 - Otra maquinaria y equipo</option>';
	else
		$opcionesUsoCFDI .= '<option value="I08">I08 - Otra maquinaria y equipo</option>';
	
	if($uso_cfdi == 'D01')
		$opcionesUsoCFDI .= '<option value="D01" selected>D01 - Honorarios médicos, dentales y gastos hospitalarios</option>';
	else
		$opcionesUsoCFDI .= '<option value="D01">D01 - Honorarios médicos, dentales y gastos hospitalarios</option>';
	
	if($uso_cfdi == 'D02')
		$opcionesUsoCFDI .= '<option value="D02" selected>D02 - Gastos médicos por incapacidad o discapacidad</option>';
	else
		$opcionesUsoCFDI .= '<option value="D02">D02 - Gastos médicos por incapacidad o discapacidad</option>';
	
	if($uso_cfdi == 'D03')
		$opcionesUsoCFDI .= '<option value="D03" selected>D03 - Gastos funerales</option>';
	else
		$opcionesUsoCFDI .= '<option value="D03">D03 - Gastos funerales</option>';
	
	if($uso_cfdi == 'D04')
		$opcionesUsoCFDI .= '<option value="D04" selected>D04 - Donativos</option>';
	else
		$opcionesUsoCFDI .= '<option value="D04">D04 - Donativos</option>';
	
	if($uso_cfdi == 'D05')
		$opcionesUsoCFDI .= '<option value="D05" selected>D05 - Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>';
	else
		$opcionesUsoCFDI .= '<option value="D05">D05 - Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>';
	
	if($uso_cfdi == 'D06')
		$opcionesUsoCFDI .= '<option value="D06" selected>D06 - Aportaciones voluntarias al SAR</option>';
	else
		$opcionesUsoCFDI .= '<option value="D06">D06 - portaciones voluntarias al SAR</option>';
	
	if($uso_cfdi == 'D07')
		$opcionesUsoCFDI .= '<option value="D07" selected>D07 - Primas por seguros de gastos médicos</option>';
	else
		$opcionesUsoCFDI .= '<option value="D07">D07 - Primas por seguros de gastos médicos</option>';
	
	if($uso_cfdi == 'D08')
		$opcionesUsoCFDI .= '<option value="D08" selected>D08 - Gastos de transportación escolar obligatoria</option>';
	else
		$opcionesUsoCFDI .= '<option value="D08">D08 - Gastos de transportación escolar obligatoria</option>';
	
	if($uso_cfdi == 'D09')
		$opcionesUsoCFDI .= '<option value="D09" selected>D09 - Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones</option>';
	else
		$opcionesUsoCFDI .= '<option value="D09">D09 - Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones</option>';
	
	if($uso_cfdi == 'D10')
		$opcionesUsoCFDI .= '<option value="D10" selected>D10 - Pagos por servicios educativos (colegiaturas)</option>';
	else
		$opcionesUsoCFDI .= '<option value="D10">D10 - Pagos por servicios educativos (colegiaturas)</option>';
	
	if($configuracion['version_cfdi'] == '3.3')
	{
		if($uso_cfdi == 'P01')
			$opcionesUsoCFDI .= '<option value="P01" selected>P01 - Por definir</option>';
		else
			$opcionesUsoCFDI .= '<option value="P01">P01 - Por definir</option>';
	}
	else if($configuracion['version_cfdi'] == '4.0')
	{
		if($uso_cfdi == 'S01')
			$opcionesUsoCFDI .= '<option value="S01" selected>S01 - Sin efectos fiscales</option>';
		else
			$opcionesUsoCFDI .= '<option value="S01">S01 - Sin efectos fiscales</option>';
		
		if($uso_cfdi == 'CP01')
			$opcionesUsoCFDI .= '<option value="CP01" selected>CP01 - Pagos</option>';
		else
			$opcionesUsoCFDI .= '<option value="CP01">CP01 - Pagos</option>';
		
		if($uso_cfdi == 'CN01')
			$opcionesUsoCFDI .= '<option value="CN01" selected>CN01 - Nómina</option>';
		else
			$opcionesUsoCFDI .= '<option value="CN01">CN01 - Nómina</option>';
	}
	
	$opcionesRegimenFiscalReceptor .= '<option value="601">601 - General de Ley Personas Morales</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="603">603 - Personas Morales con Fines no Lucrativos</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="605">605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="606">606 - Arrendamiento</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="607">607 - Régimen de Enajenación o Adquisición de Bienes</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="608">608 - Demás ingresos</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="610">610 - Residentes en el Extranjero sin Establecimiento Permanente en México</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="611">611 - Ingresos por Dividendos (socios y accionistas)</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="612">612 - Personas Físicas con Actividades Empresariales y Profesionales</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="614">614 - Ingresos por intereses</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="615">615 - Régimen de los ingresos por obtención de premios</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="616">616 - Sin obligaciones fiscales</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="620">620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="621">621 - Incorporación Fiscal</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="622">622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="623">623 - Opcional para Grupos de Sociedades</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="624">624 - Coordinados</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="625">625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>';
	$opcionesRegimenFiscalReceptor .= '<option value="626">626 - Régimen Simplificado de Confianza</option>';
	
	$campoExtra1 = '';
	$campoExtra2 = '';
	
	if($configuracionIntegracion['ci_consultarPedidos_tipo_consulta'] != '1')
	{
		if($configuracionIntegracion['ci_consultarPedidos_parametro_extra1_estado'] == '1')
		{
			$campoExtra1 = '<br/><label><font color="'.$configuracion['color_texto_formulario'].'">* '.$configuracionIntegracion['ci_consultarPedidos_parametro_extra1_nombrevisual'].'</font></label>';
			
			if($configuracionIntegracion['ci_consultarPedidos_parametro_extra1_tipo'] == 'tipo_texto')
				$campoExtra1 .= '<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="campoExtra1" name="campoExtra1" value="" placeholder="" data-original-title="'.$configuracionIntegracion['ci_consultarPedidos_parametro_extra1_nombrevisual'].'" />';
			else if($configuracionIntegracion['ci_consultarPedidos_parametro_extra1_tipo'] == 'tipo_fecha')
				$campoExtra1 .= '<input type="date" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="campoExtra1" name="campoExtra1" value="" placeholder="" data-original-title="'.$configuracionIntegracion['ci_consultarPedidos_parametro_extra1_nombrevisual'].'" />';
		}
		
		if($configuracionIntegracion['ci_consultarPedidos_parametro_extra2_estado'] == '1')
		{
			$campoExtra2 = '<br/><label><font color="'.$configuracion['color_texto_formulario'].'">* '.$configuracionIntegracion['ci_consultarPedidos_parametro_extra2_nombrevisual'].'</font></label>';
			
			if($configuracionIntegracion['ci_consultarPedidos_parametro_extra2_tipo'] == 'tipo_texto')
				$campoExtra2 .= '<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="campoExtra2" name="campoExtra2" value="" placeholder="" data-original-title="'.$configuracionIntegracion['ci_consultarPedidos_parametro_extra2_nombrevisual'].'" />';
			else if($configuracionIntegracion['ci_consultarPedidos_parametro_extra2_tipo'] == 'tipo_fecha')
				$campoExtra2 .= '<input type="date" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="campoExtra2" name="campoExtra2" value="" placeholder="" data-original-title="'.$configuracionIntegracion['ci_consultarPedidos_parametro_extra2_nombrevisual'].'" />';
		}
	}
	
	$formularioComplementoCFDI = '';
	
	if($configuracion['complementoCFDI'] == 'iedu')
	{
		$opcionesIEDUNivel = '<option value="Preescolar" selected>Preescolar</option>';
		$opcionesIEDUNivel .= '<option value="Preescolar">Primaria</option>';
		$opcionesIEDUNivel .= '<option value="Preescolar">Secundaria</option>';
		$opcionesIEDUNivel .= '<option value="Preescolar">Profesional técnico</option>';
		$opcionesIEDUNivel .= '<option value="Preescolar">Bachillerato o su equivalente</option>';
		
		$formularioComplementoCFDI = '<font size="2">Complemento Educativo (IEDU)</font><br/><table width="100%">
			<thead>
			<tr>
				<th style="vertical-align:top; width:20%; text-align: left;">
					<font color="'.$configuracion['color_texto_formulario'].'" size="2">
						<label>Nombre del alumno</label>
					</font>
				</th>
				<th style="vertical-align:top; width:20%; text-align: left;">
					<font color="'.$configuracion['color_texto_formulario'].'" size="2">
						<label>CURP</label>
					</font>
				</th>
				<th style="vertical-align:top; width:20%; text-align: left;">
					<font color="'.$configuracion['color_texto_formulario'].'" size="2">
						<label>Nivel educativo</label>
					</font>
				</th>
				<th style="vertical-align:top; width:20%; text-align: left;">
					<font color="'.$configuracion['color_texto_formulario'].'" size="2">
						<label>Clave validéz oficial</label>
					</font>
				</th>
				<th style="vertical-align:top; width:20%; text-align: left;">
					<font color="'.$configuracion['color_texto_formulario'].'" size="2">
						<label>RFC Pago</label>
					</font>
				</th>
			</thead>
			<tbody id="iedu_tablaAlumnos">
				<tr>
					<td style="vertical-align:top; width:20%; text-align: left;">
						<font color="'.$configuracion['color_texto_formulario'].'" size="2">
							<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="iedu_nombreAlumno0" name="iedu_nombreAlumno0" value="" placeholder="'.(($idiomaRVLFECFDI == 'ES') ? '':'').'"  />
						</font>
					</td>
					<td style="vertical-align:top; width:20%; text-align: left;">
						<font color="'.$configuracion['color_texto_formulario'].'" size="2">
							<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="iedu_curp0" name="iedu_curp0" value="" placeholder="'.(($idiomaRVLFECFDI == 'ES') ? '':'').'"  />
						</font>
					</td>
					<td style="vertical-align:top; width:20%; text-align: left;">
						<font color="'.$configuracion['color_texto_formulario'].'" size="2">
							<select id="iedu_nivel0" style="width: 90%; color: '.$configuracion['color_texto_controles_formulario'].';">'.$opcionesIEDUNivel.'</select>
						</font>
					</td>
					<td style="vertical-align:top; width:15%; text-align: left;">
						<font color="'.$configuracion['color_texto_formulario'].'" size="2">
							<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="iedu_autRVOE0" name="iedu_autRVOE0" value="" placeholder="'.(($idiomaRVLFECFDI == 'ES') ? '':'').'"  />
						</font>
					</td>
					<td style="vertical-align:top; width:20%; text-align: left;">
						<font color="'.$configuracion['color_texto_formulario'].'" size="2">
							<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="iedu_rfcPago0" name="iedu_rfcPago0" value="" placeholder="'.(($idiomaRVLFECFDI == 'ES') ? '':'').'"  />
						</font>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="button" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="iedu_boton_agregarAlumno" name="iedu_boton_agregarAlumno" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Agregar otro alumno':'Add another student').'" />
		';
	}
	
	$fragmentoReceptorCFDI40 = '';
	$notaFragmentoReceptorCFDI40 = '';
	$leyendaRazonSocialCFDI = ($idiomaRVLFECFDI == 'ES') ? 'Razón Social':'Business Name';
	
	if($configuracion['version_cfdi'] == '4.0')
	{
		$leyendaRazonSocialCFDI = ($idiomaRVLFECFDI == 'ES') ? '* Razón Social':'* Business Name';
		
		$fragmentoReceptorCFDI40 = '<div class="rowPaso2">
									<label><font color="'.$configuracion['color_texto_formulario'].'">'.(($idiomaRVLFECFDI == 'ES') ? '* Código Postal':'* Postal Code').'</label></font>
									<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="receptor_domicilioFiscalReceptor" name="receptor_domicilioFiscalReceptor" value="" placeholder="'.(($idiomaRVLFECFDI == 'ES') ? 'Código postal de tu domicilio fiscal':'Postal code of your tax address').'" />
									<br/>
								</div>
								<div class="rowPaso2">
									<label><font color="'.$configuracion['color_texto_formulario'].'">'.(($idiomaRVLFECFDI == 'ES') ? '* Régimen Fiscal':'* Tax Regime').'</label></font>
									<select id="receptor_regimenfiscal" name="receptor_regimenfiscal" style="width: 55%; color: '.$configuracion['color_texto_controles_formulario'].';">'.$opcionesRegimenFiscalReceptor.'</select>
									<br/>
								</div>';
								
		$notaFragmentoReceptorCFDI40 = ($idiomaRVLFECFDI == 'ES') ? '<br/><center>Por disposición oficial del SAT, la razón social, el código postal y el régimen fiscal son obligatorios<br/>para emitir la nueva versión de Facturación Electrónica 4.0.</center>' : '<br/><center>By official provision of the SAT, the postal code and the tax regime are mandatory<br/>to issue the new version of Electronic Billing 4.0.</center>';
	}
	
	$formulario = '<center><div>';
	
	if(!empty($configuracion['titulo']))
        $formulario .= '<br/><font color="#848484" size="4">'.$configuracion['titulo'].'</font>';
    
	if(!empty($configuracion['descripcion']))
        $formulario .= '<br/><font color="#848484" size="2">'.$configuracion['descripcion'].'</font>';
                 
	$default_objeto_imp_shipping = '01';
	$default_objeto_imp_producto = '01';
				 
	if(isset($configuracion['objeto_imp_shipping']) && !empty($configuracion['objeto_imp_shipping']))
		$default_objeto_imp_shipping = $configuracion['objeto_imp_shipping'];
	if(isset($configuracion['objeto_imp_producto']) && !empty($configuracion['objeto_imp_producto']))
		$default_objeto_imp_producto = $configuracion['objeto_imp_producto'];
					 
	$formulario .= '</div>
				<br/><div id="realvirtual_woocommerce_facturacion">
                    <div id="paso_uno" style="width: 100%;">
                        <div style="background:'.$configuracion['color_fondo_encabezado'].'; height: 80px; line-height: 20px; margin-bottom: 0px;">
							<br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="6">'.(($idiomaRVLFECFDI == 'ES') ? 'Paso 1/4':'Step 1/4').'</font>
							<br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="4">'.(($idiomaRVLFECFDI == 'ES') ? 'Identificar pedido':'Identify order').'</font>
                        </div>
                        
						<div style="background-color:'.$configuracion['color_fondo_formulario'].';">
							<br/>
                            <p><font color="'.$configuracion['color_texto_formulario'].'" size="2">'.(($idiomaRVLFECFDI == 'ES') ? 'Ingresa el número de pedido y el monto':'Enter the order number and the amount').'</font></p>
                            <br/>
							<form name="paso_uno_formulario" id="paso_uno_formulario" action="'.esc_url(get_permalink()).'" method="post">
                                <div class="rowPaso1">
									<label><font color="'.$configuracion['color_texto_formulario'].'">'.(($idiomaRVLFECFDI == 'ES') ? '* No. Pedido':'* Num. Order').'</font></label>
									<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="numero_pedido" name="numero_pedido" value="" placeholder="'.(($idiomaRVLFECFDI == 'ES') ? 'Sin símbolo #':'Without symbol #').'"  />
									<br/>
									<label><font color="'.$configuracion['color_texto_formulario'].'">'.(($idiomaRVLFECFDI == 'ES') ? '* Monto':'* Amount').'</font></label>
									<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="monto_pedido" name="monto_pedido" value="" placeholder="'.(($idiomaRVLFECFDI == 'ES') ? 'Sin símbolo $':'Without symbol $').'"  />'
                                .$campoExtra1.$campoExtra2.
								'</div>
								<br/>
								<div>
									<input type="submit" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="paso_uno_boton_siguiente" name="paso_uno_boton_siguiente" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Siguiente':'Next').'" />
									<img id="cargandoPaso1" src="'.plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif".'" alt="Cargando" height="32" width="32" style="visibility: hidden;">
								</div>
                            </form>
							<br/>
                        </div>
						<br/><br/>
                    </div>
                    
					<div id="paso_dos" style="width: 100%;">
                        <div style="background:'.$configuracion['color_fondo_encabezado'].'; height: 80px; line-height: 20px; margin-bottom: 0px;">
                           <br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="6">'.(($idiomaRVLFECFDI == 'ES') ? 'Paso 2/4':'Step 2/4').'</font>
							<br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="4">'.(($idiomaRVLFECFDI == 'ES') ? 'Identificar cliente':'Identify customer').'</font>
                        </div>
						
                        <div style="background-color:'.$configuracion['color_fondo_formulario'].';">
							<br/>
							<!--<p><font color="'.$configuracion['color_texto_formulario'].'" size="2">Ingresa tu RFC y pulsa el bot&oacute;n <img src="'.plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_buscar.png".'" width="24" height="24" alt="Buscar" /> si ya eres un cliente registrado.</font></p>
							<p><font color="'.$configuracion['color_texto_formulario'].'" size="2">Si no eres un cliente registrado, llena los campos y pulsa el bot&oacute;n Siguiente.</font></p>-->
                            <form name="paso_dos_formulario" id="paso_dos_formulario" action="'.esc_url(get_permalink()).'" method="post">
                                <input type="hidden" id="receptor_id" name="receptor_id" value="" placeholder="" hidden /><br/>
								<table width="90%">
								<tr>
								<td>
								<div class="rowPaso2">
									<label><font color="'.$configuracion['color_texto_formulario'].'">* RFC</label></font>
									<input type="text" style="text-transform: uppercase; color: '.$configuracion['color_texto_controles_formulario'].';" id="receptor_rfc" name="receptor_rfc" value="" placeholder="" maxlength="13" /><!--<button type="button" style="background-color: '.$configuracion['color_fondo_formulario'].';" id="paso_dos_boton_buscar_cliente" name="paso_dos_boton_buscar_cliente" ><img id="imagen_paso_dos_boton_buscar_cliente" name="imagen_paso_dos_boton_buscar_cliente" src="'.plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_buscar.png".'" width="24" height="24" alt="Buscar" /></button>-->
									<br/>
								</div>
								<div class="rowPaso2">
									<label><font color="'.$configuracion['color_texto_formulario'].'">'.$leyendaRazonSocialCFDI.'</label></font>
									<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="receptor_razon_social" name="receptor_razon_social" value="" placeholder="" />
									<br/>'.$fragmentoReceptorCFDI40.'
									<label><font color="'.$configuracion['color_texto_formulario'].'">* E-mail</label></font>
									<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="receptor_email" name="receptor_email" value="" placeholder="" />
									<br/>'.$notaFragmentoReceptorCFDI40.'<br/>';
									
								$formulario .= '</div>
								</td>
								<td>
								<div class="rowPaso2">';
								
								$formulario .= '</div>
								</td>
								</tr>
								</table>
								<br/>
								<div>
									<input type="button" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="paso_dos_boton_regresar" name="paso_dos_boton_regresar" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Regresar':'Back').'" />
									<input type="submit" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="paso_dos_boton_siguiente" name="paso_dos_boton_siguiente" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Siguiente':'Next').'" />
									<img id="cargandoPaso2" src="'.plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif".'" alt="Cargando" height="32" width="32" style="visibility: hidden;">
								</div>
                            </form>
							<br/>
                        </div>
						<br/><br/>
                    </div>
					<div id="paso_tres" style="width: 100%;">
                        <div style="background:'.$configuracion['color_fondo_encabezado'].'; height: 80px; line-height: 20px; margin-bottom: 0px;">
                            <br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="6">'.(($idiomaRVLFECFDI == 'ES') ? 'Paso 3/4':'Step 3/4').'</font>
							<br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="4">'.(($idiomaRVLFECFDI == 'ES') ? 'Verificar datos del CFDI '.$configuracion['version_cfdi']:'Check CFDI '.$configuracion['version_cfdi'].' data').'</font>
                        </div>
                        <div style="padding: 10px; background-color:'.$configuracion['color_fondo_formulario'].';">
							<br/>
							<input type="hidden" id="numeroPedido" name="numeroPedido" value="'.$numeroPedido.'" placeholder="" hidden />
							<input type="hidden" id="emisor_rfc" name="emisor_rfc" value="'.$cuenta['rfc'].'" placeholder="" hidden />
							<input type="hidden" id="emisor_usuario" name="emisor_usuario" value="'.$cuenta['usuario'].'" placeholder="" hidden />
							<input type="hidden" id="emisor_serie" name="emisor_serie" value="'.$configuracion['serie'].'" placeholder="" hidden />
							<input type="hidden" id="objeto_imp_shipping" name="objeto_imp_shipping" value="'.$default_objeto_imp_shipping.'" placeholder="" hidden />
							<input type="hidden" id="objeto_imp_producto" name="objeto_imp_producto" value="'.$default_objeto_imp_producto.'" placeholder="" hidden />
							<input type="hidden" id="producto_clave_servicio" name="producto_clave_servicio" value="'.$configuracion['clave_servicio'].'" placeholder="" hidden />
							<input type="hidden" id="producto_clave_unidad" name="producto_clave_unidad" value="'.$configuracion['clave_unidad'].'" placeholder="" hidden />
							<input type="hidden" id="producto_unidad_medida" name="producto_unidad_medida" value="'.$configuracion['unidad_medida'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_regimen_fiscal" name="cfdi_regimen_fiscal" value="'.$configuracion['regimen_fiscal'].'" placeholder="" hidden />
							<input type="hidden" id="producto_clave_producto" name="producto_clave_producto" value="'.$configuracion['clave_producto'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_clave_confirmacion" name="cfdi_clave_confirmacion" value="'.$configuracion['clave_confirmacion'].'" placeholder="" hidden />
							<input type="hidden" id="producto_numero_pedimento" name="producto_numero_pedimento" value="'.$configuracion['numero_pedimento'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_moneda" name="cfdi_moneda" value="'.$configuracion['moneda'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_tipo_cambio" name="cfdi_tipo_cambio" value="'.$configuracion['tipo_cambio'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_observacion" name="cfdi_observacion" value="'.$configuracion['observacion'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_precision_decimal" name="cfdi_precision_decimal" value="'.$configuracion['precision_decimal'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_manejo_impuestos_pedido" name="cfdi_manejo_impuestos_pedido" value="'.$configuracion['manejo_impuestos_pedido'].'" placeholder="" hidden />
							<input type="hidden" id="shipping_clave_servicio" name="shipping_clave_servicio" value="'.$configuracion['clave_servicio_shipping'].'" placeholder="" hidden />
							<input type="hidden" id="shipping_clave_unidad" name="shipping_clave_unidad" value="'.$configuracion['clave_unidad_shipping'].'" placeholder="" hidden />
							<input type="hidden" id="shipping_unidad_medida" name="shipping_unidad_medida" value="'.$configuracion['unidad_medida_shipping'].'" placeholder="" hidden />
							<input type="hidden" id="shipping_clave_producto" name="shipping_clave_producto" value="'.$configuracion['clave_producto_shipping'].'" placeholder="" hidden />
							<input type="hidden" id="shipping_numero_pedimento" name="shipping_numero_pedimento" value="'.$configuracion['numero_pedimento_shipping'].'" placeholder="" hidden />
							<input type="hidden" id="shipping_config_principal" name="shipping_config_principal" value="'.$configuracion['config_principal_shipping'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_huso_horario" name="cfdi_huso_horario" value="'.$configuracion['huso_horario'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_domicilio_receptor" name="cfdi_domicilio_receptor" value="'.$configuracion['domicilio_receptor'].'" placeholder="" hidden />
							<input type="hidden" id="mostrarMensajeErrorCliente" name="mostrarMensajeErrorCliente" value="'.$configuracion['mostrarMensajeErrorCliente'].'" placeholder="" hidden />
							<input type="hidden" id="mensajeErrorCliente" name="mensajeErrorCliente" value="'.$configuracion['mensajeErrorCliente'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_complementoCFDI" name="cfdi_complementoCFDI" value="'.$configuracion['complementoCFDI'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_complementoCFDI_iedu_configuracion_nivel" name="cfdi_complementoCFDI_iedu_configuracion_nivel" value="'.$configuracion['complementoCFDI_iedu_configuracion_nivel'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_complementoCFDI_iedu_configuracion_autRVOE" name="cfdi_complementoCFDI_iedu_configuracion_autRVOE" value="'.$configuracion['complementoCFDI_iedu_configuracion_autRVOE'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_color_texto_controles_formulario" name="cfdi_color_texto_controles_formulario" value="'.$configuracion['color_texto_controles_formulario'].'" placeholder="" hidden />
							<input type="hidden" id="cfdi_url_imagen_eliminar" name="cfdi_url_imagen_eliminar" value="'.plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cancelar.gif".'" placeholder="" hidden />
							<font color="'.$configuracion['color_texto_formulario'].'"><b><div id="numero_pedido_paso_3" style="text-align: left;"></div></b></font>
							<br/>
                            <form name="paso_tres_formulario" id="paso_tres_formulario" action="'.esc_url(get_permalink()).'" method="post">
								<table width="100%">
									<tr>
										<td style="vertical-align:top; width:50%; text-align: left;">
											<font color="'.$configuracion['color_texto_formulario'].'" size="2"><div id="paso_3_datos_emisor"></div></font>
										</td>
										<td style="vertical-align:top; width:50%; text-align: left;">
											<font color="'.$configuracion['color_texto_formulario'].'" size="2"><div id="paso_3_datos_receptor"></div></font>
										</td>
									</tr>
								</table>
								<table width="100%">';
								
								if($metodosPagoHabilitadoLB == '' && $usoCFDIHabilitadoLB == '')
									$formulario .= '<tr hidden>';
								else
									$formulario .= '<tr>';
								
								$formulario .= '<td style="vertical-align:top; width:60%; text-align: left;">
											<font color="'.$configuracion['color_texto_formulario'].'" size="2"><label>'.$metodosPagoHabilitadoLB.'</label></font>
											<font color="'.$configuracion['color_texto_formulario'].'" size="2">
												<select id="paso_3_metodos_pago" style="width: 90%; color: '.$configuracion['color_texto_controles_formulario'].';" '.$metodosPagoHabilitado.'>'.$opcionesMetodoPago.'</select>
											</font>
										</td>
										<td style="vertical-align:top; width:40%; text-align: left;">
											<font color="'.$configuracion['color_texto_formulario'].'" size="2"><label>'.$usoCFDIHabilitadoLB.'</label></font>
											<font color="'.$configuracion['color_texto_formulario'].'" size="2">
												<select id="paso_3_uso_cfdi" style="width: 90%; color: '.$configuracion['color_texto_controles_formulario'].';" '.$usoCFDIHabilitado.'>'.$opcionesUsoCFDI.'</select>
											</font>
										</td>';
									
									$formulario .= '</tr>';
									
								if($metodosPagoHabilitado33LB == '')
									$formulario .= '<tr hidden>';
								else
									$formulario .= '<tr>';
								
								$formulario .= '<td style="vertical-align:top; width:60%; text-align: left;">
											<font color="'.$configuracion['color_texto_formulario'].'" size="2"><label>'.$metodosPagoHabilitado33LB.'</label></font>
											<font color="'.$configuracion['color_texto_formulario'].'" size="2">
												<select id="paso_3_metodos_pago33" style="width: 90%; color: '.$configuracion['color_texto_controles_formulario'].';" '.$metodosPagoHabilitado33.'>'.$opcionesMetodoPago33.'</select>
											</font>
										</td></tr>
								</table>
								<div style="width: 100%;">
									<font color="'.$configuracion['color_texto_formulario'].'" size="2">
										<div id="conceptos_tabla">
										</div>
									</font>
								</div>
								<font color="'.$configuracion['color_texto_formulario'].'" size="2">
								<div>
									<table border="0" style="background-color:'.$configuracion['color_fondo_formulario'].';" width="100%">
										<tbody id="totales_cuerpo_tabla">
										</tbody>
									</table>
								</div>';
								
								$formulario .= $formularioComplementoCFDI;
								
								$formulario .= '</font>
								<div id="notaImportanteCFDI"></div><br/>
								<div>
									<input type="button" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="paso_tres_boton_regresar" name="paso_tres_boton_regresar" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Regresar':'Back').'" />
									<input type="button" style="background-color: '.$configuracion['color_boton_vistaprevia'].'; color:'.$configuracion['color_texto_boton_vistaprevia'].';" class="boton" id="paso_tres_boton_vistaprevia" name="paso_tres_boton_vistaprevia" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Descargar Vista Previa':'Download Preview').'" />
									<input type="button" style="background-color: '.$configuracion['color_boton_generarcfdi'].'; color:'.$configuracion['color_texto_boton_generarcfdi'].';" class="boton" id="paso_tres_boton_generar" name="paso_tres_boton_generar" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Generar CFDI':'Generate CFDI').'" />
									<img id="cargandoPaso3" src="'.plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif".'" alt="Cargando" height="32" width="32" style="visibility: hidden;">
								</div>
							</form>
							<br/>
						</div>
						<br/><br/>
					</div>
					<div id="paso_cuatro" style="width: 100%;">
                        <div style="background:'.$configuracion['color_fondo_encabezado'].'; height: 80px; line-height: 20px; margin-bottom: 0px;">
                            <br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="6">'.(($idiomaRVLFECFDI == 'ES') ? 'Paso 4/4':'Step 4/4').'</font>
							<br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="4">'.(($idiomaRVLFECFDI == 'ES') ? 'Descargar CFDI':'Download CFDI').'</font>
                        </div>
                        <div style="background-color:'.$configuracion['color_fondo_formulario'].';">
							<br/>
							<p><font color="'.$configuracion['color_texto_formulario'].'" size="2">'.(($idiomaRVLFECFDI == 'ES') ? 'Este pedido ha sido facturado':'This order has been invoiced').'</font></p>
                            <form name="paso_cuatro_formulario" id="paso_cuatro_formulario" action="'.esc_url(get_permalink()).'" method="post">
                                <font color="'.$configuracion['color_texto_formulario'].'"><b><label>'.(($idiomaRVLFECFDI == 'ES') ? 'Archivo XML':'XML file').'</label></b></font><br/>
								<a href="#" id="paso_cuatro_boton_xml" target="_blank">'.(($idiomaRVLFECFDI == 'ES') ? 'Descargar':'Download').'</a><br/>
                                <br/><font color="'.$configuracion['color_texto_formulario'].'"><b><label>'.(($idiomaRVLFECFDI == 'ES') ? 'Archivo PDF':'PDF file').'</label></b></font><br/>
								<a href="#" id="paso_cuatro_boton_pdf" target="_blank">'.(($idiomaRVLFECFDI == 'ES') ? 'Descargar':'Download').'</a><br/>
								<br/>
								<div>
									<input type="button" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="paso_cuatro_boton_regresar" name="paso_cuatro_boton_regresar" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Salir':'Exit').'" />
								</div>
							</form>
							<br/>
						</div>
						<br/><br/>
					</div>
					<div id="paso_cinco" style="width: 100%;">
                        <div style="background:'.$configuracion['color_fondo_encabezado'].'; height: 80px; line-height: 20px; margin-bottom: 0px;">
							<br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="6">'.(($idiomaRVLFECFDI == 'ES') ? 'Descargar CFDI':'Download CFDI').'</font>
                        </div>
                        <div style="background-color:'.$configuracion['color_fondo_formulario'].';">
							<br/>
							<p><font color="'.$configuracion['color_texto_formulario'].'" size="2">'.(($idiomaRVLFECFDI == 'ES') ? 'Este pedido ya fue facturado':'This order has already been invoiced').'</font></p>
                            <form name="paso_cinco_formulario" id="paso_cinco_formulario" action="'.esc_url(get_permalink()).'" method="post">
                                <font color="'.$configuracion['color_texto_formulario'].'"><b><label>'.(($idiomaRVLFECFDI == 'ES') ? 'Archivo XML':'XML file').'</label></b></font><br/>
								<a href="#" id="paso_cinco_boton_xml" target="_blank">'.(($idiomaRVLFECFDI == 'ES') ? 'Descargar':'Download').'</a><br/>
                                <br/><font color="'.$configuracion['color_texto_formulario'].'"><b><label>'.(($idiomaRVLFECFDI == 'ES') ? 'Archivo PDF':'PDF file').'</label></b></font><br/>
								<a href="#" id="paso_cinco_boton_pdf" target="_blank">'.(($idiomaRVLFECFDI == 'ES') ? 'Descargar':'Download').'</a><br/>
								<br/>
								<div>
									<input type="button" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="paso_cinco_boton_regresar" name="paso_cinco_boton_regresar" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Salir':'Exit').'" />
								</div>
							</form>
							<br/>
						</div>
						<br/><br/>
					</div>
                </div></center>
				
				<div id="ventanaModal" class="modal">
					<div class="modal-content">
						<span class="close">&times;</span>
						<br/>
						<center>
							<font color="#000000" size="5"><b>
								<div id="tituloModal"></div>
							</b></font>
							<br/>
							<font color="#000000" size="3">
								<div id="textoModal"></div>
							</font>
							<br/>
							<input type="button" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="botonModal" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Aceptar':'Accept').'" />
						</center>
					</div>
				</div>
				
				<div id="ventanaModalTimbrar" class="modalTimbrar">
					<div class="modal-contentTimbrar">
						<span class="closeTimbrar">&times;</span>
						<br/>
						<center>
							<font color="#000000" size="5"><b>
								<div id="tituloModalTimbrar">'.(($idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice').'</div>
							</b></font>
							<br/>
							<font color="#000000" size="3">
								<div id="textoModalTimbrar">'.(($idiomaRVLFECFDI == 'ES') ? '¿Deseas generar este CFDI?':'Do you want to generate this CFDI?').'</div>
							</font>
							<br/>
							<input type="button" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="botonModalTimbrarSi" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Sí':'Yes').'" />
							<input type="button" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="botonModalTimbrarNo" value="No" />
						</center>
					</div>
				</div>
				
				<script type="text/javascript">
				jQuery(document).ready(function($)
				{
					$("#paso_tres_boton_generar").click(function(event)
					{
						mostrarVentanaTimbrar();
					});
					
					try
					{
						var modalTimbrar = document.getElementById("ventanaModalTimbrar");
						var spanTimbrar = document.getElementsByClassName("closeTimbrar")[0];
						var botonTimbrarSi = document.getElementById("botonModalTimbrarSi");
						var botonTimbrarNo = document.getElementById("botonModalTimbrarNo");
						
						function mostrarVentanaTimbrar()
						{
							modalTimbrar.style.display = "block";
						}

						botonTimbrarSi.onclick = function()
						{
							modalTimbrar.style.display = "none";
						}
						
						botonTimbrarNo.onclick = function()
						{
							modalTimbrar.style.display = "none";
						}
						
						spanTimbrar.onclick = function()
						{
							modalTimbrar.style.display = "none";
						}
					}
					catch(error)
					{
						
					}
				});
				</script>';
	
	if($numeroPedido == 0)
		return $formulario;
	else
		echo $formulario;
}

add_action('wp_ajax_realvirtual_woocommerce_paso_uno', 'realvirtual_woocommerce_paso_uno_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_paso_uno', 'realvirtual_woocommerce_paso_uno_callback');

function realvirtual_woocommerce_paso_uno_callback()
{
    global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
    
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$numero_pedido = sanitize_text_field($_POST['numero_pedido']);
	update_post_meta($post->ID, 'numero_pedido', $numero_pedido);
	
	$monto_pedido = sanitize_text_field($_POST['monto_pedido']);
	update_post_meta($post->ID, 'monto_pedido', $monto_pedido);
	
	$campoExtra1 = sanitize_text_field($_POST['campoExtra1']);
	update_post_meta($post->ID, 'campoExtra1', $campoExtra1);
	
	$campoExtra2 = sanitize_text_field($_POST['campoExtra2']);
	update_post_meta($post->ID, 'campoExtra2', $campoExtra2);
	
	$textoCampoExtra1 = sanitize_text_field($_POST['textoCampoExtra1']);
	update_post_meta($post->ID, 'textoCampoExtra1', $textoCampoExtra1);
	
	$textoCampoExtra2 = sanitize_text_field($_POST['textoCampoExtra2']);
	update_post_meta($post->ID, 'textoCampoExtra2', $textoCampoExtra2);
	
	$numero_pedido 	= trim($_POST['numero_pedido']);
	$monto_pedido 	= trim($_POST['monto_pedido']);
	$campoExtra1 	= trim($_POST['campoExtra1']);
	$campoExtra2 	= trim($_POST['campoExtra2']);
	$textoCampoExtra1 	= trim($_POST['textoCampoExtra1']);
	$textoCampoExtra2 	= trim($_POST['textoCampoExtra2']);
	
	/*if(!intval($numero_pedido))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El número de pedido no es válido.':'The order number is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}*/
	
	if(!is_numeric($monto_pedido))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El monto del pedido no es válido.':'The order amount is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!isset($numero_pedido))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se ha recibido el número del pedido.':'Order number not received.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	else if(!isset($monto_pedido))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se ha recibido el monto del pedido.':'Order amount not received.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	else
	{
		$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
		$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
		$configuracionIntegracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
		
		$tipoConsulta = '1';
		
		if(isset($configuracionIntegracion['ci_consultarPedidos_tipo_consulta']))
		{
			if($configuracionIntegracion['ci_consultarPedidos_tipo_consulta'] != '')
				$tipoConsulta = $configuracionIntegracion['ci_consultarPedidos_tipo_consulta'];
		}
		
		if($tipoConsulta == '0' || $tipoConsulta == '2' || $tipoConsulta == '3')
		{
			if($configuracionIntegracion['ci_consultarPedidos_parametro_extra1_estado'] == '1')
			{
				if(!isset($campoExtra1))
				{
					$respuesta = array
					(
						'success' => false,
						'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se ha recibido el dato para el campo '.$textoCampoExtra1.'.':'The data for the '.$textoCampoExtra1.' field has not been received.'
					);
					
					echo json_encode($respuesta, JSON_PRETTY_PRINT);
					wp_die();
				}
			}
			
			if($configuracionIntegracion['ci_consultarPedidos_parametro_extra2_estado'] == '1')
			{
				if(!isset($campoExtra2))
				{
					$respuesta = array
					(
						'success' => false,
						'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se ha recibido el dato para el campo '.$textoCampoExtra2.'.':'The data for the '.$textoCampoExtra1.' field has not been received.'
					);
					
					echo json_encode($respuesta, JSON_PRETTY_PRINT);
					wp_die();
				}
			}
		}
		
		if($tipoConsulta == '0' || $tipoConsulta == '3')
		{
			$tipoConexion = $configuracionIntegracion['ci_consultarPedidos_tipo_conexion'];
			$tipoSolicitud = $configuracionIntegracion['ci_consultarPedidos_tipo_solicitud'];
			$url = $configuracionIntegracion['ci_consultarPedidos_url'];
			$nombreParametroNumeroPedido = $configuracionIntegracion['ci_consultarPedidos_nombre_parametro_numeropedido'];
			$nombreParametroMonto = $configuracionIntegracion['ci_consultarPedidos_nombre_parametro_monto'];
			$valorParametroExtra1 = $campoExtra1;
			$nombreParametroExtra1 = $configuracionIntegracion['ci_consultarPedidos_parametro_extra1_nombreinterno'];
			$valorParametroExtra2 = $campoExtra2;
			$nombreParametroExtra2 = $configuracionIntegracion['ci_consultarPedidos_parametro_extra2_nombreinterno'];
			
			$datosPedido = RealVirtualWooCommercePedido::obtenerPedidoExterno
			(
				$configuracion['precision_decimal'],
				$tipoConexion,
				$tipoSolicitud,
				$url,
				$numero_pedido,
				$nombreParametroNumeroPedido,
				$monto_pedido,
				$nombreParametroMonto,
				$valorParametroExtra1,
				$nombreParametroExtra1,
				$valorParametroExtra2,
				$nombreParametroExtra2,
				$idiomaRVLFECFDI,
				$cuenta['rfc'],
				$cuenta['usuario'],
				$cuenta['clave'],
				$urlSistemaAsociado
			);
			
			if(!isset($datosPedido))
			{
				$respuesta = array
				(
					'success' => false,
					'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se pudo recibir la respuesta de la consulta del pedido por parte del servicio externo.' :'Could not receive the response of the request of the order by the external service.'
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
				return;
			}
			
			if($datosPedido->mensajeError != '')
			{
				$respuesta = array
				(
					'success' => false,
					'message' => $datosPedido->mensajeError
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
				return;
			}
			
			/*
			if($datosPedido->statusPedido == false)
			{
				if($tipoConsulta == '0')
				{
					$respuesta = array
					(
						'success' => false,
						'message' => ($idiomaRVLFECFDI == 'ES') ? 'No existe ningún pedido con el número "'.$numero_pedido.'".' : 'There is no order with the number "'.$numero_pedido.'".'
					);
					
					echo json_encode($respuesta, JSON_PRETTY_PRINT);
					wp_die();
					return;
				}
				else if($tipoConsulta == '3')
				{
					
				}
			}
			*/
			$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
			$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
			$datosCFDI = RealVirtualWooCommercePedido::obtenerCFDIID($datosPedido->id, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
			
			if($datosCFDI->success == true)
			{
				$respuesta = array
				(
					'success' => false,
					'message' => ($idiomaRVLFECFDI == 'ES') ? 'Este pedido se encuentra facturado.':'This order is invoiced.',
					'numero_pedido' => $datosPedido->id,
					'CFDI_ID' => $datosCFDI->CFDI_ID,
					'urlSistemaAsociado' => $urlSistemaAsociado
				);
					
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
				return;
			}
			else
			{
				if($datosCFDI->codigo == '-1')
				{
					$respuesta = array
					(
						'success' => false,
						'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al realizar la operación. '.$datosCFDI->message : 'An error occurred while performing the operation. '.$datosCFDI->message,
						'numero_pedido' => $datosPedido->id,
						'CFDI_ID' => '0',
						'urlSistemaAsociado' => $urlSistemaAsociado
					);
				}
				else if($datosCFDI->codigo == '-2')
				{
					//No facturado
					$receptor_rfc = '';
					$receptor_razon_social = '';
					$receptor_domicilioFiscalReceptor = '';
					$receptor_regimenfiscal = '';
					$usoCFDIReceptor = '';
					$formaPagoReceptor = '';
					$metodoPagoReceptor = '';
					
					$idUser = get_current_user_id();
					$datosFiscales = obtenerDatosFiscales($idUser);
					
					if(isset($datosFiscales->rfc))
					{
						$receptor_rfc = $datosFiscales->rfc;
						$receptor_razon_social = $datosFiscales->razon_social;
						$receptor_domicilioFiscalReceptor = $datosFiscales->domicilio_fiscal;
						$receptor_regimenfiscal = $datosFiscales->regimen_fiscal;
						$usoCFDIReceptor = $datosFiscales->uso_cfdi;
						$formaPagoReceptor = $datosFiscales->forma_pago;
						$metodoPagoReceptor = $datosFiscales->metodo_pago;
					}
					
					$respuesta = array
					(
						'success' => true,
						'datosPedido' => $datosPedido,
						'numero_pedido' => $datosPedido->id,
						'plugin_dir_url' => plugin_dir_url( __FILE__ ),
						'urlSistemaAsociado' => $urlSistemaAsociado,
						'receptor_rfc' => $receptor_rfc,
						'receptor_razon_social' => $receptor_razon_social,
						'receptor_domicilioFiscalReceptor' => $receptor_domicilioFiscalReceptor,
						'receptor_regimenfiscal' => $receptor_regimenfiscal,
						'usoCFDIReceptor' => $usoCFDIReceptor,
						'formaPagoReceptor' => $formaPagoReceptor,
						'metodoPagoReceptor' => $metodoPagoReceptor,
						'versionCFDI' => $configuracion['version_cfdi']
					);
				}
				else if($datosCFDI->codigo == '-4' || $datosCFDI->codigo == '-5') //Factura previamente emitida y cancelada
				{
					$respuesta = array
					(
						'success' => false,
						'message' => $datosCFDI->message,
						'numero_pedido' => $datosPedido->id,
						'CFDI_ID' => '0',
						'urlSistemaAsociado' => $urlSistemaAsociado
					);
				}
				else
				{
					$respuesta = array
					(
						'success' => false,
						'message' => ($idiomaRVLFECFDI == 'ES') ? 'Este pedido se encuentra facturado pero no se pudo recuperar el CFDI para descargar en XML y PDF. '.$datosCFDI->message : 'This order is invoiced but the CFDI could not be retrieved for download in XML and PDF. '.$datosCFDI->message,
						'numero_pedido' => $datosPedido->id,
						'CFDI_ID' => '0',
						'urlSistemaAsociado' => $urlSistemaAsociado
					);
				}
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
				return;
			}
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
			return;
		}
		else if($tipoConsulta == '1' || $tipoConsulta == '2')
		{
			$datosPedido = RealVirtualWooCommercePedido::obtenerPedido
			(
				$numero_pedido,
				$configuracion['precision_decimal']
			);
		
			$receptor_rfc = '';
			$receptor_razon_social = '';
			$receptor_domicilioFiscalReceptor = '';
			$receptor_regimenfiscal = '';
			$usoCFDIReceptor = '';
			$formaPagoReceptor = '';
			$metodoPagoReceptor = '';
			
			$idUser = get_current_user_id();
			$datosFiscales = obtenerDatosFiscales($idUser);
			
			if(isset($datosFiscales->rfc))
			{
				$receptor_rfc = $datosFiscales->rfc;
				$receptor_razon_social = $datosFiscales->razon_social;
				$receptor_domicilioFiscalReceptor = $datosFiscales->domicilio_fiscal;
				$receptor_regimenfiscal = $datosFiscales->regimen_fiscal;
				$usoCFDIReceptor = $datosFiscales->uso_cfdi;
				$formaPagoReceptor = $datosFiscales->forma_pago;
				$metodoPagoReceptor = $datosFiscales->metodo_pago;
			}
		
			$respuesta = array
			(
				'success' => true,
				'datosPedido' => $datosPedido,
				'numero_pedido' => $datosPedido->id,
				'plugin_dir_url' => plugin_dir_url( __FILE__ ),
				'urlSistemaAsociado' => $urlSistemaAsociado,
				'receptor_rfc' => $receptor_rfc,
				'receptor_razon_social' => $receptor_razon_social,
				'receptor_domicilioFiscalReceptor' => $receptor_domicilioFiscalReceptor,
				'receptor_regimenfiscal' => $receptor_regimenfiscal,
				'usoCFDIReceptor' => $usoCFDIReceptor,
				'formaPagoReceptor' => $formaPagoReceptor,
				'metodoPagoReceptor' => $metodoPagoReceptor,
				'versionCFDI' => $configuracion['version_cfdi']
			);
			
			if($datosPedido->mensajeError != '')
			{
				$respuesta = array
				(
					'success' => false,
					'message' => $datosPedido->mensajeError
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
				return;
			}
			
			if((!isset($datosPedido->id)) || is_null($datosPedido->id) || $datosPedido->id == '' || $datosPedido->id == '0')
			{
				if($tipoConsulta == '1')
				{
					$respuesta = array
					(
						'success' => false,
						'message' => ($idiomaRVLFECFDI == 'ES') ? 'No existe ningún pedido con el número "'.$numero_pedido.'".' : 'There is no order with the number "'.$numero_pedido.'".'
					);
					
					echo json_encode($respuesta, JSON_PRETTY_PRINT);
					wp_die();
					return;
				}
				else if($tipoConsulta == '2')
				{
					$tipoConexion = $configuracionIntegracion['ci_consultarPedidos_tipo_conexion'];
					$tipoSolicitud = $configuracionIntegracion['ci_consultarPedidos_tipo_solicitud'];
					$url = $configuracionIntegracion['ci_consultarPedidos_url'];
					$nombreParametroNumeroPedido = $configuracionIntegracion['ci_consultarPedidos_nombre_parametro_numeropedido'];
					$nombreParametroMonto = $configuracionIntegracion['ci_consultarPedidos_nombre_parametro_monto'];
					$valorParametroExtra1 = $campoExtra1;
					$nombreParametroExtra1 = $configuracionIntegracion['ci_consultarPedidos_parametro_extra1_nombreinterno'];
					$valorParametroExtra2 = $campoExtra2;
					$nombreParametroExtra2 = $configuracionIntegracion['ci_consultarPedidos_parametro_extra2_nombreinterno'];
					
					$datosPedido = RealVirtualWooCommercePedido::obtenerPedidoExterno
					(
						$configuracion['precision_decimal'],
						$tipoConexion,
						$tipoSolicitud,
						$url,
						$numero_pedido,
						$nombreParametroNumeroPedido,
						$monto_pedido,
						$nombreParametroMonto,
						$valorParametroExtra1,
						$nombreParametroExtra1,
						$valorParametroExtra2,
						$nombreParametroExtra2,
						$idiomaRVLFECFDI,
						$cuenta['rfc'],
						$cuenta['usuario'],
						$cuenta['clave'],
						$urlSistemaAsociado
					);
					
					if(!isset($datosPedido))
					{
						$respuesta = array
						(
							'success' => false,
							'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se pudo recibir la respuesta de la consulta del pedido por parte del servicio externo.' :'Could not receive the response of the request of the order by the external service.'
						);
						
						echo json_encode($respuesta, JSON_PRETTY_PRINT);
						wp_die();
						return;
					}
					
					if($datosPedido->mensajeError != '')
					{
						$respuesta = array
						(
							'success' => false,
							'message' => $datosPedido->mensajeError
						);
						
						echo json_encode($respuesta, JSON_PRETTY_PRINT);
						wp_die();
						return;
					}
					
					$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
					$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
					$datosCFDI = RealVirtualWooCommercePedido::obtenerCFDIID($datosPedido->id, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
					
					if($datosCFDI->success == true)
					{
						$respuesta = array
						(
							'success' => false,
							'message' => ($idiomaRVLFECFDI == 'ES') ? 'Este pedido se encuentra facturado.':'This order is invoiced.',
							'numero_pedido' => $datosPedido->id,
							'CFDI_ID' => $datosCFDI->CFDI_ID,
							'urlSistemaAsociado' => $urlSistemaAsociado
						);
							
						echo json_encode($respuesta, JSON_PRETTY_PRINT);
						wp_die();
						return;
					}
					else
					{
						if($datosCFDI->codigo == '-1')
						{
							$respuesta = array
							(
								'success' => false,
								'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al realizar la operación. '.$datosCFDI->message : 'An error occurred while performing the operation. '.$datosCFDI->message,
								'numero_pedido' => $datosPedido->id,
								'CFDI_ID' => '0',
								'urlSistemaAsociado' => $urlSistemaAsociado
							);
						}
						else if($datosCFDI->codigo == '-2')
						{
							//No facturado
							$respuesta = array
							(
								'success' => true,
								'datosPedido' => $datosPedido,
								'numero_pedido' => $datosPedido->id,
								'plugin_dir_url' => plugin_dir_url( __FILE__ ),
								'urlSistemaAsociado' => $urlSistemaAsociado
							);
						}
						else if($datosCFDI->codigo == '-4' || $datosCFDI->codigo == '-5') //Factura previamente emitida y cancelada
						{
							$respuesta = array
							(
								'success' => false,
								'message' => $datosCFDI->message,
								'numero_pedido' => $datosPedido->id,
								'CFDI_ID' => '0',
								'urlSistemaAsociado' => $urlSistemaAsociado
							);
						}
						else
						{
							$respuesta = array
							(
								'success' => false,
								'message' => ($idiomaRVLFECFDI == 'ES') ? 'Este pedido se encuentra facturado pero no se pudo recuperar el CFDI para descargar en XML y PDF. '.$datosCFDI->message : 'This order is invoiced but the CFDI could not be retrieved for download in XML and PDF. '.$datosCFDI->message,
								'numero_pedido' => $datosPedido->id,
								'CFDI_ID' => '0',
								'urlSistemaAsociado' => $urlSistemaAsociado
							);
						}
						
						echo json_encode($respuesta, JSON_PRETTY_PRINT);
						wp_die();
						return;
					}
					
					echo json_encode($respuesta, JSON_PRETTY_PRINT);
					wp_die();
					return;
				}
			}
			else
			{
				if(!isset($datosPedido))
				{
					$respuesta = array
					(
						'success' => false,
						'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se ha recibido el pedido.' :'The order has not been received.'
					);
					
					echo json_encode($respuesta, JSON_PRETTY_PRINT);
					wp_die();
					return;
				}
				
				if($datosPedido->total != $monto_pedido)
				{
					$respuesta = array
					(
						'success' => false,
						'message' => ($idiomaRVLFECFDI == 'ES') ? 'El monto del pedido no coincide.':'The order amount does not match.'
					);
					
					echo json_encode($respuesta, JSON_PRETTY_PRINT);
					wp_die();
					return;
				}
				
				$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
				
				if($configuracion['pedido_mes_actual'] == 'no' && $datosPedido->status == 'completed')
				{
					$mesActual = date("m");
					$añoActual = date("Y");
					
					$fechaPedido = $datosPedido->completed_at;
					$fechaPedido = (string) $fechaPedido;
					
					if($fechaPedido != '' && $fechaPedido != null)
					{
						if(strlen($fechaPedido) >= 10)
						{
							$fechaPedido = substr($fechaPedido, 0, 10);
						}
						
						if(strlen($fechaPedido) == 10)
						{
							$mesPedido = substr($fechaPedido, 5, 2);
							$añoPedido = substr($fechaPedido, 0, 4);
							
							if(!($mesActual == $mesPedido && $añoActual == $añoPedido))
							{
								$respuesta = array
								(
									'success' => false,
									'message' => ($idiomaRVLFECFDI == 'ES') ? 'El pedido no se puede facturar porque fue pagado antes del mes actual. La fecha en que el pedido fue pagado es: '.$fechaPedido : 'The order cannot be invoiced because it was paid before the current month. The date the order was paid is: '.$fechaPedido
								);
								
								echo json_encode($respuesta, JSON_PRETTY_PRINT);
								wp_die();
								return;
							}
						}
						else
						{
							$respuesta = array
							(
								'success' => false,
								'message' => ($idiomaRVLFECFDI == 'ES') ? 'El pedido no se puede facturar porque fue pagado antes del mes actual. La fecha en que el pedido fue pagado es: '.$fechaPedido  : 'The order cannot be invoiced because it was paid before the current month. The date the order was paid is: '.$fechaPedido
							);
							
							echo json_encode($respuesta, JSON_PRETTY_PRINT);
							wp_die();
							return;
						}
					}
					else
					{
						$fechaPedido = ($idiomaRVLFECFDI == 'ES') ? 'Sin fecha (la orden no ha sido completada)' : 'Undated (the order has not been completed)';
						
						if(!($mesActual == $mesPedido && $añoActual == $añoPedido))
						{
							$respuesta = array
							(
								'success' => false,
								'message' => ($idiomaRVLFECFDI == 'ES') ? 'El pedido no se puede facturar porque no existe por parte del pedido en WooCommerce la fecha en que su estado cambió a <b>Completado</b> y no se puede verificar si el pedido corresponde al mes actual.' : 'The order cannot be invoiced because the date on which its status changed to <b>Completed</b> does not exist from the order in WooCommerce and it cannot be verified if the order corresponds to the current month.'
							);
							
							echo json_encode($respuesta, JSON_PRETTY_PRINT);
							wp_die();
							return;
						}
					}
				}
				
				$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
				$datosCFDI = RealVirtualWooCommercePedido::obtenerCFDIID($datosPedido->id, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
					
				if($datosCFDI->success == true)
				{
					$respuesta = array
					(
						'success' => false,
						'message' => ($idiomaRVLFECFDI == 'ES') ? 'Este pedido se encuentra facturado.':'This order is invoiced.',
						'numero_pedido' => $datosPedido->id,
						'CFDI_ID' => $datosCFDI->CFDI_ID,
						'urlSistemaAsociado' => $urlSistemaAsociado
					);
					
					echo json_encode($respuesta, JSON_PRETTY_PRINT);
					wp_die();
					return;
				}
				else
				{
					if($datosCFDI->codigo == '-1')
					{
						$respuesta = array
						(
							'success' => false,
							'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al realizar la operación. '.$datosCFDI->message : 'An error occurred while performing the operation. '.$datosCFDI->message,
							'numero_pedido' => $datosPedido->id,
							'CFDI_ID' => '0',
							'urlSistemaAsociado' => $urlSistemaAsociado
						);
					}
					else if($datosCFDI->codigo == '-2')
					{
						if($configuracion['estado_orden'] != 'cualquier-estado')
						{
							if($configuracion['estado_orden'] == 'processing-completed')
							{
								if($datosPedido->status != 'processing' && $datosPedido->status != 'completed')
								{
									$respuesta = array
									(
										'success' => false,
										'message' => ($idiomaRVLFECFDI == 'ES') ? 'Este pedido todavía no se puede facturar.':'This order can not yet be invoiced.',
										'estado_orden1' => $datosPedido->status,
										'estado_orden2' => $configuracion['estado_orden']
									);
									
									echo json_encode($respuesta, JSON_PRETTY_PRINT);
									wp_die();
									return;
								}
							}
							else if($configuracion['estado_orden'] == 'cualquier-estado-excepto')
							{
								if($datosPedido->status == 'pending' || $datosPedido->status == 'canceled'
									|| $datosPedido->status == 'refunded' || $datosPedido->status == 'failed')
								{
									$respuesta = array
									(
										'success' => false,
										'message' => ($idiomaRVLFECFDI == 'ES') ? 'Este pedido todavía no se puede facturar.':'This order can not yet be invoiced.',
										'estado_orden1' => $datosPedido->status,
										'estado_orden2' => $configuracion['estado_orden']
									);
									
									echo json_encode($respuesta, JSON_PRETTY_PRINT);
									wp_die();
									return;
								}
							}
							else
							{
								if($datosPedido->status != $configuracion['estado_orden'])
								{
									$respuesta = array
									(
										'success' => false,
										'message' => ($idiomaRVLFECFDI == 'ES') ? 'Este pedido todavía no se puede facturar.':'This order can not yet be invoiced.',
										'estado_orden1' => $datosPedido->status,
										'estado_orden2' => $configuracion['estado_orden']
									);
									
									echo json_encode($respuesta, JSON_PRETTY_PRINT);
									wp_die();
									return;
								}
							}
						}
					}
					else if($datosCFDI->codigo == '-4' || $datosCFDI->codigo == '-5')
					{
						$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
				
						if($configuracion['estado_orden_refacturacion'] != 'cualquier-estado')
						{
							if($datosPedido->status != $configuracion['estado_orden_refacturacion'])
							{
								$respuesta = array
								(
									'success' => false,
									'message' => $datosCFDI->message,
									'estado_orden1' => $datosPedido->status,
									'estado_orden_refacturacion' => $configuracion['estado_orden_refacturacion']
								);
								
								echo json_encode($respuesta, JSON_PRETTY_PRINT);
								wp_die();
								return;
							}
						}
					}
					else
					{
						$respuesta = array
						(
							'success' => false,
							'message' => ($idiomaRVLFECFDI == 'ES') ? 'Este pedido se encuentra facturado pero no se pudo recuperar el CFDI para descargar en XML y PDF. '.$datosCFDI->message : 'This order is invoiced but the CFDI could not be retrieved for download in XML and PDF. '.$datosCFDI->message,
							'numero_pedido' => $datosPedido->id,
							'CFDI_ID' => '0',
							'urlSistemaAsociado' => $urlSistemaAsociado
						);
					}
					
					echo json_encode($respuesta, JSON_PRETTY_PRINT);
					wp_die();
					return;
				}
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
				return;
			}
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
			return;
		}
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
}

add_action('wp_ajax_realvirtual_woocommerce_paso_dos_buscar_cliente', 'realvirtual_woocommerce_paso_dos_buscar_cliente_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_paso_dos_buscar_cliente', 'realvirtual_woocommerce_paso_dos_buscar_cliente_callback');

function realvirtual_woocommerce_paso_dos_buscar_cliente_callback()
{
    global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
    
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$receptor_rfc = sanitize_text_field($_POST['receptor_rfc']);
	update_post_meta($post->ID, 'receptor_rfc', $receptor_rfc);
	
	$receptor_email = sanitize_text_field($_POST['receptor_email']);
	update_post_meta($post->ID, 'receptor_email', $receptor_email);
	
	$receptor_rfc 	= trim($_POST['receptor_rfc']);
	$receptor_email = trim($_POST['receptor_email']);
	
	if(!preg_match("/^([A-Z]|&|Ñ){3,4}[0-9]{2}[0-1][0-9][0-3][0-9]([A-Z]|[0-9]){2}([0-9]|A){1}$/", $receptor_rfc))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El RFC del Receptor tiene un formato inválido.':'The Receiver RFC has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!filter_var($receptor_email, FILTER_VALIDATE_EMAIL))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El correo electrónico del Receptor tiene un formato inválido.':'The Receiver E-mail has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$respuesta = array
	(
		'success' => true,
		'message' => 'OK'
	);
		
	if(!isset($receptor_rfc))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se ha recibido el RFC del cliente.':'The RFC of the customer has not been received.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	else if(!isset($receptor_email))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se ha recibido el E-mail del cliente.':'The E-mail of the customer has not been received.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	else
	{
		$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
		$datosCliente = RealVirtualWooCommerceCliente::obtenerCliente($receptor_rfc, $receptor_email, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
		
		if($datosCliente->success == false)
		{
			$respuesta = array
			(
				'success' => false,
				'message' => $datosCliente->message
			);
		}
		else
		{
			$respuesta = array
			(
				'success' => true,
				'receptor_id' => trim($datosCliente->RECEPTOR_ID),
				'receptor_rfc' => trim($datosCliente->RECEPTOR_RFC),
				'receptor_razon_social' => trim($datosCliente->RECEPTOR_NOMBRE),
				'receptor_email' => trim($datosCliente->RECEPTOR_EMAIL)
			);
		}
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
}

add_action('wp_ajax_realvirtual_woocommerce_paso_tres_buscar_emisor', 'realvirtual_woocommerce_paso_tres_buscar_emisor_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_paso_tres_buscar_emisor', 'realvirtual_woocommerce_paso_tres_buscar_emisor_callback');

function realvirtual_woocommerce_paso_tres_buscar_emisor_callback()
{
    global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema;
    
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$idiomaRVLFECFDI = $configuracion['idioma'];
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$datosEmisor = RealVirtualWooCommerceEmisor::obtenerEmisor($cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
	if($datosEmisor->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $datosEmisor->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'emisor_id' => trim($datosEmisor->EMISOR_ID),
			'emisor_rfc' => trim($datosEmisor->EMISOR_RFC),
			'emisor_razon_social' => trim($datosEmisor->EMISOR_NOMBRE),
			'emisor_email' => trim($datosEmisor->EMISOR_EMAIL)
		);
	}
		
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_paso_tres_metodos_pago', 'realvirtual_woocommerce_paso_tres_metodos_pago_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_paso_tres_metodos_pago', 'realvirtual_woocommerce_paso_tres_metodos_pago_callback');

function realvirtual_woocommerce_paso_tres_metodos_pago_callback()
{
    global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
    
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$datosMetodosPago = RealVirtualWooCommerceMetodoPago::obtenerCatalogoMetodosPago($cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
	if($datosMetodosPago->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $datosMetodosPago->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'registros' => $datosMetodosPago->registros
		);
	}
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_paso_tres_metodos_pago33', 'realvirtual_woocommerce_paso_tres_metodos_pago33_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_paso_tres_metodos_pago33', 'realvirtual_woocommerce_paso_tres_metodos_pago33_callback');

function realvirtual_woocommerce_paso_tres_metodos_pago33_callback()
{
    global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
    
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$datosMetodosPago = RealVirtualWooCommerceMetodoPago33::obtenerCatalogoMetodosPago33($cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
	if($datosMetodosPago->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $datosMetodosPago->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'registros' => $datosMetodosPago->registros
		);
	}
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_paso_tres_vista_previa_cfdi', 'realvirtual_woocommerce_paso_tres_vista_previa_cfdi_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_paso_tres_vista_previa_cfdi', 'realvirtual_woocommerce_paso_tres_vista_previa_cfdi_callback');

function realvirtual_woocommerce_paso_tres_vista_previa_cfdi_callback()
{
    global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
    
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$numeroPedido = sanitize_text_field($_POST['numeroPedido']);
	update_post_meta($post->ID, 'numeroPedido', $numeroPedido);
	
	$receptor_id = sanitize_text_field($_POST['receptor_id']);
	update_post_meta($post->ID, 'receptor_id', $receptor_id);
	
	$receptor_rfc = sanitize_text_field($_POST['receptor_rfc']);
	update_post_meta($post->ID, 'receptor_rfc', $receptor_rfc);
	
	$receptor_razon_social = sanitize_text_field($_POST['receptor_razon_social']);
	update_post_meta($post->ID, 'receptor_razon_social', $receptor_razon_social);
	
	$receptor_email = sanitize_text_field($_POST['receptor_email']);
	update_post_meta($post->ID, 'receptor_email', $receptor_email);
	
	$metodo_pago = sanitize_text_field($_POST['metodo_pago']);
	update_post_meta($post->ID, 'metodo_pago', $metodo_pago);
	
	$metodo_pago33 = sanitize_text_field($_POST['metodo_pago33']);
	update_post_meta($post->ID, 'metodo_pago33', $metodo_pago33);
	
	$conceptos = sanitize_text_field($_POST['conceptos']);
	update_post_meta($post->ID, 'conceptos', $conceptos);
	
	$subtotal = sanitize_text_field($_POST['subtotal']);
	update_post_meta($post->ID, 'subtotal', $subtotal);
	
	$descuento = sanitize_text_field($_POST['descuento']);
	update_post_meta($post->ID, 'descuento', $descuento);
	
	$total = sanitize_text_field($_POST['total']);
	update_post_meta($post->ID, 'total', $total);
	
	$serie = sanitize_text_field($_POST['serie']);
	update_post_meta($post->ID, 'serie', $serie);
	
	$impuesto_federal = sanitize_text_field($_POST['impuesto_federal']);
	update_post_meta($post->ID, 'impuesto_federal', $impuesto_federal);
	
	$impuesto_local = sanitize_text_field($_POST['impuesto_local']);
	update_post_meta($post->ID, 'impuesto_local', $impuesto_local);
	
	$numero_pedido = sanitize_text_field($_POST['numero_pedido']);
	update_post_meta($post->ID, 'numero_pedido', $numero_pedido);
	
	$regimen_fiscal = sanitize_text_field($_POST['regimen_fiscal']);
	update_post_meta($post->ID, 'regimen_fiscal', $regimen_fiscal);
	
	$uso_cfdi = sanitize_text_field($_POST['uso_cfdi']);
	update_post_meta($post->ID, 'uso_cfdi', $uso_cfdi);
	
	$clave_confirmacion = sanitize_text_field($_POST['clave_confirmacion']);
	update_post_meta($post->ID, 'clave_confirmacion', $clave_confirmacion);
	
	$moneda = sanitize_text_field($_POST['moneda']);
	update_post_meta($post->ID, 'moneda', $moneda);
	
	$tipo_cambio = sanitize_text_field($_POST['tipo_cambio']);
	update_post_meta($post->ID, 'tipo_cambio', $tipo_cambio);
	
	$observacion = sanitize_text_field($_POST['observacion']);
	update_post_meta($post->ID, 'observacion', $observacion);
	
	$precision_decimal = sanitize_text_field($_POST['precision_decimal']);
	update_post_meta($post->ID, 'precision_decimal', $precision_decimal);
	
	$huso_horario = sanitize_text_field($_POST['huso_horario']);
	update_post_meta($post->ID, 'huso_horario', $huso_horario);
	
	$calle_receptor = sanitize_text_field($_POST['calle_receptor']);
	update_post_meta($post->ID, 'calle_receptor', $calle_receptor);
	
	$estado_receptor = sanitize_text_field($_POST['estado_receptor']);
	update_post_meta($post->ID, 'estado_receptor', $estado_receptor);
	
	$municipio_receptor = sanitize_text_field($_POST['municipio_receptor']);
	update_post_meta($post->ID, 'municipio_receptor', $municipio_receptor);
	
	$pais_receptor = sanitize_text_field($_POST['pais_receptor']);
	update_post_meta($post->ID, 'pais_receptor', $pais_receptor);
	
	$codigoPostal_receptor = sanitize_text_field($_POST['codigoPostal_receptor']);
	update_post_meta($post->ID, 'codigoPostal_receptor', $codigoPostal_receptor);
	
	$lugarExpedicion = sanitize_text_field($_POST['lugarExpedicion']);
	update_post_meta($post->ID, 'lugarExpedicion', $lugarExpedicion);
	
	$versionCFDI = sanitize_text_field($_POST['versionCFDI']);
	update_post_meta($post->ID, 'versionCFDI', $versionCFDI);
	
	$receptor_domicilioFiscalReceptor = sanitize_text_field($_POST['receptor_domicilioFiscalReceptor']);
	update_post_meta($post->ID, 'receptor_domicilioFiscalReceptor', $receptor_domicilioFiscalReceptor);
	
	$receptor_regimenfiscal = sanitize_text_field($_POST['receptor_regimenfiscal']);
	update_post_meta($post->ID, 'receptor_regimenfiscal', $receptor_regimenfiscal);
		
	$numeroPedido 				= trim($_POST['numeroPedido']);
	$receptor_id 				= trim($_POST['receptor_id']);
	$receptor_rfc 				= trim($_POST['receptor_rfc']);
	$receptor_razon_social 		= trim($_POST['receptor_razon_social']);
	$receptor_email 			= trim($_POST['receptor_email']);
	$metodo_pago 				= trim($_POST['metodo_pago']);
	$metodo_pago33 				= trim($_POST['metodo_pago33']);
	$conceptos 					= trim($_POST['conceptos']);
	$subtotal 					= trim($_POST['subtotal']);
	$descuento 					= trim($_POST['descuento']);
	$total 						= trim($_POST['total']);
	$serie 						= trim($_POST['serie']);
	$impuesto_federal 			= trim($_POST['impuesto_federal']);
	$impuesto_local 			= trim($_POST['impuesto_local']);
	$numero_pedido				= trim($_POST['numero_pedido']);
	$uso_cfdi					= trim($_POST['uso_cfdi']);
	$regimen_fiscal				= trim($_POST['regimen_fiscal']);
	$clave_confirmacion			= trim($_POST['clave_confirmacion']);
	$moneda						= trim($_POST['moneda']);
	$tipo_cambio				= trim($_POST['tipo_cambio']);
	$observacion				= trim($_POST['observacion']);
	$precision_decimal			= trim($_POST['precision_decimal']);
	$huso_horario				= trim($_POST['huso_horario']);
	$calle_receptor				= trim($_POST['calle_receptor']);
	$estado_receptor			= trim($_POST['estado_receptor']);
	$municipio_receptor			= trim($_POST['municipio_receptor']);
	$pais_receptor				= trim($_POST['pais_receptor']);
	$codigoPostal_receptor		= trim($_POST['codigoPostal_receptor']);
	$lugarExpedicion			= trim($_POST['lugarExpedicion']);
	$versionCFDI				= trim($_POST['versionCFDI']);
	$receptor_domicilioFiscalReceptor = trim($_POST['receptor_domicilioFiscalReceptor']);
	$receptor_regimenfiscal		= trim($_POST['receptor_regimenfiscal']);
	
	if(!intval($receptor_id))
	{
		$receptor_id = '';
	}
	
	if(!preg_match("/^([A-Z]|&|Ñ){3,4}[0-9]{2}[0-1][0-9][0-3][0-9]([A-Z]|[0-9]){2}([0-9]|A){1}$/", $receptor_rfc))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El RFC del Receptor tiene un formato inválido.':'The Receiver RFC has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/^[a-zA-Z0-9\s\#\$\+\%\(\)\[\]\*\¡\!\=\\/\&\.\,\;\:\-\_\ñ\á\é\í\ó\ú\Á\É\Í\Ó\Ú\Ñ]*$/", $receptor_razon_social))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La razón social del Receptor tiene un formato inválido.':'The Receiver business name has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!filter_var($receptor_email, FILTER_VALIDATE_EMAIL))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El correo electrónico del Receptor tiene un formato inválido.':'The Receiver E-mail has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($conceptos) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No hay conceptos para el CFDI.':'There are no concepts for the CFDI.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($subtotal) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El subtotal del CFDI es inválido.':'The CFDI subtotal is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($descuento) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El descuento del CFDI es inválido.':'The CFDI discount is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($total) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El total del CFDI es inválido.':'The CFDI total is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($regimen_fiscal) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El régimen fiscal del Emisor no ha sido establecido.':'The Issuer fiscal regime has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($impuesto_federal) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No hay impuestos para el CFDI.':'There are no taxes for the CFDI.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	/*if(!intval($numero_pedido))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El número de pedido tiene un formato inválido.':'The order number has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}*/
	
	if(!strlen($moneda) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La moneda no ha sido establecida.':'The currency has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($tipo_cambio) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El tipo de cambio para la moneda del CFDI no ha sido establecido.':'The exchange rate for the CFDI currency has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(is_numeric($tipo_cambio) == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El tipo de cambio debe ser un valor numérico.':'The exchange rate must be a numeric value.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($precision_decimal) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La precisión decimal del CFDI no ha sido establecida.':'The decimal precision for the CFDI has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(is_numeric($precision_decimal) == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La precisión decimal debe ser un valor numérico.':'The decimal precision must be a numeric value.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($huso_horario) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La zona horaria no ha sido establecida.':'The time zone has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	$datosCFDI = RealVirtualWooCommerceCFDI::generarVistaPreviaCFDI
				(
					$cuenta['rfc'],
					$cuenta['usuario'],
					$cuenta['clave'],
					$receptor_id,
					$receptor_rfc,
					$receptor_razon_social,
					$receptor_email,
					$metodo_pago,
					$metodo_pago33,
					$conceptos,
					$subtotal,
					$descuento,
					$total,
					$configuracion['serie'],
					$impuesto_federal,
					$impuesto_local,
					$numero_pedido,
					$urlSistemaAsociado,
					$sistema,
					$regimen_fiscal,
					$uso_cfdi,
					$idiomaRVLFECFDI,
					$clave_confirmacion,
					$moneda,
					$tipo_cambio,
					base64_encode($observacion),
					$precision_decimal,
					$huso_horario,
					$calle_receptor,
					$estado_receptor,
					$municipio_receptor,
					$pais_receptor,
					$codigoPostal_receptor,
					$lugarExpedicion,
					$versionCFDI, 
					$receptor_domicilioFiscalReceptor,
					$receptor_regimenfiscal,
					$configuracion['exportacion_cfdi'],
					$configuracion['facAtrAdquirente'],
					$configuracion['informacionGlobal_periodicidad'],
					$configuracion['informacionGlobal_meses'],
					$configuracion['informacionGlobal_año'],
					$numeroPedido
				);
	
	if($datosCFDI->success == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => $datosCFDI->message
		);
	}
	else
	{
		$respuesta = array
		(
			'success' => true,
			'message' => $datosCFDI->message,
			'CFDI_PDF' => $datosCFDI->CFDI_PDF
		);
	}
		
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_paso_tres_generar_cfdi', 'realvirtual_woocommerce_paso_tres_generar_cfdi_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_paso_tres_generar_cfdi', 'realvirtual_woocommerce_paso_tres_generar_cfdi_callback');

function realvirtual_woocommerce_paso_tres_generar_cfdi_callback()
{
    global $wpdb, $woocommerce, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
    
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$numeroPedido = sanitize_text_field($_POST['numeroPedido']);
	update_post_meta($post->ID, 'numeroPedido', $numeroPedido);
	
	$receptor_id = sanitize_text_field($_POST['receptor_id']);
	update_post_meta($post->ID, 'receptor_id', $receptor_id);
	
	$receptor_rfc = sanitize_text_field($_POST['receptor_rfc']);
	update_post_meta($post->ID, 'receptor_rfc', $receptor_rfc);
	
	$receptor_razon_social = sanitize_text_field($_POST['receptor_razon_social']);
	update_post_meta($post->ID, 'receptor_razon_social', $receptor_razon_social);
	
	$receptor_email = sanitize_text_field($_POST['receptor_email']);
	update_post_meta($post->ID, 'receptor_email', $receptor_email);
	
	$metodo_pago = sanitize_text_field($_POST['metodo_pago']);
	update_post_meta($post->ID, 'metodo_pago', $metodo_pago);
	
	$metodo_pago33 = sanitize_text_field($_POST['metodo_pago33']);
	update_post_meta($post->ID, 'metodo_pago33', $metodo_pago33);
	
	$conceptos = sanitize_text_field($_POST['conceptos']);
	update_post_meta($post->ID, 'conceptos', $conceptos);
	
	$subtotal = sanitize_text_field($_POST['subtotal']);
	update_post_meta($post->ID, 'subtotal', $subtotal);
	
	$descuento = sanitize_text_field($_POST['descuento']);
	update_post_meta($post->ID, 'descuento', $descuento);
	
	$total = sanitize_text_field($_POST['total']);
	update_post_meta($post->ID, 'total', $total);
	
	$serie = sanitize_text_field($_POST['serie']);
	update_post_meta($post->ID, 'serie', $serie);
	
	$impuesto_federal = sanitize_text_field($_POST['impuesto_federal']);
	update_post_meta($post->ID, 'impuesto_federal', $impuesto_federal);
	
	$impuesto_local = sanitize_text_field($_POST['impuesto_local']);
	update_post_meta($post->ID, 'impuesto_local', $impuesto_local);
	
	$numero_pedido = sanitize_text_field($_POST['numero_pedido']);
	update_post_meta($post->ID, 'numero_pedido', $numero_pedido);
	
	$regimen_fiscal = sanitize_text_field($_POST['regimen_fiscal']);
	update_post_meta($post->ID, 'regimen_fiscal', $regimen_fiscal);
	
	$uso_cfdi = sanitize_text_field($_POST['uso_cfdi']);
	update_post_meta($post->ID, 'uso_cfdi', $uso_cfdi);
	
	$clave_confirmacion = sanitize_text_field($_POST['clave_confirmacion']);
	update_post_meta($post->ID, 'clave_confirmacion', $clave_confirmacion);
	
	$moneda = sanitize_text_field($_POST['moneda']);
	update_post_meta($post->ID, 'moneda', $moneda);
	
	$tipo_cambio = sanitize_text_field($_POST['tipo_cambio']);
	update_post_meta($post->ID, 'tipo_cambio', $tipo_cambio);
	
	$observacion = sanitize_text_field($_POST['observacion']);
	update_post_meta($post->ID, 'observacion', $observacion);
	
	$precision_decimal = sanitize_text_field($_POST['precision_decimal']);
	update_post_meta($post->ID, 'precision_decimal', $precision_decimal);
	
	$huso_horario = sanitize_text_field($_POST['huso_horario']);
	update_post_meta($post->ID, 'huso_horario', $huso_horario);
		
	$calle_receptor = sanitize_text_field($_POST['calle_receptor']);
	update_post_meta($post->ID, 'calle_receptor', $calle_receptor);
	
	$estado_receptor = sanitize_text_field($_POST['estado_receptor']);
	update_post_meta($post->ID, 'estado_receptor', $estado_receptor);
	
	$municipio_receptor = sanitize_text_field($_POST['municipio_receptor']);
	update_post_meta($post->ID, 'municipio_receptor', $municipio_receptor);
	
	$pais_receptor = sanitize_text_field($_POST['pais_receptor']);
	update_post_meta($post->ID, 'pais_receptor', $pais_receptor);
	
	$codigoPostal_receptor = sanitize_text_field($_POST['codigoPostal_receptor']);
	update_post_meta($post->ID, 'codigoPostal_receptor', $codigoPostal_receptor);
	
	$lugarExpedicion = sanitize_text_field($_POST['lugarExpedicion']);
	update_post_meta($post->ID, 'lugarExpedicion', $lugarExpedicion);
	
	$versionCFDI = sanitize_text_field($_POST['versionCFDI']);
	update_post_meta($post->ID, 'versionCFDI', $versionCFDI);
	
	$receptor_domicilioFiscalReceptor = sanitize_text_field($_POST['receptor_domicilioFiscalReceptor']);
	update_post_meta($post->ID, 'receptor_domicilioFiscalReceptor', $receptor_domicilioFiscalReceptor);
	
	$receptor_regimenfiscal = sanitize_text_field($_POST['receptor_regimenfiscal']);
	update_post_meta($post->ID, 'receptor_regimenfiscal', $receptor_regimenfiscal);
	
	$mostrarMensajeErrorCliente = sanitize_text_field($_POST['mostrarMensajeErrorCliente']);
	update_post_meta($post->ID, 'mostrarMensajeErrorCliente', $mostrarMensajeErrorCliente);
	
	$numeroPedido 				= trim($_POST['numeroPedido']);
	$receptor_id 				= trim($_POST['receptor_id']);
	$receptor_rfc 				= trim($_POST['receptor_rfc']);
	$receptor_razon_social 		= trim($_POST['receptor_razon_social']);
	$receptor_email 			= trim($_POST['receptor_email']);
	$metodo_pago 				= trim($_POST['metodo_pago']);
	$metodo_pago33				= trim($_POST['metodo_pago33']);
	$conceptos 					= trim($_POST['conceptos']);
	$subtotal 					= trim($_POST['subtotal']);
	$descuento 					= trim($_POST['descuento']);
	$total 						= trim($_POST['total']);
	$serie 						= trim($_POST['serie']);
	$impuesto_federal 			= trim($_POST['impuesto_federal']);
	$impuesto_local 			= trim($_POST['impuesto_local']);
	$numero_pedido				= trim($_POST['numero_pedido']);
	$uso_cfdi					= trim($_POST['uso_cfdi']);
	$regimen_fiscal				= trim($_POST['regimen_fiscal']);
	$clave_confirmacion			= trim($_POST['clave_confirmacion']);
	$moneda						= trim($_POST['moneda']);
	$tipo_cambio				= trim($_POST['tipo_cambio']);
	$observacion				= trim($_POST['observacion']);
	$precision_decimal			= trim($_POST['precision_decimal']);
	$huso_horario				= trim($_POST['huso_horario']);
	$calle_receptor				= trim($_POST['calle_receptor']);
	$estado_receptor			= trim($_POST['estado_receptor']);
	$municipio_receptor			= trim($_POST['municipio_receptor']);
	$pais_receptor				= trim($_POST['pais_receptor']);
	$codigoPostal_receptor		= trim($_POST['codigoPostal_receptor']);
	$lugarExpedicion			= trim($_POST['lugarExpedicion']);
	$versionCFDI				= trim($_POST['versionCFDI']);
	$receptor_domicilioFiscalReceptor = trim($_POST['receptor_domicilioFiscalReceptor']);
	$receptor_regimenfiscal		= trim($_POST['receptor_regimenfiscal']);
	$mostrarMensajeErrorCliente	= trim($_POST['mostrarMensajeErrorCliente']);
	
	if(!intval($receptor_id))
	{
		$receptor_id = '';
	}
	
	if(!preg_match("/^([A-Z]|&|Ñ){3,4}[0-9]{2}[0-1][0-9][0-3][0-9]([A-Z]|[0-9]){2}([0-9]|A){1}$/", $receptor_rfc))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El RFC del Receptor tiene un formato inválido.':'The Receiver RFC has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/^[a-zA-Z0-9\s\#\$\+\%\(\)\[\]\*\¡\!\=\\/\&\.\,\;\:\-\_\ñ\á\é\í\ó\ú\Á\É\Í\Ó\Ú\Ñ]*$/", $receptor_razon_social))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La razón social del Receptor tiene un formato inválido.':'The Receiver business name has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!filter_var($receptor_email, FILTER_VALIDATE_EMAIL))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El correo electrónico del Receptor tiene un formato inválido.':'The Receiver E-mail has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($conceptos) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No hay conceptos para el CFDI.':'There are no concepts for the CFDI.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($subtotal) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El subtotal del CFDI es inválido.':'The CFDI subtotal is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($descuento) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El descuento del CFDI es inválido.':'The CFDI discount is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($total) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El total del CFDI es inválido.':'The CFDI total is invalid.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($regimen_fiscal) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El régimen fiscal del Emisor no ha sido establecido.':'The Issuer fiscal regime has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($impuesto_federal) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No hay impuestos para el CFDI.':'There are no taxes for the CFDI.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	/*if(!intval($numero_pedido))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El número de pedido tiene un formato inválido.':'The order number has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}*/
	
	if(!strlen($moneda) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La moneda no ha sido establecida.':'The currency has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($tipo_cambio) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El tipo de cambio para la moneda del CFDI no ha sido establecido.':'The exchange rate for the CFDI currency has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(is_numeric($tipo_cambio) == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El tipo de cambio debe ser un valor numérico.':'The exchange rate must be a numeric value.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($precision_decimal) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La precisión decimal del CFDI no ha sido establecido.':'The decimal precision for the CFDI has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(is_numeric($precision_decimal) == false)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La precisión decimal debe ser un valor numérico.':'The decimal precision must be a numeric value.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!strlen($huso_horario) > 0)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La zona horaria no ha sido establecida.':'The time zone has not been established.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$emailAdminNotificacionError = $configuracion['emailNotificacionErrorModuloClientes'];
	
	$datosCFDI = RealVirtualWooCommerceCFDI::generarCFDI
	(
		$cuenta['rfc'],
		$cuenta['usuario'],
		$cuenta['clave'],
		$receptor_id,
		$receptor_rfc,
		$receptor_razon_social,
		$receptor_email,
		$metodo_pago,
		$metodo_pago33,
		$conceptos,
		$subtotal,
		$descuento,
		$total,
		$configuracion['serie'],
		$impuesto_federal,
		$impuesto_local,
		$numero_pedido,
		$urlSistemaAsociado,
		$sistema,
		$regimen_fiscal,
		$uso_cfdi,
		$idiomaRVLFECFDI,
		$clave_confirmacion,
		$moneda,
		$tipo_cambio,
		base64_encode($observacion),
		$precision_decimal,
		$huso_horario,
		$calle_receptor,
		$estado_receptor,
		$municipio_receptor,
		$pais_receptor,
		$codigoPostal_receptor,
		$lugarExpedicion,
		$versionCFDI, 
		$receptor_domicilioFiscalReceptor,
		$receptor_regimenfiscal,
		$configuracion['exportacion_cfdi'],
		$configuracion['facAtrAdquirente'],
		$configuracion['informacionGlobal_periodicidad'],
		$configuracion['informacionGlobal_meses'],
		$configuracion['informacionGlobal_año'],
		$numeroPedido,
		$configuracion['facAtrAdquirente']
	);
	
	if($datosCFDI->success == false)
	{
		if($mostrarMensajeErrorCliente == 'si')
		{
			$title = "Un cliente no pudo emitir su CFDI del pedido ".$numero_pedido;
			$body_message = "Ocurrió un error al emitir el CFDI del pedido <b>".$numero_pedido."</b>";
			
			$mailer = $woocommerce->mailer();
			
			$message = $mailer->wrap_message
			(
				$body_message,
				$body_message."<br/><br/>El cliente no pudo emitir su CFDI desde el portal de facturación para clientes debido a un error:<br/><br/><b>Descripción del error:</b> ".$datosCFDI->message
			);
			
			$mailer->send($emailAdminNotificacionError, $title, $message);
			unset($mailer);
		}
		
		$respuesta = array
		(
			'success' => false,
			'message' => $datosCFDI->message
		);
	}
	else
	{
		$configuracionIN = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
		$resultadoEnvioXML = '';
		
		rvutils_woocommerce_enviar_xml_pedido_timbrado($numero_pedido, $datosCFDI->XML);
		
		$respuesta = array
		(
			'success' => true,
			'message' => $datosCFDI->message,
			'LAYOUT' => $datosCFDI->LAYOUT,
			'XML' => $datosCFDI->XML,
			'CFDI_ID' => $datosCFDI->CFDI_ID,
			'RESULTADO_ENVIO_XML' => $resultadoEnvioXML,
			'RFC' => $cuenta['rfc'],
			'USUARIO' => $cuenta['usuario'],
			'CLAVE' => $cuenta['clave'],
			'TIPO_CONEXION' => $configuracionIN['ci_enviarXml_tipo_conexion'],
			'TIPO_SOLICITUD' => $configuracionIN['ci_enviarXml_tipo_solicitud'],
			'URL' => $configuracionIN['ci_enviarXml_url'],
			'ID' => $numero_pedido,
			'XMLBase64' => $datosCFDI->XML
		);
		
		$order = new WC_Order($numero_pedido);
		$jsonArticulos = $datosCFDI->ARTICULOS;
		$jsonArticulosNuevo = array();
		
		for($i = 0; $i < count($jsonArticulos); $i++)
		{
			$nombre = $jsonArticulos[$i]->Nombre;
			$importe = $jsonArticulos[$i]->Importe;
			
			foreach($order->get_items() as $item_id => $item)
			{
				$product = $order->get_product_from_item($item);
				$name = $item->get_name();
				
				if($nombre == $name)
				{
					$indicador_impuesto = $product->get_attribute('indicador_impuesto');
					$articulo = $product->get_attribute('articulo');
					$material = $product->get_attribute('material');
					$jsonArticuloNuevo = array("Nombre" => $nombre, "Importe" => $importe, "Indicador_Impuesto" => $indicador_impuesto, "Articulo" => $articulo, "Material" => $material);
					$jsonArticulosNuevo[$i] = $jsonArticuloNuevo;
					break;
				}
			}
		}
		
		update_post_meta($order->get_id(), 'CFDI_UUID', $datosCFDI->UUID);
		update_post_meta($order->get_id(), 'CFDI_RFC_RECEPTOR', $datosCFDI->RFC_RECEPTOR);
		update_post_meta($order->get_id(), 'CFDI_SERIE', $datosCFDI->SERIE);
		update_post_meta($order->get_id(), 'CFDI_FOLIO', $datosCFDI->FOLIO);
		update_post_meta($order->get_id(), 'CFDI_TOTAL', $datosCFDI->TOTAL);
		update_post_meta($order->get_id(), 'CFDI_FECHA', $datosCFDI->FECHA);
		update_post_meta($order->get_id(), 'CFDI_MONEDA', $datosCFDI->MONEDA);
		update_post_meta($order->get_id(), 'CFDI_ARTICULOS', json_encode($jsonArticulosNuevo));
		
		//$datosPedido = RealVirtualWooCommercePedido::obtenerPedido($numero_pedido);
		//$pedido = new WC_Order($datosPedido->id);
        //$pedido->update_status('wc-invoiced', '');
		//$pedido->update_status('wc-completed', '');
	}
		
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function rvcfdi_woocommerce_order_status_changed($order_id)
{
	rvutils_woocommerce_enviar_pedido_cambio_de_estado($order_id);
	rvcfdi_woocommerce_cfdi_automatico($order_id);
}

function rvutils_woocommerce_enviar_pedido_creado($order_id)
{
	global $idiomaRVLFECFDI, $urlServicio;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	
	$mensajeErrorConfiguracion = '';
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo enviar el pedido al ser creado por un error en la configuración de la sección <b>Mi Cuenta</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The order could not be sent when it was created due to an error in the configuration of the <b>My Account</b> section. Please reconfigure this section to correct this problem for future orders.';
	}
	
	if(!($configuracion['ci_enviarPedidosCrear_tipo_conexion'] != '' && $configuracion['ci_enviarPedidosCrear_tipo_solicitud'] != '' && $configuracion['ci_enviarPedidosCrear_url'] != '' && $configuracion['ci_enviarPedidosCrear_tipo_consulta'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo enviar el pedido al ser creado por un error en la configuración de la sección <b>Centro de Integración</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The order could not be sent when it was created due to an error in the configuration of the <b>Integration Center</b> section. Please reconfigure this section to correct this problem for future orders.';
	}

	$estadoValidacion = $configuracion['ci_enviarPedidosCrear_tipo_consulta'];
	
	$pedidoArray = rvutils_woocommerce_obtener_pedido($order_id);
	$order = $pedidoArray['order'];
	
	if($estadoValidacion == '1')
	{
		if($mensajeErrorConfiguracion != '')
		{
			$order->add_order_note($mensajeErrorConfiguracion);
		}
		else
		{
			$parametros = array
			(
				'RFC' => $cuenta['rfc'],
				'USUARIO' => $cuenta['usuario'],
				'CLAVE' => $cuenta['clave'],
				'TIPO_CONEXION' => $configuracion['ci_enviarPedidosCrear_tipo_conexion'],
				'TIPO_SOLICITUD' => $configuracion['ci_enviarPedidosCrear_tipo_solicitud'],
				'URL' => $configuracion['ci_enviarPedidosCrear_url'],
				'PEDIDO' => $pedidoArray
			);
			
			$parametros2 = array
			(
				'RFC' => $cuenta['rfc'],
				'USUARIO' => $cuenta['usuario'],
				'CLAVE' => $cuenta['clave'],
				'TIPO_CONEXION' => $configuracion['ci_enviarPedidosCrear_tipo_conexion2'],
				'TIPO_SOLICITUD' => $configuracion['ci_enviarPedidosCrear_tipo_solicitud2'],
				'URL' => $configuracion['ci_enviarPedidosCrear_url2'],
				'PEDIDO' => $pedidoArray
			);
			
			rvutils_woocommerce_enviar_pedido_servicio_externo(1, $order, $parametros, $parametros2);
		}
	}
}

function rvutils_woocommerce_enviar_pedido_cambio_de_estado($order_id)
{
	global $idiomaRVLFECFDI, $urlServicio;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	
	$mensajeErrorConfiguracion = '';
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo enviar el pedido al cambiar de estado por un error en la configuración de la sección <b>Mi Cuenta</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The order could not be sent when changing status due to an error in the configuration of the <b>My Account</b> section. Please reconfigure this section to correct this problem for future orders.';
	}
	
	if(!($configuracion['ci_enviarPedidos_tipo_conexion'] != '' && $configuracion['ci_enviarPedidos_tipo_solicitud'] != '' && $configuracion['ci_enviarPedidos_url'] != '' && $configuracion['ci_enviarPedidos_tipo_consulta'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo enviar el pedido al cambiar de estado por un error en la configuración de la sección <b>Centro de Integración</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The order could not be sent when changing status due to an error in the configuration of the <b>Integration Center</b> section. Please reconfigure this section to correct this problem for future orders.';
	}

	$estadoValidacion = $configuracion['ci_enviarPedidos_tipo_consulta'];

	if(isset($configuracion['ci_enviarPedidos_tipo_consulta']) && $configuracion['ci_enviarPedidos_tipo_consulta'] !== null)
	{
		if($configuracion['ci_enviarPedidos_tipo_consulta'] == '1')
			$estadoValidacion = 'processing';
		else if($configuracion['ci_enviarPedidos_tipo_consulta'] == '2')
			$estadoValidacion = 'completed';
	}
	
	$pedidoArray = rvutils_woocommerce_obtener_pedido($order_id);
	$order = $pedidoArray['order'];
	$estadoPedido = $order->get_status();
	
	if($estadoPedido == $estadoValidacion)
	{
		if($mensajeErrorConfiguracion != '')
		{
			$order->add_order_note($mensajeErrorConfiguracion);
		}
		else
		{
			$parametros = array
			(
				'RFC' => $cuenta['rfc'],
				'USUARIO' => $cuenta['usuario'],
				'CLAVE' => $cuenta['clave'],
				'TIPO_CONEXION' => $configuracion['ci_enviarPedidos_tipo_conexion'],
				'TIPO_SOLICITUD' => $configuracion['ci_enviarPedidos_tipo_solicitud'],
				'URL' => $configuracion['ci_enviarPedidos_url'],
				'PEDIDO' => $pedidoArray
			);
			
			$parametros2 = array
			(
				'RFC' => $cuenta['rfc'],
				'USUARIO' => $cuenta['usuario'],
				'CLAVE' => $cuenta['clave'],
				'TIPO_CONEXION' => $configuracion['ci_enviarPedidos_tipo_conexion2'],
				'TIPO_SOLICITUD' => $configuracion['ci_enviarPedidos_tipo_solicitud2'],
				'URL' => $configuracion['ci_enviarPedidos_url2'],
				'PEDIDO' => $pedidoArray
			);
			
			rvutils_woocommerce_enviar_pedido_servicio_externo(2, $order, $parametros, $parametros2);
		}
	}
}

function rvutils_woocommerce_enviar_xml_pedido_timbrado($order_id, $XMLBase64)
{
	global $idiomaRVLFECFDI, $urlServicio;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	
	$mensajeErrorConfiguracion = '';
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo enviar el pedido al ser creado por un error en la configuración de la sección <b>Mi Cuenta</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The order could not be sent when it was created due to an error in the configuration of the <b>My Account</b> section. Please reconfigure this section to correct this problem for future orders.';
	}
	
	if(isset($configuracion['ci_enviarXml_tipo_consulta']))
	{
		if($configuracion['ci_enviarXml_tipo_consulta'] == '1')
		{
			$order = new WC_Order($order_id);
			
			if($mensajeErrorConfiguracion != '')
			{
				$order->add_order_note($mensajeErrorConfiguracion);
			}
			else
			{
				$parametros = array
				(
					'RFC' => $cuenta['rfc'],
					'USUARIO' => $cuenta['usuario'],
					'CLAVE' => $cuenta['clave'],
					'TIPO_CONEXION' => $configuracion['ci_enviarXml_tipo_conexion'],
					'TIPO_SOLICITUD' => $configuracion['ci_enviarXml_tipo_solicitud'],
					'URL' => $configuracion['ci_enviarXml_url'],
					'ID' => $order_id,
					'XMLBase64' => $XMLBase64
				);
				
				$parametros2 = array
				(
					'RFC' => $cuenta['rfc'],
					'USUARIO' => $cuenta['usuario'],
					'CLAVE' => $cuenta['clave'],
					'TIPO_CONEXION' => $configuracion['ci_enviarXml_tipo_conexion2'],
					'TIPO_SOLICITUD' => $configuracion['ci_enviarXml_tipo_solicitud2'],
					'URL' => $configuracion['ci_enviarXml_url2'],
					'ID' => $order_id,
					'XMLBase64' => $XMLBase64
				);
				
				rvutils_woocommerce_enviar_xml_pedido_timbrado_servicio_externo($order, $parametros, $parametros2);
			}
		}
	}
}

function rvutils_woocommerce_obtener_pedido($order_id)
{
	$order = new WC_Order($order_id);
	$orderData = $order->get_data();
	$orderPost = get_post($order_id);
	$orderMeta = get_post_meta($order_id);
	$estadoPedido = $order->get_status();
	
	$itemsFinal = [];
	$hayInventories = '0';
	$mi_inventories = [];
	
	foreach ( $order->get_items() as $item_id => $item )
	{
		$price = 0;
		$subtotal = 0;
		$subtotal_tax = 0;
		$total = 0;
		$total_tax = 0;
		
		/*if(is_numeric($item->get_price()))
			$price = (int)$item->get_price();
		*/
		if(is_numeric($item->get_subtotal()))
			$subtotal = $item->get_subtotal();
		
		if(is_numeric($item->get_subtotal_tax()))
			$subtotal_tax = $item->get_subtotal_tax();
		
		if(is_numeric($item->get_total()))
			$total = $item->get_total();
		
		if(is_numeric($item->get_total_tax()))
			$total_tax = $item->get_total_tax();
		
	   $product_id = $item->get_product_id();
	   
	   $producto = new WC_Product($product_id);
		$sku = '';
		$sku = is_object($producto) ? $producto->get_sku() : null;
	   
	   $variation_id = $item->get_variation_id();
	   
	   if ($variation_id) { 
		$sku = get_post_meta($item['variation_id'], '_sku', true);
	   }
	   
	   if(!isset($variation_id))
		   $variation_id = $product_id;
	   
	   $product = $item->get_product();
	   $product_name = $item->get_name();
	   $quantity = $item->get_quantity();
	   
	   if($quantity > 0)
		$price = $subtotal / $quantity;
	   
	   $tax = $item->get_subtotal_tax();
	   $taxclass = $item->get_tax_class();
	   
	   if(empty($item->get_tax_class()))
		   $taxclass = null;
	   
	   //$taxstat = $item->get_tax_status();
	   //$allmeta = $item->get_meta_data();
	   //$somemeta = $item->get_meta( '_whatever', true );
	   //$product_type = $item->get_type();
	   
	   $mi_inventories = $producto->get_attribute('mi_inventories');
	   
	   if(isset($mi_inventories))
			$hayInventories = '1';
	   
	   if($hayInventories == '1')
	   {
		   $itemsFinal[] = [
			  "id" => $item_id,
			  "name" => $product_name,
			  "product_id" => $product_id,
			  "variation_id" => $variation_id,
			  "quantity" => $quantity,
			  "tax_class" => $taxclass,
			  "subtotal" => $subtotal,
			  "subtotal_tax" => $subtotal_tax,
			  "total" => $total,
			  "total_tax" => $total_tax,
			  "taxes" => $item->get_taxes(),
			  "sku" => $sku,
			  "price" => $price,
			  "mi_inventories" => $mi_inventories
			];
	   }
	   else
	   {
		   $itemsFinal[] = [
		  "id" => $item_id,
		  "name" => $product_name,
		  "product_id" => $product_id,
		  "variation_id" => $variation_id,
		  "quantity" => $quantity,
		  "tax_class" => $taxclass,
		  "subtotal" => $subtotal,
		  "subtotal_tax" => $subtotal_tax,
		  "total" => $total,
		  "total_tax" => $total_tax,
		  "taxes" => $item->get_taxes(),
		  "sku" => $sku,
		  "price" => $price
		];
	   }
	}

	$shippingFinal = [];
	foreach($order->get_items('shipping') as $shipping_key => $shipping_item)
	{
		$shippingFinal[] = [
		  "id" => $shipping_key,
		  "method_title" => $shipping_item->get_method_title(),
		  "method_id" => $shipping_item->get_method_id(),
		  "instance_id" => $shipping_item->get_instance_id(),
		  "total" => $shipping_item->get_total(),
		  "total_tax" => $shipping_item->get_total_tax(),
		  "taxes" => $shipping_item->get_taxes()/*,
		  "meta_data" => $shipping_item->get_meta_data()*/
		];
	}
	
	$taxesFinal = [];
	foreach($order->get_items('tax') as $key => $item)
	{
		$tax_rate_id    = $item->get_rate_id(); // Tax rate ID
		$tax_rate_code  = $item->get_rate_code(); // Tax code
		$tax_label      = $item->get_label(); // Tax label name
		$tax_name       = $item->get_name(); // Tax name
		$tax_total      = $item->get_tax_total(); // Tax Total
		$tax_ship_total = $item->get_shipping_tax_total(); // Tax shipping total
		$tax_compound   = $item->get_compound(); // Tax compound
		$tax_percent    = WC_Tax::get_rate_percent( $tax_rate_id ); // Tax percentage
		$tax_rate       = str_replace('%', '', $tax_percent); // Tax rate
		
		$taxesFinal[] = [
		  "rate_id" => $tax_rate_id,
		  "rate_code" => $tax_rate_code,
		  "label" => $tax_label,
		  "name" => $tax_name,
		  "total" => $tax_total,
		  "ship_total" => $tax_ship_total,
		  "compound" => $tax_compound,
		  "percent" => $tax_percent,
		  "rate" => $tax_rate
		];
	}
	
	$paidDate = $order->get_date_paid();
	$completedDate = $order->get_date_completed();
	
	$paidDateGMT = '';
	$completedDateGMT = '';
	
	try
	{
		$paidDateGMT = get_gmt_from_date($paidDate);
	}
	catch(Exception $e)
	{
		$paidDateGMT = '';
	}
	
	try
	{
		$completedDateGMT = get_gmt_from_date($completedDate);
	}
	catch(Exception $e)
	{
		$completedDateGMT = '';
	}
	
	$orderData['date_modified'] = null;
	$orderData['date_paid'] = null;
	
	$extra = array
	(
	  "paid_date" => $paidDate,
	  "paid_date_gmt" => $paidDateGMT,
	  "completed_date" => $completedDate,
	  "completed_date_gmt" => $completedDateGMT,
	);
	
	$pedidoArray = array
	(
		'orderData' => $orderData,
		'orderPost' => $orderPost,
		'orderMeta' => $orderMeta,
		'items' => $itemsFinal,
		'shipping' => $shippingFinal,
		'taxes' => $taxesFinal,
		'extra' => $extra,
		'statusOrder' => $estadoPedido,
		'order' => $order
	);
	
	return $pedidoArray;
}

function rvutils_woocommerce_enviar_pedido_servicio_externo($tipoEnvio, $order, $parametros, $parametros2) //TipoEnvio: 1 = Pedido Creado, 2 = Pedido Cambio Estado
{
	global $idiomaRVLFECFDI, $urlSistemaAsociado;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	$respuesta = RealVirtualWooCommercePedido::enviarPedidoServicioExterno
	(
		$cuenta['rfc'],
		$cuenta['usuario'],
		$cuenta['clave'],
		$tipoEnvio,
		$parametros,
		$idiomaRVLFECFDI,
		$urlSistemaAsociado
	);
	
	if($respuesta->success == false)
	{
		if($tipoEnvio == 1)
			$nota = "Error al enviar el pedido al servicio externo al ser creado. Respuesta del servicio:<br/>".$respuesta->message;
		else if($tipoEnvio == 2)
			$nota = "Error al enviar el pedido al servicio externo al cambiar de estado. Respuesta del servicio:<br/>".$respuesta->message;
	}
	else
	{
		if($tipoEnvio == 1)
			$nota = "El pedido se envió al servicio externo al ser creado. Respuesta del servicio:<br/>".$respuesta->message;
		else if($tipoEnvio == 2)
			$nota = "El pedido se envió al servicio externo al cambiar de estado. Respuesta del servicio:<br/>".$respuesta->message;
	}
	
	$order->add_order_note($nota);
	
	if($parametros2['URL'] != '')
	{
		$respuesta = RealVirtualWooCommercePedido::enviarPedidoServicioExterno
		(
			$cuenta['rfc'],
			$cuenta['usuario'],
			$cuenta['clave'],
			$tipoEnvio,
			$parametros2,
			$idiomaRVLFECFDI,
			$urlSistemaAsociado
		);
		
		if($respuesta->success == false)
		{
			if($tipoEnvio == 1)
				$nota = "Error al enviar el pedido al segundo servicio externo al ser creado. Respuesta del servicio:<br/>".$respuesta->message;
			else if($tipoEnvio == 2)
				$nota = "Error al enviar el pedido al segundo servicio externo al cambiar de estado. Respuesta del servicio:<br/>".$respuesta->message;
		}
		else
		{
			if($tipoEnvio == 1)
				$nota = "El pedido se envió al segundo servicio externo al ser creado. Respuesta del servicio:<br/>".$respuesta->message;
			else if($tipoEnvio == 2)
				$nota = "El pedido se envió al segundo servicio externo al cambiar de estado. Respuesta del servicio:<br/>".$respuesta->message;
		}
		
		$order->add_order_note($nota);
	}
}

function rvutils_woocommerce_enviar_xml_pedido_timbrado_servicio_externo($order, $parametros, $parametros2) //TipoEnvio: 1 = Pedido Creado, 2 = Pedido Cambio Estado
{
	global $idiomaRVLFECFDI, $urlSistemaAsociado;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	$respuesta = RealVirtualWooCommercePedido::enviarXMLPedidoTimbradoServicioExterno
	(
		$cuenta['rfc'],
		$cuenta['usuario'],
		$cuenta['clave'],
		$parametros,
		$idiomaRVLFECFDI,
		$urlSistemaAsociado
	);
	
	if($respuesta->success == false)
	{
		$nota = "Error al enviar el XML del pedido al servicio externo. Respuesta del servicio:<br/>".$respuesta->message;
	}
	else
	{
		$nota = "El XML del pedido se envió al servicio externo. Respuesta del servicio:<br/>".$respuesta->message;
	}
	
	$order->add_order_note($nota);
	
	if($parametros2['URL'] != '')
	{
		$respuesta = RealVirtualWooCommercePedido::enviarXMLPedidoTimbradoServicioExterno
		(
			$cuenta['rfc'],
			$cuenta['usuario'],
			$cuenta['clave'],
			$parametros2,
			$idiomaRVLFECFDI,
			$urlSistemaAsociado
		);
		
		if($respuesta->success == false)
		{
			$nota = "Error al enviar el XML del pedido al segundo servicio externo. Respuesta del servicio:<br/>".$respuesta->message;
		}
		else
		{
			$nota = "El XML del pedido se envió al segundo servicio externo. Respuesta del servicio:<br/>".$respuesta->message;
		}
		
		$order->add_order_note($nota);
	}
}

function rvutils_woocommerce_enviar_pedido_servicio_externo_ANTERIOR($tipoEnvio, $order, $parametros, $parametros2) //TipoEnvio: 1 = Pedido Creado, 2 = Pedido Cambio Estado
{
	global $idiomaRVLFECFDI, $urlServicio;
	
	$urlServicio .= '/RVUtilsWooCommerce_CrearPedido';
	
	$params = array
	(
		'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
		'method' => 'POST',
		'timeout' => 75,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => $parametros,
		'cookies' => array()
	);
	
	$output = array
	(
		'URL API RVUTILS' => $urlServicio,
		'parametros' => $parametros
	);

	$proceso = 0;
	
	try
	{
		//$order->add_order_note("URL Servicio Externo a enviar pedido: ".$urlServicio);
		
		$proceso = 1;
		$response = wp_remote_post($urlServicio, $params);
		$proceso = 2;
		
		if(is_array($response))
		{
			$proceso = 3;
			$header = $response['headers'];
			$proceso = 4;
			$body = $response['body'];
			$proceso = 5;
			
			//API Response Stored as Post Meta
			//update_post_meta( $order_id, 'meta_message_', $body);
			$proceso = 6;
			
			$body = json_decode($body);

			if($body->Codigo == '0')
			{
				if($tipoEnvio == 1)
					$nota = "El pedido se envió al servicio externo al ser creado. Respuesta del servicio: ".json_encode($body);
				else if($tipoEnvio == 2)
					$nota = "El pedido se envió al servicio externo al cambiar de estado. Respuesta del servicio: ".json_encode($body);
			}
			else
			{
				if($tipoEnvio == 1)
					$nota = "Error al enviar el pedido al servicio externo al ser creado. Respuesta del servicio: ".json_encode($body);
				else if($tipoEnvio == 2)
					$nota = "Error al enviar el pedido al servicio externo al cambiar de estado. Respuesta del servicio: ".json_encode($body);
			}
			
			$order->add_order_note($nota);
			
			if($parametros2['URL'] != '')
			{
				$params = array
				(
					'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
					'method' => 'POST',
					'timeout' => 75,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => $parametros2,
					'cookies' => array()
				);
				
				$output = array
				(
					'URL API RVUTILS' => $urlServicio,
					'parametros' => $parametros2
				);
				
				$proceso = 0;
				
				try
				{
					$proceso = 1;
					$response = wp_remote_post($urlServicio, $params);
					$proceso = 2;
					
					//$order->add_order_note("URL Servicio Externo secundario a enviar pedido: ".$urlServicio);
					
					if(is_array($response))
					{
						$proceso = 3;
						$header = $response['headers'];
						$proceso = 4;
						$body = $response['body'];
						$proceso = 5;
						
						//API Response Stored as Post Meta
						//update_post_meta( $order_id, 'meta_message_', $body);
						$proceso = 6;
						
						$body = json_decode($body);
			
						if($body->Codigo == '0')
						{
							if($tipoEnvio == 1)
								$nota = "El pedido se envió al segundo servicio externo al ser creado. Respuesta del servicio: ".json_encode($body);
							else if($tipoEnvio == 2)
								$nota = "El pedido se envió al segundo servicio externo al cambiar de estado. Respuesta del servicio: ".json_encode($body);
						}
						else
						{
							if($tipoEnvio == 1)
								$nota = "Error al enviar el pedido al segundo servicio externo al ser creado. Respuesta del servicio: ".json_encode($body);
							else if($tipoEnvio == 2)
								$nota = "Error al enviar el pedido al segundo servicio externo al cambiar de estado. Respuesta del servicio: ".json_encode($body);
						}
						
						$order->add_order_note($nota);
					}
				}
				catch(Exception $e)
				{
					var_dump("PARAMETROS ENVIADOS AL API RVUTILS");
					var_dump("URL SEGUNDO SERVICIO: ".$parametros2['URL']);
					var_dump("PROCESO INTERRUMPIDO: ".$proceso);
					echo '<pre>';
					var_dump($output);
					echo "</pre>";
					var_dump("ERROR TRY/CATCH RECUPERADO");
					echo '<pre>';
					var_dump($e->getMessage());
					echo "</pre>";
					wp_die();
				}
			}
		}
	}
	catch(Exception $e)
	{
		var_dump("PARAMETROS ENVIADOS AL API RVUTILS");
		var_dump("URL PRIMER SERVICIO: ".$parametros['URL']);
		var_dump("PROCESO INTERRUMPIDO: ".$proceso);
		echo '<pre>';
		var_dump($output);
		echo "</pre>";
		var_dump("ERROR TRY/CATCH RECUPERADO");
		echo '<pre>';
		var_dump($e->getMessage());
		echo "</pre>";
		wp_die();
	}
}

function rvcfdi_woocommerce_cfdi_automatico($order_id)
{
	global $wpdb, $woocommerce, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $idiomaRVLFECFDI;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	$mensajeErrorConfiguracion = '';
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo emitir el CFDI automáticamente del pedido por un error en la configuración de la sección <b>Mi Cuenta</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The CFDI of the order could not be issued automatically due to an error in the configuration of the <b>My Account</b> section. Please reconfigure this section to correct this problem for future orders.';
	}
	
	$estado_orden_cfdi_automatico = $configuracion['estado_orden_cfdi_automatico'];
	$notificar_error_cfdi_automatico = $configuracion['notificar_error_cfdi_automatico'];
	$emailAdminNotificacionError = $configuracion['emailNotificacionErrorAutomatico'];
	$facturacionAutomatica = false;
	
	if($estado_orden_cfdi_automatico != 'no-especificado')
	{
		$order = new WC_Order($order_id);
		$estadoPedido = $order->get_status();
	
		if($estado_orden_cfdi_automatico == 'processing-completed')
		{
			if($estadoPedido == 'processing' || $estadoPedido == 'completed')
			{
				$facturacionAutomatica = true;
			}
		}
		else if($estado_orden_cfdi_automatico == 'cualquier-estado-excepto')
		{
			if($estadoPedido != 'pending' && $estadoPedido != 'canceled'
				&& $estadoPedido != 'refunded' && $estadoPedido != 'failed')
			{
				$facturacionAutomatica = true;
			}
		}
		else
		{
			if($estadoPedido == $estado_orden_cfdi_automatico)
			{
				$facturacionAutomatica = true;
			}
		}
		
		if($facturacionAutomatica == true)
		{
			if($mensajeErrorConfiguracion != '')
			{
				$order->add_order_note($mensajeErrorConfiguracion);
				
				$title = "Error CFDI automático del Pedido ".$order->get_order_number();
				$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
				
				if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
				{
					$mailer = $woocommerce->mailer();
					
					$message = $mailer->wrap_message
					(
						$body_message,
						$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible debido a un error interno de configuración del sistema de facturación."
					);
					
					$mailer->send($order->get_billing_email(), $title, $message);
					unset($mailer);
				}
				
				if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
				{
					$mailer = $woocommerce->mailer();
					
					$message = $mailer->wrap_message
					(
						$body_message,
						$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible debido a un error.<br/><br/><b>Descripción del error: </b>".$mensajeErrorConfiguracion
					);
					
					$mailer->send($emailAdminNotificacionError, $title, $message);
					unset($mailer);
				}
			}
			else
			{
				$datosPedido = RealVirtualWooCommercePedido::obtenerPedido
				(
					$order_id,
					$configuracion['precision_decimal']
				);
				
				$metodo_pago = $configuracion['metodo_pago'];
				$metodo_pago33 = $configuracion['metodo_pago33'];
				
				$datosFiscalesPersonalizado = 'SIN DATOS FISCALES DEFINIDOS';
				$receptor_id = '';
				$receptor_rfc = '';
				$receptor_razon_social = '';
				$receptor_email = '';
				$receptor_domicilioFiscalReceptor = '';
				$receptor_regimenfiscal = '';
				$usoCFDIReceptor = '';
				$formaPagoReceptor = '';
				$metodoPagoReceptor = '';
				
				$idUser = $order->get_user_id();
				$datosFiscales = obtenerDatosFiscales($idUser);
				
				if(isset($datosFiscales->rfc))
				{
					$datosFiscalesPersonalizado = 'DATOS FISCALES PERSONALIZADOS';
					$receptor_rfc = $datosFiscales->rfc;
					$receptor_razon_social = $datosFiscales->razon_social;
					$receptor_domicilioFiscalReceptor = $datosFiscales->domicilio_fiscal;
					$receptor_regimenfiscal = $datosFiscales->regimen_fiscal;
					$usoCFDIReceptor = $datosFiscales->uso_cfdi;
					//$formaPagoReceptor = $datosFiscales->forma_pago;
					//$metodoPagoReceptor = $datosFiscales->metodo_pago;
					$formaPagoReceptor = $metodo_pago;
					$metodoPagoReceptor = $metodo_pago33;
					
					//$order->add_order_note('Datos fiscales del Receptor (Personalizado): RFC: '.$receptor_rfc.', Razon Social: '.$receptor_razon_social.', CP: '.$receptor_domicilioFiscalReceptor.', Regimen Fiscal: '.$receptor_regimenfiscal.', Uso CFDI: '.$usoCFDIReceptor.', Forma de Pago:'.$formaPagoReceptor.', Metodo de Pago: '.$metodoPagoReceptor);
				}
				else
				{
					$receptor_rfc = 'XAXX010101000';
					$receptor_regimenfiscal = '616';
					
					if($configuracion['version_cfdi'] == '3.3')
					{
						$receptor_razon_social = $datosPedido->billing_company;
						
						if($receptor_razon_social == '')
							$receptor_razon_social = $datosPedido->billing_first_name.' '.$datosPedido->billing_last_name;
						
						$receptor_domicilioFiscalReceptor = $datosPedido->billing_postcode;
						$usoCFDIReceptor = 'P01';
					}
					else if($configuracion['version_cfdi'] == '4.0')
					{
						$receptor_razon_social = $datosPedido->billing_company;
						
						if($receptor_razon_social == '')
							$receptor_razon_social = $datosPedido->billing_first_name.' '.$datosPedido->billing_last_name;
						
						$receptor_domicilioFiscalReceptor = '';
						$usoCFDIReceptor = 'S01';
					}
					
					$formaPagoReceptor = $metodo_pago;
					$metodoPagoReceptor = $metodo_pago33;
					
					//$order->add_order_note('Datos fiscales del Receptor (No personalizado): RFC: '.$receptor_rfc.', Razon Social: '.$receptor_razon_social.', CP: '.$receptor_domicilioFiscalReceptor.', Regimen Fiscal: '.$receptor_regimenfiscal.', Uso CFDI: '.$usoCFDIReceptor.', Forma de Pago: '.$formaPagoReceptor.', Metodo de Pago: '.$metodoPagoReceptor);
				}
				
				update_post_meta($order->get_id(), 'CFDIAUTOMATICO_DATOSFISCALES_1_TIPO', $datosFiscalesPersonalizado);
				update_post_meta($order->get_id(), 'CFDIAUTOMATICO_DATOSFISCALES_2_RFC', $receptor_rfc);
				update_post_meta($order->get_id(), 'CFDIAUTOMATICO_DATOSFISCALES_3_RAZONSOCIAL', $receptor_razon_social);
				update_post_meta($order->get_id(), 'CFDIAUTOMATICO_DATOSFISCALES_4_CODIGOPOSTAL', $receptor_domicilioFiscalReceptor);
				update_post_meta($order->get_id(), 'CFDIAUTOMATICO_DATOSFISCALES_5_REGIMENFISCAL', $receptor_regimenfiscal);
				update_post_meta($order->get_id(), 'CFDIAUTOMATICO_DATOSFISCALES_6_USOCFDI', $usoCFDIReceptor);
				
				if(isset($datosFiscales->rfc) && $configuracion['version_cfdi'] == '4.0')
				{
					if($receptor_domicilioFiscalReceptor == '' || $receptor_domicilioFiscalReceptor == null || isset($receptor_domicilioFiscalReceptor) == false)
					{
						$order->add_order_note('No se emitió el CFDI automáticamente porque no se pudo obtener el Código Postal del Receptor con RFC "'.$receptor_rfc.'" desde los Datos Fiscales existentes.');
						
						$title = "Error CFDI automático del Pedido ".$order->get_order_number();
						$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
						
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Código Postal de los Datos Fiscales del RFC ".$receptor_rfc.". Por favor, inicia sesión en tu cuenta y verifica que los Datos Fiscales estén completos y sean correctos."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Código Postal del Receptor con RFC ".$receptor_rfc." desde los Datos Fiscales existentes."
							);
							
							$mailer->send($emailAdminNotificacionError, $title, $message);
							unset($mailer);
						}
						
						return;
					}
					
					if($receptor_razon_social == '' || $receptor_razon_social == null || isset($receptor_razon_social) == false)
					{
						$order->add_order_note('No se emitió el CFDI automáticamente porque no se pudo obtener la Razón Social del Receptor con RFC "'.$receptor_rfc.'" desde los Datos Fiscales existentes.');
						
						$title = "Error CFDI automático del Pedido ".$order->get_order_number();
						$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
						
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener la Razón Social de los Datos Fiscales del RFC ".$receptor_rfc.". Por favor, inicia sesión en tu cuenta y verifica que los Datos Fiscales estén completos y sean correctos."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener la Razón Social del Receptor con RFC ".$receptor_rfc." desde los Datos Fiscales existentes."
							);
							
							$mailer->send($emailAdminNotificacionError, $title, $message);
							unset($mailer);
						}
						
						return;
					}
					
					if($receptor_regimenfiscal == '' || $receptor_regimenfiscal == null || isset($receptor_regimenfiscal) == false)
					{
						$order->add_order_note('No se emitió el CFDI automáticamente porque no se pudo obtener el Régimen Fiscal del Receptor con RFC "'.$receptor_rfc.'" desde los Datos Fiscales existentes.');
						
						$title = "Error CFDI automático del Pedido ".$order->get_order_number();
						$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
						
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Régimen Fiscal de los Datos Fiscales del RFC ".$receptor_rfc.". Por favor, inicia sesión en tu cuenta y verifica que los Datos Fiscales estén completos y sean correctos."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Régimen Fiscal del Receptor con RFC ".$receptor_rfc." desde los Datos Fiscales existentes."
							);
							
							$mailer->send($emailAdminNotificacionError, $title, $message);
							unset($mailer);
						}
						
						return;
					}
					
					if($usoCFDIReceptor == '' || $usoCFDIReceptor == null || isset($usoCFDIReceptor) == false)
					{
						$order->add_order_note('No se emitió el CFDI automáticamente porque no se pudo obtener el Uso CFDI del Receptor con RFC "'.$receptor_rfc.'" desde los Datos Fiscales existentes.');
						
						$title = "Error CFDI automático del Pedido ".$order->get_order_number();
						$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
						
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Uso CFDI de los Datos Fiscales del RFC ".$receptor_rfc.". Por favor, inicia sesión en tu cuenta y verifica que los Datos Fiscales estén completos y sean correctos."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Uso CFDI del Receptor con RFC ".$receptor_rfc." desde los Datos Fiscales existentes."
							);
							
							$mailer->send($emailAdminNotificacionError, $title, $message);
							unset($mailer);
						}
						
						return;
					}
					
					/*if($formaPagoReceptor == '' || $formaPagoReceptor == null || isset($formaPagoReceptor) == false)
					{
						$order->add_order_note('No se emitió el CFDI automáticamente porque no se pudo obtener la Forma de Pago del Receptor desde los Datos Fiscales.');
						return;
					}
					
					if($metodoPagoReceptor == '' || $metodoPagoReceptor == null || isset($metodoPagoReceptor) == false)
					{
						$order->add_order_note('No se emitió el CFDI automáticamente porque no se pudo obtener el Método de Pago del Receptor desde los Datos Fiscales.');
						return;
					}*/
				}
				
				if(isset($datosFiscales->rfc) == false && $configuracion['version_cfdi'] == '3.3')
				{
					if($receptor_domicilioFiscalReceptor == '' || $receptor_domicilioFiscalReceptor == null || isset($receptor_domicilioFiscalReceptor) == false)
					{
						$order->add_order_note('No se emitió el CFDI automáticamente porque no se pudo obtener el Código Postal del Receptor desde los datos de facturación (Billing) del pedido.');
						
						$title = "Error CFDI automático del Pedido ".$order->get_order_number();
						$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
						
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Código Postal desde los datos de facturación (Billing) del pedido."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Código Postal desde los datos de facturación (Billing) del pedido."
							);
							
							$mailer->send($emailAdminNotificacionError, $title, $message);
							unset($mailer);
						}
						
						return;
					}
					
					if($formaPagoReceptor == '' || $formaPagoReceptor == null || isset($formaPagoReceptor) == false)
					{
						$order->add_order_note('No se emitió el CFDI automáticamente porque no se pudo obtener la Forma de Pago para el Receptor desde la configuración del plugin de facturación.');
						
						$title = "Error CFDI automático del Pedido ".$order->get_order_number();
						$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
						
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener la Forma de Pago para el Receptor desde la configuración del sistema de facturación."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener la Forma de Pago para el Receptor desde la configuración del plugin de facturación."
							);
							
							$mailer->send($emailAdminNotificacionError, $title, $message);
							unset($mailer);
						}
						
						return;
					}
					
					if($metodoPagoReceptor == '' || $metodoPagoReceptor == null || isset($metodoPagoReceptor) == false)
					{
						$order->add_order_note('No se emitió el CFDI automáticamente porque no se pudo obtener el Método de Pago para el Receptor desde la configuración del plugin de facturación.');
						
						$title = "Error CFDI automático del Pedido ".$order->get_order_number();
						$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
						
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Método de Pago para el Receptor desde la configuración del sistema de facturación."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No fue posible obtener el Método de Pago para el Receptor desde la configuración del plugin de facturación."
							);
							
							$mailer->send($emailAdminNotificacionError, $title, $message);
							unset($mailer);
						}
						
						return;
					}
				}
				
				$receptor_email = $datosPedido->billing_email;
				$calle_receptor = $datosPedido->billing_address_1;
				$colonia_receptor = $datosPedido->billing_address_2;
				$estado_receptor = $datosPedido->billing_state;
				$municipio_receptor = $datosPedido->billing_city;
				$pais_receptor = $datosPedido->billing_country;
				
				$subtotal = $datosPedido->subtotal;
				$descuento = $datosPedido->total_discount + $datosPedido->total_coupons;
				$total = $datosPedido->total;
				$importeTotalIVA = 0;
				
				//$order->add_order_note('Impuesto: '.count($datosPedido->impuestos));
				//$order->add_order_note('Impuesto Desc: '.$datosPedido->impuestos[0]['codigoImpuestoSAT']);
				
				$datosConceptos = RealVirtualWooCommercePedido::obtenerConceptosPedido
				(
					$datosPedido->line_items,
					$datosPedido->impuestos,
					$configuracion
				);
				
				$mensajeErrorConceptos = $datosConceptos->MensajeError;
				
				if($mensajeErrorConceptos != '')
				{
					$order->add_order_note('No se emitió el CFDI automáticamente debido a un error al leer los conceptos del pedido: '.$mensajeErrorConceptos);
					
					$title = "Error CFDI automático del Pedido ".$order->get_order_number();
					$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
					
					if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
					{
						$mailer = $woocommerce->mailer();
						
						$message = $mailer->wrap_message
						(
							$body_message,
							$body_message."<br/><br/>No se emitió el CFDI automáticamente debido a un error al leer los conceptos del pedido: ".$mensajeErrorConceptos
						);
						
						$mailer->send($order->get_billing_email(), $title, $message);
						unset($mailer);
					}
					if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
					{
						$mailer = $woocommerce->mailer();
						
						$message = $mailer->wrap_message
						(
							$body_message,
							$body_message."<br/><br/>No se emitió el CFDI automáticamente debido a un error al leer los conceptos del pedido: ".$mensajeErrorConceptos
						);
						
						$mailer->send($emailAdminNotificacionError, $title, $message);
						unset($mailer);
					}
					
					return;
				}
				
				$manejo_impuestos_pedido = $configuracion['manejo_impuestos_pedido'];
				
				if($manejo_impuestos_pedido == '1' || $manejo_impuestos_pedido == '2' || $manejo_impuestos_pedido == '3')
					$descuento = 0;
				if($manejo_impuestos_pedido == '0')
					$subtotal = $datosConceptos->Subtotal;
				if($manejo_impuestos_pedido == '4' || $manejo_impuestos_pedido == '5' || $manejo_impuestos_pedido == '6')
				{
					$subtotal = $datosConceptos->Subtotal;
					$descuento = $datosConceptos->Descuento;
					$importeTotalIVA = $datosConceptos->ImporteTotalIVA;
				}
				if($manejo_impuestos_pedido == '1' || $manejo_impuestos_pedido == '2' || $manejo_impuestos_pedido == '3')
				{
					$subtotal = $datosConceptos->Subtotal;
					$importeTotalIVA = $datosConceptos->ImporteTotalIVA;
				}
				
				//$order->add_order_note('Impuesto Rec: '.count($datosConceptos->ImpuestosRecalculados));
				//$order->add_order_note('Impuesto Rec Desc: '.$datosConceptos->ImpuestosRecalculados[0][5]);
				
				$subtotalNeto = $subtotal - $descuento;
				$datosImpuestos = RealVirtualWooCommercePedido::procesarImpuestos
				(
					$datosPedido->impuestos,
					$datosConceptos->ImpuestosRecalculados,
					$configuracion,
					$subtotalNeto,
					$importeTotalIVA,
					$total,
					$descuento,
					$subtotal
				);
				
				//$order->add_order_note('Impuesto Fed: '.count($datosImpuestos->ImpuestosFederales));
				//$order->add_order_note('Impuesto Fed Desc: '.$datosImpuestos->ImpuestosFederales[0][5]);
				
				$total = $datosImpuestos->total;
				$impuesto_federal = $datosImpuestos->ImpuestosFederales;
				$impuesto_local = $datosImpuestos->ImpuestosLocales;
				$mensajeErrorImpuestos = $datosImpuestos->MensajeError;
				
				if($mensajeErrorImpuestos != '')
				{
					$order->add_order_note('No se emitió el CFDI automáticamente debido a un error al leer los impuestos del pedido: '.$mensajeErrorImpuestos);
					
					$title = "Error CFDI automático del Pedido ".$order->get_order_number();
					$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
					
					if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
					{
						$mailer = $woocommerce->mailer();
						
						$message = $mailer->wrap_message
						(
							$body_message,
							$body_message."<br/><br/>No se emitió el CFDI automáticamente debido a un error al leer los impuestos del pedido: ".$mensajeErrorImpuestos
						);
						
						$mailer->send($order->get_billing_email(), $title, $message);
						unset($mailer);
					}
					if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
					{
						$mailer = $woocommerce->mailer();
						
						$message = $mailer->wrap_message
						(
							$body_message,
							$body_message."<br/><br/>No se emitió el CFDI automáticamente debido a un error al leer los impuestos del pedido: ".$mensajeErrorImpuestos
						);
						
						$mailer->send($emailAdminNotificacionError, $title, $message);
						unset($mailer);
					}
					
					return;
				}
				
				if($cuenta['rfc'] == 'GCM090618S91' || $cuenta['rfc'] == 'CACX7605101P8'/* || $cuenta['rfc'] == 'XIA190128J61'*/)
				{
					$datosClienteReceptor = array
					(
						'CodigoCliente' => $receptor_rfc,
						'RFC' => $receptor_rfc,
						'RazonSocial' => $receptor_razon_social,
						'UsoCfdi' => $usoCFDIReceptor,
						'RegimenFiscal' => $receptor_regimenfiscal,
						'DomicilioFiscal' => array
						(
							'Calle' => ($calle_receptor),
							'NumeroExt' => '',
							'NumeroInt' => '',
							'Colonia' => ($colonia_receptor),
							'Ciudad' => ($municipio_receptor),
							'Municipio' => ($municipio_receptor),
							'Estado' => ($estado_receptor),
							'Pais' => ($pais_receptor),
							'CP' => $receptor_domicilioFiscalReceptor
						)
					);
					
					$CFDI_UUID = get_post_meta($order_id, 'CFDI_UUID', true);
					if(empty($CFDI_UUID))
					{
						$datosCFDI = RealVirtualWooCommerceCFDI::generarCFDISinTimbrar
						(
							$cuenta['rfc'],
							$cuenta['usuario'],
							$cuenta['clave'],
							$receptor_id,
							$receptor_rfc,
							$receptor_razon_social,
							$receptor_email,
							$formaPagoReceptor,
							$metodoPagoReceptor,
							json_encode($datosConceptos->Conceptos),
							$subtotal,
							$descuento,
							$total,
							$configuracion['serie'],
							json_encode($impuesto_federal),
							json_encode($impuesto_local),
							$datosPedido->order_number,
							$urlSistemaAsociado,
							$sistema,
							$configuracion['regimen_fiscal'],
							$usoCFDIReceptor,
							$idiomaRVLFECFDI,
							$configuracion['clave_confirmacion'],
							$configuracion['moneda'],
							$configuracion['tipo_cambio'],
							base64_encode($configuracion['observacion']),
							$configuracion['precision_decimal'],
							$configuracion['huso_horario'],
							$calle_receptor,
							$estado_receptor,
							$municipio_receptor,
							$pais_receptor,
							$receptor_domicilioFiscalReceptor,
							'',
							$configuracion['version_cfdi'],
							$receptor_domicilioFiscalReceptor,
							$receptor_regimenfiscal,
							$configuracion['exportacion_cfdi'],
							$configuracion['facAtrAdquirente'],
							$configuracion['informacionGlobal_periodicidad'],
							$configuracion['informacionGlobal_meses'],
							$configuracion['informacionGlobal_año']
						);
						
						/*$order->add_order_note('metodo_pago:'.$metodo_pago);
						$order->add_order_note('metodo_pago33:'.$metodo_pago33);
						$order->add_order_note('subtotal:'.$subtotal);
						$order->add_order_note('descuento:'.$descuento);
						$order->add_order_note('total:'.$total);
						$order->add_order_note('serie:'.$configuracion['serie']);
						$order->add_order_note('order_number:'.$datosPedido->order_number);
						$order->add_order_note('moneda:'.$configuracion['moneda']);
						$order->add_order_note('tipo_cambio:'.$configuracion['tipo_cambio']);
						$order->add_order_note('precision_decimal:'.$configuracion['precision_decimal']);
						$order->add_order_note('huso_horario:'.$configuracion['huso_horario']);
						$order->add_order_note('version_cfdi:'.$configuracion['version_cfdi']);*/
						//update_post_meta($order->get_id(), 'resp_timb_', json_encode($datosCFDI));
							
						if($datosCFDI->success == false)
						{
							$nota = $datosCFDI->message;
							$order->add_order_note('No se pudo emitir el CFDI automáticamente debido a un error: '.$nota);
							
							$title = "Error CFDI automático del Pedido ".$order->get_order_number();
							$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
							
							if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
							{
								$mailer = $woocommerce->mailer();
								
								$message = $mailer->wrap_message
								(
									$body_message,
									$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible debido a un error.<br/><br/><b>Descripción del error: </b>".$nota
								);
								
								$mailer->send($order->get_billing_email(), $title, $message);
								unset($mailer);
							}
							if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
							{
								$mailer = $woocommerce->mailer();
								
								$message = $mailer->wrap_message
								(
									$body_message,
									$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible debido a un error.<br/><br/><b>Descripción del error: </b>".$nota
								);
								
								$mailer->send($emailAdminNotificacionError, $title, $message);
								unset($mailer);
							}
						}
						else
						{
							$xmlBase64SinTimbrar = $datosCFDI->XMLBase64SinTimbrar;
							//update_post_meta($order->get_id(), 'xmlBase64SinTimbrar', $xmlBase64SinTimbrar);
							$conceptosCfdi = [];
							
							foreach($order->get_items() as $item_id => $item)
							{
								$product = $order->get_product_from_item($item);
								$price = (int)$product->get_price();
								
								$conceptosCfdi[] = [
								  "codigo" => $item_id,
								  "cantidad" => $item->get_quantity(),
								  "valorUnitario" => $price,
								  "Descripcion" => $item->get_name()
								];
							}
					
							$rfcEmisorTimbrado = '';
							$usuarioEmisorTimbrado = '';
							
							if($cuenta['rfc'] == 'GCM090618S91')
							{
								$rfcEmisorTimbrado = 'GCM090618S91';
								$usuarioEmisorTimbrado = 'GCM090618S91';
							}
							else if($cuenta['rfc'] == 'CACX7605101P8'/* || $cuenta['rfc'] == 'XIA190128J61'*/)
							{
								$rfcEmisorTimbrado = 'CACX7605101P8';
								$usuarioEmisorTimbrado = 'CACX7605101P8';
							}
							
							$datosCfdi = array
							(
								'Fecha' => ObtenerDatosXml(base64_decode($xmlBase64SinTimbrar), 'Comprobante', 'Fecha'),
								'Serie' => ObtenerDatosXml(base64_decode($xmlBase64SinTimbrar), 'Comprobante', 'Serie'),
								'Moneda' => ObtenerDatosXml(base64_decode($xmlBase64SinTimbrar), 'Comprobante', 'Moneda'),
								'TipoCambio' => ObtenerDatosXml(base64_decode($xmlBase64SinTimbrar), 'Comprobante', 'TipoCambio'),
								'MetodoPago' => ObtenerDatosXml(base64_decode($xmlBase64SinTimbrar), 'Comprobante', 'MetodoPago'),
								'FormaPago' => ObtenerDatosXml(base64_decode($xmlBase64SinTimbrar), 'Comprobante', 'FormaPago'),
								'Emisor' => $rfcEmisorTimbrado, //ObtenerDatosXml(base64_decode($xmlBase64SinTimbrar), 'Emisor', 'Rfc'),
								'CodigoCliente' => $receptor_rfc,
								'TipoDocumento' => '4',
								'Observaciones' => '',
								'Conceptos' => $conceptosCfdi
							);
							
							$urlApi = 'https://utils.realvirtual.com.mx/api/data/RVUtilsWooCommerce_TimbrarCFDI';
							
							$headers = array
							(
								'cache-control' => 'no-cache',
								'content-type' => 'application/x-www-form-urlencoded'
							);
							
							$objeto = array
							(
								'RFC' => $rfcEmisorTimbrado,
								'USUARIO' => $usuarioEmisorTimbrado,
								'CLAVE' => $cuenta['clave'],
								'XMLBASE64' => $xmlBase64SinTimbrar,
								'RFCRECEPTOR' => $receptor_rfc,
								'DATOSRECEPTOR' => ($datosClienteReceptor),
								'DATOSCFDI' => ($datosCfdi),
							);
							
							$params = array
							(
								'method' => 'POST',
								'timeout' => 10000,
								'redirection' => 5,
								'httpversion' => '1.0',
								'blocking' => true,
								'headers' => $headers,
								'body' => $objeto,
								'cookies' => array()
							);
							update_post_meta($order->get_id(), 'parametros_enviados_greatness_timbrado', json_encode($params));
							$response = wp_remote_post($urlApi, $params);
							
							if(!is_wp_error($response))
							{
								$body = $response['body'];
								$body = json_decode($body);
								
								if(isset($body->Codigo))
								{
									if($body->Codigo != '0')
									{
										$nota = $body->Mensaje;
										$order->add_order_note('No se pudo emitir el CFDI automáticamente debido a un error: '.$nota);
									
										$title = "Error CFDI automático del Pedido ".$order->get_order_number();
										$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
										
										if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
										{
											$mailer = $woocommerce->mailer();
											
											$message = $mailer->wrap_message
											(
												$body_message,
												$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible debido a un error.<br/><br/><b>Descripción del error: </b>".$nota
											);
											
											$mailer->send($order->get_billing_email(), $title, $message);
											unset($mailer);
										}
										if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
										{
											$mailer = $woocommerce->mailer();
											
											$message = $mailer->wrap_message
											(
												$body_message,
												$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible debido a un error.<br/><br/><b>Descripción del error: </b>".$nota
											);
											
											$mailer->send($emailAdminNotificacionError, $title, $message);
											unset($mailer);
										}
									}
									else
									{
										$nota = $body->Mensaje;
										$order->add_order_note('Se emitió el CFDI automáticamente. '.$nota);
										
										$jsonArticulos = $datosCFDI->ARTICULOS;
										$jsonArticulosNuevo = array();
										
										for($i = 0; $i < count($jsonArticulos); $i++)
										{
											$nombre = $jsonArticulos[$i]->Nombre;
											$importe = $jsonArticulos[$i]->Importe;
											
											foreach($order->get_items() as $item_id => $item)
											{
												$product = $order->get_product_from_item($item);
												$name = $item->get_name();
												
												if($nombre == $name)
												{
													$indicador_impuesto = $product->get_attribute('indicador_impuesto');
													$articulo = $product->get_attribute('articulo');
													$material = $product->get_attribute('material');
													$jsonArticuloNuevo = array("Nombre" => $nombre, "Importe" => $importe, "Indicador_Impuesto" => $indicador_impuesto, "Articulo" => $articulo, "Material" => $material);
													$jsonArticulosNuevo[$i] = $jsonArticuloNuevo;
													break;
												}
											}
										}
										
										update_post_meta($order->get_id(), 'CFDI_UUID', $datosCFDI->UUID);
										update_post_meta($order->get_id(), 'CFDI_RFC_RECEPTOR', $datosCFDI->RFC_RECEPTOR);
										update_post_meta($order->get_id(), 'CFDI_SERIE', $datosCFDI->SERIE);
										update_post_meta($order->get_id(), 'CFDI_FOLIO', $datosCFDI->FOLIO);
										update_post_meta($order->get_id(), 'CFDI_TOTAL', $datosCFDI->TOTAL);
										update_post_meta($order->get_id(), 'CFDI_FECHA', $datosCFDI->FECHA);
										update_post_meta($order->get_id(), 'CFDI_MONEDA', $datosCFDI->MONEDA);
										update_post_meta($order->get_id(), 'CFDI_ARTICULOS', json_encode($jsonArticulosNuevo));

										$title = "CFDI emitido del Pedido ".$order->get_order_number();
										$body_message = "Se emitió el CFDI del pedido <b>".$order->get_order_number()."</b>";
										
										$mailer = $woocommerce->mailer();
										
										$message = $mailer->wrap_message
										(
											$body_message,
											$body_message."<br/><br/>El CFDI del pedido se emitió con éxito."
										);
										
										$mailer->send($order->get_billing_email(), $title, $message);
										unset($mailer);
									}
								}
								else
								{
									$nota = $body;
									$order->add_order_note('No se pudo emitir el CFDI automáticamente debido a un error al conectar con el servicio: '.$nota);
									
									$title = "Error CFDI automático del Pedido ".$order->get_order_number();
									$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
									
									if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
									{
										$mailer = $woocommerce->mailer();
										
										$message = $mailer->wrap_message
										(
											$body_message,
											$body_message."<br/><br/>No se pudo emitir el CFDI automáticamente debido a un error al conectar con el servicio: ".$nota
										);
										
										$mailer->send($order->get_billing_email(), $title, $message);
										unset($mailer);
									}
									if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
									{
										$mailer = $woocommerce->mailer();
										
										$message = $mailer->wrap_message
										(
											$body_message,
											$body_message."<br/><br/>No se pudo emitir el CFDI automáticamente debido a un error al conectar con el servicio: ".$nota
										);
										
										$mailer->send($emailAdminNotificacionError, $title, $message);
										unset($mailer);
									}
								}
							}
							else
							{
								$order->add_order_note('No se pudo emitir el CFDI automáticamente debido a un error al conectar con el servicio.');
								
								$title = "Error CFDI automático del Pedido ".$order->get_order_number();
								$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
								
								if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
								{
									$mailer = $woocommerce->mailer();
									
									$message = $mailer->wrap_message
									(
										$body_message,
										$body_message."<br/><br/>No se pudo emitir el CFDI automáticamente debido a un error al conectar con el servicio."
									);
									
									$mailer->send($order->get_billing_email(), $title, $message);
									unset($mailer);
								}
								if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
								{
									$mailer = $woocommerce->mailer();
									
									$message = $mailer->wrap_message
									(
										$body_message,
										$body_message."<br/><br/>No se pudo emitir el CFDI automáticamente debido a un error al conectar con el servicio."
									);
									
									$mailer->send($emailAdminNotificacionError, $title, $message);
									unset($mailer);
								}
							}
						}
					}
					else
					{
						$nota = 'No se emitió el CFDI automáticamente porque ya fue emitido previamente. El UUID y otros datos de este CFDI puede visualizarlos en la sección de Campos Personalizados en este pedido.';
						$order->add_order_note($nota);
						
						$title = "Error CFDI automático del Pedido ".$order->get_order_number();
						$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
						
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible porque ya fue emitido previamente."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No se emitió el CFDI automáticamente porque ya fue emitido previamente. El UUID y otros datos de este CFDI puede visualizarlos en la sección de Campos Personalizados de este pedido en WooCommerce."
							);
							
							$mailer->send($emailAdminNotificacionError, $title, $message);
							unset($mailer);
						}
					}
				}
				else
				{
					$CFDI_UUID = get_post_meta($order_id, 'CFDI_UUID', true);
					if(empty($CFDI_UUID))
					{
						$datosCFDI = RealVirtualWooCommerceCFDI::generarCFDI
						(
							$cuenta['rfc'],
							$cuenta['usuario'],
							$cuenta['clave'],
							$receptor_id,
							$receptor_rfc,
							$receptor_razon_social,
							$receptor_email,
							$formaPagoReceptor,
							$metodoPagoReceptor,
							json_encode($datosConceptos->Conceptos),
							$subtotal,
							$descuento,
							$total,
							$configuracion['serie'],
							json_encode($impuesto_federal),
							json_encode($impuesto_local),
							$datosPedido->order_number,
							$urlSistemaAsociado,
							$sistema,
							$configuracion['regimen_fiscal'],
							$usoCFDIReceptor,
							$idiomaRVLFECFDI,
							$configuracion['clave_confirmacion'],
							$configuracion['moneda'],
							$configuracion['tipo_cambio'],
							base64_encode($configuracion['observacion']),
							$configuracion['precision_decimal'],
							$configuracion['huso_horario'],
							$calle_receptor,
							$estado_receptor,
							$municipio_receptor,
							$pais_receptor,
							$receptor_domicilioFiscalReceptor,
							'',
							$configuracion['version_cfdi'],
							$receptor_domicilioFiscalReceptor,
							$receptor_regimenfiscal,
							$configuracion['exportacion_cfdi'],
							$configuracion['facAtrAdquirente'],
							$configuracion['informacionGlobal_periodicidad'],
							$configuracion['informacionGlobal_meses'],
							$configuracion['informacionGlobal_año'],
							0,
							1
						);
						
						/*$order->add_order_note('metodo_pago:'.$metodo_pago);
						$order->add_order_note('metodo_pago33:'.$metodo_pago33);
						$order->add_order_note('subtotal:'.$subtotal);
						$order->add_order_note('descuento:'.$descuento);
						$order->add_order_note('total:'.$total);
						$order->add_order_note('serie:'.$configuracion['serie']);
						$order->add_order_note('order_number:'.$datosPedido->order_number);
						$order->add_order_note('moneda:'.$configuracion['moneda']);
						$order->add_order_note('tipo_cambio:'.$configuracion['tipo_cambio']);
						$order->add_order_note('precision_decimal:'.$configuracion['precision_decimal']);
						$order->add_order_note('huso_horario:'.$configuracion['huso_horario']);
						$order->add_order_note('version_cfdi:'.$configuracion['version_cfdi']);*/
						
						if($datosCFDI->success == false)
						{
							$nota = $datosCFDI->message;
							$order->add_order_note('No se pudo emitir el CFDI automáticamente debido a un error: '.$nota);
							
							$title = "Error CFDI automático del Pedido ".$order->get_order_number();
							$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
							
							if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
							{
								$mailer = $woocommerce->mailer();
								
								$message = $mailer->wrap_message
								(
									$body_message,
									$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible debido a un error.<br/><br/><b>Descripción del error: </b>".$nota
								);
								
								$mailer->send($order->get_billing_email(), $title, $message);
								unset($mailer);
							}
							if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
							{
								$mailer = $woocommerce->mailer();
								
								$message = $mailer->wrap_message
								(
									$body_message,
									$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible debido a un error.<br/><br/><b>Descripción del error: </b>".$nota
								);
								
								$mailer->send($emailAdminNotificacionError, $title, $message);
								unset($mailer);
							}
						}
						else
						{
							$nota = $datosCFDI->message;
							$order->add_order_note('Se emitió el CFDI automáticamente. '.$nota);
							
							$jsonArticulos = $datosCFDI->ARTICULOS;
							$jsonArticulosNuevo = array();
							
							for($i = 0; $i < count($jsonArticulos); $i++)
							{
								$nombre = $jsonArticulos[$i]->Nombre;
								$importe = $jsonArticulos[$i]->Importe;
								
								foreach($order->get_items() as $item_id => $item)
								{
									$product = $order->get_product_from_item($item);
									$name = $item->get_name();
									
									if($nombre == $name)
									{
										$indicador_impuesto = $product->get_attribute('indicador_impuesto');
										$articulo = $product->get_attribute('articulo');
										$material = $product->get_attribute('material');
										$jsonArticuloNuevo = array("Nombre" => $nombre, "Importe" => $importe, "Indicador_Impuesto" => $indicador_impuesto, "Articulo" => $articulo, "Material" => $material);
										$jsonArticulosNuevo[$i] = $jsonArticuloNuevo;
										break;
									}
								}
							}
							
							update_post_meta($order->get_id(), 'CFDI_UUID', $datosCFDI->UUID);
							update_post_meta($order->get_id(), 'CFDI_RFC_RECEPTOR', $datosCFDI->RFC_RECEPTOR);
							update_post_meta($order->get_id(), 'CFDI_SERIE', $datosCFDI->SERIE);
							update_post_meta($order->get_id(), 'CFDI_FOLIO', $datosCFDI->FOLIO);
							update_post_meta($order->get_id(), 'CFDI_TOTAL', $datosCFDI->TOTAL);
							update_post_meta($order->get_id(), 'CFDI_FECHA', $datosCFDI->FECHA);
							update_post_meta($order->get_id(), 'CFDI_MONEDA', $datosCFDI->MONEDA);
							update_post_meta($order->get_id(), 'CFDI_ARTICULOS', json_encode($jsonArticulosNuevo));
							
							$title = "CFDI emitido del Pedido ".$order->get_order_number();
							$body_message = "Se emitió el CFDI del pedido <b>".$order->get_order_number()."</b>";
							
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>El CFDI del pedido se emitió con éxito."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
					}
					else
					{
						$nota = 'No se emitió el CFDI automáticamente porque ya fue emitido previamente. El UUID y otros datos de este CFDI puede visualizarlos en la sección de Campos Personalizados en este pedido.';
						$order->add_order_note($nota);
						
						$title = "Error CFDI automático del Pedido ".$order->get_order_number();
						$body_message = "Ocurrió un error al emitir automáticamente el CFDI del pedido <b>".$order->get_order_number()."</b>";
						
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 2)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>Se intentó emitir automáticamente el CFDI de este pedido pero no fue posible porque ya fue emitido previamente."
							);
							
							$mailer->send($order->get_billing_email(), $title, $message);
							unset($mailer);
						}
						if($notificar_error_cfdi_automatico == 1 || $notificar_error_cfdi_automatico == 3)
						{
							$mailer = $woocommerce->mailer();
							
							$message = $mailer->wrap_message
							(
								$body_message,
								$body_message."<br/><br/>No se emitió el CFDI automáticamente porque ya fue emitido previamente. El UUID y otros datos de este CFDI puede visualizarlos en la sección de Campos Personalizados de este pedido en WooCommerce."
							);
							
							$mailer->send($emailAdminNotificacionError, $title, $message);
							unset($mailer);
						}
					}
				}
			}
		}
	}
	
	if($cuenta['rfc'] == 'MCO701113C5A' /*|| $cuenta['rfc'] == 'XIA190128J61'*/)
	{
		$order = new WC_Order($order_id);
		
		//update_post_meta($order->get_id(), '_openpay_charge_id', 'triym0ixwsusgbdwz6va');
		
		$id_pedido = $order_id;
		$charge_id = get_post_meta($order_id, '_openpay_charge_id', true);
		$uuid = get_post_meta($order_id, 'CFDI_UUID', true);
		$rfcReceptor = get_post_meta($order_id, 'CFDI_RFC_RECEPTOR', true);
		$serie = get_post_meta($order_id, 'CFDI_SERIE', true);
		$folio = get_post_meta($order_id, 'CFDI_FOLIO', true);
		$total = get_post_meta($order_id, 'CFDI_TOTAL', true);
		$fecha = get_post_meta($order_id, 'CFDI_FECHA', true);
		$moneda = get_post_meta($order_id, 'CFDI_MONEDA', true);
		$articulos = json_decode(get_post_meta($order_id, 'CFDI_ARTICULOS', true));
		
		if(!empty($charge_id))
		{
			$urlApi = 'https://utils.realvirtual.com.mx/api/data/RVUtilsWooCommerce_EnviarDatosPedido';
		
			$objeto = array
			(
				'id_pedido' => $id_pedido,
				'charge_id' => $charge_id,
				'rfc_emisor' => $cuenta['rfc'],
				'usuario' => $cuenta['usuario'],
				'rfc_receptor' => $rfcReceptor,
				'uuid' => $uuid,
				'serie' => $serie,
				'folio' => $folio,
				'total' => $total,
				'fecha' => $fecha,
				'moneda' => $moneda,
				'articulos' => $articulos
			);
			
			$params = array
			(
				'method' => 'POST',
				'timeout' => 10000,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => $headers,
				'body' => $objeto,
				'cookies' => array()
			);
			
			$response = wp_remote_post($urlApi, $params);
			
			if(!is_wp_error($response))
			{
				$body = $response['body'];
				$body = json_decode($body);
			
				if(isset($body->Codigo))
				{
					if($body->Codigo == '0')
					{
						$mensaje = $body->Mensaje;
						$order->add_order_note($mensaje);
					}
					else
					{
						$nota = $body->Mensaje;
						$order->add_order_note('No se pudieron enviar los datos del CFDI al servicio externo debido a un error: '.$nota);
					}
				}
				else
				{
					$nota = $body;
					$order->add_order_note('No se pudieron enviar los datos del CFDI al servicio externo debido a un error al conectar con el servicio: '.$nota);
				}
			}
			else
			{
				$nota = 'No se pudieron enviar los datos del CFDI al servicio externo debido a un error al conectar con el servicio.';
				$order->add_order_note($nota);
			}
		}
		else
		{
			$nota = 'No se pudo enviar al servicio externo el dato "_openpay_charge_id" porque está vacío o no existe en la metadata del pedido.';
			$order->add_order_note($nota);
		}
	}
}

function rvcfdi_woocommerce_order_status_changed_ANTERIOR($order_id)
{
	global $idiomaRVLFECFDI, $urlServicio;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	
	$mensajeErrorConfiguracion = '';
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo enviar el pedido al cambiar de estado por un error en la configuración de la sección <b>Mi Cuenta</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The order could not be sent when changing status due to an error in the configuration of the <b>My Account</b> section. Please reconfigure this section to correct this problem for future orders.';
	}
	
	if(!($configuracion['ci_enviarPedidos_tipo_conexion'] != '' && $configuracion['ci_enviarPedidos_tipo_solicitud'] != '' && $configuracion['ci_enviarPedidos_url'] != '' && $configuracion['ci_enviarPedidos_tipo_consulta'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo enviar el pedido al cambiar de estado por un error en la configuración de la sección <b>Centro de Integración</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The order could not be sent when changing status due to an error in the configuration of the <b>Integration Center</b> section. Please reconfigure this section to correct this problem for future orders.';
	}

	$estadoValidacion = $configuracion['ci_enviarPedidos_tipo_consulta'];

	if(isset($configuracion['ci_enviarPedidos_tipo_consulta']) && $configuracion['ci_enviarPedidos_tipo_consulta'] !== null)
	{
		if($configuracion['ci_enviarPedidos_tipo_consulta'] == '1')
			$estadoValidacion = 'processing';
		else if($configuracion['ci_enviarPedidos_tipo_consulta'] == '2')
			$estadoValidacion = 'completed';
	}
	
	$urlServicio .= '/RVUtilsWooCommerce_CrearPedido';

	$order = new WC_Order($order_id);
	$orderData = $order->get_data();
	$orderPost = get_post($order_id);
	$orderMeta = get_post_meta($order_id);
	
	$estadoPedido = $order->get_status();
	
	rvcfdi_woocommerce_cfdi_automatico($order_id);
	
	if($estadoPedido == $estadoValidacion)
	{
		if($mensajeErrorConfiguracion != '')
		{
			$order->add_order_note($mensajeErrorConfiguracion);
		}
		else
		{
			$itemsFinal = [];
			$hayInventories = '0';
			$mi_inventories = [];
			
			foreach ( $order->get_items() as $item_id => $item )
			{
				$price = 0;
				$subtotal = 0;
				$subtotal_tax = 0;
				$total = 0;
				$total_tax = 0;
				
				/*if(is_numeric($item->get_price()))
					$price = (int)$item->get_price();
				*/
				if(is_numeric($item->get_subtotal()))
					$subtotal = $item->get_subtotal();
				
				if(is_numeric($item->get_subtotal_tax()))
					$subtotal_tax = $item->get_subtotal_tax();
				
				if(is_numeric($item->get_total()))
					$total = $item->get_total();
				
				if(is_numeric($item->get_total_tax()))
					$total_tax = $item->get_total_tax();
				
			   $product_id = $item->get_product_id();
			   
			   $producto = new WC_Product($product_id);
				$sku = '';
				$sku = is_object($producto) ? $producto->get_sku() : null;
			   
			   $variation_id = $item->get_variation_id();
			   
			   if ($variation_id) { 
				$sku = get_post_meta($item['variation_id'], '_sku', true);
			   }
			   
			   if(!isset($variation_id))
				   $variation_id = $product_id;
			   
			   $product = $item->get_product();
			   $product_name = $item->get_name();
			   $quantity = $item->get_quantity();
			   
			   if($quantity > 0)
				$price = $subtotal / $quantity;
			   
			   $tax = $item->get_subtotal_tax();
			   $taxclass = $item->get_tax_class();
			   
			   if(empty($item->get_tax_class()))
				   $taxclass = null;
			   
			   //$taxstat = $item->get_tax_status();
			   //$allmeta = $item->get_meta_data();
			   //$somemeta = $item->get_meta( '_whatever', true );
			   //$product_type = $item->get_type();
			   
			   $mi_inventories = $producto->get_attribute('mi_inventories');
			   
			   if(isset($mi_inventories))
					$hayInventories = '1';
			   
			   if($hayInventories == '1')
			   {
				   $itemsFinal[] = [
					  "id" => $item_id,
					  "name" => $product_name,
					  "product_id" => $product_id,
					  "variation_id" => $variation_id,
					  "quantity" => $quantity,
					  "tax_class" => $taxclass,
					  "subtotal" => $subtotal,
					  "subtotal_tax" => $subtotal_tax,
					  "total" => $total,
					  "total_tax" => $total_tax,
					  "taxes" => $item->get_taxes(),
					  "sku" => $sku,
					  "price" => $price,
					  "mi_inventories" => $mi_inventories
					];
			   }
			   else
			   {
				   $itemsFinal[] = [
				  "id" => $item_id,
				  "name" => $product_name,
				  "product_id" => $product_id,
				  "variation_id" => $variation_id,
				  "quantity" => $quantity,
				  "tax_class" => $taxclass,
				  "subtotal" => $subtotal,
				  "subtotal_tax" => $subtotal_tax,
				  "total" => $total,
				  "total_tax" => $total_tax,
				  "taxes" => $item->get_taxes(),
				  "sku" => $sku,
				  "price" => $price
				];
			   }
			}
			
			$shippingFinal = [];
			foreach($order->get_items('shipping') as $shipping_key => $shipping_item)
			{
				$shippingFinal[] = [
				  "id" => $shipping_key,
				  "method_title" => $shipping_item->get_method_title(),
				  "method_id" => $shipping_item->get_method_id(),
				  "instance_id" => $shipping_item->get_instance_id(),
				  "total" => $shipping_item->get_total(),
				  "total_tax" => $shipping_item->get_total_tax(),
				  "taxes" => $shipping_item->get_taxes()/*,
				  "meta_data" => $shipping_item->get_meta_data()*/
				];
			}
			
			$taxesFinal = [];
			foreach($order->get_items('tax') as $key => $item)
			{
				$tax_rate_id    = $item->get_rate_id(); // Tax rate ID
				$tax_rate_code  = $item->get_rate_code(); // Tax code
				$tax_label      = $item->get_label(); // Tax label name
				$tax_name       = $item->get_name(); // Tax name
				$tax_total      = $item->get_tax_total(); // Tax Total
				$tax_ship_total = $item->get_shipping_tax_total(); // Tax shipping total
				$tax_compound   = $item->get_compound(); // Tax compound
				$tax_percent    = WC_Tax::get_rate_percent( $tax_rate_id ); // Tax percentage
				$tax_rate       = str_replace('%', '', $tax_percent); // Tax rate
				
				$taxesFinal[] = [
				  "rate_id" => $tax_rate_id,
				  "rate_code" => $tax_rate_code,
				  "label" => $tax_label,
				  "name" => $tax_name,
				  "total" => $tax_total,
				  "ship_total" => $tax_ship_total,
				  "compound" => $tax_compound,
				  "percent" => $tax_percent,
				  "rate" => $tax_rate
				];
			}
			
			$paidDate = $order->get_date_paid();
			$completedDate = $order->get_date_completed();
			
			$paidDateGMT = '';
			$completedDateGMT = '';
			
			try
			{
				$paidDateGMT = get_gmt_from_date($paidDate);
			}
			catch(Exception $e)
			{
				$paidDateGMT = '';
			}
			
			try
			{
				$completedDateGMT = get_gmt_from_date($completedDate);
			}
			catch(Exception $e)
			{
				$completedDateGMT = '';
			}
			
			$orderData['date_modified'] = null;
			$orderData['date_paid'] = null;
			
			$extra = array
			(
			  "paid_date" => $paidDate,
			  "paid_date_gmt" => $paidDateGMT,
			  "completed_date" => $completedDate,
			  "completed_date_gmt" => $completedDateGMT,
			);
			
			$pedidoArray = array
			(
				'orderData' => $orderData,
				'orderPost' => $orderPost,
				'orderMeta' => $orderMeta,
				'items' => $itemsFinal,
				'shipping' => $shippingFinal,
				'taxes' => $taxesFinal,
				'extra' => $extra,
				'statusOrder' => $estadoPedido
			);
			
			$parametros = array
			(
				'RFC' => $cuenta['rfc'],
				'USUARIO' => $cuenta['usuario'],
				'CLAVE' => $cuenta['clave'],
				'TIPO_CONEXION' => $configuracion['ci_enviarPedidos_tipo_conexion'],
				'TIPO_SOLICITUD' => $configuracion['ci_enviarPedidos_tipo_solicitud'],
				'URL' => $configuracion['ci_enviarPedidos_url'],
				'PEDIDO' => $pedidoArray
			);
			
			$params = array
			(
				'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
				'method' => 'POST',
				'timeout' => 75,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => $parametros,
				'cookies' => array()
			);
			
			$output = array
			(
				'URL API RVUTILS' => $urlServicio,
				'parametros' => $parametros
			);

			$proceso = 0;
			
			try
			{
				$proceso = 1;
				$response = wp_remote_post($urlServicio, $params);
				$proceso = 2;
				
				$order->add_order_note("URL Servicio Externo a enviar pedido: ".$urlServicio);
				
				if(is_array($response))
				{
					$proceso = 3;
					$header = $response['headers'];
					$proceso = 4;
					$body = $response['body'];
					$proceso = 5;
					
					//API Response Stored as Post Meta
					//update_post_meta( $order_id, 'meta_message_', $body);
					$proceso = 6;
					
					$body = json_decode($body);
		
					if($body->Codigo == '0')
						$nota = "El pedido se envió al servicio externo al cambiar de estado. Respuesta del servicio: ".(json_encode($body));
					else
						$nota = "Error al enviar el pedido al servicio externo al cambiar de estado. Respuesta del servicio: ".(json_encode($body));
					
					$order->add_order_note($nota);
					
					if($configuracion['ci_enviarPedidos_url2'] != '')
					{
						$parametros = array
						(
							'RFC' => $cuenta['rfc'],
							'USUARIO' => $cuenta['usuario'],
							'CLAVE' => $cuenta['clave'],
							'TIPO_CONEXION' => $configuracion['ci_enviarPedidos_tipo_conexion2'],
							'TIPO_SOLICITUD' => $configuracion['ci_enviarPedidos_tipo_solicitud2'],
							'URL' => $configuracion['ci_enviarPedidos_url2'],
							'PEDIDO' => $pedidoArray
						);
						
						$params = array
						(
							'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
							'method' => 'POST',
							'timeout' => 75,
							'redirection' => 5,
							'httpversion' => '1.0',
							'blocking' => true,
							'headers' => array(),
							'body' => $parametros,
							'cookies' => array()
						);
						
						$output = array
						(
							'URL API RVUTILS' => $urlServicio,
							'parametros' => $parametros
						);
						
						$proceso = 0;
						
						try
						{
							$proceso = 1;
							$response = wp_remote_post($urlServicio, $params);
							$proceso = 2;
							
							$order->add_order_note("URL Servicio Externo secundario a enviar pedido: ".$urlServicio);
							
							if(is_array($response))
							{
								$proceso = 3;
								$header = $response['headers'];
								$proceso = 4;
								$body = $response['body'];
								$proceso = 5;
								
								//API Response Stored as Post Meta
								//update_post_meta( $order_id, 'meta_message_', $body);
								$proceso = 6;
								
								$body = json_decode($body);
					
								if($body->Codigo == '0')
									$nota = "El pedido se envió al segundo servicio externo al cambiar de estado. Respuesta del servicio: ".json_encode($body);
								else
									$nota = "Error al enviar el pedido al segundo servicio externo al cambiar de estado. Respuesta del servicio: ".json_encode($body);
								
								$order->add_order_note($nota);
							}
						}
						catch(Exception $e)
						{
							var_dump("PARAMETROS ENVIADOS AL API RVUTILS");
							var_dump("URL SEGUNDO SERVICIO: ".$configuracion['ci_enviarPedidos_url2']);
							var_dump("PROCESO INTERRUMPIDO: ".$proceso);
							echo '<pre>';
							var_dump($output);
							echo "</pre>";
							var_dump("ERROR TRY/CATCH RECUPERADO");
							echo '<pre>';
							var_dump($e->getMessage());
							echo "</pre>";
							wp_die();
						}
					}
				}
			}
			catch(Exception $e)
			{
				var_dump("PARAMETROS ENVIADOS AL API RVUTILS");
				var_dump("URL PRIMER SERVICIO: ".$configuracion['ci_enviarPedidos_url']);
				var_dump("PROCESO INTERRUMPIDO: ".$proceso);
				echo '<pre>';
				var_dump($output);
				echo "</pre>";
				var_dump("ERROR TRY/CATCH RECUPERADO");
				echo '<pre>';
				var_dump($e->getMessage());
				echo "</pre>";
				wp_die();
			}
		}
	}
}

function rvutils_woocommerce_enviar_pedido_creado_ANTERIOR($order_id)
{
	global $idiomaRVLFECFDI, $urlServicio;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceCentroIntegracion::configuracionEntidad();
	
	$mensajeErrorConfiguracion = '';
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo enviar el pedido al ser creado por un error en la configuración de la sección <b>Mi Cuenta</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The order could not be sent when it was created due to an error in the configuration of the <b>My Account</b> section. Please reconfigure this section to correct this problem for future orders.';
	}
	
	if(!($configuracion['ci_enviarPedidosCrear_tipo_conexion'] != '' && $configuracion['ci_enviarPedidosCrear_tipo_solicitud'] != '' && $configuracion['ci_enviarPedidosCrear_url'] != '' && $configuracion['ci_enviarPedidosCrear_tipo_consulta'] != ''))
	{
		$mensajeErrorConfiguracion = ($idiomaRVLFECFDI == 'ES') ? '<b>Plugin '.$nombreSistemaMenu.':</b> No se pudo enviar el pedido al ser creado por un error en la configuración de la sección <b>Centro de Integración</b>. Por favor, configure nuevamente dicha sección para corregir este problema para futuros pedidos.' : '<b>'.$nombreSistemaMenu.' Plugin:</b> The order could not be sent when it was created due to an error in the configuration of the <b>Integration Center</b> section. Please reconfigure this section to correct this problem for future orders.';
	}

	$estadoValidacion = $configuracion['ci_enviarPedidosCrear_tipo_consulta'];

	$urlServicio .= '/RVUtilsWooCommerce_CrearPedido';

	$order = new WC_Order($order_id);
	$orderData = $order->get_data();
	$orderPost = get_post($order_id);
	$orderMeta = get_post_meta($order_id);
	$estadoPedido = $order->get_status();
	
	if($estadoValidacion == '1')
	{
		if($mensajeErrorConfiguracion != '')
		{
			$order->add_order_note($mensajeErrorConfiguracion);
		}
		else
		{
			$itemsFinal = [];
			foreach ( $order->get_items() as $item_id => $item )
			{
				$price = 0;
				$subtotal = 0;
				$subtotal_tax = 0;
				$total = 0;
				$total_tax = 0;
				
				/*if(is_numeric($item->get_price()))
					$price = (int)$item->get_price();
				*/
				if(is_numeric($item->get_subtotal()))
					$subtotal = $item->get_subtotal();
				
				if(is_numeric($item->get_subtotal_tax()))
					$subtotal_tax = $item->get_subtotal_tax();
				
				if(is_numeric($item->get_total()))
					$total = $item->get_total();
				
				if(is_numeric($item->get_total_tax()))
					$total_tax = $item->get_total_tax();
				
			   $product_id = $item->get_product_id();
			   
			   $producto = new WC_Product($product_id);
				$sku = '';
				$sku = is_object($producto) ? $producto->get_sku() : null;
			   
			   $variation_id = $item->get_variation_id();
			   
			   if ($variation_id) { 
				$sku = get_post_meta($item['variation_id'], '_sku', true);
			   }
			   
			   if(!isset($variation_id))
				   $variation_id = $product_id;
			   
			   $product = $item->get_product();
			   $product_name = $item->get_name();
			   $quantity = $item->get_quantity();
			   
			   if($quantity > 0)
				$price = $subtotal / $quantity;
			   
			   $tax = $item->get_subtotal_tax();
			   $taxclass = $item->get_tax_class();
			   
			   if(empty($item->get_tax_class()))
				   $taxclass = null;
			   
			   //$taxstat = $item->get_tax_status();
			   //$allmeta = $item->get_meta_data();
			   //$somemeta = $item->get_meta( '_whatever', true );
			   //$product_type = $item->get_type();
			   
			   $itemsFinal[] = [
				  "id" => $item_id,
				  "name" => $product_name,
				  "product_id" => $product_id,
				  "variation_id" => $variation_id,
				  "quantity" => $quantity,
				  "tax_class" => $taxclass,
				  "subtotal" => $subtotal,
				  "subtotal_tax" => $subtotal_tax,
				  "total" => $total,
				  "total_tax" => $total_tax,
				  "taxes" => $item->get_taxes(),
				  "sku" => $sku,
				  "price" => $price
				];
			}
			
			$shippingFinal = [];
			foreach($order->get_items('shipping') as $shipping_key => $shipping_item)
			{
				$shippingFinal[] = [
				  "id" => $shipping_key,
				  "method_title" => $shipping_item->get_method_title(),
				  "method_id" => $shipping_item->get_method_id(),
				  "instance_id" => $shipping_item->get_instance_id(),
				  "total" => $shipping_item->get_total(),
				  "total_tax" => $shipping_item->get_total_tax(),
				  "taxes" => $shipping_item->get_taxes()/*,
				  "meta_data" => $shipping_item->get_meta_data()*/
				];
			}
			
			$taxesFinal = [];
			foreach($order->get_items('tax') as $key => $item)
			{
				$tax_rate_id    = $item->get_rate_id(); // Tax rate ID
				$tax_rate_code  = $item->get_rate_code(); // Tax code
				$tax_label      = $item->get_label(); // Tax label name
				$tax_name       = $item->get_name(); // Tax name
				$tax_total      = $item->get_tax_total(); // Tax Total
				$tax_ship_total = $item->get_shipping_tax_total(); // Tax shipping total
				$tax_compound   = $item->get_compound(); // Tax compound
				$tax_percent    = WC_Tax::get_rate_percent( $tax_rate_id ); // Tax percentage
				$tax_rate       = str_replace('%', '', $tax_percent); // Tax rate
				
				$taxesFinal[] = [
				  "rate_id" => $tax_rate_id,
				  "rate_code" => $tax_rate_code,
				  "label" => $tax_label,
				  "name" => $tax_name,
				  "total" => $tax_total,
				  "ship_total" => $tax_ship_total,
				  "compound" => $tax_compound,
				  "percent" => $tax_percent,
				  "rate" => $tax_rate
				];
			}
			
			$paidDate = $order->get_date_paid();
			$completedDate = $order->get_date_completed();
			
			$paidDateGMT = '';
			$completedDateGMT = '';
			
			try
			{
				$paidDateGMT = get_gmt_from_date($paidDate);
			}
			catch(Exception $e)
			{
				$paidDateGMT = '';
			}
			
			try
			{
				$completedDateGMT = get_gmt_from_date($completedDate);
			}
			catch(Exception $e)
			{
				$completedDateGMT = '';
			}
			
			$orderData['date_modified'] = null;
			$orderData['date_paid'] = null;
			
			$extra = array
			(
			  "paid_date" => $paidDate,
			  "paid_date_gmt" => $paidDateGMT,
			  "completed_date" => $completedDate,
			  "completed_date_gmt" => $completedDateGMT,
			);
			
			$pedidoArray = array
			(
				'orderData' => $orderData,
				'orderPost' => $orderPost,
				'orderMeta' => $orderMeta,
				'items' => $itemsFinal,
				'shipping' => $shippingFinal,
				'taxes' => $taxesFinal,
				'extra' => $extra,
				'statusOrder' => $estadoPedido
			);
			
			$parametros = array
			(
				'RFC' => $cuenta['rfc'],
				'USUARIO' => $cuenta['usuario'],
				'CLAVE' => $cuenta['clave'],
				'TIPO_CONEXION' => $configuracion['ci_enviarPedidosCrear_tipo_conexion'],
				'TIPO_SOLICITUD' => $configuracion['ci_enviarPedidosCrear_tipo_solicitud'],
				'URL' => $configuracion['ci_enviarPedidosCrear_url'],
				'PEDIDO' => $pedidoArray
			);
			
			$params = array
			(
				'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
				'method' => 'POST',
				'timeout' => 75,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array(),
				'body' => $parametros,
				'cookies' => array()
			);
			
			$output = array
			(
				'URL API RVUTILS' => $urlServicio,
				'parametros' => $parametros
			);

			$proceso = 0;
			
			try
			{
				$proceso = 1;
				$response = wp_remote_post($urlServicio, $params);
				$proceso = 2;
				
				$order->add_order_note("URL Servicio Externo a enviar pedido: ".$urlServicio);
				
				if(is_array($response))
				{
					$proceso = 3;
					$header = $response['headers'];
					$proceso = 4;
					$body = $response['body'];
					$proceso = 5;
					
					//API Response Stored as Post Meta
					//update_post_meta( $order_id, 'meta_message_', $body);
					$proceso = 6;
					
					$body = json_decode($body);
		
					if($body->Codigo == '0')
						$nota = "El pedido se envió al servicio externo al ser creado. Respuesta del servicio: ".json_encode($body);
					else
						$nota = "Error al enviar el pedido al servicio externo al ser creado. Respuesta del servicio: ".json_encode($body);
					
					$order->add_order_note($nota);
					
					if($configuracion['ci_enviarPedidosCrear_url2'] != '')
					{
						$parametros = array
						(
							'RFC' => $cuenta['rfc'],
							'USUARIO' => $cuenta['usuario'],
							'CLAVE' => $cuenta['clave'],
							'TIPO_CONEXION' => $configuracion['ci_enviarPedidosCrear_tipo_conexion2'],
							'TIPO_SOLICITUD' => $configuracion['ci_enviarPedidosCrear_tipo_solicitud2'],
							'URL' => $configuracion['ci_enviarPedidosCrear_url2'],
							'PEDIDO' => $pedidoArray
						);
						
						$params = array
						(
							'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
							'method' => 'POST',
							'timeout' => 75,
							'redirection' => 5,
							'httpversion' => '1.0',
							'blocking' => true,
							'headers' => array(),
							'body' => $parametros,
							'cookies' => array()
						);
						
						$output = array
						(
							'URL API RVUTILS' => $urlServicio,
							'parametros' => $parametros
						);
						
						$proceso = 0;
						
						try
						{
							$proceso = 1;
							$response = wp_remote_post($urlServicio, $params);
							$proceso = 2;
							
							$order->add_order_note("URL Servicio Externo secundario a enviar pedido: ".$urlServicio);
							
							if(is_array($response))
							{
								$proceso = 3;
								$header = $response['headers'];
								$proceso = 4;
								$body = $response['body'];
								$proceso = 5;
								
								//API Response Stored as Post Meta
								//update_post_meta( $order_id, 'meta_message_', $body);
								$proceso = 6;
								
								$body = json_decode($body);
					
								if($body->Codigo == '0')
									$nota = "El pedido se envió al segundo servicio externo al ser creado. Respuesta del servicio: ".json_encode($body);
								else
									$nota = "Error al enviar el pedido al segundo servicio externo al ser creado. Respuesta del servicio: ".json_encode($body);
								
								$order->add_order_note($nota);
							}
						}
						catch(Exception $e)
						{
							var_dump("PARAMETROS ENVIADOS AL API RVUTILS");
							var_dump("URL SEGUNDO SERVICIO: ".$configuracion['ci_enviarPedidosCrear_url2']);
							var_dump("PROCESO INTERRUMPIDO: ".$proceso);
							echo '<pre>';
							var_dump($output);
							echo "</pre>";
							var_dump("ERROR TRY/CATCH RECUPERADO");
							echo '<pre>';
							var_dump($e->getMessage());
							echo "</pre>";
							wp_die();
						}
					}
				}
			}
			catch(Exception $e)
			{
				var_dump("PARAMETROS ENVIADOS AL API RVUTILS");
				var_dump("URL PRIMER SERVICIO: ".$configuracion['ci_enviarPedidosCrear_url']);
				var_dump("PROCESO INTERRUMPIDO: ".$proceso);
				echo '<pre>';
				var_dump($output);
				echo "</pre>";
				var_dump("ERROR TRY/CATCH RECUPERADO");
				echo '<pre>';
				var_dump($e->getMessage());
				echo "</pre>";
				wp_die();
			}
		}
	}
}

function realvirtual_woocommerce_front_end_receptor()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $sistema, $idiomaRVLFECFDI;
	
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$idiomaRVLFECFDI = ($configuracion['idioma'] != '') ? $configuracion['idioma'] : 'ES';
	
	$opcionesMetodoPago = '';
	$metodo_pago = $configuracion['metodo_pago'];
	$metodosPagoHabilitado = '';
	$metodosPagoHabilitadoLB = ($idiomaRVLFECFDI == 'ES') ? 'Forma de pago':'Payment way';
	
	$opcionesMetodoPago33 = '';
	$metodo_pago33 = $configuracion['metodo_pago33'];
	$metodosPagoHabilitado33 = '';
	$metodosPagoHabilitado33LB = ($idiomaRVLFECFDI == 'ES') ? 'Método de pago':'Payment Method';
	
	$opcionesUsoCFDI = '';
	$uso_cfdi = $configuracion['uso_cfdi'];
	$usoCFDIHabilitado = '';
	$usoCFDIHabilitadoLB = ($idiomaRVLFECFDI == 'ES') ? '* Uso CFDI':'* CFDI Use';
	
	$receptor_rfc = '';
	$receptor_razon_social = '';
	$receptor_domicilioFiscalReceptor = '';
	$receptor_regimenfiscal = '';
	$usoCFDIReceptor = $uso_cfdi;
	$formaPagoReceptor = $metodo_pago;
	$metodoPagoReceptor = $metodo_pago33;
	
	$idUser = get_current_user_id();
	$datosFiscales = obtenerDatosFiscales($idUser);
	
	if(isset($datosFiscales->rfc))
	{
		$receptor_rfc = $datosFiscales->rfc;
		$receptor_razon_social = $datosFiscales->razon_social;
		$receptor_domicilioFiscalReceptor = $datosFiscales->domicilio_fiscal;
		$receptor_regimenfiscal = $datosFiscales->regimen_fiscal;
		$usoCFDIReceptor = $datosFiscales->uso_cfdi;
		$formaPagoReceptor = $datosFiscales->forma_pago;
		$metodoPagoReceptor = $datosFiscales->metodo_pago;
	}
	
	$opcionesRegimenFiscalReceptor = '';
	
	/*if($configuracion['uso_cfdi_seleccionar'] == 'no')
		$usoCFDIHabilitado = 'disabled';
	if($configuracion['uso_cfdi_seleccionar'] == 'noOcultar')
	{
		$usoCFDIHabilitado = 'hidden';
		$usoCFDIHabilitadoLB = '';
	}*/
	
	if($configuracion['metodo_pago_seleccionar'] == 'no')
	{
		$metodosPagoHabilitado = 'disabled';
	}
	if($configuracion['metodo_pago_seleccionar'] == 'noOcultar')
	{
		$metodosPagoHabilitado = 'hidden';
		$metodosPagoHabilitadoLB = '';
	}
	
	if($configuracion['metodo_pago_seleccionar33'] == 'no')
	{
		$metodosPagoHabilitado33 = 'disabled';
	}
	if($configuracion['metodo_pago_seleccionar33'] == 'noOcultar')
	{
		$metodosPagoHabilitado33 = 'hidden';
		$metodosPagoHabilitado33LB = '';
	}
	
	$metodoPagoReceptor = $metodo_pago33;
	$formaPagoReceptor = $metodo_pago;
	
	if($formaPagoReceptor == '01')
		$opcionesMetodoPago .= '<option value="01" selected>01 - Efectivo</option>';
	else
		$opcionesMetodoPago .= '<option value="01">01 - Efectivo</option>';
	
	if($formaPagoReceptor == '02')
		$opcionesMetodoPago .= '<option value="02" selected>02 - Cheque nominativo</option>';
	else
		$opcionesMetodoPago .= '<option value="02">02 - Cheque nominativo</option>';
											
	if($formaPagoReceptor == '03')
		$opcionesMetodoPago .= '<option value="03" selected>03 - Transferencia electrónica de fondos</option>';
	else
		$opcionesMetodoPago .= '<option value="03">03 - Transferencia electrónica de fondos</option>';
											
	if($formaPagoReceptor == '04')
		$opcionesMetodoPago .= '<option value="04" selected>04 - Tarjeta de crédito</option>';
	else
		$opcionesMetodoPago .= '<option value="04">04 - Tarjeta de crédito</option>';
											
	if($formaPagoReceptor == '05')
		$opcionesMetodoPago .= '<option value="05" selected>05 - Monedero electrónico</option>';
	else
		$opcionesMetodoPago .= '<option value="05">05 - Monedero electrónico</option>';
											
	if($formaPagoReceptor == '06')
		$opcionesMetodoPago .= '<option value="06" selected>06 - Dinero electrónico</option>';
	else
		$opcionesMetodoPago .= '<option value="06">06 - Dinero electrónico</option>';
											
	if($formaPagoReceptor == '08')
		$opcionesMetodoPago .= '<option value="08" selected>08 - Vales de despensa</option>';
	else
		$opcionesMetodoPago .= '<option value="08">08 - Vales de despensa</option>';
											
	if($formaPagoReceptor == '12')
		$opcionesMetodoPago .= '<option value="12" selected>12 - Dación de pago</option>';
	else
		$opcionesMetodoPago .= '<option value="12">12 - Dación de pago</option>';

	if($formaPagoReceptor == '13')
		$opcionesMetodoPago .= '<option value="13" selected>13 - Pago por subrogación</option>';
	else
		$opcionesMetodoPago .= '<option value="13">13 - Pago por subrogación</option>';
											
	if($formaPagoReceptor == '14')
		$opcionesMetodoPago .= '<option value="14" selected>14 - Pago por consignación</option>';
	else
		$opcionesMetodoPago .= '<option value="14">14 - Pago por consignación</option>';
	
	if($formaPagoReceptor == '15')
		$opcionesMetodoPago .= '<option value="15" selected>15 - Condonación</option>';
	else
		$opcionesMetodoPago .= '<option value="15">15 - Condonación</option>';
	
	if($formaPagoReceptor == '17')
		$opcionesMetodoPago .= '<option value="17" selected>17 - Compensación</option>';
	else
		$opcionesMetodoPago .= '<option value="17">17 - Compensación</option>';
	
	if($formaPagoReceptor == '23')
		$opcionesMetodoPago .= '<option value="23" selected>23 - Novación</option>';
	else
		$opcionesMetodoPago .= '<option value="23">23 - Novación</option>';
	
	if($formaPagoReceptor == '24')
		$opcionesMetodoPago .= '<option value="24" selected>24 - Confusión</option>';
	else
		$opcionesMetodoPago .= '<option value="24">24 - Confusión</option>';
	
	if($formaPagoReceptor == '25')
		$opcionesMetodoPago .= '<option value="25" selected>25 - Remisión de deuda</option>';
	else
		$opcionesMetodoPago .= '<option value="25">25 - Remisión de deuda</option>';
	
	if($formaPagoReceptor == '26')
		$opcionesMetodoPago .= '<option value="26" selected>26 - Prescripción o caducidad</option>';
	else
		$opcionesMetodoPago .= '<option value="26">26 - Prescripción o caducidad</option>';
	
	if($formaPagoReceptor == '27')
		$opcionesMetodoPago .= '<option value="27" selected>27 - A satisfacción del acreedor</option>';
	else
		$opcionesMetodoPago .= '<option value="27">27 - A satisfacción del acreedor</option>';
	
	if($formaPagoReceptor == '28')
		$opcionesMetodoPago .= '<option value="28" selected>28 - Tarjeta de débito</option>';
	else
		$opcionesMetodoPago .= '<option value="28">28 - Tarjeta de débito</option>';
	
	if($formaPagoReceptor == '29')
		$opcionesMetodoPago .= '<option value="29" selected>29 - Tarjeta de servicios</option>';
	else
		$opcionesMetodoPago .= '<option value="29">29 - Tarjeta de servicios</option>';
	
	if($formaPagoReceptor == '30')
		$opcionesMetodoPago .= '<option value="30" selected>30 - Aplicación de anticipos</option>';
	else
		$opcionesMetodoPago .= '<option value="30">30 - Aplicación de anticipos</option>';
	
	if($formaPagoReceptor == '31')
		$opcionesMetodoPago .= '<option value="31" selected>31 - Intermediario pagos</option>';
	else
		$opcionesMetodoPago .= '<option value="31">31 - Intermediario pagos</option>';
	
	if($formaPagoReceptor == '99')
		$opcionesMetodoPago .= '<option value="99" selected>99 - Por definir</option>';
	else
		$opcionesMetodoPago .= '<option value="99">99 - Por definir</option>';
	
	if($metodoPagoReceptor == 'PUE')
		$opcionesMetodoPago33 .= '<option value="PUE" selected>PUE - Pago en una sola exhibición</option>';
	else
		$opcionesMetodoPago33 .= '<option value="PUE">PUE - Pago en una sola exhibición</option>';
	
	if($metodoPagoReceptor == 'PPD')
		$opcionesMetodoPago33 .= '<option value="PPD" selected>PPD - Pago en parcialidades o diferido</option>';
	else
		$opcionesMetodoPago33 .= '<option value="PPD">PPD - Pago en parcialidades o diferido</option>';
	
	if($usoCFDIReceptor == 'G01')
		$opcionesUsoCFDI .= '<option value="G01" selected>G01 - Adquisición de mercancías</option>';
	else
		$opcionesUsoCFDI .= '<option value="G01">G01 - Adquisición de mercancías</option>';
	
	if($usoCFDIReceptor == 'G02')
		$opcionesUsoCFDI .= '<option value="G02" selected>G02 - Devoluciones, descuentos o bonificaciones</option>';
	else
		$opcionesUsoCFDI .= '<option value="G02">G02 - Devoluciones, descuentos o bonificaciones</option>';
	
	if($usoCFDIReceptor == 'G03')
		$opcionesUsoCFDI .= '<option value="G03" selected>G03 - Gastos en general</option>';
	else
		$opcionesUsoCFDI .= '<option value="G03">G03 - Gastos en general</option>';
	
	if($usoCFDIReceptor == 'I01')
		$opcionesUsoCFDI .= '<option value="I01" selected>I01 - Construcciones</option>';
	else
		$opcionesUsoCFDI .= '<option value="I01">I01 - Construcciones</option>';
	
	if($usoCFDIReceptor == 'I02')
		$opcionesUsoCFDI .= '<option value="I02" selected>I02 - Mobiliario y equipo de oficina por inversiones</option>';
	else
		$opcionesUsoCFDI .= '<option value="I02">I02 - Mobiliario y equipo de oficina por inversiones</option>';
	
	if($usoCFDIReceptor == 'I03')
		$opcionesUsoCFDI .= '<option value="I03" selected>I03 - Equipo de transporte</option>';
	else
		$opcionesUsoCFDI .= '<option value="I03">I03 - Equipo de transporte</option>';
	
	if($usoCFDIReceptor == 'I04')
		$opcionesUsoCFDI .= '<option value="I04" selected>I04 - Equipo de cómputo y accesorios</option>';
	else
		$opcionesUsoCFDI .= '<option value="I04">I04 - Equipo de cómputo y accesorios</option>';
	
	if($usoCFDIReceptor == 'I05')
		$opcionesUsoCFDI .= '<option value="I05" selected>I05 - Dados, troqueles, moldes, matrices y herramental</option>';
	else
		$opcionesUsoCFDI .= '<option value="I05">I05 - Dados, troqueles, moldes, matrices y herramental</option>';
	
	if($usoCFDIReceptor == 'I06')
		$opcionesUsoCFDI .= '<option value="I06" selected>I06 - Comunicaciones telefónicas</option>';
	else
		$opcionesUsoCFDI .= '<option value="I06">I06 - Comunicaciones telefónicas</option>';
	
	if($usoCFDIReceptor == 'I07')
		$opcionesUsoCFDI .= '<option value="I07" selected>I07 - Comunicaciones satelitales</option>';
	else
		$opcionesUsoCFDI .= '<option value="I07">I07 - Comunicaciones satelitales</option>';
	
	if($usoCFDIReceptor == 'I08')
		$opcionesUsoCFDI .= '<option value="I08" selected>I08 - Otra maquinaria y equipo</option>';
	else
		$opcionesUsoCFDI .= '<option value="I08">I08 - Otra maquinaria y equipo</option>';
	
	if($usoCFDIReceptor == 'D01')
		$opcionesUsoCFDI .= '<option value="D01" selected>D01 - Honorarios médicos, dentales y gastos hospitalarios</option>';
	else
		$opcionesUsoCFDI .= '<option value="D01">D01 - Honorarios médicos, dentales y gastos hospitalarios</option>';
	
	if($usoCFDIReceptor == 'D02')
		$opcionesUsoCFDI .= '<option value="D02" selected>D02 - Gastos médicos por incapacidad o discapacidad</option>';
	else
		$opcionesUsoCFDI .= '<option value="D02">D02 - Gastos médicos por incapacidad o discapacidad</option>';
	
	if($usoCFDIReceptor == 'D03')
		$opcionesUsoCFDI .= '<option value="D03" selected>D03 - Gastos funerales</option>';
	else
		$opcionesUsoCFDI .= '<option value="D03">D03 - Gastos funerales</option>';
	
	if($usoCFDIReceptor == 'D04')
		$opcionesUsoCFDI .= '<option value="D04" selected>D04 - Donativos</option>';
	else
		$opcionesUsoCFDI .= '<option value="D04">D04 - Donativos</option>';
	
	if($usoCFDIReceptor == 'D05')
		$opcionesUsoCFDI .= '<option value="D05" selected>D05 - Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>';
	else
		$opcionesUsoCFDI .= '<option value="D05">D05 - Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)</option>';
	
	if($usoCFDIReceptor == 'D06')
		$opcionesUsoCFDI .= '<option value="D06" selected>D06 - Aportaciones voluntarias al SAR</option>';
	else
		$opcionesUsoCFDI .= '<option value="D06">D06 - portaciones voluntarias al SAR</option>';
	
	if($usoCFDIReceptor == 'D07')
		$opcionesUsoCFDI .= '<option value="D07" selected>D07 - Primas por seguros de gastos médicos</option>';
	else
		$opcionesUsoCFDI .= '<option value="D07">D07 - Primas por seguros de gastos médicos</option>';
	
	if($usoCFDIReceptor == 'D08')
		$opcionesUsoCFDI .= '<option value="D08" selected>D08 - Gastos de transportación escolar obligatoria</option>';
	else
		$opcionesUsoCFDI .= '<option value="D08">D08 - Gastos de transportación escolar obligatoria</option>';
	
	if($usoCFDIReceptor == 'D09')
		$opcionesUsoCFDI .= '<option value="D09" selected>D09 - Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones</option>';
	else
		$opcionesUsoCFDI .= '<option value="D09">D09 - Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones</option>';
	
	if($usoCFDIReceptor == 'D10')
		$opcionesUsoCFDI .= '<option value="D10" selected>D10 - Pagos por servicios educativos (colegiaturas)</option>';
	else
		$opcionesUsoCFDI .= '<option value="D10">D10 - Pagos por servicios educativos (colegiaturas)</option>';
	
	if($configuracion['version_cfdi'] == '3.3')
	{
		if($usoCFDIReceptor == 'P01')
			$opcionesUsoCFDI .= '<option value="P01" selected>P01 - Por definir</option>';
		else
			$opcionesUsoCFDI .= '<option value="P01">P01 - Por definir</option>';
	}
	else if($configuracion['version_cfdi'] == '4.0')
	{
		if($usoCFDIReceptor == 'S01')
			$opcionesUsoCFDI .= '<option value="S01" selected>S01 - Sin efectos fiscales</option>';
		else
			$opcionesUsoCFDI .= '<option value="S01">S01 - Sin efectos fiscales</option>';
		
		if($usoCFDIReceptor == 'CP01')
			$opcionesUsoCFDI .= '<option value="CP01" selected>CP01 - Pagos</option>';
		else
			$opcionesUsoCFDI .= '<option value="CP01">CP01 - Pagos</option>';
		
		if($usoCFDIReceptor == 'CN01')
			$opcionesUsoCFDI .= '<option value="CN01" selected>CN01 - Nómina</option>';
		else
			$opcionesUsoCFDI .= '<option value="CN01">CN01 - Nómina</option>';
	}
	
	if($receptor_regimenfiscal == '601')
		$opcionesRegimenFiscalReceptor .= '<option value="601" selected>601 - General de Ley Personas Morales</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="601">601 - General de Ley Personas Morales</option>';
	
	if($receptor_regimenfiscal == '603')
		$opcionesRegimenFiscalReceptor .= '<option value="603" selected>603 - Personas Morales con Fines no Lucrativos</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="603">603 - Personas Morales con Fines no Lucrativos</option>';
	
	if($receptor_regimenfiscal == '605')
		$opcionesRegimenFiscalReceptor .= '<option value="605" selected>605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="605">605 - Sueldos y Salarios e Ingresos Asimilados a Salarios</option>';
	
	if($receptor_regimenfiscal == '606')
		$opcionesRegimenFiscalReceptor .= '<option value="606" selected>606 - Arrendamiento</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="606">606 - Arrendamiento</option>';
	
	if($receptor_regimenfiscal == '607')
		$opcionesRegimenFiscalReceptor .= '<option value="607" selected>607 - Régimen de Enajenación o Adquisición de Bienes</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="607">607 - Régimen de Enajenación o Adquisición de Bienes</option>';
	
	if($receptor_regimenfiscal == '608')
		$opcionesRegimenFiscalReceptor .= '<option value="608" selected>608 - Demás ingresos</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="608">608 - Demás ingresos</option>';
	
	if($receptor_regimenfiscal == '610')
		$opcionesRegimenFiscalReceptor .= '<option value="610" selected>610 - Residentes en el Extranjero sin Establecimiento Permanente en México</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="610">610 - Residentes en el Extranjero sin Establecimiento Permanente en México</option>';
	
	if($receptor_regimenfiscal == '611')
		$opcionesRegimenFiscalReceptor .= '<option value="611" selected>611 - Ingresos por Dividendos (socios y accionistas)</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="611">611 - Ingresos por Dividendos (socios y accionistas)</option>';
	
	if($receptor_regimenfiscal == '612')
		$opcionesRegimenFiscalReceptor .= '<option value="612" selected>612 - Personas Físicas con Actividades Empresariales y Profesionales</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="612">612 - Personas Físicas con Actividades Empresariales y Profesionales</option>';
	
	if($receptor_regimenfiscal == '614')
		$opcionesRegimenFiscalReceptor .= '<option value="614" selected>614 - Ingresos por intereses</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="614">614 - Ingresos por intereses</option>';
	
	if($receptor_regimenfiscal == '615')
		$opcionesRegimenFiscalReceptor .= '<option value="615" selected>615 - Régimen de los ingresos por obtención de premios</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="615">615 - Régimen de los ingresos por obtención de premios</option>';
	
	if($receptor_regimenfiscal == '616')
		$opcionesRegimenFiscalReceptor .= '<option value="616" selected>616 - Sin obligaciones fiscales</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="616">616 - Sin obligaciones fiscales</option>';
	
	if($receptor_regimenfiscal == '620')
		$opcionesRegimenFiscalReceptor .= '<option value="620" selected>620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="620">620 - Sociedades Cooperativas de Producción que optan por diferir sus ingresos</option>';
	
	if($receptor_regimenfiscal == '621')
		$opcionesRegimenFiscalReceptor .= '<option value="621" selected>621 - Incorporación Fiscal</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="621">621 - Incorporación Fiscal</option>';
	
	if($receptor_regimenfiscal == '622')
		$opcionesRegimenFiscalReceptor .= '<option value="622" selected>622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="622">622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>';
	
	if($receptor_regimenfiscal == '623')
		$opcionesRegimenFiscalReceptor .= '<option value="623" selected>623 - Opcional para Grupos de Sociedades</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="623">623 - Opcional para Grupos de Sociedades</option>';
	
	if($receptor_regimenfiscal == '624')
		$opcionesRegimenFiscalReceptor .= '<option value="624" selected>624 - Coordinados</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="624">624 - Coordinados</option>';
	
	if($receptor_regimenfiscal == '625')
		$opcionesRegimenFiscalReceptor .= '<option value="625" selected>625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="625">625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>';
	
	if($receptor_regimenfiscal == '626')
		$opcionesRegimenFiscalReceptor .= '<option value="626" selected>626 - Régimen Simplificado de Confianza</option>';
	else
		$opcionesRegimenFiscalReceptor .= '<option value="626">626 - Régimen Simplificado de Confianza</option>';
	
	$fragmentoReceptorCFDI40 = '';
	$notaFragmentoReceptorCFDI40 = '';
	$leyendaRazonSocialCFDI = ($idiomaRVLFECFDI == 'ES') ? 'Razón Social':'Business Name';
	
	if($configuracion['version_cfdi'] == '4.0')
	{
		$leyendaRazonSocialCFDI = ($idiomaRVLFECFDI == 'ES') ? '* Razón Social':'* Business Name';
		$fragmentoReceptorCFDI40 = '<div class="rowPaso2">
									<label><font color="'.$configuracion['color_texto_formulario'].'">'.(($idiomaRVLFECFDI == 'ES') ? '* Código Postal':'* Postal Code').'</label></font>
									<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="fr_receptor_domicilioFiscalReceptor" name="fr_receptor_domicilioFiscalReceptor" value="'.$receptor_domicilioFiscalReceptor.'" placeholder="'.(($idiomaRVLFECFDI == 'ES') ? 'Código postal de tu domicilio fiscal':'Postal code of your tax address').'" />
									<br/>
								</div>
								<div class="rowPaso2">
									<label><font color="'.$configuracion['color_texto_formulario'].'">'.(($idiomaRVLFECFDI == 'ES') ? '* Régimen Fiscal':'* Tax Regime').'</label></font>
									<select id="fr_receptor_regimenfiscal" name="fr_receptor_regimenfiscal" style="width: 55%; color: '.$configuracion['color_texto_controles_formulario'].';">'.$opcionesRegimenFiscalReceptor.'</select>
									<br/>
								</div>';
								
		$notaFragmentoReceptorCFDI40 = ($idiomaRVLFECFDI == 'ES') ? '<br/><center>Por disposición oficial del SAT, la razón social, el código postal y el régimen fiscal son obligatorios<br/>para emitir la nueva versión de Facturación Electrónica 4.0.</center>' : '<br/><center>By official provision of the SAT, the postal code and the tax regime are mandatory<br/>to issue the new version of Electronic Billing 4.0.</center>';
	}
	
	$formulario = '<center>
				<div id="fr_realvirtual_woocommerce_facturacion">
                    <div id="fr_paso_uno" style="width: 70%;">
                        <div style="background:'.$configuracion['color_fondo_encabezado'].'; height: 80px; line-height: 20px; margin-bottom: 0px;">
							<br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="6">'.(($idiomaRVLFECFDI == 'ES') ? 'Datos de Facturación':'Billing Information').'</font>
							<br/>
                            <font color="'.$configuracion['color_texto_encabezado'].'" size="6">'.(($idiomaRVLFECFDI == 'ES') ? 'CFDI '.$configuracion['version_cfdi'] : 'CFDI '.$configuracion['version_cfdi']).'</font>
						</div>
                        
						<div style="background-color:'.$configuracion['color_fondo_formulario'].';">
							<form name="fr_paso_uno_formulario_receptor" id="fr_paso_uno_formulario_receptor" action="'.esc_url(get_permalink()).'" method="post">
                                <table width="90%">
									<tr>
									<td>
										<br/>
										<div class="rowPaso2">
											<label><font color="'.$configuracion['color_texto_formulario'].'">* RFC</label></font>
											<input type="text" style="text-transform: uppercase; color: '.$configuracion['color_texto_controles_formulario'].';" id="fr_receptor_rfc" name="receptor_rfc" value="'.$receptor_rfc.'" placeholder="" maxlength="13" /><!--<button type="button" style="background-color: '.$configuracion['color_fondo_formulario'].';" id="fr_paso_dos_boton_buscar_cliente" name="paso_dos_boton_buscar_cliente" ><img id="fr_imagen_paso_dos_boton_buscar_cliente" name="imagen_paso_dos_boton_buscar_cliente" src="'.plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_buscar.png".'" width="24" height="24" alt="Buscar" /></button>-->
											<br/>
											<label><font color="'.$configuracion['color_texto_formulario'].'">'.$leyendaRazonSocialCFDI.'</label></font>
											<input type="text" style="color: '.$configuracion['color_texto_controles_formulario'].';" id="fr_receptor_razon_social" name="receptor_razon_social" value="'.$receptor_razon_social.'" placeholder="" />
											<br/>'.$fragmentoReceptorCFDI40.'
											<font color="'.$configuracion['color_texto_formulario'].'" size="2"><label>'.$usoCFDIHabilitadoLB.'</label></font>
											<font color="'.$configuracion['color_texto_formulario'].'" size="2">
												<select id="fr_receptor_uso_cfdi" style="width: 55%; color: '.$configuracion['color_texto_controles_formulario'].';" '.$usoCFDIHabilitado.'>'.$opcionesUsoCFDI.'</select>
											</font>
											<div style="display:none;">
											<font color="'.$configuracion['color_texto_formulario'].'" size="2"><label>'.$metodosPagoHabilitadoLB.'</label></font>
											<font color="'.$configuracion['color_texto_formulario'].'" size="2">
												<select id="fr_receptor_metodos_pago" style="width: 55%; color: '.$configuracion['color_texto_controles_formulario'].';" '.$metodosPagoHabilitado.'>'.$opcionesMetodoPago.'</select>
											</font>
											<br/>
											<font color="'.$configuracion['color_texto_formulario'].'" size="2"><label>'.$metodosPagoHabilitado33LB.'</label></font>
											<font color="'.$configuracion['color_texto_formulario'].'" size="2">
												<select id="fr_receptor_metodos_pago33" style="width: 55%; color: '.$configuracion['color_texto_controles_formulario'].';" '.$metodosPagoHabilitado33.'>'.$opcionesMetodoPago33.'</select>
											</font>
											</div>
											<br/>
											'.$notaFragmentoReceptorCFDI40.'
											<br/>
										</div>
										<center>
											<div>
												<input type="submit" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="fr_paso_uno_boton_siguiente" name="fr_paso_uno_boton_siguiente" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Guardar':'Save').'" />
												<img id="fr_cargandoPaso1" src="'.plugin_dir_url( __FILE__ )."/assets/realvirtual_woocommerce_cargando.gif".'" alt="Cargando" height="32" width="32" style="visibility: hidden;">
											</div>
											<br/>
										</center>
									</td>
								</table>
								<br/>
                            </form>
							<br/>
                        </div>
						<br/><br/>
                    </div>
				</div></center>
				
				<div id="fr_ventanaModal" class="modal">
					<div class="modal-content">
						<span class="close">&times;</span>
						<br/>
						<center>
							<font color="#000000" size="5"><b>
								<div id="fr_tituloModal"></div>
							</b></font>
							<br/>
							<font color="#000000" size="3">
								<div id="fr_textoModal"></div>
							</font>
							<br/>
							<input type="button" style="background-color: '.$configuracion['color_boton'].'; color:'.$configuracion['color_texto_boton'].';" class="boton" id="fr_botonModal" value="'.(($idiomaRVLFECFDI == 'ES') ? 'Aceptar':'Accept').'" />
						</center>
					</div>
				</div>';
				
	return $formulario;
}

add_action('wp_ajax_realvirtual_woocommerce_paso_uno_receptor', 'realvirtual_woocommerce_paso_uno_receptor_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_paso_uno_receptor', 'realvirtual_woocommerce_paso_uno_receptor_callback');

function realvirtual_woocommerce_paso_uno_receptor_callback()
{
    global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
    
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$receptor_rfc = sanitize_text_field($_POST['receptor_rfc']);
	update_post_meta($post->ID, 'receptor_rfc', $receptor_rfc);
	
	$receptor_razon_social = sanitize_text_field($_POST['receptor_razon_social']);
	update_post_meta($post->ID, 'receptor_razon_social', $receptor_razon_social);
	
	$receptor_domicilioFiscalReceptor = sanitize_text_field($_POST['receptor_domicilioFiscalReceptor']);
	update_post_meta($post->ID, 'receptor_domicilioFiscalReceptor', $receptor_domicilioFiscalReceptor);
	
	$receptor_regimenfiscal = sanitize_text_field($_POST['receptor_regimenfiscal']);
	update_post_meta($post->ID, 'receptor_regimenfiscal', $receptor_regimenfiscal);
	
	$receptor_uso_cfdi = sanitize_text_field($_POST['receptor_uso_cfdi']);
	update_post_meta($post->ID, 'receptor_uso_cfdi', $receptor_uso_cfdi);
	
	$receptor_metodos_pago = sanitize_text_field($_POST['receptor_metodos_pago']);
	update_post_meta($post->ID, 'receptor_metodos_pago', $receptor_metodos_pago);
	
	$receptor_metodos_pago33 = sanitize_text_field($_POST['receptor_metodos_pago33']);
	update_post_meta($post->ID, 'receptor_metodos_pago33', $receptor_metodos_pago33);
	
	$fr_version_cfdi = sanitize_text_field($_POST['fr_version_cfdi']);
	update_post_meta($post->ID, 'fr_version_cfdi', $fr_version_cfdi);
	
	$receptor_rfc 	= trim($_POST['receptor_rfc']);
	$receptor_razon_social = trim($_POST['receptor_razon_social']);
	$receptor_domicilioFiscalReceptor = trim($_POST['receptor_domicilioFiscalReceptor']);
	$receptor_regimenfiscal = trim($_POST['receptor_regimenfiscal']);
	$receptor_uso_cfdi = trim($_POST['receptor_uso_cfdi']);
	$receptor_metodos_pago = trim($_POST['receptor_metodos_pago']);
	$receptor_metodos_pago33 = trim($_POST['receptor_metodos_pago33']);
	$fr_version_cfdi = trim($_POST['fr_version_cfdi']);
	
	if($receptor_rfc == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ingresa tu RFC.':'Enter your RFC.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!preg_match("/^([A-Z]|&|Ñ){3,4}[0-9]{2}[0-1][0-9][0-3][0-9]([A-Z]|[0-9]){2}([0-9]|A){1}$/", $receptor_rfc))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El RFC tiene un formato inválido.':'The RFC has an invalid format.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($receptor_uso_cfdi == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona un Uso de CFDI.':'Select a CFDI Use.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($receptor_metodos_pago == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona una Forma de Pago.':'Select a Payment Way.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($receptor_metodos_pago33 == '')
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona un Método de Pago.':'Select a Payment Method.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($fr_version_cfdi == '4.0')
	{
		if($receptor_razon_social == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ingresa tu Razón Social.':'Enter your Business Name.'
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
		
		if($receptor_domicilioFiscalReceptor == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? 'Ingresa tu código postal.':'Enter your Postal Code.'
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}

		if($receptor_regimenfiscal == '')
		{
			$respuesta = array
			(
				'success' => false,
				'message' => ($idiomaRVLFECFDI == 'ES') ? 'Selecciona tu Régimen Fiscal.':'Select your Fiscal Regime.'
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
	}
	
	//Guardado de los datos fiscales del cliente
	$respuesta = array
	(
		'success' => false,
		'message' => ($idiomaRVLFECFDI == 'ES') ? 'Inicia sesión para poder guardar tu información fiscal' : 'Sign in to save your tax information'
	);
	
	if(is_user_logged_in())
	{
		$idUser = get_current_user_id();
		
		//Creación de la base de datos
		creacion_base_datos();
		
		//Guardado de datos fiscales
		eliminarDatosFiscales($idUser);
		guardarDatosFiscales($idUser, $receptor_rfc, $receptor_razon_social, $receptor_domicilioFiscalReceptor,
			$receptor_regimenfiscal, $receptor_uso_cfdi, $receptor_metodos_pago, $receptor_metodos_pago33);
		
		$respuesta = array
		(
			'success' => true,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'Datos guardados con éxito.' : 'Data saved successfully.'
		);
	}
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

function guardarDatosFiscales($idUser, $receptor_rfc, $receptor_razon_social, $receptor_domicilioFiscalReceptor,
			$receptor_regimenfiscal, $receptor_uso_cfdi, $receptor_metodos_pago, $receptor_metodos_pago33)
{     
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'realvirtual_datosfiscales';     
	$wpdb->insert($table_name, array(
		'id_user' => $idUser,
		'rfc' => $receptor_rfc,
		'razon_social' => $receptor_razon_social,
		'domicilio_fiscal' => $receptor_domicilioFiscalReceptor,
		'regimen_fiscal' => $receptor_regimenfiscal,
		'uso_cfdi' => $receptor_uso_cfdi,
		'forma_pago' => $receptor_metodos_pago,
		'metodo_pago' => $receptor_metodos_pago33));
}

function obtenerDatosFiscales($idUser)
{
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'realvirtual_datosfiscales';
	$resultado = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id_user = '".$idUser."'"));
	return $resultado;
}

function eliminarDatosFiscales($idUser)
{
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'realvirtual_datosfiscales';
	$wpdb->delete($table_name, array('id_user' => $idUser));
}

function obtenerDatosFiscalesClientes()
{
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'realvirtual_datosfiscales';
	$resultado = $wpdb->get_results($wpdb->prepare("SELECT id_user, rfc, razon_social, domicilio_fiscal, regimen_fiscal, uso_cfdi FROM $table_name ORDER BY rfc"));
	return $resultado;
}

function ObtenerDatosXml($xmlContenido , $Nodo , $atributo)
{
    $Encontrado = "";
    $Nodos = "";
	$xml=new XMLReader();
	$xml->XML($xmlContenido);	
    while ($xml->read()) {
        if ($xml->nodeType == XMLReader::ELEMENT) {
            $Nodos = $xml->localName;
            if ($xml->localName == $Nodo) {
                if ($xml->hasAttributes) {
                    while ($xml->moveToNextAttribute()) {
                        if ($xml->name == $atributo) {
                            $Encontrado = $xml->value;
                            break;
                        }
                    }
                    break;
                }
            }
        }
    }
	$xml->close();
    return $Encontrado;
}

add_action('wp_ajax_realvirtual_woocommerce_guardar_configuracion_bayer', 'realvirtual_woocommerce_guardar_configuracion_bayer_callback');

function realvirtual_woocommerce_guardar_configuracion_bayer_callback()
{
	global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;

	$rvcfdi_bayer_facturacion_c_clase_documento = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_c_clase_documento']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_c_clase_documento', $rvcfdi_bayer_facturacion_c_clase_documento);
	
	$rvcfdi_bayer_facturacion_c_sociedad = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_c_sociedad']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_c_sociedad', $rvcfdi_bayer_facturacion_c_sociedad);
	
	$rvcfdi_bayer_facturacion_c_moneda = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_c_moneda']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_c_moneda', $rvcfdi_bayer_facturacion_c_moneda);
	
	$rvcfdi_bayer_facturacion_c_tc_cab_doc = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_c_tc_cab_doc']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_c_tc_cab_doc', $rvcfdi_bayer_facturacion_c_tc_cab_doc);
	
	$rvcfdi_bayer_facturacion_p_cuenta = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_cuenta']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_cuenta', $rvcfdi_bayer_facturacion_p_cuenta);
	
	$rvcfdi_bayer_facturacion_p_division = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_division']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_division', $rvcfdi_bayer_facturacion_p_division);
	
	$rvcfdi_bayer_facturacion_p_ce_be = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_ce_be']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_ce_be', $rvcfdi_bayer_facturacion_p_ce_be);
	
	$rvcfdi_bayer_facturacion_p_texto = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_texto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_texto', $rvcfdi_bayer_facturacion_p_texto);
	
	$rvcfdi_bayer_facturacion_p_pais_destinatario = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_pais_destinatario']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_pais_destinatario', $rvcfdi_bayer_facturacion_p_pais_destinatario);
	
	$rvcfdi_bayer_facturacion_p_linea_de_producto = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_linea_de_producto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_linea_de_producto', $rvcfdi_bayer_facturacion_p_linea_de_producto);
	
	$rvcfdi_bayer_facturacion_p_grupo_de_producto = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_grupo_de_producto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_grupo_de_producto', $rvcfdi_bayer_facturacion_p_grupo_de_producto);
	
	$rvcfdi_bayer_facturacion_p_centro = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_centro']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_centro', $rvcfdi_bayer_facturacion_p_centro);
	
	$rvcfdi_bayer_facturacion_p_cliente = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_cliente']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_cliente', $rvcfdi_bayer_facturacion_p_cliente);
	
	$rvcfdi_bayer_facturacion_p_organiz_ventas = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_organiz_ventas']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_organiz_ventas', $rvcfdi_bayer_facturacion_p_organiz_ventas);
	
	$rvcfdi_bayer_facturacion_p_canal_distrib = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_canal_distrib']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_canal_distrib', $rvcfdi_bayer_facturacion_p_canal_distrib);
	
	$rvcfdi_bayer_facturacion_p_zoha_de_ventas = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_zoha_de_ventas']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_zoha_de_ventas', $rvcfdi_bayer_facturacion_p_zoha_de_ventas);
	
	$rvcfdi_bayer_facturacion_p_oficina_ventas = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_oficina_ventas']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_oficina_ventas', $rvcfdi_bayer_facturacion_p_oficina_ventas);
	
	$rvcfdi_bayer_facturacion_p_ramo = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_ramo']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_ramo', $rvcfdi_bayer_facturacion_p_ramo);
	
	$rvcfdi_bayer_facturacion_p_grupo = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_grupo']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_grupo', $rvcfdi_bayer_facturacion_p_grupo);
	
	$rvcfdi_bayer_facturacion_p_gr_vendedores = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_gr_vendedores']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_gr_vendedores', $rvcfdi_bayer_facturacion_p_gr_vendedores);
	
	$rvcfdi_bayer_facturacion_p_atributo_1_sector = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_atributo_1_sector']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_atributo_1_sector', $rvcfdi_bayer_facturacion_p_atributo_1_sector);
	
	$rvcfdi_bayer_facturacion_p_atributo_2_sector = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_atributo_2_sector']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_atributo_2_sector', $rvcfdi_bayer_facturacion_p_atributo_2_sector);
	
	$rvcfdi_bayer_facturacion_p_clase_factura = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_clase_factura']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_clase_factura', $rvcfdi_bayer_facturacion_p_clase_factura);
	
	$rvcfdi_bayer_financiero_c_clase_de_documento = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_clase_de_documento']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_clase_de_documento', $rvcfdi_bayer_financiero_c_clase_de_documento);
	
	$rvcfdi_bayer_financiero_c_sociedad = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_sociedad']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_sociedad', $rvcfdi_bayer_financiero_c_sociedad);
	
	$rvcfdi_bayer_financiero_c_moneda = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_moneda']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_moneda', $rvcfdi_bayer_financiero_c_moneda);
	
	$rvcfdi_bayer_financiero_c_t_xt_cab_doc = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_t_xt_cab_doc']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_t_xt_cab_doc', $rvcfdi_bayer_financiero_c_t_xt_cab_doc);
	
	$rvcfdi_bayer_financiero_c_cuenta_bancaria = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_cuenta_bancaria']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_cuenta_bancaria', $rvcfdi_bayer_financiero_c_cuenta_bancaria);
	
	$rvcfdi_bayer_financiero_c_texto = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_texto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_texto', $rvcfdi_bayer_financiero_c_texto);
	
	$rvcfdi_bayer_financiero_c_division = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_division']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_division', $rvcfdi_bayer_financiero_c_division);
	
	$rvcfdi_bayer_financiero_c_cebe = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_cebe']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_cebe', $rvcfdi_bayer_financiero_c_cebe);
	
	$rvcfdi_bayer_financiero_c_cliente = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_cliente']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_cliente', $rvcfdi_bayer_financiero_c_cliente);
	
	$rvcfdi_bayer_financiero_p_cuenta = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_cuenta']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_cuenta', $rvcfdi_bayer_financiero_p_cuenta);
	
	$rvcfdi_bayer_financiero_p_ind_impuestos = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_ind_impuestos']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_ind_impuestos', $rvcfdi_bayer_financiero_p_ind_impuestos);
	
	$rvcfdi_bayer_financiero_p_division = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_division']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_division', $rvcfdi_bayer_financiero_p_division);
	
	$rvcfdi_bayer_financiero_p_texto = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_texto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_texto', $rvcfdi_bayer_financiero_p_texto);
	
	$rvcfdi_bayer_financiero_p_cebe = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_cebe']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_cebe', $rvcfdi_bayer_financiero_p_cebe);
	
	$rvcfdi_bayer_financiero_p_pais_destinatario = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_pais_destinatario']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_pais_destinatario', $rvcfdi_bayer_financiero_p_pais_destinatario);
	
	$rvcfdi_bayer_financiero_p_linea_de_producto = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_linea_de_producto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_linea_de_producto', $rvcfdi_bayer_financiero_p_linea_de_producto);
	
	$rvcfdi_bayer_financiero_p_grupo_de_proudcto = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_grupo_de_proudcto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_grupo_de_proudcto', $rvcfdi_bayer_financiero_p_grupo_de_proudcto);
	
	$rvcfdi_bayer_financiero_p_centro = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_centro']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_centro', $rvcfdi_bayer_financiero_p_centro);
	
	$rvcfdi_bayer_financiero_p_articulo = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_articulo']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_articulo', $rvcfdi_bayer_financiero_p_articulo);
	
	$rvcfdi_bayer_financiero_p_zona_de_ventas = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_zona_de_ventas']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_zona_de_ventas', $rvcfdi_bayer_financiero_p_zona_de_ventas);
	
	$rvcfdi_bayer_financiero_p_material = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_material']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_material', $rvcfdi_bayer_financiero_p_material);
	
	$rvcfdi_bayer_financiero_p_atributo_2_sector = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_atributo_2_sector']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_atributo_2_sector', $rvcfdi_bayer_financiero_p_atributo_2_sector);
	
    $configuracion = array
	(
		'rvcfdi_bayer_facturacion_c_clase_documento' => $_POST['rvcfdi_bayer_facturacion_c_clase_documento'],
		'rvcfdi_bayer_facturacion_c_sociedad' => $_POST['rvcfdi_bayer_facturacion_c_sociedad'],
		'rvcfdi_bayer_facturacion_c_moneda' => $_POST['rvcfdi_bayer_facturacion_c_moneda'],
		'rvcfdi_bayer_facturacion_c_tc_cab_doc' => $_POST['rvcfdi_bayer_facturacion_c_tc_cab_doc'],
		'rvcfdi_bayer_facturacion_p_cuenta' => $_POST['rvcfdi_bayer_facturacion_p_cuenta'],
		'rvcfdi_bayer_facturacion_p_division' => $_POST['rvcfdi_bayer_facturacion_p_division'],
		'rvcfdi_bayer_facturacion_p_ce_be' => $_POST['rvcfdi_bayer_facturacion_p_ce_be'],
		'rvcfdi_bayer_facturacion_p_texto' => $_POST['rvcfdi_bayer_facturacion_p_texto'],
		'rvcfdi_bayer_facturacion_p_pais_destinatario' => $_POST['rvcfdi_bayer_facturacion_p_pais_destinatario'],
		'rvcfdi_bayer_facturacion_p_linea_de_producto' => $_POST['rvcfdi_bayer_facturacion_p_linea_de_producto'],
		'rvcfdi_bayer_facturacion_p_grupo_de_producto' => $_POST['rvcfdi_bayer_facturacion_p_grupo_de_producto'],
		'rvcfdi_bayer_facturacion_p_centro' => $_POST['rvcfdi_bayer_facturacion_p_centro'],
		'rvcfdi_bayer_facturacion_p_cliente' => $_POST['rvcfdi_bayer_facturacion_p_cliente'],
		'rvcfdi_bayer_facturacion_p_organiz_ventas' => $_POST['rvcfdi_bayer_facturacion_p_organiz_ventas'],
		'rvcfdi_bayer_facturacion_p_canal_distrib' => $_POST['rvcfdi_bayer_facturacion_p_canal_distrib'],
		'rvcfdi_bayer_facturacion_p_zoha_de_ventas' => $_POST['rvcfdi_bayer_facturacion_p_zoha_de_ventas'],
		'rvcfdi_bayer_facturacion_p_oficina_ventas' => $_POST['rvcfdi_bayer_facturacion_p_oficina_ventas'],
		'rvcfdi_bayer_facturacion_p_ramo' => $_POST['rvcfdi_bayer_facturacion_p_ramo'],
		'rvcfdi_bayer_facturacion_p_grupo' => $_POST['rvcfdi_bayer_facturacion_p_grupo'],
		'rvcfdi_bayer_facturacion_p_gr_vendedores' => $_POST['rvcfdi_bayer_facturacion_p_gr_vendedores'],
		'rvcfdi_bayer_facturacion_p_atributo_1_sector' => $_POST['rvcfdi_bayer_facturacion_p_atributo_1_sector'],
		'rvcfdi_bayer_facturacion_p_atributo_2_sector' => $_POST['rvcfdi_bayer_facturacion_p_atributo_2_sector'],
		'rvcfdi_bayer_facturacion_p_clase_factura' => $_POST['rvcfdi_bayer_facturacion_p_clase_factura'],
		'rvcfdi_bayer_financiero_c_clase_de_documento' => $_POST['rvcfdi_bayer_financiero_c_clase_de_documento'],
		'rvcfdi_bayer_financiero_c_sociedad' => $_POST['rvcfdi_bayer_financiero_c_sociedad'],
		'rvcfdi_bayer_financiero_c_moneda' => $_POST['rvcfdi_bayer_financiero_c_moneda'],
		'rvcfdi_bayer_financiero_c_t_xt_cab_doc' => $_POST['rvcfdi_bayer_financiero_c_t_xt_cab_doc'],
		'rvcfdi_bayer_financiero_c_cuenta_bancaria' => $_POST['rvcfdi_bayer_financiero_c_cuenta_bancaria'],
		'rvcfdi_bayer_financiero_c_texto' => $_POST['rvcfdi_bayer_financiero_c_texto'],
		'rvcfdi_bayer_financiero_c_division' => $_POST['rvcfdi_bayer_financiero_c_division'],
		'rvcfdi_bayer_financiero_c_cebe' => $_POST['rvcfdi_bayer_financiero_c_cebe'],
		'rvcfdi_bayer_financiero_c_cliente' => $_POST['rvcfdi_bayer_financiero_c_cliente'],
		'rvcfdi_bayer_financiero_p_cuenta' => $_POST['rvcfdi_bayer_financiero_p_cuenta'],
		'rvcfdi_bayer_financiero_p_ind_impuestos' => $_POST['rvcfdi_bayer_financiero_p_ind_impuestos'],
		'rvcfdi_bayer_financiero_p_division' => $_POST['rvcfdi_bayer_financiero_p_division'],
		'rvcfdi_bayer_financiero_p_texto' => $_POST['rvcfdi_bayer_financiero_p_texto'],
		'rvcfdi_bayer_financiero_p_cebe' => $_POST['rvcfdi_bayer_financiero_p_cebe'],
		'rvcfdi_bayer_financiero_p_pais_destinatario' => $_POST['rvcfdi_bayer_financiero_p_pais_destinatario'],
		'rvcfdi_bayer_financiero_p_linea_de_producto' => $_POST['rvcfdi_bayer_financiero_p_linea_de_producto'],
		'rvcfdi_bayer_financiero_p_grupo_de_proudcto' => $_POST['rvcfdi_bayer_financiero_p_grupo_de_proudcto'],
		'rvcfdi_bayer_financiero_p_centro' => $_POST['rvcfdi_bayer_financiero_p_centro'],
		'rvcfdi_bayer_financiero_p_articulo' => $_POST['rvcfdi_bayer_financiero_p_articulo'],
		'rvcfdi_bayer_financiero_p_zona_de_ventas' => $_POST['rvcfdi_bayer_financiero_p_zona_de_ventas'],
		'rvcfdi_bayer_financiero_p_material' => $_POST['rvcfdi_bayer_financiero_p_material'],
		'rvcfdi_bayer_financiero_p_atributo_2_sector' => $_POST['rvcfdi_bayer_financiero_p_atributo_2_sector']
    );

	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	if(!($cuenta['rfc'] != '' && $cuenta['usuario'] != '' && $cuenta['clave'] != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'No se puede guardar la configuración porque es necesario antes ingresar correctamente tu RFC, Usuario y Clave Cifrada en la sección <b>Mi Cuenta</b>.':'The configuration can not be saved because it is necessary to correctly enter your RFC, User and Coded Key in the <b>My Account</b> section.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
    $guardado = RealVirtualWooCommerceConfiguracionBayer::guardarConfiguracion($configuracion, $cuenta['rfc'], $cuenta['usuario'], $cuenta['clave'], $urlSistemaAsociado, $idiomaRVLFECFDI);
	
    $respuesta = array
	(
       'success' => $guardado->success,
	   'message' => $guardado->message
    );
	
    echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_bayer_reporte_facturacion', 'realvirtual_woocommerce_bayer_reporte_facturacion_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_bayer_reporte_facturacion', 'realvirtual_woocommerce_bayer_reporte_facturacion_callback');

function realvirtual_woocommerce_bayer_reporte_facturacion_callback()
{
    global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
    
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$rvcfdi_bayer_facturacion_c_clase_documento = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_c_clase_documento']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_c_clase_documento', $rvcfdi_bayer_facturacion_c_clase_documento);
	
	$rvcfdi_bayer_facturacion_c_sociedad = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_c_sociedad']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_c_sociedad', $rvcfdi_bayer_facturacion_c_sociedad);
	
	$rvcfdi_bayer_facturacion_c_moneda = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_c_moneda']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_c_moneda', $rvcfdi_bayer_facturacion_c_moneda);
	
	$rvcfdi_bayer_facturacion_c_tc_cab_doc = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_c_tc_cab_doc']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_c_tc_cab_doc', $rvcfdi_bayer_facturacion_c_tc_cab_doc);
	
	$rvcfdi_bayer_facturacion_p_cuenta = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_cuenta']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_cuenta', $rvcfdi_bayer_facturacion_p_cuenta);
	
	$rvcfdi_bayer_facturacion_p_division = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_division']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_division', $rvcfdi_bayer_facturacion_p_division);
	
	$rvcfdi_bayer_facturacion_p_ce_be = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_ce_be']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_ce_be', $rvcfdi_bayer_facturacion_p_ce_be);
	
	$rvcfdi_bayer_facturacion_p_texto = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_texto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_texto', $rvcfdi_bayer_facturacion_p_texto);
	
	$rvcfdi_bayer_facturacion_p_pais_destinatario = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_pais_destinatario']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_pais_destinatario', $rvcfdi_bayer_facturacion_p_pais_destinatario);
	
	$rvcfdi_bayer_facturacion_p_linea_de_producto = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_linea_de_producto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_linea_de_producto', $rvcfdi_bayer_facturacion_p_linea_de_producto);
	
	$rvcfdi_bayer_facturacion_p_grupo_de_producto = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_grupo_de_producto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_grupo_de_producto', $rvcfdi_bayer_facturacion_p_grupo_de_producto);
	
	$rvcfdi_bayer_facturacion_p_centro = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_centro']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_centro', $rvcfdi_bayer_facturacion_p_centro);
	
	$rvcfdi_bayer_facturacion_p_cliente = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_cliente']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_cliente', $rvcfdi_bayer_facturacion_p_cliente);
	
	$rvcfdi_bayer_facturacion_p_organiz_ventas = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_organiz_ventas']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_organiz_ventas', $rvcfdi_bayer_facturacion_p_organiz_ventas);
	
	$rvcfdi_bayer_facturacion_p_canal_distrib = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_canal_distrib']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_canal_distrib', $rvcfdi_bayer_facturacion_p_canal_distrib);
	
	$rvcfdi_bayer_facturacion_p_zoha_de_ventas = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_zoha_de_ventas']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_zoha_de_ventas', $rvcfdi_bayer_facturacion_p_zoha_de_ventas);
	
	$rvcfdi_bayer_facturacion_p_oficina_ventas = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_oficina_ventas']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_oficina_ventas', $rvcfdi_bayer_facturacion_p_oficina_ventas);
	
	$rvcfdi_bayer_facturacion_p_ramo = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_ramo']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_ramo', $rvcfdi_bayer_facturacion_p_ramo);
	
	$rvcfdi_bayer_facturacion_p_grupo = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_grupo']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_grupo', $rvcfdi_bayer_facturacion_p_grupo);
	
	$rvcfdi_bayer_facturacion_p_gr_vendedores = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_gr_vendedores']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_gr_vendedores', $rvcfdi_bayer_facturacion_p_gr_vendedores);
	
	$rvcfdi_bayer_facturacion_p_atributo_1_sector = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_atributo_1_sector']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_atributo_1_sector', $rvcfdi_bayer_facturacion_p_atributo_1_sector);
	
	$rvcfdi_bayer_facturacion_p_atributo_2_sector = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_atributo_2_sector']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_atributo_2_sector', $rvcfdi_bayer_facturacion_p_atributo_2_sector);
		
	$rvcfdi_bayer_facturacion_p_clase_factura = sanitize_text_field($_POST['rvcfdi_bayer_facturacion_p_clase_factura']);
	update_post_meta($post->ID, 'rvcfdi_bayer_facturacion_p_clase_factura', $rvcfdi_bayer_facturacion_p_clase_factura);
	
	$fg_dia_inicio_bayer_facturacion = sanitize_text_field($_POST['fg_dia_inicio_bayer_facturacion']);
	update_post_meta($post->ID, 'fg_dia_inicio_bayer_facturacion', $fg_dia_inicio_bayer_facturacion);
	
	$fg_mes_inicio_bayer_facturacion = sanitize_text_field($_POST['fg_mes_inicio_bayer_facturacion']);
	update_post_meta($post->ID, 'fg_mes_inicio_bayer_facturacion', $fg_mes_inicio_bayer_facturacion);
	
	$fg_año_inicio_bayer_facturacion = sanitize_text_field($_POST['fg_año_inicio_bayer_facturacion']);
	update_post_meta($post->ID, 'fg_año_inicio_bayer_facturacion', $fg_año_inicio_bayer_facturacion);
	
	$fg_dia_fin_bayer_facturacion = sanitize_text_field($_POST['fg_dia_fin_bayer_facturacion']);
	update_post_meta($post->ID, 'fg_dia_fin_bayer_facturacion', $fg_dia_fin_bayer_facturacion);
	
	$fg_mes_fin_bayer_facturacion = sanitize_text_field($_POST['fg_mes_fin_bayer_facturacion']);
	update_post_meta($post->ID, 'fg_mes_fin_bayer_facturacion', $fg_mes_fin_bayer_facturacion);
	
	$fg_año_fin_bayer_facturacion = sanitize_text_field($_POST['fg_año_fin_bayer_facturacion']);
	update_post_meta($post->ID, 'fg_año_fin_bayer_facturacion', $fg_año_fin_bayer_facturacion);
	
	$rvcfdi_bayer_facturacion_c_clase_documento 	= trim($_POST['rvcfdi_bayer_facturacion_c_clase_documento']);
	$rvcfdi_bayer_facturacion_c_sociedad 			= trim($_POST['rvcfdi_bayer_facturacion_c_sociedad']);
	$rvcfdi_bayer_facturacion_c_moneda 				= trim($_POST['rvcfdi_bayer_facturacion_c_moneda']);
	$rvcfdi_bayer_facturacion_c_tc_cab_doc 			= trim($_POST['rvcfdi_bayer_facturacion_c_tc_cab_doc']);
	$rvcfdi_bayer_facturacion_p_cuenta 				= trim($_POST['rvcfdi_bayer_facturacion_p_cuenta']);
	$rvcfdi_bayer_facturacion_p_division			= trim($_POST['rvcfdi_bayer_facturacion_p_division']);
	$rvcfdi_bayer_facturacion_p_ce_be 				= trim($_POST['rvcfdi_bayer_facturacion_p_ce_be']);
	$rvcfdi_bayer_facturacion_p_texto 				= trim($_POST['rvcfdi_bayer_facturacion_p_texto']);
	$rvcfdi_bayer_facturacion_p_pais_destinatario 	= trim($_POST['rvcfdi_bayer_facturacion_p_pais_destinatario']);
	$rvcfdi_bayer_facturacion_p_linea_de_producto 	= trim($_POST['rvcfdi_bayer_facturacion_p_linea_de_producto']);
	$rvcfdi_bayer_facturacion_p_grupo_de_producto 	= trim($_POST['rvcfdi_bayer_facturacion_p_grupo_de_producto']);
	$rvcfdi_bayer_facturacion_p_centro 				= trim($_POST['rvcfdi_bayer_facturacion_p_centro']);
	$rvcfdi_bayer_facturacion_p_cliente 			= trim($_POST['rvcfdi_bayer_facturacion_p_cliente']);
	$rvcfdi_bayer_facturacion_p_organiz_ventas		= trim($_POST['rvcfdi_bayer_facturacion_p_organiz_ventas']);
	$rvcfdi_bayer_facturacion_p_canal_distrib		= trim($_POST['rvcfdi_bayer_facturacion_p_canal_distrib']);
	$rvcfdi_bayer_facturacion_p_zoha_de_ventas		= trim($_POST['rvcfdi_bayer_facturacion_p_zoha_de_ventas']);
	$rvcfdi_bayer_facturacion_p_oficina_ventas		= trim($_POST['rvcfdi_bayer_facturacion_p_oficina_ventas']);
	$rvcfdi_bayer_facturacion_p_ramo				= trim($_POST['rvcfdi_bayer_facturacion_p_ramo']);
	$rvcfdi_bayer_facturacion_p_grupo				= trim($_POST['rvcfdi_bayer_facturacion_p_grupo']);
	$rvcfdi_bayer_facturacion_p_gr_vendedores		= trim($_POST['rvcfdi_bayer_facturacion_p_gr_vendedores']);
	$rvcfdi_bayer_facturacion_p_atributo_1_sector	= trim($_POST['rvcfdi_bayer_facturacion_p_atributo_1_sector']);
	$rvcfdi_bayer_facturacion_p_atributo_2_sector	= trim($_POST['rvcfdi_bayer_facturacion_p_atributo_2_sector']);
	$rvcfdi_bayer_facturacion_p_clase_factura		= trim($_POST['rvcfdi_bayer_facturacion_p_clase_factura']);
	$fg_dia_inicio_bayer_facturacion				= trim($_POST['fg_dia_inicio_bayer_facturacion']);
	$fg_mes_inicio_bayer_facturacion				= trim($_POST['fg_mes_inicio_bayer_facturacion']);
	$fg_año_inicio_bayer_facturacion				= trim($_POST['fg_año_inicio_bayer_facturacion']);
	$fg_dia_fin_bayer_facturacion					= trim($_POST['fg_dia_fin_bayer_facturacion']);
	$fg_mes_fin_bayer_facturacion					= trim($_POST['fg_mes_fin_bayer_facturacion']);
	$fg_año_fin_bayer_facturacion					= trim($_POST['fg_año_fin_bayer_facturacion']);
	
	if(!checkdate($fg_mes_inicio_bayer_facturacion, $fg_dia_inicio_bayer_facturacion, $fg_año_inicio_bayer_facturacion))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La fecha de inicio tiene un fomato inválido. Selecciona una fecha de inicio válida.':'The start date has an invalid format. Please select a valid start date.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!checkdate($fg_mes_fin_bayer_facturacion, $fg_dia_fin_bayer_facturacion, $fg_año_fin_bayer_facturacion))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La fecha final tiene un fomato inválido. Selecciona una fecha final válida.':'The final date has an invalid format. Please select a valid final date.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$time_input_inicio = strtotime($fg_año_inicio_bayer_facturacion."-".$fg_mes_inicio_bayer_facturacion."-".$fg_dia_inicio_bayer_facturacion); 
	$date_input_inicio = getDate($time_input_inicio);
	$time_input_fin = strtotime($fg_año_fin_bayer_facturacion."-".$fg_mes_fin_bayer_facturacion."-".$fg_dia_fin_bayer_facturacion); 
	$date_input_fin = getDate($time_input_fin); 
	
	if($time_input_fin == $time_input_inicio)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La fecha de inicio y la fecha final no pueden ser iguales.':'The start date and the end date cannot be the same.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($time_input_fin < $time_input_inicio)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La fecha final debe ser mayor que la fecha de inicio.':'The end date must be greater than the start date.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$urlApi = 'https://utils.realvirtual.com.mx/api/data/Bayer_Reporte_Facturacion';
	
	$rfcCuenta = 'MCO701113C5A'; //$cuenta['rfc'];
	$usuarioCuenta = 'MCOMERCIAL'; //$cuenta['usuario'];
	
	$objeto = array
	(
		'rvcfdi_bayer_facturacion_c_clase_documento' => $rvcfdi_bayer_facturacion_c_clase_documento,
		'rvcfdi_bayer_facturacion_c_sociedad' => $rvcfdi_bayer_facturacion_c_sociedad,
		'rvcfdi_bayer_facturacion_c_moneda' => $rvcfdi_bayer_facturacion_c_moneda,
		'rvcfdi_bayer_facturacion_c_tc_cab_doc' => $rvcfdi_bayer_facturacion_c_tc_cab_doc,
		'rvcfdi_bayer_facturacion_p_cuenta' => $rvcfdi_bayer_facturacion_p_cuenta,
		'rvcfdi_bayer_facturacion_p_division' => $rvcfdi_bayer_facturacion_p_division,
		'rvcfdi_bayer_facturacion_p_ce_be' => $rvcfdi_bayer_facturacion_p_ce_be,
		'rvcfdi_bayer_facturacion_p_texto' => $rvcfdi_bayer_facturacion_p_texto,
		'rvcfdi_bayer_facturacion_p_pais_destinatario' => $rvcfdi_bayer_facturacion_p_pais_destinatario,
		'rvcfdi_bayer_facturacion_p_linea_de_producto' => $rvcfdi_bayer_facturacion_p_linea_de_producto,
		'rvcfdi_bayer_facturacion_p_grupo_de_producto' => $rvcfdi_bayer_facturacion_p_grupo_de_producto,
		'rvcfdi_bayer_facturacion_p_centro' => $rvcfdi_bayer_facturacion_p_centro,
		'rvcfdi_bayer_facturacion_p_cliente' => $rvcfdi_bayer_facturacion_p_cliente,
		'rvcfdi_bayer_facturacion_p_organiz_ventas' => $rvcfdi_bayer_facturacion_p_organiz_ventas,
		'rvcfdi_bayer_facturacion_p_canal_distrib' => $rvcfdi_bayer_facturacion_p_canal_distrib,
		'rvcfdi_bayer_facturacion_p_zoha_de_ventas' => $rvcfdi_bayer_facturacion_p_zoha_de_ventas,
		'rvcfdi_bayer_facturacion_p_oficina_ventas' => $rvcfdi_bayer_facturacion_p_oficina_ventas,
		'rvcfdi_bayer_facturacion_p_ramo' => $rvcfdi_bayer_facturacion_p_ramo,
		'rvcfdi_bayer_facturacion_p_grupo' => $rvcfdi_bayer_facturacion_p_grupo,
		'rvcfdi_bayer_facturacion_p_gr_vendedores' => $rvcfdi_bayer_facturacion_p_gr_vendedores,
		'rvcfdi_bayer_facturacion_p_atributo_1_sector' => $rvcfdi_bayer_facturacion_p_atributo_1_sector,
		'rvcfdi_bayer_facturacion_p_atributo_2_sector' => $rvcfdi_bayer_facturacion_p_atributo_2_sector,
		'rvcfdi_bayer_facturacion_p_clase_factura' => $rvcfdi_bayer_facturacion_p_clase_factura,
		'fg_dia_inicio_bayer_facturacion' => $fg_dia_inicio_bayer_facturacion,
		'fg_mes_inicio_bayer_facturacion' => $fg_mes_inicio_bayer_facturacion,
		'fg_año_inicio_bayer_facturacion' => $fg_año_inicio_bayer_facturacion,
		'fg_dia_fin_bayer_facturacion' => $fg_dia_fin_bayer_facturacion,
		'fg_mes_fin_bayer_facturacion' => $fg_mes_fin_bayer_facturacion,
		'fg_año_fin_bayer_facturacion' => $fg_año_fin_bayer_facturacion,
		'rfc' => $rfcCuenta,
		'usuario' => $usuarioCuenta,
		'clave' => $cuenta['clave']
	);
	
	$params = array
	(
		'method' => 'POST',
		'timeout' => 10000,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => $headers,
		'body' => $objeto,
		'cookies' => array()
	);
	
	$response = wp_remote_post($urlApi, $params);
	
	if(!is_wp_error($response))
	{
		$body = $response['body'];
		$body = json_decode($body);
		
		if(isset($body->Codigo))
		{
			if($body->Codigo == '0')
			{
				$mensaje = $body->Mensaje;
				$archivo = $body->Reporte;
				
				$respuesta = array
				(
					'success' => true,
					'message' => $mensaje,
					'archivo' => base64_encode($archivo)
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
			else
			{
				$message = 'Error al generar el reporte: '.$body->Mensaje;
			
				$respuesta = array
				(
					'success' => false,
					'message' => $message
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
		}
		else
		{
			$message = 'Error al conectar con el servicio: '.$body;
			
			$respuesta = array
			(
				'success' => false,
				'message' => $message
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
	}
	else
	{
		$message = 'Error al conectar con el servicio.';
		
		$respuesta = array
		(
			'success' => false,
			'message' => $message
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
}

add_action('wp_ajax_realvirtual_woocommerce_bayer_reporte_financiero', 'realvirtual_woocommerce_bayer_reporte_financiero_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_bayer_reporte_financiero', 'realvirtual_woocommerce_bayer_reporte_financiero_callback');

function realvirtual_woocommerce_bayer_reporte_financiero_callback()
{
    global $wpdb, $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
    
	$idiomaRVLFECFDI = $_POST['idioma'];
	
	$rvcfdi_bayer_financiero_c_clase_de_documento = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_clase_de_documento']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_clase_de_documento', $rvcfdi_bayer_financiero_c_clase_de_documento);
	
	$rvcfdi_bayer_financiero_c_sociedad = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_sociedad']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_sociedad', $rvcfdi_bayer_financiero_c_sociedad);
	
	$rvcfdi_bayer_financiero_c_moneda = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_moneda']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_moneda', $rvcfdi_bayer_financiero_c_moneda);
	
	$rvcfdi_bayer_financiero_c_t_xt_cab_doc = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_t_xt_cab_doc']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_t_xt_cab_doc', $rvcfdi_bayer_financiero_c_t_xt_cab_doc);
	
	$rvcfdi_bayer_financiero_c_cuenta_bancaria = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_cuenta_bancaria']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_cuenta_bancaria', $rvcfdi_bayer_financiero_c_cuenta_bancaria);
	
	$rvcfdi_bayer_financiero_c_texto = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_texto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_texto', $rvcfdi_bayer_financiero_c_texto);
	
	$rvcfdi_bayer_financiero_c_division = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_division']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_division', $rvcfdi_bayer_financiero_c_division);
	
	$rvcfdi_bayer_financiero_c_cebe = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_cebe']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_cebe', $rvcfdi_bayer_financiero_c_cebe);
	
	$rvcfdi_bayer_financiero_c_cliente = sanitize_text_field($_POST['rvcfdi_bayer_financiero_c_cliente']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_c_cliente', $rvcfdi_bayer_financiero_c_cliente);
	
	$rvcfdi_bayer_financiero_p_cuenta = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_cuenta']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_cuenta', $rvcfdi_bayer_financiero_p_cuenta);
	
	$rvcfdi_bayer_financiero_p_ind_impuestos = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_ind_impuestos']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_ind_impuestos', $rvcfdi_bayer_financiero_p_ind_impuestos);
	
	$rvcfdi_bayer_financiero_p_division = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_division']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_division', $rvcfdi_bayer_financiero_p_division);
	
	$rvcfdi_bayer_financiero_p_texto = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_texto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_texto', $rvcfdi_bayer_financiero_p_texto);
	
	$rvcfdi_bayer_financiero_p_cebe = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_cebe']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_cebe', $rvcfdi_bayer_financiero_p_cebe);
	
	$rvcfdi_bayer_financiero_p_pais_destinatario = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_pais_destinatario']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_pais_destinatario', $rvcfdi_bayer_financiero_p_pais_destinatario);
	
	$rvcfdi_bayer_financiero_p_linea_de_producto = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_linea_de_producto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_linea_de_producto', $rvcfdi_bayer_financiero_p_linea_de_producto);
	
	$rvcfdi_bayer_financiero_p_grupo_de_proudcto = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_grupo_de_proudcto']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_grupo_de_proudcto', $rvcfdi_bayer_financiero_p_grupo_de_proudcto);
	
	$rvcfdi_bayer_financiero_p_centro = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_centro']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_centro', $rvcfdi_bayer_financiero_p_centro);
	
	$rvcfdi_bayer_financiero_p_articulo = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_articulo']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_articulo', $rvcfdi_bayer_financiero_p_articulo);
	
	$rvcfdi_bayer_financiero_p_zona_de_ventas = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_zona_de_ventas']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_zona_de_ventas', $rvcfdi_bayer_financiero_p_zona_de_ventas);
	
	$rvcfdi_bayer_financiero_p_material = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_material']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_material', $rvcfdi_bayer_financiero_p_material);
	
	$rvcfdi_bayer_financiero_p_atributo_2_sector = sanitize_text_field($_POST['rvcfdi_bayer_financiero_p_atributo_2_sector']);
	update_post_meta($post->ID, 'rvcfdi_bayer_financiero_p_atributo_2_sector', $rvcfdi_bayer_financiero_p_atributo_2_sector);
	
	$fg_dia_inicio_bayer_financiero = sanitize_text_field($_POST['fg_dia_inicio_bayer_financiero']);
	update_post_meta($post->ID, 'fg_dia_inicio_bayer_financiero', $fg_dia_inicio_bayer_financiero);
	
	$fg_mes_inicio_bayer_financiero = sanitize_text_field($_POST['fg_mes_inicio_bayer_financiero']);
	update_post_meta($post->ID, 'fg_mes_inicio_bayer_financiero', $fg_mes_inicio_bayer_financiero);
	
	$fg_año_inicio_bayer_financiero = sanitize_text_field($_POST['fg_año_inicio_bayer_financiero']);
	update_post_meta($post->ID, 'fg_año_inicio_bayer_financiero', $fg_año_inicio_bayer_financiero);
	
	$fg_dia_fin_bayer_financiero = sanitize_text_field($_POST['fg_dia_fin_bayer_financiero']);
	update_post_meta($post->ID, 'fg_dia_fin_bayer_financiero', $fg_dia_fin_bayer_financiero);
	
	$fg_mes_fin_bayer_financiero = sanitize_text_field($_POST['fg_mes_fin_bayer_financiero']);
	update_post_meta($post->ID, 'fg_mes_fin_bayer_financiero', $fg_mes_fin_bayer_financiero);
	
	$fg_año_fin_bayer_financiero = sanitize_text_field($_POST['fg_año_fin_bayer_financiero']);
	update_post_meta($post->ID, 'fg_año_fin_bayer_financiero', $fg_año_fin_bayer_financiero);
	
	$rvcfdi_bayer_financiero_c_clase_de_documento 	= trim($_POST['rvcfdi_bayer_financiero_c_clase_de_documento']);
	$rvcfdi_bayer_financiero_c_sociedad 			= trim($_POST['rvcfdi_bayer_financiero_c_sociedad']);
	$rvcfdi_bayer_financiero_c_moneda 				= trim($_POST['rvcfdi_bayer_financiero_c_moneda']);
	$rvcfdi_bayer_financiero_c_t_xt_cab_doc 		= trim($_POST['rvcfdi_bayer_financiero_c_t_xt_cab_doc']);
	$rvcfdi_bayer_financiero_c_cuenta_bancaria 		= trim($_POST['rvcfdi_bayer_financiero_c_cuenta_bancaria']);
	$rvcfdi_bayer_financiero_c_texto				= trim($_POST['rvcfdi_bayer_financiero_c_texto']);
	$rvcfdi_bayer_financiero_c_division 			= trim($_POST['rvcfdi_bayer_financiero_c_division']);
	$rvcfdi_bayer_financiero_c_cebe 				= trim($_POST['rvcfdi_bayer_financiero_c_cebe']);
	$rvcfdi_bayer_financiero_c_cliente 				= trim($_POST['rvcfdi_bayer_financiero_c_cliente']);
	$rvcfdi_bayer_financiero_p_cuenta 				= trim($_POST['rvcfdi_bayer_financiero_p_cuenta']);
	$rvcfdi_bayer_financiero_p_ind_impuestos 		= trim($_POST['rvcfdi_bayer_financiero_p_ind_impuestos']);
	$rvcfdi_bayer_financiero_p_division 			= trim($_POST['rvcfdi_bayer_financiero_p_division']);
	$rvcfdi_bayer_financiero_p_texto 				= trim($_POST['rvcfdi_bayer_financiero_p_texto']);
	$rvcfdi_bayer_financiero_p_cebe					= trim($_POST['rvcfdi_bayer_financiero_p_cebe']);
	$rvcfdi_bayer_financiero_p_pais_destinatario	= trim($_POST['rvcfdi_bayer_financiero_p_pais_destinatario']);
	$rvcfdi_bayer_financiero_p_linea_de_producto	= trim($_POST['rvcfdi_bayer_financiero_p_linea_de_producto']);
	$rvcfdi_bayer_financiero_p_grupo_de_proudcto	= trim($_POST['rvcfdi_bayer_financiero_p_grupo_de_proudcto']);
	$rvcfdi_bayer_financiero_p_centro				= trim($_POST['rvcfdi_bayer_financiero_p_centro']);
	$rvcfdi_bayer_financiero_p_articulo				= trim($_POST['rvcfdi_bayer_financiero_p_articulo']);
	$rvcfdi_bayer_financiero_p_zona_de_ventas		= trim($_POST['rvcfdi_bayer_financiero_p_zona_de_ventas']);
	$rvcfdi_bayer_financiero_p_material				= trim($_POST['rvcfdi_bayer_financiero_p_material']);
	$rvcfdi_bayer_financiero_p_atributo_2_sector	= trim($_POST['rvcfdi_bayer_financiero_p_atributo_2_sector']);
	$fg_dia_inicio_bayer_financiero					= trim($_POST['fg_dia_inicio_bayer_financiero']);
	$fg_mes_inicio_bayer_financiero					= trim($_POST['fg_mes_inicio_bayer_financiero']);
	$fg_año_inicio_bayer_financiero					= trim($_POST['fg_año_inicio_bayer_financiero']);
	$fg_dia_fin_bayer_financiero					= trim($_POST['fg_dia_fin_bayer_financiero']);
	$fg_mes_fin_bayer_financiero					= trim($_POST['fg_mes_fin_bayer_financiero']);
	$fg_año_fin_bayer_financiero					= trim($_POST['fg_año_fin_bayer_financiero']);
	
	if(!checkdate($fg_mes_inicio_bayer_financiero, $fg_dia_inicio_bayer_financiero, $fg_año_inicio_bayer_financiero))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La fecha de inicio tiene un fomato inválido. Selecciona una fecha de inicio válida.':'The start date has an invalid format. Please select a valid start date.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!checkdate($fg_mes_fin_bayer_financiero, $fg_dia_fin_bayer_financiero, $fg_año_fin_bayer_financiero))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La fecha final tiene un fomato inválido. Selecciona una fecha final válida.':'The final date has an invalid format. Please select a valid final date.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$time_input_inicio = strtotime($fg_año_inicio_bayer_financiero."-".$fg_mes_inicio_bayer_financiero."-".$fg_dia_inicio_bayer_financiero); 
	$date_input_inicio = getDate($time_input_inicio);
	$time_input_fin = strtotime($fg_año_fin_bayer_financiero."-".$fg_mes_fin_bayer_financiero."-".$fg_dia_fin_bayer_financiero); 
	$date_input_fin = getDate($time_input_fin); 
	
	if($time_input_fin == $time_input_inicio)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La fecha de inicio y la fecha final no pueden ser iguales.':'The start date and the end date cannot be the same.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if($time_input_fin < $time_input_inicio)
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La fecha final debe ser mayor que la fecha de inicio.':'The end date must be greater than the start date.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$urlApi = 'https://utils.realvirtual.com.mx/api/data/Bayer_Reporte_Financiero';
	
	$rfcCuenta = 'MCO701113C5A'; //$cuenta['rfc'];
	$usuarioCuenta = 'MCOMERCIAL'; //$cuenta['usuario'];
	
	$objeto = array
	(
		'rvcfdi_bayer_financiero_c_clase_de_documento' => $rvcfdi_bayer_financiero_c_clase_de_documento,
		'rvcfdi_bayer_financiero_c_sociedad' => $rvcfdi_bayer_financiero_c_sociedad,
		'rvcfdi_bayer_financiero_c_moneda' => $rvcfdi_bayer_financiero_c_moneda,
		'rvcfdi_bayer_financiero_c_t_xt_cab_doc' => $rvcfdi_bayer_financiero_c_t_xt_cab_doc,
		'rvcfdi_bayer_financiero_c_cuenta_bancaria' => $rvcfdi_bayer_financiero_c_cuenta_bancaria,
		'rvcfdi_bayer_financiero_c_texto' => $rvcfdi_bayer_financiero_c_texto,
		'rvcfdi_bayer_financiero_c_division' => $rvcfdi_bayer_financiero_c_division,
		'rvcfdi_bayer_financiero_c_cebe' => $rvcfdi_bayer_financiero_c_cebe,
		'rvcfdi_bayer_financiero_c_cliente' => $rvcfdi_bayer_financiero_c_cliente,
		'rvcfdi_bayer_financiero_p_cuenta' => $rvcfdi_bayer_financiero_p_cuenta,
		'rvcfdi_bayer_financiero_p_ind_impuestos' => $rvcfdi_bayer_financiero_p_ind_impuestos,
		'rvcfdi_bayer_financiero_p_division' => $rvcfdi_bayer_financiero_p_division,
		'rvcfdi_bayer_financiero_p_texto' => $rvcfdi_bayer_financiero_p_texto,
		'rvcfdi_bayer_financiero_p_cebe' => $rvcfdi_bayer_financiero_p_cebe,
		'rvcfdi_bayer_financiero_p_pais_destinatario' => $rvcfdi_bayer_financiero_p_pais_destinatario,
		'rvcfdi_bayer_financiero_p_linea_de_producto' => $rvcfdi_bayer_financiero_p_linea_de_producto,
		'rvcfdi_bayer_financiero_p_grupo_de_proudcto' => $rvcfdi_bayer_financiero_p_grupo_de_proudcto,
		'rvcfdi_bayer_financiero_p_centro' => $rvcfdi_bayer_financiero_p_centro,
		'rvcfdi_bayer_financiero_p_articulo' => $rvcfdi_bayer_financiero_p_articulo,
		'rvcfdi_bayer_financiero_p_zona_de_ventas' => $rvcfdi_bayer_financiero_p_zona_de_ventas,
		'rvcfdi_bayer_financiero_p_material' => $rvcfdi_bayer_financiero_p_material,
		'rvcfdi_bayer_financiero_p_atributo_2_sector' => $rvcfdi_bayer_financiero_p_atributo_2_sector,
		'fg_dia_inicio_bayer_financiero' => $fg_dia_inicio_bayer_financiero,
		'fg_mes_inicio_bayer_financiero' => $fg_mes_inicio_bayer_financiero,
		'fg_año_inicio_bayer_financiero' => $fg_año_inicio_bayer_financiero,
		'fg_dia_fin_bayer_financiero' => $fg_dia_fin_bayer_financiero,
		'fg_mes_fin_bayer_financiero' => $fg_mes_fin_bayer_financiero,
		'fg_año_fin_bayer_financiero' => $fg_año_fin_bayer_financiero,
		'rfc' => $rfcCuenta,
		'usuario' => $usuarioCuenta,
		'clave' => $cuenta['clave']
	);
			
	$params = array
	(
		'method' => 'POST',
		'timeout' => 10000,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => $headers,
		'body' => $objeto,
		'cookies' => array()
	);
	
	$response = wp_remote_post($urlApi, $params);
	
	if(!is_wp_error($response))
	{
		$body = $response['body'];
		$body = json_decode($body);
		
		if(isset($body->Codigo))
		{
			if($body->Codigo == '0')
			{
				$mensaje = $body->Mensaje;
				$archivo = $body->Reporte;
				
				$respuesta = array
				(
					'success' => true,
					'message' => $mensaje,
					'archivo' => base64_encode($archivo)
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
			else
			{
				$message = 'Error al generar el reporte: '.$body->Mensaje;
			
				$respuesta = array
				(
					'success' => false,
					'message' => $message
				);
				
				echo json_encode($respuesta, JSON_PRETTY_PRINT);
				wp_die();
			}
		}
		else
		{
			$message = 'Error al conectar con el servicio: '.$body;
			
			$respuesta = array
			(
				'success' => false,
				'message' => $message
			);
			
			echo json_encode($respuesta, JSON_PRETTY_PRINT);
			wp_die();
		}
	}
	else
	{
		$message = 'Error al conectar con el servicio.';
		
		$respuesta = array
		(
			'success' => false,
			'message' => $message
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
}

add_action('wp_ajax_realvirtual_woocommerce_buscar_datosfiscales_cliente', 'realvirtual_woocommerce_buscar_datosfiscales_cliente_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_buscar_datosfiscales_cliente', 'realvirtual_woocommerce_buscar_datosfiscales_cliente_callback');

function realvirtual_woocommerce_buscar_datosfiscales_cliente_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$idiomaRVLFECFDI	= $_POST['IDIOMA'];
	
	$datosFiscalesClientes = obtenerDatosFiscalesClientes();
	$datosFiscalesClientesHTML = '';
	
	foreach ($datosFiscalesClientes as $fila) 
	{
		$datosFiscalesClientesHTML .= '<tr>
			<td style="display:none;">'.$fila->id_user.'</td>
			<td style="display:none;">'.$fila->rfc.'</td>
			<td style="display:none;">'.$fila->razon_social.'</td>
			<td style="display:none;">'.$fila->domicilio_fiscal.'</td>
			<td style="display:none;">'.$fila->regimen_fiscal.'</td>
			<td style="display:none;">'.$fila->uso_cfdi.'</td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->id_user.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->rfc.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->razon_social.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->domicilio_fiscal.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->regimen_fiscal.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->uso_cfdi.'</font></td>
			</tr>';
	}
	
	$respuesta = array
	(
		'success' => true,
		'datosFiscalesClientesHTML' => $datosFiscalesClientesHTML
	);
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_eliminar_datosfiscales_cliente', 'realvirtual_woocommerce_eliminar_datosfiscales_cliente_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_eliminar_datosfiscales_cliente', 'realvirtual_woocommerce_eliminar_datosfiscales_cliente_callback');

function realvirtual_woocommerce_eliminar_datosfiscales_cliente_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	
	$ID_USUARIO = sanitize_text_field($_POST['ID_USUARIO']);
	update_post_meta($post->ID, 'ID_USUARIO', $ID_USUARIO);
	
	$ID_USUARIO 		= $_POST['ID_USUARIO'];
	$idiomaRVLFECFDI	= $_POST['IDIOMA'];
	
	if(!($ID_USUARIO != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El ID Usuario del cliente está vacío.':'Client User ID is empty.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
			
	eliminarDatosFiscales($ID_USUARIO);
	
	$datosFiscalesClientes = obtenerDatosFiscalesClientes();
	$datosFiscalesClientesHTML = '';
	
	foreach ($datosFiscalesClientes as $fila) 
	{
		$datosFiscalesClientesHTML .= '<tr>
			<td style="display:none;">'.$fila->id_user.'</td>
			<td style="display:none;">'.$fila->rfc.'</td>
			<td style="display:none;">'.$fila->razon_social.'</td>
			<td style="display:none;">'.$fila->domicilio_fiscal.'</td>
			<td style="display:none;">'.$fila->regimen_fiscal.'</td>
			<td style="display:none;">'.$fila->uso_cfdi.'</td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->id_user.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->rfc.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->razon_social.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->domicilio_fiscal.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->regimen_fiscal.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->uso_cfdi.'</font></td>
			</tr>';
	}
	
	$respuesta = array
	(
		'success' => true,
		'message' => 'Datos Fiscales eliminados con éxito.',
		'datosFiscalesClientesHTML' => $datosFiscalesClientesHTML
	);
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_realvirtual_woocommerce_editar_datosfiscales_cliente', 'realvirtual_woocommerce_editar_datosfiscales_cliente_callback');
add_action('wp_ajax_nopriv_realvirtual_woocommerce_editar_datosfiscales_cliente', 'realvirtual_woocommerce_editar_datosfiscales_cliente_callback');

function realvirtual_woocommerce_editar_datosfiscales_cliente_callback()
{
	global $sistema, $nombreSistema, $nombreSistemaAsociado, $urlSistemaAsociado, $sitioOficialSistema, $post;
	
	$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
	$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
	$ID_USUARIO = sanitize_text_field($_POST['ID_USUARIO']);
	update_post_meta($post->ID, 'ID_USUARIO', $ID_USUARIO);
	
	$RFC = sanitize_text_field($_POST['RFC']);
	update_post_meta($post->ID, 'RFC', $RFC);
	
	$RAZON_SOCIAL = sanitize_text_field($_POST['RAZON_SOCIAL']);
	update_post_meta($post->ID, 'RAZON_SOCIAL', $RAZON_SOCIAL);
	
	$CODIGO_POSTAL = sanitize_text_field($_POST['CODIGO_POSTAL']);
	update_post_meta($post->ID, 'CODIGO_POSTAL', $CODIGO_POSTAL);
	
	$REGIMEN_FISCAL = sanitize_text_field($_POST['REGIMEN_FISCAL']);
	update_post_meta($post->ID, 'REGIMEN_FISCAL', $REGIMEN_FISCAL);
	
	$USO_CFDI = sanitize_text_field($_POST['USO_CFDI']);
	update_post_meta($post->ID, 'USO_CFDI', $USO_CFDI);
	
	$ID_USUARIO 		= $_POST['ID_USUARIO'];
	$RFC 				= $_POST['RFC'];
	$RAZON_SOCIAL 		= $_POST['RAZON_SOCIAL'];
	$CODIGO_POSTAL 		= $_POST['CODIGO_POSTAL'];
	$REGIMEN_FISCAL 	= $_POST['REGIMEN_FISCAL'];
	$USO_CFDI 			= $_POST['USO_CFDI'];
	$idiomaRVLFECFDI	= $_POST['IDIOMA'];
	
	if(!($ID_USUARIO != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El ID Usuario del cliente está vacío.':'Client User ID is empty.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!($RFC != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El RFC del cliente está vacío.':'The Customer RFC is empty.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!($RAZON_SOCIAL != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'La Razón Social del cliente está vacío.':'The Customer Business Name is empty.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!($CODIGO_POSTAL != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El Código Postal del cliente está vacío.':'The customer Postal Code is empty.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!($REGIMEN_FISCAL != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El Régimen Fiscal del cliente está vacío.':'The customer Fiscal Regime is empty.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	if(!($USO_CFDI != ''))
	{
		$respuesta = array
		(
			'success' => false,
			'message' => ($idiomaRVLFECFDI == 'ES') ? 'El Uso CFDI del cliente está vacío.':'The customer Use CFDI is empty.'
		);
		
		echo json_encode($respuesta, JSON_PRETTY_PRINT);
		wp_die();
	}
	
	eliminarDatosFiscales($ID_USUARIO);
	guardarDatosFiscales($ID_USUARIO, $RFC, $RAZON_SOCIAL, $CODIGO_POSTAL,
			$REGIMEN_FISCAL, $USO_CFDI, $configuracion['metodo_pago'], $configuracion['metodo_pago33']);
	
	$datosFiscalesClientes = obtenerDatosFiscalesClientes();
	$datosFiscalesClientesHTML = '';
	
	foreach ($datosFiscalesClientes as $fila) 
	{
		$datosFiscalesClientesHTML .= '<tr>
			<td style="display:none;">'.$fila->id_user.'</td>
			<td style="display:none;">'.$fila->rfc.'</td>
			<td style="display:none;">'.$fila->razon_social.'</td>
			<td style="display:none;">'.$fila->domicilio_fiscal.'</td>
			<td style="display:none;">'.$fila->regimen_fiscal.'</td>
			<td style="display:none;">'.$fila->uso_cfdi.'</td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->id_user.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->rfc.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->razon_social.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->domicilio_fiscal.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->regimen_fiscal.'</font></td>
			<td class="columna" style="text-align:left; border-color: #a54107; padding: 5px;"><font size="2">'.$fila->uso_cfdi.'</font></td>
			</tr>';
	}
	
	$respuesta = array
	(
		'success' => true,
		'message' => 'Datos Fiscales guardados con éxito.',
		'datosFiscalesClientesHTML' => $datosFiscalesClientesHTML
	);
	
	echo json_encode($respuesta, JSON_PRETTY_PRINT);
	wp_die();
}
?>