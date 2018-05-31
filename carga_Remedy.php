<?php

	require "inc/funciones.inc";

	//Inicializa las variables utilizadas en el formulario
 

    //FIN Inicializa las variables utilizadas en el formulario

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	//Variables para la búsqueda
   	$date = new DateTime();
   	$anioMesCargaOceane = $date->format('ym'); //AAMM
   	$anioMesCargaTP = $date->format('Ym'); //AAMM
   	$conta = 0;
 
    //$anioMesFact = date('Y-m', strtotime($fechaEnvio));


	$tsql = "SELECT CAST((CONVERT(INT,INCIDENCIAS_REMEDY.NumeroIncidencia)) AS NVARCHAR(15)) AS NUMEROINCIDENCIA, 
		IIF(INCIDENCIAS_REMEDY.[TomadaPorGrupo] IS NULL,'VACIO',
		INCIDENCIAS_REMEDY.[TomadaPorGrupo]) AS TOMADAPORGRUPO, 
		INCIDENCIAS_REMEDY.Grupo AS GRUPO, INCIDENCIAS_REMEDY.GrupoEscalada AS GRUPOESCALADA, GETDATE() AS FHESCALADOTERCEROS,
		INCIDENCIAS_REMEDY.FEC_Fecha_Cierre AS FEC_FECHA_CIERRE, INCIDENCIAS_REMEDY.CAR_FTTH_NumeroCTO AS CAR_FTTH_NUMEROCTO, 
		INCIDENCIAS_REMEDY.SolucionDetallada AS COMENTARIOS, INCIDENCIAS_REMEDY.Estado AS ESTADO, 
		INCIDENCIAS_REMEDY.ServicioI AS SERVICIOI, 
	
		INCIDENCIAS_REMEDY.TipoI AS TIPOI, 
		INCIDENCIAS_REMEDY.DescripcionI AS DESCRIPCIONI,
		INCIDENCIAS_REMEDY.SintomaI AS SINTOMAI, 
		IIF(INCIDENCIAS_REMEDY.[TomadaPorGrupo]='INVENTARIO FTTH',177,54) AS IDUSUORIGEN,
		INCIDENCIAS_REMEDY.Cliente AS CLIENTE, INCIDENCIAS_REMEDY.ReferenciaExterna AS REFERENCIAEXTERNA,
		INCIDENCIAS_REMEDY.ViaEntradaTicket AS VIAENTRADATICKET
		FROM INCIDENCIAS_REMEDY

		WHERE INCIDENCIAS_REMEDY.ESTADO<>'CANCELADA'";
		
	$stmt = sqlsrv_query( $conn, $tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta".$tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );

	if ($rows === true) {					
		
		//Recorrer las incidencias de Remedy cuyo estado sea distinto de CANCELADA
		while($row = sqlsrv_fetch_array($stmt)) {	
			//echo "INCIDENCIA: ".$row['NUMEROINCIDENCIA']."<br>";
                        $existe = 0;
			//Mira si existe una tarea con ese Remedy, en su caso coge la última, último ID
			$tsqlEsiste = "SELECT TOP 1 INV_TBTAREAS.INCIDENCIA, INV_TBTAREAS.idEst AS ESTADO,
										INV_TBTAREAS.TIPO_INCIDENCIA, INV_TBTAREAS.REF_ASOCIADA, INV_TBTAREAS.id_Actuacion, INV_TBTAREAS.cod_cabecera, INV_TBTAREAS.COMENTARIOS2
							FROM INV_TBTAREAS 
								INNER JOIN INV_TBSUBACTIVIDAD ON INV_TBTAREAS.ID_SUBACTIVIDAD = INV_TBSUBACTIVIDAD.ID_SUBACTIVIDAD
								INNER JOIN INV_TBACTIVIDAD ON INV_TBSUBACTIVIDAD.ID_ACTIVIDAD = INV_TBACTIVIDAD.ID_ACTIVIDAD
							WHERE INV_TBTAREAS.INCIDENCIA = '".$row['NUMEROINCIDENCIA']."' 
							ORDER BY INV_TBTAREAS.ID DESC";
                        
                       
                        

			//echo "COMPRUEBA EXISTENCIA: ".$tsqlEsiste."<br>";				
			$stmtExiste = sqlsrv_query( $conn, $tsqlEsiste);

			if( $stmtExiste === false ){
				die ("Error al ejecutar consulta".$tsqlEsiste);
			}			

			$rowsExiste = sqlsrv_has_rows( $stmtExiste );	

			$ref_asociada = '';

			if ($rowsExiste === true) {	
				$rowExiste = sqlsrv_fetch_array($stmtExiste);							
				// HAY UNA TAREA CON ESE REMEDY en la tabla de tareas
				//echo "SIIIIII Esiste:".$row['NUMEROINCIDENCIA']."<br>";

				//comprobamos que todas las incidencias proactiva o cliente estan cerradas
                                
                                $tsqlCerradas = "SELECT * FROM INV_TBTAREAS 
                                        INNER JOIN INV_TBSUBACTIVIDAD ON INV_TBTAREAS.ID_SUBACTIVIDAD = INV_TBSUBACTIVIDAD.ID_SUBACTIVIDAD
                                        INNER JOIN INV_TBACTIVIDAD ON INV_TBSUBACTIVIDAD.ID_ACTIVIDAD = INV_TBACTIVIDAD.ID_ACTIVIDAD
                                        WHERE INV_TBACTIVIDAD.ID_ACTIVIDAD IN (31,32)
                                        AND INV_TBTAREAS.INCIDENCIA = '".$row['NUMEROINCIDENCIA']."' 
                                        AND (INV_TBTAREAS.IDEST <> 4 AND INV_TBTAREAS.IDEST <> 12 AND INV_TBTAREAS.IDEST <> 13)";
                        
                                    //echo "COMPRUEBA EXISTENCIA: ".$tsqlEsiste."<br>";				
                                    $stmtCerradas = sqlsrv_query( $conn, $tsqlCerradas);

                                    if( $stmtCerradas === false ){
                                            die ("Error al ejecutar consulta".$tsqlCerradas);
                                    }			

                                    $rowsCerradas = sqlsrv_has_rows( $stmtCerradas );	

                                    //Si la consulta no devuelve nada es que estan todas las incidencias cerradas, por lo que se puede insertar
                                    if (!($rowsCerradas===true)){
                                       
                                            /*$tsqlUltimaIncidencia = "SELECT TOP 1 INV_TBTAREAS.INCIDENCIA, INV_TBTAREAS.idEst AS ESTADO,
								INV_TBTAREAS.TIPO_INCIDENCIA, INV_TBTAREAS.REF_ASOCIADA, INV_TBTAREAS.id_Actuacion, INV_TBTAREAS.cod_cabecera, INV_TBTAREAS.COMENTARIOS2 FROM INV_TBTAREAS 
                                                                INNER JOIN INV_TBSUBACTIVIDAD ON INV_TBTAREAS.ID_SUBACTIVIDAD = INV_TBSUBACTIVIDAD.ID_SUBACTIVIDAD
                                                                INNER JOIN INV_TBACTIVIDAD ON INV_TBSUBACTIVIDAD.ID_ACTIVIDAD = INV_TBACTIVIDAD.ID_ACTIVIDAD
                                                                WHERE INV_TBACTIVIDAD.ID_ACTIVIDAD IN (31,32)
                                                                AND INV_TBTAREAS.INCIDENCIA = '".$row['NUMEROINCIDENCIA']."' 
                                                                AND INV_TBTAREAS.IDEST = 4
                                                                ORDER BY INV_TBTAREAS.ID DESC";*/
                                            
                                             $tsqlUltimaIncidencia = "SELECT TOP 1 INV_TBTAREAS.INCIDENCIA, INV_TBTAREAS.idEst AS ESTADO,
								INV_TBTAREAS.TIPO_INCIDENCIA, INV_TBTAREAS.REF_ASOCIADA, INV_TBTAREAS.id_Actuacion, INV_TBTAREAS.cod_cabecera, INV_TBTAREAS.COMENTARIOS2 FROM INV_TBTAREAS 
                                                                INNER JOIN INV_TBSUBACTIVIDAD ON INV_TBTAREAS.ID_SUBACTIVIDAD = INV_TBSUBACTIVIDAD.ID_SUBACTIVIDAD
                                                                INNER JOIN INV_TBACTIVIDAD ON INV_TBSUBACTIVIDAD.ID_ACTIVIDAD = INV_TBACTIVIDAD.ID_ACTIVIDAD
                                                                WHERE INV_TBTAREAS.INCIDENCIA = '".$row['NUMEROINCIDENCIA']."' 
                                                                ORDER BY INV_TBTAREAS.ID DESC";
                                            
                                            $stmtUltimaIncidencia = sqlsrv_query( $conn, $tsqlUltimaIncidencia);

                                            if( $stmtUltimaIncidencia === false ){
                                                die ("Error al ejecutar consulta".$tsqlUltimaIncidencia);
                                            }			

                                            $rowsUltimaIncidencia = sqlsrv_has_rows( $stmtUltimaIncidencia );	
                                            
                                            if ($rowsUltimaIncidencia===true){
                                                $rowsUltimaIncidencia = sqlsrv_fetch_array($stmtUltimaIncidencia);	
                                                $ref_asociada = $rowsUltimaIncidencia['REF_ASOCIADA'];
                                                $existe = 1;
                                                insertarTareaRemedy($row, $ref_asociada, $anioMesCargaOceane, $anioMesCargaTP,$conn, $conta,$rowsUltimaIncidencia, $existe );
                                                $conta = $conta + 1;
                                            }
                        
                                    }
                                
                           	//Si es una incidencia de instalación de cliente o proactiva con estado cerrado se da de alta una
				//tarea nueva con estado pendiente y con la referencia asociada de la que ya existe
				/*if (($rowExiste['TIPO_INCIDENCIA'] == 'CLIENTE' || $rowExiste['TIPO_INCIDENCIA'] == 'PROACTIVA') && $rowExiste['ESTADO'] == '4') {//Cerrada
					//echo "ENTRAAAAAAAAAA 1<br>";
                                        $existe = 1;
					insertarTareaRemedy($row, $ref_asociada, $anioMesCargaOceane, $anioMesCargaTP,$conn, $conta,$rowExiste, $existe );
					$conta = $conta + 1;
                                 }
                                 
                                 if (($rowExiste['TIPO_INCIDENCIA'] != 'CLIENTE' && $rowExiste['TIPO_INCIDENCIA'] != 'PROACTIVA')) {//Si existe pero no es una incidencia cliente o proactiva
					//echo "ENTRAAAAAAAAAA EN NO ES CLIENTE NI PROACTIVA";
                                        $existe = 1;
					insertarTareaRemedy($row, $ref_asociada, $anioMesCargaOceane, $anioMesCargaTP,$conn, $conta,$rowExiste, $existe );
					$conta = $conta + 1;
                                 }*/

				//En el caso de que exista y el stado sea distinta de Cerrada, no se hace nada con ella
				
			} else {
				//NO EXISTE EL REMEDY en la Tabla de Tareas, darla de Alta como pendiente
				//echo "NOOOOOO Esiste".$row['NUMEROINCIDENCIA']."<br>";
				insertarTareaRemedy($row, $ref_asociada, $anioMesCargaOceane, $anioMesCargaTP,$conn, $conta,$rowExiste, $existe );												
				$conta = $conta + 1;
			}
	
		}
	}

	sqlsrv_free_stmt( $stmt);	


function insertarTareaRemedy($row, $ref_asociada, $anioMesCargaOceane, $anioMesCargaTP,$conn, $conta, $rowExiste, $existe)
{


	//Incidencia de Instalación
	$usuarioOrigen = '54';
	$ref = round(microtime(true) * 1000) + $conta;

	//$fecha_inicio = date_format($row['FHESCALADOTERCEROS'], 'Y-m-d h:m:s.000');//para desarrollo
	//$fecha_registro = date_format($row['FHESCALADOTERCEROS'], 'Y-m-d h:m:s.000');//para desarrollo
        $fecha_inicio = date_format($row['FHESCALADOTERCEROS'], 'Y-m-d H:i:s'); //para produccion
	$fecha_registro = date_format($row['FHESCALADOTERCEROS'], 'Y-m-d H:i:s');//para produccion
      
       
	$incidencia = $row['NUMEROINCIDENCIA'];
	$comentarios = $row['COMENTARIOS'];
	$grupo_escalado = $row['GRUPOESCALADA'];

	
	$estado = '1';
	$id_tipo_entrada = '2'; //1 - Web, 2 - Remedy, 3 - Carga Masiva
	$prioridad = '2';

	
	if (empty($ref_asociada)) {
                $ref_asociada = $ref;
                //echo "ref_asociada: ".$ref_asociada."<br>";
	}

	$tipo_cliente = 'JAZZTEL';

	switch ($row['CLIENTE']) {
	    case 'CLIENTE DUMMY':
	        $tipo_cliente = 'OSP';
	        break;
	    case 'CLIENTE OSP-OSP':
	        $tipo_cliente = 'OSP';
	        break;
	    case 'VODAFONE DUMMY':
	        $tipo_cliente = 'VODAFONE';
	        $prioridad = '1';
	        break;
	}


	//Referencia Externa  
	$tp = '';
	$ticket_oceane = '';
	
	if (preg_match('/[A-Z]{2}[0-9]{10}/', $row['REFERENCIAEXTERNA'])) {
		$tp = substr($row['REFERENCIAEXTERNA'], 0, 12);
		//$tp = $row['REFERENCIAEXTERNA']; //PPAAAAMMYYYY
	} else {
		if (preg_match('/[0-9]{10}/', $row['REFERENCIAEXTERNA'])) {
			$ticket_oceane = substr($row['REFERENCIAEXTERNA'], 0, 10);
			//$ticket_oceane = $row['REFERENCIAEXTERNA']; //AAMMXXXXXX
		}

	}
	
	$tipo_incidencia = 'CLIENTE';

	/*switch ($row['TOMADAPORGRUPO']) {
	    case 'INVENTARIO FTTH':
	        $tipo_incidencia = 'PROACTIVA';
	        $prioridad = '3';
	        $usuarioOrigen = '177';
	        break;
	    case 'SSR FTTH':
	    	IF ($row['CLIENTE'] == 'CLIENTE DUMMY' || $row['CLIENTE'] == 'CLIENTE OSP-OSP' || $row['CLIENTE'] == 'VODAFONE DUMMY') {
	    		$tipo_incidencia = 'CLIENTE';
	    	} else {
	    		IF ($row['CLIENTE'] == 'SSR FTTH PROACTIVO' || $row['CLIENTE'] == 'SSR FTTH PROACTIVO 2') {
	    			IF ($row['VIAENTRADATICKET'] == 'FollowUp') {
	    				$tipo_incidencia = 'PROACTIVA';	
	    			} else {
	    				if ($row['TIPOI'] == 'ACCESO' && $ticket_oceane != '') {
	    					$prioridad = '1';
	    				}
	    			}
	    		}
	    	}
	        
	        break;
	    case 'VODAFONE DUMMY':
	        $tipo_incidencia = 'CLIENTE';
	        break;
	}*/	
        
        //echo ('COLUMNA CLIENTE: ' . $row['CLIENTE'] . '<\br>');
         if(($row['CLIENTE']==='INVENTARIO LOGICO PROACTIVAS') || ($row['CLIENTE']==='INVENTARIO FISICO PROACTIVAS') ||
           ($row['CLIENTE']==='SSR FTTH PROACTIVO 2')         || ($row['CLIENTE']==='SSR FTTH PROACTIVO')){
                $tipo_incidencia = 'PROACTIVA';
                $prioridad = '3';
            
        }else{
                $tipo_incidencia = 'CLIENTE';
                $prioridad = '1';
        
        }
        
        //echo ('tipo incidencia: ' . $tipo_incidencia);
        
	$grupo = '';
	$huella =  '';
	$tecnico = '';
	$tipologia_inicial = '';
	$id_mapeo =  '';

	$tsqlConcatenado = "SELECT TOP 1 *
					FROM INV_TBCONCATENADO
					WHERE INV_TBCONCATENADO.SERVICIO = '".$row['SERVICIOI']."' AND INV_TBCONCATENADO.TIPO = '".
						$row['TIPOI']."' AND INV_TBCONCATENADO.DESCRIPCION = '".$row['DESCRIPCIONI']."' AND
						(INV_TBCONCATENADO.SINTOMA = '".$row['SINTOMAI']."' OR INV_TBCONCATENADO.SINTOMA is NULL OR INV_TBCONCATENADO.SINTOMA = 'NULL' ) 
					ORDER BY SERVICIO, TIPO, DESCRIPCION, SINTOMA";
				
	
        $stmtConcatenado = sqlsrv_query( $conn, $tsqlConcatenado);

	if( $stmtConcatenado === false ){
		die ("Error al ejecutar consulta".$tsqlConcatenado);
	}	

	$rowsConcatenado = sqlsrv_has_rows( $stmtConcatenado );	

	if ($rowsConcatenado === true) {
		$rowConcatenado = sqlsrv_fetch_array($stmtConcatenado);
		$grupo = $rowConcatenado['GRUPO'];
		$huella =  $rowConcatenado['HUELLA'];
		$tecnico = $rowConcatenado['idtecnico'];
		$tipologia_inicial = $rowConcatenado['TIPOLOGIA_INICIAL'];
		$id_mapeo =  $rowConcatenado['ID'];
		$concatenado = $rowConcatenado['CONCATENADO'];
        }else{
                $grupo = '';
		$huella =  '';
		$tecnico = '155';
		$tipologia_inicial = '';
		$id_mapeo =  '';
            
            
        }
        
        //echo ("******TECNICO: " . $tecnico);
        /*if ($tecnico===''){
         $textoError = $textoError . "- No se ha encontrado tecnico asociado para el servicio '".$row['SERVICIOI']."', Tipo '".
						$row['TIPOI']."' y  Descripcion '".$row['DESCRIPCIONI']."' .<br/>" ;
        }*/
         
	//asiganr subactividad///////////////////////////

	//echo ("antes de mirar subactividad: " . $tipo_incidencia);

	$subactividad1 = $row['SERVICIOI'];

        if($tipo_incidencia=='PROACTIVA'){
           // echo ("ES PROACTIVA");
            //echo ("Buscamos la subactivad: " . $subactividad1);
           // echo ("el concatenado es: " . $concatenado );
           //if ($subactividad1 == 'RED OSP'){
             //  $subactividad = 141;

			if ($concatenado  == 'RED OSP-MODIFICACION PEX-AMPLIACION RED'){
				$subactividad = 125;
				} else {
					if ($concatenado  == 'RED OSP-MODIFICACION PEX-AMPLIACION RED-Diseño EM'){
						$subactividad = 141;
						} else {
							if ($concatenado  == 'RED OSP-MODIFICACION PEX-CAMBIOS TRAZADO PEX'){
								$subactividad = 126;
								} else {
									if ($concatenado  == 'VOZ+DATOS FTTH-MODIFICACION PEX-AMPLIACION RED'){
										$subactividad = 125;
										} else {
											if ($concatenado  == 'VOZ+DATOS FTTH-MODIFICACION PEX-CAMBIOS TRAZADO PEX'){
												$subactividad = 126;
												} else {
													if ($concatenado  == 'XPJ FTTH-MODIFICACION PEX-CAMBIOS TRAZADO PEX'){
														$subactividad = 142;
														} else {
															if ($concatenado  == 'XPJ FTTH-MODIFICACION PEX-AMPLIACION RED'){
																$subactividad = 129;
																} else {
																	if ($concatenado  == 'XPJ FTTH-POSTVENTA FTTH-FTTH ASISTENCIA TECNICA-ACCESO-Sincronismo'){
																		$subactividad = 129;
																		} else {
																			if ($concatenado  == 'XPJ FTTH-POSTVENTA FTTH-FTTH ASISTENCIA TECNICA-OS TOA-CORTE SERVICIO'){
																				$subactividad = 129;
																				} else {
																					if ($concatenado  == 'XPJ FTTH-POSTVENTA FTTH-FTTH NORMAL-CORTE-INDIVIDUAL-Cliente con corte del servicio'){
																						$subactividad = 129;
																						} else {
																							if ($concatenado  == 'VOZ+DATOS FTTH-MODIFICACION PEX-AMPLIACION RED-Diseño EM'){
																								$subactividad = 1005;
																								} else {          
	               
	         	     																				 $tsq3 = "SELECT TOP 1 * FROM INV_TBSUBACTIVIDAD WHERE (INV_TBSUBACTIVIDAD.id_Actividad=32 AND Descripcion='".$subactividad1."')";
	              																				 	$stmt3 = sqlsrv_query( $conn, $tsq3);


	               																					if( $stmt3 === false ){
	                    																			die ("Error al ejecutar consulta".$tsq3);
	                																									}
																								 	$rows2 = sqlsrv_has_rows( $stmt3 );		

	                																				if ($rows2 === true) {					

	                   																			 	$rows2 = sqlsrv_fetch_array($stmt3);
	                    																		 	$subactividad=$rows2['id_Subactividad'];	

	                																									}
	            
           																								}

																								}
																						}
																				}
																		}
																}
														}
												}	
										}
								}
						}														
				}

        if($tipo_incidencia=='CLIENTE'){
            //echo ("ES CLIENTE");
            //echo ("Buscamos la subactivad: " . $subactividad1);
            
           // if ($subactividad1 == 'RED OSP'){
             //  $subactividad = 124;

        	 //echo ("el concatenado es: " . $concatenado );
        		if ($concatenado  == 'RED OSP-MODIFICACION PEX-AMPLIACION RED'){
				$subactividad = 108;
				} else {
					if ($concatenado  == 'RED OSP-MODIFICACION PEX-AMPLIACION RED-Diseño EM'){
						$subactividad = 124;
						} else {
							if ($concatenado  == 'RED OSP-MODIFICACION PEX-CAMBIOS TRAZADO PEX'){
								$subactividad = 109;
								} else {
									if ($concatenado  == 'VOZ+DATOS FTTH-MODIFICACION PEX-AMPLIACION RED'){
										$subactividad = 108;
										} else {
											if ($concatenado  == 'VOZ+DATOS FTTH-MODIFICACION PEX-CAMBIOS TRAZADO PEX'){
												$subactividad = 109;
												} else {
												if ($concatenado  == 'XPJ FTTH-MODIFICACION PEX-CAMBIOS TRAZADO PEX'){
													$subactividad = 123;
													} else {
														if ($concatenado  == 'XPJ FTTH-MODIFICACION PEX-AMPLIACION RED'){
															$subactividad = 112;
															} else {
																if ($concatenado  == 'XPJ FTTH-POSTVENTA FTTH-FTTH ASISTENCIA TECNICA-ACCESO-Sincronismo'){
																	$subactividad = 112;
																	} else {
																		if ($concatenado  == 'XPJ FTTH-POSTVENTA FTTH-FTTH ASISTENCIA TECNICA-OS TOA-CORTE SERVICIO'){
																			$subactividad = 112;
																			} else {
																				if ($concatenado  == 'XPJ FTTH-POSTVENTA FTTH-FTTH NORMAL-CORTE-INDIVIDUAL-Cliente con corte del servicio'){
																					$subactividad = 112;
																					} else {   
																						if ($concatenado  == 'VOZ+DATOS FTTH-MODIFICACION PEX-AMPLIACION RED-Diseño EM'){
																								$subactividad = 1006;
																								} else {           
            
																					               $tsq3 = "SELECT TOP 1 * FROM INV_TBSUBACTIVIDAD WHERE (INV_TBSUBACTIVIDAD.id_Actividad=31 AND Descripcion='".$subactividad1."')";
																					                $stmt3 = sqlsrv_query( $conn, $tsq3);


																					                if( $stmt3 === false ){
																					                    die ("Error al ejecutar consulta".$tsq3);
																					                }

																					                $rows2 = sqlsrv_has_rows( $stmt3 );
																					                if ($rows2 === true) {					
																					                    $rows2 = sqlsrv_fetch_array($stmt3);
																					                    $subactividad=$rows2['id_Subactividad'];	

                																										}
            
           																								}

																							}
																					}
																			}
																	}
															}
													}
											}	
									}
							}
					}														
			}
               
        
             
        if (empty($subactividad)){	
        		if($rows2['id_Actividad']=31)  {       
            
            $subactividad=1004;}
            	else{
            	$subactividad=1003;	
            	}
    }
        
        //echo ("la subactividad es: " . $subactividad );
        //echo '<br />';

	//INSERTAR UNA NUEVA TAREA PENDIENTE
	//maiteben: Sin fecha de inicio
	// $tsqlInsertar = "INSERT 
	// 				INTO INV_TBTAREAS ( IDEST, IDUSUORIGEN, REF, ID_SUBACTIVIDAD, FECHA_INICIO, FECHA_REGISTRO, 
	// 					INCIDENCIA, COMENTARIOS, PRIORIDAD, GRUPO, GRUPO_ESCALADO, ID_TIPO_ENTRADA, 
	// 					TIPOLOGIA_INICIAL, HUELLA, TIPO_INCIDENCIA, TIPO_CLIENTE, ID_MAPEO, TICKET_OCEANE, TP, REF_ASOCIADA)
	// 				VALUES ('".$estado."', '".$usuarioOrigen."', '".$ref."', '".$subactividad.
	// 					"', convert(datetime, '".$fecha_inicio."', 120), convert(datetime, '".$fecha_registro."', 120), '".
	// 					$incidencia."', '".$comentarios."', '".$prioridad."', '".$grupo."', '".
	// 					$grupo_escalado."', '".$id_tipo_entrada."', '".$tipologia_inicial."', '".$huella.
	// 					"', '".$tipo_incidencia. "', '".$tipo_cliente."', '".$id_mapeo."', '".$ticket_oceane."', '".
	// 					$tp."', '".$ref_asociada."' )";
	
	$tsqlInsertar = "INSERT 
					INTO INV_TBTAREAS ( IDEST, IDUSUORIGEN, REF, ID_SUBACTIVIDAD, FECHA_REGISTRO, 
						INCIDENCIA, COMENTARIOS, PRIORIDAD, GRUPO, GRUPO_ESCALADO, ID_TIPO_ENTRADA, 
						TIPOLOGIA_INICIAL, HUELLA, TIPO_INCIDENCIA, TIPO_CLIENTE, ID_MAPEO, TICKET_OCEANE, TP, REF_ASOCIADA,idTecn)
					VALUES ('".$estado."', '".$usuarioOrigen."', '".$ref."', '".$subactividad.
						"', convert(datetime, '".$fecha_registro."', 120), '".
						$incidencia."', '".$comentarios."', '".$prioridad."', '".$grupo."', '".
						$grupo_escalado."', '".$id_tipo_entrada."', '".$tipologia_inicial."', '".$huella.
						"', '".$tipo_incidencia. "', '".$tipo_cliente."', '".$id_mapeo."', '".$ticket_oceane."', '".
						$tp."', '".$ref_asociada."' , '".$tecnico."' )";

	//echo "INSERTA TAREA: ".$tsqlInsertar."<br>";

	$stmtInsertar = sqlsrv_query( $conn, $tsqlInsertar);	

	if( $stmtInsertar === false ) {
	    if( ($errors = sqlsrv_errors() ) != null) {
	        foreach( $errors as $error ) {
	           /* echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
	            echo "code: ".$error[ 'code']."<br />";
	            echo "message: ".$error[ 'message']."<br />";
	            echo "Consulta: ".$tsqlInsertar."<br />";*/
	        }
	    }
	}	

		

	//CTO

	if ($row['CAR_FTTH_NUMEROCTO']) {
		$cto = $row['CAR_FTTH_NUMEROCTO'];
		$tsqlActualizar = "UPDATE INV_TBTAREAS
							SET INV_TBTAREAS.COD_CABECERA = INV_ACTUACIONES.COD_CABECERA, 
								INV_TBTAREAS.ID_ACTUACION = INV_ACTUACIONES.ID_ACTUACION,
								INV_TBTAREAS.HUELLA = INV_ACTUACIONES.HUELLA,
								INV_TBTAREAS.EEMM = INV_CABECERAS.EEMM
						FROM INV_TBTAREAS
							INNER JOIN INV_CTOS ON '".$cto."' = INV_CTOS.NUMERO
							INNER JOIN INV_ACTUACIONES ON INV_ACTUACIONES.ID_ACTUACION = INV_CTOS.ID_ACTUACION
							INNER JOIN INV_CABECERAS ON INV_CABECERAS.COD_CABECERA = INV_ACTUACIONES.COD_CABECERA
						WHERE INV_TBTAREAS.REF = '".$ref."' ";	
		//echo "ACTUALIZA TAREA: ".$tsqlActualizar."<br>";	

		$stmtActualizar = sqlsrv_query( $conn, $tsqlActualizar);

		if( $stmtActualizar === false ){
			die ("Error al ejecutar consulta".$tsqlInsertar);
		}	

		sqlsrv_free_stmt( $stmtActualizar);		

		$tsqlInsertar = "INSERT INTO INV_TBTAREAS_CTO ( ID, ID_GESTOR, COD_CTO, ID_ACTUACION )
							SELECT INV_TBTAREAS.ID, INV_ACTUACIONES.ID_GD, INV_CTOS.COD_CTO, INV_CTOS.ID_ACTUACION
							FROM INV_TBTAREAS 
								INNER JOIN INV_CTOS ON '".$cto."' = INV_CTOS.NUMERO
								INNER JOIN INV_ACTUACIONES ON INV_ACTUACIONES.ID_ACTUACION = INV_CTOS.ID_ACTUACION
							WHERE INV_TBTAREAS.REF = '".$ref."' " ;	
		//echo "INSERTA CTO: ".$tsqlInsertar."<br>";	

		$stmtInsertar = sqlsrv_query( $conn, $tsqlInsertar);	

		if( $stmtInsertar === false ) {
		    if( ($errors = sqlsrv_errors() ) != null) {
		        foreach( $errors as $error ) {
		           /* echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
		            echo "code: ".$error[ 'code']."<br />";
		            echo "message: ".$error[ 'message']."<br />";
		            echo "Consulta: ".$tsqlInsertar."<br />";*/
		        }
		    }
		}

		sqlsrv_free_stmt( $stmtInsertar);		
		
	}
        
        if ($existe===1) {
		$id_actuacion = $rowExiste['id_Actuacion'];
                $cod_cabecera = $rowExiste['cod_cabecera'];
                
                if (isset($rowExiste['COMENTARIOS2']) && $rowExiste['COMENTARIOS2']!==''){
                    $comentarios2 = $rowExiste['COMENTARIOS2'] . ' -' . $comentarios ;
                }
                
                if (empty($id_actuacion)){
                    $id_actuacion = 'null';
                }
                
                  if (empty($cod_cabecera)){
                    $cod_cabecera = 'null';
                }
                
                
                /*echo "------------------------Actualizar";
                echo $id_actuacion;
                echo $cod_cabecera;
                echo $comentarios2;*/
                
		$tsqlActualizarTarea = "UPDATE INV_TBTAREAS
							SET id_actuacion = ".$id_actuacion. ",
                                                            cod_cabecera = ".$cod_cabecera.",
                                                            comentarios2 = '".$comentarios2."'
						WHERE INV_TBTAREAS.REF = '".$ref."' ";	
		
                //echo "ACTUALIZA TAREA CON LO NUEVO: ".$tsqlActualizarTarea."<br>";	
                
		$stmtActualizarTarea = sqlsrv_query( $conn, $tsqlActualizarTarea);

		if( $stmtActualizarTarea === false ){
			die ("Error al ejecutar consulta".$tsqlActualizarTarea);
		}	

		sqlsrv_free_stmt( $stmtActualizarTarea);
                sqlsrv_free_stmt( $stmtInsertar);

		
	}
        if (!(empty($textoError))){
            $Texto_salida_error=$Texto_salida_error."<br/>Errores de la incidencia numero: " . $incidencia;
            $Texto_salida_error=$Texto_salida_error."<br/> - Error: <strong>".$textoError."</strong>";
            echo $Texto_salida_error;
        }



}	

?>
	