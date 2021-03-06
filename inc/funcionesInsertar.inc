<?php

function insertarTarea($conn, &$nuevoID, &$subactividad){

	$nuevoID = 0;
	$newIDtarea = 0;

	if ($_POST['id_actividad'] == '') {
		return "Actividad Obligatoria";
	} else {
		$id_actividad = $_POST['id_actividad'];
	}

	if (!isset($_POST['subactividad']) || $_POST['subactividad'] == '') {
		$subactividad = subactividadTarea($_POST['motivoBloq']);
		if ($subactividad == '') {
			return "Actividad o Motivo de Bloqueo Obligatorio";
		}
	} else {
		$subactividad = $_POST['subactividad'];
	}

	if ($_POST['GRUPO'] == '') {
		return "Grupo Obligatorio";
	}	

	if ($_POST['codCab'] == '') {
		return "Cabecera Obligatoria";
	}		

	if (isset($_FILES['adjunto']) && $_FILES['adjunto']['tmp_name'] != '') {	
		//  5MB maximum file size 
		$MAXIMUM_FILESIZE = 5 * 1024 * 1024; 
		
		//  Valid file extensions (images, word, excel, powerpoint) 
		$rEFileTypes = 
		  "/^\.(jpg|jpeg|gif|png|doc|docx|txt|rtf|pdf|xls|xlsx| 
		        ppt|pptx|ods|csv|zip){1}$/i"; 
		
		$dir_base = "upload/"; 

		$isFile = is_uploaded_file($_FILES['adjunto']['tmp_name']);

		 
		if ($isFile) {    //  do we have a file?
			$safe_filename=replace_specials_characters($_FILES['adjunto']['name']);
			 
    		if ($_FILES['adjunto']['size'] <= $MAXIMUM_FILESIZE && preg_match($rEFileTypes, strrchr($safe_filename, '.'))){ 

			} else {
				return "Error. Formato no permitido o se ha excedido el l&iacute;mite de Kilobytes";
			}		
		} else {
			return "Error. Formato no permitido o se ha excedido el l&iacute;mite de Kilobytes";
		}
	}		

	$id_usuario = $_POST['ID_USUORIGEN'];
	$Cod_Cabecera = $_POST['codCab'];
	$id_Actuacion = $_POST['idAct'];

	if (is_numeric($_POST['INCIDENCIA']) || $_POST['INCIDENCIA'] == '' ) {
		$ticket_remedy = $_POST['INCIDENCIA'];
	} else {
       		return "La incidencia Remedy debe ser numérica";

	}

	
	if ((ctype_alnum($_POST['TICKET_OCEANE']) && strlen($_POST['TICKET_OCEANE'])==10) || $_POST['TICKET_OCEANE'] == '') {	
        	$ticket_oceane = $_POST['TICKET_OCEANE'];
	} else {
		return "Ticket Oceane con formato erróneo"; //AAMMXXXXXX

	}
        
        if ((ctype_alnum($_POST['SUC']) && strlen($_POST['SUC'])==21) || $_POST['SUC'] == '') {	
		$suc = $_POST['SUC'];
	} else {
		return "SUC con formato erróneo"; //AAMMXXXXXX

	}

	
	if (preg_match('/[A-Z]{2}[0-9]{10}/', $_POST['TP']) || $_POST['TP'] == '') {	
		$ticket_tp = $_POST['TP'];
	} else {
		return "TP con formato erróneo"; //PPAAAAMMYYYY

	}	

	$cto_nueva = $_POST['CTO_NUEVA'];

	$bloqueo = NULL;
	$desbloqueo = NULL;
	$prioridad = NULL;
	$tratamientoCTOS = NULL;

	$actividadSubactividad = detallesActividadSubactividad ($id_actividad, $subactividad, $prioridad, $bloqueo, $desbloqueo, $tratamientoCTOS);

	if ($actividadSubactividad == '') {
		if ($prioridad == NULL) {
			return "No se puede asignar prioridad a la tarea";
		}

		if ($tratamientoCTOS == 'O' && $_POST['MARCAR'] == '') { //CTO obligatoria
			return "Seleccionar CTO implicada";
		}

	} else {
		return $actividadSubactividad;
	}

	$comentarios = $_POST['COMENTARIOS'];    
	$grupo = $_POST['GRUPO'];
	$refTecn = NULL;

	$referencia = round(microtime(true) * 1000); 

	if ($_POST['refAsoc'] == '') {
		$refAsociada = $referencia; 
	} else {
		$refAsociada = $_POST['refAsoc']; 
		$tsql = "SELECT TOP 1 idTecn FROM INV_TBTAREAS WHERE REF='".$refAsociada."' ORDER BY FECHA_REGISTRO DESC";
		$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);
			
		$rows = sqlsrv_has_rows( $stmt );

		if ($rows === true){						
			while($row = sqlsrv_fetch_array($stmt)) {
				$refTecn=$row["idTecn"];									
			}
		}
		sqlsrv_free_stmt( $stmt);
	}

	//RECOGER EEMM DE LA CABECERA PORQUE PUEDE CAMBIAR
	$eemm = '';	
	if ($Cod_Cabecera <> '') {
		$tsql = "SELECT EEMM FROM INV_CABECERAS WHERE Cod_Cabecera='".$Cod_Cabecera."' ";
		$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);
			
		$rows = sqlsrv_has_rows( $stmt );

		if ($rows === true){						
			while($row = sqlsrv_fetch_array($stmt)) {
				$eemm=$row["EEMM"];									
			}
		}
		sqlsrv_free_stmt( $stmt);			

	}

	//recogemos la huella

        $huella = $_POST['HUELLA'];	
        //Si la subactividad es Revisión Puentes Central hay que buscar el campo HUELLA en la tabla de cabeceras
        if (($id_actividad==='1') && ($subactividad==='4')){
            $tsql = "SELECT HUELLA FROM INV_CABECERAS WHERE Cod_Cabecera='".$Cod_Cabecera."' ";
		$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);
			
		$rows = sqlsrv_has_rows( $stmt );

		if ($rows === true){						
			while($row = sqlsrv_fetch_array($stmt)) {
				$huella=$row["HUELLA"];									
			}
		}
		sqlsrv_free_stmt( $stmt);		

        }
        
        //FICHERO????
	$tipo_entrada = '1';	//1-WEB, 2-REMEDY, 3-CARGAS MASIVAS

	// INICIO TRANSACCIÓN
	if ( sqlsrv_begin_transaction( $conn ) === false ) {
		 return "Error al actualizar la tarea sqlsrv_begin_transaction";
	} else {

		//Se inserta la tarea con estado pendiente

		if ($refTecn == '') {
			$tsqlFunc = "INSERT INV_TBTAREAS 
							(idEst, id_Subactividad, idUsuOrigen,REF, FECHA_REGISTRO, INCIDENCIA, COMENTARIOS, PRIORIDAD, GRUPO, cod_cabecera, id_Actuacion, TICKET_OCEANE, TP, EEMM, ID_TIPO_ENTRADA, REF_ASOCIADA, CTO_NUEVA, SUC,HUELLA)	VALUES 
							('1', '$subactividad', '$id_usuario', '$referencia', getdate(), '$ticket_remedy', '$comentarios', '$prioridad', '$grupo', '$Cod_Cabecera', '$id_Actuacion', '$ticket_oceane', '$ticket_tp', '$eemm', '$tipo_entrada', '$refAsociada', '$cto_nueva', '$suc','$huella');SELECT SCOPE_IDENTITY() AS theID;";
		} else {
			$tsqlFunc = "INSERT INV_TBTAREAS 
							(idEst, idTecn, id_Subactividad, idUsuOrigen,REF, FECHA_REGISTRO, INCIDENCIA, COMENTARIOS, PRIORIDAD, GRUPO, cod_cabecera, id_Actuacion, TICKET_OCEANE, TP, EEMM, ID_TIPO_ENTRADA, REF_ASOCIADA, CTO_NUEVA, SUC,HUELLA)	VALUES 
							('1', '$refTecn', '$subactividad', '$id_usuario', '$referencia', getdate(), '$ticket_remedy', '$comentarios', '$prioridad', '$grupo', '$Cod_Cabecera', '$id_Actuacion', '$ticket_oceane', '$ticket_tp', '$eemm', '$tipo_entrada', '$refAsociada', '$cto_nueva', '$suc','$huella');SELECT SCOPE_IDENTITY() AS theID;";
		}

		$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);   	

		if( $stmtFunc === false ){
			die ("Error al ejecutar consulta: ".$tsqlFunc);
		}

		sqlsrv_next_result($stmtFunc);
		sqlsrv_fetch($stmtFunc);
		$newIDtarea=sqlsrv_get_field($stmtFunc, 0);		

		sqlsrv_free_stmt($stmtFunc);		

		//CTOS ASOCIADAS
		if ($_POST['MARCAR'] == ''){	
			$marcar ='';
		}
		else{
			$marcar = $_POST['MARCAR'];
			$nfilasMarc = count ($marcar);
		}

		$idgd = $_POST['idGD'];
		
		//Relacionar las CTOS que tenga asignadas por pantalla		
		if (!empty($marcar)) {
			for ($i=0; $i<$nfilasMarc; $i++) {
				if ($idgd=="" or is_null($idgd)){ 
					$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (COD_CTO, id,ID_ACTUACION) VALUES ('".$marcar[$i]."', '".$newIDtarea."','".$id_Actuacion."')";   		
				} else {
					$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (ID_GESTOR, COD_CTO, id,ID_ACTUACION) VALUES ('".$idgd."', '".$marcar[$i]."', '".$newIDtarea."','".$id_Actuacion."')";   		
				}
				$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
			}
		}	

		//INSERTAR en tbTareas_bloqueos		
		if ($bloqueo){	

		    if (isset($_POST['motivoBloq'])) {
		    	$motivoBloqueo=$_POST['motivoBloq'];
		    } else {
		    	$motivoBloqueo=$_POST['motivoBloqueo'];
		    }	
			$tsql= "INSERT INTO INV_tbTareas_Bloqueos (id_Tarea, Tipo_Afectacion, id_Motivo) VALUES ($newIDtarea, 1, $motivoBloqueo)";   
			$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);
			sqlsrv_free_stmt( $stmt);	

			//GESCALES ASOCIADOS
			if ($_POST['MARCARGESC'] == ''){	
				$marcarGesc ='';
			}
			else{
				$marcarGesc = $_POST['MARCARGESC'];
				$nfilasMarcGesc = count ($marcarGesc);
			}

			//Relacionar las CTOS que tenga asignadas por pantalla		
			if (!empty($marcarGesc)) {
				for ($i=0; $i<$nfilasMarcGesc; $i++) {
					$codigos = explode("-",$marcarGesc[$i]);
					$codigo_cto = $codigos[0];
					$codigo_gescal = $codigos[1];
					if ($idgd=="" or is_null($idgd)){ 
						$tsqlFunc= "INSERT INTO INV_tbBloqueos_Gescales (COD_CTO, id, COD_GESCAL, id_Actuacion) VALUES ('".$codigo_cto."', '".$newIDtarea."','".$codigo_gescal."', '".$id_Actuacion."')";   		
					} else {
						$tsqlFunc= "INSERT INTO INV_tbBloqueos_Gescales (ID_GESTOR, COD_CTO, id, COD_GESCAL, id_Actuacion) VALUES ('".$idgd."', '".$codigo_cto."', '".$newIDtarea."','".$codigo_gescal."', '".$id_Actuacion."')";   		
					}
					$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);

				}
			}			
		}
			
		//INSERTAR en tbTareas_Desbloqueos
		if ($desbloqueo){			
			$tsql= "INSERT INTO INV_tbTareas_Desbloqueos (id_Tarea, Tipo_Afectacion) VALUES ($newIDtarea, 1)";   
			$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);
			sqlsrv_free_stmt( $stmt);

			//GESCALES ASOCIADOS
			if ($_POST['MARCARGESC'] == ''){	
				$marcarGesc ='';
			}
			else{
				$marcarGesc = $_POST['MARCARGESC'];
				$nfilasMarcGesc = count ($marcarGesc);
			}

			//Relacionar las CTOS que tenga asignadas por pantalla		
			if (!empty($marcarGesc)) {
				for ($i=0; $i<$nfilasMarcGesc; $i++) {
					$codigos = explode("-",$marcarGesc[$i]);
					$codigo_cto = $codigos[0];
					$codigo_gescal = $codigos[1];
					if ($idgd=="" or is_null($idgd)){ 
						$tsqlFunc= "INSERT INTO INV_tbDesbloqueos_Gescales (COD_CTO, id, COD_GESCAL, id_Actuacion) VALUES ('".$codigo_cto."', '".$newIDtarea."','".$codigo_gescal."', '".$id_Actuacion."')";   		
					} else {
						$tsqlFunc= "INSERT INTO INV_tbDesbloqueos_Gescales (ID_GESTOR, COD_CTO, id, COD_GESCAL, id_Actuacion) VALUES ('".$idgd."', '".$codigo_cto."', '".$newIDtarea."','".$codigo_gescal."', '".$id_Actuacion."')";   		
					}
						
					$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
					//echo  "Alta INV_tbBloqueos_Gescales: ".$tsqlFunc;

				}
			}							
		}

		//INSERTAR FICHERO
		if (isset($_FILES['adjunto']) && $_FILES['adjunto']['tmp_name'] != '') {	
			$isMove = move_uploaded_file ($_FILES['adjunto']['tmp_name'], $dir_base.$newIDtarea.'-'.$refAsociada.'-'.$safe_filename);
			if ($isMove){
				$nombreArchivo = $newIDtarea.'-'.$refAsociada.'-'.$safe_filename;
				$tsqlFunc= "INSERT INTO INV_TBARCHIVOS (idTarea, archivo) VALUES ('".$newIDtarea."', '".$nombreArchivo."')"; 			
				$stmtFunc= sqlsrv_query( $conn, $tsqlFunc);
				echo  "Alta Archivo: ".$tsqlFunc;
				sqlsrv_free_stmt( $stmtFunc);	
			}	
		}	

    	// CONSOLIDAR TODOS LOS CAMBIOS TRANSACCIÓN
    	sqlsrv_commit( $conn );		
    	$nuevoID = $newIDtarea;													
		return "Alta realizada correctamente, REFERENCIA: ".$refAsociada." - ".$nuevoID;	
	}

}