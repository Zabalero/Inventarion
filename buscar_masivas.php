<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Inicializa las variables utilizadas en el formulario
	$id_ra = ""; 
	$id_rd = ""; 
	$id_cab = "";
	$id = "";

	$mensaje = "";
    $seleccionadoRef = "";
    $seleccionadoProv = "";
    $seleccionadoReg = "";
    $seleccionadoGestor = "";
    $seleccionadoCab = "";
    $seleccionadoIdGd = "";
    $seleccionadoActTesa = "";
    $seleccionadoInc = "";
    $seleccionadoActJazz = "";
    $seleccionadoEst = "";
    $seleccionadoTecn = "";
    $seleccionadoTecnAsign = "";
    $seleccionadoActiv = "";
    $seleccionadoOrigenSol = "";
    $seleccionadofRegistro1 = "";
    $seleccionadofRegistro2 = "";
    $seleccionadofIni1 = "";
    $seleccionadofIni2 = "";    
    $seleccionadofResol1 = "";
    $seleccionadofResol2 = "";    
    $seleccionadoPrior = "";
    $seleccionadoOceane="";
    $seleccionadoCTO="";

    $ref = "";    
    $gestor = "";    
    $provincia = "";    
    $region = "";    
    $cabecera = "";    
    $idgd = "";    
    $acttesa = "";    
    $incidencia = "";    
    $actjazz = "";    
    $estado = "";    
	$tecnico = "";    
	$tecnicoAsign = "";
    $actividad = "";    
    $subactividad = "";    
	$origenSol = "";    
    $fRegistro1 = "";    
    $fRegistro2 = "";    
    $fInicio1 = "";    
    $fInicio2 = "";    
    $fResol1 = "";    
    $fResol2 = "";    
    $prior = "";    
    $orden = "";    
    $estado2 = "";    
    $oceane = "";    
    $CTO  = "";    
    
    $comboAct = "";    
	$comboSubAct = "";    

 
	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	$rolUsuario=get_rol($_SESSION['usuario']);
      
	// Si ya hemos introducido valores para filtros de búsqueda
    if($_SERVER['REQUEST_METHOD']=='POST')
    {  
      
        $seleccionadoRef = $_REQUEST['ref'];
        $seleccionadoGestor = $_REQUEST['gestor'];
        $seleccionadoProv = $_REQUEST['provincia'];
        $seleccionadoReg = $_REQUEST['region'];
        $seleccionadoCab = $_REQUEST['cabecera'];
        $seleccionadoIdGd = $_REQUEST['idgd'];
        $seleccionadoActTesa = $_REQUEST['acttesa'];    
        $seleccionadoInc= $_REQUEST['incidencia'];
        $seleccionadoActJazz = $_REQUEST['actjazz'];
        $seleccionadoEst = $_REQUEST['estado'];
        $seleccionadoTecn = $_REQUEST['tecnico'];
        $seleccionadotecnicoAsign = $_REQUEST['tecnicoAsign'];
        $seleccionadoActiv = $_REQUEST['actividad'];
        $seleccionadoOrigenSol= $_REQUEST['origenSol'];
        $seleccionadofRegistro1 = $_REQUEST['fRegistro1'];
        $seleccionadofRegistro2 = $_REQUEST['fRegistro2'];
        $seleccionadofIni1 = $_REQUEST['fInicio1'];
        $seleccionadofIni2 = $_REQUEST['fInicio2'];
        $seleccionadofResol1 = $_REQUEST['fResol1'];
        $seleccionadofResol2 = $_REQUEST['fResol2'];
        $seleccionadoPrior = $_REQUEST['prior'];
        $seleccionadoOrden=$_REQUEST['orden'];
        $seleccionadoestado2=$_POST["estado2"]; 
        $seleccionadoOceane= $_REQUEST['oceane'];
        $seleccionadoCTO= $_REQUEST['CTO'];

	    $ref=  $_REQUEST['ref'];
	    $gestor = $_REQUEST['gestor'];
	    $provincia = $_REQUEST['provincia'];
	    $region = $_REQUEST['region'];
	    $cabecera = $_REQUEST['cabecera'];
	    $idgd = $_REQUEST['idgd'];
	    $acttesa = $_REQUEST['acttesa'];
	    $incidencia = $_REQUEST['incidencia'];
	    $actjazz = $_REQUEST['actjazz'];
	    $estado = $_REQUEST['estado'];

	 	if (isset($_REQUEST['tecnico']) && $_REQUEST['tecnico'] != '') {
			$tecnico = $_REQUEST['tecnico'];
		} else {
			$tecnico = $_POST['tecnicoTxt'];
		}


	    //$tecnico = $_REQUEST['tecnico'];
	    $tecnicoAsign = $_REQUEST['tecnicoAsign'];
	    $actividad = $_REQUEST['actividad'];
	    $subactividad = $_REQUEST['subactividad'];

		if (isset($_REQUEST['origenSol']) && $_REQUEST['origenSol'] != '') {
			$origenSol = $_REQUEST['origenSol'];
		} else {
			$origenSol = $_POST['origenSolTxt'];
		} 

	    //$origenSol = $_REQUEST['origenSol'];
	    $fRegistro1 = $_REQUEST['fRegistro1'];
	    $fRegistro2 = $_REQUEST['fRegistro2'];
	    $fInicio1 = $_REQUEST['fInicio1'];
	    $fInicio2 = $_REQUEST['fInicio2'];
	    $fResol1 = $_REQUEST['fResol1'];
	    $fResol2 = $_REQUEST['fResol2'];        
	    $prior = $_REQUEST['prior']; 
	    $orden=$_REQUEST['orden']; 
	    $estado2=$_POST["estado2"]; 
	    $oceane= $_REQUEST['oceane'];
	    $CTO = $_REQUEST['CTO'];
	    
	    $comboAct=$_REQUEST['actividad'];
		$comboSubAct=$_REQUEST['subactividad'];	        

		$id=$_REQUEST['id'];

		//FIN Inicializa las variables utilizadas en el formulario

		//ELIMINAR TAREA
		if (isset($_REQUEST['eliminar'])) {		
											
			// Obtener número de registros a procesar
			$marcarProc = $_REQUEST['marcarProc'];
			$nfilas = count ($marcarProc);
							  
			if ($nfilas>0){
				/* Iniciar la transacción. */
				if ( sqlsrv_begin_transaction( $conn ) === false ) {
					$mensaje = 'Error al actualizar la tarea sqlsrv_begin_transaction';
				} else {		
								
					for ($i=0; $i<$nfilas; $i++) {
									
									
						//elimina el archivo insertado en la tabla tbArchivos. OBLIGATORIO hacerlo antes de eliminar la tarea
						$tsql2 = "DELETE FROM INV_tbArchivos WHERE idTarea = $marcarProc[$i]";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
										
						sqlsrv_free_stmt( $stmt2);	



						//elimina los bloqueos. OBLIGATORIO hacerlo antes de eliminar la tarea
						$tsql2 = "DELETE FROM INV_tbTareas_Bloqueos WHERE id_Tarea = $marcarProc[$i]";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
										
						sqlsrv_free_stmt( $stmt2);	


						//elimina los desbloqueos. OBLIGATORIO hacerlo antes de eliminar la tarea
						$tsql2 = "DELETE FROM INV_tbTareas_Desbloqueos WHERE id_Tarea = $marcarProc[$i]";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
						sqlsrv_free_stmt( $stmt2);				
										


						//elimina los gescales bloqueados. OBLIGATORIO hacerlo antes de eliminar las CTOS afectadas
						$tsql2 = "DELETE FROM INV_tbBloqueos_Gescales WHERE id = $marcarProc[$i]";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
						sqlsrv_free_stmt( $stmt2);	
								
								
						//elimina los gescales desbloqueados. OBLIGATORIO hacerlo antes de eliminar las CTOS afectadas
						$tsql2 = "DELETE FROM INV_tbDesbloqueos_Gescales WHERE id = $marcarProc[$i]";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
						sqlsrv_free_stmt( $stmt2);	

						//elimina las CTOS afectadas de la tarea. OBLIGATORIO hacerlo antes de eliminar la tarea
						$tsql2 = "DELETE FROM INV_tbTareas_CTO WHERE id = $marcarProc[$i]";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
						sqlsrv_free_stmt( $stmt2);
								
						//Busca el id de la tarea que se elimina en la tabla tbTipoReasignacion. OBLIGATORIO hacerlo antes de eliminar la tarea
						$tsql3 = "SELECT id, id_Tarea FROM INV_tbTipoReasignacion WHERE id_Tarea = $marcarProc[$i]";
															 
						$stmt3 = sqlsrv_query( $conn, $tsql3);
						$rows = sqlsrv_has_rows( $stmt3 );
						
						if ($rows === true){					
							while($row = sqlsrv_fetch_array($stmt3)){
								$idTipoReasignacion=$row["id"];
																		
								//elimina las filas de la tabla tbReasig_Gescales con el id de la tabla tbTipoReasignacion. OBLIGATORIO hacerlo antes de eliminar los id de tbTipoReasignacion
								$tsql4 = "DELETE FROM INV_tbReasig_Gescales WHERE id = $idTipoReasignacion";
																						 
								$stmt4 = sqlsrv_query( $conn, $tsql4);
							}
						}
															
						//elimina las filas de la tabla tbTipoReasignacion con los id marcados de la tabla tbTareas
						$tsql5 = "DELETE FROM INV_tbTipoReasignacion WHERE id_Tarea = $marcarProc[$i]";
																 
						$stmt5 = sqlsrv_query( $conn, $tsql5);
						
						
						//elimina la tarea de tbTipoIncidencia
						$tsql = "DELETE FROM INV_tbTipoIncidencia WHERE id_Tarea = $marcarProc[$i]";
													
						$stmt = sqlsrv_query( $conn, $tsql);
													
						//elimina la tarea de tbTareas
						$tsql = "DELETE FROM INV_tbTareas WHERE id = $marcarProc[$i]";
													
						$stmt = sqlsrv_query( $conn, $tsql);														
															
					}
																
				

					/* Si la última sentencia finalizó con éxito, consolidar la transacción. */
					/* En caso contrario, revertirla. */
					if( $stmt ) {
						 sqlsrv_commit( $conn );
						 $mensaje = 'Se han eliminado '.$nfilas.' tareas';
						 
						 
					} else {
						 sqlsrv_rollback( $conn );
						 $mensaje = 'No se realiza la eliminación. Algún registro no pudo eliminarse';
					}
				}	
					
			} else {
				$mensaje = 'Debe marcar las tareas que desee eliminar';
			}
				
		}
		//FIN ELIMINAR TAREA

		//ASIGNAR NUEVA TAREA
		if (isset($_REQUEST['asignar'])) {	

			//busca el id del estado 'En proceso' (para actualizarlo en la tabla tbTareas)		
			$tsql="SELECT id_Estado FROM INV_tbEstados WHERE Estado='En proceso'";
			$stmt = sqlsrv_query( $conn, $tsql)	or die ("Fallo en la consulta");
			
			while($row = sqlsrv_fetch_array($stmt)){	
				$idEstProc= $row["id_Estado"]; 
			}					
				
			sqlsrv_free_stmt( $stmt);		
			
			if($rolUsuario=='avanzado'){		
										
				//busca nombre del técnico seleccinado en el input Técnico asignado
				//echo "string: ".$tecnicoAsign;
				$tsql="SELECT nombre FROM INV_tbUsuarios WHERE id_usu='".$tecnicoAsign."'";
				$stmt = sqlsrv_query( $conn, $tsql)	or die ("Fallo en la consulta1");

				while($row = sqlsrv_fetch_array($stmt)){	
					$nombretecnicoAsign= $row["nombre"]; 
				}								

				sqlsrv_free_stmt( $stmt);
											
				// Obtener número de registros a procesar
				$marcarProc = $_REQUEST['marcarProc'];
				echo "string: ".count ($marcarProc);
				
				$nfilas = count ($marcarProc);
											  
				if ($nfilas>0){

					$textoAsignado="Tareas para asignar:<br>";


					for ($i=0; $i<$nfilas; $i++)
					{

						// Obtener datos de la registros i-ésima
						$tsql = "select * from INV_tbTareas where id = '".$marcarProc[$i]."'";
						$stmt = sqlsrv_query( $conn, $tsql)	or die ("Fallo en la consulta2");

						while($row = sqlsrv_fetch_array($stmt)){	
							$refId= $row["id"];
							$compEst = $row["idEst"];	
							$id_subactividad_tarea = $row["id_Subactividad"];													
						}

						sqlsrv_free_stmt( $stmt);
														
						//busca las tareas con el número de referencia indicado
						$tsql="SELECT * FROM INV_tbTareas WHERE id = '".$refId."'";

						$params = array();
						$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );  //necesario para función 'sqlsrv_num_rows'
						$stmt = sqlsrv_query( $conn, $tsql, array(), array('Scrollable' => 'buffered'))
																	 or die ("Error al ejecutar consulta: ".$tsql);						
						$nREF = sqlsrv_num_rows( $stmt );											
									
						sqlsrv_free_stmt( $stmt);	
						//echo "string: ".$tsql;									

						// procesar datos
						
						//sólo cambia el estado y técnico en las tareas pendientes. El resto no.
						if ($compEst == 1) {
							$tsql = "UPDATE INV_tbTareas SET idTecn='".$tecnicoAsign."', idEst='".$idEstProc."', FECHA_INICIO = getdate() where id = '".$refId."' AND idEst=1";
							$stmt = sqlsrv_query( $conn, $tsql)	or die ("Fallo en la actualización");

							//busca id del usuario registrado (para asignar técnico a la tarea)
							$tsql="SELECT id_usu FROM INV_tbUsuarios WHERE usuario='".$_SESSION['usuario']."'";
							//echo "string: ".$tsql;
							$stmt = sqlsrv_query( $conn, $tsql)	 or die ("Fallo en la consulta");
							
							while($row = sqlsrv_fetch_array($stmt)){	
									$id_usuario_cambio= $row["id_usu"]; 
							}								
							sqlsrv_free_stmt( $stmt);

							//Insertar en histórico de cambios de estado de tareas

							$tsqlFunc ="INSERT INTO INV_HISTORICO_TAREAS (ID_TAREA, ID_ESTADO_ANT, ID_ESTADO_NEW, ID_SUBACTIVIDAD, ID_USUARIO, FECHA_CAMBIO) VALUES ('".$refId."', '".$compEst."', '".$idEstProc."', '".$id_subactividad_tarea."', '".$id_usuario_cambio."', getdate())";
							echo "string: ".$tsql;
							$resultadoFunc = sqlsrv_query( $conn, $tsqlFunc);
							sqlsrv_free_stmt( $resultadoFunc);							

						} else {
							$tsql = "UPDATE INV_tbTareas SET idTecn='".$tecnicoAsign."' where id = '".$refId."'";
							$stmt = sqlsrv_query( $conn, $tsql)	or die ("Fallo en la actualización");
						}
							 
						
						$textoAsignado .= $nREF." tareas"." con el ID ".$refId."<br>";   //si se pone \n no funciona el javascript.
								
					}

					$textoAsignado .= "Se asignan al técnico ".$nombretecnicoAsign. " únicamente las tareas pendientes.";
					$mensaje = $textoAsignado;
									
				} else {
					$mensaje = 'Debe marcar las tareas a las que desee asignar técnico';
				}
			} else {
				//usuario técnico.  Asignación automática de tareas
				if($rolUsuario=='escritura'){

					
					//busca id del usuario registrado (para asignar técnico a la tarea)
					$tsql="SELECT id_usu FROM INV_tbUsuarios WHERE usuario='".$_SESSION['usuario']."'";
					//echo "string: ".$tsql;
					$stmt = sqlsrv_query( $conn, $tsql)	 or die ("Fallo en la consulta");
					
					while($row = sqlsrv_fetch_array($stmt)){	
							$idtecnicoUsu= $row["id_usu"]; 
					}								
					sqlsrv_free_stmt( $stmt);
							
					//busca id y referencia de la tarea pendiente más prioritaria y de fecha registro más antigua
					$tsql="SELECT TOP 1 INV_tbTareas.id as id, INV_tbTareas.REF as REF, INV_tbEstados.Estado as ESTADO FROM INV_tbEstados RIGHT JOIN INV_tbTareas ON INV_tbEstados.id_Estado=INV_tbTareas.idEst WHERE INV_tbEstados.Estado='Pendiente' ORDER BY INV_tbTareas.PRIORIDAD ASC, INV_tbTareas.FECHA_REGISTRO ASC";
					$stmt = sqlsrv_query( $conn, $tsql)	 or die ("Fallo en la consulta");
					
					while($row = sqlsrv_fetch_array($stmt)){	
						$refTarea= $row["REF"]; 
					}								
					sqlsrv_free_stmt( $stmt);


					//Insertar en histórico de cambios de estado de tareas
					$tsql="SELECT id, idEst, id_Subactividad FROM INV_tbTareas WHERE REF='".$refTarea."'";
					$stmt = sqlsrv_query( $conn, $tsql)	 or die ("Fallo en la consulta");
					while($row = sqlsrv_fetch_array($stmt)){

							$tsqlFunc ="INSERT INTO INV_HISTORICO_TAREAS (ID_TAREA, ID_ESTADO_ANT, ID_ESTADO_NEW, ID_SUBACTIVIDAD, ID_USUARIO, FECHA_CAMBIO) VALUES ('".$row['id']."', '".$row['idEst']."', '".$idEstProc."', '".$row['id_Subactividad']."', '".$idtecnicoUsu."', getdate())";

							$resultadoFunc = sqlsrv_query( $conn, $tsqlFunc);
							sqlsrv_free_stmt( $resultadoFunc);							
					}


					//busca las tareas con el número de referencia encontrado
					$tsql="SELECT id FROM INV_tbTareas WHERE REF='".$refTarea."'";
					$params = array();
					$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );  //necesario para función 'sqlsrv_num_rows'
					$stmt = sqlsrv_query( $conn, $tsql , $params, $options ) or die ("Fallo en la consulta");
						
					$nfilas = sqlsrv_num_rows( $stmt );	
					
					//ASIGNA AL TECNICO USUARIO ACTUAL LAS TAREAS PENDIENTES CON EL MISMO Nº DE REFERENCIA QUE LA TAREA MAS URGENTE Y DE FECHA REGISTRO MAS ANTIGUA  
					$tsql="UPDATE INV_tbTareas SET idTecn='".$idtecnicoUsu."', idEst='".$idEstProc."', FECHA_INICIO = getdate() WHERE REF='".$refTarea."'";
					$stmt = sqlsrv_query( $conn, $tsql)	 or die ("Fallo en la asignación de la tarea");

					$mensaje = 'Se han asignado : '.$nfilas.' tareas con el nº referencia '.$refTarea;
							
				} else {
					$mensaje = "Rol de Usuario no permitido";
				}
				//fin usuario escritura
			}

		}
		// FIN ASIGNAR NUEVA TAREA

     } else {
    	$id_ra = $_GET['id_ra'];
    	$id_rd = $_GET['id_rd'];
    	$id_cab = $_GET['id_cab'];
    }
  
	//Variables para la búsqueda
	$maxRows = 10;
	$pageNum = 0;
	$seleccionada = 0;
	if (isset($_GET['pageNum'])) {
	  $pageNum = $_GET['pageNum'];
	}
	$startRow = $pageNum * $maxRows;


	if($_SERVER['REQUEST_METHOD']=='POST') {
		if ($_POST['buscar'] || $_REQUEST['eliminar'] || $_REQUEST['asignar']) {
			if (isset($id) && $id != "") {
				
				$tsql = "SELECT * FROM INV_VIEW_DATOS_TODO WHERE REF_TBTAREA LIKE '%".$id."%' 
								OR REMEDY LIKE '%".$id."%' OR REF_ASOCIADA LIKE '%".$id."%' OR TP LIKE '%".$id."%' OR OCEANE_TBTAREA LIKE '%".$id."%' OR ID_TAREA LIKE '%".$id."%'";
						
				//Recuperar datos de consulta
				$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

				//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

				//crear array de ctos para asisgnarselos a cada tarea en su linea
				$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
							FROM INV_TBTAREAS
								INNER JOIN INV_TBTAREAS_CTO ON INV_TBTAREAS_CTO.ID = INV_TBTAREAS.id
								INNER JOIN INV_CTOS ON INV_CTOS.COD_CTO = INV_TBTAREAS_CTO.COD_CTO";
					
				$stmt_ctos = sqlsrv_query( $conn, $tsql_ctos);

				$array_ctos = array();

				while($row_cto = sqlsrv_fetch_array($stmt_ctos)){
					$array_ctos[] = $row_cto;
				}

				//crear array de gescales bloqueados para asisgnarselos a cada tarea en su linea
				$tsql_gescales="SELECT INV_tbBloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbBloqueos_Gescales.ID AS ID_TAREA_GESCAL
							FROM INV_TBTAREAS
								INNER JOIN INV_tbBloqueos_Gescales ON INV_tbBloqueos_Gescales.ID = INV_TBTAREAS.id";
				
				$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

				$array_gescales = array();

				while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
					$array_gescales[] = $row_gescal;
				}	

				//crear array de gescales desbloqueados para asisgnarselos a cada tarea en su linea
				$tsql_gescales="SELECT INV_tbDesbloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbDesbloqueos_Gescales.ID AS ID_TAREA_GESCAL
							FROM INV_TBTAREAS
								INNER JOIN INV_tbDesbloqueos_Gescales ON INV_tbDesbloqueos_Gescales.ID = INV_TBTAREAS.id";
					
				$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

				while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
					$array_gescales[] = $row_gescal;
				}	

			} else {
				//GARANTIZAMOS QUE HAY REGISTRO EN LA TABLA DE TAREAS
				$tsql = "SELECT * FROM INV_VIEW_DATOS_TODO WHERE ID_TAREA IS NOT NULL";

				$tsql = $tsql . " ";
				if (isset($prior) && $region != "") {
					$tsql = $tsql . " and REGION = '$region'";	
				} 
				if (isset($prior) && $prior != "") {
					$tsql = $tsql . " and PRIORIDAD = '$prior'";	
				} 		
				if (isset($estado) && $estado != "") {
					$tsql = $tsql . " and ESTADO = '$estado'";	
				}		
				if (isset($origenSol) && $origenSol != "") {
					$tsql = $tsql . " and USUORIGEN like '%$origenSol%'";	
				} 		
				if (isset($actividad) && $actividad != "") {
					$tsql = $tsql . " and id_actividad = '$actividad'";	
				}
				if (isset($subactividad) && $subactividad != "") {
					$tsql = $tsql . " and id_Subactividad = '$subactividad'";				
				}
				if (isset($tecnico) && $tecnico != "") {
					$tsql = $tsql . " and TECNICO like '%$tecnico%'";	
				}		
				if (isset($fRegistro1) && $fRegistro1 != "") {
					$tsql = $tsql . " and FECHA_REGISTRO >= '".$fRegistro1."'";
				}
				if (isset($fRegistro2) && $fRegistro2 != "") {
					$tsql = $tsql . " and FECHA_REGISTRO <= '".sumaDias($fRegistro2,1)."'";	
				}	
				if (isset($fInicio1) && $fInicio1 != "") {
								 $tsql = $tsql . " and FECHA_INICIO>='".$fInicio1."'";		 	 
				}				 
				if (isset($fInicio2) && $fInicio2 != "") {
								 $tsql = $tsql . " and FECHA_INICIO<'".sumaDias($fInicio2,1)."'";		

				}				 
				if (isset($fResol1) && $fResol1 != "") {
								 $tsql = $tsql . " and FECHA_RESOL>='".$fResol1."'";		 	 
				}				 
				if (isset($fResol2) && $fResol2 != "") {
								 $tsql = $tsql . " and FECHA_RESOL<'".sumaDias($fResol2,1)."'";				
				}

				//Recuperar datos de consulta
				$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

				//crear array de ctos para asisgnarselos a cada tarea en su linea
				$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
							FROM INV_TBTAREAS
								INNER JOIN INV_TBTAREAS_CTO ON INV_TBTAREAS_CTO.ID = INV_TBTAREAS.id
								INNER JOIN INV_CTOS ON INV_CTOS.COD_CTO = INV_TBTAREAS_CTO.COD_CTO";
					
				$stmt_ctos = sqlsrv_query( $conn, $tsql_ctos);

				$array_ctos = array();

				while($row_cto = sqlsrv_fetch_array($stmt_ctos)){
					$array_ctos[] = $row_cto;
				}

				//crear array de gescales bloqueados para asisgnarselos a cada tarea en su linea
				$tsql_gescales="SELECT INV_tbBloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbBloqueos_Gescales.ID AS ID_TAREA_GESCAL
							FROM INV_TBTAREAS
								INNER JOIN INV_tbBloqueos_Gescales ON INV_tbBloqueos_Gescales.ID = INV_TBTAREAS.id";
				
				$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

				$array_gescales = array();

				while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
					$array_gescales[] = $row_gescal;
				}	

				//crear array de gescales desbloqueados para asisgnarselos a cada tarea en su linea
				$tsql_gescales="SELECT INV_tbDesbloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbDesbloqueos_Gescales.ID AS ID_TAREA_GESCAL
							FROM INV_TBTAREAS
								INNER JOIN INV_tbDesbloqueos_Gescales ON INV_tbDesbloqueos_Gescales.ID = INV_TBTAREAS.id";
					
				$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

				while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
					$array_gescales[] = $row_gescal;
				}	
			}
		}
	} else {
		if (isset($id_ra) && $id_ra != "") {
			$tsql = "SELECT * FROM INV_VIEW_DATOS_TODO WHERE ID_ACTUACION = $id_ra";

			//Recuperar datos de consulta
			$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

			//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

			//crear array de ctos para asisgnarselos a cada tarea en su linea
			$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
						FROM INV_TBTAREAS
							INNER JOIN INV_TBTAREAS_CTO ON INV_TBTAREAS_CTO.ID = INV_TBTAREAS.id
							INNER JOIN INV_CTOS ON INV_CTOS.COD_CTO = INV_TBTAREAS_CTO.COD_CTO";
				
			$stmt_ctos = sqlsrv_query( $conn, $tsql_ctos);

			$array_ctos = array();

			while($row_cto = sqlsrv_fetch_array($stmt_ctos)){
				$array_ctos[] = $row_cto;
			}

			//crear array de gescales bloqueados para asisgnarselos a cada tarea en su linea
			$tsql_gescales="SELECT INV_tbBloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbBloqueos_Gescales.ID AS ID_TAREA_GESCAL
						FROM INV_TBTAREAS
							INNER JOIN INV_tbBloqueos_Gescales ON INV_tbBloqueos_Gescales.ID = INV_TBTAREAS.id";
			
			$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

			$array_gescales = array();

			while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
				$array_gescales[] = $row_gescal;
			}	

			//crear array de gescales desbloqueados para asisgnarselos a cada tarea en su linea
			$tsql_gescales="SELECT INV_tbDesbloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbDesbloqueos_Gescales.ID AS ID_TAREA_GESCAL
						FROM INV_TBTAREAS
							INNER JOIN INV_tbDesbloqueos_Gescales ON INV_tbDesbloqueos_Gescales.ID = INV_TBTAREAS.id";
				
			$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

			while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
				$array_gescales[] = $row_gescal;
			}				
		} else {
			if (isset($id_rd) && $id_rd != "") {
				$tsql = "SELECT * FROM INV_VIEW_DATOS_TODO WHERE ID_ACTUACION = $id_rd";

				//Recuperar datos de consulta
				$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

				//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

				//crear array de ctos para asisgnarselos a cada tarea en su linea
				$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
							FROM INV_TBTAREAS
								INNER JOIN INV_TBTAREAS_CTO ON INV_TBTAREAS_CTO.ID = INV_TBTAREAS.id
								INNER JOIN INV_CTOS ON INV_CTOS.COD_CTO = INV_TBTAREAS_CTO.COD_CTO";
					
				$stmt_ctos = sqlsrv_query( $conn, $tsql_ctos);

				$array_ctos = array();

				while($row_cto = sqlsrv_fetch_array($stmt_ctos)){
					$array_ctos[] = $row_cto;
				}

				//crear array de gescales bloqueados para asisgnarselos a cada tarea en su linea
				$tsql_gescales="SELECT INV_tbBloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbBloqueos_Gescales.ID AS ID_TAREA_GESCAL
							FROM INV_TBTAREAS
								INNER JOIN INV_tbBloqueos_Gescales ON INV_tbBloqueos_Gescales.ID = INV_TBTAREAS.id";
				
				$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

				$array_gescales = array();

				while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
					$array_gescales[] = $row_gescal;
				}	

				//crear array de gescales desbloqueados para asisgnarselos a cada tarea en su linea
				$tsql_gescales="SELECT INV_tbDesbloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbDesbloqueos_Gescales.ID AS ID_TAREA_GESCAL
							FROM INV_TBTAREAS
								INNER JOIN INV_tbDesbloqueos_Gescales ON INV_tbDesbloqueos_Gescales.ID = INV_TBTAREAS.id";
					
				$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

				while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
					$array_gescales[] = $row_gescal;
				}					
			} else {
				if (isset($id_cab) && $id_cab != "") {
					$tsqlCab = "SELECT TOP 1 Cod_Cabecera FROM INV_CABECERAS WHERE Descripcion = '".$id_cab."'";

					$resultado = sqlsrv_query($conn, $tsqlCab);

					if( $resultado === false ) {
				    	die ("Error al ejecutar consulta: ".$tsql);
					} else {
						$rows = sqlsrv_has_rows( $resultado );
						if ($rows === true){	
							$registro = sqlsrv_fetch_array($resultado);
							$Cod_Cabecera = $registro['Cod_Cabecera'];							

							$tsql = "SELECT * FROM INV_VIEW_DATOS_TODO WHERE ID_CABECERA = $Cod_Cabecera";

							//Recuperar datos de consulta
							$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

							//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

							//crear array de ctos para asisgnarselos a cada tarea en su linea
							$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
										FROM INV_TBTAREAS
											INNER JOIN INV_TBTAREAS_CTO ON INV_TBTAREAS_CTO.ID = INV_TBTAREAS.id
											INNER JOIN INV_CTOS ON INV_CTOS.COD_CTO = INV_TBTAREAS_CTO.COD_CTO";
								
							$stmt_ctos = sqlsrv_query( $conn, $tsql_ctos);

							$array_ctos = array();

							while($row_cto = sqlsrv_fetch_array($stmt_ctos)){
								$array_ctos[] = $row_cto;
							}

							//crear array de gescales bloqueados para asisgnarselos a cada tarea en su linea
							$tsql_gescales="SELECT INV_tbBloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbBloqueos_Gescales.ID AS ID_TAREA_GESCAL
										FROM INV_TBTAREAS
											INNER JOIN INV_tbBloqueos_Gescales ON INV_tbBloqueos_Gescales.ID = INV_TBTAREAS.id";
							
							$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

							$array_gescales = array();

							while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
								$array_gescales[] = $row_gescal;
							}	

							//crear array de gescales desbloqueados para asisgnarselos a cada tarea en su linea
							$tsql_gescales="SELECT INV_tbDesbloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbDesbloqueos_Gescales.ID AS ID_TAREA_GESCAL
										FROM INV_TBTAREAS
											INNER JOIN INV_tbDesbloqueos_Gescales ON INV_tbDesbloqueos_Gescales.ID = INV_TBTAREAS.id";
								
							$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

							while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
								$array_gescales[] = $row_gescal;
							}									

						}
					}

					sqlsrv_free_stmt($resultado);	

				}

			}
		}
	}


	// print the page header
	print_theme_header();


?>
			<!-- start: Content -->
		<div id="content" class="span12">
			
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="index.php">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Modificaciones Masivas</a></li>
			</ul>

			<!--FILTROS-->
			<FORM id="masivas" autocomplete="off" METHOD="POST" NAME="opciones"  class="form-horizontal">
				<fieldset>
		    	<div class="row-fluid">

		    		<div class="span3">	
						<div class="control-group">
							<label class="control-label" for="id">ID </label>
							<div class="controls">
								
								<input type="text" id="id" name="id" value="<?php echo $id;?>"/>

							</div>	
						</div>
		    		</div>

					<div class="span3">					
						<div class="control-group">
							<label class="control-label" for="prior">Region </label>
							<div class="controls">
								<?php
											
									$tsql="select Descripcion as REGION from inv_regiones order by Descripcion";
									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta");}
								
									$rows = sqlsrv_has_rows( $stmt );
								
									if ($rows === true){
										
										echo '<SELECT class="span6" tabindex="0" id="region"  name="region" onChange="ListadoProvincias(\'consultas.php?dato=PROVINCIA\', this.value); return false">';			
										
										echo '<option value=""></option>';

										while($row = sqlsrv_fetch_array($stmt)){
											
											echo '<option value="'.$row["REGION"].'" '.(($row["REGION"]==$seleccionadoReg)?'selected="selected"':"").'>'.$row["REGION"].'</option>';

										}
										
										echo '</SELECT>';		
									}

									sqlsrv_free_stmt($stmt);				
								?>
							</div>	
						</div>	
					</div>		  
					<div class="span3">					
						<div class="control-group">
							<div id="resultadoProvincia">
								<label class="control-label" for="prior">Provincia </label>
								<div class="controls">
									<?php
												
										$tsql="select Descripcion as PROVINCIA from inv_provincias order by Descripcion";
										
										$stmt = sqlsrv_query( $conn, $tsql);
									
										if( $stmt === false ){die ("Error al ejecutar consulta");}
									
										$rows = sqlsrv_has_rows( $stmt );
									
										if ($rows === true){
											
											echo '<SELECT class="span6" tabindex="0" id="provincia"  name="provincia" >';				
											
											echo '<option value=""></option>';

											while($row = sqlsrv_fetch_array($stmt)){
												
												echo '<option value="'.$row["PROVINCIA"].'" '.(($row["PROVINCIA"]==$seleccionadoProv)?'selected="selected"':"").'>'.$row["PROVINCIA"].'</option>';

											}
											
											echo '</SELECT>';		
										}
										sqlsrv_free_stmt($stmt);				
									?>
								</div>	
							</div>		
						</div>	
					</div>		  					
				</div>	
				<div class="row-fluid">  		
					<div class="span3">					
						<div class="control-group">
							<label class="control-label" for="prior">Prioridad </label>
							<div class="controls">
								
								<?php
									
									echo '<SELECT class="span6" tabindex="1" id="prior"  name="prior" >';				
										
										echo '<option value="" ></option>';
										echo '<option value="1" '.(('1'==$seleccionadoPrior)?'selected="selected"':"").'>'.'1'.'</option>';
										echo '<option value="2" '.(('2'==$seleccionadoPrior)?'selected="selected"':"").'>'.'2'.'</option>';
										echo '<option value="3" '.(('3'==$seleccionadoPrior)?'selected="selected"':"").'>'.'3'.'</option>';
										echo '<option value="4" '.(('4'==$seleccionadoPrior)?'selected="selected"':"").'>'.'4'.'</option>';
										echo '<option value="5" '.(('5'==$seleccionadoPrior)?'selected="selected"':"").'>'.'5'.'</option>';

									echo '</SELECT>';		
								?>
							</div>	
						</div>			

						<div class="control-group">
							<label class="control-label" for="estado">Estado </label>
							<div class="controls">
								<?php
											
									$tsql="select Estado as ESTADO from INV_tbEstados order by Estado";
									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta");}
								
									$rows = sqlsrv_has_rows( $stmt );
								
									if ($rows === true){
										
										echo '<SELECT class="span6" tabindex="2" id="estado"  name="estado" >';				
										
										echo '<option value=""></option>';

										while($row = sqlsrv_fetch_array($stmt)){
											
											echo '<option value="'.$row["ESTADO"].'" '.(($row["ESTADO"]==$seleccionadoEst)?'selected="selected"':"").'>'.$row["ESTADO"].'</option>';

										}
										
										echo '</SELECT>';		
									}
									sqlsrv_free_stmt($stmt);				
								?>
							</div>	
						</div>

						<div class="control-group">
							<label class="control-label" for="actividad">Actividad </label>
							<div class="controls">
								<?php
											
									$tsql="SELECT id_actividad, Actividad as ACTIVIDAD from INV_tbActividad ORDER BY Actividad";
									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta");}
								
									$rows = sqlsrv_has_rows( $stmt );
								
									if ($rows === true){
										
										echo '<SELECT class="span6" tabindex="3" id="activ"  name="actividad"  onChange="ListadoSubactividadANT(\'consultas.php?dato=SUBACTIVIDAD_ANT\', this.value); return false">';		

										echo '<option value=""></option>';		
										
										while($row = sqlsrv_fetch_array($stmt)){
											
											echo '<option value="'.$row["id_actividad"].'" '.(($row["id_actividad"]==$actividad)?'selected="selected"':"").'>'.$row["ACTIVIDAD"].'</option>';

										}
										
										echo '</SELECT>';		
									}
									sqlsrv_free_stmt($stmt);
												
								?>
							</div>	
						</div>				

					</div>							

					<div class="span3">
					
						<div class="control-group">
							<label class="control-label" for="origenSol">Origen solicitud </label>
							<div class="controls">
								
								<input type="text" id="origenSolTxt" name="origenSolTxt" value="<?php echo $origenSol;?>"/>

								<div id="resultadoOrigenSol">
								</div>									

							</div>	
						</div>		

						<div class="control-group">
							<label class="control-label" for="tecnico">Tecnico </label>
							<div class="controls">
								<input type="text" id="tecnicoTxt" name="tecnicoTxt" value="<?php echo $tecnico;?>"/>

								<div id="resultadoTecnico">
								</div>	

							</div>	
						</div>	

						<div class="control-group">
							<div id="resultadoSubactividad">
								<label class="control-label" for="subactividad">Subactividad </label>
								<div class="controls">
									<?php
										if($_SERVER['REQUEST_METHOD']=='POST' && $comboAct!=""){ 		
											$tsql="SELECT DISTINCT id_Actividad, id_Subactividad, Descripcion as SUBACTIVIDAD from INV_tbSubactividad where id_Actividad ='".$comboAct."' ORDER BY id_Subactividad";
											$stmt = sqlsrv_query( $conn, $tsql);
										
											if( $stmt === false ){die ("Error al ejecutar consulta");}
										
											$rows = sqlsrv_has_rows( $stmt );
										
											if ($rows === true){
												
												echo '<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad">';		

												echo '<option value=""></option>';		
												
												while($row = sqlsrv_fetch_array($stmt)){
													
													echo '<option value="'.$row["id_Subactividad"].'" '.(($row["id_Subactividad"]==$comboSubAct)?'selected="selected"':"").'>'.$row["SUBACTIVIDAD"].'</option>';

												}
												
												echo '</SELECT>';		
											}
											sqlsrv_free_stmt($stmt);
										} else {
											echo '<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad">';		
											echo '<option value=""></option>';	
											echo '</SELECT>';								
										}
										
													
									?>
								</div>	
							</div>
						</div>	
					</div>		

					<div class="span3">
						<div class="control-group">
							<label class="control-label" for="fRegistro1">F. Registro desde</label>
							<div class="controls">
								<?php
									echo '<input tabindex="7" type="text" class="span6 input datepicker date_field" id="fRegistro1"  name="fRegistro1" value="'.$seleccionadofRegistro1.'" data-date-format="yyyy-mm-dd">';
								?>
							</div>
						</div>		

						<div class="control-group">
							<label class="control-label" for="fInicio1">F. Inicio desde</label>
							<div class="controls">
								<?php
									echo '<input tabindex="8" type="text" class="span6 input datepicker date_field" id="fInicio1"  name="fInicio1" value="'.$seleccionadofIni1.'" data-date-format="yyyy-mm-dd">';
								?>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="fResol1">F. Resolución desde</label>
							<div class="controls">
								<?php
									echo '<input tabindex="9" type="text" class="span6 input datepicker date_field" id="fResol1"  name="fResol1" value="'.$seleccionadofResol1.'" data-date-format="yyyy-mm-dd">';
								?>
							</div>
						</div>
					</div>

					<div class="span3">
						<div class="control-group">
							<label class="control-label" for="fRegistro2">F. Registro hasta</label>
							<div class="controls">
								<?php
									echo '<input tabindex="10" type="text" class="span6 input datepicker date_field" id="fRegistro2"  name="fRegistro2" value="'.$seleccionadofRegistro2.'" data-date-format="yyyy-mm-dd">';
								?>
							</div>
						</div>	

						<div class="control-group">
							<label class="control-label" for="fInicio2">F. Inicio hasta</label>
							<div class="controls">
								<?php
									echo '<input tabindex="11" type="text" class="span6 input datepicker date_field" id="fInicio2"  name="fInicio2" value="'.$seleccionadofIni2.'" data-date-format="yyyy-mm-dd">';
								?>
							</div>
						</div>					

						<div class="control-group">
							<label class="control-label" for="fResol2">F. Resolución desde</label>
							<div class="controls">
								<?php
									echo '<input tabindex="12" type="text" class="span6 input datepicker date_field" id="fResol2"  name="fResol2" value="'.$seleccionadofResol2.'" data-date-format="yyyy-mm-dd">';
								?>
							</div>
						</div>

					 </div>	
				</div>
				<div class="row-fluid">  		
					<div class="span1">		
						<INPUT TYPE="submit" class="btn btn-primary" NAME="buscar" id = "buscar" VALUE="Buscar"> 
					</div>
					<div class="span1">		
						<?php if($_SERVER['REQUEST_METHOD']=='POST') { ?>
							<button type="submit" id="eliminar" name="eliminar" value="eliminar" class="btn btn-danger btn-small confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white trash"></i> Eliminar</button>
						<?php } ?>
					</div>	
					<div class="span1">		
						<button type="submit" id="asignar" name="asignar" value="asignar" class="btn btn-warning btn-small confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white trash"></i> Asignar</button>
					</div>
					<div class="span2">			
						<div class="control-group">
							<label class="control-label" for="tecnicoAsign">Tecnico Asignado </label>
							<div class="controls">

								<div id="resultadoTecnicoAsign">

									<?php 
										echo '<select id="tecnicoAsign" name= "tecnicoAsign">';
										$tsql2 = "select INV_tbUSUARIOS.id_usu, INV_tbUSUARIOS.nombre, INV_tbGRUPOS.grupo 
													from INV_tbUSUARIOS 
													inner join INV_tbGRUPOS on INV_tbUSUARIOS.idGrupo = INV_tbGRUPOS.id_grupo 
													where idgrupo like '1' order by INV_tbGRUPOS.grupo";
										
										$stmt2 = sqlsrv_query( $conn, $tsql2)
												or die ("Fallo en la consulta");


										$rows2 = sqlsrv_has_rows( $stmt2 );
										
										if ($rows2 === true){
												
											if($_SERVER['REQUEST_METHOD']=='POST'){
												
												echo '<option value= ""></option>';
												while($row2 = sqlsrv_fetch_array($stmt2)){		
													if($row2["nombre"]==$tecnicoAsign){
														echo '<option value= "'.$row2["id_usu"].'" selected>'.$row2["nombre"].'</option>';
													}
													else{
														echo '<option value= "'.$row2["id_usu"].'">'.$row2["nombre"].'</option>';
													}         
												} 
												
											}
											
											
											else{
												echo '<option value= ""></option>';
												while($row2 = sqlsrv_fetch_array($stmt2)){
													echo '<option value= "'.$row["id_usu"].'">'.$row["nombre"].'</option>';
												}  		
											
											}
											
											
										
										}
										
									
											echo '</select>';			
										
										sqlsrv_free_stmt( $stmt2);	
									?>		
								</div>	

							</div>	
						</div>	
					</div>	
				</div>								
				</fieldset>	

			      
			<!--FIN FILTROS-->  

				<div class="row-fluid">
					<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<?php echo $mensaje;?>
					</div>					
				</div>		

            <!-- Tabla de listado de grupos existentes -->
            
	            <div class="row-fluid sortable ui-sortable">		
					<div class="box span12">
						<div class="box-header" data-original-title>
							<h2><i class="halflings-icon user"></i><span class="break"></span>Listado</h2>
							<div class="box-icon">
								
								<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
								
							</div>
						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">
							  <thead>
								  <tr>
									  <th>x</th>
									  <th>PROV.</th>
									  <th>CABEC.</th>
									  <th class="hidden">ARBOL</th>
									  <th class="hidden">AR_ID_FDTT</th>
									  <th class="hidden">AR_ID_GD</th>
									  <th>ID_ACT</th>
									  <th>AC_JAZZTEL</th>
									  <th>AC_TESA</th>
									  <th class="hidden">AC_ID_FDTT</th>
									  <th class="hidden">AC_ID_GD</th>
									  <th class="hidden">GESTOR</th>
									  <th>REMEDY</th>
									  <th class="hidden">TP</th>
									  <th>REFERENCIA</th>
									  <th class="hidden">REF.ASOCIADA</th>
									  <th>OCEAN</th>
	                                  <th>ACTIVIDAD</th>
									  <th>SUBACTIVIDAD</th>
									  <th>SOLICITANTE</th>
									  <th>TECNICO</th>
									  <th>F.REGIS.</th>
									  <th class="hidden">F.INIC.</th>
									  <th>F.RESOL.</th>
									  <th>ESTADO</th>		
									  <th>P</th>					
									  <th class="hidden">ID_TAREA</th>	
									  <th class="hidden">EEMM</th>	 
									  <th class="hidden">CTOS</th> 
									  <th class="hidden">GESCALES</th> 
								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros)) { while ($linea = sqlsrv_fetch_array($registros)){ ?>
								<tr>
									<?php 
									//Marca de check
										print ("<TD class='center'><INPUT TYPE='CHECKBOX' NAME='marcarProc[]' VALUE='".$linea['ID_TAREA']."'></TD>\n");
									 ?>
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="hidden"><?php echo $linea['ARBOL']; ?></td>
									<td class="hidden"><?php echo $linea['ARBOL_ID_FDTT']; ?></td>
									<td class="hidden"><?php echo $linea['ARBOL_ID_GD']; ?></td>
									<td class="center"><?php echo $linea['ID_ACTUACION']; ?></td>
									<td class="center"><?php echo $linea['ACT_JAZZTEL']; ?></td>
									<td class="center"><?php echo $linea['ACT_TESA']; ?></td>
									<td class="hidden"><?php echo $linea['ACT_ID_FDTT']; ?></td>
									<td class="hidden"><?php echo $linea['ACT_ID_GD']; ?></td>
									<td class="hidden"><?php echo $linea['GESTOR']; ?></td>
									<td class="center"><?php echo $linea['REMEDY']; ?></td>
									<td class="hidden"><?php echo $linea['TP']; ?></td>
									<td class="center"><?php echo $linea['REF_TBTAREA']; ?></td>
									<td class="hidden"><?php echo $linea['REF_ASOCIADA']; ?></td>
									<td class="center"><?php echo $linea['OCEANE_TBTAREA']; ?></td>
									<td class="center"><?php echo $linea['Actividad']; ?></td>
									<td class="center"><?php echo $linea['SUBACTIVIDAD']; ?></td>
									<td class="center"><?php echo $linea['USUORIGEN']; ?></td>
									<td class="center"><?php echo $linea['TECNICO']; ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_REGISTRO'])) {echo date_format($linea['FECHA_REGISTRO'], 'Y-m-d H:i:s'); } ?></td>
									<td class="hidden"><?php if (!empty($linea['FECHA_INICIO'])) {echo date_format($linea['FECHA_INICIO'], 'Y-m-d H:i:s'); } ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_RESOL'])) {echo date_format($linea['FECHA_RESOL'], 'Y-m-d H:i:s'); } ?></td>
									<td class="center"><?php echo $linea['ESTADO']; ?></td>
									<td class="center"><?php echo $linea['PRIORIDAD']; ?></td>
									<td class="hidden"><?php echo $linea['ID_TAREA']; ?></td>
									<td class="hidden"><?php echo $linea['EEMM']; ?></td>
									<td class="hidden">
										<?php
											//$array_ctos_tarea = array_keys($array_ctos, $linea['ID_TAREA']);		//No funciona porque es bidimensional
										    foreach($array_ctos as $key=>$data) {
										    	if ($data['ID_TAREA_CTO'] == $linea['ID_TAREA']) {
										    		echo $data['NUMERO_CTO']." - ";
										    	}
										    }										
										?>
									</td>
									
									<td class="hidden">
										<?php
											
										    foreach($array_gescales as $key=>$data) {
										    	if ($data['ID_TAREA_GESCAL'] == $linea['ID_TAREA']) {
										    		echo $data['GESCAL']." - ";
										    	}
										    }										
										?>
									</td>
	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>
	        </FORM> <!-- Fin formulario -->


	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->
		
	
<?php
	print_theme_footer();
	sqlsrv_free_stmt($registros);
?>
<script type="text/JavaScript">
		//Inicializamos tareas del onload de la página
		window.onload = function ()
		{

			document.getElementById("origenSolTxt").onkeyup = ListadoUsuOrigen;
			document.getElementById("tecnicoTxt").onkeyup = ListadoTecnico;

		}



</script>