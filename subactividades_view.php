<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];
    
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

<form method="post" id="formEdit" name="formEdit" action="subactividades_edit.php" role="form">
	<div class="modal-body">       
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
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	</div>
</form>