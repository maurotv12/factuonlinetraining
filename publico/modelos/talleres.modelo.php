<?php
/**
@grcarvajal grcarvajal@gmail.com **Gildardo Restrepo Carvajal**
10/08/2020 CursosApp
 */
require_once "conexion.php";

class ModeloTalleres

{

/*=============================================
	Registro de estudiantes al Talleres
	=============================================*/

	static public function mdlRegistroTaller($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(idTaller, usuarioNombre, email, edad, telefono, comuna, pais, gradoEscolar) VALUES (:idTaller, :nombre, :email, :edad, :telefono, :comuna, :pais, :grado)");

		$stmt->bindParam(":idTaller", $datos["idTaller"], PDO::PARAM_INT);
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt->bindParam(":comuna", $datos["comuna"], PDO::PARAM_STR);
		$stmt->bindParam(":pais", $datos["pais"], PDO::PARAM_STR);
		$stmt->bindParam(":grado", $datos["grado"], PDO::PARAM_STR);
		$stmt->bindParam(":edad", $datos["edad"], PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";

		}else{

			return print_r(Conexion::conectar()->errorInfo());
		}

		$stmt->close();
		$stmt = null;

	}
	
	/*=============================================
	Registro todo el publico al festival
	=============================================*/

	static public function mdlRegistroTodoPublico($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(idTaller, usuarioNombre, email, edad, telefono, comuna, pais, institucion) VALUES (:idTaller, :nombre, :email, :edad, :telefono, :comuna, :pais, :institucion)");

		$stmt->bindParam(":idTaller", $datos["idFestival"], PDO::PARAM_INT);
		$stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
		$stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
		$stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
		$stmt->bindParam(":comuna", $datos["ciudad"], PDO::PARAM_STR);
		$stmt->bindParam(":pais", $datos["pais"], PDO::PARAM_STR);
		$stmt->bindParam(":institucion", $datos["institucion"], PDO::PARAM_STR);
		//$stmt->bindParam(":grado", "n", PDO::PARAM_STR);
		$stmt->bindParam(":edad", $datos["edad"], PDO::PARAM_INT);

		if($stmt->execute()){

			return "ok";

		}else{

			return print_r(Conexion::conectar()->errorInfo());
		}

		$stmt->close();
		$stmt = null;

	}
/*=============================================
	Crear de Taller
	=============================================*/

	static public function mdlCrearTaller($tabla, $datos){

		$stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombreTaller, duraccion, linkTaller, descripcion, puntuacion, idProfesor, fechaTaller, horaTaller) VALUES (:nombre, :duracion, :link, :descripcion, :puntuacion, :idProf, :fecha, :hora)");

		$stmt->bindParam(":nombre", $datos["nombreTaller"], PDO::PARAM_STR);
		$stmt->bindParam(":duracion", $datos["duracion"], PDO::PARAM_STR);
		$stmt->bindParam(":link", $datos["linkTaller"], PDO::PARAM_STR);
		$stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
		$stmt->bindParam(":puntuacion", $datos["puntuacion"], PDO::PARAM_INT);
		$stmt->bindParam(":idProf", $datos["idProfesor"], PDO::PARAM_INT);
		$stmt->bindParam(":fecha", $datos["fechaTaller"], PDO::PARAM_STR);
		$stmt->bindParam(":hora", $datos["hora"], PDO::PARAM_STR);

		if($stmt->execute()){

			return "ok";

		}else{

			return print_r(Conexion::conectar()->errorInfo());
		}

		$stmt->close();
		$stmt = null;

	}

/*=============================================
	Contar total Talleres, peliculas, Usuarios
	=============================================*/

	static public function mdlContarTPU($tabla){
		$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
		$stmt -> execute();

			return $stmt -> fetchAll();
			$stmt-> close();
			$stmt = null;
	}

/*=============================================
	Mostrar listado de Registro Talleres
=============================================*/
static public function mdlMostrarRegistroTalleres($tabla, $item, $valor){
		if($item != null && $valor != null){
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");
			$stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
			$stmt -> execute();
			return $stmt -> fetchAll();
		}else{
			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
			$stmt -> execute();
			return $stmt -> fetchAll();
		}
		$stmt-> close();
		$stmt = null;
	}

/*=============================================
	Mostrar Talleres
	=============================================*/

	static public function mdlMostrarTalleres($tabla, $item, $valor){

		if($item != null && $valor != null){

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");

			$stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);

			$stmt -> execute();

			return $stmt -> fetch();

		}else{

			$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE `fechaTaller` > '2021-9-30' ORDER BY id DESC");

			$stmt -> execute();

			return $stmt -> fetchAll();

		}

		$stmt-> close();

		$stmt = null;

	}

	/*=============================================
	Actualizar Taller
	=============================================*/

	static public function mdlActualizarUsuario($tabla, $id, $item, $valor){
		$stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item = :$item WHERE id_usuario = :id_usuario");

		$stmt -> bindParam(":".$item, $valor, PDO::PARAM_STR);
		$stmt -> bindParam(":id_usuario", $id, PDO::PARAM_INT);
		if($stmt -> execute()){
			return "ok";
		}else{
			return print_r(Conexion::conectar()->errorInfo());
		}
		$stmt-> close();

		$stmt = null;

	}

}
