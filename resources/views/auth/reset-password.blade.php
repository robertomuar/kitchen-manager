{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.app')

@push('styles')
@endpush

@section('content')

# Restablecer contraseña

<form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div>
        <label for="email" class="block font-medium">{{ __('Correo electrónico') }}</label>
        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
               class="w-full border rounded px-3 py-2">
        @error('email')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block font-medium">{{ __('Nueva contraseña') }}</label>
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
        <button type="submit" class="w-full px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">
            {{ __('Restablecer contraseña') }}
        </button>
    </div>
</form>

@endsection

@push('scripts')
@endpush
