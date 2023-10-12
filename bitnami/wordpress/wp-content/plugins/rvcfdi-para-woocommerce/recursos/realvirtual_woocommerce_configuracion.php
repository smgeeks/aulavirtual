<?php
	class RealVirtualWooCommerceConfiguracion
	{
		static $serie		   						= '';
		static $estado_orden   						= '';
		static $titulo   							= '';
		static $descripcion   						= '';
		static $color_fondo_encabezado   			= '';
		static $color_texto_encabezado   			= '';
		static $color_fondo_formulario   			= '';
		static $color_texto_formulario   			= '';
		static $color_texto_controles_formulario 	= '';
		static $color_boton   						= '';
		static $color_texto_boton   				= '';
		static $estado_orden_refacturacion			= '';
		static $version_cfdi						= '';
		static $metodo_pago							= '';
		static $metodo_pago_seleccionar				= '';
		static $metodo_pago33						= '';
		static $metodo_pago_seleccionar33			= '';
		static $idioma								= '';
		static $uso_cfdi							= '';
		static $uso_cfdi_seleccionar				= '';
		static $clave_servicio						= '';
		static $clave_unidad						= '';
		static $unidad_medida						= '';
		static $regimen_fiscal						= '';
		static $clave_producto						= '';
		static $clave_confirmacion					= '';
		static $numero_pedimento					= '';
		static $moneda								= '';
		static $tipo_cambio							= '';
		static $observacion							= '';
		static $pedido_mes_actual					= '';
		static $conceptos_especiales_envio			= '';
		static $precision_decimal					= '';
		static $manejo_impuestos_pedido				= '';
		static $color_boton_vistaprevia   			= '';
		static $color_texto_boton_vistaprevia   	= '';
		static $color_boton_generarcfdi  			= '';
		static $color_texto_boton_generarcfdi   	= '';
		static $clave_servicio_shipping				= '';
		static $clave_unidad_shipping				= '';
		static $unidad_medida_shipping				= '';
		static $clave_producto_shipping				= '';
		static $numero_pedimento_shipping			= '';
		static $config_principal_shipping			= '';
		static $huso_horario						= '';
		static $domicilio_receptor					= '';
		static $mostrarMensajeErrorCliente			= '';
		static $mensajeErrorCliente					= '';
		static $complementoCFDI						= '';
		static $complementoCFDI_iedu_configuracion_nivel	= '';
		static $complementoCFDI_iedu_configuracion_autRVOE	= '';
		static $manejo_impuestos_pedido_facturaGlobal		= '';
		static $manejo_impuestos_pedido_facturaGlobal_texto		= '';
		static $exportacion_cfdi = '';
		static $facAtrAdquirente = '';
		static $objeto_imp_producto = '';
		static $objeto_imp_shipping = '';
		static $estado_orden_cfdi_automatico = '';
		static $informacionGlobal_periodicidad = '';
		static $informacionGlobal_meses = '';
		static $informacionGlobal_año = '';
		static $notificar_error_cfdi_automatico = '';
		static $emailNotificacionErrorModuloClientes = '';
		static $emailNotificacionErrorAutomatico = '';
		
		static function guardarConfiguracionGeneral($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_serie', base64_encode($configuracion['serie']));
			update_option('rvcfdi_version_cfdi', base64_encode($configuracion['version_cfdi']));
			update_option('rvcfdi_regimen_fiscal', base64_encode($configuracion['regimen_fiscal']));
			update_option('rvcfdi_moneda', base64_encode($configuracion['moneda']));
			update_option('rvcfdi_tipo_cambio', base64_encode($configuracion['tipo_cambio']));
			update_option('rvcfdi_observacion', base64_encode($configuracion['observacion']));
			update_option('rvcfdi_precision_decimal', base64_encode($configuracion['precision_decimal']));
			update_option('rvcfdi_huso_horario', base64_encode($configuracion['huso_horario']));
			update_option('rvcfdi_domicilio_receptor', base64_encode($configuracion['domicilio_receptor']));
			update_option('rvcfdi_exportacion_cfdi', base64_encode($configuracion['exportacion_cfdi']));
			update_option('rvcfdi_facAtrAdquirente', base64_encode($configuracion['facAtrAdquirente']));
			update_option('rvcfdi_informacionGlobal_periodicidad', base64_encode($configuracion['informacionGlobal_periodicidad']));
			update_option('rvcfdi_informacionGlobal_meses', base64_encode($configuracion['informacionGlobal_meses']));
			update_option('rvcfdi_informacionGlobal_año', base64_encode($configuracion['informacionGlobal_año']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracionProductos($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_clave_servicio', ($configuracion['clave_servicio']));
			update_option('rvcfdi_clave_unidad', ($configuracion['clave_unidad']));
			update_option('rvcfdi_unidad_medida', base64_encode($configuracion['unidad_medida']));
			update_option('rvcfdi_clave_producto', base64_encode($configuracion['clave_producto']));
			update_option('rvcfdi_numero_pedimento', base64_encode($configuracion['numero_pedimento']));
			update_option('rvcfdi_objeto_imp_producto', base64_encode($configuracion['objeto_imp_producto']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracionEnvios($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_clave_servicio_shipping', ($configuracion['clave_servicio_shipping']));
			update_option('rvcfdi_clave_unidad_shipping', ($configuracion['clave_unidad_shipping']));
			update_option('rvcfdi_unidad_medida_shipping', base64_encode($configuracion['unidad_medida_shipping']));
			update_option('rvcfdi_clave_producto_shipping', base64_encode($configuracion['clave_producto_shipping']));
			update_option('rvcfdi_numero_pedimento_shipping', base64_encode($configuracion['numero_pedimento_shipping']));
			update_option('rvcfdi_config_principal_shipping', base64_encode($configuracion['config_principal_shipping']));
			update_option('rvcfdi_objeto_imp_shipping', base64_encode($configuracion['objeto_imp_shipping']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracionReglasModuloClientes($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_estado_orden', base64_encode($configuracion['estado_orden']));
			update_option('rvcfdi_titulo', base64_encode($configuracion['titulo']));
			update_option('rvcfdi_descripcion', base64_encode($configuracion['descripcion']));
			update_option('rvcfdi_estado_orden_refacturacion', base64_encode($configuracion['estado_orden_refacturacion']));
			update_option('rvcfdi_metodo_pago', base64_encode($configuracion['metodo_pago']));
			update_option('rvcfdi_metodo_pago_seleccionar', base64_encode($configuracion['metodo_pago_seleccionar']));
			update_option('rvcfdi_metodo_pago33', base64_encode($configuracion['metodo_pago33']));
			update_option('rvcfdi_metodo_pago_seleccionar33', base64_encode($configuracion['metodo_pago_seleccionar33']));
			update_option('rvcfdi_uso_cfdi', base64_encode($configuracion['uso_cfdi']));
			update_option('rvcfdi_uso_cfdi_seleccionar', base64_encode($configuracion['uso_cfdi_seleccionar']));
			update_option('rvcfdi_pedido_mes_actual', base64_encode($configuracion['pedido_mes_actual']));
			update_option('rvcfdi_conceptos_especiales_envio', base64_encode($configuracion['conceptos_especiales_envio']));
			update_option('rvcfdi_mostrarMensajeErrorCliente', base64_encode($configuracion['mostrarMensajeErrorCliente']));
			update_option('rvcfdi_mensajeErrorCliente', base64_encode($configuracion['mensajeErrorCliente']));
			update_option('rvcfdi_emailNotificacionErrorModuloClientes', base64_encode($configuracion['emailNotificacionErrorModuloClientes']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracionEstiloModuloClientes($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_color_fondo_encabezado', base64_encode($configuracion['color_fondo_encabezado']));
			update_option('rvcfdi_color_texto_encabezado', base64_encode($configuracion['color_texto_encabezado']));
			update_option('rvcfdi_color_fondo_formulario', base64_encode($configuracion['color_fondo_formulario']));
			update_option('rvcfdi_color_texto_formulario', base64_encode($configuracion['color_texto_formulario']));
			update_option('rvcfdi_color_texto_controles_formulario', base64_encode($configuracion['color_texto_controles_formulario']));
			update_option('rvcfdi_color_boton', base64_encode($configuracion['color_boton']));
			update_option('rvcfdi_color_texto_boton', base64_encode($configuracion['color_texto_boton']));
			update_option('rvcfdi_color_boton_vistaprevia', base64_encode($configuracion['color_boton_vistaprevia']));
			update_option('rvcfdi_color_texto_boton_vistaprevia', base64_encode($configuracion['color_texto_boton_vistaprevia']));
			update_option('rvcfdi_color_boton_generarcfdi', base64_encode($configuracion['color_boton_generarcfdi']));
			update_option('rvcfdi_color_texto_boton_generarcfdi', base64_encode($configuracion['color_texto_boton_generarcfdi']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracionAjustesAvanzados($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_manejo_impuestos_pedido', base64_encode($configuracion['manejo_impuestos_pedido']));
			update_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal', base64_encode($configuracion['manejo_impuestos_pedido_facturaGlobal']));
			update_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal_texto', base64_encode($configuracion['manejo_impuestos_pedido_facturaGlobal_texto']));
			update_option('rvcfdi_estado_orden_cfdi_automatico', base64_encode($configuracion['estado_orden_cfdi_automatico']));
			update_option('rvcfdi_notificar_error_cfdi_automatico', base64_encode($configuracion['notificar_error_cfdi_automatico']));
			update_option('rvcfdi_complementoCFDI', base64_encode($configuracion['complementoCFDI']));
			update_option('rvcfdi_complementoCFDI_iedu_configuracion_nivel', base64_encode($configuracion['complementoCFDI_iedu_configuracion_nivel']));
			update_option('rvcfdi_complementoCFDI_iedu_configuracion_autRVOE', base64_encode($configuracion['complementoCFDI_iedu_configuracion_autRVOE']));
			update_option('rvcfdi_emailNotificacionErrorAutomatico', base64_encode($configuracion['emailNotificacionErrorAutomatico']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracionIdioma($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_idioma', base64_encode($configuracion['idioma']));
			
			return self::guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);
		}
		
		static function guardarConfiguracion($rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			$configuracion = self::configuracionEntidad();
			
			$opcion = 'GuardarConfiguracion';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'SERIE' => base64_encode($configuracion['serie']),
				'ESTADO_ORDEN' => base64_encode($configuracion['estado_orden']),
				'TITULO' => base64_encode($configuracion['titulo']),
				'DESCRIPCION' => base64_encode($configuracion['descripcion']),
				'COLOR_FONDO_ENCABEZADO' => base64_encode($configuracion['color_fondo_encabezado']),
				'COLOR_TEXTO_ENCABEZADO' => base64_encode($configuracion['color_texto_encabezado']),
				'COLOR_FONDO_FORMULARIO' => base64_encode($configuracion['color_fondo_formulario']),
				'COLOR_TEXTO_FORMULARIO' => base64_encode($configuracion['color_texto_formulario']),
				'COLOR_TEXTO_CONTROLES_FORMULARIO' => base64_encode($configuracion['color_texto_controles_formulario']),
				'COLOR_BOTON' => base64_encode($configuracion['color_boton']),
				'COLOR_TEXTO_BOTON' => base64_encode($configuracion['color_texto_boton']),
				'ESTADO_ORDEN_REFACTURACION' => base64_encode($configuracion['estado_orden_refacturacion']),
				'VERSION_CFDI' => base64_encode($configuracion['version_cfdi']),
				'METODO_PAGO' => base64_encode($configuracion['metodo_pago']),
				'METODO_PAGO_SELECCIONAR' => base64_encode($configuracion['metodo_pago_seleccionar']),
				'IDIOMA' => base64_encode($configuracion['idioma']),
				'USO_CFDI' => base64_encode($configuracion['uso_cfdi']),
				'USO_CFDI_SELECCIONAR' => base64_encode($configuracion['uso_cfdi_seleccionar']),
				'CLAVE_SERVICIO' => ($configuracion['clave_servicio']),
				'CLAVE_UNIDAD' => ($configuracion['clave_unidad']),
				'UNIDAD_MEDIDA' => base64_encode($configuracion['unidad_medida']),
				'REGIMEN_FISCAL' => base64_encode($configuracion['regimen_fiscal']),
				'CLAVE_PRODUCTO' => base64_encode($configuracion['clave_producto']),
				'CLAVE_CONFIRMACION' => base64_encode($configuracion['clave_confirmacion']),
				'NUMERO_PEDIMENTO' => base64_encode($configuracion['numero_pedimento']),
				'MONEDA' => base64_encode($configuracion['moneda']),
				'TIPO_CAMBIO' => base64_encode($configuracion['tipo_cambio']),
				'OBSERVACION' => base64_encode($configuracion['observacion']),
				'PRECISION_DECIMAL' => base64_encode($configuracion['precision_decimal']),
				'PEDIDO_MES_ACTUAL' => base64_encode($configuracion['pedido_mes_actual']),
				'METODO_PAGO33' => base64_encode($configuracion['metodo_pago33']),
				'METODO_PAGO_SELECCIONAR33' => base64_encode($configuracion['metodo_pago_seleccionar33']),
				'CONCEPTOS_ESPECIALES_ENVIO' => base64_encode($configuracion['conceptos_especiales_envio']),
				'MANEJO_IMPUESTOS_PEDIDO' => base64_encode($configuracion['manejo_impuestos_pedido']),
				'COLOR_BOTON_VISTAPREVIA' => base64_encode($configuracion['color_boton_vistaprevia']),
				'COLOR_TEXTO_BOTON_VISTAPREVIA' => base64_encode($configuracion['color_texto_boton_vistaprevia']),
				'COLOR_BOTON_GENERARCFDI' => base64_encode($configuracion['color_boton_generarcfdi']),
				'COLOR_TEXTO_BOTON_GENERARCFDI' => base64_encode($configuracion['color_texto_boton_generarcfdi']),
				'CLAVE_SERVICIO_SHIPPING' => ($configuracion['clave_servicio_shipping']),
				'CLAVE_UNIDAD_SHIPPING' => ($configuracion['clave_unidad_shipping']),
				'UNIDAD_MEDIDA_SHIPPING' => base64_encode($configuracion['unidad_medida_shipping']),
				'CLAVE_PRODUCTO_SHIPPING' => base64_encode($configuracion['clave_producto_shipping']),
				'NUMERO_PEDIMENTO_SHIPPING' => base64_encode($configuracion['numero_pedimento_shipping']),
				'CONFIG_PRINCIPAL_SHIPPING' => base64_encode($configuracion['config_principal_shipping']),
				'HUSO_HORARIO' => base64_encode($configuracion['huso_horario']),
				'DOMICILIO_RECEPTOR' => base64_encode($configuracion['domicilio_receptor']),
				'MOSTRAR_MENSAJE_ERROR_CLIENTE' => base64_encode($configuracion['mostrarMensajeErrorCliente']),
				'MENSAJE_ERROR_CLIENTE' => base64_encode($configuracion['mensajeErrorCliente']),
				'COMPLEMENTO_CFDI' => base64_encode($configuracion['complementoCFDI']),
				'COMPLEMENTO_CFDI_IEDU_CONFIGURACION_NIVEL' => base64_encode($configuracion['complementoCFDI_iedu_configuracion_nivel']),
				'COMPLEMENTO_CFDI_IEDU_CONFIGURACION_AUTRVOE' => base64_encode($configuracion['complementoCFDI_iedu_configuracion_autRVOE']),
				'MANEJO_IMPUESTOS_PEDIDO_FACTURAGLOBAL' => base64_encode($configuracion['manejo_impuestos_pedido_facturaGlobal']),
				'MANEJO_IMPUESTOS_PEDIDO_FACTURAGLOBAL_TEXTO' => base64_encode($configuracion['manejo_impuestos_pedido_facturaGlobal_texto']),
				'EXPORTACION_CFDI' => base64_encode($configuracion['exportacion_cfdi']),
				'FACATRADQUIRIENTE' => base64_encode($configuracion['facAtrAdquirente']),
				'OBJETO_IMP_PRODUCTO' => base64_encode($configuracion['objeto_imp_producto']),
				'OBJETO_IMP_SHIPPING' => base64_encode($configuracion['objeto_imp_shipping']),
				'ESTADO_ORDEN_CFDI_AUTOMATICO' => base64_encode($configuracion['estado_orden_cfdi_automatico']),
				'NOTIFICAR_ERROR_CFDI_AUTOMATICO' => base64_encode($configuracion['notificar_error_cfdi_automatico']),
				'INFORMACIONGLOBAL_PERIODICIDAD' => base64_encode($configuracion['informacionGlobal_periodicidad']),
				'INFORMACIONGLOBAL_MESES' => base64_encode($configuracion['informacionGlobal_meses']),
				'INFORMACIONGLOBAL_AÑO' => base64_encode($configuracion['informacionGlobal_año']),
				'EMAILNOTIFICACIONERRORMODULOCLIENTES' => base64_encode($configuracion['emailNotificacionErrorModuloClientes']),
				'EMAILNOTIFICACIONERRORAUTOMATICO' => base64_encode($configuracion['emailNotificacionErrorAutomatico'])
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
		
		static function guardarConfiguracionLocal($configuracion, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_serie', ($configuracion['serie']));
			update_option('rvcfdi_estado_orden', ($configuracion['estado_orden']));
			update_option('rvcfdi_titulo', ($configuracion['titulo']));
			update_option('rvcfdi_descripcion', ($configuracion['descripcion']));
			update_option('rvcfdi_color_fondo_encabezado', ($configuracion['color_fondo_encabezado']));
			update_option('rvcfdi_color_texto_encabezado', ($configuracion['color_texto_encabezado']));
			update_option('rvcfdi_color_fondo_formulario', ($configuracion['color_fondo_formulario']));
			update_option('rvcfdi_color_texto_formulario', ($configuracion['color_texto_formulario']));
			update_option('rvcfdi_color_texto_controles_formulario', ($configuracion['color_texto_controles_formulario']));
			update_option('rvcfdi_color_boton', ($configuracion['color_boton']));
			update_option('rvcfdi_color_texto_boton', ($configuracion['color_texto_boton']));
			update_option('rvcfdi_estado_orden_refacturacion', ($configuracion['estado_orden_refacturacion']));
			update_option('rvcfdi_version_cfdi', ($configuracion['version_cfdi']));
			update_option('rvcfdi_metodo_pago', ($configuracion['metodo_pago']));
			update_option('rvcfdi_metodo_pago_seleccionar', ($configuracion['metodo_pago_seleccionar']));
			update_option('rvcfdi_idioma', ($configuracion['idioma']));
			update_option('rvcfdi_uso_cfdi', ($configuracion['uso_cfdi']));
			update_option('rvcfdi_uso_cfdi_seleccionar', ($configuracion['uso_cfdi_seleccionar']));
			update_option('rvcfdi_clave_servicio', $configuracion['clave_servicio']);
			update_option('rvcfdi_clave_unidad', $configuracion['clave_unidad']);
			update_option('rvcfdi_unidad_medida', ($configuracion['unidad_medida']));
			update_option('rvcfdi_regimen_fiscal', ($configuracion['regimen_fiscal']));
			update_option('rvcfdi_clave_producto', ($configuracion['clave_producto']));
			update_option('rvcfdi_clave_confirmacion', ($configuracion['clave_confirmacion']));
			update_option('rvcfdi_numero_pedimento', ($configuracion['numero_pedimento']));
			update_option('rvcfdi_moneda', ($configuracion['moneda']));
			update_option('rvcfdi_tipo_cambio', ($configuracion['tipo_cambio']));
			update_option('rvcfdi_observacion', ($configuracion['observacion']));
			update_option('rvcfdi_precision_decimal', ($configuracion['precision_decimal']));
			update_option('rvcfdi_pedido_mes_actual', ($configuracion['pedido_mes_actual']));
			update_option('rvcfdi_metodo_pago33', ($configuracion['metodo_pago33']));
			update_option('rvcfdi_metodo_pago_seleccionar33', ($configuracion['metodo_pago_seleccionar33']));
			update_option('rvcfdi_conceptos_especiales_envio', ($configuracion['conceptos_especiales_envio']));
			update_option('rvcfdi_manejo_impuestos_pedido', ($configuracion['manejo_impuestos_pedido']));
			update_option('rvcfdi_color_boton_vistaprevia', ($configuracion['color_boton_vistaprevia']));
			update_option('rvcfdi_color_texto_boton_vistaprevia', ($configuracion['color_texto_boton_vistaprevia']));
			update_option('rvcfdi_color_boton_generarcfdi', ($configuracion['color_boton_generarcfdi']));
			update_option('rvcfdi_color_texto_boton_generarcfdi', ($configuracion['color_texto_boton_generarcfdi']));
			update_option('rvcfdi_clave_servicio_shipping', $configuracion['clave_servicio_shipping']);
			update_option('rvcfdi_clave_unidad_shipping', $configuracion['clave_unidad_shipping']);
			update_option('rvcfdi_unidad_medida_shipping', ($configuracion['unidad_medida_shipping']));
			update_option('rvcfdi_clave_producto_shipping', ($configuracion['clave_producto_shipping']));
			update_option('rvcfdi_numero_pedimento_shipping', ($configuracion['numero_pedimento_shipping']));
			update_option('rvcfdi_config_principal_shipping', ($configuracion['config_principal_shipping']));
			update_option('rvcfdi_huso_horario', ($configuracion['huso_horario']));
			update_option('rvcfdi_domicilio_receptor', ($configuracion['domicilio_receptor']));
			update_option('rvcfdi_mostrarMensajeErrorCliente', ($configuracion['mostrarMensajeErrorCliente']));
			update_option('rvcfdi_mensajeErrorCliente', ($configuracion['mensajeErrorCliente']));
			update_option('rvcfdi_complementoCFDI', ($configuracion['complementoCFDI']));
			update_option('rvcfdi_complementoCFDI_iedu_configuracion_nivel', ($configuracion['complementoCFDI_iedu_configuracion_nivel']));
			update_option('rvcfdi_complementoCFDI_iedu_configuracion_autRVOE', ($configuracion['complementoCFDI_iedu_configuracion_autRVOE']));
			update_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal', ($configuracion['manejo_impuestos_pedido_facturaGlobal']));
			update_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal_texto', ($configuracion['manejo_impuestos_pedido_facturaGlobal_texto']));
			update_option('rvcfdi_exportacion_cfdi', ($configuracion['exportacion_cfdi']));
			update_option('rvcfdi_facAtrAdquirente', ($configuracion['facAtrAdquirente']));
			update_option('rvcfdi_objeto_imp_producto', ($configuracion['objeto_imp_producto']));
			update_option('rvcfdi_objeto_imp_shipping', ($configuracion['objeto_imp_shipping']));
			update_option('rvcfdi_estado_orden_cfdi_automatico', ($configuracion['estado_orden_cfdi_automatico']));
			update_option('rvcfdi_notificar_error_cfdi_automatico', ($configuracion['notificar_error_cfdi_automatico']));
			update_option('rvcfdi_informacionGlobal_periodicidad', ($configuracion['informacionGlobal_periodicidad']));
			update_option('rvcfdi_informacionGlobal_meses', ($configuracion['informacionGlobal_meses']));
			update_option('rvcfdi_informacionGlobal_año', ($configuracion['informacionGlobal_año']));
			update_option('rvcfdi_emailNotificacionErrorModuloClientes', ($configuracion['emailNotificacionErrorModuloClientes']));
			update_option('rvcfdi_emailNotificacionErrorAutomatico', ($configuracion['emailNotificacionErrorAutomatico']));
			
			return true;
		}
		
		static function configuracionEntidad()
		{
			$datosConfiguracion = self::obtenerConfiguracion();

			return array
			(
				'serie'     								=> base64_decode($datosConfiguracion[0]),
				'estado_orden' 								=> base64_decode($datosConfiguracion[1]),
				'titulo'   	 								=> base64_decode($datosConfiguracion[2]),
				'descripcion'   							=> base64_decode($datosConfiguracion[3]),
				'color_fondo_encabezado'   					=> base64_decode($datosConfiguracion[4]),
				'color_texto_encabezado'   					=> base64_decode($datosConfiguracion[5]),
				'color_fondo_formulario'   					=> base64_decode($datosConfiguracion[6]),
				'color_texto_formulario'   					=> base64_decode($datosConfiguracion[7]),
				'color_texto_controles_formulario'   		=> base64_decode($datosConfiguracion[8]),
				'color_boton'   							=> base64_decode($datosConfiguracion[9]),
				'color_texto_boton'   						=> base64_decode($datosConfiguracion[10]),
				'estado_orden_refacturacion'   				=> base64_decode($datosConfiguracion[11]),
				'version_cfdi'   							=> base64_decode($datosConfiguracion[12]),
				'metodo_pago'   							=> base64_decode($datosConfiguracion[13]),
				'metodo_pago_seleccionar'   				=> base64_decode($datosConfiguracion[14]),
				'idioma'   									=> base64_decode($datosConfiguracion[15]),
				'uso_cfdi'   								=> base64_decode($datosConfiguracion[16]),
				'uso_cfdi_seleccionar'   					=> base64_decode($datosConfiguracion[17]),
				'clave_servicio'   							=> ($datosConfiguracion[18]),
				'clave_unidad'   							=> ($datosConfiguracion[19]),
				'unidad_medida'   							=> base64_decode($datosConfiguracion[20]),
				'regimen_fiscal'   							=> base64_decode($datosConfiguracion[21]),
				'clave_producto'   							=> base64_decode($datosConfiguracion[22]),
				'clave_confirmacion'   						=> base64_decode($datosConfiguracion[23]),
				'numero_pedimento'   						=> base64_decode($datosConfiguracion[24]),
				'moneda'   									=> base64_decode($datosConfiguracion[25]),
				'tipo_cambio'   							=> base64_decode($datosConfiguracion[26]),
				'observacion'   							=> base64_decode($datosConfiguracion[27]),
				'precision_decimal'   						=> base64_decode($datosConfiguracion[28]),
				'pedido_mes_actual'   						=> base64_decode($datosConfiguracion[29]),
				'metodo_pago33'   							=> base64_decode($datosConfiguracion[30]),
				'metodo_pago_seleccionar33'   				=> base64_decode($datosConfiguracion[31]),
				'conceptos_especiales_envio'   				=> base64_decode($datosConfiguracion[32]),
				'manejo_impuestos_pedido'   				=> base64_decode($datosConfiguracion[33]),
				'color_boton_vistaprevia'   				=> base64_decode($datosConfiguracion[34]),
				'color_texto_boton_vistaprevia'   			=> base64_decode($datosConfiguracion[35]),
				'color_boton_generarcfdi'   				=> base64_decode($datosConfiguracion[36]),
				'color_texto_boton_generarcfdi'   			=> base64_decode($datosConfiguracion[37]),
				'clave_servicio_shipping'   				=> ($datosConfiguracion[38]),
				'clave_unidad_shipping'   					=> ($datosConfiguracion[39]),
				'unidad_medida_shipping'   					=> base64_decode($datosConfiguracion[40]),
				'clave_producto_shipping'   				=> base64_decode($datosConfiguracion[41]),
				'numero_pedimento_shipping'   				=> base64_decode($datosConfiguracion[42]),
				'config_principal_shipping'   				=> base64_decode($datosConfiguracion[43]),
				'huso_horario'   							=> base64_decode($datosConfiguracion[44]),
				'domicilio_receptor'   						=> base64_decode($datosConfiguracion[45]),
				'mostrarMensajeErrorCliente'   				=> base64_decode($datosConfiguracion[46]),
				'mensajeErrorCliente'   					=> base64_decode($datosConfiguracion[47]),
				'complementoCFDI'   						=> base64_decode($datosConfiguracion[48]),
				'complementoCFDI_iedu_configuracion_nivel'  => base64_decode($datosConfiguracion[49]),
				'complementoCFDI_iedu_configuracion_autRVOE' => base64_decode($datosConfiguracion[50]),
				'manejo_impuestos_pedido_facturaGlobal'   	=> base64_decode($datosConfiguracion[51]),
				'manejo_impuestos_pedido_facturaGlobal_texto'   	=> base64_decode($datosConfiguracion[52]),
				'exportacion_cfdi'   						=> base64_decode($datosConfiguracion[53]),
				'facAtrAdquirente'   						=> base64_decode($datosConfiguracion[54]),
				'objeto_imp_producto'   					=> base64_decode($datosConfiguracion[55]),
				'objeto_imp_shipping'   					=> base64_decode($datosConfiguracion[56]),
				'estado_orden_cfdi_automatico'   			=> base64_decode($datosConfiguracion[57]),
				'informacionGlobal_periodicidad'   			=> base64_decode($datosConfiguracion[58]),
				'informacionGlobal_meses'   				=> base64_decode($datosConfiguracion[59]),
				'informacionGlobal_año'   					=> base64_decode($datosConfiguracion[60]),
				'notificar_error_cfdi_automatico'   		=> base64_decode($datosConfiguracion[61]),
				'emailNotificacionErrorModuloClientes'   	=> base64_decode($datosConfiguracion[62]),
				'emailNotificacionErrorAutomatico'   		=> base64_decode($datosConfiguracion[63])
			);
		}
		
		static function obtenerConfiguracion()
		{
			$rvcfdi_serie		   						= '';
			$rvcfdi_estado_orden   						= '';
			$rvcfdi_titulo   							= '';
			$rvcfdi_descripcion   						= '';
			$rvcfdi_color_fondo_encabezado   			= '';
			$rvcfdi_color_texto_encabezado   			= '';
			$rvcfdi_color_fondo_formulario   			= '';
			$rvcfdi_color_texto_formulario   			= '';
			$rvcfdi_color_texto_controles_formulario 	= '';
			$rvcfdi_color_boton   						= '';
			$rvcfdi_color_texto_boton   				= '';
			$rvcfdi_estado_orden_refacturacion			= '';
			$rvcfdi_version_cfdi						= '';
			$rvcfdi_metodo_pago							= '';
			$rvcfdi_metodo_pago_seleccionar				= '';
			$rvcfdi_metodo_pago33						= '';
			$rvcfdi_metodo_pago_seleccionar33			= '';
			$rvcfdi_idioma								= '';
			$rvcfdi_uso_cfdi							= '';
			$rvcfdi_uso_cfdi_seleccionar				= '';
			$rvcfdi_clave_servicio						= '';
			$rvcfdi_clave_unidad						= '';
			$rvcfdi_unidad_medida						= '';
			$rvcfdi_regimen_fiscal						= '';
			$rvcfdi_clave_producto						= '';
			$rvcfdi_clave_confirmacion					= '';
			$rvcfdi_numero_pedimento					= '';
			$rvcfdi_moneda								= '';
			$rvcfdi_tipo_cambio							= '';
			$rvcfdi_observacion							= '';
			$rvcfdi_pedido_mes_actual					= '';
			$rvcfdi_conceptos_especiales_envio			= '';
			$rvcfdi_precision_decimal					= '';
			$rvcfdi_manejo_impuestos_pedido				= '';
			$rvcfdi_color_boton_vistaprevia   			= '';
			$rvcfdi_color_texto_boton_vistaprevia   	= '';
			$rvcfdi_color_boton_generarcfdi  			= '';
			$rvcfdi_color_texto_boton_generarcfdi   	= '';
			$rvcfdi_clave_servicio_shipping				= '';
			$rvcfdi_clave_unidad_shipping				= '';
			$rvcfdi_unidad_medida_shipping				= '';
			$rvcfdi_clave_producto_shipping				= '';
			$rvcfdi_numero_pedimento_shipping			= '';
			$rvcfdi_config_principal_shipping			= '';
			$rvcfdi_huso_horario						= '';
			$rvcfdi_domicilio_receptor					= '';
			$rvcfdi_mostrarMensajeErrorCliente			= '';
			$rvcfdi_mensajeErrorCliente			        = '';
			$rvcfdi_complementoCFDI				        = '';
			$rvcfdi_complementoCFDI_iedu_configuracion_nivel = '';
			$rvcfdi_complementoCFDI_iedu_configuracion_autRVOE = '';
			$rvcfdi_manejo_impuestos_pedido_facturaGlobal = '';
			$rvcfdi_manejo_impuestos_pedido_facturaGlobal_texto = '';
			$rvcfdi_exportacion_cfdi= '';
			$rvcfdi_facAtrAdquirente = '';
			$rvcfdi_objeto_imp_producto = '';
			$rvcfdi_objeto_imp_shipping = '';
			$rvcfdi_estado_orden_cfdi_automatico = '';
			$rvcfdi_notificar_error_cfdi_automatico = '';
			$rvcfdi_informacionGlobal_periodicidad = '';
			$rvcfdi_informacionGlobal_meses = '';
			$rvcfdi_informacionGlobal_año = '';
			$rvcfdi_emailNotificacionErrorModuloClientes = '';
			$rvcfdi_emailNotificacionErrorAutomatico = '';
			
			if(get_option('rvcfdi_serie') !== false)
				$rvcfdi_serie = get_option('rvcfdi_serie');
			else
				add_option('rvcfdi_serie', '');
			
			if(get_option('rvcfdi_estado_orden') !== false)
				$rvcfdi_estado_orden = get_option('rvcfdi_estado_orden');
			else
				add_option('rvcfdi_estado_orden', '');
			
			if(get_option('rvcfdi_titulo') !== false)
				$rvcfdi_titulo = get_option('rvcfdi_titulo');
			else
				add_option('rvcfdi_titulo', '');
			
			if(get_option('rvcfdi_descripcion') !== false)
				$rvcfdi_descripcion = get_option('rvcfdi_descripcion');
			else
				add_option('rvcfdi_descripcion', '');
			
			if(get_option('rvcfdi_color_fondo_encabezado') !== false)
				$rvcfdi_color_fondo_encabezado = get_option('rvcfdi_color_fondo_encabezado');
			else
				add_option('rvcfdi_color_fondo_encabezado', '');
			
			if(get_option('rvcfdi_color_texto_encabezado') !== false)
				$rvcfdi_color_texto_encabezado = get_option('rvcfdi_color_texto_encabezado');
			else
				add_option('rvcfdi_color_texto_encabezado', '');
			
			if(get_option('rvcfdi_color_fondo_formulario') !== false)
				$rvcfdi_color_fondo_formulario = get_option('rvcfdi_color_fondo_formulario');
			else
				add_option('rvcfdi_color_fondo_formulario', '');
			
			if(get_option('rvcfdi_color_texto_formulario') !== false)
				$rvcfdi_color_texto_formulario = get_option('rvcfdi_color_texto_formulario');
			else
				add_option('rvcfdi_color_texto_formulario', '');
			
			if(get_option('rvcfdi_color_texto_controles_formulario') !== false)
				$rvcfdi_color_texto_controles_formulario = get_option('rvcfdi_color_texto_controles_formulario');
			else
				add_option('rvcfdi_color_texto_controles_formulario', '');
			
			if(get_option('rvcfdi_color_boton') !== false)
				$rvcfdi_color_boton = get_option('rvcfdi_color_boton');
			else
				add_option('rvcfdi_color_boton', '');
			
			if(get_option('rvcfdi_color_texto_boton') !== false)
				$rvcfdi_color_texto_boton = get_option('rvcfdi_color_texto_boton');
			else
				add_option('rvcfdi_color_texto_boton', '');
			
			if(get_option('rvcfdi_estado_orden_refacturacion') !== false)
				$rvcfdi_estado_orden_refacturacion = get_option('rvcfdi_estado_orden_refacturacion');
			else
				add_option('rvcfdi_estado_orden_refacturacion', '');
			
			if(get_option('rvcfdi_version_cfdi') !== false)
				$rvcfdi_version_cfdi = get_option('rvcfdi_version_cfdi');
			else
				add_option('rvcfdi_version_cfdi', '');
			
			if(get_option('rvcfdi_metodo_pago') !== false)
				$rvcfdi_metodo_pago = get_option('rvcfdi_metodo_pago');
			else
				add_option('rvcfdi_metodo_pago', '');
			
			if(get_option('rvcfdi_metodo_pago_seleccionar') !== false)
				$rvcfdi_metodo_pago_seleccionar = get_option('rvcfdi_metodo_pago_seleccionar');
			else
				add_option('rvcfdi_metodo_pago_seleccionar', '');
			
			if(get_option('rvcfdi_metodo_pago33') !== false)
				$rvcfdi_metodo_pago33 = get_option('rvcfdi_metodo_pago33');
			else
				add_option('rvcfdi_metodo_pago33', '');
			
			if(get_option('rvcfdi_metodo_pago_seleccionar33') !== false)
				$rvcfdi_metodo_pago_seleccionar33 = get_option('rvcfdi_metodo_pago_seleccionar33');
			else
				add_option('rvcfdi_metodo_pago_seleccionar33', '');
			
			if(get_option('rvcfdi_idioma') !== false)
				$rvcfdi_idioma = get_option('rvcfdi_idioma');
			else
				add_option('rvcfdi_idioma', '');
			
			if(get_option('rvcfdi_uso_cfdi') !== false)
				$rvcfdi_uso_cfdi = get_option('rvcfdi_uso_cfdi');
			else
				add_option('rvcfdi_uso_cfdi', '');
			
			if(get_option('rvcfdi_uso_cfdi_seleccionar') !== false)
				$rvcfdi_uso_cfdi_seleccionar = get_option('rvcfdi_uso_cfdi_seleccionar');
			else
				add_option('rvcfdi_uso_cfdi_seleccionar', '');
			
			if(get_option('rvcfdi_clave_servicio') !== false)
				$rvcfdi_clave_servicio = get_option('rvcfdi_clave_servicio');
			else
				add_option('rvcfdi_clave_servicio', '');
			
			if(get_option('rvcfdi_clave_unidad') !== false)
				$rvcfdi_clave_unidad = get_option('rvcfdi_clave_unidad');
			else
				add_option('rvcfdi_clave_unidad', '');
			
			if(get_option('rvcfdi_unidad_medida') !== false)
				$rvcfdi_unidad_medida = get_option('rvcfdi_unidad_medida');
			else
				add_option('rvcfdi_unidad_medida', '');
			
			if(get_option('rvcfdi_regimen_fiscal') !== false)
				$rvcfdi_regimen_fiscal = get_option('rvcfdi_regimen_fiscal');
			else
				add_option('rvcfdi_regimen_fiscal', '');
			
			if(get_option('rvcfdi_clave_producto') !== false)
				$rvcfdi_clave_producto = get_option('rvcfdi_clave_producto');
			else
				add_option('rvcfdi_clave_producto', '');
			
			if(get_option('rvcfdi_clave_confirmacion') !== false)
				$rvcfdi_clave_confirmacion = get_option('rvcfdi_clave_confirmacion');
			else
				add_option('rvcfdi_clave_confirmacion', '');
			
			if(get_option('rvcfdi_numero_pedimento') !== false)
				$rvcfdi_numero_pedimento = get_option('rvcfdi_numero_pedimento');
			else
				add_option('rvcfdi_numero_pedimento', '');
			
			if(get_option('rvcfdi_moneda') !== false)
				$rvcfdi_moneda = get_option('rvcfdi_moneda');
			else
				add_option('rvcfdi_moneda', '');
			
			if(get_option('rvcfdi_tipo_cambio') !== false)
				$rvcfdi_tipo_cambio = get_option('rvcfdi_tipo_cambio');
			else
				add_option('rvcfdi_tipo_cambio', '');
			
			if(get_option('rvcfdi_observacion') !== false)
				$rvcfdi_observacion = get_option('rvcfdi_observacion');
			else
				add_option('rvcfdi_observacion', '');
			
			if(get_option('rvcfdi_pedido_mes_actual') !== false)
				$rvcfdi_pedido_mes_actual = get_option('rvcfdi_pedido_mes_actual');
			else
				add_option('rvcfdi_pedido_mes_actual', '');
			
			if(get_option('rvcfdi_conceptos_especiales_envio') !== false)
				$rvcfdi_conceptos_especiales_envio = get_option('rvcfdi_conceptos_especiales_envio');
			else
				add_option('rvcfdi_conceptos_especiales_envio', '');
			
			if(get_option('rvcfdi_precision_decimal') !== false)
				$rvcfdi_precision_decimal = get_option('rvcfdi_precision_decimal');
			else
				add_option('rvcfdi_precision_decimal', '');
			
			if(get_option('rvcfdi_manejo_impuestos_pedido') !== false)
				$rvcfdi_manejo_impuestos_pedido = get_option('rvcfdi_manejo_impuestos_pedido');
			else
				add_option('rvcfdi_manejo_impuestos_pedido', '');
			
			if(get_option('rvcfdi_color_boton_vistaprevia') !== false)
				$rvcfdi_color_boton_vistaprevia = get_option('rvcfdi_color_boton_vistaprevia');
			else
				add_option('rvcfdi_color_boton_vistaprevia', '');
			
			if(get_option('rvcfdi_color_texto_boton_vistaprevia') !== false)
				$rvcfdi_color_texto_boton_vistaprevia = get_option('rvcfdi_color_texto_boton_vistaprevia');
			else
				add_option('rvcfdi_color_texto_boton_vistaprevia', '');
			
			if(get_option('rvcfdi_color_boton_generarcfdi') !== false)
				$rvcfdi_color_boton_generarcfdi = get_option('rvcfdi_color_boton_generarcfdi');
			else
				add_option('rvcfdi_color_boton_generarcfdi', '');
			
			if(get_option('rvcfdi_color_texto_boton_generarcfdi') !== false)
				$rvcfdi_color_texto_boton_generarcfdi = get_option('rvcfdi_color_texto_boton_generarcfdi');
			else
				add_option('rvcfdi_color_texto_boton_generarcfdi', '');
			
			if(get_option('rvcfdi_clave_servicio_shipping') !== false)
				$rvcfdi_clave_servicio_shipping = get_option('rvcfdi_clave_servicio_shipping');
			else
				add_option('rvcfdi_clave_servicio_shipping', '');
			
			if(get_option('rvcfdi_clave_unidad_shipping') !== false)
				$rvcfdi_clave_unidad_shipping = get_option('rvcfdi_clave_unidad_shipping');
			else
				add_option('rvcfdi_clave_unidad_shipping', '');
			
			if(get_option('rvcfdi_unidad_medida_shipping') !== false)
				$rvcfdi_unidad_medida_shipping = get_option('rvcfdi_unidad_medida_shipping');
			else
				add_option('rvcfdi_unidad_medida_shipping', '');
			
			if(get_option('rvcfdi_clave_producto_shipping') !== false)
				$rvcfdi_clave_producto_shipping = get_option('rvcfdi_clave_producto_shipping');
			else
				add_option('rvcfdi_clave_producto_shipping', '');
			
			if(get_option('rvcfdi_numero_pedimento_shipping') !== false)
				$rvcfdi_numero_pedimento_shipping = get_option('rvcfdi_numero_pedimento_shipping');
			else
				add_option('rvcfdi_numero_pedimento_shipping', '');
			
			if(get_option('rvcfdi_config_principal_shipping') !== false)
				$rvcfdi_config_principal_shipping = get_option('rvcfdi_config_principal_shipping');
			else
				add_option('rvcfdi_config_principal_shipping', '');
			
			if(get_option('rvcfdi_huso_horario') !== false)
				$rvcfdi_huso_horario = get_option('rvcfdi_huso_horario');
			else
				add_option('rvcfdi_huso_horario', '');
			
			if(get_option('rvcfdi_domicilio_receptor') !== false)
				$rvcfdi_domicilio_receptor = get_option('rvcfdi_domicilio_receptor');
			else
				add_option('rvcfdi_domicilio_receptor', '');
			
			if(get_option('rvcfdi_mostrarMensajeErrorCliente') !== false)
				$rvcfdi_mostrarMensajeErrorCliente = get_option('rvcfdi_mostrarMensajeErrorCliente');
			else
				add_option('rvcfdi_mostrarMensajeErrorCliente', '');
			
			if(get_option('rvcfdi_mensajeErrorCliente') !== false)
				$rvcfdi_mensajeErrorCliente = get_option('rvcfdi_mensajeErrorCliente');
			else
				add_option('rvcfdi_mensajeErrorCliente', '');
			
			if(get_option('rvcfdi_complementoCFDI') !== false)
				$rvcfdi_complementoCFDI = get_option('rvcfdi_complementoCFDI');
			else
				add_option('rvcfdi_complementoCFDI', '');
			
			if(get_option('rvcfdi_complementoCFDI_iedu_configuracion_nivel') !== false)
				$rvcfdi_complementoCFDI_iedu_configuracion_nivel = get_option('rvcfdi_complementoCFDI_iedu_configuracion_nivel');
			else
				add_option('rvcfdi_complementoCFDI_iedu_configuracion_nivel', '');
			
			if(get_option('rvcfdi_complementoCFDI_iedu_configuracion_autRVOE') !== false)
				$rvcfdi_complementoCFDI_iedu_configuracion_autRVOE = get_option('rvcfdi_complementoCFDI_iedu_configuracion_autRVOE');
			else
				add_option('rvcfdi_complementoCFDI_iedu_configuracion_autRVOE', '');
			
			if(get_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal') !== false)
				$rvcfdi_manejo_impuestos_pedido_facturaGlobal = get_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal');
			else
				add_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal', '');
			
			if(get_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal_texto') !== false)
				$rvcfdi_manejo_impuestos_pedido_facturaGlobal_texto = get_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal_texto');
			else
				add_option('rvcfdi_manejo_impuestos_pedido_facturaGlobal_texto', '');
			
			if(get_option('rvcfdi_exportacion_cfdi') !== false)
				$rvcfdi_exportacion_cfdi = get_option('rvcfdi_exportacion_cfdi');
			else
				add_option('rvcfdi_exportacion_cfdi', '');
			
			if(get_option('rvcfdi_facAtrAdquirente') !== false)
				$rvcfdi_facAtrAdquirente = get_option('rvcfdi_facAtrAdquirente');
			else
				add_option('rvcfdi_facAtrAdquirente', '');
			
			if(get_option('rvcfdi_objeto_imp_producto') !== false)
				$rvcfdi_objeto_imp_producto = get_option('rvcfdi_objeto_imp_producto');
			else
				add_option('rvcfdi_objeto_imp_producto', '');
			
			if(get_option('rvcfdi_objeto_imp_shipping') !== false)
				$rvcfdi_objeto_imp_shipping = get_option('rvcfdi_objeto_imp_shipping');
			else
				add_option('rvcfdi_objeto_imp_shipping', '');
			
			if(get_option('rvcfdi_estado_orden_cfdi_automatico') !== false)
				$rvcfdi_estado_orden_cfdi_automatico = get_option('rvcfdi_estado_orden_cfdi_automatico');
			else
				add_option('rvcfdi_estado_orden_cfdi_automatico', '');
			
			if(get_option('rvcfdi_notificar_error_cfdi_automatico') !== false)
				$rvcfdi_notificar_error_cfdi_automatico = get_option('rvcfdi_notificar_error_cfdi_automatico');
			else
				add_option('rvcfdi_notificar_error_cfdi_automatico', '');
			
			if(get_option('rvcfdi_informacionGlobal_periodicidad') !== false)
				$rvcfdi_informacionGlobal_periodicidad = get_option('rvcfdi_informacionGlobal_periodicidad');
			else
				add_option('rvcfdi_informacionGlobal_periodicidad', '');
			
			if(get_option('rvcfdi_informacionGlobal_meses') !== false)
				$rvcfdi_informacionGlobal_meses = get_option('rvcfdi_informacionGlobal_meses');
			else
				add_option('rvcfdi_informacionGlobal_meses', '');
			
			if(get_option('rvcfdi_informacionGlobal_año') !== false)
				$rvcfdi_informacionGlobal_año = get_option('rvcfdi_informacionGlobal_año');
			else
				add_option('rvcfdi_informacionGlobal_año', '');
			
			if(get_option('rvcfdi_emailNotificacionErrorModuloClientes') !== false)
				$rvcfdi_emailNotificacionErrorModuloClientes = get_option('rvcfdi_emailNotificacionErrorModuloClientes');
			else
				add_option('rvcfdi_emailNotificacionErrorModuloClientes', '');
			
			if(get_option('rvcfdi_emailNotificacionErrorAutomatico') !== false)
				$rvcfdi_emailNotificacionErrorAutomatico = get_option('rvcfdi_emailNotificacionErrorAutomatico');
			else
				add_option('rvcfdi_emailNotificacionErrorAutomatico', '');
			
			$datosConfiguracion = array
			(
				$rvcfdi_serie,
				$rvcfdi_estado_orden,
				$rvcfdi_titulo,
				$rvcfdi_descripcion,
				$rvcfdi_color_fondo_encabezado,
				$rvcfdi_color_texto_encabezado,
				$rvcfdi_color_fondo_formulario,
				$rvcfdi_color_texto_formulario,
				$rvcfdi_color_texto_controles_formulario,
				$rvcfdi_color_boton,
				$rvcfdi_color_texto_boton,
				$rvcfdi_estado_orden_refacturacion,
				$rvcfdi_version_cfdi,
				$rvcfdi_metodo_pago,
				$rvcfdi_metodo_pago_seleccionar,
				$rvcfdi_idioma,
				$rvcfdi_uso_cfdi,
				$rvcfdi_uso_cfdi_seleccionar,
				$rvcfdi_clave_servicio,
				$rvcfdi_clave_unidad,
				$rvcfdi_unidad_medida,
				$rvcfdi_regimen_fiscal,
				$rvcfdi_clave_producto,
				$rvcfdi_clave_confirmacion,
				$rvcfdi_numero_pedimento,
				$rvcfdi_moneda,
				$rvcfdi_tipo_cambio,
				$rvcfdi_observacion,
				$rvcfdi_precision_decimal,
				$rvcfdi_pedido_mes_actual,
				$rvcfdi_metodo_pago33,
				$rvcfdi_metodo_pago_seleccionar33,
				$rvcfdi_conceptos_especiales_envio,
				$rvcfdi_manejo_impuestos_pedido,
				$rvcfdi_color_boton_vistaprevia,
				$rvcfdi_color_texto_boton_vistaprevia,
				$rvcfdi_color_boton_generarcfdi,
				$rvcfdi_color_texto_boton_generarcfdi,
				$rvcfdi_clave_servicio_shipping,
				$rvcfdi_clave_unidad_shipping,
				$rvcfdi_unidad_medida_shipping,
				$rvcfdi_clave_producto_shipping,
				$rvcfdi_numero_pedimento_shipping,
				$rvcfdi_config_principal_shipping,
				$rvcfdi_huso_horario,
				$rvcfdi_domicilio_receptor,
				$rvcfdi_mostrarMensajeErrorCliente,
				$rvcfdi_mensajeErrorCliente,
				$rvcfdi_complementoCFDI,
				$rvcfdi_complementoCFDI_iedu_configuracion_nivel, 
				$rvcfdi_complementoCFDI_iedu_configuracion_autRVOE,
				$rvcfdi_manejo_impuestos_pedido_facturaGlobal,
				$rvcfdi_manejo_impuestos_pedido_facturaGlobal_texto,
				$rvcfdi_exportacion_cfdi,
				$rvcfdi_facAtrAdquirente,
				$rvcfdi_objeto_imp_producto,
				$rvcfdi_objeto_imp_shipping,
				$rvcfdi_estado_orden_cfdi_automatico,
				$rvcfdi_informacionGlobal_periodicidad,
				$rvcfdi_informacionGlobal_meses,
				$rvcfdi_informacionGlobal_año,
				$rvcfdi_notificar_error_cfdi_automatico,
				$rvcfdi_emailNotificacionErrorModuloClientes,
				$rvcfdi_emailNotificacionErrorAutomatico
			);
			
			return $datosConfiguracion;
		}
	}
?>