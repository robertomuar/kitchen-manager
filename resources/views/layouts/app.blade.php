<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kitchen Manager</title>

  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Tus CSS personalizados -->
  <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
  <link href="{{ asset('css/home.css') }}" rel="stylesheet">
  <link href="{{ asset('css/overlay.css') }}" rel="stylesheet">

  <!-- Overrides para forzar opacidad -->
  <style>
  
  </style>
  @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

  <nav class="navbar navbar-expand-lg navbar-custom mb-4">
    <div class="container-fluid navbar-grid">
      <!-- Columna IZQUIERDA -->
      <div class="grid-left">
        <a class="navbar-brand" href="{{ route('home') }}">Kitchen Manager RM</a>
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false"
                aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <!-- Columna CENTRAL -->
      <div class="grid-center collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Inicio</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="recipesDropdown" data-bs-toggle="dropdown">Recetas</a>
            <ul class="dropdown-menu" aria-labelledby="recipesDropdown">
              <li><a class="dropdown-item" href="{{ route('recipes.index') }}">Ver todas</a></li>
              <li><a class="dropdown-item" href="{{ route('recipes.create') }}">Nueva receta</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="ingredientsDropdown" data-bs-toggle="dropdown">Ingredientes</a>
            <ul class="dropdown-menu" aria-labelledby="ingredientsDropdown">
              <li><a class="dropdown-item" href="{{ route('ingredients.index') }}">Ver todos</a></li>
              <li><a class="dropdown-item" href="{{ route('ingredients.create') }}">Añadir ingrediente</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="{{ route('shopping-list') }}">Lista de la compra</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
        </ul>
        <form class="search-center ms-3 d-flex" action="{{ route('search') }}" method="GET">
          <input class="form-control me-2" type="search" name="q" placeholder="Buscar..." required>
          <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </form>
      </div>

      <!-- Columna DERECHA -->
      <div class="grid-right">
        @guest
          <!-- Triggers de modales -->
          <a id="open-login-modal" class="btn btn-outline-custom me-2" href="#">Login</a>
          <a id="open-register-modal" class="btn btn-outline-custom" href="#">Registro</a>
        @else
          <!-- Usuario autenticado -->
          <a class="nav-link me-3" href="{{ route('profile.edit') }}">{{ Auth::user()->name }}</a>
          <form method="POST" action="{{ route('logout') }}" class="d-inline">
            @csrf
            <button class="btn btn-outline-custom">Cerrar sesión</button>
          </form>
        @endguest
      </div>
    </div>
  </nav>

  <main class="flex-grow-1">
    <div class="container">@yield('content')</div>
  </main>

  {{-- Modal Login --}}
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
        ¿No tienes cuenta?
        <a href="#" id="open-register-modal-inline">Regístrate</a>
      </p>
    </div>
  </div>

  {{-- Modal Registro --}}
  <div id="register-modal-backdrop" class="modal-backdrop">
    <div class="modal-panel position-relative">
      <span class="modal-close">&times;</span>
      <h2 class="h4 text-center mb-4">Regístrate</h2>
      <form method="POST" action="{{ route('register') }}" class="text-left mx-auto" style="width: 280px;">
        @csrf
        <div class="mb-3">
          <label for="name-register" class="form-label">Nombre</label>
          <input id="name-register" type="text" name="name" required class="form-control">
        </div>
        <div class="mb-3">
          <label for="email-register" class="form-label">Correo electrónico</label>
          <input id="email-register" type="email" name="email" required class="form-control">
        </div>
        <div class="mb-3">
          <label for="password-register" class="form-label">Contraseña</label>
          <input id="password-register" type="password" name="password" required class="form-control">
        </div>
        <div class="mb-3">
          <label for="password-confirm-register" class="form-label">Confirmar contraseña</label>
          <input id="password-confirm-register" type="password" name="password_confirmation" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
      </form>
      <p class="text-center mt-3">
        ¿Ya tienes cuenta?
        <a href="#" id="open-login-modal-inline">Iniciar sesión</a>
      </p>
    </div>
  </div>

  <footer class="text-white text-center py-3 mt-auto w-100" style="background-color: var(--warm-bg);">
    <div class="container">
      <small>© {{ date('Y') }} Kitchen Manager. Todos los derechos reservados.</small>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  @stack('scripts')
  <script src="{{ asset('js/overlay.js') }}"></script>
</body>
</html>
