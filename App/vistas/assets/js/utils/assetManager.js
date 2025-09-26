/**
 * Funciones JavaScript para gestión avanzada de assets
 * Incluye reemplazo automático y eliminación completa
 */

/**
 * Reemplazar un asset específico
 * @param {number} idAsset - ID del asset a reemplazar
 * @param {File} nuevoArchivo - Nuevo archivo a subir
 * @param {string} tipoAsset - Tipo de asset ('video' o 'pdf')
 * @returns {Promise} - Promesa con el resultado de la operación
 */
async function reemplazarAsset(idAsset, nuevoArchivo, tipoAsset) {
    try {
        // Validar archivo antes de enviar
        if (!validarArchivo(nuevoArchivo, tipoAsset)) {
            throw new Error('Archivo no válido');
        }

        // Crear FormData
        const formData = new FormData();
        formData.append('accion', 'reemplazarAsset');
        formData.append('archivo', nuevoArchivo);
        formData.append('idAsset', idAsset);
        formData.append('assetTipo', tipoAsset);

        // Mostrar indicador de carga
        mostrarCargando(`Reemplazando ${tipoAsset}...`);

        // Enviar petición
        const response = await fetch('/factuonlinetraining/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            mostrarNotificacion('Asset reemplazado exitosamente', 'success');
            // Recargar contenido de secciones para mostrar cambios
            await recargarContenidoSecciones();
            return data;
        } else {
            throw new Error(data.mensaje || 'Error al reemplazar asset');
        }

    } catch (error) {
        console.error('Error al reemplazar asset:', error);
        mostrarNotificacion(error.message, 'error');
        throw error;
    } finally {
        ocultarCargando();
    }
}

/**
 * Subir video a contenido (reemplaza automáticamente si existe)
 * @param {number} idContenido - ID del contenido
 * @param {number} idCurso - ID del curso  
 * @param {number} idSeccion - ID de la sección
 * @param {File} archivoVideo - Archivo de video
 * @returns {Promise} - Promesa con el resultado
 */
async function subirVideoContenido(idContenido, idCurso, idSeccion, archivoVideo) {
    try {
        // Validar video
        if (!validarVideo(archivoVideo)) {
            throw new Error('Video no válido');
        }

        const formData = new FormData();
        formData.append('accion', 'subirVideoContenido');
        formData.append('video', archivoVideo);
        formData.append('idContenido', idContenido);
        formData.append('idCurso', idCurso);
        formData.append('idSeccion', idSeccion);

        mostrarCargando('Subiendo video...');

        const response = await fetch('/factuonlinetraining/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            mostrarNotificacion('Video subido exitosamente', 'success');
            await recargarContenidoSecciones();
            return data;
        } else {
            throw new Error(data.mensaje || 'Error al subir video');
        }

    } catch (error) {
        console.error('Error al subir video:', error);
        mostrarNotificacion(error.message, 'error');
        throw error;
    } finally {
        ocultarCargando();
    }
}

/**
 * Eliminar asset con confirmación
 * @param {number} idAsset - ID del asset a eliminar
 * @param {number} idContenido - ID del contenido para actualizar duración
 * @param {string} nombreAsset - Nombre descriptivo del asset
 * @returns {Promise} - Promesa con el resultado
 */
async function eliminarAssetConConfirmacion(idAsset, idContenido, nombreAsset) {
    try {
        // Confirmar eliminación
        const confirmacion = await Swal.fire({
            title: '¿Estás seguro?',
            text: `Se eliminará el archivo "${nombreAsset}" permanentemente`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmacion.isConfirmed) {
            return;
        }

        mostrarCargando('Eliminando archivo...');

        const response = await fetch('/factuonlinetraining/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                accion: 'eliminarAsset',
                idAsset: idAsset,
                idContenido: idContenido
            })
        });

        const data = await response.json();

        if (data.success) {
            mostrarNotificacion('Archivo eliminado exitosamente', 'success');
            await recargarContenidoSecciones();
            return data;
        } else {
            throw new Error(data.mensaje || 'Error al eliminar archivo');
        }

    } catch (error) {
        console.error('Error al eliminar asset:', error);
        mostrarNotificacion(error.message, 'error');
        throw error;
    } finally {
        ocultarCargando();
    }
}

/**
 * Actualizar video promocional
 * @param {number} idCurso - ID del curso
 * @param {File} nuevoVideo - Nuevo video promocional
 * @returns {Promise} - Promesa con el resultado
 */
async function actualizarVideoPromocional(idCurso, nuevoVideo) {
    try {
        if (!validarVideo(nuevoVideo)) {
            throw new Error('Video promocional no válido');
        }

        const formData = new FormData();
        formData.append('accion', 'subirVideoPromocional');
        formData.append('video', nuevoVideo);
        formData.append('idCurso', idCurso);

        mostrarCargando('Actualizando video promocional...');

        const response = await fetch('/factuonlinetraining/App/ajax/curso_secciones.ajax.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            mostrarNotificacion('Video promocional actualizado', 'success');

            // Actualizar el video container
            const videoContainer = document.getElementById('video-container');
            if (videoContainer) {
                videoContainer.dataset.promoVideo = `/factuonlinetraining/${data.ruta}`;
                renderizarVideoPromo(`/factuonlinetraining/${data.ruta}`);
            }

            return data;
        } else {
            throw new Error(data.mensaje || 'Error al actualizar video promocional');
        }

    } catch (error) {
        console.error('Error al actualizar video promocional:', error);
        mostrarNotificacion(error.message, 'error');
        throw error;
    } finally {
        ocultarCargando();
    }
}

/**
 * Validar archivo según tipo
 * @param {File} archivo - Archivo a validar
 * @param {string} tipo - Tipo esperado ('video' o 'pdf')
 * @returns {boolean} - True si es válido
 */
function validarArchivo(archivo, tipo) {
    if (!archivo || archivo.size === 0) {
        return false;
    }

    if (tipo === 'video') {
        return validarVideo(archivo);
    } else if (tipo === 'pdf') {
        return validarPDF(archivo);
    }

    return false;
}

/**
 * Mostrar indicador de carga
 * @param {string} mensaje - Mensaje a mostrar
 */
function mostrarCargando(mensaje = 'Cargando...') {
    // Implementar según el framework UI que uses
    if (window.Swal) {
        Swal.fire({
            title: mensaje,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
}

/**
 * Ocultar indicador de carga
 */
function ocultarCargando() {
    if (window.Swal) {
        Swal.close();
    }
}

/**
 * Ejemplos de uso en el frontend
 */

// Reemplazar video de contenido
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('video-replacement-input')) {
        const idAsset = e.target.dataset.assetId;
        const archivo = e.target.files[0];

        if (archivo) {
            reemplazarAsset(parseInt(idAsset), archivo, 'video');
        }
    }
});

// Reemplazar PDF de contenido
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('pdf-replacement-input')) {
        const idAsset = e.target.dataset.assetId;
        const archivo = e.target.files[0];

        if (archivo) {
            reemplazarAsset(parseInt(idAsset), archivo, 'pdf');
        }
    }
});

// Botón para eliminar asset
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-eliminar-asset')) {
        const idAsset = parseInt(e.target.dataset.assetId);
        const idContenido = parseInt(e.target.dataset.contenidoId);
        const nombreAsset = e.target.dataset.nombreAsset;

        eliminarAssetConConfirmacion(idAsset, idContenido, nombreAsset);
    }
});

// Exportar funciones para uso global
window.AssetManager = {
    reemplazarAsset,
    subirVideoContenido,
    eliminarAssetConConfirmacion,
    actualizarVideoPromocional
};
