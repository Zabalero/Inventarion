<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
      
	//Conectar con el servidor de base de datos
	$conn=conectar_bd();


	//Si el usuario no está autorizado se le desconecta
	$rolUsuario=get_rol($_SESSION['usuario']);
	if ($rolUsuario != 'avanzado') {
		header('Location: index.php?mensaje=Usuario%20desconectado');
	}	


	$editFormAction = $_SERVER['PHP_SELF'];
	if (isset($_SERVER['QUERY_STRING'])) {
	  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
	}

	if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "formInsert")) {

            $Estado = $_POST['Estado'];       
            $FECHA_VIGENCIA=$_POST['FECHA_VIGENCIA'];
            list($dia_a, $mes_a, $year_a)=explode('/', $FECHA_VIGENCIA);
            $fecha='null';
            
            //Comprobación que no exista la actividad
            $csql = "SELECT count(*) as total  
                         FROM INV_tbEstados 
                         WHERE Estado='".ltrim(rtrim($Estado))."' ";
             $registros_check = sqlsrv_query($conn, $csql);
             if( $registros_check === false ) {
                     die ("Error al ejecutar consulta: ".$csql);
             }	
             $linea_check = sqlsrv_fetch_array($registros_check);        
             if ($linea_check["total"]>0){
                 echo ("No se puede insertar el estado de Tarea <strong>".$Estado."</strong> puesto que ya existe.");exit();
             }
             //********************
            
            $tsql = "INSERT INTO INV_tbEstados (Estado) VALUES ('{$Estado}')";
            //echo ($FECHA_VIGENCIA."<br/>");
            //echo ($mes_a."-".$dia_a."-".$year_a."<br/>");
            //var_dump (checkdate($mes_a,$dia_a, $year_a));exit();
            if (checkdate($mes_a,$dia_a, $year_a)) { 
                 //$fecha = $dia_a.'/'.$mes_a.'/'.$year_a.' 00:00:00'; //Para desarrollo
                 $fecha = $year_a.'-'.$mes_a.'-'.$dia_a.' 00:00:00'; //Para producción
                 $tsql = "INSERT INTO INV_tbEstados (Estado, FECHA_VIGENCIA) VALUES ('{$Estado}','{$fecha}')";
            }

             $resultado = sqlsrv_query($conn, $tsql);

             if( $resultado === false ) {
                 echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                 die( print_r( sqlsrv_errors(), true));
             }else{
                 
                if(!empty($_POST['check_list'])){
                    foreach($_POST['check_list'] as $selected){
                     $qsql=  "select max(id_Estado) as idEstUlt from INV_tbEstados";
                     $resultadoEstado = sqlsrv_query($conn, $qsql);
                     $ultimoEstado=sqlsrv_fetch_array($resultadoEstado);
                     $ysql = "INSERT INTO INV_tbMotor_estados (ID, ID_ESTADO_INI, ID_ESTADO_FIN) VALUES ((SELECT CAST(max([ID]) as bigint)+1 FROM [INVENTARIO].[dbo].INV_tbMotor_estados), '{$ultimoEstado['idEstUlt']}','{$selected}')";
                     $resultadoMotor = sqlsrv_query($conn, $ysql);
                     if( $resultadoMotor === false ) {
                        echo ("Error al ejecutar consulta: ".$ysql."<br/>");
                        die( print_r( sqlsrv_errors(), true));
                     }
                    }
                 }  
                  
             }

             sqlsrv_free_stmt($resultado);	
             sqlsrv_free_stmt($resultadoEstado);
             
             


	}

	//Variables para la búsqueda
	$maxRows = 10;
	$pageNum = 0;
	$seleccionada = 0;
	if (isset($_GET['pageNum'])) {
	  	$pageNum = $_GET['pageNum'];
	}
	$startRow = $pageNum * $maxRows;	

	$tsql = "select *, 
                (select count(INV_TBTAREAS.idEst) from INV_TBTAREAS where INV_tbEstados.id_Estado=INV_TBTAREAS.idEst) as estadosUtilizados,INV_tbMotor_estados.id_estado_ini as predecesor, INV_tbMotor_estados.ID_ESTADO_FIN as final	 
                from INV_tbEstados, INV_tbMotor_estados
				where INV_tbEstados.id_Estado=INV_tbMotor_estados.id;";
	$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));    
        
        $wsql = "select * from inv_tbEstados";
	$listaEstados = sqlsrv_query($conn, $wsql);

	$mensaje = "";
	$mensaje = $_REQUEST['mensaje'];
	
	// print the page header
	print_theme_header();


?>
			<!-- start: Content -->
		<div id="content" class="span12">
			
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="index.php">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Gestión de Estados</a></li>
			</ul>
			<!-- Formulario alta -->
			
	            <div class="row-fluid">
	            
		            <div class="box span12">
						<div class="box-header" data-original-title>
								<h2><i class="halflings-icon pencil"></i><span class="break"></span>Nuevo Estado </h2>
								<div class="box-icon">
									<a href="#" data-toggle="modal" data-target="#insertModal" ><i class="halflings-icon plus"></i></a>
								</div>
						</div>
						<!-- Modal Insertar Estados-->
						<div id="insertModal" class="modal hide fade" role="dialog">
						  <div class="modal-dialog">

						    <!-- Modal content-->
						    <div class="modal-content">
						      <div class="modal-header btn-primary">
								<button type="button" class="close" data-dismiss="modal">×</button>
								<h2><i class="icon-edit"></i> Insertar Estado</h2>
						      </div>
						      <div class="modal-body">

                                                    <div class="box-content">
					              
					              <form method="post" name="formInsert" id="formInsert" action="<?php echo $editFormAction; ?>">
					              <fieldset>


					                <div class="control-group form-group">
					                  <div class="controls">
					                   <strong>Estado*: </strong><input type="text" class="form-control" required name="Estado" maxlength="50" value="" style="width:70%;"></div>
					                </div>
                                                          
					                <div class="control-group form-group">
					                    <div class="controls"> 
                                                                <strong>Fecha Vigencia: </strong>
                                                                <input type="text" placeholder="dd/mm/yyyy" maxlength="10" name="FECHA_VIGENCIA" id="FECHA_VIGENCIA" value="" style="width:70%;" >
                                                            </div>
					                </div>	
                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Estados siguientes: </strong>
                                                                
                                                                
                                                                  <?php  
                                                                  $cuerpoTabla =  $cuerpoTabla . '<table><tr>';
                                                                  if (isset($listaEstados)) { 
                                                                        $i=0;
                                                                          while ($estadoI = sqlsrv_fetch_array($listaEstados)){
                                                                             if ($estadoI['FECHA_VIGENCIA']===null){ 
                                                                              if ($i%2===0){
                                                                                    $cuerpoTabla =  $cuerpoTabla .'</tr><tr>';
                                                                              }
                                                                              $cuerpoTabla =  $cuerpoTabla .        
                                                                                              '<td style="text-align:left"><input type="checkbox" name="check_list[]"  value="' .$estadoI["id_Estado"]. '"><span class="input-group-addon">' .$estadoI["Estado"].'</span></td>';
                                                                          
                                                                                $i++;
                                                                            }
                                                                           }
                                                                          $cuerpoTabla =  $cuerpoTabla . '</tr></table>';
                                                                          
                                                                        
                                                                  }
                                                                  echo $cuerpoTabla;
                                                                  ?>   
                                                                  
                                                            </div>
                                                        </div>

					     
					                <div class="control-group">
					                    <div class="controls">
					                    	<input type="submit" value="Insertar" class="btn btn-primary">
					                    	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					                	</div>
					                </div>
					                <input type="hidden" name="MM_insert" value="formInsert">
					            	</fieldset>
					              </form>
				       			</div>
						      </div>
						      <div class="modal-footer">	      	
						        
						      </div>
						    </div>
						  </div>
						</div>	
					</div><!--/span-->
				</div><!--/row-fluid-->





            <!-- Tabla de listado de estados existentes -->
            
            <div class="row-fluid sortable ui-sortable">		
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon user"></i><span class="break"></span>Listado de Estados</h2>
						<div class="box-icon">
							
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							
						</div>
					</div>
					<div class="box-content">
						<table class="cell-border datatable2" width="100%" cellspacing="0">
						  <thead>
							  <tr>
								  <th>ID</th>
                                                                  <th>Estado</th>								  
                                                                  <th>Fecha Vigencia</th>
                                                                  <th>Estados Siguientes</th>
                                                                  
                                                                  <th></th>
							  </tr>
						  </thead>   
						  <tbody>
                          <?php if (isset($registros)) { while ($linea = sqlsrv_fetch_array($registros)){ ?>
							<tr>
								<td><?php echo $linea['id_Estado']; ?></td>
								<td class="center"><?php echo $linea['Estado']; ?></td>
                                                                <td class="center"><?php echo date_format($linea['FECHA_VIGENCIA'], 'Y-m-d'); ?></td>
                                                                <td class="left">
                                                                    <?php                                                              
                                                                      $ssql = "select INV_tbMotor_estados.ID_ESTADO_FIN , INV_TBESTADOS.Estado as desEstado
                                                                                from INV_tbMotor_estados, INV_TBESTADOS
                                                                                where INV_tbMotor_estados.ID_ESTADO_INI= '{$linea['id_Estado']}'  AND
                                                                                INV_TBESTADOS.id_Estado = INV_tbMotor_estados.ID_ESTADO_FIN
                                                                                order by ID_ESTADO_FIN";
                                                                      //echo $ssql; exit();
                                                                      $listaSiguientes= sqlsrv_query($conn, $ssql);
                                                                      $estadosSiguientes='';
                                                                      if (isset($listaSiguientes)) { 
                                                                          while ($siguiente = sqlsrv_fetch_array($listaSiguientes)){
                                                                              
                                                                              //$estadosSiguientes = $estadosSiguientes . $siguiente['desEstado'] . ',';
                                                                              $estadosSiguientes = $estadosSiguientes .'<span>' .$siguiente['desEstado'] . '</span></br>';
                                                                              
                                                                          }
                                                                          
                                                                      }
                                                                      //$estadosSiguientes=substr($estadosSiguientes, 0,strlen ($estadosSiguientes)-1); 
                                                                      echo $estadosSiguientes;  
                                                                    ?>
                                                                </td>
                                                               
								<td class="center">
									<a class="btn btn-info estado" data-toggle="modal" data-target="#viewModal" data-id="<?php echo $linea['id_Estado']; ?>">		
										<i class="halflings-icon white zoom-in"></i>  
									</a>
									
									<?php if ($linea['FECHA_VIGENCIA'] == NULL) {  ?>

										<a class="btn btn-success estado" data-toggle="modal" data-target="#editModal" data-id="<?php echo $linea['id_Estado']; ?>">		
											<i class="halflings-icon white edit"></i>  
										</a>

										<a class="btn btn-danger estado" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $linea['id_Estado']; ?>">		
											<i class="halflings-icon white trash"></i> 
										</a>

									<?php }else{ ?>
                                                                                <a class="btn btn-group estado" title="Habilitar"  data-toggle="modal" data-target="#habilitarModal" data-id="<?php echo $linea['id_Estado']; ?>">		
											<i class="halflings-icon white repeat"></i> 
										</a>
									<?php } ?>
								</td>
                                </tr>
							<?php } } ?>
						  </tbody>
					  	</table>            
					</div>
				</div>
			</div>	






 	    </div><!--/#content.span12-->
            
    </div><!--/row-->
    

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Empresa-->
		
	<div class="modal hide fade" id="editModal">
		<div class="modal-header btn-success">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> Editar Estado</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>

	<div class="clearfix"></div>

	<div class="modal hide fade" id="viewModal">
		<div class="modal-header btn-info">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> Consultar Estado</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>	

	<div class="clearfix"></div>

	<div class="modal hide fade" id="deleteModal">
		<div class="modal-header btn-danger">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> ¡Eliminar Estado!</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>
        
        <div class="clearfix"></div>
        <div class="modal hide fade" id="habilitarModal">
		<div class="modal-header btn-info">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> ¿Habilitar de nuevo el Estado de la Tarea?</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>

	<div class="clearfix"></div>	
	
<?php
	print_theme_footer();
?>
<script type="text/javascript">

$(".btn-success.estado").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');
      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "estadosTareas_edit.php",
            data: dataString,
            cache: false,
            success: function (data) {
                console.log(data);
                $(modal).find('.ct').html(data);
            },
            error: function(err) {
                console.log(err);
            }
        });  
});


$(".btn-info.estado").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');
      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "estadosTareas_view.php",
            data: dataString,
            cache: false,
            success: function (data) {
                console.log(data);
                $(modal).find('.ct').html(data);
            },
            error: function(err) {
                console.log(err);
            }
        });  
});



$(".btn-danger.estado").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');

      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "estadosTareas_delete.php",
            data: dataString,
            cache: false,
            success: function (data) {
                console.log(data);
                $(modal).find('.ct').html(data);
            },
            error: function(err) {
                console.log(err);
            }
        });  
});
$(".btn-group.estado").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');

      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "estadosTareas_habilitar.php",
            data: dataString,
            cache: false,
            success: function (data) {
                console.log(data);
                $(modal).find('.ct').html(data);
            },
            error: function(err) {
                console.log(err);
            }
        });  
});
jQuery(document).ready(function($){
    $('.datatable2').dataTable({
        dom:'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        "language": {
            "lengthMenu": "Mostrando _MENU_ registros por página",
            "zeroRecords": "No se han encontrado datos",
            "info": "Página _PAGE_ de _PAGES_.  Mostrando _START_ a _END_ de _TOTAL_ registros encontrados",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "",
            "search": "Buscar:",
            "paginate": {
                "first":      "Primero",
                "last":       "Último",
                "next":       "Siguiente",
                "previous":   "Anterior"
            }
        },
        "lengthChange": false,
        "order":[],                    
        "paging":   true,
        "info":     true,
        "pageLength": 25,
        "scrollX": true
    } ); 
});
</script>