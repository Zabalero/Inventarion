<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
	require "inc/funcionesCambiarEstado.inc";
	require "inc/funcionesModificar.inc";
	require "inc/funcionesInsertar.inc";

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
      
	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	if ($_SERVER['REQUEST_METHOD']=='GET' && isset($_GET['MENSAJE'])) {  
		$mensaje = $_GET['MENSAJE'];
	} else {
		$mensaje = "";
	}

	//grupo del usuario
 	$tsql = "SELECT a.grupo as grupo, b.id_usu as id_usu
			FROM INV_tbGRUPOS as a
			INNER JOIN INV_tbUSUARIOS as b on a.id_grupo = b.idGrupo
			WHERE b.usuario = '".$_SESSION['usuario']."' ";

	$resultado = sqlsrv_query($conn, $tsql);

	if( $resultado === false ) {
    	die ("Error al ejecutar consulta: ".$tsql);
	} else {
		$registro = sqlsrv_fetch_array($resultado);
		$grupo = $registro['grupo'];
		$id_usuario = $registro['id_usu'];
	}	

	sqlsrv_free_stmt($resultado);	  

	$nuevoID = 0;

	// INICIALIZAMOS LOS DATOS DE LA TAREA
	$readonlySuperior = false;
	$altaCorrecta = false;
	$Cod_Cabecera = "";
	$cabecera = "";
	$act_jazztel = "";
	$act_tesa = "";
	$id_Actuacion = "";
	$id_gd = "";
	$id_fdtt = "";
	$huella = "";

    $id_actividad = "";
    $subactividad = "";
	$provincia = "";
	$region = "";
	$eemm = "";
	$gestor = "";
	$ticket_remedy = "";
	$ticket_oceane = "";
        $suc = "";
	$ticket_escalado = "";
	$ticket_tp = "";
	$adjunto = "";

	$comentarios = "";
	$cto_nueva = "";

	$refAsociada = ""; 

	$motivoBloq="";

	$ctosActuacion = "";
	$arrayCtosTarea = "";	

	$selected_CTOGESC ="";

	// Si ya hemos introducido valores para insertar la tarea
	// NUEVA TAREA CON CABECERA O ACTUACION
    if($_SERVER['REQUEST_METHOD']=='POST') {  


 		if (isset($_POST['insertarTarea'])) {

 			//DATOS ASOCIADOS CON LA TAREA NUEVA

			if (!isset($_POST['checkAsociada'])){

				$ref=round(microtime(true) * 1000);
				
				if ((isset($_POST['ACT_JAZZTEL']) && $_POST['ACT_JAZZTEL'] != '') || (isset($_POST['actJazz']) && $_POST['actJazz'] != '')) {
					if (isset($_POST['ACT_JAZZTEL']) && $_POST['ACT_JAZZTEL'] != '') {
						$act_jazztel = $_POST['ACT_JAZZTEL'];
					} else {
						$act_jazztel = $_POST['actJazz'];
					} 

				 	$tsql = "SELECT TOP 1 a.descripcion as provincia, c.descripcion as region, d.GESTOR as gestor, d.id_Actuacion as actuacion,
				 						d.act_jazztel as act_jazztel, d.act_tesa as act_tesa, d.id_gd as id_gd, d.id_fdtt as id_fdtt, d.huella as huella,
				 						d.Cod_Cabecera as Cod_Cabecera,
				 						b.Descripcion as cabecera, b.eemm as eemm	
				 			FROM INV_PROVINCIAS AS A
				 			INNER JOIN INV_CABECERAS AS B ON A.COD_PROVINCIA = B.COD_PROVINCIA
				 			INNER JOIN INV_ACTUACIONES AS D ON D.COD_CABECERA = B.COD_CABECERA
				 			INNER JOIN INV_regiones AS c ON a.cod_region = c.cod_region
				 			WHERE D.ACT_JAZZTEL = '$act_jazztel'";

					//Si hay restricción de región
					if (isset($restriccion)  && $restriccion != "('Todas')")	{		 
						$region = $restriccion;	
						if (isset($region) && $region != "") {
							$tsql = $tsql . " and c.Descripcion IN $region"; 
						} 	
					}				 			

					$resultado = sqlsrv_query($conn, $tsql);

					if( $resultado === false ) {
				    	die ("Error al ejecutar consulta: ".$tsql);
					} else {
						$rows = sqlsrv_has_rows( $resultado );
						if ($rows === true){	
							$registro = sqlsrv_fetch_array($resultado);
							$id_Actuacion = $registro['actuacion'];
							$Cod_Cabecera = $registro['Cod_Cabecera'];
							$cabecera = $registro['cabecera'];
							$act_jazztel = $registro['act_jazztel'];
							$act_tesa = $registro['act_tesa'];
							$id_gd = $registro['id_gd'];
							$id_fdtt = $registro['id_fdtt'];
							$huella = $registro['huella'];
							$provincia = $registro['provincia'];
							$region = $registro['region'];
							$eemm = $registro['eemm'];
							$gestor = $registro['gestor'];
							$readonlySuperior = true;							
						} else {
							$readonlySuperior = false;
							$mensaje = "Actuación no existente";							
						}
					}	
					sqlsrv_free_stmt($resultado);					
				} else {	
					
					if ((isset($_POST['ACT_TESA']) && $_POST['ACT_TESA'] != '') || (isset($_POST['actTesa']) && $_POST['actTesa'] != '')) {	

						if (isset($_POST['ACT_TESA']) && $_POST['ACT_TESA'] != '') {
							$act_tesa = $_POST['ACT_TESA'];
						} else {
							$act_tesa = $_POST['actTesa'];
						} 						

					 	$tsql = "SELECT  TOP 1 a.descripcion as provincia, c.descripcion as region, d.GESTOR as gestor, d.id_Actuacion as actuacion,
					 						d.act_jazztel as act_jazztel, d.act_tesa as act_tesa, d.id_gd as id_gd, d.id_fdtt as id_fdtt, d.huella as huella, 
					 						d.Cod_Cabecera as Cod_Cabecera, b.Descripcion as cabecera, b.eemm as eemm	
					 			FROM INV_PROVINCIAS AS A
					 			INNER JOIN INV_CABECERAS AS B ON A.COD_PROVINCIA = B.COD_PROVINCIA
					 			INNER JOIN INV_ACTUACIONES AS D ON D.COD_CABECERA = B.COD_CABECERA
					 			INNER JOIN INV_regiones AS c ON a.cod_region = c.cod_region
					 			WHERE D.ACT_TESA = '$act_tesa'";

						//Si hay restricción de región
						if (isset($restriccion) && $restriccion != "('Todas')")	{		 
							$region = $restriccion;	
							if (isset($region) && $region != "") {
								$tsql = $tsql . " and c.Descripcion IN $region"; 
							} 	
						}

						$resultado = sqlsrv_query($conn, $tsql);

						if( $resultado === false ) {
					    	die ("Error al ejecutar consulta: ".$tsql);
						} else {
							$rows = sqlsrv_has_rows( $resultado );
							if ($rows === true){								
								$registro = sqlsrv_fetch_array($resultado);
								$id_Actuacion = $registro['actuacion'];
								$Cod_Cabecera = $registro['Cod_Cabecera'];
								$cabecera = $registro['cabecera'];
								$act_jazztel = $registro['act_jazztel'];
								$act_tesa = $registro['act_tesa'];
								$id_gd = $registro['id_gd'];
								$id_fdtt = $registro['id_fdtt'];		
								$huella = $registro['huella'];					
								$provincia = $registro['provincia'];
								$region = $registro['region'];
								$eemm = $registro['eemm'];
								$gestor = $registro['gestor'];
								$readonlySuperior = true;
							} else {
								$readonlySuperior = false;
								$mensaje = "Actuación no existente";				
							}
						}	
						
						sqlsrv_free_stmt($resultado);					
					} else {					
						
						if ((isset($_POST['ID_ACTUACION']) && $_POST['ID_ACTUACION'] != '') || (isset($_POST['idAct']) && $_POST['idAct'] != '')) {	

							if (isset($_POST['ID_ACTUACION']) && $_POST['ID_ACTUACION'] != '') {
								$id_Actuacion = $_POST['ID_ACTUACION'];
							} else {
								$id_Actuacion = $_POST['idAct'];
							} 	

						 	$tsql = "SELECT TOP 1 a.descripcion as provincia, c.descripcion as region, d.GESTOR as gestor, d.id_Actuacion as actuacion,
						 						d.act_jazztel as act_jazztel, d.act_tesa as act_tesa, d.id_gd as id_gd, d.id_fdtt as id_fdtt, d.huella as huella, 
						 						d.Cod_Cabecera as Cod_Cabecera,	b.Descripcion as cabecera, b.eemm as eemm	
						 			FROM INV_PROVINCIAS AS A
						 			INNER JOIN INV_CABECERAS AS B ON A.COD_PROVINCIA = B.COD_PROVINCIA
						 			INNER JOIN INV_ACTUACIONES AS D ON D.COD_CABECERA = B.COD_CABECERA
						 			INNER JOIN INV_regiones AS c ON a.cod_region = c.cod_region
						 			WHERE D.ID_ACTUACION = '$id_Actuacion'";

							//Si hay restricción de región
							if (isset($restriccion) && $restriccion != "('Todas')")	{		 
								$region = $restriccion;	
								if (isset($region) && $region != "") {
									$tsql = $tsql . " and c.Descripcion IN $region"; 
								} 	
							}

							$resultado = sqlsrv_query($conn, $tsql);

							if( $resultado === false ) {
						    	die ("Error al ejecutar consulta: ".$tsql);
							} else {
								$rows = sqlsrv_has_rows( $resultado );
								if ($rows === true){													
									$registro = sqlsrv_fetch_array($resultado);
									$id_Actuacion = $registro['actuacion'];
									$Cod_Cabecera = $registro['Cod_Cabecera'];
									$cabecera = $registro['cabecera'];
									$act_jazztel = $registro['act_jazztel'];
									$act_tesa = $registro['act_tesa'];
									$id_gd = $registro['id_gd'];
									$id_fdtt = $registro['id_fdtt'];
									$huella = $registro['huella'];								
									$provincia = $registro['provincia'];
									$region = $registro['region'];
									$eemm = $registro['eemm'];
									$gestor = $registro['gestor'];
									$readonlySuperior = true;
								} else {
									$readonlySuperior = false;
									$mensaje = "Actuación no existente";												

								}
							}	
							
							sqlsrv_free_stmt($resultado);					
						} else {	

							if ((isset($_POST['ID_GD']) && $_POST['ID_GD'] != '') || (isset($_POST['idGD']) && $_POST['idGD'] != '')) {	

								if (isset($_POST['ID_GD']) && $_POST['ID_GD'] != '') {
									$id_gd = $_POST['ID_GD'];
								} else {
									$id_gd = $_POST['idGD'];
								} 									

							 	$tsql = "SELECT TOP 1 a.descripcion as provincia, c.descripcion as region, d.GESTOR as gestor, d.id_Actuacion as actuacion,
							 						d.act_jazztel as act_jazztel, d.act_tesa as act_tesa, d.id_gd as id_gd, d.id_fdtt as id_fdtt, d.huella as huella, 
							 						d.Cod_Cabecera as Cod_Cabecera, b.Descripcion as cabecera, b.eemm as eemm	
							 			FROM INV_PROVINCIAS AS A
							 			INNER JOIN INV_CABECERAS AS B ON A.COD_PROVINCIA = B.COD_PROVINCIA
							 			INNER JOIN INV_ACTUACIONES AS D ON D.COD_CABECERA = B.COD_CABECERA
							 			INNER JOIN INV_regiones AS c ON a.cod_region = c.cod_region
							 			WHERE D.ID_GD = '$id_gd'";

								//Si hay restricción de región
								if (isset($restriccion) && $restriccion != "('Todas')")	{		 
									$region = $restriccion;	
									if (isset($region) && $region != "") {
										$tsql = $tsql . " and c.Descripcion IN $region"; 
									} 	
								}

								$resultado = sqlsrv_query($conn, $tsql);

								if( $resultado === false ) {
							    	die ("Error al ejecutar consulta: ".$tsql);
								} else {
									$rows = sqlsrv_has_rows( $resultado );
									if ($rows === true){										
										$registro = sqlsrv_fetch_array($resultado);
										$id_Actuacion = $registro['actuacion'];
										$Cod_Cabecera = $registro['Cod_Cabecera'];
										$cabecera = $registro['cabecera'];									
										$act_jazztel = $registro['act_jazztel'];
										$act_tesa = $registro['act_tesa'];
										$id_gd = $registro['id_gd'];
										$id_fdtt = $registro['id_fdtt'];	
										$huella = $registro['huella'];								
										$provincia = $registro['provincia'];
										$region = $registro['region'];
										$eemm = $registro['eemm'];
										$gestor = $registro['gestor'];
										$readonlySuperior = true;
									} else {
										$readonlySuperior = false;
										$mensaje = "Actuación no existente";												

									}
								}	
								
								sqlsrv_free_stmt($resultado);					
							} else {									
								
								if ((isset($_POST['ID_FDTT']) && $_POST['ID_FDTT'] != '') || (isset($_POST['idFDTT']) && $_POST['idFDTT'] != '')) {	

									if (isset($_POST['ID_FDTT']) && $_POST['ID_FDTT'] != '') {
										$id_fdtt = $_POST['ID_FDTT'];
									} else {
										$id_fdtt = $_POST['idFDTT'];
									} 															

								 	$tsql = "SELECT  TOP 1 a.descripcion as provincia, c.descripcion as region, d.GESTOR as gestor, d.id_Actuacion as actuacion,
								 						d.act_jazztel as act_jazztel, d.act_tesa as act_tesa, d.id_gd as id_gd, d.id_fdtt as id_fdtt, d.huella as huella, 
								 						d.Cod_Cabecera as Cod_Cabecera, b.Descripcion as cabecera, b.eemm as eemm	
								 			FROM INV_PROVINCIAS AS A
								 			INNER JOIN INV_CABECERAS AS B ON A.COD_PROVINCIA = B.COD_PROVINCIA
								 			INNER JOIN INV_ACTUACIONES AS D ON D.COD_CABECERA = B.COD_CABECERA
								 			INNER JOIN INV_regiones AS c ON a.cod_region = c.cod_region
								 			WHERE D.ID_FDTT = '$id_fdtt'";

									//Si hay restricción de región
									if (isset($restriccion) && $restriccion != "('Todas')")	{		 
										$region = $restriccion;	
										if (isset($region) && $region != "") {
											$tsql = $tsql . " and c.Descripcion IN $region"; 
										} 	
									}

									$resultado = sqlsrv_query($conn, $tsql);

									if( $resultado === false ) {
								    	die ("Error al ejecutar consulta: ".$tsql);
									} else {
										$rows = sqlsrv_has_rows( $resultado );
										if ($rows === true){												
											$registro = sqlsrv_fetch_array($resultado);
											$id_Actuacion = $registro['actuacion'];
											$Cod_Cabecera = $registro['Cod_Cabecera'];
											$cabecera = $registro['cabecera'];
											$act_jazztel = $registro['act_jazztel'];
											$act_tesa = $registro['act_tesa'];
											$id_gd = $registro['id_gd'];
											$id_fdtt = $registro['id_fdtt'];	
											$huella = $registro['huella'];										
											$provincia = $registro['provincia'];
											$region = $registro['region'];
											$eemm = $registro['eemm'];
											$gestor = $registro['gestor'];
											$readonlySuperior = true;
										} else {
											$readonlySuperior = false;
											$mensaje = "Actuación no existente";															
										}
									}	
									
									sqlsrv_free_stmt($resultado);					
								} else {				

									if ((isset($_POST['COD_CABECERA']) && $_POST['COD_CABECERA'] != '') || (isset($_POST['cabecera']) && $_POST['cabecera'] != '')) {	

										if (isset($_POST['COD_CABECERA']) && $_POST['COD_CABECERA'] != '') {
											$Cod_Cabecera = $_POST['COD_CABECERA'];
										 	$tsql = "SELECT  TOP 1 a.descripcion as provincia, c.descripcion as region, b.Cod_Cabecera as Cod_Cabecera, b.Descripcion as cabecera, b.eemm as eemm
										 			FROM INV_PROVINCIAS AS A
										 			INNER JOIN INV_CABECERAS AS B ON A.COD_PROVINCIA = B.COD_PROVINCIA
										 			INNER JOIN INV_regiones AS c ON a.cod_region = c.cod_region
										 			WHERE B.Cod_Cabecera = '$Cod_Cabecera'";

										} else {
											$Desc_Cabecera = $_POST['cabecera'];
										 	$tsql = "SELECT  TOP 1 a.descripcion as provincia, c.descripcion as region, b.Cod_Cabecera as Cod_Cabecera, b.Descripcion as cabecera, b.eemm as eemm
										 			FROM INV_PROVINCIAS AS A
										 			INNER JOIN INV_CABECERAS AS B ON A.COD_PROVINCIA = B.COD_PROVINCIA
										 			INNER JOIN INV_regiones AS c ON a.cod_region = c.cod_region
										 			WHERE B.Descripcion = '$Desc_Cabecera'";

										} 															


										//Si hay restricción de región
										if (isset($restriccion) && $restriccion != "('Todas')")	{		 
											$region = $restriccion;	
											if (isset($region) && $region != "") {
												$tsql = $tsql . " and c.Descripcion IN $region"; 
											} 	
										}

										$resultado = sqlsrv_query($conn, $tsql);

										if( $resultado === false ) {
									    	die ("Error al ejecutar consulta: ".$tsql);
										} else {
											$rows = sqlsrv_has_rows( $resultado );
											if ($rows === true){												
												$registro = sqlsrv_fetch_array($resultado);
												$provincia = $registro['provincia'];
												$Cod_Cabecera = $registro['Cod_Cabecera'];
												$cabecera = $registro['cabecera'];
												$region = $registro['region'];
												$eemm = $registro['eemm'];
												$readonlySuperior = true;
											} else {
												$readonlySuperior = false;
												$mensaje = "Cabecera no existente";															
											}
										}	

										
										sqlsrv_free_stmt($resultado);	  
									} else {
										$readonlySuperior = false;
										$mensaje = "Cabecera o Actuación obligatorios";
									}
								}
							}
						}
					}

				}

				

			} else {

	  			//TAREA ASOCIADA A REFERENCIA YA EXISTENTE

				if ((isset($_POST['REF_ASOCIADA']) && $_POST['REF_ASOCIADA'] != '') || (isset($_POST['refAsoc']) && $_POST['refAsoc'] != '')) {	

					if (isset($_POST['REF_ASOCIADA']) && $_POST['REF_ASOCIADA'] != '') {
						$refAsociada = $_POST['REF_ASOCIADA'];
					} else {
						$refAsociada = $_POST['refAsoc'];
					} 			

				 	$tsql = "SELECT  TOP 1 *
				 			FROM INV_VIEW_DATOS_TODO
				 			WHERE REF_TBTAREA = '$refAsociada'";

					//Si hay restricción de región
					if (isset($restriccion) && $restriccion != "('Todas')")	{		 
						$region = $restriccion;	
						if (isset($region) && $region != "") {
							$tsql = $tsql . " and REGION IN $region"; 
						} 	
					}				 			

					$resultado = sqlsrv_query($conn, $tsql);

					if( $resultado === false ) {
				    	die ("Error al ejecutar consulta: ".$tsql);
					} else {
						$rows = sqlsrv_has_rows( $resultado );
						if ($rows === true){							
							$registro = sqlsrv_fetch_array($resultado);
							$provincia = $registro['PROVINCIA'];
							$region = $registro['REGION'];
							$eemm = $registro['EEMM'];
							$gestor = $registro['GESTOR'];
							$id_Actuacion = $registro['ID_ACTUACION'];
							$act_jazztel = $registro['ACT_JAZZTEL'];
							$act_tesa = $registro['ACT_TESA'];
							$id_gd = $registro['ACT_ID_GD'];
							$id_fdtt = $registro['ACT_ID_FDTT'];
							$huella = $registro['HUELLA'];								
							$ticket_remedy = $registro['REMEDY'];
							$ticket_oceane = $registro['OCEANE_TBTAREA'];
							$ticket_escalado = $registro['ESCALADO_TBTAREA'];
							$ticket_tp = $registro['TP'];
                                                        $idTareaAsociada = $registro['ID_TAREA'];
					    	$Cod_Cabecera = $registro['ID_CABECERA'];
					    	$cabecera = $registro['CABECERA'];
					    	$readonlySuperior = 'true';
					    } else {
							$readonlySuperior = false;
							$mensaje = "Referencia asociada no existente";					    	
					    }
					}	
					
					sqlsrv_free_stmt($resultado);	  
				} else {
					$readonlySuperior = false;	
					$mensaje = "Referencia asociada obligatoria";
				}

			}	
	 	} else {
	 		$readonlySuperior = true;
	 		// CONFIRMAR INSERCCIÓN
			// GUARDAMOS LOS DATOS INTRODUCIDOS EN EL FORMULARIO
			$refAsociada = $_POST['refAsoc']; 
			$id_Actuacion = $registro['ID_ACTUACION'];
			$Cod_Cabecera = $_POST['codCab'];
			$cabecera = $_POST['cabecera'];
			$act_jazztel = $_POST['actJazz'];
			$act_tesa = $_POST['actTesa'];
			$id_gd = $_POST['idGD'];
			$id_fdtt = $_POST['idFDTT'];	
			$huella = $_POST['HUELLA'];							
		    $id_actividad = $_POST['id_actividad'];
		    if (isset($_POST['subactividad'])) {
		    	$subactividad = $_POST['subactividad'];
		    }
		    
			$provincia = $_POST['PROVINCIA'];
			$region = $_POST['REGION'];
			$eemm = $_POST['EEMM'];
			$gestor = $_POST['GESTOR'];
			$ticket_remedy = $_POST['INCIDENCIA'];
			$ticket_oceane = $_POST['TICKET_OCEANE'];
                        $suc = $_POST['SUC'];
			$ticket_escalado = $_POST['TICKET_ESCALADO'];
			$ticket_tp = $_POST['TP'];	
			$adjunto = $_FILES['adjunto']['name'];	

			$comentarios = $_POST['COMENTARIOS'];	
			$cto_nueva = $_POST['CTO_NUEVA'];

			if (isset($_POST['CTOgesc'])) {
				$selected_CTOGESC = $_POST['CTOgesc'];
			}	

		    if (isset($_POST['motivoBloq'])) {
		    	$motivoBloq=$_POST['motivoBloq'];
		    } 
			
	 		//INSERTAR TAREA
	 		if (isset($_POST['confirmar'])) {
				$mensaje = insertarTarea($conn, $nuevoID, $subactividad);
				$mensaje = $mensaje;

				//Insertado correctamente
				if ($nuevoID > 0) {
					header('Location: insertarTarea.php?MENSAJE='.$mensaje);
				}
			}
		 		
	 	}
	 	//NO POST - Entrada desde el menú
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
				<li><a href="#">Insertar</a></li>
			</ul>

			<!--FORMULARIO-->
			<form method="post" action="insertarTarea.php" role="form" enctype="multipart/form-data">
				<fieldset>    
				<!--DETALLE TAREA-->

				<!-- DATOS DE CABECERA DE LA TAREA -->
				<?php if ($readonlySuperior) {?>
				<div style="padding-left:5px;" class="row-fluid yellow">
					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>PROVINCIA: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="PROVINCIA" value="<?php echo $provincia;?>">
							</div>
						</div>
																
		
					</div>			
					<div class="span2" ontablet="span4" ondesktop="span2">

						<div class="control-group form-group">
							<div class="controls">
								<strong>REGIÓN: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="REGION" value="<?php echo $region;?>">
							</div>
						</div>					
																	
		
					</div>		

					<div class="span2" ontablet="span4" ondesktop="span2">

						<div class="control-group form-group">
							<div class="controls">
								<strong>GESTOR: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="GESTOR" value="<?php echo $gestor;?>">
							</div>
						</div>																		
		
					</div>		

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								
								<strong>USUORIGEN: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="USUORIGEN" value="<?php echo get_nombreFromId($id_usuario);?>">
								<?php echo '<input readonly="true" type="text" class="form-control input uneditable-input hidden" name="ID_USUORIGEN" value="'.$id_usuario.'">';?>

							</div>
						</div>											
				
					</div>					

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>GRUPO: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="GRUPO" value="<?php echo $grupo;?>">
							</div>
						</div>											
				
					</div>							
					<div class="control-group form-group span2">
						<div class="controls">
							<strong>EEMM: </strong><input readonly="true" type="text" class="form-control input uneditable-input" name="EEMM" value="<?php echo $eemm;?>">
							
						</div>	
					</div>	
				</div>
				<?php }?>

				<div style="padding-left:5px;" class="row-fluid yellow">
						
					<div class="control-group form-group span2">
						<div class="controls">						
							<strong>CABECERA: <br /></strong>	
							<?php if ($readonlySuperior) {?>
								<input type="text" readonly id="cabecera" name="cabecera" value="<?php echo $cabecera;?>"/>
								<input type="text" class="hidden" id="codCab" name="codCab" value="<?php echo $Cod_Cabecera;?>"/>
							<?php } else {?>
								<input type="text" id="cabecera" name="cabecera" placeholder='Buscar....'  value="<?php echo $cabecera;?>"/>
								<input type="text" class="hidden" id="codCab" name="codCab" value="<?php echo $Cod_Cabecera;?>"/>

							<?php }?>
							<div id="resultadoCabecera">
							</div>	
						</div>	
					</div>

					<div class="control-group form-group span2">
						<div class="controls">
							<strong>ACT. JAZZTEL: <br /></strong>
							<?php if ($readonlySuperior) {?>
								<input type="text" readonly id="actJazz" name="actJazz" value="<?php echo $act_jazztel;?>"/>
							<?php } else {?>
								<input type="text" id="actJazz" name="actJazz" placeholder='Buscar....' value="<?php echo $act_jazztel;?>"/>
							<?php }?>
							<div id="resultadoActuacionJazz">
							</div>										
						</div>	
					</div>	
<!--
					<div class="control-group form-group span2">
						<div class="controls">
							<strong>ACT. TESA: <br /></strong>
							<?php if ($readonlySuperior) {?>
								<input type="text" readonly id="actTesa" name="actTesa" value="<?php echo $act_tesa;?>"/>
							<?php } else {?>
								<input type="text" id="actTesa" name="actTesa" placeholder='Buscar....'  value="<?php echo $act_tesa;?>"/>
							<?php }?>
							<div id="resultadoActuacionTesa">
							</div>										
						</div>	
					</div>	
-->
					
<!--
					<div class="control-group form-group span2">
						<div class="controls">
							<strong>ID_GD: <br /></strong>
							<?php if ($readonlySuperior) {?>
								<input type="text" readonly id="idGD" name="idGD" value="<?php echo $id_gd;?>"/>
							<?php } else {?>
								<input type="text" id="idGD" name="idGD" placeholder='Buscar....'  value="<?php echo $id_gd;?>"/>
							<?php }?>
							<div id="resultadoIdGD">
							</div>										
						</div>	
					</div>	
-->
					<div class="control-group form-group span2">
						<div class="controls">
							
							<strong>ID_FDTT: <br /></strong>
							<?php if ($readonlySuperior) {?>
								<input type="text" readonly id="idFDTT" name="idFDTT" value="<?php echo $id_fdtt;?>"/>
							<?php } else {?>
								<input type="text" id="idFDTT" name="idFDTT" placeholder='Buscar....'  value="<?php echo $id_fdtt;?>"/>
							<?php }?>
							<div id="resultadoIdFDTT">
							</div>	
							<input type="text" class="hidden" id="HUELLA" name="HUELLA" value="<?php echo $huella;?>"/>									
						</div>	
					</div>															
					


					<div class="control-group form-group span2">
						<div class="controls">
<!--								<strong>ID_aCT: <br /></strong>						-->
								<input type="text" readonly style="visibility:hidden" id="idAct" name="idAct" value="<?php echo $id_Actuacion;?>"/>
<!--                                                                <input type="text" readonly id="idAct" name="idAct" value="<?php echo $id_Actuacion;?>"/>        -->
																
						</div>	
					</div>	

				</div>

				<div style="padding-left:5px;" class="row-fluid yellow">


					<div class="control-group form-group span2" id="hiddenRef">
						<div class="controls">
							<strong>REFERENCIA ASOCIADA: <br /></strong>
							<?php if ($readonlySuperior) {?>
								<input type="text" readonly id="refAsoc" name="refAsoc" value="<?php echo $refAsociada;?>" />
							<?php } else {?>
								<input type="text" id="refAsoc" name="refAsoc" placeholder='Buscar....' />
							<?php }?>
							<div id="resultadoRefAsociada">
							</div>										
						</div>	
					</div>	
					<div class="form-group span3">
						<div class="control-group form-group">
							<div class="controls">
								<?php if ($readonlySuperior) {?>
									<input type="checkbox" class="hidden" name="checkAsociada" id="checkAsociada" <?=(isset($_POST['checkAsociada']))?'checked':'';?>>
								<?php } else {?>
									<input type="checkbox" name="checkAsociada" id="checkAsociada" <?=(isset($_POST['checkAsociada']))?'checked':'';?>>
									<strong> Tarea asociada a Referencia existente: </strong>
								<?php }?>
									
						
							</div>	
						</div>	
					</div>	


				</div>			

				<div style="padding-left:5px; margin-bottom:10px;" class="row-fluid yellow">



					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								<?php if (!$readonlySuperior) {?>
									<button type="submit" name="insertarTarea" value="insertarTarea" class="btn btn-primary nueva" style="vertical-align:bottom;"> Aceptar</button>
								<?php }?>
							</div>	
						</div>	
					</div>												
									
				</div>						
				<!-- FIN DATOS DE CABECERA DE LA TAREA -->

				<!-- DATOS DEL DETALLE DE LA TAREA -->
				<?php if ($readonlySuperior) {?>
				<div class="row-fluid" style="margin-bottom:10px;">

					<div class="box-header">
						<h2><i class="halflings-icon list"></i><span class="break"></span>Detalle tarea</h2>
					</div>					
					
				</div>
				<div class="row-fluid">

					<div class="span4">

						<div class="control-group">
							<label class="control-label" for="actividad"><strong>ACTIVIDAD: </strong> </label>
							<div class="controls">
								<?php
											
									$tsql="SELECT id_actividad, ACTIVIDAD from INV_tbActividad WHERE FECHA_VIGENCIA IS NULL AND ACTIVIDAD !='' ORDER BY id_actividad";
									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta");}
								
									$rows = sqlsrv_has_rows( $stmt );
								
									if ($rows === true){
										
										echo '<SELECT class="span6" id="activ"  name="id_actividad"  onChange="ListadoSubactividadIns(\'consultas.php?dato=SUBACTIVIDAD_INS\', this.value); return false">';		

										echo '<option value=""></option>';		
										
										while($row = sqlsrv_fetch_array($stmt)){
											
											echo '<option value="'.$row["id_actividad"].'" '.(($row["id_actividad"]==$id_actividad)?'selected="selected"':"").'>'.$row["ACTIVIDAD"].'</option>';

										}
										
										echo '</SELECT>';		
									}
									sqlsrv_free_stmt($stmt);
												
								?>
							</div>	
						</div>								
						<div class="control-group form-group">
							
							<div id="resultadoSubactividad">

								<div class="controls">
									<?php
										if($_SERVER['REQUEST_METHOD']=='POST' && $id_actividad != ""){ 		

											if ($motivoBloq != '') {

												$tsql="SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where FECHA_VIGENCIA IS NULL AND id_Actividad ='".$id_actividad."' ORDER BY id_Subactividad";
												$stmt = sqlsrv_query( $conn, $tsql);
											
												if( $stmt === false ){die ("Error al ejecutar consulta");}
											
												$rows = sqlsrv_has_rows( $stmt );
											
												if ($rows === true){

													echo '<div class="hidden">';

													print ('<label class="control-label" for="subactividad"><strong>SUBACTIVIDAD: </strong> </label>');
													
													echo '<SELECT class="span6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">';	

													echo '<option value=""></option>';		
													
													while($row = sqlsrv_fetch_array($stmt)){
														
														echo '<option value="'.$row["id_Subactividad"].'" '.(($row["id_Subactividad"]==$subactividad)?'selected="selected"':"").'>'.$row["Descripcion"].'</option>';

													}
													
													echo '</SELECT>';		
													echo '</div>';		
												}
												//motivos de bloqueo
												$tsql = "SELECT DISTINCT ID_MOTIVO, DESCRIPCION from INV_tbMotivos_Bloqueo order by DESCRIPCION";
												
												$stmt = sqlsrv_query( $conn, $tsql);

												if( $stmt === false ) {
													die( print_r( sqlsrv_errors(), true));
												} else {
													$rows = sqlsrv_has_rows( $stmt );
												}			
													 
												// Mostrar resultados de la consulta

												print ('<label class="control-label" for="motivoBloq"><strong>MOTIVO DE BLOQUEO: </strong> </label>');
												print ('<div class="controls">');
												print ('</br>');

												print ('<SELECT class="span6" id="motivoBloq"  name="motivoBloq" onChange="seleccionarSubactividad(this.value); return false">\n');

												print ('<option value=""></option>');

												if ($rows === true){		
													while($row= sqlsrv_fetch_array($stmt)){
														echo '<option value="'.$row["ID_MOTIVO"].'" '.(($row["ID_MOTIVO"]==$motivoBloq)?'selected="selected"':"").'>'.$row["DESCRIPCION"].'</option>';
													}  //end while
												}	
												print ("</select>\n");
												print ('</div>');											

											} else {
												$tsql="SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where FECHA_VIGENCIA IS NULL AND id_Actividad ='".$id_actividad."' ORDER BY id_Subactividad";
												$stmt = sqlsrv_query( $conn, $tsql);
											
												if( $stmt === false ){die ("Error al ejecutar consulta");}
											
												$rows = sqlsrv_has_rows( $stmt );
											
												if ($rows === true){

													echo '<div>';

													print ('<label class="control-label" for="subactividad"><strong>SUBACTIVIDAD: </strong> </label>');
													
													echo '<SELECT class="span6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">';	

													echo '<option value=""></option>';		
													
													while($row = sqlsrv_fetch_array($stmt)){
														
														echo '<option value="'.$row["id_Subactividad"].'" '.(($row["id_Subactividad"]==$subactividad)?'selected="selected"':"").'>'.$row["Descripcion"].'</option>';

													}
													
													echo '</SELECT>';		
													echo '</div>';		
												}												
											}

											sqlsrv_free_stmt($stmt);
										} else {
											print ('<label class="control-label" for="subactividad"><strong>SUBACTIVIDAD: </strong> </label>');
											echo '<SELECT class="span6" id="subactividad"  name="subactividad">';		
											echo '<option value=""></option>';	
											echo '</SELECT>';								
										}


										
													
									?>
								</div>	
							</div>
						</div>

						<div class="control-group form-group">
							<div class="controls">
								<strong>ESCALADO: </strong>
                                                                <select name="TICKET_ESCALADO">
                                                                                      <option value=""></option>
                                                                                      <option value="GAMMA">GAMMA</option>
                                                                                      <option value="Reg_Cataluña">Reg_Cataluña</option>
                                                                                      <option value="Reg_Centro">Reg_Centro</option>
                                                                                      <option value="Reg_Levante" >Reg_Levante</option>
                                                                                      <option value="Reg_Noroeste" >Reg_Noroeste</option>
                                                                                      <option value="IBM">IBM</option>
                                                                                      <option value="Ingenieria">Ingenieria</option>
                                                                                      <option value="Reg_Oriental">Reg_Oriental</option>
                                                                                      <option value="Reg_Occidental" >Reg_Occidental</option>
                                                                                      <option value="ACCENTURE" >ACCENTURE</option>
                                                                                      <option value="EEMM" >EEMM</option>
                                                                                      <option value="SSTT">SSTT</option>
                                                                                      <option value="Telefonica">Telefonica</option>
                                                                                    </select>
							</div>
						</div>	
                                                <div class="control-group form-group">
							<div class="controls">
								<strong>SUC: </strong><input type="text" class="form-control input" name="SUC" value="<?php echo $suc;?>">
							</div>
						</div>	
						<div class="control-group">
							<div class="controls">
								<div id="ctos_gescales" class="hidden" >
								<?php
									if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['confirmar']) && isset($_POST['CTOgesc'])) {  
								?>
									<div>
										<label class="radio">
											<input type="radio" name="CTOgesc" id="radioCTO" value="CTO" <?php echo (($_POST['CTOgesc'] == 'CTO')?' checked':'') ?> >
												CTOS
										</label>
										<div style="clear:both"></div>
										<label class="radio">
											<input type="radio" name="CTOgesc" id="radioGescales" value="GESC" <?php echo (($_POST['CTOgesc'] == 'GESC')?' checked':'') ?> >
												GESCALES
										</label>
									</div>
								<?php
									}  
								?>										
								</div>								
							</div>
						</div>

					</div>					
					
					<div class="span4">
						<div class="control-group form-group">
							<div class="controls">
								<strong>TICKET REMEDY: </strong><input type="text" class="form-control input" name="INCIDENCIA" value="<?php echo $ticket_remedy;?>">
							</div>
						</div>
						<div class="control-group form-group">
							<div class="controls">
								<strong>TICKET_OCEANE: </strong><input type="text" class="form-control input" name="TICKET_OCEANE" value="<?php echo $ticket_oceane;?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>TP: </strong><input type="text" class="form-control input" name="TP" value="<?php echo $ticket_tp;?>">
							</div>
						</div>			

						<div class="control-group form-group">
							<div class="controls">
								<strong>CTO_NUEVA: </strong><input type="text" class="form-control input" name="CTO_NUEVA" value="<?php echo $cto_nueva;?>">
							</div>
						</div>	
						
						<div class="control-group form-group">	
							<div class="controls">
								<strong>FICHERO: </strong><input type="file" name="adjunto" id="adjunto" value="<?php echo $adjunto;?>" />
							</div>	
						</div>	
						


					</div>


					<div id="botonCTOS"  class="span3" ontablet="span4" ondesktop="span2">
						<div class="box">
							<div class="box-header" data-original-title>
									<a href="#" data-toggle="modal" data-target="#ctosModal" ><h2><i class="halflings-icon pencil"></i><span class="break"></span>Seleccionar CTOS</h2>

									</a>
							</div>
						</div>

						<!-- Modal Insertar Grupos-->
						<div id="ctosModal" class="modal hide fade" role="dialog">
							<div class="modal-dialog">

								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header btn-primary">
										<button type="button" class="close" data-dismiss="modal">×</button>
										<h2><i class="icon-edit"></i> Gestionar CTOS</h2>
									</div>
									<div class="modal-body">

										<div class="box-content">

											<div class="control-group form-group">
												<div class="controls">
													<strong>CTOS: </strong><br>
<!--														<div id="ctos_lateral" >-->
														<?php
															//if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['confirmar']) && isset($_POST['MARCAR']) && isset($_POST['CTOgesc'])) {  
															//if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['confirmar'])) {  
																
                                                                                                                                $bloqueo = NULL;
																$desbloqueo = NULL;
																$prioridad = NULL;
																$tratamientoCTOS = NULL;
                                                                                                                                
                                                                                                                              

																$actividadSubactividad = detallesActividadSubactividad ($id_actividad, $subactividad, $prioridad, $bloqueo, $desbloqueo, $tratamientoCTOS);
                                                                                                                               

															    //CTOS DE LA ACTUACION
															    $tsql = "select id_Actuacion, COD_CTO, NUMERO, BLOQ_CTO, NUM_PUERTOS, N_PUERTO_BLOQ, TIPO_BLOQ, MOTIVO_BLOQUEO
															    			from INV_CTOS 
															    			where id_Actuacion ='".$id_Actuacion."' order by NUMERO";

																$ctosActuacion = sqlsrv_query($conn, $tsql);

																if( $ctosActuacion === false ) {
															    	die ("Error al ejecutar consulta: ".$tsql);
																}

																$rows = sqlsrv_has_rows($ctosActuacion );

																$arrayCtosTarea = array();

																if (isset($_POST['confirmar']) && isset($_POST['MARCAR'])) {

																	//CTOS marcadas anteriormente por pantalla
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
																			$arrayCtosTarea[] = $marcar[$i];
																		}
																	}											
																}
                                                                                                                                
                                                                                                                                $tsql = "select COD_CTO
                                                                                                                                        from INV_TBTAREAS_CTO 
                                                                                                                                        where id ='".$idTareaAsociada."' order by COD_CTO";


                                                                                                                                               
                                                                                                                                //echo $tsql;                       

                                                                                                                               $ctosSeleccionados = sqlsrv_query($conn, $tsql);

                                                                                                                                if( $ctosSeleccionados === false ) {
                                                                                                                                die ("Error al ejecutar consulta: ".$tsql);
                                                                                                                                }


                                                                                                                                $listaCtos = array();
                                                                                                                                   $i=0;
                                                                                                                                   while ($resulSeleccionado = sqlsrv_fetch_array($ctosSeleccionados)) {
                                                                                                                                        $listaCtos[$i]=$resulSeleccionado['COD_CTO'];
                                                                                                                                        $i++;

                                                                                                                                    }


																while ($cto = sqlsrv_fetch_array($ctosActuacion)){
																	if ($bloqueo) {

																		//COMPRUEBA QUE LAS CTOS ESTÉN PENDIENTES DE BLOQUEO EN LA APLICACION (ICONO NARANJA)
																		$tsql7 = "SELECT INV_tbTareas_CTO.COD_CTO, INV_tbSubactividad.id_Actividad, INV_tbTareas.idEst, INV_tbTareas_Bloqueos.Tipo_Afectacion";
																		$tsql7 =$tsql7." FROM INV_tbTareas INNER JOIN";
																		$tsql7 =$tsql7." INV_tbTareas_CTO ON INV_tbTareas.id = INV_tbTareas_CTO.id INNER JOIN";
																		$tsql7 =$tsql7." INV_tbSubactividad ON INV_tbTareas.id_Subactividad = INV_tbSubactividad.id_Subactividad INNER JOIN";
																		$tsql7 =$tsql7." INV_tbTareas_Bloqueos ON INV_tbTareas.id = INV_tbTareas_Bloqueos.id_Tarea";
																		$tsql7 =$tsql7." WHERE(INV_tbTareas_Bloqueos.Tipo_Afectacion = 1) AND (INV_tbTareas.idEst = 1 OR";
																		$tsql7 =$tsql7." INV_tbTareas.idEst = 2 OR";
																		$tsql7 =$tsql7." INV_tbTareas.idEst = 3 OR";
																		$tsql7 =$tsql7." INV_tbTareas.idEst = 5) AND (INV_tbSubactividad.id_Actividad = 4)  AND INV_tbTareas_CTO.COD_CTO='".$cto['COD_CTO']."'";													
																		$stmt7 = sqlsrv_query( $conn, $tsql7) or die ("Error al ejecutar consulta: ".$tsql7);
																		$rows7 = sqlsrv_has_rows( $stmt7 );	
																		
																		if ($rows7 === true) {//si la CTO está pendiente de bloqueo en la aplicación
																			echo "<i class='halflings-icon ban-circle'></i>".$cto['NUMERO']."<br> ";	
																		} else {
																			if ($cto['BLOQ_CTO']==1){ //si la CTO está bloqueada en FIR		

																				if ($cto['TIPO_BLOQ']==1) {
																					echo "<i class='halflings-icon ok-circle'></i>".$cto['NUMERO']." - ".$cto['MOTIVO_BLOQUEO']." - ".$cto['N_PUERTO_BLOQ']." Puertos<br> ";	
																				} else {
																					echo "<i class='halflings-icon ok-circle'></i>".$cto['NUMERO']." - ".$cto['MOTIVO_BLOQUEO']." Completo<br> ";	
																				}
																			} else {
																				//POR DEFECTO
																				echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "'".((in_array($cto['COD_CTO'], $arrayCtosTarea))?' checked':'')."> ".$cto['NUMERO']."<br> ";	
																			}
																		}
																		
																	} else {
																		if ($desbloqueo) {
																			//COMPRUEBA QUE LAS CTOS ESTÉN PENDIENTES DE DESBLOQUEO EN LA APLICACION (ICONO NARANJA)
																			$tsql7 = "SELECT INV_tbTareas_CTO.COD_CTO, INV_tbSubactividad.id_Actividad, INV_tbTareas.idEst, INV_tbTareas_Desbloqueos.Tipo_Afectacion";
																			$tsql7 =$tsql7." FROM INV_tbTareas INNER JOIN";
																			$tsql7 =$tsql7." INV_tbTareas_CTO ON INV_tbTareas.id = INV_tbTareas_CTO.id INNER JOIN";
																			$tsql7 =$tsql7." INV_tbSubactividad ON INV_tbTareas.id_Subactividad = INV_tbSubactividad.id_Subactividad INNER JOIN";
																			$tsql7 =$tsql7." INV_tbTareas_Desbloqueos ON INV_tbTareas.id = INV_tbTareas_Desbloqueos.id_Tarea";
																			$tsql7 =$tsql7." WHERE(INV_tbTareas_Desbloqueos.Tipo_Afectacion = 1) AND (INV_tbTareas.idEst = 1 OR";
																			$tsql7 =$tsql7." INV_tbTareas.idEst = 2 OR";
																			$tsql7 =$tsql7." INV_tbTareas.idEst = 3 OR";
																			$tsql7 =$tsql7." INV_tbTareas.idEst = 6) AND (INV_tbSubactividad.id_Actividad = 5)  AND INV_tbTareas_CTO.COD_CTO='".$cto['COD_CTO']."'";														

																			$stmt7 = sqlsrv_query( $conn, $tsql7) or die ("Error al ejecutar consulta: ".$tsql7);
																			$rows7 = sqlsrv_has_rows( $stmt7 );	
																			
																			if ($rows7 === true) {//si la CTO está pendiente de desbloqueo en la aplicación
																				echo "<i class='halflings-icon ban-circle'></i>".$cto['NUMERO']."<br> ";	
																			} else {
																				if ($cto['BLOQ_CTO']==0){ //si la CTO no está bloqueada en FIR	
																					if ($selected_CTOGESC!='CTO') {
																						echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "'".((in_array($cto['COD_CTO'], $arrayCtosTarea))?' checked':'')."> ".$cto['NUMERO']."<i class='halflings-icon ok-circle'></i><br> ";
																					} else {
																						echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "'".((in_array($cto['COD_CTO'], $arrayCtosTarea))?' checked':'')."> ".$cto['NUMERO']."<br> ";
																					}

																				} else {
																					if ($cto['TIPO_BLOQ']==1) {
																						echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "'".((in_array($cto['COD_CTO'], $arrayCtosTarea))?' checked':'')."> ".$cto['NUMERO']." - ".$cto['N_PUERTO_BLOQ']." Puertos <br> ";	
																					} else {
																						echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "'".((in_array($cto['COD_CTO'], $arrayCtosTarea))?' checked':'')."> ".$cto['NUMERO']." Completo <br> ";	
																					}

																					
																				}
																			}
																		} else {
                                                                                                                                                          if (in_array($cto['COD_CTO'], $listaCtos)){
                                                                                                                                                            echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "' checked> ".$cto['NUMERO']."<br>";
                                                                                                                                                        }else{
                                                                                                                                                            echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "'> ".$cto['NUMERO']."<br>";
                                                                                                                                                        }
                                                                                                                                                        
																			//Ni bloqueos ni desbloqueos, POR DEFECTO
																			//echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "'".((in_array($cto['COD_CTO'], $arrayCtosTarea))?' checked':'')."> ".$cto['NUMERO']."<br> ";
																		}

																	}


																	

																}
															//}					

						  								?>
<!--						  							</div>-->
												</div>
											</div>

							                <div class="control-group">
							                    <div class="controls">
							                    	<button id="cerrarCtos" onClick="verBotonGescales(); return false" type="button" class="btn btn-default" data-dismiss="modal">Aceptar</button>
							                    	<?php if ($huella == "JAZZTEL") { ?>
													<button id="marcarTodasC" onClick="marcarTodasCTOS(); return false" type="button" class="btn btn-danger">Marcar Todos</button>	
													<button id="desmarcarTodasC" onClick="desmarcarTodasCTOS(); return false" type="button" class="btn btn-primary">Desmarcar Todos</button>	
													<?php } ?>						                    	
							                	</div>
							                </div>


										</div>
									</div>
								</div>
							</div>
						</div>

						<div id="botonGescal" >
						<?php if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['confirmar']) && isset($_POST['MARCAR']) && ($bloqueo || $desbloqueo)) {?>
							<div class="box-header" data-original-title>
								<a href="#"  id="idGescales" data-toggle="modal" data-target="#gescalesModal" ><h2><i class="halflings-icon pencil"></i><span class="break"></span>Seleccionar GESCALES</h2>

								</a>
							</div>	
						<?php }?>
						</div>
						
						 							

						<!-- Modal Gescales-->
						
						<div id="gescalesModal" class="modal hide fade" role="dialog">
							<div class="modal-dialog">
								<!-- Modal content-->
								<div class="modal-content">
									<div class="modal-header btn-primary">
										
										<button type="button" class="close" data-dismiss="modal">×</button>
										<h2><i class="icon-edit"></i> Gestionar GESCALES</h2>
										
									</div>

									<div class="modal-body">

										<div class="box-content">

											<div class="control-group form-group">
												<div class="controls">
													<strong>GESCALES: </strong><br>
														<div id="gescales_lateral" >
														<?php
															if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['confirmar']) && isset($_POST['MARCAR']) && isset($_POST['CTOgesc']) && $_POST['CTOgesc'] == 'GESC') {  
																$marcarCTO = $_POST['MARCAR'];
																$nfilasCTOS = count ($marcarCTO);


																//GESCALES DE LOS CTOS MARCADOS EN PANTALLA		
																if (!empty($marcarCTO)) {

																	$tsql = "SELECT INV_CTOS.COD_CTO, INV_CTOS.NUMERO AS NUMCTO, INV_tbGESCALES.COD_GESCAL, INV_tbGESCALES.GESCAL, INV_tbGESCALES.CALLE, INV_tbGESCALES.NUMERO AS NUMCALLE,";
																	$tsql=$tsql. " INV_tbGESCALES.PORTAL, INV_tbGESCALES.BLOQUE, INV_tbGESCALES.ACLARADOR, INV_tbGESCALES.ESCALERA, INV_tbGESCALES.PISO, INV_tbGESCALES.LETRA,";
																	$tsql=$tsql. " INV_tbGESCALES.IS_ACCESS, CONVERT(varchar, INV_tbGESCALES.FECHA_CARGA, 103) AS FECHA_CARGA, INV_tbGESCALES.N_CAJA_DERIVACION, INV_tbGESCALES.UBICACION_CD, INV_tbGESCALES.BLOQ_GES, INV_tbGESCALES.MOTIVO_BLOQUEO";
																	$tsql=$tsql. " FROM INV_tbGESCALES INNER JOIN INV_CTOS ON INV_tbGESCALES.COD_CTO=INV_CTOS.COD_CTO WHERE";
																	
																	for ($i=0; $i<$nfilasCTOS; $i++) {
																		$tsql=$tsql. " INV_CTOS.COD_CTO = '$marcarCTO[$i]' OR";
																	}

																	$tsql = trim($tsql, ' OR');   //quita el último 'OR'	
									
																	$tsql=$tsql. " ORDER BY INV_CTOS.NUMERO, INV_tbGESCALES.GESCAL";
				
																	
																	$stmt = sqlsrv_query( $conn, $tsql, array(), array('Scrollable' => 'buffered'))
																	 or die ("Error al ejecutar consulta: ".$tsql);
									
																	$rows = sqlsrv_has_rows( $stmt );


																	$arrayGescTarea = array();

																	if (isset($_POST['confirmar']) && isset($_POST['MARCARGESC'])) {

																		//CTOS marcadas anteriormente por pantalla
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
																				$codigo_gescal = $codigos[1];				

																				$arrayGescTarea[] = $codigo_gescal;
																			}
																		}											
																	}
																	
																	if ($rows === true){									
																		$row_count = sqlsrv_num_rows( $stmt );			
										
																		for ($i=0; $i<$row_count; $i++){
																			$gescal = sqlsrv_fetch_array($stmt);	
																			if ($bloqueo) {																
																
																				//COMPRUEBA QUE LOS GESCALES ESTÉN PENDIENTES DE BLOQUEO EN LA APLICACION (ICONO NARANJA)
																				$tsql8 = "SELECT INV_tbTareas.id, INV_tbSubactividad.id_Actividad, INV_tbTareas.idEst, INV_tbTareas_Bloqueos.Tipo_Afectacion, INV_tbBloqueos_Gescales.COD_GESCAL";
																				$tsql8 =$tsql8." FROM INV_tbTareas INNER JOIN";
																                $tsql8 =$tsql8." INV_tbSubactividad ON INV_tbTareas.id_Subactividad = INV_tbSubactividad.id_Subactividad INNER JOIN";
																                $tsql8 =$tsql8." INV_tbTareas_Bloqueos ON INV_tbTareas.id = INV_tbTareas_Bloqueos.id_Tarea INNER JOIN";
																                $tsql8 =$tsql8." INV_tbBloqueos_Gescales ON INV_tbTareas.id = INV_tbBloqueos_Gescales.id";
																				$tsql8 =$tsql8." WHERE(INV_tbTareas_Bloqueos.Tipo_Afectacion = 0) AND (INV_tbTareas.idEst = 1 OR";
																                $tsql8 =$tsql8." INV_tbTareas.idEst = 2 OR";
																                $tsql8 =$tsql8." INV_tbTareas.idEst = 3 OR";
																                $tsql8 =$tsql8." INV_tbTareas.idEst = 8 OR";
																                $tsql8 =$tsql8." INV_tbTareas.idEst = 5) AND (INV_tbSubactividad.id_Actividad = 4) AND INV_tbBloqueos_Gescales.COD_GESCAL='".$gescal['COD_GESCAL']."'";
																				$stmt8 = sqlsrv_query( $conn, $tsql8) or die ("Error al ejecutar consulta: ".$tsql8);										 
																				$rows8 = sqlsrv_has_rows( $stmt8 );	

																		
																				if ($rows8 === true) {//si EL gescal está pendiente de bloqueo en la aplicación
																					echo "<i class='halflings-icon ban-circle'></i>".$gescal['NUMCTO']." - ".$gescal['GESCAL']."<br> ";	
																				} else {
																					if ($gescal['BLOQ_GES']==1){ //si la CTO está bloqueada en FIR		

																						echo "<INPUT TYPE='CHECKBOX' name='MARCARGESC[]' VALUE='" .$gescal['COD_CTO']."-".$gescal['COD_GESCAL']. "'".((in_array($gescal['COD_GESCAL'], $arrayGescTarea))?' checked':'')."> ".$gescal['NUMCTO']." - ".$gescal['GESCAL']."<i class='halflings-icon ok-circle'></i><br> ";	
																					} else {
																						//POR DEFECTO
																						echo "<INPUT TYPE='CHECKBOX' name='MARCARGESC[]' VALUE='" .$gescal['COD_CTO']."-".$gescal['COD_GESCAL']. "'".((in_array($gescal['COD_GESCAL'], $arrayGescTarea))?' checked':'')."> ".$gescal['NUMCTO']." - ".$gescal['GESCAL']."<br> ";	
																					}
																				}
																			} else { 
																				if ($desbloqueo) {
																					//COMPRUEBA QUE LOS GESCALES ESTÉN PENDIENTES DE DESBLOQUEO EN LA APLICACION (ICONO NARANJA)
																					$tsql8 = "SELECT INV_tbTareas.id, INV_tbSubactividad.id_Actividad, INV_tbTareas.idEst, INV_tbTareas_Desbloqueos.Tipo_Afectacion, INV_tbDesbloqueos_Gescales.COD_GESCAL";
																					$tsql8 =$tsql8." FROM INV_tbTareas INNER JOIN";
																	                $tsql8 =$tsql8." INV_tbSubactividad ON INV_tbTareas.id_Subactividad = INV_tbSubactividad.id_Subactividad INNER JOIN";
																	                $tsql8 =$tsql8." INV_tbTareas_Desbloqueos ON INV_tbTareas.id = INV_tbTareas_Desbloqueos.id_Tarea INNER JOIN";
																	                $tsql8 =$tsql8." INV_tbDesbloqueos_Gescales ON INV_tbTareas.id = INV_tbDesbloqueos_Gescales.id";
																					$tsql8 =$tsql8." WHERE(INV_tbTareas_Desbloqueos.Tipo_Afectacion = 0) AND (INV_tbTareas.idEst = 1 OR";
																	                $tsql8 =$tsql8." INV_tbTareas.idEst = 2 OR";
																	                $tsql8 =$tsql8." INV_tbTareas.idEst = 3 OR";
																	                $tsql8 =$tsql8." INV_tbTareas.idEst = 8 OR";
																	                $tsql8 =$tsql8." INV_tbTareas.idEst = 5) AND (INV_tbSubactividad.id_Actividad = 5) AND INV_tbDesbloqueos_Gescales.COD_GESCAL='".$gescal['COD_GESCAL']."'";
																				
																					$stmt8 = sqlsrv_query( $conn, $tsql8) or die ("Error al ejecutar consulta: ".$tsql8);	
																					
																					$rows8 = sqlsrv_has_rows( $stmt8 );
																					
																					if ($rows8 === true) {//si EL gescal está pendiente de bloqueo en la aplicación
																						echo "<i class='halflings-icon ban-circle'></i>".$gescal['NUMCTO']." - ".$gescal['GESCAL']."<br> ";	
																					} else {
																						if ($gescal['BLOQ_GES']==0){ //si la CTO está bloqueada en FIR		

																							echo "<INPUT TYPE='CHECKBOX' name='MARCARGESC[]' VALUE='" .$gescal['COD_CTO']."-".$gescal['COD_GESCAL']. "'".((in_array($gescal['COD_GESCAL'], $arrayGescTarea))?' checked':'')."> ".$gescal['NUMCTO']." - ".$gescal['GESCAL']."<i class='halflings-icon ok-circle'></i><br> ";	
																						} else {
																							//POR DEFECTO
																							echo "<INPUT TYPE='CHECKBOX' name='MARCARGESC[]' VALUE='" .$gescal['COD_CTO']."-".$gescal['COD_GESCAL']. "'".((in_array($gescal['COD_GESCAL'], $arrayGescTarea))?' checked':'')."> ".$gescal['NUMCTO']." - ".$gescal['GESCAL']."<br> ";	
																						}
																					}
																				} else {
																					//Ni bloqueos ni desbloqueos, POR DEFECTO
																					echo "<INPUT TYPE='CHECKBOX' name='MARCARGESC[]' VALUE='" .$gescal['COD_CTO']."-".$gescal['COD_GESCAL'] . "'".((in_array($gescal['COD_GESCAL'], $arrayGescTarea))?' checked':'')."> ".$gescal['NUMCTO']." - ".$gescal['GESCAL']."<br> ";	
																				}

																			}

																		}
																		
																	}

																}
															}					

						  								?>
						  							</div>
												</div>
											</div>

							                <div class="control-group">
							                    <div class="controls">
							                    	<button id="cerrarGescales" onClick="verSwithCtoGescal(); return false"  type="button" class="btn btn-default" data-dismiss="modal">Aceptar</button>
							                    	<?php if ($huella != "ORANGE") { ?>
													<button id="marcarTodasG" onClick="marcarTodasGESC(); return false" type="button" class="btn btn-danger">Marcar Todos</button>	
													<button id="desmarcarTodasG" onClick="desmarcarTodasGESC(); return false" type="button" class="btn btn-primary">Desmarcar Todos</button>	
													<?php } ?>						                    								                    	
							                	</div>
							                </div>


										</div>
									</div>
								</div>
							</div>
						</div>
						

					</div>




				</div>	
				<div class="row-fluid">

					<div class="form-group span8">
							<strong >COMENTARIOS: </strong>
							<textarea rows="5" name="COMENTARIOS" class="form-control" name="COMENTARIOS"><?php echo $comentarios;?></textarea>
					
					</div>

				</div>	

				<div class="row-fluid">


					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								<?php if ($readonlySuperior) {?>
									<button type="submit" name="confirmar" value="confirmar" class="btn btn-danger btn-small confirmar" onclick="return confirmarAccion();"><i class="halflings-icon white play"></i> Insertar</button>
								<?php }?>
							</div>	
						</div>	
					</div>	

					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								<?php if ($readonlySuperior) {?>
									<button type="button" name="back" value="back" onClick="history.go(-1);return true;" class="btn btn-primary btn-small back" style="vertical-align:bottom;"><i class="halflings-icon white repeat"></i> Volver</button>
								<?php }?>
							</div>	
						</div>	
					</div>									

				</div>	
				<?php }?>
				<!-- FIN DATOS DE DETALLE DE LA TAREA -->

				<div class="row-fluid">
					<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<?php echo $mensaje;?>
					</div>					
				</div>						

				</fieldset>  
			</form>    
			<!--FIN FILTROS-->  

 	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->
		

<div class="clearfix"></div>	
	
<?php
	print_theme_footer();
?>

<script type="text/JavaScript">
		//Inicializamos tareas del onload de la página
		window.onload = function ()
		{
			//document.getElementById("btnBuscar").onclick = buscar;
			document.getElementById("cabecera").onkeyup = ListadoCabecera;
			document.getElementById("actJazz").onkeyup = ListadoActJazz;
			document.getElementById("actTesa").onkeyup = ListadoActTesa;
			document.getElementById("idAct").onkeyup = ListadoIdAct;
			document.getElementById("idGD").onkeyup = ListadoIdGD;
			document.getElementById("idFDTT").onkeyup = ListadoIdFDTT;
			document.getElementById("refAsoc").onkeyup = ListadoRefAsoc;
			

			//document.getElementById("refAsoc").hide();
			if ($('#checkAsociada').prop('checked')) {
				$("#hiddenRef").show();
			} else {
				$("#hiddenRef").hide();
			}	
			
			//$("#botonCTOS").hide();

			document.getElementById("checkAsociada").onclick = OcultarRefAsoc;
			document.getElementById("checkAsociada").onload = OcultarRefAsoc;
			

		}



</script>