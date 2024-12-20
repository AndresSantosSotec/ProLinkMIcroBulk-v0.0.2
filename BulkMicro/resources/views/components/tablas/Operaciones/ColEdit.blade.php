@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="fs-3 fw-bold text-primary">
            <i class="fas fa-edit me-2"></i> Editar Columna
        </h1>
        <p class="text-muted">Estás editando la columna: <strong>{{ $column['column_name'] }}</strong></p>

        <a href="{{ route('columnas.colocaciones') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-1"></i> Regresar
        </a>

        <form method="POST" action="{{ route('update-column-colocaciones') }}" id="editColumnForm">
            @csrf
            <input type="hidden" name="columnName" value="{{ $column['column_name'] }}">

            <div class="mb-3">
                <label for="excelColumnName" class="form-label">Nombre en Excel</label>
                <input type="text" class="form-control" id="excelColumnName" name="excelColumnName"
                    value="{{ $column['excel_column_name'] }}" required>
            </div>

            <div class="mb-3">
                <label for="columnNumber" class="form-label">Número de Columna</label>
                <input type="number" class="form-control" id="columnNumber" name="columnNumber"
                    value="{{ $column['column_number'] }}" min="0" required>
            </div>

            <div class="alert alert-danger d-none" id="errorAlert"></div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('editColumnForm').addEventListener('submit', function(event) {
            const columnNumberInput = document.getElementById('columnNumber');
            const columnNumber = columnNumberInput.value;

            // Verificar si el valor de "Número de Columna" es válido
            if (columnNumber === '' || isNaN(columnNumber) || columnNumber < 0) {
                event.preventDefault();
                const errorAlert = document.getElementById('errorAlert');
                errorAlert.textContent = 'Por favor, ingresa un número de columna válido (0 o mayor).';
                errorAlert.classList.remove('d-none');
                return;
            }

            // Ocultar el mensaje de error si todo está correcto
            const errorAlert = document.getElementById('errorAlert');
            errorAlert.classList.add('d-none');
        });
    </script>
@endsection
