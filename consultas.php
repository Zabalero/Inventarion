<?php
	session_start();
	require "inc/funciones.inc";
		
	$id=$_GET['id'];
	//$rolUsuario=get_rol($_SESSION['usuario']);
        
	$conn=conectar_bd();

	if ($_GET['dato'] == 'CABECERA') {

		$tsql="SELECT * FROM INV_CABECERAS WHERE Descripcion like '%".$id."%' ORDER BY Descripcion";

		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		print ('<SELECT id="COD_CABECERA"  name="COD_CABECERA">\n');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				echo '<option class="form-control input" value="'.$row['Cod_Cabecera'].'" '.(($row["Cod_Cabecera"]==$Cod_Cabecera)?'selected="selected"':"").' >'.$row['Descripcion'].'</option>';
			}
		}	
		print ("</select>\n");

	}

	if ($_GET['dato'] == 'ACT_JAZZTEL') {

		$tsql="SELECT ACT_JAZZTEL FROM INV_ACTUACIONES WHERE ACT_JAZZTEL LIKE '%".$id."%' ORDER BY ACT_JAZZTEL";
			
		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		print ('<SELECT id="ACT_JAZZTEL"  name="ACT_JAZZTEL" >\n');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				echo '<option class="form-control input" value="'.$row['ACT_JAZZTEL'].'" '.(($row["ACT_JAZZTEL"]==$act_jazztel)?'selected="selected"':"").'>'.$row['ACT_JAZZTEL'].'</option>';
			}
		}	
		print ("</select>\n");

	}	

	if ($_GET['dato'] == 'ACT_TESA') {

		$tsql="SELECT ACT_TESA FROM INV_ACTUACIONES WHERE ACT_TESA LIKE '%".$id."%' ORDER BY ACT_TESA";
			
		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		print ('<SELECT id="ACT_TESA"  name="ACT_TESA" >\n');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				echo '<option class="form-control input" value="'.$row['ACT_TESA'].'" '.(($row["ACT_TESA"]==$act_tesa)?'selected="selected"':"").'>'.$row['ACT_TESA'].'</option>';
			}
		}	
		print ("</select>\n");

	}		

	if ($_GET['dato'] == 'ID_ACTUACION') {

		$tsql="SELECT ID_ACTUACION FROM INV_ACTUACIONES WHERE ID_ACTUACION LIKE '%".$id."%' ORDER BY ID_ACTUACION";
			
		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		print ('<SELECT id="ID_ACTUACION"  name="ID_ACTUACION" >\n');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				echo '<option class="form-control input" value="'.$row['ID_ACTUACION'].'" '.(($row["ID_ACTUACION"]==$id_Actuacion)?'selected="selected"':"").'>'.$row['ID_ACTUACION'].'</option>';
			}
		}	
		print ("</select>\n");

	}			

	if ($_GET['dato'] == 'ID_GD') {

		$tsql="SELECT ID_GD FROM INV_ACTUACIONES WHERE ID_GD LIKE '%".$id."%' ORDER BY ID_GD";
			
		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		print ('<SELECT id="ID_GD"  name="ID_GD" >\n');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				echo '<option class="form-control input" value="'.$row['ID_GD'].'" '.(($row["ID_GD"]==$id_gd)?'selected="selected"':"").'>'.$row['ID_GD'].'</option>';
			}
		}	
		print ("</select>\n");

	}			

	if ($_GET['dato'] == 'ID_FDTT') {

		$tsql="SELECT ID_FDTT FROM INV_ACTUACIONES WHERE ID_FDTT LIKE '%".$id."%' ORDER BY ID_FDTT";
			
		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		print ('<SELECT id="ID_FDTT"  name="ID_FDTT" >\n');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				echo '<option class="form-control input" value="'.$row['ID_FDTT'].'" '.(($row["ID_FDTT"]==$id_fdtt)?'selected="selected"':"").'>'.$row['ID_FDTT'].'</option>';
			}
		}	
		print ("</select>\n");

	}				


	if ($_GET['dato'] == 'REF_ASOCIADA') {

		$tsql="SELECT DISTINCT REF_TBTAREA AS REF FROM INV_VIEW_DATOS_TODO WHERE ID_REGION IS NOT NULL AND REF_TBTAREA LIKE '%".$id."%' ORDER BY REF_TBTAREA";
			
		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		print ('<SELECT id="REF_ASOCIADA"  name="REF_ASOCIADA" >\n');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				if ($row['REF'] != '') {
					echo '<option class="form-control input" value="'.$row['REF'].'" '.(($row["REF"]==$refAsociada)?'selected="selected"':"").' >'.$row['REF'].'</option>';
				}
			} 
		}	
		print ("</select>\n");		

	}		


	if ($_GET['dato'] == 'ACTUACION') {

		$tsql = "SELECT ACT_JAZZTEL, ACT_TESA, ID_ACTUACION, ID_GD, ID_FDTT FROM INV_ACTUACIONES WHERE COD_CABECERA ='".$id."' AND ID_FDTT <> '0'";

		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		$arrayActuaciones = array();

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				$arrayActuaciones[] = $row;			
			}  //end while
		}	

		//RELLENAR ACTUACIONES JAZZTEL
		foreach ($arrayActuaciones as $key => $row) {
		    $aux[$key] = $row['ACT_JAZZTEL'];
		}		
		array_multisort($aux, SORT_ASC, $arrayActuaciones);

		PRINT ('<div class="control-group form-group span2">');
		PRINT ('<div  id="resultadoActuacionJazz" class="controls">');

		PRINT ('<strong>ACT. JAZZTEL: <br /></strong>');
		PRINT ('<SELECT id="ACT_JAZZTEL"  name="ACT_JAZZTEL"  onChange="seleccionarActuacion(\'ACT_JAZZTEL\'); return false">');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			foreach ($arrayActuaciones as $key => $row) {
				if ($row['ACT_JAZZTEL'] != null && $row['ACT_JAZZTEL'] != '' && $row['ACT_JAZZTEL'] != '0') {
					echo '<option class="form-control input" value="'.$row['ACT_JAZZTEL'].'" >'.$row['ACT_JAZZTEL'].'</option>';			
				}
			}  //end while
		}	
		print ("</select>\n");		
		PRINT ('</div>');
		PRINT ('</div>');
							
		//RELLENAR ACTUACIONES TESA
		foreach ($arrayActuaciones as $key => $row) {
		    $aux[$key] = $row['ACT_TESA'];
		}		
		array_multisort($aux, SORT_ASC, $arrayActuaciones);
		
		PRINT ('<div class="control-group form-group span2">');
		PRINT ('<div id="resultadoActuacionTesa" class="controls">');
	
		PRINT ('<strong>ACT. TESA: <br /></strong>');
		PRINT ('<SELECT id="ACT_TESA"  name="ACT_TESA" onChange="seleccionarActuacion(\'ACT_TESA\'); return false">');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			foreach ($arrayActuaciones as $key => $row) {
				if ($row['ACT_TESA'] != null && $row['ACT_TESA'] != '' && $row['ACT_TESA'] != '0') {
					echo '<option class="form-control input" value="'.$row['ACT_TESA'].'" >'.$row['ACT_TESA'].'</option>';			
				}
			}  //end while
		}	
		print ("</select>\n");		
		PRINT ('</div>');
		PRINT ('</div>');		


		//RELLENAR ID ACTUACION
		foreach ($arrayActuaciones as $key => $row) {
		    $aux[$key] = $row['ID_ACTUACION'];
		}		
		array_multisort($aux, SORT_ASC, $arrayActuaciones);

		PRINT ('<div class="control-group form-group span2">');
		PRINT ('<div id="resultadoActuacionID" class="controls">');

		PRINT ('<strong>ID ACTUACIÓN: <br /></strong>');
		PRINT ('<SELECT id="ID_ACTUACION"  name="ID_ACTUACION" onChange="seleccionarActuacion(\'ID_ACTUACION\'); return false">');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			foreach ($arrayActuaciones as $key => $row) {
				if ($row['ID_ACTUACION'] != null && $row['ID_ACTUACION'] != '' && $row['ID_ACTUACION'] != '0') {
					echo '<option class="form-control input" value="'.$row['ID_ACTUACION'].'" >'.$row['ID_ACTUACION'].'</option>';			
				}
			}  //end while
		}	
		print ("</select>\n");		
		PRINT ('</div>');
		PRINT ('</div>');

		//RELLENAR ID_GD
		foreach ($arrayActuaciones as $key => $row) {
		    $aux[$key] = $row['ID_GD'];
		}		
		array_multisort($aux, SORT_ASC, $arrayActuaciones);

		PRINT ('<div class="control-group form-group span2">');
		PRINT ('<div id="resultadoActuacionIDGD" class="controls">');

		PRINT ('<strong>ID_GD: <br /></strong>');
		PRINT ('<SELECT id="ID_GD"  name="ID_GD" onChange="seleccionarActuacion(\'ID_GD\'); return false">');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			foreach ($arrayActuaciones as $key => $row) {
				if ($row['ID_GD'] != null && $row['ID_GD'] != '' && $row['ID_GD'] != '0') {
					echo '<option class="form-control input" value="'.$row['ID_GD'].'" >'.$row['ID_GD'].'</option>';			
				}
			}  //end while
		}	
		print ("</select>\n");		
		PRINT ('</div>');
		PRINT ('</div>');

		//RELLENAR ID_FDTT
		foreach ($arrayActuaciones as $key => $row) {
		    $aux[$key] = $row['ID_FDTT'];
		}		
		array_multisort($aux, SORT_ASC, $arrayActuaciones);

		PRINT ('<div class="control-group form-group span2">');
		PRINT ('<div id="resultadoActuacionIDGD" class="controls">');

		PRINT ('<strong>ID_FDTT: <br /></strong>');
		PRINT ('<SELECT id="ID_FDTT"  name="ID_FDTT" onChange="seleccionarActuacion(\'ID_FDTT\'); return false">');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			foreach ($arrayActuaciones as $key => $row) {
				if ($row['ID_FDTT'] != null && $row['ID_FDTT'] != '' && $row['ID_FDTT'] != '0') {
					echo '<option class="form-control input" value="'.$row['ID_FDTT'].'" >'.$row['ID_FDTT'].'</option>';			
				}
			}  //end while
		}	
		print ("</select>\n");		
		PRINT ('</div>');
		PRINT ('</div>');

	}




	if ($_GET['dato'] == 'SUBACTIVIDAD') {
                //Hay actividad seleccionada
		if ($id != '') {
			//Ver si la actividad tienen subactividades o motivos de bloqueo
			$tsql = "SELECT BLOQUEO from INV_tbActividad where id_Actividad ='".$id."' ";

			$stmt = sqlsrv_query( $conn, $tsql);

			if( $stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			} else {
				$row= sqlsrv_fetch_array($stmt);
				$bloqueo = $row['BLOQUEO'];
			}		

			$tsql = "SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where FECHA_VIGENCIA IS NULL AND id_Actividad ='".$id."'  order by id_Subactividad";
			
			$stmt = sqlsrv_query( $conn, $tsql);

			if( $stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			} else {
				$rows = sqlsrv_has_rows( $stmt );
			}			
				 
			// Mostrar resultados de la consulta

			if ($bloqueo == 'S') {

				//subactividad
				$tsql = "SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where FECHA_VIGENCIA IS NULL and id_Actividad ='".$id."'  order by id_Subactividad";
				
				$stmt = sqlsrv_query( $conn, $tsql);

				if( $stmt === false ) {
					die( print_r( sqlsrv_errors(), true));
				} else {
					$rows = sqlsrv_has_rows( $stmt );
				}			
				
				//print ('<div class ="hidden">');	
				print ('<label class="control-label" for="subactividad"><strong>SUBACTIVIDAD: </strong> </label>');
				print ('<div class="controls">');
				print ('</br>');

				print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">\n');

				print ('<option value=""></option>');

				if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						echo '<option value="'.$row["id_Subactividad"].'" >'.$row["Descripcion"].'</option>';
					}  //end while
				}	
				print ("</select>\n");
				print ('</div>');			
				//print ('</div>');		

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

				//print ('<SELECT class="span6" id="motivoBloq"  name="motivoBloq" onChange="seleccionarSubactividad(this.value); return false">\n');
                                print ('<SELECT class="span6" id="motivoBloq"  name="motivoBloq">\n');

				print ('<option value=""></option>');

				if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						echo '<option value="'.$row["ID_MOTIVO"].'" >'.$row["DESCRIPCION"].'</option>';
					}  //end while
				}	
				print ("</select>\n");
				print ('</div>');
			} else {
				//subactividad
				$tsql = "SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where FECHA_VIGENCIA IS NULL and id_Actividad ='".$id."'  order by id_Subactividad";
				
				$stmt = sqlsrv_query( $conn, $tsql);

				if( $stmt === false ) {
					die( print_r( sqlsrv_errors(), true));
				} else {
					$rows = sqlsrv_has_rows( $stmt );
				}			
				
				print ('<div>');	
				print ('<label class="control-label" for="subactividad"><strong>SUBACTIVIDAD: </strong> </label>');
				print ('<div class="controls">');
				print ('</br>');

				print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">\n');

				print ('<option value=""></option>');

				if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						echo '<option value="'.$row["id_Subactividad"].'" >'.$row["Descripcion"].'</option>';
					}  //end while
				}	
				print ("</select>\n");
				print ('</div>');			
				print ('</div>');						
			}	
		
		} else {
			//No hay actividad seleccionada
			print ('<label class="control-label" for="subactividad">Subactividad </label>');
			print ('<div class="controls">');
			print ('</br>');		
			print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">\n');	
			print ('<option value=""></option>');
			print ("</select>\n");
			print ('</div>');
		}

	}
        
        if ($_GET['dato'] == 'SUBACTIVIDAD_INS') {
                //Hay actividad seleccionada
		
                if ($id != '') {
			//Ver si la actividad tienen subactividades o motivos de bloqueo
			$tsql = "SELECT BLOQUEO from INV_tbActividad where id_Actividad ='".$id."' ";

			$stmt = sqlsrv_query( $conn, $tsql);

			if( $stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			} else {
				$row= sqlsrv_fetch_array($stmt);
				$bloqueo = $row['BLOQUEO'];
			}
                        
                    

			$tsql = "SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where FECHA_VIGENCIA IS NULL AND id_Actividad ='".$id."'  order by id_Subactividad";
			
			$stmt = sqlsrv_query( $conn, $tsql);

			if( $stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			} else {
				$rows = sqlsrv_has_rows( $stmt );
			}			
				 
			// Mostrar resultados de la consulta

			if ($bloqueo == 'S') {

				//subactividad
				$tsql = "SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where FECHA_VIGENCIA IS NULL and id_Actividad ='".$id."'  order by id_Subactividad";
				
				$stmt = sqlsrv_query( $conn, $tsql);

				if( $stmt === false ) {
					die( print_r( sqlsrv_errors(), true));
				} else {
					$rows = sqlsrv_has_rows( $stmt );
				}			
				
                                print ('<div class ="hidden">');	
				print ('<label class="control-label" for="subactividad"><strong>SUBACTIVIDAD: </strong> </label>');
				print ('<div class="controls">');
				print ('</br>');

				print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">\n');

				print ('<option value=""></option>');

				if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						echo '<option value="'.$row["id_Subactividad"].'" >'.$row["Descripcion"].'</option>';
					}  //end while
				}	
				print ("</select>\n");
				print ('</div>');			
				print ('</div>');		

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
                                //print ('<SELECT class="span6" id="motivoBloq"  name="motivoBloq">\n');

				print ('<option value=""></option>');

				if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						echo '<option value="'.$row["ID_MOTIVO"].'" >'.$row["DESCRIPCION"].'</option>';
					}  //end while
				}	
				print ("</select>\n");
				print ('</div>');
			} else {
				//subactividad
				$tsql = "SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where FECHA_VIGENCIA IS NULL and id_Actividad ='".$id."'  order by id_Subactividad";
				
				$stmt = sqlsrv_query( $conn, $tsql);

				if( $stmt === false ) {
					die( print_r( sqlsrv_errors(), true));
				} else {
					$rows = sqlsrv_has_rows( $stmt );
				}			
				
				print ('<div>');	
				print ('<label class="control-label" for="subactividad"><strong>SUBACTIVIDAD: </strong> </label>');
				print ('<div class="controls">');
				print ('</br>');

				print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">\n');

				print ('<option value=""></option>');

				if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						echo '<option value="'.$row["id_Subactividad"].'" >'.$row["Descripcion"].'</option>';
					}  //end while
				}	
				print ("</select>\n");
				print ('</div>');			
				print ('</div>');						
			}	
		
		} else {
			//No hay actividad seleccionada
			print ('<label class="control-label" for="subactividad">Subactividad </label>');
			print ('<div class="controls">');
			print ('</br>');		
			print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">\n');	
			print ('<option value=""></option>');
			print ("</select>\n");
			print ('</div>');
		}

	}


	if ($_GET['dato'] == 'PROVINCIA') {

		$tsql="select inv_provincias.Descripcion as PROVINCIA from inv_provincias inner join inv_regiones on inv_provincias.Cod_Region = inv_regiones.Cod_Region where inv_regiones.Descripcion = '".$id."' order by inv_provincias.Descripcion";
			
		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta
		print ('<label class="control-label" for="prior">Provincia </label>');
		print ('<div class="controls">');

		print ('<SELECT class="span6" tabindex="0" id="provincia"  name="provincia" >\n');

		PRINT ('<option class="form-control input" value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){

					
					echo '<option value="'.$row["PROVINCIA"].'" '.(($row["PROVINCIA"]==$seleccionadoProv)?'selected="selected"':"").'>'.$row["PROVINCIA"].'</option>';

			} 
		}	
		print ("</select>\n");	
		print ('</div>');	

	}		

	//Para consultas tenemos que dejar abiertas las subactividades obsoletas
	if ($_GET['dato'] == 'SUBACTIVIDAD_ANT') {

		//Hay actividad seleccionada
		if ($id != '') {
			//Ver si la actividad tienen subactividades o motivos de bloqueo
			$tsql = "SELECT BLOQUEO from INV_tbActividad where id_Actividad ='".$id."' ";

			$stmt = sqlsrv_query( $conn, $tsql);

			if( $stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			} else {
				$row= sqlsrv_fetch_array($stmt);
				$bloqueo = $row['BLOQUEO'];
			}		

			$tsql = "SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where id_Actividad ='".$id."'  order by id_Subactividad";
			
			$stmt = sqlsrv_query( $conn, $tsql);

			if( $stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			} else {
				$rows = sqlsrv_has_rows( $stmt );
			}			
				 
			// Mostrar resultados de la consulta

			if ($bloqueo == 'S') {
                               //subactividad
				$tsql = "SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where id_Actividad ='".$id."'  order by id_Subactividad";
				$stmt = sqlsrv_query( $conn, $tsql);

				if( $stmt === false ) {
					die( print_r( sqlsrv_errors(), true));
				} else {
					$rows = sqlsrv_has_rows( $stmt );
				}			
				
                                /*$cuerpoSubactividad = $cuerpoSubactividad . 
				
                                '<label class="control-label" for="subactividad">Subactividad </label><div class="controls">
                                 <SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  return false">
				<option value=""></option>';
                                
                                if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						$cuerpoSubactividad = $cuerpoSubactividad . '<option value="'.$row["id_Subactividad"].'" >'.$row["Descripcion"].'</option>';
					}  //end while
				}	
                                
                                $cuerpoSubactividad = $cuerpoSubactividad .
                                '</select>
				</div>';*/
                                
                                //print ('<div class ="hidden">');	
				print ('<label class="control-label" for="subactividad">Subactividad</label>');
				print ('<div class="controls">');
				//print ('</br>');
                                
				print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">\n');
                                //print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  return false">\n');
				print ('<option value=""></option>');
                                
				if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						echo '<option value="'.$row["id_Subactividad"].'" >'.$row["Descripcion"].'</option>';
					}  //end while
				}	
				print ("</select>\n");
				print ('</div>');			
				//print ('</div>');		
                                
                                //echo $cuerpoSubactividad;
				//motivos de bloqueo
				$tsql = "SELECT DISTINCT ID_MOTIVO, DESCRIPCION from INV_tbMotivos_Bloqueo order by DESCRIPCION";
				
				$stmt = sqlsrv_query( $conn, $tsql);

				if( $stmt === false ) {
					die( print_r( sqlsrv_errors(), true));
				} else {
					$rows = sqlsrv_has_rows( $stmt );
				}			
					 
				// Mostrar resultados de la consulta
                                print ('</br>');
				print ('<label class="control-label" for="motivoBloq">Motivo de bloqueo  </label>');
				print ('<div class="controls">');
				//print ('</br>');

				print ('<SELECT class="span6" id="motivoBloq"  name="motivoBloq" onChange="seleccionarSubactividad(this.value); return false">\n');

				print ('<option value=""></option>');

				if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						echo '<option value="'.$row["ID_MOTIVO"].'" >'.$row["DESCRIPCION"].'</option>';
					}  //end while
				}	
				print ("</select>\n");
				print ('</div>');
			} else {
				//subactividad
				$tsql = "SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where id_Actividad ='".$id."'  order by id_Subactividad";
				
				$stmt = sqlsrv_query( $conn, $tsql);

				if( $stmt === false ) {
					die( print_r( sqlsrv_errors(), true));
				} else {
					$rows = sqlsrv_has_rows( $stmt );
				}			
				
				print ('<div>');	
				print ('<label class="control-label" for="subactividad">Subactividad </label>');
				print ('<div class="controls">');
				//print ('</br>');

				print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">\n');

				print ('<option value=""></option>');

				if ($rows === true){		
					while($row= sqlsrv_fetch_array($stmt)){
						echo '<option value="'.$row["id_Subactividad"].'" >'.$row["Descripcion"].'</option>';
					}  //end while
				}	
				print ("</select>\n");
				print ('</div>');			
				print ('</div>');						
			}	
		
		} else {
			//No hay actividad seleccionada
			print ('<label class="control-label" for="subactividad">Subactividad </label>');
			print ('<div class="controls">');
			print ('</br>');		
			print ('<SELECT class="span6" tabindex="6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">\n');	
			print ('<option value=""></option>');
			print ("</select>\n");
			print ('</div>');
		}

	}
	if ($_GET['dato'] == 'SELECTSUBACTIVIDAD') {
                
                ECHO ('PRUEBA: ' . $id);
		//Busca las características de la subactividad
		$tsql="SELECT ID_SUBACTIVIDAD FROM INV_tbMotivos_Bloqueo WHERE ID_MOTIVO='".$id."' ";

		$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);

		$rows = sqlsrv_has_rows( $stmt );

		if ($rows === true){						
			$row = sqlsrv_fetch_array($stmt);
			print ($row['ID_SUBACTIVIDAD']);

		} else {
			die ("Error al ejecutar consulta: ".$tsql);
		}		
	}


	if ($_GET['dato'] == 'CTOS') {
		if ($id != '') {
			$selected_CTOGESC ='CTO';	//DAKI

			$id_actuacion=$_GET['id_actuacion'];
			$ref_asociada=$_GET['ref_asociada'];
			$idActividad=$_GET['idActividad'];

			$bloqueo = NULL;
			$desbloqueo = NULL;
			$tratamientoCTOS = NULL;
			$resultado = '';

			//Busca las características de la subactividad
			$tsql="SELECT CTOS FROM INV_tbSubactividad WHERE id_Subactividad='".$id."' ";

			$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);

			$rows = sqlsrv_has_rows( $stmt );

			if ($rows === true){						
				$row = sqlsrv_fetch_array($stmt);
				$tratamientoCTOS = $row['CTOS'];

			} else {
				die ("Error al ejecutar consulta: ".$tsql);
			}


			//Busca las características de la actividad
			if ($tratamientoCTOS == NULL || $bloqueo == NULL || $desbloqueo == NULL) {
				$tsql="SELECT CTOS, BLOQUEO, DESBLOQUEO FROM INV_tbActividad WHERE id_actividad='".$idActividad."' ";

				$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);

				$rows = sqlsrv_has_rows( $stmt );

				if ($rows === true){						
					$row = sqlsrv_fetch_array($stmt);
					
					if ($tratamientoCTOS == NULL) {
						$tratamientoCTOS = $row['CTOS'];
					}

					if ($bloqueo == NULL) {
						if ($row['BLOQUEO'] == 'S') {
							$bloqueo = true;									
						} else {
							$bloqueo = false;
						}
					}

					if ($desbloqueo == NULL) {
						if ($row['DESBLOQUEO'] == 'S') {
							$desbloqueo = true;									
						} else {
							$desbloqueo = false;
						}
					}
				} else {
					die ("Error al ejecutar consulta: ".$tsql);
				}
			}


			if ($tratamientoCTOS != NULL) {
				//CTOS DE LA ACTUACION
			    $tsql = "select id_Actuacion, COD_CTO, NUMERO, BLOQ_CTO, NUM_PUERTOS, N_PUERTO_BLOQ, TIPO_BLOQ, MOTIVO_BLOQUEO
			    			from INV_CTOS 
			    			where id_Actuacion ='".$id_actuacion."' order by NUMERO";


				$ctosActuacion = sqlsrv_query($conn, $tsql);

				if( $ctosActuacion === false ) {
			    	die ("Error al ejecutar consulta: ".$tsql);
				}

				$rows = sqlsrv_has_rows($ctosActuacion );

				$arrayCtosTarea = array();

				if ($ref_asociada != '') { 
					//CTOS DE LA TAREA ASOCIADA
					$tsql = "SELECT INV_CTOS.NUMERO, INV_CTOS.COD_CTO
								FROM INV_TBTAREAS
									INNER JOIN INV_TBTAREAS_CTO ON INV_TBTAREAS_CTO.ID = INV_TBTAREAS.id
									INNER JOIN INV_CTOS ON INV_CTOS.COD_CTO = INV_TBTAREAS_CTO.COD_CTO
								WHERE INV_TBTAREAS.REF = '$ref_asociada'";
					
					$ctosTarea = sqlsrv_query($conn, $tsql);

					if( $ctosTarea === false ) {
				    	die( print_r( sqlsrv_errors(), true));
					}	

					while(($row =  sqlsrv_fetch_array($ctosTarea))) {
					    $arrayCtosTarea[] = $row['COD_CTO'];
					}

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
							//Ni bloqueos ni desbloqueos, POR DEFECTO
							echo "<INPUT TYPE='CHECKBOX' name='MARCAR[]' VALUE='" .$cto['COD_CTO'] . "'".((in_array($cto['COD_CTO'], $arrayCtosTarea))?' checked':'')."> ".$cto['NUMERO']."<br> ";
						}

					}

				}		
			}
		}

	}

	if ($_GET['dato'] == 'BOTONGESCALES') {
		$checkedCTOS=$_GET['checkedCTOS'];
		$idActividad=$_GET['idActividad'];

		$bloqueo = NULL;
		$desbloqueo = NULL;


		//Busca las características de la actividad
	
		$tsql="SELECT BLOQUEO, DESBLOQUEO FROM INV_tbActividad WHERE id_actividad='".$idActividad."' ";

		$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);

		$rows = sqlsrv_has_rows( $stmt );

		if ($rows === true){						
			$row = sqlsrv_fetch_array($stmt);

			if ($bloqueo == NULL) {
				if ($row['BLOQUEO'] == 'S') {
					$bloqueo = true;									
				} else {
					$bloqueo = false;
				}
			}

			if ($desbloqueo == NULL) {
				if ($row['DESBLOQUEO'] == 'S') {
					$desbloqueo = true;									
				} else {
					$desbloqueo = false;
				}
			}
		} else {
			die ("Error al ejecutar consulta: ".$tsql);
		}


		if ($checkedCTOS > 0) {

			if ($bloqueo || $desbloqueo) {
				echo '<div class="box-header" data-original-title>';
				echo '<a href="#"  id="idGescales" data-toggle="modal" data-target="#gescalesModal" ><h2><i class="halflings-icon pencil"></i><span class="break"></span>Seleccionar GESCALES</h2>

								</a>';
				echo '</div>';
			}
		}
	}

	if ($_GET['dato'] == 'RADIOCTOSGESC') {
		$idActividad=$_GET['idActividad'];
		$tratamientoCTOS = NULL;

		//Busca las características de la subactividad
		$tsql="SELECT CTOS FROM INV_tbSubactividad WHERE id_Subactividad='".$id."' ";

		$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);

		$rows = sqlsrv_has_rows( $stmt );

		if ($rows === true){						
			$row = sqlsrv_fetch_array($stmt);
			$tratamientoCTOS = $row['CTOS'];
		} else {
			die ("Error al ejecutar consulta: ".$tsql);
		}


		//Busca las características de la actividad
		if ($tratamientoCTOS == NULL) {
			$tsql="SELECT CTOS FROM INV_tbActividad WHERE id_actividad='".$idActividad."' ";

			$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);

			$rows = sqlsrv_has_rows( $stmt );

			if ($rows === true){						
				$row = sqlsrv_fetch_array($stmt);
				$tratamientoCTOS = $row['CTOS'];
			} else {
				die ("Error al ejecutar consulta: ".$tsql);
			}
		}


		if ($tratamientoCTOS != NULL) {
			echo "<div>";
			echo "<label class='radio'>";
			echo "<input type='radio' name='CTOgesc' id='radioCTO' value='CTO' checked >";
			echo "CTOS";
			echo "</label>";
			echo "<div style='clear:both'></div>";
			echo "<label class='radio'>";
			echo "<input type='radio' name='CTOgesc' id='radioGescales' value='GESC' >";
			echo "GESCALES";
			echo "</label>";
			echo "</div>";
		}

	}	

	if ($_GET['dato'] == 'GESCALES') {
		$CTOgesc = $_GET['CTOgesc'];

		$marcarCTO = explode(",", $_GET['marcarCTO']);
		
		$nfilasCTOS = count ($marcarCTO);

		$marcarGES = explode(",", $_GET['marcarGES']);
		
		$nfilasGES = count ($marcarGES);		

		$bloqueo = false;
		$desbloqueo = false;

		//Ver si la actividad tiene BLOQUEOS O DESBLOQUEOS
		$tsql = "SELECT CTOS, BLOQUEO, DESBLOQUEO from INV_tbActividad where id_Actividad ='".$id."' ";

		$stmt = sqlsrv_query( $conn, $tsql) or die ("Error al ejecutar consulta: ".$tsql);

		$rows = sqlsrv_has_rows( $stmt );

		if ($rows === true){						
			$row = sqlsrv_fetch_array($stmt);
			if ($row['BLOQUEO'] == 'S') {
				$bloqueo = true;									
			}
			if ($row['DESBLOQUEO'] == 'S') {
				$desbloqueo = true;									
			}		
		} else {
			die ("Error al ejecutar consulta: ".$tsql);
		}

		//GESCALES DE LOS CTOS MARCADOS EN PANTALLA		
		if (!empty($marcarCTO[0])) {

			if ($row['CTOS'] != NULL) {



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

				if (!empty($marcarGES[0])) {
					//Relacionar las CTOS que tenga asignadas por pantalla		
					for ($i=0; $i<$nfilasGES; $i++) {	
						$arrayGescTarea[] = $marcarGES[$i];
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
								echo "<INPUT TYPE='CHECKBOX' name='MARCARGESC[]' VALUE='" .$gescal['COD_CTO']."-".$gescal['COD_GESCAL']. "'".((in_array($gescal['COD_GESCAL'], $arrayGescTarea))?' checked':'')."> ".$gescal['NUMCTO']." - ".$gescal['GESCAL']."<br> ";	
							}

						}

					}
					
				}
			}
		}

	}

	if ($_GET['dato'] == 'origenSol') {

		
		$tsql="SELECT DISTINCT USUORIGEN AS USUORIGEN from INV_VIEW_DATOS_TODO WHERE USUORIGEN LIKE '%".$id."%' ORDER BY USUORIGEN";
			
		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		print ('<SELECT id="origenSol"  name="origenSol" >\n');

		PRINT ('<option value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				echo '<option value="'.$row["USUORIGEN"].'" '.(($row["USUORIGEN"]==$seleccionadoOrigenSol)?'selected="selected"':"").'>'.$row["USUORIGEN"].'</option>';
			}
		}	
		print ("</select>\n");

	}	


	if ($_GET['dato'] == 'tecnico') {

		$tsql="select distinct TECNICO from INV_VIEW_DATOS_TODO where TECNICO  LIKE '%".$id."%' order by TECNICO";
			
		$stmt = sqlsrv_query( $conn, $tsql);

		if( $stmt === false ) {
			die( print_r( sqlsrv_errors(), true));
		} else {
			$rows = sqlsrv_has_rows( $stmt );
		}			
				 
		// Mostrar resultados de la consulta

		print ('<SELECT id="tecnico"  name="tecnico" >\n');

		PRINT ('<option value="" ></option>');

		if ($rows === true){		
			while($row= sqlsrv_fetch_array($stmt)){
				echo '<option value="'.$row["TECNICO"].'" '.(($row["TECNICO"]==$seleccionadoTecn)?'selected="selected"':"").'>'.$row["TECNICO"].'</option>';
			}
		}	
		print ("</select>\n");

	}		

	// Cerrar conexión
	if (isset($stmt)) {  
		sqlsrv_free_stmt( $stmt);						
		sqlsrv_close( $conn);
	}	
						
?>