<?php
	session_start();
	require "inc/funciones.inc";
		
	$id=$_GET['id'];

	if ($id <> '') {
		//$rolUsuario=get_rol($_SESSION['usuario']);

		$conn=conectar_bd();

		if ($_GET['dato'] == 'FECHA_PENDIENTE_RA') {

			$fecha = $_GET['valor'];

			if ($fecha == '') {
				$tsql = "UPDATE INV_RA SET FECHA_ENTREGA = NULL WHERE id_FDTT='".$id."' ";

			} else {
				$tsql = "UPDATE INV_RA SET FECHA_ENTREGA = convert(datetime, '".$_GET['valor']."', 120) WHERE id_FDTT='".$id."' ";
			}
			

			$stmt = sqlsrv_query($conn, $tsql);

			print ("Fecha de Entrega RA Actualizada, id_fdtt: ".$id);

		}

		if ($_GET['dato'] == 'FECHA_PENDIENTE_RD_DISENO') {

			$fecha = $_GET['valor'];

			if ($fecha == '') {
				$tsql = "UPDATE INV_RD SET DIA_ENTREGA_DISENO = NULL WHERE id_FDTT='".$id."' ";

			} else {
				
				$dia = substr($fecha, 8, 2);
				$anio = substr($fecha, 2, 2);
				$timestamp = strtotime($fecha);
				$mesTxt = strftime("%b", $timestamp);
				$fechaFinal = $dia.'-'.$mesTxt.'-'.$anio;

				$tsql = "UPDATE INV_RD SET DIA_ENTREGA_DISENO = '".$fechaFinal."' WHERE id_FDTT='".$id."' ";
			}
			

			$stmt = sqlsrv_query($conn, $tsql);

			print ("Fecha de Entrega RD Diseño Actualizada, id_fdtt: ".$id);

		}

		if ($_GET['dato'] == 'FECHA_PENDIENTE_RD') {

			$fecha = $_GET['valor'];

			if ($fecha == '') {
				$tsql = "UPDATE INV_RD SET FECHA_ENTREGA = NULL WHERE id_FDTT='".$id."' ";

			} else {
				
				$dia = substr($fecha, 8, 2);
				$anio = substr($fecha, 0, 4);
				$timestamp = strtotime($fecha);
				$mesTxt = strftime("%b", $timestamp);
				$fechaFinal = $mesTxt.' '.$dia.' '.$anio.' 12:00AM';

				$tsql = "UPDATE INV_RD SET FECHA_ENTREGA = '".$fechaFinal."' WHERE id_FDTT='".$id."' ";
			}
			

			$stmt = sqlsrv_query($conn, $tsql);

			print ("Fecha de Entrega RD Actualizada, id_fdtt: ".$id);

		}	

		if ($_GET['dato'] == 'FECHA_PENDIENTE_ICX') {

			$fecha = $_GET['valor'];

			if ($fecha == '') {
				$tsql = "UPDATE INV_RD_ICX SET ICX_FECHA = NULL WHERE Id_GD='".$id."' ";

			} else {
				
				$dia = substr($fecha, 8, 2);
				$mes = substr($fecha, 5, 2);
				$anio = substr($fecha, 0, 4);
				$fechaFinal = $dia.'/'.$mes.'/'.$anio;

				$tsql = "UPDATE INV_RD_ICX SET ICX_FECHA = '".$fechaFinal."' WHERE Id_GD='".$id."' ";
			}
			

			$stmt = sqlsrv_query($conn, $tsql);

			print ("Fecha de Entrega RD Actualizada, Id_GD: ".$id);

		}	


		// Cerrar conexión
		if (isset($stmt)) {  
			sqlsrv_free_stmt( $stmt);						
			sqlsrv_close( $conn);
		}	
	} //Fin si el ID no viene informado
						
?>