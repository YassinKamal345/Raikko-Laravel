@extends('layout')

@section('content')

<div class="checkout-page">
    <div class="checkout-header">
        <div class="checkout-steps">
            <div class="step active">
                <span class="step-number">1</span>
                <span class="step-label">Datos</span>
            </div>
            <div class="step-divider"></div>
            <div class="step">
                <span class="step-number">2</span>
                <span class="step-label">Envío</span>
            </div>
            <div class="step-divider"></div>
            <div class="step">
                <span class="step-number">3</span>
                <span class="step-label">Pago</span>
            </div>
        </div>
    </div>

    <div class="checkout-container">
        <form id="checkout-form" method="POST" action="/checkout/process" class="checkout-form">
            @csrf
            <input
            type="hidden"
            name="cart"
            id="cart-input"
            >
            
            <!-- PASO 1: DATOS -->
            <div class="checkout-section active" id="step-datos">
                <h2 class="checkout-section-title">Rellena tus datos</h2>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <input type="text" name="nombre" placeholder="Nombre *" required class="form-input">
                    </div>
                    <div class="form-group">
                        <input type="text" name="apellidos" placeholder="Apellidos *" required class="form-input">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="E-mail *" required class="form-input">
                    </div>
                    <div class="form-group">
                        <input type="email" name="email_confirm" placeholder="Repetir e-mail *" required class="form-input">
                    </div>
                </div>

                <div class="form-grid-phone">
                    <div class="form-group">
                        <select name="prefijo" class="form-input form-select">
                            <option value="+34" selected>+34</option>
                            <option value="+1">+1</option>
                            <option value="+44">+44</option>
                            <option value="+33">+33</option>
                            <option value="+49">+49</option>
                            <option value="+39">+39</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="tel" name="telefono" placeholder="Teléfono móvil *" required class="form-input">
                    </div>
                </div>

                <p class="form-note">Los campos marcados con asterisco son obligatorios.</p>

                <p class="form-consent">
                    Al continuar, confirmo que he podido leer y entendido la información sobre el uso de mis datos personales explicada en la <a href="#" class="form-link">Política de Privacidad</a>.
                </p>

                <div class="form-actions">
                    <a href="/cart" class="btn-secondary">Volver</a>
                    <button type="button" class="btn-primary" onclick="nextStep('envio')">Continuar</button>
                </div>
            </div>

            <!-- PASO 2: ENVÍO -->
            <div class="checkout-section" id="step-envio">
                <h2 class="checkout-section-title">Selecciona envío</h2>
                
                <div class="shipping-options">
                    <label class="shipping-option">
                        <input type="radio" name="envio" value="recogida" required>
                        <span class="shipping-label">
                            <span class="shipping-title">Por recogida</span>
                            <span class="shipping-desc">Recógelo en nuestras instalaciones</span>
                            <span class="shipping-price">Gratis</span>
                        </span>
                    </label>

                    <label class="shipping-option">
                        <input type="radio" name="envio" value="domicilio" required>
                        <span class="shipping-label">
                            <span class="shipping-title">A domicilio</span>
                            <span class="shipping-desc">Recibelo en la dirección que indiques</span>
                            <span class="shipping-price">5€</span>
                        </span>
                    </label>
                </div>

                <!-- DIRECCIÓN (visible solo si selecciona domicilio) -->
                <div class="form-group" id="address-section" style="display:none;">
                    <input type="text" name="direccion" placeholder="Dirección *" class="form-input">
                </div>

                <div class="form-group" id="city-section" style="display:none;">
                    <div class="form-grid-2">
                        <input type="text" name="ciudad" placeholder="Ciudad *" class="form-input">
                        <input type="text" name="codigo_postal" placeholder="Código Postal *" class="form-input">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="prevStep('datos')">Volver</button>
                    <button type="button" class="btn-primary" onclick="nextStep('pago')">Continuar</button>
                </div>
            </div>

            <!-- PASO 3: PAGO -->
            <div class="checkout-section" id="step-pago">
                <h2 class="checkout-section-title">Resumen y pago</h2>
                
                <div class="summary-section">
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span id="summary-subtotal">0€</span>
                    </div>
                    <div class="summary-item">
                        <span>IVA (21%)</span>
                        <span id="summary-iva">0€</span>
                    </div>
                    <div class="summary-item">
                        <span>Envío</span>
                        <span id="summary-envio">Gratis</span>
                    </div>
                    <div class="summary-item summary-total">
                        <span>Total</span>
                        <span id="summary-total">0€</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="prevStep('envio')">Volver</button>
                    <button type="submit" class="btn-primary btn-pay">Proceder al pago</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let currentStep = 'datos';

function nextStep(step) {
    // Validar paso actual
    if (currentStep === 'datos' && !validateDatos()) return;
    if (currentStep === 'envio' && !validateEnvio()) return;

    // Ocultar paso actual
    document.getElementById('step-' + currentStep).classList.remove('active');
    
    // Mostrar nuevo paso
    currentStep = step;
    document.getElementById('step-' + currentStep).classList.add('active');
    
    // Actualizar indicadores de pasos
    updateSteps();

    // Si es el paso de pago, cargar resumen
    if (step === 'pago') {
        loadPaymentSummary();
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {
    document.getElementById('step-' + currentStep).classList.remove('active');
    currentStep = step;
    document.getElementById('step-' + currentStep).classList.add('active');
    updateSteps();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateSteps() {
    const steps = {
        'datos': 1,
        'envio': 2,
        'pago': 3
    };
    
    document.querySelectorAll('.step').forEach((step, index) => {
        step.classList.remove('active');
        if (index + 1 <= steps[currentStep]) {
            step.classList.add('active');
        }
    });
}

function validateDatos() {
    const nombre = document.querySelector('input[name="nombre"]').value;
    const apellidos = document.querySelector('input[name="apellidos"]').value;
    const email = document.querySelector('input[name="email"]').value;
    const email_confirm = document.querySelector('input[name="email_confirm"]').value;
    const telefono = document.querySelector('input[name="telefono"]').value;

    if (!nombre.trim()) {
        alert('Por favor, introduce tu nombre');
        return false;
    }
    if (!apellidos.trim()) {
        alert('Por favor, introduce tus apellidos');
        return false;
    }
    if (!email.trim()) {
        alert('Por favor, introduce tu e-mail');
        return false;
    }
    if (email !== email_confirm) {
        alert('Los e-mails no coinciden');
        return false;
    }
    if (!telefono.trim()) {
        alert('Por favor, introduce tu teléfono');
        return false;
    }

    return true;
}

function validateEnvio() {
    const envio = document.querySelector('input[name="envio"]:checked');
    if (!envio) {
        alert('Por favor, selecciona un método de envío');
        return false;
    }

    if (envio.value === 'domicilio') {
        const direccion = document.querySelector('input[name="direccion"]').value;
        const ciudad = document.querySelector('input[name="ciudad"]').value;
        const codigo = document.querySelector('input[name="codigo_postal"]').value;

        if (!direccion.trim() || !ciudad.trim() || !codigo.trim()) {
            alert('Por favor, completa todos los datos de dirección');
            return false;
        }
    }

    return true;
}

function loadPaymentSummary() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    let subtotal = 0;

    cart.forEach(item => {
        subtotal += item.productPrice * item.quantity;
    });

    const iva = subtotal * 0.21;
    const envio = document.querySelector('input[name="envio"]:checked').value === 'domicilio' ? 5 : 0;
    const total = subtotal + iva + envio;

    document.getElementById('summary-subtotal').textContent = subtotal.toFixed(2) + '€';
    document.getElementById('summary-iva').textContent = iva.toFixed(2) + '€';
    document.getElementById('summary-envio').textContent = envio > 0 ? envio + '€' : 'Gratis';
    document.getElementById('summary-total').textContent = total.toFixed(2) + '€';
}

// Mostrar/ocultar dirección según selección
document.querySelectorAll('input[name="envio"]').forEach(input => {
    input.addEventListener('change', function() {
        const showAddress = this.value === 'domicilio';
        document.getElementById('address-section').style.display = showAddress ? 'block' : 'none';
        document.getElementById('city-section').style.display = showAddress ? 'block' : 'none';
    });
});

document
.getElementById('checkout-form')
.addEventListener(
    'submit',
    function () {

        const cart =
            localStorage.getItem(
                'cart'
            );

        document
        .getElementById(
            'cart-input'
        )
        .value = cart;
    }
);
</script>

@endsection
