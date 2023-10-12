<?php
	class RealVirtualWooCommerceCFDI
	{
		static function generarCFDI($rfcEmisor, $usuarioEmisor, $claveEmisor, $receptor_id, $receptor_rfc, $receptor_razon_social, $receptor_email, $metodo_pago, $metodo_pago33, $conceptos, $subtotal, $descuento, $total, $serie, $impuesto_federal, $impuesto_local, $numero_pedido, $urlSistemaAsociado, $sistema, $regimen_fiscal, $uso_cfdi, $idioma, $clave_confirmacion, $moneda, $tipo_cambio, $observacion, $precision_decimal, $huso_horario,
									$calle_receptor = "", $estado_receptor = "", $municipio_receptor = "", $pais_receptor = "", $codigoPostal_receptor = "", $lugarExpedicion = "", $versionCFDI = "", $receptor_domicilioFiscalReceptor = "", $receptor_regimenfiscal = "", $cfdi_exportacion = "", $emisor_facatradquiriente = "", $informacionGlobal_periodicidad = "", $informacionGlobal_meses = "", $informacionGlobal_año = "", $numeroPedido = 0, $idPedido = 0)
		{
			$opcion = 'GenerarCFDI33';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'RECEPTOR_ID' => $receptor_id,
				'RECEPTOR_RFC' => $receptor_rfc,
				'RECEPTOR_NOMBRE' => $receptor_razon_social,
				'RECEPTOR_EMAIL' => $receptor_email,
				'METODO_PAGO' => $metodo_pago,
				'METODO_PAGO33' => $metodo_pago33,
				'CONCEPTOS' => $conceptos,
				'SUBTOTAL' => $subtotal,
				'DESCUENTO' => $descuento,
				'TOTAL' => $total,
				'SERIE' => $serie,
				'IMPUESTO_FEDERAL' => $impuesto_federal,
				'IMPUESTO_LOCAL' => $impuesto_local,
				'NUMERO_PEDIDO' => $numero_pedido,
				'SISTEMA' => $sistema,
				'REGIMEN_FISCAL' => $regimen_fiscal,
				'USO_CFDI' => $uso_cfdi,
				'IDIOMA' => $idioma,
				'CLAVE_CONFIRMACION' => $clave_confirmacion,
				'MONEDA' => $moneda,
				'TIPO_CAMBIO' => $tipo_cambio,
				'OBSERVACION' => $observacion,
				'PRECISION_DECIMAL' => $precision_decimal,
				'HUSO_HORARIO' => $huso_horario,
				'CALLE_RECEPTOR' => $calle_receptor,
				'ESTADO_RECEPTOR' => $estado_receptor,
				'MUNICIPIO_RECEPTOR' => $municipio_receptor,
				'PAIS_RECEPTOR' => $pais_receptor,
				'CODIGOPOSTAL_RECEPTOR' => $codigoPostal_receptor,
				'LUGAR_EXPEDICION' => $lugarExpedicion,
				'RECEPTOR_DOMICILIOFISCALRECEPTOR' => $receptor_domicilioFiscalReceptor,
				'RECEPTOR_REGIMENFISCAL' => $receptor_regimenfiscal,
				'VERSION_CFDI' => $versionCFDI,
				'CFDI_EXPORTACION' => $cfdi_exportacion,
				'EMISOR_FACATRADQUIRIENTE' => $emisor_facatradquiriente,
				'NUMEROPEDIDO' => $numeroPedido,
				'IDPEDIDO' => $idPedido,
				'INFORMACIONGLOBAL_PERIODICIDAD' => $informacionGlobal_periodicidad,
				'INFORMACIONGLOBAL_MESES' => $informacionGlobal_meses,
				'INFORMACIONGLOBAL_AÑO' => $informacionGlobal_año
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
		
		static function generarFacturaGlobal($rfcEmisor, $usuarioEmisor, $claveEmisor, $serie, $forma_pago, $moneda, $tipo_cambio,
												$pedidosJSON, $subtotal, $descuento, $total, $regimen_fiscal, 
												$clave_confirmacion, $precision_decimal, $sistema, $fecha_emision, $urlSistemaAsociado, $idioma, $recalcular_impuestos, $huso_horario,
												$versionCFDI = "", $cfdi_exportacion = "", $fg_periodicidad = "", $fg_meses = "", $fg_año = "", $fg_impuestosAntiguos = "")
		{
			$opcion = 'GenerarFacturaGlobal';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'FORMA_PAGO' => $forma_pago,
				'CONCEPTOS' => $pedidosJSON,
				'SUBTOTAL' => $subtotal,
				'DESCUENTO' => $descuento,
				'TOTAL' => $total,
				'SERIE' => $serie,
				'REGIMEN_FISCAL' => $regimen_fiscal,
				'MONEDA' => $moneda,
				'TIPO_CAMBIO' => $tipo_cambio,
				'CLAVE_CONFIRMACION' => $clave_confirmacion,
				'PRECISION_DECIMAL' => $precision_decimal,
				'SISTEMA' => $sistema,
				'FECHA_EMISION' => $fecha_emision,
				'IDIOMA' => $idioma,
				'RECALCULAR_IMPUESTOS' => $recalcular_impuestos,
				'HUSO_HORARIO' => $huso_horario,
				'VERSION_CFDI' => $versionCFDI,
				'CFDI_EXPORTACION' => $cfdi_exportacion,
				'FG_PERIODICIDAD' => $fg_periodicidad,
				'FG_MESES' => $fg_meses,
				'FG_AÑO' => $fg_año,
				'FG_IMPUESTOSANTIGUOS' => $fg_impuestosAntiguos
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
		
		static function generarVistaPreviaFacturaGlobal($rfcEmisor, $usuarioEmisor, $claveEmisor, $serie, $forma_pago, $moneda, $tipo_cambio,
												$pedidosJSON, $subtotal, $descuento, $total, $regimen_fiscal, 
												$clave_confirmacion, $precision_decimal, $sistema, $fecha_emision, $urlSistemaAsociado, $idioma, $recalcular_impuestos, $huso_horario,
												$versionCFDI = "", $cfdi_exportacion = "", $fg_periodicidad = "", $fg_meses = "", $fg_año = "", $fg_impuestosAntiguos = "")
		{
			$opcion = 'GenerarVistaPreviaFacturaGlobal';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'FORMA_PAGO' => $forma_pago,
				'CONCEPTOS' => $pedidosJSON,
				'SUBTOTAL' => $subtotal,
				'DESCUENTO' => $descuento,
				'TOTAL' => $total,
				'SERIE' => $serie,
				'REGIMEN_FISCAL' => $regimen_fiscal,
				'MONEDA' => $moneda,
				'TIPO_CAMBIO' => $tipo_cambio,
				'CLAVE_CONFIRMACION' => $clave_confirmacion,
				'PRECISION_DECIMAL' => $precision_decimal,
				'SISTEMA' => $sistema,
				'FECHA_EMISION' => $fecha_emision,
				'IDIOMA' => $idioma,
				'RECALCULAR_IMPUESTOS' => $recalcular_impuestos,
				'HUSO_HORARIO' => $huso_horario,
				'VERSION_CFDI' => $versionCFDI,
				'CFDI_EXPORTACION' => $cfdi_exportacion,
				'FG_PERIODICIDAD' => $fg_periodicidad,
				'FG_MESES' => $fg_meses,
				'FG_AÑO' => $fg_año,
				'FG_IMPUESTOSANTIGUOS' => $fg_impuestosAntiguos
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
		
		static function generarVistaPreviaCFDI($rfcEmisor, $usuarioEmisor, $claveEmisor, $receptor_id, $receptor_rfc, $receptor_razon_social, $receptor_email, $metodo_pago, $metodo_pago33, $conceptos, $subtotal, $descuento, $total, $serie, $impuesto_federal, $impuesto_local, $numero_pedido, $urlSistemaAsociado, $sistema, $regimen_fiscal, $uso_cfdi, $idioma, $clave_confirmacion, $moneda, $tipo_cambio, $observacion, $precision_decimal, $huso_horario,
												$calle_receptor = "", $estado_receptor = "", $municipio_receptor = "", $pais_receptor = "", $codigoPostal_receptor = "", $lugarExpedicion = "", $versionCFDI = "", $receptor_domicilioFiscalReceptor = "", $receptor_regimenfiscal = "", $cfdi_exportacion = "", $emisor_facatradquiriente = "", $informacionGlobal_periodicidad = "", $informacionGlobal_meses = "", $informacionGlobal_año = "", $numeroPedido = 0)
		{
			$opcion = 'EscribirPDFVistaPrevia33';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'RECEPTOR_ID' => $receptor_id,
				'RECEPTOR_RFC' => $receptor_rfc,
				'RECEPTOR_NOMBRE' => $receptor_razon_social,
				'RECEPTOR_EMAIL' => $receptor_email,
				'METODO_PAGO' => $metodo_pago,
				'METODO_PAGO33' => $metodo_pago33,
				'CONCEPTOS' => $conceptos,
				'SUBTOTAL' => $subtotal,
				'DESCUENTO' => $descuento,
				'TOTAL' => $total,
				'SERIE' => $serie,
				'IMPUESTO_FEDERAL' => $impuesto_federal,
				'IMPUESTO_LOCAL' => $impuesto_local,
				'NUMERO_PEDIDO' => $numero_pedido,
				'SISTEMA' => $sistema,
				'REGIMEN_FISCAL' => $regimen_fiscal,
				'USO_CFDI' => $uso_cfdi,
				'IDIOMA' => $idioma,
				'CLAVE_CONFIRMACION' => $clave_confirmacion,
				'MONEDA' => $moneda,
				'TIPO_CAMBIO' => $tipo_cambio,
				'OBSERVACION' => $observacion,
				'PRECISION_DECIMAL' => $precision_decimal,
				'HUSO_HORARIO' => $huso_horario,
				'CALLE_RECEPTOR' => $calle_receptor,
				'ESTADO_RECEPTOR' => $estado_receptor,
				'MUNICIPIO_RECEPTOR' => $municipio_receptor,
				'PAIS_RECEPTOR' => $pais_receptor,
				'CODIGOPOSTAL_RECEPTOR' => $codigoPostal_receptor,
				'LUGAR_EXPEDICION' => $lugarExpedicion,
				'RECEPTOR_DOMICILIOFISCALRECEPTOR' => $receptor_domicilioFiscalReceptor,
				'RECEPTOR_REGIMENFISCAL' => $receptor_regimenfiscal,
				'VERSION_CFDI' => $versionCFDI,
				'CFDI_EXPORTACION' => $cfdi_exportacion,
				'EMISOR_FACATRADQUIRIENTE' => $emisor_facatradquiriente,
				'INFORMACIONGLOBAL_PERIODICIDAD' => $informacionGlobal_periodicidad,
				'INFORMACIONGLOBAL_MESES' => $informacionGlobal_meses,
				'INFORMACIONGLOBAL_AÑO' => $informacionGlobal_año,
				'NUMEROPEDIDO' => $numeroPedido
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
		
		static function obtenerVentas($rfcEmisor, $usuarioEmisor, $claveEmisor, $filtro, $sistema, $urlSistemaAsociado, $idioma)
		{
			$opcion = 'ObtenerVentas';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'FILTRO' => $filtro,
				'SISTEMA' => $sistema,
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
		
		static function generarCFDISinTimbrar($rfcEmisor, $usuarioEmisor, $claveEmisor, $receptor_id, $receptor_rfc, $receptor_razon_social, $receptor_email, $metodo_pago, $metodo_pago33, $conceptos, $subtotal, $descuento, $total, $serie, $impuesto_federal, $impuesto_local, $numero_pedido, $urlSistemaAsociado, $sistema, $regimen_fiscal, $uso_cfdi, $idioma, $clave_confirmacion, $moneda, $tipo_cambio, $observacion, $precision_decimal, $huso_horario,
									$calle_receptor = "", $estado_receptor = "", $municipio_receptor = "", $pais_receptor = "", $codigoPostal_receptor = "", $lugarExpedicion = "", $versionCFDI = "", $receptor_domicilioFiscalReceptor = "", $receptor_regimenfiscal = "", $exportacion_cfdi = "", $facAtrAdquirente = "")
		{
			$opcion = 'GenerarCFDI33SinTimbrar';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'RECEPTOR_ID' => $receptor_id,
				'RECEPTOR_RFC' => $receptor_rfc,
				'RECEPTOR_NOMBRE' => $receptor_razon_social,
				'RECEPTOR_EMAIL' => $receptor_email,
				'METODO_PAGO' => $metodo_pago,
				'METODO_PAGO33' => $metodo_pago33,
				'CONCEPTOS' => $conceptos,
				'SUBTOTAL' => $subtotal,
				'DESCUENTO' => $descuento,
				'TOTAL' => $total,
				'SERIE' => $serie,
				'IMPUESTO_FEDERAL' => $impuesto_federal,
				'IMPUESTO_LOCAL' => $impuesto_local,
				'NUMERO_PEDIDO' => $numero_pedido,
				'SISTEMA' => $sistema,
				'REGIMEN_FISCAL' => $regimen_fiscal,
				'USO_CFDI' => $uso_cfdi,
				'IDIOMA' => $idioma,
				'CLAVE_CONFIRMACION' => $clave_confirmacion,
				'MONEDA' => $moneda,
				'TIPO_CAMBIO' => $tipo_cambio,
				'OBSERVACION' => $observacion,
				'PRECISION_DECIMAL' => $precision_decimal,
				'HUSO_HORARIO' => $huso_horario,
				'CALLE_RECEPTOR' => $calle_receptor,
				'ESTADO_RECEPTOR' => $estado_receptor,
				'MUNICIPIO_RECEPTOR' => $municipio_receptor,
				'PAIS_RECEPTOR' => $pais_receptor,
				'CODIGOPOSTAL_RECEPTOR' => $codigoPostal_receptor,
				'LUGAR_EXPEDICION' => $lugarExpedicion,
				'RECEPTOR_DOMICILIOFISCALRECEPTOR' => $receptor_domicilioFiscalReceptor,
				'RECEPTOR_REGIMENFISCAL' => $receptor_regimenfiscal,
				'VERSION_CFDI' => $versionCFDI,
				'EXPORTACION_CFDI' => $exportacion_cfdi,
				'FACATRADQUIRIENTE' => $facAtrAdquirente
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
	}
?>