<?php
	require "inc/funciones.inc";
    $id = $_GET['id'];  

    $conn=conectar_bd();

 	$tsql = "select * from INV_TBCONCATENADO where id = '$id'";
			
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
<form method="post" action="consultaIdMapeo.php" role="form">
	<div class="modal-body" >       
		
		<fieldset>    
		 
		<div class="row-fluid">
 
           <div class="control-group form-group">
				<div class="controls">
					<strong>SERVICIO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="SERVICIO" value="<?php echo $registro['SERVICIO'];?>">
				</div>
			</div>		
		</div>		

		<div class="row-fluid">			
            <div class="control-group form-group">
				<div class="controls">
					<strong>TIPO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="TIPO" value="<?php echo $registro['TIPO'];?>">
				</div>
			</div>	
		</div>		

		<div class="row-fluid">			
            <div class="control-group form-group">
				<div class="controls">
					<strong>DESCRIPCION: </strong><br>
					<textarea rows="5" name="DESCRIPCION" readonly="true" class="input uneditable-input" name="DESCRIPCION"><?php echo $registro['DESCRIPCION'];?></textarea>
					
				</div>
			</div>
		</div>		

		<div class="row-fluid">	
			<div class="control-group form-group">
				<div class="controls">
					<strong>SINTOMA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="SINTOMA" value="<?php echo $registro['SINTOMA']; ?>">
				</div>
			</div>	
		</div>		

		<div class="row-fluid">	
			<div class="control-group form-group">
				<div class="controls">
					<strong>TIPOLOGIA_INICIAL: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="TIPOLOGIA_INICIAL" value="<?php echo $registro['TIPOLOGIA_INICIAL'];?>">
				</div>
			</div>
		</div>	

		<div class="row-fluid">	
 			<div class="control-group form-group">
				<div class="controls">
					<strong>GRUPO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="GRUPO" value="<?php echo $registro['GRUPO'];?>">
				</div>
			</div>
		</div>		

		<div class="row-fluid">	
 			<div class="control-group form-group">
				<div class="controls">
					<strong>HUELLA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="HUELLA" value="<?php echo $registro['HUELLA'];?>">
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
