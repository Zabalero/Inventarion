<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
	require "inc/funcionesCambiarEstado.inc";
	require "inc/funcionesModificar.inc";
      
	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	$id = $_REQUEST['id']; 

	$mensaje = "";

	// Si ya hemos introducido valores para gestionar la tarea
    if($_SERVER['REQUEST_METHOD']=='POST') {  

    	//Funcion cambio de estado de la tarea
      	if (isset($_POST['cambiarEstado'])){
        	$mensaje = cambiarEstado($conn, $id);
        }
		
		//Funcion modificar la tarea
		if (isset($_REQUEST['modificar'])){
			$mensaje = modificarTarea($conn, $id);
		}

		//Funcion subir archivo de la tarea
		if (isset($_REQUEST['subirArchivo'])){
			$mensaje = subirArchivo($conn, $id);
		}		
 
    }

	if ($id != '') {
		
	 	$tsql = "SELECT *
				FROM INV_TBTAREAS
				LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID
				WHERE ID = '$id'";

		$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
	    	die( print_r( sqlsrv_errors(), true));
		} else {
			$registro = sqlsrv_fetch_array($resultado);
		}	

		sqlsrv_free_stmt($resultado);	  

		$tsql = "SELECT *
				FROM INV_TBTAREAS
				LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID";


		if ($registro['REF'] != '') {
			if ($registro['INCIDENCIA'] != '') {
				$tsql = $tsql."	WHERE (REF = '".$registro['REF']."' OR INCIDENCIA = '".$registro['INCIDENCIA']."') 
						AND INV_TBTAREAS.ID <> '$id' 
						ORDER BY INV_TBTAREAS.FECHA_REGISTRO, INV_TBTAREAS.FECHA_INICIO, INV_TBTAREAS.FECHA_RESOL";
			} else {
				$tsql = $tsql."	WHERE REF = '".$registro['REF']."' 
						AND INV_TBTAREAS.ID <> '$id' 
						ORDER BY INV_TBTAREAS.FECHA_REGISTRO, INV_TBTAREAS.FECHA_INICIO, INV_TBTAREAS.FECHA_RESOL";
			}
		} else {
			if ($registro['INCIDENCIA'] != '') {
				$tsql = $tsql."	WHERE INCIDENCIA = '".$registro['INCIDENCIA']."' 
						AND INV_TBTAREAS.ID <> '$id' 
						ORDER BY INV_TBTAREAS.FECHA_REGISTRO, INV_TBTAREAS.FECHA_INICIO, INV_TBTAREAS.FECHA_RESOL";			
			} else {
				$tsql = $tsql."	WHERE INV_TBTAREAS.ID <> '$id' 
						ORDER BY INV_TBTAREAS.FECHA_REGISTRO, INV_TBTAREAS.FECHA_INICIO, INV_TBTAREAS.FECHA_RESOL";					
			}
		}

		$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
	    	die( print_r( sqlsrv_errors(), true));
		}	

	} else {
		die( print_r('TAREA NO INFORMADA', true));
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
				<li><a href="#">Gestionar</a></li>
			</ul>

			<!--FORMULARIO-->
			<form method="post" action="gestionarTarea.php" role="form" enctype="multipart/form-data">
				<fieldset>    
				<!--DETALLE TAREA-->
				<div style="padding-left:5px;" class="row-fluid yellow">
					
					<div class="control-group form-group span2">
						<div class="controls">
							<input class="hidden" name="ID_REGION" value="<?php echo $registro['ID_REGION'];?>">
							<strong>REGION: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="REGION" value="<?php echo $registro['REGION'];?>">
						</div>
					</div>				
					<div class="control-group form-group span2">
						<div class="controls">
							<strong>PROVINCIA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="PROVINCIA" value="<?php echo $registro['PROVINCIA'];?>">
						</div>
					</div>	
					<div class="control-group form-group span2">
						<div class="controls">
							<input class="hidden" name="COD_CABECERA" value="<?php echo $registro['cod_cabecera'];?>">
							<strong>CABECERA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="CABECERA" value="<?php echo $registro['CABECERA'];?>">
						</div>
					</div>
					<div class="control-group form-group span2">
						<div class="controls">
							<strong>ARBOL: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ARBOL" value="<?php echo $registro['ARBOL'];?>">
						</div>
					</div>	
					<div class="control-group form-group span2">
						<div class="controls">
							<strong>ID_ACTUACION: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_ACTUACION" value="<?php echo $registro['ID_ACTUACION'];?>">
						</div>
					</div>
					<div class="control-group form-group span2">
						<div class="controls">
							<strong>GESTOR: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="GESTOR" value="<?php echo $registro['GESTOR'];?>">
						</div>
					</div>					
				</div>

				<div class="row-fluid">
					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>ID: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="id" value="<?php echo $id;?>">
							</div>
						</div>				
						<div class="control-group form-group">
							<div class="controls">
								<strong>ID_GD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_GD" value="<?php echo $registro['ID_GD'];?>">
							</div>
						</div>	

						<div class="control-group form-group">
							<div class="controls">
								<strong>REF: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="REF" value="<?php echo $registro['REF'];?>">
							</div>
						</div>					
						<div class="control-group form-group">
							<div class="controls">
								<strong>INCIDENCIA: </strong><input type="text" class="form-control input" name="INCIDENCIA" value="<?php echo $registro['INCIDENCIA'];?>">
							</div>
						</div>

					</div>

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>PRIORIDAD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="PRIORIDAD" value="<?php echo $registro['PRIORIDAD'];?>">
							</div>
						</div>				

						<div class="control-group form-group">
							<div class="controls">
								<input class="hidden" name="ID_ACTIVIDAD" value="<?php echo $registro['id_actividad'];?>">
								<strong>ACTIVIDAD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACTIVIDAD" value="<?php echo $registro['ACTIVIDAD'];?>">
							</div>
						</div>					
						<div class="control-group form-group">
							<div class="controls">
								<input class="hidden" name="ID_SUBACTIVIDAD" value="<?php echo $registro['id_Subactividad'];?>">
								<strong>SUBACTIVIDAD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="SUBACTIVIDAD" value="<?php echo $registro['SUBACTIVIDAD'];?>">
							</div>
						</div>		
						<div class="control-group form-group">
							<div class="controls">
								<strong>ESTADO: <br /></strong>
								<?php

									//id del estado de la tarea
									$tsql="select id_estado from inv_tbestados where estado='".$registro['ESTADO']."'";
									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta: ".$tsql);}

									$id_estado_Tarea = sqlsrv_fetch_array($stmt);

									//tabla y array de estados de estados
									$tsql="select id_estado, estado from inv_tbestados order by id_estado";
									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta: ".$tsql);}

									//array de estados

									$array_estados = array();

									while($row_estados = sqlsrv_fetch_array($stmt)){
										$array_estados[$row_estados['id_estado']] = $row_estados['estado'];
									}

									//tabla de estados posibles desde el estado actual
									
									$tsql="select DISTINCT id_estado_fin
											from INV_tbMotor_estados
											where id_estado_ini = '".$id_estado_Tarea['id_estado']."'";

									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta: ".$tsql);}

									echo '<SELECT tabindex="2" id="ESTADO"  name="ESTADO" >';	

									echo '<option class="form-control input" value="'.$id_estado_Tarea['id_estado'].'-'.$registro['ESTADO'].'" selected="selected" >'.$registro['ESTADO'].'</option>';

									while($row = sqlsrv_fetch_array($stmt)){
										
										echo '<option class="form-control input"  value="'.$row['id_estado_fin'].'-'.$array_estados[$row['id_estado_fin']].'" >'.$array_estados[$row['id_estado_fin']].'</option>';

									}
									
									echo '</SELECT>';			
								?>
							</div>	
						</div>												

					</div>

					<div class="span2" ontablet="span4" ondesktop="span2">

						<div class="control-group form-group">
							<div class="controls">
								<strong>FECHA_INICIO: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_INICIO" value="<?php echo  date_format($registro['FECHA_INICIO'], 'Y-m-d H:i:s'); ?>">
							</div>
						</div>				
						<div class="control-group form-group">
							<div class="controls">
								<strong>FECHA_RESOL: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_RESOL" value="<?php echo  date_format($registro['FECHA_RESOL'], 'Y-m-d H:i:s'); ?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>FECHA_REGISTRO: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_REGISTRO" value="<?php echo  date_format($registro['FECHA_REGISTRO'], 'Y-m-d H:i:s'); ?>">
							</div>	
						</div>														

					</div>

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>USUORIGEN: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="USUORIGEN" value="<?php echo $registro['USUORIGEN'];?>">
								<?php echo '<input readonly="true" type="text" class="form-control input uneditable-input hidden" name="ID_USUORIGEN" value="'.get_idFromNombre($registro['USUORIGEN']).'">';?>
							</div>
						</div>			
			
						<div class="control-group form-group">
							<div class="controls">
								<strong>GRUPO: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="GRUPO" value="<?php echo $registro['GRUPO'];?>">
							</div>
						</div>					
						<div class="control-group form-group">
							<div class="controls">
								<strong>GRUPO_ESCALADO: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="GRUPO_ESCALADO" value="<?php echo $registro['GRUPO_ESCALADO'];?>">
							</div>
						</div>				
						<div class="control-group form-group">
							<div class="controls">
								<strong>TÉCNICO: </strong><?php echo '<input readonly="true" type="text" class="form-control input uneditable-input" name="TECNICO" value="'.(($registro['TECNICO'] == '')?get_nombre($_SESSION['usuario']):$registro['TECNICO']).'">';?>
							</div>
						</div>			
						<div class="control-group form-group">
							<div class="controls">
								<?php echo '<input readonly="true" type="text" class="form-control input uneditable-input hidden" name="ID_TECNICO" value="'.(($registro['TECNICO'] == '')?get_idUsu($_SESSION['usuario']):get_idFromNombre($registro['TECNICO'])).'">';?>
							</div>
						</div>									
					</div>
					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>TICKET_OCEANE: </strong><input type="text" class="form-control input" name="TICKET_OCEANE" value="<?php echo $registro['TICKET_OCEANE'];?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>TP: </strong><input type="text" class="form-control input" name="TP" value="<?php echo $registro['TP'];?>">
							</div>
						</div>
						<div class="control-group form-group">
							<div class="controls">
								<strong>ID_MAPEO: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_MAPEO" value="<?php echo $registro['ID_MAPEO'];?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<input class="hidden" name="ID_EEMM" value="<?php echo $registro['ID_EEMM'];?>">
								<strong>ID_TIPO_ENTRADA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_TIPO_ENTRADA" value="<?php echo $registro['ID_TIPO_ENTRADA'];?>">
							</div>
						</div>					
					</div>
					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>CTOS: </strong><br>
									<?php
									    //CTOS DE LA ACTUACION
									 	$tsql = "SELECT  INV_CTOS.NUMERO, INV_CTOS.COD_CTO
													FROM INV_TBTAREAS
														INNER JOIN INV_CTOS ON INV_CTOS.id_Actuacion = INV_TBTAREAS.ID_ACTUACION
													WHERE INV_TBTAREAS.ID = '$id'";

										$ctosActuacion = sqlsrv_query($conn, $tsql);

										if( $ctosActuacion === false ) {
									    	die( print_r( sqlsrv_errors(), true));
										}

										//CTOS DE LA TAREA
										$tsql = "SELECT INV_CTOS.NUMERO
													FROM INV_TBTAREAS
														INNER JOIN INV_TBTAREAS_CTO ON INV_TBTAREAS_CTO.ID = INV_TBTAREAS.id
														INNER JOIN INV_CTOS ON INV_CTOS.COD_CTO = INV_TBTAREAS_CTO.COD_CTO
													WHERE INV_TBTAREAS.ID = '$id'";

										$ctosTarea = sqlsrv_query($conn, $tsql);

										if( $resultado === false ) {
									    	die( print_r( sqlsrv_errors(), true));
										}	

										$arrayCtosTarea = array();

										while(($row =  sqlsrv_fetch_array($ctosTarea))) {
										    $arrayCtosTarea[] = $row['NUMERO'];
										}

										$rows = sqlsrv_has_rows($ctosActuacion );
															
										if ($rows === true){

											while ($cto = sqlsrv_fetch_array($ctosActuacion)){
												echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "'".((in_array($cto['NUMERO'], $arrayCtosTarea))?' checked':'')."> ".$cto['NUMERO']."<br> ";
											}
										}	

	  								?>
							</div>
						</div>						
					</div>					
				</div>	
				<div class="row-fluid">

					<div class="form-group span5">
							<strong >COMENTARIOS: </strong>
							<textarea rows="5" name="COMENTARIOS" class="form-control" name="COMENTARIOS"><?php echo $registro['COMENTARIOS'];?></textarea>
					
					</div>
					<div class="form-group span5">
							<strong>COMENTARIOS2: </strong>
							<textarea  rows="5" name="COMENTARIOS2" class="form-control" name="COMENTARIOS2"><?php echo $registro['COMENTARIOS2'];?></textarea>
					
					</div>
				</div>	

				<div class="row-fluid">
					<div class="form-group span1">
							<?php

								$tsqlArchivo="SELECT archivo from INV_TBARCHIVOS WHERE idTarea='".$id."'";
						
								$stmtArchivo = sqlsrv_query( $conn, $tsqlArchivo);

								while($rowArchivo = sqlsrv_fetch_array($stmtArchivo)){	
									$nombreArchivo= $rowArchivo["archivo"]; 
								}
								sqlsrv_free_stmt( $stmtArchivo);

						
								if (isset($nombreArchivo)){
									echo "<a class='btn btn-small' href='upload/".$nombreArchivo."'><i class='halflings-icon white paperclip'></i><span>  Archivo Anexo</span></a>";									
								}				
							?>
					</div>

					<div class="form-group span2">
						<div class="control-group form-group">	
							<div class="controls">
								<input type="file" name="adjunto" id="adjunto" />
							</div>	
						</div>	
					</div>		

					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								<button onclick="return confirmarSubir();" type="submit" name="subirArchivo" value="subirArchivo" class="btn btn-info btn-small subirArchivo"><i class="halflings-icon white upload"></i> Subir Archivo</button>
							</div>	
						</div>	
					</div>		

					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								<button type="submit" name="cambiarEstado" value="cambiarEstado" class="btn btn-danger btn-small cambiarEstado" onclick="return confirmarAccion();"><i class="halflings-icon white play"></i> Cambiar Estado</button>
							</div>	
						</div>	
					</div>			

					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								<button type="submit" name="modificar" value="modificar" class="btn btn-danger btn-small modificar" onclick="return confirmarAccion();"><i class="halflings-icon white play"></i> Modificar</button>
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
				<!--FIN DETALLE-->
		    
		    	<!--LISTADO DE TAREAS ASOCIADAS-->
		    	<div class="row-fluid">
					<div class="box span12">
						<div class="box-header" data-original-title>
							<h2><i class="halflings-icon user"></i><span class="break"></span>Listado Tareas Asociadas</h2>
						</div> 	

						<div class="box-content">
							<table class="table table-striped table-bordered bootstrap-datatable datatable">
							  <thead>
								  <tr>
								  	  <th>ID_TAREA</th>
									  <th>REMEDY</th>
									  <th>REFERENCIA</th>
									  <th>OCEAN</th>
		                              <th>ACTIVIDAD</th>
									  <th>SUBACTIVIDAD</th>
									  <th>SOLICITANTE</th>
									  <th>TÉCNICO</th>
									  <th>F.REGIS.</th>
									  <th>F.INIC.</th>
									  <th>F.RESOL.</th>
									  <th>ESTADO</th>		
									  <th>P</th>
									  <th>TRANSACCIÓN</th>
								  </tr>
							  </thead>   
							  <tbody>

		                         <?php while ($lineaAsoc = sqlsrv_fetch_array($resultado)){ ?>
										<td class="center"><?php echo $lineaAsoc['ID_TAREA']; ?></td>
										<td class="center"><?php echo $lineaAsoc['REMEDY']; ?></td>
										<td class="center"><?php echo $lineaAsoc['REF_TBTAREA']; ?></td>
										<td class="center"><?php echo $lineaAsoc['OCEANE_TBTAREA']; ?></td>
										<td class="center"><?php echo $lineaAsoc['ACTIVIDAD']; ?></td>
										<td class="center"><?php echo $lineaAsoc['SUBACTIVIDAD']; ?></td>
										<td class="center"><?php echo $lineaAsoc['USUORIGEN']; ?></td>
										<td class="center"><?php echo $lineaAsoc['TECNICO']; ?></td>
										<td class="center"><?php echo date_format($lineaAsoc['FECHA_REGISTRO'], 'Y-m-d H:i:s'); ?></td>
										<td class="center"><?php echo date_format($lineaAsoc['FECHA_INICIO'], 'Y-m-d H:i:s'); ?></td>
										<td class="center"><?php echo date_format($lineaAsoc['FECHA_RESOL'], 'Y-m-d H:i:s'); ?></td>
										<td class="center"><?php echo $lineaAsoc['ESTADO']; ?></td>
										<td class="center"><?php echo $lineaAsoc['PRIORIDAD']; ?></td>
										<td class="center">
											<a title="Gestionar" class="btn btn-danger btn-mini gestionar_tarea" href="<?php echo 'gestionarTarea.php?id='.$lineaAsoc['ID_TAREA']; ?>">		
												<i class="halflings-icon white edit"></i> 										
											</a>
										</td>										
		                            </tr>
									<?php }
										sqlsrv_free_stmt($resultado);							
										sqlsrv_close($conn);	
									?>

							  </tbody>
							</table>
						</div>	
					</div>	
				</div>
			</form>    
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