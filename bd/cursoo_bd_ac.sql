-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-09-2025 a las 17:50:44
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cursoo_bd_ac`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(300) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `descripcion`, `fecha_registro`) VALUES
(1, 'Stop Motion', 'La animación en volumen​ o animación fotograma a fotograma​ es una técnica de animación que consiste en aparentar el movimiento de objetos estáticos por medio de una serie de imágenes fijas sucesivas.', '2025-07-14 11:21:58'),
(2, 'Animación 3D', 'La animación 3D usa gráficos por computadora para que parezca que los objetos se mueven en un espacio tridimensional.', '2025-07-14 11:21:58'),
(3, 'Robotica', 'La robotica del siglo XXI', '2025-08-11 15:42:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id` int(11) NOT NULL,
  `url_amiga` varchar(100) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `descripcion` text NOT NULL,
  `lo_que_aprenderas` text DEFAULT NULL,
  `requisitos` text DEFAULT NULL,
  `para_quien` text DEFAULT NULL,
  `banner` varchar(300) DEFAULT NULL,
  `promo_video` varchar(150) DEFAULT NULL,
  `valor` int(11) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_persona` int(11) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id`, `url_amiga`, `nombre`, `descripcion`, `lo_que_aprenderas`, `requisitos`, `para_quien`, `banner`, `promo_video`, `valor`, `id_categoria`, `id_persona`, `estado`, `fecha_registro`) VALUES
(1, 'ni-os-que-hacen', 'NIÑOS QUE HACEN', 'Entiende la programación desde adentro. Descubre la lógica, los conceptos y el pensamiento detrás de todo software, sin necesidad de saber programar.\n\n¿Te gustaría entender cómo piensan los programadores?\n\n¿Cómo se crean las aplicaciones y por qué la programación es la habilidad más poderosa del siglo XXI?\n\n\n\nEste curso es tu puerta de entrada: no vas a aprender un lenguaje específico ni a escribir código, pero sí vas a incorporar la base lógica y conceptual que te permitirá avanzar hacia cualquier tecnología en el futuro.\n\n\n\n¿CÓMO ESTÁ ORGANIZADO ESTE CURSO?\n\nNuestro programa está diseñado para que entiendas y practiques, en tan solo 6 días de estudio intensivo, los fundamentos más importantes de la programación:\n\nCada día vas a descubrir un concepto clave a través de lecciones claras, ejemplos y ejercicios de pensamiento lógico.\n\nCada sección incluye cuestionarios para afianzar tu comprensión.\n\nCada jornada termina con un proyecto práctico para aplicar lo aprendido y un resumen reflexivo para celebrar tu progreso.\n\n\n\n¿QUÉ VAS A APRENDER?\n\nA lo largo de este curso vas a:\n\nEntender qué es la programación y cómo se relaciona con el mundo que te rodea.\n\nDescubrir qué es un programa, cómo se construye y para qué sirve.\n\nAprender la diferencia entre algoritmos, programas y lenguajes de programación.\n\nConocer el ciclo de vida de un programa y cómo se transforma una idea en una solución concreta.\n\nComprender los conceptos de variable, dato, tipo, función, objeto, ciclo y condicional, sin preocuparte por la sintaxis.\n\nPracticar con ejemplos visuales y diagramas de flujo que te ayudarán a pensar como un programador.\n\nReconocer y aplicar el pensamiento computacional en la vida diaria.\n\nReflexionar sobre los distintos paradigmas de la programación y para qué sirve cada uno.\n\nEntrenar tu mente para resolver problemas, analizar situaciones y descomponer desafíos en pasos claros y ordenados.\n\n\n\n¿POR QUÉ ESTE CURSO ES DIFERENTE?\n\nNo necesitás experiencia previa. Solo curiosidad y ganas de aprender.\n\nSin código ni tecnicismos complicados. Todo se explica desde lo conceptual, usando ejemplos claros, ejercicios de lógica y actividades prácticas.\n\nIdeal para quienes quieren empezar: ya sea que quieras ser programador, liderar un equipo técnico, o simplemente entender cómo funcionan las herramientas digitales, este curso te va a dar la base que necesitás.\n\nCierre diario con “ResuMate”: una sección para reflexionar, repasar y celebrar tu avance.\n\n\n\n\n\n¿QUIÉN SOY Y POR QUÉ PUEDO AYUDARTE?\n\nMi nombre es Federico Garay, soy instructor Best Seller en Udemy, con miles de estudiantes en todo el mundo. Mi misión es que aprendas desde cero, sin miedo y disfrutando cada paso del proceso. He visto cómo la programación cambia vidas, y quiero ayudarte a que vos también lo consigas.\n\n\n\nY para este curso me he asociado con el exitoso instructor Ezequiel Pratissoli, quien ha enseñado a miles de personas a programar a través de sus exitosas redes. Sus populares métodos de enseñanza han revolucionado la forma de aprender desde el principio, y llegar hasta los detalles.\n\n\n\nRecuerda que Udemy te da garantía de devolución total durante 30 días. ¡Puedes probarlo sin riesgo!\n\n\n\nNos vemos en la lección #1\n\nFEDE', 'Comprender qué es la programación y por qué es una habilidad clave en el mundo actual.\nEstar preparado para comenzar a aprender cualquier lenguaje de programación con una base conceptual sólida.\nIdentificar los componentes esenciales de un programa: entradas, procesos y salidas.\nDesarrollar pensamiento lógico y estructurado para abordar desafíos de manera secuencial.\nExplicar el concepto de algoritmo y su importancia en la resolución de problemas.\nEntender cómo funcionan las estructuras condicionales y los bucles en la lógica de programación.\nReconocer la importancia de la abstracción y la modularidad en el diseño de soluciones.\nAnalizar cómo los datos se representan y transforman dentro de un programa.\nReflexionar sobre el rol del programador como diseñador de soluciones automatizadas.', 'No se necesita experiencia previa en programación.\nNo se requiere saber matemáticas avanzadas ni tener formación técnica.\nEs indispensable tener curiosidad, ganas de aprender y una mente abierta.\nAcceso a un dispositivo con conexión a internet.', 'Personas completamente nuevas en el mundo de la programación que quieren entender cómo funciona “por dentro” antes de aprender un lenguaje específico.\nEstudiantes, profesionales o autodidactas que desean desarrollar pensamiento computacional y habilidades de resolución de problemas lógicos.\nPersonas curiosas por la tecnología que siempre se preguntaron “¿qué es programar?” pero nunca supieron por dónde empezar.\nDocentes de otras áreas que buscan comprender los principios básicos de la programación para integrarlos en sus clases.\nEmprendedores, creadores o líderes de equipos técnicos que necesitan comprender el lenguaje de los programadores para comunicarse mejor con sus equipos.\nPersonas que quieren tomar decisiones informadas antes de comprometerse con un lenguaje o una carrera técnica.\nInteresados en el desarrollo personal que desean entrenar su mente en lógica, análisis y pensamiento estructurado.', 'storage/public/banners/1 (1).png', 'storage/public/promoVideos/68a87ff700cad_1755873271.mp4', 80000, 2, 1, 'activo', '2025-07-14 16:21:17'),
(2, 'peliculas-y-cortometrajes-en', 'NIÑOS QUE HACEN PELICULAS Y CORTOMETRAJES EN CALIBELULA', 'Calibélula vivirá un nuevo encuentro con niños y adolescentes, orientados bajo el liderazgo de procesos como La Escuela Audiovisual, Belén de los Andaquies, en el Caquetá, Mi Primer corto Infantil de México y Subí que te veo de Argentina.\r\n', 'Comprender qué es la programación y por qué es una habilidad clave en el mundo actual.\nEstar preparado para comenzar a aprender cualquier lenguaje de programación con una base conceptual sólida.\nIdentificar los componentes esenciales de un programa: entradas, procesos y salidas.\nDesarrollar pensamiento lógico y estructurado para abordar desafíos de manera secuencial.\nExplicar el concepto de algoritmo y su importancia en la resolución de problemas.\nEntender cómo funcionan las estructuras condicionales y los bucles en la lógica de programación.\nReconocer la importancia de la abstracción y la modularidad en el diseño de soluciones.\nAnalizar cómo los datos se representan y transforman dentro de un programa.\nReflexionar sobre el rol del programador como diseñador de soluciones automatizadas.', NULL, NULL, 'storage/public/banners/1 (2).png', 'storage/public/promoVideos/68a87ff700cad_1755873271.mp4', 70000, 2, 1, 'activo', '2025-07-14 16:23:59'),
(3, 'talleres-libelulitos', 'Talleres Libelulit@s y convocatoria a realizadores cinematográficos', 'La magia del cine y el audiovisual regresan a Cali para el mundo, a partir del 30 de abril, cuando se hará el lanzamiento oficial del 5º- Festival Internacional de Cine Infantil y Juvenil, Calibélula, con la apertura de la convocatoria dirigida a directores, realizadores y productores para que envíen sus producciones cinematográficas antes del 15 de Junio, a través de Festhome y Google Drive. La convocatoria también estará dirigida a instituciones educativas, niños y jóvenes en general para que participen de los talleres de Libelulit@s que se dictáran gratuitamente a partir del mes de mayo.', 'Comprender qué es la programación y por qué es una habilidad clave en el mundo actual.\nEstar preparado para comenzar a aprender cualquier lenguaje de programación con una base conceptual sólida.\nIdentificar los componentes esenciales de un programa: entradas, procesos y salidas.\nDesarrollar pensamiento lógico y estructurado para abordar desafíos de manera secuencial.\nExplicar el concepto de algoritmo y su importancia en la resolución de problemas.\nEntender cómo funcionan las estructuras condicionales y los bucles en la lógica de programación.\nReconocer la importancia de la abstracción y la modularidad en el diseño de soluciones.\nAnalizar cómo los datos se representan y transforman dentro de un programa.\nReflexionar sobre el rol del programador como diseñador de soluciones automatizadas.', NULL, NULL, 'storage/public/banners/1 (3).png', 'storage/public/promoVideos/68a87ff700cad_1755873271.mp4', 100000, 2, 1, 'activo', '2025-07-14 16:24:30'),
(4, 'curso-php', 'COMO HACER UNA PAGINA WEB', 'Pagina web en PHP y JS', NULL, NULL, NULL, 'storage/public/banners/1 (5).jpg', 'storage/public/promoVideos/68a87ff700cad_1755873271.mp4', 90000, 2, 1, 'activo', '2025-07-14 16:25:20'),
(5, 'tecnologia-en-aritmetica', 'Tecnologia en Aritmetica', 'Curso es muy importante', NULL, NULL, NULL, 'storage/public/banners/687a7e30812fe_images.jpg', 'storage/public/promoVideos/68a87ff700cad_1755873271.mp4', 70000, 1, 2, 'activo', '2025-07-18 17:02:40'),
(6, 'tecnologia-en-aritmeticaaaaaaaaa', 'Tecnologia en Aritmeticaaaaaaaaa', 'sadasdad', NULL, NULL, NULL, 'storage/public/banners/687e4f78d35ce_3W2KSMRJUVJHDGDB5UZZCJ3PTE.jpg', NULL, 80, 1, 1, 'activo', '2025-07-21 14:32:24'),
(7, 'tecnologia-en-programaci-n', 'Tecnologia en Programación', 'gghgfhfh', NULL, NULL, NULL, 'storage/public/banners/687e545a8f012_premium_photo-1666672388644-2d99f3feb9f1.jpg', NULL, 23, 2, 1, 'activo', '2025-07-21 14:53:14'),
(8, 'tecnologia-en-logica', 'Tecnologia en logica', 'dasdasd', NULL, NULL, NULL, 'storage/public/banners/687e564c4c2bd_depositphotos_234542254-stock-illustration-man-profile-smiling-cartoon-vector.jpg', NULL, 23, 1, 2, 'activo', '2025-07-21 15:01:32'),
(9, 'tecnologia-en-perritos', 'Tecnologia en perritos', 'La tecnología ha transformado la forma en que cuidamos a nuestros perros, ofreciendo herramientas para mejorar su salud, seguridad y bienestar. Desde dispositivos portátiles que monitorean su actividad y signos vitales, hasta sistemas de ubicación GPS para evitar pérdidas, la innovación tecnológica ha llegado para quedarse en el mundo canino.\r\nWearables:\r\nDispositivos como collares inteligentes con GPS y sensores de salud, que permiten a los dueños monitorear la actividad física, la frecuencia cardíaca y respiratoria, e incluso detectar problemas de salud antes de que se manifiesten síntomas visibles. \r\nComederos y bebederos automáticos:\r\nProgramables para asegurar que tu perro reciba la cantidad adecuada de alimento y agua, incluso cuando no estás en casa. \r\nCámaras de vigilancia:\r\nPermiten observar a tu mascota a distancia, interactuar con ella a través de audio y video, y asegurarte de que está bien. ', NULL, NULL, NULL, 'storage/public/banners/687e68537ff02_287133.jpg', NULL, 80, 1, 1, 'activo', '2025-07-21 16:18:27'),
(10, 'tecnologia-en-tarott', 'Tecnologia en tarott', 'maksdklajd', NULL, NULL, NULL, 'storage/public/banners/687e871a23717_2871333.jpg', NULL, 34, 1, 2, 'activo', '2025-07-21 18:29:46'),
(11, 'metodolog-a-de-la-programaci-n-pseint-dfd-c-', 'Metodología de la Programación | PSeInt, DFD, C++', 'Dirigido para principiantes que quieren aprender a crear algoritmos en PSeInt para migrar su contenido a diagramas de flujo y después a un lenguaje de programación conociendo sus fundamentos previos y entender el manejo de los programas.\n\nConocerás la metodología para resolver problemas usando la programación en un ambiente institucional educativo y laboral \n\nConceptos básicos de la programación\n\nDiseñaras tus primeros algoritmos en PSeInt\n\nConoce el software que crea diagramas de flujo funcionales como lo es DFD\n\nTemario Principal\n\nBienvenidos\n\nMetas\n\nUniversidad\n\nVentajas de estudiar programación\n\nLenguajes de programación\n\n¿Qué es un algoritmo?\n\nCaracteristicas de los algoritmos\n\n¿Qué es un diagrama de flujo?\n\nSímbolos utilizados en los diagramas de flujo\n\n¿Qué es un pseudocódigo?\n\n¿Qué es PSeInt?\n\nDefiniciones\n\nUna variable es un espacio de la memoria donde guardar información\n\nLa información que se guarda en la variable puede ser de diversos tipos y puede ir cambiando a lo largo del programa\n\nCada variable tiene un tipo de dato asociado, por lo que siempre guardará el mismo tipo de dato\n\nUna variable que guarde un número no podrá guardar después otro tipo que no sea un número\n\nLas expresiones son combinaciones de constantes, variables y operadores que nos permiten trabajar con los datos\n\nDependiendo de los operadores utilizados en ellas, pueden ser de varios tipos: aritméticas, relacionales, lógicas, alfanuméricas y de asignación...................', NULL, NULL, NULL, 'storage/public/banners/687fbedfc3004_2871333a.jpg', NULL, 90000, 2, 1, 'activo', '2025-07-22 16:39:59'),
(12, 'tecnologia-en-tarottaa', 'Tecnologia en tarottaa', 'describiendo', 'muchisimo', 'no experiencia', 'Para nadie', 'storage/public/banners/687fc17e343f8_2871333aa.jpg', NULL, 234324, 1, 2, 'activo', '2025-07-22 16:51:10'),
(14, 'analitica-de-leche-de-vaca', 'Analitica de leche de vaca', 'asdasdas', 'dasdsadasd', 'asdasdsadasd', 'asdasdasd', 'storage/public/banners/68a77db350d47_1755807155.jpg', NULL, 40000, 3, 2, 'activo', '2025-08-21 20:12:35'),
(15, 'tecnologia-en-tarottaa', 'Tecnologia en tarottaa', 'dsadada', 'asdasdsa', 'asdasdas', 'dsadasdsad', 'storage/public/banners/68a87ff700a33_1755873271.jpg', 'storage/public/promoVideos/68a87ff700cad_1755873271.mp4', 90000, 1, 2, 'activo', '2025-08-22 14:34:31'),
(16, 'curso-de-generalidades-1', 'Curso de Generalidades 1', 'sssaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaayo viendo que en verCursoProfe.php el if de // Procesar actualización del curso básico (migrado desde editarCursoProfe.php) no está procesando las secciones, el contenido de secciones, el progreso para poder conectarse con la ffffffffffffffs o actualizar o eliminar las secciones   y las secciones se van a manejar de la siguiente manera:\n1. Para Insertar/actualizar sección (curso_secciones):\ncrea una función PHP (PDO) que inserte una sección en `curso_secciones`\n// Parámetros: $idCurso, $titulo, $descripcion, $orden (int)\n// Retorna: id de la sección creada\n// Usa SQL INSERT INTO curso_secciones (id_curso, titulo, descripcion, orden) VALUES (....);\n// No Manejes timestamps por defecto.\n// Valida que $orden sea >= 1. \n// Si existe, crea también una función actualizarSeccion($idSeccion, $titulo, $descripcion, $orden)\n2. Insertar contenido de tipo video/pdf en seccion_contenido\ncrea una función PHP (PDO) crearContenido($idSeccion, $titulo, $descripcion, $tipo, $duracion, $orden)\n// `tipo` ∈ {\'video\',\'pdf\'}. Para video, `duracion` = \'MM:SS\' o \'HH:MM:SS\'; para PDF puede ser NULL.\n// SQL: INSERT INTO seccion_contenido (id_seccion, titulo, descripcion, tipo, duracion, orden) VALUES (…);\n// Retorna: id del contenido\n// Valida que $orden >= 1 y tipo válido.\n3. Subir archivo y crear asset en seccion_contenido_assets\nescribe una función GuardarContenidoAsset($idContenido, $assetTipo, $rutaDestinoAbsoluta, $urlPublica = null, $tamanoBytes = null, $duracionSegundos = null)\n// Inserta en seccion_contenido_assets (id_contenido, asset_tipo, storage_path, public_url, tamano_bytes, duracion_segundos)\n// asset_tipo: \'attachment\' para MP4 directo, \'pdf\' para PDF, no generas miniatura\n4. Validaciones de subida MP4 ≤10min / hasta 1280×720 y PDF\nen el controller de subida, valida MIME:\n// - video/mp4 (max 10 min, 1280x720) -> rechaza si excede\n// - application/pdf\n// Calcula duración del video (si ffprobe está disponible) y pásala a crearContenido/guardarContenidoAsset\n// Construye rutas tipo que cree las carpetas si no existen \npara video usa storage/public/section_assets/{curso}/{seccion}/{contenido}/video/archivo.mp4 \npara pdf usa storage/public/section_assets/{curso}/{seccion}/{contenido}/pdf/archivo.pdf \n// Asegura permisos de carpeta (Windows + XAMPP), mueve el archivo y crea el asset\n\n5. Marcar progreso al consumir el contenido\nfunción upsertProgreso($idContenido, $idEstudiante, $visto, $progresoSegundos, $porcentaje)\n// Si existe fila (contenido+estudiante), haz UPDATE; si no, INSERT.\n// Si $porcentaje >= 90, fuerza $visto = 1 y porcentaje = 100.\n// Actualiza `ultima_vista` automáticamente (columna con ON UPDATE).', 'aaaaaaaaaaaaaaaaaaaainteligencia artificial, menús, distintos niveles y con acabad\nAprender nociones de diseño de juego básicas y avanzadas así como de balance de jugabilida', 'zzzzzzzaaaaaaaaaaaaaaaaaLooooos requisitos son Crear un videojuego 3d AAA con inteligencia artificial, menús, distintos niveles y con acabadasdasdasdasdasd\nAprender nociones de diseño de juego básicas y avanzadas así como de balance de jugabilida', 'zzzzzzzzzzzzaaaaaaaaaaaaaaaaaaeeeeeeeeeeeeEs para Crear un videojuego 3d AAA con inteligencia artificial, menús, distintos niveles y con acabad\nAprender nociones de diseño de juego básicas y avanzadas así como de balance de jugabilidaasdasdasdasd', 'storage/public/banners/68b48d809a2f9_1756663168.png', 'storage/public/promoVideos/68c08833ad608_1757448243.mp4', 30000, 1, 1, 'activo', '2025-08-31 17:59:28'),
(17, 'curso-prueba-secciones-test', 'Curso de Prueba Secciones - TEST DIRECTO', 'Descripción de prueba directa', 'Test directo', 'Test directo', 'Test directo', '', '', 88000, 1, 10, 'activo', '2025-09-05 02:10:03'),
(18, 'asdasdasd', 'asdasdasd', 'asdasdasdsaasdasdasdsaasdasdasdsaasdasdasdsaasdasdasdsa', 'asdasdasdsaasdasdasdsaasdasdasdsa', 'asdasdasdsaasdasdasdsaasdasdasdsa', 'asdasdasdsaasdasdasdsaasdasdasdsa', 'storage/public/banners/68bd9e76149b3_1757257334.png', 'storage/public/promoVideos/68bd9e7615168_1757257334.mp4', 0, 2, 1, 'activo', '2025-09-07 15:02:15'),
(19, 'cursod-e-pruebaacursod-e-pruebaacursod-e-pruebaa', 'Cursod e pruebaaCursod e pruebaaCursod e pruebaa', 'Cursod e pruebaaCursod e pruebaaCursod e pruebaaCursod e pruebaaaaaaaaaaaaaaaaaaaaaaaaa', 'Cursod e pruebaaCursod e pruebaa', 'Cursod e pruebaaCursod e pruebaa', 'Cursod e pruebaaCursod e pruebaa', 'storage/public/banners/68bd9ea87fb25_1757257384.png', 'storage/public/promoVideos/68be2c347aa60_1757293620.mp4', 0, 2, 1, 'borrador', '2025-09-07 15:03:04'),
(20, 'curso-1', 'curso 1', 'dasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsad\ndasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasds\naddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasd\nsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasda\nsdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddas\ndasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsadda\nsdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsadd\nasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdas\ndsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdas\ndsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdas\ndsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdas\ndsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasd\nsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasds\naddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasds\naddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasds\naddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddsdasdsadd', 'dasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasd\n\ndasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasd', 'dasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasd', 'dasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasd\n\ndasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasdsaddasdasd', 'storage/public/banners/68bf3e980be1c_1757363864.png', 'storage/public/promoVideos/68bf3ea6f1890_1757363878.mp4', 0, 1, 8, 'activo', '2025-09-08 20:02:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso_secciones`
--

CREATE TABLE `curso_secciones` (
  `id` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso_secciones`
--

INSERT INTO `curso_secciones` (`id`, `id_curso`, `titulo`, `descripcion`, `orden`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(28, 16, 'intro', 'por queq', 2, 'activo', '2025-09-07 20:15:43', '2025-09-10 14:30:16'),
(29, 16, '3', '', 3, 'activo', '2025-09-07 20:15:47', '2025-09-07 20:15:47'),
(30, 16, '4', '', 4, 'activo', '2025-09-07 20:15:52', '2025-09-07 20:15:52'),
(32, 16, '78', '', 5, 'activo', '2025-09-07 23:59:20', '2025-09-07 23:59:20'),
(34, 16, '78', '', 7, 'activo', '2025-09-08 01:05:39', '2025-09-08 01:05:39'),
(35, 16, '79', '', 8, 'activo', '2025-09-08 01:05:47', '2025-09-08 01:05:47'),
(36, 16, '85', '', 9, 'activo', '2025-09-08 01:05:54', '2025-09-08 01:05:54'),
(37, 16, 'Conclusion1', 'ultima2', 10, 'activo', '2025-09-08 16:48:25', '2025-09-08 16:56:11'),
(38, 20, '1', '2', 1, 'activo', '2025-09-08 20:10:23', '2025-09-08 20:10:23'),
(39, 20, '2', '', 2, 'activo', '2025-09-08 20:45:16', '2025-09-08 20:45:16'),
(40, 20, '3', '', 3, 'activo', '2025-09-08 20:45:20', '2025-09-08 20:45:20'),
(41, 16, 'introduccion', 'dasdasd', 11, 'activo', '2025-09-08 20:57:32', '2025-09-08 20:57:32'),
(42, 16, '344e', 'ddd', 12, 'activo', '2025-09-10 15:13:25', '2025-09-10 15:13:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_verificacion_tokens`
--

CREATE TABLE `email_verificacion_tokens` (
  `id` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `usado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestion_pagos`
--

CREATE TABLE `gestion_pagos` (
  `id` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_inscripcion` int(11) DEFAULT NULL,
  `proveedor` enum('mercadopago') NOT NULL DEFAULT 'mercadopago',
  `moneda` char(3) NOT NULL DEFAULT 'COP',
  `monto_total` int(11) NOT NULL,
  `external_payment_id` varchar(100) DEFAULT NULL,
  `preference_id` varchar(100) DEFAULT NULL,
  `init_point` varchar(500) DEFAULT NULL,
  `status` enum('pendiente','aprobado','rechazado','cancelado','devuelto','en_proceso','expirado') NOT NULL DEFAULT 'pendiente',
  `status_detail` varchar(100) DEFAULT NULL,
  `payer_id` varchar(100) DEFAULT NULL,
  `payer_email` varchar(150) DEFAULT NULL,
  `payment_method_id` varchar(50) DEFAULT NULL,
  `installments` int(11) DEFAULT NULL,
  `card_last4` char(4) DEFAULT NULL,
  `comprobante_url` varchar(500) DEFAULT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `estado` varchar(100) DEFAULT 'pendiente',
  `finalizado` tinyint(1) DEFAULT 0,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id`, `id_curso`, `id_estudiante`, `estado`, `finalizado`, `fecha_registro`) VALUES
(22, 2, 3, 'pendiente', 0, '2025-09-16 13:35:10'),
(23, 20, 3, 'activo', 0, '2025-09-16 15:49:21'),
(24, 18, 3, 'activo', 0, '2025-09-16 15:49:30'),
(25, 7, 3, 'pendiente', 0, '2025-09-17 15:16:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_ingreso`
--

CREATE TABLE `log_ingreso` (
  `id` int(11) NOT NULL,
  `id_persona` int(11) DEFAULT NULL,
  `ip_usuario` varchar(45) DEFAULT NULL,
  `navegador` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `log_ingreso`
--

INSERT INTO `log_ingreso` (`id`, `id_persona`, `ip_usuario`, `navegador`, `fecha_registro`) VALUES
(0, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 10:52:59'),
(1, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-10 14:18:20'),
(2, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-10 14:37:58'),
(3, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 11:37:56'),
(4, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 11:45:47'),
(5, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 11:51:54'),
(6, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 14:53:20'),
(7, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 14:57:04'),
(8, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-14 15:05:46'),
(9, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-15 08:23:45'),
(10, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-15 08:26:59'),
(11, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-15 08:42:12'),
(12, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-15 09:42:36'),
(13, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-15 09:43:02'),
(14, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-15 10:26:20'),
(15, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-15 11:35:24'),
(16, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-16 11:23:44'),
(17, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-17 12:06:06'),
(18, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 09:04:10'),
(19, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-18 14:44:19'),
(20, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 08:40:33'),
(21, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 08:40:39'),
(22, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 08:42:08'),
(23, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 09:28:01'),
(24, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 09:51:48'),
(25, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 09:52:49'),
(26, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 09:54:03'),
(27, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 11:17:00'),
(28, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 11:25:04'),
(29, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 11:43:17'),
(30, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 11:47:52'),
(31, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 11:49:40'),
(32, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 11:50:10'),
(33, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 12:01:31'),
(34, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:12:45'),
(35, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:22:50'),
(36, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:25:46'),
(37, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:29:28'),
(38, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:49:48'),
(39, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:49:58'),
(40, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:53:07'),
(41, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:53:26'),
(42, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:55:25'),
(43, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:55:28'),
(44, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 13:55:30'),
(45, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 14:07:06'),
(46, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 14:48:56'),
(47, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-21 14:54:06'),
(48, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 08:35:31'),
(49, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 08:46:40'),
(50, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 10:36:32'),
(51, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 10:36:41'),
(52, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 10:49:52'),
(53, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 11:19:44'),
(54, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 11:54:00'),
(55, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 14:19:51'),
(56, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 14:20:17'),
(57, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 15:04:35'),
(58, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 15:22:17'),
(59, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 15:35:15'),
(60, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 15:40:09'),
(61, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 15:41:13'),
(62, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 15:52:11'),
(63, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:37:57'),
(64, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 08:53:52'),
(65, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 09:17:10'),
(66, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 10:20:22'),
(67, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 12:09:16'),
(68, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 14:09:33'),
(69, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 14:19:57'),
(70, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-23 14:54:58'),
(71, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 13:57:55'),
(72, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 14:04:31'),
(73, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 14:08:59'),
(74, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 14:13:35'),
(75, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 14:23:34'),
(76, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 14:59:28'),
(77, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:00:16'),
(78, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:05:47'),
(79, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:08:03'),
(80, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:27:39'),
(81, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:30:17'),
(82, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:31:42'),
(83, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:32:27'),
(84, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:33:27'),
(85, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:41:26'),
(86, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:41:55'),
(87, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:45:02'),
(88, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:55:15'),
(89, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-04 15:58:34'),
(90, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 09:05:49'),
(91, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 09:31:38'),
(92, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 09:53:54'),
(93, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 09:54:34'),
(94, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 10:00:22'),
(95, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 10:12:56'),
(96, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 10:15:17'),
(97, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 10:15:55'),
(98, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 10:17:12'),
(99, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:06:43'),
(100, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:27:34'),
(101, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:27:36'),
(102, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:27:37'),
(103, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:35:01'),
(104, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:35:03'),
(105, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:35:06'),
(106, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:01'),
(107, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:03'),
(108, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:04'),
(109, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:06'),
(110, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:07'),
(111, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:08'),
(112, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:09'),
(113, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:09'),
(114, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:10'),
(115, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:11'),
(116, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:12'),
(117, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:13'),
(118, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:14'),
(119, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:15'),
(120, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:16'),
(121, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:17'),
(122, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:18'),
(123, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:19'),
(124, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:19'),
(125, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:20'),
(126, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:21'),
(127, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:22'),
(128, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:23'),
(129, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:24'),
(130, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:25'),
(131, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:26'),
(132, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:37:27'),
(133, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:49:12'),
(134, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:49:13'),
(135, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:52:50'),
(136, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:52:52'),
(137, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:53:57'),
(138, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:53:58'),
(139, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:00'),
(140, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:02'),
(141, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:03'),
(142, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:04'),
(143, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:13'),
(144, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:37'),
(145, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:39'),
(146, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:41'),
(147, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:42'),
(148, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 11:54:44'),
(149, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 12:02:26'),
(150, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 12:03:08'),
(151, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 12:03:46'),
(152, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 12:10:38'),
(153, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 12:11:42'),
(154, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 12:13:20'),
(155, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 14:25:54'),
(156, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 14:26:19'),
(157, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 14:26:38'),
(158, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 14:55:25'),
(159, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 15:02:24'),
(160, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 15:28:26'),
(161, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 15:34:28'),
(162, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 15:44:12'),
(163, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-05 15:56:15'),
(164, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 08:57:02'),
(165, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:08:36'),
(166, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:11:00'),
(167, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:21:41'),
(168, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:30:06'),
(169, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:30:25'),
(170, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:32:11'),
(171, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:33:49'),
(172, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:47:00'),
(173, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:48:47'),
(174, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:48:56'),
(175, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:49:28'),
(176, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:50:03'),
(177, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 11:50:24'),
(178, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:10:32'),
(179, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:12:59'),
(180, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:25:37'),
(181, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:39:57'),
(182, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:40:49'),
(183, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:41:00'),
(184, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:41:32'),
(185, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:41:43'),
(186, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:43:50'),
(187, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:44:07'),
(188, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:48:41'),
(189, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:54:06'),
(190, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 14:54:48'),
(191, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 15:08:33'),
(192, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 15:14:37'),
(193, 1, '::1', 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2025-08-06 15:15:51'),
(194, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 15:22:07'),
(195, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 15:51:49'),
(196, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 15:57:28'),
(197, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 16:07:00'),
(198, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 16:08:51'),
(199, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 16:18:43'),
(200, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 16:29:47'),
(201, 1, '::1', 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G955U Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2025-08-06 16:43:09'),
(202, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-06 16:44:42'),
(203, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:27:49'),
(204, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:47:15'),
(205, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:48:26'),
(206, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:48:55'),
(207, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:49:14'),
(208, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:49:31'),
(209, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:58:31'),
(210, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 09:08:51'),
(211, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 10:07:05'),
(212, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 10:08:07'),
(213, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 10:08:22'),
(214, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 10:08:36'),
(215, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 10:13:04'),
(216, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 10:38:55'),
(217, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 11:30:29'),
(218, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 11:38:41'),
(219, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 11:43:42'),
(220, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 15:52:56'),
(221, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-11 09:04:08'),
(222, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-11 15:44:46'),
(223, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 08:57:33'),
(224, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 08:58:22'),
(225, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 08:58:45'),
(226, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 09:00:11'),
(227, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 11:52:46'),
(228, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 14:30:11'),
(229, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 14:30:39'),
(230, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 14:33:26'),
(231, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 14:51:09'),
(232, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 14:53:02'),
(233, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:11:36'),
(234, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:12:59'),
(235, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:22:57'),
(236, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:23:07'),
(237, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:23:30'),
(238, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:23:55'),
(239, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:24:26'),
(240, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:24:35'),
(241, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:26:07'),
(242, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:47:55'),
(243, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:48:46'),
(244, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:48:56'),
(245, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:49:39'),
(246, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:51:13'),
(247, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-12 15:51:28'),
(248, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-13 09:13:17'),
(249, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-13 09:13:42'),
(250, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-13 09:13:55'),
(251, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 09:11:56'),
(252, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 09:12:57'),
(253, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-19 08:21:07'),
(254, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-20 09:44:38'),
(255, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-21 14:18:13'),
(256, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-21 14:59:43'),
(257, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-21 15:01:07'),
(258, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-22 09:04:54'),
(259, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-22 09:05:33'),
(260, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-22 14:48:56'),
(261, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-22 14:49:44'),
(262, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-26 16:00:57'),
(263, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-28 10:51:18'),
(264, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-28 10:52:28'),
(265, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-29 11:59:17'),
(266, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-29 19:58:45'),
(267, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-30 14:48:40'),
(268, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-30 14:49:47'),
(269, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-30 14:52:46'),
(270, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-30 19:25:44'),
(272, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-31 11:04:24'),
(273, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-31 11:04:38'),
(274, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-31 11:04:54'),
(275, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-01 21:25:53'),
(276, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-03 20:42:03'),
(277, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-03 20:43:08'),
(278, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 12:53:51'),
(279, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 18:22:37'),
(280, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 21:34:05'),
(281, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 21:52:16'),
(282, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 21:59:27'),
(283, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-04 22:06:30'),
(284, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-06 17:43:48'),
(285, 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-07 09:35:16'),
(286, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-07 21:17:19'),
(287, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-07 21:17:37'),
(288, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 10:53:47'),
(289, 8, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 15:00:21'),
(290, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 15:56:34'),
(291, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 16:03:30'),
(292, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 14:35:53'),
(293, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 14:36:01'),
(294, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 09:17:06'),
(295, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 09:17:17'),
(296, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 09:33:34'),
(297, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 09:35:12'),
(298, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 13:46:46'),
(299, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 13:46:46'),
(300, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 13:46:59'),
(301, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 13:47:13'),
(302, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-10 14:46:01'),
(303, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-11 08:38:54'),
(304, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-11 08:46:18'),
(305, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-11 13:23:06'),
(306, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-11 14:11:44'),
(307, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 08:37:27'),
(308, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 10:38:53'),
(309, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-15 13:14:59'),
(310, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-15 14:37:19'),
(311, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-15 14:44:55'),
(312, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-15 15:21:14'),
(313, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-15 15:44:39'),
(314, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-15 15:48:41'),
(315, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-15 15:50:50'),
(316, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-16 08:30:33'),
(317, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-16 10:03:33'),
(318, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-16 10:07:08'),
(319, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-16 10:48:57'),
(320, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-16 10:50:10'),
(321, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-16 11:28:26'),
(322, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-16 11:57:01'),
(323, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-16 11:57:08'),
(324, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-16 14:16:41'),
(325, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-17 09:09:27'),
(326, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-17 09:24:02');
INSERT INTO `log_ingreso` (`id`, `id_persona`, `ip_usuario`, `navegador`, `fecha_registro`) VALUES
(327, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-17 10:04:35'),
(328, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-17 10:15:30'),
(329, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-17 10:16:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `id_remitente` int(11) DEFAULT NULL,
  `id_destinatario` int(11) DEFAULT NULL,
  `asunto` varchar(150) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `fecha_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `id` int(11) NOT NULL,
  `usuario_link` varchar(100) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `verificacion` int(11) NOT NULL DEFAULT 0,
  `foto` varchar(100) DEFAULT 'vistas/img/usuarios/default/default.png',
  `profesion` varchar(300) DEFAULT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `biografia` text DEFAULT NULL,
  `pais` varchar(200) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `mostrar_email` tinyint(1) DEFAULT 0 COMMENT 'Mostrar email públicamente',
  `mostrar_telefono` tinyint(1) DEFAULT 0 COMMENT 'Mostrar teléfono públicamente',
  `mostrar_identificacion` tinyint(1) DEFAULT 0 COMMENT 'Mostrar número de identificación públicamente',
  `numero_identificacion` varchar(50) DEFAULT NULL COMMENT 'Número de identificación del usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id`, `usuario_link`, `nombre`, `email`, `password`, `verificacion`, `foto`, `profesion`, `telefono`, `direccion`, `biografia`, `pais`, `ciudad`, `estado`, `mostrar_email`, `mostrar_telefono`, `mostrar_identificacion`, `numero_identificacion`, `fecha_registro`) VALUES
(1, 'clienteRegistro', 'Mauricio Muñoz', 'mauriciomuozsanchez12@gmail.com', '$2y$10$XJjXQcSuxiVhdhkovif7B.YfVKNSkVEK2Tl0ZBJa48CDWKY3.r80a', 1, 'storage/public/usuarios/1/perfil_1757360359_504.jpg', 'Escritor de novelas acuaticas', '3135529157', 'cra - 26k8121', 'J. Robert Oppenheimer[a]​ (Nueva York, 22 de abril de 1904-Princeton, Nueva Jersey, 18 de febrero de 1967) fue un físico teórico estadounidense y profesor de física en la Universidad de California en Berkeley. Es una de las personas a menudo nombradas como «padre de la bomba atómica» debido a su destacada participación en el Proyecto Manhattan, el proyecto que consiguió desarrollar las primeras armas nucleares de la historia, durante la Segunda Guerra Mundial. La primera bomba nuclear fue detonada el 16 de julio de 1945 en la Prueba Trinity, en Nuevo México, Estados Unidos. Oppenheimer declararía más tarde que le vinieron a la mente las palabras del Bhagavad-gītā: «Ahora me he convertido en la muerte, el destructor de mundos».[4]​[b]​ Oppenheimer siempre expresó su pesar por el fallecimiento de víctimas inocentes cuando las bombas nucleares fueron lanzadas contra los japoneses en Hiroshima y Nagasaki los días 6 y 9 de agosto de 1945.\r\n\r\nDespués de la guerra ocupó el cargo de asesor jefe en la recién creada Comisión de Energía Atómica de los Estados Unidos y utilizó su posición para abogar por el control internacional del poder nuclear, evitar la proliferación de armamento nuclear y frenar la carrera armamentística entre Estados Unidos y la Unión Soviética. Después de provocar la ira de numerosos políticos por sus opiniones públicas se le acabaron retirando sus pases de seguridad, perdiendo el acceso a los documentos militares secretos de su país, y se le acabó despojando de su influencia política directa durante una muy publicitada audiencia en 1954. En esa década Estados Unidos vivía en el macartismo y todas aquellas personas sospechosas de simpatizar con el comunismo o simplemente de ser disidentes fueron perseguidas por el gobierno; Oppenheimer pudo continuar escribiendo, trabajando en física y dando conferencias. Nueve años después de la audiencia, los presidentes John F. Kennedy y Lyndon B. Johnson le concedieron y otorgaron respectivamente el Premio Enrico Fermi como un gesto de rehabilitación de su figura.', 'Ecuador', 'Cundinamarca', 'activo', 1, 1, 0, '66847374', '2025-07-10 19:18:15'),
(2, 'clienteRegistro', 'Derly Pipicano', 'm-mau55@hotmail.com', '$2y$10$AlrkWRiRR2kIBFLn7qA.nux7d6//Va6PB818ZJK7NnrENSAv8a6kS', 0, 'storage/public/usuarios/2/perfil_1755891648_811.jpg', NULL, NULL, NULL, 'No hay mucho que contar de mi a parte de que soy profesora', 'Ecuador', 'Quito', 'activo', 1, 1, 1, NULL, '2025-07-15 13:42:06'),
(3, 'clienteRegistro', 'Carlos Sanchez', 'mauro@gmail.com', '$2y$10$LwHLfPUetTXIxcrXlEp9hO/4brtB2Gdh5MbGRN.LmE1GpFa4OJpBO', 0, 'vistas/img/usuarios/default/default.png', NULL, NULL, NULL, NULL, NULL, NULL, 'activo', 0, 0, 0, NULL, '2025-08-05 14:28:33'),
(4, 'clienteRegistro', 'Carlitos', 'mauriciomuozschez12@gmail.com', '$2y$10$dzomT0r6vKvgANN6W/AXYuRgMfkpmboCRlD0IJz35UFuYzwyIkgTG', 0, 'vistas/img/usuarios/default/default.png', NULL, NULL, NULL, NULL, NULL, NULL, 'activo', 0, 0, 0, NULL, '2025-08-05 16:37:46'),
(5, 'clienteRegistro', 'Derly Pipicanos', 'nchez12@gmail.com', '$2y$10$wDpjPYqssXWyW/kNmKgnTu0x2pqzVLcwc/cvqFOSfSDBZhOv6CX7y', 1, 'vistas/img/usuarios/default/default.png', NULL, NULL, NULL, NULL, NULL, NULL, 'activo', 0, 0, 0, NULL, '2025-08-05 19:49:30'),
(6, 'clienteRegistro', 'Mauricio Muñoz', 'mauriciomuozhez12@gmail.com', '$2y$10$94VTVOeFRnB3oJOHsh5SS.3e5aDOs.m0ThuyUIenG9VGR58pRaOLm', 1, 'vistas/img/usuarios/default/default.png', NULL, NULL, NULL, NULL, NULL, NULL, 'activo', 0, 0, 0, NULL, '2025-08-05 19:50:00'),
(7, 'clienteRegistro', 'Mauricio Muñoz', 'mauriciozsanchez12@gmail.com', '$2y$10$W7HQnUVY3VuBc9.8/EUWt.JyFzofGiDafxWjlFq7XqwVhVX7c.6Jm', 1, 'vistas/img/usuarios/default/default.png', NULL, NULL, NULL, NULL, NULL, NULL, 'activo', 0, 0, 0, NULL, '2025-08-05 19:50:43'),
(8, 'clienteRegistro', 'Carlitos', 'maurinchez12@gmail.com', '$2y$10$halO6g.l5OXIoWVuMhQ80.oefbRxyiMVCyHovqxaRDomE/2e.zR5i', 1, 'vistas/img/usuarios/default/default.png', 'Ingeniero', NULL, NULL, NULL, NULL, NULL, 'activo', 0, 0, 0, NULL, '2025-08-05 19:55:02'),
(9, 'clienteRegistro', 'fgdfgdfg', 'mauricisanchez12@gmail.com', '$2y$10$iUjo10ZEIA39.toMBRbQveKk.SOlDrXXCn8wklKOagJtGDXl8erVS', 1, 'vistas/img/usuarios/default/default.png', NULL, NULL, NULL, NULL, NULL, NULL, 'activo', 0, 0, 0, NULL, '2025-08-06 19:22:28'),
(10, 'profesor-prueba', 'Profesor Prueba', 'profesor@test.com', '$2y$10$OqwueFd4hsseEvQumx6ZfOlF8ymUuvK3JDCWIQG.e3b9FQxLQ8t3i', 0, 'vistas/img/usuarios/default/default.png', NULL, NULL, NULL, NULL, NULL, NULL, 'activo', 0, 0, 0, NULL, '2025-09-05 02:10:03'),
(11, 'estudiante-prueba', 'Estudiante Prueba', 'estudiante@test.com', '$2y$10$vK0SLpm9LkjSfd9S8lZjjug7JA2WSPRiOPWvbswxDBPGaxi4ZByYq', 0, 'vistas/img/usuarios/default/default.png', NULL, NULL, NULL, NULL, NULL, NULL, 'activo', 0, 0, 0, NULL, '2025-09-05 02:10:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona_roles`
--

CREATE TABLE `persona_roles` (
  `id_persona` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona_roles`
--

INSERT INTO `persona_roles` (`id_persona`, `id_rol`) VALUES
(1, 2),
(2, 1),
(2, 2),
(2, 3),
(3, 3),
(4, 1),
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(8, 1),
(8, 2),
(8, 3),
(9, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preinscripciones`
--

CREATE TABLE `preinscripciones` (
  `id` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `id_inscripcion` int(11) DEFAULT NULL,
  `estado` enum('preinscrito','cancelado','convertido','expirado') NOT NULL DEFAULT 'preinscrito',
  `fecha_preinscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preinscripciones`
--

INSERT INTO `preinscripciones` (`id`, `id_curso`, `id_estudiante`, `id_inscripcion`, `estado`, `fecha_preinscripcion`, `fecha_actualizacion`) VALUES
(22, 2, 3, 22, 'convertido', '2025-09-16 13:34:45', '2025-09-16 13:35:10'),
(23, 11, 3, NULL, 'preinscrito', '2025-09-16 15:49:01', NULL),
(24, 10, 3, NULL, 'preinscrito', '2025-09-16 15:49:08', NULL),
(25, 12, 3, NULL, 'preinscrito', '2025-09-17 15:16:10', NULL),
(26, 16, 3, NULL, 'preinscrito', '2025-09-17 15:16:24', NULL),
(27, 9, 3, NULL, 'preinscrito', '2025-09-17 15:16:40', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'admin'),
(3, 'estudiante'),
(2, 'profesor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seccion_contenido`
--

CREATE TABLE `seccion_contenido` (
  `id` int(11) NOT NULL,
  `id_seccion` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `duracion` varchar(10) DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `seccion_contenido`
--

INSERT INTO `seccion_contenido` (`id`, `id_seccion`, `titulo`, `duracion`, `orden`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(45, 28, 'introducción', '00:00:58', 1, 'activo', '2025-09-08 15:56:11', '2025-09-10 15:16:44'),
(46, 37, 'Video de piedra papel o tijera', '00:00:58', 1, 'activo', '2025-09-08 16:56:33', '2025-09-08 19:50:13'),
(47, 38, '12', '00:04:59', 1, 'activo', '2025-09-08 20:19:21', '2025-09-08 20:19:24'),
(50, 42, '12345', '00:04:59', 1, 'activo', '2025-09-10 15:18:55', '2025-09-10 15:18:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seccion_contenido_assets`
--

CREATE TABLE `seccion_contenido_assets` (
  `id` int(11) NOT NULL,
  `id_contenido` int(11) NOT NULL,
  `asset_tipo` enum('video','pdf') NOT NULL,
  `storage_path` varchar(500) NOT NULL,
  `public_url` varchar(500) DEFAULT NULL,
  `tamano_bytes` bigint(20) DEFAULT NULL,
  `duracion_segundos` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `seccion_contenido_assets`
--

INSERT INTO `seccion_contenido_assets` (`id`, `id_contenido`, `asset_tipo`, `storage_path`, `public_url`, `tamano_bytes`, `duracion_segundos`, `created_at`) VALUES
(95, 45, 'pdf', 'C:/xampp/htdocs/cursosApp/storage/public/section_assets/16/28/45/pdf/68befc9c2bca3_1757346972.pdf', 'storage/public/section_assets/16/28/45/pdf/68befc9c2bca3_1757346972.pdf', 220773, NULL, '2025-09-08 15:56:12'),
(98, 46, 'video', 'C:/xampp/htdocs/cursosApp/storage/public/section_assets/16/37/46/video/68bf0ac244671_1757350594.mp4', 'storage/public/section_assets/16/37/46/video/68bf0ac244671_1757350594.mp4', 8146145, 58, '2025-09-08 16:56:34'),
(99, 46, 'pdf', 'C:/xampp/htdocs/cursosApp/storage/public/section_assets/16/37/46/pdf/68bf0ac2ca9b2_1757350594.pdf', 'storage/public/section_assets/16/37/46/pdf/68bf0ac2ca9b2_1757350594.pdf', 80830, NULL, '2025-09-08 16:56:34'),
(100, 46, 'pdf', 'C:/xampp/htdocs/cursosApp/storage/public/section_assets/16/37/46/pdf/68bf0ac303576_1757350595.pdf', 'storage/public/section_assets/16/37/46/pdf/68bf0ac303576_1757350595.pdf', 107322, NULL, '2025-09-08 16:56:35'),
(101, 47, 'video', 'C:/xampp/htdocs/cursosApp/storage/public/section_assets/20/38/47/video/68bf3a4c09238_1757362764.mp4', 'storage/public/section_assets/20/38/47/video/68bf3a4c09238_1757362764.mp4', 12643101, 299, '2025-09-08 20:19:24'),
(107, 45, 'video', 'C:/xampp/htdocs/cursosApp/storage/public/section_assets/16/28/45/video/68c1965bc58ee_1757517403.mp4', 'storage/public/section_assets/16/28/45/video/68c1965bc58ee_1757517403.mp4', 8146145, 58, '2025-09-10 15:16:43'),
(108, 50, 'video', 'C:/xampp/htdocs/cursosApp/storage/public/section_assets/16/42/50/video/68c196e1cee90_1757517537.mp4', 'storage/public/section_assets/16/42/50/video/68c196e1cee90_1757517537.mp4', 12643101, 299, '2025-09-10 15:18:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seccion_contenido_progreso`
--

CREATE TABLE `seccion_contenido_progreso` (
  `id` int(11) NOT NULL,
  `id_contenido` int(11) NOT NULL,
  `id_estudiante` int(11) NOT NULL,
  `visto` tinyint(1) NOT NULL DEFAULT 0,
  `progreso_segundos` int(11) DEFAULT NULL,
  `porcentaje` tinyint(3) UNSIGNED DEFAULT NULL,
  `primera_vista` datetime DEFAULT NULL,
  `ultima_vista` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_instructores`
--

CREATE TABLE `solicitudes_instructores` (
  `id` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_instructores`
--

INSERT INTO `solicitudes_instructores` (`id`, `id_persona`, `estado`, `fecha_solicitud`) VALUES
(1, 1, 'aprobada', '2025-07-15 15:43:52'),
(2, 2, 'rechazada', '2025-07-15 15:43:52');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `curso_secciones`
--
ALTER TABLE `curso_secciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_curso_orden` (`id_curso`,`orden`);

--
-- Indices de la tabla `email_verificacion_tokens`
--
ALTER TABLE `email_verificacion_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `gestion_pagos`
--
ALTER TABLE `gestion_pagos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_gpagos_external_payment` (`external_payment_id`),
  ADD KEY `idx_gpagos_estudiante_estado` (`id_estudiante`,`status`),
  ADD KEY `idx_gpagos_curso_estado` (`id_curso`,`status`),
  ADD KEY `idx_gpagos_preference` (`preference_id`),
  ADD KEY `fk_gpagos_inscripcion` (`id_inscripcion`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `log_ingreso`
--
ALTER TABLE `log_ingreso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_remitente` (`id_remitente`),
  ADD KEY `id_destinatario` (`id_destinatario`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `persona_roles`
--
ALTER TABLE `persona_roles`
  ADD PRIMARY KEY (`id_persona`,`id_rol`),
  ADD KEY `idRol` (`id_rol`);

--
-- Indices de la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_preinscripcion` (`id_curso`,`id_estudiante`),
  ADD KEY `idx_preins_estudiante` (`id_estudiante`),
  ADD KEY `idx_preins_curso` (`id_curso`),
  ADD KEY `fk_preinscripcion_inscripcion` (`id_inscripcion`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `seccion_contenido`
--
ALTER TABLE `seccion_contenido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_seccion_orden` (`id_seccion`,`orden`);

--
-- Indices de la tabla `seccion_contenido_assets`
--
ALTER TABLE `seccion_contenido_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_assets_contenido_tipo` (`id_contenido`,`asset_tipo`);

--
-- Indices de la tabla `seccion_contenido_progreso`
--
ALTER TABLE `seccion_contenido_progreso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_contenido_estudiante` (`id_contenido`,`id_estudiante`),
  ADD KEY `idx_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `solicitudes_instructores`
--
ALTER TABLE `solicitudes_instructores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persona` (`id_persona`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `curso_secciones`
--
ALTER TABLE `curso_secciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `email_verificacion_tokens`
--
ALTER TABLE `email_verificacion_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gestion_pagos`
--
ALTER TABLE `gestion_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `log_ingreso`
--
ALTER TABLE `log_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=330;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `seccion_contenido`
--
ALTER TABLE `seccion_contenido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `seccion_contenido_assets`
--
ALTER TABLE `seccion_contenido_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT de la tabla `seccion_contenido_progreso`
--
ALTER TABLE `seccion_contenido_progreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_instructores`
--
ALTER TABLE `solicitudes_instructores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`),
  ADD CONSTRAINT `curso_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `curso_secciones`
--
ALTER TABLE `curso_secciones`
  ADD CONSTRAINT `curso_secciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `email_verificacion_tokens`
--
ALTER TABLE `email_verificacion_tokens`
  ADD CONSTRAINT `email_verificacion_tokens_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `gestion_pagos`
--
ALTER TABLE `gestion_pagos`
  ADD CONSTRAINT `fk_gpagos_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gpagos_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `persona` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gpagos_inscripcion` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripciones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id`),
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `log_ingreso`
--
ALTER TABLE `log_ingreso`
  ADD CONSTRAINT `log_ingreso_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_remitente`) REFERENCES `persona` (`id`),
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_destinatario`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `persona_roles`
--
ALTER TABLE `persona_roles`
  ADD CONSTRAINT `persona_roles_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`),
  ADD CONSTRAINT `persona_roles_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  ADD CONSTRAINT `fk_preins_curso` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_preins_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `persona` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_preinscripcion_inscripcion` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripciones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `seccion_contenido`
--
ALTER TABLE `seccion_contenido`
  ADD CONSTRAINT `seccion_contenido_ibfk_1` FOREIGN KEY (`id_seccion`) REFERENCES `curso_secciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `seccion_contenido_assets`
--
ALTER TABLE `seccion_contenido_assets`
  ADD CONSTRAINT `seccion_contenido_assets_ibfk_1` FOREIGN KEY (`id_contenido`) REFERENCES `seccion_contenido` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `seccion_contenido_progreso`
--
ALTER TABLE `seccion_contenido_progreso`
  ADD CONSTRAINT `fk_prog_contenido` FOREIGN KEY (`id_contenido`) REFERENCES `seccion_contenido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prog_estudiante` FOREIGN KEY (`id_estudiante`) REFERENCES `persona` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitudes_instructores`
--
ALTER TABLE `solicitudes_instructores`
  ADD CONSTRAINT `solicitudes_instructores_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
