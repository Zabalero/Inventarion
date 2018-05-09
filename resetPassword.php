<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 
        $_SESSION['token']="";

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
      
	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	$mensaje = "";
	$mensaje = $_REQUEST['mensaje'];
	
	// print the page header
	print_theme_header();


?>
			<!-- start: Content -->
		<div id="content" class="span12">
			
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="index.php">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Reset Password</a></li>
			</ul>

			<!--FORMULARIO-->
			<div class="panel-body">
                            <form id="form1" name="form1" method="POST" action="sv_resetPassword.php">                     
                                  <div class="form-group">
                                    <label for="email"> Introduzca la dirección de correo asociada a su cuenta:</label>
                                    <input required type="email" id="input_email" placeholder="email..." class="form-control" name="input_email" style='width:450px;' >  
                                  </div> 

                                 <!--<div class="form-group">
                                      <p>Y complete el captcha:</p>
                                    <?php
                                      //require_once('jquery/recaptcha-php-1.11/recaptchalib.php');
                                      //$publickey = "6LfRQdMSAAAAAN3s0UEzB_pBuWRu_udBDB-WLMdf"; // you got this from the signup page
                                      //echo recaptcha_get_html($publickey);
                                    ?>
                                  </div>-->
                                <hr/>
                                  <div class="form-group">
                                      <input type="submit" name="btn_crearReset" id="btn_crearReset" class="btn btn-block btn-success" style='width:300px;' value="Solicitar nueva contraseña" >                      
                                  </div>


                            </form>	
                        </div>   
			<!--FIN FILTROS-->  

 	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->
		

<div class="clearfix"></div>	

<?php
	print_theme_footer();
?>
<script>
    $( "#form1" ).submit(function( event ) {
        $('#enviarPeticion').modal();
        //event.preventDefault();
      });
    $(document).ready(function(){           
        

        $("#input_email").keypress(function(){

             $("#input_email").css("border", "1px solid blue");

        });            

    });

</script>