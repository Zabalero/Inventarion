<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];
    
        $tsql = "SELECT * FROM INV_tbEstados WHERE id_Estado={$id}";
	

	$registros = sqlsrv_query($conn, $tsql);

	if( $registros === false ) {
	   	die ("Error al ejecutar consulta: ".$tsql);
	}
	
	$linea = sqlsrv_fetch_array($registros);
        

	$registros = sqlsrv_query($conn, $tsql);

	if( $registros === false ) {
	   	die ("Error al ejecutar consulta: ".$tsql);
	}
        
	
	$linea = sqlsrv_fetch_array($registros);
         $wsql = "select * from inv_tbEstados";
	$listaEstados = sqlsrv_query($conn, $wsql);
        
        $rsql = "select ID_ESTADO_FIN from inv_tbMotor_estados WHERE ID_ESTADO_INI='$id'";
       
       $estadosSiguientes= sqlsrv_query($conn, $rsql);
       //$listaEstadosSiguientes = sqlsrv_fetch_array($estadosSiguientes);
       $listaEstFin = array();
       $i=0;
       while ($listaEstadosSiguientes = sqlsrv_fetch_array($estadosSiguientes)) {
            $listaEstFin[$i]=$listaEstadosSiguientes['ID_ESTADO_FIN'];
            $i++;
            
        }


  
?>

<form method="post" id="formEdit" name="formEdit" action="estadosTareas_view.php" role="form">
	<div class="modal-body">       
		<fieldset>
                <div class="control-group form-group">
                        <div class="controls">
                         <strong>Estado: </strong><input type="text" class="form-control" readonly name="Estado" maxlength="50" value="<?php echo($linea["Estado"]); ?>" style="width:70%;"></div>
                      </div>

                      <div class="control-group form-group">
                          <div class="controls"> 
                              <strong>Fecha Vigencia: </strong>
                              <input type="date" readonly maxlength="10" name="FECHA_VIGENCIA" id="FECHA_VIGENCIA" value="<?php echo date_format($linea['FECHA_VIGENCIA'], 'Y-m-d'); ?>" style="width:70%;"  data-date-format="dd/mm/yyyy">
                          </div>
                      </div>
                       <div class="control-group form-group">
                        <div class="controls"> <strong>Estados siguientes: </strong>


                              <?php  
                              $cuerpoTabla =  $cuerpoTabla . '<table><tr>';
                              if (isset($listaEstados)) { 
                                    $i=0;
                                      while ($estadoI = sqlsrv_fetch_array($listaEstados)){ 
                                         if ($linea['id_Estado'] !== $estadoI['id_Estado'] && $estadoI['FECHA_VIGENCIA']===null){
                                          if ($i%2===0){
                                                $cuerpoTabla =  $cuerpoTabla .'</tr><tr>';
                                          }
                                     
                                          if (in_array($estadoI['id_Estado'],$listaEstFin)){
                                             $cuerpoTabla =  $cuerpoTabla .        
                                                          '<td style="text-align:left"><input type="checkbox" name="check_list[]"  value="' .$estadoI["id_Estado"]. '" checked="checked" disabled><span class="input-group-addon">' .$estadoI["Estado"].'</span></td>';
 
                                          }else{
                                              $cuerpoTabla =  $cuerpoTabla .        
                                                          '<td style="text-align:left"><input type="checkbox" name="check_list[]"  value="' .$estadoI["id_Estado"]. '" disabled><span class="input-group-addon">' .$estadoI["Estado"].'</span></td>';

                                          }
                                        
                                            $i++;
                                        }
                                       }
                                   
                                      $cuerpoTabla =  $cuerpoTabla . '</tr></table>';


                              }
                              echo $cuerpoTabla;
                              ?>   

                        </div>
                    </div>
		</fieldset>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	</div>
</form>