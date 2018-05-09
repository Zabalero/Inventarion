<?php
	
	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();	

	$id = $_GET['id'];
        

    if ((isset($_POST["MM_delete"])) && ($_POST["MM_delete"] == "MM_delete")) {
    	$id = $_POST['id_e'];

		//$current_timestamp = date('Y-m-d H:i:s');  
    	//$tsql = "Delete FROM INV_tbEstados WHERE id_Estado={$id}";
        $tsql = "UPDATE INV_tbEstados SET FECHA_VIGENCIA = GETDATE() WHERE id_Estado={$id}";
    	
    	$resultado = sqlsrv_query($conn, $tsql);

		if( $resultado === false ) {
                    echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                    die( print_r( sqlsrv_errors(), true)); 
		}else{
                    
                     $dsql = "DELETE FROM INV_tbMotor_estados where ID_ESTADO_INI={$id} or ID_ESTADO_FIN={$id}";
                     $resultado2 = sqlsrv_query($conn, $dsql);

                    if( $resultado2 === false ) {
                        echo ("Error al ejecutar consulta: ".$dsql."<br/>");
                        die( print_r( sqlsrv_errors(), true));  
                    }
                    
                    
                    
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

<form method="post" id="formEdit" name="formEdit" action="estadosTareas_delete.php" role="form">
	<div class="modal-body">       
		<fieldset>
                <div class="control-group form-group">
                    <div class="controls">
                    	<input type="hidden" name="id_e" value="<?php echo $linea['id_Estado'];?>" size="1" readonly="true">
                    </div>
                </div>		      

                  <div class="box-content">

                    <form method="post" name="formInsert" id="formInsert" action="<?php echo $editFormAction; ?>">
                    <fieldset>


                      <div class="control-group form-group">
                        <div class="controls">
                         <strong>Estado: </strong><input type="text" class="form-control" readonly name="Descripcion" maxlength="50" value="<?php echo($linea["Estado"]); ?>" style="width:70%;"></div>
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
		<input type="submit" class="btn btn-primary" name="submit" value="Borrar" />&nbsp;
		<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		<input type="hidden" name="MM_delete" value="MM_delete">
	</div>
    </div>
</form>