<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];

    if ((isset($_POST["MM_edit"])) && ($_POST["MM_edit"] == "MM_edit")) {
    	
         if(!empty($_POST['check_list'])){
                foreach($_POST['check_list'] as $selected){
                    $restricciones = $restricciones . $selected . ';' ; 
                }
                $restricciones= substr($restricciones,0, strlen($restricciones)-1);
             }  
        
             
        $id = $_POST['id_e'];
        
        
    	$usuario = $_POST['usuario_new'];
    	$nombre= $_POST['nombre'];
    	$mail = $_POST['mail'];
    	$Telefono = $_POST['Telefono'];
    	$idDpto = $_POST['idDpto'];
        $idGrupo = $_POST['idGrupo'];
        $idResponsable = $_POST['idResponsable'];
        $idRol = $_POST['idRol'];
        //$restricciones = $_POST['restricciones'];
        $Inhabilitado = $_POST['Inhabilitado'];
        $password= $_POST['password_new'];


    	$tsql = "UPDATE INV_tbUSUARIOS SET 
                     [idGrupo] = {$idGrupo}
                    ,[idDpto] = {$idDpto}
                    ,[idRol] = {$idRol}
                    ,[nombre] = '{$nombre}'
                    ,[usuario] = '{$usuario}'
                    ,[mail] = '{$mail}'
                    ,[restricciones] = '{$restricciones}'
                    ,[Inhabilitado] = {$Inhabilitado}
                    ,[idResponsable] = {$idResponsable}
                    ,[Telefono] = '{$Telefono}'
                    ,[password] = '{$password}'
                      WHERE id_usu={$id}";
    	
    	$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
                    echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                    die( print_r( sqlsrv_errors(), true));
		}

		sqlsrv_free_stmt($resultado);
    	header("location:usuarios_insert.php");
    }
    
	$tsql = "SELECT INV_tbUSUARIOS.* 
                    FROM INV_tbUSUARIOS 
                    WHERE INV_tbUSUARIOS.id_usu={$id} ";
	

	$registros = sqlsrv_query($conn, $tsql);
        
        //Obtener los dptos.
        $sqlDptos="Select * FROM INV_tbDptos order by dpto";
        $dptos=sqlsrv_query($conn, $sqlDptos);
        
        //Obtener los grupos.
        $sqlGrupos="Select * FROM INV_tbGrupos order by grupo";
        $grupos=sqlsrv_query($conn, $sqlGrupos);
        
        //Obtener los responsables.
        $sqlResponsables="Select * FROM INV_tbUSUARIOS order by nombre";
        $responsables=sqlsrv_query($conn, $sqlResponsables);
        
        //Obtener roles.
        $sqlRoles="Select * FROM INV_tbRoles order by rol";
        $roles=sqlsrv_query($conn, $sqlRoles);

	if( $registros === false ) {
	   	die ("Error al ejecutar consulta: ".$tsql);
	}
	
	$linea = sqlsrv_fetch_array($registros);
        
      
        


          
?>

<form method="post" id="formEdit" name="formEdit" action="usuarios_edit.php" role="form">
	<div class="modal-body">       
		<fieldset>
                <input type="hidden" name="id_e" value="<?php echo $linea['id_usu'];?>" readonly="true">
                <div class="control-group form-group">
                    <div class="controls"> <strong>Usuario*: </strong>
                        <input type="text" name="usuario_new" required id="usuario_new" maxlength="10" value="<?php echo $linea['usuario'];?>" style="width:70%;" class="form-control">
                    </div>
                </div>		      
                
                <div class="control-group form-group">
                    <div class="controls"> <strong>Password*: </strong>
                        <input type="password" name="password_new" required id="password_new" maxlength="10" value="<?php echo $linea['password'];?>" style="width:70%;" class="form-control">
                    </div>
                </div>	

                <div class="control-group form-group">
                    <div class="controls"> <strong>Nombre*: </strong>
                    	<input type="text" name="nombre" required id="nombre" maxlength="50" value="<?php echo $linea['nombre'];?>" style="width:70%;" class="form-control" size="50" >
                    </div>
                </div>		      
                    
                <div class="control-group form-group">
                  	<div class="controls">
                   		<strong>Mail*: </strong><input type="text" required name="mail" maxlength="50" style="width:70%;" class="form-control" value="<?php echo $linea['mail'];?>" size="50" >
                   	</div>
                </div>

                <div class="control-group form-group">
                    <div class="controls"> 
                    	<strong>Teléfono: </strong><input name="Telefono" maxlength="15" type="text" style="width:70%;" class="form-control" value="<?php echo $linea['Telefono']; ?>">
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Departamento*: </strong>
                        <SELECT name="idDpto" style="width:70%;" required>	
                            <?php if (isset($dptos)) { while ($lineaDptos = sqlsrv_fetch_array($dptos)){ ?>
                                <option value="<?php echo $lineaDptos['id_dpto'];?>" <?php if ($lineaDptos['id_dpto'] == $linea['idDpto']) {echo 'selected';} ?>><?php echo $lineaDptos['dpto'];?></option>
                            <?php } } ?>
                        </SELECT>
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Grupo*: </strong>
                        <SELECT name="idGrupo" style="width:70%;" required>	
                            <?php if (isset($grupos)) { while ($lineaGrupos = sqlsrv_fetch_array($grupos)){ ?>
                                <option value="<?php echo $lineaGrupos['id_grupo'];?>" <?php if ($lineaGrupos['id_grupo'] == $linea['idGrupo']) {echo 'selected';} ?>><?php echo $lineaGrupos['grupo'];?></option>
                            <?php } } ?>
                        </SELECT>                    	
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Responsable*: </strong>
                        <SELECT name="idResponsable" style="width:70%;" required>	
                            <?php if (isset($responsables)) { while ($lineaResponsables = sqlsrv_fetch_array($responsables)){ ?>
                                <option value="<?php echo $lineaResponsables['id_usu'];?>" <?php if ($lineaResponsables['id_usu'] == $linea['idResponsable']) {echo 'selected';} ?>><?php echo $lineaResponsables['nombre'];?></option>
                            <?php } } ?>
                        </SELECT>    
                    </div>
                </div>
                    
                <div class="control-group form-group">
                    <div class="controls"> <strong>Permisos*: </strong>
                    	<SELECT name="idRol" style="width:70%;" required>	
                            <?php if (isset($roles)) { while ($lineaRoles = sqlsrv_fetch_array($roles)){ ?>
                                <option value="<?php echo $lineaRoles['id_rol'];?>" <?php if ($lineaRoles['id_rol'] == $linea['idRol']) {echo 'selected';} ?>><?php echo $lineaRoles['rol'];?></option>
                            <?php } } ?>
                        </SELECT> 
                    </div>
                </div>
                    
                
               <div class="control-group form-group">
                    <div class="controls"> <strong>Restricción región*: </strong>
                     <table>
                         <tr>
                       
                             <td style="text-align:left"><input type="checkbox" name="check_list[]"  value="Andalucia Occidental" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Andalucia Occidental')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>><span class="input-group-addon">Andalucia Occidental  </span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]"  value="Andalucia Oriental" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Andalucia Oriental')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>> <span class="input-group-addon">Andalucia Oriental  </span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]"  value="Cataluña" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Cataluña')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>><span class="input-group-addon">Cataluña  </span></td>                                
                         </tr> 
                         <tr>
                          
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Centro" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Centro')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';}  ?>> <span style="text-align:right" class="input-group-addon">Centro</span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Levante" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Levante')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>> <span class="input-group-addon" style="text-align:right">Levante</span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Canarias" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Canarias')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>><span class="input-group-addon" style="text-align:right">Canarias</span></td>
                          </tr> 
                          <tr>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Noroeste" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Noroeste') !== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?> ><span class="input-group-addon" >Noroeste</span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 1" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Zona 1')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>> <span class="input-group-addon">Zona 1</span></td>
                             <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 2" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Zona 2')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>> <span class="input-group-addon">Zona 2</span></td>
                          </tr>
                          <tr>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 3" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Zona 3')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>><span class="input-group-addon">Zona 3</span></td>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 4" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Zona 4')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>><span class="input-group-addon">Zona 4</span></td>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 5" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Zona 5')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';} ?>><span class="input-group-addon">Zona 5</span></td>
                         </tr>
                         <tr>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Todas" onclick = "seleccionTodas() " <?php if (strpos($linea['restricciones'], 'Todas')!== false){echo 'checked="checked"'; } if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'disabled="disabled"';}?>> <span class="input-group-addon">Todas</span></td>
                            <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Ninguna" onclick = "seleccionTodas()" <?php if (strpos($linea['restricciones'], 'Ninguna')!== false){echo 'checked="checked"';} if (strpos($linea['restricciones'], 'Todas')!== false){echo 'disabled="disabled"';} ?>><span class="input-group-addon">Ninguna</span></td>
                         </tr>

                     </table>
                    </div>
                </div>
                                                
                    
                <div class="control-group form-group">
                    
                    
                    <div class="controls"> <strong>Inhabilitado*: </strong>
                        <SELECT name="Inhabilitado" style="width:70%;" required>	
                                <option value="0" <?php if ($linea['Inhabilitado']==0) {echo 'selected';} ?>>Activo</option>
                                <option value="1" <?php if ($linea['Inhabilitado']==1) {echo 'selected';} ?>>Inactivo</option>
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

<script>
   
     function seleccionTodas(){
    
        
        var chk_arr =  document.getElementsByName("check_list[]");
        var chklength = chk_arr.length;   
        var seleccionadoTodas = false;
        var seleccionadoNinguna = false;
     
        
         
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].value==='Todas' && chk_arr[k].checked ){
                  seleccionadoTodas = true;  
            }
            if (chk_arr[k].value==='Ninguna' && chk_arr[k].checked ){   
                seleccionadoNinguna = true;  
            }
        } 
         
        if (seleccionadoTodas===true){
           for(k=0;k< chklength;k++)
            {
              if (chk_arr[k].value!=='Todas'){
                 chk_arr[k].disabled = true;
                 chk_arr[k].checked = false;
             }
            }
        }
               
        if (seleccionadoNinguna===true){
              
         for(k=0;k< chklength;k++)
         {
            if (chk_arr[k].value!=='Ninguna'){
                chk_arr[k].disabled = true;
                chk_arr[k].checked = false;
            }

          }
               
         }
         
          if (seleccionadoNinguna===false &&  seleccionadoTodas===false){
             for(k=0;k< chklength;k++)
             {
                chk_arr[k].disabled = false;
             }
                  
          }
          
          
    }
  
    </script>
    
    
</form>

