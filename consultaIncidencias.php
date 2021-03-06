<?php
	require "inc/funciones.inc";
    
    $id = 0;

    $idActuacion = $_REQUEST['id']; 
   	$dato = $_REQUEST['dato']; 
   	$id = $_REQUEST['idIncidencia']; 

    if ($dato == 'RA') {
    	$id_Subactividad = 58;	//Incidencias de Carga GIS de RD
    } else {
    	$id_Subactividad = 59;	//Incidencias de Carga GIS de RD
    }	   	

    $conn=conectar_bd();

    //Si la llamada se hace con un id_actuacion, se muestra el detalle de la primera incidencia de la actuación y se listan las demás
    //
    //Si la llamada se hace con un id_tarea, se muestra el detalle de esa tarea y se listan las demas incidencia con el mismo id_actuacion que tenga la tarea del tetalle
    if ($id == 0) {
        
                $incidenciasAbiertas = 0;
                $tsqlINCA = "SELECT COUNT(*) AS CONTADOR_A FROM INV_TBTAREAS WHERE id_actuacion='".$idActuacion."' AND id_Subactividad ='".$id_Subactividad."' AND idEst <> '4'";
                $stmtINCA = sqlsrv_query( $conn, $tsqlINCA) or die ("Error al ejecutar consulta: ".$tsqlINCA);
                $rowsINCA = sqlsrv_has_rows( $stmtINCA );
                if ($rowsINCA === true){
                    $rowINCA = sqlsrv_fetch_array($stmtINCA);
                     $incidenciasAbiertas=$rowINCA['CONTADOR_A'];
                }
                
                if ($incidenciasAbiertas>0){
                    $tsql = "SELECT TOP 1 *
				FROM INV_TBTAREAS
				LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID
				WHERE INV_TBTAREAS.id_actuacion='".$idActuacion."' AND INV_TBTAREAS.id_Subactividad ='".$id_Subactividad."'  AND idEst <> '4' 
                                ORDER BY INV_TBTAREAS.FECHA_REGISTRO DESC";
                } else {
                    $tsql = "SELECT TOP 1 *
				FROM INV_TBTAREAS
				LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID
				WHERE INV_TBTAREAS.id_actuacion='".$idActuacion."' AND INV_TBTAREAS.id_Subactividad ='".$id_Subactividad."'  AND idEst = '4' 
                                ORDER BY INV_TBTAREAS.FECHA_REGISTRO DESC"; 
                }
        
               /*
        
	 	$tsql = "SELECT TOP 1 *
				FROM INV_TBTAREAS
				LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID
				WHERE INV_TBTAREAS.id_actuacion='".$idActuacion."' AND INV_TBTAREAS.id_Subactividad ='".$id_Subactividad."'";*/
	} else {
	 	$tsql = "SELECT *
				FROM INV_TBTAREAS
				LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID
				WHERE ID = '$id'";
	}

	$resultado = sqlsrv_query($conn, $tsql);

	if( $resultado === false ) {
    	die( print_r( sqlsrv_errors(), true));
	} else {
		$registro = sqlsrv_fetch_array($resultado);
		$idActuacion = $registro['ID_ACTUACION'];
		$id = $registro['ID_TAREA'];
		$id_Subactividad = $registro['id_Subactividad'];
	}	

	sqlsrv_free_stmt($resultado);	

	$tsql = "SELECT *
			FROM INV_TBTAREAS
			LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID
			WHERE INV_TBTAREAS.id_actuacion='".$idActuacion."' AND INV_TBTAREAS.id_Subactividad ='".$id_Subactividad."' AND INV_TBTAREAS.ID <> '$id' 
					ORDER BY INV_TBTAREAS.FECHA_REGISTRO, INV_TBTAREAS.FECHA_INICIO, INV_TBTAREAS.FECHA_RESOL";


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
        
        function obtenerEstado($idEstado,$conn){
            
            $desEstado='';
            $tsql = "SELECT Estado FROM INV_tbEstados where id_Estado= '$idEstado'";
			
            $resultado = sqlsrv_query($conn, $tsql);

	    if( $resultado === false ) {
    	      die( print_r( sqlsrv_errors(), true));
	    } else {
		$reg = sqlsrv_fetch_array($resultado);
                $desEstado=$reg['Estado'];
            	
            }	

            sqlsrv_free_stmt($resultado);
            return ($desEstado);
            
           
            
        }

?>
<!DOCTYPE html>
<html lang="es">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title>Consulta detalle Tarea</title>
	<meta name="description" content=">Consulta detalle Tarea">
	<meta name="author" content="Eurocontrol">
	<meta name="keyword" content="">
	<!-- end: Meta -->
	
	<!-- start: Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- end: Mobile Specific -->
	
	<!-- start: CSS -->
	<link id="bootstrap-style" href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
	<link id="base-style" href="css/style.css" rel="stylesheet">
	<link id="base-style-responsive" href="css/style-responsive.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,latin-ext' rel='stylesheet' type='text/css'>
	<!-- end: CSS -->
	
	<link href="css/bootstrapValidator.min.css" rel="stylesheet"></link>

  	<link href="css/inventario.css" rel="stylesheet">		

	<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	  	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<link id="ie-style" href="css/ie.css" rel="stylesheet">
	<![endif]-->
	
	<!--[if IE 9]>
		<link id="ie9style" href="css/ie9.css" rel="stylesheet">
	<![endif]-->
		
	<!-- start: Favicon -->
	<link rel="shortcut icon" href="img/favicon.ico">
	<!-- end: Favicon -->
	
		
		
		
</head>
<body>
<form method="post" action="consultaIncidencias.php" role="form">
	<div class="modal-body-large">     
		<fieldset>    
		<!--DETALLE TAREA-->
		<div class="row-fluid yellow">
			
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
			<div class="row-fluid">
				<div class="span2" ontablet="span4" ondesktop="span2">
					<div class="control-group form-group">
						<div class="controls">
							<strong>ID: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="ID" value="<?php echo $id;?>">
						</div>
					</div>				
					<div class="control-group form-group">
						<div class="controls">
							<strong>ID_GD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_GD" value="<?php echo $registro['ID_GD'];?>">
						</div>
					</div>	

					<div class="control-group form-group">
						<div class="controls">
							<strong>REF: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="REF" value="<?php echo $registro['REF'];?>">
						</div>
					</div>					
					<div class="control-group form-group">
						<div class="controls">
							<strong>REF. ASOCIADA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="REF_ASOCIADA" value="<?php echo $registro['REF_ASOCIADA'];?>">
						</div>
					</div>	
					<div class="control-group form-group">
						<div class="controls">
                                                        <?php $estado=''; $estado=obtenerEstado($registro['idEst'],$conn);?>
							<strong>ESTADO: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO" value="<?php echo $estado;?>">
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
							<strong>ACTIVIDAD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACTIVIDAD" value="<?php echo $registro['ACTIVIDAD'];?>">
						</div>
					</div>					
					<div class="control-group form-group">
						<div class="controls">
							<strong>SUBACTIVIDAD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="SUBACTIVIDAD" value="<?php echo $registro['SUBACTIVIDAD'];?>">
						</div>
					</div>	
					<div class="control-group form-group">
						<div class="controls">
							<strong>TICKET REMEDY: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="INCIDENCIA" value="<?php echo $registro['INCIDENCIA'];?>">
						</div>
					</div>					
					
					<div class="control-group form-group">
						<div class="controls">
							<strong>ID_TIPO_ENTRADA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_TIPO_ENTRADA" value="<?php echo $registro['ID_TIPO_ENTRADA'];?>">
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
					<div class="control-group form-group">
						<div class="controls">
							<strong>TICKET_OCEANE: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="TICKET_OCEANE" value="<?php echo $registro['TICKET_OCEANE'];?>">
						</div>
					</div>						
					<div class="control-group form-group">
						<div class="controls">
							<strong>ID_MAPEO: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_MAPEO" value="<?php echo $registro['ID_MAPEO'];?>">
						</div>
					</div>	
				</div>

				<div class="span2" ontablet="span4" ondesktop="span2">
					<div class="control-group form-group">
						<div class="controls">
							<strong>USUORIGEN: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="USUORIGEN" value="<?php echo $registro['USUORIGEN'];?>">
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
							<strong>TP: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="TP" value="<?php echo $registro['TP'];?>">
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


	    	<div class="row-fluid">
					<div class="control-group form-group span11">
						<div class="controls">
							<strong>COMENTARIOS: </strong><textarea readonly="true" class="form-control" name="COMENTARIOS"><?php echo $registro['COMENTARIOS'];?></textarea>
						</div>
					</div>
			</div>									


	    	<div class="row-fluid">
					<div class="control-group form-group span11">
						<div class="controls">
							<strong>COMENTARIOS2: </strong><textarea readonly="true" type="text" class="form-control" name="COMENTARIOS2"><?php echo $registro['COMENTARIOS2'];?></textarea>
						</div>
					</div>	
			</div>											
			
			</div>	
			</fieldset>  
			<!--FIN DETALLE-->
	    
	    	<!--LISTADO DE TAREAS ASOCIADAS-->
	    	<div class="row-fluid asociadas">
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
										<INPUT TYPE="submit" class="btn btn-success btn-mini" value="Consultar" onclick = "this.form.action = 'consultaIncidencias.php?dato=&idIncidencia=<?php echo $lineaAsoc['ID_TAREA']; ?>'">

									</td>
									</a>
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
		</div>	
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	</div>
</form>
</body>
</html>
<!--maiteben: ejecuta la consulta sobre la ventana modal en la que está actualmente -->
<script type="text/JavaScript">
	$('form').submit(function (event) {
	    event.preventDefault();
	    var $form = $(this);
	    $.ajax({
	        url: this.action,
	        type: this.method,
	        contentType: this.enctype,
	        data: $(this).serialize(),
	        success: function (result) {
	            if (result.success) {
	                $('#viewModalT').modal('hide');
	                //Refresh
	                location.reload();
	            } else {
	                $('.ct').html(result);
	                bindForm();
	            }
	        }
	    });
	});
</script>
