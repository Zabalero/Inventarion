<?php
//require_once "mrbs_sql.inc";



function inicializar_variables_buscar()
{
    global $seleccionadoRef = "";
    global $seleccionadoProv = "";
    global $seleccionadoReg = "";
    global $seleccionadoGestor = "";
    global $seleccionadoCab = "";
    global $seleccionadoIdGd = "";
    global $seleccionadoActTesa = "";
    global $seleccionadoInc = "";
    global $seleccionadoActJazz = "";
    global $seleccionadoEst = "";
    global $seleccionadoTecn = "";
    global $seleccionadoTecnAsign = "";
    global $seleccionadoActiv = "";
    global $seleccionadoOrigenSol = "";
    global $seleccionadofRegistro1 = "";
    global $seleccionadofRegistro2 = "";
    global $seleccionadofIni1 = "";
    global $seleccionadofIni2 = "";    
    global $seleccionadofResol1 = "";
    global $seleccionadofResol2 = "";    
    global $seleccionadoPrior = "";
    global $seleccionadoOceane="";
    global $seleccionadoCTO="";
      
// Si ya hemos introducido valores para filtros de búsqueda
    if($_SERVER['REQUEST_METHOD']=='POST')
    {  
      
        $seleccionadoRef = $_REQUEST['ref'];
        $seleccionadoGestor = $_REQUEST['gestor'];
        $seleccionadoProv = $_REQUEST['provincia'];
        $seleccionadoReg = $_REQUEST['region'];
        $seleccionadoCab = $_REQUEST['cabecera'];
        $seleccionadoIdGd = $_REQUEST['idgd'];
        $seleccionadoActTesa = $_REQUEST['acttesa'];    
        $seleccionadoInc= $_REQUEST['incidencia'];
        $seleccionadoActJazz = $_REQUEST['actjazz'];
        $seleccionadoEst = $_REQUEST['estado'];
        $seleccionadoTecn = $_REQUEST['tecnico'];
        $seleccionadoTecnAsign = $_REQUEST['tecnicoAsign'];
        $seleccionadoActiv = $_REQUEST['actividad'];
        $seleccionadoOrigenSol= $_REQUEST['origenSol'];
        $seleccionadofRegistro1 = $_REQUEST['fRegistro1'];
        $seleccionadofRegistro2 = $_REQUEST['fRegistro2'];
        $seleccionadofIni1 = $_REQUEST['fInicio1'];
        $seleccionadofIni2 = $_REQUEST['fInicio2'];
        $seleccionadofResol1 = $_REQUEST['fResol1'];
        $seleccionadofResol2 = $_REQUEST['fResol2'];
        $seleccionadoPrior = $_REQUEST['prior'];
        $seleccionadoOrden=$_REQUEST['orden'];
        $seleccionadoestado2=$_POST["estado2"]; 
        $seleccionadoOceane= $_REQUEST['oceane'];
        $seleccionadoCTO= $_REQUEST['CTO'];
        
    }
  
  // Obtener valores introducidos en el formulario
    global $ref=  $_REQUEST['ref'];
    global $gestor = $_REQUEST['gestor'];
    global $provincia = $_REQUEST['provincia'];
    global $region = $_REQUEST['region'];
    global $cabecera = $_REQUEST['cabecera'];
    global $idgd = $_REQUEST['idgd'];
    global $acttesa = $_REQUEST['acttesa'];
    global $incidencia = $_REQUEST['incidencia'];
    global $actjazz = $_REQUEST['actjazz'];
    global $estado = $_REQUEST['estado'];
    global $tecnico = $_REQUEST['tecnico'];
    global $actividad = $_REQUEST['actividad'];
    global $subactividad = $_REQUEST['subactividad'];
    global $origenSol = $_REQUEST['origenSol'];
    global $fRegistro1 = $_REQUEST['fRegistro1'];
    global $fRegistro2 = $_REQUEST['fRegistro2'];
    global $fInicio1 = $_REQUEST['fInicio1'];
    global $fInicio2 = $_REQUEST['fInicio2'];
    global $fResol1 = $_REQUEST['fResol1'];
    global $fResol2 = $_REQUEST['fResol2'];        
    global $prior = $_REQUEST['prior']; 
    global $orden=$_REQUEST['orden']; 
    global $estado2=$_POST["estado2"]; 
    global $oceane= $_REQUEST['oceane'];
    global $CTO = $_REQUEST['CTO'];
} 

