import Swal from 'sweetalert2';
import { validarFormulario } from '../funciones';

const FormLogin = document.getElementById('FormLogin');
const BtnIniciar = document.getElementById('BtnIniciar');
const btnText = document.getElementById('btnText');
const btnSpinner = document.getElementById('btnSpinner');
const usuCodigo = document.getElementById('usu_codigo');
const usuPassword = document.getElementById('usu_password');

// Función para mostrar estado de carga
const toggleLoadingState = (isLoading) => {
    if (isLoading) {
        BtnIniciar.disabled = true;
        BtnIniciar.classList.add('loading');
        btnText.textContent = 'Verificando...';
        btnSpinner.classList.remove('d-none');
        FormLogin.style.pointerEvents = 'none';
    } else {
        BtnIniciar.disabled = false;
        BtnIniciar.classList.remove('loading');
        btnText.textContent = 'Iniciar Sesión';
        btnSpinner.classList.add('d-none');
        FormLogin.style.pointerEvents = 'auto';
    }
};

// Función para limpiar estilos de validación
const limpiarValidacion = () => {
    usuCodigo.classList.remove('is-invalid');
    usuPassword.classList.remove('is-invalid');
};

// Función principal de login
const login = async (e) => {
    e.preventDefault();

    limpiarValidacion();

    if (!validarFormulario(FormLogin, [''])) {
        Swal.fire({
            title: "Campos vacíos",
            text: "Debe llenar todos los campos",
            icon: "warning",
            confirmButtonText: "Entendido"
        });
        return;
    }

    // Validar que el código tenga al menos 8 dígitos
    if (usuCodigo.value.length < 6) {
        usuCodigo.classList.add('is-invalid');
        Swal.fire({
            title: "Código inválido",
            text: "El código debe tener al menos 8 dígitos",
            icon: "error",
            confirmButtonText: "Entendido"
        });
        return;
    }

    // Validar que la contraseña tenga al menos 4 caracteres
    if (usuPassword.value.length < 4) {
        usuPassword.classList.add('is-invalid');
        Swal.fire({
            title: "Contraseña muy corta",
            text: "La contraseña debe tener al menos 4 caracteres",
            icon: "error",
            confirmButtonText: "Entendido"
        });
        return;
    }

    toggleLoadingState(true);

    try {
        const body = new FormData(FormLogin);
        const url = '/proyecto01/API/login';
        const config = {
            method: 'POST',
            body
        };

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const { codigo, mensaje, datos } = data;

        console.log('Respuesta del servidor:', data);

        if (codigo === 1) {
            await Swal.fire({
                title: '¡Bienvenido!',
                text: `${mensaje}. Rol: ${datos.rol}`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                background: '#e0f7fa',
                customClass: {
                    title: 'custom-title-class',
                    text: 'custom-text-class'
                }
            });

            FormLogin.reset();
            window.location.href = datos.redirect_url;
        } else {
            toggleLoadingState(false);

            if (mensaje.includes('contraseña') || mensaje.includes('incorrecta')) {
                usuPassword.classList.add('is-invalid');
            }

            if (mensaje.includes('NO EXISTE') || mensaje.includes('usuario')) {
                usuCodigo.classList.add('is-invalid');
            }

            await Swal.fire({
                title: '¡Error!',
                text: mensaje,
                icon: 'error',
                confirmButtonText: 'Intentar nuevamente',
                background: '#ffe6e6',
                customClass: {
                    title: 'custom-title-class',
                    text: 'custom-text-class'
                }
            });

            usuPassword.value = '';
        }

    } catch (error) {
        toggleLoadingState(false);
        console.error('Error en login:', error);

        await Swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Intente nuevamente.',
            icon: 'error',
            confirmButtonText: 'Entendido'
        });
    }
};

// Event listeners
FormLogin.addEventListener('submit', login);

usuCodigo.addEventListener('input', () => {
    usuCodigo.classList.remove('is-invalid');
    // Solo permitir números
    usuCodigo.value = usuCodigo.value.replace(/\D/g, '');
});

usuPassword.addEventListener('input', () => {
    usuPassword.classList.remove('is-invalid');
});

usuCodigo.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        usuPassword.focus();
    }
});

usuPassword.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        login(e);
    }
});

// Verificar sesión cada 5 minutos
setInterval(async () => {
    try {
        const respuesta = await fetch('/proyecto01/verificarSesion');
        const datos = await respuesta.json();

        if (datos.codigo === 0) {
            await Swal.fire({
                icon: 'warning',
                title: 'Sesión expirada',
                text: 'Su sesión ha expirado. Será redirigido al login.',
                confirmButtonText: 'Entendido'
            });
            window.location.href = '/proyecto01/';
        }
    } catch (error) {
        console.error('Error verificando sesión:', error);
    }
}, 5 * 60 * 1000);

console.log('Sistema de login con roles inicializado correctamente');