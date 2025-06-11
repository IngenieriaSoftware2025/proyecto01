<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>Sistema de Celulares</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <a class="navbar-brand" href="/proyecto01/inicio">
                <img src="<?= asset('./images/cit.png') ?>" width="35px'" alt="cit">
                Sistema de Celulares
            </a>
            
            <div class="collapse navbar-collapse" id="navbarToggler">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="margin: 0;">
                    
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/proyecto01/inicio">
                            <i class="bi bi-house-fill me-2"></i>Inicio
                        </a>
                    </li>

                    <!-- MENÚ PARA TODOS LOS USUARIOS AUTENTICADOS -->
                    <?php if(isset($_SESSION['login'])): ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/proyecto01/clientes">
                                <i class="bi bi-person-add me-2"></i>Clientes
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/proyecto01/marcas">
                                <i class="bi bi-phone me-2"></i>Marcas
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/proyecto01/inventario">
                                <i class="bi bi-boxes me-2"></i>Inventario
                            </a>
                        </li>

                        <!-- MENÚ SOLO PARA ADMINISTRADORES -->
                        <?php if($_SESSION['rol'] === 'ADMIN'): ?>
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear me-2"></i>Administración
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark" style="margin: 0;">
                                    <li>
                                        <a class="dropdown-item nav-link text-white" href="/proyecto01/usuarios">
                                            <i class="ms-lg-0 ms-2 bi bi-people me-2"></i>Gestión de Usuarios
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item nav-link text-white" href="/proyecto01/reportes">
                                            <i class="ms-lg-0 ms-2 bi bi-file-earmark-text me-2"></i>Reportes
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item nav-link text-white" href="/proyecto01/configuracion">
                                            <i class="ms-lg-0 ms-2 bi bi-gear-fill me-2"></i>Configuración
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>

                </ul>

                <!-- INFORMACIÓN DEL USUARIO Y LOGOUT -->
                <?php if(isset($_SESSION['login'])): ?>
                    <div class="navbar-nav">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-warning" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars($_SESSION['user']) ?>
                                <span class="badge bg-<?= $_SESSION['rol'] === 'ADMIN' ? 'danger' : 'primary' ?> ms-1">
                                    <?= $_SESSION['rol'] ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="/proyecto01/perfil">
                                        <i class="bi bi-person me-2"></i>Mi Perfil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="/proyecto01/logout">
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

    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">
        <?php echo $contenido; ?>
    </div>
    
    <div class="container-fluid">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                    Sistema de Gestión de Celulares - Comando de Informática y Tecnología, <?= date('Y') ?> &copy;
                </p>
            </div>
        </div>
    </div>

    <!-- Script para verificar sesión automáticamente -->
    <script>
        // Verificar sesión cada 5 minutos si está logueado
        <?php if(isset($_SESSION['login'])): ?>
        setInterval(async () => {
            try {
                const respuesta = await fetch('/proyecto01/verificarSesion');
                const datos = await respuesta.json();

                if (datos.codigo === 0) {
                    alert('Su sesión ha expirado. Será redirigido al login.');
                    window.location.href = '/proyecto01/';
                }
            } catch (error) {
                console.error('Error verificando sesión:', error);
            }
        }, 5 * 60 * 1000); // 5 minutos
        <?php endif; ?>
    </script>
</body>

</html>