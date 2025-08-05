# Sistema de Validación de Registro en Tiempo Real

## Descripción
Este sistema implementa validación en tiempo real para el formulario de registro de usuarios, verificando:
- Disponibilidad del email
- Fortaleza de la contraseña
- Validez del nombre

## Archivos Implementados

### 1. JavaScript (`validacionRegistro.js`)
- **Ubicación**: `publico/assetsPublico/js/validacionRegistro.js`
- **Funcionalidades**:
  - Validación en tiempo real del email con verificación de disponibilidad
  - Validación de contraseña con indicadores visuales de requisitos
  - Validación del nombre con caracteres permitidos
  - Control del estado del botón de envío
  - Debounce para optimizar las peticiones AJAX

### 2. CSS (`validacionRegistro.css`)
- **Ubicación**: `publico/assetsPublico/css/validacionRegistro.css`
- **Estilos**:
  - Indicadores visuales para campos válidos/inválidos
  - Animaciones para feedback del usuario
  - Estilos para los requisitos de contraseña
  - Estados del botón de envío

### 3. AJAX (`verificarEmail.php`)
- **Ubicación**: `App/ajax/verificarEmail.php`
- **Funcionalidad**:
  - Verifica en tiempo real si un email ya está registrado
  - Valida formato del email
  - Manejo de errores robusto
  - Respuesta en formato JSON

### 4. Plantilla Actualizada (`plantilla.php`)
- **Ubicación**: `publico/vistas/paginas/registro/vista/plantilla.php`
- **Referencias agregadas**:
  - CSS de validación en el `<head>`
  - JavaScript de validación antes del cierre del `<body>`
  - Font Awesome para iconos de validación

### 5. Controlador Actualizado (`autenticacion.controlador.php`)
- **Validaciones agregadas**:
  - Contraseña: mínimo 8 caracteres, mayúscula, minúscula, número
  - Nombre: mínimo 2 caracteres, solo letras y espacios
  - Verificación de email duplicado mejorada

## Requisitos de Contraseña

La contraseña debe cumplir con:
- ✅ Mínimo 8 caracteres
- ✅ Al menos una letra mayúscula (A-Z)
- ✅ Al menos una letra minúscula (a-z)
- ✅ Al menos un número (0-9)

## Validación de Nombre

El nombre debe:
- ✅ Tener al menos 2 caracteres
- ✅ Contener solo letras (incluye acentos) y espacios
- ✅ No comenzar o terminar con espacios

## Validación de Email

El email debe:
- ✅ Tener formato válido (ejemplo@dominio.com)
- ✅ No estar ya registrado en el sistema
- ✅ La verificación se hace con debounce de 800ms

## Flujo de Validación

1. **Usuario escribe en campo**: Se activa validación en tiempo real
2. **Formato válido**: Se verifica disponibilidad (solo para email)
3. **Todos los campos válidos**: Se habilita botón de envío
4. **Envío del formulario**: Se valida nuevamente en el servidor
5. **Registro exitoso**: Se asigna rol de "estudiante" automáticamente

## Estados del Botón

- **Deshabilitado** (gris): Cuando hay errores de validación
- **Habilitado** (azul): Cuando todos los campos son válidos
- **Hover**: Efecto visual cuando está habilitado

## Indicadores Visuales

- **Verde**: Campo válido
- **Rojo**: Campo con errores
- **Azul**: Verificando disponibilidad
- **Lista de requisitos**: Para contraseña con checks dinámicos

## Optimizaciones

- **Debounce**: Evita múltiples peticiones AJAX
- **Cache de validación**: No re-valida innecesariamente
- **Feedback inmediato**: Usuario sabe instantáneamente el estado
- **Accesibilidad**: Mensajes claros y colores contrastantes

## Seguridad

- Validación dual (cliente y servidor)
- Sanitización de entradas
- Prevención de inyección SQL
- Manejo seguro de errores
- Headers CORS apropiados

## Compatibilidad

- Funciona con navegadores modernos
- Requiere JavaScript habilitado
- Compatible con dispositivos móviles
- Degradación elegante sin JavaScript

## Uso

1. Los archivos CSS y JS están incluidos automáticamente en `plantilla.php`
2. Asegurar que el archivo AJAX esté accesible en `App/ajax/verificarEmail.php`
3. Verificar que la base de datos tenga la tabla "persona"
4. Confirmar que las rutas en el JavaScript sean correctas

## Estructura de Archivos

```
publico/
├── assetsPublico/
│   ├── css/
│   │   └── validacionRegistro.css
│   └── js/
│       └── validacionRegistro.js
└── vistas/
    └── paginas/
        └── registro/
            └── vista/
                ├── plantilla.php (incluye CSS y JS)
                └── paginas/
                    └── register.php (formulario limpio)

App/
└── ajax/
    └── verificarEmail.php
```

## Mantenimiento

- Monitorear logs de errores en `verificarEmail.php`
- Actualizar requisitos de contraseña si es necesario
- Revisar performance de peticiones AJAX
- Mantener consistencia con validaciones del servidor
