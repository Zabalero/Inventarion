<?php
session_start ();
header ( "Cache-control: private" );
$_SESSION ['detalle'] = "TRUE";

require_once "inc/theme.inc";
require "inc/funciones.inc";

// Conectar con el servidor de base de datos
$conn = conectar_bd ();

// Comentario prueba
$mensaje = "";

$mensaje = $_REQUEST ['mensaje'];

// print the page header
print_theme_header ();

?>
			<!-- start: Content -->
		<div id="content" class="span12">
			
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="index.php">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Acceso</a></li>
			</ul>

			<!--FORMULARIO-->
			<form method="post" action="login.php" autocomplete="off" role="form" name="login">
				<fieldset>    
		    	<div class="row-fluid">
					<div class="span3">					
						<div class="control-group">
							<label class="control-label" for="usuario">USUARIO: </label>
							<div class="controls">
								<input tabindex="1" type="text" class="input" id="usuario"  name="usuario" value="">
							</div>	
						</div>	
					</div>		  
				</div>
		    	<div class="row-fluid">
					<div class="span3">					
						<div class="control-group">
							<label class="control-label" for="password">PASSWORD: </label>
							<div class="controls">
								<input tabindex="2" type="password" class="input" id="password"  name="password" value="">
							</div>	
						</div>	
					</div>		  
				</div>		                        					
		    	<div class="row-fluid">
					<div class="span3">					
						<div class="control-group">
							<div class="controls">
								<INPUT TYPE="submit" class="btn btn-primary" NAME="enviar" id = "enviar" VALUE="Enviar">  
							</div>	
						</div>	
					</div>		  
				</div>
                        <div class="row-fluid">
                                <div class="span3">					
                                        <div class="control-group">
                                            <p> <a id="a_resetPassword" name="a_resetPassword" href="#"><i>Recuperar contraseña</i></a></p>	
                                        </div>	
                                </div>		  
                        </div>
				<div class="row-fluid">
					<div class="alert alert-block">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<h4 class="alert-heading">Warning!</h4>
							<?php
							
echo "<p>" . $mensaje . "</p>";
							?>
					</div>					
				</div>						

				<!--FIN DETALLE-->
		    
			</form>    
			<!--FIN FILTROS-->  

 	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->
		

<div class="clearfix"></div>	

<?php
print_theme_footer ();
?>
<script>      
    $('#a_resetPassword').click(
            function() {           
                window.location.href = "resetPassword.php";
            }
     );
</script>