-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-09-2025 a las 05:07:59
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
(1, 'Desarrollo Web', 'Impulsa tu carrera con metodologias nuevas', '2025-07-14 11:21:58'),
(2, 'Animación 3D', 'La animación 3D usa gráficos por computadora para que parezca que los objetos se mueven en un espacio tridimensional.', '2025-07-14 11:21:58'),
(3, 'Robotica', 'La robotica del siglo XXI', '2025-08-11 15:42:15'),
(4, 'Desarrollo Movil', 'Desarrollar apps para dispositivos moviles como smartphones y tablets', '2025-09-17 13:44:18'),
(5, 'Liderazgo', 'Lider en el sector empresarial', '2025-09-17 13:44:18'),
(6, 'Comunicación', 'Desarrolla tu habilidad de comunicación y persuación', '2025-09-17 13:45:36'),
(7, 'Marketing Digital', 'Aprende a confeccionar tu propia ropa', '2025-09-17 13:45:36'),
(8, 'Gestor de Proyectos', 'Desde lo más básico hasta joyas Luxury', '2025-09-17 13:46:39');

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
(21, 'desarrollo-web-completo-con-html5-css-js-ajax-php-y-mysql', 'Desarrollo Web Completo con HTML5, CSS, JS AJAX PHP y MySQL', 'Un curso paso a paso si deseas comenzar en el mundo de la Programación Web.\r\n\r\nEn este curso aprenderás 10 Lenguajes y Tecnologías Web:\r\n\r\nHTML, CSS, SASS, Workflows, JavaScript, Fetch (Antes AJAX), PHP, POO - MVC, MySQL - SQL y API\'s\r\n\r\nEl Curso Incluye 4 proyectos finales, puedes ver los videos con los demos totalmente gratis!\r\n\r\nAdemás, aprenderás otros temas muy importantes como:\r\n\r\nCreación de Cuentas - Como en el 90% de los sitios que visitas hoy en día tus usuarios podrán crear sus cuentas.\r\n\r\nCrear un Framework - Crearemos una base de código que aplicaremos a 4 Proyectos!\r\n\r\nAutenticación desde una base de datos Real - Con MySQL y Hash a los Passwords y seguridad.\r\n\r\nRecuperar Acceso - Un Password Hasheado no se puede recuperar, pero te mostraré como tus usuarios recuperarán su acceso.\r\n\r\nRecibir Pagos - Hoy en día muchos negocios requieren recibir pagos en línea y te mostraré como hacerlo.\r\n\r\nModelar Bases de Datos - No necesitarás otro curso de Bases de Datos, en este curso aprenderás sobre que es una base de datos, lenguaje SQL, tipos de datos, modelar una base de datos y relacionarla!\r\n\r\nEn este curso aprenderás lo básico de cada tecnología y después integraremos todo junto para hacer diseños increíbles, es decir: crearemos una base sólida y luego veremos como utilizarla sin duda la que considero la mejor forma de aprender.\r\n\r\nHoy en día aprender programación web es uno de los empleos más demandados y con mejor paga y he preparado este curso para que una vez que finalices tengas las armas necesarias para poder encarar cualquier proyecto.', 'Crear un íncreible Portafolio con muchos proyectos que te ayudarán a obtener un empleo\r\nAprender y Dominar HTML y CSS para crear diseños modernos siguiendo buenas prácticas \r\nAprender Metodologias HTML y CSS como Módulos y BEM \r\nCrear páginas web con HTML y CSS que puedas vender a tus clientes \r\nEscribir código JavaScript Moderno (ES6) \r\nCrear sitios web dínamicos con PHP y MYSQL \r\nEntender como funciona JavaScript, PHP y Fetch API\r\nAplicar a un empleo de Desarrollador Web Junior\r\nAprender a crear sitios dínamicos, que utilicen bases de datos \r\nCrear aplicaciones CRUD con PHP y MySQL \r\nAgregar pagos de PayPal a tus sitios web \r\nCrear aplicaciones seguras con PHP y MySQL ', 'No tienes que tener experiencia previa ni con HTML5 o CSS3 para comenzar,  \r\nNo es necesario tener experiencia en programación \r\nInstalaremos un editor de Texto y Servidor local,  \r\nEste curso integrará HTML5, CSS, JavaScript, Fetch, SASS, PHP y MySQL \r\nAprenderás JavaScript Y Fetch API para añadir interactividad a tu sitio web.\r\nEs necesario contar con una conexión a internet para poder ver los videos ', 'Si deseas obtener un trabajo en la industria de desarrollo web,  \r\nAprender Desarrollo web es un proceso de mucha práctica,  \r\nSi eres una persona que aprende más elaborando proyectos  \r\nDesarrollo Web es un área bien pagada ', 'storage/public/banners/68d56277dbc52_1758814839.jpg', 'storage/public/promoVideos/68d563fc9318f_1758815228.mp4', 120000, 1, 14, 'activo', '2025-09-25 15:40:41'),
(22, 'modelado-de-personaje-cartoon-para-animaci-n-con-blender-3d', 'Modelado de personaje cartoon para animación con Blender 3D', 'En este curso descubrirás cuál es el proceso de de creación de personajes 3D estilo cartoon para animación, utilizando todas las herramientas que nos proporciona Blender.\r\n\r\nEmpezarás conociendo la interfaz de Blender y, una vez hayas familiarizado con el programa, estudiarás un personaje base a modo de introducción, con el objetivo de conocer las reglas de la topología y los loops corporales y faciales.\r\n\r\nModelarás el cuerpo de nuestro personaje, mediante imágenes guías de diseño, creando una topología base limpia óptima para rigging y animación.\r\n\r\nA continuación, modelarás toda la ropa y los accesorios que darán personalidad al personaje, siempre pensando en un diseño que encaje dentro de una película de animación.\r\n\r\nAprenderás el proceso de unwrapping del personaje desplegando los mapas de Uvs para la aplicación de materiales y texturas, tales como tejidos y piel.\r\n\r\nLlegado a este punto estarás listo para dar movilidad al personaje, creando un esqueleto mediante Rigify, un esqueleto completo humano formado de: rig de piernas, brazos, torso, manos y cabeza.\r\n\r\nAsí como la creación de expresiones faciales, tanto por shape keys como por rigging.\r\n\r\nCrearás varías poses dinámicas y llamativas dotando de vida a nuestro personaje, estudiando la línea de acción, un elemento muy útil para dar solidez a nuestro personaje.', 'Aprenderás a dar vida a tu propio personaje 3D cartoon de principio a fin\r\nEstudiarás técnicas de modelado poligonal, para una topología limpia\r\nCrearás mapas de Uvs para la aplicación de materiales y texturas\r\nCrearás un esqueleto, con controles que te permitirán dar movimiento corporal \r\nAprenderás a crear cámaras, moverlas, realizar encuadres \r\nPublicarás tu trabajo final en un portal digital del sector de animación', 'No es indispensable tener conocimientos previos de 3D, aunque se recomienda tener nociones\r\nNo se requieren conocimientos previos de Blender.\r\nUn ordenador con Windows o macOS que sea capaz de ejecutar Blender.', 'A artistas, diseñadores, o cualquier persona que esté interesada  \r\nArtistas Junior que deseen subir de nivel, aprender o reforzar técnicas  \r\nArtistas Seniors que deseen dar el paso a Blender, aprender su flujo de trabajo ', 'storage/public/banners/68d563ba6cd4a_1758815162.jpg', 'storage/public/promoVideos/68d563e3ed986_1758815203.mp4', 200000, 2, 14, 'activo', '2025-09-25 15:46:02'),
(23, 'de-cero-a-full-stack', 'De Cero a Full Stack', '¿Quieres convertirte en un desarrollador Full Stack Web Development y no sabes por dónde empezar? Este curso es perfecto para ti. \"De Cero a FullStack\" está diseñado para llevarte desde los conceptos más básicos hasta convertirte en un desarrollador web completo, capaz de crear aplicaciones web modernas y dinámicas.\n\nObjetivo del curso: Al finalizar este curso, tendras los conocimientos necesarios en tecnologias del frontend y del backend, seras capaz de desarrollar sitios web y aplicaciones web Full Stack, manejando tanto el Front-end como el Back-end con fluidez y confianza.\n\nDirigido a: Este curso está abierto a todo el mundo y no requiere conocimientos previos. Si tienes ganas de aprender y estás dispuesto a comprometerte, este curso es para ti.\n\nContenido del curso:\n\nFront-end Development:\n\nHTML: Aprende la estructura básica de las páginas web.\n\nCSS: Dale estilo y diseño a tus sitios web.\n\nFlexbox y Grid: Diseña layouts complejos y responsivos.\n\nBootstrap: Utiliza este popular framework para acelerar tu desarrollo.\n\nJavaScript: Domina el lenguaje de programación esencial para el desarrollo web.\n\njQuery: Simplifica la manipulación del DOM y mejora la interacción del usuario.\n\nProgramación Orientada a Objetos (POO) con TypeScript: Aprende los principios de la POO y cómo aplicarlos en TypeScript.\n\nAngular: Desarrolla aplicaciones web robustas y escalables con este poderoso framework.\nCurso: De Cero a Full Stack\n\n¿Quieres convertirte en un desarrollador Full Stack Web Development y no sabes por dónde empezar? Este curso es perfecto para ti. \"De Cero a FullStack\" está diseñado para llevarte desde los conceptos más básicos hasta convertirte en un desarrollador web completo, capaz de crear aplicaciones web modernas y dinámicas.\n\nObjetivo del curso: Al finalizar este curso, tendras los conocimientos necesarios en tecnologias del frontend y del backend, seras capaz de desarrollar sitios web y aplicaciones web Full Stack, manejando tanto el Front-end como el Back-end con fluidez y confianza.\n\nDirigido a: Este curso está abierto a todo el mundo y no requiere conocimientos previos. Si tienes ganas de aprender y estás dispuesto a comprometerte, este curso es para ti.\n\nContenido del curso:\n\nFront-end Development:\n\nHTML: Aprende la estructura básica de las páginas web.\n\nCSS: Dale estilo y diseño a tus sitios web.\n\nFlexbox y Grid: Diseña layouts complejos y responsivos.\n\nBootstrap: Utiliza este popular framework para acelerar tu desarrollo.\n\nJavaScript: Domina el lenguaje de programación esencial para el desarrollo web.\n\njQuery: Simplifica la manipulación del DOM y mejora la interacción del usuario.\n\nProgramación Orientada a Objetos (POO) con TypeScript: Aprende los principios de la POO y cómo aplicarlos en TypeScript.\n\nAngular: Desarrolla aplicaciones web robustas y escalables con este poderoso framework.\n\nBack-end Development:\n\nPHP: Aprende a crear aplicaciones del lado del servidor.\n\nMySQL: Gestiona bases de datos y realiza consultas eficientes.\n\nProgramación Orientada a Objetos (POO): Aplica principios de POO en PHP.\n\nModelo Vista Controlador (MVC): Entiende y aplica este patrón de diseño para organizar tu código.\n\nLaravel: Utiliza este popular framework para desarrollar aplicaciones web modernas y escalables.\n\nIntegración Front-end y Back-end:\n\nAprende a combinar el Front-end con el Back-end.\n\nDesarrolla una API RESTful completa utilizando Angular y Laravel.\n\nDuración: El curso está diseñado para completarse a tu propio ritmo, pero se estima que tomarás varias semanas o meses para completarlo, dependiendo de tu dedicación y ritmo de estudio.\n\nTiene un total de aproximadamente 500 videos de 15 minutos cada uno. Además, se complementará con teoria y ejercicios que te permitirán aplicar lo aprendido en proyectos reales.\n\n\n\nRequisitos previos: No se requieren conocimientos previos. Este curso está diseñado para principiantes absolutos.\n\n\n\n         Beneficios:\n\nAdquirirás habilidades en desarrollo web Full Stack, una de las áreas más demandadas en la industria tecnológica.\n\nDesarrollarás proyectos prácticos que podrás incluir en tu portafolio profesional.\n\nObtendrás una comprensión profunda de las tecnologías y herramientas más utilizadas en el desarrollo web moderno.\n\n\n\nAlgunos de los proyectos que desarrollaremos desde cero:\n\nAplicaremos todo lo aprendido con css,flexbox , grid y bootstrap haciendo una plantilla completa\n\nAplicaremos todo lo aprendido con php y mysql haciendo un blog\n\nAplicaremos todo lo aprendido con Laravel y crearemos una red social\n\nMientras avanzamos en el curso iremos haciendo tareas , ejercicios y proyectos\n\nEl proyecto final sera una plataforma educativa tipo udemy (la api restful)\nCurso: De Cero a Full Stack\n\n¿Quieres convertirte en un desarrollador Full Stack Web Development y no sabes por dónde empezar? Este curso es perfecto para ti. \"De Cero a FullStack\" está diseñado para llevarte desde los conceptos más básicos hasta convertirte en un desarrollador web completo, capaz de crear aplicaciones web modernas y dinámicas.\n\nObjetivo del curso: Al finalizar este curso, tendras los conocimientos necesarios en tecnologias del frontend y del backend, seras capaz de desarrollar sitios web y aplicaciones web Full Stack, manejando tanto el Front-end como el Back-end con fluidez y confianza.\n\nDirigido a: Este curso está abierto a todo el mundo y no requiere conocimientos previos. Si tienes ganas de aprender y estás dispuesto a comprometerte, este curso es para ti.\n\nContenido del curso:\n\nFront-end Development:\n\nHTML: Aprende la estructura básica de las páginas web.\n\nCSS: Dale estilo y diseño a tus sitios web.\n\nFlexbox y Grid: Diseña layouts complejos y responsivos.\n\nBootstrap: Utiliza este popular framework para acelerar tu desarrollo.\n\nJavaScript: Domina el lenguaje de programación esencial para el desarrollo web.\n\njQuery: Simplifica la manipulación del DOM y mejora la interacción del usuario.\n\nProgramación Orientada a Objetos (POO) con TypeScript: Aprende los principios de la POO y cómo aplicarlos en TypeScript.\n\nAngular: Desarrolla aplicaciones web robustas y escalables con este poderoso framework.\n\nBack-end Development:\n\nPHP: Aprende a crear aplicaciones del lado del servidor.\n\nMySQL: Gestiona bases de datos y realiza consultas eficientes.\n\nProgramación Orientada a Objetos (POO): Aplica principios de POO en PHP.\n\nModelo Vista Controlador (MVC): Entiende y aplica este patrón de diseño para organizar tu código.\n\nLaravel: Utiliza este popular framework para desarrollar aplicaciones web modernas y escalables.\n\nIntegración Front-end y Back-end:\n\nAprende a combinar el Front-end con el Back-end.\n\nDesarrolla una API RESTful completa utilizando Angular y Laravel.\n\nDuración: El curso está diseñado para completarse a tu propio ritmo, pero se estima que tomarás varias semanas o meses para completarlo, dependiendo de tu dedicación y ritmo de estudio.\n\nTiene un total de aproximadamente 500 videos de 15 minutos cada uno. Además, se complementará con teoria y ejercicios que te permitirán aplicar lo aprendido en proyectos reales.\n\n\n\nRequisitos previos: No se requieren conocimientos previos. Este curso está diseñado para principiantes absolutos.\n\n\n\n         Beneficios:\n\nAdquirirás habilidades en desarrollo web Full Stack, una de las áreas más demandadas en la industria tecnológica.\n\nDesarrollarás proyectos prácticos que podrás incluir en tu portafolio profesional.\n\nObtendrás una comprensión profunda de las tecnologías y herramientas más utilizadas en el desarrollo web moderno.\n\n\n\nAlgunos de los proyectos que desarrollaremos desde cero:\n\nAplicaremos todo lo aprendido con css,flexbox , grid y bootstrap haciendo una plantilla completa\n\nAplicaremos todo lo aprendido con php y mysql haciendo un blog\n\nAplicaremos todo lo aprendido con Laravel y crearemos una red social\n\nMientras avanzamos en el curso iremos haciendo tareas , ejercicios y proyectos\n\nEl proyecto final sera una plataforma educativa tipo udemy (la api restful)Curso: De Cero a Full Stack\n\n¿Quieres convertirte en un desarrollador Full Stack Web Development y no sabes por dónde empezar? Este curso es perfecto para ti. \"De Cero a FullStack\" está diseñado para llevarte desde los conceptos más básicos hasta convertirte en un desarrollador web completo, capaz de crear aplicaciones web modernas y dinámicas.\n\nObjetivo del curso: Al finalizar este curso, tendras los conocimientos necesarios en tecnologias del frontend y del backend, seras capaz de desarrollar sitios web y aplicaciones web Full Stack, manejando tanto el Front-end como el Back-end con fluidez y confianza.\n\nDirigido a: Este curso está abierto a todo el mundo y no requiere conocimientos previos. Si tienes ganas de aprender y estás dispuesto a comprometerte, este curso es para ti.\n\nContenido del curso:\n\nFront-end Development:\n\nHTML: Aprende la estructura básica de las páginas web.\n\nCSS: Dale estilo y diseño a tus sitios web.\n\nFlexbox y Grid: Diseña layouts complejos y responsivos.\n\nBootstrap: Utiliza este popular framework para acelerar tu desarrollo.\n\nJavaScript: Domina el lenguaje de programación esencial para el desarrollo web.\n\njQuery: Simplifica la manipulación del DOM y mejora la interacción del usuario.\n\nProgramación Orientada a Objetos (POO) con TypeScript: Aprende los principios de la POO y cómo aplicarlos en TypeScript.\n\nAngular: Desarrolla aplicaciones web robustas y escalables con este poderoso framework.\n\nBack-end Development:\n\nPHP: Aprende a crear aplicaciones del lado del servidor.\n\nMySQL: Gestiona bases de datos y realiza consultas eficientes.\n\nProgramación Orientada a Objetos (POO): Aplica principios de POO en PHP.\n\nModelo Vista Controlador (MVC): Entiende y aplica este patrón de diseño para organizar tu código.\n\nLaravel: Utiliza este popular framework para desarrollar aplicaciones web modernas y escalables.\n\nIntegración Front-end y Back-end:\n\nAprende a combinar el Front-end con el Back-end.\n\nDesarrolla una API RESTful completa utilizando Angular y Laravel.\n\nDuración: El curso está diseñado para completarse a tu propio ritmo, pero se estima que tomarás varias semanas o meses para completarlo, dependiendo de tu dedicación y ritmo de estudio.\n\nTiene un total de aproximadamente 500 videos de 15 minutos cada uno. Además, se complementará con teoria y ejercicios que te permitirán aplicar lo aprendido en proyectos reales.\n\n\n\nRequisitos previos: No se requieren conocimientos previos. Este curso está diseñado para principiantes absolutos.\n\n\n\n         Beneficios:\n\nAdquirirás habilidades en desarrollo web Full Stack, una de las áreas más demandadas en la industria tecnológica.\n\nDesarrollarás proyectos prácticos que podrás incluir en tu portafolio profesional.\n\nObtendrás una comprensión profunda de las tecnologías y herramientas más utilizadas en el desarrollo web moderno.\n\n\n\nAlgunos de los proyectos que desarrollaremos desde cero:\n\nAplicaremos todo lo aprendido con css,flexbox , grid y bootstrap haciendo una plantilla completa\n\nAplicaremos todo lo aprendido con php y mysql haciendo un blog\n\nAplicaremos todo lo aprendido con Laravel y crearemos una red social\n\nMientras avanzamos en el curso iremos haciendo tareas , ejercicios y proyectos\n\nEl proyecto final sera una plataforma educativa tipo udemy (la api restful)', 'Programación en el Front-end\nFundamentos del Desarrollo Web\nComprende los conceptos básicos de HTML y CSS\nDiseña y estructura páginas web\nUtiliza Flexbox y Grid para crear diseños responsivos\nAplica estilos avanzados con CSS y Bootstrap\nDominio completo con css ,flexbox, grid y bootstrap\nDomina JavaScript para crear interactividad en tus sitios web\nSimplifica la manipulación del DOM con jQuery\nDominar la POO en TypeScript\nProgramación Orientada a Objetos (POO)\nFramework Angular\nDesarrollo Back-end\nAprende PHP complet', 'NO SE NECESITA NINGÚN CONOCIMIENTO PREVIO ASI QUE PUEDES INICIAR ESTE CURSO DESDE AHORA MISMO Y MEJORAR TUS HABILIDADES \nGanas de APRENDER', 'Cualquier persona (No importa si no sabe nada de programacion)\nPersonas que quieran ser desarrolladores web fulstack\nPersonas que quieran ser programadores frontend\nPersonas que quieran ser programadores backend\nProgramadores\nGente que quiere aprender desarrollo web profesional\nEstudiantes de informática\nEstudiantes de ingeníera o ciclos formativos relacionado a la informática\nInteresados en HTML\nInteresados en CSS\nInteresados en FLEXBOX\nInteresados en GRID', 'storage/public/banners/68d56dcb6b879_1758817739.jpg', 'storage/public/promoVideos/68d56a57167fb_1758816855.mp4', 0, 1, 16, 'borrador', '2025-09-25 16:12:23'),
(24, 'flutter-movil-de-cero-a-experto-en-5-horas', 'Flutter - Móvil: De cero a experto en 5 horas', 'Este curso representa años de esfuerzo y estudio en Dart y Flutter sintetizados en más de 50 horas de video bajo demanda que van desde las bases del lenguaje Dart, hasta todo lo necesario para crear aplicaciones en Flutter funcionales y atractivas visualmente.\r\n\r\nEl curso no sólo pretende enseñarte Dart y Flutter, sino que aprendas a crear aplicaciones reales siguiendo el Doman Driven Design, una forma de programar y estructurar proyectos que nos permitan hacer aplicaciones fáciles de expandir y mantener, pasando por Clean Code y varios patrones que te ayudarán a que estés orgulloso del código que escribes.\r\n\r\nPuntualmente veremos:\r\n\r\nBases de Dart\r\n\r\nDesde Hola Mundo hasta funciones generadoras\r\n\r\nPasando por clases abstractas, mixins hasta su uso en patrones arquitectónicos\r\n\r\nStateless y Stateful Widgets\r\n\r\nHojas de Atajos para acompañarte\r\n\r\nCientos de widgets de Flutter\r\n\r\nWidgets personalizados\r\n\r\nGestores de estados\r\n\r\nRiverpod 2.3 >\r\n\r\nFutter_Bloc 8>\r\n\r\nProvider 6 >\r\n\r\nCubits\r\n\r\nState en Stateful Widgets', 'prender Dart para utilizarlo cómodamente en Flutter \r\nDominar Flutter mediante muchas aplicaciones funcionales y visualmente atractivas \r\nGestionar la estructura de proyectos de Flutter\r\nAplicar principios SOLID, Clean Code y bases de arquitecturas de software \r\nPublicar Aplicaciones en la Apple AppStore y Google PlayStore ', 'Es necesario tener conceptos de programación estructurada \r\nSi no se sabe el requisito anterior, es recomendado mi curso de\r\nPuedes seguir el curso en Windows, Mac o Linux (Instalaciones \r\nRevisar los requisitos mínimos de Flutter dependiendo de tu sistema ', 'ualquier persona que quiera aprender Flutter\r\nCualquiera que quiera mejorar en Dart\r\nCualquiera que quiera aprender a crear hermosas\r\nTodos los que quieran aprender a manejar un Router auto\r\nPersonas que quieran mantenerse actualizados en la tecnolog', 'storage/public/banners/68d5707aba951_1758818426.jpg', 'storage/public/promoVideos/68d5707abaccd_1758818426.mp4', 65000, 4, 16, 'activo', '2025-09-25 16:40:27');

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
(45, 22, 'Primera sección', 'Introducción', 1, 'activo', '2025-09-25 20:16:12', '2025-09-25 20:16:12'),
(46, 22, 'Segunda sección', 'Bases de anatomia', 2, 'activo', '2025-09-25 20:18:18', '2025-09-25 20:18:18'),
(48, 22, '3', 'hola', 3, 'activo', '2025-09-25 20:59:10', '2025-09-25 20:59:10'),
(49, 22, '4', 'Porque la vida es asi', 4, 'activo', '2025-09-25 21:00:25', '2025-09-25 21:00:25'),
(50, 24, 'Sección 1: Introducción', 'Escucha con atención', 1, 'activo', '2025-09-26 02:26:09', '2025-09-26 02:26:38'),
(51, 24, 'Sección 2: Las Rutas', '', 2, 'activo', '2025-09-26 02:26:25', '2025-09-26 02:26:25'),
(52, 24, 'Sección 3: Todo sobre Controladores', '', 3, 'activo', '2025-09-26 02:27:02', '2025-09-26 02:27:02'),
(53, 24, 'Sección 4: Entendiendo los middleware', 'Es muy importante para seguridad', 4, 'activo', '2025-09-26 02:27:27', '2025-09-26 02:27:27');

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
(35, 24, 15, 'pendiente', 0, '2025-09-25 19:33:32'),
(36, 21, 13, 'pendiente', 0, '2025-09-26 02:46:52'),
(37, 24, 13, 'pendiente', 0, '2025-09-26 02:47:09'),
(38, 21, 15, 'activo', 0, '2025-09-26 03:06:10');

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
(1, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 10:05:53'),
(2, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 10:07:05'),
(3, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 10:08:36'),
(4, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 10:08:56'),
(5, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 10:10:17'),
(6, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 10:12:19'),
(7, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 10:55:38'),
(8, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 10:55:46'),
(9, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 11:05:16'),
(10, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 11:06:12'),
(11, 15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0', '2025-09-25 11:15:52'),
(12, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 11:56:56'),
(13, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 12:23:28'),
(14, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 14:31:58'),
(15, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 14:32:10'),
(16, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 14:33:02'),
(17, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 14:33:25'),
(18, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 14:35:23'),
(19, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 14:37:00'),
(20, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 14:40:19'),
(21, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 15:14:56'),
(22, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 15:15:57'),
(23, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 15:38:53'),
(24, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 15:39:00'),
(25, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 16:01:09'),
(26, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:34:55'),
(27, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:35:10'),
(28, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:36:30'),
(29, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:36:49'),
(30, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:38:31'),
(31, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:40:05'),
(32, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:40:25'),
(33, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:44:05'),
(34, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:44:16'),
(35, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:50:56'),
(36, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 19:55:38'),
(37, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 20:58:33'),
(38, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 21:11:02'),
(39, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 21:16:42'),
(40, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 21:17:16'),
(41, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 21:17:50'),
(42, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 21:21:01'),
(43, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 21:44:57'),
(44, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 21:45:30'),
(45, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 21:47:15'),
(46, 15, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 22:06:05'),
(47, 16, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 22:06:42'),
(48, 13, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 22:06:57'),
(49, 14, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 22:07:07');

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
(13, 'clienteRegistro', 'Mauricio Muñoz', 'mauro@gmail.com', '$2y$10$GqSWusQdjnD8hwuXAdj9GOCG/MibiqGhNs273QJUvBYty2FTkTTR.', 1, 'storage/public/usuarios/13/perfil_1758853052_798.jpg', 'Ing. Sistemas', '340 000 0000', 'NA', NULL, 'Colombia', 'Cali', 'activo', 0, 0, 0, '1344234567', '2025-09-25 15:05:32'),
(14, 'clienteRegistro', 'Andres Sanchez', 'mauriciomuozsanchez12@gmail.com', '$2y$10$t9VAqXuzkPu5mVouUPxOYeO.0CYP0Si6X8lRnEmB5kNqohFPCHi0.', 1, 'storage/public/usuarios/14/perfil_1758853022_783.jpg', 'Ingeniero de Sistemas', '300 123 1234', 'Carrera 123 #50 - 30', NULL, 'Colombia', 'Cali', 'activo', 1, 1, 0, '1234567890', '2025-09-25 15:07:49'),
(15, 'clienteRegistro', 'Carlos Perez', 'carlos@gmail.com', '$2y$10$xg.ZqGW3eQ8OkiN0A9By3ORkOx8pIDm1bKGqAgGHHXk7RaKvwqFdS', 1, 'storage/public/usuarios/15/perfil_1758853082_439.jpg', 'Estudiante de Ing. Sistemas', '313 500 0000', 'NA', NULL, 'Colombia', 'cali', 'activo', 1, 1, 0, '1144212229', '2025-09-25 15:10:03'),
(16, 'clienteRegistro', 'Maria Gutierrez', 'mariagutierrez@gmail.com', '$2y$10$lSj.A9w.455NwJHBQ8rk4elESw7mATZyScYeXvbox2pQQIBQig.iy', 1, 'storage/public/usuarios/16/perfil_1758852802_361.jpg', 'Ingeniera de Sistemas', '300 123 4569', 'Calle 25 # 20 30', 'I’m Maria Gutierrez, a systems analyst with a strong background in web development. I’ve dedicated myself to exploring and mastering the most advanced technologies.\r\n\r\nI’m passionate about experimenting with new projects and technologies, always looking for innovative approaches.\r\n\r\nMy practical approach, using simple words and breaking things down to their simplest form, makes me an ideal instructor for those looking to learn web development from scratch.\r\n\r\nMy passion for technology and continuous learning has driven me to dive deeper into this field and stay up to date with the latest technologies and industry trends. Now I want to teach on this platform, sharing my knowledge and offering all the material I’ve gathered.\r\n\r\nWith my course \" Complete Full Stack Web Develope,\" my goal is to guide students through a complete journey in web development, helping them gain the skills needed to become Full Stack Web Developers.\r\n-------------------------------------------\r\nSoy Maria Gutierrez, un analista de sistemas con una sólida formación en el desarrollo web. Me he dedicado a explorar y dominar las tecnologías más avanzadas.\r\n\r\nMe apasiona experimentar con nuevos proyectos y tecnologías, siempre buscando maneras innovadoras.\r\n\r\nMi enfoque práctico utilizando palabras sencillas y llevarlo todo a lo mas simple posible, me convierten en un educador ideal para aquellos que buscan aprender desarrollo web desde cero.\r\n\r\nMi pasión por la tecnología y el aprendizaje continuo me ha llevado a profundizar en esta área y a mantenerme al día con las últimas tecnologías y tendencias del sector. Ahora quiero enseñar desde esta plataforma compartiendo este conocimiento y dando todo el material que obtuve.\r\n\r\nCon mi curso \"De Cero a Full Stack\", tengo como objetivo guiar a los estudiantes a través de un recorrido completo en el desarrollo web, ayudándolos a adquirir las habilidades necesarias para convertirse en desarrolladores web Full Stack.', 'Colombia', 'Cali', 'activo', 1, 1, 0, '123456789000', '2025-09-25 16:05:07');

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
(13, 1),
(13, 2),
(13, 3),
(14, 2),
(15, 3),
(16, 2),
(16, 3);

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
(33, 21, 15, 38, 'convertido', '2025-09-25 20:15:03', '2025-09-26 03:06:10');

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
(57, 50, '1. Como instalar Flutter en Windows', '00:07:17', 1, 'activo', '2025-09-26 02:29:01', '2025-09-26 02:29:03'),
(58, 50, '2. Configuración de Flutter en Windows', '00:03:35', 2, 'activo', '2025-09-26 02:30:38', '2025-09-26 02:30:38'),
(59, 50, 'Guía definitiva en PDF', '00:00:00', 1, 'activo', '2025-09-26 02:35:34', '2025-09-26 02:43:26'),
(60, 51, '1. Navegación con Rutas en Flutter', '00:00:00', 1, 'activo', '2025-09-26 02:37:29', '2025-09-26 02:37:29'),
(61, 51, 'Como cambiar rutas de APK', '00:01:06', 2, 'activo', '2025-09-26 02:42:48', '2025-09-26 02:42:48');

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
(119, 57, 'video', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/50/57/video/68d5fa6f500d0_1758853743.mp4', 'storage/public/section_assets/24/50/57/video/68d5fa6f500d0_1758853743.mp4', 25291741, 437, '2025-09-26 02:29:03'),
(120, 58, 'video', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/50/58/video/68d5facebfb69_1758853838.mp4', 'storage/public/section_assets/24/50/58/video/68d5facebfb69_1758853838.mp4', 10989597, 215, '2025-09-26 02:30:38'),
(121, 59, 'pdf', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/50/59/pdf/68d5fbf69f042_1758854134.pdf', 'storage/public/section_assets/24/50/59/pdf/68d5fbf69f042_1758854134.pdf', 16176, NULL, '2025-09-26 02:35:34'),
(122, 60, 'pdf', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/51/60/pdf/68d5fc69543a5_1758854249.pdf', 'storage/public/section_assets/24/51/60/pdf/68d5fc69543a5_1758854249.pdf', 16176, NULL, '2025-09-26 02:37:29'),
(123, 60, 'video', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/51/60/video/68d5fc69a88a3_1758854249.mp4', 'storage/public/section_assets/24/51/60/video/68d5fc69a88a3_1758854249.mp4', 25137615, 0, '2025-09-26 02:37:29'),
(124, 61, 'pdf', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/51/61/pdf/68d5fda854ee9_1758854568.pdf', 'storage/public/section_assets/24/51/61/pdf/68d5fda854ee9_1758854568.pdf', 16176, NULL, '2025-09-26 02:42:48'),
(125, 61, 'video', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/51/61/video/68d5fda8933f3_1758854568.mp4', 'storage/public/section_assets/24/51/61/video/68d5fda8933f3_1758854568.mp4', 2450194, 66, '2025-09-26 02:42:48'),
(126, 59, 'pdf', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/50/59/pdf/68d5fdce896f3_1758854606.pdf', 'storage/public/section_assets/24/50/59/pdf/68d5fdce896f3_1758854606.pdf', 16176, NULL, '2025-09-26 02:43:26'),
(127, 59, 'pdf', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/50/59/pdf/68d5fdce9fd75_1758854606.pdf', 'storage/public/section_assets/24/50/59/pdf/68d5fdce9fd75_1758854606.pdf', 16176, NULL, '2025-09-26 02:43:26'),
(128, 59, 'pdf', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/50/59/pdf/68d5fdceb2202_1758854606.pdf', 'storage/public/section_assets/24/50/59/pdf/68d5fdceb2202_1758854606.pdf', 16176, NULL, '2025-09-26 02:43:26'),
(129, 59, 'pdf', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/50/59/pdf/68d5fdcec288e_1758854606.pdf', 'storage/public/section_assets/24/50/59/pdf/68d5fdcec288e_1758854606.pdf', 16176, NULL, '2025-09-26 02:43:26'),
(130, 59, 'pdf', 'C:/xampp/htdocs/factuonlinetraining/storage/public/section_assets/24/50/59/pdf/68d5fdcece009_1758854606.pdf', 'storage/public/section_assets/24/50/59/pdf/68d5fdcece009_1758854606.pdf', 16176, NULL, '2025-09-26 02:43:26');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `curso_secciones`
--
ALTER TABLE `curso_secciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `log_ingreso`
--
ALTER TABLE `log_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `seccion_contenido`
--
ALTER TABLE `seccion_contenido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de la tabla `seccion_contenido_assets`
--
ALTER TABLE `seccion_contenido_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT de la tabla `seccion_contenido_progreso`
--
ALTER TABLE `seccion_contenido_progreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
