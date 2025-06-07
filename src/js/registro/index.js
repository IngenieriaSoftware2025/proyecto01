import DataTable from "datatables.net-bs5";
import { validarFormulario, Toast } from "../funciones";
import { lenguaje } from "../lenguaje";
import Swal from "sweetalert2";

// Referencias a elementos del DOM
const formUsuario = document.getElementById('formUsuario');
const btnGuardar = document.getElementById('BtnGuardar');
const btnLimpiar = document.getElementById('BtnLimpiar');
const btnModificar = document.getElementById('BtnModificar');
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

// Función para mostrar tabla de usuarios con fotografías 
const mostrarTablaUsuarios = (usuarios) => {
    seccion_usuarios.classList.remove('d-none');
    seccion_usuarios.classList.add('fade-in');
    mensaje_sin_datos.classList.add('d-none');
    
    if (tabla_usuarios) {
        tabla_usuarios.destroy();
        document.getElementById('TableUsuarios').innerHTML = '';
    }
    
    tabla_usuarios = new DataTable('#TableUsuarios', {
        data: usuarios,
        language: lenguaje,
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']],
        columns: [
            {
                title: 'ID',
                data: 'usuario_id',
                width: '5%'
            },
            {
                title: 'Foto',
                data: 'usuario_fotografia',
                width: '8%',
                orderable: false,
                render: (data, type, row) => {
                    if (data && data.trim() !== '') {
                        return `<img src="/proyecto01/storage/fotosUsuarios/${data}" 
                                    alt="Foto" class="img-thumbnail rounded-circle foto-usuario" 
                                    style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;"
                                    data-imagen="/proyecto01/storage/fotosUsuarios/${data}"
                                    data-nombre="${row.usuario_nom1} ${row.usuario_ape1}">`;
                    } else {
                        return `<i class="bi bi-person-circle text-muted" style="font-size: 40px;"></i>`;
                    }
                }
            },
            {
                title: 'Nombre Completo',
                data: null,
                render: (data) => `${data.usuario_nom1} ${data.usuario_nom2 || ''} ${data.usuario_ape1} ${data.usuario_ape2 || ''}`.trim(),
                width: '18%'
            },
            {
                title: 'Teléfono',
                data: 'usuario_tel',
                width: '10%'
            },
            {
                title: 'DPI',
                data: 'usuario_dpi',
                width: '12%'
            },
            {
                title: 'Correo',
                data: 'usuario_correo',
                width: '15%'
            },
            {
                title: 'Estado',
                data: 'usuario_situacion',
                width: '8%',
                render: (data) => data == 1 
                    ? '<span class="badge bg-success">Activo</span>'
                    : '<span class="badge bg-danger">Inactivo</span>'
            },
            {
                title: 'Acciones',
                data: 'usuario_id',
                width: '14%',
                orderable: false,
                render: (data, type, row) => {
                    return `<div class='d-flex justify-content-center'>
                                <button class='btn btn-warning btn-sm modificar mx-1' 
                                    data-id="${data}" 
                                    data-nom1="${row.usuario_nom1}"
                                    data-nom2="${row.usuario_nom2 || ''}"
                                    data-ape1="${row.usuario_ape1}"
                                    data-ape2="${row.usuario_ape2 || ''}"
                                    data-tel="${row.usuario_tel}"
                                    data-dpi="${row.usuario_dpi}"
                                    data-direc="${row.usuario_direc}"
                                    data-correo="${row.usuario_correo}">
                                    <i class='bi bi-pencil me-1'></i>Editar
                                </button>
                                <button class='btn btn-danger btn-sm eliminar mx-1' data-id="${data}">
                                    <i class="bi bi-trash me-1"></i>Eliminar
                                </button>
                            </div>`;
                }
            }
        ]
    });
    
    // Event listeners para fotos y botones
    setTimeout(() => {
        // Fotos
        document.querySelectorAll('.foto-usuario').forEach(foto => {
            foto.addEventListener('click', function() {
                mostrarImagenCompleta(this.getAttribute('data-imagen'), this.getAttribute('data-nombre'));
            });
        });
        
        // Botones modificar y eliminar
        document.querySelectorAll('.modificar').forEach(btn => {
            btn.addEventListener('click', llenarFormulario);
        });
        
        document.querySelectorAll('.eliminar').forEach(btn => {
            btn.addEventListener('click', eliminarUsuario);
        });
    }, 100);
};

// Función para mostrar imagen completa en modal
const mostrarImagenCompleta = (rutaImagen, nombreUsuario) => {
    Swal.fire({
        title: `Fotografía de ${nombreUsuario}`,
        imageUrl: rutaImagen,
        imageWidth: 400,
        imageHeight: 400,
        imageAlt: `Foto de ${nombreUsuario}`,
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            image: 'img-fluid rounded'
        },
        didOpen: () => {
            // Agregar evento para cerrar con click en la imagen
            const imagen = Swal.getImage();
            if (imagen) {
                imagen.style.cursor = 'pointer';
                imagen.addEventListener('click', () => {
                    Swal.close();
                });
            }
        }
    });
};

// Función para llenar formulario para modificar
const llenarFormulario = (e) => {
    const datos = e.currentTarget.dataset;
    
    document.getElementById('usuario_id').value = datos.id;
    document.getElementById('usuario_nom1').value = datos.nom1;
    document.getElementById('usuario_nom2').value = datos.nom2;
    document.getElementById('usuario_ape1').value = datos.ape1;
    document.getElementById('usuario_ape2').value = datos.ape2;
    document.getElementById('usuario_tel').value = datos.tel;
    document.getElementById('usuario_dpi').value = datos.dpi;
    document.getElementById('usuario_direc').value = datos.direc;
    document.getElementById('usuario_correo').value = datos.correo;
    
    btnGuardar.classList.add('d-none');
    btnModificar.classList.remove('d-none');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Función para modificar usuario
const modificarUsuario = async (e) => {
    e.preventDefault();
    
    btnModificar.disabled = true;
    btnModificar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Modificando...';
    
    try {
        const body = new FormData(formUsuario);
        const respuesta = await fetch('/proyecto01/registro/modificarAPI', {
            method: 'POST',
            body
        });
        
        const data = await respuesta.json();
        
        if (data.codigo === 1) {
            Toast.fire({ icon: 'success', title: data.mensaje });
            limpiarFormulario();
            buscarUsuarios();
        } else {
            Toast.fire({ icon: 'error', title: data.mensaje });
        }
    } catch (error) {
        Toast.fire({ icon: 'error', title: 'Error de conexión' });
    } finally {
        btnModificar.disabled = false;
        btnModificar.innerHTML = '<i class="bi bi-pencil me-2"></i>Modificar';
    }
};

// Función para eliminar usuario
const eliminarUsuario = async (e) => {
    const id = e.currentTarget.dataset.id;
    
    const confirmacion = await Swal.fire({
        title: '¿Eliminar usuario?',
        text: '¿Estás seguro de eliminar este usuario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    
    if (confirmacion.isConfirmed) {
        try {
            const respuesta = await fetch(`/proyecto01/registro/eliminarAPI?id=${id}`);
            const data = await respuesta.json();
            
            if (data.codigo === 1) {
                Toast.fire({ icon: 'success', title: data.mensaje });
                buscarUsuarios();
            } else {
                Toast.fire({ icon: 'error', title: data.mensaje });
            }
        } catch (error) {
            Toast.fire({ icon: 'error', title: 'Error de conexión' });
        }
    }
};

// Función para limpiar formulario
const limpiarFormulario = () => {
    formUsuario.reset();
    preview_foto.classList.add('d-none');
    limpiarValidacion();
    btnGuardar.classList.remove('d-none');
    btnModificar.classList.add('d-none');
};

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

formUsuario.addEventListener('submit', (e) => {
    if (btnModificar.classList.contains('d-none')) {
        guardarUsuario(e);
    } else {
        modificarUsuario(e);
    }
});

// Event listeners
btnBuscarUsuarios.addEventListener('click', buscarUsuarios);
btnModificar.addEventListener('click', modificarUsuario);
btnLimpiar.addEventListener('click', limpiarFormulario);
usuario_fotografia.addEventListener('change', mostrarVistaPrevia);
console.log('Sistema de registro inicializado correctamente');