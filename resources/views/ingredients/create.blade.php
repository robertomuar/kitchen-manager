@extends('layouts.app')

@section('content')
<h1>Añadir Ingrediente</h1>

<form action="{{ route('ingredients.store') }}" method="POST">
    @csrf
    <label>Nombre:</label>
    <input type="text" name="name" value="{{ old('name') }}" required>
    <label>Cantidad:</label>
    <input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}" required>
    <label>Unidad:</label>
    <input type="text" name="unit" value="{{ old('unit') }}">
    <label>Caduca (YYYY-MM-DD):</label>
    <input type="date" name="expires_at" value="{{ old('expires_at') }}">
    <button type="submit">Guardar</button>
</form>
@endsection
