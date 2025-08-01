<?php


// require_once "../../App/modelos/conexion.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/cursosApp/App/modelos/conexion.php";

/**
 * Clase ModeloCursosInicio
 * 
 * Maneja todas las operaciones de base de datos relacionadas con 
 * la visualización de cursos en la página de inicio pública.
 * 
 * Características principales:
 * - Validación de entradas para prevenir inyecciones SQL
 * - Manejo robusto de errores con logging
 * - Métodos optimizados para el rendimiento
 * - Documentación completa de todos los métodos
 */
class ModeloCursosInicio
{
	/*=============================================
	Mostrar cursos en inicio
	=============================================*/
	/**
	 * Obtener cursos para la página de inicio
	 * @param string $tabla Nombre de la tabla
	 * @param string|null $item Campo por el que se filtra
	 * @param mixed|null $valor Valor del campo para filtrar
	 * @return array|false Array con los cursos encontrados o false si hay error
	 */
	public static function mdlMostrarCursosInicio($tabla, $item, $valor)
	{
		try {
			// Validar que la tabla sea válida para evitar inyecciones SQL
			$tablasPermitidas = ['curso', 'categoria', 'persona'];
			if (!in_array($tabla, $tablasPermitidas)) {
				throw new InvalidArgumentException("Tabla no permitida: " . $tabla);
			}

			$conexion = Conexion::conectar();

			if ($item != null && $valor != null) {
				// Validar que el campo sea válido
				$camposPermitidos = [
					'curso' => ['id', 'id_categoria', 'id_persona', 'estado', 'url_amiga'],
					'categoria' => ['id', 'nombre'],
					'persona' => ['id', 'estado']
				];

				if (!isset($camposPermitidos[$tabla]) || !in_array($item, $camposPermitidos[$tabla])) {
					throw new InvalidArgumentException("Campo no permitido: " . $item . " para tabla: " . $tabla);
				}

				$stmt = $conexion->prepare("SELECT * FROM `$tabla` WHERE `$item` = :valor");
				$stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
				$stmt->execute();

				// Verificamos si es un ID único (esperamos solo un resultado)
				if ($item === 'id') {
					$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
					return $resultado ? $resultado : false;
				} else {
					// Para otros criterios, devolvemos todos los resultados que coincidan
					return $stmt->fetchAll(PDO::FETCH_ASSOC);
				}
			} else {
				// Sin filtros, obtener todos los registros activos para cursos
				if ($tabla === 'curso') {
					$stmt = $conexion->prepare("SELECT * FROM `$tabla` WHERE estado = 'activo' ORDER BY fecha_registro DESC");
				} else {
					$stmt = $conexion->prepare("SELECT * FROM `$tabla`");
				}
				$stmt->execute();
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
		} catch (PDOException $e) {
			// Log del error para debugging (en producción usar un sistema de logs)
			error_log("Error en mdlMostrarCursosInicio: " . $e->getMessage());
			return false;
		} catch (InvalidArgumentException $e) {
			// Log del error de validación
			error_log("Error de validación en mdlMostrarCursosInicio: " . $e->getMessage());
			return false;
		} catch (Exception $e) {
			// Log de cualquier otro error
			error_log("Error general en mdlMostrarCursosInicio: " . $e->getMessage());
			return false;
		} finally {
			// Liberar la conexión si existe
			if (isset($stmt)) {
				$stmt = null;
			}
		}
	}

	/*==========================================================================
	Contar registros en la tabla que envien como parametro mostrar infoAdmin
	=============================================================================*/
	/**
	 * Contar registros de una tabla específica
	 * @param string $tabla Nombre de la tabla a contar
	 * @return array|false Array con el total o false si hay error
	 */
	static public function mdlContarRegistros($tabla)
	{
		try {
			// Validar que la tabla sea válida para evitar inyecciones SQL
			$tablasPermitidas = ['curso', 'categoria', 'persona', 'inscripciones', 'mensajes'];
			if (!in_array($tabla, $tablasPermitidas)) {
				throw new InvalidArgumentException("Tabla no permitida para conteo: " . $tabla);
			}

			$conexion = Conexion::conectar();
			$stmt = $conexion->prepare("SELECT COUNT(*) as total FROM `$tabla`");
			$stmt->execute();
			$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

			return $resultado ? $resultado : ['total' => 0];
		} catch (PDOException $e) {
			error_log("Error en mdlContarRegistros: " . $e->getMessage());
			return false;
		} catch (InvalidArgumentException $e) {
			error_log("Error de validación en mdlContarRegistros: " . $e->getMessage());
			return false;
		} catch (Exception $e) {
			error_log("Error general en mdlContarRegistros: " . $e->getMessage());
			return false;
		} finally {
			if (isset($stmt)) {
				$stmt = null;
			}
		}
	}
	/*==============================================
	Consultar los datos de un curso en inicio
	==============================================*/
	/**
	 * Consultar un registro específico por campo y valor
	 * @param string $item Campo por el que se busca
	 * @param mixed $valor Valor del campo
	 * @param string $tabla Nombre de la tabla
	 * @return array|false Array con el registro encontrado o false si hay error
	 */
	static public function mdlConsultarUnCursoInicio($item, $valor, $tabla)
	{
		try {
			// Validar que la tabla sea válida
			$tablasPermitidas = ['curso', 'categoria', 'persona'];
			if (!in_array($tabla, $tablasPermitidas)) {
				throw new InvalidArgumentException("Tabla no permitida: " . $tabla);
			}

			// Validar que el campo sea válido para la tabla
			$camposPermitidos = [
				'curso' => ['id', 'url_amiga', 'id_categoria', 'id_persona', 'estado'],
				'categoria' => ['id', 'nombre'],
				'persona' => ['id', 'email', 'nro_identificacion']
			];

			if (!isset($camposPermitidos[$tabla]) || !in_array($item, $camposPermitidos[$tabla])) {
				throw new InvalidArgumentException("Campo no permitido: " . $item . " para tabla: " . $tabla);
			}

			$conexion = Conexion::conectar();
			$stmt = $conexion->prepare("SELECT * FROM `$tabla` WHERE `$item` = :valor");
			$stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
			$stmt->execute();

			$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
			return $resultado ? $resultado : false;
		} catch (PDOException $e) {
			error_log("Error en mdlConsultarUnCursoInicio: " . $e->getMessage());
			return false;
		} catch (InvalidArgumentException $e) {
			error_log("Error de validación en mdlConsultarUnCursoInicio: " . $e->getMessage());
			return false;
		} catch (Exception $e) {
			error_log("Error general en mdlConsultarUnCursoInicio: " . $e->getMessage());
			return false;
		} finally {
			if (isset($stmt)) {
				$stmt = null;
			}
		}
	}

	/**
	 * Obtiene los cursos destacados para mostrar en el carrusel
	 * @param string $tabla Nombre de la tabla (debe ser 'curso')
	 * @param int $limite Cantidad máxima de cursos a obtener
	 * @return array Lista de cursos destacados
	 */
	static public function mdlObtenerCursosDestacados($tabla, $limite = 3)
	{
		try {
			// Validar tabla
			if ($tabla !== 'curso') {
				throw new InvalidArgumentException("Solo se permite la tabla 'curso' para cursos destacados");
			}

			// Validar límite
			$limite = (int) $limite;
			if ($limite <= 0 || $limite > 50) {
				$limite = 3; // Valor por defecto seguro
			}

			$conexion = Conexion::conectar();

			// Verificar si existe la columna 'destacado', si no, usar fecha de registro
			$stmt = $conexion->prepare("SHOW COLUMNS FROM `$tabla` LIKE 'destacado'");
			$stmt->execute();
			$tieneDestacado = $stmt->fetch();

			if ($tieneDestacado) {
				// Si existe la columna destacado
				$sql = "SELECT * FROM `$tabla` WHERE destacado = 1 AND estado = 'activo' ORDER BY fecha_registro DESC LIMIT :limite";
			} else {
				// Si no existe, obtener los más recientes activos
				$sql = "SELECT * FROM `$tabla` WHERE estado = 'activo' ORDER BY fecha_registro DESC LIMIT :limite";
			}

			$stmt = $conexion->prepare($sql);
			$stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
			$stmt->execute();

			$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $resultado ? $resultado : [];
		} catch (PDOException $e) {
			error_log("Error en mdlObtenerCursosDestacados: " . $e->getMessage());
			return [];
		} catch (InvalidArgumentException $e) {
			error_log("Error de validación en mdlObtenerCursosDestacados: " . $e->getMessage());
			return [];
		} catch (Exception $e) {
			error_log("Error general en mdlObtenerCursosDestacados: " . $e->getMessage());
			return [];
		} finally {
			if (isset($stmt)) {
				$stmt = null;
			}
		}
	}

	/**
	 * Obtiene cursos con información enriquecida para la página de inicio
	 * @param int $limite Cantidad máxima de cursos a obtener
	 * @param string $ordenPor Campo por el que ordenar ('fecha_registro', 'nombre', 'valor')
	 * @param string $direccion Dirección del ordenamiento ('ASC', 'DESC')
	 * @return array Lista de cursos con información adicional
	 */
	static public function mdlObtenerCursosParaInicio($limite = 6, $ordenPor = 'fecha_registro', $direccion = 'DESC')
	{
		try {
			// Validaciones
			$limite = (int) $limite;
			if ($limite <= 0 || $limite > 50) {
				$limite = 6;
			}

			$camposOrdenPermitidos = ['fecha_registro', 'nombre', 'valor'];
			if (!in_array($ordenPor, $camposOrdenPermitidos)) {
				$ordenPor = 'fecha_registro';
			}

			$direccion = strtoupper($direccion);
			if (!in_array($direccion, ['ASC', 'DESC'])) {
				$direccion = 'DESC';
			}

			$conexion = Conexion::conectar();

			// Consulta con JOIN para obtener información de categoría y profesor
			$sql = "SELECT 
						c.*,
						cat.nombre as categoria_nombre,
						p.nombre as profesor_nombre,
						p.foto as profesor_foto
					FROM curso c
					LEFT JOIN categoria cat ON c.id_categoria = cat.id
					LEFT JOIN persona p ON c.id_persona = p.id
					WHERE c.estado = 'activo'
					ORDER BY c.`$ordenPor` $direccion
					LIMIT :limite";

			$stmt = $conexion->prepare($sql);
			$stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
			$stmt->execute();

			$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $resultado ? $resultado : [];
		} catch (PDOException $e) {
			error_log("Error en mdlObtenerCursosParaInicio: " . $e->getMessage());
			return [];
		} catch (Exception $e) {
			error_log("Error general en mdlObtenerCursosParaInicio: " . $e->getMessage());
			return [];
		} finally {
			if (isset($stmt)) {
				$stmt = null;
			}
		}
	}
}
