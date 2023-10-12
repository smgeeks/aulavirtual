<?php
	class RealVirtualWooCommerceCliente
	{
		static function obtenerCliente($rfc, $correo, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			$opcion = 'ObtenerCliente';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'RECEPTOR_RFC' => $rfc,
				'RECEPTOR_EMAIL' => $correo,
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
	}
?>