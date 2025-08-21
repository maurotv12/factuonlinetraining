# Storage Directory Structure

## 📁 Organización del Storage

### `/storage/`
- **Propósito**: Directorio raíz para todos los archivos del sistema
- **Seguridad**: Configurado con .htaccess para control de acceso
- **Backup**: Excluir archivos sensibles, mantener estructura

### `/storage/backups/`
- **Propósito**: Copias de seguridad de la base de datos y configuraciones
- **Contenido**: Archivos .sql, .dump, .tar.gz
- **Retención**: Configurar rotación automática
- **Git**: Ignorar todo el contenido (sensible)

### `/storage/logs/`
- **Propósito**: Logs del sistema, errores, accesos
- **Contenido**: error.log, access.log, debug.log
- **Rotación**: Configurar logrotate
- **Git**: Ignorar archivos .log (pueden ser grandes)

### `/storage/private/`
- **Propósito**: Archivos privados, no accesibles desde web
- **Seguridad**: Máxima protección
- **Git**: Ignorar todo contenido por seguridad

#### `/storage/private/originals/`
- **Propósito**: Archivos originales subidos por usuarios
- **Procesamiento**: Videos antes de convertir a HLS
- **Git**: Ignorar todo (archivos grandes y sensibles)

#### `/storage/private/originals/video/`
- **Propósito**: Videos originales de cursos
- **Formato**: MP4, AVI, MOV, etc.
- **Procesamiento**: Se convierten a HLS para streaming
- **Git**: Ignorar todo (archivos muy grandes)

#### `/storage/private/processing/`
- **Propósito**: Archivos en proceso de conversión
- **Contenido**: Colas de procesamiento, archivos temporales
- **Limpieza**: Auto-limpieza después del procesamiento
- **Git**: Ignorar todo

#### `/storage/private/payments/`
- **Propósito**: Información financiera sensible
- **Contenido**: Tokens, recibos, transacciones
- **Seguridad**: CRÍTICA - Acceso muy restringido
- **Git**: Ignorar ABSOLUTAMENTE TODO

### `/storage/public/`
- **Propósito**: Archivos accesibles desde la web
- **Optimización**: Configurar cache y compresión
- **Git**: Permitir algunos archivos optimizados

#### `/storage/public/banners/`
- **Propósito**: Banners de cursos y categorías
- **Formato**: JPG, PNG, WebP optimizados
- **Git**: Permitir archivos optimizados finales

#### `/storage/public/courses/`
- **Propósito**: Materiales descargables de cursos
- **Contenido**: PDFs, documentos, código fuente
- **Git**: Permitir archivos educativos importantes

#### `/storage/public/promos/`
- **Propósito**: Videos promocionales y marketing
- **Formato**: MP4, WebM optimizados para web
- **Git**: Permitir videos finales optimizados

### `/storage/tmp/`
- **Propósito**: Archivos temporales del sistema
- **Limpieza**: Auto-limpieza cada 24 horas
- **Git**: Ignorar todo contenido

## 🔒 Niveles de Seguridad

### Nivel 1 - Público (`public/`)
- Accesible directamente desde web
- Cache habilitado
- Optimización de imágenes

### Nivel 2 - Controlado (`public/courses/`)
- Acceso solo para usuarios registrados
- Verificación de permisos

### Nivel 3 - Privado (`private/`)
- Sin acceso directo desde web
- Solo a través de scripts PHP

### Nivel 4 - Crítico (`private/payments/`)
- Encriptación adicional
- Logs de auditoría
- Acceso ultra-restringido

## 🧹 Políticas de Limpieza

### Automática
- `/tmp/` - Cada 24 horas
- `/logs/` - Rotación semanal/mensual
- `/private/processing/` - Después de completar

### Manual
- `/backups/` - Según política de retención
- `/private/originals/` - Después de optimizar

## 📊 Monitoreo

### Métricas Importantes
- Espacio usado por directorio
- Archivos más antiguos
- Velocidad de procesamiento
- Errores de acceso

### Alertas
- Espacio en disco < 10%
- Archivos en processing > 1 hora
- Errores de acceso a payments/
- Logs de error muy grandes
