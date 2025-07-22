-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-07-2025 a las 18:01:14
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
-- Estructura de tabla para la tabla `archivos_adicionales`
--

CREATE TABLE `archivos_adicionales` (
  `id` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `nombre_archivo` varchar(255) DEFAULT NULL,
  `ruta_archivo` text DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Animación 3D', 'La animación 3D usa gráficos por computadora para que parezca que los objetos se mueven en un espacio tridimensional.', '2025-07-14 11:21:58');

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
(1, 'peliculas-y-cortometrajes', 'NIÑOS QUE HACEN PELICULAS Y CORTOMETRAJES EN CALIBELULA', 'Entiende la programación desde adentro. Descubre la lógica, los conceptos y el pensamiento detrás de todo software, sin necesidad de saber programar.\n\n¿Te gustaría entender cómo piensan los programadores?\n\n¿Cómo se crean las aplicaciones y por qué la programación es la habilidad más poderosa del siglo XXI?\n\n\n\nEste curso es tu puerta de entrada: no vas a aprender un lenguaje específico ni a escribir código, pero sí vas a incorporar la base lógica y conceptual que te permitirá avanzar hacia cualquier tecnología en el futuro.\n\n\n\n¿CÓMO ESTÁ ORGANIZADO ESTE CURSO?\n\nNuestro programa está diseñado para que entiendas y practiques, en tan solo 6 días de estudio intensivo, los fundamentos más importantes de la programación:\n\nCada día vas a descubrir un concepto clave a través de lecciones claras, ejemplos y ejercicios de pensamiento lógico.\n\nCada sección incluye cuestionarios para afianzar tu comprensión.\n\nCada jornada termina con un proyecto práctico para aplicar lo aprendido y un resumen reflexivo para celebrar tu progreso.\n\n\n\n¿QUÉ VAS A APRENDER?\n\nA lo largo de este curso vas a:\n\nEntender qué es la programación y cómo se relaciona con el mundo que te rodea.\n\nDescubrir qué es un programa, cómo se construye y para qué sirve.\n\nAprender la diferencia entre algoritmos, programas y lenguajes de programación.\n\nConocer el ciclo de vida de un programa y cómo se transforma una idea en una solución concreta.\n\nComprender los conceptos de variable, dato, tipo, función, objeto, ciclo y condicional, sin preocuparte por la sintaxis.\n\nPracticar con ejemplos visuales y diagramas de flujo que te ayudarán a pensar como un programador.\n\nReconocer y aplicar el pensamiento computacional en la vida diaria.\n\nReflexionar sobre los distintos paradigmas de la programación y para qué sirve cada uno.\n\nEntrenar tu mente para resolver problemas, analizar situaciones y descomponer desafíos en pasos claros y ordenados.\n\n\n\n¿POR QUÉ ESTE CURSO ES DIFERENTE?\n\nNo necesitás experiencia previa. Solo curiosidad y ganas de aprender.\n\nSin código ni tecnicismos complicados. Todo se explica desde lo conceptual, usando ejemplos claros, ejercicios de lógica y actividades prácticas.\n\nIdeal para quienes quieren empezar: ya sea que quieras ser programador, liderar un equipo técnico, o simplemente entender cómo funcionan las herramientas digitales, este curso te va a dar la base que necesitás.\n\nCierre diario con “ResuMate”: una sección para reflexionar, repasar y celebrar tu avance.\n\n\n\n\n\n¿QUIÉN SOY Y POR QUÉ PUEDO AYUDARTE?\n\nMi nombre es Federico Garay, soy instructor Best Seller en Udemy, con miles de estudiantes en todo el mundo. Mi misión es que aprendas desde cero, sin miedo y disfrutando cada paso del proceso. He visto cómo la programación cambia vidas, y quiero ayudarte a que vos también lo consigas.\n\n\n\nY para este curso me he asociado con el exitoso instructor Ezequiel Pratissoli, quien ha enseñado a miles de personas a programar a través de sus exitosas redes. Sus populares métodos de enseñanza han revolucionado la forma de aprender desde el principio, y llegar hasta los detalles.\n\n\n\nRecuerda que Udemy te da garantía de devolución total durante 30 días. ¡Puedes probarlo sin riesgo!\n\n\n\nNos vemos en la lección #1\n\nFEDE', 'Comprender qué es la programación y por qué es una habilidad clave en el mundo actual.\nEstar preparado para comenzar a aprender cualquier lenguaje de programación con una base conceptual sólida.\nIdentificar los componentes esenciales de un programa: entradas, procesos y salidas.\nDesarrollar pensamiento lógico y estructurado para abordar desafíos de manera secuencial.\nExplicar el concepto de algoritmo y su importancia en la resolución de problemas.\nEntender cómo funcionan las estructuras condicionales y los bucles en la lógica de programación.\nReconocer la importancia de la abstracción y la modularidad en el diseño de soluciones.\nAnalizar cómo los datos se representan y transforman dentro de un programa.\nReflexionar sobre el rol del programador como diseñador de soluciones automatizadas.', 'No se necesita experiencia previa en programación.\nNo se requiere saber matemáticas avanzadas ni tener formación técnica.\nEs indispensable tener curiosidad, ganas de aprender y una mente abierta.\nAcceso a un dispositivo con conexión a internet.', 'Personas completamente nuevas en el mundo de la programación que quieren entender cómo funciona “por dentro” antes de aprender un lenguaje específico.\nEstudiantes, profesionales o autodidactas que desean desarrollar pensamiento computacional y habilidades de resolución de problemas lógicos.\nPersonas curiosas por la tecnología que siempre se preguntaron “¿qué es programar?” pero nunca supieron por dónde empezar.\nDocentes de otras áreas que buscan comprender los principios básicos de la programación para integrarlos en sus clases.\nEmprendedores, creadores o líderes de equipos técnicos que necesitan comprender el lenguaje de los programadores para comunicarse mejor con sus equipos.\nPersonas que quieren tomar decisiones informadas antes de comprometerse con un lenguaje o una carrera técnica.\nInteresados en el desarrollo personal que desean entrenar su mente en lógica, análisis y pensamiento estructurado.', 'vistas/img/cursos/1 (1).png', 'videosPromos/PROMO-diverti-motion.mp4', 80000, 2, 1, 'activo', '2025-07-14 16:21:17'),
(2, 'peliculas-y-cortometrajes-en', 'NIÑOS QUE HACEN PELICULAS Y CORTOMETRAJES EN CALIBELULA', 'Calibélula vivirá un nuevo encuentro con niños y adolescentes, orientados bajo el liderazgo de procesos como La Escuela Audiovisual, Belén de los Andaquies, en el Caquetá, Mi Primer corto Infantil de México y Subí que te veo de Argentina.\r\n', 'Comprender qué es la programación y por qué es una habilidad clave en el mundo actual.\nEstar preparado para comenzar a aprender cualquier lenguaje de programación con una base conceptual sólida.\nIdentificar los componentes esenciales de un programa: entradas, procesos y salidas.\nDesarrollar pensamiento lógico y estructurado para abordar desafíos de manera secuencial.\nExplicar el concepto de algoritmo y su importancia en la resolución de problemas.\nEntender cómo funcionan las estructuras condicionales y los bucles en la lógica de programación.\nReconocer la importancia de la abstracción y la modularidad en el diseño de soluciones.\nAnalizar cómo los datos se representan y transforman dentro de un programa.\nReflexionar sobre el rol del programador como diseñador de soluciones automatizadas.', NULL, NULL, 'vistas/img/cursos/1 (2).png', 'videosPromos/PROMO-diverti-motion.mp4', 70000, 2, 1, 'activo', '2025-07-14 16:23:59'),
(3, 'talleres-libelulitos', 'Talleres Libelulit@s y convocatoria a realizadores cinematográficos', 'La magia del cine y el audiovisual regresan a Cali para el mundo, a partir del 30 de abril, cuando se hará el lanzamiento oficial del 5º- Festival Internacional de Cine Infantil y Juvenil, Calibélula, con la apertura de la convocatoria dirigida a directores, realizadores y productores para que envíen sus producciones cinematográficas antes del 15 de Junio, a través de Festhome y Google Drive. La convocatoria también estará dirigida a instituciones educativas, niños y jóvenes en general para que participen de los talleres de Libelulit@s que se dictáran gratuitamente a partir del mes de mayo.', 'Comprender qué es la programación y por qué es una habilidad clave en el mundo actual.\nEstar preparado para comenzar a aprender cualquier lenguaje de programación con una base conceptual sólida.\nIdentificar los componentes esenciales de un programa: entradas, procesos y salidas.\nDesarrollar pensamiento lógico y estructurado para abordar desafíos de manera secuencial.\nExplicar el concepto de algoritmo y su importancia en la resolución de problemas.\nEntender cómo funcionan las estructuras condicionales y los bucles en la lógica de programación.\nReconocer la importancia de la abstracción y la modularidad en el diseño de soluciones.\nAnalizar cómo los datos se representan y transforman dentro de un programa.\nReflexionar sobre el rol del programador como diseñador de soluciones automatizadas.', NULL, NULL, 'vistas/img/cursos/1 (3).png', 'videosPromos/PROMO-diverti-motion.mp4', 100000, 2, 1, 'activo', '2025-07-14 16:24:30'),
(4, 'curso-php', 'COMO HACER UNA PAGINA WEB', 'Pagina web en PHP y JS', NULL, NULL, NULL, 'vistas/img/cursos/1 (5).jpg', 'videosPromos/PROMO-diverti-motion.mp4', 90000, 2, 1, 'activo', '2025-07-14 16:25:20'),
(5, 'tecnologia-en-aritmetica', 'Tecnologia en Aritmetica', 'Curso es muy importante', NULL, NULL, NULL, 'vistas/img/cursos/687a7e30812fe_images.jpg', 'videosPromos/687a7e30818f3_Vídeo de ríos gratis sin copyright 1 _ Paisaje _ Piedra _ Naturaleza - Pixabay - Videos Sin Copyright (720p, h264).mp4', 70000, 1, 2, 'activo', '2025-07-18 17:02:40'),
(6, 'tecnologia-en-aritmeticaaaaaaaaa', 'Tecnologia en Aritmeticaaaaaaaaa', 'sadasdad', NULL, NULL, NULL, 'vistas/img/cursos/687e4f78d35ce_3W2KSMRJUVJHDGDB5UZZCJ3PTE.jpg', NULL, 80, 1, 1, 'activo', '2025-07-21 14:32:24'),
(7, 'tecnologia-en-programaci-n', 'Tecnologia en Programación', 'gghgfhfh', NULL, NULL, NULL, 'vistas/img/cursos/687e545a8f012_premium_photo-1666672388644-2d99f3feb9f1.jpg', NULL, 23, 2, 1, 'activo', '2025-07-21 14:53:14'),
(8, 'tecnologia-en-logica', 'Tecnologia en logica', 'dasdasd', NULL, NULL, NULL, 'vistas/img/cursos/687e564c4c2bd_depositphotos_234542254-stock-illustration-man-profile-smiling-cartoon-vector.jpg', NULL, 23, 1, 2, 'activo', '2025-07-21 15:01:32'),
(9, 'tecnologia-en-perritos', 'Tecnologia en perritos', 'La tecnología ha transformado la forma en que cuidamos a nuestros perros, ofreciendo herramientas para mejorar su salud, seguridad y bienestar. Desde dispositivos portátiles que monitorean su actividad y signos vitales, hasta sistemas de ubicación GPS para evitar pérdidas, la innovación tecnológica ha llegado para quedarse en el mundo canino.\r\nWearables:\r\nDispositivos como collares inteligentes con GPS y sensores de salud, que permiten a los dueños monitorear la actividad física, la frecuencia cardíaca y respiratoria, e incluso detectar problemas de salud antes de que se manifiesten síntomas visibles. \r\nComederos y bebederos automáticos:\r\nProgramables para asegurar que tu perro reciba la cantidad adecuada de alimento y agua, incluso cuando no estás en casa. \r\nCámaras de vigilancia:\r\nPermiten observar a tu mascota a distancia, interactuar con ella a través de audio y video, y asegurarte de que está bien. ', NULL, NULL, NULL, 'vistas/img/cursos/687e68537ff02_287133.jpg', NULL, 80, 1, 1, 'activo', '2025-07-21 16:18:27'),
(10, 'tecnologia-en-tarott', 'Tecnologia en tarott', 'maksdklajd', NULL, NULL, NULL, 'vistas/img/cursos/687e871a23717_2871333.jpg', NULL, 34, 1, 2, 'activo', '2025-07-21 18:29:46');

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
-- Estructura de tabla para la tabla `gestionpagos`
--

CREATE TABLE `gestionpagos` (
  `id` int(11) NOT NULL,
  `id_inscripcion` int(11) DEFAULT NULL,
  `valor_pagado` int(11) NOT NULL,
  `medio_pago` varchar(100) NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
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
(52, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-22 10:49:52');

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
  `nro_identificacion` varchar(50) DEFAULT NULL,
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
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id`, `usuario_link`, `nombre`, `email`, `nro_identificacion`, `password`, `verificacion`, `foto`, `profesion`, `telefono`, `direccion`, `biografia`, `pais`, `ciudad`, `estado`, `fecha_registro`) VALUES
(1, 'clienteRegistro', 'Mauricio Muñoz', 'mauriciomuozsanchez12@gmail.com', NULL, '$2y$10$XJjXQcSuxiVhdhkovif7B.YfVKNSkVEK2Tl0ZBJa48CDWKY3.r80a', 0, 'vistas/img/usuarios/default/default.png', 'Contador', '3135529157', 'cra26k8121', '¿Por qué he llegado a más de 400k estudiantes pero mi calificación global no baja de 4.7 estrellas?\n\nPrimero, porque hago todo esto con mucho amor.\n\nEn segundo lugar, porque me lo tomo muy en serio, pero sin dejar de divertirme. Aunque tal vez también porque soy un apasionado por aprender y por compartir lo aprendido.\n\nSoy como mis estudiantes: quiero ser mejor, cada día, un poquito.\n\nMe he propuesto desarrollar cursos en Udemy (¡más de 50 ya!) para transmitir mi experiencia, conocer personas con ganas de ser mejores, y mejorar mi calidad de vida haciendo lo que me gusta. Más de 400k estudiantes se han unido a esta aventura.\n\nSoy el fundador de Escuela Directa, una usina de contenido que promueve la educación ACCESIBLE para TODOS.\n\nHasta los 42 años, mi profesión formal era la Psicología (con más de 20 años de experiencia clínica y especializado en Drogadependencias).\n\nMi profesión me ha llevado a trabajar mucho en docencia y en especial con jóvenes.\n\nTambién soy Licenciado en Artes Plásticas, y me paso horas dibujando cómics, programando, o tomando fotos. He cantado en coros, he bailado en murgas, he militado en política, me apasiona la buena cocina, viajar mucho, participar en mi comunidad y tener mis sentidos siempre abiertos.\n\nY ya que estamos, soy Especialista en Excel (MO-200) certificado por Microsoft.\n\nDe todos modos, mi profesión no me define. Soy lo que hago cada día, y cada día es un día nuevo.\n\nTe invito a que nos reinventemos juntos.', 'Colombia', NULL, 'activo', '2025-07-10 19:18:15'),
(2, 'clienteRegistro', 'Derly Pipicano', 'm-mau55@hotmail.com', NULL, '$2y$10$AlrkWRiRR2kIBFLn7qA.nux7d6//Va6PB818ZJK7NnrENSAv8a6kS', 0, 'vistas/img/usuarios/default/default.png', NULL, NULL, NULL, NULL, NULL, NULL, 'activo', '2025-07-15 13:42:06');

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
(1, 1),
(1, 2),
(1, 3),
(2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requisitos_curso`
--

CREATE TABLE `requisitos_curso` (
  `id` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `secciones`
--

CREATE TABLE `secciones` (
  `id` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `nombre` varchar(300) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion` varchar(100) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `tipo` varchar(200) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `secciones`
--

INSERT INTO `secciones` (`id`, `id_curso`, `nombre`, `descripcion`, `duracion`, `url`, `tipo`, `fecha_registro`) VALUES
(1, NULL, 'PROMOCIONAL Diverti Motion', 'PROMOCIONAL Diverti Motion', '28 Segundos', 'videosPromos/PROMO-diverti-motion.mp4', 'video', '2025-07-14 16:35:16'),
(2, NULL, 'PROMOCIONAL Diverti Motion', 'PROMOCIONAL Diverti Motion', '28 Segundos', 'videosPromos/PROMO-diverti-motion.mp4', 'Video', '2025-07-14 16:35:16');

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
-- Indices de la tabla `archivos_adicionales`
--
ALTER TABLE `archivos_adicionales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_curso` (`id_curso`);

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
-- Indices de la tabla `email_verificacion_tokens`
--
ALTER TABLE `email_verificacion_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_persona` (`id_persona`);

--
-- Indices de la tabla `gestionpagos`
--
ALTER TABLE `gestionpagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_inscripcion` (`id_inscripcion`);

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
-- Indices de la tabla `requisitos_curso`
--
ALTER TABLE `requisitos_curso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_curso` (`id_curso`);

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
-- AUTO_INCREMENT de la tabla `archivos_adicionales`
--
ALTER TABLE `archivos_adicionales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `email_verificacion_tokens`
--
ALTER TABLE `email_verificacion_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gestionpagos`
--
ALTER TABLE `gestionpagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `log_ingreso`
--
ALTER TABLE `log_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `requisitos_curso`
--
ALTER TABLE `requisitos_curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `secciones`
--
ALTER TABLE `secciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `solicitudes_instructores`
--
ALTER TABLE `solicitudes_instructores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `archivos_adicionales`
--
ALTER TABLE `archivos_adicionales`
  ADD CONSTRAINT `archivos_adicionales_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id`);

--
-- Filtros para la tabla `curso`
--
ALTER TABLE `curso`
  ADD CONSTRAINT `curso_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`),
  ADD CONSTRAINT `curso_ibfk_2` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `email_verificacion_tokens`
--
ALTER TABLE `email_verificacion_tokens`
  ADD CONSTRAINT `email_verificacion_tokens_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);

--
-- Filtros para la tabla `gestionpagos`
--
ALTER TABLE `gestionpagos`
  ADD CONSTRAINT `gestionpagos_ibfk_1` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripciones` (`id`);

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
-- Filtros para la tabla `requisitos_curso`
--
ALTER TABLE `requisitos_curso`
  ADD CONSTRAINT `requisitos_curso_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id`);

--
-- Filtros para la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD CONSTRAINT `secciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id`);

--
-- Filtros para la tabla `solicitudes_instructores`
--
ALTER TABLE `solicitudes_instructores`
  ADD CONSTRAINT `solicitudes_instructores_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
