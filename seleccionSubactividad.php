<?php
        session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
      
	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

if(isset($_POST['get_option']))
{
    //echo '<script language="javascript">alert("juas");</script>'; exit();
    $actividadSeleccionada = $_POST['get_option'];
    
    //$actividadSeleccionada = 1; 
    $sqlSubActividades="Select * FROM INV_tbSubActividad  where id_Actividad ='".$actividadSeleccionada."'";// order by id_Subactividad";
    
    $subActividades=sqlsrv_query($conn, $sqlSubActividades);

    if (isset($subActividades)) {
        while ($lineaSubActividades = sqlsrv_fetch_array($subActividades)){ 
            echo "<option value=" .$lineaSubActividades['id_Subactividad'].">". $lineaSubActividades['Descripcion']."</option>";
        }
    }
    exit();
}
?>