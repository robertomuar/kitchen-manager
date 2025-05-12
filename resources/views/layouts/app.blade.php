<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kitchen Manager</title>

  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Navbar personalizado -->
  <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

  <nav class="navbar navbar-expand-lg navbar-custom mb-4">
    <div class="container-fluid navbar-grid">

      <!-- Columna IZQUIERDA -->
      <div class="grid-left">
        <a class="navbar-brand" href="{{ route('home') }}">Kitchen Manager</a>
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
            <a class="nav-link dropdown-toggle" href="#" id="recipesDropdown">Recetas</a>
            <ul class="dropdown-menu" aria-labelledby="recipesDropdown">
              <li><a class="dropdown-item" href="{{ route('recipes.index') }}">Ver todas</a></li>
              <li><a class="dropdown-item" href="{{ route('recipes.create') }}">Nueva receta</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="ingredientsDropdown">Ingredientes</a>
            <ul class="dropdown-menu" aria-labelledby="ingredientsDropdown">
              <li><a class="dropdown-item" href="{{ route('ingredients.index') }}">Ver todos</a></li>
              <li><a class="dropdown-item" href="{{ route('ingredients.create') }}">Añadir ingrediente</a></li>
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
          <a class="btn btn-outline-custom me-2" href="{{ route('login') }}">Login</a>
          <a class="btn btn-outline-custom" href="{{ route('register') }}">Registro</a>
        @else
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

  <footer class="text-white text-center py-3 mt-auto w-100" style="background-color: var(--warm-bg);">
    <div class="container">
      <small>© {{ date('Y') }} Kitchen Manager. Todos los derechos reservados.</small>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
