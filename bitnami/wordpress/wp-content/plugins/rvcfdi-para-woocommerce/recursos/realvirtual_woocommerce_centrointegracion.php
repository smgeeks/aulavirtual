<?php
	class RealVirtualWooCommerceCentroIntegracion
	{
		static $ci_consultarPedidos_tipo_conexion		   				= '';
		static $ci_consultarPedidos_tipo_solicitud   					= '';
		static $ci_consultarPedidos_url   								= '';
		static $ci_consultarPedidos_nombre_parametro_numeropedido   	= '';
		static $ci_consultarPedidos_nombre_parametro_monto   			= '';
		static $ci_consultarPedidos_parametro_extra1_tipo   			= '';
		static $ci_consultarPedidos_parametro_extra1_nombrevisual   	= '';
		static $ci_consultarPedidos_parametro_extra1_nombreinterno   	= '';
		static $ci_consultarPedidos_parametro_extra1_estado 			= '';
		static $ci_consultarPedidos_parametro_extra2_tipo   			= '';
		static $ci_consultarPedidos_parametro_extra2_nombrevisual   	= '';
		static $ci_consultarPedidos_parametro_extra2_nombreinterno   	= '';
		static $ci_consultarPedidos_parametro_extra2_estado 			= '';
		static $ci_consultarPedidos_tipo_consulta						= '';
		static $ci_enviarPedidos_tipo_consulta		   					= '';
		static $ci_enviarPedidos_tipo_conexion		   					= '';
		static $ci_enviarPedidos_tipo_solicitud   						= '';
		static $ci_enviarPedidos_url   									= '';
		static $ci_enviarPedidos_tipo_conexion2		   					= '';
		static $ci_enviarPedidos_tipo_solicitud2   						= '';
		static $ci_enviarPedidos_url2   								= '';
		static $ci_enviarXml_tipo_conexion		   						= '';
		static $ci_enviarXml_tipo_solicitud   							= '';
		static $ci_enviarXml_url   										= '';
		static $ci_enviarXml_tipo_conexion2		   						= '';
		static $ci_enviarXml_tipo_solicitud2  							= '';
		static $ci_enviarXml_url2   									= '';
		static $ci_enviarXml_tipo_consulta								= '';
		static $ci_enviarPedidosCrear_tipo_consulta		   				= '';
		static $ci_enviarPedidosCrear_tipo_conexion		   				= '';
		static $ci_enviarPedidosCrear_tipo_solicitud   					= '';
		static $ci_enviarPedidosCrear_tipo_consulta2		   			= '';
		static $ci_enviarPedidosCrear_tipo_conexion2		   			= '';
		static $ci_enviarPedidosCrear_tipo_solicitud2   				= '';
		static $ci_enviarPedidosCrear_url   							= '';
		
		static function guardarConfiguracionConsultarPedidos($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_ci_consultarPedidos_tipo_conexion', base64_encode($configuracion['ci_consultarPedidos_tipo_conexion']));
			update_option('rvcfdi_ci_consultarPedidos_tipo_solicitud', base64_encode($configuracion['ci_consultarPedidos_tipo_solicitud']));
			update_option('rvcfdi_ci_consultarPedidos_url', base64_encode($configuracion['ci_consultarPedidos_url']));
			update_option('rvcfdi_ci_consultarPedidos_nombre_parametro_numeropedido', base64_encode($configuracion['ci_consultarPedidos_nombre_parametro_numeropedido']));
			update_option('rvcfdi_ci_consultarPedidos_nombre_parametro_monto', base64_encode($configuracion['ci_consultarPedidos_nombre_parametro_monto']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra1_tipo', base64_encode($configuracion['ci_consultarPedidos_parametro_extra1_tipo']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombrevisual', base64_encode($configuracion['ci_consultarPedidos_parametro_extra1_nombrevisual']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombreinterno', base64_encode($configuracion['ci_consultarPedidos_parametro_extra1_nombreinterno']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra1_estado', base64_encode($configuracion['ci_consultarPedidos_parametro_extra1_estado']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra2_tipo', base64_encode($configuracion['ci_consultarPedidos_parametro_extra2_tipo']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombrevisual', base64_encode($configuracion['ci_consultarPedidos_parametro_extra2_nombrevisual']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombreinterno', base64_encode($configuracion['ci_consultarPedidos_parametro_extra2_nombreinterno']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra2_estado', base64_encode($configuracion['ci_consultarPedidos_parametro_extra2_estado']));
			update_option('rvcfdi_ci_consultarPedidos_tipo_consulta', base64_encode($configuracion['ci_consultarPedidos_tipo_consulta']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracionEnviarPedidos($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_ci_enviarPedidos_tipo_conexion', base64_encode($configuracion['ci_enviarPedidos_tipo_conexion']));
			update_option('rvcfdi_ci_enviarPedidos_tipo_solicitud', base64_encode($configuracion['ci_enviarPedidos_tipo_solicitud']));
			update_option('rvcfdi_ci_enviarPedidos_url', base64_encode($configuracion['ci_enviarPedidos_url']));
			update_option('rvcfdi_ci_enviarPedidos_tipo_conexion2', base64_encode($configuracion['ci_enviarPedidos_tipo_conexion2']));
			update_option('rvcfdi_ci_enviarPedidos_tipo_solicitud2', base64_encode($configuracion['ci_enviarPedidos_tipo_solicitud2']));
			update_option('rvcfdi_ci_enviarPedidos_url2', base64_encode($configuracion['ci_enviarPedidos_url2']));
			update_option('rvcfdi_ci_enviarPedidos_tipo_consulta', base64_encode($configuracion['ci_enviarPedidos_tipo_consulta']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracionEnviarPedidosCrear($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion', base64_encode($configuracion['ci_enviarPedidosCrear_tipo_conexion']));
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud', base64_encode($configuracion['ci_enviarPedidosCrear_tipo_solicitud']));
			update_option('rvcfdi_ci_enviarPedidosCrear_url', base64_encode($configuracion['ci_enviarPedidosCrear_url']));
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion2', base64_encode($configuracion['ci_enviarPedidosCrear_tipo_conexion2']));
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud2', base64_encode($configuracion['ci_enviarPedidosCrear_tipo_solicitud2']));
			update_option('rvcfdi_ci_enviarPedidosCrear_url2', base64_encode($configuracion['ci_enviarPedidosCrear_url2']));
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_consulta', base64_encode($configuracion['ci_enviarPedidosCrear_tipo_consulta']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracionEnviarXml($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_ci_enviarXml_tipo_conexion', base64_encode($configuracion['ci_enviarXml_tipo_conexion']));
			update_option('rvcfdi_ci_enviarXml_tipo_solicitud', base64_encode($configuracion['ci_enviarXml_tipo_solicitud']));
			update_option('rvcfdi_ci_enviarXml_url', base64_encode($configuracion['ci_enviarXml_url']));
			update_option('rvcfdi_ci_enviarXml_tipo_conexion2', base64_encode($configuracion['ci_enviarXml_tipo_conexion2']));
			update_option('rvcfdi_ci_enviarXml_tipo_solicitud2', base64_encode($configuracion['ci_enviarXml_tipo_solicitud2']));
			update_option('rvcfdi_ci_enviarXml_url2', base64_encode($configuracion['ci_enviarXml_url2']));
			update_option('rvcfdi_ci_enviarXml_tipo_consulta', base64_encode($configuracion['ci_enviarXml_tipo_consulta']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			$configuracion = self::configuracionEntidad();
			
			$opcion = 'GuardarConfiguracionIntegracion';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'TIPO_CONEXION' => base64_encode($configuracion['ci_consultarPedidos_tipo_conexion']),
				'TIPO_SOLICITUD' => base64_encode($configuracion['ci_consultarPedidos_tipo_solicitud']),
				'URL' => base64_encode($configuracion['ci_consultarPedidos_url']),
				'NOMBRE_PARAMETRO_NUMEROPEDIDO' => base64_encode($configuracion['ci_consultarPedidos_nombre_parametro_numeropedido']),
				'NOMBRE_PARAMETRO_MONTO' => base64_encode($configuracion['ci_consultarPedidos_nombre_parametro_monto']),
				'PARAMETRO_EXTRA1_TIPO' => base64_encode($configuracion['ci_consultarPedidos_parametro_extra1_tipo']),
				'PARAMETRO_EXTRA1_NOMBREVISUAL' => base64_encode($configuracion['ci_consultarPedidos_parametro_extra1_nombrevisual']),
				'PARAMETRO_EXTRA1_NOMBREINTERNO' => base64_encode($configuracion['ci_consultarPedidos_parametro_extra1_nombreinterno']),
				'PARAMETRO_EXTRA1_ESTADO' => base64_encode($configuracion['ci_consultarPedidos_parametro_extra1_estado']),
				'PARAMETRO_EXTRA2_TIPO' => base64_encode($configuracion['ci_consultarPedidos_parametro_extra2_tipo']),
				'PARAMETRO_EXTRA2_NOMBREVISUAL' => base64_encode($configuracion['ci_consultarPedidos_parametro_extra2_nombrevisual']),
				'PARAMETRO_EXTRA2_NOMBREINTERNO' => base64_encode($configuracion['ci_consultarPedidos_parametro_extra2_nombreinterno']),
				'PARAMETRO_EXTRA2_ESTADO' => base64_encode($configuracion['ci_consultarPedidos_parametro_extra2_estado']),
				'TIPO_CONSULTA' => base64_encode($configuracion['ci_consultarPedidos_tipo_consulta']),
				'ENVIARPEDIDOS_TIPO_CONEXION' => base64_encode($configuracion['ci_enviarPedidos_tipo_conexion']),
				'ENVIARPEDIDOS_TIPO_SOLICITUD' => base64_encode($configuracion['ci_enviarPedidos_tipo_solicitud']),
				'ENVIARPEDIDOS_URL' => base64_encode($configuracion['ci_enviarPedidos_url']),
				'ENVIARPEDIDOS_TIPO_CONEXION2' => base64_encode($configuracion['ci_enviarPedidos_tipo_conexion2']),
				'ENVIARPEDIDOS_TIPO_SOLICITUD2' => base64_encode($configuracion['ci_enviarPedidos_tipo_solicitud2']),
				'ENVIARPEDIDOS_URL2' => base64_encode($configuracion['ci_enviarPedidos_url2']),
				'ENVIARPEDIDOS_TIPO_CONSULTA' => base64_encode($configuracion['ci_enviarPedidos_tipo_consulta']),
				'ENVIARXML_TIPO_CONEXION' => base64_encode($configuracion['ci_enviarXml_tipo_conexion']),
				'ENVIARXML_TIPO_SOLICITUD' => base64_encode($configuracion['ci_enviarXml_tipo_solicitud']),
				'ENVIARXML_URL' => base64_encode($configuracion['ci_enviarXml_url']),
				'ENVIARXML_TIPO_CONEXION2' => base64_encode($configuracion['ci_enviarXml_tipo_conexion2']),
				'ENVIARXML_TIPO_SOLICITUD2' => base64_encode($configuracion['ci_enviarXml_tipo_solicitud2']),
				'ENVIARXML_URL2' => base64_encode($configuracion['ci_enviarXml_url2']),
				'ENVIARXML_TIPO_CONSULTA' => base64_encode($configuracion['ci_enviarXml_tipo_consulta']),
				'ENVIARPEDIDOSCREAR_TIPO_CONEXION' => base64_encode($configuracion['ci_enviarPedidosCrear_tipo_conexion']),
				'ENVIARPEDIDOSCREAR_TIPO_SOLICITUD' => base64_encode($configuracion['ci_enviarPedidosCrear_tipo_solicitud']),
				'ENVIARPEDIDOSCREAR_URL' => base64_encode($configuracion['ci_enviarPedidosCrear_url']),
				'ENVIARPEDIDOSCREAR_TIPO_CONEXION2' => base64_encode($configuracion['ci_enviarPedidosCrear_tipo_conexion2']),
				'ENVIARPEDIDOSCREAR_TIPO_SOLICITUD2' => base64_encode($configuracion['ci_enviarPedidosCrear_tipo_solicitud2']),
				'ENVIARPEDIDOSCREAR_URL2' => base64_encode($configuracion['ci_enviarPedidosCrear_url2']),
				'ENVIARPEDIDOSCREAR_TIPO_CONSULTA' => base64_encode($configuracion['ci_enviarPedidosCrear_tipo_consulta'])
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
				else
				{
					$respuesta->success = false;
					$respuesta->message = "No se pudo guardar la configuración en la base de datos";

					return $respuesta;
				}
			}
			catch(Exception $e)
			{
				$respuesta->success = false;
				$respuesta->message = $e->getMessage();

				return $respuesta;
			}
		}
		
		static function guardarConfiguracionLocal($configuracion, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_ci_consultarPedidos_tipo_conexion', ($configuracion['ci_consultarPedidos_tipo_conexion']));
			update_option('rvcfdi_ci_consultarPedidos_tipo_solicitud', ($configuracion['ci_consultarPedidos_tipo_solicitud']));
			update_option('rvcfdi_ci_consultarPedidos_url', ($configuracion['ci_consultarPedidos_url']));
			update_option('rvcfdi_ci_consultarPedidos_nombre_parametro_numeropedido', ($configuracion['ci_consultarPedidos_nombre_parametro_numeropedido']));
			update_option('rvcfdi_ci_consultarPedidos_nombre_parametro_monto', ($configuracion['ci_consultarPedidos_nombre_parametro_monto']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra1_tipo', ($configuracion['ci_consultarPedidos_parametro_extra1_tipo']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombrevisual', ($configuracion['ci_consultarPedidos_parametro_extra1_nombrevisual']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombreinterno', ($configuracion['ci_consultarPedidos_parametro_extra1_nombreinterno']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra1_estado', ($configuracion['ci_consultarPedidos_parametro_extra1_estado']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra2_tipo', ($configuracion['ci_consultarPedidos_parametro_extra2_tipo']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombrevisual', ($configuracion['ci_consultarPedidos_parametro_extra2_nombrevisual']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombreinterno', ($configuracion['ci_consultarPedidos_parametro_extra2_nombreinterno']));
			update_option('rvcfdi_ci_consultarPedidos_parametro_extra2_estado', ($configuracion['ci_consultarPedidos_parametro_extra2_estado']));
			update_option('rvcfdi_ci_consultarPedidos_tipo_consulta', ($configuracion['ci_consultarPedidos_tipo_consulta']));
			update_option('rvcfdi_ci_enviarPedidos_tipo_conexion', ($configuracion['ci_enviarPedidos_tipo_conexion']));
			update_option('rvcfdi_ci_enviarPedidos_tipo_solicitud', ($configuracion['ci_enviarPedidos_tipo_solicitud']));
			update_option('rvcfdi_ci_enviarPedidos_url', ($configuracion['ci_enviarPedidos_url']));
			update_option('rvcfdi_ci_enviarPedidos_tipo_conexion2', ($configuracion['ci_enviarPedidos_tipo_conexion2']));
			update_option('rvcfdi_ci_enviarPedidos_tipo_solicitud2', ($configuracion['ci_enviarPedidos_tipo_solicitud2']));
			update_option('rvcfdi_ci_enviarPedidos_url2', ($configuracion['ci_enviarPedidos_url2']));
			update_option('rvcfdi_ci_enviarPedidos_tipo_consulta', ($configuracion['ci_enviarPedidos_tipo_consulta']));
			update_option('rvcfdi_ci_enviarXml_tipo_conexion', ($configuracion['ci_enviarXml_tipo_conexion']));
			update_option('rvcfdi_ci_enviarXml_tipo_solicitud', ($configuracion['ci_enviarXml_tipo_solicitud']));
			update_option('rvcfdi_ci_enviarXml_url', ($configuracion['ci_enviarXml_url']));
			update_option('rvcfdi_ci_enviarXml_tipo_conexion2', ($configuracion['ci_enviarXml_tipo_conexion2']));
			update_option('rvcfdi_ci_enviarXml_tipo_solicitud2', ($configuracion['ci_enviarXml_tipo_solicitud2']));
			update_option('rvcfdi_ci_enviarXml_url2', ($configuracion['ci_enviarXml_url2']));
			update_option('rvcfdi_ci_enviarXml_tipo_consulta', ($configuracion['ci_enviarXml_tipo_consulta']));
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion', ($configuracion['ci_enviarPedidosCrear_tipo_conexion']));
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud', ($configuracion['ci_enviarPedidosCrear_tipo_solicitud']));
			update_option('rvcfdi_ci_enviarPedidosCrear_url', ($configuracion['ci_enviarPedidosCrear_url']));
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion2', ($configuracion['ci_enviarPedidosCrear_tipo_conexion2']));
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud2', ($configuracion['ci_enviarPedidosCrear_tipo_solicitud2']));
			update_option('rvcfdi_ci_enviarPedidosCrear_url2', ($configuracion['ci_enviarPedidosCrear_url2']));
			update_option('rvcfdi_ci_enviarPedidosCrear_tipo_consulta', ($configuracion['ci_enviarPedidosCrear_tipo_consulta']));
			
			return true;
		}
		
		static function configuracionEntidad()
		{
			$datosConfiguracion = self::obtenerConfiguracion();

			return array
			(
				'ci_consultarPedidos_tipo_conexion'     						=> base64_decode($datosConfiguracion[0]),
				'ci_consultarPedidos_tipo_solicitud' 							=> base64_decode($datosConfiguracion[1]),
				'ci_consultarPedidos_url'   	 								=> base64_decode($datosConfiguracion[2]),
				'ci_consultarPedidos_nombre_parametro_numeropedido'   			=> base64_decode($datosConfiguracion[3]),
				'ci_consultarPedidos_nombre_parametro_monto'   					=> base64_decode($datosConfiguracion[4]),
				'ci_consultarPedidos_parametro_extra1_tipo'   					=> base64_decode($datosConfiguracion[5]),
				'ci_consultarPedidos_parametro_extra1_nombrevisual'   			=> base64_decode($datosConfiguracion[6]),
				'ci_consultarPedidos_parametro_extra1_nombreinterno'   			=> base64_decode($datosConfiguracion[7]),
				'ci_consultarPedidos_parametro_extra1_estado'   				=> base64_decode($datosConfiguracion[8]),
				'ci_consultarPedidos_parametro_extra2_tipo'   					=> base64_decode($datosConfiguracion[9]),
				'ci_consultarPedidos_parametro_extra2_nombrevisual'   			=> base64_decode($datosConfiguracion[10]),
				'ci_consultarPedidos_parametro_extra2_nombreinterno'   			=> base64_decode($datosConfiguracion[11]),
				'ci_consultarPedidos_parametro_extra2_estado'   				=> base64_decode($datosConfiguracion[12]),
				'ci_consultarPedidos_tipo_consulta'   							=> base64_decode($datosConfiguracion[13]),
				'ci_enviarPedidos_tipo_conexion'     							=> base64_decode($datosConfiguracion[14]),
				'ci_enviarPedidos_tipo_solicitud' 								=> base64_decode($datosConfiguracion[15]),
				'ci_enviarPedidos_url'   	 									=> base64_decode($datosConfiguracion[16]),
				'ci_enviarPedidos_tipo_conexion2'     							=> base64_decode($datosConfiguracion[17]),
				'ci_enviarPedidos_tipo_solicitud2' 								=> base64_decode($datosConfiguracion[18]),
				'ci_enviarPedidos_url2'   	 									=> base64_decode($datosConfiguracion[19]),
				'ci_enviarPedidos_tipo_consulta'   								=> base64_decode($datosConfiguracion[20]),
				'ci_enviarXml_tipo_conexion'     								=> base64_decode($datosConfiguracion[21]),
				'ci_enviarXml_tipo_solicitud' 									=> base64_decode($datosConfiguracion[22]),
				'ci_enviarXml_url'   	 										=> base64_decode($datosConfiguracion[23]),
				'ci_enviarXml_tipo_consulta'   									=> base64_decode($datosConfiguracion[24]),
				'ci_enviarPedidosCrear_tipo_conexion'     						=> base64_decode($datosConfiguracion[25]),
				'ci_enviarPedidosCrear_tipo_solicitud' 							=> base64_decode($datosConfiguracion[26]),
				'ci_enviarPedidosCrear_url'   	 								=> base64_decode($datosConfiguracion[27]),
				'ci_enviarPedidosCrear_tipo_conexion2'     						=> base64_decode($datosConfiguracion[28]),
				'ci_enviarPedidosCrear_tipo_solicitud2' 						=> base64_decode($datosConfiguracion[29]),
				'ci_enviarPedidosCrear_url2'   	 								=> base64_decode($datosConfiguracion[30]),
				'ci_enviarPedidosCrear_tipo_consulta'   						=> base64_decode($datosConfiguracion[31]),
				'ci_enviarXml_tipo_conexion2'     								=> base64_decode($datosConfiguracion[32]),
				'ci_enviarXml_tipo_solicitud2' 									=> base64_decode($datosConfiguracion[33]),
				'ci_enviarXml_url2'   	 										=> base64_decode($datosConfiguracion[34]),
			);
		}
		
		static function obtenerConfiguracion()
		{
			$rvcfdi_ci_consultarPedidos_tipo_conexion		   				= '';
			$rvcfdi_ci_consultarPedidos_tipo_solicitud   					= '';
			$rvcfdi_ci_consultarPedidos_url   								= '';
			$rvcfdi_ci_consultarPedidos_nombre_parametro_numeropedido   	= '';
			$rvcfdi_ci_consultarPedidos_nombre_parametro_monto   			= '';
			$rvcfdi_ci_consultarPedidos_parametro_extra1_tipo   			= '';
			$rvcfdi_ci_consultarPedidos_parametro_extra1_nombrevisual   	= '';
			$rvcfdi_ci_consultarPedidos_parametro_extra1_nombreinterno   	= '';
			$rvcfdi_ci_consultarPedidos_parametro_extra1_estado 			= '';
			$rvcfdi_ci_consultarPedidos_parametro_extra2_tipo   			= '';
			$rvcfdi_ci_consultarPedidos_parametro_extra2_nombrevisual   	= '';
			$rvcfdi_ci_consultarPedidos_parametro_extra2_nombreinterno   	= '';
			$rvcfdi_ci_consultarPedidos_parametro_extra2_estado 			= '';
			$rvcfdi_ci_consultarPedidos_tipo_consulta						= '';
			$rvcfdi_ci_enviarPedidos_tipo_conexion		   					= '';
			$rvcfdi_ci_enviarPedidos_tipo_solicitud   						= '';
			$rvcfdi_ci_enviarPedidos_url   									= '';
			$rvcfdi_ci_enviarPedidos_tipo_conexion2		   					= '';
			$rvcfdi_ci_enviarPedidos_tipo_solicitud2   						= '';
			$rvcfdi_ci_enviarPedidos_url2 									= '';
			$rvcfdi_ci_enviarPedidos_tipo_consulta							= '';
			$rvcfdi_ci_enviarXml_tipo_conexion		   						= '';
			$rvcfdi_ci_enviarXml_tipo_solicitud   							= '';
			$rvcfdi_ci_enviarXml_url   										= '';
			$rvcfdi_ci_enviarXml_tipo_consulta								= '';
			$rvcfdi_ci_enviarPedidosCrear_tipo_conexion		   				= '';
			$rvcfdi_ci_enviarPedidosCrear_tipo_solicitud   					= '';
			$rvcfdi_ci_enviarPedidosCrear_url 								= '';
			$rvcfdi_ci_enviarPedidosCrear_tipo_conexion2		   			= '';
			$rvcfdi_ci_enviarPedidosCrear_tipo_solicitud2   				= '';
			$rvcfdi_ci_enviarPedidosCrear_url2 								= '';
			$rvcfdi_ci_enviarPedidosCrear_tipo_consulta						= '';
			$rvcfdi_ci_enviarXml_tipo_conexion2		   						= '';
			$rvcfdi_ci_enviarXml_tipo_solicitud2   							= '';
			$rvcfdi_ci_enviarXml_url2   									= '';
			
			if(get_option('rvcfdi_ci_consultarPedidos_tipo_conexion') !== false)
				$rvcfdi_ci_consultarPedidos_tipo_conexion = get_option('rvcfdi_ci_consultarPedidos_tipo_conexion');
			else
				add_option('rvcfdi_ci_consultarPedidos_tipo_conexion', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_tipo_solicitud') !== false)
				$rvcfdi_ci_consultarPedidos_tipo_solicitud = get_option('rvcfdi_ci_consultarPedidos_tipo_solicitud');
			else
				add_option('rvcfdi_ci_consultarPedidos_tipo_solicitud', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_url') !== false)
				$rvcfdi_ci_consultarPedidos_url = get_option('rvcfdi_ci_consultarPedidos_url');
			else
				add_option('rvcfdi_ci_consultarPedidos_url', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_nombre_parametro_numeropedido') !== false)
				$rvcfdi_ci_consultarPedidos_nombre_parametro_numeropedido = get_option('rvcfdi_ci_consultarPedidos_nombre_parametro_numeropedido');
			else
				add_option('rvcfdi_ci_consultarPedidos_nombre_parametro_numeropedido', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_nombre_parametro_monto') !== false)
				$rvcfdi_ci_consultarPedidos_nombre_parametro_monto = get_option('rvcfdi_ci_consultarPedidos_nombre_parametro_monto');
			else
				add_option('rvcfdi_ci_consultarPedidos_nombre_parametro_monto', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_parametro_extra1_tipo') !== false)
				$rvcfdi_ci_consultarPedidos_parametro_extra1_tipo = get_option('rvcfdi_ci_consultarPedidos_parametro_extra1_tipo');
			else
				add_option('rvcfdi_ci_consultarPedidos_parametro_extra1_tipo', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombrevisual') !== false)
				$rvcfdi_ci_consultarPedidos_parametro_extra1_nombrevisual = get_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombrevisual');
			else
				add_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombrevisual', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombreinterno') !== false)
				$rvcfdi_ci_consultarPedidos_parametro_extra1_nombreinterno = get_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombreinterno');
			else
				add_option('rvcfdi_ci_consultarPedidos_parametro_extra1_nombreinterno', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_parametro_extra1_estado') !== false)
				$rvcfdi_ci_consultarPedidos_parametro_extra1_estado = get_option('rvcfdi_ci_consultarPedidos_parametro_extra1_estado');
			else
				add_option('rvcfdi_ci_consultarPedidos_parametro_extra1_estado', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_parametro_extra2_tipo') !== false)
				$rvcfdi_ci_consultarPedidos_parametro_extra2_tipo = get_option('rvcfdi_ci_consultarPedidos_parametro_extra2_tipo');
			else
				add_option('rvcfdi_ci_consultarPedidos_parametro_extra2_tipo', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombrevisual') !== false)
				$rvcfdi_ci_consultarPedidos_parametro_extra2_nombrevisual = get_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombrevisual');
			else
				add_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombrevisual', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombreinterno') !== false)
				$rvcfdi_ci_consultarPedidos_parametro_extra2_nombreinterno = get_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombreinterno');
			else
				add_option('rvcfdi_ci_consultarPedidos_parametro_extra2_nombreinterno', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_parametro_extra2_estado') !== false)
				$rvcfdi_ci_consultarPedidos_parametro_extra2_estado = get_option('rvcfdi_ci_consultarPedidos_parametro_extra2_estado');
			else
				add_option('rvcfdi_ci_consultarPedidos_parametro_extra2_estado', '');
			
			if(get_option('rvcfdi_ci_consultarPedidos_tipo_consulta') !== false)
				$rvcfdi_ci_consultarPedidos_tipo_consulta = get_option('rvcfdi_ci_consultarPedidos_tipo_consulta');
			else
				add_option('rvcfdi_ci_consultarPedidos_tipo_consulta', '');
			
			if(get_option('rvcfdi_ci_enviarPedidos_tipo_conexion') !== false)
				$rvcfdi_ci_enviarPedidos_tipo_conexion = get_option('rvcfdi_ci_enviarPedidos_tipo_conexion');
			else
				add_option('rvcfdi_ci_enviarPedidos_tipo_conexion', '');
			
			if(get_option('rvcfdi_ci_enviarPedidos_tipo_solicitud') !== false)
				$rvcfdi_ci_enviarPedidos_tipo_solicitud = get_option('rvcfdi_ci_enviarPedidos_tipo_solicitud');
			else
				add_option('rvcfdi_ci_enviarPedidos_tipo_solicitud', '');
			
			if(get_option('rvcfdi_ci_enviarPedidos_url') !== false)
				$rvcfdi_ci_enviarPedidos_url = get_option('rvcfdi_ci_enviarPedidos_url');
			else
				add_option('rvcfdi_ci_enviarPedidos_url', '');
			
			if(get_option('rvcfdi_ci_enviarPedidos_tipo_conexion2') !== false)
				$rvcfdi_ci_enviarPedidos_tipo_conexion2 = get_option('rvcfdi_ci_enviarPedidos_tipo_conexion2');
			else
				add_option('rvcfdi_ci_enviarPedidos_tipo_conexion2', '');
			
			if(get_option('rvcfdi_ci_enviarPedidos_tipo_solicitud2') !== false)
				$rvcfdi_ci_enviarPedidos_tipo_solicitud2 = get_option('rvcfdi_ci_enviarPedidos_tipo_solicitud2');
			else
				add_option('rvcfdi_ci_enviarPedidos_tipo_solicitud2', '');
			
			if(get_option('rvcfdi_ci_enviarPedidos_url2') !== false)
				$rvcfdi_ci_enviarPedidos_url2 = get_option('rvcfdi_ci_enviarPedidos_url2');
			else
				add_option('rvcfdi_ci_enviarPedidos_url2', '');
			
			if(get_option('rvcfdi_ci_enviarPedidos_tipo_consulta') !== false)
				$rvcfdi_ci_enviarPedidos_tipo_consulta = get_option('rvcfdi_ci_enviarPedidos_tipo_consulta');
			else
				add_option('rvcfdi_ci_enviarPedidos_tipo_consulta', '');
			
			if(get_option('rvcfdi_ci_enviarXml_tipo_conexion') !== false)
				$rvcfdi_ci_enviarXml_tipo_conexion = get_option('rvcfdi_ci_enviarXml_tipo_conexion');
			else
				add_option('rvcfdi_ci_enviarXml_tipo_conexion', '');
			
			if(get_option('rvcfdi_ci_enviarXml_tipo_solicitud') !== false)
				$rvcfdi_ci_enviarXml_tipo_solicitud = get_option('rvcfdi_ci_enviarXml_tipo_solicitud');
			else
				add_option('rvcfdi_ci_enviarXml_tipo_solicitud', '');
			
			if(get_option('rvcfdi_ci_enviarXml_url') !== false)
				$rvcfdi_ci_enviarXml_url = get_option('rvcfdi_ci_enviarXml_url');
			else
				add_option('rvcfdi_ci_enviarXml_url', '');
			
			if(get_option('rvcfdi_ci_enviarXml_tipo_consulta') !== false)
				$rvcfdi_ci_enviarXml_tipo_consulta = get_option('rvcfdi_ci_enviarXml_tipo_consulta');
			else
				add_option('rvcfdi_ci_enviarXml_tipo_consulta', '');
			
			if(get_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion') !== false)
				$rvcfdi_ci_enviarPedidosCrear_tipo_conexion = get_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion');
			else
				add_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion', '');
			
			if(get_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud') !== false)
				$rvcfdi_ci_enviarPedidosCrear_tipo_solicitud = get_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud');
			else
				add_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud', '');
			
			if(get_option('rvcfdi_ci_enviarPedidosCrear_url') !== false)
				$rvcfdi_ci_enviarPedidosCrear_url = get_option('rvcfdi_ci_enviarPedidosCrear_url');
			else
				add_option('rvcfdi_ci_enviarPedidosCrear_url', '');
			
			if(get_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion2') !== false)
				$rvcfdi_ci_enviarPedidosCrear_tipo_conexion2 = get_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion2');
			else
				add_option('rvcfdi_ci_enviarPedidosCrear_tipo_conexion2', '');
			
			if(get_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud2') !== false)
				$rvcfdi_ci_enviarPedidosCrear_tipo_solicitud2 = get_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud2');
			else
				add_option('rvcfdi_ci_enviarPedidosCrear_tipo_solicitud2', '');
			
			if(get_option('rvcfdi_ci_enviarPedidosCrear_url2') !== false)
				$rvcfdi_ci_enviarPedidosCrear_url2 = get_option('rvcfdi_ci_enviarPedidosCrear_url2');
			else
				add_option('rvcfdi_ci_enviarPedidosCrear_url2', '');
			
			if(get_option('rvcfdi_ci_enviarPedidosCrear_tipo_consulta') !== false)
				$rvcfdi_ci_enviarPedidosCrear_tipo_consulta = get_option('rvcfdi_ci_enviarPedidosCrear_tipo_consulta');
			else
				add_option('rvcfdi_ci_enviarPedidosCrear_tipo_consulta', '');
			
			
			
			if(get_option('rvcfdi_ci_enviarXml_tipo_conexion2') !== false)
				$rvcfdi_ci_enviarXml_tipo_conexion2 = get_option('rvcfdi_ci_enviarXml_tipo_conexion2');
			else
				add_option('rvcfdi_ci_enviarXml_tipo_conexion2', '');
			
			if(get_option('rvcfdi_ci_enviarXml_tipo_solicitud2') !== false)
				$rvcfdi_ci_enviarXml_tipo_solicitud2 = get_option('rvcfdi_ci_enviarXml_tipo_solicitud2');
			else
				add_option('rvcfdi_ci_enviarXml_tipo_solicitud2', '');
			
			if(get_option('rvcfdi_ci_enviarXml_url2') !== false)
				$rvcfdi_ci_enviarXml_url2 = get_option('rvcfdi_ci_enviarXml_url2');
			else
				add_option('rvcfdi_ci_enviarXml_url2', '');
			
			$datosConfiguracion = array
			(
				$rvcfdi_ci_consultarPedidos_tipo_conexion,
				$rvcfdi_ci_consultarPedidos_tipo_solicitud,
				$rvcfdi_ci_consultarPedidos_url,
				$rvcfdi_ci_consultarPedidos_nombre_parametro_numeropedido,
				$rvcfdi_ci_consultarPedidos_nombre_parametro_monto,
				$rvcfdi_ci_consultarPedidos_parametro_extra1_tipo,
				$rvcfdi_ci_consultarPedidos_parametro_extra1_nombrevisual,
				$rvcfdi_ci_consultarPedidos_parametro_extra1_nombreinterno,
				$rvcfdi_ci_consultarPedidos_parametro_extra1_estado,
				$rvcfdi_ci_consultarPedidos_parametro_extra2_tipo,
				$rvcfdi_ci_consultarPedidos_parametro_extra2_nombrevisual,
				$rvcfdi_ci_consultarPedidos_parametro_extra2_nombreinterno,
				$rvcfdi_ci_consultarPedidos_parametro_extra2_estado,
				$rvcfdi_ci_consultarPedidos_tipo_consulta,
				$rvcfdi_ci_enviarPedidos_tipo_conexion,
				$rvcfdi_ci_enviarPedidos_tipo_solicitud,
				$rvcfdi_ci_enviarPedidos_url,
				$rvcfdi_ci_enviarPedidos_tipo_conexion2,
				$rvcfdi_ci_enviarPedidos_tipo_solicitud2,
				$rvcfdi_ci_enviarPedidos_url2,
				$rvcfdi_ci_enviarPedidos_tipo_consulta,
				$rvcfdi_ci_enviarXml_tipo_conexion,
				$rvcfdi_ci_enviarXml_tipo_solicitud,
				$rvcfdi_ci_enviarXml_url,
				$rvcfdi_ci_enviarXml_tipo_consulta,
				$rvcfdi_ci_enviarPedidosCrear_tipo_conexion,
				$rvcfdi_ci_enviarPedidosCrear_tipo_solicitud,
				$rvcfdi_ci_enviarPedidosCrear_url,
				$rvcfdi_ci_enviarPedidosCrear_tipo_conexion2,
				$rvcfdi_ci_enviarPedidosCrear_tipo_solicitud2,
				$rvcfdi_ci_enviarPedidosCrear_url2,
				$rvcfdi_ci_enviarPedidosCrear_tipo_consulta,
				$rvcfdi_ci_enviarXml_tipo_conexion2,
				$rvcfdi_ci_enviarXml_tipo_solicitud2,
				$rvcfdi_ci_enviarXml_url2
			);
			
			return $datosConfiguracion;
		}
	}
?>