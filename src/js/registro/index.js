import DataTable from "datatables.net-bs5";
import { validarFormulario, Toast } from "../funciones";
import { lenguaje } from "../lenguaje";
import Swal from "sweetalert2";

// Referencias a elementos del DOM
const formUsuario = document.getElementById('formUsuario');
const btnGuardar = document.getElementById('BtnGuardar');
const btnLimpiar = document.getElementById('BtnLimpiar');
const btnBuscarUsuarios = document.getElementById('BtnBuscarUsuarios');
const usuario_fotografia = document.getElementById('usuario_fotografia');
const preview_foto = document.getElementById('preview_foto');
const seccion_usuarios = document.getElementById('seccion_usuarios');
const contenedor_tabla = document.getElementById('contenedor_tabla');
const mensaje_sin_datos = document.getElementById('mensaje_sin_datos');

// Variables globales
let tabla_usuarios = null;
let usuarios_cargados = false;

// Validación personalizada de campos
const validarCampos = () => {
    const errores = [];
    
    // Validar primer nombre
    if (!formUsuario.usuario_nom1.value.trim()) {
        errores.push('El primer nombre es obligatorio');
    }
    
    // Validar primer apellido
    if (!formUsuario.usuario_ape1.value.trim()) {
        errores.push('El primer apellido es obligatorio');
    }
    
    // Validar teléfono
    const telefono = formUsuario.usuario_tel.value.trim();
    if (!telefono) {
        errores.push('El teléfono es obligatorio');
    } else if (telefono.length !== 8 || !/^\d{8}$/.test(telefono)) {
        errores.push('El teléfono debe tener exactamente 8 dígitos');
    }
    
    // Validar DPI
    const dpi = formUsuario.usuario_dpi.value.trim();
    if (!dpi) {
        errores.push('El DPI es obligatorio');
    } else if (dpi.length !== 13 || !/^\d{13}$/.test(dpi)) {
        errores.push('El DPI debe tener exactamente 13 dígitos');
    }
    
    // Validar dirección
    if (!formUsuario.usuario_direc.value.trim()) {
        errores.push('La dirección es obligatoria');
    }
    
    // Validar correo electrónico
    const correo = formUsuario.usuario_correo.value.trim();
    if (!correo) {
        errores.push('El correo electrónico es obligatorio');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        errores.push('El formato del correo electrónico no es válido');
    }
    
    // Validar contraseña
    const password = formUsuario.usuario_contra.value;
    if (!password) {
        errores.push('La contraseña es obligatoria');
    } else if (password.length < 10) {
        errores.push('La contraseña debe tener al menos 10 caracteres');
    } else {
        // Validar complejidad de contraseña
        if (!/[A-Z]/.test(password)) {
            errores.push('La contraseña debe contener al menos una letra mayúscula');
        }
        if (!/[a-z]/.test(password)) {
            errores.push('La contraseña debe contener al menos una letra minúscula');
        }
        if (!/[0-9]/.test(password)) {
            errores.push('La contraseña debe contener al menos un número');
        }
        if (!/[!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?]/.test(password)) {
            errores.push('La contraseña debe contener al menos un carácter especial');
        }
    }
    
    // Validar confirmación de contraseña
    const confirmar = formUsuario.confirmar_contra.value;
    if (!confirmar) {
        errores.push('Debe confirmar la contraseña');
    } else if (password !== confirmar) {
        errores.push('Las contraseñas no coinciden');
    }
    
    return errores;
};

// Función para mostrar errores
const mostrarErrores = (errores) => {
    const listaErrores = errores.map(error => `• ${error}`).join('<br>');
    
    Swal.fire({
        icon: 'error',
        title: 'Errores en el formulario',
        html: listaErrores,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#dc3545'
    });
};

// Función para limpiar estilos de validación
const limpiarValidacion = () => {
    const inputs = formUsuario.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-invalid', 'is-valid');
    });
};

// Vista previa de imagen
const mostrarVistaPrevia = (event) => {
    const archivo = event.target.files[0];
    
    if (archivo) {
        // Validar tipo de archivo
        const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!tiposPermitidos.includes(archivo.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Tipo de archivo no válido',
                text: 'Solo se permiten archivos JPG, JPEG y PNG',
                confirmButtonText: 'Entendido'
            });
            event.target.value = '';
            preview_foto.classList.add('d-none');
            return;
        }
        
        // Validar tamaño (máximo 2MB)
        const tamañoMaximo = 2 * 1024 * 1024;
        if (archivo.size > tamañoMaximo) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo muy grande',
                text: 'El archivo no puede ser mayor a 2MB',
                confirmButtonText: 'Entendido'
            });
            event.target.value = '';
            preview_foto.classList.add('d-none');
            return;
        }
        
        // Mostrar vista previa
        const reader = new FileReader();
        reader.onload = (e) => {
            preview_foto.src = e.target.result;
            preview_foto.classList.remove('d-none');
        };
        reader.readAsDataURL(archivo);
    } else {
        preview_foto.classList.add('d-none');
    }
};

// Función principal de guardar usuario
const guardarUsuario = async (e) => {
    e.preventDefault();
    
    limpiarValidacion();
    
    // Validar campos
    const errores = validarCampos();
    if (errores.length > 0) {
        mostrarErrores(errores);
        return;
    }
    
    // Deshabilitar botón y mostrar estado de carga
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
    
    try {
        const body = new FormData(formUsuario);
        const url = "/proyecto01/registro/guardar";
        const config = {
            method: 'POST',
            body
        };
        
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const { codigo, mensaje, detalle } = data;
        
        console.log('Respuesta del servidor:', data);
        
        if (codigo === 1) {
            // Éxito
            Toast.fire({
                icon: 'success',
                title: mensaje
            });
            
            formUsuario.reset();
            preview_foto.classList.add('d-none');
            limpiarValidacion();
            
            // Si hay tabla cargada, recargar datos
            if (usuarios_cargados) {
                buscarUsuarios();
            }
            
        } else {
            // Error
            Toast.fire({
                icon: 'error',
                title: mensaje
            });
            
            if (detalle) {
                console.error('Detalle del error:', detalle);
            }
        }
        
    } catch (error) {
        console.error('Error en la petición:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error de conexión con el servidor'
        });
    } finally {
        // Restaurar botón
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = '<i class="bi bi-floppy me-2"></i>Registrar Usuario';
    }
};

// Función para buscar usuarios
const buscarUsuarios = async () => {
    try {
        btnBuscarUsuarios.disabled = true;
        btnBuscarUsuarios.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cargando...';
        
        const respuesta = await fetch('/proyecto01/registro/buscarAPI');
        const data = await respuesta.json();
        
        if (data.codigo === 1) {
            mostrarTablaUsuarios(data.data);
            usuarios_cargados = true;
        } else {
            Toast.fire({
                icon: 'error',
                title: 'Error al cargar usuarios'
            });
        }
        
    } catch (error) {
        console.error('Error al buscar usuarios:', error);
        Toast.fire({
            icon: 'error',
            title: 'Error de conexión al buscar usuarios'
        });
    } finally {
        btnBuscarUsuarios.disabled = false;
        btnBuscarUsuarios.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Usuarios';
    }
};

// Función para mostrar tabla de usuarios
const mostrarTablaUsuarios = (usuarios) => {
    // Mostrar sección de usuarios
    seccion_usuarios.classList.remove('d-none');
    seccion_usuarios.classList.add('fade-in');
    
    // Ocultar mensaje sin datos
    mensaje_sin_datos.classList.add('d-none');
    
    // Destruir tabla existente si existe
    if (tabla_usuarios) {
        tabla_usuarios.destroy();
        document.getElementById('TableUsuarios').innerHTML = '';
    }
    
    // Configurar DataTable
    tabla_usuarios = new DataTable('#TableUsuarios', {
        data: usuarios,
        language: lenguaje,
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']], // Ordenar por ID descendente
        columns: [
            {
                title: 'ID',
                data: 'usuario_id',
                width: '5%'
            },
            {
                title: 'Nombre Completo',
                data: null,
                render: (data) => {
                    return `${data.usuario_nom1} ${data.usuario_nom2 || ''} ${data.usuario_ape1} ${data.usuario_ape2 || ''}`.trim();
                },
                width: '25%'
            },
            {
                title: 'Teléfono',
                data: 'usuario_tel',
                width: '10%'
            },
            {
                title: 'DPI',
                data: 'usuario_dpi',
                width: '15%'
            },
            {
                title: 'Correo',
                data: 'usuario_correo',
                width: '20%'
            },
            {
                title: 'Dirección',
                data: 'usuario_direc',
                width: '20%',
                render: (data) => {
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
                }
            },
            {
                title: 'Estado',
                data: 'usuario_situacion',
                width: '5%',
                render: (data) => {
                    return data == 1 
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                }
            }
        ]
    });
};

// Función para limpiar formulario
const limpiarFormulario = () => {
    formUsuario.reset();
    preview_foto.classList.add('d-none');
    limpiarValidacion();
};

// Event listeners
formUsuario.addEventListener('submit', guardarUsuario);
btnBuscarUsuarios.addEventListener('click', buscarUsuarios);
btnLimpiar.addEventListener('click', limpiarFormulario);
usuario_fotografia.addEventListener('change', mostrarVistaPrevia);

// Validación en tiempo real para campos específicos
formUsuario.usuario_tel.addEventListener('input', (e) => {
    // Solo permitir números
    e.target.value = e.target.value.replace(/\D/g, '');
    if (e.target.value.length > 8) {
        e.target.value = e.target.value.slice(0, 8);
    }
});

formUsuario.usuario_dpi.addEventListener('input', (e) => {
    // Solo permitir números
    e.target.value = e.target.value.replace(/\D/g, '');
    if (e.target.value.length > 13) {
        e.target.value = e.target.value.slice(0, 13);
    }
});

// Validación de confirmación de contraseña en tiempo real
formUsuario.confirmar_contra.addEventListener('input', () => {
    const password = formUsuario.usuario_contra.value;
    const confirmar = formUsuario.confirmar_contra.value;
    
    if (confirmar && password !== confirmar) {
        formUsuario.confirmar_contra.classList.add('is-invalid');
    } else {
        formUsuario.confirmar_contra.classList.remove('is-invalid');
    }
});

console.log('Sistema de registro inicializado correctamente');