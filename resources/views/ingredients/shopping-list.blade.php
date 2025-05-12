@extends('layouts.app')

@section('content')
<div class="container my-5">
  <h1 class="h3 mb-4">Lista de la compra</h1>

  @if($items->isEmpty())
    <div class="alert alert-success">
      ¡Tu despensa está llena! No hay nada que comprar.
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Disponible</th>
            <th>Mínimo</th>
            <th>Unidad</th>
            <th>Ubicación</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $i)
            <tr>
              <td>{{ $i->name }}</td>
              <td>{{ $i->formatted_quantity }} {{ $i->unit }}</td>
              <td>{{ $i->min_quantity }} {{ $i->unit }}</td>
              <td>{{ $i->unit }}</td>
              <td>{{ $i->location }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
