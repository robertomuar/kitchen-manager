@extends('layouts.app')

@section('content')
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Ingredientes</h1>
    <a href="{{ route('ingredients.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-lg"></i> Añadir ingrediente
    </a>
  </div>

  {{-- Filtros y búsqueda --}}
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" class="row gx-3 gy-2 align-items-end">
      <div class="col-md-4">
  <label for="filter" class="form-label">Estado</label>
  <select name="filter" id="filter" class="form-select">
    <option value="">Todos</option>
    <option value="expired" @selected(request('filter')=='expired')>
      Expirados
    </option>
    <option value="soon" @selected(request('filter')=='soon')>
      Por expirar (7 días)
    </option>
    <option value="buy" @selected(request('filter')=='buy')>
      Comprar
    </option>
  </select>
</div>

        <div class="col-md-4">
          <label for="search" class="form-label">Buscar</label>
          <input type="text" name="search" id="search"
                 class="form-control" placeholder="Nombre del ingrediente"
                 value="{{ request('search') }}">
        </div>
        <div class="col d-flex justify-content-end align-items-end">
          <button type="submit" class="btn btn-primary me-2">
            <i class="bi bi-funnel"></i> Filtrar
          </button>
          <a href="{{ route('ingredients.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle"></i> Limpiar
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- Tabla responsive --}}
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Nombre</th>
              <th>Cantidad</th>
              <th>Mínimo</th>
         
              <th>Ubicación</th>
              <th>Caduca</th>
              <th>Estado</th>
              <th>Alarma</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($ingredients as $ing)
              <tr @class([
                  'table-danger'  => $ing->alarm,
              ])>
                <td>{{ $ing->name }}</td>
                <td>{{ $ing->formatted_quantity }} {{ $ing->unit }}</td>
                <td>{{ rtrim(rtrim(number_format($ing->min_quantity, 2, '.', ''), '0'), '.') }} {{ $ing->unit }}</td>
             
                <td>{{ $ing->location }}</td>
                <td>{{ optional($ing->expires_at)->format('d/m/Y') ?? '—' }}</td>
                @php
  $exp = optional($ing->expires_at);
@endphp
<td>
  @if($exp->isPast())
    <span class="badge bg-danger">Expirado</span>
  @elseif($exp->diffInDays(now()) <= 7)
    <span class="badge bg-warning text-dark">Por expirar</span>
  @else
    <span class="badge bg-success">Válido</span>
  @endif
</td>

                <td>
                  @if($ing->alarm)
                    <span class="badge bg-danger">¡Comprar!</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="{{ route('ingredients.edit', $ing) }}"
                       class="btn btn-sm btn-outline-primary" title="Editar">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <button type="button"
                            class="btn btn-sm btn-outline-danger dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-expanded="false">
                      <span class="visually-hidden">Eliminar</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li class="px-3 py-2 text-center">
                        <p class="mb-2">¿Eliminar <strong>{{ $ing->name }}</strong>?</p>
                        <form action="{{ route('ingredients.destroy', $ing) }}" method="POST">
                          @csrf @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger w-100">
                            Confirmar
                          </button>
                        </form>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center py-4 text-muted">
                  No hay ingredientes para mostrar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Paginación --}}
    <div class="card-footer d-flex justify-content-between align-items-center">
      <span class="text-muted">
        Mostrando {{ $ingredients->firstItem() ?? 0 }}
        a {{ $ingredients->lastItem() ?? 0 }}
        de {{ $ingredients->total() }} ingredientes
      </span>
      {{ $ingredients->withQueryString()->links() }}
    </div>
  </div>
</div>
@endsection
