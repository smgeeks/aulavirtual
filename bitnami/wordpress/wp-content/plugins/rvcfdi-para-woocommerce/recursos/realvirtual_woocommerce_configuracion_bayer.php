<?php
	class RealVirtualWooCommerceConfiguracionBayer
	{
		static $rvcfdi_bayer_facturacion_c_clase_documento		   			= '';
		static $rvcfdi_bayer_facturacion_c_sociedad   						= '';
		static $rvcfdi_bayer_facturacion_c_moneda   						= '';
		static $rvcfdi_bayer_facturacion_c_tc_cab_doc   					= '';
		static $rvcfdi_bayer_facturacion_p_cuenta   						= '';
		static $rvcfdi_bayer_facturacion_p_division   						= '';
		static $rvcfdi_bayer_facturacion_p_ce_be   							= '';
		static $rvcfdi_bayer_facturacion_p_texto   							= '';
		static $rvcfdi_bayer_facturacion_p_pais_destinatario 				= '';
		static $rvcfdi_bayer_facturacion_p_linea_de_producto   				= '';
		static $rvcfdi_bayer_facturacion_p_grupo_de_producto   				= '';
		static $rvcfdi_bayer_facturacion_p_centro							= '';
		static $rvcfdi_bayer_facturacion_p_cliente							= '';
		static $rvcfdi_bayer_facturacion_p_organiz_ventas					= '';
		static $rvcfdi_bayer_facturacion_p_canal_distrib					= '';
		static $rvcfdi_bayer_facturacion_p_zoha_de_ventas					= '';
		static $rvcfdi_bayer_facturacion_p_oficina_ventas					= '';
		static $rvcfdi_bayer_facturacion_p_ramo								= '';
		static $rvcfdi_bayer_facturacion_p_grupo							= '';
		static $rvcfdi_bayer_facturacion_p_gr_vendedores					= '';
		static $rvcfdi_bayer_facturacion_p_atributo_1_sector				= '';
		static $rvcfdi_bayer_facturacion_p_atributo_2_sector				= '';
		static $rvcfdi_bayer_facturacion_p_clase_factura					= '';
		static $rvcfdi_bayer_financiero_c_clase_de_documento				= '';
		static $rvcfdi_bayer_financiero_c_sociedad							= '';
		static $rvcfdi_bayer_financiero_c_moneda							= '';
		static $rvcfdi_bayer_financiero_c_t_xt_cab_doc						= '';
		static $rvcfdi_bayer_financiero_c_cuenta_bancaria					= '';
		static $rvcfdi_bayer_financiero_c_texto								= '';
		static $rvcfdi_bayer_financiero_c_division							= '';
		static $rvcfdi_bayer_financiero_c_cebe								= '';
		static $rvcfdi_bayer_financiero_c_cliente							= '';
		static $rvcfdi_bayer_financiero_p_cuenta							= '';
		static $rvcfdi_bayer_financiero_p_ind_impuestos						= '';
		static $rvcfdi_bayer_financiero_p_division   						= '';
		static $rvcfdi_bayer_financiero_p_texto   							= '';
		static $rvcfdi_bayer_financiero_p_cebe  							= '';
		static $rvcfdi_bayer_financiero_p_pais_destinatario   				= '';
		static $rvcfdi_bayer_financiero_p_linea_de_producto					= '';
		static $rvcfdi_bayer_financiero_p_grupo_de_proudcto					= '';
		static $rvcfdi_bayer_financiero_p_centro							= '';
		static $rvcfdi_bayer_financiero_p_articulo							= '';
		static $rvcfdi_bayer_financiero_p_zona_de_ventas					= '';
		static $rvcfdi_bayer_financiero_p_material							= '';
		static $rvcfdi_bayer_financiero_p_atributo_2_sector					= '';
		
		static function guardarConfiguracion($configuracion, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_bayer_facturacion_c_clase_documento', base64_encode($configuracion['rvcfdi_bayer_facturacion_c_clase_documento']));
			update_option('rvcfdi_bayer_facturacion_c_sociedad', base64_encode($configuracion['rvcfdi_bayer_facturacion_c_sociedad']));
			update_option('rvcfdi_bayer_facturacion_c_moneda', base64_encode($configuracion['rvcfdi_bayer_facturacion_c_moneda']));
			update_option('rvcfdi_bayer_facturacion_c_tc_cab_doc', base64_encode($configuracion['rvcfdi_bayer_facturacion_c_tc_cab_doc']));
			update_option('rvcfdi_bayer_facturacion_p_cuenta', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_cuenta']));
			update_option('rvcfdi_bayer_facturacion_p_division', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_division']));
			update_option('rvcfdi_bayer_facturacion_p_ce_be', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_ce_be']));
			update_option('rvcfdi_bayer_facturacion_p_texto', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_texto']));
			update_option('rvcfdi_bayer_facturacion_p_pais_destinatario', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_pais_destinatario']));
			update_option('rvcfdi_bayer_facturacion_p_linea_de_producto', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_linea_de_producto']));
			update_option('rvcfdi_bayer_facturacion_p_grupo_de_producto', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_grupo_de_producto']));
			update_option('rvcfdi_bayer_facturacion_p_centro', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_centro']));
			update_option('rvcfdi_bayer_facturacion_p_cliente', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_cliente']));
			update_option('rvcfdi_bayer_facturacion_p_organiz_ventas', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_organiz_ventas']));
			update_option('rvcfdi_bayer_facturacion_p_canal_distrib', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_canal_distrib']));
			update_option('rvcfdi_bayer_facturacion_p_zoha_de_ventas', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_zoha_de_ventas']));
			update_option('rvcfdi_bayer_facturacion_p_oficina_ventas', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_oficina_ventas']));
			update_option('rvcfdi_bayer_facturacion_p_ramo', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_ramo']));
			update_option('rvcfdi_bayer_facturacion_p_grupo', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_grupo']));
			update_option('rvcfdi_bayer_facturacion_p_gr_vendedores', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_gr_vendedores']));
			update_option('rvcfdi_bayer_facturacion_p_atributo_1_sector', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_atributo_1_sector']));
			update_option('rvcfdi_bayer_facturacion_p_atributo_2_sector', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_atributo_2_sector']));
			update_option('rvcfdi_bayer_facturacion_p_clase_factura', base64_encode($configuracion['rvcfdi_bayer_facturacion_p_clase_factura']));
			update_option('rvcfdi_bayer_financiero_c_clase_de_documento', base64_encode($configuracion['rvcfdi_bayer_financiero_c_clase_de_documento']));
			update_option('rvcfdi_bayer_financiero_c_sociedad', base64_encode($configuracion['rvcfdi_bayer_financiero_c_sociedad']));
			update_option('rvcfdi_bayer_financiero_c_moneda', base64_encode($configuracion['rvcfdi_bayer_financiero_c_moneda']));
			update_option('rvcfdi_bayer_financiero_c_t_xt_cab_doc', base64_encode($configuracion['rvcfdi_bayer_financiero_c_t_xt_cab_doc']));
			update_option('rvcfdi_bayer_financiero_c_cuenta_bancaria', base64_encode($configuracion['rvcfdi_bayer_financiero_c_cuenta_bancaria']));
			update_option('rvcfdi_bayer_financiero_c_texto', base64_encode($configuracion['rvcfdi_bayer_financiero_c_texto']));
			update_option('rvcfdi_bayer_financiero_c_division', base64_encode($configuracion['rvcfdi_bayer_financiero_c_division']));
			update_option('rvcfdi_bayer_financiero_c_cebe', base64_encode($configuracion['rvcfdi_bayer_financiero_c_cebe']));
			update_option('rvcfdi_bayer_financiero_c_cliente', base64_encode($configuracion['rvcfdi_bayer_financiero_c_cliente']));
			update_option('rvcfdi_bayer_financiero_p_cuenta', base64_encode($configuracion['rvcfdi_bayer_financiero_p_cuenta']));
			update_option('rvcfdi_bayer_financiero_p_ind_impuestos', base64_encode($configuracion['rvcfdi_bayer_financiero_p_ind_impuestos']));
			update_option('rvcfdi_bayer_financiero_p_division', base64_encode($configuracion['rvcfdi_bayer_financiero_p_division']));
			update_option('rvcfdi_bayer_financiero_p_texto', base64_encode($configuracion['rvcfdi_bayer_financiero_p_texto']));
			update_option('rvcfdi_bayer_financiero_p_cebe', base64_encode($configuracion['rvcfdi_bayer_financiero_p_cebe']));
			update_option('rvcfdi_bayer_financiero_p_pais_destinatario', base64_encode($configuracion['rvcfdi_bayer_financiero_p_pais_destinatario']));
			update_option('rvcfdi_bayer_financiero_p_linea_de_producto', base64_encode($configuracion['rvcfdi_bayer_financiero_p_linea_de_producto']));
			update_option('rvcfdi_bayer_financiero_p_grupo_de_proudcto', base64_encode($configuracion['rvcfdi_bayer_financiero_p_grupo_de_proudcto']));
			update_option('rvcfdi_bayer_financiero_p_centro', base64_encode($configuracion['rvcfdi_bayer_financiero_p_centro']));
			update_option('rvcfdi_bayer_financiero_p_articulo', base64_encode($configuracion['rvcfdi_bayer_financiero_p_articulo']));
			update_option('rvcfdi_bayer_financiero_p_zona_de_ventas', base64_encode($configuracion['rvcfdi_bayer_financiero_p_zona_de_ventas']));
			update_option('rvcfdi_bayer_financiero_p_material', base64_encode($configuracion['rvcfdi_bayer_financiero_p_material']));
			update_option('rvcfdi_bayer_financiero_p_atributo_2_sector', base64_encode($configuracion['rvcfdi_bayer_financiero_p_atributo_2_sector']));
			
			$body = json_encode(array("success" => true, "message" => ""));
			return json_decode($body);
		}
		
		static function configuracionEntidad()
		{
			$datosConfiguracion = self::obtenerConfiguracion();

			return array
			(
				'rvcfdi_bayer_facturacion_c_clase_documento'     		=> base64_decode($datosConfiguracion[0]),
				'rvcfdi_bayer_facturacion_c_sociedad' 					=> base64_decode($datosConfiguracion[1]),
				'rvcfdi_bayer_facturacion_c_moneda'   	 				=> base64_decode($datosConfiguracion[2]),
				'rvcfdi_bayer_facturacion_c_tc_cab_doc'   				=> base64_decode($datosConfiguracion[3]),
				'rvcfdi_bayer_facturacion_p_cuenta'   					=> base64_decode($datosConfiguracion[4]),
				'rvcfdi_bayer_facturacion_p_division'   				=> base64_decode($datosConfiguracion[5]),
				'rvcfdi_bayer_facturacion_p_ce_be'   					=> base64_decode($datosConfiguracion[6]),
				'rvcfdi_bayer_facturacion_p_texto'   					=> base64_decode($datosConfiguracion[7]),
				'rvcfdi_bayer_facturacion_p_pais_destinatario'   		=> base64_decode($datosConfiguracion[8]),
				'rvcfdi_bayer_facturacion_p_linea_de_producto'   		=> base64_decode($datosConfiguracion[9]),
				'rvcfdi_bayer_facturacion_p_grupo_de_producto'   		=> base64_decode($datosConfiguracion[10]),
				'rvcfdi_bayer_facturacion_p_centro'   					=> base64_decode($datosConfiguracion[11]),
				'rvcfdi_bayer_facturacion_p_cliente'   					=> base64_decode($datosConfiguracion[12]),
				'rvcfdi_bayer_facturacion_p_organiz_ventas'   			=> base64_decode($datosConfiguracion[13]),
				'rvcfdi_bayer_facturacion_p_canal_distrib'   			=> base64_decode($datosConfiguracion[14]),
				'rvcfdi_bayer_facturacion_p_zoha_de_ventas'   			=> base64_decode($datosConfiguracion[15]),
				'rvcfdi_bayer_facturacion_p_oficina_ventas'   			=> base64_decode($datosConfiguracion[16]),
				'rvcfdi_bayer_facturacion_p_ramo'   					=> base64_decode($datosConfiguracion[17]),
				'rvcfdi_bayer_facturacion_p_grupo'   					=> base64_decode($datosConfiguracion[18]),
				'rvcfdi_bayer_facturacion_p_gr_vendedores'   			=> base64_decode($datosConfiguracion[19]),
				'rvcfdi_bayer_facturacion_p_atributo_1_sector'   		=> base64_decode($datosConfiguracion[20]),
				'rvcfdi_bayer_facturacion_p_atributo_2_sector'   		=> base64_decode($datosConfiguracion[21]),
				'rvcfdi_bayer_facturacion_p_clase_factura'   			=> base64_decode($datosConfiguracion[22]),
				'rvcfdi_bayer_financiero_c_clase_de_documento'   		=> base64_decode($datosConfiguracion[23]),
				'rvcfdi_bayer_financiero_c_sociedad'   					=> base64_decode($datosConfiguracion[24]),
				'rvcfdi_bayer_financiero_c_moneda'   					=> base64_decode($datosConfiguracion[25]),
				'rvcfdi_bayer_financiero_c_t_xt_cab_doc'   				=> base64_decode($datosConfiguracion[26]),
				'rvcfdi_bayer_financiero_c_cuenta_bancaria'   			=> base64_decode($datosConfiguracion[27]),
				'rvcfdi_bayer_financiero_c_texto'   					=> base64_decode($datosConfiguracion[28]),
				'rvcfdi_bayer_financiero_c_division'   					=> base64_decode($datosConfiguracion[29]),
				'rvcfdi_bayer_financiero_c_cebe'   						=> base64_decode($datosConfiguracion[30]),
				'rvcfdi_bayer_financiero_c_cliente'   					=> base64_decode($datosConfiguracion[31]),
				'rvcfdi_bayer_financiero_p_cuenta'   					=> base64_decode($datosConfiguracion[32]),
				'rvcfdi_bayer_financiero_p_ind_impuestos'   			=> base64_decode($datosConfiguracion[33]),
				'rvcfdi_bayer_financiero_p_division'   					=> base64_decode($datosConfiguracion[34]),
				'rvcfdi_bayer_financiero_p_texto'   					=> base64_decode($datosConfiguracion[35]),
				'rvcfdi_bayer_financiero_p_cebe'   						=> base64_decode($datosConfiguracion[36]),
				'rvcfdi_bayer_financiero_p_pais_destinatario'   		=> base64_decode($datosConfiguracion[37]),
				'rvcfdi_bayer_financiero_p_linea_de_producto'   		=> base64_decode($datosConfiguracion[38]),
				'rvcfdi_bayer_financiero_p_grupo_de_proudcto'   		=> base64_decode($datosConfiguracion[39]),
				'rvcfdi_bayer_financiero_p_centro'   					=> base64_decode($datosConfiguracion[40]),
				'rvcfdi_bayer_financiero_p_articulo'   					=> base64_decode($datosConfiguracion[41]),
				'rvcfdi_bayer_financiero_p_zona_de_ventas'   			=> base64_decode($datosConfiguracion[42]),
				'rvcfdi_bayer_financiero_p_material'   					=> base64_decode($datosConfiguracion[43]),
				'rvcfdi_bayer_financiero_p_atributo_2_sector'   		=> base64_decode($datosConfiguracion[44])
			);
		}
		
		static function obtenerConfiguracion()
		{
			$rvcfdi_bayer_facturacion_c_clase_documento		   			= '';
			$rvcfdi_bayer_facturacion_c_sociedad   						= '';
			$rvcfdi_bayer_facturacion_c_moneda   						= '';
			$rvcfdi_bayer_facturacion_c_tc_cab_doc   					= '';
			$rvcfdi_bayer_facturacion_p_cuenta   						= '';
			$rvcfdi_bayer_facturacion_p_division   						= '';
			$rvcfdi_bayer_facturacion_p_ce_be   						= '';
			$rvcfdi_bayer_facturacion_p_texto   						= '';
			$rvcfdi_bayer_facturacion_p_pais_destinatario 				= '';
			$rvcfdi_bayer_facturacion_p_linea_de_producto   			= '';
			$rvcfdi_bayer_facturacion_p_grupo_de_producto   			= '';
			$rvcfdi_bayer_facturacion_p_centro							= '';
			$rvcfdi_bayer_facturacion_p_cliente							= '';
			$rvcfdi_bayer_facturacion_p_organiz_ventas					= '';
			$rvcfdi_bayer_facturacion_p_canal_distrib					= '';
			$rvcfdi_bayer_facturacion_p_zoha_de_ventas					= '';
			$rvcfdi_bayer_facturacion_p_oficina_ventas					= '';
			$rvcfdi_bayer_facturacion_p_ramo							= '';
			$rvcfdi_bayer_facturacion_p_grupo							= '';
			$rvcfdi_bayer_facturacion_p_gr_vendedores					= '';
			$rvcfdi_bayer_facturacion_p_atributo_1_sector				= '';
			$rvcfdi_bayer_facturacion_p_atributo_2_sector				= '';
			$rvcfdi_bayer_facturacion_p_clase_factura					= '';
			$rvcfdi_bayer_financiero_c_clase_de_documento				= '';
			$rvcfdi_bayer_financiero_c_sociedad							= '';
			$rvcfdi_bayer_financiero_c_moneda							= '';
			$rvcfdi_bayer_financiero_c_t_xt_cab_doc						= '';
			$rvcfdi_bayer_financiero_c_cuenta_bancaria					= '';
			$rvcfdi_bayer_financiero_c_texto							= '';
			$rvcfdi_bayer_financiero_c_division							= '';
			$rvcfdi_bayer_financiero_c_cebe								= '';
			$rvcfdi_bayer_financiero_c_cliente							= '';
			$rvcfdi_bayer_financiero_p_cuenta							= '';
			$rvcfdi_bayer_financiero_p_ind_impuestos					= '';
			$rvcfdi_bayer_financiero_p_division   						= '';
			$rvcfdi_bayer_financiero_p_texto   							= '';
			$rvcfdi_bayer_financiero_p_cebe  							= '';
			$rvcfdi_bayer_financiero_p_pais_destinatario   				= '';
			$rvcfdi_bayer_financiero_p_linea_de_producto				= '';
			$rvcfdi_bayer_financiero_p_grupo_de_proudcto				= '';
			$rvcfdi_bayer_financiero_p_centro							= '';
			$rvcfdi_bayer_financiero_p_articulo							= '';
			$rvcfdi_bayer_financiero_p_zona_de_ventas					= '';
			$rvcfdi_bayer_financiero_p_material							= '';
			$rvcfdi_bayer_financiero_p_atributo_2_sector				= '';
			
			if(get_option('rvcfdi_bayer_facturacion_c_clase_documento') !== false)
				$rvcfdi_bayer_facturacion_c_clase_documento = get_option('rvcfdi_bayer_facturacion_c_clase_documento');
			else
				add_option('rvcfdi_bayer_facturacion_c_clase_documento', '');
			
			if(get_option('rvcfdi_bayer_facturacion_c_sociedad') !== false)
				$rvcfdi_bayer_facturacion_c_sociedad = get_option('rvcfdi_bayer_facturacion_c_sociedad');
			else
				add_option('rvcfdi_bayer_facturacion_c_sociedad', '');
			
			if(get_option('rvcfdi_bayer_facturacion_c_moneda') !== false)
				$rvcfdi_bayer_facturacion_c_moneda = get_option('rvcfdi_bayer_facturacion_c_moneda');
			else
				add_option('rvcfdi_bayer_facturacion_c_moneda', '');
			
			if(get_option('rvcfdi_bayer_facturacion_c_tc_cab_doc') !== false)
				$rvcfdi_bayer_facturacion_c_tc_cab_doc = get_option('rvcfdi_bayer_facturacion_c_tc_cab_doc');
			else
				add_option('rvcfdi_bayer_facturacion_c_tc_cab_doc', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_cuenta') !== false)
				$rvcfdi_bayer_facturacion_p_cuenta = get_option('rvcfdi_bayer_facturacion_p_cuenta');
			else
				add_option('rvcfdi_bayer_facturacion_p_cuenta', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_division') !== false)
				$rvcfdi_bayer_facturacion_p_division = get_option('rvcfdi_bayer_facturacion_p_division');
			else
				add_option('rvcfdi_bayer_facturacion_p_division', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_ce_be') !== false)
				$rvcfdi_bayer_facturacion_p_ce_be = get_option('rvcfdi_bayer_facturacion_p_ce_be');
			else
				add_option('rvcfdi_bayer_facturacion_p_ce_be', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_texto') !== false)
				$rvcfdi_bayer_facturacion_p_texto = get_option('rvcfdi_bayer_facturacion_p_texto');
			else
				add_option('rvcfdi_bayer_facturacion_p_texto', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_pais_destinatario') !== false)
				$rvcfdi_bayer_facturacion_p_pais_destinatario = get_option('rvcfdi_bayer_facturacion_p_pais_destinatario');
			else
				add_option('rvcfdi_bayer_facturacion_p_pais_destinatario', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_linea_de_producto') !== false)
				$rvcfdi_bayer_facturacion_p_linea_de_producto = get_option('rvcfdi_bayer_facturacion_p_linea_de_producto');
			else
				add_option('rvcfdi_bayer_facturacion_p_linea_de_producto', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_grupo_de_producto') !== false)
				$rvcfdi_bayer_facturacion_p_grupo_de_producto = get_option('rvcfdi_bayer_facturacion_p_grupo_de_producto');
			else
				add_option('rvcfdi_bayer_facturacion_p_grupo_de_producto', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_centro') !== false)
				$rvcfdi_bayer_facturacion_p_centro = get_option('rvcfdi_bayer_facturacion_p_centro');
			else
				add_option('rvcfdi_bayer_facturacion_p_centro', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_cliente') !== false)
				$rvcfdi_bayer_facturacion_p_cliente = get_option('rvcfdi_bayer_facturacion_p_cliente');
			else
				add_option('rvcfdi_bayer_facturacion_p_cliente', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_organiz_ventas') !== false)
				$rvcfdi_bayer_facturacion_p_organiz_ventas = get_option('rvcfdi_bayer_facturacion_p_organiz_ventas');
			else
				add_option('rvcfdi_bayer_facturacion_p_organiz_ventas', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_canal_distrib') !== false)
				$rvcfdi_bayer_facturacion_p_canal_distrib = get_option('rvcfdi_bayer_facturacion_p_canal_distrib');
			else
				add_option('rvcfdi_bayer_facturacion_p_canal_distrib', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_zoha_de_ventas') !== false)
				$rvcfdi_bayer_facturacion_p_zoha_de_ventas = get_option('rvcfdi_bayer_facturacion_p_zoha_de_ventas');
			else
				add_option('rvcfdi_bayer_facturacion_p_zoha_de_ventas', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_oficina_ventas') !== false)
				$rvcfdi_bayer_facturacion_p_oficina_ventas = get_option('rvcfdi_bayer_facturacion_p_oficina_ventas');
			else
				add_option('rvcfdi_bayer_facturacion_p_oficina_ventas', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_ramo') !== false)
				$rvcfdi_bayer_facturacion_p_ramo = get_option('rvcfdi_bayer_facturacion_p_ramo');
			else
				add_option('rvcfdi_bayer_facturacion_p_ramo', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_grupo') !== false)
				$rvcfdi_bayer_facturacion_p_grupo = get_option('rvcfdi_bayer_facturacion_p_grupo');
			else
				add_option('rvcfdi_bayer_facturacion_p_grupo', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_gr_vendedores') !== false)
				$rvcfdi_bayer_facturacion_p_gr_vendedores = get_option('rvcfdi_bayer_facturacion_p_gr_vendedores');
			else
				add_option('rvcfdi_bayer_facturacion_p_gr_vendedores', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_atributo_1_sector') !== false)
				$rvcfdi_bayer_facturacion_p_atributo_1_sector = get_option('rvcfdi_bayer_facturacion_p_atributo_1_sector');
			else
				add_option('rvcfdi_bayer_facturacion_p_atributo_1_sector', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_atributo_2_sector') !== false)
				$rvcfdi_bayer_facturacion_p_atributo_2_sector = get_option('rvcfdi_bayer_facturacion_p_atributo_2_sector');
			else
				add_option('rvcfdi_bayer_facturacion_p_atributo_2_sector', '');
			
			if(get_option('rvcfdi_bayer_facturacion_p_clase_factura') !== false)
				$rvcfdi_bayer_facturacion_p_clase_factura = get_option('rvcfdi_bayer_facturacion_p_clase_factura');
			else
				add_option('rvcfdi_bayer_facturacion_p_clase_factura', '');
			
			if(get_option('rvcfdi_bayer_financiero_c_clase_de_documento') !== false)
				$rvcfdi_bayer_financiero_c_clase_de_documento = get_option('rvcfdi_bayer_financiero_c_clase_de_documento');
			else
				add_option('rvcfdi_bayer_financiero_c_clase_de_documento', '');
			
			if(get_option('rvcfdi_bayer_financiero_c_sociedad') !== false)
				$rvcfdi_bayer_financiero_c_sociedad = get_option('rvcfdi_bayer_financiero_c_sociedad');
			else
				add_option('rvcfdi_bayer_financiero_c_sociedad', '');
			
			if(get_option('rvcfdi_bayer_financiero_c_moneda') !== false)
				$rvcfdi_bayer_financiero_c_moneda = get_option('rvcfdi_bayer_financiero_c_moneda');
			else
				add_option('rvcfdi_bayer_financiero_c_moneda', '');
			
			if(get_option('rvcfdi_bayer_financiero_c_t_xt_cab_doc') !== false)
				$rvcfdi_bayer_financiero_c_t_xt_cab_doc = get_option('rvcfdi_bayer_financiero_c_t_xt_cab_doc');
			else
				add_option('rvcfdi_bayer_financiero_c_t_xt_cab_doc', '');
			
			if(get_option('rvcfdi_bayer_financiero_c_cuenta_bancaria') !== false)
				$rvcfdi_bayer_financiero_c_cuenta_bancaria = get_option('rvcfdi_bayer_financiero_c_cuenta_bancaria');
			else
				add_option('rvcfdi_bayer_financiero_c_cuenta_bancaria', '');
			
			if(get_option('rvcfdi_bayer_financiero_c_texto') !== false)
				$rvcfdi_bayer_financiero_c_texto = get_option('rvcfdi_bayer_financiero_c_texto');
			else
				add_option('rvcfdi_bayer_financiero_c_texto', '');
			
			if(get_option('rvcfdi_bayer_financiero_c_division') !== false)
				$rvcfdi_bayer_financiero_c_division = get_option('rvcfdi_bayer_financiero_c_division');
			else
				add_option('rvcfdi_bayer_financiero_c_division', '');
			
			if(get_option('rvcfdi_bayer_financiero_c_cebe') !== false)
				$rvcfdi_bayer_financiero_c_cebe = get_option('rvcfdi_bayer_financiero_c_cebe');
			else
				add_option('rvcfdi_bayer_financiero_c_cebe', '');
			
			if(get_option('rvcfdi_bayer_financiero_c_cliente') !== false)
				$rvcfdi_bayer_financiero_c_cliente = get_option('rvcfdi_bayer_financiero_c_cliente');
			else
				add_option('rvcfdi_bayer_financiero_c_cliente', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_cuenta') !== false)
				$rvcfdi_bayer_financiero_p_cuenta = get_option('rvcfdi_bayer_financiero_p_cuenta');
			else
				add_option('rvcfdi_bayer_financiero_p_cuenta', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_ind_impuestos') !== false)
				$rvcfdi_bayer_financiero_p_ind_impuestos = get_option('rvcfdi_bayer_financiero_p_ind_impuestos');
			else
				add_option('rvcfdi_bayer_financiero_p_ind_impuestos', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_division') !== false)
				$rvcfdi_bayer_financiero_p_division = get_option('rvcfdi_bayer_financiero_p_division');
			else
				add_option('rvcfdi_bayer_financiero_p_division', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_texto') !== false)
				$rvcfdi_bayer_financiero_p_texto = get_option('rvcfdi_bayer_financiero_p_texto');
			else
				add_option('rvcfdi_bayer_financiero_p_texto', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_cebe') !== false)
				$rvcfdi_bayer_financiero_p_cebe = get_option('rvcfdi_bayer_financiero_p_cebe');
			else
				add_option('rvcfdi_bayer_financiero_p_cebe', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_pais_destinatario') !== false)
				$rvcfdi_bayer_financiero_p_pais_destinatario = get_option('rvcfdi_bayer_financiero_p_pais_destinatario');
			else
				add_option('rvcfdi_bayer_financiero_p_pais_destinatario', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_linea_de_producto') !== false)
				$rvcfdi_bayer_financiero_p_linea_de_producto = get_option('rvcfdi_bayer_financiero_p_linea_de_producto');
			else
				add_option('rvcfdi_bayer_financiero_p_linea_de_producto', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_grupo_de_proudcto') !== false)
				$rvcfdi_bayer_financiero_p_grupo_de_proudcto = get_option('rvcfdi_bayer_financiero_p_grupo_de_proudcto');
			else
				add_option('rvcfdi_bayer_financiero_p_grupo_de_proudcto', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_centro') !== false)
				$rvcfdi_bayer_financiero_p_centro = get_option('rvcfdi_bayer_financiero_p_centro');
			else
				add_option('rvcfdi_bayer_financiero_p_centro', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_articulo') !== false)
				$rvcfdi_bayer_financiero_p_articulo = get_option('rvcfdi_bayer_financiero_p_articulo');
			else
				add_option('rvcfdi_bayer_financiero_p_articulo', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_zona_de_ventas') !== false)
				$rvcfdi_bayer_financiero_p_zona_de_ventas = get_option('rvcfdi_bayer_financiero_p_zona_de_ventas');
			else
				add_option('rvcfdi_bayer_financiero_p_zona_de_ventas', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_material') !== false)
				$rvcfdi_bayer_financiero_p_material = get_option('rvcfdi_bayer_financiero_p_material');
			else
				add_option('rvcfdi_bayer_financiero_p_material', '');
			
			if(get_option('rvcfdi_bayer_financiero_p_atributo_2_sector') !== false)
				$rvcfdi_bayer_financiero_p_atributo_2_sector = get_option('rvcfdi_bayer_financiero_p_atributo_2_sector');
			else
				add_option('rvcfdi_bayer_financiero_p_atributo_2_sector', '');
			
			$datosConfiguracion = array
			(
				$rvcfdi_bayer_facturacion_c_clase_documento,
				$rvcfdi_bayer_facturacion_c_sociedad,
				$rvcfdi_bayer_facturacion_c_moneda,
				$rvcfdi_bayer_facturacion_c_tc_cab_doc,
				$rvcfdi_bayer_facturacion_p_cuenta,
				$rvcfdi_bayer_facturacion_p_division,
				$rvcfdi_bayer_facturacion_p_ce_be,
				$rvcfdi_bayer_facturacion_p_texto,
				$rvcfdi_bayer_facturacion_p_pais_destinatario,
				$rvcfdi_bayer_facturacion_p_linea_de_producto,
				$rvcfdi_bayer_facturacion_p_grupo_de_producto,
				$rvcfdi_bayer_facturacion_p_centro,
				$rvcfdi_bayer_facturacion_p_cliente,
				$rvcfdi_bayer_facturacion_p_organiz_ventas,
				$rvcfdi_bayer_facturacion_p_canal_distrib,
				$rvcfdi_bayer_facturacion_p_zoha_de_ventas,
				$rvcfdi_bayer_facturacion_p_oficina_ventas,
				$rvcfdi_bayer_facturacion_p_ramo,
				$rvcfdi_bayer_facturacion_p_grupo,
				$rvcfdi_bayer_facturacion_p_gr_vendedores,
				$rvcfdi_bayer_facturacion_p_atributo_1_sector,
				$rvcfdi_bayer_facturacion_p_atributo_2_sector,
				$rvcfdi_bayer_facturacion_p_clase_factura,
				$rvcfdi_bayer_financiero_c_clase_de_documento,
				$rvcfdi_bayer_financiero_c_sociedad,
				$rvcfdi_bayer_financiero_c_moneda,
				$rvcfdi_bayer_financiero_c_t_xt_cab_doc,
				$rvcfdi_bayer_financiero_c_cuenta_bancaria,
				$rvcfdi_bayer_financiero_c_texto,
				$rvcfdi_bayer_financiero_c_division,
				$rvcfdi_bayer_financiero_c_cebe,
				$rvcfdi_bayer_financiero_c_cliente,
				$rvcfdi_bayer_financiero_p_cuenta,
				$rvcfdi_bayer_financiero_p_ind_impuestos,
				$rvcfdi_bayer_financiero_p_division,
				$rvcfdi_bayer_financiero_p_texto,
				$rvcfdi_bayer_financiero_p_cebe,
				$rvcfdi_bayer_financiero_p_pais_destinatario,
				$rvcfdi_bayer_financiero_p_linea_de_producto,
				$rvcfdi_bayer_financiero_p_grupo_de_proudcto,
				$rvcfdi_bayer_financiero_p_centro,
				$rvcfdi_bayer_financiero_p_articulo,
				$rvcfdi_bayer_financiero_p_zona_de_ventas,
				$rvcfdi_bayer_financiero_p_material,
				$rvcfdi_bayer_financiero_p_atributo_2_sector
			);
			
			return $datosConfiguracion;
		}
	}
?>