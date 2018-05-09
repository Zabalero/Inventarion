<?php

	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	$tsql = "{call pa_prueba()}"; 

	$stmt = sqlsrv_query($conn, $tsql);

	if( $stmt )  {  
	     echo "Actualización correcta de FDTT<br>";  
	}  else  {  
	     echo "Error al actualizar FDTT<br>";  
	     die( print_r( sqlsrv_errors(), true));  
	}  

	sqlsrv_free_stmt( $stmt);  
	sqlsrv_close( $conn);


?>