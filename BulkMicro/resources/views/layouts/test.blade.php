@extends('layouts.app')

@section('content')
    <style>
        @media (max-width: 768px) {
            #personalization-container {
                margin-top: 20px;
            }

            #dynamic-personalization {
                overflow-x: auto;
            }
        }

        #dynamic-personalization p {
            word-wrap: break-word;
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
                        <button class="btn btn-primary mx-2 step-btn" data-tipo="Asociados">Asociados</button>
                        <button class="btn btn-secondary mx-2 step-btn" data-tipo="Captaciones">Captaciones</button>
                        <button class="btn btn-secondary mx-2 step-btn" data-tipo="Colocaciones">Colocaciones</button>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="col-md-6">
                    <div id="personalization-container" class="accordion">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Personalización Dinámica</h5>
                            </div>
                            <div class="card-body">
                                <div id="dynamic-personalization">
                                    <p class="text-muted">Seleccione un tipo para cargar los datos de personalización.</p>
                                </div>
                                <button class="btn btn-success mt-3" id="personalization-save" data-tipo="Asociados">
                                    Guardar Personalización Asociados
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- SweetAlert Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- JavaScript --}}
    <script>
        function updatePersonalizationCard(tipo) {
            const normalizedTipo = tipo.toLowerCase();
            const dynamicContainer = document.getElementById('dynamic-personalization');
            const saveButton = document.querySelector('#personalization-save');

            if (!dynamicContainer || !saveButton) {
                console.warn('El contenedor o el botón no fue encontrado.');
                return;
            }

            fetch(`/personalization/${tipo}`)
                .then((response) => {
                    if (!response.ok) throw new Error(`Error al cargar datos para ${tipo}`);
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
                            <label class="form-check-label" for="email-notifications-${normalizedTipo}">
                                <i class="fas fa-envelope"></i> Notificaciones por Email
                            </label>
                        </div>
                    `;

                    saveButton.setAttribute('data-tipo', tipo);
                    saveButton.innerText = `Guardar Personalización ${tipo}`;
                })
                .catch((error) => {
                    console.error(`Error cargando datos para ${tipo}:`, error);
                    dynamicContainer.innerHTML = `<p class="text-danger">Error al cargar los datos.</p>`;
                });

            saveButton.addEventListener('click', function () {
                const intervalInput = document.getElementById(`interval-${normalizedTipo}`);
                const emailCheckbox = document.getElementById(`email-notifications-${normalizedTipo}`);

                if (!intervalInput || !emailCheckbox) {
                    console.warn('No se encontraron los campos para guardar.');
                    return;
                }

                const intervalo = intervalInput.value;
                const emailNotifications = emailCheckbox.checked;

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
                        if (!response.ok) throw new Error('Error al guardar configuración');
                        return response.json();
                    })
                    .then((data) => {
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
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const stepButtons = document.querySelectorAll('.step-btn');
            stepButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const tipo = button.dataset.tipo;
                    updatePersonalizationCard(tipo);
                });
            });

            updatePersonalizationCard('Asociados');
        });
    </script>
@endsection
