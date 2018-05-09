<?php
	require "inc/funciones.inc";
    $id = $_GET['id'];  

    $conn=conectar_bd();

    //CAMBIOS DE ESTADO DE LA TAREA
 	$tsql = "SELECT  *
				FROM INV_HISTORICO_TAREAS
				WHERE ID_TAREA = '$id'
				ORDER BY FECHA_CAMBIO ASC";

	$cambiosTarea = sqlsrv_query($conn, $tsql);

	if( $cambiosTarea === false ) {
    	die( print_r( sqlsrv_errors(), true));
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
<form method="post" action="consultaHitoriaTarea.php" role="form">
	<div class="modal-body">
		<!--DETALLE CTOS MARCADAS CON BLOQUEO-->
		

		<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">
			<thead>
				<tr>
					<th>FECHA</th>	
					<th>ESTADO ANTERIOR</th>	
					<th>NUEVO ESTADO</th>	
					<th>SUBACTIVIDAD</th>	
					<th>USUARIO</th>	
				</tr>
			</thead>   
			<tbody>

		<?php 
		$rows = sqlsrv_has_rows($cambiosTarea );
		if ($rows === true){			
			
			while ($cambio = sqlsrv_fetch_array($cambiosTarea)){ ?>
				<tr>
					<td class="center"><?php echo date_format($cambio['FECHA_CAMBIO'], 'Y-m-d h:m:s.000'); ?></td>
					<td class="center"><?php echo descripcionEstado($cambio['ID_ESTADO_ANT']); ?></td>
					<td class="center"><?php echo descripcionEstado($cambio['ID_ESTADO_NEW']); ?></td>
					<td class="center"><?php echo descripcionSubactividad($cambio['ID_SUBACTIVIDAD']); ?></td>
					<td class="center"><?php echo get_nombreFromId($cambio['ID_USUARIO']); ?></td>
				</tr>	
			<?php }
		} else {
			echo '<h2>NO HAY CAMBIOS DE ESTADO PARA ESTA TAREA</h2>';
		}
				sqlsrv_free_stmt($cambiosTarea);							
				sqlsrv_close($conn);	
			?>	
			</tbody>
		</table>							
	</div>
		<!--FIN DETALLE-->
 	
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	</div>
</form>
</body>
</html>
