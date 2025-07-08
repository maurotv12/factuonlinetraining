 function genericSocialShare(url){
    window.open(url,'sharer','toolbar=0,status=0,width=648,height=395');
    return true;
  }
function carga_ajax_EditarMDN(id,div,url) 
        {
          // alert(ruta );
           $.post
            (
                url,
                {id:id},
         
                function(resp)
               {
                    $("#"+div+"").html(resp);                                     
               }
            );              
        }
function carga_ajaxEditar(id,id2,id3,div,url) 
        {
          // alert(ruta );
           $.post
            (
                url,
                {id:id, id2:id2, id3:id3},
         
                function(resp)
               {
                    $("#"+div+"").html(resp);                                     
               }
            );              
        }
function carga_ajax(id,id2,div,url) 
        {
          // alert(ruta );
           $.post
            (
                url,
                {id:id, id2:id2},
         
                function(resp)
               {
                    $("#"+div+"").html(resp);                                     
               }
            );              
        }



/*=============================================
TABLA USUARIOS
=============================================*/

$(".tablaUsuarios").DataTable({
 	"deferRender": true,
  	"retrieve": true,
  	"processing": true,
	"language": {

	    "sProcessing":     "Procesando...",
	    "sLengthMenu":     "Mostrar _MENU_ registros",
	    "sZeroRecords":    "No se encontraron resultados",
	    "sEmptyTable":     "Ningún dato disponible en esta tabla",
	    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
	    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
	    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
	    "sInfoPostFix":    "",
	    "sSearch":         "Buscar:",
	    "sUrl":            "",
	    "sInfoThousands":  ",",
	    "sLoadingRecords": "Cargando...",
	    "oPaginate": {
	      "sFirst":    "Primero",
	      "sLast":     "Último",
	      "sNext":     "Siguiente",
	      "sPrevious": "Anterior"
	    },
	    "oAria": {
	        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
	        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
	    }

   }

});

