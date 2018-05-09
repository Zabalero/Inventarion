<?php
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 

	require_once "inc/theme.inc";
	require "inc/funciones.inc";
	require "inc/funcionesCambiarEstado.inc";
	require "inc/funcionesModificar.inc";
	require "inc/funcionesImportar.inc";
      
	//Conectar con el servidor de base de datos
	$conn=conectar_bd();

	
	// Si ya hemos introducido valores para gestionar la tarea
    if($_SERVER['REQUEST_METHOD']=='POST') {  

 		//Funcion subir archivo de la tarea
		if (isset($_REQUEST['importarArchivos'])){
			$mensaje = importarArchivos($conn);
		}		
 
    } else {
    	$mensaje = "IMPORTANTE: LOS FICHERO PROCESADOS POR ESTE MODULO DEBEN ESTAR EN EL FORMATO SOLICITADO (CSV) Y SU ESTRUCTURA DEBE SER LA ESTABLECIDA INICIALMENTE";
    }

	// print the page header
	print_theme_header();


?>
			<!-- start: Content -->
		<div id="content" class="span12">
			
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
					<a href="index.html">Home</a> 
					<i class="icon-angle-right"></i>
				</li>
				<li><a href="#">Importar</a></li>
			</ul>

			<!--FORMULARIO-->
			<form method="post" action="importarOrigenDatos.php" role="form" enctype="multipart/form-data">

				<fieldset>    
				<!--DETALLE TAREA-->

				<div class="row-fluid">
					<div class="control-group form-group">
						<table class="control-group form-group">

							<tbody>
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> informeSeguimiento_RA (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="informeSeguimiento_RA" type="file"></input>
									</td>
									<td class="alert alert-info"></td>
								</tr>
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> InformeMasterRD (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="InformeMasterRD" type="file"></input>
									</td>
									<td class="alert alert-info"></td>
								</tr>
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> InformeMasterRDTESA (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="InformeMasterRDTESA" type="file"></input>
									</td>
									<td class="alert alert-info"></td>
								</tr>																								
								<tr class="controls" style="padding-top:15px;">
									<td class="controls"><i class="halflings-icon file"></i> GD_informe_RA (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="GD_informe_RA" type="file"></input>
									</td>
									<td class="alert alert-info"></td>
								</tr>
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> GD_informe_RD_JAZZTEL (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="GD_informe_RD_JAZZTEL" type="file"></input>
									</td>
									<td class="alert alert-info"></td>
								</tr>
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> GD_informe_RD_TESA (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="GD_informe_RD_TESA" type="file"></input>
									</td>
									<td class="alert alert-info"></td>
								</tr>
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> informeSeguimiento_RD_ICX (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="informeSeguimiento_RD_ICX" type="file"></input>
									</td>
									<td class="alert alert-info"></td>
								</tr>
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> Estado_ICX_General (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="Estado_ICX_General" type="file"></input>
									</td>
									<td class="alert alert-info">Z:\CalidadAceptacionRed\ENTREGA\02_PUESTA_EN_COBERTURA</td>
								</tr>			
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> FTTH_ACTUACIONES_FIR (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="FTTH_ACTUACIONES_FIR" type="file"></input>
									</td>
									<td class="alert alert-info">\\pnaspom2\ftth$\I&amp;M\Procesos\Datawarehouse\FIR\1_Trazado_ctos_por_actuacion\ftth_actuaciones_fir.csv</td>
								</tr>											
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> FTTH_CTO_LISTADO (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="FTTH_CTO_LISTADO" type="file"></input>
									</td>
									<td class="alert alert-info">\\pnaspom2\ftth$\I&amp;M\Procesos\Datawarehouse\FIR\6_ffth_cto_listado\ftth_cto_listado.csv</td>
								</tr>		
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> FTTH_GA_PROVINCIAS (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="FTTH_GA_PROVINCIAS" type="file"></input>
									</td>
									<td class="alert alert-info"></td>
								</tr>	
								
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> FTTH_CTO_BLOQUEADAS (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="FTTH_CTO_BLOQUEADAS" type="file"></input>
									</td>
									<td class="alert alert-info">\\pnaspom2\ftth$\I&amp;M\Procesos\Datawarehouse\FIR\3_ftth_cto_bloqueadas\ftth_cto_bloqueadas.csv</td>
								</tr>								
								<tr class="controls">
									<td class="controls"><i class="halflings-icon file"></i> FTTH_GESCAL_BLOQUEADO (CSV)</td>
									<td class="controls">
										<input style="width: 100%;" name="FTTH_GESCAL_BLOQUEADO" type="file"></input>
									</td>
									<td class="alert alert-info">\\pnaspom2\ftth$\I&amp;M\Procesos\Datawarehouse\FIR\7_ftth_gescal_bloqueado\ftth_gescal_bloqueado.csv</td>
								</tr>																	
																		
							</tbody>
						</table>
					</div>
					
					<div class="control-group form-group">	
						<div class="controls">
							<button onclick="return confirmarSubir();" type="submit" name="importarArchivos" value="importarArchivos" class="btn btn-info btn-small importarArchivos"><i class="halflings-icon white upload"></i> Imporar Archivos</button>
						</div>	
					</div>						
				</div>	

				<div class="row-fluid">
					<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<?php echo $mensaje;?>
					</div>					
				</div>						

				</fieldset>  
				<!--FIN DETALLE-->
			</form>    
			<!--FIN FILTROS-->  

 	    </div><!--/#content.span10-->
            
    </div><!--/row-->

</div><!--/.fluid-container-->
   
    <!-- Modal Editar Grupo-->
		

<div class="clearfix"></div>	
	
<?php
	print_theme_footer();
?>