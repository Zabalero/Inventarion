<?php
        //*** Javier Fernández ***
        //*** 08/08/2017 ***
        
	session_start();
	header("Cache-control: private");
	$_SESSION['detalle']="TRUE"; 
	//echo "string"; esto estaba antes en actividades, no sé porque, lo comento

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
            
            
     
           
          if(!empty($_POST['check_list'])){
                foreach($_POST['check_list'] as $selected){
                    $restricciones = $restricciones . $selected . ';' ;
                }
                $restricciones= substr($restricciones,0, strlen($restricciones)-1);
             }  
                                           
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
            $password = $_POST['password_new'];

	    $tsql = "INSERT INTO [dbo].[INV_tbUSUARIOS]
                        ([idGrupo]
                        ,[idDpto]
                        ,[idRol]
                        ,[nombre]
                        ,[usuario]
                        ,[mail]
                        ,[restricciones]
                        ,[Inhabilitado]
                        ,[idResponsable]
                        ,[Telefono]
                        ,[password])
                  VALUES
                        ({$idGrupo}
                        ,{$idDpto}
                        ,{$idRol}
                        ,'{$nombre}'
                        ,'{$usuario}'
                        ,'{$mail}'
                        ,'{$restricciones}'
                        ,{$Inhabilitado}
                        ,{$idResponsable}
                        ,'{$Telefono}'"
                        . ",'{$password}')";


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

	$tsql = "SELECT INV_tbUSUARIOS.*, 
                    INV_tbDptos.dpto, 
                    tbusers2.nombre as Responsable,
                    INV_tbRoles.rol,
                    INV_tbGrupos.grupo
                    FROM INV_tbUSUARIOS 
                    left join INV_tbDptos on INV_tbUSUARIOS.idDpto=INV_tbDptos.id_dpto
                    inner join INV_tbUSUARIOS as tbusers2 on INV_tbUSUARIOS.idResponsable=tbusers2.id_usu
                    left join INV_tbRoles on INV_tbUSUARIOS.idRol=INV_tbRoles.id_rol
                    left join INV_tbGrupos on INV_tbUSUARIOS.idGrupo=INV_tbGrupos.id_grupo";
	$registros = sqlsrv_query($conn, $tsql, array(), array( "Scrollable" => 'static' ));
        
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
				<li><a href="#">Gestión de Usuarios</a></li>
			</ul>


                    <!-- Formulario alta -->
	            <div class="row-fluid">
	            
		            <div class="box span12">
						<div class="box-header" data-original-title>
								<h2><i class="halflings-icon pencil"></i><span class="break"></span>Nuevo Usuario </h2>
								<div class="box-icon">
									<a href="#" data-toggle="modal" data-target="#insertModal" ><i class="halflings-icon plus"></i></a>
								</div>
						</div>
						<!-- Modal Insertar Usuario-->
						<div id="insertModal" class="modal hide fade" role="dialog">
						  <div class="modal-dialog">

						    <!-- Modal content-->
						    <div class="modal-content">
						      <div class="modal-header btn-primary">
								<button type="button" class="close" data-dismiss="modal">×</button>
								<h2><i class="icon-edit"></i> Insertar Usuario</h2>
						      </div>
						      <div class="modal-body">

				              	<div class="box-content">
					              
					              <form method="post" name="formInsert" id="formInsert" autocomplete="off" action="<?php echo $editFormAction; ?>">
					              <fieldset>
                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Usuario*: </strong>
                                                                <input type="text" name="usuario_new" required id="usuario_new" maxlength="10" value=" " style="width:70%;" class="form-control" >
                                                            </div>
                                                        </div>	
                                                          
                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Password*: </strong>
                                                                <input type="password" name="password_new" required id="password_new" maxlength="10" value="" style="width:70%;" class="form-control">
                                                            </div>
                                                        </div>	

                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Nombre*: </strong>
                                                                <input type="text" name="nombre" required id="nombre" maxlength="50" value="" style="width:70%;" class="form-control" size="50" >
                                                            </div>
                                                        </div>		      

                                                        <div class="control-group form-group">
                                                                <div class="controls">
                                                                        <strong>Mail*: </strong><input type="text" required name="mail" maxlength="50" style="width:70%;" class="form-control" value="" size="50" >
                                                                </div>
                                                        </div>

                                                        <div class="control-group form-group">
                                                            <div class="controls"> 
                                                                <strong>Teléfono: </strong><input name="Telefono" maxlength="15" type="text" style="width:70%;" class="form-control" value="">
                                                            </div>
                                                        </div>

                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Departamento*: </strong>
                                                                <SELECT name="idDpto" style="width:70%;" required>	
                                                                    <?php if (isset($dptos)) { while ($lineaDptos = sqlsrv_fetch_array($dptos)){ ?>
                                                                        <option value="<?php echo $lineaDptos['id_dpto'];?>"><?php echo $lineaDptos['dpto'];?></option>
                                                                    <?php } } ?>
                                                                </SELECT>
                                                            </div>
                                                        </div>

                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Grupo*: </strong>
                                                                <SELECT name="idGrupo" style="width:70%;" required>	
                                                                    <?php if (isset($grupos)) { while ($lineaGrupos = sqlsrv_fetch_array($grupos)){ ?>
                                                                        <option value="<?php echo $lineaGrupos['id_grupo'];?>"><?php echo $lineaGrupos['grupo'];?></option>
                                                                    <?php } } ?>
                                                                </SELECT>                    	
                                                            </div>
                                                        </div>

                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Responsable*: </strong>
                                                                <SELECT name="idResponsable" style="width:70%;" required>	
                                                                    <option selected value=""></option>
                                                                    <?php if (isset($responsables)) { while ($lineaResponsables = sqlsrv_fetch_array($responsables)){ ?>
                                                                        <option value="<?php echo $lineaResponsables['id_usu'];?>"><?php echo $lineaResponsables['nombre'];?></option>
                                                                    <?php } } ?>
                                                                </SELECT>    
                                                            </div>
                                                        </div>

                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Permisos*: </strong>
                                                                <SELECT name="idRol" style="width:70%;" required>	
                                                                    <?php if (isset($roles)) { while ($lineaRoles = sqlsrv_fetch_array($roles)){ ?>
                                                                        <option value="<?php echo $lineaRoles['id_rol'];?>"><?php echo $lineaRoles['rol'];?></option>
                                                                    <?php } } ?>
                                                                </SELECT> 
                                                            </div>
                                                        </div>

                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Restricción región*: </strong>
                                                             <table>
                                                                 <tr>

                                                                     <td style="text-align:left"><input type="checkbox" name="check_list[]"  value="Andalucia Occidental" onclick = "seleccionTodas()" checked="checked"><span class="input-group-addon">Andalucia Occidental  </span></td>
                                                                     <td style="text-align:left"><input type="checkbox" name="check_list[]"  value="Andalucia Oriental" onclick = "seleccionTodas()" ><span class="input-group-addon">Andalucia Oriental  </span></td>
                                                                     <td style="text-align:left"><input type="checkbox" name="check_list[]"  value="Cataluña" onclick = "seleccionTodas()"><span class="input-group-addon">Cataluña  </span></td>                                
                                                                 </tr> 
                                                                 <tr>

                                                                     <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Centro" onclick = "seleccionTodas()" ><span style="text-align:right" class="input-group-addon">Centro</span></td>
                                                                     <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Levante" onclick = "seleccionTodas()" ><span class="input-group-addon" >Levante</span></td>
                                                                     <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Canarias" onclick = "seleccionTodas()" ><span class="input-group-addon">Canarias</span></td>
                                                                  </tr> 
                                                                  <tr>
                                                                     <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Noroeste" onclick = "seleccionTodas()"><span class="input-group-addon">Noroeste</span></td>
                                                                     <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 1" onclick = "seleccionTodas()"> <span class="input-group-addon">Zona 1</span></td>
                                                                     <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 2" onclick = "seleccionTodas()"> <span class="input-group-addon">Zona 2</span></td>
                                                                  </tr>
                                                                  <tr>
                                                                    <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 3" onclick = "seleccionTodas()" ><span class="input-group-addon">Zona 3</span></td>
                                                                    <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 4" onclick = "seleccionTodas()"><span class="input-group-addon">Zona 4</span></td>
                                                                    <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Zona 5" onclick = "seleccionTodas()"><span class="input-group-addon">Zona 5</span></td>
                                                                 </tr>
                                                                 <tr>
                                                                    <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Todas" onclick = "seleccionTodas() " > <span class="input-group-addon">Todas</span></td>
                                                                    <td style="text-align:left"><input type="checkbox" name="check_list[]" value="Ninguna" onclick = "seleccionTodas()"><span class="input-group-addon">Ninguna</span></td>
                                                                 </tr>

                                                             </table>
                                                            </div>
                                                        </div>


                                                  
                                                        <div class="control-group form-group">
                                                            <div class="controls"> <strong>Inhabilitado*: </strong>
                                                                <SELECT name="Inhabilitado" style="width:70%;" required>	
                                                                        <option value="0" >Activo</option>
                                                                        <option value="1" >Inactivo</option>
                                                                </SELECT> 
                                                            </div>
                                                        </div>


                                                        </fieldset>
					     
					                <div class="control-group">
					                    <div class="controls">
					                    	<input type="submit" value="Insertar" class="btn btn-primary">
					                    	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					                	</div>
					                </div>
					                <input type="hidden" name="MM_insert" value="formInsert">
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
						<h2><i class="halflings-icon user"></i><span class="break"></span>Listado de Usuarios</h2>
						<div class="box-icon">
							
							<a href="#" class="btn-minimize"><i class="halflings-icon chevron-up"></i></a>
							
						</div>
					</div>
					<div class="box-content">
						<table class="cell-border datatable2" width="100%" cellspacing="0">
						  <thead>
							  <tr>
								  <th>USUARIO</th>
								  <th>NOMBRE</th>
                                                                  <th>MAIL</th>
								  <th>TELÉFONO</th>
								  <th>DEPARTAMENTO</th>
								  <th>GRUPO</th>
								  <th>RESPONSABLE</th>
                                                                  <th>PERMISOS</th>
                                                                  <th>RESTRICCIÓN<br/>REGIÓN</th>
                                                                  <th>INHABILITADO</th>
                                                                  <th></th>
							  </tr>
						  </thead>   
						  <tbody>
                          <?php if (isset($registros)) { while ($linea = sqlsrv_fetch_array($registros)){ ?>
							<tr>
								<td class="center"><?php echo $linea['usuario']; ?></td>
								<td class="center"><?php echo $linea['nombre']; ?></td>
                                                                <td class="center"><?php echo $linea['mail']; ?></td>
                                                                <td class="center"><?php echo $linea['Telefono']; ?></td>
                                                                <td class="center"><?php echo $linea['dpto']; ?></td>
                                                                <td class="center"><?php echo $linea['grupo']; ?></td>
                                                                <td class="center"><?php echo $linea['Responsable']; ?></td>
                                                                <td class="center"><?php echo $linea['rol']; ?></td>
                                                                <td class="center"><?php echo $linea['restricciones']; ?></td>
                                                                <td class="center"><?php 
                                                                                        if ($linea['Inhabilitado']==1) {
                                                                                            echo ("Inactivo");
                                                                                        } else {
                                                                                            echo ("Activo");
                                                                                        }
                                                                                    ?>
                                                                </td>
                                                                
								<td class="center">
									<a class="btn btn-info usuarios" data-toggle="modal" data-target="#viewModal" data-id="<?php echo $linea['id_usu']; ?>">		
										<i class="halflings-icon white zoom-in"></i>  
									</a>
                                                                        <a class="btn btn-success usuarios" data-toggle="modal" data-target="#editModal" data-id="<?php echo $linea['id_usu']; ?>">		
                                                                                <i class="halflings-icon white edit"></i>  
                                                                        </a>

                                                                        <a class="btn btn-danger usuarios" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $linea['id_usu']; ?>">		
                                                                                <i class="halflings-icon white trash"></i> 
                                                                        </a>

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
   
    <!-- Modal Editar Usuario-->
		
	<div class="modal hide fade" id="editModal">
		<div class="modal-header btn-success">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-edit"></i> Editar Usuario</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>

	<div class="clearfix"></div>

	<div class="modal hide fade" id="viewModal">
		<div class="modal-header btn-info">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-search"></i> Consultar Usuario</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>	

	<div class="clearfix"></div>

	<div class="modal hide fade" id="deleteModal">
		<div class="modal-header btn-danger">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h2><i class="icon-asterisk"></i> ¡Eliminar Usuario!</h2>
		</div>
        <div class="ct">
      
        </div>
	</div>	

	<div class="clearfix"></div>	
	
<?php
	print_theme_footer();
?>
<script>
// Botón Ver usuario        
$(".btn-info.usuarios").click(function(){
    var button = $(this); // Button that triggered the modal
    var idSelect = button.data('id'); // Extract info from data-* attributes
    var modal = button.data('target');
    var dataString = 'id=' + idSelect;

      $.ajax({
          type: "GET",
          url: "usuarios_view.php",
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

//Botón eliminar usuario
$(".btn-danger.usuarios").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');

      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "usuarios_delete.php",
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
//Botón editar usuario
$(".btn-success.usuarios").click(function(){
      var button = $(this); // Button that triggered the modal
      var idSelect = button.data('id'); // Extract info from data-* attributes
      var modal = button.data('target');
      var dataString = 'id=' + idSelect;

        $.ajax({
            type: "GET",
            url: "usuarios_edit.php",
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