<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <title>Sistema de Celulares</title>

    <!-- Estilos elegantes y sencillos -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-light: #6366f1;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-600: #475569;
            --gray-800: #1e293b;
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--gray-50);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--gray-800);
        }

        /* Navbar limpio y elegante */
        .navbar-custom {
            background-color: var(--white) !important;
            border-bottom: 1px solid var(--gray-200);
            box-shadow: var(--shadow);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .navbar-brand img {
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .nav-link {
            color: var(--gray-600) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            margin: 0 0.25rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: var(--gray-100);
            color: var(--primary-color) !important;
        }

        .nav-link.active {
            background-color: var(--primary-color);
            color: var(--white) !important;
        }

        .nav-link i {
            color: var(--secondary-color);
            transition: color 0.2s ease;
        }

        .nav-link:hover i,
        .nav-link.active i {
            color: inherit;
        }

        /* Dropdown sencillo */
        .dropdown-menu {
            background-color: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            padding: 0.5rem;
            margin-top: 0.25rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            color: var(--gray-600);
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: var(--gray-100);
            color: var(--primary-color);
        }

        /* Badge simple */
        .badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .bg-danger {
            background-color: var(--danger-color) !important;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        /* Usuario en navbar */
        .user-info {
            color: var(--gray-600) !important;
            font-weight: 500;
        }

        .user-info:hover {
            color: var(--primary-color) !important;
        }

        /* Botón del navbar */
        .navbar-toggler {
            border: 1px solid var(--gray-200);
            border-radius: 6px;
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        /* Barra de progreso minimalista */
        .progress {
            height: 3px;
            background-color: var(--gray-200);
        }

        .progress-bar {
            background-color: var(--primary-color);
        }

        /* Contenedor principal */
        .main-container {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin: 2rem 1rem;
        }

        /* Footer discreto */
        .footer {
            color: var(--secondary-color);
            font-size: 0.875rem;
            padding: 1rem 0;
        }

        /* Notificación elegante */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: var(--white);
            border: 1px solid var(--gray-200);
            border-left: 4px solid var(--danger-color);
            color: var(--gray-800);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 1rem 0.5rem;
                padding: 1.5rem;
            }

            .navbar-brand {
                font-size: 1.125rem;
            }
        }

        /* Estados de focus para accesibilidad */
        .nav-link:focus,
        .dropdown-item:focus,
        .navbar-toggler:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand" href="/proyecto01/inicio">
                <img src="<?= asset('./images/cit.png') ?>" width="32" height="32" alt="CIT">
                Sistema de Celulares
            </a>

            <div class="collapse navbar-collapse" id="navbarToggler">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link" href="/proyecto01/inicio">
                            <i class="bi bi-house me-2"></i>Inicio
                        </a>
                    </li>

                    <!-- MENÚ PARA USUARIOS AUTENTICADOS -->
                    <?php if (isset($_SESSION['login'])): ?>

                        <li class="nav-item">
                            <a class="nav-link" href="/proyecto01/clientes">
                                <i class="bi bi-people me-2"></i>Clientes
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/proyecto01/marcas">
                                <i class="bi bi-phone me-2"></i>Marcas
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/proyecto01/inventario">
                                <i class="bi bi-box-seam me-2"></i>Inventario
                            </a>
                        </li>

                        <?php if ($_SESSION['rol'] === 'ADMIN'): ?>
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="/proyecto01/ventas">
                                    <i class="bi bi-cart3 me-2"></i>Ventas
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link" href="/proyecto01/reparaciones">
                                <i class="bi bi-tools me-2"></i>Reparaciones
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/proyecto01/reportes">
                                <i class="bi bi-graph-up me-2"></i>Reportes
                            </a>
                        </li>

                        <!-- MENÚ ADMINISTRADOR -->
                        <?php if ($_SESSION['rol'] === 'ADMIN'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear me-2"></i>Administración
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="/proyecto01/usuarios">
                                            <i class="bi bi-person-gear me-2"></i>Usuarios
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/proyecto01/reportes">
                                            <i class="bi bi-file-text me-2"></i>Reportes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/proyecto01/configuracion">
                                            <i class="bi bi-sliders me-2"></i>Configuración
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>

                    <?php endif; ?>

                </ul>

                <!-- INFORMACIÓN DEL USUARIO -->
                <?php if (isset($_SESSION['login'])): ?>
                    <div class="navbar-nav">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-info" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars($_SESSION['user']) ?>
                                <span class="badge bg-<?= $_SESSION['rol'] === 'ADMIN' ? 'danger' : 'primary' ?> ms-2">
                                    <?= $_SESSION['rol'] ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="/proyecto01/perfil">
                                        <i class="bi bi-person me-2"></i>Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/proyecto01/logout">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </nav>

    <div class="progress fixed-bottom">
        <div class="progress-bar progress-bar-animated" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <div class="container-fluid" style="min-height: 85vh">
        <div class="main-container">
            <?php echo $contenido; ?>
        </div>
    </div>

    <footer class="footer text-center">
        <div class="container-fluid">
            <p class="mb-0">
                Sistema de Gestión de Celulares - Comando de Informática y Tecnología <?= date('Y') ?> &copy;
            </p>
        </div>
    </footer>

    <script>
        // Verificar sesión cada 5 minutos
        <?php if (isset($_SESSION['login'])): ?>
            setInterval(async () => {
                try {
                    const respuesta = await fetch('/proyecto01/verificarSesion');
                    const datos = await respuesta.json();

                    if (datos.codigo === 0) {
                        // Mostrar notificación elegante
                        const notification = document.createElement('div');
                        notification.className = 'notification';
                        notification.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="bi bi-exclamation-triangle-fill" style="color: var(--danger-color);"></i>
                            <span>Su sesión ha expirado. Redirigiendo...</span>
                        </div>
                    `;
                        document.body.appendChild(notification);

                        setTimeout(() => {
                            window.location.href = '/proyecto01/';
                        }, 2000);
                    }
                } catch (error) {
                    console.error('Error verificando sesión:', error);
                }
            }, 5 * 60 * 1000);
        <?php endif; ?>
    </script>
</body>

</html>