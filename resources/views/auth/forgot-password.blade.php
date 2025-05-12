{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.app')

@push('styles')
<style>
  .forgot-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 80vh;
    background-color: var(--bs-gray-100);
  }
  .forgot-panel {
    background: #fff;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.175);
    width: 100%;
    max-width: 360px;
  }
  .btn-warm {
    background-color: var(--warm-bg);
    color: #fff;
  }
  .btn-warm:hover {
    background-color: var(--warm-hover-text);
    color: #fff;
  }
  /* Asegura que el input y el botón tengan el mismo ancho */
  .forgot-panel input,
  .forgot-panel .btn-warm {
    width: 100%;
    max-width: 100%;
  }
  /* Espaciado vertical extra */
  .forgot-panel .mb-4 {
    margin-bottom: 1.5rem;
  }
</style>
@endpush

@section('content')
<div class="forgot-container">
  <div class="forgot-panel">
    <h2 class="h4 text-center mb-4">Restablecer contraseña</h2>
    <p class="mb-4 text-center text-sm">
      ¿Olvidaste tu contraseña? Introduce tu correo y te enviaremos un enlace para restablecerla.
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
      @csrf

      <div class="mb-4">
        <label for="email" class="block font-medium mb-1">{{ __('Correo electrónico') }}</label>
        <input
          id="email"
          type="email"
          name="email"
          value="{{ old('email') }}"
          required
          class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-warm-bg"
        >
        @error('email')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div class="mb-4">
        <button type="submit" class="btn btn-warm px-4 py-2 rounded">
          {{ __('Enviar enlace de restablecimiento') }}
        </button>
      </div>
    </form>

    <p class="mt-4 text-center text-sm">
      ¿Recordaste tu contraseña?
      <a href="#" id="open-login-modal" class="underline font-medium">Iniciar sesión</a>
    </p>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Si usas overlay, reabre el modal de login
  $('#open-login-modal').on('click', function(e) {
    e.preventDefault();
    $('#login-modal-backdrop').addClass('show');
  });
</script>
@endpush
