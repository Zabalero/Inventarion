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

            $DESCRIPCION = $_POST['DESCRIPCION'];       
            $FECHA_VIGENCIA=$_POST['FECHA_VIGENCIA'];
            $ID_SUBACTIVIDAD = $_POST['id_SubActividad'];
            list($dia_a, $mes_a, $year_a)=explode('/', $FECHA_VIGENCIA);
            $fecha='null';
            
            //Comprobación que no exista la actividad
            $csql = "SELECT count(*) as total  
                         FROM INV_tbMotivos_Bloqueo
                         WHERE DESCRIPCION='".ltrim(rtrim($DESCRIPCION))."' ";
             $registros_check = sqlsrv_query($conn, $csql);
             if( $registros_check === false ) {
                     die ("Error al ejecutar consulta: ".$csql);
             }	
             $linea_check = sqlsrv_fetch_array($registros_check);        
             if ($linea_check["total"]>0){
                 echo ("No se puede insertar el motivo de Bloqueo <strong>".$Estado."</strong> puesto que ya existe.");exit();
             }
             //********************
            
            $tsql = "INSERT INV_tbMotivos_Bloqueo (DESCRIPCION, ID_SUBACTIVIDAD) VALUES ('{$DESCRIPCION}', '{$ID_SUBACTIVIDAD}')";
            //echo ($FECHA_VIGENCIA."<br/>");
            //echo ($mes_a."-".$dia_a."-".$year_a."<br/>");
            //var_dump (checkdate($mes_a,$dia_a, $year_a));exit();
            if (checkdate($mes_a,$dia_a, $year_a)) { 
                 //$fecha = $dia_a.'/'.$mes_a.'/'.$year_a.' 00:00:00'; //Para desarrollo
                 $fecha = $year_a.'-'.$mes_a.'-'.$dia_a.' 00:00:00'; //Para producción
                 $tsql = "INSERT INTO INV_tbMotivos_Bloqueo (DESCRIPCION, ID_SUBACTIVIDAD, FECHA_VIGENCIA) VALUES ('{$DESCRIPCION}', '{$ID_SUBACTIVIDAD}', '{$fecha}')";
            }

             $resultado = sqlsrv_query($conn, $tsql);

             if( $resultado === false ) {
                 echo ("Error al ejecutar consulta: ".$tsql."<br/>");
                 die( print_r( sqlsrv_errors(), true));
             }

             sqlsrv_free_stmt($resultado);	


	}
        
        //Obtener las actividades.
        $sqlActividades="Select * FROM INV_tbActividad order by Actividad";
        $actividades=sqlsrv_query($conn, $sqlActividades);

	//Variables para la búsqueda
	$maxRows = 10;
	$pageNum = 0;
	$seleccionada = 0;
	if (isset($_GET['pageNum'])) {
	  	$pageNum = $_GET['pageNum'];
	}
	$startRow = $pageNum * $maxRows;	

	$tsql = "select ID_MOTIVO, DESCRIPCION, FECHA_VIGENCIA, 
                (select descripcion from INV_TBsubactividad where INV_tbMotivos_Bloqueo.id_subactividad=INV_TBsubActividad.id_subactividad) as desSubactividad 
                from INV_tbMotivos_Bloqueo";
        $registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));        

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
				<li><a href="#">Gestión de Motivos Bloqueo</a></li>
			</ul>
			<!-- Formulario alta -->
			
	            <div class="row-fluid">
	            
		            <div class="box span12">
						<div class="box-header" data-original-title>
								<h2><i class="halflings-icon pencil"></i><span class="break"></span>Nuevo Motivo Bloqueo</h2>
								<div class="box-icon">
									<a href="#" data-toggle="modal" data-target="#insertModal" ><i class="halflings-icon plus"></i></a>
								</div>
						</div>
						<!-- Modal Insertar Motivo Bloqueo-->
						<div id="insertModal" class="modal hide fade" role="dialog">
						  <div class="modal-dialog">

						    <!-- Modal content-->
						    <div class="modal-content">
						      <div class="modal-header btn-primary">
								<button type="button" class="close" data-dismiss="modal">×</button>
								<h2><i class="icon-edit"></i> Insertar Motivo Bloqueo</h2>
						      </div>
						      <div class="modal-body">

                                                    <div class="box-content">
					              
					              <form method="post" name="formInsert" id="formInsert" action="<?php echo $editFormAction; ?>">
					              <fieldset>


					                <div class="control-group form-group">
					                  <div class="controls">
					                   <strong>Motivo Bloqueo*: </strong><input type="text" class="form-control" required name="DESCRIPCION"  maxlength="200" value="" style="width:70%;"></div>
					                </div>
                                                          
					                <div class="control-group form-group">
					                    <div class="controls"> 
                                                                <strong>Fecha Vigencia: </strong>
                                                                <input type="text" placeholder="dd/mm/yyyy" maxlength="10" name="FECHA_VIGENCIA" id="FECHA_VIGENCIA" value="" style="width:70%;" >
                                                            </div>
					                </div>	
                                                         
					                  	
					                 <div class="control-group form-group">
                                                            <div class="controls"> <strong>Actividad*: </strong>
                                                                <SELECT id="id_Actividad" name="id_Actividad" style="width:70%;"  onchange="fetch_select(this.value);" required>	
                                                                    <option value=""></option>
                                                                    <?php if (isset($actividades)) { while ($lineaActividades = sqlsrv_fetch_array($actividades)){ ?>
                                                                        <option value="<?php echo $lineaActividades['id_actividad'];?>"><?php echo $lineaActividades['Actividad'];?></option>
                                                                    <?php } } ?>
                                                                </SELECT>
                                                            </div>
                                                        </div>
                                                          
                                                         <div class="control-group form-group">
                                                            <div class="controls"> <strong>Subactividad*: </strong>
                                                                <SELECT id="id_SubActividad" name="id_SubActividad" style="width:70%;" required>
                                                                    
                                                                   
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





            <!-- Tabla de listado de motivos existentes -->
            
            <div class="row-fluid sortable ui-sortable">		
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon user"></i><span class="break"></span>Listado de Motivos</h2>
						<div class="box-icon">
							
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							
						</div>
					</div>
					<div class="box-content">
						<table class="cell-border datatable2" width="100%" cellspacing="0">
						  <thead>
							  <tr>
								  <th>ID</th>
                                                                  <th>Descripcion</th>								  
                                                                  <th>Fecha Vigencia</th>
                                                                  <th>Subactividad</th>
                                                                  <th></th>
							  </tr>
						  </thead>   
						  <tbody>
                          <?php if (isset($registros)) { while ($linea = sqlsrv_fetch_array($registros)){ ?>
							<tr>
								<td><?php echo $linea['ID_MOTIVO']; ?></td>
								<td class="center"><?php echo $linea['DESCRIPCION']; ?></td>
                                                                <td class="center"><?php echo date_format($linea['FECHA_VIGENCIA'], 'Y-m-d'); ?></td>
                                                                <td class="center"><?php echo $linea['desSubactividad']; ?></td>
                                                                 
								<td class="center">
									<a class="btn btn-info estado" data-toggle="modal" data-target="#viewModal" data-id="<?php echo $linea['ID_MOTIVO']; ?>">		
										<i class="halflings-icon white zoom-in"></i>  
									</a>
									
									<?php if ($linea['FECHA_VIGENCIA'] == NULL) {  ?>

										<a class="btn btn-success estado" data-toggle="modal" data-target="#editModal" data-id="<?php echo $linea['ID_MOTIVO']; ?>">		
											<i class="halflings-icon white edit"></i>  
										</a>

										<a class="btn btn-danger estado" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $linea['ID_MOTIVO']; ?>">		
											<i class="halflings-icon white trash"></i> 
										</a>

									<?php }else{ ?>
                                                                                <a class="btn btn-group estado" title="Habilitar"  data-toggle="modal" data-target="#habilitarModal" data-id="<?php echo $linea['ID_MOTIVO']; ?>">		
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
			<h2><i class="icon-edit"></i> Editar Motivo</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>

	<div class="clearfix"></div>

	<div class="modal hide fade" id="viewModal">
		<div class="modal-header btn-info">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> Consultar Motivo</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>	

	<div class="clearfix"></div>

	<div class="modal hide fade" id="deleteModal">
		<div class="modal-header btn-danger">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> ¡Eliminar Motivo!</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>
        
        <div class="clearfix"></div>
        <div class="modal hide fade" id="habilitarModal">
		<div class="modal-header btn-info">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> ¿Habilitar de nuevo el Motivo de Bloqueo?</h2>
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
            url: "motivosBloqueo_edit.php",
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
            url: "motivosBloqueo_view.php",
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
            url: "motivosBloqueo_delete.php",
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
            url: "motivosBloqueo_habilitar.php",
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

function fetch_select(val)
{
 $.ajax({
    type: 'post',
    url: 'seleccionSubactividad.php',
 data: {
    get_option:val
 },
    
    success: function (response) {
    $("#id_SubActividad").html(response);
 }
 });
}


</script>