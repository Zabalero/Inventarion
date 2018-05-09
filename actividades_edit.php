<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];

    if ((isset($_POST["MM_edit"])) && ($_POST["MM_edit"] == "MM_edit")) {
    	$id = $_POST['id_e'];
    	$actividad = $_POST['Actividad_e'];
    	$prioridad = $_POST['PRIORIDAD_e'];
    	$bloqueo = $_POST['BLOQUEO_e'];
    	$ctos = $_POST['CTOS_e'];
    	$desbloqueo = $_POST['DESBLOQUEO_e'];

    	$tsql = "UPDATE INV_tbActividad SET Actividad = '$actividad', PRIORIDAD = '$prioridad', BLOQUEO = '$bloqueo', CTOS = '$ctos', DESBLOQUEO = '$desbloqueo' WHERE id_actividad='$id'";
    	
    	$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
	    	die ("Error al ejecutar consulta: ".$tsql);
		}

		sqlsrv_free_stmt($resultado);
    	header("location:actividades_insert.php");
    }
    
	$tsql = "SELECT * FROM INV_tbActividad WHERE id_actividad='$id'";
	

	$registros = sqlsrv_query($conn, $tsql);

	if( $registros === false ) {
	   	die ("Error al ejecutar consulta: ".$tsql);
	}
	
	$linea = sqlsrv_fetch_array($registros);

  
?>

<form method="post" id="formEdit" name="formEdit" action="actividades_edit.php" role="form">
	<div class="modal-body">       
		<fieldset>
                <div class="control-group form-group">
                    <div class="controls"> <strong>ID: </strong>
                    	<input type="text" name="id_e" value="<?php echo $linea['id_actividad'];?>" size="1" readonly="true">
                    </div>
                </div>		      

                <div class="control-group form-group">
                  	<div class="controls">
                   		<strong>Actividad: </strong><input type="text" class="form-control" name="Actividad_e" value="<?php echo $linea['Actividad'];?>" size="30">
                   	</div>
                </div>

                <div class="control-group form-group">
                    <div class="controls"> 
                    	<strong>FECHA VIGENCIA: </strong><input type="text" class="date_field" name="FECHA_VIGENCIA_e" value="<?php echo date_format($linea['FECHA_VIGENCIA'], 'Y-m-d');?>" readonly="true">
                    </div>
                </div>
                
                <div class="control-group form-group">
                    <div class="controls"> <strong>PRIORIDAD: </strong>
                    	<input type="text" name="PRIORIDAD_e" value="<?php echo $linea['PRIORIDAD'];?>" size="1">
                    </div>
                </div>
                
                <div class="control-group form-group">
                  	
                    <div class="controls"> <strong>APAREZCA BLOQUEO: </strong>
                    	
						<SELECT name="BLOQUEO_e" value="<?php echo $linea['BLOQUEO'];?>">				
							<option value="" <?php if ($linea['BLOQUEO'] == '') {echo 'selected';} ?>>NO</option>
							<option value="S" <?php if ($linea['BLOQUEO'] == 'S') {echo 'selected';} ?>>SI</option>	
								
						</SELECT>


                    </div>
                </div>
                
                <div class="control-group form-group">
                    <div class="controls"> <strong>APAREZCA CTOS: </strong>
                    	
						<SELECT name="CTOS_e" value="<?php echo $linea['CTOS'];?>">				
							<option value="" <?php if ($linea['CTOS'] == '') {echo 'selected';} ?>>NO</option>
							<option value="S" <?php if ($linea['CTOS'] == 'S') {echo 'selected';} ?>>SI</option>									
						</SELECT>

                    </div>
                </div>
                
                <div class="control-group form-group">
                    <div class="controls"> <strong>APAREZCA DESBLOQUEO: </strong>
                    	
						<SELECT name="DESBLOQUEO_e" value="<?php echo $linea['DESBLOQUEO'];?>">				
							<option value="" <?php if ($linea['DESBLOQUEO'] == '') {echo 'selected';} ?>>NO</option>
							<option value="S" <?php if ($linea['DESBLOQUEO'] == 'S') {echo 'selected';} ?>>SI</option>										
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
<script>

    $('#formEdit').bootstrapValidator({
       message: 'Este valor no es valido',
       feedbackIcons: {
         valid: 'glyphicon glyphicon-ok',
         invalid: 'glyphicon glyphicon-remove',
         validating: 'glyphicon glyphicon-refresh'
       },
       fields: {
         Actividad_e: {
           validators: {
             notEmpty: {
               message: 'Actividad obligatoria'
             }
           }
         },
         PRIORIDAD_e: {
           validators: {
             notEmpty: {
               message: 'PRIORIDAD obligatoria'
             },

             regexp: {
     
               regexp: /^[0-9]/,
     
               message: 'La prioridad es entre 0 y 9'
     
             }         
           }
         },    
       }
    });


</script>