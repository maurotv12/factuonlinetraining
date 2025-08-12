# JavaScript de Vistas de Estudiante - Documentación

## Estructura Modularizada

El sistema JavaScript para las vistas de estudiante ha sido reorganizado en una estructura modular para mejorar el mantenimiento y la escalabilidad.

## Archivos Creados

### 1. `estudiante-base.js` - Funcionalidades Compartidas
**Ubicación:** `/App/vistas/assets/js/pages/estudiante-base.js`

**Contiene:**
- Funcionalidad de búsqueda común
- Gestión de cards de cursos
- Navegación rápida
- Funciones utilitarias compartidas
- Manejo de estados de carga y error

**Funciones principales:**
- `initSearchFunctionality()`
- `searchCourses(query)`
- `loadCourses(category)`
- `displayCourses(courses)`
- `createCourseCard(course)`
- `viewCourse(courseId)`
- `showLoadingCards()`
- `showErrorMessage(message)`
- `updateResultsCount(total, query)`

### 2. `inicio-estudiante.js` - Vista de Inicio
**Ubicación:** `/App/vistas/assets/js/pages/inicio-estudiante.js`
**Vista:** `inicioEstudiante.php`

**Funcionalidades específicas:**
- Animaciones de bienvenida
- Gestión de action cards
- Contador inicial de cursos
- Navegación hacia otras secciones

**Funciones principales:**
- `initWelcomeAnimations()`
- `initActionCards()`
- `updateInitialCount()`
- `navigateToCategories()`
- `navigateToPreregistrations()`
- `navigateToCourses()`

### 3. `cursos-categorias.js` - Vista de Categorías
**Ubicación:** `/App/vistas/assets/js/pages/cursos-categorias.js`
**Vista:** `cursosCategorias.php`

**Funcionalidades específicas:**
- Filtros de categorías
- Animaciones de botones de categoría
- Búsqueda específica por categoría
- Gestión de URL parameters

**Funciones principales:**
- `initCategoryFilters()`
- `filterCategory(categoryId, categoryName)`
- `searchInCategory(query)`
- `updateCategoryInfo()`
- `showCategoryStats(categoryId)`

### 4. `cursos-estudiante.js` - Vista de Mis Cursos
**Ubicación:** `/App/vistas/assets/js/pages/cursos-estudiante.js`
**Vista:** `cursosEstudiante.php`

**Funcionalidades específicas:**
- Filtros por estado de curso
- Ordenamiento de cursos
- Animaciones de progreso
- Gestión de estadísticas
- Acciones de curso (continuar, revisar, certificado)

**Funciones principales:**
- `filterCourses(filter)`
- `sortCourses(sortBy)`
- `continuarCurso(courseId)`
- `revisarCurso(courseId)`
- `verCertificado(courseId)`
- `initProgressAnimations()`
- `updateMyCourses()`

### 5. `preinscripciones.js` - Vista de Preinscripciones
**Ubicación:** `/App/vistas/assets/js/pages/preinscripciones.js`
**Vista:** `preinscripciones.php`

**Funcionalidades específicas:**
- Gestión de preinscripciones
- Calculadora de precios
- Acciones masivas (completar todas, limpiar)
- Animaciones de eliminación
- Estados vacíos

**Funciones principales:**
- `completarInscripcion(courseId)`
- `removerPreinscripcion(courseId)`
- `completarTodasInscripciones()`
- `limpiarPreinscripciones()`
- `updateTotalPrice()`
- `showEmptyState()`

## Implementación en Vistas PHP

### Patrón de Carga
Cada vista PHP debe cargar:
1. **Archivo base:** `estudiante-base.js`
2. **Archivo específico:** `[nombre-vista].js`

### Ejemplo de implementación:
```html
<!-- JavaScript base y específico -->
<script src="/cursosApp/App/vistas/assets/js/pages/estudiante-base.js"></script>
<script src="/cursosApp/App/vistas/assets/js/pages/inicio-estudiante.js"></script>
```

## Vistas Actualizadas

### ✅ `inicioEstudiante.php`
- Carga: `estudiante-base.js` + `inicio-estudiante.js`
- JavaScript inline mínimo para configuración específica

### ✅ `cursosCategorias.php`
- Carga: `estudiante-base.js` + `cursos-categorias.js`
- Mantiene funcionalidad de filtros por categoría

### ✅ `cursosEstudiante.php`
- Carga: `estudiante-base.js` + `cursos-estudiante.js`
- Funcionalidades completas de gestión de cursos del estudiante

### ✅ `preinscripciones.php`
- Carga: `estudiante-base.js` + `preinscripciones.js`
- Sistema completo de gestión de preinscripciones

## Ventajas de la Nueva Estructura

### 1. **Mantenibilidad**
- Código específico por vista
- Funcionalidades compartidas centralizadas
- Fácil localización de bugs

### 2. **Rendimiento**
- Carga solo el JavaScript necesario
- Reutilización de funciones base
- Menor duplicación de código

### 3. **Escalabilidad**
- Fácil agregar nuevas vistas
- Funcionalidades base extensibles
- Patrón claro de implementación

### 4. **Debugging**
- Logs específicos por vista
- Separación clara de responsabilidades
- Fácil identificación de errores

## Funciones Globales Disponibles

### Desde `estudiante-base.js`:
- `viewCourse(courseId)`
- `loadCourses(category)`
- `searchCourses(query)`
- `initSearchFunctionality()`
- `initCourseCards()`
- `initQuickActions()`
- `showLoadingCards()`
- `showErrorMessage(message)`
- `updateResultsCount(total, query)`

### Específicas por Vista:
Cada archivo específico expone sus propias funciones globales según la funcionalidad de la vista.

## Compatibilidad

✅ **Mantiene compatibilidad completa** con:
- Funciones existentes
- Referencias desde PHP
- Eventos onclick en HTML
- Integraciones con SweetAlert2

## Notas de Desarrollo

1. **Orden de carga:** Siempre cargar `estudiante-base.js` antes que los archivos específicos
2. **Dependencias:** Todas las vistas dependen de las funciones base
3. **Convenciones:** Usar `init[NombreVista]()` como función principal de cada vista
4. **Logging:** Cada archivo específico incluye log de carga exitosa

## Archivo Original

El archivo original `estudiante.js` se mantiene como referencia pero ya no se usa en las vistas actualizadas.
