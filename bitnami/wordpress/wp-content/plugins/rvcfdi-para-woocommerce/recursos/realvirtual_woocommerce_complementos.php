<?php
	class RealVirtualWooCommerceComplementos
	{
		static $facturaGlobal  						= '';
		static $facturacionDashboard				= '';
		static $wsObtenerPedidosExternos			= '';
		static $wsEnviarPedidosEstado   			= '';
		static $wsEnviarPedidosCreado   			= '';
		static $wsEnviarXMLTimbrado		   			= '';
		static $emisionCFDIAutomatico   			= '';
		//static $integracionAvanzada   			= '';
		//static $notaCreditoAutomatico   			= '';
		//static $emisionFacturaGlobalAutomatico	= '';
		//static $controlUsuarios   				= '';
		
		static function guardarConfiguracionLocal($configuracion, $idioma)
		{
			global $wp_version;
			
			update_option('rvcfdi_complementos_facturaGlobal', ($configuracion['facturaGlobal']));
			update_option('rvcfdi_complementos_facturacionDashboard', ($configuracion['facturacionDashboard']));
			update_option('rvcfdi_complementos_wsObtenerPedidosExternos', ($configuracion['wsObtenerPedidosExternos']));
			update_option('rvcfdi_complementos_wsEnviarPedidosEstado', ($configuracion['wsEnviarPedidosEstado']));
			update_option('rvcfdi_complementos_wsEnviarPedidosCreado', ($configuracion['wsEnviarPedidosCreado']));
			update_option('rvcfdi_complementos_wsEnviarXMLTimbrado', ($configuracion['wsEnviarXMLTimbrado']));
			update_option('rvcfdi_complementos_emisionCFDIAutomatico', ($configuracion['emisionCFDIAutomatico']));
			//update_option('rvcfdi_complementos_integracionAvanzada', ($configuracion['integracionAvanzada']));
			//update_option('rvcfdi_complementos_notaCreditoAutomatico', ($configuracion['notaCreditoAutomatico']));
			//update_option('rvcfdi_complementos_emisionFacturaGlobalAutomatico', ($configuracion['emisionFacturaGlobalAutomatico']));
			//update_option('rvcfdi_complementos_controlUsuarios', ($configuracion['controlUsuarios']));
			
			return true;
		}
		
		static function configuracionEntidad()
		{
			$datosConfiguracion = self::obtenerConfiguracion();

			return array
			(
				'facturaGlobal'     					=> ($datosConfiguracion[0]),
				'facturacionDashboard' 					=> ($datosConfiguracion[1]),
				'wsObtenerPedidosExternos'   	 		=> ($datosConfiguracion[2]),
				'wsEnviarPedidosEstado'   				=> ($datosConfiguracion[3]),
				'wsEnviarPedidosCreado'   				=> ($datosConfiguracion[4]),
				'wsEnviarXMLTimbrado'   				=> ($datosConfiguracion[5]),
				'emisionCFDIAutomatico'   				=> ($datosConfiguracion[6])
				//'integracionAvanzada'   				=> ($datosConfiguracion[7]),
				//'notaCreditoAutomatico'   				=> ($datosConfiguracion[8]),
				//'emisionFacturaGlobalAutomatico'   		=> ($datosConfiguracion[9]),
				//'controlUsuarios'   					=> ($datosConfiguracion[10])
			);
		}
		
		static function obtenerConfiguracion()
		{
			$rvcfdi_complementos_facturaGlobal		   				= '';
			$rvcfdi_complementos_facturacionDashboard   			= '';
			$rvcfdi_complementos_wsObtenerPedidosExternos   		= '';
			$rvcfdi_complementos_wsEnviarPedidosEstado   			= '';
			$rvcfdi_complementos_wsEnviarPedidosCreado  			= '';
			$rvcfdi_complementos_wsEnviarXMLTimbrado   				= '';
			$rvcfdi_complementos_emisionCFDIAutomatico   			= '';
			//$rvcfdi_complementos_integracionAvanzada   				= '';
			//$rvcfdi_complementos_notaCreditoAutomatico   			= '';
			//$rvcfdi_complementos_emisionFacturaGlobalAutomatico 	= '';
			//$rvcfdi_complementos_controlUsuarios   					= '';
			
			if(get_option('rvcfdi_complementos_facturaGlobal') !== false)
				$rvcfdi_complementos_facturaGlobal = get_option('rvcfdi_complementos_facturaGlobal');
			else
				add_option('rvcfdi_complementos_facturaGlobal', '');
			
			if(get_option('rvcfdi_complementos_facturacionDashboard') !== false)
				$rvcfdi_complementos_facturacionDashboard = get_option('rvcfdi_complementos_facturacionDashboard');
			else
				add_option('rvcfdi_complementos_facturacionDashboard', '');
			
			if(get_option('rvcfdi_complementos_wsObtenerPedidosExternos') !== false)
				$rvcfdi_complementos_wsObtenerPedidosExternos = get_option('rvcfdi_complementos_wsObtenerPedidosExternos');
			else
				add_option('rvcfdi_complementos_wsObtenerPedidosExternos', '');
			
			if(get_option('rvcfdi_complementos_wsEnviarPedidosEstado') !== false)
				$rvcfdi_complementos_wsEnviarPedidosEstado = get_option('rvcfdi_complementos_wsEnviarPedidosEstado');
			else
				add_option('rvcfdi_complementos_wsEnviarPedidosEstado', '');
			
			if(get_option('rvcfdi_complementos_wsEnviarPedidosCreado') !== false)
				$rvcfdi_complementos_wsEnviarPedidosCreado = get_option('rvcfdi_complementos_wsEnviarPedidosCreado');
			else
				add_option('rvcfdi_complementos_wsEnviarPedidosCreado', '');
			
			if(get_option('rvcfdi_complementos_wsEnviarXMLTimbrado') !== false)
				$rvcfdi_complementos_wsEnviarXMLTimbrado = get_option('rvcfdi_complementos_wsEnviarXMLTimbrado');
			else
				add_option('rvcfdi_complementos_wsEnviarXMLTimbrado', '');
			
			if(get_option('rvcfdi_complementos_emisionCFDIAutomatico') !== false)
				$rvcfdi_complementos_emisionCFDIAutomatico = get_option('rvcfdi_complementos_emisionCFDIAutomatico');
			else
				add_option('rvcfdi_complementos_emisionCFDIAutomatico', '');
			
			/*if(get_option('rvcfdi_complementos_integracionAvanzada') !== false)
				$rvcfdi_complementos_integracionAvanzada = get_option('rvcfdi_complementos_integracionAvanzada');
			else
				add_option('rvcfdi_complementos_integracionAvanzada', '');*/
			
			/*if(get_option('rvcfdi_complementos_notaCreditoAutomatico') !== false)
				$rvcfdi_complementos_notaCreditoAutomatico = get_option('rvcfdi_complementos_notaCreditoAutomatico');
			else
				add_option('rvcfdi_complementos_notaCreditoAutomatico', '');*/
			
			/*if(get_option('rvcfdi_complementos_emisionFacturaGlobalAutomatico') !== false)
				$rvcfdi_complementos_emisionFacturaGlobalAutomatico = get_option('rvcfdi_complementos_emisionFacturaGlobalAutomatico');
			else
				add_option('rvcfdi_complementos_emisionFacturaGlobalAutomatico', '');*/
			
			/*if(get_option('rvcfdi_complementos_controlUsuarios') !== false)
				$rvcfdi_complementos_controlUsuarios = get_option('rvcfdi_complementos_controlUsuarios');
			else
				add_option('rvcfdi_complementos_controlUsuarios', '');*/
			
			$datosConfiguracion = array
			(
				$rvcfdi_complementos_facturaGlobal,
				$rvcfdi_complementos_facturacionDashboard,
				$rvcfdi_complementos_wsObtenerPedidosExternos,
				$rvcfdi_complementos_wsEnviarPedidosEstado,
				$rvcfdi_complementos_wsEnviarPedidosCreado,
				$rvcfdi_complementos_wsEnviarXMLTimbrado,
				$rvcfdi_complementos_emisionCFDIAutomatico
				/*$rvcfdi_complementos_integracionAvanzada,
				$rvcfdi_complementos_notaCreditoAutomatico,
				$rvcfdi_complementos_emisionFacturaGlobalAutomatico,
				$rvcfdi_complementos_controlUsuarios*/
			);
			
			return $datosConfiguracion;
		}
	}
?>