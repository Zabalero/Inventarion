<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
	require "inc/funcionesExportar.inc";
	require_once "PHPExcel_1.8.0/Classes/PHPExcel.php";

	//Si el usuario no está autorizado se le desconecta
	$rolUsuario=get_rol($_SESSION['usuario']);
	if ($rolUsuario != 'escritura' && $rolUsuario != 'avanzado') {
		header('Location: index.php?mensaje=Usuario%20desconectado');
	}	


    
	//Inicializa las variables utilizadas en el formulario
 
    $empresa = "";  
    $empresaEnvio = "";  
    $fechaEnvio = ""; 

      
	// Si ya hemos introducido valores para filtros de búsqueda
    if($_SERVER['REQUEST_METHOD']=='POST')
    {  
            
	    $empresa = $_REQUEST['empresa'];
	    $fechaEnvio = $_REQUEST['fechaEnvio'];
        
    }
  
    //FIN Inicializa las variables utilizadas en el formulario

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	//Variables para la búsqueda
	$maxRows = 10;
	$pageNum = 0;
	$seleccionada = 0;
	if (isset($_GET['pageNum'])) {
	  $pageNum = $_GET['pageNum'];
	}
	$startRow = $pageNum * $maxRows;


	if($_SERVER['REQUEST_METHOD']=='POST') {
             
	    if ($empresa == '') {
	    	$empresaEnvio = 'GAMMA';
         } else {
	    	$empresaEnvio = $empresa;
	    }		


		if ($_REQUEST['fechaEnvio'] <> '' || (isset($_POST['descargar']))) {
		


			//Envío a GAMMA
			if (isset($_POST['enviarGAMMA'])) {		



				// ACTUALIZAR ENTREGAS
				$marcarEntregaRdJazztel = $_REQUEST['marcarEntregaRdJazztel'];
				$nfilasEntregaRdJazztel = count ($marcarEntregaRdJazztel);
				
				$marcarEntregaRdTesa = $_REQUEST['marcarEntregaRdTesa'];
				$nfilasEntregaRdTesa = count ($marcarEntregaRdTesa);

				$marcarEntregaRa = $_REQUEST['marcarEntregaRa'];
				$nfilasEntregaRa = count ($marcarEntregaRa);			

				if ($fechaEnvio <> ''){

					if ($nfilasEntregaRdJazztel>0){

						//Formato fecha envío diseño
						$fecha = $fechaEnvio;
						$dia = substr($fecha, 8, 2);
						$anio = substr($fecha, 0, 4);
						$timestamp = strtotime($fecha);
						$mesTxt = strftime("%b", $timestamp);
						$fechaFinal = $mesTxt.' '.$dia.' '.$anio.' 12:00AM';


						for ($i=0; $i<$nfilasEntregaRdJazztel; $i++) {
							
							//Actualiza la tabla RD marcando los diseños que vamos a enviar a GAMMA
							$tsql2 = "UPDATE INV_RD SET FECHA_ENVIO = '".$fechaFinal."', EECC_CARGA_RD = '".$empresaEnvio."' WHERE ID_FDTT = '".$marcarEntregaRdJazztel[$i]."' ";

							$stmt2 = sqlsrv_query( $conn, $tsql2);
											
							sqlsrv_free_stmt( $stmt2);						

						}

					}

					if ($nfilasEntregaRdTesa>0){

						//Formato fecha envío diseño
						$fecha = $fechaEnvio;
						$dia = substr($fecha, 8, 2);
						$anio = substr($fecha, 0, 4);
						$timestamp = strtotime($fecha);
						$mesTxt = strftime("%b", $timestamp);
						$fechaFinal = $mesTxt.' '.$dia.' '.$anio.' 12:00AM';


						for ($i=0; $i<$nfilasEntregaRdTesa; $i++) {
							
							//Actualiza la tabla RD marcando los diseños que vamos a enviar a GAMMA
							$tsql2 = "UPDATE INV_RD SET FECHA_ENVIO = '".$fechaFinal."', EECC_CARGA_RD = '".$empresaEnvio."' WHERE ID_FDTT = '".$marcarEntregaRdTesa[$i]."' ";

							$stmt2 = sqlsrv_query( $conn, $tsql2);
											
							sqlsrv_free_stmt( $stmt2);						

						}

					}				

					if ($nfilasEntregaRa>0){

						for ($i=0; $i<$nfilasEntregaRa; $i++) {
							
							//Actualiza la tabla RD marcando los diseños que vamos a enviar a GAMMA
							$tsql2 = "UPDATE INV_RA SET FECHA_ENVIO = convert(datetime, '".$fechaEnvio."', 120), EECC_CARGA_RA = '".$empresaEnvio."' WHERE ID_FDTT = '".$marcarEntregaRa[$i]."' ";

							$stmt2 = sqlsrv_query( $conn, $tsql2);
											
							sqlsrv_free_stmt( $stmt2);						

						}

					}								

					$mensaje = 'Entrega Procesada correctamente';
				} else {
					$mensaje = 'Introduce fecha de envío';
				}
			} else { //Fin Enviar GAMMA
				if (isset($_POST['descargarEntrega'])) {	
					if ($fechaEnvio <> ''){
						// EXPORTAR EXCELL DE ENTREGA
						generarEnvioGAMMAEntrega($conn,$fechaEnvio,$empresaEnvio);						
					} else {
						$mensaje = 'Introduce fecha de envío';
					}
				} else { // Fin Descargar Pendientes
					if (isset($_POST['descargarPendientes'])) {	
						// EXPORTAR EXCELL DE PENDIENTES
						generarEnvioGAMMAPendientes($conn,$fechaEnvio,$empresaEnvio);	
					} else {
						if (isset($_POST['descargar'])) {	
							// EXPORTAR EXCELL DE PENDIENTES DE ENVIAR + PENDIENTES DE RECIBIR
                                                          if ($empresa == '') {
                                                                    $empresaRecibir = '';
                                                           } else {
                                                                    $empresaRecibir = $empresa;
                                                                }
                                                       generarFicheroGAMMA($conn,$fechaEnvio,$empresaRecibir);	
						}						
					}
				}
				
			}

			//if ($_POST['buscar']) {

				//PENDIENTES DE RECIBIR
				$tsql_rd_pend_diseno = "SELECT Id_GD, Id_FDTT, PROVINCIA, CABECERA, ARBOL, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, 
												UUII_AI, GESTOR, EECC_CARGA_RD_DISENO, DIA_ENVIO_DISENO, DIA_ENTREGA_DISENO, INC_EECCRD_GRAL 
										From INV_VIEW_RD_TODO
										WHERE ((DIA_ENVIO_DISENO Is Not Null) AND (DIA_ENTREGA_DISENO Is Null))";
				if (isset($empresaEnvio) && $empresaEnvio != "") {
					$tsql_rd_pend_diseno = $tsql_rd_pend_diseno . " AND EECC_CARGA_RD_DISENO = '$empresaEnvio'";				
				}					
				
				//Recuperar datos de consulta
				$registros_rd_pend_diseno = sqlsrv_query($conn, $tsql_rd_pend_diseno, array(), array( "Scrollable" => 'static' ));

				$tsql_rd_pend = "SELECT Id_GD,Id_FDTT, PROVINCIA, CABECERA, ARBOL, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, UUII_AI, GESTOR, 
										EECC_CARGA_RD, FECHA_ENVIO, FECHA_ENTREGA, INC_EECCRD_GRAL 
								From INV_VIEW_RD_TODO 
								WHERE ((FECHA_ENVIO Is Not Null) AND (FECHA_ENTREGA Is Null))";
				if (isset($empresaEnvio) && $empresaEnvio != "") {
					$tsql_rd_pend = $tsql_rd_pend . " AND EECC_CARGA_RD = '$empresaEnvio'";				
				}							
				
				//Recuperar datos de consulta
				$registros_rd_pend = sqlsrv_query($conn, $tsql_rd_pend, array(), array( "Scrollable" => 'static' ));	

				// $tsql_ra_pend = "SELECT Id_GD,Id_FDTT, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, 
				// 						FECHA_ENVIO, FECHA_ENTREGA, INC_EECC, ID_ACTUACION 
				// 				FROM INV_RA
				// 				LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
				// 				LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
				// 				 WHERE ((FECHA_ENVIO Is Not Null) AND (FECHA_ENTREGA Is Null))";
				// if (isset($empresaEnvio) && $empresaEnvio != "") {
				// 	$tsql_ra_pend = $tsql_ra_pend . " AND EECC_CARGA_RA = '$empresaEnvio'";				
				// }							
				
				// //Recuperar datos de consulta
				// $registros_ra_pend = sqlsrv_query($conn, $tsql_ra_pend, array(), array( "Scrollable" => 'static' ));			

				//Maiteben nueva version GIS
				$tsql_ra_pend = "SELECT Id_GD,Id_FDTT, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, 
										FECHA_ENVIO, FECHA_ENTREGA, INC_EECC, ID_ACTUACION 
								FROM INV_RA
								LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
								LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
								 WHERE ((FECHA_ENVIO Is Not Null) AND (FECHA_ENTREGA Is Null))";
				if (isset($empresaEnvio) && $empresaEnvio != "") {
					$tsql_ra_pend = $tsql_ra_pend . " AND EECC_CARGA_RA = '$empresaEnvio'";				
				}			
				
				//Recuperar datos de consulta
				$registros_ra_pend = sqlsrv_query($conn, $tsql_ra_pend, array(), array( "Scrollable" => 'static' ));



				$tsql_icx_pend = "SELECT INV_VIEW_RD_TODO.Id_GD,INV_VIEW_RD_TODO.Id_FDTT, INV_VIEW_RD_TODO.PROVINCIA, INV_VIEW_RD_TODO.REGION, INV_VIEW_RD_TODO.CABECERA, 
										INV_VIEW_RD_TODO.ARBOL, INV_VIEW_RD_TODO.ACTUACION_JAZZTEL, INV_VIEW_RD_TODO.ACTUACION_TESA, INV_VIEW_RD_TODO.ID_ZONA, 
										INV_VIEW_RD_TODO.UUII_AI, INV_VIEW_RD_TODO.GESTOR, INV_VIEW_RD_TODO.FASE, INV_RD_ICX.ICX_EECC_CARGA, 
										INV_RD_ICX.ICX_FECHA_COMUNICADO_EECC, INV_RD_ICX.ICX_FECHA, INV_RD_ICX.INC_EECC 
								FROM INV_VIEW_RD_TODO LEFT JOIN INV_RD_ICX ON INV_VIEW_RD_TODO.Id_GD = INV_RD_ICX.Id_GD 
								LEFT JOIN INV_RA ON INV_VIEW_RD_TODO.ARBOL = INV_RA.NOMBRE_FICHERO 
								WHERE ((INV_RD_ICX.ICX_FECHA_COMUNICADO_EECC Is Not Null) AND (INV_RD_ICX.ICX_FECHA Is Null) AND (INV_RA.FECHA_ENTREGA Is Not Null))";
				if (isset($empresaEnvio) && $empresaEnvio != "") {
					$tsql_icx_pend = $tsql_icx_pend . " AND ICX_EECC_CARGA = '$empresaEnvio'";				
				}							
				
				//Recuperar datos de consulta
				$registros_icx_pend = sqlsrv_query($conn, $tsql_icx_pend, array(), array( "Scrollable" => 'static' ));			

				//PENDIENTES DE ENVIAR

				//RD JAZZTEL
				// maiteben: falta filtrar bien por FASES y ver que campos se pueden omitir de la lista porque no entran
				$tsql_rd_pend_envio_jazztel = "SELECT Id_GD, ID_FDTT, PROVINCIA, REGION, CABECERA, ARBOL, ACTUACION_JAZZTEL_FDTT, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, 
														UUII_AI, UUII_AI_DISEÑO, GESTOR, FASE, ESTADO_GIS, EECC_CARGA_RD, FECHA_ENVIO, FECHA_ENTREGA, AI_RD_GRAL, AIE2E, ESTADO_AB_PL4,
														FECHA_FIR, FIR, FX_INTERCAMBIO, FX_ENTREGA_INTER, ESTADO_RD_FDTT
											FROM INV_VIEW_RD_TODO
											WHERE (ID_FDTT Is Not Null AND GESTOR='JAZZTEL' AND FECHA_ENVIO Is Null AND (ESTADO_AB_PL4='APROBADO' Or 
													ESTADO_AB_PL4='REPAROS') AND FECHA_FIR Is Not Null) AND (FASE LIKE 'A%F1' OR FASE LIKE 'A%F2' OR FASE LIKE 'A%F3' OR FASE LIKE 'A%F4'
													OR FASE LIKE 'A%F5' OR FASE LIKE 'A%F6' OR FASE LIKE 'A%F7' OR FASE LIKE 'A%F8')";
				
				//Recuperar datos de consulta
				$registros_rd_pend_envio_jazztel = sqlsrv_query($conn, $tsql_rd_pend_envio_jazztel, array(), array( "Scrollable" => 'static' ));

				//RD TESA
				$tsql_rd_pend_envio_tesa = "SELECT Id_GD, ID_FDTT, PROVINCIA, REGION, CABECERA, ARBOL, ACTUACION_JAZZTEL_FDTT, 
												ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, UUII_AI, UUII_AI_DISEÑO AS UUII_AI_DISENO, GESTOR, FASE,
												ESTADO_GIS, EECC_CARGA_RD, FECHA_ENVIO, FECHA_ENTREGA, AI_RD_GRAL, AIE2E, ESTADO_AB_PL4,
												FECHA_FIR, FIR, FX_INTERCAMBIO, FX_ENTREGA_INTER, ESTADO_RD_FDTT
										FROM INV_VIEW_RD_TODO
										WHERE ((ID_FDTT Is Not Null) AND (GESTOR='TESA') AND (FECHA_ENVIO Is Null) AND 
												(ESTADO_AB_PL4='APROBADO' Or ESTADO_AB_PL4='REPAROS'))";
				
				//Recuperar datos de consulta
				$registros_rd_pend_envio_tesa = sqlsrv_query($conn, $tsql_rd_pend_envio_tesa, array(), array( "Scrollable" => 'static' ));		

				//RA
				// $tsql_ra_pend_envio = "SELECT Id_GD, Id_FDTT, PR.Descripcion AS PROVINCIA, RE.Descripcion AS REGION, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, NOMBRE_FICHERO_FDTT, FASE, EECC_CARGA_RA, FECHA_ENVIO,
				// 							OBSERVACIONES_AUDITORIA, FECHA_ENTREGA, PL4, SUC, CARTAS_EMPALME, AB_RA_GIS, EECC_DISEÑO_RA AS EECC_DISENO_RA, Fx_inicio_diseño AS Fx_inicio_diseno, Fx_fin_diseño AS Fx_fin_diseno, EECC_CONSTRUCCION,
				// 							Fx_inicio_construccion, Fx_fin_construccion, Estado_carga_GIS, RESPONSABLE_CARGA, FX_CARGA_GIS, 
				// 							IIf([FECHA_ENTREGA] Is Not Null,[FECHA_ENTREGA]-[FECHA_ENVIO],GETDATE()-[FECHA_ENVIO]) AS DIAS_ENVIO, 
				// 							IIf(IIf([FECHA_ENTREGA] Is Not Null,[FECHA_ENTREGA]-[FECHA_ENVIO],GETDATE()-[FECHA_ENVIO])>7,'FUERA DE PLAZO','OK') AS limite
				// 					FROM INV_RA
				// 					LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
				// 					LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
				// 					LEFT JOIN inv_regiones AS RE ON RE.Cod_Region = PR.Cod_Region
				// 					WHERE ((Id_FDTT Is Not Null) AND (FECHA_ENVIO Is Null) AND (PL4<>'SIN SUBIR' And PL4<>'RECHAZADO' And PL4<>'Pendiente de aprobar') AND
				// 							(SUC<>'SIN SUBIR') AND (Fx_inicio_diseño Is Not Null) AND (Fx_fin_construccion<>''))";
												
				// //Recuperar datos de consulta
				// $registros_ra_pend_envio = sqlsrv_query($conn, $tsql_ra_pend_envio, array(), array( "Scrollable" => 'static' ));			

				//Maiteben nueva version GIS
				$tsql_ra_pend_envio = "SELECT Id_GD, Id_FDTT, PR.Descripcion AS PROVINCIA, RE.Descripcion AS REGION, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, NOMBRE_FICHERO_FDTT, FASE, EECC_CARGA_RA, FECHA_ENVIO,
											OBSERVACIONES_AUDITORIA, FECHA_ENTREGA, PL4, SUC, CARTAS_EMPALME, AB_RA_GIS, EECC_DISEÑO_RA AS EECC_DISENO_RA, Fx_inicio_diseño AS Fx_inicio_diseno, Fx_fin_diseño AS Fx_fin_diseno, EECC_CONSTRUCCION,
											Fx_inicio_construccion, Fx_fin_construccion, Estado_carga_GIS, RESPONSABLE_CARGA, FX_CARGA_GIS, 
											IIf([FECHA_ENTREGA] Is Not Null,[FECHA_ENTREGA]-[FECHA_ENVIO],GETDATE()-[FECHA_ENVIO]) AS DIAS_ENVIO, 
											IIf(IIf([FECHA_ENTREGA] Is Not Null,[FECHA_ENTREGA]-[FECHA_ENVIO],GETDATE()-[FECHA_ENVIO])>7,'FUERA DE PLAZO','OK') AS limite
									FROM INV_RA
									LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
									LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
									LEFT JOIN inv_regiones AS RE ON RE.Cod_Region = PR.Cod_Region
									WHERE ((Id_FDTT Is Not Null)  AND (ESTADO_RA_FDTT = 'RA Disponible' OR ESTADO_RA_FDTT = 'RA FINALIZADO') AND (Estado_carga_GIS <> 'AS-BUILT') AND (Fx_fin_construccion<>'') 
											AND (PL4 = 'Aprobado' OR PL4 = 'Aprobado con reparos') AND (SUC<>'Rechazado' AND SUC<>'SIN SUBIR' AND SUC<>'') AND (FECHA_ENVIO Is Null) )";
												
				//Recuperar datos de consulta
				$registros_ra_pend_envio = sqlsrv_query($conn, $tsql_ra_pend_envio, array(), array( "Scrollable" => 'static' ));									

			//}


		} else {
			
			$mensaje = 'Introduce fecha de envío';
		}
	}//añadido BASI para sacar listado por defecto
			else{


			//Maiteben nueva version GIS
				$tsql_ra_pend = "SELECT Id_GD,Id_FDTT, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, 
										FECHA_ENVIO, FECHA_ENTREGA, INC_EECC, ID_ACTUACION 
								FROM INV_RA
								LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
								LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
								 WHERE ((FECHA_ENVIO Is Not Null) AND (FECHA_ENTREGA Is Null)) AND EECC_CARGA_RA = 'GAMMA'";
				
							
											
				
				//Recuperar datos de consulta
				$registros_ra_pend = sqlsrv_query($conn, $tsql_ra_pend, array(), array( "Scrollable" => 'static' ));



					//Maiteben nueva version GIS
				$tsql_ra_pend_envio = "SELECT Id_GD, Id_FDTT, PR.Descripcion AS PROVINCIA, RE.Descripcion AS REGION, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, NOMBRE_FICHERO_FDTT, FASE, EECC_CARGA_RA, FECHA_ENVIO,
											OBSERVACIONES_AUDITORIA, FECHA_ENTREGA, PL4, SUC, CARTAS_EMPALME, AB_RA_GIS, EECC_DISEÑO_RA AS EECC_DISENO_RA, Fx_inicio_diseño AS Fx_inicio_diseno, Fx_fin_diseño AS Fx_fin_diseno, EECC_CONSTRUCCION,
											Fx_inicio_construccion, Fx_fin_construccion, Estado_carga_GIS, RESPONSABLE_CARGA, FX_CARGA_GIS, 
											IIf([FECHA_ENTREGA] Is Not Null,[FECHA_ENTREGA]-[FECHA_ENVIO],GETDATE()-[FECHA_ENVIO]) AS DIAS_ENVIO, 
											IIf(IIf([FECHA_ENTREGA] Is Not Null,[FECHA_ENTREGA]-[FECHA_ENVIO],GETDATE()-[FECHA_ENVIO])>7,'FUERA DE PLAZO','OK') AS limite
									FROM INV_RA
									LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
									LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
									LEFT JOIN inv_regiones AS RE ON RE.Cod_Region = PR.Cod_Region
									WHERE ((Id_FDTT Is Not Null)  AND (ESTADO_RA_FDTT = 'RA Disponible' OR ESTADO_RA_FDTT = 'RA FINALIZADO') AND (Estado_carga_GIS <> 'AS-BUILT') AND (Fx_fin_construccion<>'') 
											AND (PL4 = 'Aprobado' OR PL4 = 'Aprobado con reparos') AND (SUC<>'Rechazado' AND SUC<>'SIN SUBIR' AND SUC<>'') AND (FECHA_ENVIO Is Null) )";
												
				//Recuperar datos de consulta
				$registros_ra_pend_envio = sqlsrv_query($conn, $tsql_ra_pend_envio, array(), array( "Scrollable" => 'static' ));									




			}//fin añadido basi

			

	// print the page header
	print_theme_header();


?>
			<!-- start: Content -->
		<div id="content" class="span12">
			
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="index.php">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Envío GAMMA</a></li>
			</ul>

			<!--FILTROS-->
			<FORM id="busqueda" autocomplete="off" METHOD="POST" NAME="opciones"  class="form-horizontal">
				<fieldset>
		    	<div class="row-fluid">
					<div class="span10">					
						<div class="control-group">
							<label class="control-label" for="prior">Empresa </label>
							<div class="controls">
								<SELECT tabindex="0" id="empresa"  name="empresa" >';				
										
										<option value=""></option>
										<?php
										echo '<option value="GAMMA" '.(($empresa=="GAMMA")?'selected="selected"':"").'>GAMMA</option>';
										?>
										
										
								</SELECT>
                                                                &nbsp;&nbsp;
                                                                <INPUT TYPE="submit" class="btn btn-primary" NAME="buscar" id = "buscar" VALUE="Buscar" onclick = "this.form.action = 'buscar_pendientes_RA_RD_ICX.php'">  
							</div>	
						</div>	
					</div>		  
				</div>				
				<div class="row-fluid">								
					<div class="span3">
						<div class="control-group">
							<label class="control-label" for="fRegistro1">Fecha Envío</label>
							<div class="controls">
								<?php
									echo '<input tabindex="7" type="text" class="input datepicker date_field" id="fechaEnvio"  name="fechaEnvio" value="'.$fechaEnvio.'" data-date-format="yyyy-mm-dd">';
								?>
							</div>
						</div>		
					 </div>	

	

					<div class="span2">		
						<?php if($_SERVER['REQUEST_METHOD']=='POST') { ?>
							<button type="submit" id="enviarGAMMA" name="enviarGAMMA" value="enviarGAMMA" class="btn btn-danger btn-small confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white play-circle"></i> Procesar Entrega</button>
						<?php } ?>
					</div>	


					<div class="span2">		
						
						<button type="submit" id="descargar" name="descargar" value="descargar" class="btn btn-success btn-small confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white download"></i> Descargar Fichero</button>
						
					</div>											 

<!-- 					<div class="span2">		
						<?php if($_SERVER['REQUEST_METHOD']=='POST') { ?>
							<button type="submit" id="descargarPendientes" name="descargarPendientes" value="descargarPendientes" class="btn btn-success btn-small confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white download"></i> Descargar Pendientes Recibir</button>
						<?php } ?>
					</div>		

					<div class="span2">		
						<?php if($_SERVER['REQUEST_METHOD']=='POST') { ?>
							<button type="submit" id="descargarEntrega" name="descargarEntrega" value="descargarEntrega" class="btn btn-success btn-small confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white download"></i> Descargar Entrega</button>
						<?php } ?>
					</div>		 -->
																
				</div>


				<!--FIN FILTROS-->  

				<div class="row-fluid">
					<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<?php echo $mensaje;?>
					</div>					
				</div>		

	            <!-- Tabla de listado de grupos existentes -->

	            <div class="row-fluid sortable ui-sortable">	
	            	<div class="box span12">
	            		<h2>Pendientes de ENTREGAR</h2>
	            	</div>	
	            </div>   

	           <div class="row-fluid sortable ui-sortable">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>							
							<h2><i class="halflings-icon file"></i><span class="break"></span>RA</h2>

						</div>
						<div class="box-content">
							<!--<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">-->
                                                        <table class="cell-border datatable" width="100%" cellspacing="0">
							  <thead>
								  <tr>
								  	  <th>x</th>
									  <!--<th>Id_GD</th>-->
									  <th>Id_FDTT</th>
									 <!-- <th>PROVINCIA</th>-->
									  <th>REGION</th>
									  <th>CABECERA</th>
									  <!--<th>ARBOL</th>-->
<!-- 									  <th>NOMBRE_FICHERO</th> -->
									  <th>NOMB. FICHERO_FDTT</th>
									  <!--<th>FASE</th>-->
<!-- 									  <th>EECC_CARGA_RA</th>
	                                  <th>FECHA_ENVIO</th>
	                                  <th>OBSERVACIONES_AUDITORIA</th>
									  <th>FECHA_ENTREGA</th> -->
									  <!--<th>PL4</th>-->
									  <!--<th>SUC</th>-->
									  <!--<th>CARTAS EMPALME</th>-->
									  <!--<th>AB_RA_GIS</th>-->
<!-- 									  <th>EECC_DISEÑO_RA</th>
									  <th>Fx_inicio_diseño</th>
									  <th>Fx_fin_diseño</th>
									  <th>EECC_CONSTRUCCION</th>
									  <th>Fx_inicio_construccion</th>
									  <th>Fx_fin_construccion</th> -->
									  <th>EST. CARGA_GIS</th>
									  <th>RESP. CARGA</th>
									 <!-- <th>FX. CARGA_GIS</th>-->
									  <!--<th>DIAS ENVIO</th>-->
									  <!--<th>LIMITE</th>-->

								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_ra_pend_envio)) {  
                                      while ($linea = sqlsrv_fetch_array($registros_ra_pend_envio)){ ?>
								<tr>
									<?php 
									//Marca de check para las entregas RA DISEÑO
										print ("<TD class='center'><INPUT TYPE='CHECKBOX' NAME='marcarEntregaRa[]' VALUE='".$linea['Id_FDTT']."'></TD>\n");
									 ?>								
									<!--<td class="center"><?php echo $linea['Id_GD']; ?></td>-->
									<td class="center"><?php echo $linea['Id_FDTT']; ?></td>
<!--									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>-->
									<td class="center"><?php echo $linea['REGION']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
<!--									<td class="center"><?php echo $linea['ARBOL']; ?></td>-->
<!-- 									<td class="center"><?php echo $linea['NOMBRE_FICHERO']; ?></td> -->
									<td class="center"><?php echo $linea['NOMBRE_FICHERO_FDTT']; ?></td>
<!--									<td class="center"><?php echo $linea['FASE']; ?></td>-->
<!-- 									<td class="center"><?php echo $linea['EECC_CARGA_RA']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENVIO']; ?></td>
									<td class="center"><?php echo $linea['OBSERVACIONES_AUDITORIA']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENTREGA']; ?></td> -->
<!--									<td class="center"><?php echo $linea['PL4']; ?></td>
									<td class="center"><?php echo $linea['SUC']; ?></td>
									<td class="center"><?php echo $linea['CARTAS_EMPALME']; ?></td>
									<td class="center"><?php echo $linea['AB_RA_GIS']; ?></td>-->
<!-- 									<td class="center"><?php echo $linea['EECC_DISENO_RA']; ?></td>
									<td class="center"><?php echo $linea['Fx_inicio_diseno']; ?></td>
									<td class="center"><?php echo $linea['Fx_fin_diseno']; ?></td>
									<td class="center"><?php echo $linea['EECC_CONSTRUCCION']; ?></td>
									<td class="center"><?php echo $linea['Fx_inicio_construccion']; ?></td>
									<td class="center"><?php echo $linea['Fx_fin_construccion']; ?></td> -->
									<td class="center"><?php echo $linea['Estado_carga_GIS']; ?></td>
									<td class="center"><?php echo $linea['RESPONSABLE_CARGA']; ?></td>
<!--									<td class="center"><?php echo $linea['FX_CARGA_GIS']; ?></td>
									<td class="center"><?php echo $linea['DIAS_ENVIO']; ?></td>
									<td class="center"><?php echo $linea['limite']; ?></td>-->
									
	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>

	    <!--	           <div class="row-fluid sortable ui-sortable">		
				<div class="box span12">
						<div class="box-header" data-original-title>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>							
							<h2><i class="halflings-icon file"></i><span class="break"></span>RD JAZZTEL</h2>

						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">
							  <thead>
								  <tr>
								  	  <th>x</th>
									  <th>Id_GD</th>
									  <th>Id_FDTT</th>
									  <th>PROVINCIA</th>
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <th>ACT. JAZZTEL FDTT</th>
<!- 									  <th>ACT. JAZZTEL</th>
									  <th>ACT. TESA</th>
									  <th>ID_ZONA</th>
	                                  <th>UUII_AI</th>
	                                  <th>UUII_AI_DISEÑO</th>
									  <th>GESTOR</th> ->
									  <th>FASE</th>
									  <th>EST. GIS</th>
<!-									  <th>EECC_CARGA_RD</th>
									  <th>FECHA_ENVIO</th>
									  <th>FECHA_ENTREGA</th> ->
									  <th>AI_RD_GRAL</th>
									  <th>AIE2E</th>
									  <th>EST. AB_PL4</th>
									  <th>FECHA_FIR</th>
									  <th>FIR</th>
									  <th>FX. INTERCAMBIO</th>
									  <th>FX. ENTREGA_INTER</th>
									  <th>EST. RD_FDTT</th>

								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_rd_pend_envio_jazztel)) { 
                                      while ($linea = sqlsrv_fetch_array($registros_rd_pend_envio_jazztel)){ ?>
								<tr>
									<?php 
									//Marca de check para las entregas RD DISEÑO
										print ("<TD class='center'><INPUT TYPE='CHECKBOX' NAME='marcarEntregaRdJazztel[]' VALUE='".$linea['ID_FDTT']."'></TD>\n");
									 ?>								
									<td class="center"><?php echo $linea['Id_GD']; ?></td>
									<td class="center"><?php echo $linea['ID_FDTT']; ?></td>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_JAZZTEL_FDTT']; ?></td>
<!- 									<td class="center"><?php echo $linea['ACTUACION_JAZZTEL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_TESA']; ?></td>
									<td class="center"><?php echo $linea['ID_ZONA']; ?></td>
									<td class="center"><?php echo $linea['UUII_AI']; ?></td>
									<td class="center"><?php echo $linea['UUII_AI_DISENO']; ?></td>
									<td class="center"><?php echo $linea['GESTOR']; ?></td> ->
									<td class="center"><?php echo $linea['FASE']; ?></td>
									<td class="center"><?php echo $linea['ESTADO_GIS']; ?></td>
<!- 									<td class="center"><?php echo $linea['EECC_CARGA_RD']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENVIO']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENTREGA']; ?></td> ->
									<td class="center"><?php echo $linea['AI_RD_GRAL']; ?></td>
									<td class="center"><?php echo $linea['AIE2E']; ?></td>
									<td class="center"><?php echo $linea['ESTADO_AB_PL4']; ?></td>
									<td class="center"><?php echo $linea['FECHA_FIR']; ?></td>
									<td class="center"><?php echo $linea['FIR']; ?></td>
									<td class="center"><?php echo $linea['FX_INTERCAMBIO']; ?></td>
									<td class="center"><?php echo $linea['FX_ENTREGA_INTER']; ?></td>
									<td class="center"><?php echo $linea['ESTADO_RD_FDTT']; ?></td>
									
	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>	          

	           <div class="row-fluid sortable ui-sortable ">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>							
							<h2><i class="halflings-icon file"></i><span class="break"></span>RD TESA</h2>

						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">
							  <thead>
								  <tr>
								  	  <th>x</th>
									  <th>Id_GD</th>
									  <th>Id_FDTT</th>
									  <th>PROVINCIA</th>
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <th>ACT. JAZZTEL FDTT</th>
<!- 									  <th>ACT. JAZZTEL</th>
									  <th>ACT. TESA</th>
									  <th>ID_ZONA</th>
	                                  <th>UUII_AI</th>
	                                  <th>UUII_AI_DISEÑO</th>
									  <th>GESTOR</th> ->
									  <th>FASE</th>
									  <th>EST. GIS</th>
<!-									  <th>EECC_CARGA_RD</th>
									  <th>FECHA_ENVIO</th>
									  <th>FECHA_ENTREGA</th> ->
									  <th>AI_RD_GRAL</th>
									  <th>AIE2E</th>
									  <th>EST. AB_PL4</th>
									  <th>FECHA_FIR</th>
									  <th>FIR</th>
									  <th>FX. INTERCAMBIO</th>
									  <th>FX. ENTREGA_INTER</th>
									  <th>EST. RD_FDTT</th>

								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_rd_pend_envio_tesa)) { 
                                      while ($linea = sqlsrv_fetch_array($registros_rd_pend_envio_tesa)){ ?>
								<tr>
									<?php 
									//Marca de check para las entregas RD DISEÑO
										print ("<TD class='center'><INPUT TYPE='CHECKBOX' NAME='marcarEntregaRdTesa[]' VALUE='".$linea['ID_FDTT']."'></TD>\n");
									 ?>								
									<td class="center"><?php echo $linea['Id_GD']; ?></td>
									<td class="center"><?php echo $linea['ID_FDTT']; ?></td>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_JAZZTEL_FDTT']; ?></td>
<!-									<td class="center"><?php echo $linea['ACTUACION_JAZZTEL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_TESA']; ?></td>
									<td class="center"><?php echo $linea['ID_ZONA']; ?></td>
									<td class="center"><?php echo $linea['UUII_AI']; ?></td>
									<td class="center"><?php echo $linea['UUII_AI_DISENO']; ?></td>
									<td class="center"><?php echo $linea['GESTOR']; ?></td> ->
									<td class="center"><?php echo $linea['FASE']; ?></td>
									<td class="center"><?php echo $linea['ESTADO_GIS']; ?></td>
<!-									<td class="center"><?php echo $linea['EECC_CARGA_RD']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENVIO']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENTREGA']; ?></td> ->
									<td class="center"><?php echo $linea['AI_RD_GRAL']; ?></td>
									<td class="center"><?php echo $linea['AIE2E']; ?></td>
									<td class="center"><?php echo $linea['ESTADO_AB_PL4']; ?></td>
									<td class="center"><?php echo $linea['FECHA_FIR']; ?></td>
									<td class="center"><?php echo $linea['FIR']; ?></td>
									<td class="center"><?php echo $linea['FX_INTERCAMBIO']; ?></td>
									<td class="center"><?php echo $linea['FX_ENTREGA_INTER']; ?></td>
									<td class="center"><?php echo $linea['ESTADO_RD_FDTT']; ?></td>
									
	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>	 -->           

	     
	            <div class="row-fluid sortable ui-sortable">	
	            	<div class="box span12">
	            		<h2>Pendientes de RECIBIR</h2>
	            	</div>	
	            </div>       
	            
	           <div class="row-fluid sortable ui-sortable">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<h2><i class="halflings-icon file"></i><span class="break"></span>RA</h2>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>
						</div>
						<div class="box-content">
							<table class="cell-border datatable" width="100%" cellspacing="0">
							  <thead>
								  <tr>
									 <!-- <th>Id_GD</th> -->
									  <th>Id_FDTT</th>
									 <!-- <th>PROVINCIA</th> -->
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <!--<th>NOMBRE_FICHERO</th>-->
									  <!--<th>FASE</th>-->
									  <th>EECC_CARGA_RA</th>
	                                  <th>FECHA_ENVIO</th>
									  <th>FECHA_ENTREGA</th>
									  <th>INC_EECC</th>
								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_ra_pend)) { 
                                      while ($linea = sqlsrv_fetch_array($registros_ra_pend)){ ?>

								<tr>
									<!--<td class="center"><?php echo $linea['Id_GD']; ?></td>-->
									<td class="center"><?php echo $linea['Id_FDTT']; ?></td>
									<!--<td class="center"><?php echo $linea['PROVINCIA']; ?></td>-->
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<!--<td class="center"><?php echo $linea['NOMBRE_FICHERO']; ?></td>-->
									<!--<td class="center"><?php echo $linea['FASE']; ?></td>-->
									<td class="center"><?php echo $linea['EECC_CARGA_RA']; ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_ENVIO'])) {echo date_format($linea['FECHA_ENVIO'], 'Y-m-d'); } ?></td>
									<td class="center"><input class="fechaModifRA span6 datepicker " type="datetime" name="fechaModifRA" id="<?php echo $linea['Id_FDTT']; ?>">
									<?php
										// Buscamos si la RA tiene incidencias (Subactividad=58 y relacionadas con el ID_ACTUACION de la RA)
										$incidencias = 0;
										$idActuacion = $linea['ID_ACTUACION']; 
										$tsqlINC = "SELECT COUNT(*) AS CONTADOR FROM INV_TBTAREAS WHERE id_actuacion='".$idActuacion."' AND id_Subactividad ='58'";
										$stmtINC = sqlsrv_query( $conn, $tsqlINC) or die ("Error al ejecutar consulta: ".$tsqlINC);
											
										$rowsINC = sqlsrv_has_rows( $stmtINC );

										if ($rowsINC === true){						
											$rowINC = sqlsrv_fetch_array($stmtINC);
											$incidencias=$rowINC['CONTADOR'];
                                                                                        $incidenciasAbiertas = 0;
                                                                                        $tsqlINCA = "SELECT COUNT(*) AS CONTADOR_A FROM INV_TBTAREAS WHERE id_actuacion='".$idActuacion."' AND id_Subactividad ='58' AND idEst <> '4'";
                                                                                        $stmtINCA = sqlsrv_query( $conn, $tsqlINCA) or die ("Error al ejecutar consulta: ".$tsqlINCA);
                                                                                        $rowsINCA = sqlsrv_has_rows( $stmtINCA );
                                                                                        if ($rowsINCA === true){
                                                                                            $rowINCA = sqlsrv_fetch_array($stmtINCA);
                                                                                            $incidenciasAbiertas=$rowINCA['CONTADOR_A'];
                                                                                            
                                                                                        }
										}
                                                                               
										sqlsrv_free_stmt( $stmtINC);
                                                                                sqlsrv_free_stmt( $stmtINCA);

									?>						
									<!-- <td class="center"><?php echo $linea['INC_EECC']; ?></td> -->
									<?php if (($incidencias==0) || ($incidenciasAbiertas==0)){ ?>
                                                                            <td class="center"><a title="Incidencias de RA" class="btn btn-success btn-mini buscar_incidencias_ra" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $idActuacion; ?>"><?php echo $incidencias; ?></td>
                                                                        <?php }
                                                                         if ($incidenciasAbiertas > 0){  ?>
                                                                            <td class="center"><a title="Incidencias de RA" class="btn btn-success-orange btn-mini buscar_incidencias_ra" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $idActuacion; ?>"><?php echo $incidencias; ?></td>
                                                                         <?php } ?>
										<i class="halflings-icon white eye-open"></i>  
									</a>

	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>
<!--

	            <div class="row-fluid sortable ui-sortable ">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<h2><i class="halflings-icon file"></i><span class="break"></span>RD DISEÑO</h2>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>
						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">
							  <thead>
								  <tr>
									  <th>Id_GD</th>
									  <th>Id_FDTT</th>
									  <th>PROVINCIA</th>
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <th>ACT. JAZZTEL</th>
									  <th>ACT. TESA</th>
									  <th>ID_ZONA</th>
	                                  <th>UUII_AI</th>
									  <th>GESTOR</th>
									  <th>EECC_DISENO</th>
									  <th>F.ENVIO</th>
									  <th>F.ENTREGA</th>
									  <th>INC_EECC</th>
								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_rd_pend_diseno)) {
                                      while ($linea = sqlsrv_fetch_array($registros_rd_pend_diseno)){ ?>
								<tr>
									<td class="center"><?php echo $linea['Id_GD']; ?></td>
									<td class="center"><?php echo $linea['Id_FDTT']; ?></td>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_JAZZTEL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_TESA']; ?></td>
									<td class="center"><?php echo $linea['ID_ZONA']; ?></td>
									<td class="center"><?php echo $linea['UUII_AI']; ?></td>
									<td class="center"><?php echo $linea['GESTOR']; ?></td>
									<td class="center"><?php echo $linea['EECC_CARGA_RD_DISENO']; ?></td>
									<td class="center"><?php if (!empty($linea['DIA_ENVIO_DISENO'])) {echo date_format($linea['DIA_ENVIO_DISENO'], 'Y-m-d H:i:s'); } ?></td>
									<td class="center"><input class="fechaModifRdDiseno span6 datepicker " type="datetime" name="fechaModifRdDiseno" id="<?php echo $linea['Id_FDTT']; ?>">
									

									<?php
										// Buscamos si la RD tiene incidencias (Subactividad=59 y relacionadas con el ID_ACTUACION de la RD)
										$incidencias = 0;
										$idActuacion = $linea['ID_ACTUACION']; 
										$tsqlINC = "SELECT COUNT(*) AS CONTADOR FROM INV_TBTAREAS WHERE id_actuacion='".$idActuacion."' AND id_Subactividad ='59'";
										$stmtINC = sqlsrv_query( $conn, $tsqlINC) or die ("Error al ejecutar consulta: ".$tsqlINC);
											
										$rowsINC = sqlsrv_has_rows( $stmtINC );

										if ($rowsINC === true){						
											$rowINC = sqlsrv_fetch_array($stmtINC);
											$incidencias=$rowINC['CONTADOR'];
										}
										sqlsrv_free_stmt( $stmtINC);

									?>						
									<!- <td class="center"><?php echo $linea['INC_EECCRD_GRAL']; ?></td> ->
									
									<td class="center"><a title="Incidencias de RD DISEÑO" class="btn btn-success btn-mini buscar_incidencias_rd" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $idActuacion; ?>"><?php echo $incidencias; ?></td>		
										<i class="halflings-icon white eye-open"></i>  
									</a>									

	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>

	            <div class="row-fluid sortable ui-sortable ">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<h2><i class="halflings-icon file"></i><span class="break"></span>RD</h2>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>
						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">
							  <thead>
								  <tr>
									  <th>Id_GD</th>
									  <th>Id_FDTT</th>
									  <th>PROVINCIA</th>
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <th>ACT. JAZZTEL</th>
									  <th>ACT. TESA</th>
									  <th>ID_ZONA</th>
	                                  <th>UUII_AI</th>
									  <th>GESTOR</th>
									  <th>EECC_CARGA</th>
									  <th>F.ENVIO</th>
									  <th>F.ENTREGA</th>
									  <th>INC_EECC</th>
								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_rd_pend)) { 
                                      while ($linea = sqlsrv_fetch_array($registros_rd_pend)){ ?>

								<tr>
									<td class="center"><?php echo $linea['Id_GD']; ?></td>
									<td class="center"><?php echo $linea['Id_FDTT']; ?></td>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_JAZZTEL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_TESA']; ?></td>
									<td class="center"><?php echo $linea['ID_ZONA']; ?></td>
									<td class="center"><?php echo $linea['UUII_AI']; ?></td>
									<td class="center"><?php echo $linea['GESTOR']; ?></td>
									<td class="center"><?php echo $linea['EECC_CARGA_RD']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENVIO']; ?></td>
									<td class="center"><input class="fechaModifRD span6 datepicker " type="datetime" name="fechaModifRD" id="<?php echo $linea['Id_FDTT']; ?>">
									

									<?php
										// Buscamos si la RD tiene incidencias (Subactividad=59 y relacionadas con el ID_ACTUACION de la RD)
										$incidencias = 0;
										$idActuacion = $linea['ID_ACTUACION']; 
										$tsqlINC = "SELECT COUNT(*) AS CONTADOR FROM INV_TBTAREAS WHERE id_actuacion='".$idActuacion."' AND id_Subactividad ='59'";
										$stmtINC = sqlsrv_query( $conn, $tsqlINC) or die ("Error al ejecutar consulta: ".$tsqlINC);
											
										$rowsINC = sqlsrv_has_rows( $stmtINC );

										if ($rowsINC === true){						
											$rowINC = sqlsrv_fetch_array($stmtINC);
											$incidencias=$rowINC['CONTADOR'];
										}
										sqlsrv_free_stmt( $stmtINC);

									?>						
									<!- <td class="center"><?php echo $linea['INC_EECCRD_GRAL']; ?></td> ->
									
									<td class="center"><a title="Incidencias de RD" class="btn btn-success btn-mini buscar_incidencias_rd" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $idActuacion; ?>"><?php echo $incidencias; ?></td>		
										<i class="halflings-icon white eye-open"></i>  
									</a>



	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>-->

            

<!-- 	          <div class="row-fluid sortable ui-sortable">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<h2><i class="halflings-icon file"></i><span class="break"></span>ICX</h2>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>
						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">
							  <thead>
								  <tr>
									  <th>Id_GD</th>
									  <th>Id_FDTT</th>
									  <th>PROVINCIA</th>
									  <th>REGION</th>
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <th>ACT.JAZZTEL</th>
									  <th>ACT.TESA</th>
									  <th>ID_ZONA</th>
	                                  <th>UUII_AI</th>
									  <th>GESTOR</th>
									  <th>FASE</th>
									  <th>EECC_CARGA</th>
									  <th>FECHA_COMUNICADO</th>
									  <th>ICX_FECHA</th>
									  <th>INC_EECC</th>
								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_icx_pend)) { 
                                      while ($linea = sqlsrv_fetch_array($registros_icx_pend)){ ?>

								<tr>
									<td class="center"><?php echo $linea['Id_GD']; ?></td>
									<td class="center"><?php echo $linea['Id_FDTT']; ?></td>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['REGION']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_JAZZTEL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_TESA']; ?></td>
									<td class="center"><?php echo $linea['ID_ZONA']; ?></td>
									<td class="center"><?php echo $linea['UUII_AI']; ?></td>	
									<td class="center"><?php echo $linea['GESTOR']; ?></td>
									<td class="center"><?php echo $linea['FASE']; ?></td>
									<td class="center"><?php echo $linea['ICX_EECC_CARGA']; ?></td>
									<td class="center"><?php echo $linea['ICX_FECHA_COMUNICADO_EECC']; ?></td>
									<td class="center"><input class="fechaModifICX span6 datepicker " type="datetime" name="fechaModifICX" id="<?php echo $linea['Id_GD']; ?>">
									<td class="center"><?php echo $linea['INC_EECC']; ?></td>							

	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>                         -->
	   

				 </fieldset>	

			</FORM>       	            
	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->
		
<div class="modal hide fade large" id="viewModalT" data-backdrop="static" data-keyboard="false" >
	<div class="modal-header btn-info">
		<button type="button" id="cerrarConsulta" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar</h2>
	</div>
    <div class="ct" style="height:80%;">
  
    </div>
</div>	

<div class="clearfix"></div>	

	
<?php
	print_theme_footer();
	sqlsrv_free_stmt($registros);
?>
<script type="text/JavaScript">
		//Inicializamos tareas del onload de la página
		window.onload = function ()
		{
		    $(".btn-minimize").each(function(){
		       $(this).click();
		    });

		    $("#fechaEnvio").on('change', function(e) {
	       		$(this).val($(this).attr("value").substring(4));
	       		var valor=$(this).attr("value");
	       		var id=$(this).attr("id");
		    });		    

		    $(".fechaModifRA").each(function(){
		       $(this).on('change', function(e) {
		       		$(this).val($(this).attr("value").substring(4));
		       		var valor=$(this).attr("value");
		       		var id=$(this).attr("id");
		       		
		       		ModificarFecha('FECHA_PENDIENTE_RA',id,valor);
		       		
		       	});	
		    });

		    $(".fechaModifRdDiseno").each(function(){
		       $(this).on('change', function(e) {
		       		$(this).val($(this).attr("value").substring(4));
		       		var valor=$(this).attr("value");
		       		var id=$(this).attr("id");
		       		
		       		ModificarFecha('FECHA_PENDIENTE_RD_DISENO',id,valor);
		       		
		       	});	
		    });		  

		    $(".fechaModifRD").each(function(){
		       $(this).on('change', function(e) {
		       		$(this).val($(this).attr("value").substring(4));
		       		var valor=$(this).attr("value");
		       		var id=$(this).attr("id");
		       		
		       		ModificarFecha('FECHA_PENDIENTE_RD',id,valor);
		       		
		       	});	
		    });		 

		    $(".fechaModifICX").each(function(){
		       $(this).on('change', function(e) {
		       		$(this).val($(this).attr("value").substring(4));
		       		var valor=$(this).attr("value");
		       		var id=$(this).attr("id");
		       		
		       		ModificarFecha('FECHA_PENDIENTE_ICX',id,valor);
		       		
		       	});	
		    });		 		     		      

		}		


</script>