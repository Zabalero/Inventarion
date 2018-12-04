rem TASKKILL /IM IEXPLORE.EXE /F
copy/Y "\\pnaspom2\ftth$\I&M\Procesos\Proyectos\Inventario Logico\Hoy\Incidencias_Remedy2.xls" "E:\SrvWeb\inventarion\upload\Remedy\"

timeout /T 6

START /WAIT /MAX "iexplorer" "C:\Program Files (x86)\Internet Explorer\iexplore.exe" "http://ftth-dst.jazztel.com/inventarion/importar_incidencias_remedy.php"

timeout /T 300
TASKKILL /IM iexplore.exe /F


