<?php

// $Id$


// Print the page header
function print_theme_header()
{

  //global $empresa_seleccionada;
  
  //$page = basename($PHP_SELF, ".php");
  //$user = getUserName();
  //$is_admin = (authGetUserLevel($user) >= $max_level);
  
  // Need to set the timezone before we can use date()
$restriccion=get_restriccion($_SESSION['usuario']);

$rolUsuario=get_rol($_SESSION['usuario']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  
  <!-- start: Meta -->
  <meta charset="utf-8">
  <title>Inventario</title>
  <meta name="description" content="Inventario">
  <meta name="author" content="Eurocontrol">
  <meta name="keyword" content="">
  <!-- end: Meta -->
  
  <!-- start: Mobile Specific -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- end: Mobile Specific -->
  
  <!-- start: CSS -->
  <link id="bootstrap-style" href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-responsive.min.css" rel="stylesheet">
  <link id="base-style" href="css/style.css" rel="stylesheet">
  <link id="base-style-responsive" href="css/style-responsive.css" rel="stylesheet">
  <link href="css/inventario.css" rel="stylesheet">
  <link href="css/bootstrapValidator.min.css" rel="stylesheet"></link>
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,latin-ext' rel='stylesheet' type='text/css'>
  <!-- end: CSS -->
  

  <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <link id="ie-style" href="css/ie.css" rel="stylesheet">
  <![endif]-->
  
  <!--[if IE 9]>
    <link id="ie9style" href="css/ie9.css" rel="stylesheet">
  <![endif]-->
    
  <!-- start: Favicon -->
  <link rel="shortcut icon" href="img/favicon.ico">
  <!-- end: Favicon -->
  
    
</head>

<body>
    <!-- start: Header -->

  <div class="navbar">
    <div class="navbar-inner">
      <div class="container-fluid">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="index.php"><span><img src="img/logo.jpg" alt="Avatar"></span></a>
                
        <!-- start: Header Menu -->
        <?php if (es_usuario($_SESSION['usuario'],$_SESSION['password'])) { ?>
        <div class="nav-no-collapse header-nav">
          <ul class="nav pull-right">

            <li class="dropdown">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                 Tareas
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li class="dropdown-menu-title">
                  <span>Tareas</span>
                </li>
                <li><a href="insertarTarea.php"><i class="halflings-icon file"></i> Insertar Tarea</a></li>                
                <li><a href="importarExcelTareas.php"><i class="halflings-icon list-alt"></i> Importar Tareas</a></li>
              </ul>
            </li>


            <li class="dropdown">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                 Búsquedas
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li class="dropdown-menu-title">
                  <span>Búsquedas</span>
                </li>
                <?php if ($rolUsuario != 'lectura') { ?>
                <li>
                  <a href="GIS_buscar_estructura.php"><i class="halflings-icon file"></i> Carga Inventario</a>
                </li>
                <?php } ?>
                <li><a href="buscar.php"><i class="halflings-icon file"></i> Activ./Incid.</a></li>
              </ul>
            </li>


            <?php if ($rolUsuario != 'lectura') { ?>
            <li class="dropdown">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                 Empresas Colaboradoras
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li class="dropdown-menu-title">
                  <span>Empresas Colaboradoras</span>
                </li>
                <li><a href="GIS_actualiza_estados.php"><i class="halflings-icon file"></i> Actualizar Estados RA/RD</a></li>
                <li><a href="buscar_pendientes_RA_RD_ICX.php"><i class="halflings-icon file"></i> Procesar Entrega</a></li>
              </ul>
            </li>
            <?php } ?>

            <li class="dropdown">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                 Informes
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li class="dropdown-menu-title">
                  <span>Descarga de  informes</span>
                </li>
                <li><a href="scripts/excelExport.php?origen=Informe_general"><i class="halflings-icon file"></i>Informe general</a></li>
                <li><a href="scripts/excelExport.php?origen=Saturadas"><i class="halflings-icon file"></i>Saturadas</a></li>
                <li><a href="scripts/excelExport.php?origen=Bloqueos/Desbloqueos"><i class="halflings-icon file"></i>Bloqueos Desbloqueos</a></li>
              </ul>
            </li>





            <?php if ($rolUsuario == 'avanzado') { ?>
            <li class="dropdown">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="halflings-icon white wrench"></i>
              </a>
             <ul class="dropdown-menu">
                <li class="dropdown-menu-title">
                  <span>Gestión</span>
                </li>
                <li><a href="actividades_insert.php"><i class="halflings-icon file"></i> Actividades</a></li>
                <li><a style="margin-left:18px;" href="subactividades_insert.php"><i class="halflings-icon th-list"></i> SubActividades</a></li>                   
                <li><a href="estadosTareas_insert.php"><i class="halflings-icon tasks"></i> Estados Tareas</a></li>
                <li><a href="usuarios_insert.php"><i class="halflings-icon user"></i> Usuarios</a></li>
              </ul>

            </li>
            <?php } ?>

            <!-- start: User Dropdown -->
            <li class="dropdown">
              <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="halflings-icon white user"></i> <?php echo get_nombre($_SESSION['usuario']);?>
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li class="dropdown-menu-title">
                  <span>Usuario</span>
                </li>
                <li>
                    <a href="logout.php"><i class="halflings-icon off"></i> Desconexión</a>
                </li>
              </ul>
            </li>
            <!-- end: User Dropdown -->
          </ul>
        </div>
        <?php } else { ?>
        <div class="nav-no-collapse header-nav">
          <ul class="nav pull-right">     
            <li class="dropdown">
              <ul class="dropdown-menu">
                <li class="dropdown-menu-title">
                  <span>Usuario</span>
                </li>                
                <li>
                    <a href="index.php"><i class="halflings-icon off"></i> Acceso</a>
                </li>              
              </ul>
            </li>
            <!-- end: User Dropdown -->
          </ul>
        </div>
        <?php } ?>
        <!-- end: Header Menu -->
        
      </div>
    </div>
  </div>
  <!-- start: Header -->
  
    <div class="container-fluid-full">
    <div class="row-fluid">
        

      
<?php

}
// end of print_theme_header()

function print_theme_footer()
{
?>

  
  <div class="clearfix">
  </div>

  <footer>

    <p>
      <span style="text-align:left;float:left">&copy; 2017 Inventario</span>
      
    </p>

  </footer>
  
  <!-- start: JavaScript-->

    <script src="js/jquery-1.9.1.min.js"></script>
  <script src="js/jquery-migrate-1.0.0.min.js"></script>
  
    <script src="js/jquery-ui-1.10.0.custom.min.js"></script>
  
    <script src="js/jquery.ui.touch-punch.js"></script>
  
    <script src="js/modernizr.js"></script>
  
    <!--<script src="js/bootstrap.min.js"></script>-->
    <script src="js/bootstrap.js"></script>
  
    <script src="js/jquery.cookie.js"></script>
  
    <script src='js/fullcalendar.min.js'></script>
  
    <script src='js/jquery.dataTables.min.js'></script>

    <script src="js/excanvas.js"></script>
  <script src="js/jquery.flot.js"></script>
  <script src="js/jquery.flot.pie.js"></script>
  <script src="js/jquery.flot.stack.js"></script>
  <script src="js/jquery.flot.resize.min.js"></script>
  
    <script src="js/jquery.chosen.min.js"></script>
  
    <script src="js/jquery.uniform.min.js"></script>
    
    <script src="js/jquery.cleditor.min.js"></script>
  
    <script src="js/jquery.noty.js"></script>
  
    <script src="js/jquery.elfinder.min.js"></script>
  
    <script src="js/jquery.raty.min.js"></script>
  
    <script src="js/jquery.iphone.toggle.js"></script>
  
    <script src="js/jquery.uploadify-3.1.min.js"></script>
  
    <script src="js/jquery.gritter.min.js"></script>
  
    <script src="js/jquery.imagesloaded.js"></script>
  
    <script src="js/jquery.masonry.min.js"></script>
  
    <script src="js/jquery.knob.modified.js"></script>
  
    <script src="js/jquery.sparkline.min.js"></script>
  
    <script src="js/counter.js"></script>
  
    <script src="js/retina.js"></script>

    




    <script src="js/custom.js"></script>
    <!--<script src="js/transaccion.js"></script>-->
    <script language="JavaScript" type="text/javascript" src="js/ajax.js?ver=1.0"></script>
    <script src="js/main.js"></script> <!-- Resource jQuery -->
    <script src="js/bootstrapValidator.min.js"></script>

    <script>
      $.datepicker.regional['es'] = {
          closeText: 'Cerrar',
          prevText: '<Ant',
          nextText: 'Sig>',
          currentText: 'Hoy',
          monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
          monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
          dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
          dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
          dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
          weekHeader: 'Sm',
          dateFormat: 'yyyy-mm-dd',
          firstDay: 1,
          isRTL: false,
          showMonthAfterYear: false,
          yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);

    </script>

  <!-- end: JavaScript-->
  
</body>

<?php
}
// end of print_theme_header()
