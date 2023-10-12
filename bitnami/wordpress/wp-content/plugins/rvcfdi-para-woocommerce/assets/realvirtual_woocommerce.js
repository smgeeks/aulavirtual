jQuery(document).ready( function($) 
{
	$('#paso_dos').hide();
	$('#paso_tres').hide();
	$('#paso_cuatro').hide();
	$('#paso_cinco').hide();
	
	try
	{
		var modalConfiguracion = document.getElementById('ventanaModalConfiguracion');
		var spanConfiguracion = document.getElementsByClassName('closeConfiguracion')[0];
		var botonConfiguracion = document.getElementById('botonModalConfiguracion');
		
		function mostrarVentanaConfiguracion(texto)
		{
			modalConfiguracion.style.display = "block";
			document.getElementById('tituloModalConfiguracion').innerHTML = (idiomaRVLFECFDI == 'ES') ? 'Aviso' : 'Notice';
			document.getElementById('textoModalConfiguracion').innerHTML = texto;
		}
		
		botonConfiguracion.onclick = function()
		{
			modalConfiguracion.style.display = "none";
			document.getElementById('tituloModalConfiguracion').innerHTML = '';
			document.getElementById('textoModalConfiguracion').innerHTML = '';
		}
		
		spanConfiguracion.onclick = function()
		{
			modalConfiguracion.style.display = "none";
			document.getElementById('tituloModalConfiguracion').innerHTML = '';
			document.getElementById('textoModalConfiguracion').innerHTML = '';
		}
	}
	catch(error)
	{
		
	}
	
	try
	{
		var modalCuenta = document.getElementById('ventanaModalCuenta');
		var spanCuenta = document.getElementsByClassName('closeCuenta')[0];
		var botonCuenta = document.getElementById('botonModalCuenta');
		
		function mostrarVentanaCuenta(texto, titulo)
		{
			modalCuenta.style.display = "block";
			document.getElementById('tituloModalCuenta').innerHTML = titulo;
			document.getElementById('textoModalCuenta').innerHTML = texto;
		}
		
		botonCuenta.onclick = function()
		{
			modalCuenta.style.display = "none";
			document.getElementById('tituloModalCuenta').innerHTML = '';
			document.getElementById('textoModalCuenta').innerHTML = '';
		}
		
		spanCuenta.onclick = function()
		{
			modalCuenta.style.display = "none";
			document.getElementById('tituloModalCuenta').innerHTML = '';
			document.getElementById('textoModalCuenta').innerHTML = '';
		}
	}
	catch(error)
	{
		
	}
	
	try
	{
		var modalCentroIntegracion = document.getElementById('ventanaModalCentroIntegracion');
		var spanCentroIntegracion = document.getElementById('closeCentroIntegracion');
		var botonCentroIntegracion = document.getElementById('botonModalCentroIntegracion');
		
		function mostrarVentanaCentroIntegracion(texto)
		{
			modalCentroIntegracion.style.display = "block";
			document.getElementById('tituloModalCentroIntegracion').innerHTML = (idiomaRVLFECFDI == 'ES') ? 'Aviso' : 'Notice';
			document.getElementById('textoModalCentroIntegracion').innerHTML = texto;
		}
		
		botonCentroIntegracion.onclick = function()
		{
			modalCentroIntegracion.style.display = "none";
			document.getElementById('tituloModalCentroIntegracion').innerHTML = '';
			document.getElementById('textoModalCentroIntegracion').innerHTML = '';
		}
		
		spanCentroIntegracion.onclick = function()
		{
			modalCentroIntegracion.style.display = "none";
			document.getElementById('tituloModalCentroIntegracion').innerHTML = '';
			document.getElementById('textoModalCentroIntegracion').innerHTML = '';
		}
	}
	catch(error)
	{
		
	}
	
	try
	{
		var modalConfiguracionBayer = document.getElementById('ventanaModalConfiguracionBayer');
		var spanConfiguracionBayer = document.getElementsByClassName('closeConfiguracionBayer')[0];
		var botonConfiguracionBayer = document.getElementById('botonModalConfiguracionBayer');
		
		function mostrarVentanaConfiguracionBayer(texto)
		{
			modalConfiguracionBayer.style.display = "block";
			document.getElementById('tituloModalConfiguracionBayer').innerHTML = (idiomaRVLFECFDI == 'ES') ? 'Aviso' : 'Notice';
			document.getElementById('textoModalConfiguracionBayer').innerHTML = texto;
		}
		
		botonConfiguracionBayer.onclick = function()
		{
			modalConfiguracionBayer.style.display = "none";
			document.getElementById('tituloModalConfiguracionBayer').innerHTML = '';
			document.getElementById('textoModalConfiguracionBayer').innerHTML = '';
		}
		
		spanConfiguracionBayer.onclick = function()
		{
			modalConfiguracionBayer.style.display = "none";
			document.getElementById('tituloModalConfiguracionBayer').innerHTML = '';
			document.getElementById('textoModalConfiguracionBayer').innerHTML = '';
		}
	}
	catch(error)
	{
		
	}
	
	try
	{
		var modalFacturacion = document.getElementById('ventanaModal');
		var spanFacturacion = document.getElementsByClassName('close')[0];
		var botonFacturacion = document.getElementById('botonModal');
		
		function mostrarVentana(texto)
		{
			modalFacturacion.style.display = "block";
			document.getElementById('tituloModal').innerHTML = (idiomaRVLFECFDI == 'ES') ? 'Aviso' : 'Notice';
			document.getElementById('textoModal').innerHTML = texto;
		}

		botonFacturacion.onclick = function()
		{
			modalFacturacion.style.display = "none";
			document.getElementById('tituloModal').innerHTML = '';
			document.getElementById('textoModal').innerHTML = '';
		}
		
		spanFacturacion.onclick = function()
		{
			modalFacturacion.style.display = "none";
			document.getElementById('tituloModal').innerHTML = '';
			document.getElementById('textoModal').innerHTML = '';
		}
	}
	catch(error)
	{
		
	}
	
	try
	{
		var modalDatosFiscalesReceptor = document.getElementById('fr_ventanaModal');
		var spanDatosFiscalesReceptor = document.getElementsByClassName('close')[0];
		var botonDatosFiscalesReceptor = document.getElementById('fr_botonModal');
		
		function mostrarVentanaReceptor(texto)
		{
			modalDatosFiscalesReceptor.style.display = "block";
			document.getElementById('fr_tituloModal').innerHTML = (idiomaRVLFECFDI == 'ES') ? 'Aviso' : 'Notice';
			document.getElementById('fr_textoModal').innerHTML = texto;
		}

		botonDatosFiscalesReceptor.onclick = function()
		{
			modalDatosFiscalesReceptor.style.display = "none";
			document.getElementById('fr_tituloModal').innerHTML = '';
			document.getElementById('fr_textoModal').innerHTML = '';
		}
		
		spanDatosFiscalesReceptor.onclick = function()
		{
			modalDatosFiscalesReceptor.style.display = "none";
			document.getElementById('fr_tituloModal').innerHTML = '';
			document.getElementById('fr_textoModal').innerHTML = '';
		}
	}
	catch(error)
	{
		
	}
	
	window.onclick = function(event)
	{
		if (event.target == modalFacturacion)
		{
			modalFacturacion.style.display = "none";
			document.getElementById('textoModal').innerHTML ='';
		}
		
		if (event.target == modalConfiguracion)
		{
			modalConfiguracion.style.display = "none";
			document.getElementById('textoModalConfiguracion').innerHTML ='';
		}
		
		if (event.target == modalCuenta)
		{
			modalCuenta.style.display = "none";
			document.getElementById('textoModalCuenta').innerHTML ='';
		}
		
		if (event.target == modalCentroIntegracion)
		{
			modalCentroIntegracion.style.display = "none";
			document.getElementById('textoModalCentroIntegracion').innerHTML ='';
		}
		
		if (event.target == modalDatosFiscalesReceptor)
		{
			modalDatosFiscalesReceptor.style.display = "none";
			document.getElementById('fr_textoModal').innerHTML ='';
		}
    }
	
	let plugin_dir_url = '';
	let urlSistemaAsociado = '';
	let numero_pedido = '';
	let datosPedido = '';
	let array_Conceptos = new Array();
	let subtotal = '';
	let descuento = '';
	let total = '';
	let array_ImpuestosFederales = new Array();
	let array_ImpuestosLocales = new Array();
	let xml = '';
	let CFDI_ID = '';
	let calle_receptor = '';
	let estado_receptor = '';
	let municipio_receptor = '';
	let pais_receptor = '';
	let codigoPostal_receptor = '';
	let mensajeErroresConceptos = '';
	let general_mostrarMensajeErrorCliente = '';
	let general_mensajeErrorCliente = '';
	let general_complementoCFDI = '';
	let general_lugarExpedicion = '';
	
	if($('#realvirtual_woocommerce_cuenta').length)
	{
		$('#realvirtual_woocommerce_cuenta_pruebas').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoCuenta').style.visibility = 'visible';
			
			var rfcPruebas = 'XIA190128J61';
			var usuarioPruebas = 'PRUEBASRV';
			var clavePruebas = 'ff68f569f16179394aa8d02ccbc761497e42beafc956ab17ab6c7c539649d392';
			
			if(document.getElementById('micuenta_sistema').value == 'LFECFDI')
			{
				rfcPruebas = 'XIA190128J61';
				usuarioPruebas = 'PRUEBASLFE';
				clavePruebas = 'ff68f569f16179394aa8d02ccbc761497e42beafc956ab17ab6c7c539649d392';
			}
			
			document.getElementById('rfc').value = rfcPruebas;
			document.getElementById('usuario').value = usuarioPruebas;
			document.getElementById('clave').value = clavePruebas;
			
			data =
			{
				action      						: 'realvirtual_woocommerce_guardar_cuenta',
				rfc   								: document.getElementById('rfc').value,
				usuario   							: document.getElementById('usuario').value,
				clave       						: document.getElementById('clave').value,
				idioma       						: idiomaRVLFECFDI
            }
			
			$.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoCuenta').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var EMISOR_RENOVACION = response.EMISOR_RENOVACION;
				var EMISOR_VIGENCIA = response.EMISOR_VIGENCIA;
				var EMISOR_ESTADO = response.EMISOR_ESTADO;
				var EMISOR_TIPO_USUARIO = response.EMISOR_TIPO_USUARIO;
				var sistema = response.sistema;
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaCuenta(((idiomaRVLFECFDI == 'ES') ? '<center>Ocurrió un error al guardar la cuenta:<br/><br/>' + message + '</center>' : '<center>There was an error saving your account:<br/><br/>' + message + '</center>'), ((idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice'));
				}
				else
				{
					var estadoEmisor = (idiomaRVLFECFDI == 'ES') ? '<font color="#63a55a"><b>CUENTA DE PRUEBAS UTILIZADA</b></font><br/><font color="#515151" size="2"><b>RFC</b> ' + rfcPruebas + '<br/><b>Usuario</b> ' + usuarioPruebas + '<br/><b>Clave Cifrada</b> ' + clavePruebas + '</font>' : '<font color="#63a55a"><b>TEST ACCOUNT USED</b></font><br/><font color="#515151" size="2"><b>RFC</b> ' + rfcPruebas + '<br/><b>Usuario</b> ' + usuarioPruebas + '<br/><b>Clave Cifrada</b> ' + clavePruebas + '</font>';
					
					estadoEmisor += (idiomaRVLFECFDI == 'ES') ? '<br/><br/><b>VALIDACIÓN DE LA CUENTA</b><br/><font color="#515151" size="2">Tu RFC, Usuario y Clave Cifrada son correctos. Tu cuenta ha sido guardada con éxito.</font>' : '<b>ACCOUNT VALIDATION</b><br/><font color="#515151" size="2">Your RFC, User and Coded Key are right. Your account has been saved successfully.</font>';
					
					if(sistema == 'RVCFDI')
						estadoEmisor += (idiomaRVLFECFDI == 'ES') ? '<br/><br/><b>ESTADO DE LA CUENTA</b><br/><font color="#515151" size="2"><b>Vigencia de timbrado</b>: Del ' + EMISOR_RENOVACION + ' al ' + EMISOR_VIGENCIA + ', <b>Estado: </b>' + EMISOR_ESTADO + ', <b>Tipo: </b>' + EMISOR_TIPO_USUARIO + '.</font>': '<br/><br/><b>ACCOUNT STATUS</b><br/><font color="#515151" size="2"><b>Validity CFDI Issue</b>: Since ' + EMISOR_RENOVACION + ' to ' + EMISOR_VIGENCIA + ', <b>Status: </b>' + EMISOR_ESTADO + ', <b>Type: </b>' + EMISOR_TIPO_USUARIO + '.</font>';
					else
						estadoEmisor += (idiomaRVLFECFDI == 'ES') ? '<br/><br/><b>ESTADO DE LA CUENTA</b><br/><font color="#515151" size="2"><b>Estado: </b>' + EMISOR_ESTADO + ', <b>Tipo: </b>' + EMISOR_TIPO_USUARIO + '.</font>' : '<br/><br/><b>ACCOUNT STATUS</b><br/><font color="#515151" size="2"><b>Status: </b>' + EMISOR_ESTADO + ', <b>Type: </b>' + EMISOR_TIPO_USUARIO + '.</font>';
					
					estadoEmisor += (idiomaRVLFECFDI == 'ES') ? '<br/><br/><b>CONFIGURACIÓN DEL PLUGIN</b><br/><font color="#515151" size="2">' + message + '</font>':'<br/><br/><b>PLUGIN CONFIGURATION</b><br/><font color="#515151" size="2">' + message + '</font>';
					
					mostrarVentanaCuenta(estadoEmisor, ((idiomaRVLFECFDI == 'ES') ? 'PROCESO COMPLETADO' : 'PROCESS COMPLETED'));
				}
            });
			
            return false;
		});
		
        $('#realvirtual_woocommerce_enviar_cuenta').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoCuenta').style.visibility = 'visible';
            
			var formularioValido = validarFormularioCuenta();

            if(formularioValido != '')
			{
				mostrarVentanaCuenta('<center>' + formularioValido + '</center>', ((idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice'));
				document.getElementById('cargandoCuenta').style.visibility = 'hidden';
                return false;
            }

            datosFormulario = $('#realvirtual_woocommerce_cuenta').serializeArray();
            
			data =
			{
				action      						: 'realvirtual_woocommerce_guardar_cuenta',
				rfc   								: datosFormulario[0].value,
				usuario   							: datosFormulario[1].value,
				clave       						: datosFormulario[2].value,
				idioma       						: idiomaRVLFECFDI
            }

            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoCuenta').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var EMISOR_RENOVACION = response.EMISOR_RENOVACION;
				var EMISOR_VIGENCIA = response.EMISOR_VIGENCIA;
				var EMISOR_ESTADO = response.EMISOR_ESTADO;
				var EMISOR_TIPO_USUARIO = response.EMISOR_TIPO_USUARIO;
				var sistema = response.sistema;
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaCuenta(((idiomaRVLFECFDI == 'ES') ? '<center>Ocurrió un error al guardar la cuenta:<br/><br/>' + message + '</center>' : '<center>There was an error saving your account:<br/><br/>' + message + '</center>'), ((idiomaRVLFECFDI == 'ES') ? 'Aviso':'Notice'));
				}
				else
				{
					var estadoEmisor = '';
					var rfcPruebas = 'XIA190128J61';
					
					if(datosFormulario[0].value == rfcPruebas)
					{
						estadoEmisor += (idiomaRVLFECFDI == 'ES') ? '<font color="#63a55a"><b>CUENTA DE PRUEBAS UTILIZADA</b></font><br/><font color="#515151" size="2"><b>RFC</b> ' + rfcPruebas + '<br/><b>Usuario</b> ' + datosFormulario[1].value + '<br/><b>Clave Cifrada</b> ' + datosFormulario[2].value + '</font><br/><br/>' : '<font color="#63a55a"><b>TEST ACCOUNT USED</b></font><br/><font color="#515151" size="2"><b>RFC</b> ' + rfcPruebas + '<br/><b>Usuario</b> ' + datosFormulario[1].value + '<br/><b>Clave Cifrada</b> ' + datosFormulario[2].value + '</font><br/><br/>';
					}
					
					estadoEmisor += (idiomaRVLFECFDI == 'ES') ? '<b>VALIDACIÓN DE LA CUENTA</b><br/><font color="#515151" size="2">Tu RFC, Usuario y Clave Cifrada son correctos. Tu cuenta ha sido guardada con éxito.</font>' : '<b>ACCOUNT VALIDATION</b><br/><font color="#515151" size="2">Your RFC, User and Coded Key are right. Your account has been saved successfully.</font>';
					
					if(sistema == 'RVCFDI')
						estadoEmisor += (idiomaRVLFECFDI == 'ES') ? '<br/><br/><b>ESTADO DE LA CUENTA</b><br/><font color="#515151" size="2"><b>Vigencia de timbrado</b>: Del ' + EMISOR_RENOVACION + ' al ' + EMISOR_VIGENCIA + ', <b>Estado: </b>' + EMISOR_ESTADO + ', <b>Tipo: </b>' + EMISOR_TIPO_USUARIO + '.</font>': '<br/><br/><b>ACCOUNT STATUS</b><br/><font color="#515151" size="2"><b>Validity CFDI Issue</b>: Since ' + EMISOR_RENOVACION + ' to ' + EMISOR_VIGENCIA + ', <b>Status: </b>' + EMISOR_ESTADO + ', <b>Type: </b>' + EMISOR_TIPO_USUARIO + '.</font>';
					else
						estadoEmisor += (idiomaRVLFECFDI == 'ES') ? '<br/><br/><b>ESTADO DE LA CUENTA</b><br/><font color="#515151" size="2"><b>Estado: </b>' + EMISOR_ESTADO + ', <b>Tipo: </b>' + EMISOR_TIPO_USUARIO + '.</font>' : '<br/><br/><b>ACCOUNT STATUS</b><br/><font color="#515151" size="2"><b>Status: </b>' + EMISOR_ESTADO + ', <b>Type: </b>' + EMISOR_TIPO_USUARIO + '.</font>';
					
					estadoEmisor += (idiomaRVLFECFDI == 'ES') ? '<br/><br/><b>CONFIGURACIÓN DEL PLUGIN</b><br/><font color="#515151" size="2">' + message + '</font>':'<br/><br/><b>PLUGIN CONFIGURATION</b><br/><font color="#515151" size="2">' + message + '</font>';
					
					mostrarVentanaCuenta(estadoEmisor, ((idiomaRVLFECFDI == 'ES') ? 'PROCESO COMPLETADO' : 'PROCESS COMPLETED'));
				}
            });
			
            return false;
        });
    }
	
	function validarFormularioCuenta()
	{
        var respuesta = false;
                
		var rfc = $('#realvirtual_woocommerce_cuenta').find('#rfc');
                
		if(rfc.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa el RFC.":"Enter the RFC.";

        var usuario = $('#realvirtual_woocommerce_cuenta').find('#usuario');
                
		if(usuario.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa el Usuario.":"Enter the User.";

		var clave = $('#realvirtual_woocommerce_cuenta').find('#clave');
                
		if(clave.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la Clave Cifrada.":"Enter the Coded Key.";
		
        return respuesta;
    }
	
    if($('#realvirtual_woocommerce_configuracion_general').length)
	{
        $('#realvirtual_woocommerce_enviar_configuracion_general').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoConfiguracionGeneral').style.visibility = 'visible';
            
			var formularioValido = validarFormularioConfiguracionGeneral();

            if(formularioValido != '')
			{
				mostrarVentanaConfiguracion(formularioValido);
				document.getElementById('cargandoConfiguracionGeneral').style.visibility = 'hidden';
                return false;
            }

			var version_cfdi = $('#realvirtual_woocommerce_configuracion_general').find('#version_cfdi');
			var serie = $('#realvirtual_woocommerce_configuracion_general').find('#serie');
			var regimen_fiscal = $('#realvirtual_woocommerce_configuracion_general').find('#regimen_fiscal');
			var moneda = $('#realvirtual_woocommerce_configuracion_general').find('#moneda');
			var tipo_cambio = $('#realvirtual_woocommerce_configuracion_general').find('#tipo_cambio');
			var clave_confirmacion = $('#realvirtual_woocommerce_configuracion_general').find('#clave_confirmacion');
			var observacion = $('#realvirtual_woocommerce_configuracion_general').find('#observacion');
			var huso_horario = $('#realvirtual_woocommerce_configuracion_general').find('#huso_horario');
			var domicilio_receptor = $('#realvirtual_woocommerce_configuracion_general').find('#domicilio_receptor');
			var precision_decimal = $('#realvirtual_woocommerce_configuracion_general').find('#precision_decimal');
			var exportacion_cfdi = $('#realvirtual_woocommerce_configuracion_general').find('#exportacion_cfdi');
			var facAtrAdquirente = $('#realvirtual_woocommerce_configuracion_general').find('#facAtrAdquirente');
			var informacionGlobal_periodicidad = $('#realvirtual_woocommerce_configuracion_general').find('#informacionGlobal_periodicidad');
			var informacionGlobal_meses = $('#realvirtual_woocommerce_configuracion_general').find('#informacionGlobal_meses');
			var informacionGlobal_año = $('#realvirtual_woocommerce_configuracion_general').find('#informacionGlobal_año');
			
			data =
			{
				action      									: 'realvirtual_woocommerce_guardar_configuracion_general',
				version_cfdi       								: version_cfdi.val(),
				serie   										: serie.val(),
				regimen_fiscal									: regimen_fiscal.val(),
				moneda											: moneda.val(),
				tipo_cambio										: tipo_cambio.val(),
				clave_confirmacion								: clave_confirmacion.val(),
				observacion										: observacion.val(),
				huso_horario									: huso_horario.val(),
				domicilio_receptor								: domicilio_receptor.val(),
				precision_decimal								: precision_decimal.val(),
				exportacion_cfdi								: exportacion_cfdi.val(),
				facAtrAdquirente								: facAtrAdquirente.val(),
				informacionGlobal_periodicidad					: informacionGlobal_periodicidad.val(),
				informacionGlobal_meses							: informacionGlobal_meses.val(),
				informacionGlobal_año							: informacionGlobal_año.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoConfiguracionGeneral').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	if($('#realvirtual_woocommerce_configuracion_productos').length)
	{
        $('#realvirtual_woocommerce_enviar_configuracion_productos').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoConfiguracionProductos').style.visibility = 'visible';
            
			var formularioValido = validarFormularioConfiguracionProductos();

            if(formularioValido != '')
			{
				mostrarVentanaConfiguracion(formularioValido);
				document.getElementById('cargandoConfiguracionProductos').style.visibility = 'hidden';
                return false;
            }

			var clave_servicio = $('#realvirtual_woocommerce_configuracion_productos').find('#clave_servicio');
			var clave_unidad = $('#realvirtual_woocommerce_configuracion_productos').find('#clave_unidad');
			var unidad_medida = $('#realvirtual_woocommerce_configuracion_productos').find('#unidad_medida');
			var clave_producto = $('#realvirtual_woocommerce_configuracion_productos').find('#clave_producto');
			var numero_pedimento = $('#realvirtual_woocommerce_configuracion_productos').find('#numero_pedimento');
			var objeto_imp_producto = $('#realvirtual_woocommerce_configuracion_productos').find('#objeto_imp_producto');
			
			data =
			{
				action      									: 'realvirtual_woocommerce_guardar_configuracion_productos',
				clave_servicio									: clave_servicio.val(),
				clave_unidad									: clave_unidad.val(),
				unidad_medida									: unidad_medida.val(),
				clave_producto									: clave_producto.val(),
				numero_pedimento								: numero_pedimento.val(),
				objeto_imp_producto								: objeto_imp_producto.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoConfiguracionProductos').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	if($('#realvirtual_woocommerce_configuracion_envios').length)
	{
        $('#realvirtual_woocommerce_enviar_configuracion_envios').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoConfiguracionEnvios').style.visibility = 'visible';
            
			var formularioValido = validarFormularioConfiguracionEnvios();

            if(formularioValido != '')
			{
				mostrarVentanaConfiguracion(formularioValido);
				document.getElementById('cargandoConfiguracionEnvios').style.visibility = 'hidden';
                return false;
            }

			var clave_servicio_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#clave_servicio_shipping');
			var clave_unidad_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#clave_unidad_shipping');
			var unidad_medida_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#unidad_medida_shipping');
			var numero_pedimento_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#numero_pedimento_shipping');
			var clave_producto_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#clave_producto_shipping');
			var config_principal_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#config_principal_shipping');
			var objeto_imp_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#objeto_imp_shipping');
		
			data =
			{
				action      									: 'realvirtual_woocommerce_guardar_configuracion_envios',
				clave_servicio_shipping							: clave_servicio_shipping.val(),
				clave_unidad_shipping							: clave_unidad_shipping.val(),
				unidad_medida_shipping							: unidad_medida_shipping.val(),
				clave_producto_shipping							: clave_producto_shipping.val(),
				numero_pedimento_shipping						: numero_pedimento_shipping.val(),
				config_principal_shipping						: (config_principal_shipping.val() != '1') ? '0' : config_principal_shipping.val(),
				objeto_imp_shipping								: objeto_imp_shipping.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoConfiguracionEnvios').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	if($('#realvirtual_woocommerce_configuracion_reglasModuloClientes').length)
	{
        $('#realvirtual_woocommerce_enviar_configuracion_reglasModuloClientes').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoConfiguracionReglasModuloClientes').style.visibility = 'visible';
            
			var formularioValido = validarFormularioConfiguracionReglasModuloClientes();

            if(formularioValido != '')
			{
				mostrarVentanaConfiguracion(formularioValido);
				document.getElementById('cargandoConfiguracionReglasModuloClientes').style.visibility = 'hidden';
                return false;
            }

			var estado_orden = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#estado_orden');
			var estado_orden_refacturacion = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#estado_orden_refacturacion');
			var metodo_pago = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#metodo_pago');
			var metodo_pago33 = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#metodo_pago33');
			var conceptos_especiales_envio = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#conceptos_especiales_envio');
			var uso_cfdi = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#uso_cfdi');
			var pedido_mes_actual = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#pedido_mes_actual');
			var titulo = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#titulo');
			var descripcion = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#descripcion');
			var uso_cfdi_seleccionar = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#uso_cfdi_seleccionar');
			var metodo_pago_seleccionar = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#metodo_pago_seleccionar');
			var metodo_pago_seleccionar33 = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#metodo_pago_seleccionar33');
			var mostrarMensajeErrorCliente = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#mostrarMensajeErrorCliente');
			var mensajeErrorCliente = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#mensajeErrorCliente');
			var emailNotificacionErrorModuloClientes = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#emailNotificacionErrorModuloClientes');
			
			data =
			{
				action      									: 'realvirtual_woocommerce_guardar_configuracion_reglasModuloClientes',
				estado_orden   									: estado_orden.val(),
				estado_orden_refacturacion						: estado_orden_refacturacion.val(),
				pedido_mes_actual								: pedido_mes_actual.val(),
				uso_cfdi										: uso_cfdi.val(),
				uso_cfdi_seleccionar							: uso_cfdi_seleccionar.val(),
				metodo_pago										: metodo_pago.val(),
				metodo_pago_seleccionar							: metodo_pago_seleccionar.val(),
				metodo_pago33									: metodo_pago33.val(),
				metodo_pago_seleccionar33						: metodo_pago_seleccionar33.val(),
				conceptos_especiales_envio						: conceptos_especiales_envio.val(),
				titulo       									: titulo.val(),
				descripcion     								: descripcion.val(),
				mostrarMensajeErrorCliente						: mostrarMensajeErrorCliente.val(),
				mensajeErrorCliente								: mensajeErrorCliente.val(),
				emailNotificacionErrorModuloClientes			: emailNotificacionErrorModuloClientes.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoConfiguracionReglasModuloClientes').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	if($('#realvirtual_woocommerce_configuracion_estiloModuloClientes').length)
	{
        $('#realvirtual_woocommerce_enviar_configuracion_estiloModuloClientes').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoConfiguracionEstiloModuloClientes').style.visibility = 'visible';
            
			var formularioValido = validarFormularioConfiguracionEstiloModuloClientes();

            if(formularioValido != '')
			{
				mostrarVentanaConfiguracion(formularioValido);
				document.getElementById('cargandoConfiguracionEstiloModuloClientes').style.visibility = 'hidden';
                return false;
            }

			var color_fondo_encabezado_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_fondo_encabezado_hexadecimal');
			var color_texto_encabezado_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_encabezado_hexadecimal');
			var color_fondo_formulario_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_fondo_formulario_hexadecimal');
			var color_texto_formulario_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_formulario_hexadecimal');
			var color_texto_controles_formulario_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_controles_formulario_hexadecimal');
			var color_boton_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_boton_hexadecimal');
			var color_texto_boton_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_boton_hexadecimal');
			var color_boton_hexadecimal_vistaprevia = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_boton_hexadecimal_vistaprevia');
			var color_texto_boton_hexadecimal_vistaprevia = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_boton_hexadecimal_vistaprevia');
			var color_boton_hexadecimal_generarcfdi = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_boton_hexadecimal_generarcfdi');
			var color_texto_boton_hexadecimal_generarcfdi = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_boton_hexadecimal_generarcfdi');
			
			data =
			{
				action      									: 'realvirtual_woocommerce_guardar_configuracion_estiloModuloClientes',
				color_fondo_encabezado_hexadecimal    			: color_fondo_encabezado_hexadecimal.val(),
				color_texto_encabezado_hexadecimal      		: color_texto_encabezado_hexadecimal.val(),
				color_fondo_formulario_hexadecimal    			: color_fondo_formulario_hexadecimal.val(),
				color_texto_formulario_hexadecimal      		: color_texto_formulario_hexadecimal.val(),
				color_texto_controles_formulario_hexadecimal  	: color_texto_controles_formulario_hexadecimal.val(),
				color_boton_hexadecimal       					: color_boton_hexadecimal.val(),
				color_texto_boton_hexadecimal       			: color_texto_boton_hexadecimal.val(),
				color_boton_hexadecimal_vistaprevia       		: color_boton_hexadecimal_vistaprevia.val(),
				color_texto_boton_hexadecimal_vistaprevia		: color_texto_boton_hexadecimal_vistaprevia.val(),
				color_boton_hexadecimal_generarcfdi      		: color_boton_hexadecimal_generarcfdi.val(),
				color_texto_boton_hexadecimal_generarcfdi       : color_texto_boton_hexadecimal_generarcfdi.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoConfiguracionEstiloModuloClientes').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	if($('#realvirtual_woocommerce_configuracion_ajustesAvanzados').length)
	{
        $('#realvirtual_woocommerce_enviar_configuracion_ajustesAvanzados').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoConfiguracionAjustesAvanzados').style.visibility = 'visible';
            
			var formularioValido = validarFormularioConfiguracionAjustesAvanzados();

            if(formularioValido != '')
			{
				mostrarVentanaConfiguracion(formularioValido);
				document.getElementById('cargandoConfiguracionAjustesAvanzados').style.visibility = 'hidden';
                return false;
            }
			
			var manejo_impuestos_pedido = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#manejo_impuestos_pedido');
			var manejo_impuestos_pedido_facturaGlobal = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#manejo_impuestos_pedido_facturaGlobal');
			var manejo_impuestos_pedido_facturaGlobal_texto = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#manejo_impuestos_pedido_facturaGlobal_texto');
			var estado_orden_cfdi_automatico = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#estado_orden_cfdi_automatico');
			var notificar_error_cfdi_automatico = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#notificar_error_cfdi_automatico');
			var complementoCFDI = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#complementoCFDI');
			var complementoCFDI_iedu_configuracion_nivel = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#complementoCFDI_iedu_configuracion_nivel');
			var complementoCFDI_iedu_configuracion_autRVOE = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#complementoCFDI_iedu_configuracion_autRVOE');
			var emailNotificacionErrorAutomatico = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#emailNotificacionErrorAutomatico');
		 
			data =
			{
				action      									: 'realvirtual_woocommerce_guardar_configuracion_ajustesAvanzados',
				manejo_impuestos_pedido			       			: manejo_impuestos_pedido.val(),
				manejo_impuestos_pedido_facturaGlobal			: manejo_impuestos_pedido_facturaGlobal.val(),
				manejo_impuestos_pedido_facturaGlobal_texto		: manejo_impuestos_pedido_facturaGlobal_texto.val(),
				estado_orden_cfdi_automatico					: estado_orden_cfdi_automatico.val(),
				notificar_error_cfdi_automatico					: notificar_error_cfdi_automatico.val(),
				complementoCFDI									: complementoCFDI.val(),
				complementoCFDI_iedu_configuracion_nivel		: complementoCFDI_iedu_configuracion_nivel.val(),
				complementoCFDI_iedu_configuracion_autRVOE		: complementoCFDI_iedu_configuracion_autRVOE.val(),
				emailNotificacionErrorAutomatico				: emailNotificacionErrorAutomatico.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoConfiguracionAjustesAvanzados').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	if($('#realvirtual_woocommerce_configuracion_idioma').length)
	{
        $('#realvirtual_woocommerce_enviar_configuracion_idioma').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoConfiguracionIdioma').style.visibility = 'visible';
            
			var formularioValido = validarFormularioConfiguracionIdioma();

            if(formularioValido != '')
			{
				mostrarVentanaConfiguracion(formularioValido);
				document.getElementById('cargandoConfiguracionIdioma').style.visibility = 'hidden';
                return false;
            }

			var idioma = $('#realvirtual_woocommerce_configuracion_idioma').find('#idioma');
			
			data =
			{
				action      									: 'realvirtual_woocommerce_guardar_configuracion_idioma',
				idioma											: idioma.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoConfiguracionIdioma').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaConfiguracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	function validarFormularioConfiguracionGeneral()
	{
        var respuesta = false;
        
		var version_cfdi = $('#realvirtual_woocommerce_configuracion_general').find('#version_cfdi');
		
		if(version_cfdi.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona la versión de CFDI.":"Select the CFDI version.";	
		
		var serie = $('#realvirtual_woocommerce_configuracion_general').find('#serie');
		
		if(serie.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la serie.":"Enter the serie.";	
		
		var regimen_fiscal = $('#realvirtual_woocommerce_configuracion_general').find('#regimen_fiscal');
		var moneda = $('#realvirtual_woocommerce_configuracion_general').find('#moneda');
		var tipo_cambio = $('#realvirtual_woocommerce_configuracion_general').find('#tipo_cambio');
		var precision_decimal = $('#realvirtual_woocommerce_configuracion_general').find('#precision_decimal');
		
		if(regimen_fiscal.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el régimen fiscal.":"Select the fiscal regime.";
		
		if(moneda.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona la moneda por defecto.":"Select the default currency.";
		
		if(tipo_cambio.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa el tipo de cambio de la moneda seleccionada." : "Enter the exchange rate of the selected currency.";
		
		if(precision_decimal.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona la precisión decimal." : "Select the decimal precision.";
		
		var huso_horario = $('#realvirtual_woocommerce_configuracion_general').find('#huso_horario');
		
		if(huso_horario.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Zona horaria</b>." : "Select an option for <b>Time zone</b>.";
		
		var domicilio_receptor = $('#realvirtual_woocommerce_configuracion_general').find('#domicilio_receptor');
		
		if(domicilio_receptor.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Mostrar la dirección del cliente en la facturación</b>." : "Select an option for <b>Show customer's address on billing</b>.";
		
		var exportacion_cfdi = $('#realvirtual_woocommerce_configuracion_general').find('#exportacion_cfdi');
		
		if(exportacion_cfdi.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona un valor para Exportación.":"Select a value for Export.";	
		
		if(version_cfdi.val() == '4.0')
		{
			var informacionGlobal_periodicidad = $('#realvirtual_woocommerce_configuracion_general').find('#informacionGlobal_periodicidad');
			var informacionGlobal_meses = $('#realvirtual_woocommerce_configuracion_general').find('#informacionGlobal_meses');
			var informacionGlobal_año = $('#realvirtual_woocommerce_configuracion_general').find('#informacionGlobal_año');
			
			if(informacionGlobal_periodicidad.val().length == 0)
				respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Periodicidad</b>." : "Select an option for <b>Periodicity</b>.";
			if(informacionGlobal_meses.val().length == 0)
				respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Meses</b>." : "Select an option for <b>Months</b>.";
			if(informacionGlobal_año.val().length == 0)
				respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Año</b>." : "Select an option for <b>Year</b>.";
			
			if(informacionGlobal_periodicidad.val() == '05' && regimen_fiscal.val() != '621')
				respuesta = (idiomaRVLFECFDI == 'ES') ? "Cuando el valor del campo <b>Periodicidad</b> es <b>05 - Bimestral</b> el valor del campo <b>Régimen fiscal del Emisor</b> debe ser <b>621 - Incorporación Fiscal</b>." : "When the value of the <b>Periodicity</b> field is <b>05 - Bimestral</b>, the value of the <b>Issuer fiscal regime</b> field must be <b>621 - Incorporación Fiscal</b>.";
			if(informacionGlobal_periodicidad.val() == '05' && informacionGlobal_meses.val() != '13' && informacionGlobal_meses.val() != '14' && informacionGlobal_meses.val() != '15' && informacionGlobal_meses.val() != '16'
				&& informacionGlobal_meses.val() != '17' && informacionGlobal_meses.val() != '18')
				respuesta = (idiomaRVLFECFDI == 'ES') ? "Cuando el valor del campo <b>Periodicidad</b> es <b>05 - Bimestral</b> el valor del campo <b>Meses</b> debe ser <b>13 - Enero-Febrero</b>, <b>14 - Marzo-Abril</b>, <b>15 - Mayo-Junio</b>, <b>16 - Julio-Agosto</b>, <b>17 - Septiembre-Octubre</b> o <b>18 - Noviembre-Diciembre</b>." : "When the value of the <b>Periodicity</b> field is <b>05 - Bimestral</b>, the value of the <b>Months</b> field must be <b>13 - Enero-Febrero</b>, <b>14 - Marzo-Abril</b>, <b>15 - Mayo-Junio</b>, <b>16 - Julio-Agosto</b>, <b>17 - Septiembre-Octubre</b> o <b>18 - Noviembre-Diciembre</b>.";
			if(informacionGlobal_periodicidad.val() != '05' && (informacionGlobal_meses.val() == '13' || informacionGlobal_meses.val() == '14' || informacionGlobal_meses.val() == '15' || informacionGlobal_meses.val() == '16'
				|| informacionGlobal_meses.val() == '17' || informacionGlobal_meses.val() == '18'))
				respuesta = (idiomaRVLFECFDI == 'ES') ? "Cuando el valor del campo <b>Periodicidad</b> es diferente de <b>05 - Bimestral</b> el valor del campo <b>Meses</b> no puede ser <b>13 - Enero-Febrero</b>, <b>14 - Marzo-Abril</b>, <b>15 - Mayo-Junio</b>, <b>16 - Julio-Agosto</b>, <b>17 - Septiembre-Octubre</b> o <b>18 - Noviembre-Diciembre</b>." : "When the value of the <b>Periodicity</b> field is different from <b>05 - Bimestral</b>, the value of the <b>Months</b> field cannot be <b>13 - Enero-Febrero</b>, <b>14 - Marzo-Abril</b>, <b>15 - Mayo-Junio</b>, <b>16 - Julio-Agosto</b>, <b>17 - Septiembre-Octubre</b> o <b>18 - Noviembre-Diciembre</b>.";
		}
		
        return respuesta;
    }
	
	function validarFormularioConfiguracionProductos()
	{
        var respuesta = false;
        
		var clave_servicio = $('#realvirtual_woocommerce_configuracion_productos').find('#clave_servicio');
		var clave_unidad = $('#realvirtual_woocommerce_configuracion_productos').find('#clave_unidad');
		var objeto_imp_producto = $('#realvirtual_woocommerce_configuracion_productos').find('#objeto_imp_producto');
		var version_cfdi = $('#realvirtual_woocommerce_configuracion_general').find('#version_cfdi');
		
		if(clave_servicio.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la clave servicio por defecto.":"Enter the default service code.";
		
		if(clave_unidad.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la clave unidad por defecto.":"Enter the default unit code.";
		
		if(version_cfdi.val() == "4.0")
		{
			if(objeto_imp_producto.val().length == 0)
				respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona un valor para Objeto de Impuesto de los productos.":"Select a value for Tax object of the products.";
		}
		
        return respuesta;
    }
	
	function validarFormularioConfiguracionEnvios()
	{
        var respuesta = false;
        
		var clave_servicio_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#clave_servicio_shipping');
		var clave_unidad_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#clave_unidad_shipping');
		var config_principal_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#config_principal_shipping');
		var objeto_imp_shipping = $('#realvirtual_woocommerce_configuracion_envios').find('#objeto_imp_shipping');
		var version_cfdi = $('#realvirtual_woocommerce_configuracion_general').find('#version_cfdi');
		
		if(config_principal_shipping.val() == '1')
		{
			if(clave_servicio_shipping.val().length == 0)
				respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la clave servicio del concepto de envío (shipping) por defecto.":"Enter the default service code for the shipping concept.";
		
			if(clave_unidad_shipping.val().length == 0)
				respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la clave unidad del concepto de envío (shipping) por defecto.":"Enter the default unit code for the shipping concept.";
			
			if(version_cfdi.val() == "4.0")
			{
				if(objeto_imp_shipping.val().length == 0)
					respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona un valor para Objeto de Impuesto de los conceptos de envío (shipping).":"Select a value for Tax object of the shipping concepts.";
			}
		}
		
        return respuesta;
    }
	
	function validarFormularioConfiguracionReglasModuloClientes()
	{
        var respuesta = false;
        
		var estado_orden = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#estado_orden');
		
		if(estado_orden.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el estado de la orden para permitir facturación.":"Select the order status to allow CFDI issue.";	
		
		var estado_orden_refacturacion = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#estado_orden_refacturacion');
		
		if(estado_orden_refacturacion.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el estado de la orden para permitir refacturación tras cancelación.":"Select the order status to allow CFDI issue after cancellation.";
		
		var metodo_pago = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#metodo_pago');
		var metodo_pago33 = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#metodo_pago33');
		var conceptos_especiales_envio = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#conceptos_especiales_envio');
		var uso_cfdi = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#uso_cfdi');
		
		if(metodo_pago.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona la forma de pago por defecto.":"Select the default payment way.";
		
		if(metodo_pago33.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el método de pago por defecto.":"Select the default payment method.";
		
		if(conceptos_especiales_envio.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el comportamiento de conceptos especiales.":"Select the behavior of special concepts.";
		
		if(uso_cfdi.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el uso CFDI por defecto.":"Select the default CFDI Use.";
		
		var mostrarMensajeErrorCliente = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#mostrarMensajeErrorCliente');
		var mensajeErrorCliente = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#mensajeErrorCliente');
		var emailNotificacionErrorModuloClientes = $('#realvirtual_woocommerce_configuracion_reglasModuloClientes').find('#emailNotificacionErrorModuloClientes');
		
		if(mostrarMensajeErrorCliente.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para el campo <b>Mostrar mensaje personalizado en pantalla...</b>.":"Select an option for <b>Show on-screen personalized message...</b>.";
		
		if(mostrarMensajeErrorCliente.val() == "si" && mensajeErrorCliente.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingrese un texto para el campo <b>Mensaje personalizado en pantalla...</b>.":"Select an option for <b>On-screen personalized message...</b>.";
		if(mostrarMensajeErrorCliente.val() == "si" && emailNotificacionErrorModuloClientes.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingrese un correo electrónico para el campo <b>E-mail a donde se enviará el error...</b>.":"Enter an email for the <b>Email to which the error will be sent...</b> field.";
		
        return respuesta;
    }
	
	function validarFormularioConfiguracionEstiloModuloClientes()
	{
        var respuesta = false;
        
		var color_fondo_encabezado_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_fondo_encabezado_hexadecimal');
		var color_texto_encabezado_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_encabezado_hexadecimal');
		var color_fondo_formulario_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_fondo_formulario_hexadecimal');
		var color_texto_formulario_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_formulario_hexadecimal');
		var color_texto_controles_formulario_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_controles_formulario_hexadecimal');
		var color_boton_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_boton_hexadecimal');
		var color_texto_boton_hexadecimal = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_boton_hexadecimal');
		var color_boton_hexadecimal_vistaprevia = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_boton_hexadecimal_vistaprevia');
		var color_texto_boton_hexadecimal_vistaprevia = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_boton_hexadecimal_vistaprevia');
		var color_boton_hexadecimal_generarcfdi = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_boton_hexadecimal_generarcfdi');
		var color_texto_boton_hexadecimal_generarcfdi = $('#realvirtual_woocommerce_configuracion_estiloModuloClientes').find('#color_texto_boton_hexadecimal_generarcfdi');
		
		if(color_fondo_encabezado_hexadecimal.val().length == 0 ||
			color_texto_encabezado_hexadecimal.val().length == 0 ||
			color_fondo_formulario_hexadecimal.val().length == 0 ||
			color_texto_formulario_hexadecimal.val().length == 0 ||
			color_texto_controles_formulario_hexadecimal.val().length == 0 ||
			color_boton_hexadecimal.val().length == 0 ||
			color_texto_boton_hexadecimal.val().length == 0 ||
			color_boton_hexadecimal_vistaprevia.val().length == 0 ||
			color_texto_boton_hexadecimal_vistaprevia.val().length == 0 ||
			color_boton_hexadecimal_generarcfdi.val().length == 0 ||
			color_texto_boton_hexadecimal_generarcfdi.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Verifica que todos los colores estén establecidos.":"Verify that all colors are set.";	
		
        return respuesta;
    }
	
	function validarFormularioConfiguracionAjustesAvanzados()
	{
        var respuesta = false;
        
		var manejo_impuestos_pedido = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#manejo_impuestos_pedido');
		console.log("Dato manejo_impuestos_pedido: " + manejo_impuestos_pedido.val());
		if(manejo_impuestos_pedido.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>¿Cómo utilizará el plugin los datos de los pedidos de WooCommerce para su facturación?</b>." : "Select an option for <b>How will the plugin use WooCommerce order data for invoicing?</b>.";
		var manejo_impuestos_pedido_facturaGlobal = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#manejo_impuestos_pedido_facturaGlobal');
		console.log("Dato manejo_impuestos_pedido_facturaGlobal: " + manejo_impuestos_pedido_facturaGlobal.val());
		if(manejo_impuestos_pedido_facturaGlobal.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Factura Global - ¿Cómo utilizará el plugin los datos de los pedidos de WooCommerce para su facturación?</b>." : "Select an option for <b>Global Invoice - How will the plugin use WooCommerce order data for the global invoice?</b>.";
		
		/*var estado_orden_cfdi_automatico = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#estado_orden_cfdi_automatico');
		console.log("Dato estado_orden_cfdi_automatico: " + estado_orden_cfdi_automatico.val());
		if(estado_orden_cfdi_automatico.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Facturación Automática - Estado del pedido para emitir CFDI automáticamente</b>." : "Select an option for <b>Automatic Billing - Order status to issue CFDI automatically</b>.";
		var notificar_error_cfdi_automatico = $('#realvirtual_woocommerce_configuracion_ajustesAvanzados').find('#notificar_error_cfdi_automatico');
		console.log("Dato notificar_error_cfdi_automatico: " + notificar_error_cfdi_automatico.val());
		if(notificar_error_cfdi_automatico.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción para <b>Facturación Automática - Enviar notificación por correo cuando ocurra un error al emitir el CFDI</b>." : "Select an option for <b>Automatic Billing - Send notification by email when an error occurs when issuing the CFDI</b>.";
		*/
        return respuesta;
    }
	
    function validarFormularioConfiguracionIdioma()
	{
        var respuesta = false;
        
		var idioma = $('#realvirtual_woocommerce_configuracion_idioma').find('#idioma');
		
		if(idioma.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el idioma del plugin.":"Select the plugin language.";
		
        return respuesta;
    }
	
	if($('#realvirtual_woocommerce_ci_consultarPedidos').length)
	{
        $('#realvirtual_woocommerce_ci_consultarPedidos_botonGuardar').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargando_ci_consultarPedidos').style.visibility = 'visible';
            
			var formularioValido = validarFormularioCIConsultarPedidos();

            if(formularioValido != '')
			{
				mostrarVentanaCentroIntegracion(formularioValido);
				document.getElementById('cargando_ci_consultarPedidos').style.visibility = 'hidden';
                return false;
            }

			var ci_consultarPedidos_formaConsulta = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_formaConsulta');
			var ci_consultarPedidos_tipoConexion = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_tipoConexion');
			var ci_consultarPedidos_tipoSolicitud = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_tipoSolicitud');
			var ci_consultarPedidos_url = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_url');
			var ci_consultarPedidos_numero_de_pedido = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_numero_de_pedido');
			var ci_consultarPedidos_monto = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_monto');
			var ci_consultarPedidos_tipoCampo1 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_tipoCampo1');
			var ci_consultarPedidos_nombreCampo1 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_nombreCampo1');
			var ci_consultarPedidos_parametroCampo1 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_parametroCampo1');
			var ci_consultarPedidos_activadoCampo1 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_activadoCampo1');
			var ci_consultarPedidos_tipoCampo2 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_tipoCampo2');
			var ci_consultarPedidos_nombreCampo2 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_nombreCampo2');
			var ci_consultarPedidos_parametroCampo2 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_parametroCampo2');
			var ci_consultarPedidos_activadoCampo2 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_activadoCampo2');
		
			data =
			{
				action      												: 'realvirtual_woocommerce_ci_consultarPedidos_guardar',
				ci_consultarPedidos_tipo_consulta							: ci_consultarPedidos_formaConsulta.val(),
				ci_consultarPedidos_tipo_conexion							: ci_consultarPedidos_tipoConexion.val(),
				ci_consultarPedidos_tipo_solicitud   						: ci_consultarPedidos_tipoSolicitud.val(),
				ci_consultarPedidos_url       								: ci_consultarPedidos_url.val(),
				ci_consultarPedidos_nombre_parametro_numeropedido			: ci_consultarPedidos_numero_de_pedido.val(),
				ci_consultarPedidos_nombre_parametro_monto					: ci_consultarPedidos_monto.val(),
				ci_consultarPedidos_parametro_extra1_estado					: ci_consultarPedidos_activadoCampo1.val(),
				ci_consultarPedidos_parametro_extra1_tipo					: ci_consultarPedidos_tipoCampo1.val(),
				ci_consultarPedidos_parametro_extra1_nombrevisual			: ci_consultarPedidos_nombreCampo1.val(),
				ci_consultarPedidos_parametro_extra1_nombreinterno			: ci_consultarPedidos_parametroCampo1.val(),
				ci_consultarPedidos_parametro_extra2_estado					: ci_consultarPedidos_activadoCampo2.val(),
				ci_consultarPedidos_parametro_extra2_tipo					: ci_consultarPedidos_tipoCampo2.val(),
				ci_consultarPedidos_parametro_extra2_nombrevisual			: ci_consultarPedidos_nombreCampo2.val(),
				ci_consultarPedidos_parametro_extra2_nombreinterno			: ci_consultarPedidos_parametroCampo2.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargando_ci_consultarPedidos').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaCentroIntegracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaCentroIntegracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	if($('#realvirtual_woocommerce_ci_enviarPedidos').length)
	{
        $('#realvirtual_woocommerce_ci_enviarPedidos_botonGuardar').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargando_ci_enviarPedidos').style.visibility = 'visible';
            
			var formularioValido = validarFormularioCIEnviarPedidos();

            if(formularioValido != '')
			{
				mostrarVentanaCentroIntegracion(formularioValido);
				document.getElementById('cargando_ci_enviarPedidos').style.visibility = 'hidden';
                return false;
            }

			var ci_enviarPedidos_formaConsulta = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_formaConsulta');
			var ci_enviarPedidos_tipoConexion = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_tipoConexion');
			var ci_enviarPedidos_tipoSolicitud = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_tipoSolicitud');
			var ci_enviarPedidos_url = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_url');
			var ci_enviarPedidos_tipoConexion2 = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_tipoConexion2');
			var ci_enviarPedidos_tipoSolicitud2 = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_tipoSolicitud2');
			var ci_enviarPedidos_url2 = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_url2');
			
			data =
			{
				action      											: 'realvirtual_woocommerce_ci_enviarPedidos_guardar',
				ci_enviarPedidos_tipo_consulta							: ci_enviarPedidos_formaConsulta.val(),
				ci_enviarPedidos_tipo_conexion							: ci_enviarPedidos_tipoConexion.val(),
				ci_enviarPedidos_tipo_solicitud   						: ci_enviarPedidos_tipoSolicitud.val(),
				ci_enviarPedidos_url       								: ci_enviarPedidos_url.val(),
				ci_enviarPedidos_tipo_conexion2							: ci_enviarPedidos_tipoConexion2.val(),
				ci_enviarPedidos_tipo_solicitud2   						: ci_enviarPedidos_tipoSolicitud2.val(),
				ci_enviarPedidos_url2      								: ci_enviarPedidos_url2.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargando_ci_enviarPedidos').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaCentroIntegracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaCentroIntegracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	if($('#realvirtual_woocommerce_ci_enviarPedidosCrear').length)
	{
        $('#realvirtual_woocommerce_ci_enviarPedidosCrear_botonGuardar').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargando_ci_enviarPedidosCrear').style.visibility = 'visible';
            
			var formularioValido = validarFormularioCIEnviarPedidosCrear();

            if(formularioValido != '')
			{
				mostrarVentanaCentroIntegracion(formularioValido);
				document.getElementById('cargando_ci_enviarPedidosCrear').style.visibility = 'hidden';
                return false;
            }

			var ci_enviarPedidosCrear_formaConsulta = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_formaConsulta');
			var ci_enviarPedidosCrear_tipoConexion = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_tipoConexion');
			var ci_enviarPedidosCrear_tipoSolicitud = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_tipoSolicitud');
			var ci_enviarPedidosCrear_url = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_url');
			var ci_enviarPedidosCrear_tipoConexion2 = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_tipoConexion2');
			var ci_enviarPedidosCrear_tipoSolicitud2 = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_tipoSolicitud2');
			var ci_enviarPedidosCrear_url2 = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_url2');
			
			data =
			{
				action      												: 'realvirtual_woocommerce_ci_enviarPedidosCrear_guardar',
				ci_enviarPedidosCrear_tipo_consulta							: ci_enviarPedidosCrear_formaConsulta.val(),
				ci_enviarPedidosCrear_tipo_conexion							: ci_enviarPedidosCrear_tipoConexion.val(),
				ci_enviarPedidosCrear_tipo_solicitud   						: ci_enviarPedidosCrear_tipoSolicitud.val(),
				ci_enviarPedidosCrear_url       							: ci_enviarPedidosCrear_url.val(),
				ci_enviarPedidosCrear_tipo_conexion2						: ci_enviarPedidosCrear_tipoConexion2.val(),
				ci_enviarPedidosCrear_tipo_solicitud2   					: ci_enviarPedidosCrear_tipoSolicitud2.val(),
				ci_enviarPedidosCrear_url2       							: ci_enviarPedidosCrear_url2.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargando_ci_enviarPedidosCrear').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaCentroIntegracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaCentroIntegracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	if($('#realvirtual_woocommerce_ci_enviarXml').length)
	{
        $('#realvirtual_woocommerce_ci_enviarXml_botonGuardar').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargando_ci_enviarXml').style.visibility = 'visible';
            
			var formularioValido = validarFormularioCIEnviarXml();

            if(formularioValido != '')
			{
				mostrarVentanaCentroIntegracion(formularioValido);
				document.getElementById('cargando_ci_enviarXml').style.visibility = 'hidden';
                return false;
            }

			var ci_enviarXml_formaConsulta = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_formaConsulta');
			var ci_enviarXml_tipoConexion = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_tipoConexion');
			var ci_enviarXml_tipoSolicitud = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_tipoSolicitud');
			var ci_enviarXml_url = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_url');
			var ci_enviarXml_tipoConexion2 = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_tipoConexion2');
			var ci_enviarXml_tipoSolicitud2 = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_tipoSolicitud2');
			var ci_enviarXml_url2 = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_url2');
		
			data =
			{
				action      										: 'realvirtual_woocommerce_ci_enviarXml_guardar',
				ci_enviarXml_tipo_consulta							: ci_enviarXml_formaConsulta.val(),
				ci_enviarXml_tipo_conexion							: ci_enviarXml_tipoConexion.val(),
				ci_enviarXml_tipo_solicitud   						: ci_enviarXml_tipoSolicitud.val(),
				ci_enviarXml_url       								: ci_enviarXml_url.val(),
				ci_enviarXml_tipo_conexion2							: ci_enviarXml_tipoConexion2.val(),
				ci_enviarXml_tipo_solicitud2   						: ci_enviarXml_tipoSolicitud2.val(),
				ci_enviarXml_url2       							: ci_enviarXml_url2.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargando_ci_enviarXml').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaCentroIntegracion(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaCentroIntegracion(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
    function validarFormularioCIConsultarPedidos()
	{
        var respuesta = '';
        
		var ci_consultarPedidos_formaConsulta = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_formaConsulta');
		var ci_consultarPedidos_tipoConexion = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_tipoConexion');
		var ci_consultarPedidos_tipoSolicitud = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_tipoSolicitud');
		var ci_consultarPedidos_url = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_url');
		var ci_consultarPedidos_numero_de_pedido = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_numero_de_pedido');
		var ci_consultarPedidos_monto = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_monto');
		var ci_consultarPedidos_tipoCampo1 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_tipoCampo1');
		var ci_consultarPedidos_nombreCampo1 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_nombreCampo1');
		var ci_consultarPedidos_parametroCampo1 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_parametroCampo1');
		var ci_consultarPedidos_activadoCampo1 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_activadoCampo1');
		var ci_consultarPedidos_tipoCampo2 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_tipoCampo2');
		var ci_consultarPedidos_nombreCampo2 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_nombreCampo2');
		var ci_consultarPedidos_parametroCampo2 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_parametroCampo2');
		var ci_consultarPedidos_activadoCampo2 = $('#realvirtual_woocommerce_ci_consultarPedidos').find('#realvirtual_woocommerce_ci_consultarPedidos_activadoCampo2');
		
		if(ci_consultarPedidos_formaConsulta.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción en <b>¿Cómo deseas que el plugin realice la búsqueda de pedidos?</b>.":"Select an option in <b>How do you want the plugin to search for orders?</b>.";
			return respuesta;
		}
		
		if(ci_consultarPedidos_formaConsulta.val() == '1')
		{
			respuesta = '';
			return respuesta;
		}
		
		if(ci_consultarPedidos_tipoConexion.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Conexión</b>.":"Select the <b>Connection Type</b>.";	
			return respuesta;
		}
			
		if(ci_consultarPedidos_tipoSolicitud.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Solicitud</b>.":"Select the <b>Request Type</b>.";	
			return respuesta;
		}
		
		if(ci_consultarPedidos_url.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la <b>URL</b> de tu servicio.":"Enter the <b>URL</b>.";	
			return respuesta;
		}
		
		if(ci_consultarPedidos_numero_de_pedido.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa el <b>nombre del parámetro</b> en tu servicio al que se enviará el <b>número de pedido</b>.":"Enter the <b>parameter name</b> in your service to which the <b>order number</b> will be sent.";	
			return respuesta;
		}
			
		if(ci_consultarPedidos_monto.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa el <b>nombre del parámetro</b> en tu servicio al que se enviará el <b>monto del pedido</b>.":"Enter the <b>parameter name</b> in your service to which the <b>order amount</b> will be sent.";	
			return respuesta;
		}
			
		if(ci_consultarPedidos_activadoCampo1.val() == '1')
		{
			if(ci_consultarPedidos_tipoCampo1.val().length == 0)
			{
				respuesta = (idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 1</b> está activado pero faltan datos obligatorios.<br/><br/>Selecciona el <b>Tipo de datos</b> para el <b>CAMPO 1</b>.":"<b>FIELD 1</b> is activated but mandatory data is missing.<br/><br/>Select the <b>Data type</b> for the <b>FIELD 1</b>.";	
				return respuesta;
			}
			
			if(ci_consultarPedidos_nombreCampo1.val().length == 0)
			{
				respuesta = (idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 1</b> está activado pero faltan datos obligatorios.<br/><br/>Ingresa el <b>Nombre del campo en el módulo de facturación</b> para el <b>CAMPO 1</b>.":"<b>FIELD 1</b> is activated but mandatory data is missing.<br/><br/>Enter the <b>Name of the field in the invoicing module</b> for the <b>FIELD 1</b>.";	
				return respuesta;
			}
			
			if(ci_consultarPedidos_parametroCampo1.val().length == 0)
			{
				respuesta = (idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 1</b> está activado pero faltan datos obligatorios.<br/><br/>Ingresa el <b>nombre del parámetro</b> en tu servicio al que se enviará el valor del <b>CAMPO 1</b>.":"<b>FIELD 1</b> is activated but mandatory data is missing.<br/><br/>Enter the <b>parameter name</b> in your service to which the <b>FIELD 1</b> value will be sent.";	
				return respuesta;
			}
		}
		
		if(ci_consultarPedidos_activadoCampo2.val() == '1')
		{
			if(ci_consultarPedidos_tipoCampo2.val().length == 0)
			{	
				respuesta = (idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 2</b> está activado pero faltan datos obligatorios.<br/><br/>Selecciona el <b>Tipo de datos</b> para el <b>CAMPO 2</b>.":"<b>FIELD 2</b> is activated but mandatory data is missing.<br/><br/>Select the <b>Data type</b> for the <b>FIELD 2</b>.";	
				return respuesta;
			}
			
			if(ci_consultarPedidos_nombreCampo2.val().length == 0)
			{
				respuesta = (idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 2</b> está activado pero faltan datos obligatorios.<br/><br/>Ingresa el <b>Nombre del campo en el módulo de facturación</b> para el <b>CAMPO 2</b>.":"<b>FIELD 2</b> is activated but mandatory data is missing.<br/><br/>Enter the <b>Name of the field in the invoicing module</b> for the <b>FIELD 2</b>.";	
				return respuesta;
			}
			
			if(ci_consultarPedidos_parametroCampo2.val().length == 0)
			{
				respuesta = (idiomaRVLFECFDI == 'ES') ? "El <b>CAMPO 2</b> está activado pero faltan datos obligatorios.<br/><br/>Ingresa el <b>nombre del parámetro</b> en tu servicio al que se enviará el valor del <b>CAMPO 2</b>.":"<b>FIELD 2</b> is activated but mandatory data is missing.<br/><br/>Enter the <b>parameter name</b> in your service to which the <b>FIELD 2</b> value will be sent.";	
				return respuesta;
			}
		}
		
        return respuesta;
    }
	
	function validarFormularioCIEnviarPedidos()
	{
        var respuesta = '';
        
		var ci_enviarPedidos_formaConsulta = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_formaConsulta');
		var ci_enviarPedidos_tipoConexion = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_tipoConexion');
		var ci_enviarPedidos_tipoSolicitud = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_tipoSolicitud');
		var ci_enviarPedidos_url = $('#realvirtual_woocommerce_ci_enviarPedidos').find('#realvirtual_woocommerce_ci_enviarPedidos_url');
		
		if(ci_enviarPedidos_formaConsulta.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción en <b>¿Cómo deseas que el plugin realice la búsqueda de pedidos?</b>.":"Select an option in <b>How do you want the plugin to search for orders?</b>.";
			return respuesta;
		}
		
		if(ci_enviarPedidos_formaConsulta.val() == '0')
		{
			respuesta = '';
			return respuesta;
		}
		
		if(ci_enviarPedidos_tipoConexion.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Conexión</b>.":"Select the <b>Connection Type</b>.";	
			return respuesta;
		}
			
		if(ci_enviarPedidos_tipoSolicitud.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Solicitud</b>.":"Select the <b>Request Type</b>.";	
			return respuesta;
		}
		
		if(ci_enviarPedidos_url.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la <b>URL</b> de tu servicio.":"Enter the <b>URL</b>.";	
			return respuesta;
		}
		
        return respuesta;
    }
	
	function validarFormularioCIEnviarPedidosCrear()
	{
        var respuesta = '';
        
		var ci_enviarPedidosCrear_formaConsulta = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_formaConsulta');
		var ci_enviarPedidosCrear_tipoConexion = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_tipoConexion');
		var ci_enviarPedidosCrear_tipoSolicitud = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_tipoSolicitud');
		var ci_enviarPedidosCrear_url = $('#realvirtual_woocommerce_ci_enviarPedidosCrear').find('#realvirtual_woocommerce_ci_enviarPedidosCrear_url');
		
		if(ci_enviarPedidosCrear_formaConsulta.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción en <b>¿Cómo deseas que el plugin realice la búsqueda de pedidos?</b>.":"Select an option in <b>How do you want the plugin to search for orders?</b>.";
			return respuesta;
		}
		
		if(ci_enviarPedidosCrear_formaConsulta.val() == '0')
		{
			respuesta = '';
			return respuesta;
		}
		
		if(ci_enviarPedidosCrear_tipoConexion.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Conexión</b>.":"Select the <b>Connection Type</b>.";	
			return respuesta;
		}
			
		if(ci_enviarPedidosCrear_tipoSolicitud.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Solicitud</b>.":"Select the <b>Request Type</b>.";	
			return respuesta;
		}
		
		if(ci_enviarPedidosCrear_url.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la <b>URL</b> de tu servicio.":"Enter the <b>URL</b>.";	
			return respuesta;
		}
		
        return respuesta;
    }
	
	function validarFormularioCIEnviarXml()
	{
        var respuesta = '';
        
		var ci_enviarXml_formaConsulta = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_formaConsulta');
		var ci_enviarXml_tipoConexion = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_tipoConexion');
		var ci_enviarXml_tipoSolicitud = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_tipoSolicitud');
		var ci_enviarXml_url = $('#realvirtual_woocommerce_ci_enviarXml').find('#realvirtual_woocommerce_ci_enviarXml_url');
		
		if(ci_enviarXml_formaConsulta.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona una opción en <b>¿Cómo deseas que el plugin realice la búsqueda de pedidos?</b>.":"Select an option in <b>How do you want the plugin to search for orders?</b>.";
			return respuesta;
		}
		
		if(ci_enviarXml_formaConsulta.val() == '0')
		{
			respuesta = '';
			return respuesta;
		}
		
		if(ci_enviarXml_tipoConexion.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Conexión</b>.":"Select the <b>Connection Type</b>.";	
			return respuesta;
		}
			
		if(ci_enviarXml_tipoSolicitud.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el <b>Tipo de Solicitud</b>.":"Select the <b>Request Type</b>.";	
			return respuesta;
		}
		
		if(ci_enviarXml_url.val().length == 0)
		{
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Ingresa la <b>URL</b> de tu servicio.":"Enter the <b>URL</b>.";	
			return respuesta;
		}
		
        return respuesta;
    }

	if($('#realvirtual_woocommerce_configuracion_bayer').length)
	{
        $('#realvirtual_woocommerce_enviar_configuracion_bayer').click(function(event)
		{
            event.preventDefault();
			document.getElementById('cargandoConfiguracionBayer').style.visibility = 'visible';
            
			var rvcfdi_bayer_facturacion_c_clase_documento = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_c_clase_documento');
			var rvcfdi_bayer_facturacion_c_sociedad = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_c_sociedad');
			var rvcfdi_bayer_facturacion_c_moneda = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_c_moneda');
			var rvcfdi_bayer_facturacion_c_tc_cab_doc = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_c_tc_cab_doc');
			var rvcfdi_bayer_facturacion_p_cuenta = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_cuenta');
			var rvcfdi_bayer_facturacion_p_division = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_division');
			var rvcfdi_bayer_facturacion_p_ce_be = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_ce_be');
			var rvcfdi_bayer_facturacion_p_texto = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_texto');
			var rvcfdi_bayer_facturacion_p_pais_destinatario = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_pais_destinatario');
			var rvcfdi_bayer_facturacion_p_linea_de_producto = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_linea_de_producto');
			var rvcfdi_bayer_facturacion_p_grupo_de_producto = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_grupo_de_producto');
			var rvcfdi_bayer_facturacion_p_centro = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_centro');
			var rvcfdi_bayer_facturacion_p_cliente = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_cliente');
			var rvcfdi_bayer_facturacion_p_organiz_ventas = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_organiz_ventas');
			var rvcfdi_bayer_facturacion_p_canal_distrib = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_canal_distrib');
			var rvcfdi_bayer_facturacion_p_zoha_de_ventas = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_zoha_de_ventas');
			var rvcfdi_bayer_facturacion_p_oficina_ventas = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_oficina_ventas');
			var rvcfdi_bayer_facturacion_p_ramo = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_ramo');
			var rvcfdi_bayer_facturacion_p_grupo = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_grupo');
			var rvcfdi_bayer_facturacion_p_gr_vendedores = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_gr_vendedores');
			var rvcfdi_bayer_facturacion_p_atributo_1_sector = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_atributo_1_sector');
			var rvcfdi_bayer_facturacion_p_atributo_2_sector = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_atributo_2_sector');
			var rvcfdi_bayer_facturacion_p_clase_factura = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_facturacion_p_clase_factura');
			var rvcfdi_bayer_financiero_c_clase_de_documento = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_c_clase_de_documento');
			var rvcfdi_bayer_financiero_c_sociedad = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_c_sociedad');
			var rvcfdi_bayer_financiero_c_moneda = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_c_moneda');
			var rvcfdi_bayer_financiero_c_t_xt_cab_doc = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_c_t_xt_cab_doc');
			var rvcfdi_bayer_financiero_c_cuenta_bancaria = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_c_cuenta_bancaria');
			var rvcfdi_bayer_financiero_c_texto = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_c_texto');
			var rvcfdi_bayer_financiero_c_division = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_c_division');
			var rvcfdi_bayer_financiero_c_cebe = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_c_cebe');
			var rvcfdi_bayer_financiero_c_cliente = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_c_cliente');
			var rvcfdi_bayer_financiero_p_cuenta = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_cuenta');
			var rvcfdi_bayer_financiero_p_ind_impuestos = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_ind_impuestos');
			var rvcfdi_bayer_financiero_p_division = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_division');
			var rvcfdi_bayer_financiero_p_texto = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_texto');
			var rvcfdi_bayer_financiero_p_cebe = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_cebe');
			var rvcfdi_bayer_financiero_p_pais_destinatario = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_pais_destinatario');
			var rvcfdi_bayer_financiero_p_linea_de_producto = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_linea_de_producto');
			var rvcfdi_bayer_financiero_p_grupo_de_proudcto = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_grupo_de_proudcto');
			var rvcfdi_bayer_financiero_p_centro = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_centro');
			var rvcfdi_bayer_financiero_p_articulo = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_articulo');
			var rvcfdi_bayer_financiero_p_zona_de_ventas = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_zona_de_ventas');
			var rvcfdi_bayer_financiero_p_material = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_material');
			var rvcfdi_bayer_financiero_p_atributo_2_sector = $('#realvirtual_woocommerce_configuracion_bayer').find('#rvcfdi_bayer_financiero_p_atributo_2_sector');
			
			data =
			{
				action      										: 'realvirtual_woocommerce_guardar_configuracion_bayer',
				rvcfdi_bayer_facturacion_c_clase_documento			: rvcfdi_bayer_facturacion_c_clase_documento.val(),
				rvcfdi_bayer_facturacion_c_sociedad   				: rvcfdi_bayer_facturacion_c_sociedad.val(),
				rvcfdi_bayer_facturacion_c_moneda       			: rvcfdi_bayer_facturacion_c_moneda.val(),
				rvcfdi_bayer_facturacion_c_tc_cab_doc				: rvcfdi_bayer_facturacion_c_tc_cab_doc.val(),
				rvcfdi_bayer_facturacion_p_cuenta					: rvcfdi_bayer_facturacion_p_cuenta.val(),
				rvcfdi_bayer_facturacion_p_division					: rvcfdi_bayer_facturacion_p_division.val(),
				rvcfdi_bayer_facturacion_p_ce_be					: rvcfdi_bayer_facturacion_p_ce_be.val(),
				rvcfdi_bayer_facturacion_p_texto					: rvcfdi_bayer_facturacion_p_texto.val(),
				rvcfdi_bayer_facturacion_p_pais_destinatario		: rvcfdi_bayer_facturacion_p_pais_destinatario.val(),
				rvcfdi_bayer_facturacion_p_linea_de_producto		: rvcfdi_bayer_facturacion_p_linea_de_producto.val(),
				rvcfdi_bayer_facturacion_p_grupo_de_producto		: rvcfdi_bayer_facturacion_p_grupo_de_producto.val(),
				rvcfdi_bayer_facturacion_p_centro					: rvcfdi_bayer_facturacion_p_centro.val(),
				rvcfdi_bayer_facturacion_p_cliente					: rvcfdi_bayer_facturacion_p_cliente.val(),
				rvcfdi_bayer_facturacion_p_organiz_ventas			: rvcfdi_bayer_facturacion_p_organiz_ventas.val(),
				rvcfdi_bayer_facturacion_p_canal_distrib   			: rvcfdi_bayer_facturacion_p_canal_distrib.val(),
				rvcfdi_bayer_facturacion_p_zoha_de_ventas			: rvcfdi_bayer_facturacion_p_zoha_de_ventas.val(),
				rvcfdi_bayer_facturacion_p_oficina_ventas			: rvcfdi_bayer_facturacion_p_oficina_ventas.val(),
				rvcfdi_bayer_facturacion_p_ramo						: rvcfdi_bayer_facturacion_p_ramo.val(),
				rvcfdi_bayer_facturacion_p_grupo					: rvcfdi_bayer_facturacion_p_grupo.val(),
				rvcfdi_bayer_facturacion_p_gr_vendedores			: rvcfdi_bayer_facturacion_p_gr_vendedores.val(),
				rvcfdi_bayer_facturacion_p_atributo_1_sector		: rvcfdi_bayer_facturacion_p_atributo_1_sector.val(),
				rvcfdi_bayer_facturacion_p_atributo_2_sector		: rvcfdi_bayer_facturacion_p_atributo_2_sector.val(),
				rvcfdi_bayer_facturacion_p_clase_factura			: rvcfdi_bayer_facturacion_p_clase_factura.val(),
				rvcfdi_bayer_financiero_c_clase_de_documento		: rvcfdi_bayer_financiero_c_clase_de_documento.val(),
				rvcfdi_bayer_financiero_c_sociedad       			: rvcfdi_bayer_financiero_c_sociedad.val(),
				rvcfdi_bayer_financiero_c_moneda     				: rvcfdi_bayer_financiero_c_moneda.val(),
				rvcfdi_bayer_financiero_c_t_xt_cab_doc    			: rvcfdi_bayer_financiero_c_t_xt_cab_doc.val(),
				rvcfdi_bayer_financiero_c_cuenta_bancaria      		: rvcfdi_bayer_financiero_c_cuenta_bancaria.val(),
				rvcfdi_bayer_financiero_c_texto    					: rvcfdi_bayer_financiero_c_texto.val(),
				rvcfdi_bayer_financiero_c_division      			: rvcfdi_bayer_financiero_c_division.val(),
				rvcfdi_bayer_financiero_c_cebe  					: rvcfdi_bayer_financiero_c_cebe.val(),
				rvcfdi_bayer_financiero_c_cliente       			: rvcfdi_bayer_financiero_c_cliente.val(),
				rvcfdi_bayer_financiero_p_cuenta       				: rvcfdi_bayer_financiero_p_cuenta.val(),
				rvcfdi_bayer_financiero_p_ind_impuestos       		: rvcfdi_bayer_financiero_p_ind_impuestos.val(),
				rvcfdi_bayer_financiero_p_division					: rvcfdi_bayer_financiero_p_division.val(),
				rvcfdi_bayer_financiero_p_texto      				: rvcfdi_bayer_financiero_p_texto.val(),
				rvcfdi_bayer_financiero_p_cebe       				: rvcfdi_bayer_financiero_p_cebe.val(),
				rvcfdi_bayer_financiero_p_pais_destinatario			: rvcfdi_bayer_financiero_p_pais_destinatario.val(),
				rvcfdi_bayer_financiero_p_linea_de_producto			: rvcfdi_bayer_financiero_p_linea_de_producto.val(),
				rvcfdi_bayer_financiero_p_grupo_de_proudcto			: rvcfdi_bayer_financiero_p_grupo_de_proudcto.val(),
				rvcfdi_bayer_financiero_p_centro					: rvcfdi_bayer_financiero_p_centro.val(),
				rvcfdi_bayer_financiero_p_articulo					: rvcfdi_bayer_financiero_p_articulo.val(),
				rvcfdi_bayer_financiero_p_zona_de_ventas			: rvcfdi_bayer_financiero_p_zona_de_ventas.val(),
				rvcfdi_bayer_financiero_p_material					: rvcfdi_bayer_financiero_p_material.val(),
				rvcfdi_bayer_financiero_p_atributo_2_sector			: rvcfdi_bayer_financiero_p_atributo_2_sector.val()
            }
			
            $.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoConfiguracionBayer').style.visibility = 'hidden';
				var response = JSON.parse(response);
				
				var message = response.message;
				
				if(!response.success)
				{
					mostrarVentanaConfiguracionBayer(((idiomaRVLFECFDI == 'ES') ? 'Ocurrió un error al guardar la configuración. ' + message : 'Failed to save the configuration. ' + message));
				}
				else
				{
					mostrarVentanaConfiguracionBayer(((idiomaRVLFECFDI == 'ES') ? 'Configuración guardada con éxito.':'Configuration saved successfully.'));
				}
            });
			
            return false;
        });
    }
	
	function validarFormularioConfiguracionBayer()
	{
        var respuesta = '';
        
		var version_cfdi = $('#realvirtual_woocommerce_configuracion_bayer').find('#version_cfdi');
		
		if(idioma.val().length == 0)
			respuesta = (idiomaRVLFECFDI == 'ES') ? "Selecciona el idioma del plugin.":"Select the plugin language.";
		
        return respuesta;
    }

	function validarFormularioPaso1()
	{
        var numero_pedido = $("#numero_pedido");
        var monto_pedido = $("#monto_pedido");
		var campoExtra1 = '';
		var campoExtra2 = '';
		
        if(numero_pedido.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa el número del pedido.':'Enter the order number.'));
			return false;
		}
		
        if(monto_pedido.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa el monto del pedido.':'Enter the order amount.'));
			return false;
		}
		else
		{
			monto_pedido = monto_pedido.val().replace(',', '');
			
			if(isNaN(monto_pedido))
			{
				mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El monto del pedido es inválido.':'The order amount is invalid.'));
				return false;
			}
			else
			{
				if(monto_pedido <= 0)
				{
					mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El monto del pedido debe ser mayor a cero.':'The order amount must be greater than zero.'));
					return false;
				}
			}
        }
		
		if($("#campoExtra1").length)
		{
			campoExtra1 = $("#campoExtra1");
			textoCampo = $('#campoExtra1').data('original-title');
			
			if(campoExtra1.val().length == 0)
			{
				mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El campo <b>' + textoCampo + '</b> no puede ser vacío.':'The <b>' + textoCampo + '</b> field can not be empty.'));
				return false;
			}
		}
		
		if($("#campoExtra2").length)
		{
			campoExtra2 = $("#campoExtra2");
			textoCampo = $('#campoExtra2').data('original-title');
			
			if(campoExtra2.val().length == 0)
			{
				mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El campo <b>' + textoCampo + '</b> no puede ser vacío.':'The <b>' + textoCampo + '</b> field can not be empty.'));
				return false;
			}
		}
		
        return true;
    }
	
	function validarFormularioPaso2()
	{
		var receptor_rfc = $("#receptor_rfc");
		var receptor_razon_social = $("#receptor_razon_social");
        var receptor_email = $("#receptor_email");
		var receptor_pais = $("#receptor_pais");
		var receptor_domicilioFiscalReceptor = $("#receptor_domicilioFiscalReceptor");
		var receptor_regimenfiscal = $("#receptor_regimenfiscal");
		
		if(receptor_rfc.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa tu RFC.':'Enter your RFC.'));
			return false;
		}
		
		if(!(receptor_rfc.val().length == 12 || receptor_rfc.val().length == 13))
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El RFC debe tener 12 o 13 caracteres.':'The RFC must have 12 or 13 characters.'));
			return false;
		}
		
		texto = receptor_rfc.val().toUpperCase();
		re = /^[A-Z&Ñ]{3,4}[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{2}[0-9A]$/;
	   
		if (re.test(texto) == false)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El RFC tiene un formato inválido.':'The RFC has an invalid format.'));
			return false;
		}
		
		if(receptor_razon_social.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa tu Razón Social.':'Enter your Business Name.'));
			return false;
		}
		
		if(receptor_domicilioFiscalReceptor.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa el código postal de tu domicilio fiscal.':'Enter the postal code of your tax address.'));
			return false;
		}
		
		if(receptor_regimenfiscal.selectedIndex < 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Selecciona tu régimen fiscal.':'Select your tax regime.'));
			return false;
		}
		
		if(receptor_email.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa tu correo electrónico al que enviaremos tu CFDI en formato PDF y XML.':'Enter your email to which we will send your CFDI in PDF and XML format.'));
			return false;
		}
		
		/*texto = receptor_email.val();
		re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	   
		if (re.test(texto) == false)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El correo electrónico tiene un formato inválido.':'The E-mail has an invalid format.'));
			return false;
		}*/
		
		return true;
    }
	
	function validarFormularioPaso2BuscarRFC()
	{
		var receptor_rfc   = $("#receptor_rfc");
		
		if(receptor_rfc.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa tu RFC.':'Enter your RFC.'));
			return false;
		}
		
		if(!(receptor_rfc.val().length == 12 || receptor_rfc.val().length == 13))
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El RFC debe tener 12 o 13 caracteres.':'The RFC must have 12 or 13 characters.'));
			return false;
		}
		
		texto = receptor_rfc.val().toUpperCase();
		re = /^[A-Z&Ñ]{3,4}[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{2}[0-9A]$/;
	   
		if (re.test(texto) == false)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El RFC tiene un formato inválido.' : 'The RFC has an invalid format.'));
			return false;
		}
		
		return true;
    }
	
	function validarFormularioPaso1Receptor()
	{
		var fr_receptor_rfc = $("#fr_receptor_rfc");
        var fr_receptor_razon_social = $("#fr_receptor_razon_social");
        var fr_receptor_uso_cfdi = $("#fr_receptor_uso_cfdi");
		var fr_receptor_metodos_pago = $("#fr_receptor_metodos_pago");
		var fr_receptor_metodos_pago33 = $("#fr_receptor_metodos_pago33");
		var fr_receptor_domicilioFiscalReceptor = $("#fr_receptor_domicilioFiscalReceptor");
		var fr_receptor_regimenfiscal = $("#fr_receptor_regimenfiscal");
		
        if(fr_receptor_rfc.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa tu RFC.':'Enter your RFC.'));
			return false;
		}
		
		if(!(fr_receptor_rfc.val().length == 12 || fr_receptor_rfc.val().length == 13))
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El RFC debe tener 12 o 13 caracteres.':'The RFC must have 12 or 13 characters.'));
			return false;
		}
		
		texto = fr_receptor_rfc.val().toUpperCase();
		re = /^[A-Z&Ñ]{3,4}[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{2}[0-9A]$/;
	   
		if (re.test(texto) == false)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'El RFC tiene un formato inválido.':'The RFC has an invalid format.'));
			return false;
		}
		
		if (fr_receptor_domicilioFiscalReceptor.length)
		{
			if(fr_receptor_domicilioFiscalReceptor.val().length == 0)
			{
				mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa el código postal de tu domicilio fiscal.':'Enter the postal code of your tax address.'));
				return false;
			}
		}
		
		if (fr_receptor_regimenfiscal.length)
		{
			if(fr_receptor_regimenfiscal.val().length == 0)
			{
				mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Selecciona tu régimen fiscal.':'Select your tax regime.'));
				return false;
			}
		}
		
		if (fr_receptor_regimenfiscal.length)
		{
			if(fr_receptor_razon_social.val().length == 0)
			{
				mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Ingresa tu Razón Social.':'Enter your Business Name.'));
				return false;
			}
		}
		
        if(fr_receptor_uso_cfdi.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Selecciona un Uso de CFDI.':'Select a CFDI Use.'));
			return false;
		}
		
		if(fr_receptor_metodos_pago.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Selecciona una forma de pago.':'Select a Paymente Way.'));
			return false;
		}
		
		if(fr_receptor_metodos_pago33.val().length == 0)
		{
			mostrarVentana(((idiomaRVLFECFDI == 'ES') ? 'Selecciona un Método de Pago.':'Select a Payment Method.'));
			return false;
		}
		
        return true;
    }
	
	$('#paso_uno_formulario').submit(function(e)
	{
        e.preventDefault();
		
		plugin_dir_url = '';
		urlSistemaAsociado = '';
		numero_pedido = '';
		datosPedido = '';
		array_Conceptos = new Array();
		subtotal = '';
		descuento = '';
		total = '';
		array_ImpuestosFederales = new Array();
		array_ImpuestosLocales = new Array();
		xml = '';
		CFDI_ID = '';
		calle_receptor = '';
		estado_receptor = '';
		municipio_receptor = '';
		pais_receptor = '';
		codigoPostal_receptor = '';
		mensajeErroresConceptos = '';
		general_mostrarMensajeErrorCliente = '';
		general_mensajeErrorCliente = '';
		general_complementoCFDI = '';
		general_lugarExpedicion = '';
		
		document.getElementById('cargandoPaso1').style.visibility = 'visible';
		
        if(!validarFormularioPaso1())
		{
			document.getElementById('cargandoPaso1').style.visibility = 'hidden';
			return false;
		}
		
        datosFormulario = $(this).serializeArray();

		var campoExtra1 = '';
		var campoExtra2 = '';
		var textoCampoExtra1 = '';
		var textoCampoExtra2 = '';
		
		if($("#campoExtra1").length)
		{
			campoExtra1 = $("#campoExtra1").val();
			textoCampoExtra1 = $('#campoExtra1').attr('textoCampo');
		}
		
		if($("#campoExtra2").length)
		{
			campoExtra2 = $("#campoExtra2").val();
			textoCampoExtra2 = $('#campoExtra2').attr('textoCampo');
		}

        data = 
		{
			action 			: 'realvirtual_woocommerce_paso_uno',
            numero_pedido   : datosFormulario[0].value,
            monto_pedido    : datosFormulario[1].value.replace(',', ''),
			campoExtra1		: campoExtra1,
			campoExtra2		: campoExtra2,
			textoCampoExtra1 : textoCampoExtra1,
			textoCampoExtra2 : textoCampoExtra2,
			idioma			: idiomaRVLFECFDI
        }

        $.post(myAjax.ajaxurl, data, function(response)
		{
			document.getElementById('cargandoPaso1').style.visibility = 'hidden';
			
            if(!response.success)
			{
				if(response.CFDI_ID > '0')
				{
					CFDI_ID = response.CFDI_ID;
					urlSistemaAsociado = response.urlSistemaAsociado;
					
					$('#paso_cinco_boton_xml').click(function(event)
					{
						event.preventDefault();
						location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarXML&CFDI_ID=' + CFDI_ID + '&IDIOMA=' + idiomaRVLFECFDI;
					});
					
					$('#paso_cinco_boton_pdf').click(function(event)
					{
						event.preventDefault();
						location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarPDF33&CFDI_ID=' + CFDI_ID + '&IDIOMA=' + idiomaRVLFECFDI;
					});
					
					$('#paso_uno').stop().hide();
					$('#paso_cinco').stop().fadeIn('slow');
					return false;
				}
				else
				{
					mostrarVentana(response.message);
					return false;
				}
            }
			
			numero_pedidoRespuesta = response.numero_pedido;
			urlSistemaAsociado = response.urlSistemaAsociado;
			plugin_dir_url = response.plugin_dir_url;
			numero_pedido = response.numero_pedido;
			datosPedido = response.datosPedido;
			
			let versionCFDI = response.versionCFDI;
			
			df_receptor_rfc = response.receptor_rfc;
			df_receptor_razon_social = response.receptor_razon_social;
			
			df_receptor_domicilioFiscalReceptor = response.receptor_domicilioFiscalReceptor;
			df_receptor_regimenfiscal = response.receptor_regimenfiscal;
			
			df_usoCFDIReceptor = response.usoCFDIReceptor;
			df_formaPagoReceptor = response.formaPagoReceptor;
			df_metodoPagoReceptor = response.metodoPagoReceptor;
			
			document.getElementById('receptor_id').value = '';
			document.getElementById('receptor_rfc').value = df_receptor_rfc;
			document.getElementById('receptor_razon_social').value = df_receptor_razon_social;
			
			if(versionCFDI == '4.0')
			{
				document.getElementById('receptor_domicilioFiscalReceptor').value = df_receptor_domicilioFiscalReceptor;
				
				if(typeof df_receptor_regimenfiscal === 'undefined' || df_receptor_regimenfiscal == null || df_receptor_regimenfiscal == '')
					df_receptor_regimenfiscal = '';
				
				if(df_receptor_regimenfiscal != '')
					document.getElementById('receptor_regimenfiscal').value = df_receptor_regimenfiscal;
				else
					document.getElementById('receptor_regimenfiscal').value = '601';
			}
			
			if(df_usoCFDIReceptor != '')
				document.getElementById('paso_3_uso_cfdi').value = df_usoCFDIReceptor;
			
			/*document.getElementById('paso_3_metodos_pago').value = df_formaPagoReceptor;
			document.getElementById('paso_3_metodos_pago33').value = df_metodoPagoReceptor;*/
			
			document.getElementById('receptor_email').value = '';
			
			if(df_receptor_rfc == '' && datosPedido.billing_company.trim() != '')
			{
				document.getElementById('receptor_razon_social').value = datosPedido.billing_company.trim();
			}
			
			if(datosPedido.billing_email != '')
			{
				document.getElementById('receptor_email').value = datosPedido.billing_email;
			}
			
			data = 
			{
				action : 'realvirtual_woocommerce_paso_tres_buscar_emisor'
			}
			
			$.post(myAjax.ajaxurl, data, function(response)
			{
				document.getElementById('cargandoPaso2').style.visibility = 'hidden';
				
				if(!response.success)
				{
					mostrarVentana(response.message);
					return false;
				}
				else
				{
					var emisor_id = response.emisor_id;
					var emisor_rfc = response.emisor_rfc;
					var emisor_razon_social = response.emisor_razon_social;
					var emisor_email = response.emisor_email;
					
					var datos_emisor = '<font size="3"><b>' + ((idiomaRVLFECFDI == 'ES') ? 'EMISOR' : 'ISSUER') + '</b></font>';
					var contacto_emisor = '';
					
					if(emisor_razon_social.length > 0)
						datos_emisor += '<br/>' + emisor_razon_social;
			
					if(emisor_rfc.length > 0)
						datos_emisor += '<br/>' + emisor_rfc;
				
					if(contacto_emisor != '' && emisor_email.length > 0)
						contacto_emisor += '<br/>';
					
					if(emisor_email.length > 0)
						contacto_emisor += 'E-mail: ' + emisor_email;
					
					document.getElementById('paso_3_datos_emisor').innerHTML = datos_emisor + '<br/><font size="2" color="#757575" style="font-style: italic;">' + contacto_emisor + '</font>';
				}
			}, 'json');
			
			document.getElementById('numero_pedido_paso_3').innerHTML = ((idiomaRVLFECFDI == 'ES') ? 'No. Pedido <font color="#d40000">' : 'No. Order <font color="#d40000">') + numero_pedidoRespuesta + '</font>';
			
            $('#paso_uno').stop().hide();
            $('#paso_dos').stop().fadeIn('slow');
			
        }, 'json');

        return true;
    });
	
	$('#paso_dos_formulario').submit(function(e)
	{
        e.preventDefault();
		document.getElementById('cargandoPaso2').style.visibility = 'visible';
		
        if(!validarFormularioPaso2())
		{
			document.getElementById('cargandoPaso2').style.visibility = 'hidden';
			return false;
		}
		
		var receptor_id = document.getElementById('receptor_id').value;
		
		var receptor_rfc = document.getElementById('receptor_rfc').value;
		receptor_rfc = receptor_rfc.toUpperCase();
		document.getElementById('receptor_rfc').value = receptor_rfc;
		
		var receptor_razon_social = document.getElementById('receptor_razon_social').value;
		
		var receptor_email = document.getElementById('receptor_email').value;
		
		var datos_receptor = '<font size="3"><b>' + ((idiomaRVLFECFDI == 'ES') ? 'RECEPTOR' : 'RECEIVER') + '</b></font>';
		var contacto_receptor = '';
		
		if(receptor_razon_social.length > 0)
			datos_receptor += '<br/>' + receptor_razon_social;
	
		if(receptor_rfc.length > 0)
			datos_receptor += '<br/>' + receptor_rfc;
		
		var receptor_domicilioFiscalReceptor = document.getElementById('receptor_domicilioFiscalReceptor');
		var receptor_regimenfiscal = document.getElementById('receptor_regimenfiscal');
		
		if(receptor_domicilioFiscalReceptor != null && receptor_domicilioFiscalReceptor != '')
			datos_receptor += '<br/>C.P. ' + receptor_domicilioFiscalReceptor.value;
		if(receptor_regimenfiscal != null && receptor_regimenfiscal != '')
			datos_receptor += '<br/>Reg. Fiscal: ' + receptor_regimenfiscal.value;
		
		if(contacto_receptor != '' && receptor_email.length > 0)
			contacto_receptor += '<br/>';
		
		if(receptor_email.length > 0)
			contacto_receptor += 'E-mail: ' + receptor_email;
		
		var domicilio_receptor = '';
		var cfdi_domicilio_receptor = document.getElementById('cfdi_domicilio_receptor').value;
		console.log("cfdi_domicilio_receptor: " + cfdi_domicilio_receptor);
		if(cfdi_domicilio_receptor == '1')
		{
			if(datosPedido.billing_address_1 != '')
			{
				domicilio_receptor += datosPedido.billing_address_1;
				calle_receptor = datosPedido.billing_address_1;
			}
			
			if(domicilio_receptor != '')
				domicilio_receptor += ', ';
			
			if(datosPedido.billing_state != '')
			{
				domicilio_receptor += datosPedido.billing_state;
				estado_receptor = datosPedido.billing_state;
			}
			
			if(domicilio_receptor != '')
				domicilio_receptor += ', ';
			
			if(datosPedido.billing_city != '')
			{
				domicilio_receptor += datosPedido.billing_city;
				municipio_receptor = datosPedido.billing_city;
			}
			
			if(domicilio_receptor != '')
				domicilio_receptor += ', ';
			
			if(datosPedido.billing_country != '')
			{
				domicilio_receptor += datosPedido.billing_country;
				pais_receptor = datosPedido.billing_country;
			}
			
			if(domicilio_receptor != '')
				domicilio_receptor += ', ';
			
			if(datosPedido.billing_postcode != '')
			{
				domicilio_receptor += datosPedido.billing_postcode;
				codigoPostal_receptor = datosPedido.billing_postcode;
			}
		}
		
		document.getElementById('paso_3_datos_receptor').innerHTML = datos_receptor + '<br/>' + domicilio_receptor + '<br/><font size="2" color="#757575" style="font-style: italic;">' + contacto_receptor + '</font>';
		
		document.getElementById('cargandoPaso2').style.visibility = 'visible';
		
		//Obtener datos de configuración
		var objeto_imp_shipping = document.getElementById('objeto_imp_shipping').value;
		var objeto_imp_producto = document.getElementById('objeto_imp_producto').value;
		var producto_clave_servicio = document.getElementById('producto_clave_servicio').value;
		var producto_clave_unidad = document.getElementById('producto_clave_unidad').value;
		var producto_unidad_medida = document.getElementById('producto_unidad_medida').value;
		var producto_clave_producto = document.getElementById('producto_clave_producto').value;
		var producto_numero_pedimento = document.getElementById('producto_numero_pedimento').value;
		var shipping_clave_servicio = document.getElementById('shipping_clave_servicio').value;
		var shipping_clave_unidad = document.getElementById('shipping_clave_unidad').value;
		var shipping_unidad_medida = document.getElementById('shipping_unidad_medida').value;
		var shipping_clave_producto = document.getElementById('shipping_clave_producto').value;
		var shipping_numero_pedimento = document.getElementById('shipping_numero_pedimento').value;
		var cfdi_precision_decimal = document.getElementById('cfdi_precision_decimal').value;
		var cfdi_manejo_impuestos_pedido = document.getElementById('cfdi_manejo_impuestos_pedido').value;
		var shipping_config_principal = document.getElementById('shipping_config_principal').value;
		var cfdi_huso_horario = document.getElementById('cfdi_huso_horario').value;
		var mostrarMensajeErrorCliente = document.getElementById('mostrarMensajeErrorCliente').value;
		var mensajeErrorCliente = document.getElementById('mensajeErrorCliente').value;
		var complementoCFDI = document.getElementById('cfdi_complementoCFDI').value;
		var complementoCFDI_iedu_configuracion_nivel = document.getElementById('cfdi_complementoCFDI_iedu_configuracion_nivel').value;
		var complementoCFDI_iedu_configuracion_autRVOE = document.getElementById('cfdi_complementoCFDI_iedu_configuracion_autRVOE').value;
		
		general_mostrarMensajeErrorCliente = mostrarMensajeErrorCliente;
		general_mensajeErrorCliente = mensajeErrorCliente;
		general_complementoCFDI = complementoCFDI;
		
		general_lugarExpedicion = datosPedido.lugarExpedicion;
		
		if(cfdi_huso_horario === undefined || cfdi_huso_horario == null || cfdi_huso_horario.length <= 0)
			cfdi_huso_horario = 'America/Mexico_City';
		
		if(cfdi_precision_decimal === undefined || cfdi_precision_decimal == null || cfdi_precision_decimal.length <= 0)
			cfdi_precision_decimal = '2';
		
		if(cfdi_manejo_impuestos_pedido === undefined || cfdi_manejo_impuestos_pedido == null || cfdi_manejo_impuestos_pedido.length <= 0)
			cfdi_manejo_impuestos_pedido = '0';
		
		if(shipping_config_principal === undefined || shipping_config_principal == null || shipping_config_principal.length <= 0)
			shipping_config_principal = '0';
		
		//Obtener datos del pedido de WooCommerce
		let WC_Subtotal = Number(datosPedido.subtotal);
		let WC_Descuento = Number(datosPedido.total_discount);
		let WC_Total = Number(datosPedido.total);
		let WC_Conceptos = datosPedido.line_items;
		let WC_Impuestos = datosPedido.impuestos;
		let WC_SubtotalOriginal = Number(0);
		let WC_Cupones = Number(datosPedido.total_coupons);
		
		let tasaIVA = '';
		let importeTotalIVA = 0;
		let array_ImpuestosRecalculados = new Array();
		
		let tablaHTML_Conceptos = '';
		let tablaHTML_Totales = '';
		let tablaHTML_Impuestos = '';
		
		console.log('Configuraciones');
		console.log('Manejo Impuestos: ' + cfdi_manejo_impuestos_pedido);
		console.log('shipping_config_principal: ' + shipping_config_principal);
		console.log('cfdi_huso_horario: ' + cfdi_huso_horario);
		console.log('lugarExpedicion: ' + general_lugarExpedicion);
		
		if(cfdi_manejo_impuestos_pedido == '1'/* || cfdi_manejo_impuestos_pedido == '4'*/)
			tasaIVA = '16';
		else if(cfdi_manejo_impuestos_pedido == '2'/* || cfdi_manejo_impuestos_pedido == '5'*/)
			tasaIVA = '08';
		else if(cfdi_manejo_impuestos_pedido == '3'/* || cfdi_manejo_impuestos_pedido == '6'*/)
			tasaIVA = '00';
		
		if(cfdi_manejo_impuestos_pedido == '1' || cfdi_manejo_impuestos_pedido == '2' || cfdi_manejo_impuestos_pedido == '3')
		{
			WC_Descuento = Number(0);
			WC_Cupones = Number(0);
		}
		else if(cfdi_manejo_impuestos_pedido == '4' || cfdi_manejo_impuestos_pedido == '5' || cfdi_manejo_impuestos_pedido == '6')
		{
			/*console.log("-------------------------------------");
			console.log("Calculando nuevo subtotal y nuevo IVA (PASO 1 y PASO 2)");
			console.log("-------------------------------------");
			WC_SubtotalOriginal = WC_Subtotal - WC_Descuento;
			console.log("Subtotal de WooCommerce (sin descuentos): " + WC_SubtotalOriginal);
			WC_Subtotal = Number(WC_Total / Number('1.' + tasaIVA));
			WC_Subtotal = Number(parseFloat(WC_Subtotal).toFixed(cfdi_precision_decimal));
			console.log("Nuevo Subtotal = Total / 1." + tasaIVA);
			console.log("Nuevo Subtotal = " + WC_Total + " / 1." + tasaIVA);
			console.log("Nuevo Subtotal (" + cfdi_precision_decimal + " decimales) = " + WC_Subtotal);
			console.log("Nuevo IVA = Total - Nuevo Subtotal");
			console.log("Nuevo IVA = " + WC_Total + " - " + WC_Subtotal);
			importeTotalIVA = WC_Total - WC_Subtotal;
			importeTotalIVA = Number(parseFloat(importeTotalIVA).toFixed(cfdi_precision_decimal));
			console.log("Nuevo IVA (" + cfdi_precision_decimal + " decimales) = " + importeTotalIVA);
			WC_Descuento = 0;*/
			//WC_Descuento = 0;
			
			for(let i = 0; i < WC_Impuestos.length; i++)
			{
				let codigoImpuestoSAT = WC_Impuestos[i]['codigoImpuestoSAT'];
				let nombreImpuesto = WC_Impuestos[i]['impuesto'];
				let importeImpuesto = Number(WC_Impuestos[i]['importe']);
				let tasaPorcentaje = Number(WC_Impuestos[i]['tasaPorcentaje']);
				let naturaleza = WC_Impuestos[i]['naturaleza'];
				
				array_ImpuestosRecalculados[i] = new Array(6);
				array_ImpuestosRecalculados[i][0] = naturaleza; //1 traslado, 2 retencion.
				array_ImpuestosRecalculados[i][1] = nombreImpuesto;
				array_ImpuestosRecalculados[i][2] = tasaPorcentaje;
				array_ImpuestosRecalculados[i][3] = Number(0);
				array_ImpuestosRecalculados[i][4] = (nombreImpuesto == 'IVA EXENTO') ? 'Exento' : 'Tasa';
				array_ImpuestosRecalculados[i][5] = codigoImpuestoSAT;
			}
		}
		
        let resultado = LeerConceptosPorDefecto(WC_Conceptos, WC_Subtotal, cfdi_precision_decimal, cfdi_manejo_impuestos_pedido, array_ImpuestosRecalculados, producto_clave_servicio, producto_clave_unidad, producto_unidad_medida, producto_clave_producto, producto_numero_pedimento, tasaIVA, shipping_clave_servicio, shipping_clave_unidad, shipping_unidad_medida, shipping_clave_producto, shipping_numero_pedimento, shipping_config_principal, objeto_imp_shipping, objeto_imp_producto);
		console.log(resultado.tablaHTML_Conceptos);
		document.getElementById('conceptos_tabla').innerHTML = resultado.tablaHTML_Conceptos;
		
		if(cfdi_manejo_impuestos_pedido == '0')
		{
			WC_Subtotal = resultado.WC_Subtotal;
		}
		
		if(cfdi_manejo_impuestos_pedido == '4' || cfdi_manejo_impuestos_pedido == '5' || cfdi_manejo_impuestos_pedido == '6')
		{
			WC_Subtotal = resultado.WC_Subtotal;
			WC_Descuento = resultado.WC_Descuento;
			WC_Cupones = Number(0);
			importeTotalIVA = resultado.importeTotalIVA;
			array_ImpuestosRecalculados = resultado.array_ImpuestosRecalculados;
		}
		
		if(cfdi_manejo_impuestos_pedido == '1' || cfdi_manejo_impuestos_pedido == '2' || cfdi_manejo_impuestos_pedido == '3')
		{
			WC_Subtotal = resultado.WC_Subtotal;
			importeTotalIVA = resultado.importeTotalIVA;
		}
	
		tablaHTML_Totales += '<tr><td style="text-align:right; width:80%;"><b>SUBTOTAL</b></td><td style="text-align:right; width:20%;">';
		tablaHTML_Totales += '$' + WC_Subtotal.formatMoney(2, '.', ',');
        tablaHTML_Totales += '</td></tr>';
		
		tablaHTML_Totales += '<tr><td style="text-align:right; width:80%;"><b>' + ((idiomaRVLFECFDI == 'ES') ? 'DESCUENTO':'DISCOUNT') + '</b></td><td style="text-align:right; width:20%;">';
		tablaHTML_Totales += '$' + WC_Descuento.formatMoney(2, '.', ',');
		tablaHTML_Totales += '</td></tr>';
		
		if(WC_Cupones > 0)
		{
			tablaHTML_Totales += '<tr><td style="text-align:right; width:80%;"><b>' + ((idiomaRVLFECFDI == 'ES') ? 'CUPONES':'COUPONS') + '</b></td><td style="text-align:right; width:20%;">';
			tablaHTML_Totales += '$' + WC_Cupones.formatMoney(2, '.', ',');
			tablaHTML_Totales += '</td></tr>';
		}
		
		let subtotalNeto = Number(WC_Subtotal - WC_Descuento - WC_Cupones);
		
		tablaHTML_Totales += '<tr><td style="text-align:right; width:80%;"><b>' + ((idiomaRVLFECFDI == 'ES') ? 'SUBTOTAL NETO':'NET SUBTOTAL') + '</b></td><td style="text-align:right; width:20%;">';
		tablaHTML_Totales += '$' + subtotalNeto.formatMoney(2, '.', ',');
		tablaHTML_Totales += '</td></tr>';
		
		if(cfdi_manejo_impuestos_pedido == '0')
		{
			for(var i = 0; i < WC_Impuestos.length; i++)
			{
				let codigoImpuestoSAT = WC_Impuestos[i]['codigoImpuestoSAT'];
				let nombreImpuesto = WC_Impuestos[i]['impuesto'];
				let importeImpuesto = Number(WC_Impuestos[i]['importe']);
				let tasaPorcentaje = Number(WC_Impuestos[i]['tasaPorcentaje']);
				let naturaleza = WC_Impuestos[i]['naturaleza'];
				
				let font = '<font size="2">';
				
				let hayImpuestosErroneos = false;
				
				if(importeImpuesto !== 0 && tasaPorcentaje === 0)
				{
					tasaPorcentaje = Number((importeImpuesto / subtotalNeto) * 100);
					font = '<font size="2" color="#e20000">';
					hayImpuestosErroneos = true;
				}
				
				tablaHTML_Impuestos += '<tr><td style="text-align:right; width:80%;">' + font +'<b>' + nombreImpuesto + ' ' + Number(parseFloat(tasaPorcentaje).toFixed(cfdi_precision_decimal)).formatMoney(cfdi_precision_decimal, '.', ',') + '%</b></font></td><td style="text-align:right; width:20%;">';
				tablaHTML_Impuestos += font + '$' + Number(parseFloat(importeImpuesto).toFixed(2)).formatMoney(2, '.', ',');
				tablaHTML_Impuestos += '</font></td></tr>';
				
				if(hayImpuestosErroneos)
					document.getElementById('notaImportanteCFDI').innerHTML = '<font size="3" color="#e20000"><b>' + ((idiomaRVLFECFDI == 'ES') ? 'AVISO IMPORTANTE':'IMPORTANT NOTICE')+ '</b></font><br/><font size="2">' + ((idiomaRVLFECFDI == 'ES') ? 'Se encontraron errores con los impuestos en color rojo. Los impuestos fueron editados o eliminados desde WooCommerce y no es posible obtener la tasa original. Sin embargo, se intentó calcular la tasa de cada uno internamente pero es necesario contactar a tu proveedor para confirmar que estén antes de generar el CFDI. En caso de que las tasas de los impuestos no estén bien, será necesario editar o rehacer el pedido de manera correcta.':'Errors were found with taxes in red. Taxes were edited or deleted from WooCommerce and it is not possible to obtain the original fee. However, an attempt was made to calculate the rate of each internally but it is necessary to contact your provider to confirm that they are before generating the CFDI. In case the tax rates are not right, it will be necessary to edit or redo the order correctly.') + '</font>';
				
				if(codigoImpuestoSAT != 'ISH')
				{
					array_ImpuestosFederales[array_ImpuestosFederales.length] = new Array(6);
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][0] = naturaleza; //1 traslado, 2 retencion.
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][1] = nombreImpuesto;
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][2] = parseFloat(Number(parseFloat(tasaPorcentaje).toFixed(6)) / 100).toFixed(6);
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][3] = Number(parseFloat(importeImpuesto).toFixed(2));
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][4] = (nombreImpuesto == 'IVA EXENTO') ? 'Exento' : 'Tasa';
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][5] = codigoImpuestoSAT;
				}
				else
				{
					array_ImpuestosLocales[array_ImpuestosLocales.length] = new Array(4);
					array_ImpuestosLocales[array_ImpuestosLocales.length - 1][0] = naturaleza;
					array_ImpuestosLocales[array_ImpuestosLocales.length - 1][1] = codigoImpuestoSAT;
					array_ImpuestosLocales[array_ImpuestosLocales.length - 1][2] = parseFloat(Number(parseFloat(tasaPorcentaje).toFixed(6)) / 100).toFixed(6);
					array_ImpuestosLocales[array_ImpuestosLocales.length - 1][3] = Number(parseFloat(importeImpuesto).toFixed(2));
				}
			}
		}
        else if(cfdi_manejo_impuestos_pedido == '1' || cfdi_manejo_impuestos_pedido == '2' || cfdi_manejo_impuestos_pedido == '3')
		{
			let font = '<font size="2">';
			
			tablaHTML_Impuestos += '<tr><td style="text-align:right; width:80%;">' + font +'<b>IVA ' + Number(parseFloat(tasaIVA).toFixed(cfdi_precision_decimal)).formatMoney(cfdi_precision_decimal, '.', ',') + '%</b></font></td><td style="text-align:right; width:20%;">';
			tablaHTML_Impuestos += font + '$' + Number(parseFloat(importeTotalIVA).toFixed(2)).formatMoney(2, '.', ',');
			tablaHTML_Impuestos += '</font></td></tr>';
			
			array_ImpuestosFederales[0] = new Array(6);
			array_ImpuestosFederales[0][0] = '1'; //1 traslado, 2 retencion.
			array_ImpuestosFederales[0][1] = 'IVA';
			array_ImpuestosFederales[0][2] = parseFloat(Number(parseFloat(tasaIVA).toFixed(6)) / 100).toFixed(6);
			array_ImpuestosFederales[0][3] = importeTotalIVA;
			array_ImpuestosFederales[0][4] = 'Tasa';
			array_ImpuestosFederales[0][5] = '002';
			
			if(cfdi_manejo_impuestos_pedido == '1' || cfdi_manejo_impuestos_pedido == '2' || cfdi_manejo_impuestos_pedido == '3')
				WC_Total = subtotalNeto + Number(importeTotalIVA);
		}
		else if(cfdi_manejo_impuestos_pedido == '4' || cfdi_manejo_impuestos_pedido == '5' || cfdi_manejo_impuestos_pedido == '6')
		{
			let importeTotalImpuestos = 0;
			
			for(let i = 0; i < array_ImpuestosRecalculados.length; i++)
			{
				let codigoImpuestoSAT = array_ImpuestosRecalculados[i][5];
				let nombreImpuesto = array_ImpuestosRecalculados[i][1];
				let importeImpuesto = Number(array_ImpuestosRecalculados[i][3]);
				let tasaPorcentaje = Number(array_ImpuestosRecalculados[i][2]);
				let naturaleza = array_ImpuestosRecalculados[i][0];
				
				let font = '<font size="2">';
			
				tablaHTML_Impuestos += '<tr><td style="text-align:right; width:80%;">' + font +'<b>' + nombreImpuesto + ' ' + Number(parseFloat(tasaPorcentaje).toFixed(cfdi_precision_decimal)).formatMoney(cfdi_precision_decimal, '.', ',') + '%</b></font></td><td style="text-align:right; width:20%;">';
				tablaHTML_Impuestos += font + '$' + Number(parseFloat(importeImpuesto).toFixed(2)).formatMoney(2, '.', ',');
				tablaHTML_Impuestos += '</font></td></tr>';
			
				if(codigoImpuestoSAT != 'ISH')
				{
					array_ImpuestosFederales[array_ImpuestosFederales.length] = new Array(6);
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][0] = naturaleza; //1 traslado, 2 retencion.
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][1] = nombreImpuesto;
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][2] = parseFloat(Number(parseFloat(tasaPorcentaje).toFixed(6)) / 100).toFixed(6);
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][3] = Number(parseFloat(importeImpuesto).toFixed(2));
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][4] = (nombreImpuesto == 'IVA EXENTO') ? 'Exento' : 'Tasa';
					array_ImpuestosFederales[array_ImpuestosFederales.length - 1][5] = codigoImpuestoSAT;
				}
				else
				{
					array_ImpuestosLocales[array_ImpuestosLocales.length] = new Array(4);
					array_ImpuestosLocales[array_ImpuestosLocales.length - 1][0] = naturaleza;
					array_ImpuestosLocales[array_ImpuestosLocales.length - 1][1] = codigoImpuestoSAT;
					array_ImpuestosLocales[array_ImpuestosLocales.length - 1][2] = parseFloat(Number(parseFloat(tasaPorcentaje).toFixed(6)) / 100).toFixed(6);
					array_ImpuestosLocales[array_ImpuestosLocales.length - 1][3] = Number(parseFloat(importeImpuesto).toFixed(2));
				}
				
				if(cfdi_manejo_impuestos_pedido == '5')
				{
					if(nombreImpuesto == 'IVA' || nombreImpuesto == 'IEPS' || nombreImpuesto == 'ISH')
						importeTotalImpuestos = Number(importeTotalImpuestos) + Number(importeImpuesto);
					else if(nombreImpuesto == 'IVA RETENIDO' || nombreImpuesto == 'IEPS RETENIDO' || nombreImpuesto == 'ISR')
						importeTotalImpuestos = Number(importeTotalImpuestos) - Number(importeImpuesto);
				}
			}
			
			if(cfdi_manejo_impuestos_pedido == '5')
				WC_Total = WC_Subtotal - WC_Descuento - WC_Cupones + importeTotalImpuestos;
		}
		
		tablaHTML_Totales += tablaHTML_Impuestos;
		tablaHTML_Totales += '<tr><td style="text-align:right; width:80%;"><b>TOTAL</b></td><td style="text-align:right; width:20%;">';
		tablaHTML_Totales += '$' + (WC_Total).formatMoney(2, '.', ',');
		tablaHTML_Totales += '</td></tr>';
		
		document.getElementById('totales_cuerpo_tabla').innerHTML = tablaHTML_Totales;
		document.getElementById('cargandoPaso2').style.visibility = 'hidden';
		
		subtotal = WC_Subtotal;
		total = WC_Total;
		descuento = WC_Descuento + WC_Cupones;
		arrayConceptos = array_Conceptos;
		
		$('#paso_dos').stop().hide();
		$('#paso_tres').stop().fadeIn('slow');			
		
        return true;
    });
	
	function LeerConceptosPorDefecto(WC_Conceptos, WC_Subtotal, cfdi_precision_decimal, cfdi_manejo_impuestos_pedido, array_ImpuestosRecalculados,
		producto_clave_servicio, producto_clave_unidad, producto_unidad_medida, producto_clave_producto, producto_numero_pedimento, tasaIVA,
		shipping_clave_servicio, shipping_clave_unidad, shipping_unidad_medida, shipping_clave_producto, shipping_numero_pedimento, shipping_config_principal,
		objeto_imp_shipping, objeto_imp_producto)
	{
		let tablaHTML_Conceptos = '';
		let importeTotalIVA = 0;
		let array_FactoresProporcion = new Array();
		array_Conceptos = new Array();
		mensajeErroresConceptos = '';
		
		tablaHTML_Conceptos = '<table border="1" style="background-color:#FFFFFF; border-color:#dedede;" width="100%">';
		tablaHTML_Conceptos += '<thead><tr>';
		tablaHTML_Conceptos += (idiomaRVLFECFDI == 'ES') ? '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Descripción</b></td>' : '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Description</b></td>';
		tablaHTML_Conceptos += (idiomaRVLFECFDI == 'ES') ? '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Precio Unitario</b></td>' : '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Unit Price</b></td>';
		tablaHTML_Conceptos += (idiomaRVLFECFDI == 'ES') ? '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Cantidad</b></td>' : '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Quantity</b></td>';
		tablaHTML_Conceptos += (idiomaRVLFECFDI == 'ES') ? '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Descuento</b></td>' : '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Discount</b></td>';
		tablaHTML_Conceptos += (idiomaRVLFECFDI == 'ES') ? '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Importe</b></td>' : '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Amount</b></td>';
		tablaHTML_Conceptos += (idiomaRVLFECFDI == 'ES') ? '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Impuestos</b></td>' : '<td style="text-align:center; border-color: #dedede; background-color: #ececec;"><b>Taxes</b></td>';
		tablaHTML_Conceptos += '</tr></thead><tbody>';
		
		let nuevoSubtotal = Number(0);
		let nuevoDescuento = Number(0);
		for(let i = 0; i < WC_Conceptos.length; i++)
		{
			let importeIVAConcepto = "";
			let importeIEPSConcepto = "";
			let importeIVARetenidoConcepto = "";
			let importeIEPSRetenidoConcepto = "";
			let importeISRConcepto = "";
			let importeISHConcepto = "";
			
			let tasaIVAConcepto = "";
			let tasaIEPSConcepto = "";
			let tasaIVARetenidoConcepto = "";
			let tasaIEPSRetenidoConcepto = "";
			let tasaISRConcepto = "";
			let tasaISHConcepto = "";
			
			let tasaPorcentajeIVAConcepto = "";
			let tasaPorcentajeIEPSConcepto = "";
			let tasaPorcentajeIVARetenidoConcepto = "";
			let tasaPorcentajeIEPSRetenidoConcepto = "";
			let tasaPorcentajeISRConcepto = "";
			let tasaPorcentajeISHConcepto = "";
			
			let nombre = WC_Conceptos[i]['name'].replace("ʺ", "\"");
			
			console.log('TipoConcepto: ' + WC_Conceptos[i]['tipoConcepto']);
			console.log('shipping_config_principal: ' + shipping_config_principal);
			if(shipping_config_principal == '0' && WC_Conceptos[i]['tipoConcepto'] == 'shipping')
			{
				continue;
				console.log('No se agregó el concepto de tipo Shipping');
			}
			
			let objetoImpuesto = '';
			
			if(WC_Conceptos[i]['objeto_impuesto'] === undefined || WC_Conceptos[i]['objeto_impuesto'] == null || WC_Conceptos[i]['objeto_impuesto'] == '')
				objetoImpuesto = (WC_Conceptos[i]['tipoConcepto'] == 'shipping') ? objeto_imp_shipping : objeto_imp_producto;
			else
				objetoImpuesto = WC_Conceptos[i]['objeto_impuesto'];
			
			let claveServicio = '';
			
			if(WC_Conceptos[i]['clave_servicio'] === undefined || WC_Conceptos[i]['clave_servicio'] == null || WC_Conceptos[i]['clave_servicio'] == '')
				claveServicio = (WC_Conceptos[i]['tipoConcepto'] == 'shipping') ? shipping_clave_servicio : producto_clave_servicio;
			else 
				claveServicio = WC_Conceptos[i]['clave_servicio'];
			
			let claveUnidad = '';
			
			if(WC_Conceptos[i]['clave_unidad'] === undefined || WC_Conceptos[i]['clave_unidad'] == null || WC_Conceptos[i]['clave_unidad'] == '')
				claveUnidad = (WC_Conceptos[i]['tipoConcepto'] == 'shipping') ? shipping_clave_unidad : producto_clave_unidad;
			else 
				claveUnidad = WC_Conceptos[i]['clave_unidad'];
			
			let unidadMedida = '';
			
			if(WC_Conceptos[i]['unidad_medida'] === undefined || WC_Conceptos[i]['unidad_medida'] == null || WC_Conceptos[i]['unidad_medida'] == '')
				unidadMedida = (WC_Conceptos[i]['tipoConcepto'] == 'shipping') ? shipping_unidad_medida : producto_unidad_medida;
			else 
				unidadMedida = WC_Conceptos[i]['unidad_medida'];
			
			let claveProducto = '';
			
			if(WC_Conceptos[i]['clave_producto'] === undefined || WC_Conceptos[i]['clave_producto'] == null || WC_Conceptos[i]['clave_producto'] == '')
				claveProducto = (WC_Conceptos[i]['tipoConcepto'] == 'shipping') ? shipping_clave_producto : producto_clave_producto;
			else 
				claveProducto = WC_Conceptos[i]['clave_producto'];
			
			let noPedimento = '';
			
			if(WC_Conceptos[i]['numero_pedimento'] === undefined || WC_Conceptos[i]['numero_pedimento'] == null || WC_Conceptos[i]['numero_pedimento'] == '')
				noPedimento = (WC_Conceptos[i]['tipoConcepto'] == 'shipping') ? shipping_numero_pedimento : producto_numero_pedimento;
			else 
				noPedimento = WC_Conceptos[i]['numero_pedimento'];
			
			let precioUnitario = Number(WC_Conceptos[i]['subtotal']);
			let cantidad = Number(WC_Conceptos[i]['quantity']);
			let importeUnitario = Number(WC_Conceptos[i]['total']);
			let impuestosConcepto = WC_Conceptos[i]['impuestos'];
			let descuentoUnitario = Number(0);
			let textoImpuestos = '';
			let columnaTextoImpuestos = '';
			let importeConcepto = Number(WC_Conceptos[i]['importe']);
			
			if(cfdi_manejo_impuestos_pedido == '0')
				precioUnitario = Number(WC_Conceptos[i]['subtotal2']);
			
			if(cfdi_manejo_impuestos_pedido == '0')
			{
				descuentoUnitario = (WC_Conceptos[i]['descuento'] == '') ? (importeConcepto - Number(WC_Conceptos[i]['total'])) : Number(WC_Conceptos[i]['descuento']);
				let descripcionImpuestos = '';
				
				nuevoSubtotal = Number(nuevoSubtotal) + importeConcepto;
				
				for(var k = 0; k < impuestosConcepto.length; k++)
				{
					let nombreImpuesto = impuestosConcepto[k]['impuesto'];
					let importeImpuesto = Number(impuestosConcepto[k]['importe']);
					let tasaImpuesto = Number(impuestosConcepto[k]['tasa']);
					let tasaPorcentajeImpuesto = Number(impuestosConcepto[k]['tasaPorcentaje']);
					
					if(nombreImpuesto == 'IVA' && importeIVAConcepto == '')
					{
						importeIVAConcepto = importeImpuesto;
						tasaIVAConcepto = tasaImpuesto;
						tasaPorcentajeIVAConcepto = tasaPorcentajeImpuesto;
					}
					else if(nombreImpuesto == 'IVA EXENTO' && importeIVAConcepto == '')
					{
						importeIVAConcepto = Number(-1);
						tasaIVAConcepto = Number(-1);
						tasaPorcentajeIVAConcepto = Number(-1);
					}
					else if(nombreImpuesto == 'IEPS' && importeIEPSConcepto == '')
					{
						importeIEPSConcepto = importeImpuesto;
						tasaIEPSConcepto = tasaImpuesto;
						tasaPorcentajeIEPSConcepto = tasaPorcentajeImpuesto;
					}
					else if(nombreImpuesto == 'IVA RETENIDO' && importeIVARetenidoConcepto == '')
					{
						importeIVARetenidoConcepto = importeImpuesto;
						tasaIVARetenidoConcepto = tasaImpuesto;
						tasaPorcentajeIVARetenidoConcepto = tasaPorcentajeImpuesto;
					}
					else if(nombreImpuesto == 'IEPS RETENIDO' && importeIEPSRetenidoConcepto == '')
					{
						importeIEPSRetenidoConcepto = importeImpuesto;
						tasaIEPSRetenidoConcepto = tasaImpuesto;
						tasaPorcentajeIEPSRetenidoConcepto = tasaPorcentajeImpuesto;
					}
					else if(nombreImpuesto == 'ISR' && importeISRConcepto == '')
					{
						importeISRConcepto = importeImpuesto;
						tasaISRConcepto = tasaImpuesto;
						tasaPorcentajeISRConcepto = tasaPorcentajeImpuesto;
					}
					
					if(importeIVAConcepto >= 0 || importeIEPSConcepto >= 0 || importeIVARetenidoConcepto >= 0 || importeIEPSRetenidoConcepto >= 0 || importeISRConcepto >= 0)
					{	
						if(descripcionImpuestos != '')
							descripcionImpuestos += ', ';
				
						descripcionImpuestos += nombreImpuesto + '-' + tasaPorcentajeImpuesto + ' $' + Number(parseFloat(importeImpuesto).toFixed(2)).formatMoney(2, '.', ',');
					}
				}
				
				textoImpuestos = '<td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">' + descripcionImpuestos + '</td>';
				columnaTextoImpuestos = textoImpuestos;
			}
			else if(cfdi_manejo_impuestos_pedido == '1' || cfdi_manejo_impuestos_pedido == '2' || cfdi_manejo_impuestos_pedido == '3')
			{
				precioUnitario = Number(importeUnitario / Number('1.' + tasaIVA));
				precioUnitario = Number(parseFloat(precioUnitario).toFixed(cfdi_precision_decimal));
				
				//WC_Subtotal = Number(WC_Subtotal) + Number(precioUnitario);
				nuevoSubtotal = Number(nuevoSubtotal) + precioUnitario;
				
				let IVAUnitario = importeUnitario - precioUnitario;
				IVAUnitario = Number(parseFloat(IVAUnitario).toFixed(cfdi_precision_decimal));
				importeTotalIVA = Number(importeTotalIVA) + IVAUnitario;
				importeUnitario = precioUnitario;
				importeConcepto = precioUnitario;
				precioUnitario = Number(precioUnitario / cantidad);
				
				importeIVAConcepto = IVAUnitario;
				tasaIVAConcepto = parseFloat(Number(parseFloat(Number(tasaIVA)).toFixed(6)) / 100).toFixed(6);
				tasaPorcentajeIVAConcepto = tasaIVA;
				
				textoImpuestos = '<td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">IVA-' + tasaIVA + ' $' + Number(parseFloat(IVAUnitario).toFixed(2)).formatMoney(2, '.', ',') + '</td>';
				columnaTextoImpuestos = textoImpuestos;
			}
			else if(cfdi_manejo_impuestos_pedido == '4' || cfdi_manejo_impuestos_pedido == '5' || cfdi_manejo_impuestos_pedido == '6')
			{
				/*
				importeUnitario = Number(WC_Conceptos[i]['total']);
				importeConcepto = Number(WC_Conceptos[i]['price']) * cantidad; //Number(WC_Conceptos[i]['subtotal2']) * cantidad;
				//importeUnitario = importeConcepto;
				
				//Recalcular descuento
				descuentoUnitario = importeConcepto - importeUnitario;
				
				if(descuentoUnitario < 0)
				{
					importeUnitario = importeConcepto;
					descuentoUnitario = Number(parseFloat(0).toFixed(6));
					
					if(mensajeErroresConceptos != '')
					{
						mensajeErroresConceptos += '<br/><br/>';
					}
					
					mensajeErroresConceptos += 'Hay un problema con el concepto <b>' + nombre + '</b> porque su Importe Original en el pedido en WooCommerce ' + importeUnitario + ' es incorrecto porque el resultado de multiplicar el Precio Unitario (' + precioUnitario + ') por la Cantidad (' + cantidad + ') debe ser ' + importeConcepto;
				}
				else
				{
					descuentoUnitario = Number(parseFloat(descuentoUnitario).toFixed(6));
				}
				
				nuevoDescuento = Number(nuevoDescuento) + Number(parseFloat(descuentoUnitario).toFixed(2));
				
				//Recalcular precio unitario
				precioUnitario = Number(WC_Conceptos[i]['price']); //Number(WC_Conceptos[i]['subtotal2']); //Number((importeUnitario + descuentoUnitario) / cantidad);
				precioUnitario = Number(parseFloat(precioUnitario).toFixed(6));
				
				nuevoSubtotal = Number(nuevoSubtotal) + (Number(parseFloat(importeUnitario).toFixed(2)) + Number(parseFloat(descuentoUnitario).toFixed(2)));
				*/
				
				//PROCESO 29/07/2021
				console.log('---------------------------------');
				console.log('Concepto a recalcular: ' + nombre);
				
				importeUnitario = Number(WC_Conceptos[i]['total']); //Importe del Articulo de WooCommerce
				importeConcepto = importeUnitario;
				
				console.log('Importe del concepto obtenido desde el pedido en WooCommerce: ' + importeUnitario);
				
				descuentoUnitario = Number(parseFloat(0).toFixed(6));
				nuevoDescuento = Number(nuevoDescuento) + Number(parseFloat(descuentoUnitario).toFixed(2));
				
				console.log('Se descartó cualquier descuento para este concepto');
				
				//Recalcular precio unitario
				precioUnitario = Number((importeUnitario + descuentoUnitario) / cantidad);
				precioUnitario = Number(parseFloat(precioUnitario).toFixed(6));
				
				let subtotalAnterior = Number(nuevoSubtotal);
				nuevoSubtotal = Number(nuevoSubtotal) + (Number(parseFloat(importeUnitario).toFixed(2)) + Number(parseFloat(descuentoUnitario).toFixed(2)));
				nuevoSubtotal = Number(parseFloat(nuevoSubtotal).toFixed(2));
				
				console.log('Se recalculó el precio unitario del concepto.');
				console.log('Importe Unitario / Cantidad = Precio Unitario');
				console.log(Number(parseFloat(importeUnitario).toFixed(2)) + " / " + cantidad + " = " + precioUnitario);
				console.log('El precio unitario recalculado es: ' + precioUnitario);
				console.log('Se recalculó el Subtotal del pedido');
				console.log('NuevoSubtotal = SubtotalAnterior + Importe Unitario');
				console.log('NuevoSubtotal = ' + subtotalAnterior + ' + ' + (Number(parseFloat(importeUnitario).toFixed(2))));
				console.log('El nuevo Subtotal recalculado para el pedido es: ' + nuevoSubtotal);
				
				//FIN PROCESO 29/07/2021
				
				columnaTextoImpuestos = '<td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">';
				textoImpuestos = '';
				
				for(let k = 0; k < impuestosConcepto.length; k++)
				{
					let nombreImpuesto = impuestosConcepto[k]['impuesto'];
					let importeImpuesto = Number(impuestosConcepto[k]['importe']);
					let tasaImpuesto = Number(impuestosConcepto[k]['tasa']);
					let tasaPorcentajeImpuesto = Number(impuestosConcepto[k]['tasaPorcentaje']);
					
					if(nombreImpuesto == 'IVA' && importeIVAConcepto == '')
					{
						importeIVAConcepto = importeImpuesto;
						tasaIVAConcepto = tasaImpuesto;
						tasaPorcentajeIVAConcepto = tasaPorcentajeImpuesto;
						
						let importeTotalIVA = Number(parseFloat(importeImpuesto).toFixed(2));
						
						//PROCESO 25/08/2021
						let importeIVARecalculado = Number(parseFloat(Number(parseFloat(importeUnitario).toFixed(2)) * tasaImpuesto).toFixed(2));
						if(importeTotalIVA != importeIVARecalculado)
						{
							console.log("Se recalculó el importe del IVA del concepto '" + nombre + "'. El importe del IVA obtenido desde el pedido en WooCommerce es " + importeImpuesto + " y es inválido porque no es el resultado de multiplicar el importe del concepto por la tasa del impuesto " + tasaImpuesto + ". El importe correcto del IVA es " + importeIVARecalculado + " y ha sido actualizado en este pedido (sin afectar al pedido original en WooCommerce) para su facturación.");
							importeTotalIVA = importeIVARecalculado;
							importeImpuesto = importeIVARecalculado;
							importeIVAConcepto = importeIVARecalculado;
						}
						//FIN PROCESO 25/08/2021
						
						for(let m = 0; m < array_ImpuestosRecalculados.length; m++)
						{
							if(array_ImpuestosRecalculados[m][1] == 'IVA' && array_ImpuestosRecalculados[m][2] == tasaPorcentajeImpuesto)
								array_ImpuestosRecalculados[m][3] = Number(array_ImpuestosRecalculados[m][3]) + importeTotalIVA;
						}
					}
					else if(nombreImpuesto == 'IVA EXENTO' && importeIVAConcepto == '')
					{
						importeIVAConcepto = Number(-1);
						tasaIVAConcepto = Number(-1);
						tasaPorcentajeIVAConcepto = Number(-1);
						
						let importeTotalIVAExento = Number(parseFloat(importeImpuesto).toFixed(2));
						
						for(let m = 0; m < array_ImpuestosRecalculados.length; m++)
						{
							if(array_ImpuestosRecalculados[m][1] == 'IVA EXENTO' && array_ImpuestosRecalculados[m][2] == tasaPorcentajeImpuesto)
								array_ImpuestosRecalculados[m][3] = Number(array_ImpuestosRecalculados[m][3]) + importeTotalIVAExento;
						}
					}
					else if(nombreImpuesto == 'IEPS' && importeIEPSConcepto == '')
					{
						importeIEPSConcepto = importeImpuesto;
						tasaIEPSConcepto = tasaImpuesto;
						tasaPorcentajeIEPSConcepto = tasaPorcentajeImpuesto;
						
						let importeTotalIEPS = Number(parseFloat(importeImpuesto).toFixed(2));
						
						for(let m = 0; m < array_ImpuestosRecalculados.length; m++)
						{
							if(array_ImpuestosRecalculados[m][1] == 'IEPS' && array_ImpuestosRecalculados[m][2] == tasaPorcentajeImpuesto)
								array_ImpuestosRecalculados[m][3] = Number(array_ImpuestosRecalculados[m][3]) + importeTotalIEPS;
						}
					}
					else if(nombreImpuesto == 'IVA RETENIDO' && importeIVARetenidoConcepto == '')
					{
						importeIVARetenidoConcepto = importeImpuesto;
						tasaIVARetenidoConcepto = tasaImpuesto;
						tasaPorcentajeIVARetenidoConcepto = tasaPorcentajeImpuesto;
						
						let importeTotalIVARetenido = Number(parseFloat(importeImpuesto).toFixed(2));
						
						for(let m = 0; m < array_ImpuestosRecalculados.length; m++)
						{
							if(array_ImpuestosRecalculados[m][1] == 'IVA RETENIDO' && array_ImpuestosRecalculados[m][2] == tasaPorcentajeImpuesto)
								array_ImpuestosRecalculados[m][3] = Number(array_ImpuestosRecalculados[m][3]) + importeTotalIVARetenido;
						}
					}
					else if(nombreImpuesto == 'IEPS RETENIDO' && importeIEPSRetenidoConcepto == '')
					{
						importeIEPSRetenidoConcepto = importeImpuesto;
						tasaIEPSRetenidoConcepto = tasaImpuesto;
						tasaPorcentajeIEPSRetenidoConcepto = tasaPorcentajeImpuesto;
						
						let importeTotalIEPSRetenido = Number(parseFloat(importeImpuesto).toFixed(2));
						
						for(let m = 0; m < array_ImpuestosRecalculados.length; m++)
						{
							if(array_ImpuestosRecalculados[m][1] == 'IEPS RETENIDO' && array_ImpuestosRecalculados[m][2] == tasaPorcentajeImpuesto)
								array_ImpuestosRecalculados[m][3] = Number(array_ImpuestosRecalculados[m][3]) + importeTotalIEPSRetenido;
						}
					}
					else if(nombreImpuesto == 'ISR' && importeISRConcepto == '')
					{
						importeISRConcepto = importeImpuesto;
						tasaISRConcepto = tasaImpuesto;
						tasaPorcentajeISRConcepto = tasaPorcentajeImpuesto;
						
						let importeTotalISR = Number(parseFloat(importeImpuesto).toFixed(2));
						
						for(let m = 0; m < array_ImpuestosRecalculados.length; m++)
						{
							if(array_ImpuestosRecalculados[m][1] == 'ISR' && array_ImpuestosRecalculados[m][2] == tasaPorcentajeImpuesto)
								array_ImpuestosRecalculados[m][3] = Number(array_ImpuestosRecalculados[m][3]) + importeTotalISR;
						}
					}
					else if(nombreImpuesto == 'ISH' && importeISHConcepto == '')
					{
						importeISHConcepto = importeImpuesto;
						tasaISHConcepto = tasaImpuesto;
						tasaPorcentajeISHConcepto = tasaPorcentajeImpuesto;
						
						let importeTotalISH = Number(parseFloat(importeImpuesto).toFixed(2));
						
						for(let m = 0; m < array_ImpuestosRecalculados.length; m++)
						{
							if(array_ImpuestosRecalculados[m][1] == 'ISH' && array_ImpuestosRecalculados[m][2] == tasaPorcentajeImpuesto)
								array_ImpuestosRecalculados[m][3] = Number(array_ImpuestosRecalculados[m][3]) + importeTotalISH;
						}
					}
					
					if(importeImpuesto >= 0)
					{
						if(textoImpuestos != '')
							textoImpuestos += ', ';
						
						textoImpuestos += nombreImpuesto + '-' + tasaPorcentajeImpuesto + ' $' + Number(parseFloat(importeImpuesto).toFixed(2)).formatMoney(2, '.', ',');
					}
				}
				
				if(textoImpuestos == '')
					textoImpuestos = '-';
				
				columnaTextoImpuestos += textoImpuestos + '</center></td>';
			}
			
			tablaHTML_Conceptos += '<tr>';
			tablaHTML_Conceptos += '<td style="text-align:left; border-color: #dedede; background-color: #fbfbfb">' + nombre + '</td>';
			tablaHTML_Conceptos += '<td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">$' + Number(parseFloat(precioUnitario).toFixed(cfdi_precision_decimal)).formatMoney(cfdi_precision_decimal, '.', ',') + '</td>';
            tablaHTML_Conceptos += '<td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">' + Number(parseFloat(cantidad).toFixed(cfdi_precision_decimal)).formatMoney(cfdi_precision_decimal, '.', ',') + '</td>';
            tablaHTML_Conceptos += '<td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">$' + Number(parseFloat(descuentoUnitario).toFixed(cfdi_precision_decimal)).formatMoney(cfdi_precision_decimal, '.', ',') + '</td>';
            tablaHTML_Conceptos += '<td style="text-align:right; border-color: #dedede; background-color: #fbfbfb">$' + Number(parseFloat(importeUnitario).toFixed(cfdi_precision_decimal)).formatMoney(cfdi_precision_decimal, '.', ',') + '</td>';
			tablaHTML_Conceptos += columnaTextoImpuestos;
            tablaHTML_Conceptos += '</tr>';
			
			let baseImpuesto = parseFloat(importeConcepto).toFixed(cfdi_precision_decimal) - parseFloat(descuentoUnitario).toFixed(cfdi_precision_decimal);
			
			if(cfdi_manejo_impuestos_pedido == '1' || cfdi_manejo_impuestos_pedido == '2' || cfdi_manejo_impuestos_pedido == '3' || cfdi_manejo_impuestos_pedido == '4' || cfdi_manejo_impuestos_pedido == '5' || cfdi_manejo_impuestos_pedido == '6')
			{	
				//importeConcepto = (Number(parseFloat(importeUnitario).toFixed(2)) + Number(parseFloat(descuentoUnitario).toFixed(2))); //importeUnitario + descuentoUnitario;
				baseImpuesto = importeUnitario;
			}
			
			AgregarConceptoCFDI(i, cfdi_precision_decimal, cfdi_manejo_impuestos_pedido, claveServicio, claveProducto, nombre, claveUnidad, unidadMedida, cantidad,
				precioUnitario, importeConcepto, descuentoUnitario, noPedimento,
				tasaIVAConcepto, importeIVAConcepto,
				tasaIEPSConcepto, importeIEPSConcepto,
				tasaIVARetenidoConcepto, importeIVARetenidoConcepto,
				tasaIEPSRetenidoConcepto, importeIEPSRetenidoConcepto,
				tasaISRConcepto, importeISRConcepto,
				baseImpuesto, objetoImpuesto);
		}
		
		console.log(array_Conceptos);
		tablaHTML_Conceptos += '</tbody></table>';
		
		/*if(mensajeErroresConceptos != '')
		{
			tablaHTML_Conceptos += '<br/>';
			tablaHTML_Conceptos += '<font size="3" color="#e20000"><b>' + ((idiomaRVLFECFDI == 'ES') ? 'AVISO IMPORTANTE':'IMPORTANT NOTICE')+ '</b></font><br/><font size="2">' + ((idiomaRVLFECFDI == 'ES') ? 'Se encontraron problemas con los siguientes conceptos, ya que en el pedido de WooCommerce los importes de los mismos son incorrectos. Sin embargo, hemos intentado recalcular los importes correctos para que sea posible emitir el CFDI a continuación. Si no fuera posible emitir el CFDI, será debido a inconsistencia de cálculos matemáticos derivado del problema mencionado anteriormente, por lo que la única solución será verificar, corregir y actualizar los importes y otras cantidades del pedido en WooCommerce por parte del administrador del sitio web.':'Problems were found with the following concepts, since in the WooCommerce order their amounts are incorrect. However, we have tried to recalculate the correct amounts so that it is possible to issue the CFDI next. If it is not possible to issue the CFDI, it will be due to inconsistency of mathematical calculations derived from the problem mentioned above, so the only solution will be to verify, correct and update the amounts and other quantities of the order in WooCommerce by the website administrator.') + '</font>';
			tablaHTML_Conceptos += mensajeErroresConceptos;
			tablaHTML_Conceptos += '<br/>';
		}*/
		
		let json_Respuesta = 
		{
			tablaHTML_Conceptos : tablaHTML_Conceptos,
			importeTotalIVA : importeTotalIVA,
			WC_Subtotal : nuevoSubtotal,
			WC_Descuento : nuevoDescuento,
			array_ImpuestosRecalculados : array_ImpuestosRecalculados
		};
		
		return json_Respuesta;
	}
	
	function AgregarConceptoCFDI(i, cfdi_precision_decimal, cfdi_manejo_impuestos_pedido, claveServicio, claveProducto, nombre, claveUnidad, unidadMedida, cantidad, precioUnitario, importeUnitario,
			descuentoUnitario, noPedimento, tasaIVAConcepto, importeIVAConcepto, tasaIEPSConcepto, importeIEPSConcepto, tasaIVARetenidoConcepto, importeIVARetenidoConcepto,
			tasaIEPSRetenidoConcepto, importeIEPSRetenidoConcepto, tasaISRConcepto, importeISRConcepto, baseImpuesto, objetoImpuesto)
	{
		if(cfdi_manejo_impuestos_pedido == '1' || cfdi_manejo_impuestos_pedido == '2' || cfdi_manejo_impuestos_pedido == '3' || cfdi_manejo_impuestos_pedido == '4' || cfdi_manejo_impuestos_pedido == '5' || cfdi_manejo_impuestos_pedido == '6')
			cfdi_precision_decimal = Number(2);
		
		let tipoImpuestoIVA = 'F';
		let importeIVAConceptoFinal = (parseFloat(importeIVAConcepto).toFixed(cfdi_precision_decimal));
		let tipoFactorIVA = 'Tasa';
		
		if(importeIVAConcepto == -1 && tasaIVAConcepto == -1)
		{
			tipoFactorIVA = 'Exento';
			tasaIVAConcepto = 0;
		}
		else if(importeIVAConcepto === '' || importeIVAConcepto < 0)
		{
			tipoImpuestoIVA = '';
			tasaIVAConcepto = '';
			importeIVAConceptoFinal = '';
		}
		
		array_Conceptos[i] = new Array(51);
		array_Conceptos[i][0] = ''; //TIPO DE CFDI: RA, RH O VACIO
		array_Conceptos[i][1] = claveServicio; //ClaveProdServ
		array_Conceptos[i][2] = claveProducto; //CLAVE
		array_Conceptos[i][3] = btoa(unescape(encodeURIComponent(nombre))); //DESCRIPCION
		array_Conceptos[i][4] = claveUnidad; //CLAVE UNIDAD
		array_Conceptos[i][5] = unidadMedida; //UNIDAD MEDIDA
		array_Conceptos[i][6] = (parseFloat(cantidad).toFixed(cfdi_precision_decimal)); //CANTIDAD
		array_Conceptos[i][7] = (parseFloat(precioUnitario).toFixed(cfdi_precision_decimal)); //PRECIO UNITARIO
		array_Conceptos[i][8] = (parseFloat(importeUnitario).toFixed(cfdi_precision_decimal)); //IMPORTE
		array_Conceptos[i][9] = (parseFloat(descuentoUnitario).toFixed(cfdi_precision_decimal)); //DESCUENTO
		array_Conceptos[i][10] = ''; //CUENTA PREDIAL
		array_Conceptos[i][11] = noPedimento; //NUMERO ADUANA
		array_Conceptos[i][12] = ''; //FECHA ADUANA
		array_Conceptos[i][13] = ''; //ADUANA
		array_Conceptos[i][14] = '002'; //CODIGO IVA
		array_Conceptos[i][15] = tipoFactorIVA; //FACTOR
		array_Conceptos[i][16] = tipoImpuestoIVA; //TIPO IMPUESTO
		array_Conceptos[i][17] = '1'; //1 (TRASLADADO)
		array_Conceptos[i][18] = tasaIVAConcepto; //TASA
		array_Conceptos[i][19] = importeIVAConceptoFinal; //IMPORTE
		array_Conceptos[i][20] = '003'; //CODIGO IEPS
		array_Conceptos[i][21] = 'Tasa'; //FACTOR
		array_Conceptos[i][22] = (importeIEPSConcepto !== '' && importeIEPSConcepto >= 0) ? 'F' : ''; //TIPO IMPUESTO
		array_Conceptos[i][23] = '1'; //1 (TRASLADADO)
		array_Conceptos[i][24] = (importeIEPSConcepto !== '' && importeIEPSConcepto >= 0) ? tasaIEPSConcepto : ''; //TASA
		array_Conceptos[i][25] = (importeIEPSConcepto !== '' && importeIEPSConcepto >= 0) ? (parseFloat(importeIEPSConcepto).toFixed(cfdi_precision_decimal)) : ''; //IMPORTE
		array_Conceptos[i][26] = '002'; //CODIGO IVA RETENIDO
		array_Conceptos[i][27] = 'Tasa'; //FACTOR
		array_Conceptos[i][28] = (importeIVARetenidoConcepto !== '' && importeIVARetenidoConcepto >= 0) ? 'F' : ''; //TIPO IMPUESTO
		array_Conceptos[i][29] = '2'; //2 (RETENIDO)
		array_Conceptos[i][30] = (importeIVARetenidoConcepto !== '' && importeIVARetenidoConcepto >= 0) ? tasaIVARetenidoConcepto : ''; //TASA
		array_Conceptos[i][31] = (importeIVARetenidoConcepto !== '' && importeIVARetenidoConcepto >= 0) ? (parseFloat(importeIVARetenidoConcepto).toFixed(cfdi_precision_decimal)) : ''; //IMPORTE
		array_Conceptos[i][32] = '003'; //CODIGO IEPS RETENIDO
		array_Conceptos[i][33] = 'Tasa'; //FACTOR
		array_Conceptos[i][34] = (importeIEPSRetenidoConcepto !== '' && importeIEPSRetenidoConcepto >= 0) ? 'F' : ''; //TIPO IMPUESTO
		array_Conceptos[i][35] = '2'; //2 (RETENIDO)
		array_Conceptos[i][36] = (importeIEPSRetenidoConcepto !== '' && importeIEPSRetenidoConcepto >= 0) ? tasaIEPSRetenidoConcepto : ''; //TASA
		array_Conceptos[i][37] = (importeIEPSRetenidoConcepto !== '' && importeIEPSRetenidoConcepto >= 0) ? (parseFloat(importeIEPSRetenidoConcepto).toFixed(cfdi_precision_decimal)) : ''; //IMPORTE
		array_Conceptos[i][38] = '001'; //CODIGO ISR RETENIDO
		array_Conceptos[i][39] = 'Tasa'; //FACTOR
		array_Conceptos[i][40] = (importeISRConcepto !== '' && importeISRConcepto >= 0) ? 'F' : ''; //TIPO IMPUESTO
		array_Conceptos[i][41] = '2'; //2 (RETENIDO)
		array_Conceptos[i][42] = (importeISRConcepto !== '' && importeISRConcepto >= 0) ? tasaISRConcepto : ''; //TASA
		array_Conceptos[i][43] = (importeISRConcepto !== '' && importeISRConcepto >= 0) ? (parseFloat(importeISRConcepto).toFixed(cfdi_precision_decimal)) : ''; //IMPORTE
		array_Conceptos[i][44] = '';
		array_Conceptos[i][45] = '';
		array_Conceptos[i][46] = '';
		array_Conceptos[i][47] = '';
		array_Conceptos[i][48] = ''; 
		array_Conceptos[i][49] = '';
		array_Conceptos[i][50] = (parseFloat(baseImpuesto).toFixed(cfdi_precision_decimal));
		array_Conceptos[i][51] = '';
		array_Conceptos[i][52] = '';
		array_Conceptos[i][53] = objetoImpuesto;
	}
	
	$("#paso_tres_boton_vistaprevia").click(function(event)
	{
		event.preventDefault();
		
		document.getElementById("paso_tres_boton_regresar").disabled = true;
		document.getElementById("paso_tres_boton_generar").disabled = true;
		document.getElementById("cargandoPaso3").style.visibility = "visible";
		
		var numeroPedido = document.getElementById("numeroPedido").value;
		var emisor_rfc = document.getElementById("emisor_rfc").value;
		var emisor_usuario = document.getElementById("emisor_usuario").value;
		var emisor_serie = document.getElementById("emisor_serie").value;
		var receptor_id = document.getElementById("receptor_id").value;
		var receptor_rfc = document.getElementById("receptor_rfc").value;
		var receptor_razon_social = document.getElementById("receptor_razon_social").value;
		var receptor_email = document.getElementById("receptor_email").value;
		var metodo_pago = document.getElementById("paso_3_metodos_pago").value;
		var metodo_pago33 = document.getElementById("paso_3_metodos_pago33").value;
		var uso_cfdi = document.getElementById('paso_3_uso_cfdi').value;
		var regimen_fiscal = document.getElementById('cfdi_regimen_fiscal').value;
		var cfdi_clave_confirmacion = document.getElementById('cfdi_clave_confirmacion').value;
		var cfdi_moneda = document.getElementById('cfdi_moneda').value;
		var cfdi_tipo_cambio = document.getElementById('cfdi_tipo_cambio').value;
		var cfdi_observacion = document.getElementById('cfdi_observacion').value;
		var cfdi_precision_decimal = document.getElementById('cfdi_precision_decimal').value;
		var cfdi_huso_horario = document.getElementById('cfdi_huso_horario').value;
		var receptor_domicilioFiscalReceptor = document.getElementById('receptor_domicilioFiscalReceptor');
		var receptor_regimenfiscal = document.getElementById('receptor_regimenfiscal');
		
		let versionCFDI = '3.3';
		
		if(receptor_domicilioFiscalReceptor != null && receptor_domicilioFiscalReceptor != '')
		{
			versionCFDI = '4.0';
			receptor_domicilioFiscalReceptor = receptor_domicilioFiscalReceptor.value;
			receptor_regimenfiscal = receptor_regimenfiscal.value;
		}
		
		/*var parametros = '&EMISOR_RFC=' + emisor_rfc + 
						'&EMISOR_USUARIO=' + emisor_usuario + 
						'&RECEPTOR_ID=' + receptor_id +
						'&RECEPTOR_RFC=' + receptor_rfc +
						'&RECEPTOR_NOMBRE=' + receptor_razon_social +
						'&RECEPTOR_EMAIL=' + receptor_email +
						'&METODO_PAGO=' + metodo_pago +
						'&CONCEPTOS=' + JSON.stringify(array_Conceptos) +
						'&SUBTOTAL=' + subtotal +
						'&DESCUENTO=' + descuento +
						'&TOTAL=' + total +
						'&SERIE=' + emisor_serie + 
						'&IMPUESTO_FEDERAL=' + JSON.stringify(arrayImpuestosFederales) +
						'&NUMERO_PEDIDO=' + numero_pedido +
						'&USO_CFDI=' + uso_cfdi +
						'&REGIMEN_FISCAL=' + regimen_fiscal +
						'&CLAVE_CONFIRMACION=' + cfdi_clave_confirmacion +
						'&MONEDA=' + cfdi_moneda +
						'&TIPO_CAMBIO=' + cfdi_tipo_cambio +
						'&OBSERVACION=' + btoa(cfdi_observacion);
						
		location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=GenerarVistaPrevia33' + parametros + '&IDIOMA=' + idiomaRVLFECFDI;*/
		
		data = 
		{
			action 					: 'realvirtual_woocommerce_paso_tres_vista_previa_cfdi',
			numeroPedido			: numeroPedido,
			receptor_id				: receptor_id,
			receptor_rfc			: receptor_rfc,
			receptor_razon_social	: receptor_razon_social,
			receptor_email			: receptor_email,
			metodo_pago 			: metodo_pago,
			metodo_pago33 			: metodo_pago33,
			uso_cfdi				: uso_cfdi,
			regimen_fiscal			: regimen_fiscal,
			conceptos				: JSON.stringify(array_Conceptos),
			subtotal				: subtotal,
			descuento				: descuento,
			total					: total,
			impuesto_federal		: JSON.stringify(array_ImpuestosFederales),
			impuesto_local			: JSON.stringify(array_ImpuestosLocales),
			numero_pedido			: numero_pedido,
			clave_confirmacion		: cfdi_clave_confirmacion,
			moneda					: cfdi_moneda,
			tipo_cambio				: cfdi_tipo_cambio,
			observacion				: cfdi_observacion,
			idioma					: idiomaRVLFECFDI,
			precision_decimal		: cfdi_precision_decimal,
			huso_horario			: cfdi_huso_horario,
			calle_receptor			: calle_receptor,
			estado_receptor			: estado_receptor, 
			municipio_receptor		: municipio_receptor,
			pais_receptor			: pais_receptor, 
			codigoPostal_receptor	: codigoPostal_receptor,
			lugarExpedicion			: general_lugarExpedicion,
			versionCFDI				: versionCFDI,
			receptor_domicilioFiscalReceptor : receptor_domicilioFiscalReceptor,
			receptor_regimenfiscal : receptor_regimenfiscal
        }
		
		console.log('Datos enviados para la generación de la Vista Previa del CFDI');
		console.log(data);
		
		$.post(myAjax.ajaxurl, data, function(response)
		{
			document.getElementById('paso_tres_boton_regresar').disabled = false;
			document.getElementById('paso_tres_boton_generar').disabled = false;
			document.getElementById('cargandoPaso3').style.visibility = 'hidden';
			
            if(!response.success)
			{
				mostrarVentana(response.message);
				return false;
			}
			else
			{
				mensaje = response.message;
				CFDI_PDF = response.CFDI_PDF;
				
				if(mensaje == '')
				{				
					event.preventDefault();
					location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarVistaPrevia33&CFDI_PDF=' + CFDI_PDF + '&IDIOMA=' + idiomaRVLFECFDI;
					
					return true;
				}
				else
				{
					mostrarVentana(mensaje);
					return false;
				}
				
			}
		}, 'json');
	});
	
	function iedu_eliminarAlumno(idFila)
	{
		console.log('fila: ' + idFila);
	}
	
	$("#iedu_boton_agregarAlumno").click(function(event)
	{
		event.preventDefault();
		
		let rowCount = $('#iedu_tablaAlumnos tr').length;
		let color_texto_controles_formulario = document.getElementById('cfdi_color_texto_controles_formulario').value;
		let url_imagen_eliminar = document.getElementById('cfdi_url_imagen_eliminar').value;
		
		let markup = '<tr>'+
					'<td style="vertical-align:top; width:20%; text-align: left;">'+
						'<font size="2">'+
							'<input type="text" id="iedu_nombreAlumno' + rowCount + '" name="iedu_nombreAlumno' + rowCount + '" value="" style="color: ' + color_texto_controles_formulario + ';" />'+
						'</font>'+
					'</td>'+
					'<td style="vertical-align:top; width:20%; text-align: left;">'+
						'<font size="2">'+
						'	<input type="text" id="iedu_curp' + rowCount + '" name="iedu_curp' + rowCount + '" style="color: ' + color_texto_controles_formulario + ';" />'+
						'</font>'+
					'</td>'+
					'<td style="vertical-align:top; width:20%; text-align: left;">'+
						'<font size="2">'+
							'<select id="iedu_nivel' + rowCount + '" style="width: 90%; color: ' + color_texto_controles_formulario + ';" >'+
								'<option value="Preescolar" selected>Preescolar</option>'+
								'<option value="Preescolar">Primaria</option>'+
								'<option value="Preescolar">Secundaria</option>'+
								'<option value="Preescolar">Profesional técnico</option>'+
								'<option value="Preescolar">Bachillerato o su equivalente</option>'+
							'</select>'+
						'</font>'+
					'</td>'+
					'<td style="vertical-align:top; width:15%; text-align: left;">'+
						'<font size="2">'+
							'<input type="text" id="iedu_autRVOE' + rowCount + '" name="iedu_autRVOE' + rowCount + '" value="" style="color: ' + color_texto_controles_formulario + ';" />'+
						'</font>'+
					'</td>'+
					'<td style="vertical-align:top; width:20%; text-align: left;">'+
						'<font size="2">'+
							'<input type="text" id="iedu_rfcPago' + rowCount + '" name="iedu_rfcPago' + rowCount + '" value="" style="color: ' + color_texto_controles_formulario + ';" />'+
						'</font>'+
					'</td>'+
				'</tr>';
		
		$('#iedu_tablaAlumnos tr:last').after(markup);
	});
	
	$('#botonModalTimbrarSi').click(function(event)
	{
        event.preventDefault();
		
		document.getElementById('paso_tres_boton_regresar').disabled = true;
		document.getElementById('paso_tres_boton_generar').disabled = true;
		document.getElementById('cargandoPaso3').style.visibility = 'visible';
		
		var numeroPedido = document.getElementById('numeroPedido').value;
		var receptor_id = document.getElementById('receptor_id').value;
		var receptor_rfc = document.getElementById('receptor_rfc').value;
		var receptor_razon_social = document.getElementById('receptor_razon_social').value;
		var receptor_email = document.getElementById('receptor_email').value;
		var metodo_pago = document.getElementById('paso_3_metodos_pago').value;
		var metodo_pago33 = document.getElementById('paso_3_metodos_pago33').value;
		var uso_cfdi = document.getElementById('paso_3_uso_cfdi').value;
		var regimen_fiscal = document.getElementById('cfdi_regimen_fiscal').value;
		var cfdi_clave_confirmacion = document.getElementById('cfdi_clave_confirmacion').value;
		var cfdi_moneda = document.getElementById('cfdi_moneda').value;
		var cfdi_tipo_cambio = document.getElementById('cfdi_tipo_cambio').value;
		var cfdi_observacion = document.getElementById('cfdi_observacion').value;
		var cfdi_precision_decimal = document.getElementById('cfdi_precision_decimal').value;
		var cfdi_huso_horario = document.getElementById('cfdi_huso_horario').value;
		var receptor_domicilioFiscalReceptor = document.getElementById('receptor_domicilioFiscalReceptor');
		var receptor_regimenfiscal = document.getElementById('receptor_regimenfiscal');
		
		let versionCFDI = '3.3';
		
		if(receptor_domicilioFiscalReceptor != null && receptor_domicilioFiscalReceptor != '')
		{
			versionCFDI = '4.0';
			receptor_domicilioFiscalReceptor = receptor_domicilioFiscalReceptor.value;
			receptor_regimenfiscal = receptor_regimenfiscal.value;
		}
		
		var datosComplementoCFDI = '';
		
		if(general_complementoCFDI == 'iedu')
		{
			let iedu_nombreAlumno = document.getElementById('iedu_nombreAlumno').value;
			let iedu_curp = document.getElementById('iedu_curp').value;
			let iedu_nivel = document.getElementById('iedu_nivel').value;
			let iedu_autRVOE = document.getElementById('iedu_autRVOE').value;
			let iedu_rfcPago = document.getElementById('iedu_rfcPago').value;
			
			if(iedu_nombreAlumno == '')
			{
				mostrarVentana("El campo <b>Nombre del alumno</b> no puede ser vacío.");
				return false;
			}
			
			if(iedu_curp == '')
			{
				mostrarVentana("El campo <b>CURP</b> no puede ser vacío.");
				return false;
			}
			
			if(iedu_nivel == '')
			{
				mostrarVentana("El campo <b>Nivel educativo</b> no puede ser vacío.");
				return false;
			}
			
			if(iedu_autRVOE == '')
			{
				mostrarVentana("El campo <b>Clave validéz oficial</b> no puede ser vacío.");
				return false;
			}
			
			if(iedu_rfcPago == '')
			{
				mostrarVentana("El campo <b>RFC pago</b> no puede ser vacío.");
				return false;
			}
			
			datosComplementoCFDI =
			{
				complemento : general_complementoCFDI,
				iedu_nombreAlumno : iedu_nombreAlumno,
				iedu_curp : iedu_curp,
				iedu_nivel : iedu_nivel,
				iedu_autRVOE : iedu_autRVOE,
				iedu_rfcPago : iedu_rfcPago
			}
		}
		
		/*var fecha_actual = new Date();
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

		var cfdi_fecha_emision = año + "-" + mes + "-" + dia + "T" + hora + ":" + minutos + ":" + segundos;*/
		
		data = 
		{
			action 					: 'realvirtual_woocommerce_paso_tres_generar_cfdi',
			numeroPedido			: numeroPedido,
			receptor_id				: receptor_id,
			receptor_rfc			: receptor_rfc,
			receptor_razon_social	: receptor_razon_social,
			receptor_email			: receptor_email,
			metodo_pago 			: metodo_pago,
			metodo_pago33 			: metodo_pago33,
			uso_cfdi				: uso_cfdi,
			regimen_fiscal			: regimen_fiscal,
			conceptos				: JSON.stringify(array_Conceptos),
			subtotal				: subtotal,
			descuento				: descuento,
			total					: total,
			impuesto_federal		: JSON.stringify(array_ImpuestosFederales),
			impuesto_local			: JSON.stringify(array_ImpuestosLocales),
			numero_pedido			: numero_pedido,
			clave_confirmacion		: cfdi_clave_confirmacion,
			moneda					: cfdi_moneda,
			tipo_cambio				: cfdi_tipo_cambio,
			observacion				: cfdi_observacion,
			idioma					: idiomaRVLFECFDI,
			precision_decimal		: cfdi_precision_decimal,
			huso_horario			: cfdi_huso_horario,
			calle_receptor			: calle_receptor,
			estado_receptor			: estado_receptor, 
			municipio_receptor		: municipio_receptor,
			pais_receptor			: pais_receptor, 
			codigoPostal_receptor	: codigoPostal_receptor,
			mostrarMensajeErrorCliente : general_mostrarMensajeErrorCliente,
			datosComplementoCFDI	: datosComplementoCFDI,
			lugarExpedicion			: general_lugarExpedicion,
			versionCFDI				: versionCFDI,
			receptor_domicilioFiscalReceptor : receptor_domicilioFiscalReceptor,
			receptor_regimenfiscal : receptor_regimenfiscal
        }
		
		console.log('Datos enviados para la generación del CFDI');
		console.log(data);
		
		$.post(myAjax.ajaxurl, data, function(response)
		{
			document.getElementById('paso_tres_boton_regresar').disabled = false;
			document.getElementById('paso_tres_boton_generar').disabled = false;
			document.getElementById('cargandoPaso3').style.visibility = 'hidden';
			
            if(!response.success)
			{
				if(general_mostrarMensajeErrorCliente == 'si' && numeroPedido == 0)
				{
					mostrarVentana(general_mensajeErrorCliente);
					console.log("Mensaje de error del servicio de timbrado");
					console.log(response.message);
				}
				else
				{
					mostrarVentana(response.message);
				}
				
				return false;
			}
			else
			{
				mostrarVentana(response.message);
				xml = response.XML;
				CFDI_ID = response.CFDI_ID;
				
				$('#paso_cuatro_boton_xml').click(function(event)
				{
					event.preventDefault();
					location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarXML&CFDI_ID=' + CFDI_ID + '&IDIOMA=' + idiomaRVLFECFDI;
				});
				
				$('#paso_cuatro_boton_pdf').click(function(event)
				{
					event.preventDefault();
					location.href = urlSistemaAsociado + 'Php/Archivos_Proyecto/realvirtual_woocommerce_plugin.php?opcion=DescargarPDF33&CFDI_ID=' + CFDI_ID + '&IDIOMA=' + idiomaRVLFECFDI;
				});
				
				$('#paso_tres').stop().hide();
				$('#paso_cuatro').stop().fadeIn('slow');
				
				return true;
			}
		}, 'json');
	});
	
	$('#paso_dos_boton_regresar').click(function(event)
	{
		event.preventDefault();
		$('#paso_uno').stop().fadeIn('slow');
		$('#paso_dos').stop().hide();
	});
	
	$('#paso_tres_boton_regresar').click(function(event)
	{
		event.preventDefault();
		$('#paso_dos').stop().fadeIn('slow');
		$('#paso_tres').stop().hide();
	});
	
	$('#paso_cuatro_boton_regresar').click(function(event)
	{
		event.preventDefault();
		LimpiarFormularios();
		$('#paso_uno').stop().fadeIn('slow');
		$('#paso_dos').hide();
		$('#paso_tres').hide();
		$('#paso_cuatro').hide();
		$('#paso_cinco').hide();
	});
	
	$('#paso_cinco_boton_regresar').click(function(event)
	{
		event.preventDefault();
		LimpiarFormularios();
		$('#paso_uno').stop().fadeIn('slow');
		$('#paso_dos').hide();
		$('#paso_tres').hide();
		$('#paso_cuatro').hide();
		$('#paso_cinco').hide();
	});
	
	$('#fr_paso_uno_formulario_receptor').submit(function(e)
	{
        e.preventDefault();
		document.getElementById('fr_cargandoPaso1').style.visibility = 'visible';
		
        datosFormulario = $(this).serializeArray();
		
		var fr_version_cfdi = '3.3';
		var fr_receptor_rfc = $('#fr_paso_uno_formulario_receptor').find('#fr_receptor_rfc').val();
		var fr_receptor_razon_social = $('#fr_paso_uno_formulario_receptor').find('#fr_receptor_razon_social').val();
		var fr_receptor_domicilioFiscalReceptor = $('#fr_paso_uno_formulario_receptor').find('#fr_receptor_domicilioFiscalReceptor');
		var fr_receptor_regimenfiscal = $('#fr_paso_uno_formulario_receptor').find('#fr_receptor_regimenfiscal');
		var fr_receptor_uso_cfdi = $('#fr_paso_uno_formulario_receptor').find('#fr_receptor_uso_cfdi').val();
		var fr_receptor_metodos_pago = $('#fr_paso_uno_formulario_receptor').find('#fr_receptor_metodos_pago').val();
		var fr_receptor_metodos_pago33 = $('#fr_paso_uno_formulario_receptor').find('#fr_receptor_metodos_pago33').val();
		
		if(fr_receptor_domicilioFiscalReceptor.length)
		{
			fr_receptor_domicilioFiscalReceptor = fr_receptor_domicilioFiscalReceptor.val();
			fr_version_cfdi = '4.0';
		}
		else
		{
			fr_receptor_domicilioFiscalReceptor = '';
		}
		
		if(fr_receptor_regimenfiscal.length)
		{
			fr_receptor_regimenfiscal = fr_receptor_regimenfiscal.val();
			fr_version_cfdi = '4.0';
		}
		else
		{
			fr_receptor_regimenfiscal = '';
		}
		
        data = 
		{
			action 								: 'realvirtual_woocommerce_paso_uno_receptor',
            receptor_rfc   						: fr_receptor_rfc,
            receptor_razon_social    			: fr_receptor_razon_social,
			receptor_domicilioFiscalReceptor	: fr_receptor_domicilioFiscalReceptor,
			receptor_regimenfiscal				: fr_receptor_regimenfiscal,
			receptor_uso_cfdi 					: fr_receptor_uso_cfdi,
			receptor_metodos_pago 				: fr_receptor_metodos_pago,
			receptor_metodos_pago33 			: fr_receptor_metodos_pago33,
			fr_version_cfdi						: fr_version_cfdi,
			idioma								: idiomaRVLFECFDI
        }
		
        $.post(myAjax.ajaxurl, data, function(response)
		{
			document.getElementById('fr_cargandoPaso1').style.visibility = 'hidden';
			
			if(!response.success)
			{
				mostrarVentanaReceptor(((idiomaRVLFECFDI == 'ES') ? response.message : response.message));
			}
			else
			{
				mostrarVentanaReceptor(((idiomaRVLFECFDI == 'ES') ? 'Datos guardados con éxito.':'Data saved successfully.'));
			}
        }, 'json');

        return true;
    });
	
	function LimpiarFormularios()
	{
		document.getElementById('numero_pedido').value = '';
		document.getElementById('monto_pedido').value = '';
		
		document.getElementById('receptor_id').value = '';
		document.getElementById('receptor_rfc').value = '';
		document.getElementById('receptor_razon_social').value = '';
		document.getElementById('receptor_email').value = '';
	}
	
	Number.prototype.formatMoney = function(c, d, t)
	{
		var n = this, 
		c = isNaN(c = Math.abs(c)) ? 2 : c, 
		d = d == undefined ? "." : d, 
		t = t == undefined ? "," : t, 
		s = n < 0 ? "-" : "", 
		i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
		j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	};
});