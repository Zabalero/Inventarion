<?php
//*** Javier Fernández ***
//*** 08/08/2017 ***

session_start();
header("Cache-control: private");
$_SESSION['detalle']="TRUE"; 
//echo "string"; esto estaba antes en actividades, no sé porque, lo comento

require_once "inc/theme.inc";
require "inc/funciones.inc";
require_once "inc/ImportadorExcelGenerico.php";

//Conectar con el servidor de base de datos
$conn=conectar_bd();

//Si el usuario no está autorizado se le desconecta
$rolUsuario=get_rol($_SESSION['usuario']);
if ($rolUsuario != 'avanzado') {
header('Location: index.php?mensaje=Usuario%20desconectado');
}	

$Texto_salida_error="";

if (isset($_FILES["ruta_input"]["name"]) && $_FILES["ruta_input"]["name"] != "") {

//echo $_FILES["ruta_input"]["name"];exit();

$nombreFichero = $_FILES["ruta_input"]["name"];
$rutaTemporal = $_FILES["ruta_input"]["tmp_name"];           

//configuramos para evitar notación científica
ini_set("precision", "20");

//importamos excel a array
$imp = new Importador();

$totalFilas=$imp->getArrayByExcelIndice($rutaTemporal, "A:N", "2", false);
//var_dump($totalFilas);exit();
$filaActual=2;
$countImportados=0;
$countErroresImport=0;
$datosExcel = $imp->getData();

/***************************************************************************************/
/**                                                                                   **/
/**                    Tratamiento de Datos Excel                                     **/
/**                                                                                   **/
/***************************************************************************************/

//echo($rutaTemporal."<br/>");
//var_dump($datosExcel);exit();

foreach ($datosExcel as $indice=>$filaMaterial) {
$ctrl_error=false;
$txt_error="";
/** CAMPOS 
'referencia' => string '' (length=0)
'actuacion_jazztel' => string '' (length=0)
'cto' => string '' (length=0)
'actividad' => string '' (length=0)
'motivos_bloqueo' => string '' (length=0)
'subactividad' => string '' (length=0)
'comentario' => string '' (length=0)
'estado' => string '' (length=0)
'solicitante' => string '' (length=0)
'tEcnico' => string '' (length=0)
'f._registro' => string '' (length=0)
'f._resol' => string '' (length=0)
'ticket_oceane' => string '' (length=0)
'incid._remedy' => string '' (length=0)
*/      

//En producción es un IIs y no un apache los caracteres se cargandiferentes, así que no puedo leer la cabecera del excel y voy por posición.
$pos_array_fila=array_keys($filaMaterial);                               

/*
*  0 => string 'referencia' (length=10)
1 => string 'actuacion_jazztel' (length=17)
2 => string 'cto' (length=3)
3 => string 'actividad' (length=9)
4 => string 'motivos_bloqueo' (length=15)
5 => string 'subactividad' (length=12)
6 => string 'comentario' (length=10)
7 => string 'estado' (length=6)
8 => string 'solicitante' (length=11)
9 => string 'tEcnico' (length=7)
10 => string 'f._registro' (length=11)
11 => string 'f._resol' (length=8)
12 => string 'ticket_oceane' (length=13)
13 => string 'incid._remedy' (length=13)
*/

$TBTAREAS_REF = $filaMaterial[$pos_array_fila[0]]; //Si la Referencia está vacia entonces se creará la tarea, sino será una modificación

$TBTAREAS_ID_ACTUACION="";               
$ACT_JAZZTEL = $filaMaterial[$pos_array_fila[1]];
//var_dump($pos_array_fila);
//echo ($filaMaterial["actuacion_jazztel"]."-".$filaMaterial[$pos_array_fila[1]]);exit();

$TBTAREASCTO_COD_CTO="";
$TBTAREASCTO_ID_GESTOR="";
$NUMERO=$filaMaterial[$pos_array_fila[2]];

$Actividad=$filaMaterial[$pos_array_fila[3]];
$Subactividad=$filaMaterial[$pos_array_fila[5]];
$TBTAREAS_id_Subactividad="";

$TBTAREAS_COMENTARIOS2=str_replace('\r',' ',str_replace('\n',' ', $filaMaterial[$pos_array_fila[4]]));                
$TBTAREAS_COMENTARIOS=str_replace('\r',' ',str_replace('\n',' ', $filaMaterial[$pos_array_fila[6]]));
//echo($TBTAREAS_COMENTARIOS2."<br/>".$TBTAREAS_COMENTARIOS);exit();

$Estado=$filaMaterial[$pos_array_fila[7]];
$TBTAREAS_idEst="";

$nombre_usuOrigen=$filaMaterial[$pos_array_fila[8]];
$TBTAREAS_idUsuOrigen="";

$nombre_Tecnico=$filaMaterial[$pos_array_fila[9]];
$TBTAREAS_idTecn='NULL';

$FECHA_REGISTRO=$filaMaterial[$pos_array_fila[10]];
$TBTAREAS_FECHA_REGISTRO='';

$FECHA_RESOL=$filaMaterial[$pos_array_fila[11]];
$TBTAREAS_FECHA_RESOL='';

$TBTAREAS_TICKET_OCEANE=$filaMaterial[$pos_array_fila[12]];
$TBTAREAS_INCIDENCIA=$filaMaterial[$pos_array_fila[13]];  

$TBTAREAS_ID_TIPO_ENTRADA = '3';

/***************************/
/** Comprobación de datos **/
/***************************/


//Actuación jazztel
if ($ACT_JAZZTEL=="" || $ACT_JAZZTEL==null){
if (($TBTAREAS_REF=="") || ($TBTAREAS_REF==null)){
$ctrl_error=true;
$txt_error=$txt_error."La actuación de Jazztel está vacia. ";
}
} else {
$tsql="Select ID_ACTUACION FROM inv_actuaciones where ACT_JAZZTEL='{$ACT_JAZZTEL}'";
$registros = sqlsrv_query($conn, $tsql);

$linea = sqlsrv_fetch_array($registros);
$TBTAREAS_ID_ACTUACION=$linea['ID_ACTUACION'];
$TBTAREAS_HUELLA=$linea['HUELLA'];

if ($TBTAREAS_ID_ACTUACION=="" || $TBTAREAS_ID_ACTUACION==null){
$ctrl_error=true;
$txt_error=$txt_error."La actuación de Jazztel '{$ACT_JAZZTEL}' no se encontró en la BD. ";
}
sqlsrv_free_stmt($registros);
}

//CTO //Despues de comprobar el CTO se debe crear el registro en la tabla INV_TAREAS_CTO
if ($NUMERO=="" || $NUMERO==null){
if (($TBTAREAS_REF=="") || ($TBTAREAS_REF==null)){
$ctrl_error=true;
$txt_error=$txt_error."El CTO está vacio. ";                    
}
} else {
$tsql="Select COD_CTO, ID_GESTOR FROM INV_CTOS where NUMERO='{$NUMERO}'";
$registros = sqlsrv_query($conn, $tsql);

$linea = sqlsrv_fetch_array($registros);
$TBTAREASCTO_COD_CTO=$linea['COD_CTO'];
$TBTAREASCTO_ID_GESTOR=$linea['ID_GESTOR'];
//echo $TBTAREASCTO_COD_CTO."-".$TBTAREASCTO_ID_GESTOR."<br/>";
/*if ($TBTAREASCTO_COD_CTO=="" || $TBTAREASCTO_COD_CTO==null){
$ctrl_error=true;
$txt_error=$txt_error."El código CTO '{$NUMERO}' no se encontró en la BD. ";
}*/
sqlsrv_free_stmt($registros);
}

//Actividad y Subactividad (Si viene vacio, pues nada) - En la tabla  Tareas solo está la subactividad, es la que habrá que insertar
if ($Actividad!="" && $Actividad!=null && $Subactividad!="" && $Subactividad!=null){
$tsql="Select id_Actividad from INV_tbActividad where Actividad = '{$Actividad}'";                    
$registros = sqlsrv_query($conn, $tsql);

$linea = sqlsrv_fetch_array($registros);
$id_Actividad=$linea['id_Actividad'];

if ($id_Actividad=="" || $id_Actividad==null){
$ctrl_error=true;
$txt_error=$txt_error."La Actividad '{$Actividad}' no se encontró en la BD. ";
} else {

$tsql="Select id_Actividad, id_Subactividad from INV_tbSubactividad where Descripcion like '%{$Subactividad}%' and id_Actividad={$id_Actividad}";

$registros = sqlsrv_query($conn, $tsql);

$linea = sqlsrv_fetch_array($registros);
$TBTAREAS_id_Subactividad=$linea['id_Subactividad'];
if ($TBTAREAS_id_Subactividad=="" || $TBTAREAS_id_Subactividad==null){
$ctrl_error=true;
$txt_error=$txt_error."La Subactividad '{$Subactividad}' no se encontró en la BD. ";
}
sqlsrv_free_stmt($registros);
}
sqlsrv_free_stmt($registros);
}

//Solicitante 
if ($nombre_usuOrigen=="" || $nombre_usuOrigen==null){
if (($TBTAREAS_REF=="") || ($TBTAREAS_REF==null)){
$ctrl_error=true;
$txt_error=$txt_error."El SOLICITANTE está vacio. ";                    
}
} else {
$tsql="Select id_usu FROM INV_tbUSUARIOS where nombre='{$nombre_usuOrigen}'";
$registros = sqlsrv_query($conn, $tsql);

$linea = sqlsrv_fetch_array($registros);
$TBTAREAS_idUsuOrigen=$linea['id_usu'];
if ($TBTAREAS_idUsuOrigen=="" || $TBTAREAS_idUsuOrigen==null){
$ctrl_error=true;
$txt_error=$txt_error."El SOLICITANTE '{$nombre_usuOrigen}' no se encontró en la BD. ";
}
sqlsrv_free_stmt($registros);
}

//Fecha registro
if ($FECHA_REGISTRO!="" && $FECHA_REGISTRO!=null){
$timestamp = PHPExcel_Shared_Date::ExcelToPHP($FECHA_REGISTRO);                     
$TBTAREAS_FECHA_REGISTRO = date("Y-m-d H:i:s",$timestamp);//Para producción
//$TBTAREAS_FECHA_REGISTRO = date("d/m/Y H:i:s",$timestamp);//Para desarrollo
}

//Fecha Resol
if ($FECHA_RESOL!="" && $FECHA_RESOL!=null)
{$timestamp = PHPExcel_Shared_Date::ExcelToPHP($FECHA_RESOL);                     
$TBTAREAS_FECHA_RESOL = date("Y-m-d H:i:s",$timestamp);//Para producción
//$TBTAREAS_FECHA_RESOL = date("d/m/Y H:i:s",$timestamp);//Para desarrollo                
}

//Estado 
if ($Estado!="" && $Estado!=null)
{ //Translate $Estado
$tsql="Select id_Estado FROM INV_tbEstados where Estado='{$Estado}'";
$registros = sqlsrv_query($conn, $tsql);
$linea = sqlsrv_fetch_array($registros);
$TBTAREAS_idEst=$linea['id_Estado'];
if ($TBTAREAS_idEst=="" || $TBTAREAS_idEst==null){
$ctrl_error=true;
$txt_error=$txt_error."El Estado '{$Estado}' no se encontró en la BD. ";}
sqlsrv_free_stmt($registros);
}


//Tecnico 
if ($nombre_Tecnico!="" || $nombre_Tecnico!=null){
$tsql="Select id_usu FROM INV_tbUSUARIOS where nombre='{$nombre_Tecnico}'";
$registros = sqlsrv_query($conn, $tsql);
$linea = sqlsrv_fetch_array($registros);
$TBTAREAS_idTecn=$linea['id_usu'];
if ($TBTAREAS_idTecn=="" || $TBTAREAS_idTecn==null){
$ctrl_error=true;
$txt_error=$txt_error."El Técnico '{$nombre_Tecnico}' no se encontró en la BD. ";
}
sqlsrv_free_stmt($registros);
}


/*******************************/
/** Fin comprobación de datos **/
/*******************************/

/*********************************/
/** Inserción o update en la BD **/
/*********************************/
if (!$ctrl_error) { //Solo insertará si  comprobó que los datos están bien
if ($TBTAREAS_REF=="" || $TBTAREAS_REF==null) { //Inserción nueva en la BD
$TBTAREAS_FECHA_RESOL_insert1="";
$TBTAREAS_FECHA_RESOL_insert2="";
if ($TBTAREAS_FECHA_RESOL!=""){
$TBTAREAS_FECHA_RESOL_insert1=",[FECHA_RESOL]";
$TBTAREAS_FECHA_RESOL_insert2=",'{$TBTAREAS_FECHA_RESOL}'";
}
$TBTAREAS_FECHA_REGISTRO_insert1="";
$TBTAREAS_FECHA_REGISTRO_insert2="";
if ($TBTAREAS_FECHA_REGISTRO!=""){
$TBTAREAS_FECHA_REGISTRO_insert1=",[FECHA_REGISTRO]";
$TBTAREAS_FECHA_REGISTRO_insert2=",'{$TBTAREAS_FECHA_REGISTRO}'";
}
//Inserción nueva en la BD
$sql="INSERT INTO [dbo].[INV_TBTAREAS]
([idEst]
,[REF]
,[idTecn]
,[id_Subactividad]
,[idUsuOrigen]
{$TBTAREAS_FECHA_RESOL_insert1}
{$TBTAREAS_FECHA_REGISTRO_insert1}
,[INCIDENCIA]
,[COMENTARIOS]
,[COMENTARIOS2]
,[PRIORIDAD]
,[id_Actuacion]
,[REF_ASOCIADA]
,[TICKET_OCEANE]
,[ID_TIPO_ENTRADA]
,[HUELLA]
)
VALUES (
{$TBTAREAS_idEst}
,(SELECT CAST(max([REF]) as bigint)+1 FROM [INVENTARIO].[dbo].[INV_TBTAREAS])
,{$TBTAREAS_idTecn}
,{$TBTAREAS_id_Subactividad}
,{$TBTAREAS_idUsuOrigen}
{$TBTAREAS_FECHA_RESOL_insert2}
{$TBTAREAS_FECHA_REGISTRO_insert2}
,'{$TBTAREAS_INCIDENCIA}'
,'{$TBTAREAS_COMENTARIOS}'
,'{$TBTAREAS_COMENTARIOS2}'
,3
,{$TBTAREAS_ID_ACTUACION}
,(SELECT CAST(max([REF]) as bigint)+1 FROM [INVENTARIO].[dbo].[INV_TBTAREAS])
,'{$TBTAREAS_TICKET_OCEANE}'
, $TBTAREAS_ID_TIPO_ENTRADA
,'{$TBTAREAS_HUELLA}'
)"
. ";SELECT SCOPE_IDENTITY()"; //Esto último es para conseguir el id que acabo de insertar
//echo $sql;exit();                                    
$resultado = sqlsrv_query($conn, $sql);

sqlsrv_next_result($resultado);
sqlsrv_fetch($resultado);
$id_Tarea_insertado=sqlsrv_get_field($resultado, 0);

if( $resultado === false ) {
$ctrl_error=true;
$txt_error=$txt_error."No se pudo realizar el insert en la BD. SQL ERROR: '{$sql}'";
}
sqlsrv_free_stmt($resultado);

//Inserci?n INV_TBTAREAS_CTO
if ($TBTAREASCTO_COD_CTO!="" && $TBTAREASCTO_COD_CTO!=null){
$TBTAREASCTO_ID_GESTOR_insert1="";
$TBTAREASCTO_ID_GESTOR_insert2="";
if ($TBTAREASCTO_ID_GESTOR!="" && $TBTAREASCTO_ID_GESTOR!=null){
$TBTAREASCTO_ID_GESTOR_insert1="[ID_GESTOR],";
$TBTAREASCTO_ID_GESTOR_insert2="{$TBTAREASCTO_ID_GESTOR},";
}
$sql="INSERT INTO [dbo].[INV_TBTAREAS_CTO]
({$TBTAREASCTO_ID_GESTOR_insert1}
[COD_CTO]
,[id]
,[id_Actuacion])
VALUES
({$TBTAREASCTO_ID_GESTOR_insert2}
{$TBTAREASCTO_COD_CTO}
,{$id_Tarea_insertado}
,{$TBTAREAS_ID_ACTUACION})";
$resultado = sqlsrv_query($conn, $sql);
if( $resultado === false ) {
$ctrl_error=true;
$txt_error=$txt_error."Se pudo realizar el registro en la tabla de TAREAS pero fallo al crear el CTO. SQL ERROR: '{$sql}'";
}
sqlsrv_free_stmt($resultado);
}

//echo "-".$id_Tarea_insertado."-";exit();
} else {
//Update en la BD
$tsql="Select TOP (1) id FROM INV_TBTAREAS where REF='{$TBTAREAS_REF}'";
$registros = sqlsrv_query($conn, $tsql);

$linea = sqlsrv_fetch_array($registros);
$TBTAREAS_id=$linea['id'];
sqlsrv_free_stmt($registros);
if ($TBTAREAS_id=="" || $TBTAREAS_id==null){
$ctrl_error=true;
$txt_error=$txt_error."No se ha encontrado la REF '{$TBTAREAS_REF}' para actualizar la TAREA en la BD. ";
} else {
$TBTAREAS_FECHA_RESOL_insert1="";
if ($TBTAREAS_FECHA_RESOL!=""){
$TBTAREAS_FECHA_RESOL_insert1=",[FECHA_RESOL] = '{$TBTAREAS_FECHA_RESOL}'";
}
$TBTAREAS_FECHA_REGISTRO_insert1="";
if ($TBTAREAS_FECHA_REGISTRO!=""){
$TBTAREAS_FECHA_REGISTRO_insert1=",[FECHA_REGISTRO] = '{$TBTAREAS_FECHA_REGISTRO}'";
}

$TBTAREAS_id_Subactividad_insert1="";
if ($TBTAREAS_id_Subactividad!==""){
$TBTAREAS_id_Subactividad_insert1=",[id_Subactividad] = '{$TBTAREAS_id_Subactividad}'";
}
$TBTAREAS_idUsuOrigen_insert1="";
if ($TBTAREAS_idUsuOrigen!==""){
$TBTAREAS_idUsuOrigen_insert1=",[idUsuOrigen] = '{$TBTAREAS_idUsuOrigen}'";
}
$TBTAREAS_INCIDENCIA_insert1="";
if ($TBTAREAS_INCIDENCIA!==""){
$TBTAREAS_INCIDENCIA_insert1=",[INCIDENCIA] = '{$TBTAREAS_INCIDENCIA}'";
}

$TBTAREAS_COMENTARIOS_insert1="";
if ($TBTAREAS_COMENTARIOS!==""){
$TBTAREAS_COMENTARIOS_insert1=",[COMENTARIOS] = concat([COMENTARIOS],' - ','{$TBTAREAS_COMENTARIOS}')";
}

$TBTAREAS_COMENTARIOS2_insert1="";
if ($TBTAREAS_COMENTARIOS2!==""){
$TBTAREAS_COMENTARIOS2_insert1=",[COMENTARIOS2] = concat([COMENTARIOS2],' - ','{$TBTAREAS_COMENTARIOS2}')";
}

$TBTAREAS_ID_ACTUACION_insert1="";
if ($TBTAREAS_ID_ACTUACION!==""){
$TBTAREAS_ID_ACTUACION_insert1=",[id_Actuacion] = {$TBTAREAS_ID_ACTUACION}";
}
$TBTAREAS_TICKET_OCEANE_insert1="";
if ($TBTAREAS_TICKET_OCEANE!==""){
$TBTAREAS_TICKET_OCEANE_insert1=",[TICKET_OCEANE] = '{$TBTAREAS_TICKET_OCEANE}'";
}

$tsql="UPDATE [dbo].[INV_TBTAREAS]
SET [idEst] = {$TBTAREAS_idEst}
,[idTecn] = {$TBTAREAS_idTecn}
{$TBTAREAS_id_Subactividad_insert1}
{$TBTAREAS_idUsuOrigen_insert1}
{$TBTAREAS_FECHA_RESOL_insert1}
{$TBTAREAS_FECHA_REGISTRO_insert1}
{$TBTAREAS_INCIDENCIA_insert1}
{$TBTAREAS_COMENTARIOS_insert1}
{$TBTAREAS_COMENTARIOS2_insert1}
{$TBTAREAS_ID_ACTUACION_insert1}
{$TBTAREAS_TICKET_OCEANE_insert1}
WHERE id={$TBTAREAS_id}";

$resultado = sqlsrv_query($conn, $tsql);

if( $resultado === false ) {
$ctrl_error=true;
$txt_error=$txt_error."No se pudo actualizar la TAREA con la REF: '{$TBTAREAS_REF}' en la BD. SQL ERROR: '{$tsql}'";
}
sqlsrv_free_stmt($resultado);

//Inserci?n INV_TBTAREAS_CTO
if ($TBTAREASCTO_COD_CTO!="" && $TBTAREASCTO_COD_CTO!=null){
//Antes debo comprobar que ese CTO, para esa actuaci? esa tarea no existe, porque si ya existe no se actualiza.
$tsql="Select count(*) as total FROM INV_TBTAREAS_CTO where COD_CTO={$TBTAREASCTO_COD_CTO} and id={$TBTAREAS_id} and id_Actuacion={$TBTAREAS_ID_ACTUACION}";
$registros = sqlsrv_query($conn, $tsql);

$linea = sqlsrv_fetch_array($registros);
$Total=$linea['total'];
sqlsrv_free_stmt($registros);
//echo ($Total."-<br/>");
if ($Total<=0){

$TBTAREASCTO_ID_GESTOR_insert1="";
$TBTAREASCTO_ID_GESTOR_insert2="";
if ($TBTAREASCTO_ID_GESTOR!="" && $TBTAREASCTO_ID_GESTOR!=null){
$TBTAREASCTO_ID_GESTOR_insert1="[ID_GESTOR],";
$TBTAREASCTO_ID_GESTOR_insert2="{$TBTAREASCTO_ID_GESTOR},";
}
$sql="INSERT INTO [dbo].[INV_TBTAREAS_CTO]
({$TBTAREASCTO_ID_GESTOR_insert1}
 [COD_CTO]
,[id]
,[id_Actuacion])
VALUES
({$TBTAREASCTO_ID_GESTOR_insert2}
{$TBTAREASCTO_COD_CTO}
,{$TBTAREAS_id}
,{$TBTAREAS_ID_ACTUACION})";
$resultado = sqlsrv_query($conn, $sql);
if( $resultado === false ) {
	$ctrl_error=true;
	$txt_error=$txt_error."Se pudo actualizar el registro con REF: '{$TBTAREAS_REF}' en la tabla de TAREAS pero fallo al crear el CTO. SQL ERROR: '{$sql}'";
}
sqlsrv_free_stmt($resultado);
}
}
}


}
}

/*************************************/
/** FIN Inserci? update en la BD **/
/*************************************/

if ($ctrl_error){
$Texto_salida_error=$Texto_salida_error."<br/><strong>".$filaActual."</strong> - Error: <strong>".$txt_error."</strong>";
}
$filaActual++;
}
//echo $nombreFichero."-".$rutaTemporal ;exit();


}

$seleccionada = 0;

// print the page header
print_theme_header();

?>
<!-- start: Content -->
<div id="content" class="span12">

<!-- Formulario alta -->
<div class="row-fluid">
<form class="form-horizontal" method="POST" action="importarExcelTareas.php" id="form_importacion" name="form_importacion" enctype="multipart/form-data">
<div class="box-body">
<div class="form-group">
<label class="col-sm-3 control-label">Ruta*:</label>
<div class="col-sm-5">
<input type="file" class="" id="ruta_input" name="ruta_input" style="width:250px;">
&nbsp;&nbsp;
<button id="btn_importar" name="btn_importar" type="submit" class="btn btn-success pull left" >Importar Excel</button>
</div>
</div>
</div>                        
<div class="box-footer">

</div>
<!-- /.box-footer -->
</form>
<?php if($Texto_salida_error!="") {?>
<div class="alert alert-block">
<p style='color:red;font-size: 120%;font-weight: bold;'>No se han podido insertar o modificar las siguientes filas:</p>
<p style='color:red;'><?php echo($Texto_salida_error); ?></p>
</div>
<?php } else { ?>
<?php if ($nombreFichero!="" && $nombreFichero!=null) { ?>
<div class="success">
<p style='color:green;font-size: 120%;'>La importaci�n del archivo '<strong><?php echo ($nombreFichero);?></strong>' se ha realizado con �xito.</p>
</div>
<?php } ?>
<?php } ?>
</div><!--/row-fluid-->

</div><!--/#content.span12-->

</div><!--/row-->

</div><!--/.fluid-container-->


<?php
print_theme_footer();
?>
