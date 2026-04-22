@extends('layout')

@section('content')

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <h1>Crear Cuenta</h1>
            <p>Únete a RAIKKO</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label for="name">Nombre Completo</label>
                <input 
                    id="name" 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="Tu nombre"
                    required 
                    autofocus
                />
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="tu@email.com"
                    required
                />
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                />
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input 
                    id="password_confirmation" 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="••••••••"
                    required
                />
                @error('password_confirmation')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-register">Registrarse</button>

            <p class="auth-footer">
                ¿Ya tienes cuenta? 
                <a href="{{ route('login') }}">Inicia sesión aquí</a>
            </p>
        </form>
    </div>
</div>

@endsection
