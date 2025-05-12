@extends('layouts.app')

@section('content')
<div class="container my-5">
  <h1 class="h3 mb-4">Añadir Ingrediente</h1>

  <form action="{{ route('ingredients.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label for="name" class="form-label">Nombre</label>
      <input type="text" name="name" id="name"
             class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name') }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row">
      {{-- Cantidad actual --}}
      <div class="col-md-3 mb-3">
        <label for="quantity" class="form-label">Cantidad actual</label>
        <input type="number" step="0.01" name="quantity" id="quantity"
               class="form-control @error('quantity') is-invalid @enderror"
               value="{{ old('quantity') }}" required>
        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      {{-- Cantidad mínima --}}
      <div class="col-md-3 mb-3">
        <label for="min_quantity" class="form-label">Cantidad mínima</label>
        <input type="number" step="0.01" name="min_quantity" id="min_quantity"
               class="form-control @error('min_quantity') is-invalid @enderror"
               value="{{ old('min_quantity', 0) }}" required>
        @error('min_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      {{-- Unidad --}}
      <div class="col-md-3 mb-3">
        <label for="unit" class="form-label">Unidad</label>
        <select name="unit" id="unit"
                class="form-select @error('unit') is-invalid @enderror" required>
          <option value="">Selecciona unidad…</option>
          @foreach(\App\Models\Ingredient::units() as $code => $label)
            <option value="{{ $code }}" @selected(old('unit') == $code)>
              {{ $code }} — {{ $label }}
            </option>
          @endforeach
        </select>
        @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      {{-- Ubicación --}}
      <div class="col-md-3 mb-3">
        <label for="location" class="form-label">Ubicación</label>
        <select name="location" id="location"
                class="form-select @error('location') is-invalid @enderror" required>
          <option value="">Selecciona un armario…</option>
          @foreach(\App\Models\Ingredient::locations() as $loc)
            <option value="{{ $loc }}" @selected(old('location') == $loc)>
              {{ $loc }}
            </option>
          @endforeach
        </select>
        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="mb-3">
      <label for="expires_at" class="form-label">Fecha de caducidad</label>
      <input type="date" name="expires_at" id="expires_at"
             class="form-control @error('expires_at') is-invalid @enderror"
             value="{{ old('expires_at') }}">
      @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary">
      <i class="bi bi-save"></i> Guardar ingrediente
    </button>
    <a href="{{ route('ingredients.index') }}" class="btn btn-outline-secondary ms-2">
      Cancelar
    </a>
  </form>
</div>
@endsection
