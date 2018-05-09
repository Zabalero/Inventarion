<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";

	//Inicializa las variables utilizadas en el formulario
 
	$id = "";    

      
	// Si ya hemos introducido valores para filtros de búsqueda
    if($_SERVER['REQUEST_METHOD']=='POST')
    {  
      
        $id = $_REQUEST['id'];  
        
    }
  


    //FIN Inicializa las variables utilizadas en el formulario

	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	//Variables para la búsqueda
	$maxRows = 10;
	$pageNum = 0;
	$seleccionada = 0;
	if (isset($_GET['pageNum'])) {
	  $pageNum = $_GET['pageNum'];
	}
	$startRow = $pageNum * $maxRows;


	if($_SERVER['REQUEST_METHOD']=='POST') {
		if ($_POST['buscar']) {
			$tsql = "SELECT * 
					FROM INV_VIEW_RD_TODO
					WHERE			 
						ID_GD LIKE '%".$id."%' OR 
						Id_FDTT LIKE '%".$id."%' OR 
						ID_ACTUACION LIKE '%".$id."%' OR
						CABECERA LIKE '%".$id."%' OR
						ARBOL LIKE '%".$id."%' OR
						ACTUACION_JAZZTEL_FDTT LIKE '%".$id."%' OR
						ACTUACION_JAZZTEL LIKE '%".$id."%' OR
						ACTUACION_TESA LIKE '%".$id."%' OR
						ACTUACION LIKE '%".$id."%' OR
						ID_ACTUACION_RA LIKE '%".$id."%' OR
						ID_FDTT_RA LIKE '%".$id."%' OR
						ID_GD_RA LIKE '%".$id."%'";
			//Recuperar datos de consulta
			$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));

		}
	}


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
				<li><a href="#">Buscar Estructura</a></li>
			</ul>

			<!--FILTROS-->
			<FORM id="busqueda" autocomplete="off" METHOD="POST" NAME="opciones"  class="form-horizontal">
				<fieldset>
		    	<div class="row-fluid">

					<div class="span3">
					
						<div class="control-group">
							<label class="control-label" for="origenSol">Buscar: </label>
							<div class="controls">
								
								<input type="text" id="id" name="id" value="<?php echo $id;?>"/>

							</div>	
						</div>		

					 </div>	
				</div>

					<INPUT TYPE="submit" class="btn btn-primary" NAME="buscar" id = "buscar" VALUE="Buscar" onclick = "this.form.action = 'buscar_estructura.php'">  

				 </fieldset>	

			</FORM>       
			<!--FIN FILTROS-->  


            <!-- Tabla de listado de grupos existentes -->
            
            <div class="row-fluid sortable ui-sortable">		
				<div class="box span12">
					<div class="box-header" data-original-title>
						<h2><i class="halflings-icon user"></i><span class="break"></span>Listado</h2>
						<div class="box-icon">
							
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							
						</div>
					</div>
					<div class="box-content">
						<table class="table table-striped table-bordered bootstrap-datatable datatable buscar">
						  <thead>
							  <tr>
								  <th>REG.</th>
								  <th>PROV.</th>
								  <th>CABEC.</th>
								  <th>ARBOL</th>
								  <th>ACT_JAZZTEL_FDTT</th>
								  <th>ACT_JAZZTEL</th>
								  <th>AC_TESA</th>
								  <th class="hidden">CABECERA</th>
								  <th class="hidden">ID_ACTUACION_RA</th>
								  <th class="hidden">ID_ACTUACION</th>

								  <th>ACCION</th>
							  </tr>
						  </thead>   
						  <tbody>

                          <?php if (isset($registros)) { while ($linea = sqlsrv_fetch_array($registros)){ ?>
							<tr>
								<td class="center"><?php echo $linea['REGION']; ?></td>
								<td class="center"><?php echo $linea['PROVINCIA']; ?></td>
								<td class="center"><?php echo $linea['CABECERA']; ?></td>
								<td class="center"><?php echo $linea['ARBOL']; ?></td>
								<td class="center"><?php echo $linea['ACTUACION_JAZZTEL_FDTT']; ?></td>
								<td class="center"><?php echo $linea['ACTUACION_JAZZTEL']; ?></td>
								<td class="center"><?php echo $linea['ACTUACION_TESA']; ?></td>
								<td class="hidden"><?php echo $linea['CABECERA']; ?></td>
								<td class="hidden"><?php echo $linea['ID_ACTUACION_RA']; ?></td>
								<td class="hidden"><?php echo $linea['ID_ACTUACION']; ?></td>
								

								<td class="center">
									<a title="Detalle RA-RD" class="btn btn-info btn-mini buscar_ra_rd" data-toggle="modal" data-target="#viewModalR" data-id="<?php echo $linea['ID_ACTUACION']; ?>">		
										<i class="halflings-icon white zoom-in"></i>  
									</a>
									
									<a title="Consulta TAREAS CABECERA" class="btn btn-primary btn-mini buscar_tarea_CABECERA" href="buscar.php?id_cab=<?php echo $linea['CABECERA']; ?>">		
										<i class="halflings-icon white eye-open"></i>  
									</a>

									<a title="Consulta TAREAS RA" class="btn btn-success btn-mini buscar_tarea_RA" href="buscar.php?id_ra=<?php echo $linea['ID_ACTUACION_RA']; ?>">		
										<i class="halflings-icon white eye-open"></i>  
									</a>									

									<a title="Consulta TAREAS RD" class="btn btn-danger btn-mini buscar_tarea_RD" href="buscar.php?id_rd=<?php echo $linea['ID_ACTUACION']; ?>">		
										<i class="halflings-icon white eye-open"></i>  
									</a>	

									<a title="Masivas TAREAS CABECERA" class="btn btn-primary btn-mini buscar_tarea_CABECERA" href="buscar_masivas.php?id_cab=<?php echo $linea['CABECERA']; ?>">		
										<i class="halflings-icon white refresh"></i>  
									</a>

									<a title="Masivas TAREAS RA" class="btn btn-success btn-mini buscar_tarea_RA" href="buscar_masivas.php?id_ra=<?php echo $linea['ID_ACTUACION_RA']; ?>">		
										<i class="halflings-icon white refresh"></i>  
									</a>									

									<a title="Masivas TAREAS RD" class="btn btn-danger btn-mini buscar_tarea_RD" href="buscar_masivas.php?id_rd=<?php echo $linea['ID_ACTUACION']; ?>">		
										<i class="halflings-icon white refresh"></i>  
									</a>										
																	

								</td>
                            </tr>
							<?php } } ?>
						  </tbody>
					  </table>            
					</div>
				</div>
            
            </div>


   

	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->


<div class="modal hide fade large" id="viewModalR">
	<div class="modal-header btn-info">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h2><i class="icon-edit"></i> Consultar</h2>
	</div>
    <div class="ct">
  
    </div>
</div>	

<div class="clearfix"></div>
	
	
<?php
	print_theme_footer();
	sqlsrv_free_stmt($registros);
?>
