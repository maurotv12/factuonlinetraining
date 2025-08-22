// JavaScript para la página de crear curso
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar validaciones
    inicializarValidacionViñetas();
    inicializarValidacionFormulario();
    inicializarValidacionNombre();
    configurarVistaPrevia();
});

/**
 * Inicializar validación del nombre único del curso
 */
function inicializarValidacionNombre() {
    const inputNombre = document.getElementById('nombre');
    if (inputNombre) {
        let timeoutId;

        // Validar al salir del campo (blur)
        inputNombre.addEventListener('blur', function () {
            const nombre = this.value.trim();
            if (nombre.length >= 3) {
                clearTimeout(timeoutId);
                validarNombreUnico(nombre);
            }
        });

        // Validar mientras escribe con debounce
        inputNombre.addEventListener('input', function () {
            const nombre = this.value.trim();

            // Limpiar mensajes previos inmediatamente
            limpiarMensajeErrorNombre(this);

            if (nombre.length >= 3) {
                // Debounce para evitar múltiples peticiones
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => validarNombreUnico(nombre), 800);
            } else if (nombre.length > 0) {
                // Mostrar mensaje si es muy corto
                mostrarErrorNombre(this, 'El nombre debe tener al menos 3 caracteres');
                this.dataset.nombreValido = 'false';
            }
        });
    }
}

/**
 * Validar que el nombre del curso sea único
 */
function validarNombreUnico(nombre) {
    const inputNombre = document.getElementById('nombre');
    if (!inputNombre) return;

    // Limpiar errores previos
    limpiarMensajeErrorNombre(inputNombre);

    // Mostrar indicador de carga
    mostrarIndicadorCargaNombre(inputNombre, true);

    // Hacer petición AJAX para validar
    fetch('ajax/validaciones.ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'accion=validar_nombre_curso&nombre=' + encodeURIComponent(nombre)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text(); // Usar text() primero para debug
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                mostrarIndicadorCargaNombre(inputNombre, false);

                if (data.error) {
                    mostrarErrorNombre(inputNombre, data.mensaje);
                    inputNombre.dataset.nombreValido = 'false';
                } else {
                    mostrarExitoNombre(inputNombre, data.mensaje || 'Nombre disponible');
                    inputNombre.dataset.nombreValido = 'true';
                }
            } catch (parseError) {
                mostrarIndicadorCargaNombre(inputNombre, false);
                mostrarErrorNombre(inputNombre, 'Error de conexión. Intenta nuevamente.');
                inputNombre.dataset.nombreValido = 'false';
            }
        })
        .catch(error => {
            mostrarIndicadorCargaNombre(inputNombre, false);
            inputNombre.dataset.nombreValido = 'true'; // Asumir válido en caso de error de red
        });
}

/**
 * Mostrar indicador de carga para validación de nombre
 */
function mostrarIndicadorCargaNombre(input, mostrar) {
    let indicador = input.parentNode.querySelector('.validacion-carga');

    if (mostrar) {
        if (!indicador) {
            indicador = document.createElement('div');
            indicador.className = 'validacion-carga text-info';
            indicador.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
            indicador.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando nombre...';
            input.parentNode.appendChild(indicador);
        }
    } else if (indicador) {
        indicador.remove();
    }
}

/**
 * Mostrar error de validación de nombre
 */
function mostrarErrorNombre(input, mensaje) {
    limpiarMensajeErrorNombre(input);

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-nombre text-danger';
    errorDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${mensaje}`;

    input.parentNode.appendChild(errorDiv);
    input.classList.add('is-invalid');
}

/**
 * Mostrar éxito de validación de nombre
 */
function mostrarExitoNombre(input, mensaje) {
    limpiarMensajeErrorNombre(input);

    const exitoDiv = document.createElement('div');
    exitoDiv.className = 'exito-nombre text-success';
    exitoDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
    exitoDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${mensaje}`;

    input.parentNode.appendChild(exitoDiv);
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
}

/**
 * Limpiar mensaje de error del nombre
 */
function limpiarMensajeErrorNombre(input) {
    const contenedor = input.parentNode;
    const errorExistente = contenedor.querySelector('.error-nombre');
    const exitoExistente = contenedor.querySelector('.exito-nombre');

    if (errorExistente) errorExistente.remove();
    if (exitoExistente) exitoExistente.remove();

    input.classList.remove('is-invalid', 'is-valid');
}

/**
 * Configurar validación de campos de viñetas
 */
function inicializarValidacionViñetas() {
    const camposViñetas = ['lo_que_aprenderas', 'requisitos', 'para_quien'];

    camposViñetas.forEach(id => {
        const textarea = document.getElementById(id);
        if (textarea) {
            // Validación en tiempo real
            textarea.addEventListener('blur', validarLineaViñeta);
            textarea.addEventListener('input', mostrarContadorCaracteres);

            // Agregar contador de caracteres
            agregarContadorCaracteres(textarea);
        }
    });
}

/**
 * Validar límite de caracteres por línea en campos de viñetas
 */
function validarLineaViñeta(e) {
    const textarea = e.target;
    const lines = textarea.value.split('\n');
    const maxCaracteres = 100;

    // Limpiar mensajes de error previos
    limpiarMensajeError(textarea);

    // Verificar cada línea
    let lineaProblematica = -1;

    for (let i = 0; i < lines.length; i++) {
        const linea = lines[i].trim();
        if (linea.length > maxCaracteres) {
            lineaProblematica = i;
            break;
        }
    }

    if (lineaProblematica >= 0) {
        mostrarMensajeError(
            textarea,
            `La línea ${lineaProblematica + 1} excede el límite de ${maxCaracteres} caracteres.`
        );

        // Resaltar el área del problema
        resaltarLineaProblematica(textarea, lineaProblematica);
        return false;
    }

    return true;
}

/**
 * Mostrar contador de caracteres para cada línea
 */
function mostrarContadorCaracteres(e) {
    const textarea = e.target;
    const lines = textarea.value.split('\n');
    const maxCaracteres = 70;

    // Encontrar la línea actual
    const cursorPos = textarea.selectionStart;
    const textBeforeCursor = textarea.value.substring(0, cursorPos);
    const currentLineIndex = textBeforeCursor.split('\n').length - 1;

    if (lines[currentLineIndex]) {
        const caracteresActuales = lines[currentLineIndex].length;
        const caracteresRestantes = maxCaracteres - caracteresActuales;

        actualizarContadorCaracteres(textarea, caracteresActuales, caracteresRestantes);
    }
}

/**
 * Agregar contador de caracteres visual
 */
function agregarContadorCaracteres(textarea) {
    const contador = document.createElement('div');
    contador.className = 'contador-caracteres';
    contador.style.cssText = `
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.25rem;
        text-align: right;
    `;

    const contenedor = textarea.parentNode;
    contenedor.appendChild(contador);

    // Almacenar referencia para actualizar
    textarea.contadorElemento = contador;
}

/**
 * Actualizar contador de caracteres
 */
function actualizarContadorCaracteres(textarea, actuales, restantes) {
    if (textarea.contadorElemento) {
        const color = restantes < 10 ? '#dc3545' : restantes < 20 ? '#f7c00bff' : '#6c757d';
        textarea.contadorElemento.innerHTML = `
            <span style="color: ${color};">
                Línea actual: ${actuales}/70 caracteres
                ${restantes < 0 ? `(${Math.abs(restantes)} caracteres de más)` : ''}
            </span>
        `;
    }
}

/**
 * Resaltar línea problemática
 */
function resaltarLineaProblematica(textarea, lineaIndex) {
    const lines = textarea.value.split('\n');
    let startPos = 0;

    // Calcular posición de inicio de la línea problemática
    for (let i = 0; i < lineaIndex; i++) {
        startPos += lines[i].length + 1; // +1 por el \n
    }

    const endPos = startPos + lines[lineaIndex].length;

    // Seleccionar la línea problemática
    textarea.setSelectionRange(startPos, endPos);
    textarea.focus();
}

/**
 * Mostrar mensaje de error
 */
function mostrarMensajeError(textarea, mensaje) {
    limpiarMensajeError(textarea);

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-viñeta text-danger';
    errorDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${mensaje}`;

    textarea.parentNode.appendChild(errorDiv);
    textarea.errorElemento = errorDiv;
}

/**
 * Limpiar mensaje de error
 */
function limpiarMensajeError(textarea) {
    if (textarea.errorElemento) {
        textarea.errorElemento.remove();
        textarea.errorElemento = null;
    }
}

/**
 * Configurar validación del formulario completo
 */
function inicializarValidacionFormulario() {
    const form = document.getElementById('form-crear-curso');

    if (form) {
        form.addEventListener('submit', validarFormularioCompleto);
    }
}

/**
 * Validar formulario completo antes del envío
 */
function validarFormularioCompleto(e) {
    const camposViñetas = ['lo_que_aprenderas', 'requisitos', 'para_quien'];
    let formularioValido = true;
    let primerCampoConError = null;

    // Validar nombre único - ser más flexible
    const inputNombre = document.getElementById('nombre');
    if (inputNombre) {
        const nombre = inputNombre.value.trim();
        const nombreValido = inputNombre.dataset.nombreValido;

        if (nombre.length < 3) {
            mostrarErrorNombre(inputNombre, 'El nombre debe tener al menos 3 caracteres');
            formularioValido = false;
            if (!primerCampoConError) primerCampoConError = inputNombre;
        } else if (nombreValido === 'false') {
            // Solo bloquear si explícitamente sabemos que es inválido
            mostrarErrorNombre(inputNombre, 'Este nombre ya existe. Por favor, elige otro.');
            formularioValido = false;
            if (!primerCampoConError) primerCampoConError = inputNombre;
        } else if (!nombreValido || nombreValido === '') {
            // Si no se ha validado, hacer validación síncrona antes de enviar
            e.preventDefault();
            validarNombreAntesDEnviar(nombre);
            return false;
        }
    }

    // Validar cada campo de viñetas
    camposViñetas.forEach(id => {
        const textarea = document.getElementById(id);
        if (textarea && textarea.value.trim()) {
            const evento = { target: textarea };
            const esValido = validarLineaViñeta(evento);

            if (!esValido && !primerCampoConError) {
                primerCampoConError = textarea;
                formularioValido = false;
            }
        }
    });

    // Validar imagen
    const imagen = document.getElementById('imagen');
    if (imagen && imagen.files.length > 0) {
        const archivo = imagen.files[0];
        if (!validarTipoImagen(archivo)) {
            mostrarErrorImagen('Solo se permiten archivos de imagen (JPG, PNG, GIF, WebP).');
            formularioValido = false;
            if (!primerCampoConError) primerCampoConError = imagen;
        }
    }

    // Si hay errores, prevenir envío y enfocar primer campo con error
    if (!formularioValido) {
        e.preventDefault();

        if (primerCampoConError) {
            primerCampoConError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            primerCampoConError.focus();
        }

        // Mostrar mensaje general
        Swal.fire({
            icon: 'warning',
            title: 'Formulario incompleto',
            text: 'Por favor, corrija los errores antes de continuar.',
            confirmButtonText: 'Entendido'
        });
    }
}

/**
 * Validar tipo de archivo de imagen
 */
function validarTipoImagen(archivo) {
    const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    return tiposPermitidos.includes(archivo.type);
}

/**
 * Mostrar error de imagen
 */
function mostrarErrorImagen(mensaje) {
    const imagen = document.getElementById('imagen');
    const errorExistente = imagen.parentNode.querySelector('.error-imagen');

    if (errorExistente) {
        errorExistente.remove();
    }

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-imagen text-danger';
    errorDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${mensaje}`;

    imagen.parentNode.appendChild(errorDiv);
}

/**
 * Configurar vista previa de archivos
 */
function configurarVistaPrevia() {
    const inputImagen = document.getElementById('imagen');
    const inputVideo = document.getElementById('video');

    if (inputImagen) {
        inputImagen.addEventListener('change', mostrarVistaPreviaImagen);
    }

    if (inputVideo) {
        inputVideo.addEventListener('change', mostrarVistaPreviaVideo);
    }
}

/**
 * Mostrar vista previa de imagen
 */
function mostrarVistaPreviaImagen(e) {
    const archivo = e.target.files[0];
    const contenedor = e.target.parentNode;

    // Limpiar vista previa existente
    const vistaPreviaExistente = contenedor.querySelector('.vista-previa-imagen');
    if (vistaPreviaExistente) {
        vistaPreviaExistente.remove();
    }

    if (archivo && validarTipoImagen(archivo)) {
        const reader = new FileReader();

        reader.onload = function (event) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.className = 'vista-previa-imagen';
            img.style.cssText = `
                max-width: 200px;
                max-height: 150px;
                margin-top: 0.5rem;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                object-fit: cover;
            `;

            contenedor.appendChild(img);
        };

        reader.readAsDataURL(archivo);
    }
}

/**
 * Mostrar información de video
 */
function mostrarVistaPreviaVideo(e) {
    const archivo = e.target.files[0];
    const contenedor = e.target.parentNode;

    // Limpiar información existente
    const infoExistente = contenedor.querySelector('.info-video');
    if (infoExistente) {
        infoExistente.remove();
    }

    if (archivo) {
        const infoDiv = document.createElement('div');
        infoDiv.className = 'info-video';
        infoDiv.style.cssText = 'margin-top: 0.5rem; font-size: 0.9rem; color: #6c757d;';

        const tamaño = (archivo.size / (1024 * 1024)).toFixed(2);
        infoDiv.innerHTML = `
            <i class="fas fa-video"></i> 
            ${archivo.name} (${tamaño} MB)
        `;

        contenedor.appendChild(infoDiv);
    }
}

/**
 * Validar nombre antes de enviar el formulario
 */
function validarNombreAntesDEnviar(nombre) {
    const inputNombre = document.getElementById('nombre');
    const form = document.getElementById('form-crear-curso');

    if (!inputNombre || !form) return;

    console.log('Validando nombre antes de enviar:', nombre);

    mostrarIndicadorCargaNombre(inputNombre, true);

    fetch('ajax/validaciones.ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'accion=validar_nombre_curso&nombre=' + encodeURIComponent(nombre)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                mostrarIndicadorCargaNombre(inputNombre, false);

                if (data.error) {
                    mostrarErrorNombre(inputNombre, data.mensaje);
                    inputNombre.dataset.nombreValido = 'false';

                    Swal.fire({
                        icon: 'error',
                        title: 'Nombre duplicado',
                        text: data.mensaje,
                        confirmButtonText: 'Entendido'
                    });
                } else {
                    mostrarExitoNombre(inputNombre, data.mensaje || 'Nombre disponible');
                    inputNombre.dataset.nombreValido = 'true';

                    // Ahora sí enviar el formulario
                    form.removeEventListener('submit', validarFormularioCompleto);
                    form.submit();
                }
            } catch (parseError) {
                console.error('Error parsing JSON:', parseError);
                mostrarIndicadorCargaNombre(inputNombre, false);

                // En caso de error, permitir envío con confirmación
                Swal.fire({
                    icon: 'warning',
                    title: 'No se pudo validar el nombre',
                    text: 'Hubo un problema de conexión. ¿Deseas continuar?',
                    showCancelButton: true,
                    confirmButtonText: 'Continuar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.removeEventListener('submit', validarFormularioCompleto);
                        form.submit();
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error al validar nombre antes de enviar:', error);
            mostrarIndicadorCargaNombre(inputNombre, false);

            // En caso de error de red, permitir envío
            Swal.fire({
                icon: 'warning',
                title: 'No se pudo validar el nombre',
                text: 'Hubo un problema de conexión. ¿Deseas continuar?',
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.removeEventListener('submit', validarFormularioCompleto);
                    form.submit();
                }
            });
        });
}
