<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Kitchen Manager')</title>

  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Tus CSS personalizados -->
  <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
  <link href="{{ asset('css/home.css') }}" rel="stylesheet">
  <link href="{{ asset('css/overlay.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

  <nav class="navbar navbar-expand-lg navbar-custom mb-4">
    <div class="container-fluid navbar-grid">
      <div class="grid-left">
        <a class="navbar-brand" href="{{ route('home') }}">
          <img src="{{ asset('images/centollo.png') }}" alt="Logo" class="d-inline-block align-text-top">
          <span class="brand-text">El Centollo Samurai</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <div class="grid-center collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Inicio</a></li>

          {{-- Recetas --}}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="recipesDropdown" data-bs-toggle="dropdown">
              Recetas
            </a>
            <ul class="dropdown-menu" aria-labelledby="recipesDropdown">
              {{-- Ver todas recetas siempre es público --}}
              <li>
                <a class="dropdown-item" href="{{ route('recipes.index') }}">Ver todas</a>
              </li>

              {{-- Sólo Nueva receta requiere autenticación --}}
              @guest
                <li>
                  <a class="dropdown-item auth-required" href="#">Nueva receta</a>
                </li>
              @else
                <li>
                  <a class="dropdown-item" href="{{ route('recipes.create') }}">Nueva receta</a>
                </li>
              @endguest
            </ul>
          </li>

          {{-- Ingredientes - Todo requiere autenticación --}}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Ingredientes</a>
            <ul class="dropdown-menu">
              @guest
                <li><a class="dropdown-item auth-required" href="#">Ver todos</a></li>
                <li><a class="dropdown-item auth-required" href="#">Añadir ingrediente</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item auth-required" href="#">Lista de la compra</a></li>
              @else
                <li><a class="dropdown-item" href="{{ route('ingredients.index') }}">Ver todos</a></li>
                <li><a class="dropdown-item" href="{{ route('ingredients.create') }}">Añadir ingrediente</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('shopping-list') }}">Lista de la compra</a></li>
              @endguest
            </ul>
          </li>

          <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
        </ul>

        <form class="search-center ms-3 d-flex" action="{{ route('search') }}" method="GET">
          <input class="form-control me-2" type="search" name="q" placeholder="Buscar..." required>
          <button class="btn btn-primary"><i class="bi bi-search"></i></button>
        </form>
      </div>

      <div class="grid-right">
        @guest
          <a id="open-login-modal" class="btn btn-outline-custom me-2" href="#">Login</a>
          <a id="open-register-modal" class="btn btn-outline-custom" href="#">Registro</a>
    @else
  <div class="d-flex align-items-center">
    @if(Auth::user()->profile_image)
      <img src="{{ asset('storage/' . Auth::user()->profile_image) }}"
           alt="Foto de perfil"
           class="rounded-circle me-2"
           style="width:32px; height:32px; object-fit:cover;">
    @endif
    <a class="nav-link me-3 text-capitalize" href="{{ route('profile.edit') }}">
      {{ Str::lower(Auth::user()->name) === Auth::user()->name 
           ? ucfirst(Auth::user()->name) 
           : Auth::user()->name 
      }}
    </a>
    <form method="POST" action="{{ route('logout') }}" class="d-inline">
      @csrf
      <button class="btn btn-outline-custom">Cerrar sesión</button>
    </form>
  </div>
@endguest

      </div>
    </div>
  </nav>

  <main class="flex-grow-1">
    <div class="container">@yield('content')</div>
  </main>

  {{-- Modales de Login y Registro --}}
  <div id="login-modal-backdrop" class="modal-backdrop">
    <div class="modal-panel position-relative">
      <span class="modal-close">&times;</span>
      <h2 class="h4 text-center mb-4">Iniciar sesión</h2>
      <form method="POST" action="{{ route('login') }}" class="mx-auto" style="max-width:280px;">
        @csrf
        <div class="mb-3">
          <label for="email_login" class="form-label">Correo electrónico</label>
          <input id="email_login" type="email" name="email" required class="form-control">
        </div>
        <div class="mb-3">
          <label for="password_login" class="form-label">Contraseña</label>
          <input id="password_login" type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
        <p class="text-center mt-3">
          ¿No tienes cuenta? <a id="open-register-modal-inline" href="#">Regístrate</a>
        </p>
      </form>
    </div>
  </div>

  <div id="register-modal-backdrop" class="modal-backdrop">
    <div class="modal-panel position-relative">
      <span class="modal-close">&times;</span>
      <h2 class="h4 text-center mb-4">Registro</h2>
      <form method="POST" action="{{ route('register') }}" class="mx-auto" style="max-width:280px;">
        @csrf
        <div class="mb-3">
          <label for="name_register" class="form-label">Nombre</label>
          <input id="name_register" type="text" name="name" required class="form-control">
        </div>
        <div class="mb-3">
          <label for="email_register" class="form-label">Correo electrónico</label>
          <input id="email_register" type="email" name="email" required class="form-control">
        </div>
        <div class="mb-3">
          <label for="password_register" class="form-label">Contraseña</label>
          <input id="password_register" type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Registrar</button>
        <p class="text-center mt-3">
          ¿Ya tienes cuenta? <a id="open-login-modal-inline" href="#">Iniciar sesión</a>
        </p>
      </form>
    </div>
  </div>

  <footer class="text-white text-center py-3 mt-auto w-100" style="background-color: var(--warm-bg);">
    <div class="container"><small>© {{ date('Y') }} Kitchen Manager</small></div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="{{ asset('js/overlay.js') }}"></script>
  @stack('scripts')
</body>
</html>