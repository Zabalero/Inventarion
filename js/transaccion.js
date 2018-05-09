
//ESTE CODIGO YA NO VALE, ESTÁ EN BOOTSTRAP.JS
$(document).ready(function(){
	$('.modal').on('hidden', function() {
	    $(this).removeData('modal');
	});	
	//Consulta de los datos RA y RD de la Tarea
	$(".buscar_ra_rd").click(function(event){
		  event.preventDefault();
		  $(".modal-body").empty();
	      var button = $(this); // Button that triggered the modal
	      var idSelect = button.data('id'); // Extract info from data-* attributes
	      var modal = button.data('target');
	      var dataString = 'id=' + idSelect;
	      

	        $.ajax({
	            type: "GET",
	            url: "consultaActuacion.php",
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

	//Consulta del detalle de la tarea y listado de sus tareas asociadas
	$(".buscar_tarea").click(function(){
		  $(".modal-body").empty();
	      var button = $(this); // Button that triggered the modal
	      var idSelect = button.data('id'); // Extract info from data-* attributes
	      var modal = button.data('target');
	      var dataString = 'id=' + idSelect;
	      

	        $.ajax({
	            type: "GET",
	            url: "consultaTarea.php",
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

	//Consulta del detalle de las ctos de la actuación y de las que están bloqueadas por la tarea
	$(".buscar_cto").click(function(){
		  $(".modal-body").empty();
	      var button = $(this); // Button that triggered the modal
	      var idSelect = button.data('id'); // Extract info from data-* attributes
	      var modal = button.data('target');
	      var dataString = 'id=' + idSelect;
	      

	        $.ajax({
	            type: "GET",
	            url: "consultaTareaCTO.php",
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

}); 
