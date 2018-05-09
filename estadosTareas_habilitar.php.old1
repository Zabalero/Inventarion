<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];
        

    if ((isset($_POST["MM_habilitar"])) && ($_POST["MM_habilitar"] == "MM_habilitar")) {
    	$id = $_POST['id_e'];

		//$current_timestamp = date('Y-m-d H:i:s');  
    	$tsql = "UPDATE INV_tbEstados SET FECHA_VIGENCIA = NULL WHERE id_Estado={$id}";
    	
    	$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
                    echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                    die( print_r( sqlsrv_errors(), true));
		}

		sqlsrv_free_stmt($resultado);
    	header("location:estadosTareas_insert.php");
    }
    
	$tsql = "SELECT * FROM INV_tbEstados WHERE id_Estado={$id}";
	

	$registros = sqlsrv_query($conn, $tsql);

	if( $registros === false ) {
	   	die ("Error al ejecutar consulta: ".$tsql);
	}
        
	
	$linea = sqlsrv_fetch_array($registros);

  
?>

<form method="post" id="formEdit" name="formEdit" action="estadosTareas_habilitar.php" role="form">
	<div class="modal-body">       
		<fieldset>
                <div class="control-group form-group">
                    <div class="controls">
                    	<input type="hidden" name="id_e" value="<?php echo $linea['id_Estado'];?>" size="1" readonly="true">
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