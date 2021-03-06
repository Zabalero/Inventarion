<?php
function generarEnvioGAMMAPendientes($conn,$fechaEnvio,$empresa) {


	//PENDIENTES DE RECIBIR

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();	

	//RD DISEÑO
	$tsql = "SELECT Id_GD, Id_FDTT, PROVINCIA, CABECERA, ARBOL, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, 
											UUII_AI, GESTOR, EECC_CARGA_RD_DISENO, DIA_ENVIO_DISENO, DIA_ENTREGA_DISENO, INC_EECCRD_GRAL 
									From INV_VIEW_RD_TODO
									WHERE ((DIA_ENVIO_DISENO Is Not Null) AND (DIA_ENTREGA_DISENO Is Null)) AND EECC_CARGA_RD_DISENO = '$empresa'";

	$stmt = sqlsrv_query($conn,$tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta");
	}

	$rows = sqlsrv_has_rows( $stmt );


	// Create a first sheet, representing sales data
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id_GD');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Id_FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PROVINCIA');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'CABECERA');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ARBOL');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'ACT. JAZZTEL FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', 'ACT. JAZZTEL');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', 'ACT. TESA');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', 'ID_ZONA');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', 'UUII_AI');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', 'GESTOR');
	$objPHPExcel->getActiveSheet()->setCellValue('L1', 'EECC_CARGA_RD_DISENO');
	$objPHPExcel->getActiveSheet()->setCellValue('M1', 'F.ENVIO DIS.');
	$objPHPExcel->getActiveSheet()->setCellValue('N1', 'F.ENTREGA DIS.');
	$objPHPExcel->getActiveSheet()->setCellValue('O1', 'INC_EECC');

	if ($rows === true){		

		$i=2;

		while($row = sqlsrv_fetch_array($stmt)){

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['Id_GD']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row['Id_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row['PROVINCIA']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row['CABECERA']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['ARBOL']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['ACTUACION_JAZZTEL_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['ACTUACION_JAZZTEL']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['ACTUACION_TESA']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row['ID_ZONA']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['UUII_AI']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['GESTOR']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row['EECC_CARGA_RD_DISENO']);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, substr($row['FECHA_ENVIO'], 0, 11));
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row['DIA_ENVIO_DISENO']);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row['DIA_ENTREGA_DISENO']);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row['INC_EECCRD_GRAL']);		

			$i++;
		}				
			
	}

	sqlsrv_free_stmt( $stmt);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('RD_DISEÑO');

	//RD

	//Formato fecha envío RD
	$fecha = $fechaEnvio;
	$dia = substr($fecha, 8, 2);
	$anio = substr($fecha, 0, 4);
	$timestamp = strtotime($fecha);
	$mesTxt = strftime("%b", $timestamp);
	$fechaFinal = $mesTxt.' '.$dia.' '.$anio.' 12:00AM';	

	$tsql = "SELECT Id_GD,Id_FDTT, PROVINCIA, CABECERA, ARBOL, ACTUACION_JAZZTEL_FDTT, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, UUII_AI, GESTOR, 
									EECC_CARGA_RD, FECHA_ENVIO, FECHA_ENTREGA, INC_EECCRD_GRAL 
							From INV_VIEW_RD_TODO 
							WHERE ((FECHA_ENVIO Is Not Null) AND (FECHA_ENTREGA Is Null)) AND EECC_CARGA_RD = '$empresa'";

	$stmt = sqlsrv_query($conn,$tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta");
	}

	$rows = sqlsrv_has_rows( $stmt );

	$objPHPExcel->createSheet();
	
	$objPHPExcel->setActiveSheetIndex(1);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id_GD');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Id_FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PROVINCIA');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'CABECERA');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ARBOL');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'ACT. JAZZTEL FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', 'ACT. JAZZTEL');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', 'ACT. TESA');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', 'ID_ZONA');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', 'UUII_AI');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', 'GESTOR');
	$objPHPExcel->getActiveSheet()->setCellValue('L1', 'EECC_CARGA');
	$objPHPExcel->getActiveSheet()->setCellValue('M1', 'F.ENVIO');
	$objPHPExcel->getActiveSheet()->setCellValue('N1', 'F.ENTREGA');
	$objPHPExcel->getActiveSheet()->setCellValue('O1', 'INC_EECC');

	if ($rows === true){		

		$i=2;

		while($row = sqlsrv_fetch_array($stmt)){

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['Id_GD']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row['Id_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row['PROVINCIA']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row['CABECERA']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['ARBOL']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['ACTUACION_JAZZTEL_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['ACTUACION_JAZZTEL']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['ACTUACION_TESA']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row['ID_ZONA']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['UUII_AI']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['GESTOR']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row['EECC_CARGA_RD']);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, substr($row['FECHA_ENVIO'], 0, 11));
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row['FECHA_ENTREGA']);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row['INC_EECCRD_GRAL']);		

			$i++;
		}				
			
	}

	sqlsrv_free_stmt( $stmt);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('RD');

	//RA

	$tsql = "SELECT Id_GD,Id_FDTT, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, 
									FECHA_ENVIO, FECHA_ENTREGA, INC_EECC 
							FROM INV_RA
							LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
							LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
							 WHERE ((FECHA_ENVIO Is Not Null) AND (FECHA_ENTREGA Is Null)) AND EECC_CARGA_RA = '$empresa'";

	$stmt = sqlsrv_query($conn,$tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta");
	}

	$rows = sqlsrv_has_rows( $stmt );

	$objPHPExcel->createSheet();
	
	$objPHPExcel->setActiveSheetIndex(2);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id_GD');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Id_FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PROVINCIA');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'CABECERA');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ARBOL');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'NOMBRE_FICHERO');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', 'FASE');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', 'EECC_CARGA_RA');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', 'F.ENVIO');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', 'F.ENTREGA');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', 'INC_EECC');

	if ($rows === true){		

		$i=2;

		while($row = sqlsrv_fetch_array($stmt)){

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['Id_GD']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row['Id_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row['PROVINCIA']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row['CABECERA']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['ARBOL']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['NOMBRE_FICHERO']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['FASE']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['EECC_CARGA_RA']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, date_format($row['FECHA_ENVIO'], 'Y-m-d'));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['FECHA_ENTREGA']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['INC_EECC']);		

			$i++;
		}				
			
	}

	sqlsrv_free_stmt( $stmt);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('RA');

	// Redirect output to a client’s web browser (Excel5)
	$nombreFichero = "PENDIENTE_CARGA_GIS_".$empresa.date('Ymd').".xls";

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$nombreFichero.'"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

}

function generarEnvioGAMMAEntrega($conn,$fechaEnvio,$empresa) {




	//PENDIENTES DE ENVIAR

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();	

	//RD DISEÑO

	$fecha = $fechaEnvio;
	$dia = substr($fecha, 8, 2);
	$anio = substr($fecha, 2, 2);
	$timestamp = strtotime($fecha);
	$mesTxt = strftime("%b", $timestamp);
	$fechaFinal = $dia.'-'.$mesTxt.'-'.$anio;



	$tsql = "SELECT Id_GD,Id_FDTT, PROVINCIA, CABECERA, ARBOL, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, UUII_AI, GESTOR, EECC_CARGA_RD_DISENO, DIA_ENVIO_DISENO,
						DIA_ENTREGA_DISENO, INC_EECCRD_GRAL 
			From INV_VIEW_RD_TODO WHERE DIA_ENTREGA_DISENO Is Null AND DIA_ENVIO_DISENO = '$fechaFinal' AND EECC_CARGA_RD_DISENO = '$empresa'";

	$stmt = sqlsrv_query($conn,$tsql);

	if( $stmt === false ){
		die ($tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );


	// Create a first sheet, representing sales data
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id_GD');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Id_FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PROVINCIA');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'CABECERA');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ARBOL');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'ACT. JAZZTEL FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', 'ACT. JAZZTEL');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', 'ACT. TESA');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', 'ID_ZONA');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', 'UUII_AI');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', 'GESTOR');
	$objPHPExcel->getActiveSheet()->setCellValue('L1', 'EECC_CARGA_RD_DISENO');
	$objPHPExcel->getActiveSheet()->setCellValue('M1', 'F.ENVIO DIS.');
	$objPHPExcel->getActiveSheet()->setCellValue('N1', 'F.ENTREGA DIS.');
	$objPHPExcel->getActiveSheet()->setCellValue('O1', 'INC_EECC');

	if ($rows === true){		

		$i=2;

		while($row = sqlsrv_fetch_array($stmt)){

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['Id_GD']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row['Id_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row['PROVINCIA']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row['CABECERA']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['ARBOL']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['ACTUACION_JAZZTEL_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['ACTUACION_JAZZTEL']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['ACTUACION_TESA']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row['ID_ZONA']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['UUII_AI']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['GESTOR']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row['EECC_CARGA_RD_DISENO']);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, substr($row['FECHA_ENVIO'], 0, 11));
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row['DIA_ENVIO_DISENO']);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row['DIA_ENTREGA_DISENO']);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row['INC_EECCRD_GRAL']);		

			$i++;
		}				
			
	}

	sqlsrv_free_stmt( $stmt);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('RD_DISEÑO');

	//RD

	//Formato fecha envío RD
	$fecha = $fechaEnvio;
	$dia = substr($fecha, 8, 2);
	$anio = substr($fecha, 0, 4);
	$timestamp = strtotime($fecha);
	$mesTxt = strftime("%b", $timestamp);
	$fechaFinal = $mesTxt.' '.$dia.' '.$anio.' 12:00AM';	

	$tsql = "SELECT Id_GD,Id_FDTT, PROVINCIA, CABECERA, ARBOL, ACTUACION_JAZZTEL_FDTT, ACTUACION_JAZZTEL, ACTUACION_TESA, ID_ZONA, UUII_AI, GESTOR, 
									EECC_CARGA_RD, FECHA_ENVIO, FECHA_ENTREGA, INC_EECCRD_GRAL 
							From INV_VIEW_RD_TODO 
							WHERE ((FECHA_ENVIO = '$fechaFinal' ) AND (FECHA_ENTREGA Is Null)) AND EECC_CARGA_RD = '$empresa'";

	$stmt = sqlsrv_query($conn,$tsql);

	if( $stmt === false ){
		die ($tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );

	$objPHPExcel->createSheet();
	
	$objPHPExcel->setActiveSheetIndex(1);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id_GD');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Id_FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PROVINCIA');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'CABECERA');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ARBOL');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'ACT. JAZZTEL FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', 'ACT. JAZZTEL');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', 'ACT. TESA');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', 'ID_ZONA');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', 'UUII_AI');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', 'GESTOR');
	$objPHPExcel->getActiveSheet()->setCellValue('L1', 'EECC_CARGA');
	$objPHPExcel->getActiveSheet()->setCellValue('M1', 'F.ENVIO');
	$objPHPExcel->getActiveSheet()->setCellValue('N1', 'F.ENTREGA');
	$objPHPExcel->getActiveSheet()->setCellValue('O1', 'INC_EECC');

	if ($rows === true){		

		$i=2;

		while($row = sqlsrv_fetch_array($stmt)){

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['Id_GD']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row['Id_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row['PROVINCIA']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row['CABECERA']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['ARBOL']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['ACTUACION_JAZZTEL_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['ACTUACION_JAZZTEL']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['ACTUACION_TESA']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row['ID_ZONA']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['UUII_AI']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['GESTOR']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row['EECC_CARGA_RD']);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, substr($row['FECHA_ENVIO'], 0, 11));
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row['FECHA_ENTREGA']);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row['INC_EECCRD_GRAL']);		

			$i++;
		}				
			
	}

	sqlsrv_free_stmt( $stmt);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('RD');

	//RA

	//Formato fecha envío RD
	$fecha = $fechaEnvio;

	$tsql = "SELECT Id_GD,Id_FDTT, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, 
									FECHA_ENVIO, FECHA_ENTREGA, INC_EECC 
							FROM INV_RA
							LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
							LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
							 WHERE ((FECHA_ENVIO = convert(datetime, '".$fecha."', 120) ) AND (FECHA_ENTREGA Is Null)) AND EECC_CARGA_RA = '$empresa'";

	$stmt = sqlsrv_query($conn,$tsql);

	if( $stmt === false ){
		die ($tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );

	$objPHPExcel->createSheet();
	
	$objPHPExcel->setActiveSheetIndex(2);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id_GD');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Id_FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PROVINCIA');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'CABECERA');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ARBOL');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'NOMBRE_FICHERO');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', 'FASE');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', 'EECC_CARGA_RA');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', 'F.ENVIO');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', 'F.ENTREGA');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', 'INC_EECC');

	if ($rows === true){		

		$i=2;

		while($row = sqlsrv_fetch_array($stmt)){

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['Id_GD']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row['Id_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row['PROVINCIA']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row['CABECERA']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['ARBOL']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['NOMBRE_FICHERO']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['FASE']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['EECC_CARGA_RA']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, date_format($row['FECHA_ENVIO'], 'Y-m-d'));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['FECHA_ENTREGA']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['INC_EECC']);		

			$i++;
		}				
			
	}

	sqlsrv_free_stmt( $stmt);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('RA');


	// Redirect output to a client’s web browser (Excel5)
	$nombreFichero = "ENCARGO_".$empresa.'_'.date('Ymd').".xls";

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$nombreFichero.'"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

}

function generarFicheroGAMMA($conn,$fechaEnvio,$empresa) {


	//PENDIENTES DE RECIBIR

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();	

	//RA

	$tsql = "SELECT Id_GD,Id_FDTT, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, 
									FECHA_ENVIO, FECHA_ENTREGA, INC_EECC 
							FROM INV_RA
							LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
							LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
							 WHERE ((FECHA_ENVIO Is Not Null) AND (FECHA_ENTREGA Is Null))";
        
        if (isset($empresa) && $empresa != "") {
					$tsql = $tsql . " AND EECC_CARGA_RA = '$empresa'";				
				}							
        
       	$stmt = sqlsrv_query($conn,$tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta");
	}

	$rows = sqlsrv_has_rows( $stmt );

	$objPHPExcel->createSheet();
	
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id_GD');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Id_FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PROVINCIA');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'CABECERA');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ARBOL');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'NOMBRE_FICHERO');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', 'FASE');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', 'EECC_CARGA_RA');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', 'F.ENVIO');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', 'F.ENTREGA');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', 'INC_EECC');

	if ($rows === true){		

		$i=2;

		while($row = sqlsrv_fetch_array($stmt)){

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['Id_GD']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row['Id_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row['PROVINCIA']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row['CABECERA']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['ARBOL']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['NOMBRE_FICHERO']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['FASE']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['EECC_CARGA_RA']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, date_format($row['FECHA_ENVIO'], 'Y-m-d'));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['FECHA_ENTREGA']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['INC_EECC']);		

			$i++;
		}				
			
	}

	sqlsrv_free_stmt( $stmt);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('PEND_RECIBIR_RA');


	//PENDIENTES DE ENTREGAR

	//RA

	//Formato fecha envío
	$fecha = $fechaEnvio;



	$tsql = "SELECT Id_GD, Id_FDTT, PR.Descripcion AS PROVINCIA, CA.Descripcion AS CABECERA, ARBOL, NOMBRE_FICHERO, FASE, EECC_CARGA_RA, RESPONSABLE_CARGA,
									FECHA_ENVIO, FECHA_ENTREGA, INC_EECC
						FROM INV_RA
						LEFT JOIN inv_cabeceras AS CA ON CA.Cod_Cabecera = INV_RA.ID_CABECERA
						LEFT JOIN inv_provincias AS PR ON PR.Cod_Provincia = CA.Cod_Provincia
						WHERE ((Id_FDTT Is Not Null)  AND (ESTADO_RA_FDTT = 'RA Disponible' OR ESTADO_RA_FDTT = 'RA FINALIZADO') AND (Estado_carga_GIS <> 'AS-BUILT') AND (Fx_fin_construccion<>'') 
								AND (PL4 = 'Aprobado' OR PL4 = 'Aprobado con reparos') AND (SUC<>'Rechazado' AND SUC<>'SIN SUBIR' AND SUC<>'') AND (FECHA_ENVIO Is Null) )";



	$stmt = sqlsrv_query($conn,$tsql);

	if( $stmt === false ){
		die ($tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );

	$objPHPExcel->createSheet();
	
	$objPHPExcel->setActiveSheetIndex(1);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id_GD');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Id_FDTT');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'PROVINCIA');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'CABECERA');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', 'ARBOL');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'NOMBRE_FICHERO');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', 'FASE');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', 'RESPONSABLE_CARGA');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', 'F.ENVIO');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', 'F.ENTREGA');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', 'INC_EECC');

	if ($rows === true){		

		$i=2;

		while($row = sqlsrv_fetch_array($stmt)){

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['Id_GD']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row['Id_FDTT']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row['PROVINCIA']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row['CABECERA']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['ARBOL']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['NOMBRE_FICHERO']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['FASE']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['RESPONSABLE_CARGA']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, date_format($row['FECHA_ENVIO'], 'Y-m-d'));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['FECHA_ENTREGA']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['INC_EECC']);	

			$i++;
		}				
			
	}

	sqlsrv_free_stmt( $stmt);

	// Rename sheet
	$objPHPExcel->getActiveSheet()->setTitle('PEND_ENTREGAR_RA');
        
        //Borrar pestaña Worksheet
        $objPHPExcel->setActiveSheetIndexByName('Worksheet');
        $sheetIndex = $objPHPExcel->getActiveSheetIndex();
        $objPHPExcel->removeSheetByIndex($sheetIndex);


	// Redirect output to a client’s web browser (Excel5)
	$nombreFichero = "ENCARGO_GAMMA_".$empresa.date('Ymd').".xls";

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$nombreFichero.'"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

}
