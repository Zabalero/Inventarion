<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];
      

    if ((isset($_POST["MM_edit"])) && ($_POST["MM_edit"] == "MM_edit")) {
    	$id = $_POST['id_e'];
    	$DESCRIPCION = $_POST['DESCRIPCION'];       
        $FECHA_VIGENCIA=$_POST['FECHA_VIGENCIA'];
        $ID_SUBACTIVIDAD=$_POST['id_SubActividadE'];
        list($dia_a, $mes_a, $year_a)=explode('/', $FECHA_VIGENCIA);
        $fecha='null';
        $tsql = "UPDATE INV_tbMotivos_Bloqueo SET DESCRIPCION = '{$DESCRIPCION}', ID_SUBACTIVIDAD = '{$ID_SUBACTIVIDAD}' WHERE ID_MOTIVO='$id'";
        if (checkdate($mes_a,$dia_a, $year_a)) { 
             //$fecha = $dia_a.'/'.$mes_a.'/'.$year_a.' 00:00:00'; //Para desarrollo
             $fecha = $year_a.'-'.$mes_a.'-'.$dia_a.' 00:00:00'; //Para producción          
             $tsql = "UPDATE INV_tbMotivos_Bloqueo SET DESCRIPCION = '{$DESCRIPCION}', ID_SUBACTIVIDAD = '{$ID_SUBACTIVIDAD}', FECHA_VIGENCIA = '{$FECHA_VIGENCIA}' WHERE ID_MOTIVO='$id'";
        }
    	
    	
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
        
        $cambioActividad = 'N';
        $encontrado= 'N';


  
?>

<form method="post" id="formEdit" name="formEdit" action="motivosBloqueo_edit.php" role="form">
	<div class="modal-body">       
		<fieldset>
                <div class="control-group form-group">
                    <div class="controls">
                    	<input type="hidden" name="id_e" value="<?php echo $linea['ID_MOTIVO'];?>" size="1" readonly="true">
                    </div>
                </div>		      

                <div class="control-group form-group">
                    <div class="controls">
                     <strong>Motivo*: </strong><input type="text" class="form-control" required name="DESCRIPCION" maxlength="200" value="<?php echo($linea["DESCRIPCION"]); ?>" style="width:70%;"></div>
                  </div>

                  <div class="control-group form-group">
                      <div class="controls"> 
                          <strong>Fecha Vigencia: </strong>
                          <input type="text" placeholder="dd/mm/yyyy" maxlength="10" name="FECHA_VIGENCIA" id="FECHA_VIGENCIA" value="<?php echo date_format($linea['FECHA_VIGENCIA'], 'Y-m-d'); ?>" style="width:70%;">
                      </div>
                  </div>
                  <div class="control-group form-group">
                      <div class="controls"> <strong>Actividad*: </strong>
                          <SELECT name="id_ActividadE" id="id_ActividadE"  style="width:70%;" onchange="fetch_select(this.value);"  required>	
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
                      <div class="controls"> <strong>Subactividad*: </strong>
                        
                             <SELECT id="id_SubActividadE" name="id_SubActividadE" style="width:70%;" required>
                               <?php 
                                
                                        if (isset($subactividades)) { while ($lineaSubActividades = sqlsrv_fetch_array($subactividades)){ ?>
                                            <option value='<?php echo($lineaSubActividades["id_Subactividad"]);?>' <?php 
                                                    if ($linea['ID_SUBACTIVIDAD']==$lineaSubActividades["id_Subactividad"]) {
                                                        echo ("selected");
                                                    }
                                                ?> ><?php echo $lineaSubActividades['Descripcion'];?></option>
                                        <?php } }?>                                     
                                                                   
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
<script type="text/javascript">

function fetch_select(val)
{

 $.ajax({
    type: 'post',
    url: 'seleccionSubactividad.php',
 data: {
    get_option:val
 },
    
    success: function (response) {
        $("#id_SubActividadE").html("");
        $("#id_SubActividadE").append(response);
 }
 });
}


</script>