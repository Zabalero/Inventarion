function mandarMail($conn, $id, $tarea, $cambioEstado){


				include_once( './Classes/class.phpmailer.php' ); //Importamos PHPMailer
				$mail = new PHPMailer(); //Creamos un objeto
				
					
				//DESTINATARIOS	

				//usuario que realiza la petición (usuario origen)
				//$mailUsu=$_REQUEST['mail']; 	
				//$nombreUsu=$_REQUEST['nombreUsuOr']; 
				$mailUsu='maria.benito@ext.jazztel.com'; 	
				$nombreUsu=$_REQUEST['Maite Benito']; 
					
				//ST inv lógico				
				//$mailST='servicios.tecnicos.inventario.logico@jazztel.com';
				//$nombreST='ST_InventarioLogico';

				//ST inv lógico				
				$mailST='maiteben@gmail.com';
				$nombreST='ST_InventarioLogico';				
				
				//ST inv fco
				/*$mailSTfco='servicios.tecnicos.inventario@jazztel.com';
				$nombreSTfco='Servicios Tecnicos I.Fisico';*/

				//Francisco Flores Diéguez 
				//$mailFrancF='francisco.flores@jazztel.com';
				//$nombreFrancF='Francisco Flores Diéguez';
				
				//SSR
				//$mailSSR='ssrftth@jazztel.com';
				//$nombreSSR='SSR';
				
				//Ana Mercedes
				//$mailAnaM='anamercedes.roldan@jazztel.com';
				//$nombreAnaM='Ana Mercedes Roldán';
				
				
				//Javier Mora
				//$mailJavierMora='javier.mora@jazztel.com';
				//$nombreJavierMora='Javier Mora Alcázar';

				//Álvaro Martin Barrena
				//$mailAlvaroMartin ='alvaro.martin@jazztel.com';
				//$nombreAlvaroMartin = 'Álvaro Martin Barrena';

				//Álvaro Martin Barrena
				//$mailGerardoAlberto ='gerardoalberto.marcelo@ext.jazztel.com';
				//$nombreGerardoAlberto = 'Gerardo Alberto de Marcelo Benito';	
				
				//Buzón región
				switch ($_POST['ID_REGION']) {
					case 1://Andalucía Occidental
						$mailBuzonRegion='';
						$nombreBuzonRegion='';
						break;
					case 2: //Andalucía Oriental
						$mailBuzonRegion='';
						$nombreBuzonRegion='';
						break;
					case 3://Cataluña
						$mailBuzonRegion='';
						$nombreBuzonRegion='';
						break;
					case 4://Centro
						//$mailBuzonRegion='pedro.ramirez@ext.jazztel.com;manuel.barajas@jazztel.com';
						//$nombreBuzonRegion='Regional Madrid';					
						$mailBuzonRegion='';
						$nombreBuzonRegion='';
						break;
					case 5://Levante
						$mailBuzonRegion='maiteben@gmail.com';
						$nombreBuzonRegion='Región Levante';					
						break;
				}
		
				//Destinatarios según Actividad de la tarea					
						
				$codActividad=$_POST["ID_ACTIVIDAD"];
				$codSubactividad=$_POST["ID_SUBACTIVIDAD"];
				
				switch ($codActividad) {
				
					case 1://Bloqueo/Desbloqueo cobertura						
					case 8://Bloqueo cobertura
					case 9://Desbloqueo cobertura
						if ($codSubactividad == 148) {
							//$mailporActiv=$mailAlvaroMartin;
							//$nombreporActiv=$nombreAlvaroMartin;	
						}
						break;							
					case 2: //Incidencia instalación
						//$mailporActiv=$mailSSR;
						//$nombreporActiv=$nombreSSR;
						//otros
						//sólo cuando las subactividades sean 'Modificaciones de trazado',  'Timbrado CTOS', 'Rediseño RED', 'Nueva cobertura' o 'Nuevos elementos de trazado'.
						//if ($codSubactividad==56 || $codSubactividad==57 || $codSubactividad==58 || $codSubactividad==60 || $codSubactividad==61){						
						//	$mailporActiv2=$mailSTfco;
						//	$nombreporActiv2=$nombreSTfco;								
						//}							
						
						break;
					
					case 5://Mantenimiento preventivo
						//sólo cuando las subactividades sean 'Puertos Averiados' o 'Revisión Puentes Central' 
						//if ($codSubactividad==63 || $codSubactividad==64){
						//	$mailporActiv=$mailJavierMora;
						//	$nombreporActiv=$nombreJavierMora;
						//}
						break;
						
					case 6://Modificaciones nueva red											
						//$mailporActiv=$mailSTfco;
						//$nombreporActiv=$nombreSTfco;
						//Si el subestado es 'Nueva cobertura PLAN HUECOS' envia tambien a Francisco Flores
						//if ($codSubactividad==109){
						//	$mailporActiv2=$mailFrancF;
						//	$nombreporActiv2=$nombreFrancF;
						//}
						break;
					
					
					case 7://Modificaciones red existente
						//sólo cuando las subactividades sean 'Modificaciones de trazado',  'Integración FIR-GIS', 'Timbrado CTOS' o 'Rediseño RED'
						//if ($codSubactividad==67 || $codSubactividad==68 || $codSubactividad==70 || $codSubactividad==72){						
						//	$mailporActiv=$mailSTfco;
						//	$nombreporActiv=$nombreSTfco;								
						//}
						//if ($codSubactividad==132){						
						//	$mailporActiv=$mailAlvaroMartin;
						//	$nombreporActiv=$nombreAlvaroMartin;
						//	$mailporActiv2=$mailGerardoAlberto;
						//	$nombreporActiv2=$nombreGerardoAlberto;
						//}
						break;
						
					case 4://General-inventario logico							
						break;
							
				}
					
				//busca al responsable del usuario de origen si lo tiene (para el envío automático de mail al responsable)			
				$tsql = "select tbUsuarios1.nombre, tbUsuarios2.nombre as nombreResponsable, tbUsuarios2.mail as mailResponsable from INV_tbUsuarios as tbUsuarios1 inner join INV_tbUsuarios as tbUsuarios2 on tbUsuarios1.idResponsable=tbUsuarios2.id_usu where tbUsuarios1.id_usu='".$_POST["ID_USUORIGEN"]."'";
				
				$stmt = sqlsrv_query( $conn, $tsql);
								
				$rows = sqlsrv_has_rows( $stmt );
					
				// if ($rows === true){					
				// 	while($row = sqlsrv_fetch_array($stmt)){
				// 		if ($row["mailResponsable"]!=''){
				// 			$mailResponsable=$row["mailResponsable"];
				// 			$nombreResponsable=$row["nombreResponsable"];
				// 		}
				// 	}
				// }
				// else{
					$mailResponsable='';
					$nombreResponsable='';
				// }
						
				//ENVIO DE MAIL A DESTINATARIOS										
						
				
				$mail->AddAddress( $mailUsu, $nombreUsu );//usuario que realiza la petición (usuario origen)
				$mail->AddCC( $mailST, $nombreST );//ST inv lógico	SI	
				//$mail->AddCC( $mailAnaM, $nombreAnaM );//Ana Mercedes SI					
				
				//$mail->AddCC( $mailBuzonRegion, $nombreBuzonRegion );//buzón según región
				//$mail->AddCC( $mailporActiv, $nombreporActiv );//mail según actividad
				//$mail->AddCC( $mailporActiv2, $nombreporActiv2 );//mail según actividad2
				//$mail->AddCC( $mailResponsable, $nombreResponsable );//responsable de cada usuario de origen
				
				
				//CUERPO MENSAJE
							
				$cuerpo='
				
						<html>
						<head>
						</head>
						<body style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12px">
					
						<p>Hola,</p>

						<p>En relación a la solicitud de '.$_REQUEST["activ"].' - '.$_REQUEST["descrSubact"].' con Nº REF: <span style="font-weight: bold">'.$ref.'</span></p>';
				
						if (isset($_REQUEST["act_Jazz"]) && $_REQUEST["act_Jazz"]!="") {
							$cuerpo=$cuerpo.'<p><span style="font-style: italic">Actuación: </span><span style="font-weight: bold">'.$_REQUEST["act_Jazz"].'</span></br>';	
						} else {
							$cuerpo=$cuerpo.'<p><span style="font-style: italic">Cabecera: </span><span style="font-weight: bold">'.$_REQUEST["cab"].'</span></br>';
						}
						
						if (isset($incid)){
							$cuerpo=$cuerpo.'<span style="font-style: italic">Incidencia de instalación: </span><span style="font-weight: bold">'.$incid.'</span></br>';	
						}
							
						if (isset($oceane)){
							$cuerpo=$cuerpo.'<span style="font-style: italic">TICKET OCEANE: </span><span style="font-weight: bold">'.$oceane.'</span></br>';	
						}	

						if (isset($coment2) && $coment2!="") {
						
							switch ($cambioEstado) {
							
								 case 4:	//cerrada		
											$cuerpo=$cuerpo.'
													<span style="font-style: italic">Comentarios:</span></br>
													<span style="color: #103A7D"><pre>'.$coment2.'</pre></span></p>								
											';
											
											break; 
											
								 case 3:	//Pte contestación
								
											$cuerpo=$cuerpo.'
											</br>
													Para poder llevar a cabo la actualización solicitada en el aplicativo es necesario:</br>
													<span style="color: #103A7D"><pre>'.$coment2.'</pre></span></p>								
											';													
																								
											break; 
																									
								 case 5:	//Pte bloqueo
											$cuerpo=$cuerpo.'
													<span style="font-style: italic">Comentarios:</span></br>
													<span style="color: #103A7D"><pre>'.$coment2.'</pre></span></p>								
											';

											break;
																									
								case 6:	//Pte desbloqueo
											$cuerpo=$cuerpo.'
													<span style="font-style: italic">Comentarios:</span></br>
													<span style="color: #103A7D"><pre>'.$coment2.'</pre></span></p>								
											';

											break;

								case 8:	//En construcion
											$cuerpo=$cuerpo.'
													<span style="font-style: italic">Comentarios:</span></br>
													<span style="color: #103A7D"><pre>'.$coment2.'</pre></span></p>								
											';
											break;			
							}	
																
						}
										
						switch ($cambioEstado) {
							
								 case 4:	//cerrada		

											$cuerpo=$cuerpo.'</br><p>La solicitud ha sido cerrada.</p></br>';
											
											break; 
											
											
								 case 3:	//Pte contestación
								 	
											
											$cuerpo=$cuerpo.'										
											</br>
											
											<p>Quedamos a la espera de vuestra contestación, para ello es necesario introducir en la aplicación web
											el número de referencia '.$ref.' asignado a esta tarea.</p>																				
											
											</br></br>';
											
											break; 
											
								 case 8:	//En construccion	

											$cuerpo=$cuerpo.'

											</br>

											<p>	La solicitud queda con estado En construccion </p>

											</br>';
											
								 			break;

								 case 9:	//Pendiente de SUC

											$cuerpo=$cuerpo.'

											</br>

											<p>	La solicitud queda con estado Pendiente de SUC </p>

											</br>';

								 			break;

								 case 10:	//Pendiente de aprobación Presupuesto

											$cuerpo=$cuerpo.'

											</br>

											<p>	La solicitud queda con estado Pendiente de aprobación Presupuesto </p>

											</br>';
											
								 			break;

								 case 11:	//Pendiente de Ingeniería	

											$cuerpo=$cuerpo.'

											</br>

											<p>	La solicitud queda con estado Pendiente de Ingeniería </p>

											</br>';
											
								 			break;
											
								 case 5:	//Pte bloqueo
											
											$cuerpo=$cuerpo.'		
														
											</br>
											<p>La solicitud queda pendiente de bloqueo.</p>	
																								
											</br>';
											
											break;
											
											
								case 6:	//Pte desbloqueo
											
											$cuerpo=$cuerpo.'		
														
											</br>
											<p>La solicitud queda pendiente de desbloqueo.</p>	
																								
											</br>';

											break;	
							}	

							if ($_POST['MARCAR'] == ''){	
								$marcar ='';
							}
							else{
								$marcar = $_POST['MARCAR'];
								$nfilasMarc = count ($marcar);
							}							
										
							if ($nfilas>0) {
							

								switch ($cambioEstado) {
									 case 4:	//cerrada		
												$cuerpo=$cuerpo.'	
												<p>Se han cerrado '.$nfilas .' tareas relacionadas:</p>
												';	
												break; 
									 case 3:	//Pte contestación
												$cuerpo=$cuerpo.'	
													<p>Tareas relacionadas:</p>
												';
												break; 			
									 case 5:	//Pte bloqueo
												$cuerpo=$cuerpo.'	
													<p>Quedan como pendientes de bloqueo las '.$nfilas .' tareas relacionadas:</p>
												';
												break;
									 case 6:	//Pte desbloqueo
												$cuerpo=$cuerpo.'	
													<p>Quedan como pendientes de desbloqueo las '.$nfilas .' tareas relacionadas:</p>
												';
												break;	
									 case 8:	//En Construccion
												$cuerpo=$cuerpo.'	
													<p>Quedan como En construccion las '.$nfilas .' tareas relacionadas:</p>
												';
												break;	

									 case 9:	//Pendiente de SUC
												$cuerpo=$cuerpo.'	
													<p>Quedan como Pendiente de SUC las '.$nfilas .' tareas relacionadas:</p>
												';
												break;	

									 case 10:	//Pendiente de aprobación Presupuesto
												$cuerpo=$cuerpo.'	
													<p>Quedan como Pendiente de aprobación Presupuesto las '.$nfilas .' tareas relacionadas:</p>
												';
												break;	
									 case 11:	//Pendiente de Ingeniería
												$cuerpo=$cuerpo.'	
													<p>Quedan como Pendiente de Ingeniería las '.$nfilas .' tareas relacionadas:</p>
												';
												break;			
												
								}		
								
								
								for ($i=0; $i<$nfilas; $i++){
										
											$tsql3 = "SELECT tbTareas.REF, tbTareas.COMENTARIOS2, tbTareas.INCIDENCIA, DatosActuaciones.ACT_JAZZTEL, tbCabeceras.Descripcion";
											$tsql3=$tsql3." FROM tbTareas LEFT JOIN DatosActuaciones";	
											$tsql3=$tsql3." ON tbTareas.ID_ACTUACION = DatosActuaciones.ID_ACTUACION";
											$tsql3=$tsql3." LEFT JOIN tbCabeceras ON tbTareas.cod_cabecera=tbCabeceras.Cod_Cabecera";
											$tsql3=$tsql3." WHERE id = $marcarProc[$i]";
																 $stmt3 = sqlsrv_query( $conn, $tsql3)
																 or die ("Fallo en la consulta");
										

						
										$rows3 = sqlsrv_has_rows( $stmt3 );
									
										if ($rows3 === true){
											while($row3 = sqlsrv_fetch_array($stmt3)){											
														
																					
													
													
											if (isset($row3['ACT_JAZZTEL']) && $row3['ACT_JAZZTEL']!="") {
												$cuerpo=$cuerpo.'
													<p><span style="font-style: italic">Actuación: </span><span style="font-weight: bold">'.$row3['ACT_JAZZTEL'].'</span></br>								
											';	
											}
										
											else{
												$cuerpo=$cuerpo.'
														<p><span style="font-style: italic">Cabecera: </span><span style="font-weight: bold">'.$row3['Descripcion'].'</span></br>								
												';
											}
										
											
								
								
												if (isset($row3['INCIDENCIA'])){
													$cuerpo=$cuerpo.'
															<span style="font-style: italic">Incidencia de instalación: </span><span style="font-weight: bold">'.$row3['INCIDENCIA'].'</span></br>								
													';	
												}
												

												if (isset($row3['COMENTARIOS2']) && $row3['COMENTARIOS2']!="") {
													$cuerpo=$cuerpo.'
													<span style="font-style: italic">Comentarios:</span></br>
													<span style="color: #103A7D"><pre>'.$row3['COMENTARIOS2'].'</pre></span></p>							
											';	
												}
			
																							
											}
										
										}										
																			

									}

									$cuerpo=$cuerpo.'	
									</br>
								';
									
									
								
								}
								
								
								$cuerpo=$cuerpo.'		
										
										
										
										</br>

															<p>	Para cualquier aclaración podéis poneros en contacto con el departamento de Inventario.
																Buzón del departamento:</p>
																	<p>•	ST_InventarioLogico: servicios.tecnicos.inventario.logico@jazztel.com</p>
																	<p>•	ST_InventarioFisico: servicios.tecnicos.inventario@jazztel.com</p>
																</br>
																<p>Aplicación web http://ftth-dst.jazztel.com/inventariologico </p>

															</br>

															
										<p>Saludos,</p>
										
									
										
										<p>'.get_nombre($_SESSION['usuario']).'<p>
										
										</br></br>
										
										<p><span style="font-weight: bold;color:#103A7D">Inventario Lógico</span></br>
										<span style="font-weight: bold;color:#707070">Servicios Técnicos FTTH</span></p>
										 
											
											<p><a href="http://www.jazztel.com/internet/fibra-no-clientes.html?slc=n&enableFullSite=true&utm_source=firma-corporativa&utm_medium=email&utm_campaign=firma-corporativa"><img src="cid:my-attach"></a></p>
										
										<p><span style="color:#707070">C/ Anabel Segura, 11 – Edificio C Albatros</br>
											28108 Alcobendas. Madrid</br>
											Tel. fijo: 910861909</span></p>
											
										<p>ST_InventarioLogico: servicios.tecnicos.inventario.logico@jazztel.com</p>
										
										<p><a href="https://www.facebook.com/Jazztel"><img src="cid:my-attach1"></a>
										<a href="https://twitter.com/jazztel_es"><img src="cid:my-attach2"></a>
										<a href="https://www.youtube.com/jazztel"><img src="cid:my-attach3"></a>
										<a href="https://plus.google.com/+jazztel"><img src="cid:my-attach4"></a>
										<a href="http://www.anexom.es/"><img src="cid:my-attach5"></a>
										</p>
										
									
									</body>
									</html>
									
								';	
								
								//FIN CUERPO MENSAJE
								
					
					
					

					//asunto
					
					
					//cabecera
					$cabeceraMail='[Inv Lógico] REF: '.$ref;
					
					
					if (isset($incid)){
						$cabeceraMail=$cabeceraMail.' - INC: '.$incid;
					}	
					if (isset($oceane)){
						$cabeceraMail=$cabeceraMail.' - TICKET OCEANE: '.$oceane;
					}	
					
					if (isset($_REQUEST["act_Jazz"]) && $_REQUEST["act_Jazz"]!="") {
						$cabeceraMail=$cabeceraMail.' - ACT: '.$_REQUEST["act_Jazz"];
					}								
					else{
						$cabeceraMail=$cabeceraMail.' - CAB: '.$_REQUEST["cab"];
					}
					
					
					
					switch ($selected_cambioEst) {
						 case 4:	//cerrada		
									$cabeceraMail=$cabeceraMail.'- Solicitud finalizada'; 	
									break; 
						 case 3:	//Pte contestación
									$cabeceraMail=$cabeceraMail.'- Solicitud pendiente de contestación'; 	
									break; 			
						 case 5:	//Pte bloqueo
									$cabeceraMail=$cabeceraMail.'- Solicitud pendiente de bloqueo'; 	
									break;
						 case 6:	//Pte desbloqueo
									$cabeceraMail=$cabeceraMail.'- Solicitud pendiente de desbloqueo'; 	
									break;	
				  		 case 8:	//En construccion
									$cabeceraMail=$cabeceraMail.'- Solicitud En construccion'; 	
									break;	
						 case 9:	//Pendiente de SUC
									$cabeceraMail=$cabeceraMail.'- Solicitud Pendiente de SUC'; 	
									break;	
						 case 10:	//Pendiente de aprobación Presupuesto
									$cabeceraMail=$cabeceraMail.'- Solicitud Pendiente de aprobación Presupuesto'; 	
									break;	
						 case 11:	//Pendiente de Ingeniería
									$cabeceraMail=$cabeceraMail.'- Solicitud Pendiente de Ingeniería'; 	
									break;		
					}	
					
					
					
					
					
					$mail->Subject = $cabeceraMail; 	

					
				
				
				
				
				
				//remitente
				//$mail->SetFrom( 'ana.gonzalo@ext.jazztel.com', 'Ana Gonzalo' );
				$mail->SetFrom( $mailST, $nombreST );
				
				$mail->AddEmbeddedImage("images/iconFirma.jpg", "my-attach", "images/iconFirma.jpg");
				$mail->AddEmbeddedImage("images/firmaMail1.jpg", "my-attach1", "images/firmaMail1.jpg");
				$mail->AddEmbeddedImage("images/firmaMail2.jpg", "my-attach2", "images/firmaMail2.jpg");
				$mail->AddEmbeddedImage("images/firmaMail3.jpg", "my-attach3", "images/firmaMail3.jpg");
				$mail->AddEmbeddedImage("images/firmaMail4.jpg", "my-attach4", "images/firmaMail4.jpg");
				$mail->AddEmbeddedImage("images/firmaMail5.jpg", "my-attach5", "images/firmaMail5.jpg");
				
				
				//email para responder
				//$mail->AddReplyTo( 'ana.gonzalo@ext.jazztel.com' );
				$mail->AddReplyTo( $mailST, $nombreST );
				
				
				
				//cuerpo del mail
				$mail->Body = $cuerpo; 
				
				
				
				$mail->IsHTML(true);
				$mail->CharSet = 'UTF-8';
				
				$mail->Send(); //Enviamos el email
				
				
				echo "<script>alert('Mail enviado correctamente')</script>";
				
			//}	
			
		}	
		
}