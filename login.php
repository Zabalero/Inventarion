<?php
	//Se inicia la sesión
	session_start();

	//Se establecen las variables de sesión
	$_SESSION['usuario']=$_POST['usuario'];
	$_SESSION['password']=$_POST['password'];
	//Se incluyen las funciones necesarias
	require "inc/funciones.inc";

	if (es_usuario($_SESSION['usuario'],$_SESSION['password'])){
		//Si el usuario es un usuario registrado
		header("Location: buscar.php");
	} else{
		$mensaje = "Acceso denegado";
		header("Location: index.php?mensaje=$mensaje");
	}


?>

