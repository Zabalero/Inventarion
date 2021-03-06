<?php
// Establece una conexión con la base de datos
function cambiarEstado($conn, $id, $usuario_cambio,$comentarios){
   
        //echo("-".$_POST['id_tecnico']."-");exit();
	if($_POST['tecnico'] == NULL || $_POST['tecnico'] == ''){
		return "No puede cambiar el estado a una tarea sin técnico asignado";
	} else {
             $nuevoEstado = explode("-", $_POST['ESTADO']);
             if (($nuevoEstado[0] == '8') && ($_POST['INCIDENCIA'] == '')){ //No se puede pasar a estado Construcción sin un Remedy        
                    return "Si desea pasar a estado Construcción, primero debe indicar un Ticket Remedy y pulsar Modificar";                 
              }else{
               
		if ($id != '') {
			
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

			                   

			if ($registroFunc['idEst'] != $nuevoEstado[0]) {
                            //echo $nuevoEstado[0];exit();

				// INICIO TRANSACCIÓN
				if ( sqlsrv_begin_transaction( $conn ) === false ) {
					 return "Error al actualizar la tarea sqlsrv_begin_transaction";
				} else {

					//Actualiza datos en INV_TBTAREAS
					switch ($nuevoEstado[0]) {
					    case 2:																//EN PROCESO
							$tsqlFunc = "UPDATE INV_TBTAREAS SET idEst = '".$nuevoEstado[0]."', COMENTARIOS = '".$comentarios."', COMENTARIOS2 = '".$_POST['COMENTARIOS2']."',
											FECHA_INICIO = getdate()".", idTecn = '".$_POST['id_tecnico']."' WHERE id = '".$id."' ";
					        break;	
					    case 4:																//CERRADA
                                            case 12:																//CANCELADA
                                            case 13:																//NO APLICA MODIFICACIÓN
                                                
							$tsqlFunc = "UPDATE INV_TBTAREAS SET idEst = '".$nuevoEstado[0]."', COMENTARIOS = '".$comentarios."', COMENTARIOS2 = '".$_POST['COMENTARIOS2']."', FECHA_RESOL = getdate() WHERE id = '".$id."' ";
					        break;
                                            default:
							$tsqlFunc = "UPDATE INV_TBTAREAS SET idEst = '".$nuevoEstado[0]."', COMENTARIOS = '".$comentarios."', COMENTARIOS2 = '".$_POST['COMENTARIOS2']."'  WHERE id = '".$id."' ";
					        break;	 	    
					}	
                                        //echo $tsqlFunc;exit();
					$resultadoFunc = sqlsrv_query($conn, $tsqlFunc);

					sqlsrv_free_stmt($resultadoFunc);		

					//Borra las CTOS asociadas a la tarea, para actualizarlas con las nuevas introducidas
					// maiteben: no modifico CTOS al cambiar de estado
					
					// $tsqlFunc ="DELETE FROM INV_TBTAREAS_CTO WHERE id='".$id."'";  
					// $resultadoFunc = sqlsrv_query( $conn, $tsqlFunc);
					// sqlsrv_free_stmt( $resultadoFunc);	

					// if ($_POST['MARCAR'] == ''){	
					// 	$marcar ='';
					// }
					// else{
					// 	$marcar = $_POST['MARCAR'];
					// 	$nfilasMarc = count ($marcar);
					// }

					// //Relacionar las CTOS que haya marcados en pantalla		
					// if (!empty($marcar)) {
					// 	for ($i=0; $i<$nfilasMarc; $i++) {

					// 		if ($registroFunc['ID_GD'] == '' || is_null($registroFunc['ID_GD'])) {
					// 			$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (ID_GESTOR, COD_CTO, id,ID_ACTUACION) VALUES (".$registroFunc['id_Actuacion'].", '".$marcar[$i]."', ".$id.",".$registroFunc['id_Actuacion'].")";   		
					// 			$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
					// 		} else {
					// 			$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (ID_GESTOR, COD_CTO, id,ID_ACTUACION) VALUES (".$registroFunc['ID_GD'].", '".$marcar[$i]."', ".$id.",".$registroFunc['id_Actuacion'].")";   		
					// 			$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
					// 		}

					// 	}
					// }												
					$tsqlFunc ="INSERT INTO INV_HISTORICO_TAREAS (ID_TAREA, ID_ESTADO_ANT, ID_ESTADO_NEW, ID_SUBACTIVIDAD, ID_USUARIO, FECHA_CAMBIO) VALUES ('".$id."', '".$registroFunc['idEst']."', '".$nuevoEstado[0]."', '".$registroFunc['id_Subactividad']."', '".$usuario_cambio."', getdate())";

					$resultadoFunc = sqlsrv_query( $conn, $tsqlFunc);
					sqlsrv_free_stmt( $resultadoFunc);
					
					//Realiza todas las FUNCIONES EXTRA DEL CAMBIO DE ESTADO según subactividad
					$resp = '';
					$newID = transaccionesCambiarEstado($conn, $id, $nuevoEstado[0], $registroFunc);
                                        
					if( $newID === NULL ) {
						// DESHACER TODOS LOS CAMBIOS TRANSACCIÓN
						sqlsrv_rollback( $conn );
				    	return "Error ar realizar el cambio de estado de la siguiente tarea: ".$id;	
				    } else {	
				    	// CONSOLIDAR TODOS LOS CAMBIOS TRANSACCIÓN
				    	sqlsrv_commit( $conn );	
				    	if ($newID == 0) {
				    		return "Cambio de Estado realizado correctamente";	
				    	} else {
				    		return "Cambio de Estado realizado correctamente. Se ha dado de alta la siguiente tarea: ".$newID;		
				    	}		
					}
				}	

			} else {
				return "El estado anterior es el mismo que el nuevo";
			}	  	

		} else {
			return "ID de la Tarea no informado";
		}
	}
     }
        
}   

function transaccionesCambiarEstado($conn, $id, $nuevoEstado, $tareaOrigen){

	$newID = NULL;

	switch ($nuevoEstado) {
	    case 2:																//EN PROCESO
	        $newID = 0;
	        break;	
	    case 4:																//CERRADA
			$newID = crearTareaPorCambioEstado($conn, $id, $tareaOrigen);
	        break;
	    default:
	        $newID = 0;
	        break;	 	    
	}	

	return $newID;
	

}

function crearTareaPorCambioEstado($conn, $id, $tareaOrigen){

	$newID = NULL;

	switch ($tareaOrigen['id_Subactividad']) {							
	    case 126:													//SEGUIMIENTO AMPLIACIONES CTO SATURADA
	        
	        $idusuario = '177';													//servicios.tecnicos.inventario.logico@jazztel.com
	        $subactividad = '162';												//Diseño – CTO saturada
	        $prioridad = '1';
	        $tipoEntrada = '1';													//WEB

	        if ($tareaOrigen['ID_GD'] == '' || is_null($tareaOrigen['ID_GD'])) { 
				$tsqlFunc = "insert into inv_tbTareas (REF, cod_cabecera, FECHA_REGISTRO, idEst, idUsuOrigen, id_Subactividad, PRIORIDAD" ;
			} else {
				$tsqlFunc = "insert into inv_tbTareas (REF, ID_GD, cod_cabecera, FECHA_REGISTRO, idEst, idUsuOrigen, id_Subactividad, PRIORIDAD" ;
			}

			if (!empty($tareaOrigen['INCIDENCIA']))
				$tsqlFunc = $tsqlFunc . ", INCIDENCIA";		
			if (!empty($tareaOrigen['id_Actuacion']))
				$tsqlFunc = $tsqlFunc . ", id_Actuacion";
			if (!empty($tareaOrigen['TICKET_OCEANE']))
				$tsqlFunc = $tsqlFunc . ", TICKET_OCEANE";
			if (!empty($tareaOrigen['TP']))
				$tsqlFunc = $tsqlFunc . ", TP";
			if (!empty($tareaOrigen['ID_EEMM']))
				$tsqlFunc = $tsqlFunc . ", ID_EEMM";
			if (!empty($tareaOrigen['ID_MAPEO']))
				$tsqlFunc = $tsqlFunc . ", ID_MAPEO";
			if (!empty($tareaOrigen['ID_TIPO_ENTRADA']))
				$tsqlFunc = $tsqlFunc . ", ID_TIPO_ENTRADA";															
				

			if ($tareaOrigen['ID_GD'] == '' || is_null($tareaOrigen['ID_GD'])) { 
				$tsqlFunc = $tsqlFunc . ") values ('".$tareaOrigen['REF']."', '".$tareaOrigen['cod_cabecera']."', getdate(), 1, '".$idusuario."', '".$subactividad."', '".$prioridad."'";
			} else {
				$tsqlFunc = $tsqlFunc . ") values ('".$tareaOrigen['REF']."', '".$tareaOrigen['ID_GD']."', '".$tareaOrigen['cod_cabecera']."', getdate(), 1, '".$idusuario."', '".$subactividad."', '".$prioridad."'";
			}

			if (!empty($tareaOrigen['INCIDENCIA']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['INCIDENCIA']."'";
			if (!empty($tareaOrigen['id_Actuacion']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['id_Actuacion']."'";			
			if (!empty($tareaOrigen['TICKET_OCEANE']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['TICKET_OCEANE']."'";
			if (!empty($tareaOrigen['TP']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['TP']."'";
			if (!empty($tareaOrigen['ID_EEMM']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['ID_EEMM']."'";
			if (!empty($tareaOrigen['ID_MAPEO']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['ID_MAPEO']."'";
			if (!empty($tareaOrigen['ID_TIPO_ENTRADA']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['ID_TIPO_ENTRADA']."'";															
			
				
			$tsqlFunc = $tsqlFunc . ");SELECT SCOPE_IDENTITY() AS theID;";	 

			$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);   	
			sqlsrv_next_result($stmtFunc);
			sqlsrv_fetch($stmtFunc);
			$newID=sqlsrv_get_field($stmtFunc, 0);		

			if ($_POST['MARCAR'] == ''){	
				$marcar ='';
			}
			else{
				$marcar = $_POST['MARCAR'];
				$nfilasMarc = count ($marcar);
			}

			//Relacionar las CTOS que tenga la tarea origen a la nueva tarea		
			if (!empty($marcar)) {
				for ($i=0; $i<$nfilasMarc; $i++) {

					if ($tareaOrigen['ID_GD'] == '' || is_null($tareaOrigen['ID_GD'])) {
						$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (ID_GESTOR, COD_CTO, id,ID_ACTUACION) VALUES (".$tareaOrigen['id_Actuacion'].", '".$marcar[$i]."', ".$newID.",".$tareaOrigen['id_Actuacion'].")";   		
						$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
					} else {
						$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (ID_GESTOR, COD_CTO, id,ID_ACTUACION) VALUES (".$tareaOrigen['ID_GD'].", '".$marcar[$i]."', ".$newID.",".$tareaOrigen['id_Actuacion'].")";   		
						$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
					}

				}
			}				

			break;
	    case 162:													//Diseño – CTO saturada
	        
	        $idusuario = '177';													//servicios.tecnicos.inventario.logico@jazztel.com
	        $subactividad = '244';												//As-Built – Ampl. de red-CTOS saturadas
	        $prioridad = '1';
	        $tipoEntrada = '1';													//WEB

	        if ($tareaOrigen['ID_GD'] == '' || is_null($tareaOrigen['ID_GD'])) { 
				$tsqlFunc = "insert into inv_tbTareas (REF, cod_cabecera, FECHA_REGISTRO, idEst, idUsuOrigen, id_Subactividad, PRIORIDAD" ;
			} else {
				$tsqlFunc = "insert into inv_tbTareas (REF, ID_GD, cod_cabecera, FECHA_REGISTRO, idEst, idUsuOrigen, id_Subactividad, PRIORIDAD" ;
			}

			if (!empty($tareaOrigen['INCIDENCIA']))
				$tsqlFunc = $tsqlFunc . ", INCIDENCIA";		
			if (!empty($tareaOrigen['id_Actuacion']))
				$tsqlFunc = $tsqlFunc . ", id_Actuacion";
			if (!empty($tareaOrigen['TICKET_OCEANE']))
				$tsqlFunc = $tsqlFunc . ", TICKET_OCEANE";
			if (!empty($tareaOrigen['TP']))
				$tsqlFunc = $tsqlFunc . ", TP";
			if (!empty($tareaOrigen['ID_EEMM']))
				$tsqlFunc = $tsqlFunc . ", ID_EEMM";
			if (!empty($tareaOrigen['ID_MAPEO']))
				$tsqlFunc = $tsqlFunc . ", ID_MAPEO";
			if (!empty($tareaOrigen['ID_TIPO_ENTRADA']))
				$tsqlFunc = $tsqlFunc . ", ID_TIPO_ENTRADA";															
				

			if ($tareaOrigen['ID_GD'] == '' || is_null($tareaOrigen['ID_GD'])) { 
				$tsqlFunc = $tsqlFunc . ") values ('".$tareaOrigen['REF']."', '".$tareaOrigen['cod_cabecera']."', getdate(), 1, '".$idusuario."', '".$subactividad."', '".$prioridad."'";
			} else {
				$tsqlFunc = $tsqlFunc . ") values ('".$tareaOrigen['REF']."', '".$tareaOrigen['ID_GD']."', '".$tareaOrigen['cod_cabecera']."', getdate(), 1, '".$idusuario."', '".$subactividad."', '".$prioridad."'";
			}

			if (!empty($tareaOrigen['INCIDENCIA']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['INCIDENCIA']."'";
			if (!empty($tareaOrigen['id_Actuacion']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['id_Actuacion']."'";			
			if (!empty($tareaOrigen['TICKET_OCEANE']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['TICKET_OCEANE']."'";
			if (!empty($tareaOrigen['TP']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['TP']."'";
			if (!empty($tareaOrigen['ID_EEMM']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['ID_EEMM']."'";
			if (!empty($tareaOrigen['ID_MAPEO']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['ID_MAPEO']."'";
			if (!empty($tareaOrigen['ID_TIPO_ENTRADA']))
				$tsqlFunc = $tsqlFunc . ", '".$tareaOrigen['ID_TIPO_ENTRADA']."'";															
			
				
			$tsqlFunc = $tsqlFunc . ");SELECT SCOPE_IDENTITY() AS theID;";	 

			$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);   	
			sqlsrv_next_result($stmtFunc);
			sqlsrv_fetch($stmtFunc);
			$newID=sqlsrv_get_field($stmtFunc, 0);		

			if ($_POST['MARCAR'] == ''){	
				$marcar ='';
			}
			else{
				$marcar = $_POST['MARCAR'];
				$nfilasMarc = count ($marcar);
			}

			//Relacionar las CTOS que tenga la tarea origen a la nueva tarea		
			if (!empty($marcar)) {
				for ($i=0; $i<$nfilasMarc; $i++) {

					if ($tareaOrigen['ID_GD'] == '' || is_null($tareaOrigen['ID_GD'])) {
						$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (ID_GESTOR, COD_CTO, id,ID_ACTUACION) VALUES (".$tareaOrigen['id_Actuacion'].", '".$marcar[$i]."', ".$newID.",".$tareaOrigen['id_Actuacion'].")";   		
						$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
					} else {
						$tsqlFunc= "INSERT INTO INV_TBTAREAS_CTO (ID_GESTOR, COD_CTO, id,ID_ACTUACION) VALUES (".$tareaOrigen['ID_GD'].", '".$marcar[$i]."', ".$newID.",".$tareaOrigen['id_Actuacion'].")";   		
						$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
					}

				}
			}				

			break;			
	    default:
	        $newID = 0;
	        break;	 			
	}

	return $newID;
}

