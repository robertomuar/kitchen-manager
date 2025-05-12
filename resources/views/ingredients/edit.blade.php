@extends('layouts.app')

@section('content')
    <h1>Editar Ingrediente</h1>

    <form action="{{ route('ingredients.update', $ingredient) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Nombre:</label><br>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $ingredient->name) }}"
                required
            >
            @error('name')
                <div style="color:red">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 1em;">
            <label for="quantity">Cantidad:</label><br>
            <input
                type="number"
                step="0.01"
                id="quantity"
                name="quantity"
                value="{{ old('quantity', $ingredient->quantity) }}"
                required
            >
            @error('quantity')
                <div style="color:red">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 1em;">
            <label for="unit">Unidad:</label><br>
            <input
                type="text"
                id="unit"
                name="unit"
                value="{{ old('unit', $ingredient->unit) }}"
            >
            @error('unit')
                <div style="color:red">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 1em;">
            <label for="expires_at">Caduca (YYYY-MM-DD):</label><br>
            <input
                type="date"
                id="expires_at"
                name="expires_at"
                value="{{ old('expires_at', optional($ingredient->expires_at)->format('Y-m-d')) }}"
            >
            @error('expires_at')
                <div style="color:red">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 1.5em;">
            <button type="submit">Actualizar Ingrediente</button>
            <a href="{{ route('ingredients.index') }}" style="margin-left: 1em;">Cancelar</a>
        </div>
    </form>
@endsection
