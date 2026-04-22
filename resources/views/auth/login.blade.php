@extends('layout')

@section('content')

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <h1>Inicia Sesión</h1>
            <p>Bienvenido a RAIKKO</p>
        </div>

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

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
                    autofocus
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

            <!-- Remember Me -->
            <div class="form-group checkbox">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    name="remember"
                >
                <label for="remember_me">Recuérdame</label>
            </div>

            <button type="submit" class="btn-login">Inicia Sesión</button>

            <p class="auth-footer">
                ¿No tienes cuenta? 
                <a href="{{ route('register') }}">Regístrate aquí</a>
            </p>

            @if (Route::has('password.request'))
                <p class="auth-footer forgot">
                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </p>
            @endif
        </form>
    </div>
</div>

@endsection
