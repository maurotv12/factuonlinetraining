
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
  usuario_link VARCHAR(100) NOT NULL,
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
  fecha_registroegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLA INTERMEDIA PERSONA_ROLES
CREATE TABLE persona_roles (
  id_persona INT,
  id_rol INT,
  PRIMARY KEY (id_persona, id_rol),
  FOREIGN KEY (id_persona) REFERENCES persona(id),
  FOREIGN KEY (id_rol) REFERENCES roles(id)
);

-- TABLA CATEGORIAS
CREATE TABLE categoria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion VARCHAR(300),
  fecha_registroegistro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABLA CURSO
CREATE TABLE curso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  url_amiga VARCHAR(100) NOT NULL,
  nombre VARCHAR(300) NOT NULL,
  descripcion TEXT NOT NULL,
  banner VARCHAR(300),
  promo_video VARCHAR(150),
  valor INT NOT NULL,
  id_categoria INT,
  id_persona INT,
  estado VARCHAR(20) DEFAULT 'activo',
  fecha_registroegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_categoria) REFERENCES categoria(id),
  FOREIGN KEY (id_persona) REFERENCES persona(id)
);

-- TABLA SECCIONES
CREATE TABLE secciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_curso INT,
  nombre VARCHAR(300),
  descripcion TEXT,
  duracion VARCHAR(100),
  url VARCHAR(250),
  tipo VARCHAR(200),
  fecha_registroegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_curso) REFERENCES curso(id)
);

-- TABLA INSCRIPCIONES
CREATE TABLE inscripciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_curso INT,
  id_estudiante INT,
  estado VARCHAR(100) DEFAULT 'pendiente',
  finalizado BOOLEAN DEFAULT FALSE,
  fecha_registroegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_curso) REFERENCES curso(id),
  FOREIGN KEY (id_estudiante) REFERENCES persona(id)
);

-- TABLA GESTION PAGOS
CREATE TABLE gestionpagos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_inscripcion INT,
  valor_pagado INT NOT NULL,
  medio_pago VARCHAR(100) NOT NULL,
  fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  fecha_registroegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_inscripcion) REFERENCES inscripciones(id)
);

-- TABLA LOG INGRESO
CREATE TABLE log_ingreso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_persona INT,
  ipUsuario VARCHAR(45),
  navegador VARCHAR(255),
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_persona) REFERENCES persona(id)
);

-- TABLA REQUISITOS DE CURSO
CREATE TABLE requisitos_curso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_curso INT,
  descripcion TEXT NOT NULL,
  FOREIGN KEY (id_curso) REFERENCES curso(id)
);

-- TABLA ARCHIVOS ADICIONALES
CREATE TABLE archivos_adicionales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_curso INT,
  nombre_archivo VARCHAR(255),
  ruta_archivo TEXT,
  tipo VARCHAR(50),
  fecha_registroegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_curso) REFERENCES curso(id)
);

-- TABLA MENSAJES ENTRE USUARIOS
CREATE TABLE mensajes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_remitente INT,
  id_destinatario INT,
  asunto VARCHAR(150),
  mensaje TEXT,
  leido BOOLEAN DEFAULT FALSE,
  fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_remitente) REFERENCES persona(id),
  FOREIGN KEY (id_destinatario) REFERENCES persona(id)
);


-- TABLA SOLICITUDES DE INSTRUCTORES
CREATE TABLE solicitudes_instructores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_persona INT NOT NULL,
  estado ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente',
  fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_persona) REFERENCES persona(id)
);
