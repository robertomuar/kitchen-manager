{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@push('styles')
<style>
  /* Panel ajustado al contenido */
  .login-panel {
    display: inline-block;
    width: auto !important;
    max-width: none !important;
    padding: 2rem;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
  }

  /* Alinea todo el texto al inicio de cada campo */
  .login-panel label,
  .login-panel input,
  .login-panel button,
  .login-panel a {
    text-align: left;
    display: block;
  }

  /* Inputs con texto a la izquierda */
  .login-panel input {
    text-align: left;
  }

  /* Checkbox “Recuérdame” centrado */
  .remember-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
  }
</style>
@endpush

@section('content')
  {{-- ESTE contenedor flex centra TODO el panel en la ventana --}}
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="login-panel">
      <h1 class="text-4xl font-bold text-center mb-8">Iniciar sesión</h1>

      <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div class="w-auto">
          <label for="email" class="font-medium mb-1">{{ __('Correo electrónico') }}</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
            class="w-64 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
          @error('email')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="w-auto">
          <label for="password" class="font-medium mb-1">{{ __('Contraseña') }}</label>
          <input id="password" type="password" name="password" required
            class="w-64 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
          @error('password')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="remember-wrapper">
          <input type="checkbox" name="remember" id="remember" class="mr-2">
          <label for="remember" class="text-sm">{{ __('Recuérdame') }}</label>
        </div>

        <div class="text-center">
          <button type="submit"
            class="inline-block px-6 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
            {{ __('Iniciar sesión') }}
          </button>
        </div>

        @if (Route::has('password.request'))
          <div class="text-center">
            <a href="{{ route('password.request') }}" class="text-sm underline">
              {{ __('¿Olvidaste tu contraseña?') }}
            </a>
          </div>
        @endif
      </form>

      <p class="mt-8 text-center text-sm">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" class="underline font-medium">Regístrate</a>
      </p>
    </div>
  </div>
@endsection

@push('scripts')
{{-- Scripts específicos para login --}}
@endpush
