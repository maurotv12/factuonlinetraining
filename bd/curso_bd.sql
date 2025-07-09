
-- ESTRUCTURA BASE DE DATOS CURSOS TIPO UDEMY (OPTIMIZADA)

CREATE DATABASE IF NOT EXISTS cursos_udemy;
USE cursos_udemy;

-- TABLA ROLES
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO roles (nombre) VALUES 
  ('superadministrador'),
  ('profesor'),
  ('estudiante');

-- TABLA PERSONA (USUARIOS)
CREATE TABLE persona (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuarioLink VARCHAR(100) NOT NULL,
  nombre VARCHAR(200) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password TEXT NOT NULL,
  verificacion INT NOT NULL DEFAULT 0,
  foto VARCHAR(100) DEFAULT 'vistas/img/usuarios/default/default.png',
  profesion VARCHAR(300),
  telefono VARCHAR(100),
  direccion VARCHAR(200),
  perfil TEXT,
  Pais VARCHAR(200),
  estado BOOLEAN DEFAULT TRUE,
  fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLA INTERMEDIA PERSONA_ROLES
CREATE TABLE persona_roles (
  idPersona INT,
  idRol INT,
  PRIMARY KEY (idPersona, idRol),
  FOREIGN KEY (idPersona) REFERENCES persona(id),
  FOREIGN KEY (idRol) REFERENCES roles(id)
);

-- TABLA CATEGORIAS
CREATE TABLE categoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion VARCHAR(300),
  fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABLA CURSO
CREATE TABLE curso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  urlAmiga VARCHAR(100) NOT NULL,
  nombre VARCHAR(300) NOT NULL,
  descripcion TEXT NOT NULL,
  banner VARCHAR(300),
  promoVideo VARCHAR(150),
  valor INT NOT NULL,
  idCategoria INT,
  idPersona INT,
  estado VARCHAR(20) DEFAULT 'activo',
  fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (idCategoria) REFERENCES categoria(id),
  FOREIGN KEY (idPersona) REFERENCES persona(id)
);

-- TABLA SECCIONES
CREATE TABLE secciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  idCurso INT,
  nombre VARCHAR(300),
  descripcion TEXT,
  duracion VARCHAR(100),
  url VARCHAR(250),
  tipo VARCHAR(200),
  fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (idCurso) REFERENCES curso(id)
);

-- TABLA INSCRIPCIONES
CREATE TABLE inscripciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  idCurso INT,
  idEstudiante INT,
  estado VARCHAR(100) DEFAULT 'pendiente',
  finalizado BOOLEAN DEFAULT FALSE,
  fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (idCurso) REFERENCES curso(id),
  FOREIGN KEY (idEstudiante) REFERENCES persona(id)
);

-- TABLA GESTION PAGOS
CREATE TABLE gestionpagos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  idInscripcion INT,
  valorPagado INT NOT NULL,
  mediodePago VARCHAR(100) NOT NULL,
  fechaPago TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (idInscripcion) REFERENCES inscripciones(id)
);

-- TABLA LOG INGRESO
CREATE TABLE log_ingreso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuarioId INT,
  ipUsuario VARCHAR(45),
  navegador VARCHAR(255),
  fechaR DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuarioId) REFERENCES persona(id)
);

-- TABLA REQUISITOS DE CURSO
CREATE TABLE requisitos_curso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  idCurso INT,
  descripcion TEXT NOT NULL,
  FOREIGN KEY (idCurso) REFERENCES curso(id)
);

-- TABLA ARCHIVOS ADICIONALES
CREATE TABLE archivos_adicionales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  idCurso INT,
  nombreArchivo VARCHAR(255),
  rutaArchivo TEXT,
  tipo VARCHAR(50),
  fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (idCurso) REFERENCES curso(id)
);

-- TABLA MENSAJES ENTRE USUARIOS
CREATE TABLE mensajes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  idRemitente INT,
  idDestinatario INT,
  asunto VARCHAR(150),
  mensaje TEXT,
  leido BOOLEAN DEFAULT FALSE,
  fechaEnvio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (idRemitente) REFERENCES persona(id),
  FOREIGN KEY (idDestinatario) REFERENCES persona(id)
);
