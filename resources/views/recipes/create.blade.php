@extends('layouts.app')

@section('content')
  <h1>Crear Receta</h1>

  <form action="{{ route('recipes.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label for="title" class="form-label">Título:</label>
      <input type="text" id="title" name="title"
             class="form-control @error('title') is-invalid @enderror"
             value="{{ old('title') }}" required>
      @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Descripción:</label>
      <textarea id="description" name="description"
                class="form-control @error('description') is-invalid @enderror"
                rows="3">{{ old('description') }}</textarea>
      @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <h5>Ingredientes</h5>
    <div class="row">
      @foreach($ingredients as $ing)
        <div class="col-12 col-md-6 mb-3">
          <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   name="ingredients[{{ $loop->index }}][id]"
                   value="{{ $ing->id }}"
                   id="ing{{ $ing->id }}"
                   {{ old("ingredients.{$loop->index}.id") == $ing->id ? 'checked' : '' }}>
            <label class="form-check-label" for="ing{{ $ing->id }}">
              {{ $ing->name }}
            </label>
          </div>
          <div class="input-group input-group-sm mt-1">
            <input type="number" step="0.01"
                   name="ingredients[{{ $loop->index }}][quantity]"
                   class="form-control @error("ingredients.{$loop->index}.quantity") is-invalid @enderror"
                   placeholder="Cant." value="{{ old("ingredients.{$loop->index}.quantity") }}">
            <input type="text"
                   name="ingredients[{{ $loop->index }}][unit]"
                   class="form-control @error("ingredients.{$loop->index}.unit") is-invalid @enderror"
                   placeholder="Unidad" value="{{ old("ingredients.{$loop->index}.unit") }}">
            @error("ingredients.{$loop->index}.quantity")
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error("ingredients.{$loop->index}.unit")
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      @endforeach
    </div>

    <button class="btn btn-primary">Guardar Receta</button>
    <a href="{{ route('recipes.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
  </form>
@endsection
