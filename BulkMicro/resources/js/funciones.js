document.addEventListener('DOMContentLoaded', function() {
    const stepButtons = document.querySelectorAll('.step-btn'); // Botones para cambiar de paso
    const dynamicContainer = document.getElementById('dynamic-personalization'); // Contenedor dinámico
    const tableBody = document.querySelector('.table tbody'); // Cuerpo de la tabla para historial

    // --- Función para manejar vistas dinámicas ---
    function loadDynamicView(step) {
        // Mostrar y ocultar las secciones dinámicas basadas en el paso
        document.querySelectorAll('.step-panel').forEach((panel) => {
            if (panel.id === `step-${step}`) {
                panel.style.display = 'block'; // Mostrar la sección seleccionada
            } else {
                panel.style.display = 'none'; // Ocultar las demás secciones
            }
        });

        // Cargar contenido dinámico en el contenedor personalizado
        let componentName;
        switch (step) {
            case '1':
                componentName = 'PerAso'; // Personalización Asociados
                break;
            case '2':
                componentName = 'PerCap'; // Personalización Captaciones
                break;
            case '3':
                componentName = 'PerColo'; // Personalización Colocaciones
                break;
            default:
                console.error('Paso no válido seleccionado');
                return;
        }

        fetch(`/loadPersonalization/${componentName}`)
            .then((response) => {
                if (!response.ok) throw new Error('Error al cargar el componente.');
                return response.text();
            })
            .then((html) => {
                dynamicContainer.innerHTML = html; // Insertar contenido dinámico
                initializePersonalization(componentName); // Inicializar personalización
            })
            .catch((error) => {
                console.error('Error al cargar la personalización:', error);
                dynamicContainer.innerHTML = '<p>Error al cargar el contenido dinámico.</p>';
            });
    }

    // --- Función para cargar los datos de las tablas ---
    function loadCargas(step) {
        fetch(`/cargas/${step}`)
            .then((response) => {
                if (!response.ok) throw new Error('Error al recuperar los datos');
                return response.json();
            })
            .then((data) => {
                tableBody.innerHTML = ''; // Limpiar la tabla
                if (data.length > 0) {
                    data.forEach((carga) => {
                        const row = `
                    <tr>
                        <td>${carga.nombre_archivo}</td>
                        <td>${carga.metadatos || '<em>Sin metadatos</em>'}</td>
                        <td><span class="text-truncate d-inline-block" style="max-width: 250px;">${carga.ruta_archivo}</span></td>
                        <td>${new Date(carga.created_at).toLocaleString()}</td>
                    </tr>
                `;
                        tableBody.innerHTML += row;
                    });
                } else {
                    tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">No se encontraron archivos cargados para este paso.</td>
                </tr>`;
                }
            })
            .catch((error) => {
                console.error('Error al cargar los datos:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al recuperar los datos.',
                });
            });
    }

    function initializePersonalization(tipo) {
        // Convertir el tipo a minúsculas para hacer coincidir con los IDs
        const normalizedTipo = tipo.toLowerCase();
        const saveButton = document.querySelector(`#timer-${normalizedTipo}`);

        if (!saveButton) {
            console.warn(`No se encontró el botón: #timer-${normalizedTipo}`);
            return;
        }

        console.log(`Botón encontrado: #timer-${normalizedTipo}`);

        // Fetch para obtener datos de personalización
        fetch(`/personalization/${tipo}`)
            .then((response) => {
                if (!response.ok) throw new Error('No se encontró configuración');
                return response.json();
            })
            .then((data) => {
                const intervalInput = document.getElementById(`interval-${normalizedTipo}`);
                const emailCheckbox = document.getElementById(`email-notifications-${normalizedTipo}`);

                if (!intervalInput || !emailCheckbox) {
                    console.warn(`Campos de configuración no encontrados para: ${tipo}`);
                    return;
                }

                // Asignar valores obtenidos
                intervalInput.value = data.intervalo_horas;
                emailCheckbox.checked = data.notificaciones_email;
                saveButton.innerText = 'Editar Personalización';
            })
            .catch((error) => {
                console.warn(`Error obteniendo configuración para ${tipo}:`, error.message);
                saveButton.innerText = 'Guardar Personalización';
            });

        // Asociar el evento de clic
        saveButton.addEventListener('click', function() {
            const intervalInput = document.getElementById(`interval-${normalizedTipo}`);
            const emailCheckbox = document.getElementById(`email-notifications-${normalizedTipo}`);

            if (!intervalInput || !emailCheckbox) {
                console.warn(`No se encontraron campos para guardar personalización de ${tipo}`);
                return;
            }

            const intervalo = intervalInput.value;
            const emailNotifications = emailCheckbox.checked;

            console.log(`Guardando personalización para: ${tipo}`);
            console.log(`Intervalo: ${intervalo}, Notificaciones: ${emailNotifications}`);

            // Enviar datos al backend
            fetch(`/personalization/${tipo}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content,
                    },
                    body: JSON.stringify({
                        intervalo_horas: intervalo,
                        notificaciones_email: emailNotifications ? 1 : 0,
                    }),
                })
                .then((response) => {
                    if (!response.ok) throw new Error('Error al guardar configuración');
                    return response.json();
                })
                .then((data) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: data.message,
                    });
                    saveButton.innerText = 'Editar Personalización';
                })
                .catch((error) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message,
                    });
                });
        });
    }

    // Ejecutar después de cargar el DOM
    document.addEventListener('DOMContentLoaded', function() {
        const tipos = ['Asociados', 'Captaciones', 'Colocaciones'];
        const dynamicContainer = document.getElementById('dynamic-personalization');

        /**
         * Cargar la vista dinámica según el tipo seleccionado
         */
        function loadPersonalizationView(tipo) {
            fetch(`/personalization/view/${tipo}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(
                            `Error al cargar la vista para ${tipo}: ${response.statusText}`);
                    }
                    return response.text();
                })
                .then((html) => {
                    dynamicContainer.innerHTML = html; // Inserta la vista en el contenedor
                    attachSaveEvent(
                    tipo); // Asocia el evento de guardar al botón de la vista cargada
                })
                .catch((error) => {
                    console.error(error.message);
                    dynamicContainer.innerHTML =
                        `<p class="text-danger">Error al cargar la vista de ${tipo}.</p>`;
                });
        }

        /**
         * Asociar el evento de guardar al botón de la vista cargada
         */
        function attachSaveEvent(tipo) {
            const saveButton = document.querySelector(`#timer-${tipo.toLowerCase()}`);
            if (!saveButton) {
                console.warn(`No se encontró el botón de guardar para ${tipo}`);
                return;
            }

            saveButton.addEventListener('click', function() {
                const intervalo = document.querySelector(`#interval-${tipo.toLowerCase()}`)
                    .value;
                const emailNotifications = document.querySelector(
                    `#email-notifications-${tipo.toLowerCase()}`
                ).checked;

                console.log(`Guardando personalización para ${tipo}`);
                console.log(
                    `Intervalo: ${intervalo}, Notificaciones: ${emailNotifications}`);

                // Puedes enviar los datos al backend aquí si fuera necesario.
                // Esto solo es un ejemplo para mostrar el evento funcionando correctamente.
                Swal.fire({
                    icon: 'success',
                    title: `Datos de ${tipo}`,
                    text: `Intervalo: ${intervalo}, Notificaciones: ${emailNotifications}`,
                });
            });
        }

        /**
         * Inicializar los eventos para cambiar vistas
         */
        tipos.forEach((tipo) => {
            const button = document.querySelector(`[data-tipo="${tipo}"]`);
            if (button) {
                button.addEventListener('click', function() {
                    loadPersonalizationView(tipo); // Carga la vista dinámica
                });
            }
        });

        // Cargar la vista inicial por defecto (Asociados)
        loadPersonalizationView('Asociados');
    });



    // --- Inicializar botones de pasos ---
    stepButtons.forEach((button) => {
        button.addEventListener('click', function() {
            const step = this.getAttribute('data-step');
            loadDynamicView(step); // Cambiar vista dinámica
            loadCargas(step); // Cargar datos de tabla
            stepButtons.forEach((btn) => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-secondary');
            });
            this.classList.add('btn-primary'); // Marcar el botón seleccionado
        });
    });

    // --- Carga inicial ---
    loadDynamicView('1'); // Vista inicial
    loadCargas(1); // Datos de tabla inicial
});
