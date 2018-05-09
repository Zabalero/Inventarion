<?php
function importarArchivos($conn) {

	if (!empty($_FILES['informeSeguimiento_RA']['tmp_name'])) {
		$nombre = $_FILES['informeSeguimiento_RA']['name'];
		$tamano = $_FILES['informeSeguimiento_RA']['size'] / 1024;
		$tipo = $_FILES['informeSeguimiento_RA']['type'];
		$e = 0;
		if ($archivo = @fopen($_FILES['informeSeguimiento_RA']["tmp_name"],"r")) {
			ini_set("auto_detect_line_endings", true);
			$delete = "DELETE FROM informeSeguimiento_RA";
			$query_delete = sqlsrv_query($conn,$delete);
			while(!feof($archivo)){
				$tsql = "INSERT INTO informeSeguimiento_RA SELECT ";
				$linea = fgets($archivo);
				$registro = explode(';', $linea);
				$campos = count($registro);
				for ($i=0; $i < $campos; $i++) {
					if ($e > 0){ 
						if (empty(rtrim($registro[$i]))) {
							if ($i === ($campos -1)) {
								$tsql .= 'NULL';
							} else {
								$tsql .= 'NULL,';
							}
						} else {
							if ($i === ($campos -1)) {
								//$tsql .= "'".rtrim(strtoupper(replaceSpChar($registro[$i])))."'";
								if (!preg_match("/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/", $registro[$i])) {
								 	$tsql .= "'".rtrim(strtoupper($registro[$i]))."'";
								} else {
									$tsql .= "'".date_format(date_create($registro[$i]), 'Y-m-d H:i:s')."'";
								}								
								
							} else {
								//$tsql .= "'".rtrim(strtoupper(replaceSpChar($registro[$i])))."',";
								if (!preg_match("/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/", $registro[$i])) {
									$tsql .= "'".rtrim(strtoupper($registro[$i]))."',";
								} else {
									$tsql .= "'".date_format(date_create($registro[$i]), 'Y-m-d H:i:s')."',";
								}
							}
						}
					}
				}
				//echo $tsql;

				if($e > 0) {
					return $tsql;
					$query = sqlsrv_query($conn,$tsql) /*or die ("Error al intentar ejecutar ==> ".$tsql)*/;
					sqlsrv_free_stmt($query);
				}
				//print('<br>--'.$e.'--<br>');
				$e++;
				$tsql = '';
			}
			fclose($archivo);
			unset($archivo);
			return "informeSeguimiento_RA - Importación correcta";			
		} else {
			return "informeSeguimiento_RA - Importación correcta";
		}

	}




	// if ($_FILES["adjunto"]["error"] > 0){
	// 	return "No se ha seleccionado correctamente el archivo";
	// } else {
	// 	//  5MB maximum file size 
	// 	$MAXIMUM_FILESIZE = 5 * 1024 * 1024; 
		
	// 	//  Valid file extensions (images, word, excel, powerpoint) 
	// 	$rEFileTypes = 
	// 	  "/^\.(jpg|jpeg|gif|png|doc|docx|txt|rtf|pdf|xls|xlsx| 
	// 	        ppt|pptx|ods|csv|zip){1}$/i"; 
		
	// 	$dir_base = "upload/"; 

	// 	$isFile = is_uploaded_file($_FILES['adjunto']['tmp_name']);

		 
	// 	if ($isFile) {    //  do we have a file?
	// 		$safe_filename=replace_specials_characters($_FILES['adjunto']['name']);
			 
 //    		if ($_FILES['adjunto']['size'] <= $MAXIMUM_FILESIZE && preg_match($rEFileTypes, strrchr($safe_filename, '.'))){ 
	// 			$tsqlFunc = "DELETE FROM INV_TBARCHIVOS WHERE idTarea = $id";
	// 			$stmtFunc = sqlsrv_query( $conn, $tsqlFunc);
	// 			sqlsrv_free_stmt( $stmtFunc);	  
	// 			$isMove = move_uploaded_file ($_FILES['adjunto']['tmp_name'], $dir_base.$id.'-'.$_POST['ref'].'-'.$safe_filename);
	// 			if ($isMove){
	// 				$nombreArchivo=$id.'-'.$_POST['ref'].'-'.$safe_filename;
	// 				$tsql9= "INSERT INTO INV_TBARCHIVOS (idTarea, archivo) VALUES (".$id.", '".$nombreArchivo."')"; 			
	// 				$stmt9= sqlsrv_query( $conn, $tsql9);
	// 				sqlsrv_free_stmt( $stmt9);	
	// 				return "El archivo se ha insertado correctamente";
	// 			} else {
	// 				return "Ha ocurrido un error al insertar el archivo";
	// 			}	
	// 		} else {
	// 			return "Error. Formato no permitido o se ha excedido el l&iacute;mite de Kilobytes";
	// 		}		
	// 	} else {
	// 		return "Error. Formato no permitido o se ha excedido el l&iacute;mite de Kilobytes";
	// 	}
	// }	

}

