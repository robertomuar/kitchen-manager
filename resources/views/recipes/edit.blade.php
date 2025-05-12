@extends('layouts.app')

@section('content')
  <h1>Editar Receta</h1>

  <form action="{{ route('recipes.update', $recipe) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Título --}}
    <div class="mb-3">
      <label for="title" class="form-label">Título:</label>
      <input
        type="text"
        id="title"
        name="title"
        class="form-control @error('title') is-invalid @enderror"
        value="{{ old('title', $recipe->title) }}"
        required
      >
      @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Descripción --}}
    <div class="mb-3">
      <label for="description" class="form-label">Descripción:</label>
      <textarea
        id="description"
        name="description"
        class="form-control @error('description') is-invalid @enderror"
        rows="3"
      >{{ old('description', $recipe->description) }}</textarea>
      @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Ingredientes --}}
    <h5 class="mt-4">Ingredientes</h5>
    <div class="row">
      @foreach($ingredients as $ing)
        @php
          // ¿Está seleccionado este ingrediente?
          $inRecipe = $recipe->ingredients->contains($ing->id);
          $oldId   = old('ingredients.'.$loop->index.'.id');
          $checked = $oldId !== null
                     ? ($oldId == $ing->id)
                     : $inRecipe;

          // Valores de cantidad y unidad
          $pivot = $inRecipe
                   ? $recipe->ingredients->firstWhere('id', $ing->id)->pivot
                   : null;
          $quantity = old('ingredients.'.$loop->index.'.quantity',
                          $pivot->quantity ?? '');
          $unit     = old('ingredients.'.$loop->index.'.unit',
                          $pivot->unit ?? '');
        @endphp

        <div class="col-12 col-md-6 mb-3">
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              name="ingredients[{{ $loop->index }}][id]"
              value="{{ $ing->id }}"
              id="ing{{ $ing->id }}"
              {{ $checked ? 'checked' : '' }}
            >
            <label class="form-check-label" for="ing{{ $ing->id }}">
              {{ $ing->name }}
            </label>
          </div>

          <div class="input-group input-group-sm mt-1">
            <input
              type="number"
              step="0.01"
              name="ingredients[{{ $loop->index }}][quantity]"
              class="form-control @error('ingredients.'.$loop->index.'.quantity') is-invalid @enderror"
              placeholder="Cant."
              value="{{ $quantity }}"
            >
            <input
              type="text"
              name="ingredients[{{ $loop->index }}][unit]"
              class="form-control @error('ingredients.'.$loop->index.'.unit') is-invalid @enderror"
              placeholder="Unidad"
              value="{{ $unit }}"
            >
            @error('ingredients.'.$loop->index.'.quantity')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error('ingredients.'.$loop->index.'.unit')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
      @endforeach
    </div>

    {{-- Botones --}}
    <div class="mt-4">
      <button type="submit" class="btn btn-primary">Actualizar Receta</button>
      <a href="{{ route('recipes.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
  </form>
@endsection
