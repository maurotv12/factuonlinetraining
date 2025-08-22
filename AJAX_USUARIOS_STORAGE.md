# 🔧 Actualización AJAX - Fotos de Perfil Storage

## ✅ Cambios Implementados en `usuarios.ajax.php`

### 1. **Función Auxiliar Agregada**
```php
function eliminarFotoAnteriorAjax($idUsuario)
```
- ✅ **Eliminación inteligente:** No elimina `default.png`
- ✅ **Validación de existencia:** Verifica archivo antes de eliminar
- ✅ **Compatibilidad dual:** Funciona con rutas legacy y storage

### 2. **Método `actualizar_foto` - Mejorado**

#### **ANTES:**
```php
// Problemas del código anterior:
- No eliminaba fotos anteriores
- Tamaño 200x200 (inconsistente)
- Ruta incorrecta en BD
- Sin timestamp en nombres
- Directorio mal construido
```

#### **AHORA:**
```php
// Mejoras implementadas:
✅ Elimina foto anterior automáticamente
✅ Tamaño 500x500 (consistente con controlador)  
✅ Nombres con timestamp: "perfil_[timestamp]_[random].ext"
✅ Directorio correcto: /storage/public/usuarios/{id}/
✅ Ruta relativa en BD: "storage/public/usuarios/{id}/archivo.ext"
✅ URL pública correcta en respuesta
```

### 3. **Nuevo Método AJAX en Clase**
```php
public function ajaxObtenerFotoUsuario()
```
- ✅ **Validación automática:** Usa `ctrValidarFotoUsuario()`
- ✅ **Fallback robusto:** Default.png si usuario no existe
- ✅ **Respuesta JSON:** Estructura consistente

### 4. **Endpoint Agregado**
```php
// POST: obtenerFotoUsuario
if (isset($_POST["obtenerFotoUsuario"])) {
    // Retorna foto validada del usuario
}
```

## 🔧 **Proceso Actualizado de Cambio de Foto**

### **Flujo Mejorado:**
1. **Validación:** Tipo archivo (JPG/PNG) + tamaño (5MB máx)
2. **Directorio:** Crear `/storage/public/usuarios/{id}/` si no existe
3. **Limpieza:** Eliminar foto anterior (excepto default.png)
4. **Procesamiento:** Redimensionar a 500x500 con transparencia PNG
5. **Nomenclatura:** `perfil_{timestamp}_{random}.ext`
6. **Base de datos:** Guardar ruta relativa `storage/public/usuarios/{id}/archivo.ext`
7. **Respuesta:** URL pública completa `/cursosApp/storage/public/usuarios/{id}/archivo.ext`

### **Estructura de Respuesta JSON:**
```json
{
  "success": true,
  "message": "Foto actualizada correctamente",
  "nueva_ruta": "/cursosApp/storage/public/usuarios/123/perfil_1692345678_456.jpg"
}
```

## 📊 **Comparación Técnica**

| Aspecto | ANTES | AHORA |
|---------|--------|--------|
| **Directorio** | `storage/public/usuarios/{id}` (incorrecto) | `/storage/public/usuarios/{id}/` (correcto) |
| **Eliminación** | ❌ No elimina anteriores | ✅ Elimina automáticamente |
| **Tamaño imagen** | 200x200 | 500x500 (consistente) |
| **Nomenclatura** | `uniqid().ext` | `perfil_{timestamp}_{random}.ext` |
| **Ruta BD** | Absoluta incorrecta | Relativa correcta |
| **URL respuesta** | Ruta física | URL pública accesible |
| **Transparencia** | ✅ Preservada | ✅ Preservada |
| **Validaciones** | ✅ Básicas | ✅ Mejoradas |

## 🚀 **Funcionalidades Nuevas**

### **1. Eliminación Automática:**
```php
// Elimina foto anterior automáticamente
eliminarFotoAnteriorAjax($idUsuario);
```

### **2. Nomenclatura Descriptiva:**
```php
// Nombres más informativos
$nombreArchivo = "perfil_" . $timestamp . "_" . $aleatorio . "." . $extension;
```

### **3. Validación de Fotos:**
```php
// Endpoint para obtener foto validada
POST: obtenerFotoUsuario = {id_usuario}
Response: {"success": true, "foto": "/ruta/validada"}
```

### **4. Compatibilidad Garantizada:**
- ✅ **Rutas legacy:** Siguen funcionando
- ✅ **Rutas storage:** Nuevas implementaciones
- ✅ **Fallback:** Default.png siempre disponible

## 🔍 **Código JavaScript Recomendado**

### **Para actualizar foto:**
```javascript
const formData = new FormData();
formData.append('accion', 'actualizar_foto');
formData.append('imagen', archivoImagen);

fetch('ajax/usuarios.ajax.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        // Actualizar imagen en interfaz
        document.querySelector('#foto-perfil').src = data.nueva_ruta;
    }
});
```

### **Para obtener foto validada:**
```javascript
const formData = new FormData();
formData.append('obtenerFotoUsuario', idUsuario);

fetch('ajax/usuarios.ajax.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        document.querySelector('#foto-perfil').src = data.foto;
    }
});
```

## ⚡ **Archivos Afectados**

1. ✅ `App/ajax/usuarios.ajax.php` - **ACTUALIZADO COMPLETAMENTE**
   - Función auxiliar eliminación
   - Método actualizar_foto mejorado
   - Nuevo método obtener foto
   - Endpoint adicional

2. ✅ `App/controladores/usuarios.controlador.php` - **YA ACTUALIZADO**
   - Métodos de validación disponibles
   - Compatibilidad garantizada

3. ✅ `storage/public/usuarios/` - **ESTRUCTURA LISTA**
   - Directorios y permisos correctos
   - Imagen default.png disponible

---
**Estado: ✅ COMPLETADO** - AJAX de usuarios actualizado con nueva estructura storage
