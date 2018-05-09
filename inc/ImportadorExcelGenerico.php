<?php
require_once './inc/funcionesImportarExcel.inc';


//$imp = new Importador();
//$imp->getArrayByExcelIndice("C:\wInterferencias\INT_DNO.xlsx","A:S", "2", false);

//var_dump($imp->getCabeceras());
//var_dump($imp->getData());


class Importador {
    
    /***************************************************************************************/
    /** ->$rutaFichero   -> "C:\excel.xlsx" | "C:\excel.xls"                              **/
    /** ->$columnasRango -> "0:5" Todas las columnas inluidas 0 y 5                       **/
    /**                     "A:Z" Todas las columnas incluidas A y Z                      **/
    /**                                                                                   **/
    /** ->$columnasRango ->  "0:3:5" Lee las 3 columnas                                   **/
    /**                      "A:B:Q:Z" Lee las 4 columnas                                 **/
    /**                                                                                   **/
    /** ->$comienzo     -> "1" Primera fila, se puede especificar cualquier numero        **/
    /**                    NOTA: No se controla que el $comienzo sea menor a numero col   **/
    /** ->$indiceNumerico -> boolean True indice array numerico                           **/
    /**                              False indice Nombre columnas                         **/
    /**  Ojo! Los campos tipo fecha pueden dar error, se debe conocer la columna que      **/
    /**  contiene la fecha, PHPExcel interpreta las celdas de Fecha como si fueran Float  **/
    /***************************************************************************************/
    /**LIMITACIONES: No se indica el numero de hoja, se leen todas, si el documento tiene***/
    /***************************************************************************************/
    function getArrayByExcelIndice($rutaFichero, $columnasRango, $comienzo, $indiceNumerico) {
        global $arrData;
        $arrData = Array();
        global $arrCabeceras;
        $arrCabeceras = Array();
        require_once './Classes/PHPExcel/IOFactory.php';
        //Lee XLS y XLSX
        $importador = new Importador;
        
        /*
         * $columnasRango
         */
        $rango = explode(":", $columnasRango);
        if(count($rango) == 2) {        //Un rango ejem: 3:5 o C:F
            if(ctype_alpha($rango[0])){ // Entra si es texto: A:D
                $comienzoRango = $importador->cambiaLetraPorNumero($rango[0]);
                $finRango = $importador->cambiaLetraPorNumero($rango[1]);
                
                $columnasLeer[0]=$comienzoRango;
                $columnasLeer[1]=$finRango;
            } else {//Entra si son numeros 3:5
                $arrResultado = explode(":", $columnasRango);//Entrada 5:10
                $comienzoRango = $arrResultado[0];
                $finRango = $arrResultado[1];
                
                $columnasLeer[0]=$comienzoRango;
                $columnasLeer[1]=$finRango;
            }
            
            
        } elseif(count($rango) > 2) {
            //$columnasLeer = explode(":", $columnasRango);
            if(ctype_alpha($columnasLeer[0])){// Entra si es texto: A:D:J -> Solo comprobamos el primer caracter.
                for($i=0; $i<count($rango); $i++) {
                    $columnasLeer[$i] = $importador->cambiaLetraPorNumero($rango[$i]);
                }
                $comienzo= $columnasLeer[0];
                $finRango= $columnasLeer[count($columnasLeer)-1];
            } else {//0:1:2

            }
            
        }
        
        $objPHPExcel = PHPExcel_IOFactory::load($rutaFichero);//"C:\CONTRATO2.xls"
        //$objPHPExcel = PHPExcel_IOFactory::load("C:\wInterferencias\INT_DNO.xlsx");//"C:\CONTRATO2.xls"
        //$objPHPExcel = PHPExcel_IOFactory::load("C:\\Users\\nacho\\Desktop\\ejemplo.xlsx");//"C:\CONTRATO2.xls"

        
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {  //Cada vuelta es una hoja en el excel.
            $numeroFilas = $worksheet->getHighestRow(); // Numero filas.
            
            for ($row = $comienzo; $row <= $numeroFilas;  $row++) {
                for ($col = $comienzoRango; $col <= $finRango;  $col++) {
                    if(count($columnasLeer) == 2) {
                        $arrFila = $importador->leerCelda($worksheet, $indiceNumerico, $comienzo, $row, $col);
                    }
                }
                $arrData[$row] =  $arrFila;

            }
        }
        return $numeroFilas;
    }
    
    function cambiaLetraPorNumero($caracter) {
        //Entrada-> 'A' Salida->1       ||      Entrada-> 'BA' Salida->53 (Z*2)+A = (26*2)+1
        //Entrada-> 'J' Salida->10      ||      Entrada-> 'Z' Salida->26
        //Entrada-> 'AA' Salida->27     
        if(strlen($caracter) == 1) {
            $codAscii = ord (strtoupper ($caracter))-64;//Convertimos los caracteres a mayuscula y devuelve su codifgo Ascii
            return $codAscii-1;//Al restar por 64 coincide con nuestro array '$letras'
        } elseif(strlen($caracter) == 2) {
            $primerLetraIndice = ord(strtoupper($caracter[0]))-64;
            $segundaLetraIndice = ord(strtoupper ($caracter[1]))-64;

            $resul = ($primerLetraIndice*26) + $segundaLetraIndice;
            return $resul-1;
        }
    }

    function getCabeceras() {
        global $arrCabeceras;
        return $arrCabeceras;
    }
    
    function getData() {
        global $arrData;
        return $arrData;
    }
    
    function leerCelda($worksheet, $indiceNumerico, $comienzo, $row, $col) {
        global $arrCabeceras;
        global $arrFila;
                
        if($indiceNumerico) {
            //$arrFila[$col] = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            $arrFila[$col] = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
        } else {
            if($row == $comienzo) {
                //$nombreColumna = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
                $nombreColumna = strtolower($worksheet->getCellByColumnAndRow($col, 1)->getCalculatedValue());
                $nombreColumna = str_replace(" ", "_", $nombreColumna);
                $arrCabeceras[$col] = sustituirAcentos($nombreColumna);
            }        
            //$arrFila[$arrCabeceras[$col]] = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            //$arrFila[$arrCabeceras[$col]] = escapeString($worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue());
            $arrFila[$arrCabeceras[$col]] = $worksheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
        }
        return $arrFila;
    }
    
}

//function getDatosHojaExcel($rutaExcel,$hojaExcel){
//    require_once './Classes/PHPExcel/IOFactory.php';
//    $objPHPExcel = PHPExcel_IOFactory::load($rutaFichero);
//    $workSheet=$objPHPExcel->getSheetByName($hojaExcel);
//    $numeroFilas = $worksheet->getHighestRow();
//    var_dump(worksheet);
//    exit();
//
//}
$letras = [
    1 => "A",2 => "B",3 => "C",4 => "D",5 => "E",6 => "F",7 => "G",8 => "H",9 => "I",10 => "J",11 => "K",12 => "L",13 => "M",14 => "N",15 => "O",16 => "P",
    17 => "Q",18 => "R",19 => "S",20 => "T",21 => "U",22 => "V",23 => "W",24 => "X",25 => "Y",26 => "Z",27 => "AA",28 => "AB",29 => "AC",30 => "AD",
    31 => "AE",32 => "AF",33 => "AG",34 => "AH",35 => "AI",36 => "AJ",37 => "AK",38 => "AL",39 => "AM",40 => "AN",41 => "AO",42 => "AP",43 => "AQ",
    44 => "AR",45 => "AS",46 => "AT",47 => "AU",48 => "AV",49 => "AW",50 => "AX",51 => "AY",52 => "AZ",]
?>
