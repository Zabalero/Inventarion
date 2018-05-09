<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];
        

    if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "MM_delete")) {
    	$id = $_POST['id_e'];

		//$current_timestamp = date('Y-m-d H:i:s');  
    	$tsql = "UPDATE INV_tbSubActividad SET FECHA_VIGENCIA = GETDATE() WHERE id_Subactividad='$id'";
    	
    	$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
                    echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                    die( print_r( sqlsrv_errors(), true));
		}

		sqlsrv_free_stmt($resultado);
    	header("location:subactividades_insert.php");
    }
    
	$tsql = "SELECT * FROM INV_tbSubactividad WHERE id_Subactividad='$id'";
	

	$registros = sqlsrv_query($conn, $tsql);

	if( $registros === false ) {
	   	die ("Error al ejecutar consulta: ".$tsql);
	}
        
	
	$linea = sqlsrv_fetch_array($registros);
        
        //Obtener las actividades.
        $sqlActividades="Select * FROM INV_tbActividad order by Actividad";
        $actividades=sqlsrv_query($conn, $sqlActividades);

  
?>

<form method="post" id="formEdit" name="formEdit" action="subactividades_delete.php" role="form">
	<div class="modal-body">       
		<fieldset>
                <div class="control-group form-group">
                    <div class="controls">
                    	<input type="hidden" name="id_e" value="<?php echo $linea['id_Subactividad'];?>" size="1" readonly="true">
                    </div>
                </div>		      

                  <div class="box-content">

                    <form method="post" name="formInsert" id="formInsert" action="<?php echo $editFormAction; ?>">
                    <fieldset>


                      <div class="control-group form-group">
                        <div class="controls">
                         <strong>Descripcion*: </strong><input type="text" class="form-control" readonly name="Descripcion" maxlength="50" value="<?php echo($linea["Descripcion"]); ?>" style="width:70%;"></div>
                      </div>

                      <div class="control-group form-group">
                          <div class="controls"> <strong>Actividad*: </strong>
                              <SELECT readonly name="id_Actividad" style="width:70%;" required>	
                                  <?php if (isset($actividades)) { while ($lineaActividades = sqlsrv_fetch_array($actividades)){ ?>
                                      <option value='<?php echo($lineaActividades["id_actividad"]);?>' <?php 
                                                    if ($linea['id_Actividad']==$lineaActividades["id_actividad"]) {
                                                        echo ("selected");
                                                    }
                                                ?> ><?php echo $lineaActividades['Actividad'];?></option>
                                  <?php } } ?>
                              </SELECT>
                          </div>
                      </div>

                      <div class="control-group form-group">
                          <div class="controls"> 
                              <strong>Fecha Vigencia: </strong>
                              <input type="date" readonly maxlength="10" name="FECHA_VIGENCIA" id="FECHA_VIGENCIA" value="<?php echo date_format($linea['FECHA_VIGENCIA'], 'Y-m-d'); ?>" style="width:70%;"  data-date-format="dd/mm/yyyy">
                          </div>
                      </div>

                      <div class="control-group form-group">
                          <div class="controls"> <strong>Prioridad*: </strong>
                              <input type="text" name="PRIORIDAD" value="<?php echo($linea["PRIORIDAD"]); ?>" readonly>
                          </div>
                      </div>

                       <div class="control-group form-group">
                          <div class="controls"> <strong>APAREZCA CTOS: </strong>					                    	
                              <SELECT id="CTOS"  name="CTOS" readonly>';				
                                      <option value="" <?php if ($linea['CTOS']!="S") { echo ("selected"); }?>>NO</option>
                                      <option value="S" <?php if ($linea['CTOS']=="S") { echo ("selected"); }?>>SI</option>
                              </SELECT>

                          </div>
                      </div>	
		</fieldset>
	</div>
	<div class="modal-footer">
		<input type="submit" class="btn btn-primary" name="submit" value="Borrar" />&nbsp;
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		<input type="hidden" name="MM_delete" value="MM_delete">
	</div>
</form>