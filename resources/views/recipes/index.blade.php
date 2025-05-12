@extends('layouts.app')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Recetas</h1>
    <a href="{{ route('recipes.create') }}" class="btn btn-success">Nueva Receta</a>
  </div>

  <div class="row g-3">
    @foreach($recipes as $r)
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">{{ $r->title }}</h5>
            <p class="card-text">{{ Str::limit($r->description, 80) }}</p>
          </div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              Ingredientes: {{ $r->ingredients_count }}
            </li>
          </ul>
          <div class="card-footer text-end">
            <a href="{{ route('recipes.edit', $r) }}" class="btn btn-sm btn-outline-primary">Editar</a>
            <form action="{{ route('recipes.destroy', $r) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Eliminar</button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-4">{{ $recipes->links() }}</div>
@endsection
