<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];

    if ((isset($_POST["MM_edit"])) && ($_POST["MM_edit"] == "MM_edit")) {
    	$id = $_POST['id_e'];
    	$actividad = $_POST['id_Actividad'];       
        $Descripcion = $_POST['Descripcion'];
        $prioridad = $_POST['PRIORIDAD'];
        $ctos = $_POST['CTOS'];
        $FECHA_VIGENCIA=$_POST['FECHA_VIGENCIA'];
        list($dia_a, $mes_a, $year_a)=explode('/', $FECHA_VIGENCIA);
        $fecha='null';
        $tsql = "UPDATE INV_tbSubactividad SET id_Actividad = '$actividad', PRIORIDAD = '$prioridad', Descripcion = '$Descripcion', CTOS = '$ctos' WHERE id_Subactividad='$id'";
        if (checkdate($mes_a,$dia_a, $year_a)) { 
             //$fecha = $dia_a.'/'.$mes_a.'/'.$year_a.' 00:00:00'; //Para desarrollo
             $fecha = $year_a.'-'.$mes_a.'-'.$dia_a.' 00:00:00'; //Para producción          
             $tsql = "UPDATE INV_tbSubactividad SET id_Actividad = '$actividad', PRIORIDAD = '$prioridad', Descripcion = '$Descripcion', CTOS = '$ctos', FECHA_VIGENCIA = '$fecha' WHERE id_Subactividad='$id'";
        }
    	
    	
    	$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
                    echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                    die( print_r( sqlsrv_errors(), true));
		}

		sqlsrv_free_stmt($resultado);
    	header("location:subactividades_insert.php");
    }
    
	$tsql = "SELECT * FROM INV_tbSubactividad WHERE id_subactividad='$id'";
	

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
                    	<input type="hidden" name="id_e" value="<?php echo $linea['id_Subactividad'];?>" size="1" readonly="true">
                    </div>
                </div>		      

                <div class="control-group form-group">
                    <div class="controls">
                     <strong>Descripcion*: </strong><input type="text" class="form-control" required name="Descripcion" maxlength="50" value="<?php echo($linea["Descripcion"]); ?>" style="width:70%;"></div>
                  </div>

                  <div class="control-group form-group">
                      <div class="controls"> <strong>Actividad*: </strong>
                          <SELECT name="id_Actividad" style="width:70%;" required>	
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
                          <input type="text" placeholder="dd/mm/yyyy" maxlength="10" name="FECHA_VIGENCIA" id="FECHA_VIGENCIA" value="<?php echo date_format($linea['FECHA_VIGENCIA'], 'Y-m-d'); ?>" style="width:70%;">
                      </div>
                  </div>

                    
                  <div class="control-group form-group">
                      <div class="controls"> <strong>Prioridad*: </strong>
                          <input type="text" name="PRIORIDAD" maxlength="1" value="<?php echo($linea["PRIORIDAD"]); ?>" size="1" required>
                      </div>
                  </div>

                   <div class="control-group form-group">
                      <div class="controls"> <strong>APAREZCA CTOS: </strong>					                    	
                          <SELECT id="CTOS"  name="CTOS" >';				
                                      <option value="" <?php if ($linea['CTOS']!="S") { echo ("selected"); }?>>NO</option>
                                      <option value="S" <?php if ($linea['CTOS']=="S") { echo ("selected"); }?>>SI</option>
                              </SELECT>

                      </div>
                  </div>					                  	
		</fieldset>
	</div>
	<div class="modal-footer">
		<input type="submit" class="btn btn-primary" name="submit" value="Actualizar" />&nbsp;
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		<input type="hidden" name="MM_edit" value="MM_edit">
	</div>
</form>