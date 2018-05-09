<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];

    if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "MM_delete")) {
    	$id = $_POST['id_e'];

		$current_timestamp = date('Y-m-d H:i:s');  
    	$tsql = "Delete FROM INV_tbUSUARIOS WHERE id_usu={$id}";
    	
    	$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
                    echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                    die( print_r( sqlsrv_errors(), true));
		}

		sqlsrv_free_stmt($resultado);
    	header("location:usuarios_insert.php");
    }
    
	$tsql = "SELECT INV_tbUSUARIOS.*, 
                    INV_tbDptos.dpto, 
                    tbusers2.nombre as Responsable,
                    INV_tbRoles.rol,
                    INV_tbGrupos.grupo
                    FROM INV_tbUSUARIOS 
                    left join INV_tbDptos on INV_tbUSUARIOS.idDpto=INV_tbDptos.id_dpto
                    inner join INV_tbUSUARIOS as tbusers2 on INV_tbUSUARIOS.id_usu=tbusers2.id_usu
                    left join INV_tbRoles on INV_tbUSUARIOS.idRol=INV_tbRoles.id_rol
                    left join INV_tbGrupos on INV_tbUSUARIOS.idGrupo=INV_tbGrupos.id_grupo WHERE INV_tbUSUARIOS.id_usu={$id} ";
	

	$registros = sqlsrv_query($conn, $tsql);

	if( $registros === false ) {
	   	die ("Error al ejecutar consulta: ".$tsql);
	}
	       
	$linea = sqlsrv_fetch_array($registros);

  
?>

<form method="post" id="formEdit" name="formEdit" action="usuarios_delete.php" role="form">
	<div class="modal-body">       
		<fieldset>
                <input type="hidden" name="id_e" value="<?php echo $linea['id_usu'];?>" readonly="true">
                <div class="control-group form-group">
                    <div class="controls"> <strong>Usuario: </strong>
                    	<input type="text" value="<?php echo $linea['usuario'];?>" style="width:70%;" class="form-control" readonly="true">
                    </div>
                </div>		      

                <div class="control-group form-group">
                    <div class="controls"> <strong>Nombre: </strong>
                    	<input type="text" value="<?php echo $linea['nombre'];?>" style="width:70%;" class="form-control" size="50" readonly="true">
                    </div>
                </div>		      
                    
                <div class="control-group form-group">
                  	<div class="controls">
                   		<strong>Mail: </strong><input type="text" style="width:70%;" class="form-control" value="<?php echo $linea['mail'];?>"  size="50" readonly="true">
                   	</div>
                </div>

                <div class="control-group form-group">
                    <div class="controls"> 
                    	<strong>Teléfono: </strong><input type="text" style="width:70%;" class="form-control" value="<?php echo $linea['Telefono']; ?>" readonly="true">
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Departamento: </strong>
                    	<input type="text" value="<?php echo $linea['dpto'];?>" style="width:70%;" class="form-control" size="50" readonly="true">
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Grupo: </strong>
                    	<input type="text" value="<?php echo $linea['grupo'];?>" style="width:70%;" style="width:70%;" readonly="true">
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Responsable: </strong>
                    	<input type="text" value="<?php echo $linea['Responsable'];?>" style="width:70%;" class="form-control" size="50" readonly="true">
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Permisos: </strong>
                    	<input type="text" value="<?php echo $linea['rol'];?>" style="width:70%;" class="form-control" readonly="true">
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Restricción región*: </strong>
                     <table>
                         <tr>
                       
                             <td style="text-align:left"><input type="checkbox" name="check_list[]"  value="Andalucia Occidental"  disabled="disabled" <?php if (strpos($linea['restricciones'], 'Andalucia Occidental')!== false){echo 'checked="checked"';} ?>><span class="input-group-addon">Andalucia Occidental  </span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]"  value="Andalucia Oriental"  disabled="disabled" <?php if (strpos($linea['restricciones'], 'Andalucia Oriental')!== false){echo 'checked="checked"';} ?>> <span class="input-group-addon">Andalucia Oriental  </span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]"  value="Cataluña"  disabled="disabled" <?php if (strpos($linea['restricciones'], 'Cataluña')!== false){echo 'checked="checked"';} ?>><span class="input-group-addon">Cataluña  </span></td>                                
                         </tr> 
                         <tr>
                          
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Centro" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Centro')!== false){echo 'checked="checked"';} ?>> <span class="input-group-addon">Centro</span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Levante" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Levante')!== false){echo 'checked="checked"';}?>> <span class="input-group-addon">Levante</span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Canarias" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Canarias')!== false){echo 'checked="checked"';}?>><span class="input-group-addon">Canarias</span></td>
                          </tr> 
                          <tr>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Noroeste" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Noroeste') !== false){echo 'checked="checked"';} ?> ><span class="input-group-addon" >Noroeste</span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 1" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Zona 1')!== false){echo 'checked="checked"';} ?>> <span class="input-group-addon">Zona 1</span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 2" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Zona 2')!== false){echo 'checked="checked"';} ?>> <span class="input-group-addon">Zona 2</span></td>
                          </tr>
                          <tr>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 3" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Zona 3')!== false){echo 'checked="checked"';} ?>><span class="input-group-addon">Zona 3</span></td>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 4" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Zona 4')!== false){echo 'checked="checked"';} ?>><span class="input-group-addon">Zona 4</span></td>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 5" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Zona 5')!== false){echo 'checked="checked"';} ?>><span class="input-group-addon">Zona 5</span></td>
                         </tr>
                         <tr>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Todas" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Todas')!== false){echo 'checked="checked"'; } ?>> <span class="input-group-addon">Todas</span></td>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Ninguna" disabled="disabled" <?php if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'checked="checked"';} ?>><span class="input-group-addon">Ninguna</span></td>
                         </tr>

                     </table>
                    </div>
                </div>
               
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Inhabilitado: </strong>
                    	<input type="text" style="width:70%;" value='<?php 
                                                    if ($linea['Inhabilitado']==0) {
                                                        echo ("Inactivo");
                                                    } else {
                                                        echo ("Activo");
                                                    }
                                                ?>' class="form-control" readonly="true">
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