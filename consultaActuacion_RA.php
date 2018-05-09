<?php
	require "inc/funciones.inc";
    $id = $_GET['id'];  

    $conn=conectar_bd();

 	$tsql = "SELECT CAB.Descripcion as CABECERA_RA,
 					RA.EECC_CARGA_RA AS EECC_CARGA_RA,
 					RA.FECHA_ENVIO AS FECHA_ENVIO_RA,
 					RA.FECHA_ENTREGA AS FECHA_ENTREGA_RA,
 					RA.EEMM AS EEMM_RA,
 					PROV.Descripcion AS PROVINCIA_RA,
 					ACT.ACT_JAZZTEL AS ACT_JAZTELL_RA,
 					ACT.ID_FDTT AS ACT_ID_FDTT,
 					RA.ARBOL AS ARBOL_RA,
 					RA.EECC_DISEÑO_RA AS EECC_DISENO_RA,
 					RA.EECC_CONSTRUCCION AS EECC_CONSTRUCCION_RA,
 					RA.RESPONSABLE_CARGA AS RESPONSABLE_CARGA_RA,
 					RA.ESTADO_RA_FDTT AS ESTADO_RA,
 					RA.ESTADO_CARGA_GIS AS ESTADO_GIS_RA
			FROM INV_RA AS RA
			LEFT JOIN inv_actuaciones AS ACT ON RA.ID_ACTUACION = ACT.ID_ACTUACION
			LEFT JOIN inv_cabeceras AS CAB ON RA.ID_CABECERA = CAB.Cod_Cabecera
			LEFT JOIN inv_provincias AS PROV ON CAB.Cod_Provincia = PROV.Cod_Provincia
			WHERE RA.ID_ACTUACION = '$id'";
			
	$resultado = sqlsrv_query($conn, $tsql);

	if( $resultado === false ) {
    	die( print_r( sqlsrv_errors(), true));
	} else {
		$registro = sqlsrv_fetch_array($resultado);
	}	
		
	sqlsrv_free_stmt($resultado);						
	

$tsql2 = "SELECT tbE.Estado AS ESTADO
			FROM INV_TBTAREAS as tareas 
			LEFT JOIN INV_tbEstados AS tbE ON tareas.idEst = tbE.id_Estado			
			WHERE tareas.id_Actuacion = '$id' and tareas.id_subactividad = 117";

			
			
	$resultado2 = sqlsrv_query($conn, $tsql2);

	if( $resultado2 === false ) {
    	die( print_r( sqlsrv_errors(), true));
	} else {
		$registro2 = sqlsrv_fetch_array($resultado2);
	}	
		
	sqlsrv_free_stmt($resultado);					





	sqlsrv_close($conn);	



  
?>
<!DOCTYPE html>
<html lang="es">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title>Consulta RA Actuación</title>
	<meta name="description" content=">Consulta RA-RD Actuación">
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
<form method="post" action="consultaActuacion_RA.php" role="form">
	<div class="modal-body-large span12">       
		
		<fieldset>    
	    
	    	<div class="row-fluid">
			<div class="span3" ontablet="span4" ondesktop="span2">
	            <div class="control-group form-group">
					<div class="controls">
						<strong>CABECERA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="CABECERA_RA" value="<?php echo $registro['CABECERA_RA'];?>">
					</div>
				</div>	

				<div class="control-group form-group">
					<div class="controls">
						<strong>PROVINCIA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="PROVINCIA_RA" value="<?php echo $registro['PROVINCIA_RA'];?>">
					</div>
				</div>	

<!-- 				<div class="control-group form-group">
					<div class="controls">
						<strong>ARBOL_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ARBOL_RA" value="<?php echo $registro['ARBOL_RA'];?>">
					</div>
				</div> -->	

				<div class="control-group form-group">
					<div class="controls">
						<strong>ACT_JAZTELL_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACT_JAZTELL_RA" value="<?php echo $registro['ACT_JAZTELL_RA'];?>">
					</div>
				</div>	


	            <div class="control-group form-group">
					<div class="controls">
						<strong>ACT_ID_FDTT: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACT_ID_FDTT" value="<?php echo $registro['ACT_ID_FDTT'];?>">
					</div>
				</div>	

																				


			</div>

			<div class="span3" ontablet="span4" ondesktop="span2">
				<div class="control-group form-group">
					<div class="controls">
						<strong>EECC_DISENO_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_DISENO_RA" value="<?php echo $registro['EECC_DISENO_RA'];?>">
					</div>
				</div>		
	            <div class="control-group form-group">
					<div class="controls">
						<strong>EECC_CONSTRUCCION_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_CONSTRUCCION_RA" value="<?php echo $registro['EECC_CONSTRUCCION_RA'];?>">
					</div>
				</div>	

				<div class="control-group form-group">
					<div class="controls">
						<strong>EECC_CARGA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_CARGA_RA" value="<?php echo $registro['EECC_CARGA_RA'];?>">
					</div>
				</div>	

				<div class="control-group form-group">
					<div class="controls">
						<strong>FECHA_ENVIO_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENVIO_RA" value="<?php echo date_format($registro['FECHA_ENVIO_RA'], 'd/m/Y');?>">
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<strong>FECHA_ENTREGA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENTREGA_RA" value="<?php echo date_format($registro['FECHA_ENTREGA_RA'], 'd/m/Y');?>">
					</div>
				</div>	



			</div>				

			<div class="span3" ontablet="span4" ondesktop="span2">
												
	            <div class="control-group form-group">
					<div class="controls">
						<strong>RESPONSABLE_CARGA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="RESPONSABLE_CARGA_RA" value="<?php echo $registro['RESPONSABLE_CARGA_RA'];?>">
					</div>
				</div>	
				
				<div class="control-group form-group">
					<div class="controls">
						<strong>ESTADO_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO_RA" value="<?php echo $registro['ESTADO_RA'];?>">
					</div>
				</div>		
				
				<div class="control-group form-group">
					<div class="controls">
						<strong>ESTADO_GIS_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO_GIS_RA" value="<?php echo $registro['ESTADO_GIS_RA'];?>">
					</div>
				</div>	

				<div class="control-group form-group">
					<div class="controls">
						<strong>EEMM: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="EEMM_RA" value="<?php echo $registro['EEMM_RA'];?>">
					</div>
				</div>	

				<div class="control-group form-group">
					<div class="controls">
						<strong>ESTADO AUDITORIA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO_AUDITORIA" value="<?php echo $registro2['ESTADO'];?>">
					</div>
				</div>	
	

			</div>


		</fieldset>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	</div>
</form>
</body>
</html>
