<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];
        

    if ((isset($_POST["MM_habilitar"])) && ($_POST["MM_habilitar"] == "MM_habilitar")) {
    	$id = $_POST['id_e'];

		//$current_timestamp = date('Y-m-d H:i:s');  
    	
        $tsql = "UPDATE INV_tbMotivos_Bloqueo SET FECHA_VIGENCIA = NULL WHERE ID_MOTIVO={$id}";
    	
    	$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
                    echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                    die( print_r( sqlsrv_errors(), true));
		}

		sqlsrv_free_stmt($resultado);
    	header("location:motivosBloqueo_insert.php");
    }
    
	$tsql = "SELECT ID_MOTIVO, DESCRIPCION, FECHA_VIGENCIA,ID_SUBACTIVIDAD,
                (select ID_ACTIVIDAD from INV_TBsubactividad where INV_tbMotivos_Bloqueo.id_subactividad=INV_TBsubActividad.id_subactividad) as id_Actividad
                from INV_tbMotivos_Bloqueo WHERE ID_MOTIVO={$id}";
       
	$registros = sqlsrv_query($conn, $tsql);

	if( $registros === false ) {
	   	die ("Error al ejecutar consulta: ".$tsql);
	}
	
	$linea = sqlsrv_fetch_array($registros);
        
        $sqlActividades="Select * FROM INV_tbActividad order by Actividad";
        $actividades=sqlsrv_query($conn, $sqlActividades);
        
       
        $sqlSubactividad="Select * FROM INV_tbSubactividad where id_actividad = {$linea["id_Actividad"]} order by Descripcion";
        $subactividades=sqlsrv_query($conn, $sqlSubactividad);

  
?>

<form method="post" id="formEdit" name="formEdit" action="motivosBloqueo_habilitar.php" role="form">
	<div class="modal-body">       
		<fieldset>
                <div class="control-group form-group">
                    <div class="controls">
                    	<input type="hidden" name="id_e" value="<?php echo $linea['ID_MOTIVO'];?>" size="1" readonly="true">
                    </div>
                </div>		      

                  <div class="box-content">
	</div>
	<div class="modal-footer">
		<input type="submit" class="btn btn-primary" name="submit" value="Habilitar" />&nbsp;
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		<input type="hidden" name="MM_habilitar" value="MM_habilitar">
	</div>
    </div>
</form>