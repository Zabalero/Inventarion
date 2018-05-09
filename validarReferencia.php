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
    $parametros = $_POST['get_option'];
    $listaParametros = explode ("#",$parametros);;
    $listaTareas = $listaParametros[0];
    $referencia =  $listaParametros[1];
    $Idtareas =  explode (";",$listaTareas);
     
    $sqlTareas="Select * FROM INV_tbTareas  where REF ='".$referencia."'";
    
    $result=sqlsrv_query($conn, $sqlTareas);
    $rows = sqlsrv_has_rows( $result );
    
    if ($rows === true) {
        foreach ($Idtareas as $id){
           if ($id!==''){
            $sqlUpdate="update INV_tbTareas set REF_ASOCIADA = '".$referencia."' where id = '".$id."' ";
            $resultUpdate=sqlsrv_query($conn, $sqlUpdate);
           }
        }
        echo ("Las tareas se han modificado correctamente");
    }else{
        echo ("La referencia introducida no existe");
    }
       
    exit();
} else {
    echo ("La referencia asociada está vacia");
}
?>