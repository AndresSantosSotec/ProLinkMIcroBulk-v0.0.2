@extends('layouts.app')

@section('content')
    <style>
        @media (max-width: 768px) {
            #personalization-container {
                margin-top: 20px;
            }

            #dynamic-personalization {
                overflow-x: auto;
                /* Permitir desplazamiento horizontal si el contenido es demasiado ancho */
            }
        }

        #dynamic-personalization p {
            word-wrap: break-word;
            /* Ajustar texto largo para evitar desbordes */
        }
    </style>
    <div class="min-vh-100 bg-light">
        {{-- Header --}}
        <header class="bg-primary text-white py-4">
            <div class="container d-flex align-items-center">
                <i class="fas fa-database me-2" style="font-size: 32px;"></i>
                <h1 class="fs-3 fw-bold">Gestión de Bulk de Datos - Microservicio</h1>
            </div>
        </header>

        <main class="container py-4">

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-check-circle text-success"></i> Finalizado</span>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100"
                        aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Left Column --}}
                <div class="col-md-6">
                    {{-- Upload History --}}
                    <div class="mb-4">
                        <h2 class="fs-4 fw-semibold"><i class="fas fa-history me-2"></i>Historial de Cargas</h2>
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">Nombre del Archivo</th>
                                    <th scope="col">Metadatos</th>
                                    <th scope="col">Ruta del Archivo</th>
                                    <th scope="col">Fecha y Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Las filas serán cargadas dinámicamente aquí -->
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-4 text-center">
                        <button class="btn btn-primary mx-2 step-btn" data-step="1" data-tipo="Asociados">Paso 1</button>
                        <button class="btn btn-secondary mx-2 step-btn" data-step="2" data-tipo="Captaciones">Paso
                            2</button>
                        <button class="btn btn-secondary mx-2 step-btn" data-step="3" data-tipo="Colocaciones">Paso
                            3</button>
                    </div>
                    <div id="step-content">
                        {{-- Paso 1: Clientes --}}
                        <div class="step-panel" id="step-1" style="display: block;">
                            @include('components.pasos.asociados')
                        </div>

                        {{-- Paso 2: Captaciones --}}
                        <div class="step-panel" id="step-2" style="display: none;">
                            @include('components.pasos.captaciones')
                        </div>

                        {{-- Paso 3: Colocaciones --}}
                        <div class="step-panel" id="step-3" style="display: none;">
                            @include('components.pasos.colocaciones')
                        </div>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="col-md-6">
                    <div id="personalization-container" class="accordion">
                        <!-- Acordeón de Personalización -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPersonalization">
                                <button class="accordion-button bg-primary text-white" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapsePersonalization" aria-expanded="true"
                                    aria-controls="collapsePersonalization">
                                    Personalización Dinámica
                                </button>
                            </h2>
                            <div id="collapsePersonalization" class="accordion-collapse collapse show"
                                aria-labelledby="headingPersonalization" data-bs-parent="#personalization-container">
                                <div class="accordion-body">
                                    <div id="dynamic-personalization">
                                        <p class="text-muted">Seleccione un tipo para cargar los datos de personalización.
                                        </p>
                                    </div>
                                    <button class="btn btn-success mt-3" id="personalization-save" data-tipo="Asociados">
                                        Guardar Personalización Asociados
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{--SweetAlert Script--}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{--JavaScript para manejar funcionalidad--}}
    {{----}}
    <script>
        //carga de los pasos en las vistas
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

            //funcion que inicializa la personalizacion de codigo js 
            function initializePersonalization() {
                // Botones de pasos
                const stepButtons = document.querySelectorAll('.step-btn');

                stepButtons.forEach((button) => {
                    button.addEventListener('click', function() {
                        const step = button.dataset.step;
                        const tipo = button.dataset.tipo;

                        console.log(`Cambiando a paso ${step}, tipo: ${tipo}`);
                        // Cargar la tarjeta de personalización correspondiente al paso y tipo
                        updatePersonalizationCard(tipo, step);
                    });
                });

                // Inicializar con el primer paso por defecto
                updatePersonalizationCard('Asociados', 1);
            }
            // --- Carga inicial ---
            loadDynamicView('1'); // Vista inicial
            loadCargas(1); // Datos de tabla inicial
        });
    </script>
    {{--Funcion para el cargado de datos de Actualizacion --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stepButtons = document.querySelectorAll('.step-btn'); // Botones de pasos
            const dynamicContainer = document.getElementById('dynamic-personalization'); // Contenedor dinámico
            const saveButton = document.querySelector('#personalization-save'); // Botón guardar
            let currentTipo = 'Asociados'; // Tipo actual

            // --- Función para manejar vistas dinámicas ---
            function loadDynamicView(step) {
                console.log(`Cambiando a paso ${step}`);
                document.querySelectorAll('.step-panel').forEach((panel) => {
                    panel.style.display = panel.id === `step-${step}` ? 'block' : 'none';
                });
            }

            // --- Función para cargar datos en la tabla ---
            function loadCargas(step) {
                console.log(`Cargando historial para paso ${step}`);
                fetch(`/cargas/${step}`)
                    .then((response) => {
                        if (!response.ok) throw new Error('Error al recuperar los datos');
                        return response.json();
                    })
                    .then((data) => {
                        const tableBody = document.querySelector('.table tbody'); // Tabla para historial
                        tableBody.innerHTML = ''; // Limpiar tabla
                        if (data.length > 0) {
                            data.forEach((carga) => {
                                const row = `
                                    <tr>
                                        <td>${carga.nombre_archivo}</td>
                                        <td>${carga.metadatos || '<em>Sin metadatos</em>'}</td>
                                        <td><span class="text-truncate d-inline-block" style="max-width: 250px;">${carga.ruta_archivo}</span></td>
                                        <td>${new Date(carga.created_at).toLocaleString()}</td>
                                    </tr>`;
                                tableBody.innerHTML += row;
                            });
                        } else {
                            tableBody.innerHTML =
                                `<tr><td colspan="4" class="text-center">No se encontraron archivos cargados para este paso.</td></tr>`;
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

            // --- Función para cargar personalización ---
            function updatePersonalizationCard(tipo) {
                const normalizedTipo = tipo.toLowerCase();
                console.log(`Cargando personalización para tipo ${tipo}`);

                fetch(`/personalization/${tipo}`)
                    .then((response) => {
                        if (!response.ok) throw new Error(`No se encontraron datos para ${tipo}`);
                        return response.json();
                    })
                    .then((data) => {
                        dynamicContainer.innerHTML = `
                            <div class="mb-3">
                                <label for="interval-${normalizedTipo}" class="form-label">
                                    <i class="fas fa-clock"></i> Intervalo de Ejecución (horas)
                                </label>
                                <input type="number" class="form-control" id="interval-${normalizedTipo}" value="${data.intervalo_horas || 12}" />
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email-notifications-${normalizedTipo}" ${data.notificaciones_email ? 'checked' : ''} />
                                <label class="form-check-label" for="email-notifications-${normalizedTipo}"> Notificaciones por Email </label>
                            </div>`;
                        saveButton.setAttribute('data-tipo', tipo);
                        saveButton.innerText = `Guardar Personalización ${tipo}`;
                    })
                    .catch(() => {
                        console.log(`No se encontraron datos para ${tipo}. Usando valores predeterminados.`);
                        dynamicContainer.innerHTML = `
                            <div class="mb-3">
                                <label for="interval-${normalizedTipo}" class="form-label">
                                    <i class="fas fa-clock"></i> Intervalo de Ejecución (horas)
                                </label>
                                <input type="number" class="form-control" id="interval-${normalizedTipo}" value="12" />
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="email-notifications-${normalizedTipo}" />
                                <label class="form-check-label" for="email-notifications-${normalizedTipo}"> Notificaciones por Email </label>
                            </div>`;
                        saveButton.setAttribute('data-tipo', tipo);
                        saveButton.innerText = `Guardar Personalización ${tipo}`;
                    });
            }

            // --- Configuración del botón guardar ---
            saveButton.onclick = function() {
                const tipo = saveButton.getAttribute('data-tipo');
                const normalizedTipo = tipo.toLowerCase();
                const intervalo = document.getElementById(`interval-${normalizedTipo}`).value;
                const emailNotifications = document.getElementById(`email-notifications-${normalizedTipo}`)
                    .checked;

                console.log(`Guardando datos para tipo ${tipo}`);
                fetch(`/personalization/${tipo}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            intervalo_horas: intervalo,
                            notificaciones_email: emailNotifications ? 1 : 0,
                        }),
                    })
                    .then((response) => {
                        if (!response.ok) throw new Error('Error al guardar la configuración');
                        return response.json();
                    })
                    .then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Personalización guardada correctamente.',
                        });
                    })
                    .catch((error) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message,
                        });
                    });
            };

            // --- Configuración inicial de botones ---
            stepButtons.forEach((button) => {
                button.addEventListener('click', function() {
                    const step = button.dataset.step;
                    const tipo = button.dataset.tipo;

                    console.log(`Seleccionado paso ${step}, tipo ${tipo}`);
                    if (currentTipo !== tipo) {
                        currentTipo = tipo; // Actualizar el tipo actual
                        updatePersonalizationCard(tipo); // Actualizar datos de personalización
                    }

                    loadDynamicView(step); // Cambiar la vista dinámica
                    loadCargas(step); // Cargar datos de la tabla
                });
            });

            // Cargar el paso inicial por defecto
            loadDynamicView('1');
            loadCargas(1);
            updatePersonalizationCard('Asociados');
        });
    </script>
@endsection
