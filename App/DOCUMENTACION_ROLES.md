# Sistema de Control de Acceso por Roles

Este sistema permite controlar el acceso a páginas y contenido específico basado en los roles de usuario.

## Configuración Inicial

### 1. Estructura de Base de Datos
Asegúrate de tener las siguientes tablas:
- `usuarios` - Tabla de usuarios
- `roles` - Tabla de roles (admin, profesor, estudiante, etc.)
- `persona_roles` - Tabla de relación usuarios-roles

### 2. Activar el Sistema
En `vistas/plantilla.php`, cambia:
```php
include ControladorGeneral::ctrCargarPagina();
```
Por:
```php
include ControladorGeneral::ctrCargarPaginaConAcceso();
```

## Funciones Disponibles

### 1. Control de Acceso a Páginas Completas

#### `ctrCargarPaginaConAcceso()`
Controla automáticamente el acceso a páginas basado en la configuración de roles.

#### Configurar Restricciones
Edita el método `obtenerRestriccionesPaginas()` en `ControladorGeneral`:
```php
private static function obtenerRestriccionesPaginas()
{
    return [
        'superAdmin/usuarios' => ['admin', 'superadmin'],
        'cursos' => ['profesor', 'admin', 'superadmin'],
        'misCursos' => ['estudiante', 'profesor', 'admin', 'superadmin'],
        // Agrega más páginas según necesites
    ];
}
```

### 2. Verificación de Roles

#### `ctrUsuarioTieneRol($nombreRol)`
Verifica si el usuario actual tiene un rol específico:
```php
if (ControladorGeneral::ctrUsuarioTieneRol('admin')) {
    // Mostrar contenido de administrador
}
```

#### `ctrUsuarioTieneAlgunRol($rolesPermitidos)`
Verifica si el usuario tiene alguno de los roles especificados:
```php
if (ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin'])) {
    // Mostrar contenido para profesores o administradores
}
```

#### `ctrObtenerRolesUsuarioActual()`
Obtiene todos los roles del usuario actual:
```php
$roles = ControladorGeneral::ctrObtenerRolesUsuarioActual();
foreach ($roles as $rol) {
    echo $rol['nombre'];
}
```

### 3. Control de Contenido en Vistas

#### Ejemplo en una página PHP:
```php
<?php
// Verificar acceso general a la página
$tieneAcceso = ControladorGeneral::ctrVerificarAccesoContenido('misPagina');
if (!$tieneAcceso) {
    echo '<div class="alert alert-warning">Sin permisos</div>';
    return;
}
?>

<!-- Contenido solo para administradores -->
<?php if (ControladorGeneral::ctrUsuarioTieneRol('admin')): ?>
    <div class="admin-panel">
        <h3>Panel de Administración</h3>
        <!-- Contenido admin -->
    </div>
<?php endif; ?>

<!-- Contenido para profesores y administradores -->
<?php if (ControladorGeneral::ctrUsuarioTieneAlgunRol(['profesor', 'admin'])): ?>
    <div class="teacher-panel">
        <h3>Panel de Profesor</h3>
        <!-- Contenido profesor -->
    </div>
<?php endif; ?>
```

### 4. Menú Dinámico

#### `ctrGenerarMenuPorRoles()`
Genera un menú automático basado en roles:
```php
$menuItems = ControladorGeneral::ctrGenerarMenuPorRoles();
foreach ($menuItems as $item) {
    echo '<a href="' . $item['url'] . '">' . $item['nombre'] . '</a>';
}
```

## Páginas Especiales

### Página de Acceso Denegado
Se creó automáticamente en `vistas/paginas/accesoDenegado.php`. Se muestra cuando un usuario no tiene permisos para acceder a una página.

## Casos de Uso Comunes

### 1. Proteger una Página Completa
Solo agrega la página y sus roles permitidos en `obtenerRestriccionesPaginas()`:
```php
'mipagina' => ['admin', 'superadmin']
```

### 2. Mostrar Botones Condicionalmente
```php
<?php if (ControladorGeneral::ctrUsuarioTieneRol('admin')): ?>
    <button class="btn btn-danger">Eliminar Usuario</button>
<?php endif; ?>
```

### 3. Cargar Diferentes Vistas según Rol
```php
<?php if (ControladorGeneral::ctrUsuarioTieneRol('admin')): ?>
    <?php include 'vistas/admin/dashboard.php'; ?>
<?php elseif (ControladorGeneral::ctrUsuarioTieneRol('profesor')): ?>
    <?php include 'vistas/profesor/dashboard.php'; ?>
<?php else: ?>
    <?php include 'vistas/estudiante/dashboard.php'; ?>
<?php endif; ?>
```

### 4. AJAX con Verificación de Roles
En tus archivos AJAX, verifica roles antes de procesar:
```php
session_start();
if (!ControladorGeneral::ctrUsuarioTieneAlgunRol(['admin', 'profesor'])) {
    echo json_encode(['error' => 'Sin permisos']);
    exit;
}
// Procesar solicitud AJAX
```

## Roles Predefinidos

- `superadmin` - Acceso total al sistema
- `admin` - Administrador general
- `profesor` - Puede gestionar cursos y estudiantes
- `estudiante` - Acceso básico a cursos

## Notas Importantes

1. **Sesión Requerida**: El sistema requiere que `$_SESSION['id']` esté definido.
2. **Seguridad**: Siempre verifica permisos tanto en frontend como backend.
3. **Rendimiento**: Las consultas de roles se optimizan automáticamente.
4. **Extensibilidad**: Fácil agregar nuevos roles y restricciones.

## Troubleshooting

### Error: "Acceso Denegado" para todos los usuarios
- Verifica que la tabla `persona_roles` tenga registros
- Confirma que los nombres de roles coincidan exactamente

### Página no encontrada después de implementar
- Verifica que el archivo PHP existe en `vistas/paginas/`
- Confirma que el nombre de la página está en `obtenerRestriccionesPaginas()`

### Usuario sin roles
- Asigna al menos un rol en la tabla `persona_roles`
- Verifica que el ID de usuario sea correcto en la sesión
