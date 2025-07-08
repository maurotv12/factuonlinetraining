function carga_ajaxEditarLink(id,id2,div,url) 
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


function carga_ajaxPassword(id,div,url) 
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
vista previa del color del boton
=============================================*/
    var muestrario;
    var colorPredeterminado = "#02aec4";

    window.addEventListener("load", startup, false);
    function startup() {
      muestrario = document.querySelector("#muestrario");
      //muestrario.value = colorPredeterminado;
      muestrario.addEventListener("input", actualizarPrimero, false);
      muestrario.addEventListener("change", actualizarTodo, false);
      muestrario.select();
    }

    function actualizarPrimero(event) {
      var titulo = document.getElementById("titulo");

      if (titulo) {
        titulo.style.background = event.target.value;
      }
    }
/*=============================================
vista previa crear linc campo titulo
=============================================*/

function VistaPrevia(entrada, salida){
    document.getElementById(salida).innerHTML = entrada.value.replace(/\r?\n/g, "<br>");
}

/*=============================================
Boton para copiar en portapapeles
=============================================*/
document.getElementById('BotonCopiar').addEventListener('click', copiarAlPortapapeles);
	function copiarAlPortapapeles(ev) {
	    // Obtener contenido del div oculto
	    let contenido = document.getElementById('TextoACopiar').textContent;
	    // Crear input
	    let input = document.createElement('input');
	    // Asignar contenido
	    input.value = contenido;
	    // Agregar input a documento
	    document.body.appendChild(input);
	    // Seleccionar contenido
	    input.select();
	    // Copiar
	    document.execCommand('copy');
	    // Eliminar input
	    input.remove();
	}

/*=============================================
BORRAR ALERTAS
=============================================*/

$("input[name='emailRegistro'], input[name='usuarioRegistro'], #politicas").change(function(){

	$(".alert").remove();

})

/*=============================================
VALIDAR EMAIL REPETIDO
=============================================*/
var ruta = $("#ruta").val();
$("input[name='emailRegistro']").change(function(){
	var email = $(this).val();
	var datos = new FormData();
	datos.append("validarEmail", email);
	$.ajax({
		url: ruta+"eKlycsApp/ajax/usuarios.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType:"json",
		success:function(respuesta){
			if(respuesta){
			$("input[name='emailRegistro']").val("");
			$("input[name='emailRegistro']").after(`
                <div class="alert alert-danger" role="alert">
				  El e-Mail ya existe, puede 
				  <a class="font-bold" href="forgot-password"> [Recuperar contraseña]</a>
				</div>				
			`)
			return;
			}
		}
	})
})

/*=============================================
VALIDAR USUARIO REPETIDO
=============================================*/
var ruta = $("#ruta").val();
$("input[name='usuarioRegistro']").change(function(){
	var usuario = $(this).val();
	var datos = new FormData();
	datos.append("validarUsuario", usuario);
	$.ajax({
		url: ruta+"eKlycsApp/ajax/usuarios.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType:"json",
		success:function(respuesta){
			if(respuesta){
			$("input[name='usuarioRegistro']").val("");
			$("input[name='usuarioRegistro']").after(`
				<div class="alert alert-danger" role="alert">
				  El usuario ya existe, por favor ingrese otro, o puede 
				  <a class="font-bold" href="forgot-password">[Recuperar contraseña]</a>
				</div>
				
			`)
			return;
			}
		}
	})
})

/*=============================================
Validar políticas
=============================================*/

function validarPoliticas(){
	var politicas = $("#politicas:checked").val();
	if(politicas != "on"){
		$("#politicas").before(`
			<span class="text-danger">
			 Debe aceptar las políticas de privacidad.
			</span>
			`);
		return false;
	}
	return true;
}