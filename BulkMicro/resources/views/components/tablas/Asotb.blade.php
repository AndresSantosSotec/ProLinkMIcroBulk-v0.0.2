@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fs-3 fw-bold text-primary">
                <i class="fas fa-columns me-2"></i> Configuración de Columnas para Clientes
            </h1>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Regresar al Dashboard
            </a>
        </div>

        <p class="text-muted">Aquí puedes gestionar las columnas para los clientes.</p>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <i class="fas fa-list-alt me-2"></i> Columnas
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr class="table-light">
                            <th>#</th>
                            <th>Nombre de la Columna</th>
                            <th>Nombre en Excel</th>
                            <th>Número de Columna</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($allColumns as $index => $column)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $column['column_name'] }}</td>
                                <td>{{ $column['excel_column_name'] }}</td>
                                <td>{{ $column['column_number'] }}</td>
                                <td>
                                    @if ($column['is_configured'])
                                        <span class="badge bg-success">Configurada</span>
                                    @else
                                        <span class="badge bg-warning text-dark">No Configurada</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('columnas.clientes.editar', $column['column_name']) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> {{ $column['is_configured'] ? 'Editar' : 'Configurar' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
@endsection

@section('scripts')
    <script>
        // Función para eliminar una columna con confirmación
        window.deleteColumn = function(columnName) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('delete-column') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            columnName
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(
                            `Error al eliminar la columna: ${response.status} ${response.statusText}`
                        );
                        return response.json();
                    })
                    .then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'La columna se eliminó correctamente.',
                            confirmButtonText: 'Aceptar'
                        }).then(() => location.reload());
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al eliminar',
                            text: error.message
                        });
                    });
                }
            });
        };
    </script>
@endsection
