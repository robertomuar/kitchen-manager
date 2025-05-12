@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/home.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="{{ asset('css/overlay.css') }}" rel="stylesheet">
@endpush

@section('content')
  <!-- Hero Section -->
  <section class="hero d-flex flex-column justify-content-center text-center">
    <div class="container">
      <h1 class="display-4 hero-title">¡Bienvenido a Kitchen Manager!</h1>
      <p class="lead hero-subtitle">
        Organiza tu despensa y recetas de forma sencilla y moderna.
      </p>
      <form action="{{ route('search') }}" method="GET" class="search-bar mx-auto mb-4">
        <div class="input-group input-group-lg rounded-pill overflow-hidden">
          <input
            type="text"
            name="q"
            class="form-control border-0"
            placeholder="Buscar recetas o ingredientes..."
            aria-label="Buscar"
            required
          >
          <button class="btn btn-primary px-4" type="submit">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </form>
    </div>
  </section>

  <!-- Modal Overlay -->
  <div id="login-modal-backdrop" class="modal-backdrop">
    <div class="modal-panel position-relative">
      <span class="modal-close">&times;</span>
      <h2 class="h4 text-center mb-4">Iniciar sesión</h2>
      <form method="POST" action="{{ route('login') }}" class="text-left mx-auto" style="width: 280px;">
        @csrf
        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico</label>
          <input id="email" type="email" name="email" required class="form-control">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input id="password" type="password" name="password" required class="form-control">
        </div>
        <div class="form-check text-center mb-3">
          <input type="checkbox" name="remember" id="remember" class="form-check-input">
          <label for="remember" class="form-check-label">Recuérdame</label>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
        @if(Route::has('password.request'))
          <div class="text-center mt-2">
            <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
          </div>
        @endif
      </form>
      <p class="text-center mt-3">
        ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a>
      </p>
    </div>
  </div>

  <!-- Características -->
  <section id="features" class="py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card h-100 shadow-sm text-center">
            <div class="card-body">
              <h5 class="card-title">Control de Ingredientes</h5>
              <p class="card-text">Lleva el stock y la caducidad de tus ingredientes al día.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm text-center">
            <div class="card-body">
              <h5 class="card-title">Gestión de Recetas</h5>
              <p class="card-text">Crea y edita recetas asociadas a tus ingredientes.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm text-center">
            <div class="card-body">
              <h5 class="card-title">Dashboard Intuitivo</h5>
              <p class="card-text">Visualiza estadísticas y accesos directos a tus herramientas.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/home.js') }}"></script>
<script src="{{ asset('js/overlay.js') }}"></script>
@endpush
