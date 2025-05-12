@extends('layouts.app')

@section('content')
  <div class="py-5">
    <div class="container">
      <h2>Resultados de búsqueda para: <em>{{ $q }}</em></h2>

      <hr>

      <h4>Recetas ({{ $recipes->count() }})</h4>
      @if($recipes->isEmpty())
        <p>No se encontraron recetas.</p>
      @else
        <ul class="list-group mb-4">
          @foreach($recipes as $r)
            <li class="list-group-item">
              <a href="{{ route('recipes.show', $r) }}">{{ $r->title }}</a>
            </li>
          @endforeach
        </ul>
      @endif

      <h4>Ingredientes ({{ $ingredients->count() }})</h4>
      @if($ingredients->isEmpty())
        <p>No se encontraron ingredientes.</p>
      @else
        <ul class="list-group">
          @foreach($ingredients as $ing)
            <li class="list-group-item">
              <a href="{{ route('ingredients.show', $ing) }}">{{ $ing->name }}</a>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
@endsection
