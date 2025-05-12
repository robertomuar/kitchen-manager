{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@push('styles')
    {{-- Estilos específicos para el registro --}}
@endpush

@section('content')

# Registrarse

<form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
    @csrf

    <div>
        <label for="name" class="block font-medium">{{ __('Nombre') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
               class="w-full border rounded px-3 py-2">
        @error('name')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block font-medium">{{ __('Correo electrónico') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required
               class="w-full border rounded px-3 py-2">
        @error('email')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block font-medium">{{ __('Contraseña') }}</label>
        <input id="password" type="password" name="password" required
               class="w-full border rounded px-3 py-2">
        @error('password')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block font-medium">{{ __('Confirmar contraseña') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required
               class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <button type="submit" class="w-full px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
            {{ __('Registrarse') }}
        </button>
    </div>
</form>

<p class="mt-4 text-center text-sm">
    ¿Ya tienes cuenta?
    <a href="{{ route('login') }}" class="underline">Iniciar sesión</a>
</p>

@endsection

@push('scripts')
    {{-- Scripts para registro --}}
@endpush
