@extends('layouts.app')

@section('content')
<div class="container py-6">

  {{-- Header --}}
  <div class="mb-6">
    <h2 class="h3">Tu Dashboard</h2>
  </div>

  {{-- Estadísticas --}}
  <div class="row mb-6">
    <div class="col-md-6">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Ingredientes</h5>
          <p class="card-text display-4">{{ $ingCount }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card text-center">
        <div class="card-body">
          <h5 class="card-title">Recetas</h5>
          <p class="card-text display-4">{{ $recCount }}</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Formulario de perfil --}}
  <div class="card mb-6">
    <div class="card-header">Actualizar Perfil</div>
    <div class="card-body">

      @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
      @endif

      <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Nombre --}}
        <div class="mb-3">
          <label for="name" class="form-label">Nombre</label>
          <input id="name" type="text"
                 name="name"
                 value="{{ old('name', $user->name) }}"
                 required
                 class="form-control @error('name') is-invalid @enderror">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" type="email"
                 name="email"
                 value="{{ old('email', $user->email) }}"
                 required
                 class="form-control @error('email') is-invalid @enderror">
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Foto de perfil --}}
        <div class="mb-3">
          <label for="profile_image" class="form-label">Foto de Perfil</label>
          <div class="d-flex align-items-center mb-2">
          @if($user->profile_image)
  <img 
    src="{{ asset('storage/' . $user->profile_image) }}" 
    alt="Foto de perfil" 
    style="width:100px; height:100px; object-fit:cover; border-radius:50%;">
@endif
            <input id="profile_image" type="file"
                   name="profile_image"
                   accept="image/*"
                   class="form-control-file @error('profile_image') is-invalid @enderror">
          </div>
          @error('profile_image')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>

        {{-- Botón --}}
        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
      </form>
    </div>
  </div>
</div>
@endsection
