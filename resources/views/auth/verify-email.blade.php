{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.app')

@push('styles')
@endpush

@section('content')

# Verificar dirección de correo electrónico

@if (session('status') == 'verification-link-sent')
    <div class="mb-4 text-sm text-green-600">
        {{ __('Te hemos enviado un nuevo enlace de verificación.') }}
    </div>
@endif

<p class="mb-4 text-sm">
    {{ __('Antes de continuar, revisa tu correo para ver el enlace de verificación.') }}
    {{ __('Si no lo has recibido, puedes solicitar otro.') }}
</p>

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
        {{ __('Reenviar enlace de verificación') }}
    </button>
</form>

<form method="POST" action="{{ route('logout') }}" class="mt-4">
    @csrf
    <button type="submit" class="underline text-sm">
        {{ __('Cerrar sesión') }}
    </button>
</form>

@endsection

@push('scripts')
@endpush
