{{-- Configuración para las migraciones de las colocaciones --}}
<div class="step-panel" id="step-3" style="display: none;">
    <h2 class="fs-4 fw-semibold">Importar Colocaciones (Créditos)</h2>
    <p>Seleccione el archivo para cargar datos de colocaciones. Asegúrese de seguir el formato correcto.</p>
    <button class="btn btn-info mt-3" onclick="location.href='{{ route('columnas.colocaciones') }}'">
        <i class="fas fa-cogs"></i> Configuración de Colocaciones
    </button>

    <h2 class="fs-4 fw-semibold"><i class="fas fa-folder-open me-2"></i>Configuración de Carpeta o Servidor</h2>

    <div class="form-check">
        <input class="form-check-input config-radio" type="radio" name="config_step3" id="local_step3" value="local" checked>
        <label class="form-check-label" for="local_step3"><i class="fas fa-folder"></i> Carpeta Local</label>
    </div>
    <div class="form-check">
        <input class="form-check-input config-radio" type="radio" name="config_step3" id="aws_step3" value="aws">
        <label class="form-check-label" for="aws_step3"><i class="fab fa-aws"></i> Bucket AWS</label>
    </div>

    {{-- Configuración para Carpeta Local --}}
    <div id="local-config-step3" class="mt-3">
        <label for="manual-path-step3" class="form-label"><i class="fas fa-folder"></i> Ruta de la Carpeta</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="manual-path-step3" placeholder="Ejemplo: C:/mis-datos/colocaciones/">
            <button class="btn btn-primary" id="add-path-step3"><i class="fas fa-plus"></i> Agregar Ruta</button>
        </div>

        <label for="local-file-step3" class="form-label mt-3"><i class="fas fa-upload"></i> Seleccionar Archivos</label>
        <input type="file" class="form-control folder-input" id="local-file-step3" accept=".xlsx,.xls,.csv" multiple>
    </div>

    {{-- Configuración para Bucket AWS --}}
    <div id="aws-config-step3" class="mt-3" style="display: none;">
        <label for="bucket-path-step3" class="form-label"><i class="fas fa-link"></i> Ruta del Bucket</label>
        <input type="text" class="form-control bucket-path" id="bucket-path-step3" placeholder="s3://my-bucket/path">

        <label for="access-key-step3" class="form-label mt-2"><i class="fas fa-key"></i> Llave de Acceso</label>
        <input type="text" class="form-control access-key" id="access-key-step3">

        <label for="secret-key-step3" class="form-label mt-2"><i class="fas fa-lock"></i> Llave Secreta</label>
        <input type="password" class="form-control secret-key" id="secret-key-step3">

        <label for="aws-file-step3" class="form-label mt-3"><i class="fas fa-file-alt"></i> Nombre del Archivo</label>
        <input type="text" class="form-control" id="aws-file-step3" placeholder="Ejemplo: reporte-colocaciones.xlsx">
    </div>

    <button class="btn btn-success mt-3 save-config" data-step="3"><i class="fas fa-save"></i> Guardar Configuración</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('input[name="config_step3"]').change(function () {
            if ($(this).val() === 'local') {
                $('#local-config-step3').show();
                $('#aws-config-step3').hide();
            } else {
                $('#local-config-step3').hide();
                $('#aws-config-step3').show();
            }
        });

        $('.save-config[data-step="3"]').click(function () {
            const configType = $('input[name="config_step3"]:checked').val();
            const url = '/save-configuration';
            const configStep = 'colocaciones';
            let data = {};

            if (configType === 'local') {
                const manualPath = $('#manual-path-step3').val().trim();
                if (!manualPath) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ruta Vacía',
                        text: 'Por favor, especifique la ruta para la carpeta local.',
                    });
                    return;
                }
                data = { configType: 'local', configStep, path: manualPath };
            } else if (configType === 'aws') {
                const bucketPath = $('#bucket-path-step3').val().trim();
                const accessKey = $('#access-key-step3').val().trim();
                const secretKey = $('#secret-key-step3').val().trim();
                const fileName = $('#aws-file-step3').val().trim();

                if (!bucketPath || !accessKey || !secretKey || !fileName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos Incompletos',
                        text: 'Por favor, complete todos los campos para la configuración del bucket.',
                    });
                    return;
                }
                data = {
                    configType: 'aws',
                    configStep,
                    bucket_path: bucketPath,
                    access_key: accessKey,
                    secret_key: secretKey,
                    file_name: fileName,
                };
            }

            $.post(url, data, function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: response.message,
                });
            }).fail(function (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.responseJSON?.message || 'No se pudo guardar la configuración.',
                });
            });
        });
    });
</script>