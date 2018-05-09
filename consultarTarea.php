<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";

    $id = $_REQUEST['id'];  

    $conn=conectar_bd();

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


	if ($registro['REF_ASOCIADA'] != '') {
		if ($registro['INCIDENCIA'] != '') {
			$tsql = $tsql."	WHERE (INV_TBTAREAS.REF_ASOCIADA = '".$registro['REF_ASOCIADA']."' OR INCIDENCIA = '".$registro['INCIDENCIA']."') 
					AND INV_TBTAREAS.ID <> '$id' 
					ORDER BY INV_TBTAREAS.FECHA_REGISTRO, INV_TBTAREAS.FECHA_INICIO, INV_TBTAREAS.FECHA_RESOL";
		} else {
			$tsql = $tsql."	WHERE INV_TBTAREAS.REF_ASOCIADA = '".$registro['REF_ASOCIADA']."' 
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

	//crear array de gescales bloqueados para asisgnarselos a cada tarea en su linea
	$tsql_gescales="SELECT INV_tbGESCALES.GESCAL AS GESCAL
				FROM INV_tbBloqueos_Gescales
				INNER JOIN INV_tbGESCALES ON INV_tbGESCALES.COD_GESCAL = INV_tbBloqueos_Gescales.COD_GESCAL
				WHERE INV_tbBloqueos_Gescales.ID = '$id'";
	
	$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

	$array_gescales = array();

	while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
		$array_gescales[] = $row_gescal;
	}	

	//crear array de gescales desbloqueados para asisgnarselos a cada tarea en su linea
	$tsql_gescales="SELECT INV_tbGESCALES.GESCAL AS GESCAL
				FROM INV_tbDesbloqueos_Gescales
				INNER JOIN INV_tbGESCALES ON INV_tbGESCALES.COD_GESCAL = INV_tbDesbloqueos_Gescales.COD_GESCAL
				WHERE INV_tbDesbloqueos_Gescales.ID = '$id'";
		
	$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

	while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
		$array_gescales[] = $row_gescal;
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
				<li><a href="#">Consultar</a></li>
			</ul>

			<!--FORMULARIO-->
			<form method="post" action="consultarTarea.php" role="form">
				<fieldset>    
				<!--DETALLE TAREA-->

				<!-- DATOS DE CABECERA DE LA TAREA -->
				<div style="padding-left:5px;" class="row-fluid yellow">
				
					<div class="control-group form-group span2">
						<div class="controls" style="padding-left:5px;">
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
							<strong>EEMM: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="REGION" value="<?php echo $registro['EEMM'];?>">
						</div>
					</div>						
				</div>

				<div style="padding-left:5px;">

				<!-- FIN DATOS DE CABECERA DE LA TAREA -->

				<!-- DATOS DEL DETALLE DE LA TAREA -->

				<div class="row-fluid" style="margin-bottom:10px;">

					<div class="box-header">

						<h2><i class="halflings-icon list"></i><span class="break"></span>Detalle tarea</h2><a style="margin-left:20px;" title="Historia Tarea" class="btn btn-mini btn-primary buscar_historia" data-toggle="modal" data-target="#historiaModal" data-id="<?php echo $id; ?>"><i class="halflings-icon white eye-open"></i></a>

					</div>					
					
				</div>


				<div class="row-fluid">
					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>ID: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="ID" value="<?php echo $id;?>">
							</div>
						</div>				
						<div class="control-group form-group">
							<div class="controls">
								<strong>ID_GD: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_GD" value="<?php echo $registro['ID_GD'];?>">
							</div>
						</div>	

						<div class="control-group form-group">
							<div class="controls">
								<strong>REF: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="REF" value="<?php echo $registro['REF'];?>">
							</div>
						</div>					
						<div class="control-group form-group">
							<div class="controls">
								<strong>REF. ASOCIADA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="REF_ASOCIADA" value="<?php echo $registro['REF_ASOCIADA'];?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>ESTADO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO" value="<?php echo $registro['Estado'];?>">
							</div>
						</div>		

						<div class="control-group form-group">
								<div class="controls">
								<strong>ID_MAPEO: </strong><br>
								
								<a title="Detalle datos ID_MAPEO" class="btn btn-mini buscar_mapeo" data-toggle="modal" data-target="#viewModalMP" data-id="<?php echo $registro['ID_MAPEO'];?>">		
									<i class="halflings-icon white eye-open"></i>  
								</a>
								<input readonly="true" type="text" class="form-control input uneditable-input" name="id_mapeo" value="<?php echo $registro['ID_MAPEO'];?>">
								</div>
							</div>	
							
						</div>

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>PRIORIDAD: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="PRIORIDAD" value="<?php echo $registro['PRIORIDAD'];?>">
							</div>
						</div>				
						<div class="control-group form-group">
							<div class="controls">
								<strong>ACTIVIDAD: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="ACTIVIDAD" value="<?php echo $registro['Actividad'];?>">
							</div>
						</div>					
						<div class="control-group form-group">
							<div class="controls">
								<strong>SUBACTIVIDAD: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="SUBACTIVIDAD" value="<?php echo $registro['SUBACTIVIDAD'];?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>TICKET REMEDY: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="INCIDENCIA" value="<?php echo $registro['INCIDENCIA'];?>">
							</div>
						</div>					
						
						<div class="control-group form-group">
							<div class="controls">
								<strong>ID_TIPO_ENTRADA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_TIPO_ENTRADA" value="<?php echo $registro['ID_TIPO_ENTRADA'];?>">
							</div>
						</div>	

						<div class="control-group form-group">
							<div class="controls">
								<strong>ESCALADO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="ESCALADO" value="<?php echo $registro['ESCALADO'];?>">
							</div>
						</div>	
                                               	

					</div>

					<div class="span2" ontablet="span4" ondesktop="span2">

						<div class="control-group form-group">
							<div class="controls">
								<strong>FECHA_INICIO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_INICIO" value="<?php echo  date_format($registro['FECHA_INICIO'], 'Y-m-d H:i:s'); ?>">
							</div>
						</div>				
						<div class="control-group form-group">
							<div class="controls">
								<strong>FECHA_RESOL: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_RESOL" value="<?php echo  date_format($registro['FECHA_RESOL'], 'Y-m-d H:i:s'); ?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>FECHA_REGISTRO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_REGISTRO" value="<?php echo  date_format($registro['FECHA_REGISTRO'], 'Y-m-d H:i:s'); ?>">
							</div>	
						</div>		
						<div class="control-group form-group">
							<div class="controls">
								<strong>TICKET_OCEANE: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="TICKET_OCEANE" value="<?php echo $registro['TICKET_OCEANE'];?>">
							</div>
						</div>		
						<div class="control-group form-group">
							<div class="controls">
									
									<strong>TIPOLOGIA_INICIAL: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="tipologia_inicial" value="<?php echo $registro['TIPOLOGIA_INICIAL'];?>">
									
							</div>
						</div>	
                                                <div class="control-group form-group">
							<div class="controls">
								<strong>SUC: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="SUC" value="<?php echo $registro['SUC'];?>">
							</div>
						</div>	
							
	
					</div>

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>USUORIGEN: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="USUORIGEN" value="<?php echo $registro['USUORIGEN'];?>">
							</div>
						</div>			
			
						<div class="control-group form-group">
							<div class="controls">
								<strong>GRUPO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="GRUPO" value="<?php echo $registro['GRUPO'];?>">
							</div>
						</div>					
						<div class="control-group form-group">
							<div class="controls">
								<strong>GRUPO_ESCALADO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="GRUPO_ESCALADO" value="<?php echo $registro['GRUPO_ESCALADO'];?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>TP: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="TP" value="<?php echo $registro['TP'];?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>TIPO_INCIDENCIA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="TP" value="<?php echo $registro['TIPO_INCIDENCIA'];?>">
							</div>
						</div>

						
									
				
					</div>

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls ctos">
								<strong>CTOS: </strong><br>
									<?php 
									$rows = sqlsrv_has_rows($ctosTarea );
									if ($rows === true){			
										
										while ($cto = sqlsrv_fetch_array($ctosTarea)){		
											echo '<input readonly="true" type="text" class="form-control input uneditable-input" value="'.$cto['NUMERO'].'">';
										}
									}
									?>													
							</div>
						</div>
					</div>	
					<div class="span2" ontablet="span4" ondesktop="span2">	
						<div class="control-group form-group">
							<div class="controls gescales">
								<strong>GESCALES: </strong><br>
									<?php
									    foreach($array_gescales as $key=>$data) {
									    		echo '<input readonly="true" type="text" class="form-control input uneditable-input" value="'.$data['GESCAL'].'">';
									    }										
									?>										
							</div>
						</div>					
					</div>									

				</div>
					
		    	<div class="row-fluid">
						<div class="control-group form-group span11">
							<div class="controls">
								<strong>COMENTARIOS: </strong><br><textarea readonly="true" class="form-control" name="COMENTARIOS"><?php echo $registro['COMENTARIOS'];?></textarea>
							</div>
						</div>
				</div>									


		    	<div class="row-fluid">
						<div class="control-group form-group span11">
							<div class="controls">
								<strong>COMENTARIOS2: </strong><br><textarea readonly="true" type="text" class="form-control" name="COMENTARIOS2"><?php echo $registro['COMENTARIOS2'];?></textarea>
							</div>
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
                                        
					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								
									<button type="button" name="back" value="back" onClick="history.go(-1);return true;" class="btn btn-primary btn-small back" style="vertical-align:bottom;"><i class="halflings-icon white repeat"></i> Volver</button>
								
							</div>	
						</div>	
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
  									  <th>ESCALADO</th>
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
										<td class="center"><?php echo $lineaAsoc['ESCALADO_TBTAREA']; ?></td>
										<td class="center"><?php echo $lineaAsoc['ACTIVIDAD']; ?></td>
										<td class="center"><?php echo $lineaAsoc['SUBACTIVIDAD']; ?></td>
										<td class="center"><?php echo $lineaAsoc['USUORIGEN']; ?></td>
										<td class="center"><?php echo $lineaAsoc['TECNICO']; ?></td>
										<td class="center"><?php echo date_format($lineaAsoc['FECHA_REGISTRO'], 'Y-m-d H:i:s'); ?></td>
										<td class="center"><?php echo date_format($lineaAsoc['FECHA_INICIO'], 'Y-m-d H:i:s'); ?></td>
										<td class="center"><?php echo date_format($lineaAsoc['FECHA_RESOL'], 'Y-m-d H:i:s'); ?></td>
										<td class="center"><?php echo $lineaAsoc['Estado']; ?></td>
										<td class="center"><?php echo $lineaAsoc['PRIORIDAD']; ?></td>
										<td class="center">
											<a title="Consular" class="btn btn-success btn-mini buscar_tarea" href="<?php echo 'consultarTarea.php?id='.$lineaAsoc['ID_TAREA']; ?>">		
												<i class="halflings-icon white zoom-in"></i>									
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
		
<div class="modal hide fade" id="viewModalMP" data-backdrop="static" data-keyboard="false" >
	<div class="modal-header btn-info">
		<button type="button" id="cerrarConsulta" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar</h2>
	</div>
    <div class="ct" style="height:80%;">
  
    </div>
</div>	

<div class="clearfix"></div>	

<div class="modal hide fade" id="historiaModal" data-backdrop="static" data-keyboard="false" >
	<div class="modal-header btn-info">
		<button type="button" id="cerrarConsulta" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar Historia</h2>
	</div>
    <div class="ct" style="height:80%;">
  
    </div>
</div>	

<div class="clearfix"></div>	
	
<?php
	print_theme_footer();
?>
