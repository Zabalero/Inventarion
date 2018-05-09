<?php
	require "inc/funciones.inc";
    $id = $_GET['id'];  

    $conn=conectar_bd();

 	$tsql = "SELECT TOP 1 RA.ID_GD AS ID_GD_RA, RA.ID_FDTT AS ID_FDTT_RA, RA.EECC_CARGA_RA_DISEÑO AS EECC_CARGA_DIS_RA,
 				RA.Fx_ENVIO_RA_DISEÑO AS FECHA_ENVIO_DIS_RA, RA.Fx_ENTREGA_RA_DISEÑO AS FECHA_ENTREGA_DIS_RA,
 				RA.EECC_CARGA_RA AS EECC_CARGA_RA, V.CABECERA AS CABECERA_RA,
				RA.FECHA_ENVIO AS FECHA_ENVIO_RA, RA.FECHA_ENTREGA AS FECHA_ENTREGA_RA, V.PROVINCIA AS PROVINCIA_RA,
				RA.FX_AUDITADO_INF AS FX_AUDITORIA_RA,
				RA.FX_REV_AUDITADO AS FX_REV_AUDITORIA_RA, RA.AUDITADO_POR AS AUDITADO_POR_RA, RA.OBSERVACIONES_AUDITORIA AS OBS_AUDITORIA_RA,
				V.ACTUACION_JAZZTEL AS ACT_JAZTELL_RA, V.ID_FDTT AS ACT_ID_FDTT, RA.ESTADO_RA_FDTT AS ESTADO_RA_FDTT, RA.REPAROS AS REPAROS_RA,
				RA.N_AUDITORIAS AS N_AUDITORIAS_RA, V.ARBOL AS ARBOL_RA, RA.PL4 AS ESTADO_AB_PL4_RA, RA.SUC AS SUC_RA, RA.CARTAS_EMPALME AS CE_RA,
				RA.AB_RA_GIS AS AB_RA_GIS, RA.EECC_DISEÑO_RA AS EECC_DISENO_RA, RA.EECC_CONSTRUCCION AS EECC_CONSTRUCCION_RA,
				RA.FX_INICIO_CONSTRUCCION AS FECHA_INI_OBRA_RA, RA.FX_FIN_CONSTRUCCION AS FECHA_FIN_OBRA_RA,
				RA.SEGUIMIENTO_RA AS SEGUIMIENTO_RA, RA.RESPONSABLE_CARGA AS RESPONSABLE_CARGA_RA, RA.ESTADO_CARGA_GIS AS ESTADO_GIS_RA,
				RA.ESTADO_RA_FDTT AS ESTADO_RA, RD.ID_GD AS ID_GD_RD, RD.ID_FDTT AS ID_FDTT_RD, RD.EECC_CARGA_RD_DISENO AS EECC_CARGA_RD_DISENO,
				RD.DIA_ENVIO_DISENO AS DIA_ENVIO_DISENO_RD, RD.DIA_ENTREGA_DISENO AS DIA_ENTREGA_DISENO_RD, RD.ID_ZONA AS ID_ZONA_RD, V.REGION AS REGION_RD,
				RD.EECC_CARGA_RD AS EECC_CARGA_RD, RD.FECHA_ENVIO AS FECHA_ENVIO_RD, RD.FECHA_ENTREGA AS FECHA_ENTREGA_RD, RD.ESTADO_AB_PL4 AS ESTADO_AB_PL4_RD,
				V.ACTUACION_TESA AS ACT_TESA_RD, RD.AUDITADO_FECHA AS AUDITADO_FECHA_RD, RD.AUDITADO_POR AS AUDITADO_POR_RD, RD.REPAROS AS REPAROS_RD,
				RD.OBSERVACIONES_AUDITORIA AS OBSERVACIONES_AUDITORIA_RD, V.ACTUACION_JAZZTEL AS ACT_JAZZTEL_RD, V.ID_FDTT AS ACT_ID_FDTT_RD, RD.GESTOR AS GESTOR_RD,
				RD.EECC_DIS_RD AS EECC_DIS_RD, RD.EECC_CONS_RD AS EECC_CONS_RD, RD.Fx_fin_CONS_RD AS Fx_fin_CONS_RD, RD.FASE AS FASE_RD, RD.UUII_AI AS UUII_AI_RD,
				RD.FIR AS FIR_RD, RD.INC AS INC_RD, RD.AS_BUILT_ARBOL AS AS_BUILT_ARBOL_RD, RD.AIE2E AS AIE2E_RD, RD.Fx_ENTRADA_PL_CAMPO AS Fx_ENTRADA_PL_CAMPO_RD,
				RD.Fx_ACTUALIZACION_PL_CAMPO AS Fx_ACTUALIZACION_PL_CAMPO_RD, RD.Observaciones_PLANO_CAMPO AS Observaciones_PLANO_CAMPO_RD,
				RD.FX_INTERCAMBIO AS FX_INTERCAMBIO_RD, RD.BLOQUEADO AS BLOQUEADO_RD, RD.Fx_BLOQUEADO AS Fx_BLOQUEADO_RD, RD.Fx_SOL_BLOQUEO AS Fx_SOL_BLOQUEO_RD,
				RD.OBS_BLOQUEO AS OBS_BLOQUEO_RD, RD.ESTADO_GIS AS ESTADO_GIS_RD, RA.EEMM AS EEMM_RA, RD.EEMM AS EEMM_RD
			FROM INV_VIEW_RD_TODO AS V
			LEFT JOIN INV_RD AS RD ON V.ID_ACTUACION = RD.ID_ACTUACION
			LEFT JOIN INV_RA AS RA ON RD.ID_RA = RA.ID
			LEFT JOIN INV_CABECERAS AS CAB ON V.CABECERA = CAB.Descripcion
			WHERE V.ID_ACTUACION = '$id'";
			
	$resultado = sqlsrv_query($conn, $tsql);

	if( $resultado === false ) {
    	die( print_r( sqlsrv_errors(), true));
	} else {
		$registro = sqlsrv_fetch_array($resultado);
	}	
	//echo "tsql: ".$tsql;
	//echo "<br>ID_GD_RA: ".$registro['ID_GD_RA'];
	//echo "<br>CABECERA_RA: ".$registro['CABECERA_RA'];
		
	sqlsrv_free_stmt($resultado);						
	sqlsrv_close($conn);	
  
?>
<!DOCTYPE html>
<html lang="es">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title>Consulta RA-RD Actuación</title>
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
<form method="post" action="consultaActuacion.php" role="form">
	<div class="modal-body-large" style="line-height:13px;">       
		
		<fieldset>    
		 
		<div class="row-fluid yellow">
 
           <div class="span1 blue">
           	<h2>RA</h2> 
		   </div>	

 <!--           <div class="control-group form-group span2">
				<div class="controls">
					<strong>ID_GD_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_GD_RA" value="<?php echo $registro['ID_GD_RA'];?>">
				</div>
			</div>				
            <div class="control-group form-group span2">
				<div class="controls">
					<strong>ID_FDTT_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_FDTT_RA" value="<?php echo $registro['ID_FDTT_RA'];?>">
				</div>
			</div>	
			<div class="control-group form-group span2">
				<div class="controls">
					<strong>EECC_CARGA_DIS_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_CARGA_DIS_RA" value="<?php echo $registro['EECC_CARGA_DIS_RA'];?>">
				</div>
			</div>
			<div class="control-group form-group span2">
				<div class="controls">
					<strong>FECHA_ENVIO_DIS_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENVIO_DIS_RA" value="<?php echo date_format($registro['FECHA_ENVIO_DIS_RA'], 'Y-m-d H:i:s'); ?>">
				</div>
			</div>	
			<div class="control-group form-group span2">
				<div class="controls">
					<strong>FECHA_ENTREGA_DIS_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENTREGA_DIS_RA" value="<?php echo date_format($registro['FECHA_ENTREGA_DIS_RA'], 'Y-m-d H:i:s');?>">
				</div>
			</div>

           <div class="span1">
		   </div>	 -->

		</div>	
    
    	<div class="row-fluid">
		<div class="span2 blue" ontablet="span4" ondesktop="span2">
            <div class="control-group form-group">
				<div class="controls">
					<strong>CABECERA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="CABECERA_RA" value="<?php echo $registro['CABECERA_RA'];?>">
				</div>
			</div>	
			       <div class="control-group form-group">
				<div class="controls">
					<strong>EECC_CARGA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_CARGA_RA" value="<?php echo $registro['EECC_CARGA_RA'];?>">
				</div>
			</div>	
			<div class="control-group form-group">
				<div class="controls">
					<strong>FECHA_ENVIO_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENVIO_RA" value="<?php echo date_format($registro['FECHA_ENVIO_RA'], 'Y-m-d H:i:s');?>">
				</div>
			</div>
			<div class="control-group form-group">
				<div class="controls">
					<strong>FECHA_ENTREGA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENTREGA_RA" value="<?php echo date_format($registro['FECHA_ENTREGA_RA'], 'Y-m-d H:i:s');?>">
				</div>
			</div>	
		</div>

		<div class="span2 blue" ontablet="span4" ondesktop="span2">
			<div class="control-group form-group">
				<div class="controls">
					<strong>EEMM: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EEMM_RA" value="<?php echo $registro['EEMM_RA'];?>">
				</div>
			</div>		
			<div class="control-group form-group">
				<div class="controls">
					<strong>PROVINCIA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="PROVINCIA_RA" value="<?php echo $registro['PROVINCIA_RA'];?>">
				</div>
			</div>					
<!--            <div class="control-group form-group">
				<div class="controls">
					<strong>FX_AUDITORIA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FX_AUDITORIA_RA" value="<?php echo $registro['FX_AUDITORIA_RA'];?>">
				</div>
			</div>	
			       <div class="control-group form-group">
				<div class="controls">
					<strong>FX_REV_AUDITORIA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FX_REV_AUDITORIA_RA" value="<?php echo $registro['FX_REV_AUDITORIA_RA'];?>">
				</div>
			</div>	
			<div class="control-group form-group">
				<div class="controls">
					<strong>AUDITADO_POR_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="AUDITADO_POR_RA" value="<?php echo $registro['AUDITADO_POR_RA'];?>">
				</div>
			</div>
			<div class="control-group form-group">
				<div class="controls">
					<strong>OBS_AUDITORIA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="OBS_AUDITORIA_RA" value="<?php echo $registro['OBS_AUDITORIA_RA'];?>">
				</div>
			</div>	 -->
		</div>

		<div class="span2 blue" ontablet="span4" ondesktop="span2">
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
<!-- 			<div class="control-group form-group">
				<div class="controls">
					<strong>ESTADO_RA_FDTT: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO_RA_FDTT" value="<?php echo $registro['ESTADO_RA_FDTT'];?>">
				</div>
			</div>	
			<div class="control-group form-group">
				<div class="controls">
					<strong>REPAROS_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="REPAROS_RA" value="<?php echo $registro['REPAROS_RA'];?>">
				</div>
			</div>
			<div class="control-group form-group">
				<div class="controls">
					<strong>N_AUDITORIAS_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="N_AUDITORIAS_RA" value="<?php echo $registro['N_AUDITORIAS_RA'];?>">
				</div>
			</div>	 -->
		</div>			

		<div class="span2 blue" ontablet="span4" ondesktop="span2">
			<div class="control-group form-group">
				<div class="controls">
					<strong>ARBOL_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ARBOL_RA" value="<?php echo $registro['ARBOL_RA'];?>">
				</div>
			</div>		
 <!--            <div class="control-group form-group">
				<div class="controls">
					<strong>ESTADO_AB_PL4_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO_AB_PL4_RA" value="<?php echo $registro['ESTADO_AB_PL4_RA'];?>">
				</div>
			</div>	
			       <div class="control-group form-group">
				<div class="controls">
					<strong>SUC_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="SUC_RA" value="<?php echo $registro['SUC_RA'];?>">
				</div>
			</div>	
			<div class="control-group form-group">
				<div class="controls">
					<strong>CE_RA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="CE_RA" value="<?php echo $registro['CE_RA'];?>">
				</div>
			</div>
			<div class="control-group form-group">
				<div class="controls">
					<strong>AB_RA_GIS: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="AB_RA_GIS" value="<?php echo $registro['AB_RA_GIS'];?>">
				</div>
			</div> -->
		</div>				

		<div class="span2 blue" ontablet="span4" ondesktop="span2">
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
<!-- 			       <div class="control-group form-group">
				<div class="controls">
					<strong>FECHA_INI_OBRA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_INI_OBRA_RA" value="<?php echo date_format($registro['FECHA_INI_OBRA_RA'], 'Y-m-d H:i:s');?>">
				</div>
			</div>	
			<div class="control-group form-group">
				<div class="controls">
					<strong>FECHA_FIN_OBRA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_FIN_OBRA_RA" value="<?php echo date_format($registro['FECHA_FIN_OBRA_RA'], 'Y-m-d H:i:s');?>">
				</div>
			</div> -->
		</div>

		<div class="span2 blue" ontablet="span4" ondesktop="span2">
<!-- 			<div class="control-group form-group">
				<div class="controls">
					<strong>SEGUIMIENTO_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="SEGUIMIENTO_RA" value="<?php echo $registro['SEGUIMIENTO_RA'];?>">
				</div>
			</div> -->
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
		</div>					
		</div>							





		
		<div class="row-fluid yellow">
 
           <div class="span1 blue">
           		<h2>RD</h2>
		   </div>	

<!--            <div class="control-group form-group span2">
				<div class="controls">
					<strong>ID_GD_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_GD_RD" value="<?php echo $registro['ID_GD_RD'];?>">
				</div>
			</div>				
            <div class="control-group form-group span2">
				<div class="controls">
					<strong>ID_FDTT_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ID_FDTT_RD" value="<?php echo $registro['ID_FDTT_RD'];?>">
				</div>
			</div>	
			<div class="control-group form-group span2">
				<div class="controls">
					<strong>EECC_CARGA_RD_DISENO: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_CARGA_RD_DISENO" value="<?php echo $registro['EECC_CARGA_RD_DISENO'];?>">
				</div>
			</div>
			<div class="control-group form-group span2">
				<div class="controls">
					<strong>DIA_ENVIO_DISENO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="DIA_ENVIO_DISENO_RD" value="<?php echo $registro['DIA_ENVIO_DISENO_RD'];?>">
				</div>
			</div>	
			<div class="control-group form-group span2">
				<div class="controls">
					<strong>DIA_ENTREGA_DISENO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="DIA_ENTREGA_DISENO_RD" value="<?php echo $registro['DIA_ENTREGA_DISENO_RD'];?>">
				</div>
			</div>

           <div class="span1">
		   </div>	 -->

		</div>	
    
    	<div class="row-fluid">
			<div class="span2 blue" ontablet="span4" ondesktop="span2">
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
				<div class="control-group form-group">
					<div class="controls">
						<strong>EECC_CARGA_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EECC_CARGA_RD" value="<?php echo $registro['EECC_CARGA_RD'];?>">
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<strong>FECHA_ENVIO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENVIO_RD" value="<?php echo date_format($registro['FECHA_ENVIO_RD'], 'Y-m-d H:i:s');?>">
					</div>
				</div>	
				<div class="control-group form-group">
					<div class="controls">
						<strong>FECHA_ENTREGA_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_ENTREGA_RD" value="<?php echo date_format($registro['FECHA_ENTREGA_RD'], 'Y-m-d H:i:s');?>">
					</div>
				</div>	
<!-- 				<div class="control-group form-group">
					<div class="controls">
						<strong>ESTADO_AB_PL4_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO_AB_PL4_RD" value="<?php echo $registro['ESTADO_AB_PL4_RD'];?>">
					</div>
				</div>	 -->						
			</div>


			<div class="span2 blue" ontablet="span4" ondesktop="span2">
				<div class="control-group form-group">
					<div class="controls">
						<strong>ACT_TESA_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACT_TESA_RD" value="<?php echo $registro['ACT_TESA_RD'];?>">
					</div>
				</div>		
				<div class="control-group form-group">
					<div class="controls">
						<strong>CABECERA_RA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="CABECERA_RA" value="<?php echo $registro['CABECERA_RA'];?>">
					</div>
				</div>			
				<div class="control-group form-group">
					<div class="controls">
						<strong>EEMM: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EEMM_RD" value="<?php echo $registro['EEMM_RD'];?>">
					</div>
				</div>		
				<div class="control-group form-group">
					<div class="controls">
						<strong>ESTADO_GIS_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ESTADO_GIS_RD" value="<?php echo $registro['ESTADO_GIS_RD'];?>">
					</div>
				</div>									
<!-- 	           <div class="control-group form-group">
					<div class="controls">
						<strong>AUDITADO_FECHA_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="AUDITADO_FECHA_RD" value="<?php echo date_format($registro['AUDITADO_FECHA_RD'], 'Y-m-d H:i:s');?>">
					</div>
				</div>	
				       <div class="control-group form-group">
					<div class="controls">
						<strong>AUDITADO_POR_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="AUDITADO_POR_RD" value="<?php echo $registro['AUDITADO_POR_RD'];?>">
					</div>
				</div>	
				<div class="control-group form-group">
					<div class="controls">
						<strong>REPAROS_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="REPAROS_RD" value="<?php echo $registro['REPAROS_RD'];?>">
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<strong>OBSERVACIONES_AUDITORIA_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="OBSERVACIONES_AUDITORIA_RD" value="<?php echo $registro['OBSERVACIONES_AUDITORIA_RD'];?>">
					</div>
				</div>	 -->
			</div>

			<div class="span2 blue" ontablet="span4" ondesktop="span2">
				<div class="control-group form-group">
					<div class="controls">
						<strong>ACT_JAZZTEL_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACT_JAZZTEL_RD" value="<?php echo $registro['ACT_JAZZTEL_RD'];?>">
					</div>
				</div>		
	            <div class="control-group form-group">
					<div class="controls">
						<strong>ACT_ID_FDTT_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="ACT_ID_FDTT_RD" value="<?php echo $registro['ACT_ID_FDTT_RD'];?>">
					</div>
				</div>	
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
<!-- 				<div class="control-group form-group">
					<div class="controls">
						<strong>Fx_fin_CONS_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="Fx_fin_CONS_RD" value="<?php echo $registro['Fx_fin_CONS_RD'];?>">
					</div>
				</div>	 -->			
			</div>			

			<div class="span2 blue" ontablet="span4" ondesktop="span2">
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
<!-- 				<div class="control-group form-group">
					<div class="controls">
						<strong>UUII_AI_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="UUII_AI_RD" value="<?php echo $registro['UUII_AI_RD'];?>">
					</div>
				</div>		
	            <div class="control-group form-group">
					<div class="controls">
						<strong>FIR_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FIR_RD" value="<?php echo $registro['FIR_RD'];?>">
					</div>
				</div>	
				       <div class="control-group form-group">
					<div class="controls">
						<strong>INC_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="INC_RD" value="<?php echo $registro['INC_RD'];?>">
					</div>
				</div>	 -->				
			</div>				



<!-- 			<div class="span2 blue" ontablet="span4" ondesktop="span2">
				<div class="control-group form-group">
					<div class="controls">
						<strong>AS_BUILT_ARBOL_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="AS_BUILT_ARBOL_RD" value="<?php echo $registro['AS_BUILT_ARBOL_RD'];?>">
					</div>
				</div>
	            <div class="control-group form-group">
					<div class="controls">
						<strong>AIE2E_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="AIE2E_RD" value="<?php echo $registro['AIE2E_RD'];?>">
					</div>
				</div>	
				<div class="control-group form-group">
					<div class="controls">
						<strong>Fx_ENTRADA_PL_CAMPO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="Fx_ENTRADA_PL_CAMPO_RD" value="<?php echo $registro['Fx_ENTRADA_PL_CAMPO_RD'];?>">
					</div>
				</div>		
				<div class="control-group form-group">
					<div class="controls">
						<strong>Fx_ACTUALIZACION_PL_CAMPO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="Fx_ACTUALIZACION_PL_CAMPO_RD" value="<?php echo $registro['Fx_ACTUALIZACION_PL_CAMPO_RD'];?>">
					</div>
				</div>	
				<div class="control-group form-group">
					<div class="controls">
						<strong>Observaciones_PLANO_CAMPO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="Observaciones_PLANO_CAMPO_RD" value="<?php echo $registro['Observaciones_PLANO_CAMPO_RD'];?>">
					</div>
				</div>				
			</div>	

			<div class="span2 blue" ontablet="span4" ondesktop="span2">

					<div class="controls">
						<strong>FX_INTERCAMBIO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="FX_INTERCAMBIO_RD" value="<?php echo $registro['FX_INTERCAMBIO_RD'];?>">
					</div>
			

					<div class="controls">
						<strong>BLOQUEADO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="BLOQUEADO_RD" value="<?php echo $registro['BLOQUEADO_RD'];?>">
					</div>

					<div class="controls">
						<strong>Fx_BLOQUEADO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="Fx_BLOQUEADO_RD" value="<?php echo $registro['Fx_BLOQUEADO_RD'];?>">
					</div>

					<div class="controls">
						<strong>Fx_SOL_BLOQUEO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="Fx_SOL_BLOQUEO_RD" value="<?php echo $registro['Fx_SOL_BLOQUEO_RD'];?>">
					</div>

					<div class="controls">
						<strong>OBS_BLOQUEO_RD: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="OBS_BLOQUEO_RD" value="<?php echo $registro['OBS_BLOQUEO_RD'];?>">
					</div>

			</div> -->

		</div>						



		</fieldset>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	</div>
</form>
</body>
</html>
