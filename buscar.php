<?php
        // Inicio del documento Javi
        // Calculamos segundos y microsegundos desde Epoch Unix
/*
        $tiempo = microtime();
        // Separamos en un array el tiempo en segundos y en microsegundos
        $tiempo = explode(" ",$tiempo);
        // Sumamos segundos y microsegundos
        $tiempo_inicial = $tiempo[0] + $tiempo[1];*/

	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";


	//Si el usuario no está autorizado se le desconecta
	$rolUsuario=get_rol($_SESSION['usuario']);
	if ($rolUsuario != 'lectura' && $rolUsuario != 'escritura' && $rolUsuario != 'avanzado') {
		header('Location: index.php?mensaje=Usuario%20desconectado');
	}	
	$restriccion=get_restriccion($_SESSION['usuario']);
        $regiones = split(";", $restriccion);
        
        //echo ($regiones[0]); exit();
        if(!empty($regiones)){
            
            $restriccion = '';
            foreach($regiones as $reg){
                $restriccion = $restriccion . "'" . $reg. "'" .',' ;
            }
            $restriccion= substr($restriccion,0, strlen($restriccion)-1);
            $restriccion= '(' . $restriccion . ')';
        }  
        //echo $restriccion; exit();

	//Inicializa las variables utilizadas en el formulario
	$mensaje = "";

	$id_ra = ""; 
	$id_rd = ""; 
	$id_cab = "";
	$id = "";

    $seleccionadoRef = "";
    $seleccionadoProv = "";
    $seleccionadoReg = "";
    $seleccionadoGestor = "";
    $seleccionadoCab = "";
    $seleccionadoIdGd = "";
    $seleccionadoActTesa = "";
    $seleccionadoSuc = "";
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
    $seleccionadoEscalado="";

    $ref = "";    
    $gestor = "";    
    $provincia = "";    
    $region = "";    
    $cabecera = "";    
    $idgd = "";    
    $acttesa = "";    
    $suc = "";
    $incidencia = "";    
    $actjazz = "";    
    $estado = "";    
	$tecnico = ""; 
	$tecnicoAsign = "";   
    $actividad = "";    
    $subactividad = "";
    $tipoIncidencia = "";      
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
    $cto  = "";  
    $escalado = "";   
    
    $comboAct = "";    
    $comboSubAct = "";    
    $comboTipInc = "";

      
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
        $seleccionadoSuc = $_REQUEST['suc'];   
        $seleccionadoInc= $_REQUEST['incidencia'];
        $seleccionadoActJazz = $_REQUEST['actjazz'];
        $seleccionadoEst = $_REQUEST['estado'];
        $seleccionadoTecn = $_REQUEST['tecnico'];
        $seleccionadoTecnAsign = $_REQUEST['tecnicoAsign'];
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
 	$seleccionadoEscalado= $_REQUEST['escalado'];
	    $ref=  $_REQUEST['ref'];
	    $gestor = $_REQUEST['gestor'];
	    $provincia = $_REQUEST['provincia'];
	    $region = $_REQUEST['region'];
	    $cabecera = $_REQUEST['cabecera'];
	    $idgd = $_REQUEST['idgd'];
	    $acttesa = $_REQUEST['acttesa'];
            $suc = $_REQUEST['suc'];
	    $incidencia = $_REQUEST['incidencia'];
	    $actjazz = $_REQUEST['actjazz'];
	    $estado = $_REQUEST['estado'];
	    $cto = $_REQUEST['cto'];


	 	if (isset($_REQUEST['tecnico']) && $_REQUEST['tecnico'] != '') {
			$tecnico = $_REQUEST['tecnico'];
		} else {
			$tecnico = $_POST['tecnicoTxt'];
		}    

	    //$tecnico = $_REQUEST['tecnico'];
	    $tecnicoAsign = $_REQUEST['tecnicoAsign'];
	    $actividad = $_REQUEST['actividad'];
	    $subactividad = $_REQUEST['subactividad'];
            $tipoIncidencia = $_REQUEST['tipoIncidencia'];

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
	     $escalado= $_REQUEST['escalado'];
	    $comboAct=$_REQUEST['actividad'];
            $comboSubAct=$_REQUEST['subactividad'];	   
            $comboTipInc=$_REQUEST['tipoIncidencia'];	  
            $id=$_REQUEST['id'];	
               
        
    } else {
    	$id_ra = $_GET['id_ra'];
    	$id_rd = $_GET['id_rd'];
    	$id_cab = $_GET['id_cab'];
    }
  


    //FIN Inicializa las variables utilizadas en el formulario

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	//Variables para la búsqueda
	$maxRows = 10;
	$pageNum = 0;
	$seleccionada = 0;
	if (isset($_GET['pageNum'])) {
	  $pageNum = $_GET['pageNum'];
	}
	$startRow = $pageNum * $maxRows;


	if($_SERVER['REQUEST_METHOD']=='POST') {

                

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
						$tsql2 = "DELETE FROM INV_tbArchivos WHERE idTarea = '$marcarProc[$i]'";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
										
						sqlsrv_free_stmt( $stmt2);	



						//elimina los bloqueos. OBLIGATORIO hacerlo antes de eliminar la tarea
						$tsql2 = "DELETE FROM INV_tbTareas_Bloqueos WHERE id_Tarea = '$marcarProc[$i]'";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
										
						sqlsrv_free_stmt( $stmt2);	


						//elimina los desbloqueos. OBLIGATORIO hacerlo antes de eliminar la tarea
						$tsql2 = "DELETE FROM INV_tbTareas_Desbloqueos WHERE id_Tarea = '$marcarProc[$i]'";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
						sqlsrv_free_stmt( $stmt2);				
										


						//elimina los gescales bloqueados. OBLIGATORIO hacerlo antes de eliminar las CTOS afectadas
						$tsql2 = "DELETE FROM INV_tbBloqueos_Gescales WHERE id = '$marcarProc[$i]'";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
						sqlsrv_free_stmt( $stmt2);	
								
								
						//elimina los gescales desbloqueados. OBLIGATORIO hacerlo antes de eliminar las CTOS afectadas
						$tsql2 = "DELETE FROM INV_tbDesbloqueos_Gescales WHERE id = '$marcarProc[$i]'";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
						sqlsrv_free_stmt( $stmt2);	

						//elimina las CTOS afectadas de la tarea. OBLIGATORIO hacerlo antes de eliminar la tarea
						$tsql2 = "DELETE FROM INV_tbTareas_CTO WHERE id = '$marcarProc[$i]'";

						$stmt2 = sqlsrv_query( $conn, $tsql2);
						sqlsrv_free_stmt( $stmt2);
								
						//Busca el id de la tarea que se elimina en la tabla tbTipoReasignacion. OBLIGATORIO hacerlo antes de eliminar la tarea
						$tsql3 = "SELECT id, id_Tarea FROM INV_tbTipoReasignacion WHERE id_Tarea = '$marcarProc[$i]'";
															 
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
						$tsql5 = "DELETE FROM INV_tbTipoReasignacion WHERE id_Tarea = '$marcarProc[$i]'";
																 
						$stmt5 = sqlsrv_query( $conn, $tsql5);
						
						
						//elimina la tarea de tbTipoIncidencia
						$tsql = "DELETE FROM INV_tbTipoIncidencia WHERE id_Tarea = '$marcarProc[$i]'";

						//elimina la tarea del histórico de tareas
						$tsql = "DELETE FROM INV_HISTORICO_TAREAS WHERE ID_TAREA = '$marcarProc[$i]'";						
													
						$stmt = sqlsrv_query( $conn, $tsql);
													
						//elimina la tarea de tbTareas
						$tsql = "DELETE FROM INV_tbTareas WHERE id = '$marcarProc[$i]'";
													
						$stmt = sqlsrv_query( $conn, $tsql);														
															
					}
																
				

					/* Si la última sentencia finalizó con éxito, consolidar la transacción. */
					/* En caso contrario, revertirla. */
					if( $stmt === false ) {
						 sqlsrv_rollback( $conn );
						 $mensaje = 'No se realiza la eliminación. Algún registro no pudo eliminarse'.$tsql;				    	
						 
					} else {
						sqlsrv_commit( $conn );
						$mensaje = 'Se han eliminado '.$nfilas.' tareas';

					}
				}	
					
			} else {
				$mensaje = 'Debe marcar las tareas que desee eliminar';
			}
				
		}
		//FIN ELIMINAR TAREA

                //ASOCIAR TAREAS POR REFERENCIA
                /*if (isset($_REQUEST['linar'])) {	
                         $marcarProc = $_REQUEST['marcarProc'];	
                        $nfilas = count ($marcarProc);
                       			  
			if ($nfilas>0){
                            for ($i=0; $i<$nfilas; $i++) {
				$seleccionados = $seleccionados . ';'. $marcarProc[$i];
                            }
                           $prueba = '<script language="JavaScript"> alert("Error. Vuelve a identificarte"); </script>';
                            echo $prueba;
                            
             
                            
                       $prueba2 = '<script language="JavaScript"> 
                               var dataString = 900000;
                            alert (dataString);
                            $.ajax({
                                type: "GET",
                                url: "consultaHistoriaTarea.php",
                                data: dataString,
                                cache: false,
                                success: function (data) {
                                console.log(data);
                                $(modal).find(".ct").html(data);
                            },
                            error: function(err) {
                            console.log(err);
                            }
                        });  

                     

</script>';

        
                       
                       echo $prueba2;
                            
				
                        } else {
				$mensaje = 'Debe marcar las tareas que desee asociar';
			}
                        
                        echo $seleccionados;
                        exit();
                }*/


		//ASIGNAR NUEVA TAREA
		if (isset($_REQUEST['asignar'])) {	

			//busca el id del estado 'En proceso' (para actualizarlo en la tabla tbTareas)		
			$tsql="SELECT id_Estado FROM INV_tbEstados WHERE Estado='En proceso'";
			$stmt = sqlsrv_query( $conn, $tsql)	or die ("Fallo en la consulta");
			
			while($row = sqlsrv_fetch_array($stmt)){	
				$idEstProc= $row["id_Estado"]; 
			}					
				
			sqlsrv_free_stmt( $stmt);		
			
			if(($rolUsuario=='avanzado') || ($rolUsuario=='escritura')){		
                                //con el rol de escritura solo se pueden asignar tareas asi mismo
				if (($tecnicoAsign == '') || ($rolUsuario=='escritura')){										
					//Si no informa el técnico se le asigna a si mismo
					$tsql="SELECT id_usu FROM INV_tbUsuarios WHERE usuario='".$_SESSION['usuario']."'";
					
					$stmt = sqlsrv_query( $conn, $tsql)	 or die ("Fallo en la consulta");
					
					while($row = sqlsrv_fetch_array($stmt)){	
							$tecnicoAsign= $row["id_usu"]; 
					}								
					sqlsrv_free_stmt( $stmt);
				}
                                
                                                               
				//busca nombre del técnico seleccinado en el input Técnico asignado
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
							$stmt = sqlsrv_query( $conn, $tsql)	or die ("Fallo en la actualización: ".$tsql);

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
							$stmt = sqlsrv_query( $conn, $tsql)	or die ("Fallo en la actualización: ".$tsql);

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
				/*if($rolUsuario=='escritura'){

					
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
							
				} else {*/
					$mensaje = "Rol de Usuario no permitido";
				//}
				//fin usuario escritura
			}

		}
		// FIN ASIGNAR NUEVA TAREA




		//Que haga la búsqueda con los criterios aunque se haya dado a asignar o a eliminar
		//if ($_POST['buscar']) {
			if (isset($id) && $id != "") {
				//sI HAY RESTRICCION DE REGION
                                if (isset($restriccion) && $restriccion != "('Todas')" && $seleccionadoReg == "")	{	
                                    
                                    $tsql = "SELECT TOP (100) PERCENT dbo.inv_regiones.Cod_Region AS ID_REGION, dbo.inv_regiones.Descripcion AS REGION, dbo.inv_provincias.Cod_Provincia AS ID_PROVINCIA, dbo.inv_provincias.Descripcion AS PROVINCIA, 
                                             dbo.inv_cabeceras.Cod_Cabecera AS ID_CABECERA, dbo.inv_cabeceras.Descripcion AS CABECERA, 
                                              dbo.inv_actuaciones.ACT_JAZZTEL, dbo.inv_actuaciones.ACT_TESA, dbo.inv_actuaciones.ID_FDTT AS ACT_ID_FDTT, dbo.inv_actuaciones.ID_GD AS ACT_ID_GD, 
                                             dbo.inv_actuaciones.GESTOR, dbo.inv_actuaciones.HUELLA, dbo.INV_TBTAREAS.INCIDENCIA AS REMEDY, dbo.INV_TBTAREAS.REF AS REF_TBTAREA, dbo.INV_TBTAREAS.REF_ASOCIADA, dbo.INV_TBTAREAS.TP, 
                                             dbo.INV_TBTAREAS.TICKET_OCEANE AS OCEANE_TBTAREA, dbo.INV_TBTAREAS.ESCALADO AS ESCALADO_TBTAREA, dbo.INV_tbActividad.id_actividad, dbo.INV_tbActividad.Actividad, dbo.INV_tbSubactividad.id_Subactividad, 
                                             dbo.INV_tbSubactividad.Descripcion AS SUBACTIVIDAD, dbo.INV_tbEstados.Estado, usuarios1.nombre AS TECNICO, usuarios2.nombre AS USUORIGEN, dbo.INV_TBTAREAS.PRIORIDAD, dbo.INV_TBTAREAS.FECHA_REGISTRO, 
                                             dbo.INV_TBTAREAS.FECHA_INICIO, dbo.INV_TBTAREAS.FECHA_RESOL, dbo.INV_TBTAREAS.id AS ID_TAREA, dbo.INV_TBTAREAS.EEMM
                                             , dbo.INV_TBTAREAS.TIPO_INCIDENCIA, dbo.INV_TBTAREAS.SUC
                                                    FROM            dbo.INV_TBTAREAS LEFT OUTER JOIN
                                             dbo.inv_actuaciones ON dbo.INV_TBTAREAS.id_Actuacion = dbo.inv_actuaciones.ID_ACTUACION LEFT OUTER JOIN
                                             dbo.inv_cabeceras ON dbo.inv_actuaciones.COD_CABECERA = dbo.inv_cabeceras.Cod_Cabecera LEFT OUTER JOIN
                                             dbo.inv_provincias ON dbo.inv_cabeceras.Cod_Provincia = dbo.inv_provincias.Cod_Provincia LEFT OUTER JOIN
                                             dbo.inv_regiones ON dbo.inv_provincias.Cod_Region = dbo.inv_regiones.Cod_Region LEFT OUTER JOIN
                                             dbo.INV_tbUSUARIOS AS usuarios1 ON usuarios1.id_usu = dbo.INV_TBTAREAS.idTecn LEFT OUTER JOIN
                                             dbo.INV_tbUSUARIOS AS usuarios2 ON usuarios2.id_usu = dbo.INV_TBTAREAS.idUsuOrigen LEFT OUTER JOIN
                                             dbo.INV_tbSubactividad ON dbo.INV_tbSubactividad.id_Subactividad = dbo.INV_TBTAREAS.id_Subactividad LEFT OUTER JOIN
                                             dbo.INV_tbActividad ON dbo.INV_tbSubactividad.id_Actividad = dbo.INV_tbActividad.id_actividad LEFT OUTER JOIN
                                             dbo.INV_tbEstados ON dbo.INV_tbEstados.id_Estado = dbo.INV_TBTAREAS.idEst
                                                WHERE (dbo.INV_TBTAREAS.REF LIKE '%".$id."%' OR dbo.INV_TBTAREAS.INCIDENCIA LIKE '%".$id."%' OR dbo.INV_TBTAREAS.REF_ASOCIADA LIKE '%".$id."%' OR dbo.INV_TBTAREAS.TP LIKE '%".$id."%' OR dbo.INV_TBTAREAS.TICKET_OCEANE LIKE '%".$id."%' OR dbo.INV_TBTAREAS.ESCALADO LIKE '%".$id."%' OR dbo.INV_TBTAREAS.id LIKE '%".$id."%')
                                                AND dbo.inv_regiones.Descripcion IN $region
                                            GROUP BY dbo.inv_regiones.Cod_Region, dbo.inv_regiones.Descripcion, dbo.inv_provincias.Cod_Provincia, dbo.inv_provincias.Descripcion, dbo.inv_cabeceras.Cod_Cabecera, dbo.inv_cabeceras.Descripcion,
                                             dbo.inv_actuaciones.ACT_JAZZTEL, dbo.inv_actuaciones.ACT_TESA, dbo.inv_actuaciones.ID_FDTT, dbo.inv_actuaciones.ID_GD, 
                                             dbo.inv_actuaciones.GESTOR, dbo.inv_actuaciones.HUELLA, dbo.INV_TBTAREAS.INCIDENCIA, dbo.INV_TBTAREAS.REF, dbo.INV_TBTAREAS.REF_ASOCIADA, dbo.INV_TBTAREAS.TP, dbo.INV_TBTAREAS.TICKET_OCEANE, 
                                             dbo.INV_TBTAREAS.ESCALADO, dbo.INV_tbActividad.id_actividad, dbo.INV_tbActividad.Actividad, dbo.INV_tbSubactividad.id_Subactividad, dbo.INV_tbSubactividad.Descripcion, dbo.INV_tbEstados.Estado, usuarios1.nombre, 
                                             usuarios2.nombre, dbo.INV_TBTAREAS.PRIORIDAD, dbo.INV_TBTAREAS.FECHA_REGISTRO, dbo.INV_TBTAREAS.FECHA_INICIO, dbo.INV_TBTAREAS.FECHA_RESOL, dbo.INV_TBTAREAS.id, dbo.INV_TBTAREAS.EEMM, 
                                              dbo.INV_TBTAREAS.TIPO_INCIDENCIA, dbo.INV_TBTAREAS.SUC"; 

                                    
                                }else{
                            
                            
                                       $tsql = "SELECT TOP (100) PERCENT dbo.inv_regiones.Cod_Region AS ID_REGION, dbo.inv_regiones.Descripcion AS REGION, dbo.inv_provincias.Cod_Provincia AS ID_PROVINCIA, dbo.inv_provincias.Descripcion AS PROVINCIA, 
                                             dbo.inv_cabeceras.Cod_Cabecera AS ID_CABECERA, dbo.inv_cabeceras.Descripcion AS CABECERA, 
                                              dbo.inv_actuaciones.ACT_JAZZTEL, dbo.inv_actuaciones.ACT_TESA, dbo.inv_actuaciones.ID_FDTT AS ACT_ID_FDTT, dbo.inv_actuaciones.ID_GD AS ACT_ID_GD, 
                                             dbo.inv_actuaciones.GESTOR, dbo.inv_actuaciones.HUELLA, dbo.INV_TBTAREAS.INCIDENCIA AS REMEDY, dbo.INV_TBTAREAS.REF AS REF_TBTAREA, dbo.INV_TBTAREAS.REF_ASOCIADA, dbo.INV_TBTAREAS.TP, 
                                             dbo.INV_TBTAREAS.TICKET_OCEANE AS OCEANE_TBTAREA, dbo.INV_TBTAREAS.ESCALADO AS ESCALADO_TBTAREA, dbo.INV_tbActividad.id_actividad, dbo.INV_tbActividad.Actividad, dbo.INV_tbSubactividad.id_Subactividad, 
                                             dbo.INV_tbSubactividad.Descripcion AS SUBACTIVIDAD, dbo.INV_tbEstados.Estado, usuarios1.nombre AS TECNICO, usuarios2.nombre AS USUORIGEN, dbo.INV_TBTAREAS.PRIORIDAD, dbo.INV_TBTAREAS.FECHA_REGISTRO, 
                                             dbo.INV_TBTAREAS.FECHA_INICIO, dbo.INV_TBTAREAS.FECHA_RESOL, dbo.INV_TBTAREAS.id AS ID_TAREA, dbo.INV_TBTAREAS.EEMM
                                             , dbo.INV_TBTAREAS.TIPO_INCIDENCIA, dbo.INV_TBTAREAS.SUC
                                                    FROM            dbo.INV_TBTAREAS LEFT OUTER JOIN
                                             dbo.inv_actuaciones ON dbo.INV_TBTAREAS.id_Actuacion = dbo.inv_actuaciones.ID_ACTUACION LEFT OUTER JOIN
                                             dbo.inv_cabeceras ON dbo.inv_actuaciones.COD_CABECERA = dbo.inv_cabeceras.Cod_Cabecera LEFT OUTER JOIN
                                             dbo.inv_provincias ON dbo.inv_cabeceras.Cod_Provincia = dbo.inv_provincias.Cod_Provincia LEFT OUTER JOIN
                                             dbo.inv_regiones ON dbo.inv_provincias.Cod_Region = dbo.inv_regiones.Cod_Region LEFT OUTER JOIN
                                             dbo.INV_tbUSUARIOS AS usuarios1 ON usuarios1.id_usu = dbo.INV_TBTAREAS.idTecn LEFT OUTER JOIN
                                             dbo.INV_tbUSUARIOS AS usuarios2 ON usuarios2.id_usu = dbo.INV_TBTAREAS.idUsuOrigen LEFT OUTER JOIN
                                             dbo.INV_tbSubactividad ON dbo.INV_tbSubactividad.id_Subactividad = dbo.INV_TBTAREAS.id_Subactividad LEFT OUTER JOIN
                                             dbo.INV_tbActividad ON dbo.INV_tbSubactividad.id_Actividad = dbo.INV_tbActividad.id_actividad LEFT OUTER JOIN
                                             dbo.INV_tbEstados ON dbo.INV_tbEstados.id_Estado = dbo.INV_TBTAREAS.idEst
                                                WHERE dbo.INV_TBTAREAS.REF LIKE '%".$id."%' OR dbo.INV_TBTAREAS.INCIDENCIA LIKE '%".$id."%' OR dbo.INV_TBTAREAS.REF_ASOCIADA LIKE '%".$id."%' OR dbo.INV_TBTAREAS.TP LIKE '%".$id."%' OR dbo.INV_TBTAREAS.TICKET_OCEANE LIKE '%".$id."%' OR dbo.INV_TBTAREAS.ESCALADO LIKE '%".$id."%' OR dbo.INV_TBTAREAS.id LIKE '%".$id."%'
                                            GROUP BY dbo.inv_regiones.Cod_Region, dbo.inv_regiones.Descripcion, dbo.inv_provincias.Cod_Provincia, dbo.inv_provincias.Descripcion, dbo.inv_cabeceras.Cod_Cabecera, dbo.inv_cabeceras.Descripcion,
                                             dbo.inv_actuaciones.ACT_JAZZTEL, dbo.inv_actuaciones.ACT_TESA, dbo.inv_actuaciones.ID_FDTT, dbo.inv_actuaciones.ID_GD, 
                                             dbo.inv_actuaciones.GESTOR, dbo.inv_actuaciones.HUELLA, dbo.INV_TBTAREAS.INCIDENCIA, dbo.INV_TBTAREAS.REF, dbo.INV_TBTAREAS.REF_ASOCIADA, dbo.INV_TBTAREAS.TP, dbo.INV_TBTAREAS.TICKET_OCEANE, 
                                             dbo.INV_TBTAREAS.ESCALADO, dbo.INV_tbActividad.id_actividad, dbo.INV_tbActividad.Actividad, dbo.INV_tbSubactividad.id_Subactividad, dbo.INV_tbSubactividad.Descripcion, dbo.INV_tbEstados.Estado, usuarios1.nombre, 
                                             usuarios2.nombre, dbo.INV_TBTAREAS.PRIORIDAD, dbo.INV_TBTAREAS.FECHA_REGISTRO, dbo.INV_TBTAREAS.FECHA_INICIO, dbo.INV_TBTAREAS.FECHA_RESOL, dbo.INV_TBTAREAS.id, dbo.INV_TBTAREAS.EEMM, 
                                              dbo.INV_TBTAREAS.TIPO_INCIDENCIA, dbo.INV_TBTAREAS.SUC"; 
				
                                
                                }
                               /* echo ("1");
				echo $tsql; */	
				$_SESSION['TSQL_INCIDENCIAS'] = $tsql;		
				//Recuperar datos de consulta
				$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

				//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

				//crear array de ctos para asisgnarselos a cada tarea en su linea
				/*$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
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
				}	*/

			} else {


				if (isset($cto) && $cto != "") {

					$tsqlCto = "SELECT top 1 COD_CTO FROM INV_CTOS WHERE NUMERO = '".$cto."'";

					$resultado = sqlsrv_query($conn, $tsqlCto);

					if( $resultado === false ) {
				    	die ("Error al ejecutar consulta: ".$tsqlCto);
					} else {

						$rows = sqlsrv_has_rows( $resultado );

						if ($rows === true){	

							$registro = sqlsrv_fetch_array($resultado);
							$Cod_Cto = $registro['COD_CTO'];	

							$tsqlCto = "SELECT id FROM INV_TBTAREAS_CTO WHERE COD_CTO = '".$Cod_Cto."'";

							$resultado = sqlsrv_query($conn, $tsqlCto);

							if( $resultado === false ) {
						    	die ("Error al ejecutar consulta: ".$tsqlCto);
							} else {

								$rows = sqlsrv_has_rows( $resultado );

								if ($rows === true){	

									$reg_ctos = sqlsrv_fetch_array($resultado);

									$tsql = "SELECT TOP 1000 * FROM INV_VIEW_DATOS_TODO WHERE (ID_TAREA = '".$reg_ctos[id]."'";

									while($reg_ctos = sqlsrv_fetch_array($resultado)){
										
										$tsql = $tsql . " or ID_TAREA = '".$reg_ctos[id]."'";
									}									
									
									$tsql = $tsql . " )";


									//Si hay restricción de región
									if (isset($restriccion) && $restriccion != "('Todas')" && $seleccionadoReg == "")	{		 
										$region = $restriccion;	
										if (isset($region) && $region != "") {
											$tsql = $tsql . " and REGION IN $region"; 
										} 	
									}



									if (isset($actjazz) && $actjazz != "") {
										$tsql = $tsql . " and ACT_JAZZTEL like '%$actjazz%'";
									} 

									if (isset($acttesa) && $acttesa != "") {
										$tsql = $tsql . " and ACT_TESA like '%$acttesa%'";
									} 
                                                                        
                                                                        if (isset($suc) && $suc != "") {
										$tsql = $tsql . " and SUC like '%$suc%'";
									} 

									if (isset($region) && $region != "" && $seleccionadoReg != "") {
										$tsql = $tsql . " and REGION = '$region'";	
									} 
									if (isset($prior) && $prior != "") {
										$tsql = $tsql . " and PRIORIDAD = '$prior'";	
									} 	

									// if (isset($estado) && $estado != "") {
									// 	$tsql = $tsql . " and ESTADO = '$estado'";	
									// }

									if (count($estado) > 0) {
										$tsql = $tsql . " and ( ESTADO = '".$estado[0]."'";
										for ($i=1;$i<count($estado);$i++)    
										{     
											$tsql = $tsql . " or ESTADO = '".$estado[$i]."'";
										}				
										$tsql = $tsql . " )";
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
                                                                        if (isset($tipoIncidencia) && $tipoIncidencia != "") {
										$tsql = $tsql . " and TIPO_INCIDENCIA = '$tipoIncidencia'";				
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
                                                                        /*echo ("2");
                                                                        echo $tsql; exit();	*/  
									$_SESSION['TSQL_INCIDENCIAS'] = $tsql;	
                                                                     
									//Recuperar datos de consulta
									$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

									//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

									//crear array de ctos para asisgnarselos a cada tarea en su linea
									/*$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
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
									}*/


								}	
							}	

						}	
					}	

				} else {

					//$tsql = "SELECT TOP 1000 * FROM INV_VIEW_DATOS_TODO WHERE ID_TAREA IS NOT NULL";
                                        $tsql = "SELECT TOP (1000) dbo.inv_regiones.Cod_Region AS ID_REGION, dbo.inv_regiones.Descripcion AS REGION, dbo.inv_provincias.Cod_Provincia AS ID_PROVINCIA, dbo.inv_provincias.Descripcion AS PROVINCIA, 
                                             dbo.inv_cabeceras.Cod_Cabecera AS ID_CABECERA, dbo.inv_cabeceras.Descripcion AS CABECERA, 
                                              dbo.inv_actuaciones.ACT_JAZZTEL, dbo.inv_actuaciones.ACT_TESA, dbo.inv_actuaciones.ID_FDTT AS ACT_ID_FDTT, dbo.inv_actuaciones.ID_GD AS ACT_ID_GD, 
                                             dbo.inv_actuaciones.GESTOR, dbo.inv_actuaciones.HUELLA, dbo.INV_TBTAREAS.INCIDENCIA AS REMEDY, dbo.INV_TBTAREAS.REF AS REF_TBTAREA, dbo.INV_TBTAREAS.REF_ASOCIADA, dbo.INV_TBTAREAS.TP, 
                                             dbo.INV_TBTAREAS.TICKET_OCEANE AS OCEANE_TBTAREA, dbo.INV_TBTAREAS.ESCALADO AS ESCALADO_TBTAREA, dbo.INV_tbActividad.id_actividad, dbo.INV_tbActividad.Actividad, dbo.INV_tbSubactividad.id_Subactividad, 
                                             dbo.INV_tbSubactividad.Descripcion AS SUBACTIVIDAD, dbo.INV_tbEstados.Estado, usuarios1.nombre AS TECNICO, usuarios2.nombre AS USUORIGEN, dbo.INV_TBTAREAS.PRIORIDAD, dbo.INV_TBTAREAS.FECHA_REGISTRO, 
                                             dbo.INV_TBTAREAS.FECHA_INICIO, dbo.INV_TBTAREAS.FECHA_RESOL, dbo.INV_TBTAREAS.id AS ID_TAREA, dbo.INV_TBTAREAS.EEMM
                                             , dbo.INV_TBTAREAS.TIPO_INCIDENCIA, dbo.INV_TBTAREAS.SUC
                                                    FROM            dbo.INV_TBTAREAS LEFT OUTER JOIN
                                             dbo.inv_actuaciones ON dbo.INV_TBTAREAS.id_Actuacion = dbo.inv_actuaciones.ID_ACTUACION LEFT OUTER JOIN
                                             dbo.inv_cabeceras ON dbo.inv_actuaciones.COD_CABECERA = dbo.inv_cabeceras.Cod_Cabecera LEFT OUTER JOIN
                                             dbo.inv_provincias ON dbo.inv_cabeceras.Cod_Provincia = dbo.inv_provincias.Cod_Provincia LEFT OUTER JOIN
                                             dbo.inv_regiones ON dbo.inv_provincias.Cod_Region = dbo.inv_regiones.Cod_Region LEFT OUTER JOIN
                                             dbo.INV_tbUSUARIOS AS usuarios1 ON usuarios1.id_usu = dbo.INV_TBTAREAS.idTecn LEFT OUTER JOIN
                                             dbo.INV_tbUSUARIOS AS usuarios2 ON usuarios2.id_usu = dbo.INV_TBTAREAS.idUsuOrigen LEFT OUTER JOIN
                                             dbo.INV_tbSubactividad ON dbo.INV_tbSubactividad.id_Subactividad = dbo.INV_TBTAREAS.id_Subactividad LEFT OUTER JOIN
                                             dbo.INV_tbActividad ON dbo.INV_tbSubactividad.id_Actividad = dbo.INV_tbActividad.id_actividad LEFT OUTER JOIN
                                             dbo.INV_tbEstados ON dbo.INV_tbEstados.id_Estado = dbo.INV_TBTAREAS.idEst
                                                WHERE INV_TBTAREAS.ID IS NOT NULL";
                                          
					//Si hay restricción de región
					if (isset($restriccion) && $restriccion != "('Todas')" && $seleccionadoReg == "") {			 
						$region = $restriccion;	
						if (isset($region) && $region != "") {
							$tsql = $tsql . " and dbo.inv_regiones.Descripcion IN $region"; 
						} 	
					}


					if (isset($actjazz) && $actjazz != "") {
						$tsql = $tsql . " and dbo.inv_actuaciones.ACT_JAZZTEL like '%$actjazz%'";
					} 

					if (isset($acttesa) && $acttesa != "") {
						$tsql = $tsql . " and dbo.inv_actuaciones.ACT_TESA like '%$acttesa%'";
					} 	
                                        
                                        if (isset($suc) && $suc != "") {
						$tsql = $tsql . " and dbo.INV_TBTAREAS.SUC like '%$suc%'";
					} 

					if (isset($region) && $region != "" && $seleccionadoReg != "") {
						$tsql = $tsql . " and dbo.inv_regiones.Descripcion = '$region'";	
					} 
					if (isset($prior) && $prior != "") {
						$tsql = $tsql . " and dbo.INV_TBTAREAS.PRIORIDAD = '$prior'";	
					} 	

					// if (isset($estado) && $estado != "") {
					// 	$tsql = $tsql . " and ESTADO = '$estado'";	
					// }

					if (count($estado) > 0) { 
						$tsql = $tsql . " and ( dbo.INV_tbEstados.ESTADO = '".$estado[0]."'";
						for ($i=1;$i<count($estado);$i++)    
						{     
							$tsql = $tsql . " or dbo.INV_tbEstados.ESTADO = '".$estado[$i]."'";
						}				
						$tsql = $tsql . " )";
					}	

					if (isset($origenSol) && $origenSol != "") {
						$tsql = $tsql . " and usuarios2.nombre like '%$origenSol%'";	
					} 		
					if (isset($actividad) && $actividad != "") {
						$tsql = $tsql . " and dbo.INV_tbActividad.id_actividad = '$actividad'";	
					}
					if (isset($subactividad) && $subactividad != "") {
						$tsql = $tsql . " and dbo.INV_tbSubactividad.id_Subactividad = '$subactividad'";				
					}
                                        if (isset($tipoIncidencia) && $tipoIncidencia != "") {
						$tsql = $tsql . " and dbo.INV_TBTAREAS.TIPO_INCIDENCIA = '$tipoIncidencia'";				
					}
					if (isset($tecnico) && $tecnico != "") {
						$tsql = $tsql . " and usuarios1.nombre like '%$tecnico%'";	
					}		
					if (isset($fRegistro1) && $fRegistro1 != "") {
						$tsql = $tsql . " and dbo.INV_TBTAREAS.FECHA_REGISTRO >= '".$fRegistro1."'";
					}
					if (isset($fRegistro2) && $fRegistro2 != "") {
						$tsql = $tsql . " and dbo.INV_TBTAREAS.FECHA_REGISTRO <= '".sumaDias($fRegistro2,1)."'";	
					}	
					if (isset($fInicio1) && $fInicio1 != "") {
									 $tsql = $tsql . " and dbo.INV_TBTAREAS.FECHA_INICIO>='".$fInicio1."'";		 	 
					}				 
					if (isset($fInicio2) && $fInicio2 != "") {
									 $tsql = $tsql . " and dbo.INV_TBTAREAS.FECHA_INICIO<'".sumaDias($fInicio2,1)."'";		

					}				 
					if (isset($fResol1) && $fResol1 != "") {
									 $tsql = $tsql . " and dbo.INV_TBTAREAS.FECHA_RESOL>='".$fResol1."'";		 	 
					}				 
					if (isset($fResol2) && $fResol2 != "") {
									 $tsql = $tsql . " and dbo.INV_TBTAREAS.FECHA_RESOL<'".sumaDias($fResol2,1)."'";				
					}
                                        
                                        if (isset($provincia) && $provincia != "") {
									 $tsql = $tsql . " and dbo.inv_provincias.Descripcion = '".$provincia."'";				
					}
                                        
                                        $tsql = $tsql . " GROUP BY dbo.inv_regiones.Cod_Region, dbo.inv_regiones.Descripcion, dbo.inv_provincias.Cod_Provincia, dbo.inv_provincias.Descripcion, dbo.inv_cabeceras.Cod_Cabecera, dbo.inv_cabeceras.Descripcion,
                                             dbo.inv_actuaciones.ACT_JAZZTEL, dbo.inv_actuaciones.ACT_TESA, dbo.inv_actuaciones.ID_FDTT, dbo.inv_actuaciones.ID_GD, 
                                             dbo.inv_actuaciones.GESTOR, dbo.inv_actuaciones.HUELLA, dbo.INV_TBTAREAS.INCIDENCIA, dbo.INV_TBTAREAS.REF, dbo.INV_TBTAREAS.REF_ASOCIADA, dbo.INV_TBTAREAS.TP, dbo.INV_TBTAREAS.TICKET_OCEANE, 
                                             dbo.INV_TBTAREAS.ESCALADO, dbo.INV_tbActividad.id_actividad, dbo.INV_tbActividad.Actividad, dbo.INV_tbSubactividad.id_Subactividad, dbo.INV_tbSubactividad.Descripcion, dbo.INV_tbEstados.Estado, usuarios1.nombre, 
                                             usuarios2.nombre, dbo.INV_TBTAREAS.PRIORIDAD, dbo.INV_TBTAREAS.FECHA_REGISTRO, dbo.INV_TBTAREAS.FECHA_INICIO, dbo.INV_TBTAREAS.FECHA_RESOL, dbo.INV_TBTAREAS.id, dbo.INV_TBTAREAS.EEMM, 
                                              dbo.INV_TBTAREAS.TIPO_INCIDENCIA, dbo.INV_TBTAREAS.SUC";
                                        

					$_SESSION['TSQL_INCIDENCIAS'] = $tsql;	
                                       /* echo ("3");
                                        echo $tsql; exit();*/
					//Recuperar datos de consulta
					$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

					//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

					//crear array de ctos para asisgnarselos a cada tarea en su linea
					/*$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
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
                                        //var_dump($array_gescales);
                                        
                                        //Este array_gescales anula al anterior, no tiene mucho sentido; aunque de momento lo dejo
					//crear array de gescales desbloqueados para asisgnarselos a cada tarea en su linea
					$tsql_gescales="SELECT INV_tbDesbloqueos_Gescales.COD_GESCAL AS GESCAL, INV_tbDesbloqueos_Gescales.ID AS ID_TAREA_GESCAL
								FROM INV_TBTAREAS
									INNER JOIN INV_tbDesbloqueos_Gescales ON INV_tbDesbloqueos_Gescales.ID = INV_TBTAREAS.id";
						
					$stmt_gescales = sqlsrv_query( $conn, $tsql_gescales);

					while($row_gescal = sqlsrv_fetch_array($stmt_gescales)){
						$array_gescales[] = $row_gescal;
					}	*/
                                        //var_dump($array_gescales);exit();
				}
			}

		//}
	} else {
		if (isset($id_ra) && $id_ra != "") {
			$tsql = "SELECT TOP 1000 * FROM INV_VIEW_DATOS_TODO WHERE ID_ACTUACION = $id_ra";

			//Si hay restricción de región
			if (isset($restriccion) && $restriccion != "('Todas')" && $seleccionadoReg == "")	{		 
				$region = $restriccion;	
				if (isset($region) && $region != "") {
					$tsql = $tsql . " and REGION IN $region"; 
				} 	
			}			

			$_SESSION['TSQL_INCIDENCIAS'] = $tsql;	
                        /*echo ("4");
			echo $tsql; exit();*/	
			//Recuperar datos de consulta
			$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

			//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

			//crear array de ctos para asisgnarselos a cada tarea en su linea
			/*$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
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
			}*/				
		} else {
			if (isset($id_rd) && $id_rd != "") {
				$tsql = "SELECT TOP 1000 * FROM INV_VIEW_DATOS_TODO WHERE ID_ACTUACION = $id_rd";

				//Si hay restricción de región
				if (isset($restriccion) && $restriccion != "('Todas')" && $seleccionadoReg == "")	{		 
					$region = $restriccion;	
					if (isset($region) && $region != "") {
						$tsql = $tsql . " and REGION IN $region"; 
					} 	
				}				

				$_SESSION['TSQL_INCIDENCIAS'] = $tsql;	
                                /*echo ("5");
				echo $tsql; exit();*/	
				//Recuperar datos de consulta
				$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

				//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

				//crear array de ctos para asisgnarselos a cada tarea en su linea
				/*$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
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
				}*/					
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

							$tsql = "SELECT TOP 1000 * FROM INV_VIEW_DATOS_TODO WHERE ID_CABECERA = $Cod_Cabecera";

							//Si hay restricción de región
							if (isset($restriccion) && $restriccion != "('Todas')" && $seleccionadoReg == "")	{		 
								$region = $restriccion;	
								if (isset($region) && $region != "") {
									$tsql = $tsql . " and REGION IN $region"; 
								} 	
							}							

							$_SESSION['TSQL_INCIDENCIAS'] = $tsql;	
                                                        /*echo ("6");
                                                        echo $tsql; exit();*/
							//Recuperar datos de consulta
							$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

							//$row = sqlsrv_fetch_array( $registros, SQLSRV_FETCH_ASSOC);

							//crear array de ctos para asisgnarselos a cada tarea en su linea
							/*$tsql_ctos="SELECT INV_CTOS.NUMERO AS NUMERO_CTO, INV_TBTAREAS_CTO.ID AS ID_TAREA_CTO
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
							}*/									

						}
					}

					sqlsrv_free_stmt($resultado);	

				}

			}
		}
	}


	// print the page header
	print_theme_header();

        //Hasta aquí en desarrollo la página tarda 0.526 segundos, bastante aceptable
        /*
        // Fin del documento
        $tiempo = microtime();
        $tiempo = explode(" ",$tiempo);
        // Calculamos en tiempo al final del documento
        $tiempo_final = $tiempo[0] + $tiempo[1];
        // Calculamos en tiempo de carga
        $tiempo_carga = $tiempo_final-$tiempo_inicial;
        // Redondeamos el valor del flotante a tres decimales
        $tiempo_carga = round($tiempo_carga,3);
        echo "Pagina generada en {$tiempo_carga} segundos"; exit();
         */
?>
			<!-- start: Content -->
		<div id="content" class="span12">
			
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="index.php">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Buscar</a></li>
			</ul>

			<!--FILTROS-->
			<FORM id="busqueda" autocomplete="off" METHOD="POST" NAME="opciones"  class="form-horizontal">
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
											
									//Si hay restricción de región
									if (isset($restriccion) && $restriccion != "('Todas')") {			 
										$region = $restriccion;	
										if (isset($region) && $region != "") {
											$tsql="select Descripcion as REGION from inv_regiones WHERE Descripcion IN $region order by Descripcion";
										} else {
											$tsql="select Descripcion as REGION from inv_regiones order by Descripcion";
										}

									} else {
										$tsql="select Descripcion as REGION from inv_regiones order by Descripcion";	
									}
                                                                            
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
							<label class="control-label" for="actjazz">Act. Jazztel </label>
							<div class="controls">
								
								<input type="text" id="actjazz" name="actjazz" value="<?php echo $actjazz;?>"/>

							</div>	
						</div>
		    		</div>

		    		<div class="span3">	
						<div class="control-group">
							<label class="control-label" for="acttesa">Act. Tesa </label>
							<div class="controls">
								
								<input type="text" id="acttesa" name="acttesa" value="<?php echo $acttesa;?>"/>

							</div>	
						</div>
		    		</div>

		    		<div class="span3">	
						<div class="control-group">
							<label class="control-label" for="cto">CTO </label>
							<div class="controls">
								
								<input type="text" id="cto" name="cto" value="<?php echo $cto;?>"/>

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
										
										echo '<SELECT multiple tabindex="2" id="estado"  name="estado[]" >';				
										
										echo '<option value=""></option>';

										while($row = sqlsrv_fetch_array($stmt)){
											
											if ($row["ESTADO"] != NULL) {											
												echo '<option value="'.$row["ESTADO"].'" '.(($row["ESTADO"]==$seleccionadoEst)?'selected="selected"':"").'>'.$row["ESTADO"].'</option>';
											}

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
                                                <div class="control-group">
							<div id="resultadoTipoIncidencia">
								<label class="control-label" for="tipoIncidencia">Tipo Incidencia </label>
								<div class="controls">
									<?php
											
											$tsql="SELECT DISTINCT (TIPO_INCIDENCIA) from INV_TBtAREAS WHERE TIPO_INCIDENCIA != 'NULL'  ORDER BY TIPO_INCIDENCIA;";
											$stmt = sqlsrv_query( $conn, $tsql);
										
											if( $stmt === false ){die ("Error al ejecutar consulta");}
										
											$rows = sqlsrv_has_rows( $stmt );
										
											if ($rows === true){
												
												echo '<SELECT class="span6" tabindex="6" id="tipoIncidencia"  name="tipoIncidencia">';		

												echo '<option value=""></option>';		
												
												while($row = sqlsrv_fetch_array($stmt)){
													
													echo '<option value="'.$row["TIPO_INCIDENCIA"].'" '.(($row["TIPO_INCIDENCIA"]==$comboTipInc)?'selected="selected"':"").'>'.$row["TIPO_INCIDENCIA"].'</option>';

												}
												
												echo '</SELECT>';		
											}
											sqlsrv_free_stmt($stmt);
										
										
													
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
                                              	
						<div class="control-group">
							<label class="control-label" for="suc">SUC </label>
							<div class="controls">
								
								<input type="text" id="suc" name="suc" value="<?php echo $suc;?>"/>

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

                                    <table border="0" style="width:100%;">
                                        <tr>
                                            <td style="width:200px;">
                                            <?php if($rolUsuario == 'escritura' || $rolUsuario == 'avanzado') { ?>	
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
														where idgrupo = '1' or idgrupo = '2' order by INV_tbGRUPOS.grupo";
											
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
					<?php } ?>
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-primary" name="buscar" id = "buscar" onclick = "this.form.action = 'buscar.php'"><i class="halflings-icon white search"></i> Buscar</button>                                            
                                                <?php if($_SERVER['REQUEST_METHOD']=='POST' && 	$rolUsuario == 'avanzado') { ?>
                                                    &nbsp;&nbsp;
                                                    <button type="submit" id="eliminar" name="eliminar" value="eliminar" class="btn btn-danger confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white trash"></i> Eliminar</button>
                                                <?php } ?>
                                                <?php if($rolUsuario == 'escritura' || $rolUsuario == 'avanzado') { ?>
                                                    &nbsp;&nbsp;
                                                    <button type="submit" id="asignar" name="asignar" value="asignar" class="btn btn-warning confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white hand-down"></i> Asignar</button>
                                                <?php } ?>
                                                <?php if($_SERVER['REQUEST_METHOD']=='POST') { ?>
                                                    &nbsp;&nbsp;
                                                    <a href="scripts/excelExport.php?origen=DESCARGA_TAREAS" title="Exportar TAREAS" class="btn btn-success">		
                                                            <i class="halflings-icon white download"></i> EXPORTAR 
                                                    </a>
                                                <?php } ?>
                                              
                                                 <?php if($_SERVER['REQUEST_METHOD']=='POST' &&  ($rolUsuario == 'escritura' || $rolUsuario == 'avanzado')) { ?>
                                                    &nbsp;&nbsp;
                                                     <a title="Historia Tarea" class="btn btn-info asociar" data-toggle="modal" data-target="#asociarModal" data-id="<?php echo $linea['ID_TAREA']; ?>">
							<i class="halflings-icon white road"></i>Asociar
						     </a>
                                                <?php } ?>
                                               
                                                   
                                            </td>
                                        </tr>
                                    </table>

				 </fieldset>	

	    
				<!--FIN FILTROS-->  

				<div class="row-fluid">
					<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<?php echo $mensaje;?>
					</div>					
				</div>		
                                <?php 
                                    //Hasta aquí en desarrollo la página tarda 0.545 segundos, bastante aceptable
                                
                                // Fin del documento
                                /*
                                $tiempo = microtime();
                                $tiempo = explode(" ",$tiempo);
                                // Calculamos en tiempo al final del documento
                                $tiempo_final = $tiempo[0] + $tiempo[1];
                                // Calculamos en tiempo de carga
                                $tiempo_carga = $tiempo_final-$tiempo_inicial;
                                // Redondeamos el valor del flotante a tres decimales
                                $tiempo_carga = round($tiempo_carga,3);
                                echo "Pagina generada en {$tiempo_carga} segundos"; exit();
                                 */
                                ?>

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
							<!--<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">-->
                                                            <table id="example" class="cell-border datatable" width="100%" cellspacing="0">
							  <thead>
								  <tr>
									<?php 
									if (($rolUsuario == 'avanzado') || ($rolUsuario == 'escritura')) {	?> 							  	
									  <th>x</th>
									<?php } ?> 							  	
									  <th>PROV.</th>
									  <th>CABEC.</th>
									  <th class="hidden">ARBOL</th>
									  <th class="hidden">AR_ID_FDTT</th>
									  <th class="hidden">AR_ID_GD</th>
									  <!--<th>ID_ACT</th>-->
									  <th>AC_JAZZTEL</th>
									  <th>TIPO_INC</th>
									  <th class="hidden">AC_ID_FDTT</th>
									  <th class="hidden">AC_ID_GD</th>
									  <th class="hidden">GESTOR</th>
									  <th>REMEDY</th>
									  <th class="hidden">TP</th>
									  <th>REF.</th>
									  <th class="hidden">REF.ASOCIADA</th>
									  <th>OCEAN</th>
									  <th>ESCALADO</th>
                                                                          <th>ACTIVIDAD</th>
									  <th>SUBACT.</th>
									  <th>SOLICITA</th>
									  <th>TECNICO</th>
									  <th>F.REGIS.</th>
									  <th class="hidden">F.INIC.</th>
									  <th>F.RESOL.</th>
									  <th>ESTADO</th>		
									  <th>P</th>					
									  <th class="hidden">ID_TAREA</th>	
									  <th class="hidden">EEMM</th>	 
									  <th class="hidden">CTOS</th> 
									  <th class="hidden">GESCAL</th> 
									  <th>_____ACCIONES_____</th>
								  </tr>
							  </thead>   
							  <tbody>
	                          <?php if (isset($registros)) { while ($linea = sqlsrv_fetch_array($registros)){ ?>
											<?php
											//Vemos si es RA o RD
                                                                                        /* COMENTADO, EXPLICACIÓN AL FINAL DE LA PÁGINA EN LA MEDICIÓN DE LOS TIEMPOS
												$tsql2 = "SELECT * 
														FROM INV_RA
														WHERE ID_ACTUACION = '".$linea['ID_ACTUACION']."'";
												//Recuperar datos de consulta
												$resultado2 = sqlsrv_query($conn, $tsql2);

												//Si no se encuentra la RA
												$rows2 = sqlsrv_has_rows( $resultado2 );
												
												if ($rows2 === false){		

													//Buscamos a ver si es una RD
													$tsql2 = "SELECT * 
															FROM INV_RD
															WHERE ID_ACTUACION = '".$linea['ID_ACTUACION']."'";

													//Recuperar datos de consulta
													$resultado2 = sqlsrv_query($conn, $tsql2);
													
													$rows2 = sqlsrv_has_rows( $resultado2 );
													//Si no se encuentra la RD
													
													if ($rows2 === false){
														$act_no_exsite = 1;	
													
													} else {
														$tipo_actuacion = 'RD';
														$registro_rd = sqlsrv_fetch_array($resultado2);

														//Buscamos su RA
														$tsql2 = "SELECT * 
																FROM INV_RA
																WHERE ID_FDTT = '".$registro_rd['ID_RA']."'";
														//Recuperar datos de consulta
														$resultado2 = sqlsrv_query($conn, $tsql2);

														//Si no se encuentra la RA
														$rows2 = sqlsrv_has_rows( $resultado2 );
														//echo "consulta: ".$tsql;
														if ($rows2 === false){	
															$act_no_exsite = 1;
														} else {
															$registro_ra = sqlsrv_fetch_array($resultado2);	
														}	

													}

												} else {
													$tipo_actuacion = 'RA';
													$registro_ra = sqlsrv_fetch_array($resultado2);
												}									

											 */ ?>	

								<tr>
									<?php 
									if (($rolUsuario == 'avanzado') || ($rolUsuario == 'escritura')) {								
									//Marca de check?>
										<TD class='center'><input onclick="seleccionarIdTarea(<?php echo $linea['ID_TAREA']?>);" type='checkbox' name='marcarProc[]' value='<?php echo $linea['ID_TAREA']?>'></TD>
									<?php } ?>								
									<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
									<td class="center"><?php echo $linea['CABECERA']; ?></td>
									<td class="hidden"><?php echo $linea['ARBOL']; ?></td>
									<td class="hidden"><?php echo $linea['ARBOL_ID_FDTT']; ?></td>
									<td class="hidden"><?php echo $linea['ARBOL_ID_GD']; ?></td>
									<!--<td class="center"><?php echo $linea['ID_ACTUACION']; ?></td>-->
									<td class="center"><?php echo $linea['ACT_JAZZTEL']; ?></td>
									<td class="center"><?php echo $linea['TIPO_INCIDENCIA']; ?></td>
									<td class="hidden"><?php echo $linea['ACT_ID_FDTT']; ?></td>
									<td class="hidden"><?php echo $linea['ACT_ID_GD']; ?></td>
									<td class="hidden"><?php echo $linea['GESTOR']; ?></td>
									<td class="center"><?php echo $linea['REMEDY']; ?></td>
									<td class="hidden"><?php echo $linea['TP']; ?></td>
									<td class="center"><?php echo $linea['REF_TBTAREA']; ?></td>
									<td class="hidden"><?php echo $linea['REF_ASOCIADA']; ?></td>
									<td class="center"><?php echo $linea['OCEANE_TBTAREA']; ?></td>
									<td class="center"><?php echo $linea['ESCALADO_TBTAREA']; ?></td>
									<td class="center"><?php echo $linea['Actividad']; ?></td>
									<td class="center"><?php echo $linea['SUBACTIVIDAD']; ?></td>
									<td class="center"><?php echo $linea['USUORIGEN']; ?></td>
									<td class="center"><?php echo $linea['TECNICO']; ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_REGISTRO'])) {echo date_format($linea['FECHA_REGISTRO'], 'Y-m-d'); } ?></td>
									<td class="hidden"><?php if (!empty($linea['FECHA_INICIO'])) {echo date_format($linea['FECHA_INICIO'], 'Y-m-d'); } ?></td>
									<td class="center"><?php if (!empty($linea['FECHA_RESOL'])) {echo date_format($linea['FECHA_RESOL'], 'Y-m-d'); } ?></td>
									<td class="center"><?php echo $linea['Estado']; ?></td>
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

											echo $SESSION['usuario']
											echo $linea['USUORIGEN']	

									<td class="center">
										<?php if ($linea['RA_Actuacion'] != '' && $linea['RA_Actuacion'] !=null) {
										?>	
											<a title="Detalle RA" class="btn btn-success btn-mini buscar_ra" data-toggle="modal" data-target="#viewModalRA" data-id="<?php echo $linea['RA_Actuacion']; ?>">		
												<i class="halflings-icon white eye-open"></i>  
											</a>
										<?php } ?>	
											
										<?php if ($linea['RD_Actuacion'] != '' && $linea['RD_Actuacion'] !=null) {
										?>	
										<a title="Detalle RD" class="btn btn-success btn-mini buscar_rd" data-toggle="modal" data-target="#viewModalRD" data-id="<?php echo $linea['RD_Actuacion']; ?>">		
											<i class="halflings-icon white eye-open"></i>  
										</a>
										<?php } ?>
										
										<a title="Detalle datos TAREA" class="btn btn-success btn-mini buscar_tarea" href="<?php echo 'consultarTarea.php?id='.$linea['ID_TAREA']; ?>">		
											<i class="halflings-icon white zoom-in"></i>										
										</a>
										<!-- <a title="Información CTOS" class="btn btn-warning btn-mini buscar_cto" data-toggle="modal" data-target="#viewModalC" data-id="<?php echo $linea['ID_TAREA']; ?>">		
											<i class="halflings-icon white info-sign"></i>  -->
										
										<?php if ($rolUsuario == 'escritura' || $rolUsuario == 'avanzado' ) {
										?>
										<a title="Gestionar Tarea" class="btn btn-success btn-mini gestionar_tarea" href="<?php echo 'modificarTarea.php?id='.$linea['ID_TAREA']; ?>">		
											<i class="halflings-icon white edit"></i> 										
										</a>
										<?php } ?>

										<a title="Historia Tarea" class="btn btn-mini btn-success buscar_historia" data-toggle="modal" data-target="#historiaModal" data-id="<?php echo $linea['ID_TAREA']; ?>">
											<i class="halflings-icon white list"></i>
										</a>

										<?php if ($rolUsuario == 'lectura' || $linea['USUORIGEN'] == $_SESSION['usuario'] ) {
										?>
										<a title="Gestionar Tarea" class="btn btn-success btn-mini gestionar_tarea" href="<?php echo 'modificarTarea.php?id='.$linea['ID_TAREA']; ?>">		
											<i class="halflings-icon white edit"></i> 	


										</a>
										<?php } ?>

									</td>
	                            </tr>
								<?php } } ?>
							  </tbody>
						  </table>            
						</div>
					</div>
	            
	            </div>

	        </FORM>       
   

	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->
<?php 
//Hasta aquí en desarrollo la página tarda 11.949 solo los datos y dibjuar tabla hasta aquí

// Fin del documento
/*
$tiempo = microtime();
$tiempo = explode(" ",$tiempo);
// Calculamos en tiempo al final del documento
$tiempo_final = $tiempo[0] + $tiempo[1];
// Calculamos en tiempo de carga
$tiempo_carga = $tiempo_final-$tiempo_inicial;
// Redondeamos el valor del flotante a tres decimales
$tiempo_carga = round($tiempo_carga,3);
echo "Pagina generada en {$tiempo_carga} segundos"; exit();
*/
?>		
<div class="modal hide fade" id="editModal">
	<div class="modal-header btn-success">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Editar</h2>
	</div>
    <div class="ct">
  
    </div>
</div>

<div class="clearfix"></div>

<div class="modal hide fade large" id="viewModalR">
	<div class="modal-header btn-info">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar</h2>
	</div>
    <div class="ct">
  
    </div>
</div>	

<div class="modal hide fade medio" id="viewModalRD">
	<div class="box-header" data-original-title>	
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar RD</h2>
	</div>
    <div class="ct">
  
    </div>
</div>	

<div class="clearfix"></div>

<div class="modal hide fade medio" id="viewModalRA">
	<div class="box-header" data-original-title>	
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar RA</h2>
	</div>
    <div class="ct">
  
    </div>
</div>	

<div class="clearfix"></div>

<div class="clearfix"></div>

<div class="modal hide fade large" id="viewModalT" data-backdrop="static" data-keyboard="false" >
	<div class="modal-header btn-info">
		<button type="button" id="cerrarConsulta" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar</h2>
	</div>
    <div class="ct" style="height:80%;">
  
    </div>
</div>	


<div class="clearfix"></div>

<div class="modal hide fade large" id="viewModalC">
	<div class="modal-header btn-info">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar</h2>
	</div>
    <div class="ct">
  
    </div>
</div>	

<div class="clearfix"></div>

<div class="modal hide fade" id="deleteModal">
	<div class="modal-header btn-danger">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> ¡Eliminar!</h2>
	</div>
    <div class="ct">
  
    </div>
</div>	

<div class="clearfix"></div>	

<div class="modal hide fade" id="historiaModal" data-backdrop="static" data-keyboard="false" >
	<div class="modal-header btn-success">
		<button type="button" id="cerrarConsulta" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar Historia</h2>
	</div>
    <div class="ct" style="height:80%;">
  
    </div>
</div>	

<div class="modal hide fade" id="asociarModal" data-backdrop="static" data-keyboard="false" >
	<div class="modal-header btn-info">
		<button type="button" id="cerrarConsulta" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i>Asociar Tareas</h2>
	</div>
    <div class="ct" style="height:80%;">
  
    </div>
</div>	


<div class="clearfix"></div>	
	
<?php
	print_theme_footer();
	sqlsrv_free_stmt($registros);
?>


<script type="text/JavaScript">

   var listaTareas ='';

function seleccionarIdTarea (idTarea){
    
    var n = listaTareas.indexOf(idTarea);
    var cadenaEliminar = '';
    
    //Si no está seleccionada la añadimos
    if (n < 0){
         listaTareas = listaTareas + idTarea + ';';
     }
     else{ //Si estaba seleccionada pero ya no, la eliminamos
         cadenaEliminar = idTarea +';';
         listaTareas = listaTareas.replace (cadenaEliminar, '');
    }
   
}

//asocia varias tareasa por referencia
  $(".asociar").click(function(){
       
        $(".modal-body").empty();
        var button = $(this); // Button that triggered the modal
        var idSelect = button.data('id'); // Extract info from data-* attributes
        var modal = button.data('target');
        var dataString = 'listaTareas=' + listaTareas;


          $.ajax({
              type: "GET",
              url: "asociarTareas.php",
              data: dataString,
              cache: false,
              success: function (data) {
                  console.log(data);
                  $(modal).find('.ct').html(data);
              },
              error: function(err) {
                  console.log(err);
              }
          });  

  });     

		//Inicializamos tareas del onload de la página
		window.onload = function ()
		{

			document.getElementById("origenSolTxt").onkeyup = ListadoUsuOrigen;
			document.getElementById("tecnicoTxt").onkeyup = ListadoTecnico;

		    $("#fRegistro1").on('change', function(e) {
	       		$(this).val($(this).attr("value").substring(4));
		    });	

		    $("#fRegistro2").on('change', function(e) {
	       		$(this).val($(this).attr("value").substring(4));
		    });			    				

		    $("#fInicio1").on('change', function(e) {
	       		$(this).val($(this).attr("value").substring(4));
		    });			    				

		    $("#fInicio2").on('change', function(e) {
	       		$(this).val($(this).attr("value").substring(4));
		    });		

		    $("#fResol1").on('change', function(e) {
	       		$(this).val($(this).attr("value").substring(4));
		    });			    				

		    $("#fResol2").on('change', function(e) {
	       		$(this).val($(this).attr("value").substring(4));
		    });			    				


		}



</script>
<?php 
/* Por Javier Fernández
Hasta aquí en desarrollo la página tarda 11.964 solo los datos y dibjuar tabla hasta aquí
Esto es solo carga datos el tiempo total es de 38 segundos, es decir el javascript tarda 26 segundos, este es el primer punto a mejorar

Mejoras:
            1: Comprobado la linea del custom.js dentro de la función template_functions() 
                $("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();
                Solución: Lo comento en el custom el input del table, por que parece que no sirve para nada aquí, así no asgina esas propiedades a todos ellos
                una vez comentada reduce en casi 20 segundos la carga, ahora con todo ha pasado de 38 a 18 segundos
            2: Las select para FROM INV_RA y FROM INV_RD hacen que de 0.596 pase a 11.964
                Comentando esas lineas la página tarda 8 segundos en vez de los 18 anteriores
                Solución: Modificada la vista INV_VIEW_DATOS_TODO para que se traiga de la BD los datos 
                           INV_RD.ID_ACTUACION as RD_Actuacion, INV_RA.ID_ACTUACION as RA_Actuacion y así no tener que hacer estas select, aunque las dejo comentadas en el código
                           
            3: Los botones de acciones y las 4 columnas últimas están retrasando tambien la carga, he probado diferentes combinaciones y nada, creo que habrá que seguir probando
                Solución: **PENDIENTE

// Fin del documento
*/
/*
$tiempo = microtime();
$tiempo = explode(" ",$tiempo);
// Calculamos en tiempo al final del documento
$tiempo_final = $tiempo[0] + $tiempo[1];
// Calculamos en tiempo de carga
$tiempo_carga = $tiempo_final-$tiempo_inicial;
// Redondeamos el valor del flotante a tres decimales
$tiempo_carga = round($tiempo_carga,3);
echo "Pagina generada en {$tiempo_carga} segundos"; exit();
*/
?>