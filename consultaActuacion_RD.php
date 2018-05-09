<?php
	require "inc/funciones.inc";
    $id = $_GET['id'];  

    $conn=conectar_bd();

	$tsql = "SELECT RD.ID_ZONA AS ID_ZONA_RD,
					REG.Descripcion AS REGION_RD,
					RD.EECC_CARGA_RD AS EECC_CARGA_RD,
					RD.FECHA_ENVIO AS FECHA_ENVIO_RD,
					RD.FECHA_ENTREGA AS FECHA_ENTREGA_RD,
					ACT.ACT_TESA AS ACT_TESA_RD,
					CAB.Descripcion AS CABECERA_RA,
					RD.EEMM AS EEMM_RD,
					RD.ESTADO_GIS AS ESTADO_GIS_RD,
					RD.ACTUACION_JAZZTEL_FDTT AS ACTUACION_JAZZTEL_FDTT,
					ACT.ID_FDTT AS ACT_ID_FDTT_RD,
					RD.GESTOR AS GESTOR_RD,
					RD.EECC_DIS_RD AS EECC_DIS_RD,
					RD.EECC_CONS_RD AS EECC_CONS_RD,
					RA.ARBOL AS ARBOL_RA,
					RD.FASE AS FASE_RD
			FROM INV_RD AS RD
			LEFT JOIN INV_RA AS RA ON RA.ID_FDTT = RD.ID_RA
			LEFT JOIN inv_actuaciones AS ACT ON RD.ID_ACTUACION = ACT.ID_ACTUACION
			LEFT JOIN inv_cabeceras AS CAB ON RA.ID_CABECERA = CAB.Cod_Cabecera
			LEFT JOIN inv_provincias AS PROV ON CAB.Cod_Provincia = PROV.Cod_Provincia
			LEFT JOIN inv_regiones AS REG ON PROV.Cod_Region = REG.Cod_Region
			WHERE RD.ID_ACTUACION = '$id'";
	
	$resultado = sqlsrv_query($conn, $tsql);

	if( $resultado === false ) {
    	die( print_r( sqlsrv_errors(), true));
	} else {
		$registro = sqlsrv_fetch_array($resultado);
	}	
		
	sqlsrv_free_stmt($resultado);						
	sqlsrv_close($conn);	
  
?>
<!DOCTYPE html>
<html lang="es">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title>Consulta RD Actuación</title>
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
						<strong>ACT_ID_FDTT_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACT_ID_FDTT_RD" value="<?php echo $registro['ACT_ID_FDTT_RD'];?>">
					</div>
				</div>	

				<div class="control-group form-group">
					<div class="controls">
						<strong>ACTUACION_JAZZTEL_FDTT: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACTUACION_JAZZTEL_FDTT" value="<?php echo $registro['ACTUACION_JAZZTEL_FDTT'];?>">
					</div>
				</div>		
				
				<div class="control-group form-group">
					<div class="controls">
						<strong>ACT_TESA_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACT_TESA_RD" value="<?php echo $registro['ACT_TESA_RD'];?>">
					</div>
				</div>		

	            <div class="control-group form-group">
					<div class="controls">
						<strong>ID_ZONA_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_ZONA_RD" value="<?php echo $registro['ID_ZONA_RD'];?>">
					</div>
				</div>		
				
				<div class="control-group form-group">
					<div class="controls">
						<strong>REGION_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="REGION_RD" value="<?php echo $registro['REGION_RD'];?>">
					</div>
				</div>								


		
			</div>	

			<div class="span3" ontablet="span4" ondesktop="span2">
	
				<div class="control-group form-group">
					<div class="controls">
						<strong>CABECERA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="CABECERA_RA" value="<?php echo $registro['CABECERA_RA'];?>">
					</div>
				</div>		

				<div class="control-group form-group">
					<div class="controls">
						<strong>ARBOL_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ARBOL_RA" value="<?php echo $registro['ARBOL_RA'];?>">
					</div>
				</div>	

	            <div class="control-group form-group">
					<div class="controls">
						<strong>FASE_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FASE_RD" value="<?php echo $registro['FASE_RD'];?>">
					</div>
				</div>	
													
				<div class="control-group form-group">
					<div class="controls">
						<strong>EEMM: </strong><br/><input readonly="true" type="text" class="form-control input uneditable-input" name="EEMM_RD" value="<?php echo $registro['EEMM_RD'];?>">
					</div>
				</div>		
				<div class="control-group form-group">
					<div class="controls">
						<strong>ESTADO_GIS_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO_GIS_RD" value="<?php echo $registro['ESTADO_GIS_RD'];?>">
					</div>
				</div>	
													
			</div>

			<div class="span3" ontablet="span4" ondesktop="span2">

				<div class="control-group form-group">
					<div class="controls">
						<strong>GESTOR_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="GESTOR_RD" value="<?php echo $registro['GESTOR_RD'];?>">
					</div>
				</div>	
				<div class="control-group form-group">
					<div class="controls">
						<strong>EECC_DIS_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_DIS_RD" value="<?php echo $registro['EECC_DIS_RD'];?>">
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<strong>EECC_CONS_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_CONS_RD" value="<?php echo $registro['EECC_CONS_RD'];?>">
					</div>
				</div>	
				

				<div class="control-group form-group">
					<div class="controls">
						<strong>EECC_CARGA_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_CARGA_RD" value="<?php echo $registro['EECC_CARGA_RD'];?>">
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<strong>FECHA_ENVIO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENVIO_RD" value="<?php echo date_format($registro['FECHA_ENVIO_RD'], 'd/m/Y');?>">
					</div>
				</div>	
				<div class="control-group form-group">
					<div class="controls">
						<strong>FECHA_ENTREGA_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENTREGA_RD" value="<?php echo date_format($registro['FECHA_ENTREGA_RD'], 'd/m/Y');?>">
					</div>
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
