<?php
    require "inc/funciones.inc";
    $listaTareas = $_GET['listaTareas'];  


    $conn=conectar_bd();

   
    //CAMBIOS DE ESTADO DE LA TAREA
 	/*$tsql = "SELECT  *
				FROM INV_HISTORICO_TAREAS
				WHERE ID_TAREA = '$id'
				ORDER BY FECHA_CAMBIO ASC";

	$cambiosTarea = sqlsrv_query($conn, $tsql);

	if( $cambiosTarea === false ) {
    	die( print_r( sqlsrv_errors(), true));
	}*/

  
?>
<!DOCTYPE html>
<html lang="es">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title>Escribir referencia asociada</title>
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
<form method="post" action="asociarTareas.php" role="form">
	<div class="modal-body">
		<!--DETALLE CTOS MARCADAS CON BLOQUEO-->
		<?php 
                   if ($listaTareas===''){ ?>
                
                  <div class="row-fluid">
			<div class="alert alert-success">
                            <?php echo "Debe seleccionar previamente las tareas que desee asociar." ?>
			</div>					
		 </div>		
                       
                <?php   }else{ ?>
                 <div class="control-group form-group">
                     <div class="controls">
			     <strong>Referencia Asociada*: </strong>
                             <input type="text" class="form-control" required id="REF_ASOCIADA" <?php if ($listaTareas==='') { ?>  disabled <?php } ?> name="REF_ASOCIADA" maxlength="50" value="" style="width:70%;">
                     </div>
                  </div>
                <?php } ?>
	</div>
     
		<!--FIN DETALLE-->
 	
	<div class="modal-footer">
            
          <?php if ($listaTareas!==''){ ?>
            <button type="submit" name="btnAceptar" id="btnAceptar"  class="btn btn-primary" onclick="validarReferencia(REF_ASOCIADA.value);" data-dismiss="modal">Aceptar</button>
          <?php } ?>   
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	</div>
</form>
</body>
</html>
<script>
    function verificar (valorReferencia){
        
       //alert (valorReferencia);
       if (valorReferencia == ''){
            alert ("Esta vacio");
            
        }else{
            alert ("NO Esta vacio");
            validarReferencia(valorReferencia);
        }
        
        
        
    }
    function validarReferencia (referencia){
      
         $.ajax({
         type: 'post',
         url: 'validarReferencia.php',
        data: {
            get_option:listaTareas + '#' + referencia
         },
    
    success: function (response) {
        alert (response);
        $("#mensaje").html(response);
 }
 });
        
    }
    
</script>
