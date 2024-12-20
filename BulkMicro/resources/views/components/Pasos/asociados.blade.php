<!-- Configuraciones de Migraciones Asociados -->
<div class="step-panel" id="step-1" style="display: block;">
    <h2 class="fs-4 fw-semibold">Importar Clientes (Asociados)</h2>
    <p>Seleccione el archivo para cargar datos de clientes. Asegúrese de seguir el formato correcto.</p>
    <button class="btn btn-info mt-3" onclick="location.href='{{ route('columnas.clientes') }}'">
        Columnas para Clientes
    </button>

    <h2 class="fs-4 fw-semibold"><i class="fas fa-folder-open me-2"></i>Configuración de Carpeta o Servidor</h2>
    <div class="form-check">
        <input class="form-check-input config-radio" type="radio" name="config_step1" id="local_step1" value="local"
            checked>
        <label class="form-check-label" for="local_step1"><i class="fas fa-folder"></i> Carpeta Local</label>
    </div>
    <div class="form-check">
        <input class="form-check-input config-radio" type="radio" name="config_step1" id="aws_step1" value="aws">
        <label class="form-check-label" for="aws_step1"><i class="fab fa-aws"></i> Bucket AWS</label>
    </div>

    <!-- Configuración para Carpeta Local -->
    <div class="local-config mt-3" id="local-config-step1">
        <label for="manual-path-step1" class="form-label"><i class="fas fa-folder"></i> Ruta de la Carpeta</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="manual-path-step1"
                placeholder="Ejemplo: C:/mis-datos/clientes/">
        </div>

        <label for="local-file-step1" class="form-label mt-3"><i class="fas fa-upload"></i> Seleccionar Archivos</label>
        <input type="file" class="form-control folder-input" id="local-file-step1" accept=".xlsx,.xls,.csv" multiple>
    </div>

    <!-- Configuración para Bucket AWS -->
    <div class="aws-config mt-3" id="aws-config-step1" style="display: none;">
        <label for="bucket-path-step1" class="form-label"><i class="fas fa-link"></i> Ruta del Bucket</label>
        <input type="text" class="form-control bucket-path" id="bucket-path-step1" placeholder="s3://my-bucket/path">
        <label for="access-key-step1" class="form-label mt-2"><i class="fas fa-key"></i> Llave de Acceso</label>
        <input type="text" class="form-control access-key" id="access-key-step1">
        <label for="secret-key-step1" class="form-label mt-2"><i class="fas fa-lock"></i> Llave Secreta</label>
        <input type="password" class="form-control secret-key" id="secret-key-step1">
        <label for="aws-file-step1" class="form-label mt-3"><i class="fas fa-upload"></i> Archivo Asociado</label>
        <input type="text" class="form-control" id="aws-file-step1" placeholder="Ejemplo: reporte-clientes.xlsx">
    </div>

    <button class="btn btn-primary mt-3 import-btn" data-step="1" disabled><i class="fas fa-upload"></i>
        Importar</button>
    <button class="btn btn-success mt-3 save-config" data-step="1" disabled><i class="fas fa-save"></i> Guardar
        Configuración</button>

    <!-- Contenedor para mostrar datos válidos -->
    <div id="data-display" class="mt-4"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content')
            }
        });

        // Alternar entre Carpeta Local y Bucket AWS
        $('input[name="config_step1"]').change(function() {
            if ($(this).val() === 'local') {
                $('#local-config-step1').show();
                $('#aws-config-step1').hide();
            } else {
                $('#local-config-step1').hide();
                $('#aws-config-step1').show();
            }
        });

        // Habilitar botón solo si hay archivo seleccionado
        $('#local-file-step1').change(function() {
            const file = $(this).prop('files')[0];
            validateFile(file);
        });

        // Validar archivo antes de enviar
        function validateFile(file) {
            const allowedExtensions = ['csv', 'xls', 'xlsx'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const maxFileSize = 20 * 1024 * 1024; // 20 MB

            if (!allowedExtensions.includes(fileExtension)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Formato no permitido',
                    text: 'Solo se permiten archivos CSV, XLS o XLSX.'
                });
                $('#local-file-step1').val('');
                $('.import-btn[data-step="1"]').prop('disabled', true);
                return false;
            }

            if (file.size > maxFileSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo demasiado grande',
                    text: 'El archivo debe ser menor a 20 MB.'
                });
                $('#local-file-step1').val('');
                $('.import-btn[data-step="1"]').prop('disabled', true);
                return false;
            }

            $('.import-btn[data-step="1"]').prop('disabled', false);
            return true;
        }

        // Enviar archivo al servidor
        $('.import-btn[data-step="1"]').click(function() {
            const fileInput = $('#local-file-step1');
            const file = fileInput.prop('files')[0];

            if (!validateFile(file)) return;

            Swal.fire({
                title: 'Cargando...',
                text: 'Procesando archivo, por favor espera.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const formData = new FormData();
            formData.append('file', file);

            $.ajax({
                url: '/convert-excel-to-json',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();

                    if (response.validData.length > 0) {
                        displayValidData(response.validData, response.errors);
                        Swal.fire({
                            icon: 'success',
                            title: 'Datos procesados',
                            text: 'El archivo fue procesado con éxito.',
                        });
                    }

                    if (response.errors.length > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Errores en el archivo',
                            html: `<ul>${response.errors.map(e => `<li>${e}</li>`).join('')}</ul>`,
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message ||
                            'Hubo un problema al procesar el archivo.'
                    });
                }
            });
        });

        // Mostrar datos válidos y errores en una tabla
        function displayValidData(data, errors) {
            const table = $('<table>').addClass('table table-bordered table-hover');
            const thead = $('<thead>');
            const tbody = $('<tbody>');

            if (data.length > 0) {
                const headers = Object.keys(data[0]);
                const headerRow = $('<tr>');
                headers.forEach(header => headerRow.append($('<th>').text(header)));
                headerRow.append($('<th>').text('Errores')); // Columna adicional para errores
                thead.append(headerRow);

                data.forEach((row, index) => {
                    const rowElement = $('<tr>');
                    headers.forEach(header => {
                        const cell = $('<td>').text(row[header] || '');
                        rowElement.append(cell);
                    });

                    // Agregar errores si existen
                    if (errors[index]) {
                        rowElement.addClass('table-danger');
                        const errorCell = $('<td>').html(errors[index].join('<br>'));
                        rowElement.append(errorCell);
                    } else {
                        rowElement.append($('<td>').text('')); // Celda vacía si no hay error
                    }

                    tbody.append(rowElement);
                });
            } else {
                tbody.append($('<tr>').append($('<td>').attr('colspan', '100%').text('No hay datos válidos.')));
            }

            table.append(thead).append(tbody);
            $('#data-display').html(table);
        }
    });
</script>
