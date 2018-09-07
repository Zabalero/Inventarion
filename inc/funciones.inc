<?php
// Establece una conexión con la base de datos
function conectar_bd(){
	/* Nombre del servidor. */
	$serverName = "172.29.0.80";
      //$serverName = "192.168.31.50";

	/* Usuario y clave.  */
	$uid = "DST_ADM";
        //$uid = "desarrollo";
	$pwd = "reparil2014";
      //$pwd = "desarrollo2016";

	/* Array asociativo con la información de la conexion */
	$connectionInfo = array( "UID"=>$uid,
	"PWD"=>$pwd,
	"Database"=>"INVENTARIO", "CharacterSet" => "UTF-8");
	 
	/* Nos conectamos mediante la autenticación de SQL Server . */
	$conn = sqlsrv_connect( $serverName, $connectionInfo);
	if( $conn === false ){
		//die ("No se puede conectar con el servidor");
		die( print_r( sqlsrv_errors(), true));  
	}

	//Devolvemos el enlace a la conexión
	return $conn;
}


//Comprueba si la combinación usuario-contraseña es correcta
function es_usuario($usuario,$password){
    if ($usuario!="") {
	//Se conecta a la base de datos
	$conn=conectar_bd();
	//Busca al usuario y su contraseña en la tabla usuarios
	$tsql="SELECT * FROM INV_tbUsuarios WHERE usuario='".$usuario."'AND
	password='".$password."' and inhabilitado=0";

	//Obtiene el resultado
	$stmt = sqlsrv_query( $conn, $tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta".$tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );
        
        $registro = sqlsrv_fetch_array($stmt);
        $_SESSION['mail']=$registro["mail"];

	/* Cerramos la conexión */
	sqlsrv_free_stmt( $stmt);
	sqlsrv_close( $conn);

        

        if ($rows === true)               
               return true;
        else 
               return false;
    } else {
        return false;
    }
}




//Devuelve el rol de usuario pasado como parámetro
function get_rol($usuario){

	//Se conecta a la base de datos
	$conn=conectar_bd();
	//Busca al usuario y su contraseña en la tabla usuarios
	$tsql="SELECT INV_tbRoles.rol FROM INV_tbUsuarios INNER JOIN INV_tbRoles on INV_tbUsuarios.idRol=INV_tbRoles.id_Rol WHERE INV_tbUsuarios.usuario='".$usuario."'";

	//Obtiene el resultado
	$stmt = sqlsrv_query( $conn, $tsql)	
			 or die ('Error en consulta de roles');
/*
	if( $stmt === false ){
		die ("Error al ejecutar consulta");
	}
*/
	/* Mostramos el resultado. */
	//(una fila)
	while($row = sqlsrv_fetch_array($stmt)){	
		$rol= $row["rol"]; 
	}

	/* Cerramos la conexión */
	sqlsrv_free_stmt( $stmt);
	sqlsrv_close( $conn);

	return $rol;

}




//Devuelve el id de una tabla pasando como parámetro cualquier campo
function get_id($tabla, $campoOrigen, $valor, $campoDestino){
//Se conecta a la base de datos
$conn=conectar_bd();
//Busca al usuario en la tabla roles
$tsql="SELECT ".$campoDestino." FROM ".$tabla." WHERE ".$campoOrigen."='".$valor."'";
	//Obtiene el resultado
	$stmt = sqlsrv_query( $conn, $tsql);

	if( $stmt === false ){
		die ("Error al ejecutarr consulta");
	}

	/* Mostramos el resultado. */
	//(una fila)
	while($row = sqlsrv_fetch_array($stmt)){	
		$id= $row[$campoDestino]; 
	}

	/* Cerramos la conexión */
	sqlsrv_free_stmt( $stmt);
	sqlsrv_close( $conn);

	return $id;
}




//Devuelve el nombre de usuario pasado como parámetro a partir del usuario
function get_idUsu($usuario){
//Se conecta a la base de datos
$conn=conectar_bd();
//Busca al usuario en la tabla roles
$tsql="SELECT id_usu FROM INV_tbUsuarios WHERE usuario='".$usuario."'";
//echo $tsql;exit();
$stmt = sqlsrv_query( $conn, $tsql);
$row = sqlsrv_fetch_array($stmt);
$id_usu= $row["id_usu"]; 
sqlsrv_free_stmt( $stmt);
sqlsrv_close( $conn);
return $id_usu;
}




//Devuelve el nombre de usuario pasado como parámetro a partir del id del usuario
function get_nombreFromId($usuario){
//Se conecta a la base de datos
$conn=conectar_bd();
//Busca al usuario en la tabla roles
$tsql="SELECT nombre FROM INV_tbUsuarios WHERE id_usu='".$usuario."'";
//Obtiene el resultado
$stmt = sqlsrv_query( $conn, $tsql);
$row = sqlsrv_fetch_array($stmt);
$nombre= $row["nombre"]; 
sqlsrv_free_stmt( $stmt);
sqlsrv_close( $conn);
return $nombre;
}


function get_idFromNombre($usuario){
//Se conecta a la base de datos
$conn=conectar_bd();
//Busca al usuario en la tabla roles
$tsql="SELECT id_usu FROM INV_tbUsuarios WHERE nombre='".$usuario."'";
//Obtiene el resultado
$stmt = sqlsrv_query( $conn, $tsql);
$row = sqlsrv_fetch_array($stmt);
$id_usu= $row["id_usu"]; 
sqlsrv_free_stmt( $stmt);
sqlsrv_close( $conn);
return $id_usu;
}


//Devuelve el nombre de usuario pasado como parámetro a partir del usuario
function get_nombre($usuario){


//Se conecta a la base de datos
	$conn=conectar_bd();
	//Busca al usuario y su contraseña en la tabla usuarios
	$tsql="SELECT nombre FROM INV_tbUsuarios WHERE usuario='".$usuario."'";

	//Obtiene el resultado
	$stmt = sqlsrv_query( $conn, $tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta");
	}

	/* Mostramos el resultado. */
	//(una fila)
	while($row = sqlsrv_fetch_array($stmt)){	
		$nombre= $row["nombre"]; 
	}

	/* Cerramos la conexión */
	sqlsrv_free_stmt( $stmt);
	sqlsrv_close( $conn);

	return $nombre;
}


//Devuelve la restricción de consulta de usuario pasado como parámetro
function get_restriccion($usuario){

	//Se conecta a la base de datos
	$conn=conectar_bd();
	//Busca al usuario y su contraseña en la tabla usuarios
	$tsql="SELECT restricciones FROM INV_tbUsuarios WHERE usuario='".$usuario."'";

	//Obtiene el resultado
	$stmt = sqlsrv_query( $conn, $tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta");
	}

	/* Mostramos el resultado. */
	//(una fila)
	while($row = sqlsrv_fetch_array($stmt)){	
		$rol= $row["restricciones"]; 
	}

	/* Cerramos la conexión */
	sqlsrv_free_stmt( $stmt);
	sqlsrv_close( $conn);

	return $rol;

}





function lastId($queryID) {
			 sqlsrv_next_result($queryID);
			 sqlsrv_fetch($queryID);
			 return sqlsrv_get_field($queryID, 0);
}






function sumaDias($fecha,$dias){
$nuevafecha = strtotime ( $dias." day" , strtotime ( $fecha ) ); 
$nuevafecha = date ( 'Y-m-d' , $nuevafecha ); //formatea nueva fecha 
return $nuevafecha; //retorna valor de la fecha 
}




//Convierte fecha de español a americano

function sqlDate($fecha){
   //Reformat date 
	$sqlDate = date('Y-m-d', strtotime($fecha));	
	return $sqlDate;
}




function replace_specials_characters($s) {
		//$s = mb_convert_encoding($s, 'UTF-8','');
		$s = preg_replace("/á|à|â|ã|ª/","a",$s);
		$s = preg_replace("/Á|À|Â|Ã/","A",$s); 
		$s = preg_replace("/é|è|ê/","e",$s);
		$s = preg_replace("/É|È|Ê/","E",$s);
		$s = preg_replace("/í|ì|î/","i",$s);
		$s = preg_replace("/Í|Ì|Î/","I",$s);
		$s = preg_replace("/ó|ò|ô|õ|º/","o",$s);
		$s = preg_replace("/Ó|Ò|Ô|Õ/","O",$s);
		$s = preg_replace("/ú|ù|û/","u",$s);
		$s = preg_replace("/Ú|Ù|Û/","U",$s);
		$s = str_replace(" ","_",$s);
		$s = str_replace("ñ","n",$s);
		$s = str_replace("Ñ","N",$s);

		$s = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $s);
		return $s;
	}

function htmlToSql($fecha) {
	$separoFecha = explode('-', $fecha);
	$separoTiempo = explode('T', $separoFecha[2]);
	$fechaTiempoSql = $separoFecha[0].'-'.$separoFecha[1].'-'.$separoTiempo[0].' '.$separoTiempo[1];
	if (strlen($fechaTiempoSql) == 3) {
		$fechaTiempoSql = NULL;
	}
	return $fechaTiempoSql;
}

function sqlToHtml($fecha){
	$separoFecha = explode(' ', $fecha);
		if (!empty(substr($separoFecha[1], 1,4))) {
			$fechaTiempoHtml = $separoFecha[0].'T'.substr($separoFecha[1], 1,4);
		} else {
			$fechaTiempoHtml = $separoFecha[0].'T00:00';
		}
		return $fechaTiempoHtml;
}

function replaceSpChar($s) {
	//$s = mb_convert_encoding($s, 'UTF-8','');
	$s = preg_replace("/á|à|â|ã|ª/","a",$s);
	$s = preg_replace("/Á|À|Â|Ã/","A",$s); 
	$s = preg_replace("/é|è|ê/","e",$s);
	$s = preg_replace("/É|È|Ê/","E",$s);
	$s = preg_replace("/í|ì|î/","i",$s);
	$s = preg_replace("/Í|Ì|Î/","I",$s);
	$s = preg_replace("/ó|ò|ô|õ|º/","o",$s);
	$s = preg_replace("/Ó|Ò|Ô|Õ/","O",$s);
	$s = preg_replace("/ú|ù|û/","u",$s);
	$s = preg_replace("/Ú|Ù|Û/","U",$s);
	//$s = str_replace(" ","_",$s);
	$s = str_replace("ñ","n",$s);
	$s = str_replace("Ñ","N",$s);
	$s = preg_replace('/[^a-zA-Z0-9_\.-]/', ' ', $s);
	return $s;
}

function detallesActividadSubactividad ($id_actividad, $subactividad, &$prioridad, &$bloqueo, &$desbloqueo, &$tratamientoCTOS) {

	$bloqueo = NULL;
	$desbloqueo = NULL;
	$prioridad = NULL;
	$tratamientoCTOS = NULL;
	$resultado = '';

	$conn=conectar_bd();
	
	//Busca las características de la subactividad
	$tsql="SELECT PRIORIDAD, CTOS FROM INV_tbSubactividad WHERE id_Subactividad='".$subactividad."' ";

	//Obtiene el resultado
	$stmt = sqlsrv_query( $conn, $tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta".$tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );

	if ($rows === true) {

		$row = sqlsrv_fetch_array($stmt);
		$prioridad = $row['PRIORIDAD'];
		$tratamientoCTOS = $row['CTOS'];


	} else {
		$resultado = "Subactividad no existente";
	}

	if ($resultado == '') {
		if ($prioridad == NULL || $tratamientoCTOS == NULL || $bloqueo == NULL || $desbloqueo == NULL) {
			//Se le asignan las características de la actividad en general
			$tsql="SELECT PRIORIDAD, CTOS, BLOQUEO, DESBLOQUEO FROM INV_tbActividad WHERE id_actividad='".$id_actividad."' ";

			//Obtiene el resultado
			$stmt = sqlsrv_query( $conn, $tsql);

			if( $stmt === false ){
				die ("Error al ejecutar consulta".$tsql);
			}

			$rows = sqlsrv_has_rows( $stmt );

			if ($rows === true) {
			 	$row = sqlsrv_fetch_array($stmt);

				if ($prioridad == NULL) {
					$prioridad = $row['PRIORIDAD'];
				}

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
				$resultado = "Actividad no existente";
			}
	  
		}

	}

	sqlsrv_free_stmt( $stmt);
	sqlsrv_close( $conn);	

	return $resultado;
}

function subactividadTarea($motivoBloq) {

	$subactividad = '';

	$conn=conectar_bd();
	//Busca al usuario y su contraseña en la tabla usuarios
	$tsql="SELECT ID_SUBACTIVIDAD FROM INV_tbMotivos_Bloqueo WHERE ID_MOTIVO='".$motivoBloq."' ";

	//Obtiene el resultado
	$stmt = sqlsrv_query( $conn, $tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta".$tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );

	if ($rows === true) {
	  $row = sqlsrv_fetch_array($stmt);
	  $subactividad = $row['ID_SUBACTIVIDAD'];
	}

	sqlsrv_free_stmt( $stmt);
	sqlsrv_close( $conn);	

		
	return $subactividad;

}

function descripcionEstado($id_estado) {

	$estado = '';

	$conn=conectar_bd();
	//Busca al usuario y su contraseña en la tabla usuarios
	$tsql="SELECT Estado FROM INV_tbEstados WHERE id_Estado='".$id_estado."' ";

	//Obtiene el resultado
	$stmt = sqlsrv_query( $conn, $tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta".$tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );

	if ($rows === true) {
	  $row = sqlsrv_fetch_array($stmt);
	  $estado = $row['Estado'];
	}

	sqlsrv_free_stmt( $stmt);
	sqlsrv_close( $conn);	

		
	return $estado;

}

function descripcionSubactividad($id_subactividad) {

	$subactividad = '';

	$conn=conectar_bd();
	//Busca al usuario y su contraseña en la tabla usuarios
	$tsql="SELECT Descripcion FROM INV_tbSubactividad WHERE id_Subactividad='".$id_subactividad."' ";

	//Obtiene el resultado
	$stmt = sqlsrv_query( $conn, $tsql);

	if( $stmt === false ){
		die ("Error al ejecutar consulta".$tsql);
	}

	$rows = sqlsrv_has_rows( $stmt );

	if ($rows === true) {
	  $row = sqlsrv_fetch_array($stmt);
	  $subactividad = $row['Descripcion'];
	}

	sqlsrv_free_stmt( $stmt);
	sqlsrv_close( $conn);	

		
	return $subactividad;

}