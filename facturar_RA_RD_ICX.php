<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
	require "inc/funcionesImportar.inc";
	require "inc/funcionesFacturar.inc";
	require_once "PHPExcel_1.8.0/Classes/PHPExcel.php";
	require_once "PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php";

	//Inicializa las variables utilizadas en el formulario
 
    $empresa = "";  
    $empresaEnvio = "";  
    $fechaEnvio = ""; 
    $ficheroFacturacion = "";

    //FIN Inicializa las variables utilizadas en el formulario

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	//Variables para la búsqueda
	$maxRows = 999999;
	$pageNum = 0;
	$seleccionada = 0;
	if (isset($_GET['pageNum'])) {
	  $pageNum = $_GET['pageNum'];
	}
	$startRow = $pageNum * $maxRows;


	if($_SERVER['REQUEST_METHOD']=='POST') {
	    $empresa = $_REQUEST['empresa'];
	    $fechaEnvio = $_REQUEST['fechaEnvio'];
	    $ficheroFacturacion = $_FILES['ficheroFacturacion']['name'];
	    	    
	    if ($empresa == '') {
	    	$empresaEnvio = 'GAMMA';
	    } else {
	    	$empresaEnvio = $empresa;
	    }		

	    if ($fechaEnvio == '') {
	    	$date = new DateTime();
	    	$fechaEnvio = $date->format('Y-m-d');
	    }		

	    $anioMesFact = date('Y-m', strtotime($fechaEnvio));

		// if ($_POST['buscar']) {

		// 	//PENDIENTES DE FACTURAR

		// 	$tsql_rd_pend_fact = "SELECT Id_GD,Id_FDTT, PROVINCIA, CABECERA, ARBOL, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, UUII_AI, GESTOR, 
		// 							EECC_CARGA_RD, FECHA_ENVIO, FECHA_ENTREGA, INC_EECCRD_GRAL 
		// 					From INV_VIEW_RD_TODO 
		// 					WHERE ((FECHA_ENTREGA Is Not Null) AND (FECHA_FACTURACION Is Null) AND 
		// 						(ESTADO_RD_FDTT = 'RD FINALIZADA'))";
		// 	if (isset($empresa) && $empresa != "") {
		// 		$tsql_rd_pend_fact = $tsql_rd_pend_fact . " AND EECC_CARGA_RD = '$empresa'";				
		// 	}							
			
		// 	//Recuperar datos de consulta
		// 	$registros_rd_pend_fact = sqlsrv_query($conn, $tsql_rd_pend_fact, array(), array( "Scrollable" => 'static' ));	

		// 	$tsql_ra_pend_fact = "SELECT Id_GD,Id_FDTT, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, 
		// 							FECHA_ENVIO, FECHA_ENTREGA, INC_EECC 
		// 					FROM INV_RA
		// 					LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
		// 					LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
		// 					WHERE ((FECHA_ENTREGA Is Not Null) AND (FECHA_FACTURACION Is Null) AND
		// 						(ESTADO_RA_FDTT = 'RA FINALIZADO'))";
							
		// 	if (isset($empresa) && $empresa != "") {
		// 		$tsql_ra_pend_fact = $tsql_ra_pend_fact . " AND EECC_CARGA_RA = '$empresa'";				
		// 	}							
			
		// 	//Recuperar datos de consulta
		// 	$registros_ra_pend_fact = sqlsrv_query($conn, $tsql_ra_pend_fact, array(), array( "Scrollable" => 'static' ));			

		// }

		if (isset($_POST['procesar'])) {	
			// PROCESAR FICHERO ENTRADA FACTURACIÓN


			if (isset($_FILES['ficheroFacturacion']) && $_FILES['ficheroFacturacion']['tmp_name'] != '') {	
				

				$safe_filename=replace_specials_characters($_FILES['ficheroFacturacion']['name']);
				
				$isMove = move_uploaded_file ($_FILES['ficheroFacturacion']['tmp_name'], 'upload/'.$safe_filename);

				if ($isMove){
					$ficheroEntrada = './upload/'.$safe_filename; 

					$objPHPExcel = PHPExcel_IOFactory::load($ficheroEntrada); 
					
					//NUEVAS RA
					//Asigno la hoja de calculo activa
					$objPHPExcel->setActiveSheetIndex(1);
					//Obtengo el numero de filas del archivo
					$numRows = $objPHPExcel->setActiveSheetIndex(1)->getHighestRow();

					if ($numRows > 0) {
						
						$Fich_RA_Diseno[] = array();
						
						for ($i = 1; $i <= $numRows; $i++) {
						    $empresaEntrada = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getFormattedValue();
						    $fechaE = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getFormattedValue();
						    $fechaEntrada = date_format(date_create_from_format('m-d-y', $fechaE), 'Y-m-d');
						    $anioMesOrden = date('Y-m', strtotime($fechaEntrada));
						    
						    if ($empresaEntrada == $empresaEnvio && $anioMesOrden == $anioMesFact) {
						    	$Fich_RA_Diseno[] = "'".$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getFormattedValue()."'";
						    }

						}

						$mensaje = "ficheroFacturacion - Importación correcta";

					}

					//ACTUALIZACION DE RA
					//Asigno la hoja de calculo activa
					$objPHPExcel->setActiveSheetIndex(2);
					//Obtengo el numero de filas del archivo
					$numRows = $objPHPExcel->setActiveSheetIndex(2)->getHighestRow();

					if ($numRows > 0) {
						
						$Fich_RA_Actualizada[] = array();
						
						for ($i = 1; $i <= $numRows; $i++) {
						    $empresaEntrada = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getFormattedValue();
						    $fechaE = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getFormattedValue();
						    $fechaEntrada = date_format(date_create_from_format('m-d-y', $fechaE), 'Y-m-d');
						    $anioMesOrden = date('Y-m', strtotime($fechaEntrada));
						    
						    if ($empresaEntrada == $empresaEnvio && $anioMesOrden == $anioMesFact) {
						    	$Fich_RA_Actualizada[] = "'".$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getFormattedValue()."'";
						    }

						}

						$mensaje = "ficheroFacturacion - Importación correcta";

					}

					//NUEVOS RD
					//Asigno la hoja de calculo activa
					$objPHPExcel->setActiveSheetIndex(3);
					//Obtengo el numero de filas del archivo
					$numRows = $objPHPExcel->setActiveSheetIndex(3)->getHighestRow();

					if ($numRows > 0) {
						
						$Fich_RD_Diseno[] = array();
						
						for ($i = 1; $i <= $numRows; $i++) {
						    $empresaEntrada = $objPHPExcel->getActiveSheet()->getCell('T'.$i)->getFormattedValue();
						    $fechaE = $objPHPExcel->getActiveSheet()->getCell('U'.$i)->getFormattedValue();
						    $fechaEntrada = date_format(date_create_from_format('m-d-y', $fechaE), 'Y-m-d');
						    $anioMesOrden = date('Y-m', strtotime($fechaEntrada));
						    
						    if ($empresaEntrada == $empresaEnvio && $anioMesOrden == $anioMesFact) {
						    	$Fich_RD_Diseno[] = "'".$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getFormattedValue()."'";
						    }

						}

						$mensaje = "ficheroFacturacion - Importación correcta";

					}

					//ACTUALIZACION DE RD
					//Asigno la hoja de calculo activa
					$objPHPExcel->setActiveSheetIndex(4);
					//Obtengo el numero de filas del archivo
					$numRows = $objPHPExcel->setActiveSheetIndex(4)->getHighestRow();

					if ($numRows > 0) {
						
						$Fich_RD_Actualizada[] = array();
						
						for ($i = 1; $i <= $numRows; $i++) {
						    $empresaEntrada = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getFormattedValue();
						    $fechaE = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getFormattedValue();
						    $fechaEntrada = date_format(date_create_from_format('m-d-y', $fechaE), 'Y-m-d');
						    $anioMesOrden = date('Y-m', strtotime($fechaEntrada));
						    
						    if ($empresaEntrada == $empresaEnvio && $anioMesOrden == $anioMesFact) {
						    	$Fich_RD_Actualizada[] = "'".$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getFormattedValue()."'";
						    }

						}

						$mensaje = "ficheroFacturacion - Importación correcta";

					}					

				} else {
					$mensaje = 'Error al copiar el fichero de Facturación';
				}

			} else {
				$mensaje = 'Fichero de Facturación obligatorio';
			}

			//NUEVAS RA
			if (count($Fich_RA_Diseno) > 0) {
				$lista = implode(',', $Fich_RA_Diseno);
				$long = strlen($lista) - 6;
				$list=substr($lista,6,$long);
				$tsql_ra_pend_fact = "SELECT Id_GD,Id_FDTT, ID_ACTUACION, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, 
										FECHA_ENVIO, FECHA_ENTREGA, INC_EECC, ESTADO_RA_FDTT, FECHA_FACTURACION
								FROM INV_RA
								LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
								LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
								WHERE Id_GD IN ($list)";

				//Recuperar datos de consulta NUEVAS RA
				$registros_ra_pend_fact = sqlsrv_query($conn, $tsql_ra_pend_fact, array(), array( "Scrollable" => 'static' ));			

			}

			//ACTUALIZACION DE RA
			if (count($Fich_RA_Actualizada) > 0) {
				$lista = implode(',', $Fich_RA_Actualizada);
				$long = strlen($lista) - 6;
				$list=substr($lista,6,$long);
				$tsql_ra_pend_fact_act = "SELECT Id_GD,Id_FDTT, ID_ACTUACION, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, 
										FECHA_ENVIO, FECHA_ENTREGA, INC_EECC, ESTADO_RA_FDTT, FECHA_FACTURACION 
								FROM INV_RA
								LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
								LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
								WHERE Id_GD IN ($list)";

				//Recuperar datos de consulta NUEVAS RA
				$registros_ra_pend_fact_act = sqlsrv_query($conn, $tsql_ra_pend_fact_act, array(), array( "Scrollable" => 'static' ));			

			}

			//NUEVOS RD
			if (count($Fich_RD_Diseno) > 0) {
				$lista = implode(',', $Fich_RD_Diseno);
				$long = strlen($lista) - 6;
				$list=substr($lista,6,$long);
				$tsql_rd_pend_fact = "SELECT Id_GD,Id_FDTT, ID_ACTUACION, PROVINCIA, CABECERA, ARBOL, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, UUII_AI, GESTOR, 
										EECC_CARGA_RD, FECHA_ENVIO, FECHA_ENTREGA, INC_EECCRD_GRAL, ESTADO_RD_FDTT,
										FECHA_FACTURACION 
								From INV_VIEW_RD_TODO 
								WHERE Id_GD IN ($list)";

				//Recuperar datos de consulta NUEVAS RA
				$registros_rd_pend_fact = sqlsrv_query($conn, $tsql_rd_pend_fact, array(), array( "Scrollable" => 'static' ));			

			}				

			//ACTUALIZACION DE RD
			if (count($Fich_RD_Actualizada) > 0) {
				$lista = implode(',', $Fich_RD_Actualizada);
				$long = strlen($lista) - 6;
				$list=substr($lista,6,$long);
				$tsql_rd_pend_fact_act = "SELECT Id_GD,Id_FDTT, ID_ACTUACION, PROVINCIA, CABECERA, ARBOL, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, UUII_AI, GESTOR, 
										EECC_CARGA_RD, FECHA_ENVIO, FECHA_ENTREGA, INC_EECCRD_GRAL, ESTADO_RD_FDTT,
										FECHA_FACTURACION 
								From INV_VIEW_RD_TODO 
								WHERE Id_GD IN ($list)";

				//Recuperar datos de consulta NUEVAS RA
				$registros_rd_pend_fact_act = sqlsrv_query($conn, $tsql_rd_pend_fact_act, array(), array( "Scrollable" => 'static' ));			

			}									

		} //FIN PROCESAR FICHERO

		if (isset($_POST['facturar'])) {	
			// FACTURAR
			$marcarFacturaRd = $_REQUEST['marcarFacturaRd'];
			$marcarFacturaRdAct = $_REQUEST['marcarFacturaRdAct'];
			$marcarFacturaRa = $_REQUEST['marcarFacturaRa'];
			$marcarFacturaRaAct = $_REQUEST['marcarFacturaRaAct'];
			
			facturarGAMMA($conn,$fechaEnvio,$empresaEnvio,$marcarFacturaRd,$marcarFacturaRdAct,$marcarFacturaRa, $marcarFacturaRaAct);	
		}		

	}


	// print the page header
	print_theme_header();


?>
			<!-- start: Content -->
		<div id="content" class="span12">
			
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="index.html">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Envío GAMMA</a></li>
			</ul>

			<!--FILTROS-->
			<FORM id="busqueda" autocomplete="off" METHOD="POST" NAME="opciones"  class="form-horizontal"  enctype="multipart/form-data">
				<fieldset>
		    	<div class="row-fluid">
					<div class="span3">					
						<div class="control-group">
							<label class="control-label" for="prior">Empresa </label>
							<div class="controls">
								<SELECT tabindex="0" id="empresa"  name="empresa" >';				
										
										<option value=""></option>
										<?php
										echo '<option value="GAMMA" '.(($empresa=="GAMMA")?'selected="selected"':"").'>GAMMA</option>';
										?>
										
										
								</SELECT>
							</div>	
						</div>	
					</div>		  
	
				</div>				
				<div class="row-fluid">								
					<div class="span3">
						<div class="control-group">
							<label class="control-label" for="ficheroFacturacion">Fichero Facturación</label>
							<div class="controls">
								<?php
									echo '<input type="file" name="ficheroFacturacion" id="ficheroFacturacion" value="<?php echo $ficheroFacturacion;?>" />';
								?>
								
							</div>
						</div>		
					 </div>	

					<div class="span2">		
						<button type="submit" id="procesar" name="procesar" value="procesar" class="btn btn-success btn-small confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white check"></i> Procesar</button>
					</div>							
																					
				</div>


				<div class="row-fluid">								
					<div class="span3">
						<div class="control-group">
							<label class="control-label" for="fRegistro1">Fecha Facturación</label>
							<div class="controls">
								<?php
									echo '<input tabindex="7" type="text" class="input datepicker date_field" id="fechaEnvio"  name="fechaEnvio" value="'.$fechaEnvio.'" data-date-format="yyyy-mm-dd">';
								?>
							</div>
						</div>		
					 </div>	

					<div class="span2">		
						<?php if($_SERVER['REQUEST_METHOD']=='POST') { ?>
							<button type="submit" id="facturar" name="facturar" value="facturar" class="btn btn-warning btn-small confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white check"></i> Facturar</button>
						<?php } ?>
					</div>																
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
	            		<h2>Pendientes de FACTURAR</h2>
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
							<table class="table table-striped table-bordered buscar">
							  <thead>
								  <tr>
								  	  <th class="hidden">x</th>
									  <th>Id_GD</th>
									  <th>Id_FDTT</th>
									  <th>PROVINCIA</th>
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <th>FASE</th>
									  <th>EST_RA_FDTT</th>
									  <th>EECC_CARGA_RA</th>
	                                  <th>F_ENVIO</th>
									  <th>F_ENTREGA</th>
									  <th>F_FACTURACION</th>
									  <th>INC_EECC</th>

								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_ra_pend_fact)) { while ($linea = sqlsrv_fetch_array($registros_ra_pend_fact)){ ?>
								<tr>
									<?php 
									//Marca de check para las entregas RD DISEÑO
										print ("<TD class='center hidden'><INPUT TYPE='CHECKBOX' checked NAME='marcarFacturaRa[]' VALUE='".$linea['Id_FDTT']."'></TD>\n");
									 ?>								
									<td class="center"><?php echo $linea['Id_GD']; ?></td>
									<td class="center"><?php echo $linea['Id_FDTT']; ?></td>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<td class="center"><?php echo $linea['FASE']; ?></td>
									<td class="center"><?php if ($linea['ESTADO_RA_FDTT'] != 'RA FINALIZADO') {echo '<span style="color:red">'.$linea['ESTADO_RA_FDTT'].'</span>'; } else {echo $linea['ESTADO_RA_FDTT']; }; ?></td>
									<td class="center"><?php echo $linea['EECC_CARGA_RA']; ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_ENVIO'])) {echo date_format($linea['FECHA_ENVIO'], 'Y-m-d'); } ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_ENTREGA'])) {echo date_format($linea['FECHA_ENTREGA'], 'Y-m-d'); } ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_FACTURACION'])) {echo '<span style="color:red">'.date_format($linea['FECHA_FACTURACION'], 'Y-m-d').'</span>'; } ?></td>
									

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
										}
										sqlsrv_free_stmt( $stmtINC);

									?>						
									<!-- <td class="center"><?php echo $linea['INC_EECC']; ?></td> -->
									
									<td class="center"><a title="Incidencias de RA" class="btn btn-success btn-mini buscar_incidencias_ra" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $idActuacion; ?>"><?php echo $incidencias; ?></td>		
										<i class="halflings-icon white eye-open"></i>  
									</a>									
									
	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>	  

 	           <div class="row-fluid sortable ui-sortable">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>							
							<h2><i class="halflings-icon file"></i><span class="break"></span>RA ACT</h2>

						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered buscar">
							  <thead>
								  <tr>
								  	  <th class="hidden">x</th>
									  <th>Id_GD</th>
									  <th>Id_FDTT</th>
									  <th>PROVINCIA</th>
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <th>FASE</th>
									  <th>EST_RA_FDTT</th>
									  <th>EECC_CARGA_RA</th>
	                                  <th>F_ENVIO</th>
									  <th>F_ENTREGA</th>
									  <th>F_FACTURACION</th>
									  <th>INC_EECC</th>

								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_ra_pend_fact_act)) { while ($linea = sqlsrv_fetch_array($registros_ra_pend_fact_act)){ ?>
								<tr>
									<?php 
									//Marca de check para las entregas RD DISEÑO
										print ("<TD class='center hidden'><INPUT TYPE='CHECKBOX' checked NAME='marcarFacturaRaAct[]' VALUE='".$linea['Id_FDTT']."'></TD>\n");
									 ?>								
									<td class="center"><?php echo $linea['Id_GD']; ?></td>
									<td class="center"><?php echo $linea['Id_FDTT']; ?></td>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<td class="center"><?php echo $linea['FASE']; ?></td>
									<td class="center"><?php if ($linea['ESTADO_RA_FDTT'] != 'RA FINALIZADO') {echo '<span style="color:red">'.$linea['ESTADO_RA_FDTT'].'</span>'; } else {echo $linea['ESTADO_RA_FDTT']; }; ?></td>
									<td class="center"><?php echo $linea['EECC_CARGA_RA']; ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_ENVIO'])) {echo date_format($linea['FECHA_ENVIO'], 'Y-m-d'); } ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_ENTREGA'])) {echo date_format($linea['FECHA_ENTREGA'], 'Y-m-d'); } ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_FACTURACION'])) {echo '<span style="color:red">'.date_format($linea['FECHA_FACTURACION'], 'Y-m-d').'</span>'; } ?></td>
									

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
										}
										sqlsrv_free_stmt( $stmtINC);

									?>						
									<!-- <td class="center"><?php echo $linea['INC_EECC']; ?></td> -->
									
									<td class="center"><a title="Incidencias de RA" class="btn btn-success btn-mini buscar_incidencias_ra" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $idActuacion; ?>"><?php echo $incidencias; ?></td>		
										<i class="halflings-icon white eye-open"></i>  
									</a>																		
									
	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>	  


	           <div class="row-fluid sortable ui-sortable">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>							
							<h2><i class="halflings-icon file"></i><span class="break"></span>RD</h2>

						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered buscar">
							  <thead>
								  <tr>
								  	  <th class="hidden">x</th>	
									  <th>Id_GD</th>
									  <th>Id_FDTT</th>
									  <th>PROVINCIA</th>
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <th>ACT. JAZZTEL</th>
									  <th>ACT. TESA</th>
<!-- 									  <th>ID_ZONA</th>
	                                  <th>UUII_AI</th>
									  <th>GESTOR</th> -->
									  <th>EST_RD_FDTT</th>
									  <th>EECC_CARGA</th>
									  <th>F_ENVIO</th>
									  <th>F_ENTREGA</th>
									  <th>F_FACTURACION</th>
									  <th>INC_EECC</th>

								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_rd_pend_fact)) { while ($linea = sqlsrv_fetch_array($registros_rd_pend_fact)){ ?>
								<tr>
									<?php 
									//Marca de check para las entregas RD
										print ("<TD class='center hidden'><INPUT TYPE='CHECKBOX' checked NAME='marcarFacturaRd[]' VALUE='".$linea['Id_FDTT']."'></TD>\n");
									 ?>								
									<td class="center"><?php echo $linea['Id_GD']; ?></td>
									<td class="center"><?php echo $linea['Id_FDTT']; ?></td>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_JAZZTEL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_TESA']; ?></td>
<!-- 									<td class="center"><?php echo $linea['ID_ZONA']; ?></td>
									<td class="center"><?php echo $linea['UUII_AI']; ?></td>
									<td class="center"><?php echo $linea['GESTOR']; ?></td> -->
									<td class="center"><?php if ($linea['ESTADO_RD_FDTT'] != 'RD FINALIZADA') {echo '<span style="color:red">'.$linea['ESTADO_RD_FDTT'].'</span>'; } else {echo $linea['ESTADO_RD_FDTT']; }; ?></td>
									<td class="center"><?php echo $linea['EECC_CARGA_RD']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENVIO']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENTREGA']; ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_FACTURACION'])) {echo '<span style="color:red">'.date_format($linea['FECHA_FACTURACION'], 'Y-m-d').'</span>'; } ?></td>
									
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
									<!-- <td class="center"><?php echo $linea['INC_EECCRD_GRAL']; ?></td> -->
									
									<td class="center"><a title="Incidencias de RD" class="btn btn-success btn-mini buscar_incidencias_rd" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $idActuacion; ?>"><?php echo $incidencias; ?></td>		
										<i class="halflings-icon white eye-open"></i>  
									</a>

									
	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>	            


	           <div class="row-fluid sortable ui-sortable">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>							
							<h2><i class="halflings-icon file"></i><span class="break"></span>RD ACT</h2>

						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered buscar">
							  <thead>
								  <tr>
								  	  <th class="hidden">x</th>	
									  <th>Id_GD</th>
									  <th>Id_FDTT</th>
									  <th>PROVINCIA</th>
									  <th>CABECERA</th>
									  <th>ARBOL</th>
									  <th>ACT. JAZZTEL</th>
									  <th>ACT. TESA</th>
<!-- 									  <th>ID_ZONA</th>
	                                  <th>UUII_AI</th>
									  <th>GESTOR</th> -->
									  <th>EST_RD_FDTT</th>
									  <th>EECC_CARGA</th>
									  <th>F_ENVIO</th>
									  <th>F_ENTREGA</th>
									  <th>F_FACTURACION</th>
									  <th>INC_EECC</th>

								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros_rd_pend_fact_act)) { while ($linea = sqlsrv_fetch_array($registros_rd_pend_fact_act)){ ?>
								<tr>
									<?php 
									//Marca de check para las entregas RD
										print ("<TD class='center hidden'><INPUT TYPE='CHECKBOX' checked NAME='marcarFacturaRdAct[]' VALUE='".$linea['Id_FDTT']."'></TD>\n");
									 ?>								
									<td class="center"><?php echo $linea['Id_GD']; ?></td>
									<td class="center"><?php echo $linea['Id_FDTT']; ?></td>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="center"><?php echo $linea['ARBOL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_JAZZTEL']; ?></td>
									<td class="center"><?php echo $linea['ACTUACION_TESA']; ?></td>
<!-- 									<td class="center"><?php echo $linea['ID_ZONA']; ?></td>
									<td class="center"><?php echo $linea['UUII_AI']; ?></td>
									<td class="center"><?php echo $linea['GESTOR']; ?></td> -->
									<td class="center"><?php if ($linea['ESTADO_RD_FDTT'] != 'RD FINALIZADA') {echo '<span style="color:red">'.$linea['ESTADO_RD_FDTT'].'</span>'; } else {echo $linea['ESTADO_RD_FDTT']; }; ?></td>
									<td class="center"><?php echo $linea['EECC_CARGA_RD']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENVIO']; ?></td>
									<td class="center"><?php echo $linea['FECHA_ENTREGA']; ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_FACTURACION'])) {echo '<span style="color:red">'.date_format($linea['FECHA_FACTURACION'], 'Y-m-d').'</span>'; } ?></td>
									
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
									<!-- <td class="center"><?php echo $linea['INC_EECCRD_GRAL']; ?></td> -->
									
									<td class="center"><a title="Incidencias de RD" class="btn btn-success btn-mini buscar_incidencias_rd" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $idActuacion; ?>"><?php echo $incidencias; ?></td>		
										<i class="halflings-icon white eye-open"></i>  
									</a>									

									
	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>	            

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