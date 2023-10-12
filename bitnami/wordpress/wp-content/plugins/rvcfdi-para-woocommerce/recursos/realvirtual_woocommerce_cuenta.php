<?php
	class RealVirtualWooCommerceCuenta
	{
		static $rfc      = '';
		static $usuario  = '';
		static $clave    = '';
		
		static function guardarCuenta($cuenta, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_rfcEmisor', base64_encode($rfcEmisor));
			update_option('rvcfdi_usuarioEmisor', base64_encode($usuarioEmisor));
			update_option('rvcfdi_claveEmisor', base64_encode($claveEmisor));
			
			$archivoCuenta = fopen(dirname(__FILE__).'/realvirtual_woocommerce_cuenta.conf', 'w') or die((($idioma == 'ES') ? 'No se puede abrir el archivo de cuenta.' : 'The account file can not be opened.'));
			
			fwrite($archivoCuenta, base64_encode($cuenta['rfc'])."\n");
			fwrite($archivoCuenta, base64_encode($cuenta['usuario'])."\n");
			fwrite($archivoCuenta, base64_encode($cuenta['clave'])."\n");
			fclose($archivoCuenta);
			
			$opcion = 'EstadoEmisor';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'IDIOMA' => $idioma
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
					return json_decode($body);
				}
			}
			catch(Exception $e)
			{
				print('Exception occured: ' . $e->getMessage());
			}
		}
		
		static function obtenerLicencias($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			$opcion = 'ObtenerLicencias';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'IDIOMA' => $idioma
			);
			
			$params = array
			(
				'method' => 'POST',
				'timeout' => 10000,
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
					return json_decode($body);
				}
			}
			catch(Exception $e)
			{
				print('Exception occured: ' . $e->getMessage());
			}
		}
		
		static function cuentaEntidad()
		{
			$datosCuenta = self::obtenerCuenta();

			return array
			(
				'rfc'      									=> base64_decode($datosCuenta[0]),
				'usuario'   								=> base64_decode($datosCuenta[1]),
				'clave'     								=> base64_decode($datosCuenta[2])
			);
		}
		
		static function obtenerCuenta()
		{
			$rfc = '';
			$usuario = '';
			$clave = '';
			
			if(get_option('rvcfdi_rfcEmisor') !== false)
				$rfc = get_option('rvcfdi_rfcEmisor');
			else
				add_option('rvcfdi_rfcEmisor', '');
			
			if(get_option('rvcfdi_usuarioEmisor') !== false)
				$usuario = get_option('rvcfdi_usuarioEmisor');
			else
				add_option('rvcfdi_usuarioEmisor', '');
			
			if(get_option('rvcfdi_claveEmisor') !== false)
				$clave = get_option('rvcfdi_claveEmisor');
			else
				add_option('rvcfdi_claveEmisor', '');
			
			$datosCuenta = array($rfc, $usuario, $clave);
			
			/*$archivo = @fopen(dirname(__FILE__).'/realvirtual_woocommerce_cuenta.conf', 'r');

			if($archivo)
			   $datosCuenta = explode("\n", fread($archivo, filesize(dirname(__FILE__) .'/realvirtual_woocommerce_cuenta.conf')));
			else
				$datosCuenta = explode("|", '||');*/
			
			return $datosCuenta;
		}
	}
?>