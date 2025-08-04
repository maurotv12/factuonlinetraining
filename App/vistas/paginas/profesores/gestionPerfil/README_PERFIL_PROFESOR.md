# Perfil del Profesor - Documentación de Implementación

## Descripción
Vista completa para el perfil del profesor que permite mostrar información pública y gestionar la privacidad de los datos sensibles, con edición dinámica en tiempo real.

## Archivos Creados/Modificados

### 1. Vista Principal
- **Archivo**: `App/vistas/paginas/profesores/gestionPerfil/perfilProfesor.php`
- **Descripción**: Vista principal del perfil del profesor con:
  - Información personal (nombre, foto, profesión, biografía)
  - Ubicación (país, ciudad)
  - Estadísticas de cursos
  - Configuración de privacidad
  - Edición dinámica de campos

### 2. Estilos CSS
- **Archivo**: `App/vistas/assets/css/pages/perfilProfesor.css`
- **Características**:
  - Paleta de colores especificada:
    - Primary: #3682c4
    - Accent: #ff8d14
    - Dark: #040c2c
    - Background: #fffffe
    - Gray: #8a9cac
  - Diseño responsive
  - Animaciones y transiciones suaves
  - Componentes interactivos

### 3. JavaScript
- **Archivo**: `App/vistas/assets/js/pages/perfilProfesor.js`
- **Funcionalidades**:
  - Edición dinámica de campos sin recargar página
  - Validación en tiempo real
  - Gestión de configuración de privacidad
  - Upload y preview de imágenes
  - Manejo de errores y alertas

### 4. AJAX
- **Archivo**: `App/ajax/usuarios.ajax.php` (modificado)
- **Nuevas funciones**:
  - `actualizar_campo`: Actualiza campos individuales del perfil
  - `actualizar_privacidad`: Gestiona configuración de privacidad

### 5. Configuración de Rutas
- **Archivo**: `App/controladores/general.controlador.php` (modificado)
- **Cambios**: Agregada restricción de acceso para `perfilProfesor`

### 6. Menú de Navegación
- **Archivo**: `App/vistas/plantillaPartes/menuProfesor.php` (modificado)
- **Cambios**: Agregado enlace "Mi Perfil" en el menú del profesor

## Base de Datos

### Campos Requeridos en la tabla `persona`:
```sql
-- Campos básicos (ya existentes)
id, usuario_link, nombre, email, password, foto, profesion, telefono, 
biografia, pais, ciudad, estado, fecha_registro

-- Campos de privacidad (nuevos - ejecutar script SQL)
mostrar_email TINYINT(1) DEFAULT 0
mostrar_telefono TINYINT(1) DEFAULT 0  
mostrar_identificacion TINYINT(1) DEFAULT 0
numero_identificacion VARCHAR(50) DEFAULT NULL
```

### Script de Base de Datos
- **Archivo**: `bd/agregar_campos_privacidad.sql`
- **Uso**: Ejecutar este script en la base de datos para agregar los campos de privacidad

## Funcionalidades Implementadas

### 1. Vista de Perfil
- **Información personal**: Nombre, foto, profesión, biografía
- **Ubicación**: País y ciudad
- **Estadísticas**: Número de cursos, estudiantes, calificación
- **Estado**: Activo/Inactivo con fecha de registro

### 2. Edición Dinámica (Solo Profesor Logueado)
- **Campos editables**: Nombre, profesión, biografía, país, ciudad
- **Validación**: En tiempo real con feedback visual
- **Guardado**: Automático vía AJAX
- **Cancelación**: Con ESC o botón cancelar
- **Navegación**: Enter para guardar (excepto textarea)

### 3. Gestión de Privacidad
- **Información sensible**: Email, teléfono, número de identificación
- **Configuración**: Switches para mostrar/ocultar públicamente
- **Preview**: Vista previa de información pública
- **Guardado**: Automático al cambiar configuración

### 4. Gestión de Imagen
- **Upload**: Modal para cambiar foto de perfil
- **Validación**: Solo JPG/PNG, máximo 2MB
- **Preview**: Vista previa antes de guardar
- **Redimensionado**: Automático a 500x500px

### 5. Tabs de Contenido
- **Acerca de**: Biografía e información personal
- **Cursos**: Lista de cursos del profesor
- **Privacidad**: Configuración de datos sensibles (solo profesor logueado)

## Paleta de Colores

```css
:root {
    --primary-color: #3682c4;   /* Azul principal */
    --accent-color: #ff8d14;    /* Naranja acentuado */
    --dark-color: #040c2c;      /* Azul muy oscuro */
    --background-color: #fffffe; /* Blanco de fondo */
    --gray-color: #8a9cac;      /* Gris para texto secundario */
}
```

## Seguridad Implementada

### 1. Validación de Sesión
- Verificación de usuario logueado
- Control de acceso basado en roles
- Validación de permisos de edición

### 2. Validación de Datos
- Sanitización de entrada (htmlspecialchars)
- Validación de campos requeridos
- Límites de caracteres (biografía: 1000 chars)
- Validación de tipos de archivo para imágenes

### 3. Prevención XSS
- Escape de HTML en todas las salidas
- Validación de campos permitidos
- Uso de PDO con parámetros vinculados

## Responsive Design

### Breakpoints:
- **Desktop**: > 768px - Layout completo con sidebar
- **Tablet**: 768px - Layout adaptado con ajustes de grid
- **Mobile**: < 576px - Layout vertical optimizado

### Adaptaciones:
- Grid de estadísticas adaptativo
- Navegación de tabs optimizada
- Formularios responsivos
- Imágenes escalables

## Uso

### Para Profesores Logueados:
1. Acceder desde menú lateral "Mi Perfil"
2. Hacer clic en cualquier campo editable
3. Modificar información directamente
4. Guardar con Enter o botón de confirmación
5. Configurar privacidad en tab "Privacidad"

### Para Visitantes:
1. Solo visualización de información pública
2. Información de contacto según configuración de privacidad
3. Lista de cursos del profesor

## Extensiones Futuras

### Posibles Mejoras:
1. **Sistema de notificaciones** para cambios de perfil
2. **Historial de cambios** con timestamps
3. **Integración con redes sociales**
4. **Sistema de badges/certificaciones**
5. **Métricas avanzadas** (vistas de perfil, engagement)
6. **Chat directo** con el profesor
7. **Calendario de disponibilidad**

### APIs Sugeridas:
- **Upload de archivos** con drag & drop
- **Geolocalización** para ciudades
- **Integración con servicios** de validación de identidad
- **Notificaciones push** para actualizaciones

## Notas Técnicas

### Dependencias:
- Bootstrap 5.x (UI framework)
- jQuery o Vanilla JS (ya implementado en Vanilla)
- PDO para base de datos
- PHP Sessions para autenticación

### Compatibilidad:
- PHP 7.4+
- MySQL/MariaDB 10.x
- Navegadores modernos (ES6+)

### Performance:
- Lazy loading de imágenes
- Debounce en validaciones
- Caché de consultas frecuentes
- Optimización de CSS/JS

## Troubleshooting

### Problemas Comunes:

1. **Error "Sesión no válida"**
   - Verificar variables de sesión ($_SESSION['id'])
   - Comprobar configuración de sesiones PHP

2. **Campos no se guardan**
   - Verificar permisos de la base de datos
   - Comprobar estructura de tabla `persona`
   - Revisar logs de errores PHP/MySQL

3. **Imágenes no se suben**
   - Verificar permisos de carpeta `vistas/img/usuarios/`
   - Comprobar límites de upload en PHP (upload_max_filesize)
   - Verificar extensiones permitidas

4. **CSS/JS no se cargan**
   - Verificar rutas de archivos
   - Limpiar caché del navegador
   - Comprobar permisos de archivos estáticos

### Debug Mode:
El JavaScript incluye funciones de debug disponibles en `window.profileDebug` cuando se ejecuta en localhost.

---

**Desarrollado para**: Sistema de Cursos Calibélula
**Versión**: 1.0
**Fecha**: Agosto 2025
