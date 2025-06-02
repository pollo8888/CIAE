<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMSS - Sistema de Autenticación SIAIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!-- Contenedor Principal -->
    <div class="login-wrapper">
        <!-- Header del Sistema -->


        <!-- Tarjeta de Login -->
        <div class="login-card">
            <!-- Lado Izquierdo - Información -->
            <div class="login-info">
                <div class="logo-container">
                    <div class="imss-logo">
                        <svg width="35" height="35" viewBox="0 0 100 100" fill="white">
                            <rect x="15" y="40" width="20" height="40"/>
                            <rect x="40" y="20" width="20" height="60"/>
                            <rect x="65" y="30" width="20" height="50"/>
                        </svg>
                    </div>
                </div>
                
                <div class="info-content">
                    <h2>Bienvenido a CIAE</h2>
                    <p>Sistema Integral de Administración de Infraestructura y Servicios del IMSS</p>
                    
                    <div class="features">
                        <div class="feature">
                            <i class="fas fa-shield-alt"></i>
                            <div class="feature-text">
                                <h3>Seguridad Avanzada</h3>
                                <p>Protección de datos con encriptación </p>
                            </div>
                        </div>
                        <div class="feature">
                            <i class="fas fa-chart-line"></i>
                            <div class="feature-text">
                                <h3>Monitoreo en Tiempo Real</h3>
                                <p>Supervise el estado de todos los servidores 24/7</p>
                            </div>
                        </div>
                        <div class="feature">
                            <i class="fas fa-users"></i>
                            <div class="feature-text">
                                <h3>Gestión Centralizada</h3>
                                <p>Control total desde una única plataforma</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lado Derecho - Formulario -->
            <div class="login-form-section">
                <div class="form-header">
                    <h3>Iniciar Sesión</h3>
                    <p>Ingrese sus credenciales para acceder</p>
                </div>

                <!-- Mensaje de Alerta -->
                <div class="alert alert-error" id="alert-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Usuario o contraseña incorrectos</span>
                </div>

                <!-- Tabs -->
                <div class="login-tabs">
                    <button class="tab-btn active" data-tab="employee">
                        <i class="fas fa-user"></i>
                        <span>Empleado</span>
                    </button>
                    <button class="tab-btn" data-tab="admin">
                        <i class="fas fa-user-shield"></i>
                        <span>Administrador</span>
                    </button>
                </div>

                <!-- Formulario Empleado -->
                <form class="login-form active" id="employee-form">
                    <div class="form-group">
                        <label for="employee-id" class="form-label">Matricula</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="text" 
                                id="employee-id" 
                                class="form-input" 
                                placeholder="12345"
                                required
                                autocomplete="username"
                            >
                            <i class="fas fa-id-badge input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="employee-password" class="form-label">Contraseña</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="password" 
                                id="employee-password" 
                                class="form-input" 
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('employee-password')" aria-label="Mostrar contraseña">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="form-checkbox">
                            <input type="checkbox" id="remember-employee">
                            <span>Recordarme</span>
                        </label>
                        <a href="#" class="forgot-link" onclick="showForgotPassword()">¿Olvidó su contraseña?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        <span class="loading-spinner"></span>
                        <span class="btn-text">Iniciar Sesión</span>
                    </button>
                </form>

                <!-- Formulario Administrador -->
                <form class="login-form" id="admin-form">
                    <div class="form-group">
                        <label for="admin-username" class="form-label">Usuario</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="text" 
                                id="admin-username" 
                                class="form-input" 
                                placeholder="admin"
                                required
                                autocomplete="username"
                            >
                            <i class="fas fa-user-shield input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="admin-password" class="form-label">Contraseña</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="password" 
                                id="admin-password" 
                                class="form-input" 
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('admin-password')" aria-label="Mostrar contraseña">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="form-checkbox">
                            <input type="checkbox" id="remember-admin">
                            <span>Recordarme</span>
                        </label>
                        <a href="#" class="forgot-link" onclick="showForgotPassword()">¿Necesita ayuda?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        <span class="loading-spinner"></span>
                        <span class="btn-text">Acceder al Sistema</span>
                    </button>
                </form>

                <!-- Footer del formulario -->
                <div class="login-footer">
                    <p>¿Primera vez? <a href="#">Solicitar acceso</a></p>
                    <div class="security-info">
                        <i class="fas fa-lock"></i>
                        <span>Conexión segura SSL/TLS</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Recuperación -->
    <div class="modal" id="forgot-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Recuperar Contraseña</h3>
                <button class="modal-close" onclick="closeForgotPassword()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Ingrese su número de empleado o correo electrónico para recibir instrucciones.</p>
                
                <form id="forgot-form">
                    <div class="form-group">
                        <label for="recovery-input" class="form-label">Número de Empleado o Email</label>
                        <div class="form-input-wrapper">
                            <input 
                                type="text" 
                                id="recovery-input" 
                                class="form-input" 
                                placeholder="12345 o correo@imss.gob.mx"
                                required
                            >
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-paper-plane"></i>
                        <span>Enviar Instrucciones</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Inferior -->
    <footer class="page-footer">
        <div class="footer-links">
            <a href="#">Ayuda</a>
            <a href="#">Términos</a>
            <a href="#">Privacidad</a>
            <a href="#">Contacto</a>
        </div>
        <div class="system-status">
            <span class="status-dot"></span>
            <span>Sistema operativo</span>
        </div>
        <div>
            © 2024 IMSS - Versión 2.1.0
        </div>
    </footer>

    <script>
        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            initializeEventListeners();
            checkRememberedCredentials();
            
            // Demo: mostrar credenciales de prueba
            console.log('Credenciales de prueba:');
            console.log('Empleado: 12345 / imss2024');
            console.log('Admin: admin / Admin@2024');
        });

        function initializeEventListeners() {
            // Tabs
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', () => switchTab(btn.dataset.tab));
            });

            // Formularios
            document.getElementById('employee-form').addEventListener('submit', handleEmployeeLogin);
            document.getElementById('admin-form').addEventListener('submit', handleAdminLogin);
            document.getElementById('forgot-form').addEventListener('submit', handleForgotPassword);

            // Limpiar errores al escribir
            document.querySelectorAll('.form-input').forEach(input => {
                input.addEventListener('input', () => clearError());
            });
        }

        // Cambiar tab
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.tab === tab);
            });

            document.querySelectorAll('.login-form').forEach(form => {
                form.classList.toggle('active', form.id === `${tab}-form`);
            });

            clearError();
        }

        // Toggle contraseña
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Login de empleado
        async function handleEmployeeLogin(e) {
            e.preventDefault();

            const employeeId = document.getElementById('employee-id').value;
            const password = document.getElementById('employee-password').value;
            const remember = document.getElementById('remember-employee').checked;

            showLoading('employee-form');

            const formData = new FormData();
            formData.append("tipo", "empleado");
            formData.append("usuario", employeeId); // aquí 'usuario' se refiere a la matrícula
            formData.append("contrasena", password);

            const response = await fetch("funciones/login.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

if (data.success) {
    if (remember) {
        localStorage.setItem('imss_employee', employeeId);
    }
    redirectToSystem(data.rol);
} else {
                showError();
                hideLoading('employee-form');
            }
        }


        // Login de administrador
        async function handleAdminLogin(e) {
            e.preventDefault();

            const username = document.getElementById('admin-username').value;
            const password = document.getElementById('admin-password').value;
            const remember = document.getElementById('remember-admin').checked;

            showLoading('admin-form');

            const formData = new FormData();
            formData.append("tipo", "admin");
            formData.append("usuario", username);
            formData.append("contrasena", password);

            const response = await fetch("funciones/login.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

if (data.success) {
    if (remember) {
        localStorage.setItem('imss_admin', username);
    }
    redirectToSystem(data.rol);
} else {
                showError();
                hideLoading('admin-form');
            }
        }


        // Mostrar loading
        function showLoading(formId) {
            const form = document.getElementById(formId);
            const btn = form.querySelector('.btn-login');
            btn.classList.add('loading');
            btn.disabled = true;
        }

        // Ocultar loading
        function hideLoading(formId) {
            const form = document.getElementById(formId);
            const btn = form.querySelector('.btn-login');
            btn.classList.remove('loading');
            btn.disabled = false;
        }

        // Mostrar error
        function showError() {
            const alert = document.getElementById('alert-message');
            alert.classList.add('show');
            
            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }

        // Limpiar error
        function clearError() {
            document.getElementById('alert-message').classList.remove('show');
        }

        // Redireccionar al sistema
        function redirectToSystem(rol) {
            const alert = document.getElementById('alert-message');
            alert.classList.remove('alert-error');
            alert.classList.add('alert-success');
            alert.innerHTML = '<i class="fas fa-check-circle"></i><span>Acceso autorizado. Redirigiendo...</span>';
            alert.classList.add('show');

            setTimeout(() => {
                if (rol === 'administrador') {
                    window.location.href = 'admin/inicio.php';
                } else {
                    window.location.href = 'usuario/inicio.php';
                }
            }, 1500);
        }


        // Modal de recuperación
        function showForgotPassword() {
            document.getElementById('forgot-modal').classList.add('active');
        }

        function closeForgotPassword() {
            document.getElementById('forgot-modal').classList.remove('active');
        }

        // Manejar recuperación
        function handleForgotPassword(e) {
            e.preventDefault();
            
            const input = document.getElementById('recovery-input').value;
            
            if (!input) return;

            const btn = e.target.querySelector('.btn-login');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Enviando...</span>';
            btn.disabled = true;

            setTimeout(() => {
                closeForgotPassword();
                
                const alert = document.getElementById('alert-message');
                alert.classList.remove('alert-error');
                alert.classList.add('alert-success');
                alert.innerHTML = '<i class="fas fa-check-circle"></i><span>Instrucciones enviadas a su correo</span>';
                alert.classList.add('show');
                
                // Reset form
                document.getElementById('forgot-form').reset();
                btn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Enviar Instrucciones</span>';
                btn.disabled = false;
            }, 2000);
        }

        // Verificar credenciales guardadas
        function checkRememberedCredentials() {
            const employeeId = localStorage.getItem('imss_employee');
            const adminUser = localStorage.getItem('imss_admin');

            if (employeeId) {
                document.getElementById('employee-id').value = employeeId;
                document.getElementById('remember-employee').checked = true;
            } else if (adminUser) {
                switchTab('admin');
                document.getElementById('admin-username').value = adminUser;
                document.getElementById('remember-admin').checked = true;
            }
        }

        // Cerrar modal con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeForgotPassword();
            }
        });

        // Click fuera del modal para cerrar
        document.getElementById('forgot-modal').addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                closeForgotPassword();
            }
        });
    </script>
</body>
</html>

