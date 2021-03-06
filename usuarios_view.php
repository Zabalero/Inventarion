<?php
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];
    
	$tsql = "SELECT INV_tbUSUARIOS.*, 
                    INV_tbDptos.dpto, 
                    tbusers2.nombre as Responsable,
                    INV_tbRoles.rol,
                    INV_tbGrupos.grupo
                    FROM INV_tbUSUARIOS 
                    left join INV_tbDptos on INV_tbUSUARIOS.idDpto=INV_tbDptos.id_dpto
                    inner join INV_tbUSUARIOS as tbusers2 on INV_tbUSUARIOS.idResponsable=tbusers2.id_usu
                    left join INV_tbRoles on INV_tbUSUARIOS.idRol=INV_tbRoles.id_rol
                    left join INV_tbGrupos on INV_tbUSUARIOS.idGrupo=INV_tbGrupos.id_grupo WHERE INV_tbUSUARIOS.id_usu={$id} ";
	

	$registros = sqlsrv_query($conn, $tsql);

	if( $registros === false ) {
	   	die ("Error al ejecutar consulta: ".$tsql);
	}
	
	$linea = sqlsrv_fetch_array($registros);

  
?>

<form method="post" id="formEdit" name="formEdit" action="usuarios_view.php" role="form">
	<div class="modal-body">       
		<fieldset>
                
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
                    <div class="controls"> <strong>Restricción región: </strong>
                    	<input type="text" value="<?php echo $linea['restricciones'];?>" style="width:70%;" class="form-control" readonly="true">
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Inhabilitado: </strong>
                    	<input type="text" style="width:70%;" value='<?php 
                                                    if ($linea['Inhabilitado']==0) {
                                                        echo ("Activo");
                                                    } else {
                                                        echo ("Inactivo");
                                                    }
                                                ?>' class="form-control" readonly="true">
                    </div>
                </div>
                
                
		</fieldset>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	</div>
</form>