<?php

function mes($index)
{
    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    return $meses[$index];
}	

function facturarGAMMA($conn,$fechaEnvio,$empresaEnvio,$marcarFacturaRd,$marcarFacturaRdAct,$marcarFacturaRa, $marcarFacturaRaAct) {

	$mes = date('m', strtotime($fechaEnvio));
	$anio = date('y', strtotime($fechaEnvio));
	$fechaFacturacion = date('Y-m-d 00:00:00.000', strtotime($fechaEnvio));
	//$mes = mes(strftime("%m")-1); //Mes actual
	//$anio = strftime("%Y"); //Año Actual

	$nfilasFacturaRd = count ($marcarFacturaRd);
	$nfilasFacturaRa = count ($marcarFacturaRa);
	$nfilasFacturaRdAct = count ($marcarFacturaRdAct);
	$nfilasFacturaRaAct = count ($marcarFacturaRaAct);


	if ($nfilasFacturaRa>0){

		for ($i=0; $i<$nfilasFacturaRa; $i++) {
			
			//Actualiza la tabla RD marcando los diseños que vamos a enviar a GAMMA
			$tsql2 = "UPDATE INV_RA SET FECHA_FACTURACION = convert(datetime, '".$fechaFacturacion."', 120) WHERE ID_FDTT = '".$marcarFacturaRa[$i]."' ";
			
			$stmt2 = sqlsrv_query( $conn, $tsql2);
							
			sqlsrv_free_stmt( $stmt2);						

		}

	}		

	if ($nfilasFacturaRd>0){

		for ($i=0; $i<$nfilasFacturaRd; $i++) {
			
			//Actualiza la tabla RD marcando los diseños que vamos a enviar a GAMMA
			$tsql2 = "UPDATE INV_RD SET FECHA_FACTURACION = convert(datetime, '".$fechaFacturacion."', 120) WHERE ID_FDTT = '".$marcarFacturaRd[$i]."' ";

			$stmt2 = sqlsrv_query( $conn, $tsql2);
							
			sqlsrv_free_stmt( $stmt2);						

		}

	}						

	if ($nfilasFacturaRaAct>0){


		for ($i=0; $i<$nfilasFacturaRaAct; $i++) {
			
			//Actualiza la tabla RD marcando los diseños que vamos a enviar a GAMMA
			$tsql2 = "UPDATE INV_RA SET FECHA_FACTURACION = convert(datetime, '".$fechaFacturacion."', 120) WHERE ID_FDTT = '".$marcarFacturaRaAct[$i]."' ";

			$stmt2 = sqlsrv_query( $conn, $tsql2);
							
			sqlsrv_free_stmt( $stmt2);						

		}

	}		

	if ($nfilasFacturaRdAct>0){

		for ($i=0; $i<$nfilasFacturaRdAct; $i++) {
			
			//Actualiza la tabla RD marcando los diseños que vamos a enviar a GAMMA
			$tsql2 = "UPDATE INV_RD SET FECHA_FACTURACION = convert(datetime, '".$fechaFacturacion."', 120) WHERE ID_FDTT = '".$marcarFacturaRdAct[$i]."' ";

			$stmt2 = sqlsrv_query( $conn, $tsql2);
							
			sqlsrv_free_stmt( $stmt2);						

		}

	}										

	//ENVIO DE MAIL	 
	include_once( './Classes/class.phpmailer.php' ); //Importamos PHPMailer
	$mail = new PHPMailer(); //Creamos un objeto

	$mailUsu='maiteben@gmail.com'; 	
	$nombreUsu='Maite Benito'; 	

	$mailOtro='maria.benito@ext.jazztel.com'; 	
	$nombreOtro='Maite Benito Jazztel'; 	

	$mail->AddAddress( $mailUsu, $nombreUsu );
	$mail->AddCC( $mailOtro, $nombreOtro );

	$cuerpo='<html>
			<head>
			</head>
			<body style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12px">
		
			<p>Hola,</p>

			<p>Tras realizar las comprobaciones pertinentes sobre la facturación del me de Febrero damos el OK con un total de: </p>';	
	
	$cuerpo = $cuerpo.'<p><span style="font-style: italic">TABLA RD - '.$mes.' - AS_BUILT: </span><span style="font-weight: bold">'.$nfilasFacturaRd.'</span></p>';

	$cuerpo = $cuerpo.'<p><span style="font-style: italic">TABLA RA - '.$mes.' - AS_BUILT: </span><span style="font-weight: bold">'.$nfilasFacturaRa.'</span></p>';

	$cuerpo = $cuerpo.'<p>Un saludo</p>';

	//cabecera
	$cabeceraMail='facturación : '.$mes.' '.$anio.' - AB Gamma Solutions';	

	$mail->Subject = $cabeceraMail; 	

	$mail->SetFrom( $mailOtro, $nombreOtro );

	$mail->AddReplyTo( $mailOtro, $nombreOtro );

	$mail->Body = $cuerpo; 

	$mail->IsHTML(true);
	$mail->CharSet = 'UTF-8';
	
	$mail->Send(); //Enviamos el email
	
	
	echo "<script>alert('Mail enviado correctamente')</script>";

}



