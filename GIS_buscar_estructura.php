<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";


	//Si el usuario no está autorizado se le desconecta
	$rolUsuario=get_rol($_SESSION['usuario']);
	if ($rolUsuario != 'escritura' && $rolUsuario != 'avanzado') {
		header('Location: index.php?mensaje=Usuario%20desconectado');
	}	



	//Inicializa las variables utilizadas en el formulario
 
	$id = "";    
	$primera_vez = 1;
	$act_no_exsite = 1;
	$tipo_actuacion = '';

      
	// Si ya hemos introducido valores para filtros de búsqueda
    if($_SERVER['REQUEST_METHOD']=='POST')
    {  
      
        $id = $_REQUEST['id'];  
        
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


	// Si ha introducido la actuación a buscar
	if($_SERVER['REQUEST_METHOD']=='POST') {
		$primera_vez = 0;
		if ($_POST['buscar']) {
			//Buscamos la actuación por actuacion_jazztel (incluidas aqui las OLT) o actuacion_tesa	
			$tsql = "SELECT * 
					FROM inv_actuaciones
					WHERE	
						ACT_JAZZTEL = '".$id."' OR
						ACT_TESA = '".$id."'";
			//Recuperar datos de consulta
			$resultado = sqlsrv_query($conn, $tsql);
			$rows = sqlsrv_has_rows( $resultado );
		
			//Si no se encuentra la actuación por estos datos, hay que buscar por CTO
			if ($rows === false){				

				$tsql = "SELECT * 
						FROM INV_CTOS
						WHERE	
							NUMERO = '".$id."'";
				//Recuperar datos de consulta
				$resultado = sqlsrv_query($conn, $tsql);
				$rows = sqlsrv_has_rows( $resultado );
				//Si no se encuentra la actuación por estos datos, hay que mostrar el mensaje de actuación no existente
				if ($rows === false){	
				   	$act_no_exsite = 1;
				} else {
					$registro = sqlsrv_fetch_array($resultado);
					//Si hemos encontrado la CTO, accedemos a la tabla de actuaciones para extraer sus datos
					$tsql = "SELECT * 
							FROM inv_actuaciones
							WHERE	
								ID_ACTUACION = '".$registro['id_Actuacion']."'";
					//Recuperar datos de consulta
					$resultado = sqlsrv_query($conn, $tsql);
					$rows = sqlsrv_has_rows( $resultado );

					//Si no se encuentra la actuación por estos datos, hay que mostrar el mensaje de actuación no existente
					if ($rows === false){
						$act_no_exsite = 1;
					} else {
						$registro_act = sqlsrv_fetch_array($resultado);
						$act_no_exsite = 0;						
					}

				}	

		    	
			} else {
				$registro_act = sqlsrv_fetch_array($resultado);
				$act_no_exsite = 0;
			}	

			//Si hemos encontrado la atuacion en inv_actuaciones
			if ($act_no_exsite == 0) {

				//Buscamos a ver si es una RA
				$tsql = "SELECT * 
						FROM INV_RA
						WHERE ID_ACTUACION = '".$registro_act['ID_ACTUACION']."'";
				//Recuperar datos de consulta
				$resultado = sqlsrv_query($conn, $tsql);

				//Si no se encuentra la RA
				$rows = sqlsrv_has_rows( $resultado );
				if ($rows === false){	
					
					//Buscamos a ver si es una RD
					$tsql = "SELECT * 
							FROM INV_RD
							WHERE	
								ID_ACTUACION = '".$registro_act['ID_ACTUACION']."'";
					//Recuperar datos de consulta
					$resultado = sqlsrv_query($conn, $tsql);
					
					$rows = sqlsrv_has_rows( $resultado );
					//Si no se encuentra la RD
					
					if ($rows === false){
						$act_no_exsite = 1;	
					
					} else {
						$tipo_actuacion = 'RD';
						$registro_rd = sqlsrv_fetch_array($resultado);

						//Buscamos su RA
						$tsql = "SELECT * 
								FROM INV_RA
								WHERE ID_FDTT = '".$registro_rd['ID_RA']."'";
						//Recuperar datos de consulta
						$resultado = sqlsrv_query($conn, $tsql);

						//Si no se encuentra la RA
						$rows = sqlsrv_has_rows( $resultado );
						//echo "consulta: ".$tsql;
						if ($rows === false){	
							$act_no_exsite = 1;
						} else {
							$registro_ra = sqlsrv_fetch_array($resultado);	
						}	

					}


				} else {
					
					$tipo_actuacion = 'RA';
					$registro_ra = sqlsrv_fetch_array($resultado);

				}

			//  SI NO HEMOS ENCONTRADO LA ACTUACIÓN EN CONCRETO
			//	Tanto para una RD, como para una RA, si introducimos la actuación JZZ sin lateral o sin cámara, debería listarnos todos los posibles resultados encontrados en el registro	
			} else {

				$tsql = "SELECT * 
						FROM inv_actuaciones
						WHERE	
							ACT_JAZZTEL LIKE '%".$id."%'";

				//Recuperar datos de consulta
				$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

			}

			sqlsrv_free_stmt($resultado);	


		}
	//No ha introducido todavía ninguna actuación para buscar 	
	} else {
		$primera_vez = 1;

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
				<li><a href="#">Carga Inventario</a></li>
			</ul>

			<!--FILTROS-->
			<FORM id="busqueda" autocomplete="off" METHOD="POST" NAME="opciones"  class="form-horizontal">
				<fieldset>
			    	<div class="row-fluid">

						<div class="span2">
						
							<div class="control-group">
								<label class="control-label" for="origenSol">Actuación: </label>
								<div class="controls">
									<input type="text" id="id" name="id" value="<?php echo $id;?>"/>
								</div>	
							</div>		

						 </div>	
						<div class="span2">
							<div class="control-group">
								<div class="controls">
									<INPUT TYPE="submit" class="btn btn-success" NAME="buscar" id = "buscar" VALUE="Buscar" onclick = "this.form.action = 'GIS_buscar_estructura.php'">  
								</div>	
							</div>		
						 </div>		
						<div class="span2">
							<div class="control-group">
								<div class="controls">

									<a href="scripts/excelExport.php?origen=DESCARGA_RA" title="Exportar RA" class="btn btn-success btn-mini">		
										<i class="halflings-icon white download"></i> EXPORTAR RA 
									</a>	

								</div>	
							</div>		
						 </div>					
						<div class="span2">
							<div class="control-group">
								<div class="controls">

									<a href="scripts/excelExport.php?origen=DESCARGA_RD" title="Exportar RD" class="btn btn-success btn-mini">		
										<i class="halflings-icon white download"></i> EXPORTAR RD 
									</a>	

								</div>	
							</div>		
						 </div>								 			 				 
					</div>
				</fieldset>	

			</FORM>       
			<!--FIN FILTROS-->  


            <!-- Tabla de listado de grupos existentes -->
            
            <?php if ($primera_vez == 0) { 
            	//Si ya ha introducido actuación a buscar, muestra esquema de datos
            	?>

            <div class="row-fluid sortable ui-sortable">		
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon user"></i><span class="break"></span>Listado</h2>
						<div class="box-icon">
							
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							
						</div>
					</div>
					<div class="box-content">
						<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">
						  <thead>
							  <tr>
								  <th>RA/RD</th>
								  <!-- <th>ID_GD</th> -->
								  <th>ID_FDTT</th>
								  <th>ACT_JAZZTEL</th>
								  <th>ACT_TESA</th>
								  <!-- <th>CODIGO_MIGA</th> -->
								  <th class="hidden">ID_ACTUACION</th>

								  <th>ACCION</th>
							  </tr>
						  </thead>   
						  <tbody>

						  <!-- Búsqueda de una actuación	 -->
                          <?php if (isset($registro_act)) { ?>
							<tr>
								<td class="center"><?php echo $tipo_actuacion; ?></td>
								<!-- <td class="center"><?php echo $registro_act['ID_GD']; ?></td> -->
								<td class="center"><?php echo $registro_act['ID_FDTT']; ?></td>
								<td class="center"><?php echo $registro_act['ACT_JAZZTEL']; ?></td>
								<td class="center"><?php echo $registro_act['ACT_TESA']; ?></td>
								<!-- <td class="center"><?php echo $registro_act['CODIGO_MIGA']; ?></td> -->
								<td class="hidden"><?php echo $registro_act['ID_ACTUACION']; ?></td>
								

								<td class="center">

									<a title="Detalle RA" class="btn btn-success btn-mini buscar_ra" data-toggle="modal" data-target="#viewModalRA" data-id="<?php echo $registro_ra['ID_ACTUACION']; ?>">		
										<i class="halflings-icon white eye-open"></i>  
									</a>


									<?php if ($tipo_actuacion == 'RD') {
										//Podemos consultar los datos de RD si la actuación es una RD
									?>
										<a title="Detalle RD" class="btn btn-success btn-mini buscar_rd" data-toggle="modal" data-target="#viewModalRD" data-id="<?php echo $registro_rd['ID_ACTUACION']; ?>">		
											<i class="halflings-icon white eye-open"></i>  
										</a>									
									<?php } ?>

									<a title="Tareas RA" class="btn btn-success btn-mini consultar_tareas_actuacion" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $registro_ra['ID_ACTUACION']; ?>">		
										<i class="halflings-icon white zoom-in"></i>  
									</a>

									<?php if ($tipo_actuacion == 'RD') {
										//Podemos consultar los datos de RD si la actuación es una RD
									?>

										<a title="Tareas RD" class="btn btn-success btn-mini consultar_tareas_actuacion" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $registro_rd['ID_ACTUACION']; ?>">		
											<i class="halflings-icon white zoom-in"></i>  
										</a>											
									<?php } ?>

								</td>
                            </tr>
                            <!-- Búsqueda de actuación parcial	 -->
							<?php 
								} else {
									if (isset($registros)) { while ($linea = sqlsrv_fetch_array($registros)){ ?>
										
										<?php
										//Vemos si es RA o RD

											$tsql2 = "SELECT * 
													FROM INV_RA
													WHERE ID_ACTUACION = '".$linea['ID_ACTUACION']."'";
											//Recuperar datos de consulta
											$resultado2 = sqlsrv_query($conn, $tsql2);

											//Si no se encuentra la RA
											$rows2 = sqlsrv_has_rows( $resultado2 );
											
											if ($rows2 === false){		

												//Buscamos a ver si es una RD
												$tsql2 = "SELECT * 
														FROM INV_RD
														WHERE ID_ACTUACION = '".$linea['ID_ACTUACION']."'";

												//Recuperar datos de consulta
												$resultado2 = sqlsrv_query($conn, $tsql2);
												
												$rows2 = sqlsrv_has_rows( $resultado2 );
												//Si no se encuentra la RD
												
												if ($rows2 === false){
													$act_no_exsite = 1;	
												
												} else {
													$tipo_actuacion = 'RD';
													$registro_rd = sqlsrv_fetch_array($resultado2);

													//Buscamos su RA
													$tsql2 = "SELECT * 
															FROM INV_RA
															WHERE ID_FDTT = '".$registro_rd['ID_RA']."'";
													//Recuperar datos de consulta
													$resultado2 = sqlsrv_query($conn, $tsql2);

													//Si no se encuentra la RA
													$rows2 = sqlsrv_has_rows( $resultado2 );
													//echo "consulta: ".$tsql;
													if ($rows2 === false){	
														$act_no_exsite = 1;
													} else {
														$registro_ra = sqlsrv_fetch_array($resultado2);	
													}	

												}

											} else {
												$tipo_actuacion = 'RA';
											}									

										 ?>	

										<tr>
											<td class="center"><?php echo $tipo_actuacion; ?></td>
											<!-- <td class="center"><?php echo $linea['ID_GD']; ?></td> -->
											<td class="center"><?php echo $linea['ID_FDTT']; ?></td>
											<td class="center"><?php echo $linea['ACT_JAZZTEL']; ?></td>
											<td class="center"><?php echo $linea['ACT_TESA']; ?></td>
											<!-- <td class="center"><?php echo $linea['CODIGO_MIGA']; ?></td> -->
											<td class="hidden"><?php echo $linea['ID_ACTUACION']; ?></td>
											

											<td class="center">
												<?php if ($tipo_actuacion == 'RD') { ?>

													<a title="Detalle RA" class="btn btn-success btn-mini buscar_ra" data-toggle="modal" data-target="#viewModalRA" data-id="<?php echo $registro_ra['ID_ACTUACION']; ?>">		
														<i class="halflings-icon white eye-open"></i>  
													</a>


													<a title="Detalle RD" class="btn btn-success btn-mini buscar_rd" data-toggle="modal" data-target="#viewModalRD" data-id="<?php echo $registro_rd['ID_ACTUACION']; ?>">		
														<i class="halflings-icon white eye-open"></i>  
													</a>									
													

													<a title="Tareas RA" class="btn btn-success btn-mini consultar_tareas_actuacion" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $registro_ra['ID_ACTUACION']; ?>">		
														<i class="halflings-icon white zoom-in"></i>  
													</a>

													<a title="Tareas RD" class="btn btn-success btn-mini consultar_tareas_actuacion" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $registro_rd['ID_ACTUACION']; ?>">		
														<i class="halflings-icon white zoom-in"></i>  
													</a>	

												<?php } else { ?>


													<a title="Detalle RA" class="btn btn-success btn-mini buscar_ra" data-toggle="modal" data-target="#viewModalRA" data-id="<?php echo $linea['ID_ACTUACION']; ?>">		
														<i class="halflings-icon white eye-open"></i>  
													</a>

													<a title="Tareas RA" class="btn btn-success btn-mini consultar_tareas_actuacion" data-toggle="modal" data-target="#viewModalT" data-id="<?php echo $linea['ID_ACTUACION']; ?>">		
														<i class="halflings-icon white zoom-in"></i>  
													</a>

												<?php } ?>										

											</td>
			                            </tr>


									<?php } } ?>
								<?php } ?>
						  </tbody>
					  </table>            
					</div>
				</div>
            
            </div>
			<?php } ?><!--primera vez-->

   

	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->


<div class="modal hide fade medio" id="viewModalRD">
	<div class="box-header" data-original-title>	
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar RD</h2>
	</div>
    <div class="ct">
  
    </div>
</div>	

<div class="clearfix"></div>

<div class="modal hide fade medio" id="viewModalRA">
	<div class="box-header" data-original-title>	
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar RA</h2>
	</div>
    <div class="ct">
  
    </div>
</div>	

<div class="clearfix"></div>

<div class="modal hide fade large" id="viewModalT" data-backdrop="static" data-keyboard="false" >
	<!-- <div class="modal-header btn-info"> -->
	<div class="box-header" data-original-title>	
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
