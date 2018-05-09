<?php
    /**
    * controlador para enviar link a usuario y crear registro en tabla
    *
    * @param recibe datos de formulario 
    *
    */

session_start();
header("Cache-control: private");
$_SESSION['detalle']="TRUE"; 

require_once "inc/theme.inc";
require "inc/funciones.inc";

//Conectar con el servidor de base de datos
$conn=conectar_bd();

$mensaje = "";
$mensaje = $_REQUEST['mensaje'];


$arrayJson=array();
$valido=false;
$respuesta="No existe ningún usuario con ese email asociado";

//recibimos el email
$email=$_POST['input_email'];

//Elimino captcha en el servidor no funciona por los puertos
/*require_once('jquery/recaptcha-php-1.11/recaptchalib.php');
$privatekey = "6LfRQdMSAAAAALhNk3NAszrSeJSDmgB571gIhYWT";
$resp = recaptcha_check_answer ($privatekey,
                              'localhost',
                              $_POST["recaptcha_challenge_field"],
                              $_POST["recaptcha_response_field"]);

*/

// print the page header
print_theme_header();

// Se genera una cadena para validar el cambio de contraseña
$cadena = rand(1,9999999).date('Y-m-d');
$token = sha1($cadena);
$total_mail=1;
$error_envio=true;

//si el cpatcha no es correcto no hará falta enviar mail
//if ($resp->is_valid) {
    $total_mail=0;
    
    //Comprobar mail
    $tsql = "SELECT count(*) as total 
                FROM INV_tbUSUARIOS 
                WHERE mail='{$email}' ";


    $registros = sqlsrv_query($conn, $tsql);

    if( $registros === false ) {
            die ("Error al ejecutar consulta: ".$tsql." Error: ".sqlsrv_errors());
    }
    $linea = sqlsrv_fetch_array($registros);
    $total_mail=intval($linea['total']);

    //Existe el mail en la BD
    if ($total_mail>=1) {       
        
        $_SESSION['token']=$token;

        //ENVIO DE MAIL	DESARROLLO
        /*
        include_once( 'Classes/pruebas/class.phpmailer.php' ); 
        $mail = new PHPMailer(); //Creamos un objeto

        $mailUsu=$email; 	
        $nombreUsu=$email; 	
        


        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.gmail.com";
        $mail->Port =465;
        $mail->Username = "eurocontroljazztel@gmail.com";
        $mail->Password = "qawsed1234";
        $mail->SMTPSecure = 'ssl';
        $mail->SetFrom("eurocontroljazztel@gmail.com", "Jazztel");


        $mail->AddAddress( $mailUsu, $nombreUsu );

        $cuerpo='<html>
                        <head>
                        </head>
                        <body style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12px">

                        <p>Hola,</p>

                        <p>A continuación te mostramos el código de validación que debes introducir para crear o recuperar tu contraseña en el portal</p>';	

        $cuerpo = $cuerpo.'<p><strong>'.$token.'</strong></p>';

        $cuerpo = $cuerpo.'<p>Un saludo</p>';

        //cabecera
        /*
        $cabeceraMail='Código de validación.';	

        $mail->Subject = $cabeceraMail; 	

        $mail->Body = $cuerpo; 

        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';

        $error_envio=false;
        $error_envio_texto="";
        //Enviamos el email
        if(!$mail->Send()) {
            $error_envio_texto=$mail->ErrorInfo;
            $error_envio=true;    
        };*/
        
        /** Para producción **/
        $errorMessage="";
        $cuerpo='<html>
                        <head>
                        </head>
                        <body style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12px">

                        <p>Hola,</p>

                        <p>A continuación te mostramos el código de validación que debes introducir para crear o recuperar tu contraseña en el portal</p>';	

        $cuerpo = $cuerpo.'<p><strong>'.$token.'</strong></p>';

        $cuerpo = $cuerpo.'<p>Un saludo</p>';
        
        $mailOtro='maria.benito@ext.jazztel.com'; 	
        $nombreOtro='Maite Benito Jazztel'; 
        
        $headers = "From: $nombreOtro <$mailOtro>\r\n".
               "MIME-Version: 1.0" . "\r\n" .
               "Content-type: text/html; charset=UTF-8" . "\r\n"; 
        
         if(mail($email,'Código de validación.',$cuerpo,$headers)){
             $error_envio=false;
         } else {
             $error_envio=true;
             $errorMessage = error_get_last()['message'];
         }
    }
//}
?>

<!-- start: Content -->
        <div id="content" class="span12">					
            <ul class="breadcrumb">
                    <li>
                            <i class="icon-home"></i>
                            <a href="index.php">Home</a> 
                            <i class="icon-angle-right"></i>
                    </li>
                    <li><a href="#">Validar Código recuperación contraseña</a></li>
            </ul>

            <!--FORMULARIO-->
            <div class="panel-body">
                <?php if ($total_mail<1) {?>
                    <div class="alert alert-block">
                        <p style='color:red;'>El mail <strong><?php echo ($email);?></strong> no existe en la base de datos.</p>
                    </div>
                    <a href='resetPassword.php' class='btn'> << Volver </a>
                <?php } else { ?>
                    <?php if($error_envio) {?>
                        <div class="alert alert-block">
                            <p style='color:red;'>Se ha producido un error al enviar el mail a su cuenta, intentelo de nuevo y si vuelve a producirse contacte con el administrador. Gracias.</p>
                            <p style='color:red;'>ERROR: <?php echo($errorMessage); ?></p>
                        </div>
                        <a href='resetPassword.php' class='btn'> << Volver </a>
                    <?php } else { ?>
                        <?php //if (!$resp->is_valid) { ?>
                            <!--<div class="alert alert-block">
                                <p style='color:red;'>El captcha no es correcto</p>   
                            </div>
                            <a href='resetPassword.php' class='btn'> << Volver </a>-->
                          <?php //} else { ?>
                              <form id="form1" name="form1" method="POST" action="sv_resetPassword_bd.php">
                                  <input type='hidden' id='input_email' name='input_email' value='<?php echo ($email);?>' />
                                 <div class="form-group">
                                     <label for="email"> Introduzca el <strong>código de validación enviado a su cuenta de mail</strong>, para realizar el cambio de contraseña:</label>
                                  <input required type="text" id="token" placeholder="Código de validación..." class="form-control" name="token" >  
                                </div>
                                <div class="form-group">
                                  <label for="email"> Introduzca nueva contraseña:</label>
                                  <input required type="password" id="password" placeholder="Contraseña..." class="form-control" name="password" >  
                                </div> 
                                <div class="form-group">
                                  <label for="email"> Repita contraseña:</label>
                                  <input required type="password" id="password2" placeholder="Repetir Contraseña..." class="form-control" name="password2" >  
                                </div>                                  


                                <div class="form-group">
                                    <input type="submit" name="btn_crearReset" id="btn_crearReset" class="btn" style='width:300px;' value="Cambiar contraseña" >                      
                                </div>
                            </form>
                          <?php //} ?>   
                    <?php } ?>
                <?php } ?>
           </div>   
            <!--FIN FILTROS-->  

        </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->

<?php
	print_theme_footer();
?>