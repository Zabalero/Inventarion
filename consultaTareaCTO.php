<?php
	require "inc/funciones.inc";
    $id = $_GET['id'];  

    $conn=conectar_bd();

    //CTOS DE LA ACTUACION
 	$tsql = "SELECT  INV_CTOS.NUMERO
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
  
?>
<!DOCTYPE html>
<html lang="es">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title>Consulta Información CTO´s</title>
	<meta name="description" content="Consulta Información CTO´s">
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
<form method="post" action="consultaTareaCTO.php" role="form">
	<div class="modal-body">
		<!--DETALLE CTOS MARCADAS CON BLOQUEO-->
		<div class="row-fluid">	
	
		<?php 
		$rows = sqlsrv_has_rows($ctosActuacion );
		$contador = 0;						
		if ($rows === true){			
			
			while ($cto = sqlsrv_fetch_array($ctosActuacion)){ ?>
				<?php
					if (($contador%6) == 0) {
						echo '</div>';
						echo '<div class="row-fluid">';
					}
					$contador = $contador + 1;
				?>	
				<div class="control-group form-group span2">
					<div class="controls">
					<?php
						echo '<input readonly="true" type="text" class="form-control input uneditable-input '.((in_array($cto['NUMERO'], $arrayCtosTarea))?"yellow":"").'"  value="'.$cto['NUMERO'].'">';	
					?>							
					</div>
				</div>		
			<?php }
		} else {
			echo '<h2>NO HAY INFORMACIÓN DE CTO´S</h2>';
		}
				sqlsrv_free_stmt($ctosActuacion);							
				sqlsrv_close($conn);	
			?>					
		</div>
		<!--FIN DETALLE-->
 	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	</div>
</form>
</body>
</html>
