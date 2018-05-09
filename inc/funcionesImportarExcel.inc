<?php

function file_extension($filename)
{
    $path_info = pathinfo($filename);
    return strtolower($path_info['extension']);
}
//
function callback($buffer) {

    $buffer=(str_replace(chr(13).chr(10), '', $buffer));

    $buffer=(str_replace(chr(13), '', $buffer));

    $buffer=(str_replace(chr(10), '', $buffer));

    $buffer=(str_replace('\r\n', '', $buffer));

    $buffer=(str_replace(chr(9), '', $buffer));

    $buffer=(str_replace('  ', '', $buffer));

    return $buffer;

}
//
// ME DICE QUE DIA ES
//
function prtDay($dia) {
    switch($dia) {
        case '0':$dia="Domingo";break;
        case '1':$dia="Lunes";break;
        case '2':$dia="Martes";break;
        case '3':$dia="Mi&eacute;rcoles";break;
        case '4':$dia="Jueves";break;
        case '5':$dia="Viernes";break;
        case '6':$dia="S&aacute;bado";break;
    }
    return $dia;
}
function prtMonth($mes) {
    switch($mes) {
        case '1':$mes="Enero";break;
        case '2':$mes="Febrero";break;
        case '3':$mes="Marzo";break;
        case '4':$mes="Abril";break;
        case '5':$mes="Mayo";break;
        case '6':$mes="Junio";break;
        case '7':$mes="Julio";break;
        case '8':$mes="Agosto";break;
        case '9':$mes="Septiembre";break;
        case '10':$mes="Octubre";break;
        case '11':$mes="Noviembre";break;
        case '12':$mes="Diciembre";break;
    }
    return $mes;
}

function monthReplace($pstrfecha) {
    $strFecha = str_replace("January", "Enero", $pstrfecha);
    $strFecha = str_replace("February", "Febrero", $strFecha);
    $strFecha = str_replace("March", "Marzo", $strFecha);
    $strFecha = str_replace("April", "Abril", $strFecha);
    $strFecha = str_replace("May", "Mayo", $strFecha);
    $strFecha = str_replace("June", "Junio", $strFecha);
    $strFecha = str_replace("July", "Julio", $strFecha);
    $strFecha = str_replace("August", "Agosto", $strFecha);
    $strFecha = str_replace("September", "Septiembre", $strFecha);
    $strFecha = str_replace("October", "Octubre", $strFecha);
    $strFecha = str_replace("November", "Noviembre", $strFecha);
    $strFecha = str_replace("December", "Diciembre", $strFecha);

    return $strFecha;
}

//
// FORMATEA FECHAS SEGUN EL PARAMETRO $strFormat
//

function convertDate($strDate, $strFormat) {
    
   
        switch ($strFormat) {
            case 'DD/MM/YYYY':
                list( $year, $month, $day ) = explode( '-', $strDate );
                $strDateFormated = $day . "/" . $month . "/" . $year;
                break;

            case 'MM/DD/YYYY':
                list( $year, $month, $day ) = explode( '-', $strDate );
                $strDateFormated = $month . "/" . $day . "/" . $year;
                break;

            case 'YYYY/MM/DD':
                list( $day, $month, $year ) = explode( '/', $strDate );
                $strDateFormated = $year . "-" . $month . "-" . $day;
                break;

            default:
                break;
        }
        return ($strDateFormated == "//" || $strDateFormated=="--"?"":$strDateFormated);

}

/**
    * Función que consigue la fecha actual
    *
    * @param String $operacion (Opcional, puede pasarse día a restar o sumar<br/>
    *
    * @return  dd/mm/aaaa
    * @throws  
    * @todo    
    *
    * @since   14/12/2016
    * @author  Javier Fernández <javier.fernandez@eurocontrol-solutions.com>
    *
    * @edit    
    */
function getFechaActualddmmaaaa($operacion=""){    
    $fecha_actual=date('d/m/Y');
    if ($operacion!=""){
        $fecha_temp=strtotime ( $operacion , strtotime ( date('Y-m-j') ) );
        $fecha_actual = date ( 'd/m/Y' , $fecha_temp );
    }
    list( $day, $month, $year ) = explode( '/', $fecha_actual );    
    if (strlen($day)<=1){
        $day="0".$day;
    }
    if (strlen($month)<=1){
        $month="0".$day;
    }
    return $day . "/" . $month . "/" . $year;
}

//añadida por sofía
function now(){
      return date("Y-m-d H:i:s");
     
}
//creada por sofía
function ahora(){
      return date("d/m/Y H:i:s");     
}

function convertDateTime($strDate, $strFormat) {
    switch ($strFormat) {
        case 'DD/MM/YYYY':
            list($fecha, $hora) = explode(' ', $strDate);
            list( $year, $month, $day ) = explode( '-', $fecha );
            $strDateFormated = $day . "/" . $month . "/" . $year . " " . $hora;
            break;

        case 'MM/DD/YYYY':
            list($fecha, $hora) = explode(' ', $strDate);
            list( $year, $month, $day ) = explode( '-', $fecha );
            $strDateFormated = $month . "/" . $day . "/" . $year . " " . $hora;
            break;

        case 'YYYY/MM/DD':
            list($fecha, $hora) = explode(' ', $strDate);
            list( $day, $month, $year ) = explode( '-', $fecha );
            $strDateFormated = $month . "/" . $day . "/" . $year . " " . $hora;
            break;
        
        case 'DD/MM/YYYY NO HORA':
            list($fecha, $hora) = explode(' ', $strDate);
            list( $year, $month, $day ) = explode( '-', $fecha );
            $strDateFormated = $day . "/" . $month . "/" . $year;
            break;
        
        default:
            break;
    }

    return ($strDateFormated == "//"?"":$strDateFormated);
}
//
// FORMATEA LOS CHECKBOX
//
function getCheckboxValue($strValue) {
    if ($strValue == "on") {
        $strValue = "si";
    }
    else {
        $strValue = "no";
    }

    return $strValue;
}

/**
*
* Formateo los datos de una variable para evitar un SQL-Inyection
*
*
* @param string $pstrValue Es el string ya sea obtenido por GET o POST a formatear
*
*/
function escapeString($pstrValue){
   if(get_magic_quotes_gpc() != 0) {
   	$pstrValue = stripslashes($pstrValue);
   }
   // de forma temporal para evitar caracteres html
   $pstrValue = strip_tags($pstrValue);
   
   $pstrValue = str_replace('"', '', $pstrValue);
   
   return mysql_real_escape_string($pstrValue);
}

//
//	REEMPLAZA CARACTERES EXTRAÑOS EN LAS URL
//
function buildLink($pstrTitulo) {
    $strURL = str_replace(" ", "-", stripslashes($pstrTitulo));
    $strURL = str_replace("?", "", $strURL);
    $strURL = str_replace("/", "-", $strURL);
    $strURL = str_replace("|", "", $strURL);
    $strURL = str_replace(",", "", $strURL);
    $strURL = str_replace("!", "", $strURL);
    $strURL = str_replace("'", "", $strURL);
    $strURL = str_replace("=", "", $strURL);
    //$strURL = str_replace(".", "", $strURL);
    $strURL = str_replace(":", "", $strURL);
    $strURL = str_replace("\"", "", $strURL);
    $strURL = str_replace("á", "a", $strURL);
    $strURL = str_replace("é", "e", $strURL);
    $strURL = str_replace("í", "i", $strURL);
    $strURL = str_replace("ó", "o", $strURL);
    $strURL = str_replace("ú", "u", $strURL);
    $strURL = str_replace("ñ", "n", $strURL);
    $strURL = str_replace("Á", "A", $strURL);
    $strURL = str_replace("É", "E", $strURL);
    $strURL = str_replace("Í", "I", $strURL);
    $strURL = str_replace("Ó", "O", $strURL);
    $strURL = str_replace("Ú", "U", $strURL);
    $strURL = str_replace("Ñ", "N", $strURL);
    return $strURL;
}

//
//	DEVUELVE LA URL EXACTA
//
function requested_page() {
    $strProtocol = ((int) $_SERVER['SERVER_PORT'] === 443)? 'https://' : 'http://';
    $strCurrentPage = $strProtocol . $_SERVER['HTTP_HOST'] . ((!empty($_SERVER['REQUEST_URI']))? $_SERVER['REQUEST_URI'] : '');

    return $strCurrentPage;
}

function getRealIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
 
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
 
    return $_SERVER['REMOTE_ADDR'];
}

function url_exists($url) {
    $h = get_headers($url);
    $status = array();
    preg_match('/HTTP\/.* ([0-9]+) .*/', $h[0] , $status);
    return ($status[1] == 200);
}

function encrypt($string, $key) {
   $result = '';
   for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)+ord($keychar));
      $result.=$char;
   }
   return base64_encode($result);
}

function decrypt($string, $key) {
   $result = '';
   $string = base64_decode($string);
   for($i=0; $i<strlen($string); $i++) {
      $char = substr($string, $i, 1);
      $keychar = substr($key, ($i % strlen($key))-1, 1);
      $char = chr(ord($char)-ord($keychar));
      $result.=$char;
   }
   return $result;
}

function sanear_string($string)
{
    //Esta parte se encarga de eliminar cualquier caracter extrano
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":",
             "."),
        '',
        $string
    );

    return $string;
}

function truncateFloat($number, $digitos)
{
    $raiz = 10;
    $multiplicador = pow ($raiz,$digitos);
    $resultado = ((int)($number * $multiplicador)) / $multiplicador;
    return number_format($resultado, $digitos,",",".");
}

function VersionNavegador($user_agent) {
            if (strpos($user_agent, "Trident/7")>0) {
                return 'Internet Explorer';
                
            }
            if (strpos($user_agent, "Trident/6")>0) {
                return 'Internet Explorer';
                
            }
            if (strpos($user_agent, "Trident/5")>0) {
                return 'Internet Explorer';                
            }
            if (strpos($user_agent, "Trident/4")>0) {
                return 'Internet Explorer';                
            }
            if (strpos($user_agent, "Trident")>0) {
                return 'Internet Explorer';                
            }
            if (strpos($user_agent, "MSIE")>0) {
                return 'Internet Explorer';                
            }
             $navegadores = array(
                  'Opera' => 'Opera',
                  'Mozilla Firefox'=> '(Firebird)|(Firefox)',
                  'Galeon' => 'Galeon',
                  'Mozilla'=>'Gecko',
                  'MyIE'=>'MyIE',
                  'Lynx' => 'Lynx',
                  'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
                  'Konqueror'=>'Konqueror',
            );
            foreach($navegadores as $navegador=>$pattern){
                   if (preg_match("/".$pattern."/i", $user_agent))
                   return $navegador;
                }
            return 'Desconocido';
        }
        
function getProvincia($pintCP){
    $CodigoPostalPrefijo[0] = '01'; //Primeros digitos del codigo postal de Álava
    $CodigoPostalPrefijo[1] = '02'; //Primeros digitos del codigo postal de Albacete
    $CodigoPostalPrefijo[2] = '03'; //Primeros digitos del codigo postal de Alicante
    $CodigoPostalPrefijo[3] = '04'; //Primeros digitos del codigo postal de Almería
    $CodigoPostalPrefijo[4] = '33'; //Primeros digitos del codigo postal de Asturias
    $CodigoPostalPrefijo[5] = '05'; //Primeros digitos del codigo postal de Ávila
    $CodigoPostalPrefijo[6] = '06'; //Primeros digitos del codigo postal de Badajoz
    $CodigoPostalPrefijo[7] = '08'; //Primeros digitos del codigo postal de Barcelona
    $CodigoPostalPrefijo[8] = '09'; //Primeros digitos del codigo postal de Burgos
    $CodigoPostalPrefijo[9] = '10'; //Primeros digitos del codigo postal de Cáceres
    $CodigoPostalPrefijo[10] = '11'; //Primeros digitos del codigo postal de Cádiz
    $CodigoPostalPrefijo[11] = '39'; //Primeros digitos del codigo postal de Cantabria
    $CodigoPostalPrefijo[12] = '12'; //Primeros digitos del codigo postal de Castellón de la Plana
    $CodigoPostalPrefijo[13] = '51'; //Primeros digitos del codigo postal de Ceuta
    $CodigoPostalPrefijo[14] = '13'; //Primeros digitos del codigo postal de Ciudad Real
    $CodigoPostalPrefijo[15] = '14'; //Primeros digitos del codigo postal de Córdoba
    $CodigoPostalPrefijo[16] = '15'; //Primeros digitos del codigo postal de Coruña, A
    $CodigoPostalPrefijo[17] = '16'; //Primeros digitos del codigo postal de Cuenca
    $CodigoPostalPrefijo[18] = '17'; //Primeros digitos del codigo postal de Girona
    $CodigoPostalPrefijo[19] = '18'; //Primeros digitos del codigo postal de Granada
    $CodigoPostalPrefijo[20] = '19'; //Primeros digitos del codigo postal de Guadalajara
    $CodigoPostalPrefijo[21] = '20'; //Primeros digitos del codigo postal de Guipúzcoa
    $CodigoPostalPrefijo[22] = '21'; //Primeros digitos del codigo postal de Huelva
    $CodigoPostalPrefijo[23] = '22'; //Primeros digitos del codigo postal de Huesca
    $CodigoPostalPrefijo[24] = '07'; //Primeros digitos del codigo postal de Illes Balears
    $CodigoPostalPrefijo[25] = '23'; //Primeros digitos del codigo postal de Jaén
    $CodigoPostalPrefijo[26] = '24'; //Primeros digitos del codigo postal de León
    $CodigoPostalPrefijo[27] = '25'; //Primeros digitos del codigo postal de Lleida
    $CodigoPostalPrefijo[28] = '27'; //Primeros digitos del codigo postal de Lugo
    $CodigoPostalPrefijo[29] = '28'; //Primeros digitos del codigo postal de Madrid
    $CodigoPostalPrefijo[30] = '29'; //Primeros digitos del codigo postal de Málaga
    $CodigoPostalPrefijo[31] = '52'; //Primeros digitos del codigo postal de Melilla
    $CodigoPostalPrefijo[32] = '30'; //Primeros digitos del codigo postal de Murcia
    $CodigoPostalPrefijo[33] = '31'; //Primeros digitos del codigo postal de Navarra
    $CodigoPostalPrefijo[34] = '32'; //Primeros digitos del codigo postal de Ourense
    $CodigoPostalPrefijo[35] = '34'; //Primeros digitos del codigo postal de Palencia
    $CodigoPostalPrefijo[36] = '35'; //Primeros digitos del codigo postal de Palmas, Las
    $CodigoPostalPrefijo[37] = '36'; //Primeros digitos del codigo postal de Pontevedra
    $CodigoPostalPrefijo[38] = '26'; //Primeros digitos del codigo postal de Rioja, La
    $CodigoPostalPrefijo[39] = '37'; //Primeros digitos del codigo postal de Salamanca
    $CodigoPostalPrefijo[40] = '38'; //Primeros digitos del codigo postal de Santa Cruz de Tenerife
    $CodigoPostalPrefijo[41] = '40'; //Primeros digitos del codigo postal de Segovia
    $CodigoPostalPrefijo[42] = '41'; //Primeros digitos del codigo postal de Sevilla
    $CodigoPostalPrefijo[43] = '42'; //Primeros digitos del codigo postal de Soria
    $CodigoPostalPrefijo[44] = '43'; //Primeros digitos del codigo postal de Tarragona
    $CodigoPostalPrefijo[45] = '44'; //Primeros digitos del codigo postal de Teruel
    $CodigoPostalPrefijo[46] = '45'; //Primeros digitos del codigo postal de Toledo
    $CodigoPostalPrefijo[47] = '46'; //Primeros digitos del codigo postal de Valencia
    $CodigoPostalPrefijo[48] = '47'; //Primeros digitos del codigo postal de Valladolid
    $CodigoPostalPrefijo[49] = '48'; //Primeros digitos del codigo postal de Vizcaya
    $CodigoPostalPrefijo[50] = '49'; //Primeros digitos del codigo postal de Zamora
    $CodigoPostalPrefijo[51] = '50'; //Primeros digitos del codigo postal de Zaragoza
    //
    $arrPcias[0] = 'Álava';
    $arrPcias[1] = 'Albacete';
    $arrPcias[2] = 'Alicante';
    $arrPcias[3] = 'Almería';
    $arrPcias[4] = 'Asturias';
    $arrPcias[5] = 'Ávila';
    $arrPcias[6] = 'Badajoz';
    $arrPcias[7] = 'Barcelona';
    $arrPcias[8] = 'Burgos';
    $arrPcias[9] = 'Cáceres';
    $arrPcias[10] = 'Cádiz';
    $arrPcias[11] = 'Cantabria';
    $arrPcias[12] = 'Castellón de la Plana';
    $arrPcias[13] = 'Ceuta';
    $arrPcias[14] = 'Ciudad Real';
    $arrPcias[15] = 'Córdoba';
    $arrPcias[16] = 'A Coruña';
    $arrPcias[17] = 'Cuenca';
    $arrPcias[18] = 'Girona';
    $arrPcias[19] = 'Granada';
    $arrPcias[20] = 'Guadalajara';
    $arrPcias[21] = 'Guipúzcoa';
    $arrPcias[22] = 'Huelva';
    $arrPcias[23] = 'Huesca';
    $arrPcias[24] = 'Illes Balears';
    $arrPcias[25] = 'Jaén';
    $arrPcias[26] = 'León';
    $arrPcias[27] = 'Lleida';
    $arrPcias[28] = 'Lugo';
    $arrPcias[29] = 'Madrid';
    $arrPcias[30] = 'Málaga';
    $arrPcias[31] = 'Melilla';
    $arrPcias[32] = 'Murcia';
    $arrPcias[33] = 'Navarra';
    $arrPcias[34] = 'Ourense';
    $arrPcias[35] = 'Palencia';
    $arrPcias[36] = 'Las Palmas';
    $arrPcias[37] = 'Pontevedra';
    $arrPcias[38] = 'La Rioja';
    $arrPcias[39] = 'Salamanca';
    $arrPcias[40] = 'Santa Cruz de Tenerife';
    $arrPcias[41] = 'Segovia';
    $arrPcias[42] = 'Sevilla';
    $arrPcias[43] = 'Soria';
    $arrPcias[44] = 'Tarragona';
    $arrPcias[45] = 'Teruel';
    $arrPcias[46] = 'Toledo';
    $arrPcias[47] = 'Valencia';
    $arrPcias[48] = 'Valladolid';
    $arrPcias[49] = 'Vizcaya';
    $arrPcias[50] = 'Zamora';
    $arrPcias[51] = 'Zaragoza';
    
    if (strlen($pintCP) != 5) {
        return false; //Si la longitud de "TuCodigoPostal" es diferente de 5 devuelve falso
    }
    else if (is_nan($pintCP)) {
        return false; //Si "TuCodigoPostal" no es un numero
    }
    else {
        for($i=0;$i < sizeof($CodigoPostalPrefijo);$i++){
            if(substr($pintCP, 0,2) == $CodigoPostalPrefijo[$i]){
                return $arrPcias[$i];
            }
        }
    }
}

function selfURL(){
   $s        = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
   $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
   $port     = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
   return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}
function strleft($s1, $s2){
   return substr($s1, 0, strpos($s1, $s2));
}




function getAllProvincia(){
    $CodigoPostalPrefijo[0] = '01'; //Primeros digitos del codigo postal de Álava
    $CodigoPostalPrefijo[1] = '02'; //Primeros digitos del codigo postal de Albacete
    $CodigoPostalPrefijo[2] = '03'; //Primeros digitos del codigo postal de Alicante
    $CodigoPostalPrefijo[3] = '04'; //Primeros digitos del codigo postal de Almería
    $CodigoPostalPrefijo[4] = '33'; //Primeros digitos del codigo postal de Asturias
    $CodigoPostalPrefijo[5] = '05'; //Primeros digitos del codigo postal de Ávila
    $CodigoPostalPrefijo[6] = '06'; //Primeros digitos del codigo postal de Badajoz
    $CodigoPostalPrefijo[7] = '08'; //Primeros digitos del codigo postal de Barcelona
    $CodigoPostalPrefijo[8] = '09'; //Primeros digitos del codigo postal de Burgos
    $CodigoPostalPrefijo[9] = '10'; //Primeros digitos del codigo postal de Cáceres
    $CodigoPostalPrefijo[10] = '11'; //Primeros digitos del codigo postal de Cádiz
    $CodigoPostalPrefijo[11] = '39'; //Primeros digitos del codigo postal de Cantabria
    $CodigoPostalPrefijo[12] = '12'; //Primeros digitos del codigo postal de Castellón de la Plana
    $CodigoPostalPrefijo[13] = '51'; //Primeros digitos del codigo postal de Ceuta
    $CodigoPostalPrefijo[14] = '13'; //Primeros digitos del codigo postal de Ciudad Real
    $CodigoPostalPrefijo[15] = '14'; //Primeros digitos del codigo postal de Córdoba
    $CodigoPostalPrefijo[16] = '15'; //Primeros digitos del codigo postal de Coruña, A
    $CodigoPostalPrefijo[17] = '16'; //Primeros digitos del codigo postal de Cuenca
    $CodigoPostalPrefijo[18] = '17'; //Primeros digitos del codigo postal de Girona
    $CodigoPostalPrefijo[19] = '18'; //Primeros digitos del codigo postal de Granada
    $CodigoPostalPrefijo[20] = '19'; //Primeros digitos del codigo postal de Guadalajara
    $CodigoPostalPrefijo[21] = '20'; //Primeros digitos del codigo postal de Guipúzcoa
    $CodigoPostalPrefijo[22] = '21'; //Primeros digitos del codigo postal de Huelva
    $CodigoPostalPrefijo[23] = '22'; //Primeros digitos del codigo postal de Huesca
    $CodigoPostalPrefijo[24] = '07'; //Primeros digitos del codigo postal de Illes Balears
    $CodigoPostalPrefijo[25] = '23'; //Primeros digitos del codigo postal de Jaén
    $CodigoPostalPrefijo[26] = '24'; //Primeros digitos del codigo postal de León
    $CodigoPostalPrefijo[27] = '25'; //Primeros digitos del codigo postal de Lleida
    $CodigoPostalPrefijo[28] = '27'; //Primeros digitos del codigo postal de Lugo
    $CodigoPostalPrefijo[29] = '28'; //Primeros digitos del codigo postal de Madrid
    $CodigoPostalPrefijo[30] = '29'; //Primeros digitos del codigo postal de Málaga
    $CodigoPostalPrefijo[31] = '52'; //Primeros digitos del codigo postal de Melilla
    $CodigoPostalPrefijo[32] = '30'; //Primeros digitos del codigo postal de Murcia
    $CodigoPostalPrefijo[33] = '31'; //Primeros digitos del codigo postal de Navarra
    $CodigoPostalPrefijo[34] = '32'; //Primeros digitos del codigo postal de Ourense
    $CodigoPostalPrefijo[35] = '34'; //Primeros digitos del codigo postal de Palencia
    $CodigoPostalPrefijo[36] = '35'; //Primeros digitos del codigo postal de Palmas, Las
    $CodigoPostalPrefijo[37] = '36'; //Primeros digitos del codigo postal de Pontevedra
    $CodigoPostalPrefijo[38] = '26'; //Primeros digitos del codigo postal de Rioja, La
    $CodigoPostalPrefijo[39] = '37'; //Primeros digitos del codigo postal de Salamanca
    $CodigoPostalPrefijo[40] = '38'; //Primeros digitos del codigo postal de Santa Cruz de Tenerife
    $CodigoPostalPrefijo[41] = '40'; //Primeros digitos del codigo postal de Segovia
    $CodigoPostalPrefijo[42] = '41'; //Primeros digitos del codigo postal de Sevilla
    $CodigoPostalPrefijo[43] = '42'; //Primeros digitos del codigo postal de Soria
    $CodigoPostalPrefijo[44] = '43'; //Primeros digitos del codigo postal de Tarragona
    $CodigoPostalPrefijo[45] = '44'; //Primeros digitos del codigo postal de Teruel
    $CodigoPostalPrefijo[46] = '45'; //Primeros digitos del codigo postal de Toledo
    $CodigoPostalPrefijo[47] = '46'; //Primeros digitos del codigo postal de Valencia
    $CodigoPostalPrefijo[48] = '47'; //Primeros digitos del codigo postal de Valladolid
    $CodigoPostalPrefijo[49] = '48'; //Primeros digitos del codigo postal de Vizcaya
    $CodigoPostalPrefijo[50] = '49'; //Primeros digitos del codigo postal de Zamora
    $CodigoPostalPrefijo[51] = '50'; //Primeros digitos del codigo postal de Zaragoza
    //
    $arrPcias[0] = 'Álava';
    $arrPcias[1] = 'Albacete';
    $arrPcias[2] = 'Alicante';
    $arrPcias[3] = 'Almería';
    $arrPcias[4] = 'Asturias';
    $arrPcias[5] = 'Ávila';
    $arrPcias[6] = 'Badajoz';
    $arrPcias[7] = 'Barcelona';
    $arrPcias[8] = 'Burgos';
    $arrPcias[9] = 'Cáceres';
    $arrPcias[10] = 'Cádiz';
    $arrPcias[11] = 'Cantabria';
    $arrPcias[12] = 'Castellón de la Plana';
    $arrPcias[13] = 'Ceuta';
    $arrPcias[14] = 'Ciudad Real';
    $arrPcias[15] = 'Córdoba';
    $arrPcias[16] = 'A Coruña';
    $arrPcias[17] = 'Cuenca';
    $arrPcias[18] = 'Girona';
    $arrPcias[19] = 'Granada';
    $arrPcias[20] = 'Guadalajara';
    $arrPcias[21] = 'Guipúzcoa';
    $arrPcias[22] = 'Huelva';
    $arrPcias[23] = 'Huesca';
    $arrPcias[24] = 'Illes Balears';
    $arrPcias[25] = 'Jaén';
    $arrPcias[26] = 'León';
    $arrPcias[27] = 'Lleida';
    $arrPcias[28] = 'Lugo';
    $arrPcias[29] = 'Madrid';
    $arrPcias[30] = 'Málaga';
    $arrPcias[31] = 'Melilla';
    $arrPcias[32] = 'Murcia';
    $arrPcias[33] = 'Navarra';
    $arrPcias[34] = 'Ourense';
    $arrPcias[35] = 'Palencia';
    $arrPcias[36] = 'Las Palmas';
    $arrPcias[37] = 'Pontevedra';
    $arrPcias[38] = 'La Rioja';
    $arrPcias[39] = 'Salamanca';
    $arrPcias[40] = 'Santa Cruz de Tenerife';
    $arrPcias[41] = 'Segovia';
    $arrPcias[42] = 'Sevilla';
    $arrPcias[43] = 'Soria';
    $arrPcias[44] = 'Tarragona';
    $arrPcias[45] = 'Teruel';
    $arrPcias[46] = 'Toledo';
    $arrPcias[47] = 'Valencia';
    $arrPcias[48] = 'Valladolid';
    $arrPcias[49] = 'Vizcaya';
    $arrPcias[50] = 'Zamora';
    $arrPcias[51] = 'Zaragoza';
    
    $arrayCompleto[0] = $CodigoPostalPrefijo;
    $arrayCompleto[1] = $arrPcias;
    return $arrayCompleto;
}

function sustituirAcentos($entrada){
    
    $search  =      array( 'á', 'é', 'í','ó','ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ë', 'Ë', 'ü', 'Ü');
    $reemplazo  =   array( 'a', 'e', 'i','o','u', 'A', 'E', 'I', 'O', 'U', 'e', 'E', 'u', 'U');
    $salida = str_replace($search, $reemplazo, $entrada);//Sustituye el caracter encontrado en $search, por uno de $reemplazo. La posicion es la misma.
    
    return $salida;
}

/**
    * Función que valida una fecha.
    *
    * @param String dd/mm/yyyy
    *
    * @return true or false.
    * 
    * @throws 
    * @todo    
    *
    * @since   
    * @author  Sofía
    *
    */
function validarFecha($fechaStr){
//Creada por Sofía: 
//Funcion para validar fechas:

//elimina posibles espacios en la cadena   
$miFecha=  trim($fechaStr);    


//sustituye "-" por "/" en la cadena
$miFecha=str_replace("/","-",$miFecha);

//divide la fecha en partes y las guarda en un array
$partesFecha=explode("-",$miFecha);
        
//asigna los valores de mes,dia,año
$day=$partesFecha[0];
$month=$partesFecha[1];
$year=$partesFecha[2];

        //var_dump("DIA=".$day." MES= ".$month." AÑO=".$year." valor devuelto por checkdate=".checkdate($month,$day,$year));
       // exit;
       // 
//evalua la fecha con checkdate y devuelve el resultado
return checkdate($month,$day,$year);
          
}

/**
    * Función que devuelve los días entre fechas
    *
    * @param String $fechaInicio dd/mm/yyyy
    * @param String $fechaFin dd/mm/yyyy 
    *
    * @return true or false.
    * 
    * @throws  
    * @todo    
    *
    * @since   
    * @author  Javier Fernández <javier.fernandez@eurocontrol-solutions.com>
    *
    */
function diasDiferenciaEntreFechas ($fechaI, $fechaF){
       
    //pasan al formato que tenemos en SQL para fechas
    $fechaIni=str_replace("/","-",$fechaI);
    $fechaIni = date('Y-m-d', strtotime($fechaIni));  

    $fechaFin=str_replace("/","-",$fechaF);
    $fechaFin = date('Y-m-d', strtotime($fechaFin)); 

    //convierto en date para poder comparar
    $f1=  date_create($fechaIni);
    $f2=  date_create($fechaFin);
    $dDiff = $f1->diff($f2);
    
    return $dDiff->format('%R%a'); //<0 la fecha inicio es superior a la final, sino devuelve días de diferencia
}

/**
    * Función que devuelve el total de días que contiene un rango de fechas.
    *
    * @param String $fechaInicio dd/mm/yyyy
    * @param String $fechaFin dd/mm/yyyy 
    *
    * @return entero cantidad de días entre las fechas
    * 
    * @throws  
    * @todo    
    *
    * @since   30/01/2017
    * @author  Sofía Bueno <sbueno@eurocontrol-solutions.com>
    * Version de diasDiferenciaEntreFechas creada por Sofía. La otra función daba el resultado -1
    */
function totalDiasRangoFechas ($fechaI, $fechaF){
       
    //pasan al formato que tenemos en SQL para fechas
    $fechaIni=str_replace("/","-",$fechaI);
    $fechaIni = date('Y-m-d', strtotime($fechaIni)); 
    
    $fechaFin=str_replace("/","-",$fechaF);
    $fechaFin = date('Y-m-d', strtotime($fechaFin)); 

    //convierto en date para poder comparar
    $f1=  date_create($fechaIni);
    $f2=  date_create($fechaFin);
    $dDiff = $f1->diff($f2);
    
    //a la diferencia en días le sumamos 1 para obtener el total de días
    $totalDias=$dDiff->format('%R%a')+1;

    return $totalDias; 
}

/**
    * Función valida que la hora sea correcta
    *
    * @param String $hora HH:MM
    *
    * @return true or false.
    * 
    * @throws  
    * @todo    
    *
    * @since   
    * @author  Javier Fernández <javier.fernandez@eurocontrol-solutions.com>
    *
    */
function chequearFormatoHoraHHMM ($hora){
    return preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $hora);
}

/**
    * Función que comprueba los permisos de un usuario para un area
    *
    * @param String $nomArea (los nombres del area a los que tengo permiso
    * @param $arrPermisos de los permisos para el perfil del usuario 
    *
    * @return  true si tiene permisos el usuairo sino false
    *
    * @since   28/12/2016
    * @author  Javier Fernández
    *
    */
function comprobarRoles($nomArea, $arrPermisos) {
    try {
        $arrPermisosPag = explode(";", $nomArea);    
        foreach ($arrPermisosPag as $permisoPag) {
            if ($arrPermisos[$permisoPag]==0 || $arrPermisos[$permisoPag]==2) {
                    return true;
            }
        }
        return false;
    } catch (Exception $e) {
       return false; 
    }
}

/**
    * Función que comprueba el permiso de lectura de un area
    * El 2 es el permiso de lectura, si devuelve true es que solo tiene de lectura.
    *
    * @param String $nomArea (los nombres del area a los que tengo permiso
    * @param $arrPermisos de los permisos para el perfil del usuario 
    *
    * @return  true si tiene permisos el usuairo sino false
    *
    * @since   17/01/2017
    * @author  Javier Fernández
    *
    */
function comprobarRolLectura($nomArea, $arrPermisos) {
    //El 2 es el permiso de lectura, si devuelve true es que solo tiene de lectura.
    try {
        $arrPermisosPag = explode(";", $nomArea);    
        foreach ($arrPermisosPag as $permisoPag) {
            if ($arrPermisos[$permisoPag]==2) {
                    return true;
            }
        }
        return false;
    } catch (Exception $e) {
       return false; 
    }
}

function validar_clave($clave,&$error_clave){
   if(strlen($clave) < 8){
      $error_clave = "La clave debe tener al menos 8 caracteres";
      return false;
   }
   if (!preg_match('`[a-z]`',$clave)){
      $error_clave = "La clave debe tener al menos una letra minúscula";
      return false;
   }
   if (!preg_match('`[A-Z]`',$clave)){
      $error_clave = "La clave debe tener al menos una letra mayúscula";
      return false;
   }
   if (!preg_match('`[0-9]`',$clave)){
      $error_clave = "La clave debe tener al menos un caracter numérico";
      return false;
   }
   $error_clave = "";
   return true;
}

//Creada por sofía 26-1-2017
//Recibe un entero con el número de días y una fecha en formato Y-m-d
//Devuelve el resultado de sumar los días a la fecha en formato Y-m-d
function sumarDias($cantidadDias,$fecha){
    
    $fechaResultado =  date('Y-m-d', strtotime("$fecha {$cantidadDias} day"));;
    return $fechaResultado;
}

//Creada por sofía 26/01/2017
// recibe un array ASOCIATIVO
// devuelve una cadena con los valores del array separados por comas
function printarArrayConComas($miArray){
       
    $cadena="";

    foreach ($miArray as $value) {
        $cadena=$cadena.$value.",";
    }      
    return trim($cadena, ',');
}

//Creada por sofía: Prepara una fecha para formato mysql
function formatFechaQuery($fecha){

    if($fecha == "" || $fecha==NULL) {

        $fecha='';
        
    }else{
        if (strpos($fecha,"/")){ 
             list( $day, $month, $year ) = explode( '/', $fecha );
            
        }else{
            list( $day, $month, $year ) = explode( '-', $fecha );
            
        }
        if (strlen($day)==4){
            $contenedor=$day;
            $day=$year;
            $year=$contenedor;
        }
        $fecha=$year."-".$month."-".$day;      
    } 

    return $fecha;
}
//Creada por sofía: Prepara un id para formato mysql
//no pueden ser 0 ni vacíos y tienen que ser número
function FormatIdNumberQuery($id){
    
    if($id == "" || $id==NULL || is_numeric($id)==false) {
            $id='NULL';
    }
    return $id;
}

function displayPermisos($valor){
    if($valor=="0"){
        return "<font style='color:green;'>Total</font>";                                                                
    }else{
        if($valor=="1"){
             return "<font style='color:red;'>Denegado</font>";
        } else {
             return "<font style='color:gray;'>Lectura</font>";
        }                                                               
    }
}

?>