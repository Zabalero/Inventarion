<?php
function subirArchivo($conn, $id) {

	if ($_FILES["adjunto"]["error"] > 0){
		return "No se ha seleccionado correctamente el archivo";
	} else {
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
				$tsqlFunc = "DELETE FROM INV_TBARCHIVOS WHERE idTarea = $id";
				$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
				sqlsrv_free_stmt( $stmtFunc);	  
				$isMove = move_uploaded_file ($_FILES['adjunto']['tmp_name'], $dir_base.$id.'-'.$_POST['ref'].'-'.$safe_filename);
				if ($isMove){
					$nombreArchivo=$id.'-'.$_POST['ref'].'-'.$safe_filename;
					$tsql9= "INSERT INTO INV_TBARCHIVOS (idTarea, archivo) VALUES (".$id.", '".$nombreArchivo."')"; 			
					$stmt9= sqlsrv_query( $conn, $tsql9);
					sqlsrv_free_stmt( $stmt9);	
					return "El archivo se ha insertado correctamente";
				} else {
					return "Ha ocurrido un error al insertar el archivo";
				}	
			} else {
				return "Error. Formato no permitido o se ha excedido el l&iacute;mite de Kilobytes";
			}		
		} else {
			return "Error. Formato no permitido o se ha excedido el l&iacute;mite de Kilobytes";
		}
	}	

}

function modificarTarea($conn, $id, $comentarios=""){

        //echo $id;exit();
	if ($id != '') {


//cogemos la actuacion y sacamos el ID_ACTUACION
		$actJazz=$_POST['actJazz'];



		//return $actJazz;

		$tsqlFunc0 = "SELECT top 1 ID_ACTUACION
				FROM INV_actuaciones where ACT_JAZZTEL= '".$actJazz."'"; 

				$resultadoFunc0  = sqlsrv_query($conn, $tsqlFunc0);
		
				if( $resultadoFunc0  === false ) {
	  					die( print_r( sqlsrv_errors(), true));
					} else {
							$id_actuacion = sqlsrv_fetch_array($resultadoFunc0 );
		}	

					sqlsrv_free_stmt($resultadoFunc0);	

				//	return $id_actuacion['ID_ACTUACION'];

	//actualizamos la id_actuacion de tbareas

		$tsqlFunc1 = "UPDATE inv_tbtareas  set id_Actuacion=".$id_actuacion['ID_ACTUACION']."  WHERE ID =$id";

					//return $tsqlFunc1;
			$resultadoFunc1 = sqlsrv_query($conn, $tsqlFunc1);

			sqlsrv_free_stmt($resultadoFunc1);	
///////////////
		
	 	$tsqlFunc = "SELECT *
				FROM INV_TBTAREAS
				WHERE ID = '$id'";

		$resultadoFunc = sqlsrv_query($conn, $tsqlFunc);

		if( $resultadoFunc === false ) {
	    	die( print_r( sqlsrv_errors(), true));
		} else {
			$registroFunc = sqlsrv_fetch_array($resultadoFunc);
		}	

		sqlsrv_free_stmt($resultadoFunc);	

		if (($registroFunc['idEst'] != '4') && ($registroFunc['idEst'] != '1')) {

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
				//echo ("Entra en else"); exit();
                                return "SUC con formato erróneo"; //AAMMXXXXXX

                        }
                        
                        $ticket_escalado = $_POST['TICKET_ESCALADO'];
			/*if (preg_match('/[A-Z]{16}[0-9]{16}/', $_POST['TICKET_ESCALADO']) || $_POST['TICKET_ESCALADO'] == '') {	
				$ticket_escalado = $_POST['TICKET_ESCALADO'];
			} else {
				return "escalado a terceros:texto muy largo"; //tamao 16

			}*/
			

			
			if (preg_match('/[A-Z]{2}[0-9]{10}/', $_POST['TP']) || $_POST['TP'] == '') {	
				$ticket_tp = $_POST['TP'];
                               
			} else {
				return "TP con formato erróneo"; //PPAAAAMMYYYY

			}	
                        //Validar que si el estado está en construcción, el campo Ticket Remedy tiene que estar informado
                        if ($registroFunc['idEst'] == '8'){
                            if ($_POST['INCIDENCIA'] == ''){
                                return "Si está en estado Construcción debe informar el campo Ticket Remedy"; 
                            }
                        }

			// INICIO TRANSACCIÓN
			if ( sqlsrv_begin_transaction( $conn ) === false ) {
				 return "Error al actualizar la tarea sqlsrv_begin_transaction";
			} else {			
   		
				$tsqlFunc = "UPDATE INV_TBTAREAS SET COMENTARIOS = '".$comentarios."', COMENTARIOS2 = '".$_POST['COMENTARIOS2']."', INCIDENCIA = '".$_POST['INCIDENCIA'].
				"', TICKET_OCEANE = '".$_POST['TICKET_OCEANE']."', SUC = '".$_POST['SUC']."', ESCALADO = '".$_POST['TICKET_ESCALADO']."',  TP = '".$_POST['TP'].
				"', CTO_NUEVA = '".$_POST['CTO_NUEVA']."', id_Subactividad = '".$subactividad."', PRIORIDAD ='".$prioridad."' WHERE id = '".$id."' ";

                                //echo $tsqlFunc;exit();
				$resultadoFunc = sqlsrv_query($conn, $tsqlFunc);

				sqlsrv_free_stmt($resultadoFunc);		

				//Borra las CTOS asociadas a la tarea, para actualizarlas con las nuevas introducidas
				$tsqlFunc ="DELETE FROM INV_TBTAREAS_CTO WHERE id='".$id."'";  
				$resultadoFunc = sqlsrv_query( $conn, $tsqlFunc);
				sqlsrv_free_stmt( $resultadoFunc);	

				//CTOS ASOCIADAS
				if ($_POST['MARCAR'] == ''){	
					$marcar ='';
				}
				else{
					$marcar = $_POST['MARCAR'];
					$nfilasMarc = count ($marcar);
				}

				//Relacionar las CTOS que tenga asignadas por pantalla		
				if (!empty($marcar)) {
					for ($i=0; $i<$nfilasMarc; $i++) {

						if ($registroFunc['ID_GD'] == '' || is_null($registroFunc['ID_GD'])) {
							$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (ID_GESTOR, COD_CTO, id,ID_ACTUACION) VALUES ('".$registroFunc['id_Actuacion']."', '".$marcar[$i]."', '".$id."', '".$registroFunc['id_Actuacion']."')";   		
							$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
						} else {
							$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (ID_GESTOR, COD_CTO, id,ID_ACTUACION) VALUES ('".$registroFunc['ID_GD']."', '".$marcar[$i]."', '".$id."', '".$registroFunc['id_Actuacion']."')";   		
							$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
						}
						

					}
				}		

				

				//INSERTAR en tbTareas_bloqueos		
				if ($bloqueo){	

				    if (isset($_POST['motivoBloq'])) {
				    	$motivoBloqueo=$_POST['motivoBloq'];
				    } else {
				    	$motivoBloqueo=$_POST['motivoBloqueo'];
				    }	
					$tsql= "INSERT INTO INV_tbTareas_Bloqueos (id_Tarea, Tipo_Afectacion, id_Motivo) VALUES ($id, 1, $motivoBloqueo)";   
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
							if ($registroFunc['ID_GD']=="" or is_null($registroFunc['ID_GD'])){ 
								$tsqlFunc= "INSERT INTO INV_tbBloqueos_Gescales (COD_CTO, id, COD_GESCAL, id_Actuacion) VALUES ('".$codigo_cto."', '".$id."','".$codigo_gescal."', '".$registroFunc['id_Actuacion']."')";   		
							} else {
								$tsqlFunc= "INSERT INTO INV_tbBloqueos_Gescales (ID_GESTOR, COD_CTO, id, COD_GESCAL, id_Actuacion) VALUES ('".$registroFunc['ID_GD']."', '".$codigo_cto."', '".$id."','".$codigo_gescal."', '".$registroFunc['id_Actuacion']."')";   		
							}
							$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);

						}
					}			
				}
					
				//INSERTAR en tbTareas_Desbloqueos
				if ($desbloqueo){			
					$tsql= "INSERT INTO INV_tbTareas_Desbloqueos (id_Tarea, Tipo_Afectacion) VALUES ($id, 1)";   
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
							if ($registroFunc['ID_GD']=="" or is_null($registroFunc['ID_GD'])){ 
								$tsqlFunc= "INSERT INTO INV_tbDesbloqueos_Gescales (COD_CTO, id, COD_GESCAL, id_Actuacion) VALUES ('".$codigo_cto."', '".$id."','".$codigo_gescal."', '".$registroFunc['id_Actuacion']."')";   		
							} else {
								$tsqlFunc= "INSERT INTO INV_tbDesbloqueos_Gescales (ID_GESTOR, COD_CTO, id, COD_GESCAL, id_Actuacion) VALUES ('".$idgd."', '".$codigo_cto."', '".$id."','".$codigo_gescal."', '".$registroFunc['id_Actuacion']."')";   		
							}
								
							$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
							//echo  "Alta INV_tbBloqueos_Gescales: ".$tsqlFunc;

						}
					}							
				}


		    	// CONSOLIDAR TODOS LOS CAMBIOS TRANSACCIÓN
		    	sqlsrv_commit( $conn );															
				return "Modificación realizada correctamente";	
			}
		} else {
			return "Las tareas con estado pendiente o cerrado no se pueden modificar";	
		}
	} else {
		return "ID de la Tarea no informado";	
	}
}