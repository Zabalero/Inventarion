<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
	require "inc/funcionesImportar.inc";
	require_once "PHPExcel_1.8.0/Classes/PHPExcel.php";
	require_once "PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php";		

	//Si el usuario no está autorizado se le desconecta
	$rolUsuario=get_rol($_SESSION['usuario']);
	if ($rolUsuario != 'escritura' && $rolUsuario != 'avanzado') {
		header('Location: index.php?mensaje=Usuario%20desconectado');
	}	


	//Inicializa las variables utilizadas en el formulario
    $ficheroEstadosRA = "";
    $ficheroEstadosRD = "";
    $mensaje = "";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	if($_SERVER['REQUEST_METHOD']=='POST') {
		$ficheroEstadosRA = $_FILES['ficheroEstadosRA']['name'];
		$ficheroEstadosRD = $_FILES['ficheroEstadosRD']['name'];

		//Actualizar estados de RA
		if (isset($_POST['actualizarEstadosRA'])) {		

			if (isset($_FILES['ficheroEstadosRA']) && $_FILES['ficheroEstadosRA']['tmp_name'] != '') {	

				$safe_filename=replace_specials_characters($_FILES['ficheroEstadosRA']['name']);
				
				$isMove = move_uploaded_file ($_FILES['ficheroEstadosRA']['tmp_name'], 'upload/'.$safe_filename);

				if ($isMove){

					$ficheroEntrada = './upload/'.$safe_filename; 

					$objPHPExcel = PHPExcel_IOFactory::load($ficheroEntrada); 

					//Asigno la hoja de calculo activa
					$objPHPExcel->setActiveSheetIndex(0);

					//Obtengo el numero de filas del archivo
					$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

					if ($numRows > 0) {

						for ($i = 1; $i <= $numRows; $i++) {
						    $Id_FDTT_entrada = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getFormattedValue();
						    $Estado_carga_GIS_entrada = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getFormattedValue();

						    if ($Id_FDTT_entrada != '') {
								//Actualiza la tabla RA con el nuevo estado
								$tsql2 = "UPDATE INV_RA SET Estado_carga_GIS = '".$Estado_carga_GIS_entrada."' WHERE Id_FDTT = '".$Id_FDTT_entrada."' AND (Estado_carga_GIS is null or Estado_carga_GIS != '".$Estado_carga_GIS_entrada."' )";

								//echo "SENTENCIA: ".$tsql2."<br>";

								$stmt2 = sqlsrv_query( $conn, $tsql2);
												
								sqlsrv_free_stmt( $stmt2);		
							}

						}

						$mensaje = "ficheroEstadosRA - Actualización correcta";

					}
				} else {
					$mensaje = 'Error al copiar el fichero de Estados de RA';
				}

			} else {
				$mensaje = 'Fichero de Estados de RA obligatorio';
			}					

		} else {


			//Actualizar estados de RD
			if (isset($_POST['actualizarEstadosRD'])) {		

				if (isset($_FILES['ficheroEstadosRD']) && $_FILES['ficheroEstadosRD']['tmp_name'] != '') {	

					$safe_filename=replace_specials_characters($_FILES['ficheroEstadosRD']['name']);
					
					$isMove = move_uploaded_file ($_FILES['ficheroEstadosRD']['tmp_name'], 'upload/'.$safe_filename);

					if ($isMove){

						$ficheroEntrada = './upload/'.$safe_filename; 

						$objPHPExcel = PHPExcel_IOFactory::load($ficheroEntrada); 

						//Asigno la hoja de calculo activa
						$objPHPExcel->setActiveSheetIndex(0);

						//Obtengo el numero de filas del archivo
						$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

						if ($numRows > 0) {
							
							for ($i = 1; $i <= $numRows; $i++) {
							    $Id_FDTT_entrada = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getFormattedValue();
							    $ESTADO_GIS_entrada = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getFormattedValue();
							    
							    if ($Id_FDTT_entrada != '') {
									//Actualiza la tabla RA con el nuevo estado
									$tsql2 = "UPDATE INV_RD SET ESTADO_GIS = '".$ESTADO_GIS_entrada."' WHERE Id_FDTT = '".$Id_FDTT_entrada."' AND (ESTADO_GIS is null or ESTADO_GIS != '".$ESTADO_GIS_entrada."') ";

									$stmt2 = sqlsrv_query( $conn, $tsql2);

									sqlsrv_free_stmt( $stmt2);
								}
							}

							$mensaje = "ficheroEstadosRD - Actualización correcta";

						}
					} else {
						$mensaje = 'Error al copiar el fichero de Estados de RD';
					}

				} else {
					$mensaje = 'Fichero de Estados de RD obligatorio';
				}					

			}



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
					<a href="index.php">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Actualiza Estados RA/RD</a></li>
			</ul>

			<FORM id="actualiza_estados" autocomplete="off" METHOD="POST" NAME="actualiza_estados"  class="form-horizontal" enctype="multipart/form-data">
				<fieldset>

					<!-- RA -->
			    	<div class="row-fluid">
						
						<div class="span3">
							<div class="control-group">
								<label class="control-label" for="ficheroEstadosRA">EXCEL Estados RA</label>
								<div class="controls">
									<?php
										echo '<input type="file" name="ficheroEstadosRA" id="ficheroEstadosRA" value="<?php echo $ficheroEstadosRA;?>" />';
									?>
									
								</div>
							</div>		

						</div>	
						
						<div class="span2">
							<div class="control-group form-group">	
								<div class="controls">
									<button onclick="return confirmarAccion();" type="submit" id="actualizarEstadosRA"  name="actualizarEstadosRA" value="actualizarEstadosRA" class="btn btn-danger btn-small confirmar"><i class="halflings-icon white upload"></i> Actualizar Estados RA</button>
								</div>	
							</div>			
						</div>


						<div class="span1">
							<div class="control-group">
								<div class="controls">

									<a href="scripts/excelExport.php?origen=DESCARGA_ESTADO_RA" title="Exportar Estados RA" class="btn btn-success btn-mini">		
										<i class="halflings-icon white download"></i> EXPORTAR ESTADOS RA 
									</a>	

								</div>	
							</div>		
						 </div>					
					 			 				 
					</div>


					<!-- RD -->
			    	<div class="row-fluid">
						
						<div class="span3">
							<div class="control-group">
								<label class="control-label" for="ficheroEstadosRA">EXCEL Estados RD</label>
								<div class="controls">
									<?php
										echo '<input type="file" name="ficheroEstadosRD" id="ficheroEstadosRD" value="<?php echo $ficheroEstadosRD;?>" />';
									?>
									
								</div>
							</div>		

						</div>	
						
						<div class="span2">
							<div class="control-group form-group">	
								<div class="controls">
									<button onclick="return confirmarAccion();" type="submit" id="actualizarEstadosRD"  name="actualizarEstadosRD" value="actualizarEstadosRD" class="btn btn-danger btn-small confirmar"><i class="halflings-icon white upload"></i> Actualizar Estados RD</button>
								</div>	
							</div>			
						</div>


						<div class="span1">
							<div class="control-group">
								<div class="controls">

									<a href="scripts/excelExport.php?origen=DESCARGA_ESTADO_RD" title="Exportar Estados RD" class="btn btn-success btn-mini">		
										<i class="halflings-icon white download"></i> EXPORTAR ESTADOS RD 
									</a>	

								</div>	
							</div>		
						 </div>					
					 			 				 
					</div>					

					<div class="row-fluid">
						<div class="alert alert-success">
								<button type="button" class="close" data-dismiss="alert">×</button>
								<?php echo $mensaje;?>
						</div>					
					</div>		

				</fieldset>	

			</FORM>       
			<!--FIN FILTROS-->  




	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->

<div class="clearfix"></div>
	
<?php
	print_theme_footer();
	sqlsrv_free_stmt($registros);
?>
