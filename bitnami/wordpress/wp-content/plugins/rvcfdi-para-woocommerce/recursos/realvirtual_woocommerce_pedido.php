<?php
	class RealVirtualWooCommercePedido
	{
		function inciarSesion()
		{
			$email = $_POST['email'];
			$password = $_POST['password'];
			
			$mensaje = 'DATOS RECIBIDOS email: '.$email.', password: '.$password;
			
			$respuesta = array
			(
				'mensaje'      => $mensaje
			);
				
			return (object)$respuesta;
		}
		
		static function procesarImpuestos($WC_Impuestos, $array_ImpuestosRecalculados, $configuracion, $subtotalNeto, $importeTotalIVA, $total, $descuento, $subtotal)
		{
			try
			{
				$cfdi_manejo_impuestos_pedido = $configuracion['manejo_impuestos_pedido'];
				
				$tasaIVA = '';
				
				if($cfdi_manejo_impuestos_pedido == '1')
					$tasaIVA = '16';
				else if($cfdi_manejo_impuestos_pedido == '2')
					$tasaIVA = '08';
				else if($cfdi_manejo_impuestos_pedido == '3')
					$tasaIVA = '00';
				
				$array_ImpuestosFederales = array();
				$array_ImpuestosLocales = array();
				$mensajeError = '';
				
				if($cfdi_manejo_impuestos_pedido == '0')
				{
					for($i = 0; $i < count($WC_Impuestos); $i++)
					{
						$codigoImpuestoSAT = $WC_Impuestos[$i]['codigoImpuestoSAT'];
						$nombreImpuesto = $WC_Impuestos[$i]['impuesto'];
						$importeImpuesto = $WC_Impuestos[$i]['importe'];
						$tasaPorcentaje = $WC_Impuestos[$i]['tasaPorcentaje'];
						$naturaleza = $WC_Impuestos[$i]['naturaleza'];
						
						$hayImpuestosErroneos = false;
						
						if($importeImpuesto !== 0 && $tasaPorcentaje === 0)
						{
							$tasaPorcentaje = ($importeImpuesto / $subtotalNeto) * 100;
							$hayImpuestosErroneos = true;
						}
						
						if($hayImpuestosErroneos)
							$mensajeError = 'Se encontraron errores con los impuestos en color rojo. Los impuestos fueron editados o eliminados desde WooCommerce y no es posible obtener la tasa original. Sin embargo, se intentó calcular la tasa de cada uno internamente pero es necesario contactar a tu proveedor para confirmar que estén antes de generar el CFDI. En caso de que las tasas de los impuestos no estén bien, será necesario editar o rehacer el pedido de manera correcta.';
						
						if($codigoImpuestoSAT != 'ISH')
						{
							$tipoFactor = ($nombreImpuesto == 'IVA EXENTO') ? 'Exento' : 'Tasa';
							
							$array_ImpuestosFederales[count($array_ImpuestosFederales)] = array
							(
								$naturaleza, 
								$nombreImpuesto, 
								wc_format_decimal(wc_format_decimal($tasaPorcentaje, 6) / 100, 6), 
								wc_format_decimal($importeImpuesto, 2), 
								$tipoFactor, 
								$codigoImpuestoSAT
							);
						}
						else
						{
							$array_ImpuestosLocales[count($array_ImpuestosLocales)] = array
							(
								$naturaleza,
								$codigoImpuestoSAT,
								wc_format_decimal(wc_format_decimal($tasaPorcentaje, 6) / 100, 6),
								wc_format_decimal($importeImpuesto, 2)
							);
						}
					}
				}
				else if($cfdi_manejo_impuestos_pedido == '1' || $cfdi_manejo_impuestos_pedido == '2' || $cfdi_manejo_impuestos_pedido == '3')
				{
					$array_ImpuestosFederales[0] = array
					(
						'1',
						'IVA',
						wc_format_decimal(wc_format_decimal($tasaIVA, 6) / 100, 6),
						$importeTotalIVA,
						'Tasa',
						'002'
					);
					
					if($cfdi_manejo_impuestos_pedido == '1' || $cfdi_manejo_impuestos_pedido == '2' || $cfdi_manejo_impuestos_pedido == '3')
						$total = $subtotalNeto + $importeTotalIVA;
				}
				else if($cfdi_manejo_impuestos_pedido == '4' || $cfdi_manejo_impuestos_pedido == '5' || $cfdi_manejo_impuestos_pedido == '6')
				{
					$importeTotalImpuestos = 0;
			
					for($i = 0; $i < count($array_ImpuestosRecalculados); $i++)
					{
						$codigoImpuestoSAT = $array_ImpuestosRecalculados[$i][5];
						$nombreImpuesto = $array_ImpuestosRecalculados[$i][1];
						$importeImpuesto = $array_ImpuestosRecalculados[$i][3];
						$tasaPorcentaje = $array_ImpuestosRecalculados[$i][2];
						$naturaleza = $array_ImpuestosRecalculados[$i][0];
						
						$tipoFactor = ($nombreImpuesto == 'IVA EXENTO') ? 'Exento' : 'Tasa';
						
						if($codigoImpuestoSAT != 'ISH')
						{
							$array_ImpuestosFederales[count($array_ImpuestosFederales)] = array
							(
								$naturaleza, 
								$nombreImpuesto, 
								wc_format_decimal(wc_format_decimal($tasaPorcentaje, 6) / 100, 6), 
								wc_format_decimal($importeImpuesto, 2), 
								$tipoFactor, 
								$codigoImpuestoSAT
							);
						}
						else
						{
							$array_ImpuestosLocales[count($array_ImpuestosLocales)] = array
							(
								$naturaleza,
								$codigoImpuestoSAT,
								wc_format_decimal(wc_format_decimal($tasaPorcentaje, 6) / 100, 6),
								wc_format_decimal($importeImpuesto, 2)
							);
						}
						
						if($cfdi_manejo_impuestos_pedido == '5')
						{
							if($nombreImpuesto == 'IVA' || $nombreImpuesto == 'IEPS' || $nombreImpuesto == 'ISH')
								$importeTotalImpuestos = $importeTotalImpuestos + $importeImpuesto;
							else if($nombreImpuesto == 'IVA RETENIDO' || $nombreImpuesto == 'IEPS RETENIDO' || $nombreImpuesto == 'ISR')
								$importeTotalImpuestos = $importeTotalImpuestos - $importeImpuesto;
						}
					}
					
					if($cfdi_manejo_impuestos_pedido == '5')
						$total = $subtotal - $descuento + $importeTotalImpuestos;
				}
				
				$datosImpuestos = array
				(
					'total' => $total,
					'ImpuestosFederales' => $array_ImpuestosFederales,
					'ImpuestosLocales' => $array_ImpuestosLocales,
					'MensajeError' => '',
					'cfdi_manejo_impuestos_pedido' => $cfdi_manejo_impuestos_pedido
				);
				
				return (object)$datosImpuestos;
			}
			catch(Exception $e)
			{
				$datosImpuestos = array
				(
					'MensajeError' => $e->getMessage()
				);
				
				return (object)$datosImpuestos;
			}
		}
		
		static function obtenerConceptosPedido($conceptos, $impuestos, $configuracion)
		{
			try
			{
				$array_Conceptos = array();
				$importeTotalIVA = 0;
				$nuevoSubtotal = 0;
				$nuevoDescuento = 0;
				
				$cfdi_precision_decimal = $configuracion['precision_decimal'];
				
				$cfdi_manejo_impuestos_pedido = $configuracion['manejo_impuestos_pedido'];
				
				$tasaIVA = '';
				
				if($cfdi_manejo_impuestos_pedido == '1')
					$tasaIVA = '16';
				else if($cfdi_manejo_impuestos_pedido == '2')
					$tasaIVA = '08';
				else if($cfdi_manejo_impuestos_pedido == '3')
					$tasaIVA = '00';
				
				$array_ImpuestosRecalculados = array();
				
				if($cfdi_manejo_impuestos_pedido == '4' || $cfdi_manejo_impuestos_pedido == '5' || $cfdi_manejo_impuestos_pedido == '6')
				{
					for($i = 0; $i < count($impuestos); $i++)
					{
						$codigoImpuestoSAT = $impuestos[$i]['codigoImpuestoSAT'];
						$nombreImpuesto = $impuestos[$i]['impuesto'];
						$importeImpuesto = $impuestos[$i]['importe'];
						$tasaPorcentaje = $impuestos[$i]['tasaPorcentaje'];
						$naturaleza = $impuestos[$i]['naturaleza'];
						$tipoFactor = ($nombreImpuesto == 'IVA EXENTO') ? 'Exento' : 'Tasa';
						
						$array_ImpuestosRecalculados[$i] = array($naturaleza, $nombreImpuesto, $tasaPorcentaje, 0, $tipoFactor, $codigoImpuestoSAT);
					}
				}
				
				for($i = 0; $i < count($conceptos); $i++)
				{
					$importeIVAConcepto = "";
					$importeIEPSConcepto = "";
					$importeIVARetenidoConcepto = "";
					$importeIEPSRetenidoConcepto = "";
					$importeISRConcepto = "";
					$importeISHConcepto = "";
					
					$tasaIVAConcepto = "";
					$tasaIEPSConcepto = "";
					$tasaIVARetenidoConcepto = "";
					$tasaIEPSRetenidoConcepto = "";
					$tasaISRConcepto = "";
					$tasaISHConcepto = "";
					
					$tasaPorcentajeIVAConcepto = "";
					$tasaPorcentajeIEPSConcepto = "";
					$tasaPorcentajeIVARetenidoConcepto = "";
					$tasaPorcentajeIEPSRetenidoConcepto = "";
					$tasaPorcentajeISRConcepto = "";
					$tasaPorcentajeISHConcepto = "";
					
					$nombre = $conceptos[$i]['name'];
					$nombre = str_replace("ʺ", "\"", $nombre);
					
					$tipoConcepto = $conceptos[$i]['tipoConcepto'];
					$shipping_config_principal = $configuracion['config_principal_shipping'];
					
					if($shipping_config_principal == '0' && $tipoConcepto == 'shipping')
					{
						continue;
					}
					
					$claveServicio = '';
					
					if($conceptos[$i]['clave_servicio'] == '')
						$claveServicio = ($tipoConcepto == 'shipping') ? $configuracion['clave_servicio_shipping'] : $configuracion['clave_servicio'];
					else 
						$claveServicio = $conceptos[$i]['clave_servicio'];
					
					$claveUnidad = '';
					
					if($conceptos[$i]['clave_unidad'] == '')
						$claveUnidad = ($tipoConcepto == 'shipping') ? $configuracion['clave_unidad_shipping'] : $configuracion['clave_unidad'];
					else 
						$claveUnidad = $conceptos[$i]['clave_unidad'];
					
					$unidadMedida = '';
					
					if($conceptos[$i]['unidad_medida'] == '')
						$unidadMedida = ($tipoConcepto == 'shipping') ? $configuracion['unidad_medida_shipping'] : $configuracion['unidad_medida'];
					else 
						$unidadMedida = $conceptos[$i]['unidad_medida'];
					
					$claveProducto = '';
					
					if($conceptos[$i]['clave_producto'] == '')
						$claveProducto = ($tipoConcepto == 'shipping') ? $configuracion['clave_producto_shipping'] : $configuracion['clave_producto'];
					else 
						$claveProducto = $conceptos[$i]['clave_producto'];
					
					$noPedimento = '';
					
					if($conceptos[$i]['numero_pedimento'] == '')
						$noPedimento = ($tipoConcepto == 'shipping') ? $configuracion['numero_pedimento_shipping'] : $configuracion['numero_pedimento'];
					else 
						$noPedimento = $conceptos[$i]['numero_pedimento'];
					
					$objetoImpuesto = '';
					
					if($conceptos[$i]['objeto_impuesto'] == '')
						$objetoImpuesto = ($tipoConcepto == 'shipping') ? $configuracion['objeto_imp_shipping'] : $configuracion['objeto_imp_producto'];
					else 
						$objetoImpuesto = $conceptos[$i]['objeto_impuesto'];
					
					$precioUnitario = $conceptos[$i]['subtotal'];
					$cantidad = $conceptos[$i]['quantity'];
					$importeUnitario = $conceptos[$i]['total'];
					$impuestosConcepto = $conceptos[$i]['impuestos'];
					$descuentoUnitario = 0;
					$textoImpuestos = '';
					$columnaTextoImpuestos = '';
					$importeConcepto = $conceptos[$i]['importe'];
					
					if($cfdi_manejo_impuestos_pedido == '0')
						$precioUnitario = $conceptos[$i]['subtotal2'];
					
					if($cfdi_manejo_impuestos_pedido == '0')
					{
						$descuentoUnitario = ($conceptos[$i]['descuento'] == '') ? ($importeConcepto - $conceptos[$i]['total']) : $conceptos[$i]['descuento'];
						$descripcionImpuestos = '';
						
						$nuevoSubtotal = $nuevoSubtotal + $importeConcepto;
						
						for($k = 0; $k < count($impuestosConcepto); $k++)
						{
							$nombreImpuesto = $impuestosConcepto[$k]['impuesto'];
							$importeImpuesto = $impuestosConcepto[$k]['importe'];
							$tasaImpuesto = $impuestosConcepto[$k]['tasa'];
							$tasaPorcentajeImpuesto = $impuestosConcepto[$k]['tasaPorcentaje'];
							
							if($nombreImpuesto == 'IVA' && $importeIVAConcepto == '')
							{
								$importeIVAConcepto = $importeImpuesto;
								$tasaIVAConcepto = $tasaImpuesto;
								$tasaPorcentajeIVAConcepto = $tasaPorcentajeImpuesto;
							}
							else if($nombreImpuesto == 'IVA EXENTO' && $importeIVAConcepto == '')
							{
								$importeIVAConcepto = -1;
								$tasaIVAConcepto = -1;
								$tasaPorcentajeIVAConcepto = -1;
							}
							else if($nombreImpuesto == 'IEPS' && $importeIEPSConcepto == '')
							{
								$importeIEPSConcepto = $importeImpuesto;
								$tasaIEPSConcepto = $tasaImpuesto;
								$tasaPorcentajeIEPSConcepto = $tasaPorcentajeImpuesto;
							}
							else if($nombreImpuesto == 'IVA RETENIDO' && $importeIVARetenidoConcepto == '')
							{
								$importeIVARetenidoConcepto = $importeImpuesto;
								$tasaIVARetenidoConcepto = $tasaImpuesto;
								$tasaPorcentajeIVARetenidoConcepto = $tasaPorcentajeImpuesto;
							}
							else if($nombreImpuesto == 'IEPS RETENIDO' && $importeIEPSRetenidoConcepto == '')
							{
								$importeIEPSRetenidoConcepto = $importeImpuesto;
								$tasaIEPSRetenidoConcepto = $tasaImpuesto;
								$tasaPorcentajeIEPSRetenidoConcepto = $tasaPorcentajeImpuesto;
							}
							else if($nombreImpuesto == 'ISR' && $importeISRConcepto == '')
							{
								$importeISRConcepto = $importeImpuesto;
								$tasaISRConcepto = $tasaImpuesto;
								$tasaPorcentajeISRConcepto = $tasaPorcentajeImpuesto;
							}
						}
					}
					else if($cfdi_manejo_impuestos_pedido == '1' || $cfdi_manejo_impuestos_pedido == '2' || $cfdi_manejo_impuestos_pedido == '3')
					{
						$precioUnitario = $importeUnitario / (float)('1.'.$tasaIVA);
						$precioUnitario = wc_format_decimal($precioUnitario, $cfdi_precision_decimal);
						
						$nuevoSubtotal = $nuevoSubtotal + $precioUnitario;
						
						$IVAUnitario = $importeUnitario - $precioUnitario;
						$IVAUnitario = wc_format_decimal($IVAUnitario, $cfdi_precision_decimal);
						$importeTotalIVA = $importeTotalIVA + $IVAUnitario;
						$importeUnitario = $precioUnitario;
						$importeConcepto = $precioUnitario;
						$precioUnitario = $precioUnitario / $cantidad;
						
						$importeIVAConcepto = $IVAUnitario;
						$tasaIVAConcepto = wc_format_decimal((wc_format_decimal($tasaIVA, 6) / 100), 6);
						$tasaPorcentajeIVAConcepto = $tasaIVA;
					}
					else if($cfdi_manejo_impuestos_pedido == '4' || $cfdi_manejo_impuestos_pedido == '5' || $cfdi_manejo_impuestos_pedido == '6')
					{
						$importeUnitario = $conceptos[$i]['total']; //Importe del Articulo de WooCommerce
						$importeConcepto = $importeUnitario;
						$descuentoUnitario = wc_format_decimal(0, 6);
						$nuevoDescuento = $nuevoDescuento + wc_format_decimal($descuentoUnitario, 2);
						//Recalcular precio unitario
						$precioUnitario = ($importeUnitario + $descuentoUnitario) / $cantidad;
						$precioUnitario = wc_format_decimal($precioUnitario, 6);
						$subtotalAnterior = $nuevoSubtotal;
						$nuevoSubtotal = $nuevoSubtotal + (wc_format_decimal($importeUnitario, 2) + wc_format_decimal($descuentoUnitario, 2));
						$nuevoSubtotal = wc_format_decimal($nuevoSubtotal, 2);
						
						for($k = 0; $k < count(impuestosConcepto); $k++)
						{
							$nombreImpuesto = $impuestosConcepto[$k]['impuesto'];
							$importeImpuesto = $impuestosConcepto[$k]['importe'];
							$tasaImpuesto = $impuestosConcepto[$k]['tasa'];
							$tasaPorcentajeImpuesto = $impuestosConcepto[$k]['tasaPorcentaje'];
							
							if($nombreImpuesto == 'IVA' && $importeIVAConcepto == '')
							{
								$importeIVAConcepto = $importeImpuesto;
								$tasaIVAConcepto = $tasaImpuesto;
								$tasaPorcentajeIVAConcepto = $tasaPorcentajeImpuesto;
								
								$importeTotalIVA = wc_format_decimal($importeImpuesto, 2);
								
								//PROCESO 25/08/2021
								$importeIVARecalculado = wc_format_decimal(wc_format_decimal($importeUnitario, 2) * $tasaImpuesto, 2);
								if($importeTotalIVA != $importeIVARecalculado)
								{
									$importeTotalIVA = $importeIVARecalculado;
									$importeImpuesto = $importeIVARecalculado;
									$importeIVAConcepto = $importeIVARecalculado;
								}
								//FIN PROCESO 25/08/2021
								
								for($m = 0; $m < count($array_ImpuestosRecalculados); $m++)
								{
									if($array_ImpuestosRecalculados[$m][1] == 'IVA' && $array_ImpuestosRecalculados[$m][2] == $tasaPorcentajeImpuesto)
										$array_ImpuestosRecalculados[$m][3] = $array_ImpuestosRecalculados[$m][3] + $importeTotalIVA;
								}
							}
							else if($nombreImpuesto == 'IVA EXENTO' && $importeIVAConcepto == '')
							{
								$importeIVAConcepto = -1;
								$tasaIVAConcepto = -1;
								$tasaPorcentajeIVAConcepto = -1;
								
								$importeTotalIVAExento = wc_format_decimal($importeImpuesto, 2);
								
								for($m = 0; $m < count($array_ImpuestosRecalculados); $m++)
								{
									if($array_ImpuestosRecalculados[$m][1] == 'IVA EXENTO' && $array_ImpuestosRecalculados[$m][2] == $tasaPorcentajeImpuesto)
										$array_ImpuestosRecalculados[$m][3] = $array_ImpuestosRecalculados[$m][3] + $importeTotalIVAExento;
								}
							}
							else if($nombreImpuesto == 'IEPS' && $importeIEPSConcepto == '')
							{
								$importeIEPSConcepto = $importeImpuesto;
								$tasaIEPSConcepto = $tasaImpuesto;
								$tasaPorcentajeIEPSConcepto = $tasaPorcentajeImpuesto;
								
								$importeTotalIEPS = wc_format_decimal($importeImpuesto, 2);
								
								for($m = 0; $m < count($array_ImpuestosRecalculados); $m++)
								{
									if($array_ImpuestosRecalculados[$m][1] == 'IEPS' && $array_ImpuestosRecalculados[$m][2] == $tasaPorcentajeImpuesto)
										$array_ImpuestosRecalculados[$m][3] = $array_ImpuestosRecalculados[$m][3] + $importeTotalIEPS;
								}
							}
							else if($nombreImpuesto == 'IVA RETENIDO' && $importeIVARetenidoConcepto == '')
							{
								$importeIVARetenidoConcepto = $importeImpuesto;
								$tasaIVARetenidoConcepto = $tasaImpuesto;
								$tasaPorcentajeIVARetenidoConcepto = $tasaPorcentajeImpuesto;
								
								$importeTotalIVARetenido = wc_format_decimal($importeImpuesto, 2);
								
								for($m = 0; $m < count($array_ImpuestosRecalculados); $m++)
								{
									if($array_ImpuestosRecalculados[$m][1] == 'IVA RETENIDO' && $array_ImpuestosRecalculados[$m][2] == $tasaPorcentajeImpuesto)
										$array_ImpuestosRecalculados[$m][3] = $array_ImpuestosRecalculados[$m][3] + $importeTotalIVARetenido;
								}
							}
							else if($nombreImpuesto == 'IEPS RETENIDO' && $importeIEPSRetenidoConcepto == '')
							{
								$importeIEPSRetenidoConcepto = $importeImpuesto;
								$tasaIEPSRetenidoConcepto = $tasaImpuesto;
								$tasaPorcentajeIEPSRetenidoConcepto = $tasaPorcentajeImpuesto;
								
								$importeTotalIEPSRetenido = wc_format_decimal($importeImpuesto, 2);
								
								for($m = 0; $m < count($array_ImpuestosRecalculados); $m++)
								{
									if($array_ImpuestosRecalculados[$m][1] == 'IEPS RETENIDO' && $array_ImpuestosRecalculados[$m][2] == $tasaPorcentajeImpuesto)
										$array_ImpuestosRecalculados[$m][3] = $array_ImpuestosRecalculados[$m][3] + $importeTotalIEPSRetenido;
								}
							}
							else if($nombreImpuesto == 'ISR' && $importeISRConcepto == '')
							{
								$importeISRConcepto = $importeImpuesto;
								$tasaISRConcepto = $tasaImpuesto;
								$tasaPorcentajeISRConcepto = $tasaPorcentajeImpuesto;
								
								$importeTotalISR = wc_format_decimal($importeImpuesto, 2);
								
								for($m = 0; $m < count($array_ImpuestosRecalculados); $m++)
								{
									if($array_ImpuestosRecalculados[$m][1] == 'ISR' && $array_ImpuestosRecalculados[$m][2] == $tasaPorcentajeImpuesto)
										$array_ImpuestosRecalculados[$m][3] = $array_ImpuestosRecalculados[$m][3] + $importeTotalISR;
								}
							}
							else if($nombreImpuesto == 'ISH' && $importeISHConcepto == '')
							{
								$importeISHConcepto = $importeImpuesto;
								$tasaISHConcepto = $tasaImpuesto;
								$tasaPorcentajeISHConcepto = $tasaPorcentajeImpuesto;
								
								$importeTotalISH = wc_format_decimal($importeImpuesto, 2);
								
								for($m = 0; $m < count($array_ImpuestosRecalculados); $m++)
								{
									if($array_ImpuestosRecalculados[$m][1] == 'ISH' && $array_ImpuestosRecalculados[$m][2] == $tasaPorcentajeImpuesto)
										$array_ImpuestosRecalculados[$m][3] = $array_ImpuestosRecalculados[$m][3] + $importeTotalISH;
								}
							}
						}
					}
					
					$baseImpuesto = wc_format_decimal($importeConcepto, $cfdi_precision_decimal) - wc_format_decimal($descuentoUnitario, $cfdi_precision_decimal);
					
					if($cfdi_manejo_impuestos_pedido == '1' || $cfdi_manejo_impuestos_pedido == '2' || $cfdi_manejo_impuestos_pedido == '3' || $cfdi_manejo_impuestos_pedido == '4' || $cfdi_manejo_impuestos_pedido == '5' || $cfdi_manejo_impuestos_pedido == '6')
					{	
						$baseImpuesto = $importeUnitario;
					}
					
					if($cfdi_manejo_impuestos_pedido == '1' || $cfdi_manejo_impuestos_pedido == '2' || $cfdi_manejo_impuestos_pedido == '3' || $cfdi_manejo_impuestos_pedido == '4' || $cfdi_manejo_impuestos_pedido == '5' || $cfdi_manejo_impuestos_pedido == '6')
						$cfdi_precision_decimal = 2;
					
					$tipoImpuestoIVA = 'F';
					$importeIVAConceptoFinal = wc_format_decimal($importeIVAConcepto, $cfdi_precision_decimal);
					$tipoFactorIVA = 'Tasa';
					
					if($importeIVAConcepto == -1 && $tasaIVAConcepto == -1)
					{
						$tipoFactorIVA = 'Exento';
						$tasaIVAConcepto = 0;
					}
					else if($importeIVAConcepto === '' || $importeIVAConcepto < 0)
					{
						$tipoImpuestoIVA = '';
						$tasaIVAConcepto = '';
						$importeIVAConceptoFinal = '';
					}
					
					$array_Conceptos[$i] = array();
					$array_Conceptos[$i][0] = ''; //TIPO DE CFDI: RA, RH O VACIO
					$array_Conceptos[$i][1] = $claveServicio; //ClaveProdServ
					$array_Conceptos[$i][2] = $claveProducto; //CLAVE
					$array_Conceptos[$i][3] = base64_encode($nombre); //DESCRIPCION
					$array_Conceptos[$i][4] = $claveUnidad; //CLAVE UNIDAD
					$array_Conceptos[$i][5] = $unidadMedida; //UNIDAD MEDIDA
					$array_Conceptos[$i][6] = wc_format_decimal($cantidad, $cfdi_precision_decimal); //CANTIDAD
					$array_Conceptos[$i][7] = wc_format_decimal($precioUnitario, $cfdi_precision_decimal); //PRECIO UNITARIO
					$array_Conceptos[$i][8] = wc_format_decimal($importeUnitario, $cfdi_precision_decimal); //IMPORTE
					$array_Conceptos[$i][9] = wc_format_decimal($descuentoUnitario, $cfdi_precision_decimal); //DESCUENTO
					$array_Conceptos[$i][10] = ''; //CUENTA PREDIAL
					$array_Conceptos[$i][11] = $noPedimento; //NUMERO ADUANA
					$array_Conceptos[$i][12] = ''; //FECHA ADUANA
					$array_Conceptos[$i][13] = ''; //ADUANA
					$array_Conceptos[$i][14] = '002'; //CODIGO IVA
					$array_Conceptos[$i][15] = $tipoFactorIVA; //FACTOR
					$array_Conceptos[$i][16] = $tipoImpuestoIVA; //TIPO IMPUESTO
					$array_Conceptos[$i][17] = '1'; //1 (TRASLADADO)
					$array_Conceptos[$i][18] = $tasaIVAConcepto; //TASA
					$array_Conceptos[$i][19] = $importeIVAConceptoFinal; //IMPORTE
					$array_Conceptos[$i][20] = '003'; //CODIGO IEPS
					$array_Conceptos[$i][21] = 'Tasa'; //FACTOR
					$array_Conceptos[$i][22] = ($importeIEPSConcepto !== '' && $importeIEPSConcepto >= 0) ? 'F' : ''; //TIPO IMPUESTO
					$array_Conceptos[$i][23] = '1'; //1 (TRASLADADO)
					$array_Conceptos[$i][24] = ($importeIEPSConcepto !== '' && $importeIEPSConcepto >= 0) ? $tasaIEPSConcepto : ''; //TASA
					$array_Conceptos[$i][25] = ($importeIEPSConcepto !== '' && $importeIEPSConcepto >= 0) ? (wc_format_decimal($importeIEPSConcepto, $cfdi_precision_decimal)) : ''; //IMPORTE
					$array_Conceptos[$i][26] = '002'; //CODIGO IVA RETENIDO
					$array_Conceptos[$i][27] = 'Tasa'; //FACTOR
					$array_Conceptos[$i][28] = ($importeIVARetenidoConcepto !== '' && $importeIVARetenidoConcepto >= 0) ? 'F' : ''; //TIPO IMPUESTO
					$array_Conceptos[$i][29] = '2'; //2 (RETENIDO)
					$array_Conceptos[$i][30] = ($importeIVARetenidoConcepto !== '' && $importeIVARetenidoConcepto >= 0) ? $tasaIVARetenidoConcepto : ''; //TASA
					$array_Conceptos[$i][31] = ($importeIVARetenidoConcepto !== '' && $importeIVARetenidoConcepto >= 0) ? (wc_format_decimal($importeIVARetenidoConcepto, $cfdi_precision_decimal)) : ''; //IMPORTE
					$array_Conceptos[$i][32] = '003'; //CODIGO IEPS RETENIDO
					$array_Conceptos[$i][33] = 'Tasa'; //FACTOR
					$array_Conceptos[$i][34] = ($importeIEPSRetenidoConcepto !== '' && $importeIEPSRetenidoConcepto >= 0) ? 'F' : ''; //TIPO IMPUESTO
					$array_Conceptos[$i][35] = '2'; //2 (RETENIDO)
					$array_Conceptos[$i][36] = ($importeIEPSRetenidoConcepto !== '' && $importeIEPSRetenidoConcepto >= 0) ? $tasaIEPSRetenidoConcepto : ''; //TASA
					$array_Conceptos[$i][37] = ($importeIEPSRetenidoConcepto !== '' && $importeIEPSRetenidoConcepto >= 0) ? (wc_format_decimal($importeIEPSRetenidoConcepto, $cfdi_precision_decimal)) : ''; //IMPORTE
					$array_Conceptos[$i][38] = '001'; //CODIGO ISR RETENIDO
					$array_Conceptos[$i][39] = 'Tasa'; //FACTOR
					$array_Conceptos[$i][40] = ($importeISRConcepto !== '' && $importeISRConcepto >= 0) ? 'F' : ''; //TIPO IMPUESTO
					$array_Conceptos[$i][41] = '2'; //2 (RETENIDO)
					$array_Conceptos[$i][42] = ($importeISRConcepto !== '' && $importeISRConcepto >= 0) ? $tasaISRConcepto : ''; //TASA
					$array_Conceptos[$i][43] = ($importeISRConcepto !== '' && $importeISRConcepto >= 0) ? (wc_format_decimal($importeISRConcepto, $cfdi_precision_decimal)) : ''; //IMPORTE
					$array_Conceptos[$i][44] = '';
					$array_Conceptos[$i][45] = '';
					$array_Conceptos[$i][46] = '';
					$array_Conceptos[$i][47] = '';
					$array_Conceptos[$i][48] = ''; 
					$array_Conceptos[$i][49] = '';
					$array_Conceptos[$i][50] = (wc_format_decimal($baseImpuesto, $cfdi_precision_decimal));
					$array_Conceptos[$i][51] = '';
					$array_Conceptos[$i][52] = '';
					$array_Conceptos[$i][53] = $objetoImpuesto;
				}
				
				$datosConceptos = array
				(
					'ImporteTotalIVA' => $importeTotalIVA,
					'Subtotal' => $nuevoSubtotal,
					'Descuento' => $nuevoDescuento,
					'ImpuestosRecalculados' => $array_ImpuestosRecalculados,
					'Conceptos' => $array_Conceptos,
					'MensajeError' => ''
				);
				
				return (object)$datosConceptos;
			}
			catch(Exception $e)
			{
				$datosConceptos = array
				(
					'MensajeError' => $e->getMessage()
				);
				
				return (object)$datosConceptos;
			}
		}
		
		static function obtenerPedido($idPedido, $precision_decimal)
		{
			if(!isset($precision_decimal))
				$precision_decimal = '2';
			
			$precision_decimal = '6';
				
			try
			{
				//$pedido = wc_get_order($idPedido); //Para versión inferior a 2.2 de WooCommerce
				$pedido = new WC_Order($idPedido); //Para versión superior o igual a 2.2 de WooCommerce
				$order_post = get_post($idPedido);
				
				$orderData = $pedido->get_data();
				$orderMeta = get_post_meta($idPedido);
				
				$subtotal = 0;
				$total = 0;
				$total_tax = 0;
				$total_shipping = 0;
				$cart_tax = 0;
				$shipping_tax = 0;
				$total_discount = 0;
				
				if(is_numeric($pedido->get_subtotal()))
					$subtotal = $pedido->get_subtotal();
				
				if(is_numeric($pedido->get_total()))
					$total = $pedido->get_total();
				
				if(is_numeric($pedido->get_total_tax()))
					$total_tax = $pedido->get_total_tax();
				
				if(is_numeric($pedido->get_total_shipping()))
					$total_shipping = $pedido->get_total_shipping();
				
				if(is_numeric($pedido->get_cart_tax()))
					$cart_tax = $pedido->get_cart_tax();
				
				if(is_numeric($pedido->get_shipping_tax()))
					$shipping_tax = $pedido->get_shipping_tax();
				
				if(is_numeric($pedido->get_total_discount()))
					$total_discount = $pedido->get_total_discount();
				
				$total_coupons = 0;
				
				/*$coupons = $pedido->get_used_coupons();
				$coupon_lines = array();
				
				foreach($coupons as $coupon)
				{
					$coupon_post_object = get_page_by_title($coupon, OBJECT, 'shop_coupon');
					$coupon_id = $coupon_post_object->ID;
					$coupon_object = new WC_Coupon($coupon_id);
					$coupon_code = $coupon_object->get_code();
					$coupon_amount = $coupon_object->get_amount();
					
					if(is_numeric($coupon_amount))
						$total_coupons = $total_coupons + $coupon_amount;
					
					$coupon_lines[] = array
					(
						'coupon_id' => $coupon_id,
						'coupon_code' => $coupon_code,
						'coupon_amount' => $coupon_amount
					);
				}*/
				
				$datosPedido = array
				(
					'mensajeError'				=> '',
					'impuestos'					=> '',
					'flujoImpuestos' 			=> '',
					'id'                        => $pedido->id,
					'order_number'              => $pedido->get_order_number(),
					'created_at'                => $order_post->post_date_gmt,
					'updated_at'                => $order_post->post_modified_gmt,
					'completed_at'              => $pedido->completed_date,
					'status'                    => $pedido->get_status(),
					'currency'                  => $pedido->order_currency,
					'subtotal'                  => wc_format_decimal($subtotal, $precision_decimal ),
					'total_coupons'             => wc_format_decimal($total_coupons, $precision_decimal ),
					'total'                     => wc_format_decimal($total, $precision_decimal ),
					'total_line_items_quantity' => $pedido->get_item_count(),
					'total_tax'                 => wc_format_decimal($total_tax, $precision_decimal),
					'total_shipping'            => wc_format_decimal($total_shipping, $precision_decimal),
					'cart_tax'                  => wc_format_decimal($cart_tax, $precision_decimal),
					'shipping_tax'              => wc_format_decimal($shipping_tax, $precision_decimal),
					'total_discount'            => wc_format_decimal($total_discount, $precision_decimal),
					'shipping_methods'          => $pedido->get_shipping_method(),
					'payment_details' => array
					(
						'method_id'    => $pedido->payment_method,
						'method_title' => $pedido->payment_method_title,
						'paid'         => isset( $pedido->paid_date ),
					),
					'billing_first_name' => $pedido->billing_first_name,
					'billing_last_name'  => $pedido->billing_last_name,
					'billing_company'    => $pedido->billing_company,
					'billing_address_1'  => $pedido->billing_address_1,
					'billing_address_2'  => $pedido->billing_address_2,
					'billing_city'       => $pedido->billing_city,
					'billing_state'      => $pedido->billing_state,
					'billing_postcode'   => $pedido->billing_postcode,
					'billing_country'    => $pedido->billing_country,
					'billing_email'      => $pedido->billing_email,
					'billing_phone'      => $pedido->billing_phone,
					'billing_address' => array
					(
						'first_name' => $pedido->billing_first_name,
						'last_name'  => $pedido->billing_last_name,
						'company'    => $pedido->billing_company,
						'address_1'  => $pedido->billing_address_1,
						'address_2'  => $pedido->billing_address_2,
						'city'       => $pedido->billing_city,
						'state'      => $pedido->billing_state,
						'postcode'   => $pedido->billing_postcode,
						'country'    => $pedido->billing_country,
						'email'      => $pedido->billing_email,
						'phone'      => $pedido->billing_phone,
					),
					'shipping_address' => array
					(
						'first_name' => $pedido->shipping_first_name,
						'last_name'  => $pedido->shipping_last_name,
						'company'    => $pedido->shipping_company,
						'address_1'  => $pedido->shipping_address_1,
						'address_2'  => $pedido->shipping_address_2,
						'city'       => $pedido->shipping_city,
						'state'      => $pedido->shipping_state,
						'postcode'   => $pedido->shipping_postcode,
						'country'    => $pedido->shipping_country,
					),
					'note'                      => $pedido->customer_note,
					'customer_ip'               => $pedido->customer_ip_address,
					'customer_user_agent'       => $pedido->customer_user_agent,
					'customer_id'               => $pedido->customer_user,
					'view_order_url'            => $pedido->get_view_order_url(),
					'line_items'                => array(),
					'shipping_lines'            => array(),
					'tax_lines'                 => array(),
					'fee_lines'                 => array(),
					'coupon_lines'              => array(), //$coupon_lines,
					'orderMeta'					=> $orderMeta,
					'orderData'					=> $orderData
				);
				
				$impuestosTemporal = array();
				
				foreach($pedido->get_items('tax') as $key => $tax)
				{
					$amount = (float) $tax->get_tax_total() + (float) $tax->get_shipping_tax_total();
					$tasa = WC_Tax::get_rate_percent($tax->get_rate_id());
					$tasa = str_replace("%", '', $tasa);
					
					$codigoImpuestoSAT = '';
					$naturaleza = '1';
					$nombreImpuesto = strtoupper($tax->get_label());
								
					if($nombreImpuesto == 'IVA' || $nombreImpuesto == 'IVA 0' || $nombreImpuesto == 'IVA 16' || $nombreImpuesto == 'IVA 8' || $nombreImpuesto == 'IVA RETENIDO'  || $nombreImpuesto == 'IVA EXENTO')
						$codigoImpuestoSAT = '002';
					else if($nombreImpuesto == 'IEPS' || $nombreImpuesto == 'IEPS RETENIDO')
						$codigoImpuestoSAT = '003';
					else if($nombreImpuesto == 'ISR')
						$codigoImpuestoSAT = '001';
					else if($nombreImpuesto == 'ISH')
						$codigoImpuestoSAT = 'ISH';
					else
						$codigoImpuestoSAT = '';
					
					if($nombreImpuesto == 'IVA RETENIDO' || $nombreImpuesto == 'IEPS RETENIDO' || $nombreImpuesto == 'ISR')
						$naturaleza = '2';
					
					if($nombreImpuesto == 'IVA 0' || $nombreImpuesto == 'IVA 16' || $nombreImpuesto == 'IVA 8')
						$nombreImpuesto = 'IVA';
					
					if($codigoImpuestoSAT != '')
					{
						$impuestosTemporal[] = array
						(
							'id'				=> $key,
							'rate_id'			=> $tax->get_rate_id(),
							'is_compound'		=> $tax->is_compound(),
							'label'				=> $nombreImpuesto,
							'amount'			=> $amount,
							'rate'				=> $tasa,
							'formatted_amount'	=> wc_price(wc_round_tax_total($amount), array('currency' => $pedido->get_currency())),
							'codigoImpuestoSAT' => $codigoImpuestoSAT,
							'impuesto' 			=> $nombreImpuesto,
							'naturaleza' 		=> $naturaleza,
							'tasaPorcentaje' 	=> $tasa,
							'tasa' 				=> number_format(($tasa / 100), 6, '.', ''),
							'importe' 			=> $amount,
						);
					}
				}
				
				/*if(apply_filters('woocommerce_order_hide_zero_taxes', true))
				{
					$amounts = array_filter(wp_list_pluck($impuestosTemporal, 'amount'));
					$impuestosTemporal = array_intersect_key($impuestosTemporal, $amounts);
				}

				$impuestosTemporal = apply_filters('woocommerce_order_get_tax_totals', $impuestosTemporal, $pedido);*/
				
				$impuestosGenerales = array();
				
				for($i = 0; $i < count($impuestosTemporal); $i++)
				{
					$labelImpuesto = strtoupper($impuestosTemporal[$i]['label']);
					$rateImpuesto = $impuestosTemporal[$i]['tasaPorcentaje'];
					
					$existe = false;
					$datosPedido['flujoImpuestos'] .= '|[IMPUESTO_AntesRevision]|'.$labelImpuesto.'|'.$rateImpuesto.'|'.$impuestosTemporal[$i]['importe'].'|'.$impuestosTemporal[$i]['id'].'|'.$impuestosTemporal[$i]['rate_id'];
					for($j = 0; $j < count($impuestosGenerales); $j++)
					{
						$labelImpuestoGeneral = strtoupper($impuestosGenerales[$j]['label']);
						$rateImpuestoGeneral = $impuestosGenerales[$j]['tasaPorcentaje'];
						
						$datosPedido['flujoImpuestos'] .= '|[BUSQUEDA_IMP]|'.$labelImpuesto.'|'.$rateImpuesto.'|'.$impuestosTemporal[$i]['importe'].'|'.$impuestosTemporal[$i]['id'].'|'.$impuestosTemporal[$i]['rate_id'].'|[BUSQUEDA_IMP_GRAL]|'.$labelImpuestoGeneral.'|'.$rateImpuestoGeneral.'|'.$impuestosGenerales[$j]['importe'].'|'.$impuestosGenerales[$j]['id'].'|'.$impuestosGenerales[$j]['rate_id'];
						
						if($labelImpuesto === $labelImpuestoGeneral && $rateImpuesto === $rateImpuestoGeneral)
						{
							$importeImpuestoGeneral = $impuestosGenerales[$j]['importe'];
							$importeImpuesto = $impuestosTemporal[$i]['importe'];
							
							$impuestosGenerales[$j]['importe'] = $importeImpuestoGeneral + $importeImpuesto;
							$datosPedido['flujoImpuestos'] .= '|[SUMA_IMPORTE]|'.$impuestosGenerales[$j]['importe'].'|[OPERACION SUMA]'.$importeImpuesto.' (IMP) + '.$importeImpuestoGeneral.' (IMP_GRAL)';
							$existe = true;
							break;
						}
					}
					
					if($existe === false)
					{
						$datosPedido['flujoImpuestos'] .= '|[IMPUESTO_PrimeraVez]|'.$impuestosTemporal[$i]['label'].'|'.$impuestosTemporal[$i]['tasaPorcentaje'].'|'.$impuestosTemporal[$i]['importe'].'|'.$impuestosTemporal[$i]['id'].'|'.$impuestosTemporal[$i]['rate_id'];
					
						$impuestosGenerales[] = array
						(
							'id'				=> $impuestosTemporal[$i]['id'],
							'rate_id'			=> $impuestosTemporal[$i]['rate_id'],
							'is_compound'		=> $impuestosTemporal[$i]['is_compound'],
							'label'				=> $impuestosTemporal[$i]['label'],
							'codigoImpuestoSAT' => $impuestosTemporal[$i]['codigoImpuestoSAT'],
							'impuesto' 			=> $impuestosTemporal[$i]['impuesto'],
							'naturaleza' 		=> $impuestosTemporal[$i]['naturaleza'],
							'tasaPorcentaje' 	=> $impuestosTemporal[$i]['tasaPorcentaje'],
							'tasa' 				=> $impuestosTemporal[$i]['tasa'],
							'importe' 			=> $impuestosTemporal[$i]['importe']
						);
					}
				}
				
				/*foreach($impuestosTemporal as $impuesto)
				{
					$labelImpuesto = strtoupper($impuesto['label']);
					$rateImpuesto = $impuesto['tasaPorcentaje'];
					
					$existe = false;
					$datosPedido['flujoImpuestos'] .= '|[IMPUESTO_AntesRevision]|'.$labelImpuesto.'|'.$rateImpuesto.'|'.$impuesto['importe'].'|'.$impuesto['id'].'|'.$impuesto['rate_id'];
					foreach($impuestosGenerales as &$impuestoGeneral)
					{
						$labelImpuestoGeneral = strtoupper($impuestoGeneral['label']);
						$rateImpuestoGeneral = $impuestoGeneral['tasaPorcentaje'];
						
						$datosPedido['flujoImpuestos'] .= '|[BUSQUEDA_IMP]|'.$labelImpuesto.'|'.$rateImpuesto.'|'.$impuesto['importe'].'|'.$impuesto['id'].'|'.$impuesto['rate_id'].'|[BUSQUEDA_IMP_GRAL]|'.$labelImpuestoGeneral.'|'.$rateImpuestoGeneral.'|'.$impuestoGeneral['importe'].'|'.$impuestoGeneral['id'].'|'.$impuestoGeneral['rate_id'];
						
						if($labelImpuesto === $labelImpuestoGeneral && $rateImpuesto === $rateImpuestoGeneral)
						{
							$importeImpuestoGeneral = $impuestoGeneral['importe'];
							$importeImpuesto = $impuesto['importe'];
							
							$impuestoGeneral['importe'] = $importeImpuestoGeneral + $importeImpuesto;
							$datosPedido['flujoImpuestos'] .= '|[SUMA_IMPORTE]|'.$impuestoGeneral['importe'].'|[OPERACION SUMA]'.$importeImpuesto.' (IMP) + '.$importeImpuestoGeneral.' (IMP_GRAL)';
							$existe = true;
							break;
						}
					}
					
					if($existe === false)
					{
						$datosPedido['flujoImpuestos'] .= '|[IMPUESTO_PrimeraVez]|'.$impuesto['label'].'|'.$impuesto['tasaPorcentaje'].'|'.$impuesto['importe'].'|'.$impuesto['id'].'|'.$impuesto['rate_id'];
					
						$impuestosGenerales[] = array
						(
							'id'				=> $impuesto['id'],
							'rate_id'			=> $impuesto['rate_id'],
							'is_compound'		=> $impuesto['is_compound'],
							'label'				=> $impuesto['label'],
							'codigoImpuestoSAT' => $impuesto['codigoImpuestoSAT'],
							'impuesto' 			=> $impuesto['impuesto'],
							'naturaleza' 		=> $impuesto['naturaleza'],
							'tasaPorcentaje' 	=> $impuesto['tasaPorcentaje'],
							'tasa' 				=> $impuesto['tasa'],
							'importe' 			=> $impuesto['importe']
						);
					}
				}*/
				$datosPedido['impuestos'] = $impuestosGenerales;
				// add line items
				foreach( $pedido->get_items() as $item_id => $item)
				{
					$product = $pedido->get_product_from_item($item);
					//$impuestos = $item['taxes']['total'];
					//$impuestos = array_values($impuestos);
					$impuestos = $item->get_taxes();
					
					$arregloImpuestos = array();
					
					foreach($impuestos['total'] as $rate_idParticular => $importeParticular)
					{
						$labelGeneral = '';
						$rateGeneral = '';
						
						foreach($datosPedido['impuestos'] as $impuestoGeneral)
						{
							$rate_idGeneral = $impuestoGeneral['rate_id'];
							
							if($rate_idParticular == $rate_idGeneral && is_numeric($importeParticular))
							{							
								$idGeneral = $impuestoGeneral['id'];
								$labelGeneral = strtoupper($impuestoGeneral['label']);
								$rateGeneral = $impuestoGeneral['tasaPorcentaje'];
								$codigoImpuestoSAT = '';
								
								if($labelGeneral == 'IVA' || $labelGeneral == 'IVA RETENIDO' || $labelGeneral == 'IVA 0' ||
									$labelGeneral == 'IVA 16' || $labelGeneral == 'IVA 8' || $labelGeneral == 'IVA EXENTO')
									$codigoImpuestoSAT = '002';
								else if($labelGeneral == 'IEPS' || $labelGeneral == 'IEPS RETENIDO')
									$codigoImpuestoSAT = '003';
								else if($labelGeneral == 'ISR')
									$codigoImpuestoSAT = '001';
								else if($labelGeneral == 'ISH')
									$codigoImpuestoSAT = 'ISH';
								else
									$codigoImpuestoSAT = '';
								
								if($labelGeneral == 'IVA 0' || $labelGeneral == 'IVA 16' || $labelGeneral == 'IVA 8')
									$labelGeneral = 'IVA';
								
								if($codigoImpuestoSAT != '')
								{
									$arregloImpuestos[] = array
									(
										'impuestoID' => $idGeneral,
										'tasaImpuestoID' => $rate_idGeneral,
										'codigoImpuestoSAT' => $codigoImpuestoSAT,
										'impuesto' => $labelGeneral,
										'tasaPorcentaje' => $rateGeneral,
										'tasa' => number_format(($rateGeneral / 100), 6, '.', ''),
										'importe' => $importeParticular
									);
								}
								
								break;
							}
						}
					}
					
					/*$iva = '';
					$ieps = '';
					$ivaretenido = '';
					$iepsretenido = '';
					$isrretenido = '';
					
					if(count($impuestos) > 0)
						$iva = $impuestos[0];
					if(count($impuestos) > 1)
						$ieps = $impuestos[1];
					if(count($impuestos) > 2)
						$ivaretenido = $impuestos[2];
					if(count($impuestos) > 3)
						$iepsretenido = $impuestos[3];
					if(count($impuestos) > 4)
						$isrretenido = $impuestos[4];*/
					
					$claveServicio = '';
					$claveUnidad = '';
					$unidadMedida = '';
					$claveProducto = '';
					$noPedimento = '';
					$obtejoImpuesto = '';
					
					$claveServicio = array_shift( wc_get_product_terms( $product->id, 'pa_clave_servicio', array( 'fields' => 'names' ) ) );
					$claveUnidad = array_shift( wc_get_product_terms( $product->id, 'pa_clave_unidad', array( 'fields' => 'names' ) ) );
					$unidadMedida = array_shift( wc_get_product_terms( $product->id, 'pa_unidad_medida', array( 'fields' => 'names' ) ) );
					$claveProducto = array_shift( wc_get_product_terms( $product->id, 'pa_clave_identificacion', array( 'fields' => 'names' ) ) );
					$noPedimento = array_shift( wc_get_product_terms( $product->id, 'pa_numero_pedimento', array( 'fields' => 'names' ) ) );
					$obtejoImpuesto = array_shift( wc_get_product_terms( $product->id, 'pa_objeto_impuesto', array( 'fields' => 'names' ) ) );
					
					if($claveServicio == null)
						$claveServicio = '';
					if($claveUnidad == null)
						$claveUnidad = '';
					if($unidadMedida == null)
						$unidadMedida = '';
					if($claveProducto == null)
						$claveProducto = '';
					if($noPedimento == null)
						$noPedimento = '';
					if($obtejoImpuesto == null)
						$obtejoImpuesto = '';
					
					/*
					//AQUI PUEDES FORZAR A UTILIZAR SIEMPRE PARA TODOS LOS PRODUCTOS LOS DATOS QUE DESEES PARA LAS SIGUIENTES VARIABLES.
					$claveServicio = '';
					$claveUnidad = '';
					$unidadMedida = '';
					$claveProducto = '';
					$noPedimento = '';
					$obtejoImpuesto = '';
					*/
					
					$item_data = $item->get_data();
					
					$datosPedido['line_items'][] = array
					(
						'id'         => $item_id,
						'importe2'    => wc_format_decimal( $pedido->get_line_subtotal( $item ), $precision_decimal ),
						'subtotal2'   => wc_format_decimal( $pedido->get_item_subtotal( $item ), $precision_decimal ),
						'importe'    => $item_data['subtotal'],//wc_format_decimal( $pedido->get_line_subtotal( $item ), $precision_decimal ),
						'subtotal'   => $item_data['subtotal'],//wc_format_decimal( $pedido->get_item_subtotal( $item ), $precision_decimal ),
						'descuento'  => '',
						'total'      => $item_data['total'],//wc_format_decimal( $pedido->get_line_total( $item ), $precision_decimal ),
						'total_tax'  => $item_data['total_tax'],//wc_format_decimal( $pedido->get_item_tax( $item ), $precision_decimal ),
						'price'      => wc_format_decimal( $pedido->get_item_total( $item ), $precision_decimal ),
						'meta'       => array(
							'item_total' => wc_format_decimal( $pedido->get_item_total( $item ), $precision_decimal ),
							'line_tax'   => wc_format_decimal( $pedido->get_line_tax( $item ), $precision_decimal ),
							'item_tax'   => wc_format_decimal( $pedido->get_item_tax( $item ), $precision_decimal ),
						),
						/*'IVA'  => $iva,
						'IEPS'  => $ieps,
						'IVA_RETENIDO'  => $ivaretenido,
						'IEPS_RETENIDO'  => $iepsretenido,
						'ISR_RETENIDO'  => $isrretenido,*/
						'quantity'   => wc_format_decimal( $item['qty'], $precision_decimal ),//(int) $item['qty'],
						'tax_class'  => ( ! empty( $item['tax_class'] ) ) ? $item['tax_class'] : null,
						'name'       => $item['name'],
						'product_id' => ( isset( $product->variation_id ) ) ? $product->variation_id : $product->id,
						'sku'        => is_object( $product ) ? $product->get_sku() : null,
						'clave_servicio' => $claveServicio,
						'clave_unidad' => $claveUnidad,
						'unidad_medida' => $unidadMedida,
						'clave_producto' => $claveProducto,
						'numero_pedimento' => $noPedimento,
						'objeto_impuesto' => $obtejoImpuesto,
						'impuestos' => $arregloImpuestos,
						'tipoConcepto' => 'articulo'
					);
				}

				$cuenta = RealVirtualWooCommerceCuenta::cuentaEntidad();
				$configuracion = RealVirtualWooCommerceConfiguracion::configuracionEntidad();
	
				// add shipping as a product
				foreach($pedido->get_items('shipping') as $shipping_key => $shipping_item)
				{
					if($shipping_item['method_id'] != 'free_shipping' && (wc_format_decimal($shipping_item['cost'], $precision_decimal)) > 0)
					{
						/*$impuestos = $shipping_item['taxes']['total'];
						$impuestos = array_values($impuestos);
						$iva = '';
						$ieps = '';
						$ivaretenido = '';
						$iepsretenido = '';
						$isrretenido = '';
						
						if(count($impuestos) > 0)
							$iva = $impuestos[0];
						if(count($impuestos) > 1)
							$ieps = $impuestos[1];
						if(count($impuestos) > 2)
							$ivaretenido = $impuestos[2];
						if(count($impuestos) > 3)
							$iepsretenido = $impuestos[3];
						if(count($impuestos) > 4)
							$isrretenido = $impuestos[4];*/
						
						$impuestos = $shipping_item->get_taxes();
					
						$arregloImpuestos = array();
						foreach($impuestos['total'] as $rate_idParticular => $importeParticular)
						{
							$labelGeneral = '';
							$rateGeneral = '';
							
							foreach($datosPedido['impuestos'] as $impuestoGeneral)
							{
								$rate_idGeneral = $impuestoGeneral['rate_id'];
								
								if($rate_idParticular == $rate_idGeneral && is_numeric($importeParticular))
								{							
									$idGeneral = $impuestoGeneral['id'];
									$labelGeneral = strtoupper($impuestoGeneral['label']);
									$rateGeneral = $impuestoGeneral['tasaPorcentaje'];
									$codigoImpuestoSAT = '';
									
									if($labelGeneral == 'IVA' || $labelGeneral == 'IVA RETENIDO' || $labelGeneral == 'IVA 0' || $labelGeneral == 'IVA 16' || $labelGeneral == 'IVA 8'  || $labelGeneral == 'IVA EXENTO')
										$codigoImpuestoSAT = '002';
									else if($labelGeneral == 'IEPS' || $labelGeneral == 'IEPS RETENIDO')
										$codigoImpuestoSAT = '003';
									else if($labelGeneral == 'ISR')
										$codigoImpuestoSAT = '001';
									else if($labelGeneral == 'ISH')
										$codigoImpuestoSAT = 'ISH';
									else
										$codigoImpuestoSAT = '';
									
									if($labelGeneral == 'IVA 0' || $labelGeneral == 'IVA 16' || $labelGeneral == 'IVA 8')
										$labelGeneral = 'IVA';
								
									$arregloImpuestos[] = array
									(
										'impuestoID' => $idGeneral,
										'tasaImpuestoID' => $rate_idGeneral,
										'codigoImpuestoSAT' => $codigoImpuestoSAT,
										'impuesto' => $labelGeneral,
										'tasaPorcentaje' => $rateGeneral,
										'tasa' => number_format(($rateGeneral / 100), 6, '.', ''),
										'importe' => $importeParticular
									);
									
									break;
								}
							}
						}
						
						$claveServicio = '';
						$claveUnidad = '';
						$unidadMedida = '';
						$claveProducto = '';
						$noPedimento = '';
						$obtejoImpuesto = '';
						
						$claveServicio = array_shift( wc_get_product_terms( $product->id, 'pa_clave_servicio_shipping', array( 'fields' => 'names' ) ) );
						$claveUnidad = array_shift( wc_get_product_terms( $product->id, 'pa_clave_unidad_shipping', array( 'fields' => 'names' ) ) );
						$unidadMedida = array_shift( wc_get_product_terms( $product->id, 'pa_unidad_medida_shipping', array( 'fields' => 'names' ) ) );
						$claveProducto = array_shift( wc_get_product_terms( $product->id, 'pa_clave_identificacion_shipping', array( 'fields' => 'names' ) ) );
						$noPedimento = array_shift( wc_get_product_terms( $product->id, 'pa_numero_pedimento_shipping', array( 'fields' => 'names' ) ) );
						$obtejoImpuesto = array_shift( wc_get_product_terms( $product->id, 'pa_objeto_impuesto_shipping', array( 'fields' => 'names' ) ) );
					
						if($claveServicio == null)
							$claveServicio = '';
						if($claveUnidad == null)
							$claveUnidad = '';
						if($unidadMedida == null)
							$unidadMedida = '';
						if($claveProducto == null)
							$claveProducto = '';
						if($noPedimento == null)
							$noPedimento = '';
						if($obtejoImpuesto == null)
							$obtejoImpuesto = '';
					
						$valorUnitario = wc_format_decimal($shipping_item['cost'], $precision_decimal);
						$agregarConcepto = true;
						
						if($valorUnitario <= '0')
						{
							if($configuracion['conceptos_especiales_envio'] == 'no')
								$agregarConcepto = false;
						}
					
						if($agregarConcepto == true)
						{
							$datosPedido['line_items'][] = array
							(
								'id'         => $shipping_key,
								'importe2'   => wc_format_decimal($shipping_item['cost'], $precision_decimal),
								'subtotal2'   => wc_format_decimal($shipping_item['cost'], $precision_decimal),
								'importe'   => $shipping_item['cost'],//wc_format_decimal($shipping_item['cost'], $precision_decimal),
								'subtotal'   => $shipping_item['cost'],//wc_format_decimal($shipping_item['cost'], $precision_decimal),
								'descuento'  => '',
								'total'      => wc_format_decimal($shipping_item['cost'], $precision_decimal),
								'total_tax'  => round($pedido->order_shipping_tax, $precision_decimal),
								'price'      => $valorUnitario,
								/*'IVA'  => $iva,
								'IEPS'  => $ieps,
								'IVA_RETENIDO'  => $ivaretenido,
								'IEPS_RETENIDO'  => $iepsretenido,
								'ISR_RETENIDO'  => $isrretenido,*/
								'quantity'   => wc_format_decimal(1, $precision_decimal),
								'tax_class'  => null,
								'name'       => $shipping_item['name'],
								'product_id' =>$shipping_key,
								'sku'        => $shipping_item['method_id'],
								'clave_servicio' => $claveServicio,
								'clave_unidad' => $claveUnidad,
								'unidad_medida' => $unidadMedida,
								'clave_producto' => $claveProducto,
								'numero_pedimento' => $noPedimento,
								'objeto_impuesto' => $obtejoImpuesto,
								'impuestos_shipping' => $shipping_item['taxes'],
								'impuestos' => $arregloImpuestos,
								'tipoConcepto' => 'shipping'
							);
						}
					}
				}
				
				return (object)$datosPedido;
			}
			catch(Exception $e)
			{
				$datosPedido = array
				(
					'mensajeError'      => $e->getMessage()
				);
				
				return (object)$datosPedido;
			}
		}
		
		static function obtenerCFDIID($idPedido, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			global $wp_version;
			
			$opcion = 'ObtenerCFDIID';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'NUMERO_PEDIDO' => $idPedido,
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
		
		static function obtenerEstadoPedidosEmitidos($idsPedidos, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			$opcion = 'ObtenerEstadoPedidosEmitidos';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'NUMEROS_PEDIDOS' => $idsPedidos,
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
		
		static function obtenerPedidos($fechaDesde, $fechaHasta, $estadoOrden, $metodoPagoOrden, $numerosPedidosExcluir, $precision_decimal, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma, $versionCFDI)
		{
			if(!isset($precision_decimal))
				$precision_decimal = '2';
				
			try
			{
				$date1 = new DateTime($fechaDesde);
				$date2 = new DateTime($fechaHasta);
				$diff = $date1->diff($date2);

				$fecha_desde = strtotime($fechaDesde);
				$fecha_hasta = strtotime($fechaHasta);

				if($fecha_desde > $fecha_hasta)
				{
					$datosPedido = array
					(
						'success' => false,
						'message' => ($idioma == 'ES') ? 'La <b>Fecha Inicial</b> es mayor que la <b>Fecha Final</b>.' : 'The <b>Start Date</b> is greater than the <b>End Date</b>.'
					);
					
					return (object)$datosPedido;
				}
				
				$mes1 = $date1->format('m');
				$mes2 = $date2->format('m');
				
				if($mes1 != $mes2 && ($mes1 + 1) != $mes2)
				{
					$datosPedido = array
					(
						'success' => false,
						'message' => ($idioma == 'ES') ? 'El rango de tiempo a consultar a través de la <b>Fecha Inicial</b> y la <b>Fecha Final</b> debe corresponder a <b>un mismo mes</b> o a <b>un bimestre</b> de la siguiente lista:<br/><br/>Enero-Febrero.<br/>Marzo-Abril.<br/>Mayo-Junio.<br/>Julio-Agosto.<br/>Septiembre-Octubre.<br/>Noviembre-Diciembre.' : 'The time range to consult through the <b>Start Date</b> and the <b>End Date</b> must correspond to <b>the same month</b> or <b>a two-month period</b> from the following list:<br/><br/>January-February.<br/>March-April.<br/>May-June.<br/>July-August.<br/>September-October.<br/>November-December.'
					);
					
					return (object)$datosPedido;
				}
				
				$arrayPedidosExcluidos = explode(",", $numerosPedidosExcluir);
				
				$parametros = array(
					'limit'	=> -1,
					'date_created'=> strtotime($fechaDesde.' 00:00:00').'...'.strtotime($fechaHasta.' 23:59:59'),
					//'status' => $estadoOrden,
					'exclude' => $arrayPedidosExcluidos,
					//'payment_method_title' => 'Check payments',
					//'payment_method' => $metodoPagoOrden,
					'return' => 'ids',
					'orderby' => 'date',
					'order' => 'DESC'
					//'type'=> 'shop_order',
					//'prices_include_tax' => 'no'
					//'date_paid' => '2016-02-12',
					//'customer_id' => 12,
					//'customer' => 'woocommerce@woocommerce.com'
				);
				
				if($estadoOrden != '')
				{
					$parametros_estadoOrden = array(
						'status' => $estadoOrden
					);
					
					$parametros = array_merge((array)$parametros, (array)$parametros_estadoOrden); 
				}
				
				if($metodoPagoOrden != '')
				{
					$parametros_metodoPagoOrden = array(
						'payment_method' => $metodoPagoOrden
					);
					
					$parametros = array_merge((array)$parametros, (array)$parametros_metodoPagoOrden); 
				}
				
				$pedidos_ids = wc_get_orders($parametros);
				
				if(count($pedidos_ids) <= 0)
				{
					$datosPedido = array
					(
						'success' => false,
						'message' => ($idioma == 'ES') ? 'No se encontraron pedidos con el filtro de búsqueda especificado.' : 'No orders were found with the specified search filter.'
					);
					
					return (object)$datosPedido;
				}
				
				$estadoPedidosEmitidos = self::obtenerEstadoPedidosEmitidos(implode(",", $pedidos_ids), $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);

				if($estadoPedidosEmitidos->success == false)
				{
					$datosPedido = array
					(
						'success' => false,
						'message' => $estadoPedidosEmitidos->message
					);
					
					return (object)$datosPedido;
				}

				$pedidosFinales = array();
				$tablaEstadoPedidosEmitidos = $estadoPedidosEmitidos->data;
				
				foreach($pedidos_ids as $idPedido)
				{
					$estatusPedido = '0';
					
					foreach($tablaEstadoPedidosEmitidos as $estadoPedido)
					{
						if($idPedido == $estadoPedido->NoOrden)
						{
							$estatusPedido = $estadoPedido->Estatus;
							break;
						}
					}
					
					$pedido['NoOrden'] = $idPedido;
					$pedido['Estatus'] = $estatusPedido;
					$pedidosFinales[] = $pedido;
				}

				$pedidosHTML = '';
				$pedidos = array();
				
				$existeIVA = false;
				$existeIVARetenido = false;
				$existeIEPS = false;
				$existeIEPSRetenido = false;
				$existeISRRetenido = false;
				
				$total_pedidos = 0;
				$total_subtotal = 0;
				$total_iva = 0;
				$total_ieps = 0;
				$total_otros_impuestos = 0;
				$total_descuento = 0;
				$total_total = 0;
				
				$total_total_noFacturado = 0;
				$total_subtotal_noFacturado = 0;
				$total_descuento_noFacturado = 0;
				
			    foreach ($pedidosFinales as $pedidoFinal)
				{
					$advertencias = '';
					$pedido = new WC_Order($pedidoFinal['NoOrden']);
					$order_post = get_post($idPedido);
					
					$subtotal = 0;
					$total = 0;
					$total_tax = 0;
					$total_shipping = 0;
					$cart_tax = 0;
					$shipping_tax = 0;
					$total_discount = 0;
					
					if(is_numeric($pedido->get_subtotal()))
						$subtotal = $pedido->get_subtotal();
					
					if(is_numeric($pedido->get_total()))
						$total = $pedido->get_total();
				
					if(is_numeric($pedido->get_total_tax()))
						$total_tax = $pedido->get_total_tax();
					
					if(is_numeric($pedido->get_total_shipping()))
						$total_shipping = $pedido->get_total_shipping();
					
					if(is_numeric($pedido->get_cart_tax()))
						$cart_tax = $pedido->get_cart_tax();
				
					if(is_numeric($pedido->get_shipping_tax()))
						$shipping_tax = $pedido->get_shipping_tax();
					
					if(is_numeric($pedido->get_total_discount()))
						$total_discount = $pedido->get_total_discount();
				
					$idPedido = $pedido->id;
					$numeroOrden = $pedido->get_order_number();
					$fechaPedido = $pedido->get_date_created()->format('d/m/Y');
					$fechaCreacion = $order_post->post_date_gmt;
					$fechaModificacion = $order_post->post_modified_gmt;
					$fechaCompletado = $pedido->completed_date;
					$estadoPedido = $pedido->get_status();
					$metodoPagoPedido = $pedido->payment_method_title;
					$monedaPedido = $pedido->order_currency;
					$subtotal = wc_format_decimal($subtotal, $precision_decimal);
					$total = wc_format_decimal($total, $precision_decimal);
					$total_tax = wc_format_decimal($total_tax, $precision_decimal);
					$total_shipping = wc_format_decimal($total_shipping, $precision_decimal);
					$cart_tax = wc_format_decimal($cart_tax, $precision_decimal);
					$shipping_tax = wc_format_decimal($shipping_tax, $precision_decimal);
					$total_discount = wc_format_decimal($total_discount, $precision_decimal);
					$clienteNombre = $pedido->billing_first_name.' '.$pedido->billing_last_name;
					$emailCliente = $pedido->billing_email;
					$metodoPagoID = $pedido->payment_method;
					$metodoPagoDescripcion = $pedido->payment_method_title;
					
					$subtotal = $subtotal + $total_shipping;
					
					$total_subtotal += $subtotal;
					$total_descuento += $total_discount;
					$total_total += $total;
					
					$estadoCFDI = '0';
					$codigoEstadoCFDI = '0';
					
					if($pedidoFinal['Estatus'] == '0')
					{
						$estadoCFDI = ($idioma == 'ES') ? '<font size="2" color="#006ce1"><b>No Facturado</b></font>':'<font size="2" color="#006ce1"><b>Not Invoiced</b></font>';
						$codigoEstadoCFDI = '0';
						$total_total_noFacturado += $total;
						$total_subtotal_noFacturado += $subtotal;
						$total_descuento_noFacturado += $total_discount;
					}
					else if($pedidoFinal['Estatus'] == '1')
					{
						$estadoCFDI = ($idioma == 'ES') ? '<font size="2" color="#039500">Vigente</font>':'<font size="2" color="#039500">Current</font>';
						$codigoEstadoCFDI = '1';
					}
					else if($pedidoFinal['Estatus'] == '2' || $pedidoFinal['Estatus'] == '3')
					{
						$estadoCFDI = ($idioma == 'ES') ? '<font size="2" color="#e80000">Cancelado</font>':'<font size="2" color="#e80000">Canceled</font>';
						$codigoEstadoCFDI = '1';
					}
					
					$impuestosPedido = array();
					
					foreach($pedido->get_items('tax') as $key => $tax)
					{
						$amount = (float) $tax->get_tax_total() + (float) $tax->get_shipping_tax_total();
						$tasa = WC_Tax::get_rate_percent($tax->get_rate_id());
						$tasa = str_replace("%", '', $tasa);
						
						$nombreImpuesto = utf8_encode(strtoupper($tax->get_label()));
						
						if($nombreImpuesto == 'TASA CERO')
						{
							$nombreImpuesto = 'IVA';
						}
						else if($nombreImpuesto == 'tasa cero')
						{
							$nombreImpuesto = 'IVA';
						}
						else if($nombreImpuesto == 'VAT')
						{
							$nombreImpuesto = 'IVA';
						}
						
						$impuestosPedido[] = array
						(
							//'id'				=> $key,
							//'rate_id'			=> $tax->get_rate_id(),
							//'is_compound'		=> $tax->is_compound(),
							'label'				=> $nombreImpuesto,
							'amount'			=> utf8_encode(number_format($amount, $precision_decimal, '.', '')),
							'rate'				=> utf8_encode(number_format(($tasa / 100), 6, '.', ''))//,
							//'formatted_amount'	=> wc_price(wc_round_tax_total($amount), array('currency' => $pedido->get_currency()))
						);
					}
					
					/*if(apply_filters('woocommerce_order_hide_zero_taxes', true))
					{
						$amounts = array_filter(wp_list_pluck($impuestosPedido, 'amount'));
						$impuestosPedido = array_intersect_key($impuestosPedido, $amounts);
					}
					
					$impuestosPedido = apply_filters('woocommerce_order_get_tax_totals', $impuestosPedido, $pedido);
					*/
					$iva_importe = '';
					$ieps_importe = '';
					//$ivaretenido_importe = '';
					//$iepsretenido_importe = '';
					//$isrretenido_importe = '';
					
					$iva_tasa = '';
					$ieps_tasa = '';
					//$ivaretenido_tasa = 0;
					//$iepsretenido_tasa = 0;
					//$isrretenido_tasa = 0;
					
					//$otrosImpuestos = '';
					
					$leyendaIVA = '';
					$leyendaIEPS = '';
					$impuestoAntiguo = false;
					
					for($i = 0; $i < count($impuestosPedido); $i++)
					{
						$impuesto = $impuestosPedido[$i];
						$impuesto['label'] = strtoupper($impuesto['label']);
						if($impuesto['label'] == 'IVA' || $impuesto['label'] == 'VAT')
						{
							$iva_importe = $impuesto['amount'];
							$iva_tasa = $impuesto['rate'];
							$existeIVA = true;
							$total_iva += $iva_importe;
							
							if($iva_importe != '')
							{
								if($iva_tasa != '')
								{
									if($leyendaIVA != '')
										$leyendaIVA .= ', ';
									
									$leyendaIVA .= '<b>'.number_format(($iva_tasa * 100), 0).'%</b> - '.'$'.number_format($iva_importe, $precision_decimal);
								}
							}
						}
						else if($impuesto['label'] == 'IEPS')
						{
							$ieps_importe = $impuesto['amount'];
							$ieps_tasa = $impuesto['rate'];
							$existeIEPS = true;
							$total_ieps += $ieps_importe;
							
							if($ieps_importe != '')
							{
								if($ieps_tasa != '')
								{
									if($leyendaIEPS != '')
										$leyendaIEPS .= ', ';
									
									$leyendaIEPS .= '<b>'.number_format(($ieps_tasa * 100), 0).'%</b> - '.'$'.number_format($ieps_importe, $precision_decimal);
								}
							}
						}
						/*else if($impuesto['label'] == 'IVA Ret' || $impuesto['label'] == 'IVA Ret.' || $impuesto['label'] == 'IVA Retenido')
						{
							$ivaretenido_importe = $impuesto['amount'];
							$ivaretenido_tasa = $impuesto['rate'];
							$existeIVARetenido = true;
							$otrosImpuestos .= $impuesto['label'].' = $'.number_format($ivaretenido_importe, $precision_decimal);
							$total_otros_impuestos += $ivaretenido_importe;
						}
						else if($impuesto['label'] == 'IEPS Ret' || $impuesto['label'] == 'IEPS Ret.' || $impuesto['label'] == 'IEPS Retenido')
						{
							$iepsretenido_importe = $impuesto['amount'];
							$iepsretenido_tasa = $impuesto['rate'];
							$existeIEPSRetenido = true;
							$otrosImpuestos .= $impuesto['label'].' = $'.number_format($iepsretenido_importe, $precision_decimal);
							$total_otros_impuestos += $iepsretenido_importe;
						}
						else if($impuesto['label'] == 'ISR')
						{
							$isrretenido_importe = $impuesto['amount'];
							$isrretenido_tasa = $impuesto['rate'];
							$existeISRRetenido = true;
							$otrosImpuestos .= $impuesto['label'].' = $'.number_format($isrretenido_importe, $precision_decimal);
							$total_otros_impuestos += $isrretenido_importe;
						}*/
						else
						{
							//$advertencias .= 'El impuesto "'.$impuesto['label'].'" con importe '.$impuesto['amount'].' no se reconoce como un impuesto valido y no será considerado en la factura global.';
							//$advertencias .= '</br>';
							
							$advertencias .= '<div class="tooltip left"><font size="2" color="#d80000"><b>Ver más</b></font>
							  <span class="tiptext">El impuesto "'.$impuesto['label'].'" con importe '.$impuesto['amount'].' de este pedido no se reconoce como un impuesto válido y no será considerado en la factura global. Las leyendas de impuesto válidas son <b>IVA</b> e <b>IEPS</b>. Para ver más detalles sobre la compatibilidad de impuestos con el plugin vaya a la sección <b>Configuración > Impuestos</b></span>
							</div>';
							
							$impuestoAntiguo = true;
						}
					}
					
					if($estadoPedido == 'pending')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#947c41'>Pendiente de pago</font>":"<font size='2' color='#947c41'>Pending</font>";
					if($estadoPedido == 'processing')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#004c94'>Procesando</font>":"<font size='2' color='#004c94'>Processing</font>";
					if($estadoPedido == 'on-hold')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#cf8a00'>En espera</font>":"<font size='2' color='#cf8a00'>On hold</font>";
					if($estadoPedido == 'completed')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#5b841b'>Completado</font>":"<font size='2' color='#5b841b'>Completed</font>";
					if($estadoPedido == 'canceled' || $estadoPedido == 'cancelled')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#828282'>Cancelado</font>":"<font size='2' color='#828282'>Canceled</font>";
					if($estadoPedido == 'refunded')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#828282'>Reembolsado</font>":"<font size='2' color='#828282'>Refunded</font>";
					if($estadoPedido == 'failed')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#b90000'>Fallido</font>":"<font size='2' color='#b90000'>Failed</font>";
					
					$metodoPagoPedido = strlen($metodoPagoPedido) > 50 ? substr($metodoPagoPedido, 0, 50)."..." : $metodoPagoPedido;
					
					$objetoImpuesto = '01';
					
					if($leyendaIVA != '' || $leyendaIEPS != '' || $impuestoAntiguo == true)
						$objetoImpuesto = '02';
					
					$pedidosHTML .= '<tr>
						<td style="display:none;">'.$idPedido.'</td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2"><b>#'.$numeroOrden.'</b> '.$clienteNombre.'</font></td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2">'.$fechaPedido.'</font></td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><b>'.$estadoPedido.'</b></td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;">'.$metodoPagoPedido.'</td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2">'.$monedaPedido.'</font></td>
						<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">$'.number_format($subtotal, $precision_decimal).'</font></td>
						<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">$'.number_format($total_discount, $precision_decimal).'</font></td>';
						
					if($versionCFDI == '4.0')
						$pedidosHTML .= '<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;">'.$objetoImpuesto.'</td>';
						
					$pedidosHTML .=	'<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">'.$leyendaIVA.'</font></td>
						<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">'.$leyendaIEPS.'</font></td>
						<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">$'.number_format($total, $precision_decimal).'</font></td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2">'.$estadoCFDI.'</font></td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2">'.$advertencias.'</font></td>
						</tr>';
						
					$pedidos[] = array
					(
						"numeroOrden" => utf8_encode($numeroOrden),
						"subtotal" => utf8_encode($subtotal),
						"descuento" => utf8_encode($total_discount),
						"total" => utf8_encode($total),
						"impuestosPedido" => $impuestosPedido,
						"clienteNombre" => utf8_encode($clienteNombre),
						"emailCliente" => utf8_encode($emailCliente),
						"fechaCreacion" => utf8_encode($fechaCreacion),
						"fechaModificacion" => utf8_encode($fechaModificacion),
						"fechaCompletado" => utf8_encode($fechaCompletado),
						"estadoPedido" => utf8_encode($estadoPedido),
						"monedaPedido" => utf8_encode($monedaPedido),
						"total_tax" => utf8_encode($total_tax),
						"total_shipping" => utf8_encode($total_shipping),
						"cart_tax" => utf8_encode($cart_tax),
						"shipping_tax" => utf8_encode($shipping_tax),
						"codigoEstadoCFDI" => utf8_encode($codigoEstadoCFDI),
						"objetoImpuesto" => utf8_encode($objetoImpuesto)
					);
					
					$total_pedidos++;
				}

				$pedidosJSON = json_encode($pedidos);
				//$pedidos = json_decode($pedidos);

				$datosPedido = array
				(
					'success' => true,
					'pedidosHTML' => $pedidosHTML,
					'pedidosJSON' => $pedidosJSON,
					'pedidos' => $pedidos,
					'pedidos_total_subtotal' => number_format($total_subtotal, $precision_decimal, '.', ''),
					'pedidos_total_descuento' => number_format($total_descuento, $precision_decimal, '.', ''),
					'pedidos_total_iva' => number_format($total_iva, $precision_decimal, '.', ''),
					'pedidos_total_ieps' => number_format($total_ieps, $precision_decimal, '.', ''),
					'pedidos_total_total' => number_format($total_total, $precision_decimal, '.', ''),
					'total_pedidos' => $total_pedidos,
					'total_subtotal' => number_format($total_subtotal, $precision_decimal),
					'total_descuento' => number_format($total_descuento, $precision_decimal),
					'total_iva' => number_format($total_iva, $precision_decimal),
					'total_ieps' => number_format($total_ieps, $precision_decimal),
					//'total_otros_impuestos' => number_format($total_otros_impuestos, $precision_decimal),
					'total_total' => number_format($total_total, $precision_decimal),
					'fechaDesde' => $fechaDesde,
					'fechaHasta' => $fechaHasta,
					'estadoOrden' => $estadoOrden,
					'metodoPagoOrden' => $metodoPagoOrden,
					'numerosPedidosExcluir' => $numerosPedidosExcluir,
					'total_subtotal_noFacturado' => number_format($total_subtotal_noFacturado, $precision_decimal, '.', ''),
					'total_descuento_noFacturado' => number_format($total_descuento_noFacturado, $precision_decimal, '.', ''),
					'total_total_noFacturado' => number_format($total_total_noFacturado, $precision_decimal, '.', '')
				);

				return (object)$datosPedido;
			}
			catch(Exception $e)
			{
				$datosPedido = array
				(
					'success' => false,
					'message' => $e->getMessage()
				);
				
				return (object)$datosPedido;
			}
		}
		
		static function obtenerPedidosCSV($csv, $precision_decimal, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma, $versionCFDI)
		{
			if(!isset($precision_decimal))
				$precision_decimal = '2';
				
			try
			{
				$csv = str_replace("\r","",$csv);
				$csv = str_replace("\n","|",$csv);
				$csv = str_split($csv);
				
				$texto = '';
				$elemento = 0;
				$pedidos_ids = '';
				$idPedido = '';
				$monto = '';
				$subtotal = '';
				$iva = '';
				$tasaiva = '';
				$ieps = '';
				$tasaieps = '';
				
				$pedidosFinales = array();
				
				for($i = 0; $i < count($csv); $i++)
				{
					if($csv[$i] == ',')
					{
						if($elemento == 0)
						{
							$idPedido = $texto;
							
							if($pedidos_ids != '')
								$pedidos_ids .= ',';
							
							$pedidos_ids .= $idPedido;
						}
						
						if($elemento == 1)
							$monto = $texto;
						
						if($elemento == 2)
							$subtotal = $texto;
						
						if($elemento == 3)
							$iva = $texto;
						
						if($elemento == 4)
							$tasaiva = $texto;
						
						if($elemento == 5)
							$ieps = $texto;
						
						if($elemento == 6)
							$tasaieps = $texto;
						
						$texto = '';
						$elemento++;
						
						continue;
					}
					
					if($csv[$i] == '|' || $i == count($csv) - 1)
					{
						if($i == count($csv) - 1)
							$texto .= $csv[$i];
						
						if($elemento == 0)
						{
							$idPedido = $texto;
							
							if($pedidos_ids != '')
								$pedidos_ids .= ',';
							
							$pedidos_ids .= $idPedido;
						}
						
						if($elemento == 1)
							$monto = $texto;
						
						if($elemento == 2)
							$subtotal = $texto;
						
						if($elemento == 3)
							$iva = $texto;
						
						if($elemento == 4)
							$tasaiva = $texto;
						
						if($elemento == 5)
							$ieps = $texto;
						
						if($elemento == 6)
							$tasaieps = $texto;
						
						$pedidoLinea['numeroOrden'] = $idPedido;
						$pedidoLinea['total'] = $monto;
						$pedidoLinea['subtotal'] = $subtotal;
						$pedidoLinea['estadoPedido'] = '-';
						$pedidoLinea['codigoEstadoCFDI'] = '0';
						$pedidoLinea['moneda'] = 'MXN';
						$pedidoLinea['IVA'] = $iva;
						$pedidoLinea['IEPS'] = $ieps;
						$pedidoLinea['IVADescripcion'] = ($iva != '') ? '<b>'.number_format($tasaiva, 0).'%</b> - '.'$'.number_format($iva, $precision_decimal) : '';
						$pedidoLinea['IEPSDescripcion'] = ($ieps != '') ? '<b>'.number_format($tasaieps, 0).'%</b> - '.'$'.number_format($ieps, $precision_decimal) : '';
						$pedidoLinea['impuestosPedido'] = array();
						
						if($iva != '')
						{
							$impuestos = array();
							
							$impuestos[] = array
							(
								'label' => 'IVA',
								'amount' => $iva,
								'rate' => utf8_encode(number_format(($tasaiva / 100), 6, '.', ''))
							);
							$pedidoLinea['impuestosPedido'] = $impuestos;
						}
						
						if($ieps != '')
						{
							$impuestos[] = array
							(
								'label' => 'IEPS',
								'amount' => $ieps,
								'rate' => utf8_encode(number_format(($tasaieps / 100), 6, '.', ''))
							);
							$pedidoLinea['impuestosPedido'] = $impuestos;
						}
						
						$pedidosFinales[] = $pedidoLinea;
						
						$texto = '';
						$elemento = 0;
						$idPedido = '';
						$monto = '';
						$subtotal = '';
						$iva = '';
						$tasaiva = '';
						$ieps = '';
						$tasaieps = '';
						
						continue;
					}
					
					$texto .= $csv[$i];
				}
				
				$estadoPedidosEmitidos = self::obtenerEstadoPedidosEmitidos($pedidos_ids, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);

				if($estadoPedidosEmitidos->success == false)
				{
					$datosPedido = array
					(
						'success' => false,
						'message' => 'Error al consultar si los pedidos ya fueron facturados previamente: '.$estadoPedidosEmitidos->message
					);
					
					return (object)$datosPedido;
				}

				$tablaEstadoPedidosEmitidos = $estadoPedidosEmitidos->data;

				for($i = 0; $i < count($pedidosFinales); $i++)
				{
					foreach($tablaEstadoPedidosEmitidos as $estadoPedido)
					{
						if($pedidosFinales[$i]['numeroOrden'] == $estadoPedido->NoOrden)
						{
							$pedidosFinales[$i]['codigoEstadoCFDI'] = $estadoPedido->Estatus;
							break;
						}
					}
				}

				$pedidosHTML = '';
				$pedidos = array();
				
				$existeIVA = false;
				$existeIVARetenido = false;
				$existeIEPS = false;
				$existeIEPSRetenido = false;
				$existeISRRetenido = false;
				
				$total_pedidos = 0;
				$total_subtotal = 0;
				$total_iva = 0;
				$total_ieps = 0;
				$total_otros_impuestos = 0;
				$total_descuento = 0;
				$total_total = 0;
				
				$total_total_noFacturado = 0;
				$total_subtotal_noFacturado = 0;
				$total_descuento_noFacturado = 0;
				
				foreach ($pedidosFinales as $pedidoFinal)
				{
					$advertencias = '';
					
					try
					{
						$total_subtotal += $pedidoFinal['subtotal'];
						$total_descuento += 0;
						$total_total += $pedidoFinal['total'];
						
						$total_iva += $pedidoFinal['IVA'];
						$total_ieps += $pedidoFinal['IEPS'];
						
						$estadoCFDI = '0';
						
						if($pedidoFinal['codigoEstadoCFDI'] == '0')
						{
							$estadoCFDI = ($idioma == 'ES') ? '<font size="2" color="#006ce1"><b>No Facturado</b></font>':'<font size="2" color="#006ce1"><b>Not Invoiced</b></font>';
							
							$total_total_noFacturado += $pedidoFinal['total'];
							$total_subtotal_noFacturado += $pedidoFinal['subtotal'];
							$total_descuento_noFacturado += $total_discount;
						}
						else if($pedidoFinal['codigoEstadoCFDI'] == '1')
						{
							$estadoCFDI = ($idioma == 'ES') ? '<font size="2" color="#039500">Vigente</font>':'<font size="2" color="#039500">Current</font>';
						}
						else if($pedidoFinal['codigoEstadoCFDI'] == '2' || $pedidoFinal['codigoEstadoCFDI'] == '3')
						{
							$estadoCFDI = ($idioma == 'ES') ? '<font size="2" color="#e80000">Cancelado</font>':'<font size="2" color="#e80000">Canceled</font>';
						}
						
						$objetoImpuesto = '01';
					
						if($pedidoFinal['IVADescripcion'] != '' || $pedidoFinal['IEPSDescripcion'] != '')
							$objetoImpuesto = '02';
						
						$pedidosHTML .= '<tr>
							<td style="display:none;">'.$idPedido.'</td>
							<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2"><b>#'.$pedidoFinal['numeroOrden'].'</b></font></td>
							<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2">-</font></td>
							<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><b>-</b></td>
							<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;">-</td>
							<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2">MXN</font></td>
							<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">$'.number_format($pedidoFinal['subtotal'], $precision_decimal).'</font></td>
							<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">$'.number_format(0, $precision_decimal).'</font></td>';
						
						if($versionCFDI == '4.0')
							$pedidosHTML .= '<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;">'.$objetoImpuesto.'</td>';
												
							
						$pedidosHTML .=	'<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">'.$pedidoFinal['IVADescripcion'].'</font></td>
							<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">'.$pedidoFinal['IEPSDescripcion'].'</font></td>
							<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">$'.number_format($pedidoFinal['total'], $precision_decimal).'</font></td>
							<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2">'.$estadoCFDI.'</font></td>
							<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2"></font></td>
							</tr>';
							
						$pedidos[] = array
						(
							"numeroOrden" => utf8_encode($pedidoFinal['numeroOrden']),
							"subtotal" => utf8_encode($pedidoFinal['subtotal']),
							"descuento" => utf8_encode(0),
							"total" => utf8_encode($pedidoFinal['total']),
							"impuestosPedido" => $pedidoFinal['impuestosPedido'],
							"clienteNombre" => '',
							"emailCliente" => '',
							"fechaCreacion" => '',
							"fechaModificacion" => '',
							"fechaCompletado" => '',
							"estadoPedido" => '',
							"monedaPedido" => 'MXN',
							"total_tax" => 0,
							"total_shipping" => 0,
							"cart_tax" => 0,
							"shipping_tax" => 0,
							"codigoEstadoCFDI" => utf8_encode($pedidoFinal['codigoEstadoCFDI']),
							"objetoImpuesto" => utf8_encode($objetoImpuesto)
						);
					
						$total_pedidos++;
					}
					catch(Exception $e)
					{
						continue;
					}
				}

				$pedidosJSON = json_encode($pedidos);
				//$pedidos = json_decode($pedidos);

				$datosPedido = array
				(
					'success' => true,
					'pedidosHTML' => $pedidosHTML,
					'pedidosJSON' => $pedidosJSON,
					'pedidos' => $pedidos,
					'pedidos_total_subtotal' => number_format($total_subtotal, $precision_decimal, '.', ''),
					'pedidos_total_descuento' => number_format($total_descuento, $precision_decimal, '.', ''),
					'pedidos_total_iva' => number_format($total_iva, $precision_decimal, '.', ''),
					'pedidos_total_ieps' => number_format($total_ieps, $precision_decimal, '.', ''),
					'pedidos_total_total' => number_format($total_total, $precision_decimal, '.', ''),
					'total_pedidos' => $total_pedidos,
					'total_subtotal' => number_format($total_subtotal, $precision_decimal),
					'total_descuento' => number_format($total_descuento, $precision_decimal),
					'total_iva' => number_format($total_iva, $precision_decimal),
					'total_ieps' => number_format($total_ieps, $precision_decimal),
					//'total_otros_impuestos' => number_format($total_otros_impuestos, $precision_decimal),
					'total_total' => number_format($total_total, $precision_decimal),
					'fechaDesde' => '',
					'fechaHasta' => '',
					'estadoOrden' => '',
					'metodoPagoOrden' => '',
					'numerosPedidosExcluir' => '',
					'total_subtotal_noFacturado' => number_format($total_subtotal_noFacturado, $precision_decimal, '.', ''),
					'total_descuento_noFacturado' => number_format($total_descuento_noFacturado, $precision_decimal, '.', ''),
					'total_total_noFacturado' => number_format($total_total_noFacturado, $precision_decimal, '.', '')
				);

				return (object)$datosPedido;
			}
			catch(Exception $e)
			{
				$datosPedido = array
				(
					'success' => false,
					'message' => $e->getMessage()
				);
				
				return (object)$datosPedido;
			}
		}
		
		static function obtenerPedidoExterno($precision_decimal, $tipoConexion, $tipoSolicitud, $url, $numeroPedido,
				$nombreParametroNumeroPedido, $monto, $nombreParametroMonto, $valorParametroExtra1,
				$nombreParametroExtra1, $valorParametroExtra2, $nombreParametroExtra2, $idioma, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado)
		{
			$opcion = 'ObtenerPedidoExterno';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'precision_decimal' => $precision_decimal,
				'tipoConexion' => $tipoConexion,
				'tipoSolicitud' => $tipoSolicitud,
				'url' => $url,
				'nombreParametroNumeroPedido' => $nombreParametroNumeroPedido,
				'numeroPedido' => $numeroPedido,
				'monto' => $monto,
				'nombreParametroMonto' => $nombreParametroMonto,
				'valorParametroExtra1' => $valorParametroExtra1,
				'nombreParametroExtra1' => $nombreParametroExtra1,
				'valorParametroExtra2' => $valorParametroExtra2,
				'nombreParametroExtra2' => $nombreParametroExtra2,
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
				$datosPedido = array
				(
					'mensajeError' => $e->getMessage()
				);
				
				return json_decode($datosPedido);
			}
		}
		
		static function enviarPedidoServicioExterno(
		$rfcEmisor, $usuarioEmisor, $claveEmisor, $accion, $parametros, $idioma, $urlSistemaAsociado)
		{
			$opcion = 'EnviarPedidoServicioExterno';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'ACCION' => $accion,
				'PARAMETROS' => $parametros,
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
				$datosPedido = array
				(
					'mensajeError' => $e->getMessage()
				);
				
				return json_decode($datosPedido);
			}
		}
		
		static function enviarXMLPedidoTimbradoServicioExterno(
		$rfcEmisor, $usuarioEmisor, $claveEmisor, $parametros, $idioma, $urlSistemaAsociado)
		{
			$opcion = 'EnviarPedidoXMLServicioExterno';
			
			$parametros = array
			(
				'OPCION' => $opcion,
				'EMISOR_RFC' => $rfcEmisor,
				'EMISOR_USUARIO' => $usuarioEmisor,
				'EMISOR_CLAVE' => $claveEmisor,
				'PARAMETROS' => $parametros,
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
				$datosPedido = array
				(
					'mensajeError' => $e->getMessage()
				);
				
				return json_decode($datosPedido);
			}
		}
		
		static function obtenerPedidosFacturacion($fechaDesde, $fechaHasta, $estadoOrden, $precision_decimal, $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma)
		{
			if(!isset($precision_decimal))
				$precision_decimal = '2';
				
			try
			{
				$date1 = new DateTime($fechaDesde);
				$date2 = new DateTime($fechaHasta);
				$diff = $date1->diff($date2);

				$fecha_desde = strtotime($fechaDesde);
				$fecha_hasta = strtotime($fechaHasta);

				if($fecha_desde > $fecha_hasta)
				{
					$datosPedido = array
					(
						'success' => false,
						'message' => ($idioma == 'ES') ? 'La <b>Fecha Inicial</b> es mayor que la <b>Fecha Final</b>.' : 'The <b>Start Date</b> is greater than the <b>End Date</b>.'
					);
					
					return (object)$datosPedido;
				}
				
				$parametros = array(
					'limit'	=> -1,
					'date_created'=> strtotime($fechaDesde.' 00:00:00').'...'.strtotime($fechaHasta.' 23:59:59'),
					'return' => 'ids',
					'orderby' => 'date',
					'order' => 'DESC'
				);
				
				if($estadoOrden != '')
				{
					$parametros_estadoOrden = array(
						'status' => $estadoOrden
					);
					
					$parametros = array_merge((array)$parametros, (array)$parametros_estadoOrden); 
				}
				
				$pedidos_ids = wc_get_orders($parametros);
				
				if(count($pedidos_ids) <= 0)
				{
					$datosPedido = array
					(
						'success' => false,
						'message' => ($idioma == 'ES') ? 'No se encontraron pedidos con el filtro de búsqueda especificado.' : 'No orders were found with the specified search filter.'
					);
					
					return (object)$datosPedido;
				}
				
				$estadoPedidosEmitidos = self::obtenerEstadoPedidosEmitidos(implode(",", $pedidos_ids), $rfcEmisor, $usuarioEmisor, $claveEmisor, $urlSistemaAsociado, $idioma);

				if($estadoPedidosEmitidos->success == false)
				{
					$datosPedido = array
					(
						'success' => false,
						'message' => $estadoPedidosEmitidos->message
					);
					
					return (object)$datosPedido;
				}

				$pedidosFinales = array();
				$tablaEstadoPedidosEmitidos = $estadoPedidosEmitidos->data;
				
				foreach($pedidos_ids as $idPedido)
				{
					$estatusPedido = '0';
					
					foreach($tablaEstadoPedidosEmitidos as $estadoPedido)
					{
						if($idPedido == $estadoPedido->NoOrden)
						{
							$estatusPedido = $estadoPedido->Estatus;
							break;
						}
					}
					
					$pedido['NoOrden'] = $idPedido;
					$pedido['Estatus'] = $estatusPedido;
					$pedidosFinales[] = $pedido;
				}

				$pedidosHTML = '';
				$pedidos = array();
				
			    foreach ($pedidosFinales as $pedidoFinal)
				{
					$pedido = new WC_Order($pedidoFinal['NoOrden']);
					$order_post = get_post($idPedido);
					
					$total = 0;
					
					if(is_numeric($pedido->get_total()))
						$total = $pedido->get_total();
					
					$idPedido = $pedido->id;
					$numeroOrden = $pedido->get_order_number();
					$fechaPedido = $pedido->get_date_created()->format('d/m/Y');
					$estadoPedido = $pedido->get_status();
					$total = wc_format_decimal($total, $precision_decimal);
					$clienteNombre = $pedido->billing_first_name.' '.$pedido->billing_last_name;
					
					$estadoCFDI = '0';
					$codigoEstadoCFDI = '0';
					
					if($pedidoFinal['Estatus'] == '0')
					{
						$estadoCFDI = ($idioma == 'ES') ? '<font size="2" color="#006ce1"><b>No Facturado</b></font>':'<font size="2" color="#006ce1"><b>Not Invoiced</b></font>';
						$codigoEstadoCFDI = '0';
					}
					else if($pedidoFinal['Estatus'] == '1')
					{
						$estadoCFDI = ($idioma == 'ES') ? '<font size="2" color="#039500">Vigente</font>':'<font size="2" color="#039500">Current</font>';
						$codigoEstadoCFDI = '1';
					}
					else if($pedidoFinal['Estatus'] == '2' || $pedidoFinal['Estatus'] == '3')
					{
						$estadoCFDI = ($idioma == 'ES') ? '<font size="2" color="#e80000">Cancelado</font>':'<font size="2" color="#e80000">Canceled</font>';
						$codigoEstadoCFDI = '1';
					}
					
					if($estadoPedido == 'pending')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#947c41'>Pendiente de pago</font>":"<font size='2' color='#947c41'>Pending</font>";
					if($estadoPedido == 'processing')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#004c94'>Procesando</font>":"<font size='2' color='#004c94'>Processing</font>";
					if($estadoPedido == 'on-hold')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#cf8a00'>En espera</font>":"<font size='2' color='#cf8a00'>On hold</font>";
					if($estadoPedido == 'completed')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#5b841b'>Completado</font>":"<font size='2' color='#5b841b'>Completed</font>";
					if($estadoPedido == 'canceled' || $estadoPedido == 'cancelled')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#828282'>Cancelado</font>":"<font size='2' color='#828282'>Canceled</font>";
					if($estadoPedido == 'refunded')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#828282'>Reembolsado</font>":"<font size='2' color='#828282'>Refunded</font>";
					if($estadoPedido == 'failed')
						$estadoPedido = ($idioma == 'ES') ? "<font size='2' color='#b90000'>Fallido</font>":"<font size='2' color='#b90000'>Failed</font>";
					
					$pedidosHTML .= '<tr>
						<td style="display:none;">'.$numeroOrden.'</td>
						<td style="display:none;">'.$total.'</td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2"><b>#'.$numeroOrden.'</b> '.$clienteNombre.'</font></td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2">'.$fechaPedido.'</font></td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><b>'.$estadoPedido.'</b></td>
						<td class="columna" style="text-align:right; border-color: #004c91; padding: 5px;"><font size="2">$'.number_format($total, $precision_decimal).'</font></td>
						<td class="columna" style="text-align:left; border-color: #004c91; padding: 5px;"><font size="2">'.$estadoCFDI.'</font></td>
						</tr>';
						
					$pedidos[] = array
					(
						"numeroOrden" => utf8_encode($numeroOrden),
						"total" => utf8_encode($total),
						"clienteNombre" => utf8_encode($clienteNombre),
						"estadoPedido" => utf8_encode($estadoPedido),
						"codigoEstadoCFDI" => utf8_encode($codigoEstadoCFDI)
					);
				}

				$pedidosJSON = json_encode($pedidos);

				$datosPedido = array
				(
					'success' => true,
					'pedidosHTML' => $pedidosHTML,
					'pedidosJSON' => $pedidosJSON,
					'pedidos' => $pedidos
				);

				return (object)$datosPedido;
			}
			catch(Exception $e)
			{
				$datosPedido = array
				(
					'success' => false,
					'message' => $e->getMessage()
				);
				
				return (object)$datosPedido;
			}
		}
	}
?>