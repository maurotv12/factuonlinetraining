/**
 * JavaScript para gestión de inscripciones y preinscripciones
 * Funcionalidad para manejar botones de inscripción/preinscripción en vista de cursos
 */

document.addEventListener('DOMContentLoaded', function () {
    // Inicializar funcionalidad de inscripciones si estamos en la vista del curso
    if (typeof window.cursoData !== 'undefined') {
        inicializarBotonesInscripcion();
    }
});

/**
 * Inicializar la funcionalidad de los botones de inscripción/preinscripción
 */
function inicializarBotonesInscripcion() {
    const btnInscribirse = document.getElementById('btn-inscribirse');
    const btnPreinscribirse = document.getElementById('btn-preinscribirse');

    if (!btnInscribirse || !btnPreinscribirse) {
        console.log('Botones de inscripción no encontrados');
        return;
    }

    // Verificar si el usuario es el instructor del curso
    if (window.cursoData.id_persona == window.cursoData.usuario_actual_id) {
        console.log('El usuario es el instructor del curso, no mostrar opciones de inscripción');
        return;
    }

    // Verificar estado inicial al cargar la página
    verificarEstadoInscripcion();

    // Agregar event listeners a los botones
    btnInscribirse.addEventListener('click', manejarInscripcion);
    btnPreinscribirse.addEventListener('click', manejarPreinscripcion);
}

/**
 * Verificar el estado actual de inscripción/preinscripción del usuario
 */
async function verificarEstadoInscripcion() {
    if (!window.cursoData || !window.cursoData.id) {
        console.error('Datos del curso no disponibles');
        return;
    }

    try {
        // Verificar preinscripción
        const estadoPreinscripcion = await verificarPreinscripcion(window.cursoData.id);

        // Verificar inscripción
        const estadoInscripcion = await verificarInscripcion(window.cursoData.id);

        // Actualizar botones según el estado
        actualizarBotonesSegunEstado(estadoInscripcion, estadoPreinscripcion);

    } catch (error) {
        console.error('Error al verificar estado de inscripción:', error);
        mostrarMensaje('Error al verificar el estado de inscripción', 'error');
    }
}

/**
 * Verificar si el usuario tiene una preinscripción activa
 */
async function verificarPreinscripcion(idCurso) {
    try {
        const response = await fetch('/cursosApp/App/ajax/inscripciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                accion: 'verificarPreinscripcion',
                idCurso: idCurso
            })
        });

        const data = await response.json();
        return data.success ? data.preinscripcion : false;
    } catch (error) {
        console.error('Error al verificar preinscripción:', error);
        return false;
    }
}

/**
 * Verificar si el usuario está inscrito en el curso
 */
async function verificarInscripcion(idCurso) {
    try {
        const response = await fetch('/cursosApp/App/ajax/inscripciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                accion: 'verificarInscripcion',
                idCurso: idCurso
            })
        });

        const data = await response.json();
        return data.success ? data.inscripcion : false;
    } catch (error) {
        console.error('Error al verificar inscripción:', error);
        return false;
    }
}

/**
 * Actualizar el estado y texto de los botones según el estado del usuario
 */
function actualizarBotonesSegunEstado(inscripcion, preinscripcion) {
    const btnInscribirse = document.getElementById('btn-inscribirse');
    const btnPreinscribirse = document.getElementById('btn-preinscribirse');

    if (!btnInscribirse || !btnPreinscribirse) return;

    // Reset de clases
    btnInscribirse.className = 'btn';
    btnPreinscribirse.className = 'btn';

    if (inscripcion) {
        // Usuario ya está inscrito
        btnInscribirse.textContent = 'Inscrito';
        btnInscribirse.className += ' btn-success';
        btnInscribirse.disabled = true;

        btnPreinscribirse.style.display = 'none';

    } else if (preinscripcion && preinscripcion.estado === 'preinscrito') {
        // Usuario está preinscrito
        btnPreinscribirse.textContent = 'Preinscrito';
        btnPreinscribirse.className += ' btn-info';
        btnPreinscribirse.disabled = true;

        // Mostrar botón para cancelar preinscripción
        const btnCancelar = document.createElement('button');
        btnCancelar.className = 'btn btn-outline-danger btn-sm ms-2';
        btnCancelar.textContent = 'Cancelar';
        btnCancelar.onclick = () => cancelarPreinscripcion(preinscripcion.id);
        btnPreinscribirse.parentNode.appendChild(btnCancelar);

        btnInscribirse.style.display = 'none';

    } else {
        // Usuario no está inscrito ni preinscrito
        btnInscribirse.textContent = 'Inscribirse';
        btnInscribirse.className += ' btn-primary';
        btnInscribirse.disabled = false;

        btnPreinscribirse.textContent = 'Preinscribirse';
        btnPreinscribirse.className += ' btn-secondary';
        btnPreinscribirse.disabled = false;
    }
}

/**
 * Manejar click en el botón de inscripción
 */
async function manejarInscripcion() {
    if (!window.cursoData || !window.cursoData.id) {
        mostrarMensaje('Error: Datos del curso no disponibles', 'error');
        return;
    }

    // Confirmar acción
    const confirmacion = await Swal.fire({
        title: '¿Confirmar inscripción?',
        text: `¿Estás seguro de que quieres inscribirte al curso "${window.cursoData.nombre}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, inscribirme',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const response = await fetch('/cursosApp/App/ajax/inscripciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                accion: 'autoInscripcion',
                idCurso: window.cursoData.id
            })
        });

        const data = await response.json();

        if (data.success) {
            mostrarMensaje('¡Te has inscrito exitosamente al curso!', 'success');
            // Recargar estado de botones
            setTimeout(() => verificarEstadoInscripcion(), 1000);
        } else {
            mostrarMensaje(data.mensaje || 'Error al procesar la inscripción', 'error');
        }

    } catch (error) {
        console.error('Error en inscripción:', error);
        mostrarMensaje('Error de conexión al procesar la inscripción', 'error');
    }
}

/**
 * Manejar click en el botón de preinscripción
 */
async function manejarPreinscripcion() {
    if (!window.cursoData || !window.cursoData.id) {
        mostrarMensaje('Error: Datos del curso no disponibles', 'error');
        return;
    }

    // Confirmar acción
    const confirmacion = await Swal.fire({
        title: '¿Confirmar preinscripción?',
        text: `¿Estás seguro de que quieres preinscribirte al curso "${window.cursoData.nombre}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, preinscribirme',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const response = await fetch('/cursosApp/App/ajax/inscripciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                accion: 'crearPreinscripcion',
                idCurso: window.cursoData.id
            })
        });

        const data = await response.json();

        if (data.success) {
            mostrarMensaje('¡Te has preinscrito exitosamente al curso!', 'success');
            // Recargar estado de botones
            setTimeout(() => verificarEstadoInscripcion(), 1000);
        } else {
            mostrarMensaje(data.mensaje || 'Error al procesar la preinscripción', 'error');
        }

    } catch (error) {
        console.error('Error en preinscripción:', error);
        mostrarMensaje('Error de conexión al procesar la preinscripción', 'error');
    }
}

/**
 * Cancelar una preinscripción
 */
async function cancelarPreinscripcion(idPreinscripcion) {
    // Confirmar cancelación
    const confirmacion = await Swal.fire({
        title: '¿Cancelar preinscripción?',
        text: '¿Estás seguro de que quieres cancelar tu preinscripción?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No'
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const response = await fetch('/cursosApp/App/ajax/inscripciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                accion: 'cancelarPreinscripcion',
                idPreinscripcion: idPreinscripcion
            })
        });

        const data = await response.json();

        if (data.success) {
            mostrarMensaje('Preinscripción cancelada exitosamente', 'success');
            // Recargar estado de botones
            setTimeout(() => verificarEstadoInscripcion(), 1000);
        } else {
            mostrarMensaje(data.mensaje || 'Error al cancelar la preinscripción', 'error');
        }

    } catch (error) {
        console.error('Error al cancelar preinscripción:', error);
        mostrarMensaje('Error de conexión al cancelar la preinscripción', 'error');
    }
}

/**
 * Mostrar mensaje al usuario
 */
function mostrarMensaje(mensaje, tipo = 'info') {
    // Usar SweetAlert2 si está disponible
    if (typeof Swal !== 'undefined') {
        const iconos = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };

        Swal.fire({
            icon: iconos[tipo] || 'info',
            title: tipo === 'error' ? 'Error' : tipo === 'success' ? '¡Éxito!' : 'Información',
            text: mensaje,
            confirmButtonText: 'Aceptar'
        });
    } else {
        // Fallback con alert nativo
        alert(mensaje);
    }
}
