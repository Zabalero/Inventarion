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

            $actividad = $_POST['id_Actividad'];       
            $Descripcion = $_POST['Descripcion'];
            $prioridad = $_POST['PRIORIDAD'];
            $ctos = $_POST['CTOS'];
            $FECHA_VIGENCIA=$_POST['FECHA_VIGENCIA'];
            list($dia_a, $mes_a, $year_a)=explode('/', $FECHA_VIGENCIA);
            $fecha='null';
            $tsql = "INSERT INTO INV_tbSubactividad (id_Actividad, Descripcion, PRIORIDAD, CTOS) VALUES ('{$actividad}','{$Descripcion}', '{$prioridad}', '{$ctos}')";
            //echo ($FECHA_VIGENCIA."<br/>");
            //echo ($mes_a."-".$dia_a."-".$year_a."<br/>");
            //var_dump (checkdate($mes_a,$dia_a, $year_a));exit();
            if (checkdate($mes_a,$dia_a, $year_a)) { 
                 //$fecha = $dia_a.'/'.$mes_a.'/'.$year_a.' 00:00:00'; //Para desarrollo
                $fecha = $year_a.'-'.$mes_a.'-'.$dia_a.' 00:00:00'; //Para producción
                 $tsql = "INSERT INTO INV_tbSubactividad (id_Actividad, Descripcion, FECHA_VIGENCIA, PRIORIDAD, CTOS) VALUES ('{$actividad}','{$Descripcion}','{$fecha}', '{$prioridad}', '{$ctos}')";
            }





                 $resultado = sqlsrv_query($conn, $tsql);

                     if( $resultado === false ) {
                         echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                         die( print_r( sqlsrv_errors(), true));
                     }

                     sqlsrv_free_stmt($resultado);	


	}

	//Variables para la búsqueda
	$maxRows = 10;
	$pageNum = 0;
	$seleccionada = 0;
	if (isset($_GET['pageNum'])) {
	  	$pageNum = $_GET['pageNum'];
	}
	$startRow = $pageNum * $maxRows;	

	$tsql = "select INV_tbSubactividad.*, INV_tbActividad.Actividad 
                from INV_tbSubactividad 
                left join INV_tbActividad on INV_tbSubactividad.id_Actividad=INV_tbActividad.id_actividad
                order by INV_tbSubactividad.Descripcion";
	$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));
        
        //Obtener las actividades.
        $sqlActividades="Select * FROM INV_tbActividad order by Actividad";
        $actividades=sqlsrv_query($conn, $sqlActividades);

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
				<li><a href="#">Gestión de SubActividades</a></li>
			</ul>
			<!-- Formulario alta -->
			
	            <div class="row-fluid">
	            
		            <div class="box span12">
						<div class="box-header" data-original-title>
								<h2><i class="halflings-icon pencil"></i><span class="break"></span>Nueva SubActividad </h2>
								<div class="box-icon">
									<a href="#" data-toggle="modal" data-target="#insertModal" ><i class="halflings-icon plus"></i></a>
								</div>
						</div>
						<!-- Modal Insertar SubActividad-->
						<div id="insertModal" class="modal hide fade" role="dialog">
						  <div class="modal-dialog">

						    <!-- Modal content-->
						    <div class="modal-content">
						      <div class="modal-header btn-primary">
								<button type="button" class="close" data-dismiss="modal">×</button>
								<h2><i class="icon-edit"></i> Insertar SubActividad</h2>
						      </div>
						      <div class="modal-body">

                                                    <div class="box-content">
					              
					              <form method="post" name="formInsert" id="formInsert" action="<?php echo $editFormAction; ?>">
					              <fieldset>


					                <div class="control-group form-group">
					                  <div class="controls">
					                   <strong>Descripcion*: </strong><input type="text" class="form-control" required name="Descripcion" maxlength="50" value="" style="width:70%;"></div>
					                </div>
                                                          
                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Actividad*: </strong>
                                                                <SELECT name="id_Actividad" style="width:70%;" required>	
                                                                    <?php if (isset($actividades)) { while ($lineaActividades = sqlsrv_fetch_array($actividades)){ ?>
                                                                        <option value="<?php echo $lineaActividades['id_actividad'];?>"><?php echo $lineaActividades['Actividad'];?></option>
                                                                    <?php } } ?>
                                                                </SELECT>
                                                            </div>
                                                        </div>

					                <div class="control-group form-group">
					                    <div class="controls"> 
                                                                <strong>Fecha Vigencia: </strong>
                                                                <input type="text" placeholder="dd/mm/yyyy" maxlength="10" name="FECHA_VIGENCIA" id="FECHA_VIGENCIA" value="" style="width:70%;" >
                                                            </div>
					                </div>
					                  
					                <div class="control-group form-group">
					                    <div class="controls"> <strong>Prioridad*: </strong>
					                    	<input type="text" name="PRIORIDAD" maxlength="1" value="" size="1" required>
					                    </div>
					                </div>
					                  					               
					                 <div class="control-group form-group">
					                    <div class="controls"> <strong>APAREZCA CTOS: </strong>					                    	
                                                                <SELECT id="CTOS"  name="CTOS" >';				
                                                                        <option value="">NO</option>
                                                                        <option value="S">SI</option>
                                                                </SELECT>

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





            <!-- Tabla de listado de actividades existentes -->
            
            <div class="row-fluid sortable ui-sortable">		
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon user"></i><span class="break"></span>Listado de SubActividades</h2>
						<div class="box-icon">
							
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							
						</div>
					</div>
					<div class="box-content">
						<!--<table class="table table-striped table-bordered bootstrap-datatable datatable">-->
                                                <table class="cell-border datatable2" width="100%" cellspacing="0">
						  <thead>
							  <tr>
								  <th>ID</th>
                                                                  <th>Descripción</th>								  
                                                                  <th>Fecha Vigencia</th>
								  <th>Prioridad</th>
								  <th>CTOS</th>
                                                                  <th>Actividad</th>
                                                                  <th></th>
							  </tr>
						  </thead>   
						  <tbody>
                          <?php if (isset($registros)) { while ($linea = sqlsrv_fetch_array($registros)){ ?>
							<tr>
								<td><?php echo $linea['id_Subactividad']; ?></td>
								<td class="center"><?php echo $linea['Descripcion']; ?></td>
                                                                <td class="center"><?php echo date_format($linea['FECHA_VIGENCIA'], 'Y-m-d'); ?></td>
								<td class="center"><?php echo $linea['PRIORIDAD']; ?></td>
								<td class="center"><?php echo $linea['CTOS']; ?></td>
                                                                <td class="center"><?php echo $linea['Actividad']; ?></td>
								<td class="center">
									<a class="btn btn-info subactividades" data-toggle="modal" data-target="#viewModal" data-id="<?php echo $linea['id_Subactividad']; ?>">		
										<i class="halflings-icon white zoom-in"></i>  
									</a>
									
									<?php if ($linea['FECHA_VIGENCIA'] == NULL) {  ?>

										<a class="btn btn-success subactividades" data-toggle="modal" data-target="#editModal" data-id="<?php echo $linea['id_Subactividad']; ?>">		
											<i class="halflings-icon white edit"></i>  
										</a>

										<a class="btn btn-danger subactividades" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $linea['id_Subactividad']; ?>">		
											<i class="halflings-icon white trash"></i> 
										</a>
                                                                        <?php }else{ ?>
                                                                                <a class="btn btn-group subactividades" title="Habilitar"  data-toggle="modal" data-target="#habilitarModal" data-id="<?php echo $linea['id_Subactividad']; ?>">		
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
			<h2><i class="icon-edit"></i> Editar SubActividad</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>

	<div class="clearfix"></div>

	<div class="modal hide fade" id="viewModal">
		<div class="modal-header btn-info">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> Consultar SubActividad</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>	

	<div class="clearfix"></div>

	<div class="modal hide fade" id="deleteModal">
		<div class="modal-header btn-danger">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> ¡Eliminar SubActividad!</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>	
        <div class="clearfix"></div>
        <div class="modal hide fade" id="habilitarModal">
		<div class="modal-header btn-info">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> ¿Habilitar de nuevo la SubActividad?</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>	

	<div class="clearfix"></div>	
	
<?php
	print_theme_footer();
?>
<script type="text/javascript">

$(".btn-success.subactividades").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');
      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "subactividades_edit.php",
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





$(".btn-info.subactividades").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');
      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "subactividades_view.php",
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



$(".btn-group.subactividades").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');

      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "subactividades_habilitar.php",
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
$(".btn-danger.subactividades").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');

      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "subactividades_delete.php",
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
$('#formInsert').bootstrapValidator({
           message: 'Este valor no es valido',
           feedbackIcons: {
             valid: 'glyphicon glyphicon-ok',
             invalid: 'glyphicon glyphicon-remove',
             validating: 'glyphicon glyphicon-refresh'
           },
           fields: {
             Actividad: {
               validators: {
                 notEmpty: {
                   message: 'Actividad obligatoria'
                 }
               }
             },
             PRIORIDAD: {
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