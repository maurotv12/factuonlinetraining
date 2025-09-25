/**
 * JavaScript para crear curso - Vista Profesor
 * Funcionalidades específicas para la creación de cursos por profesores
 */

document.addEventListener('DOMContentLoaded', function () {
    // Elementos del formulario
    const form = document.getElementById('form-crear-curso-profesor');
    const nombreInput = document.getElementById('nombre');
    const descripcionInput = document.getElementById('descripcion');
    const aprendizajesTextarea = document.getElementById('lo_que_aprenderas');
    const requisitosTextarea = document.getElementById('requisitos');
    const paraQuienTextarea = document.getElementById('para_quien');
    const imagenInput = document.getElementById('imagen');
    const videoInput = document.getElementById('video_promocional');
    const btnCrear = document.getElementById('btn-crear-curso');
    const progressBar = document.getElementById('progress-bar');

    // Contadores de caracteres
    const contadorNombre = document.getElementById('contador-nombre');
    const contadorDescripcion = document.getElementById('contador-descripcion');

    // Elementos de mensajes
    const mensajeNombre = document.getElementById('mensaje-nombre');
    const vistaImagen = document.getElementById('vista-previa-imagen');
    const infoVideo = document.getElementById('info-video');
    const errorImagen = document.getElementById('error-imagen');

    // Inicializar funcionalidades
    inicializarContadores();
    inicializarValidaciones();
    inicializarVistaPreviaArchivos();
    inicializarBarraProgreso();
    inicializarDragAndDrop();

    /**
     * Inicializar contadores de caracteres
     */
    function inicializarContadores() {
        // Contador para nombre del curso
        nombreInput.addEventListener('input', function () {
            const longitud = this.value.length;
            contadorNombre.textContent = longitud;

            if (longitud > 100) {
                contadorNombre.style.color = '#dc3545';
                this.classList.add('is-invalid');
            } else {
                contadorNombre.style.color = '#6c757d';
                this.classList.remove('is-invalid');
            }

            actualizarProgreso();
        });

        // Contador para descripción
        descripcionInput.addEventListener('input', function () {
            const longitud = this.value.length;
            contadorDescripcion.textContent = longitud;

            if (longitud > 2000) {
                contadorDescripcion.style.color = '#dc3545';
                this.classList.add('is-invalid');
            } else {
                contadorDescripcion.style.color = '#6c757d';
                this.classList.remove('is-invalid');
            }

            actualizarProgreso();
        });
    }

    /**
     * Inicializar validaciones en tiempo real
     */
    function inicializarValidaciones() {
        // Validación del nombre del curso con debounce mejorado
        let timeoutNombre;
        nombreInput.addEventListener('input', function () {
            clearTimeout(timeoutNombre);
            const nombre = this.value.trim();

            // Limpiar mensajes previos inmediatamente
            limpiarMensajeErrorNombre(this);

            if (nombre.length >= 3) {
                // Debounce para evitar múltiples peticiones
                timeoutNombre = setTimeout(() => validarNombreUnico(nombre), 800);
            } else if (nombre.length > 0) {
                // Mostrar mensaje si es muy corto
                mostrarErrorNombre(this, 'El nombre debe tener al menos 3 caracteres');
                this.dataset.nombreValido = 'false';
            }

            actualizarProgreso();
        });

        // Validar al salir del campo nombre
        nombreInput.addEventListener('blur', function () {
            const nombre = this.value.trim();
            if (nombre.length >= 3) {
                clearTimeout(timeoutNombre);
                validarNombreUnico(nombre);
            }
        });

        // Validación de campos de viñetas con validación mejorada
        [aprendizajesTextarea, requisitosTextarea, paraQuienTextarea].forEach(textarea => {
            // Validación al escribir
            textarea.addEventListener('input', function () {
                mostrarContadorCaracteresViñetas(this);
                actualizarProgreso();
            });

            // Validación al salir del campo
            textarea.addEventListener('blur', function () {
                validarLineaViñetaCompleta(this);
            });

            // Agregar contador de caracteres visual
            agregarContadorCaracteresViñetas(textarea);
        });
    }

    /**
     * Inicializar vista previa de archivos
     */
    function inicializarVistaPreviaArchivos() {
        // Vista previa de imagen
        imagenInput.addEventListener('change', function () {
            const archivo = this.files[0];
            if (archivo) {
                mostrarVistaPrevia(archivo);
            } else {
                // Limpiar vista previa si no hay archivo
                const contenedor = this.parentNode;
                const vistaPreviaExistente = contenedor.querySelector('.vista-previa-imagen');
                if (vistaPreviaExistente) {
                    vistaPreviaExistente.remove();
                }
            }
            actualizarProgreso();
        });

        // Información de video
        videoInput.addEventListener('change', function () {
            const archivo = this.files[0];

            // Limpiar errores previos
            limpiarErrorVideo();

            if (archivo) {
                // Validar archivo inmediatamente
                const validacion = validarArchivoVideo(archivo);

                if (validacion.esValido) {
                    mostrarInfoVideo(archivo);
                } else {
                    // Mostrar error y limpiar input
                    mostrarErrorVideo(validacion.mensaje);
                    this.classList.add('is-invalid');
                    this.value = ''; // Limpiar input para evitar envío de archivo inválido
                }
            }
            actualizarProgreso();
        });
    }

    /**
     * Inicializar barra de progreso
     */
    function inicializarBarraProgreso() {
        // Agregar listeners a todos los campos para actualizar progreso
        [nombreInput, descripcionInput, aprendizajesTextarea, paraQuienTextarea].forEach(campo => {
            campo.addEventListener('input', actualizarProgreso);
        });

        document.getElementById('id_categoria').addEventListener('change', actualizarProgreso);
        document.getElementById('estado').addEventListener('change', actualizarProgreso);
    }

    /**
     * Validar nombre único mediante AJAX
     */
    function validarNombreUnico(nombre) {
        if (!nombre || nombre.length < 3) return;

        // Limpiar errores previos
        limpiarMensajeErrorNombre(nombreInput);

        // Mostrar indicador de carga
        mostrarIndicadorCargaNombre(nombreInput, true);

        fetch('/factuonlinetraining/App/ajax/validaciones.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `accion=validar_nombre_curso&nombre=${encodeURIComponent(nombre)}`
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
                    mostrarIndicadorCargaNombre(nombreInput, false);

                    if (data.error) {
                        mostrarErrorNombre(nombreInput, data.mensaje);
                        nombreInput.dataset.nombreValido = 'false';
                    } else {
                        mostrarExitoNombre(nombreInput, data.mensaje || 'Nombre disponible');
                        nombreInput.dataset.nombreValido = 'true';
                    }
                } catch (parseError) {
                    mostrarIndicadorCargaNombre(nombreInput, false);
                    mostrarErrorNombre(nombreInput, 'Error de conexión. Intenta nuevamente.');
                    nombreInput.dataset.nombreValido = 'false';
                }
            })
            .catch(error => {
                mostrarIndicadorCargaNombre(nombreInput, false);
                nombreInput.dataset.nombreValido = 'true'; // Asumir válido en caso de error de red
            });
    }

    /**
     * Mostrar indicador de carga para validación de nombre
     */
    function mostrarIndicadorCargaNombre(input, mostrar) {
        let indicador = input.parentNode.querySelector('.validacion-carga');

        if (mostrar) {
            if (!indicador) {
                indicador = document.createElement('span');
                indicador.className = 'validacion-carga';
                indicador.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
                indicador.style.cssText = 'font-size: 0.8rem; color: #6c757d; margin-left: 0.5rem;';
                input.parentNode.appendChild(indicador);
            }
        } else {
            if (indicador) {
                indicador.remove();
            }
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
     * Validar línea de viñeta completamente
     */
    function validarLineaViñetaCompleta(textarea) {
        const lines = textarea.value.split('\n');
        const maxCaracteres = 100;

        // Limpiar mensajes de error previos
        limpiarMensajeErrorViñeta(textarea);

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
            mostrarErrorViñeta(
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
     * Mostrar contador de caracteres para viñetas
     */
    function mostrarContadorCaracteresViñetas(textarea) {
        const lines = textarea.value.split('\n');
        const maxCaracteres = 100;

        // Encontrar la línea actual
        const cursorPos = textarea.selectionStart;
        const textBeforeCursor = textarea.value.substring(0, cursorPos);
        const currentLineIndex = textBeforeCursor.split('\n').length - 1;

        if (lines[currentLineIndex]) {
            const caracteresActuales = lines[currentLineIndex].length;
            const caracteresRestantes = maxCaracteres - caracteresActuales;

            actualizarContadorCaracteresViñetas(textarea, caracteresActuales, caracteresRestantes);
        }
    }

    /**
     * Agregar contador de caracteres para viñetas
     */
    function agregarContadorCaracteresViñetas(textarea) {
        const contador = document.createElement('div');
        contador.className = 'contador-caracteres-viñetas';
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
     * Actualizar contador de caracteres para viñetas
     */
    function actualizarContadorCaracteresViñetas(textarea, actuales, restantes) {
        if (textarea.contadorElemento) {
            const color = restantes < 10 ? '#dc3545' : '#6c757d';
            textarea.contadorElemento.innerHTML = `Línea actual: ${actuales}/100 caracteres`;
            textarea.contadorElemento.style.color = color;
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
     * Mostrar error de viñeta
     */
    function mostrarErrorViñeta(textarea, mensaje) {
        limpiarMensajeErrorViñeta(textarea);

        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-viñeta text-danger';
        errorDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${mensaje}`;

        textarea.parentNode.appendChild(errorDiv);
        textarea.errorElemento = errorDiv;
        textarea.classList.add('is-invalid');
    }

    /**
     * Limpiar mensaje de error de viñeta
     */
    function limpiarMensajeErrorViñeta(textarea) {
        if (textarea.errorElemento) {
            textarea.errorElemento.remove();
            textarea.errorElemento = null;
        }
        textarea.classList.remove('is-invalid');
    }

    /**
     * Validar imagen
     */
    function validarImagen(archivo) {
        const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png'];
        const tamañoMaximo = 5 * 1024 * 1024; // 5MB

        // Limpiar errores previos
        const errorExistente = imagenInput.parentNode.querySelector('.error-imagen');
        if (errorExistente) {
            errorExistente.remove();
        }

        if (!tiposPermitidos.includes(archivo.type)) {
            mostrarErrorImagen('Formato de imagen no válido. Use JPG, JPEG o PNG.');
            imagenInput.classList.add('is-invalid');
            return false;
        }

        if (archivo.size > tamañoMaximo) {
            mostrarErrorImagen('La imagen es muy grande. Tamaño máximo: 5MB.');
            imagenInput.classList.add('is-invalid');
            return false;
        }

        imagenInput.classList.remove('is-invalid');
        return true;
    }

    /**
     * Mostrar error de imagen
     */
    function mostrarErrorImagen(mensaje) {
        const imagen = imagenInput;
        if (!imagen) return;

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
     * Mostrar vista previa de imagen
     */
    function mostrarVistaPrevia(archivo) {
        const contenedor = imagenInput.parentNode;

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
                img.style.cssText = 'max-width: 200px; max-height: 150px; margin-top: 0.5rem; border: 1px solid #dee2e6; border-radius: 0.375rem; object-fit: cover;';

                contenedor.appendChild(img);
            };
            reader.readAsDataURL(archivo);

            imagenInput.classList.remove('is-invalid');
        } else if (archivo) {
            mostrarErrorImagen('Tipo de archivo no válido. Use JPG, JPEG o PNG.');
            imagenInput.classList.add('is-invalid');
        }
    }

    /**
     * Mostrar información del video
     */
    function mostrarInfoVideo(archivo) {
        const tamañoMB = (archivo.size / (1024 * 1024)).toFixed(2);
        const tipoVideo = archivo.type;

        const contenedor = videoInput.parentNode;

        // Limpiar información existente
        const infoExistente = contenedor.querySelector('.info-video');
        if (infoExistente) {
            infoExistente.remove();
        }

        const infoDiv = document.createElement('div');
        infoDiv.className = 'info-video';
        infoDiv.style.cssText = 'margin-top: 0.5rem; font-size: 0.9rem; color: #6c757d;';
        infoDiv.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-video"></i>
                <span><strong>Archivo:</strong> ${archivo.name}</span>
                <span><strong>Tamaño:</strong> ${tamañoMB} MB</span>
                <span><strong>Tipo:</strong> ${tipoVideo}</span>
            </div>
        `;

        contenedor.appendChild(infoDiv);
    }

    /**
     * Actualizar barra de progreso
     */
    function actualizarProgreso() {
        const campos = [
            nombreInput.value.trim(),
            descripcionInput.value.trim(),
            aprendizajesTextarea.value.trim(),
            paraQuienTextarea.value.trim(),
            document.getElementById('id_categoria').value,
            imagenInput.files.length > 0
        ];

        const completados = campos.filter(campo => campo).length;
        const porcentaje = (completados / campos.length) * 100;

        progressBar.style.width = `${porcentaje}%`;
    }

    /**
     * Inicializar drag and drop para archivos
     */
    function inicializarDragAndDrop() {
        const dropZones = [
            { input: imagenInput, zone: imagenInput.parentElement },
            { input: videoInput, zone: videoInput.parentElement }
        ];

        dropZones.forEach(({ input, zone }) => {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                zone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                zone.addEventListener(eventName, () => zone.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                zone.addEventListener(eventName, () => zone.classList.remove('dragover'), false);
            });

            zone.addEventListener('drop', function (e) {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });
    }

    /**
     * Validar formulario antes del envío
     */
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Validar campos obligatorios
        if (!validarFormulario()) {
            Swal.fire({
                icon: 'error',
                title: 'Formulario incompleto',
                text: 'Por favor completa todos los campos obligatorios.',
                confirmButtonText: 'Revisar'
            });
            return;
        }

        // Mostrar confirmación antes de crear
        Swal.fire({
            title: '¿Crear curso?',
            text: 'Se creará un nuevo curso con la información proporcionada.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, crear curso',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarFormulario();
            }
        });
    });

    /**
     * Enviar formulario
     */
    function enviarFormulario() {
        // Deshabilitar botón durante el envío
        btnCrear.disabled = true;
        btnCrear.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando curso...';

        // Enviar formulario directamente (no usar fetch para manejo de archivos)
        form.submit();
    }

    /**
     * Validar todo el formulario
     */
    function validarFormulario() {
        let esValido = true;
        let primerCampoConError = null;

        // Validar nombre único
        const nombreValido = nombreInput.dataset.nombreValido;
        if (nombreValido === 'false' || !nombreInput.value.trim()) {
            mostrarErrorNombre(nombreInput, 'Debes ingresar un nombre válido y único para el curso');
            esValido = false;
            if (!primerCampoConError) primerCampoConError = nombreInput;
        }

        // Validar descripción
        if (descripcionInput.value.trim().length < 50) {
            descripcionInput.classList.add('is-invalid');
            esValido = false;
            if (!primerCampoConError) primerCampoConError = descripcionInput;
        }

        // Validar campos de viñetas
        const camposViñetas = [aprendizajesTextarea, paraQuienTextarea]; // requisitos es opcional
        camposViñetas.forEach(textarea => {
            const valor = textarea.value.trim();

            if (!valor) {
                textarea.classList.add('is-invalid');
                esValido = false;
                if (!primerCampoConError) primerCampoConError = textarea;
            } else if (!validarLineaViñetaCompleta(textarea)) {
                esValido = false;
                if (!primerCampoConError) primerCampoConError = textarea;
            }
        });

        // Validar requisitos si tiene contenido
        if (requisitosTextarea.value.trim() && !validarLineaViñetaCompleta(requisitosTextarea)) {
            esValido = false;
            if (!primerCampoConError) primerCampoConError = requisitosTextarea;
        }

        // Validar categoría
        const categoria = document.getElementById('id_categoria');
        if (!categoria.value) {
            categoria.classList.add('is-invalid');
            esValido = false;
            if (!primerCampoConError) primerCampoConError = categoria;
        }

        // Validar imagen
        if (imagenInput.files.length === 0) {
            imagenInput.classList.add('is-invalid');
            mostrarErrorImagen('Debes subir una imagen para el banner del curso.');
            esValido = false;
            if (!primerCampoConError) primerCampoConError = imagenInput;
        } else {
            const archivo = imagenInput.files[0];
            if (!validarTipoImagen(archivo)) {
                mostrarErrorImagen('Tipo de archivo no válido. Use JPG, JPEG o PNG.');
                imagenInput.classList.add('is-invalid');
                esValido = false;
                if (!primerCampoConError) primerCampoConError = imagenInput;
            }
        }

        // Validar video promocional (opcional pero si se sube debe ser válido)
        if (videoInput.files.length > 0) {
            const archivoVideo = videoInput.files[0];
            const validacionVideo = validarArchivoVideo(archivoVideo);

            if (!validacionVideo.esValido) {
                videoInput.classList.add('is-invalid');
                mostrarErrorVideo(validacionVideo.mensaje);
                esValido = false;
                if (!primerCampoConError) primerCampoConError = videoInput;
            }
        }

        // Si hay errores, enfocar primer campo con error
        if (!esValido && primerCampoConError) {
            primerCampoConError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => primerCampoConError.focus(), 300);
        }

        return esValido;
    }

    /**
     * Validar tipo de archivo de imagen
     */
    function validarTipoImagen(archivo) {
        const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png'];
        return tiposPermitidos.includes(archivo.type);
    }

    /**
     * Validar archivo de video promocional
     */
    function validarArchivoVideo(archivo) {
        // Tipos de video permitidos
        const tiposPermitidos = ['video/mp4'];

        // Validar tipo
        if (!tiposPermitidos.includes(archivo.type)) {
            return {
                esValido: false,
                mensaje: 'Tipo de video no válido. Use MP4.'
            };
        }

        // Validar tamaño (15MB = 15 * 1024 * 1024 bytes)
        const maxTamaño = 15 * 1024 * 1024; // 15MB
        if (archivo.size > maxTamaño) {
            const tamañoMB = Math.round(archivo.size / 1024 / 1024);
            return {
                esValido: false,
                mensaje: `El video es demasiado grande (${tamañoMB}MB). El límite es 15MB.`
            };
        }

        return {
            esValido: true,
            mensaje: 'Video válido'
        };
    }

    /**
     * Mostrar error de video
     */
    function mostrarErrorVideo(mensaje) {
        // Buscar o crear elemento de error para video
        let errorVideo = document.getElementById('error-video');

        if (!errorVideo) {
            errorVideo = document.createElement('div');
            errorVideo.id = 'error-video';
            errorVideo.className = 'invalid-feedback d-block';
            videoInput.parentNode.appendChild(errorVideo);
        }

        errorVideo.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${mensaje}`;
        errorVideo.style.display = 'block';
    }

    /**
     * Limpiar error de video
     */
    function limpiarErrorVideo() {
        const errorVideo = document.getElementById('error-video');
        if (errorVideo) {
            errorVideo.style.display = 'none';
            errorVideo.innerHTML = '';
        }
        videoInput.classList.remove('is-invalid');
    }

    /**
     * Limpiar errores cuando el usuario empieza a escribir
     */
    [nombreInput, descripcionInput, aprendizajesTextarea, paraQuienTextarea].forEach(campo => {
        campo.addEventListener('input', function () {
            this.classList.remove('is-invalid');
        });
    });

    // Limpiar errores de archivos cuando cambian
    imagenInput.addEventListener('change', function () {
        this.classList.remove('is-invalid');
        const errorImagen = document.getElementById('error-imagen');
        if (errorImagen) {
            errorImagen.style.display = 'none';
        }
    });

    videoInput.addEventListener('change', function () {
        // La validación se hace en el evento principal, aquí solo limpiamos si es necesario
        if (this.files.length === 0) {
            limpiarErrorVideo();
        }
    });

    document.getElementById('id_categoria').addEventListener('change', function () {
        this.classList.remove('is-invalid');
        actualizarProgreso();
    });

    // Actualizar progreso inicial
    actualizarProgreso();
});

/**
 * Función para resetear el formulario completamente
 */
function resetearFormulario() {
    const form = document.getElementById('form-crear-curso-profesor');
    if (form) {
        form.reset();

        // Limpiar elementos específicos
        const elementos = [
            'vista-previa-imagen',
            'info-video',
            'contador-nombre',
            'contador-descripcion'
        ];

        elementos.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                if (id.includes('contador')) {
                    elemento.textContent = '0';
                } else {
                    elemento.innerHTML = '';
                }
            }
        });

        // Resetear barra de progreso
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            progressBar.style.width = '0%';
        }

        // Limpiar clases de validación
        document.querySelectorAll('.is-invalid, .is-valid').forEach(elemento => {
            elemento.classList.remove('is-invalid', 'is-valid');
        });

        // Limpiar mensajes
        document.querySelectorAll('[id^="error-"], [id^="mensaje-"]').forEach(elemento => {
            elemento.innerHTML = '';
            elemento.style.display = 'none';
        });
    }
}
