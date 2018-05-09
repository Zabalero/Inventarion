<?php
	error_reporting(E_ALL);
	set_time_limit(0);

	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
	require "inc/funcionesImportar.inc";
	require "inc/funcionesFacturar.inc";
	require_once "PHPExcel_1.8.0/Classes/PHPExcel.php";
	require_once "PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php";

	//Inicializa las variables utilizadas en el formulario
 
    $NumeroIncidencia = "";  
    $TomadaPorGrupo = "";  
    $Grupo = ""; 
    $GrupoEscalada = "";
    $FHEscaladoTerceros = "";
    $FEC_Fecha_Cierre = "";
    $CAR_FTTH_NumeroCTO = "";
    $SolucionDetallada = "";
    $Estado = "";
    $ServicioI = "";
    $TipoI = "";
    $DescripcionI = "";
    $SintomaI = "";
    $Cliente = "";
    $ReferenciaExterna = "";
    $ViaEntradaTicket = "";


    $ficheroFacturacion = "";

    //FIN Inicializa las variables utilizadas en el formulario

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	$safe_filename	=  'upload/Remedy/Incidencias_Remedy2.xls';

	$objPHPExcel = PHPExcel_IOFactory::load($safe_filename); 

		
	//Asigno la hoja de calculo activa
	$objPHPExcel->setActiveSheetIndex(0);

	//Obtengo el numero de filas del archivo
	$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	
	$tsql= "DELETE FROM INCIDENCIAS_REMEDY";
	$stmt = sqlsrv_query( $conn, $tsql);	

	if ($numRows > 0) {
		echo "numRows: ".$numRows.'<br>';
		for ($i = 2; $i <= $numRows; $i++) {

		    $NumeroIncidencia = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getFormattedValue();
		    $TomadaPorGrupo = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getFormattedValue();  
		    $Grupo = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getFormattedValue();
		    $GrupoEscalada = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getFormattedValue();
		    $FHEscaladoTerceros = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getFormattedValue();

		    
		    $FEC_Fecha_Cierre = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getFormattedValue();
		    $CAR_FTTH_NumeroCTO = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getFormattedValue();
		    $SolucionDetallada = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getFormattedValue();
		    $Estado = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getFormattedValue();
		    $ServicioI = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getFormattedValue();
		    $TipoI = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getFormattedValue();
		    $DescripcionI = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getFormattedValue();
		    $SintomaI = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getFormattedValue();
		    $Cliente = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getFormattedValue();
		    $ReferenciaExterna = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getFormattedValue();
		    $ViaEntradaTicket = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getFormattedValue();
		    
                   $tsql= "INSERT INTO INCIDENCIAS_REMEDY (NumeroIncidencia, TomadaPorGrupo,Grupo,GrupoEscalada,CAR_FTTH_NumeroCTO, 
		    						SolucionDetallada,Estado,ServicioI, TipoI,DescripcionI,SintomaI,Cliente, ReferenciaExterna,ViaEntradaTicket, FHEscaladoTerceros,FEC_Fecha_Cierre)
		    				VALUES ('".$NumeroIncidencia."', '".iconv('','UTF-8',utf8_decode($TomadaPorGrupo))."','".iconv('','UTF-8',utf8_decode($Grupo))."', 
		    						'".iconv('','UTF-8',utf8_decode($GrupoEscalada))."','".iconv('','UTF-8',$CAR_FTTH_NumeroCTO)."',
		    						'".iconv('','UTF-8',utf8_decode($SolucionDetallada))."','".iconv('','UTF-8',utf8_decode($Estado))."', '".iconv('','UTF-8',utf8_decode($ServicioI))."',
		    						'".iconv('','UTF-8',utf8_decode($TipoI))."', '".iconv('','UTF-8',utf8_decode($DescripcionI))."','".iconv('','UTF-8',utf8_decode($SintomaI))."', '".iconv('','UTF-8',utf8_decode($Cliente))."','".iconv('','UTF-8',utf8_decode($ReferenciaExterna))."', 
		    						'".iconv('','UTF-8',utf8_decode($ViaEntradaTicket))."'";	
			
                        
                        if (empty($FHEscaladoTerceros)) {
				$tsql = $tsql.",NULL";
			} else {

			        $timestamp = PHPExcel_Shared_Date::ExcelToPHP($FHEscaladoTerceros);  
                                //$FEC_Fecha_EscaladoTerceros = date("d/m/Y H:i:s.000",$timestamp);//para desarrollo 
                                $FEC_Fecha_EscaladoTerceros = date("Y-m-d H:i:s",$timestamp); //para produccion
                                $tsql = $tsql.",'".$FEC_Fecha_EscaladoTerceros."'";
                          
			}		    	    

			if (empty($FEC_Fecha_Cierre)) {
				$tsql = $tsql.",NULL)";
			} else {
				
                                $timestamp2 = PHPExcel_Shared_Date::ExcelToPHP($FEC_Fecha_Cierre);  
                                //$FEC_Fecha_Cierre = date("d/m/Y H:i:s.000",$timestamp2);//para desarrollo
                                $FEC_Fecha_Cierre = date("Y-m-d H:i:s",$timestamp2); //para produccion
				$tsql = $tsql.",'".$FEC_Fecha_Cierre."')";
			}	
                    
                          
		    $stmt = sqlsrv_query( $conn, $tsql);

			if( $stmt === false ) {
			    if( ($errors = sqlsrv_errors() ) != null) {
			        foreach( $errors as $error ) {
			            echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
			            echo "code: ".$error[ 'code']."<br />";
			            echo "message: ".$error[ 'message']."<br />";
			            echo "Consulta: ".$tsql."<br />";
			        }
			    }
			}

		}

		unlink($safe_filename);
		
		header('Location: carga_Remedy.php');

	} 

?>
			<!-- start: Content -->
