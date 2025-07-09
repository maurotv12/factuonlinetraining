-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-07-2025 a las 22:20:07
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cursos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(300) NOT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `descripcion`, `fechaRegistro`) VALUES
(2, 'Stop Motion', 'La animación en volumen​ o animación fotograma a fotograma​ es una técnica de animación que consiste en aparentar el movimiento de objetos estáticos por medio de una serie de imágenes fijas sucesivas.', '2022-05-27 13:03:40'),
(3, 'Animación 3D', 'La animación 3D usa gráficos por computadora para que parezca que los objetos se mueven en un espacio tridimensional.', '2022-05-27 13:06:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id` int(11) NOT NULL,
  `urlAmiga` varchar(35) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `descripcion` text NOT NULL,
  `banner` varchar(300) NOT NULL,
  `promoVideo` varchar(150) NOT NULL,
  `valor` int(11) NOT NULL,
  `idCategoria` int(11) NOT NULL,
  `idPersona` int(11) NOT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id`, `urlAmiga`, `nombre`, `descripcion`, `banner`, `promoVideo`, `valor`, `idCategoria`, `idPersona`, `fechaRegistro`) VALUES
(1, 'peliculas-y-cortometrajes', 'NIÑOS QUE HACEN PELICULAS Y CORTOMETRAJES EN CALIBELULA', 'Calibélula vivirá un nuevo encuentro con niños y adolescentes, orientados bajo el liderazgo de procesos como La Escuela Audiovisual, Belén de los Andaquies, en el Caquetá, Mi Primer corto Infantil de México y Subí que te veo de Argentina.\r\nEncuentro con niños', 'App/vistas/img/cursos/1 (1).png', 'videosPromos/PROMO-diverti-motion.mp4', 80000, 3, 1, '2022-05-26 19:53:18'),
(2, 'peliculas-y-cortometrajes-en', 'NIÑOS QUE HACEN PELICULAS Y CORTOMETRAJES EN CALIBELULA', 'Calibélula vivirá un nuevo encuentro con niños y adolescentes, orientados bajo el liderazgo de procesos como La Escuela Audiovisual, Belén de los Andaquies, en el Caquetá, Mi Primer corto Infantil de México y Subí que te veo de Argentina.\r\nEncuentro con niños', 'App/vistas/img/cursos/1 (2).png', 'videosPromos/PROMO-diverti-motion.mp4', 70000, 3, 1, '2022-05-26 19:53:18'),
(3, 'talleres-libelulitos', 'Talleres Libelulit@s y convocatoria a realizadores cinematográficos', 'La magia del cine y el audiovisual regresan a Cali para el mundo, a partir del 30 de abril, cuando se hará el lanzamiento oficial del 5º- Festival Internacional de Cine Infantil y Juvenil, Calibélula, con la apertura de la convocatoria dirigida a directores, realizadores y productores para que envíen sus producciones cinematográficas antes del 15 de Junio, a través de Festhome y Google Drive. La convocatoria también estará dirigida a instituciones educativas, niños y jóvenes en general para que participen de los talleres de Libelulit@s que se dictáran gratuitamente a partir del mes de mayo.', 'App/vistas/img/cursos/1 (3).png', 'videosPromos/PROMO-diverti-motion.mp4', 100000, 2, 2, '2022-05-26 20:40:44'),
(4, 'talleres-libelulitos-convocatoria', 'Talleres Libelulit@s y convocatoria a realizadores cinematográficos', 'La magia del cine y el audiovisual regresan a Cali para el mundo, a partir del 30 de abril, cuando se hará el lanzamiento oficial del 5º- Festival Internacional de Cine Infantil y Juvenil, Calibélula, con la apertura de la convocatoria dirigida a directores, realizadores y productores para que envíen sus producciones cinematográficas antes del 15 de Junio, a través de Festhome y Google Drive. La convocatoria también estará dirigida a instituciones educativas, niños y jóvenes en general para que participen de los talleres de Libelulit@s que se dictáran gratuitamente a partir del mes de mayo.', 'App/vistas/img/cursos/1 (4).png', 'videosPromos/PROMO-diverti-motion.mp4', 77000, 2, 2, '2022-05-26 20:40:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gestionpagos`
--

CREATE TABLE `gestionpagos` (
  `id` int(11) NOT NULL,
  `idInscripcion` int(11) NOT NULL,
  `valorPagado` int(11) NOT NULL,
  `fechaPago` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mediodePago` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `idEstudiante` int(11) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_ingreso`
--

CREATE TABLE `log_ingreso` (
  `id` int(11) NOT NULL,
  `usuarioId` int(11) NOT NULL,
  `ipUsuario` int(11) NOT NULL,
  `navegador` int(11) NOT NULL,
  `fechaR` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `log_ingreso`
--

INSERT INTO `log_ingreso` (`id`, `usuarioId`, `ipUsuario`, `navegador`, `fechaR`) VALUES
(1, 1, 0, 0, '2025-07-07 15:16:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `id` int(11) NOT NULL,
  `usuarioLink` varchar(100) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `verificacion` int(11) NOT NULL,
  `foto` varchar(50) NOT NULL,
  `profesion` varchar(300) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `perfil` text NOT NULL,
  `Pais` varchar(200) NOT NULL,
  `estado` int(11) NOT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `persona`
--

INSERT INTO `persona` (`id`, `usuarioLink`, `nombre`, `email`, `password`, `verificacion`, `foto`, `profesion`, `telefono`, `direccion`, `perfil`, `Pais`, `estado`, `fechaRegistro`) VALUES
(1, 'clienteRegistro', 'Gildardo Restrepo', 'grcarvajal@gmail.com', '$2y$10$PlJ/FnW9a36aB2vD1TBlIeiodb3kj8Q81V3D8IiBJqQEPvEF0ij0u', 0, 'vistas/img/usuarios/default/default.png', '', '', '', '', '', 1, '2025-07-07 17:35:28'),
(2, 'clienteRegistro', 'Gildardo', 'ffffff@gmail.com', '$2y$10$51EmD.3hIVy7aPEUps7ik.UmYsMqVSUvk7pdJcGZsQ3ACGSqu4m.W', 0, 'vistas/img/usuarios/default/default.png', '', '', '', '', '', 1, '2025-07-07 19:12:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones`
--

CREATE TABLE `secciones` (
  `id` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `descripcion` text NOT NULL,
  `duracion` varchar(100) NOT NULL,
  `url` varchar(250) NOT NULL,
  `tipo` varchar(200) NOT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `secciones`
--

INSERT INTO `secciones` (`id`, `idCurso`, `nombre`, `descripcion`, `duracion`, `url`, `tipo`, `fechaRegistro`) VALUES
(1, 1, 'PROMOCIONAL Diverti Motion', 'PROMOCIONAL Diverti Motion', '28 Segundos', 'videosPromos/PROMO-diverti-motion.mp4', 'video', '2022-05-30 14:14:26'),
(2, 2, 'PROMOCIONAL Diverti Motion', 'PROMOCIONAL Diverti Motion', '28 Segundos', 'videosPromos/PROMO-diverti-motion.mp4', 'Video', '2022-05-30 14:14:26');

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
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `gestionpagos`
--
ALTER TABLE `gestionpagos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `log_ingreso`
--
ALTER TABLE `log_ingreso`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `gestionpagos`
--
ALTER TABLE `gestionpagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `log_ingreso`
--
ALTER TABLE `log_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `secciones`
--
ALTER TABLE `secciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Relaciones entre tablas

ALTER TABLE `curso`
  ADD CONSTRAINT `fk_curso_categoria` FOREIGN KEY (`idCategoria`) REFERENCES `categoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_curso_persona` FOREIGN KEY (`idPersona`) REFERENCES `persona` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `inscripciones`
  ADD CONSTRAINT `fk_inscripciones_curso` FOREIGN KEY (`idCurso`) REFERENCES `curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscripciones_persona` FOREIGN KEY (`idEstudiante`) REFERENCES `persona` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `gestionpagos`
  ADD CONSTRAINT `fk_gestionpagos_inscripcion` FOREIGN KEY (`idInscripcion`) REFERENCES `inscripciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `secciones`
  ADD CONSTRAINT `fk_secciones_curso` FOREIGN KEY (`idCurso`) REFERENCES `curso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `log_ingreso`
  ADD CONSTRAINT `fk_log_ingreso_usuario` FOREIGN KEY (`usuarioId`) REFERENCES `persona` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;