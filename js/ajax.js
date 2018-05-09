


function objetoAjax(){
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

/*
function ListadoRef(datos){

	divResultado = document.getElementById('resultadoREF');
	ajax=objetoAjax();
	ajax.open("GET", datos);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}
	ajax.send(null)
		
}
*/


function ListadoRef(datos1, datos2){



	divResultado2 = document.getElementById('resultadoCTO');
	ajax2=objetoAjax();
	ajax2.open("GET", datos2);
	ajax2.onreadystatechange=function() {
		if (ajax2.readyState==4) {
			divResultado2.innerHTML = ajax2.responseText
		}
	}
	ajax2.send(null)	
	
	
	

	divResultado1 = document.getElementById('resultadoREF');
	ajax1=objetoAjax();
	ajax1.open("GET", datos1);
	ajax1.onreadystatechange=function() {
		if (ajax1.readyState==4) {
			divResultado1.innerHTML = ajax1.responseText
		}
	}
	ajax1.send(null)
		
		
		
	
}



function ListadoCTO(datos){
	divResultado = document.getElementById('resultadoCTO');
	ajax=objetoAjax();
	ajax.open("GET", datos);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}
	ajax.send(null)
}



function marcarTodasCTOS(){
	var checkedCTO = document.getElementsByName('MARCAR[]');
    $(checkedCTO).prop('checked', true);
}

function desmarcarTodasCTOS() {

	var checkedCTO = document.getElementsByName('MARCAR[]');
	$(checkedCTO).removeAttr('checked');
}

function marcarTodasGESC(){

      $("#gescales_lateral input:checkbox").prop('checked', true);
}

function desmarcarTodasGESC() {


	$("#gescales_lateral input:checkbox").removeAttr('checked');
}




function ListadoSubactividad(){

	var idActividad = document.getElementById("activ").value;

	divResultado = document.getElementById('resultadoSubactividad');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

	var vinculo="consultas.php?dato=SUBACTIVIDAD"+"&id="+idActividad+"&rand="+mi_aleatorio; 
	 
	ajax=objetoAjax();
		
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
			document.getElementById('ctos_gescales').innerHTML = "";
			document.getElementById('gescales_lateral').innerHTML = "";
			document.getElementById('botonGescal').innerHTML = "";
			ListadoCtos($('#subactividad').val());
		}
	}

	ajax.send(null);

}
function ListadoSubactividadIns(){

	var idActividad = document.getElementById("activ").value;

	divResultado = document.getElementById('resultadoSubactividad');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

	var vinculo="consultas.php?dato=SUBACTIVIDAD_INS"+"&id="+idActividad+"&rand="+mi_aleatorio; 
	 
	ajax=objetoAjax();
		
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
			document.getElementById('ctos_gescales').innerHTML = "";
			document.getElementById('gescales_lateral').innerHTML = "";
			document.getElementById('botonGescal').innerHTML = "";
			ListadoCtos($('#subactividad').val());
		}
	}

	ajax.send(null);

}




//Lista las provincias a partir de la Región
function ListadoProvincias(){

	var idRegion = document.getElementById("region").value;

	divResultado = document.getElementById('resultadoProvincia');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

	var vinculo="consultas.php?dato=PROVINCIA"+"&id="+idRegion+"&rand="+mi_aleatorio; 
	 
	ajax=objetoAjax();
		
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null);

}


//Para las consultas tambien se deben mostrar las subactividades obsoletas
function ListadoSubactividadANT(){

	var idActividad = document.getElementById("activ").value;

	divResultado = document.getElementById('resultadoSubactividad');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

	var vinculo="consultas.php?dato=SUBACTIVIDAD_ANT"+"&id="+idActividad+"&rand="+mi_aleatorio; 
	 
	ajax=objetoAjax();
		
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
			document.getElementById('ctos_gescales').innerHTML = "";
			document.getElementById('gescales_lateral').innerHTML = "";
			document.getElementById('botonGescal').innerHTML = "";
			ListadoCtos($('#subactividad').val());
		}
	}

	ajax.send(null);

}

function seleccionarSubactividad(id) {

	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

	var vinculo="consultas.php?dato=SELECTSUBACTIVIDAD"+"&id="+id+"&rand="+mi_aleatorio; 
	 
	ajax=objetoAjax();

	var idSubactividad = '';
		
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			idSubactividad = ajax.responseText;
			$('#subactividad').val(idSubactividad);
			ListadoCtos(idSubactividad);
			
		}
	}

	
	ajax.send(null);	


}

function ListadoCtos(id){

	divResultado2 = document.getElementById('ctos_lateral');
	var mi_aleatorio2=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 


	var idActividad = document.getElementById("activ").value;
	var idAct = document.getElementById("idAct").value;
	var refAsoc = document.getElementById("refAsoc").value;
	var vinculo2="consultas.php?dato=CTOS"+"&id="+id+"&idActividad="+idActividad+"&id_actuacion="+idAct+"&ref_asociada="+refAsoc+"&rand="+mi_aleatorio2; 
	 
	ajax2=objetoAjax();
		
	ajax2.open("GET",vinculo2,true);//ponemos true para que la petición sea asincrónica 
	ajax2.onreadystatechange=function() {
		if (ajax2.readyState==4) {
			divResultado2.innerHTML = ajax2.responseText
			SwitchCtoGescal(idActividad, id);
		}
	}
	ajax2.send(null);	

	

}

function SwitchCtoGescal(idActividad, id){

	divResultado3 = document.getElementById('ctos_gescales');
	var mi_aleatorio3=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

	var vinculo3="consultas.php?dato=RADIOCTOSGESC"+"&id="+id+"&idActividad="+idActividad+"&rand="+mi_aleatorio3; 
	 
	ajax3=objetoAjax();
		
	ajax3.open("GET",vinculo3,true);//ponemos true para que la petición sea asincrónica 
	ajax3.onreadystatechange=function() {
		if (ajax3.readyState==4) {
			divResultado3.innerHTML = ajax3.responseText
			verBotonGescales();

			document.getElementById("cerrarCtos").onclick = verBotonGescales;
			document.getElementById("cerrarGescales").onclick = verSwithCtoGescal;			
		}
	}

	ajax3.send(null);	



}

function verSwithCtoGescal(){

	$("#radioCTO").attr('checked', true);
	$("#radioGescales").removeAttr('checked');
	
	var checkedGESC = document.getElementsByName('MARCARGESC[]');

	for (var i=0; i<checkedGESC.length; i++) {
	    if (checkedGESC[i].checked) {

			$("#radioCTO").removeAttr('checked');
			$("#radioGescales").attr('checked', true);

	    }
	}	

}

function ListadoGescales(){

	divResultado4 = document.getElementById('gescales_lateral');
	var mi_aleatorio4=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

	var id = document.getElementById('activ').value;

	var CTOgesc = 'CTO';

	if (document.getElementById('radioGescales').length > 0) {
		if (document.getElementById('radioGescales').checked) {
			CTOgesc = 'GESC'
		}
	}

	var checkedCTOS = document.getElementsByName('MARCAR[]');
	var marcarCTO = [];
	for (var i=0; i<checkedCTOS.length; i++) {
	    if (checkedCTOS[i].checked) {
	        marcarCTO.push(checkedCTOS[i].value);
	    }
	}	
	
	var checkedGESC = document.getElementsByName('MARCARGESC[]');
	var marcarGES = [];
	for (var i=0; i<checkedGESC.length; i++) {
	    if (checkedGESC[i].checked) {
	    	var codigos = checkedGESC[i].value.split("-");
			codigo_gescal = codigos[1];		    	
	        marcarGES.push(codigo_gescal);
	    }
	}	

	var vinculo4="consultas.php?dato=GESCALES"+"&id="+id+"&CTOgesc="+CTOgesc+"&marcarCTO="+marcarCTO+"&marcarGES="+marcarGES+"&rand="+mi_aleatorio4; 
	 
	ajax4=objetoAjax();
		
	ajax4.open("GET",vinculo4,true);//ponemos true para que la petición sea asincrónica 
	ajax4.onreadystatechange=function() {
		if (ajax4.readyState==4) {
			divResultado4.innerHTML = ajax4.responseText
		}
	}
	ajax4.send(null);	

}

function verBotonGescales(){

	divResultado = document.getElementById('botonGescal');

	var checkedCTOS = document.getElementsByName('MARCAR[]');

	var conta = 0;
	for (var i=0; i<checkedCTOS.length; i++) {
	    if (checkedCTOS[i].checked) {
	        conta++;
	    }
	}	

	var id = document.getElementById('subactividad').value;
	var idActividad = document.getElementById("activ").value;

	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

	var vinculo="consultas.php?dato=BOTONGESCALES"+"&id="+id+"&idActividad="+idActividad+"&checkedCTOS="+conta+"&rand="+mi_aleatorio; 
	 
	ajax=objetoAjax();
		
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText;
			ListadoGescales();
		}
	}
	ajax.send(null);	

	

}

function ListadoCabecera(){

	var cab = document.getElementById("cabecera").value;
	if(cab.length<3) return;

	limpiarSelect('COD_CABECERA');

	divResultado = document.getElementById('resultadoCabecera');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

    var vinculo='consultas.php?dato=CABECERA'+"&id="+cab+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null)

}

function ListadoActJazz(){

	var act = document.getElementById("actJazz").value;
	if(act.length<3) return;

	limpiarSelect('ACT_JAZZTEL');

	divResultado = document.getElementById('resultadoActuacionJazz');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

    var vinculo='consultas.php?dato=ACT_JAZZTEL'+"&id="+act+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null)

}

function ListadoActTesa(){

	var act = document.getElementById("actTesa").value;
	if(act.length<3) return;

	limpiarSelect('ACT_TESA');

	divResultado = document.getElementById('resultadoActuacionTesa');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

    var vinculo='consultas.php?dato=ACT_TESA'+"&id="+act+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null)

}

function ListadoIdAct(){

	var act = document.getElementById("idAct").value;
	if(act.length<3) return;

	limpiarSelect('ID_ACTUACION');

	divResultado = document.getElementById('resultadoIdActuacion');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

    var vinculo='consultas.php?dato=ID_ACTUACION'+"&id="+act+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null)

}

function ListadoIdGD(){

	var act = document.getElementById("idGD").value;
	if(act.length<3) return;

	limpiarSelect('ID_GD');

	divResultado = document.getElementById('resultadoIdGD');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

    var vinculo='consultas.php?dato=ID_GD'+"&id="+act+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null)

}

function ListadoIdFDTT(){

	var act = document.getElementById("idFDTT").value;
	if(act.length<3) return;

	limpiarSelect('ID_FDTT');

	divResultado = document.getElementById('resultadoIdFDTT');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

    var vinculo='consultas.php?dato=ID_FDTT'+"&id="+act+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null)

}

function ListadoRefAsoc(){

	var ref = document.getElementById("refAsoc").value;
	if(ref.length<3) return;

	limpiarSelect('REF_ASOCIADA');

	divResultado = document.getElementById('resultadoRefAsociada');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

    var vinculo='consultas.php?dato=REF_ASOCIADA'+"&id="+ref+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null)

}

function limpiarSelect(dato){


    $("SELECT").each(function (index) 
    { 

        if ($(this).attr("id") != dato) {
			$(this).find('option:selected').attr("selected",false)
        }

    }) 

}

function OcultarRefAsoc(){

	$("#hiddenRef").toggle();

}

function ListadoActuacion(url, id){

	if (id != '') {
		divResultado = document.getElementById('resultadoActuacion');
		var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

	    var vinculo=url+"&id="+id+"&rand="+mi_aleatorio; 
	 
		ajax=objetoAjax();
		
		ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divResultado.innerHTML = ajax.responseText
			}
		}
		ajax.send(null)
	}

}

function seleccionarActuacion(dato, id){


    $("#resultadoActuacion SELECT").each(function (index) 
    { 

        if ($(this).attr("id") != dato) {
			$(this).find('option:selected').attr("selected",false)
        }

		if (id != '') {
			divResultado = document.getElementById('resultadoRefAsociada');
			var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

		    var vinculo="consultas.php?dato="+dato+"&id="+id+"&rand="+mi_aleatorio; 
		 
			ajax=objetoAjax();
			
			ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divResultado.innerHTML = ajax.responseText
				}
			}
			ajax.send(null)
		}        

    }) 

}

function ListadoUsuOrigen(){

	var usu = document.getElementById("origenSolTxt").value;
	if(usu.length<3) return;

	divResultado = document.getElementById('resultadoOrigenSol');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

    var vinculo='consultas.php?dato=origenSol'+"&id="+usu+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null)

}

function ListadoTecnico(){

	var usu = document.getElementById("tecnicoTxt").value;
	if(usu.length<3) return;

	divResultado = document.getElementById('resultadoTecnico');
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 

    var vinculo='consultas.php?dato=tecnico'+"&id="+usu+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divResultado.innerHTML = ajax.responseText
		}
	}

	ajax.send(null)

}
function ModificarFecha(dato,id,valor){
	
	var mi_aleatorio=parseInt(Math.random()*99999999);//para que no guarde la página en el caché... 
    var vinculo='modificaciones.php?dato='+dato+"&id="+id+"&valor="+valor+"&rand="+mi_aleatorio; 
 
	ajax=objetoAjax();
	
	ajax.open("GET",vinculo,true);//ponemos true para que la petición sea asincrónica 
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			alert(ajax.responseText);
		}
	}

	ajax.send(null)

}
