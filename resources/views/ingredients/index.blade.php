@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Ingredientes</h1>
    <a href="{{ route('ingredients.create') }}" class="btn btn-success">Añadir ingrediente</a>
  </div>

  <div class="mb-3">
    <form class="row gx-2 gy-1 align-items-center">
      <div class="col-auto">
        <select name="filter" class="form-select">
          <option value="">Todos</option>
          <option value="expired"  @selected(request('filter')=='expired')>Expirados</option>
          <option value="soon"     @selected(request('filter')=='soon')>Por expirar (7 días)</option>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-primary">Filtrar</button>
      </div>
    </form>
  </div>

  <table class="table table-hover bg-white">
    <thead class="table-light">
      <tr>
        <th>Nombre</th><th>Cantidad</th><th>Unidad</th><th>Caduca</th><th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($ingredients as $ing)
        <tr class="@if($ing->expires_at && $ing->expires_at < now()) table-danger @elseif($ing->expires_at && $ing->expires_at->diffInDays(now())<=3) table-warning @endif">
          <td>{{ $ing->name }}</td>
          <td>{{ $ing->quantity }}</td>
          <td>{{ $ing->unit }}</td>
          <td>{{ $ing->expires_at?->format('Y-m-d') }}</td>
          <td>
            <a href="{{ route('ingredients.edit', $ing) }}" class="btn btn-sm btn-outline-primary">Editar</a>
            <form action="{{ route('ingredients.destroy', $ing) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Eliminar</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  {{ $ingredients->links() }}
@endsection
