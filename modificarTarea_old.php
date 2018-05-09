<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
	require "inc/funcionesCambiarEstado.inc";
	require "inc/funcionesModificar.inc";
      
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
	$modificacionCorrecta = false;
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
	$gestor = "";
	$ticket_remedy = "";
	$ticket_oceane = "";
	$ticket_escalado = "";
	$ticket_tp = "";
	$adjunto = "";
	$prioridad = "";

	$fecha_inicio = "";
	$fecha_resol = "";
	$fecha_registro = "";

	$grupo_escalado = "";
	$tecnico = "";
	$id_tecnico = "";

	$id_mapeo = "";
	$id_tipo_entrada = "";
	$tipologia_inicial = "";
	$tipo_incidencia = "";
	$tipo_cliente = "";

	$comentarios = "";
	$comentarios2 = "";

	$cto_nueva = "";

	$refAsociada = ""; 
	$ref = "";

	$motivoBloq="";

	$ctosActuacion = "";
	$arrayCtosTarea = "";	

	$selected_CTOGESC ="";

	$id = $_REQUEST['id']; 

	// Si ya hemos introducido valores para insertar la tarea
	// NUEVA TAREA CON CABECERA O ACTUACION
    if($_SERVER['REQUEST_METHOD']=='POST') {  

		$refAsociada = $_POST['refAsoc'];
		$ref =  $_POST['ref']; 
		$id_Actuacion = $_POST['idAct'];
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
		$gestor = $_POST['GESTOR'];
		$ticket_remedy = $_POST['INCIDENCIA'];
		$ticket_oceane = $_POST['TICKET_OCEANE'];
		$ticket_escalado = $_POST['TICKET_ESCALADO'];
		$ticket_tp = $_POST['TP'];
		$eemm = $_POST['EEMM'];
		$grupo = $_POST['GRUPO'];
		$id_usuorigen = $_POST['ID_USUORIGEN'];

		$prioridad = $_POST['prioridad'];

		$fecha_inicio = $_POST['fecha_inicio'];
		$fecha_resol = $_POST['fecha_resol'];
		$fecha_registro = $_POST['fecha_registro'];

		$grupo_escalado = $_POST['grupo_escalado'];
		$tecnico = $_POST['tecnico'];
		$id_tecnico = $_POST['id_tecnico'];

		$id_mapeo = $_POST['id_mapeo'];
		$id_tipo_entrada = $_POST['id_tipo_entrada'];
		$tipologia_inicial = $_POST['tipologia_inicial'];
		$tipo_incidencia = $_POST['tipo_incidencia'];
		$tipo_cliente = $_POST['tipo_cliente'];


		$adjunto = $_FILES['adjunto']['name'];	

		//$comentarios = $_POST['COMENTARIOS'];	
                $txt_estado = explode("-", $_POST['ESTADO']);
                $dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
                $comentarios = $_POST['COMENTARIOS']."\r\n\r\n"."*".$dias[date("w")].", ". date("d") . " del " . date("m") . " de " . date("Y")." ".date("H").":".date("i")." - Se ha grabado la Tarea con estado: ".$txt_estado[1]." - Modificada por usuario: ".$_SESSION['usuario'];	
		$comentarios2 = $_POST['COMENTARIOS2']; 
		   	
		$cto_nueva = $_POST['cto_nueva'];

		if (isset($_POST['CTOgesc'])) {
			$selected_CTOGESC = $_POST['CTOgesc'];
		}	

                if (isset($_POST['motivoBloq'])) {
                    $motivoBloq=$_POST['motivoBloq'];
                } 

                $ctrl_envioMail=false; //para producción
                //$ctrl_envioMail=true; //Para pruebas
                
                //Funcion cambio de estado de la tarea
                $nuevoEstado="";
                if (isset($_POST['cambiarEstado'])){
                        //echo ($_POST['ESTADO']);exit(); //Ejemplpo: 1-Pendiente
                        $mensaje = cambiarEstado($conn, $id, $id_usuario);
                        
                        //Si el estado al que cambia es 4 (Cerrada) entonces se enviará un mail por defecto
                        $nuevoEstado = explode("-", $_POST['ESTADO']);
                        if ($nuevoEstado =="4"){
                            $ctrl_envioMail=true;
                        }
                }
                
                //Comprobar si se ha pulsado el botón de enviar un mail
                $envio_mail=$_POST["envio_mail"];
                if ($envio_mail=="enviar"){
                    $ctrl_envioMail=true;
                }
                
                //
                if ($ctrl_envioMail){

                    $selected_cambioEst = $nuevoEstado[0];
                    
                    // Obtener número de registros a procesar
                    $marcarProc = $_REQUEST['marcarProc'];
                   // $tecnico = $_REQUEST['tecnico'];
                    $nfilas = count ($marcarProc);
	 
                    //ENVIO DE MAIL	 
                    //include_once( './Classes/class.phpmailer.php' ); //Importamos PHPMailer
                    //$mail = new PHPMailer(); //Creamos un objeto	 
					 
					 
                    switch ($selected_cambioEst) {
                            case 4:	//cerrada		
                                                   $cerrar_status = 'checked';	
                                                   break; 
                            case 3:	//Pte contestación
                                                   $pteCont_status = 'checked';
                                                   break; 			
                            case 5:	//Pte bloqueo
                                                   $pteBloq_status = 'checked';
                                                   break;
                            case 6:	//Pte desbloqueo
                                                   $pteDesbloq_status = 'checked';
                                                   break;	
                            case 8:	//En construccion
                                                   $enConst_status = 'checked';
                                                   break; 		
                            case 9:	//Pendiente de SUC
                                                   $penSuc_status = 'checked';
                                                   break; 	
                            case 10:	//Pendiente de aprobación Presupuesto
                                                   $penPresu_status = 'checked';
                                                   break; 	
                            case 11:	//Pendiente de Ingeniería
                                                   $penInge_status = 'checked';
                                                   break; 	
                    }	
				
                    
                    //DESTINATARIOS	
                    //
                    //usuario que realiza la petición (usuario origen)
                    $mailUsu=$_REQUEST['mail']; 	
                    $nombreUsu=$_REQUEST['nombreUsuOr']; 

                    //ST inv lógico				
                    $mailST='invLogico.ftth@orange.com';
                    $nombreST='ST_InventarioLogico';

                    //ST inv fco
                    /*$mailSTfco='servicios.tecnicos.inventario@jazztel.com';
                    $nombreSTfco='Servicios Tecnicos I.Fisico';*/

                    //Francisco Flores Diéguez 
                    $mailFrancF='francisco.flores@jazztel.com';
                    $nombreFrancF='Francisco Flores Diéguez';

                    //SSR
                    $mailSSR='ssrftth@jazztel.com';
                    $nombreSSR='SSR';

                    //Ana Mercedes
                    $mailAnaM='anamercedes.roldan@jazztel.com';
                    $nombreAnaM='Ana Mercedes Roldán';


                    //Javier Mora
                    $mailJavierMora='javier.mora@jazztel.com';
                    $nombreJavierMora='Javier Mora Alcázar';

                    //Álvaro Martin Barrena
                    $mailAlvaroMartin ='alvaro.martin@jazztel.com';
                    $nombreAlvaroMartin = 'Álvaro Martin Barrena';

                    //Álvaro Martin Barrena
                    $mailGerardoAlberto ='gerardoalberto.marcelo@ext.jazztel.com';
                    $nombreGerardoAlberto = 'Gerardo Alberto de Marcelo Benito';									

                    //yo prueba
                    //$mailPrueba='ana.gonzalo@ext.jazztel.com';
                    //$nombrePrueba='Ana Gonzalo';
  

                    //Destinatarios según Actividad de la tarea																	
                    $codActividad=$id_actividad;
                    $codSubactividad=$subactividad;

                    switch ($codActividad) {
                        case 1://Mantenimiento preventivo
                                //sólo cuando las subactividades sean 'Puertos Averiados' o 'Revisión Puentes Central' 
                                if ($codSubactividad==63 || $codSubactividad==64){
                                        $mailporActiv=$mailJavierMora;
                                        $nombreporActiv=$nombreJavierMora;
                                }
                                break;
                        case 2://Modificaciones nueva red											
                                        $mailporActiv=$mailSTfco;
                                        $nombreporActiv=$nombreSTfco;
                                        //Si el subestado es 'Nueva cobertura PLAN HUECOS' envia tambien a Francisco Flores
                                        if ($codSubactividad==109){
                                                $mailporActiv2=$mailFrancF;
                                                $nombreporActiv2=$nombreFrancF;
                                        }
                                break;
                        case 3://Modificaciones red existente
                                //sólo cuando las subactividades sean 'Modificaciones de trazado',  'Integración FIR-GIS', 'Timbrado CTOS' o 'Rediseño RED'
                                if ($codSubactividad==67 || $codSubactividad==68 || $codSubactividad==70 || $codSubactividad==72){						
                                        $mailporActiv=$mailSTfco;
                                        $nombreporActiv=$nombreSTfco;								
                                }
                                if ($codSubactividad==132){						
                                        $mailporActiv=$mailAlvaroMartin;
                                        $nombreporActiv=$nombreAlvaroMartin;
                                        $mailporActiv2=$mailGerardoAlberto;
                                        $nombreporActiv2=$nombreGerardoAlberto;
                                }
                                break;        
                        case 4://Bloqueo cobertura
                        case 5://Desbloqueo cobertura
                                if ($codSubactividad == 148) {
                                        $mailporActiv=$mailAlvaroMartin;
                                        $nombreporActiv=$nombreAlvaroMartin;	
                                }
                                break;							
                        case 8: //Incidencia instalación
                                $mailporActiv=$mailSSR;
                                $nombreporActiv=$nombreSSR;
                                //otros
                                //sólo cuando las subactividades sean 'Modificaciones de trazado',  'Timbrado CTOS', 'Rediseño RED', 'Nueva cobertura' o 'Nuevos elementos de trazado'.
                                if ($codSubactividad==56 || $codSubactividad==57 || $codSubactividad==58 || $codSubactividad==60 || $codSubactividad==61){						
                                        $mailporActiv2=$mailSTfco;
                                        $nombreporActiv2=$nombreSTfco;								
                                }							

                                break;
                        case 9://General-inventario logico							
                                break;
                    }

                    //busca al responsable del usuario de origen si lo tiene (para el envío automático de mail al responsable)			
                    $tsql = "select tbUsuarios1.nombre, tbUsuarios2.nombre as nombreResponsable, tbUsuarios2.mail as mailResponsable from INV_tbUSUARIOS as tbUsuarios1 inner join INV_tbUSUARIOS as tbUsuarios2 on tbUsuarios1.idResponsable=tbUsuarios2.id_usu where tbUsuarios1.id_usu=".$id_usuario;
                    $stmt = sqlsrv_query( $conn, $tsql) or die ("Fallo en la consulta4");
                    $rows = sqlsrv_has_rows( $stmt );
                    if ($rows === true){					
                            while($row = sqlsrv_fetch_array($stmt)){
                                    if ($row["mailResponsable"]!=''){
                                            $mailResponsable=$row["mailResponsable"];
                                            $nombreResponsable=$row["nombreResponsable"];
                                    }
                            }
                    }
                    else{
                            $mailResponsable='';
                            $nombreResponsable='';
                    }
                    //echo ($tsql);exit();
									
		
                    //Debo obtener el texto descriptivo 
                    $descr_activ="";
                    $descr_subactiv="";
                    
                    if ($id_actividad!="" && $id_actividad!=null){                        
                        $tsqlA = "SELECT ACTIVIDAD
                                        FROM INV_tbActividad
                                        WHERE id_actividad = ".$id_actividad;
                        $resultado = sqlsrv_query($conn, $tsqlA);
                        if( $resultado === false ) {
                            //Nada
                        } else {
                            $registro = sqlsrv_fetch_array($resultado);
                            $descr_activ = $registro['ACTIVIDAD'];
                        }	
                        sqlsrv_free_stmt($resultado);
                    }
                    if ($subactividad!="" && $subactividad!=null){                        
                        $tsqlA = "SELECT Descripcion
                                        FROM INV_tbSubactividad
                                        WHERE id_Subactividad = ".$subactividad;
                        $resultado = sqlsrv_query($conn, $tsqlA);
                        if( $resultado === false ) {
                            //Nada
                        } else {
                            $registro = sqlsrv_fetch_array($resultado);
                            $descr_subactiv = $registro['Descripcion'];
                        }	
                        sqlsrv_free_stmt($resultado);
                    }

                    
                    //Cuerpo mensaje							
                    $cuerpo='
                            <html>
                            <head>
                            </head>
                            <body style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12px">

                            <p>Hola,</p>
                            <p>En relación a la solicitud de '.$descr_activ.' - '.$descr_subactiv.' con Nº REF: <span style="font-weight: bold">'.$ref.'</span></p>					
                            ';
                    //echo ($cuerpo);exit();
								
                    if (isset($_REQUEST["act_Jazz"]) && $_REQUEST["act_Jazz"]!="") {
                            $cuerpo=$cuerpo.'
                                            <p><span style="font-style: italic">Actuación: </span><span style="font-weight: bold">'.$_REQUEST["actJazz"].'</span></br>								
                            ';	
                    }
                    else{
                            $cuerpo=$cuerpo.'
                                            <p><span style="font-style: italic">Cabecera: </span><span style="font-weight: bold">'.$_REQUEST["cabecera"].'</span></br>								
                            ';
                    }
                    //echo ($cuerpo);exit();
																
                    if (isset($_REQUEST["INCIDENCIA"]) && $_REQUEST["INCIDENCIA"]!=""){
                            $cuerpo=$cuerpo.'
                                            <span style="font-style: italic">Incidencia de instalación: </span><span style="font-weight: bold">'.$_REQUEST["INCIDENCIA"].'</span></br>								
                            ';	
                    }

                    if (isset($_REQUEST["TICKET_OCEANE"]) && $_REQUEST["TICKET_OCEANE"]!=""){
                            $cuerpo=$cuerpo.'
                                            <span style="font-style: italic">TICKET OCEANE: </span><span style="font-weight: bold">'.$_REQUEST["TICKET_OCEANE"].'</span></br>								
                            ';	
                    }	
                    //echo ($cuerpo);exit();
											
                    if (isset($comentarios2) && $comentarios2!="") {
                            switch ($selected_cambioEst) {
                                     case 4:	//cerrada		
                                                            $cuerpo=$cuerpo.'
                                                                            <span style="font-style: italic">Comentarios:</span></br>
                                                                            <span style="color: #103A7D"><pre>'.$comentarios2.'</pre></span></p>								
                                                            ';


                                                            break; 
                                     case 3:	//Pte contestación

                                                            $cuerpo=$cuerpo.'
                                                            </br>
                                                                            Para poder llevar a cabo la actualización solicitada en el aplicativo es necesario:</br>
                                                                            <span style="color: #103A7D"><pre>'.$comentarios2.'</pre></span></p>								
                                                            ';													

                                                            break; 


                                     case 5:	//Pte bloqueo
                                                            $cuerpo=$cuerpo.'
                                                                            <span style="font-style: italic">Comentarios:</span></br>
                                                                            <span style="color: #103A7D"><pre>'.$comentarios2.'</pre></span></p>								
                                                            ';

                                                            break;


                                    case 6:	//Pte desbloqueo
                                                            $cuerpo=$cuerpo.'
                                                                            <span style="font-style: italic">Comentarios:</span></br>
                                                                            <span style="color: #103A7D"><pre>'.$comentarios2.'</pre></span></p>								
                                                            ';
                                                            break;
                                    case 8:	//En construcion
                                                            $cuerpo=$cuerpo.'
                                                                            <span style="font-style: italic">Comentarios:</span></br>
                                                                            <span style="color: #103A7D"><pre>'.$comentarios2.'</pre></span></p>								
                                                            ';
                                                            break;			
                            }	

                    }
																		
                    switch ($selected_cambioEst) {

                                     case 4:	//cerrada		


                                                            $cuerpo=$cuerpo.'

                                                            </br>
                                                            <p>La solicitud ha sido cerrada.</p>	

                                                            </br>

                                                            ';

                                                            break; 


                                     case 3:	//Pte contestación


                                                            $cuerpo=$cuerpo.'	

                                                            </br>

                                                            <p>Quedamos a la espera de vuestra contestación, para ello es necesario introducir en la aplicación web
                                                            el número de referencia '.$ref.' asignado a esta tarea.</p>																								

                                                            </br></br>

                                                            ';

                                                            break; 

                                     case 8:	//En construccion	


                                                            $cuerpo=$cuerpo.'

                                                            </br>

                                                            <p>	La solicitud queda con estado En construccion </p>

                                                            </br>

                                                            ';


                                                            break;



                                     case 9:	//Pendiente de SUC


                                                            $cuerpo=$cuerpo.'

                                                            </br>

                                                            <p>	La solicitud queda con estado Pendiente de SUC </p>

                                                            </br>

                                                            ';


                                                            break;



                                     case 10:	//Pendiente de aprobación Presupuesto


                                                            $cuerpo=$cuerpo.'

                                                            </br>

                                                            <p>	La solicitud queda con estado Pendiente de aprobación Presupuesto </p>

                                                            </br>

                                                            ';


                                                            break;



                                     case 11:	//Pendiente de Ingeniería	


                                                            $cuerpo=$cuerpo.'

                                                            </br>

                                                            <p>	La solicitud queda con estado Pendiente de Ingeniería </p>

                                                            </br>

                                                            ';


                                                            break;


                                     case 5:	//Pte bloqueo

                                                            $cuerpo=$cuerpo.'		

                                                            </br>
                                                            <p>La solicitud queda pendiente de bloqueo.</p>	

                                                            </br>

                                                            ';

                                                            break;


                                    case 6:	//Pte desbloqueo

                                                            $cuerpo=$cuerpo.'		

                                                            </br>
                                                            <p>La solicitud queda pendiente de desbloqueo.</p>	

                                                            </br>

                                                            ';

                                                            break;	
                            }
                            //echo ($selected_cambioEst."-".$cuerpo);exit();

                            if ($nfilas>0){
                                switch ($selected_cambioEst) {
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

                                    $tsql3 = "SELECT tbTareas.REF, tbTareas.COMENTARIOS2, tbTareas.INCIDENCIA ";
                                    $tsql3=$tsql3." FROM INV_TBTAREAS ";	
                                    $tsql3=$tsql3." WHERE id = $marcarProc[$i]";
                                    $stmt3 = sqlsrv_query( $conn, $tsql3);

                                    $rows3 = sqlsrv_has_rows( $stmt3 );

                                    if ($rows3 === true){
                                        while($row3 = sqlsrv_fetch_array($stmt3)){											
                                            if (isset($row3['INCIDENCIA'])){
                                                    $cuerpo=$cuerpo.'
                                                                    <span style="font-style: italic">Incidencia de instalación: </span><span style="font-weight: bold">'.$row3['INCIDENCIA'].'</span></br>								
                                                    ';	
                                            }
                                            if (isset($row3['COMENTARIOS2']) && $row3['COMENTARIOS2']!="") {
                                                    $cuerpo=$cuerpo.'
                                                    <span style="font-style: italic">Comentarios:</span></br>
                                                    <span style="color: #103A7D"><pre>'.$row3['COMENTARIOS2'].'</pre></span></p>';	
                                            }
                                        }
                                    }										
                                }
                                $cuerpo=$cuerpo.'</br>';
                            } //Fin For
                            //echo ($selected_cambioEst."-".$cuerpo);exit();
                            
                            $cuerpo=$cuerpo.'		
                                            <p>Un saludo</p>

                                            <p>
                                                <strong>'.get_nombre($_SESSION['usuario']).'</strong></br>
                                                <span style="font-weight: bold;color:#c45911">Inventario Lógico</span></br>
                                                <span style="font-weight: bold;color:#c45911">Servicios Técnicos</span>
                                            </p>
                                            <p style="color:#8b8b8b">
                                                C/ Campezo, 1 </br>
                                                Parque Empresarial Las Mercedes Edif. 3</br>
                                                28022 Madrid</br>
                                                Tel. fijo: 91.090.0860</br>
                                                <span style="color:#0563c1">'.$_SESSION['mail'].'</span>
                                            </p>
                                    </body>
                                    </html>

                            ';	
                            //FIN CUERPO MENSAJE
                            //var_dump($_SESSION);
                            //echo ($cuerpo);exit();

                            //asunto
                            //cabecera
                            $cabeceraMail='[Inv Lógico] REF: '.$ref;
                            
                            if (isset($_REQUEST["INCIDENCIA"]) && $_REQUEST["INCIDENCIA"]!=""){
                                    $cabeceraMail=$cabeceraMail.' - INC: '.$_REQUEST["INCIDENCIA"];
                            }	
                            if (isset($_REQUEST["TICKET_OCEANE"]) && $_REQUEST["TICKET_OCEANE"]!=""){
                                    $cabeceraMail=$cabeceraMail.' - TICKET OCEANE: '.$_REQUEST["TICKET_OCEANE"];
                            }	

                            if (isset($_REQUEST["act_Jazz"]) && $_REQUEST["act_Jazz"]!="") {
                                    $cabeceraMail=$cabeceraMail.' - ACT: '.$_REQUEST["act_Jazz"];
                            }								
                            else{
                                    $cabeceraMail=$cabeceraMail.' - CAB: '.$_REQUEST["cabecera"];
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
                            
				
                            //ENVIO DE MAIL DESARROLLO
                            /*
                            include_once( 'Classes/pruebas/class.phpmailer.php' ); 
                            $mail = new PHPMailer(); //Creamos un objeto

                            $mailUsu=$email; 	
                            $nombreUsu=$email; 	

                            $mail->IsSMTP();
                            $mail->SMTPAuth = true;
                            $mail->Host = "smtp.gmail.com";
                            $mail->Port =465;
                            $mail->Username = "eurocontroljazztel@gmail.com";
                            $mail->Password = "qawsed1234";
                            $mail->SMTPSecure = 'ssl';
                            $mail->SetFrom("eurocontroljazztel@gmail.com", "Jazztel");


                            $mail->AddAddress( $_SESSION['mail'], $nombreUsu );	

                            $mail->Subject = $cabeceraMail; 	

                            $mail->Body = $cuerpo; 

                            $mail->IsHTML(true);
                            $mail->CharSet = 'UTF-8';

                            $error_envio=false;
                            $error_envio_texto="";
                            if(!$mail->Send()) {
                                $error_envio_texto=$mail->ErrorInfo;
                                $error_envio=true;    
                            };
                            exit();
                            */
                            
                            /** Envio mail producción ***/
                                   
                            $headers = "From: $nombreST <$mailST>\r\n".
                                   "MIME-Version: 1.0" . "\r\n" .
                                   "Content-type: text/html; charset=UTF-8" . "\r\n"; 

                            $destinatarios_mail="$nombreST <$mailST>,$nombreUsu <$mailUsu>,$nombreAnaM <$mailAnaM>";
                            if (isset($mailporActiv) && $mailporActiv!=""){
                                $destinatarios_mail=$destinatarios_mail.",$nombreporActiv <$mailporActiv>";
                            }
                            if (isset($mailporActiv2) && $mailporActiv!=""){
                                $destinatarios_mail=$destinatarios_mail.",$nombreporActiv2 <$mailporActiv2>";
                            }
                            if (isset($mailResponsable) && $mailporActiv!=""){
                                $destinatarios_mail=$destinatarios_mail.",$nombreResponsable <$mailResponsable>";
                            }
                            
                            //Para pruebas en produccion
                            //$destinatarios_mail=",";
                            
                            $ctrl_error_mail="";
                            if(mail($destinatarios_mail,$cabeceraMail,$cuerpo,$headers)){
                                $ctrl_error_mail= "Mail enviado correctamente";
                            } else {                                 
                                $errorMessage = error_get_last()['message'];
                                $ctrl_error_mail="El mail no pudo ser enviado. Error:".$errorMessage;
                            }	
                }
        
		
		//Funcion modificar la tarea
		if (isset($_REQUEST['modificar'])){
                        //echo $comentarios;exit();
			$mensaje = modificarTarea($conn, $id,$comentarios);
		}

		//Funcion subir archivo de la tarea
		if (isset($_REQUEST['subirArchivo'])){
			$mensaje = subirArchivo($conn, $id);
		}		
    }
    
	if ($id != '') {
			
	 	$tsql = "SELECT *
				FROM INV_TBTAREAS
				LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID
				WHERE ID = '$id'";

		$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
	    	die( print_r( sqlsrv_errors(), true));
		} else {
			$registro = sqlsrv_fetch_array($resultado);
		}	

		sqlsrv_free_stmt($resultado);	

		$refAsociada = $registro['REF_ASOCIADA']; 
		$ref =  $registro['REF'];
		$id_Actuacion = $registro['ID_ACTUACION'];
		$Cod_Cabecera = $registro['ID_CABECERA'];
		$cabecera = $registro['CABECERA'];
		$act_jazztel = $registro['ACT_JAZZTEL'];
		$act_tesa = $registro['ACT_TESA'];
		$id_gd = $registro['ACT_ID_GD'];
		$id_fdtt = $registro['ACT_ID_FDTT'];
		$huella = $registro['HUELLA'];							
	    $id_actividad = $registro['id_actividad'];

	    if (isset($registro['id_Subactividad'])) {
	    	$subactividad = $registro['id_Subactividad'];
	    }
	    
		$provincia = $registro['PROVINCIA'];
		$region = $registro['REGION'];
		$gestor = $registro['GESTOR'];
		$ticket_remedy = $registro['INCIDENCIA'];
		$ticket_oceane = $registro['TICKET_OCEANE'];
		$ticket_escalado = $registro['ESCALADO'];
		$ticket_tp = $registro['TP'];	
		$eemm = $registro['EEMM'];
		$grupo = $registro['GRUPO'];
		$id_usuorigen = $registro['idUsuOrigen'];

		$prioridad = $registro['PRIORIDAD'];

		$fecha_inicio = $registro['FECHA_INICIO'];
		$fecha_resol = $registro['FECHA_RESOL'];
		$fecha_registro = $registro['FECHA_REGISTRO'];

		$grupo_escalado = $registro['GRUPO_ESCALADO'];	
		$tecnico = $registro['TECNICO'];
		$id_tecnico = $registro['idTecn'];	

		$id_mapeo = $registro['ID_MAPEO'];
		$id_tipo_entrada = $registro['ID_TIPO_ENTRADA'];
		$tipologia_inicial = $registro['TIPOLOGIA_INICIAL'];
		$tipo_incidencia = $registro['TIPO_INCIDENCIA'];
		$tipo_cliente = $registro['TIPO_CLIENTE'];		

		$comentarios = $registro['COMENTARIOS'];	
		$comentarios2 = $registro['COMENTARIOS2'];     	

		$cto_nueva = $registro['CTO_NUEVA'];

		if ($id_actividad == '4') {
			$tsqlMotivBloq = "SELECT TOP 1 id_Motivo
					FROM INV_tbTareas_Bloqueos
					WHERE id_Tarea ='$id' AND Tipo_Afectacion = '1'";
			$resultMotiv = sqlsrv_query($conn, $tsqlMotivBloq);	
			$registroMotiv = sqlsrv_fetch_array($resultMotiv);
			$motivoBloq=$registroMotiv['id_Motivo'];
		}
			

		$tsql = "SELECT *
				FROM INV_TBTAREAS AS A
				LEFT JOIN INV_VIEW_DATOS_TODO AS B ON B.ID_TAREA = A.ID";


		if ($registro['REF_ASOCIADA'] != '') {
			if ($registro['INCIDENCIA'] != '') {
				$tsql = $tsql."	WHERE (A.REF_ASOCIADA = '".$registro['REF_ASOCIADA']."' OR A.INCIDENCIA = '".$registro['INCIDENCIA']."') 
						AND A.ID <> '$id' 
						ORDER BY A.FECHA_REGISTRO, A.FECHA_INICIO, A.FECHA_RESOL";
			} else {
				$tsql = $tsql."	WHERE A.REF_ASOCIADA = '".$registro['REF_ASOCIADA']."' 
						AND A.ID <> '$id' 
						ORDER BY A.FECHA_REGISTRO, A.FECHA_INICIO, A.FECHA_RESOL";
			}
		} else {
			if ($registro['INCIDENCIA'] != '') {
				$tsql = $tsql."	WHERE A.INCIDENCIA = '".$registro['INCIDENCIA']."' 
						AND A.ID <> '$id' 
						ORDER BY A.FECHA_REGISTRO, A.FECHA_INICIO, A.FECHA_RESOL";			
			} else {
				$tsql = $tsql."	WHERE A.ID <> '$id' 
						ORDER BY A.FECHA_REGISTRO, A.FECHA_INICIO, A.FECHA_RESOL";					
			}
		}

		$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
	    	die( print_r( sqlsrv_errors(), true));
		}	

	} else {
		die( print_r('TAREA NO INFORMADA', true));
	}



 

	// print the page header
	print_theme_header();


?>
			<!-- start: Content -->
		<div id="content" class="span12">
			
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="index.html">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Modificar</a></li>
			</ul>

			<!--FORMULARIO-->
			<form method="post" action="modificarTarea.php" role="form" enctype="multipart/form-data">
				<fieldset>    
				<!--DETALLE TAREA-->

				<!-- DATOS DE CABECERA DE LA TAREA -->
				<div style="padding-left:5px;" class="row-fluid yellow">
					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>PROVINCIA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="PROVINCIA" value="<?php echo $provincia;?>">
							</div>
						</div>
																
		
					</div>			
					<div class="span2" ontablet="span4" ondesktop="span2">

						<div class="control-group form-group">
							<div class="controls">
								<strong>REGIÓN: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="REGION" value="<?php echo $region;?>">
							</div>
						</div>					
																	
		
					</div>		

					<div class="span2" ontablet="span4" ondesktop="span2">

						<div class="control-group form-group">
							<div class="controls">
								<strong>GESTOR: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="GESTOR" value="<?php echo $gestor;?>">
							</div>
						</div>																		
		
					</div>		

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								
								<strong>USUORIGEN: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="USUORIGEN" value="<?php echo get_nombreFromId($id_usuorigen);?>">
								<?php echo '<input readonly="true" class="hidden" name="ID_USUORIGEN" value="'.$id_usuorigen.'">';?>

							</div>
						</div>											
				
					</div>					

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>GRUPO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="GRUPO" value="<?php echo $grupo;?>">
							</div>
						</div>											
				
					</div>							

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>GRUPO_ESCALADO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="grupo_escalado" value="<?php echo $grupo_escalado;?>">
							</div>
						</div>	
					</div>		


				</div>


				<div style="padding-left:5px;" class="row-fluid yellow">
						
					<div class="control-group form-group span2">
						<div class="controls">						
							<strong>CABECERA: <br /></strong>	
								<input type="text" readonly id="cabecera" name="cabecera" value="<?php echo $cabecera;?>"/>
								<input class="hidden" id="codCab" name="codCab" value="<?php echo $Cod_Cabecera;?>"/>

							<div id="resultadoCabecera">
							</div>	
						</div>	
					</div>

					<div class="control-group form-group span2">
						<div class="controls">
							<strong>ACT. JAZZTEL: <br /></strong>
								<input type="text" readonly id="actJazz" name="actJazz" value="<?php echo $act_jazztel;?>"/>
							<div id="resultadoActuacionJazz">
							</div>										
						</div>	
					</div>	

					<div class="control-group form-group span2">
						<div class="controls">
							<strong>ACT. TESA: <br /></strong>
								<input type="text" readonly id="actTesa" name="actTesa" value="<?php echo $act_tesa;?>"/>
							<div id="resultadoActuacionTesa">
							</div>										
						</div>	
					</div>	

					<div class="control-group form-group span2">
						<div class="controls">
							<strong>ID ACTUACIÓN: <br /></strong>
								<input type="text" readonly id="idAct" name="idAct" value="<?php echo $id_Actuacion;?>"/>
							<div id="resultadoIdActuacion">
							</div>										
						</div>	
					</div>	


					<div class="control-group form-group span2">
						
						<div class="controls">
							<strong>ID_FDTT: <br /></strong>
								<input type="text" readonly id="idFDTT" name="idFDTT" value="<?php echo $id_fdtt;?>"/>
							<div id="resultadoIdFDTT">
							</div>										
						</div>	
					</div>	
						
					<div class="control-group form-group span2">
						<div class="controls">
							<strong>TÉCNICO: </strong><br><?php echo '<input readonly="true" type="text" class="form-control input uneditable-input" name="tecnico" value="'.(($tecnico == '')?get_nombre($_SESSION['usuario']):$tecnico).'">';?>
						</div>
					</div>			
					<div class="control-group form-group">
						<div class="controls">
							<input readonly id="idGD" class="hidden" name="idGD" value="<?php echo $id_gd;?>"/>
							<?php echo '<input readoly="true" class="hidden" name="id_tecnico" value="'.(($tecnico == '')?get_idUsu($_SESSION['usuario']):get_idFromNombre($tecnico)).'">';?>
						</div>
					</div>		

	

				</div>	
				<div style="padding-left:5px;" class="row-fluid yellow">


					<div class="control-group form-group span2">
						<div class="controls">
							<strong>REFERENCIA ASOCIADA: <br /></strong>
								<input type="text" readonly id="refAsoc" name="refAsoc" value="<?php echo $refAsociada;?>" />
							<div id="resultadoRefAsociada">
							</div>										
						</div>	
					</div>	

					<div class="control-group form-group span2">
						<div class="controls">
							<strong>HUELLA: <br /></strong>
								<input type="text" readonly id="huella" name="huella" value="<?php echo $huella;?>" />
						</div>	
					</div>	

					<div class="control-group form-group span2">
						<div class="controls">
							<strong>REF: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="ref" value="<?php echo $ref;?>">
						</div>
					</div>	

					<div class="control-group form-group span2">
						<div class="controls">
							<strong>ID: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="id" value="<?php echo $id;?>">
						</div>
					</div>										
			
					<div class="control-group form-group span2">
						<div class="controls">
							<strong>PRIORIDAD: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="prioridad" value="<?php echo $prioridad;?>">
						</div>
					</div>		

					<div class="control-group form-group span2">
						<div class="controls">
							<strong>EEMM: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="EEMM" value="<?php echo $eemm;?>">
							
						</div>	
					</div>									

				</div>			

				<div style="padding-left:5px;" class="row-fluid yellow">
					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>ID_MAPEO: </strong><br>
								
								<a title="Detalle datos ID_MAPEO" class="btn btn-mini buscar_mapeo" data-toggle="modal" data-target="#viewModalMP" data-id="<?php echo $id_mapeo; ?>">		
									<i class="halflings-icon white eye-open"></i>  
								</a>
								<input readonly="true" type="text" class="form-control input uneditable-input" name="id_mapeo" value="<?php echo $id_mapeo;?>">
							</div>
						</div>
																
		
					</div>			
					<div class="span2" ontablet="span4" ondesktop="span2">

						<div class="control-group form-group">
							<div class="controls">
								<strong>ID_TIPO_ENTRADA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="id_tipo_entrada" value="<?php echo $id_tipo_entrada;?>">
							</div>
						</div>					
																	
		
					</div>		


					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								
								<strong>TIPOLOGIA_INICIAL: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="tipologia_inicial" value="<?php echo $tipologia_inicial;?>">
								
							</div>
						</div>											
				
					</div>					

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>TIPO_INCIDENCIA: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="tipo_incidencia" value="<?php echo $tipo_incidencia;?>">
							</div>
						</div>											
				
					</div>							

					<div class="span2" ontablet="span4" ondesktop="span2">
						<div class="control-group form-group">
							<div class="controls">
								<strong>TIPO_CLIENTE: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="tipo_cliente" value="<?php echo $tipo_cliente;?>">
							</div>
						</div>	
					</div>		


				</div>

				<!-- FIN DATOS DE CABECERA DE LA TAREA -->
                                    
                                <?php if ($ctrl_error_mail!="") {?>
                                    <div class="alert alert-block">
                                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                                    <h4 class="alert-heading"></h4>
                                                    <?php echo "<p>".$ctrl_error_mail."</p>";?>
                                    </div>	
                                <?php } ?>
                                
				<!-- DATOS DEL DETALLE DE LA TAREA -->

				<div class="row-fluid" style="margin-bottom:10px;">

					<div class="box-header">

						<h2><i class="halflings-icon list"></i><span class="break"></span>Detalle tarea</h2><a style="margin-left:20px;" title="Historia Tarea" class="btn btn-mini btn-primary buscar_historia" data-toggle="modal" data-target="#historiaModal" data-id="<?php echo $id; ?>"><i class="halflings-icon white eye-open"></i></a>

					</div>					
					
				</div>
				<div class="row-fluid">

					<div class="span4">

						<div class="control-group">

							<label class="control-label" for="actividad"><strong>ACTIVIDAD: </strong><br> </label>
							<div class="controls">
								<?php
													
									$tsql="SELECT id_actividad, ACTIVIDAD from INV_tbActividad WHERE FECHA_VIGENCIA IS NULL AND ACTIVIDAD !='' ORDER BY id_actividad";
									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta");}
								
									$rows = sqlsrv_has_rows( $stmt );
								
									if ($rows === true){
										
										echo '<SELECT class="span6" id="activ"  name="id_actividad"  onChange="ListadoSubactividad(\'consultas.php?dato=SUBACTIVIDAD\', this.value); return false">';		

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
										if ($id_actividad != ""){ 		

											if ($motivoBloq != '') {

												$tsql="SELECT DISTINCT id_actividad, id_Subactividad, Descripcion from INV_tbSubactividad where FECHA_VIGENCIA IS NULL AND id_Actividad ='".$id_actividad."' ORDER BY id_Subactividad";
												$stmt = sqlsrv_query( $conn, $tsql);
											
												if( $stmt === false ){die ("Error al ejecutar consulta");}
											
												$rows = sqlsrv_has_rows( $stmt );
											
												if ($rows === true){

													echo '<div class="hidden">';

													print ('<label class="control-label" for="subactividad"><strong>SUBACTIVIDAD: </strong><br> </label>');
													
													echo '<SELECT class="span6" id="subactividad"  name="subactividad"  onChange="ListadoCtos(this.value); return false">';	

													echo '<option value=""></option>';		
													
													while($row = sqlsrv_fetch_array($stmt)){
														echo "string: ".$id_actividad;
														
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

												print ('<label class="control-label" for="motivoBloq"><strong>MOTIVO DE BLOQUEO: </strong><br></label>');
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
											print ('<label class="control-label" for="subactividad"><strong>SUBACTIVIDAD: </strong><br> </label>');
											echo '<SELECT class="span6" id="subactividad"  name="subactividad">';		
											echo '<option value=""></option>';	
											echo '</SELECT>';								
										}


										
													
									?>
						<div class="control-group form-group">
							<div class="controls">
								<strong>ESCALADO: </strong><br><input type="text" class="form-control input" name="ESCALADO" value="<?php echo $ticket_escalado;?>">
							</div>
						</div>	

								</div>	
							</div>
						</div>		
						<div class="control-group">
							<div class="controls">
								<div id="ctos_gescales" class="hidden" >
								<?php
									if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['modificar']) && isset($_POST['CTOgesc'])) {  
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
					
					<div class="span3">
						<div class="control-group form-group">
							<div class="controls">
								<strong>TICKET REMEDY: </strong><br><input type="text" class="form-control input" name="INCIDENCIA" value="<?php echo $ticket_remedy;?>">
							</div>
						</div>
						<div class="control-group form-group">
							<div class="controls">
								<strong>TICKET_OCEANE: </strong><br><input type="text" class="form-control input" name="TICKET_OCEANE" value="<?php echo $ticket_oceane;?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>TP: </strong><br><input type="text" class="form-control input" name="TP" value="<?php echo $ticket_tp;?>">
							</div>
						</div>	

						<div class="control-group form-group">
							<div class="controls">
								<strong>CTO_NUEVA: </strong><br><input type="text" class="form-control input" name="CTO_NUEVA" value="<?php echo $cto_nueva;?>">
							</div>
						</div>									
						
					</div>



					<div id="botonCTOS"  class="span2">
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
														<div id="ctos_lateral" >
														<?php
															//if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['modificar']) && isset($_POST['MARCAR']) && isset($_POST['CTOgesc'])) {  
															if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['modificar'])) {  
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

																if (isset($_POST['modificar']) && isset($_POST['MARCAR'])) {

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

						  								?>
						  							</div>
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
						<?php if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['modificar']) && isset($_POST['MARCAR']) && ($bloqueo || $desbloqueo)) {?>
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
															if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['modificar']) && isset($_POST['MARCAR']) && isset($_POST['CTOgesc']) && $_POST['CTOgesc'] == 'GESC') {  
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

																	if (isset($_POST['modificar']) && isset($_POST['MARCARGESC'])) {

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

					<div class="span2">

						<div class="control-group form-group">
							<div class="controls">
								<strong>FECHA_INICIO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_INICIO" value="<?php echo  date_format($registro['FECHA_INICIO'], 'Y-m-d H:i:s'); ?>">
							</div>
						</div>				
						<div class="control-group form-group">
							<div class="controls">
								<strong>FECHA_RESOL: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_RESOL" value="<?php echo  date_format($registro['FECHA_RESOL'], 'Y-m-d H:i:s'); ?>">
							</div>
						</div>	
						<div class="control-group form-group">
							<div class="controls">
								<strong>FECHA_REGISTRO: </strong><br><input readonly="true" type="text" class="form-control input uneditable-input" name="FECHA_REGISTRO" value="<?php echo  date_format($registro['FECHA_REGISTRO'], 'Y-m-d H:i:s'); ?>">
							</div>	
						</div>														

					</div>


				</div>	
				<div class="row-fluid">

					<div class="form-group span8">
							<strong >COMENTARIOS: </strong><br>
							<textarea rows="5" name="COMENTARIOS" class="form-control" name="COMENTARIOS"><?php echo $comentarios;?></textarea>
					
					</div>

				</div>	
				<div class="row-fluid">

					<div class="form-group span8">
							<strong >COMENTARIOS2: </strong><br>
							<textarea rows="5" name="COMENTARIOS2" class="form-control" name="COMENTARIOS2"><?php echo $comentarios2;?></textarea>
					
					</div>

				</div>					

				<div class="row-fluid">
					<div class="form-group span1">
							<?php

								$tsqlArchivo="SELECT archivo from INV_TBARCHIVOS WHERE idTarea='".$id."'";
						
								$stmtArchivo = sqlsrv_query( $conn, $tsqlArchivo);

								while($rowArchivo = sqlsrv_fetch_array($stmtArchivo)){	
									$nombreArchivo= $rowArchivo["archivo"]; 
								}
								sqlsrv_free_stmt( $stmtArchivo);

						
								if (isset($nombreArchivo)){
									echo "<a class='btn btn-small' href='upload/".$nombreArchivo."'><i class='halflings-icon white paperclip'></i><span>  Archivo Anexo</span></a>";									
								}				
							?>
					</div>




				






					<div class="form-group span2">
						<div class="control-group form-group">	
							<div class="controls">
								<input type="file" name="adjunto" id="adjunto" />
							</div>	
						</div>	
					</div>		



					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								<button onclick="return confirmarSubir();" type="submit" name="subirArchivo" value="subirArchivo" class="btn btn-info btn-small subirArchivo"><i class="halflings-icon white upload"></i> Subir Archivo</button>
							</div>	
						</div>	
					</div>		

						<div class="control-group form-group span2">
							<div class="controls">
								<?php

									//id del estado de la tarea
									$tsql="select id_estado from inv_tbestados where estado='".$registro['Estado']."'";
									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta: ".$tsql);}

									$id_estado_Tarea = sqlsrv_fetch_array($stmt);

									//tabla y array de estados de estados
									$tsql="select id_estado, estado from inv_tbestados order by id_estado";
									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta: ".$tsql);}

									//array de estados

									$array_estados = array();

									while($row_estados = sqlsrv_fetch_array($stmt)){
										$array_estados[$row_estados['id_estado']] = $row_estados['estado'];
									}

									//tabla de estados posibles desde el estado actual
									
									$tsql="select DISTINCT id_estado_fin
											from INV_tbMotor_estados
											where id_estado_ini = '".$id_estado_Tarea['id_estado']."'";

									$stmt = sqlsrv_query( $conn, $tsql);
								
									if( $stmt === false ){die ("Error al ejecutar consulta: ".$tsql);}

									echo '<SELECT tabindex="2" id="ESTADO"  name="ESTADO" >';	

									echo '<option class="form-control input" value="'.$id_estado_Tarea['id_estado'].'-'.$registro['Estado'].'" selected="selected" >'.$registro['Estado'].'</option>';

									while($row = sqlsrv_fetch_array($stmt)){
										
										echo '<option class="form-control input"  value="'.$row['id_estado_fin'].'-'.$array_estados[$row['id_estado_fin']].'" >'.$array_estados[$row['id_estado_fin']].'</option>';

									}
									
									echo '</SELECT>';			
								?>
							</div>	
						</div>							

					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								<button type="submit" name="cambiarEstado" value="cambiarEstado" class="btn btn-danger btn-small cambiarEstado" onclick="return confirmarAccion();"><i class="halflings-icon white play"></i> Cambiar Estado</button>
							</div>	
						</div>	
					</div>			

					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								<button type="submit" name="modificar" value="modificar" class="btn btn-danger btn-small modificar" onclick="return confirmarAccion();"><i class="halflings-icon white play"></i> Modificar</button>
							</div>	
						</div>	
					</div>		
                                        
                                        <div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
                                                            <input name="envioMail" type="image" width="40" height="30" src="images/mail.jpg" title="Envío tarea cerrada" onclick="$('#envio_mail').val('enviar');return confirmarAccion();">
                                                            <input type='hidden' id='envio_mail' name='envio_mail' value='' />
                                                        </div>	
						</div>	
					</div>		

					<div class="form-group span1">
						<div class="control-group form-group">	
							<div class="controls">
								
									<button type="button" name="back" value="back" onClick="history.go(-1);return true;" class="btn btn-primary btn-small back" style="vertical-align:bottom;"><i class="halflings-icon white repeat"></i> Volver</button>
								
							</div>	
						</div>	
					</div>	



				</div>	


				<!-- FIN DATOS DE DETALLE DE LA TAREA -->

				



				<div class="row-fluid">
					<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<?php echo $mensaje;?>
					</div>					
				</div>						

				</fieldset>  

		    	<!--LISTADO DE TAREAS ASOCIADAS-->
		    	<div class="row-fluid">
					<div class="box span12">
						<div class="box-header" data-original-title>
							<h2><i class="halflings-icon user"></i><span class="break"></span>Listado Tareas Asociadas</h2>
						</div> 	

						<div class="box-content">
							<table class="table table-striped table-bordered bootstrap-datatable datatable">
							  <thead>
								  <tr>
								  	  <th>ID_TAREA</th>
									  <th>REMEDY</th>
									  <th>REFERENCIA</th>
									  <th>OCEAN</th>
									  <th>ESCALADO</th>
		                              <th>ACTIVIDAD</th>
									  <th>SUBACTIVIDAD</th>
									  <th>SOLICITANTE</th>
									  <th>TÉCNICO</th>
									  <th>F.REGIS.</th>
									  <th>F.INIC.</th>
									  <th>F.RESOL.</th>
									  <th>ESTADO</th>		
									  <th>P</th>
									  <th>TRANSACCIÓN</th>
								  </tr>
							  </thead>   
							  <tbody>

		                         <?php while ($lineaAsoc = sqlsrv_fetch_array($resultado)){ ?>
										<td class="center"><?php echo $lineaAsoc['ID_TAREA']; ?></td>
										<td class="center"><?php echo $lineaAsoc['REMEDY']; ?></td>
										<td class="center"><?php echo $lineaAsoc['REF_TBTAREA']; ?></td>
										<td class="center"><?php echo $lineaAsoc['OCEANE_TBTAREA']; ?></td>
										<td class="center"><?php echo $lineaAsoc['ESCALADO_TBTAREA']; ?></td>
										<td class="center"><?php echo $lineaAsoc['ACTIVIDAD']; ?></td>
										<td class="center"><?php echo $lineaAsoc['SUBACTIVIDAD']; ?></td>
										<td class="center"><?php echo $lineaAsoc['USUORIGEN']; ?></td>
										<td class="center"><?php echo $lineaAsoc['TECNICO']; ?></td>
										<td class="center"><?php echo date_format($lineaAsoc['FECHA_REGISTRO'], 'Y-m-d H:i:s'); ?></td>
										<td class="center"><?php echo date_format($lineaAsoc['FECHA_INICIO'], 'Y-m-d H:i:s'); ?></td>
										<td class="center"><?php echo date_format($lineaAsoc['FECHA_RESOL'], 'Y-m-d H:i:s'); ?></td>
										<td class="center"><?php echo $lineaAsoc['Estado']; ?></td>
										<td class="center"><?php echo $lineaAsoc['PRIORIDAD']; ?></td>
										<td class="center">
											<a title="Gestionar" class="btn btn-danger btn-mini gestionar_tarea" href="<?php echo 'modificarTarea.php?id='.$lineaAsoc['ID_TAREA']; ?>">		
												<i class="halflings-icon white edit"></i> 										
											</a>
										</td>										
		                            </tr>
									<?php }
										sqlsrv_free_stmt($resultado);							
										sqlsrv_close($conn);	
									?>

							  </tbody>
							</table>
						</div>	
					</div>	
				</div>

			</form>    
			<!--FIN FILTROS-->  

 	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->
		
<div class="modal hide fade" id="viewModalMP" data-backdrop="static" data-keyboard="false" >
	<div class="modal-header btn-info">
		<button type="button" id="cerrarConsulta" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar</h2>
	</div>
    <div class="ct" style="height:80%;">
  
    </div>
</div>	

<div class="clearfix"></div>	

<div class="modal hide fade" id="historiaModal" data-backdrop="static" data-keyboard="false" >
	<div class="modal-header btn-info">
		<button type="button" id="cerrarConsulta" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar Historia</h2>
	</div>
    <div class="ct" style="height:80%;">
  
    </div>
</div>	

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
			
			//ListadoSubactividad('consultas.php?dato=SUBACTIVIDAD', $('#id_actividad').val());
			ListadoCtos($('#subactividad').val());

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