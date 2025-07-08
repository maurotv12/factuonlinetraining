<?php
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
26/05/2022 Plataforma Cursos Ver cursos en inicio
 */

class ControladorCursosInicio
{
/*--=====================================
	Mostrar cursos en inicio
======================================--*/
static public function ctrMostrarCursosInicio(){
	$tabla = "curso";
	$item = null;
     $valor = null;
	$cursos = ModeloCursosInicio::mdlMostrarCursosInicio($tabla, $item, $valor);
	foreach ($cursos as $key => $value)
		{
		$descripcion = $value["descripcion"];
		$resulDescripcion = substr("$descripcion", 0, 100);
		$nCurso = $value["nombre"];
		$rnCurso = substr("$nCurso", 0, 30);
      echo '<div class="col-md-4 col-sm-6 team-marg">
                 <div class="team-thumb">
                      <div class="team-image">
                           <a href="'.$value["urlAmiga"].'"><img src="registro/'.$value["banner"].'" class="img-responsive" alt="Curso Calibélula"></a>
                      </div>
                      <div class="team-info">
                           <h3><a href="'.$value["urlAmiga"].'">'.$rnCurso.'</a></h3>
                           <a href="'.$value["urlAmiga"].'"><span>'.$resulDescripcion.'</span></a>
                           <h4 class="valorC">$ ';
                           if($value["valor"] == 0) {echo "Gratis";} 
                           else { echo $value["valor"]; } 
                           echo '</h4>
                           <p>Profesor: '.$value["idPersona"].'</p>
                           <div class="d-grid gap-2">
                           <a class="ingresar-btn btn btn-default" href="'.$value["urlAmiga"].'" role="button">Ver Curso</a>
                           </div>
                      </div>
                       <ul class="social-icon">
                          <li><a href="https://www.instagram.com/festivaldecine_calibelula/" class="fa fa-instagram" target="_blank"></a></li>
                          <li><a href="https://www.facebook.com/festivaldecinecalibelula/" class="fa fa-facebook-square" target="_blank"></a></li>
                          <li><a href="https://www.youtube.com/channel/UCWbTp6hNKlX7QPKsNMYbCWg" class="fa fa-youtube-play" target="_blank"></a></li>
                          <li><a href="https://twitter.com/FCalibelula" class="fa fa-twitter" target="_blank"></a></li>
                      </ul>
                 </div>
            </div>';
            }
		}
/*--==========================================
     Mostrar cursos en inicio todos o solo 1
============================================--*/
static public function ctrMostrarUnCursoInicio($item, $valor){
     $tabla = "curso";
     $cursos = ModeloCursosInicio::mdlMostrarCursosInicio($tabla, $item, $valor);
     return $cursos;
     }

/*--==========================================
  Consultar los datos de un curso en inicio
============================================--*/
static public function ctrConsultarUnCursoInicio($item, $valor, $tabla){
     $resul = ModeloCursosInicio::mdlConsultarUnCursoInicio($item, $valor, $tabla);
     return $resul;

     }

}