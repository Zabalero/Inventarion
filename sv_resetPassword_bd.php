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

$text_error="";
$error=false;

if ((isset($_POST["token"])) && isset($_POST['password']) && isset($_POST['password2'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $email=$_POST['input_email'];
                
    if($password!=$password2){
        $text_error="Las contraseñas no son iguales.";
        $error=true;
    } else {
        if ($token!=$_SESSION['token']) {
            $text_error="El código de validación no es correcto";
            $error=true;
        } else {
            $tsql = "UPDATE INV_tbUSUARIOS SET 
                         [password] = '{$password}'
                          WHERE mail='{$email}'";

            $resultado = sqlsrv_query($conn, $tsql);

            if( $resultado === false ) {
                $text_error="Error al actualizar la BD: ".$tsql." ".sqlsrv_errors();
                $error=true;
            } else {
                //libero token
                $_SESSION['token']="";
            }

            sqlsrv_free_stmt($resultado);
        }
    }
} else {
    $text_error="Campos vacios en el token o la contraseña.";
    $error=true;
}
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
                    <li><a href="#">Recuperación contraseña</a></li>
            </ul>

            <!--FORMULARIO-->
            <div class="panel-body">
                <?php if ($error) {?>
                    <div class="alert alert-block">
                        <p style='color:red;'><?php echo ($text_error);?></p>  
                    </div>                                      
                <?php } else { 
                    ?>
                    <p>Su contraseña se ha actualizado correctamente</p>
                <?php } ?>
                <a href='index.php' class='btn'> << Volver </a>
           </div>   
            <!--FIN FILTROS-->  

        </div><!--/#content.span10-->            
    </div><!--/row-->
</div><!--/.fluid-container-->
<?php
	print_theme_footer();
?>