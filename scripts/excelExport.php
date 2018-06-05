<?php
session_start ();
$nombreFichero = date ( 'YmdHis' );
$solicitud = $_GET ['origen'];
header ( "Content-Disposition: attachment; filename=" . $solicitud . "_" . $nombreFichero . ".xls" );
header ( "Content-Type: application/vnd.ms-excel" );
?>
<html LANG="es">
<head>
<meta http-equiv=content-type content=text/html; charset= UTF-8> 
<title>GENERAR FICHERO EXCEL</title>
</head>
<body>

<?php
require "../inc/funciones.inc";

if ($solicitud === 'DESCARGA_RA') {
	$conn = conectar_bd ();
	
	$tsql = "SELECT  [CABECERA_RA]
      ,[PROVINCIA_RA]
      ,[REGION_RA]
      ,[ACT_JAZTELL_RA]
      ,[ACT_ID_FDTT]
      ,[EECC_DISENO_RA]
      ,[EECC_CONSTRUCCION_RA]
      ,[EECC_CARGA_RA]
      ,[FECHA_ENVIO_RA]
      ,[FECHA_ENTREGA_RA]
      ,[FECHA_INICIO_DISENO_RA]
      ,[FECHA_FIN_CONSTRUCCION_RA]
      ,[RESPONSABLE_CARGA_RA]
      ,[ESTADO_RA]
      ,[ESTADO_GIS_RA]
      ,[EEMM_RA]
      ,[AUDITORIA_RA]
		FROM RA_GRAL_DESCARGA group by [CABECERA_RA],[PROVINCIA_RA],[REGION_RA],[ACT_JAZTELL_RA],[ACT_ID_FDTT],[EECC_DISENO_RA],[EECC_CONSTRUCCION_RA],[EECC_CARGA_RA],[FECHA_ENVIO_RA],[FECHA_ENTREGA_RA],[FECHA_INICIO_DISENO_RA],[FECHA_FIN_CONSTRUCCION_RA],[RESPONSABLE_CARGA_RA],[ESTADO_RA],[ESTADO_GIS_RA],[EEMM_RA],[AUDITORIA_RA]";
	
	$stmt = sqlsrv_query ( $conn, $tsql );
	
	if ($stmt === false) {
		die ( "Error al ejecutar consulta" );
	}
	
	$rows = sqlsrv_has_rows ( $stmt );
	
	if ($rows === true) {
		print ("<table border='1' bordercolor='black'>\n") ;
		print ("<tr aling='center' >\n") ;
		print ("<th bgcolor='#EAA724'>CABECERA_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>PROVINCIA_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>REGION</th>\n") ;
		print ("<th bgcolor='#EAA724'>ACT_JAZTELL_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>ACT_ID_FDTT</th>\n") ;
		print ("<th bgcolor='#EAA724'>EECC_DISENO_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>EECC_CONSTRUCCION_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>EECC_CARGA_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>FECHA_ENVIO_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>FECHA_ENTREGA_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>FX INICIO DISENO</th>\n") ;
		print ("<th bgcolor='#EAA724'>FX FIN CONSTRUCCION</th>\n") ;
		print ("<th bgcolor='#EAA724'>RESPONSABLE_CARGA_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>ESTADO_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>ESTADO_GIS_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>EEMM_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>AUDITORIA_RA</th>\n") ;
		print ("</tr>\n") ;
		
		while ( $row = sqlsrv_fetch_array ( $stmt ) ) {
			$fechaFinConstruccion = '';
			if (! empty ( $row ['FECHA_FIN_CONSTRUCCION_RA'] )) {
				$time = strtotime ( $row ['FECHA_FIN_CONSTRUCCION_RA'] );
				$fechaFinConstruccion = date ( 'Y-m-d', $time );
			}
			
			print ("<tr>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['CABECERA_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['PROVINCIA_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['REGION_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_JAZTELL_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_ID_FDTT'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['EECC_DISENO_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['EECC_CONSTRUCCION_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['EECC_CARGA_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_ENVIO_RA'], 'd/m/Y' ) . "</td>\n") ;
			print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_ENTREGA_RA'], 'd/m/Y' ) . "</td>\n") ;
			print ("<td bgcolor='#FAE187'>" . $row ['FECHA_INICIO_DISENO_RA'] . "</td>\n") ;
			print ("<td bgcolor='#FAE187'>" . $fechaFinConstruccion . "</td>\n") ;
			print ("<td bgcolor='#FAE187'>" . $row ['RESPONSABLE_CARGA_RA'] . "</td>\n") ;
			print ("<td bgcolor='#FAE187'>" . $row ['ESTADO_RA'] . "</td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ESTADO_GIS_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['EEMM_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['AUDITORIA_RA'] . "</b></td>\n") ;
			print ("</tr>\n") ;
		}
		print ("</table>\n") ;
	}
	sqlsrv_free_stmt ( $stmt );
	sqlsrv_close ( $conn );
}

if ($solicitud === 'DESCARGA_RD') {
	$conn = conectar_bd ();
	
	$tsql = "SELECT *
			FROM RD_GRAL_DESCARGA
			WHERE ACT_ID_FDTT_RD IS NOT NULL";
	
	$stmt = sqlsrv_query ( $conn, $tsql );
	
	if ($stmt === false) {
		die ( "Error al ejecutar consulta" );
	}
	
	$rows = sqlsrv_has_rows ( $stmt );
	
	if ($rows === true) {
		print ("<table border='1' bordercolor='black'>\n") ;
		print ("<tr aling='center' >\n") ;
		print ("<th bgcolor='#EAA724'>ACT_ID_FDTT_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>ACTUACION_JAZZTEL_FDTT</th>\n") ;
		print ("<th bgcolor='#EAA724'>ACT_TESA_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>ID_ZONA_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>REGION_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>CABECERA_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>ARBOL_RA</th>\n") ;
		print ("<th bgcolor='#EAA724'>FASE_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>EEMM_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>ESTADO_GIS_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>GESTOR_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>EECC_DIS_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>EECC_CONS_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>EECC_CARGA_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>FECHA_ENVIO_RD</th>\n") ;
		print ("<th bgcolor='#EAA724'>FECHA_ENTREGA_RD</th>\n") ;
		print ("</tr>\n") ;
		while ( $row = sqlsrv_fetch_array ( $stmt ) ) {
			print ("<tr>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_ID_FDTT_RD'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ACTUACION_JAZZTEL_FDTT'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_TESA_RD'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ID_ZONA_RD'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['REGION_RD'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['CABECERA_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ARBOL_RA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'>" . $row ['FASE_RD'] . "</td>\n") ;
			print ("<td bgcolor='#FAE187'>" . $row ['EEMM_RD'] . "</td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ESTADO_GIS_RD'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['GESTOR_RD'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'>" . $row ['EECC_DIS_RD'] . "</td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['EECC_CONS_RD'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['EECC_CARGA_RD'] . "</b></td>\n") ;
			
			print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_ENVIO_RD'], 'd/m/Y' ) . "</td>\n") ;
			print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_ENTREGA_RD'], 'd/m/Y' ) . "</td>\n") ;
			
			print ("</tr>\n") ;
		}
		print ("</table>\n") ;
	}
	sqlsrv_free_stmt ( $stmt );
	sqlsrv_close ( $conn );
}

if ($solicitud === 'DESCARGA_ESTADO_RA') {
	$conn = conectar_bd ();
	
	$tsql = "SELECT Id_FDTT, Estado_carga_GIS
			FROM INV_RA
			WHERE Id_FDTT IS NOT NULL";
	
	$stmt = sqlsrv_query ( $conn, $tsql );
	
	if ($stmt === false) {
		die ( "Error al ejecutar consulta" );
	}
	
	$rows = sqlsrv_has_rows ( $stmt );
	
	if ($rows === true) {
		print ("<table border='1' bordercolor='black'>\n") ;
		print ("<tr aling='center' >\n") ;
		print ("<th bgcolor='#EAA724'>Id_FDTT</th>\n") ;
		print ("<th bgcolor='#EAA724'>Estado_carga_GIS</th>\n") ;
		print ("</tr>\n") ;
		while ( $row = sqlsrv_fetch_array ( $stmt ) ) {
			print ("<tr>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['Id_FDTT'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['Estado_carga_GIS'] . "</b></td>\n") ;
			print ("</tr>\n") ;
		}
		print ("</table>\n") ;
	}
	sqlsrv_free_stmt ( $stmt );
	sqlsrv_close ( $conn );
}

if ($solicitud === 'DESCARGA_ESTADO_RD') {
	$conn = conectar_bd ();
	
	$tsql = "SELECT Id_FDTT, ESTADO_GIS
			FROM INV_RD
			WHERE Id_FDTT IS NOT NULL";
	
	$stmt = sqlsrv_query ( $conn, $tsql );
	
	if ($stmt === false) {
		die ( "Error al ejecutar consulta" );
	}
	
	$rows = sqlsrv_has_rows ( $stmt );
	
	if ($rows === true) {
		print ("<table border='1' bordercolor='black'>\n") ;
		print ("<tr aling='center' >\n") ;
		print ("<th bgcolor='#EAA724'>Id_FDTT</th>\n") ;
		print ("<th bgcolor='#EAA724'>ESTADO_GIS</th>\n") ;
		print ("</tr>\n") ;
		while ( $row = sqlsrv_fetch_array ( $stmt ) ) {
			print ("<tr>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['Id_FDTT'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ESTADO_GIS'] . "</b></td>\n") ;
			print ("</tr>\n") ;
		}
		print ("</table>\n") ;
	}
	sqlsrv_free_stmt ( $stmt );
	sqlsrv_close ( $conn );
}

if ($solicitud === 'DESCARGA_TAREAS') {
	$conn = conectar_bd ();
	
	$tsql = $_SESSION ['TSQL_INCIDENCIAS'];
	
	$stmt = sqlsrv_query ( $conn, $tsql );
	
	if ($stmt === false) {
		die ( "Error al ejecutar consulta" );
	}
	
	$rows = sqlsrv_has_rows ( $stmt );
	
	if ($rows === true) {
		print ("<table border='1' bordercolor='black'>\n") ;
		print ("<tr aling='center' >\n") ;
		print ("<th bgcolor='#EAA724'>PROV.</th>\n") ;
		print ("<th bgcolor='#EAA724'>CABEC.</th>\n") ;
		print ("<th bgcolor='#EAA724'>ID_ACT</th>\n") ;
		print ("<th bgcolor='#EAA724'>AC_JAZZTEL</th>\n") ;
		print ("<th bgcolor='#EAA724'>AC_TESA</th>\n") ;
		print ("<th bgcolor='#EAA724'>REMEDY</th>\n") ;
		print ("<th bgcolor='#EAA724'>REFERENCIA</th>\n") ;
		print ("<th bgcolor='#EAA724'>OCEAN</th>\n") ;
		print ("<th bgcolor='#EAA724'>ESCALADO</th>\n") ;
		print ("<th bgcolor='#EAA724'>ACTIVIDAD</th>\n") ;
		print ("<th bgcolor='#EAA724'>SUBACTIVIDAD</th>\n") ;
		print ("<th bgcolor='#EAA724'>SOLICITANTE</th>\n") ;
		print ("<th bgcolor='#EAA724'>TECNICO</th>\n") ;
		print ("<th bgcolor='#EAA724'>F.REGIS.</th>\n") ;
		print ("<th bgcolor='#EAA724'>F.RESOL.</th>\n") ;
		print ("<th bgcolor='#EAA724'>ESTADO</th>\n") ;
		print ("<th bgcolor='#EAA724'>P</th>\n") ;
		
		print ("</tr>\n") ;
		while ( $row = sqlsrv_fetch_array ( $stmt ) ) {
			print ("<tr>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['PROVINCIA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['CABECERA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ID_ACTUACION'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_JAZZTEL'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_TESA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['REMEDY'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['REF_TBTAREA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['OCEANE_TBTAREA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['ESCALADO_TBTAREA'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['Actividad'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['SUBACTIVIDAD'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['USUORIGEN'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['TECNICO'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . date_format ( $row ['FECHA_REGISTRO'], 'Y-m-d' ) . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . date_format ( $row ['FECHA_RESOL'], 'Y-m-d' ) . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['Estado'] . "</b></td>\n") ;
			print ("<td bgcolor='#FAE187'><b>" . $row ['PRIORIDAD'] . "</b></td>\n") ;
			
			print ("</tr>\n") ;
		}
		print ("</table>\n") ;
	}
	sqlsrv_free_stmt ( $stmt );
	sqlsrv_close ( $conn );
}

if ($solicitud === 'Informe_general') {
	$conn = conectar_bd ();
	
	$tsql = "   SELECT	  a.ID as ID_TAREA,
                                                  a.REF as REFERENCIA,
						  a.HUELLA,
						  i.Descripcion as PROVINCIA,
						  j.descripcion as CABECERA, 
						  d.ACT_JAZZTEL,
						  d.ACT_TESA,
						  a.[INCIDENCIA] as REMEDY,
						  a.TICKET_OCEANE,
						  a.ESCALADO,
						  f.estado as Estado,
						  h.actividad as Actividad,
						  g.descripcion as SUBACTIVIDAD,
						  b.usuario as TECNICO,
						  c.usuario as SOLICITANTE,
						  a.FECHA_INICIO,
						  a.FECHA_REGISTRO,
						  a.FECHA_RESOL AS FECHA_RESOLUCION
 				FROM INV_TBTAREAS as a
				left join inv_tbusuarios as b on a.idTecn=b.id_usu
				left join inv_tbusuarios as c on a.idUsuOrigen=c.id_usu
				left join inv_actuaciones as d on a.id_Actuacion=d.ID_ACTUACION
				left join inv_cabeceras as e on d.COD_CABECERA=e.Cod_Cabecera
				left join INV_tbEstados as f on a.idEst=f.id_Estado
				left join INV_tbsubactividad as g on a.id_Subactividad=g.id_Subactividad
				left join INV_tbactividad as h on g.id_actividad=h.id_actividad
				left join inv_provincias as i  on i.Cod_Provincia=e.cod_provincia
				left join inv_cabeceras as j  on j.Cod_cabecera=d.COD_CABECERA
";
	
	$stmt = sqlsrv_query ( $conn, $tsql );
	
	if ($stmt === false) {
		die ( "Error al ejecutar consulta" );
	}
	
	$rows = sqlsrv_has_rows ( $stmt );
	
	// if ($rows === true){
	print ("<table border='1' bordercolor='black'>\n") ;
	print ("<tr aling='center' >\n") ;
	print ("<th bgcolor='#EAA724'>REFERENCIA</th>\n") ;
	print ("<th bgcolor='#EAA724'>HUELLA</th>\n") ;
	print ("<th bgcolor='#EAA724'>PROVINCIA</th>\n") ;
	print ("<th bgcolor='#EAA724'>CABECERA</th>\n") ;
	print ("<th bgcolor='#EAA724'>ACT_JAZZTEL</th>\n") ;
	print ("<th bgcolor='#EAA724'>ACT_TESA</th>\n") ;
	print ("<th bgcolor='#EAA724'>REMEDY</th>\n") ;
	print ("<th bgcolor='#EAA724'>TICKET_OCEANE</th>\n") ;
	print ("<th bgcolor='#EAA724'>ESCALADO</th>\n") ;
	print ("<th bgcolor='#EAA724'>Estado</th>\n") ;
	print ("<th bgcolor='#EAA724'>Actividad</th>\n") ;
	print ("<th bgcolor='#EAA724'>SUBACTIVIDAD</th>\n") ;
	print ("<th bgcolor='#EAA724'>TECNICO</th>\n") ;
	print ("<th bgcolor='#EAA724'>SOLICITANTE</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_INICIO</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_REGISTRO</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_RESOLUCION</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_CONSTRUCCION</th>\n") ;
	print ("</tr>\n") ;
	while ( $row = sqlsrv_fetch_array ( $stmt ) ) {
		
		$tsql2 = "select FECHA_CAMBIO from inv_historico_tareas where ID_ESTADO_NEW = '8' AND ID_TAREA = '" . $row ['ID_TAREA'] . "'";
		
		$stmt2 = sqlsrv_query ( $conn, $tsql2 );
		
		$row2 = sqlsrv_fetch_array ( $stmt2 );
		
		if ($stmt2 === false) {
			die ( "Error al ejecutar consulta " . $tsql2 );
		}
		
		$rows2 = sqlsrv_has_rows ( $stmt2 );
		
		print ("<tr>\n") ;
		print ("<td bgcolor='#FAE187'><b>=texto(" . $row ['REFERENCIA'] . ";00000)</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['HUELLA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['PROVINCIA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['CABECERA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_JAZZTEL'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_TESA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['REMEDY'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'>" . $row ['TICKET_OCEANE'] . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . $row ['ESCALADO'] . "</td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['Estado'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['Actividad'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'>" . $row ['SUBACTIVIDAD'] . "</td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['TECNICO'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['SOLICITANTE'] . "</b></td>\n") ;
		
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_INICIO'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_REGISTRO'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_RESOLUCION'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row2 ['FECHA_CAMBIO'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		
		print ("</tr>\n") ;
	}
	print ("</table>\n") ;
	// }
	sqlsrv_free_stmt ( $stmt );
	sqlsrv_close ( $conn );
}

if ($solicitud === 'Saturadas') {
	$conn = conectar_bd ();
	
	$tsql = "SELECT D.NUMERO as NUMERO,REF,
		inv_tbTareas.id AS ID_TAREA,
                inv_tbtareas.HUELLA,
                PROVINCIA,
                CABECERA,
                ACT_JAZZTEL,
                ACT_TESA,REMEDY
                ,TICKET_OCEANE,
                ESCALADO,Estado,
                Actividad,SUBACTIVIDAD,TECNICO,USUORIGEN,
                inv_tbtareas.FECHA_INICIO,inv_tbtareas.FECHA_REGISTRO,inv_tbtareas.FECHA_RESOL,inv_tbtareas.TIPO_INCIDENCIA 
                FROM INV_TBTAREAS LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID
                LEFT JOIN INV_TBTAREAS_CTO AS C ON INV_TBTAREAS.ID = C.ID 
                LEFT JOIN INV_CTOS AS D ON C.COD_CTO = D.COD_CTO 
                WHERE (Actividad='General' AND SUBACTIVIDAD='Seguimiento Ampliaciones CTOS Saturadas') or ( SUBACTIVIDAD='Rediseño de RED') or (SUBACTIVIDAD='Ampl. de red-CTOS saturadas') or (SUBACTIVIDAD='Diseño – CTO saturada') ;";
	$stmt = sqlsrv_query ( $conn, $tsql );
	
	$row = sqlsrv_fetch_array ( $stmt );
	
	if ($stmt === false) {
		die ( "Error al ejecutar consulta" );
	}
	
	$rows = sqlsrv_has_rows ( $stmt );
	
	// if ($rows === true){
	print ("<table border='1' bordercolor='black'>\n") ;
	print ("<tr aling='center' >\n") ;
	print ("<th bgcolor='#EAA724'>CTO</th>\n") ;
	print ("<th bgcolor='#EAA724'>REFERENCIA</th>\n") ;
	print ("<th bgcolor='#EAA724'>HUELLA</th>\n") ;
	print ("<th bgcolor='#EAA724'>PROVINCIA</th>\n") ;
	print ("<th bgcolor='#EAA724'>CABECERA</th>\n") ;
	print ("<th bgcolor='#EAA724'>ACT_JAZZTEL</th>\n") ;
	print ("<th bgcolor='#EAA724'>ACT_TESA</th>\n") ;
	print ("<th bgcolor='#EAA724'>REMEDY</th>\n") ;
	print ("<th bgcolor='#EAA724'>TICKET_OCEANE</th>\n") ;
	print ("<th bgcolor='#EAA724'>ESCALADO</th>\n") ;
	print ("<th bgcolor='#EAA724'>Estado</th>\n") ;
	print ("<th bgcolor='#EAA724'>Actividad</th>\n") ;
	print ("<th bgcolor='#EAA724'>SUBACTIVIDAD</th>\n") ;
	print ("<th bgcolor='#EAA724'>TECNICO</th>\n") ;
	print ("<th bgcolor='#EAA724'>SOLICITANTE</th>\n") ;
	print ("<th bgcolor='#EAA724'>TIPO_INCIDENCIA</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_INICIO</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_REGISTRO</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_RESOLUCION</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_CONSTRUCCIÓN</th>\n") ;
	print ("</tr>\n") ;
	while ( $row = sqlsrv_fetch_array ( $stmt ) ) {
		
		$tsql2 = "select FECHA_CAMBIO from inv_historico_tareas where ID_ESTADO_NEW = '8' AND ID_TAREA = '" . $row ['ID_TAREA'] . "'";
		
		$stmt2 = sqlsrv_query ( $conn, $tsql2 );
		
		$row2 = sqlsrv_fetch_array ( $stmt2 );
		
		if ($stmt2 === false) {
			die ( "Error al ejecutar consulta " . $tsql2 );
		}
		
		$rows2 = sqlsrv_has_rows ( $stmt2 );
		
		print ("<tr>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['NUMERO'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>=text(" . $row ['REF'] . ";00000)</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['HUELLA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['PROVINCIA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['CABECERA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_JAZZTEL'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_TESA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['REMEDY'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'>" . $row ['TICKET_OCEANE'] . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . $row ['ESCALADO'] . "</td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['Estado'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['Actividad'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'>" . $row ['SUBACTIVIDAD'] . "</td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['TECNICO'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['USUORIGEN'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['TIPO_INCIDENCIA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_INICIO'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_REGISTRO'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_RESOL'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row2 ['FECHA_CAMBIO'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("</tr>\n") ;
		sqlsrv_free_stmt ( $stmt2 );
	}
	print ("</table>\n") ;
	// }
	sqlsrv_free_stmt ( $stmt );
	sqlsrv_close ( $conn );
}

if ($solicitud === 'Bloqueos/Desbloqueos') {
	$conn = conectar_bd ();
	
	$tsql = "SELECT REF,
	INV_TBTAREAS.HUELLA,
	PROVINCIA,
	CABECERA,
	ACT_JAZZTEL,
	ACT_TESA,
	REMEDY,
	Estado,
	Actividad,
	SUBACTIVIDAD,
	TECNICO,
	inv_tbtareas.FECHA_INICIO,inv_tbtareas.FECHA_REGISTRO,inv_tbtareas.FECHA_RESOL AS FECHA_RESOLUCION,D.NUMERO AS CTO, COMENTARIOS,COMENTARIOS2
     FROM INV_TBTAREAS
    LEFT JOIN INV_VIEW_DATOS_TODO ON INV_VIEW_DATOS_TODO.ID_TAREA = INV_TBTAREAS.ID
    LEFT JOIN INV_TBTAREAS_CTO AS C ON INV_TBTAREAS.ID = C.ID 
    LEFT JOIN INV_CTOS AS D ON C.COD_CTO = D.COD_CTO 
    WHERE Actividad='Bloqueo Cobertura'  or Actividad='Desbloqueo Cobertura'";
	
	$stmt = sqlsrv_query ( $conn, $tsql );
	
	if ($stmt === false) {
		die ( "Error al ejecutar consulta" );
	}
	
	$rows = sqlsrv_has_rows ( $stmt );
	
	// if ($rows === true){
	print ("<table border='1' bordercolor='black'>\n") ;
	print ("<tr aling='center' >\n") ;
	print ("<th bgcolor='#EAA724'>REFERENCIA</th>\n") ;
	print ("<th bgcolor='#EAA724'>HUELLA</th>\n") ;
	print ("<th bgcolor='#EAA724'>PROVINCIA</th>\n") ;
	print ("<th bgcolor='#EAA724'>CABECERA</th>\n") ;
	print ("<th bgcolor='#EAA724'>ACT_JAZZTEL</th>\n") ;
	print ("<th bgcolor='#EAA724'>ACT_TESA</th>\n") ;
	print ("<th bgcolor='#EAA724'>REMEDY</th>\n") ;
	print ("<th bgcolor='#EAA724'>Estado</th>\n") ;
	print ("<th bgcolor='#EAA724'>Actividad</th>\n") ;
	print ("<th bgcolor='#EAA724'>SUBACTIVIDAD</th>\n") ;
	print ("<th bgcolor='#EAA724'>TECNICO</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_INICIO</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_REGISTRO</th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA_RESOLUCION</th>\n") ;
	print ("<th bgcolor='#EAA724'>CTO</th>\n") ;
	
	print ("<th bgcolor='#EAA724'>COMENTARIOS</th>\n") ;
	print ("<th bgcolor='#EAA724'>COMENTARIOS2</th>\n") ;
	print ("</tr>\n") ;
	while ( $row = sqlsrv_fetch_array ( $stmt ) ) {
		print ("<tr>\n") ;
		print ("<td bgcolor='#FAE187'><b>=texto(" . $row ['REF'] . ";00000)</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['HUELLA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['PROVINCIA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['CABECERA'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_JAZZTEL'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['ACT_TESA'] . "   </b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['REMEDY'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['Estado'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['Actividad'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'>" . $row ['SUBACTIVIDAD'] . "</td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['TECNICO'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_INICIO'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_REGISTRO'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['FECHA_RESOLUCION'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['CTO'] . "</b></td>\n") ;
		
		print ("<td bgcolor='#FAE187'><b>" . $row ['COMENTARIOS'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['COMENTARIOS2'] . "</b></td>\n") ;
		print ("</tr>\n") ;
	}
	print ("</table>\n") ;
	// }
	sqlsrv_free_stmt ( $stmt );
	sqlsrv_close ( $conn );
}

if ($solicitud === 'Informe RD – CTO nueva') {
	$conn = conectar_bd ();
	
	$tsql = "SELECT f.Id_FDTT as IDFDTT,b.ACT_JAZZTEL as 'ACTUACION_JZZ',f.ESTADO_GIS as ESTADO_GIS_RD,a.[INCIDENCIA] as [remedy],
                 a.ref as [referencia],d.Actividad as  Actividad, c.Descripcion as Subactividad ,a.[FECHA_RESOL] as 'Fecha_resolución',a.[CTO_NUEVA],e.Estado as 'Estado'   
                 from [INV_TBTAREAS] as a
                 inner join inv_actuaciones as b on a.id_Actuacion=b.ID_ACTUACION 
                 inner join INV_tbSubactividad as c on c.id_Subactividad=a.id_Subactividad
                 inner join INV_tbActividad as d on c.id_actividad=d.id_actividad
                 inner join INV_tbEstados as e on e.id_Estado=a.idEst
                 left  join inv_RD as f on f.ID_ACTUACION= a.id_Actuacion  
                 where cto_nueva <>'' and cto_nueva is not null";
	
	$stmt = sqlsrv_query ( $conn, $tsql );
	
	if ($stmt === false) {
		die ( "Error al ejecutar consulta" );
	}
	
	$rows = sqlsrv_has_rows ( $stmt );
	
	// if ($rows === true){
	print ("<table border='1' bordercolor='black'>\n") ;
	print ("<tr aling='center' >\n") ;
	print ("<th bgcolor='#EAA724'>IDFDTT</th>\n") ;
	print ("<th bgcolor='#EAA724'>ACTUACION JZZ</th>\n") ;
	print ("<th bgcolor='#EAA724'>ESTADO_GIS_RD</th>\n") ;
	print ("<th bgcolor='#EAA724'>Nº de remedy</th>\n") ;
	print ("<th bgcolor='#EAA724'>Nº de referencia</th>\n") ;
	print ("<th bgcolor='#EAA724'>Actividad</th>\n") ;
	print ("<th bgcolor='#EAA724'>Subactividad </th>\n") ;
	print ("<th bgcolor='#EAA724'>FECHA RESOLUCION</th>\n") ;
	print ("<th bgcolor='#EAA724'>CTO_NUEVA</th>\n") ;
	print ("<th bgcolor='#EAA724'>Estado de la tarea</th>\n") ;
	
	print ("</tr>\n") ;
	while ( $row = sqlsrv_fetch_array ( $stmt ) ) {
		print ("<tr>\n") ;
		print ("<td bgcolor='#FAE187' ><b>" . $row ['IDFDTT'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['ACTUACION_JZZ'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['ESTADO_GIS_RD'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['remedy'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>=texto(" . $row ['referencia'] . ";00000)</b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['Actividad'] . "   </b></td>\n") ;
		print ("<td bgcolor='#FAE187'><b>" . $row ['Subactividad'] . "</b></td>\n") ;
		print ("<td bgcolor='#FAE187'>" . date_format ( $row ['Fecha_resolución'], 'd/m/Y H:i:s' ) . "</td>\n") ;
		$primerCaracter = substr ( $row ['CTO_NUEVA'], 0, 1 );
		if ($primerCaracter === '-') {
			$restoCTO = substr ( $row ['CTO_NUEVA'], 1 );
			print ("<td bgcolor='#FAE187'>" . "&nbsp-" . $restoCTO . "</td>\n") ;
		} else {
			print ("<td bgcolor='#FAE187'>" . $row ['CTO_NUEVA'] . "</td>\n") ;
		}
		
		print ("<td bgcolor='#FAE187'>" . $row ['Estado'] . "</td>\n") ;
		
		print ("</tr>\n") ;
	}
	print ("</table>\n") ;
	// }
	sqlsrv_free_stmt ( $stmt );
	sqlsrv_close ( $conn );
}

?>
</body>
</html>