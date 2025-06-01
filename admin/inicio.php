<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMSS - Sistema de Monitoreo de Servidores SIAIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/inicio.css">
</head>
<body>
    <!-- Partículas de Fondo -->
    <div class="particles" id="particles"></div>

    <!-- Header Principal -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo-section">
                <div class="imss-logo">
                    <svg width="50" height="50" viewBox="0 0 100 100">
                        <rect x="10" y="40" width="20" height="40" fill="var(--imss-verde)"/>
                        <rect x="40" y="20" width="20" height="60" fill="var(--imss-verde)"/>
                        <rect x="70" y="30" width="20" height="50" fill="var(--imss-verde)"/>
                    </svg>
                </div>
                <div class="header-text">
                    <h1 class="gradient-text">Instituto Mexicano del Seguro Social</h1>
                    <p>Sistema Integral de Monitoreo de Servidores SIAIS</p>
                </div>
            </div>
            <div class="header-actions">
                <div class="notification-bell tooltip" data-tooltip="3 nuevas notificaciones">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count">3</span>
                </div>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <div class="user-details">
                        <div class="name">Dr. López García</div>
                        <div class="role">Administrador de Sistemas</div>
                    </div>
                </div>
                <button class="btn-refresh interactive" onclick="refreshAllServers()">
                    <i class="fas fa-sync-alt"></i>
                    <span>Actualizar Todo</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Navegación Principal -->
    <nav class="main-nav">
        <div class="nav-container">
            <ul class="nav-menu">
                <li class="nav-item active">
                    <a href="#dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#servers">
                        <i class="fas fa-server"></i>
                        <span>Servidores</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#databases">
                        <i class="fas fa-database"></i>
                        <span>Bases de Datos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#reports">
                        <i class="fas fa-chart-line"></i>
                        <span>Reportes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#alerts">
                        <i class="fas fa-bell"></i>
                        <span>Alertas</span>
                        <span class="badge">3</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#logs">
                        <i class="fas fa-file-alt"></i>
                        <span>Logs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#config">
                        <i class="fas fa-cog"></i>
                        <span>Configuración</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="main-content">
        <!-- Panel de Estadísticas -->
        <section class="stats-panel">
            <div class="stats-container">
                <div class="stat-card online hover-lift">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Servidores Activos</h3>
                        <p class="stat-number" id="servers-online">0</p>
                        <span class="stat-label">Operando correctamente</span>
                    </div>
                    <div class="stat-chart">
                        <canvas id="online-chart"></canvas>
                    </div>
                </div>

                <div class="stat-card offline hover-lift">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Servidores Inactivos</h3>
                        <p class="stat-number" id="servers-offline">0</p>
                        <span class="stat-label">Sin conexión</span>
                    </div>
                    <div class="stat-chart">
                        <canvas id="offline-chart"></canvas>
                    </div>
                </div>

                <div class="stat-card warning hover-lift">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Advertencias</h3>
                        <p class="stat-number" id="servers-warning">0</p>
                        <span class="stat-label">Requieren atención</span>
                    </div>
                    <div class="stat-chart">
                        <canvas id="warning-chart"></canvas>
                    </div>
                </div>

                <div class="stat-card total hover-lift">
                    <div class="stat-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Servidores</h3>
                        <p class="stat-number" id="servers-total">0</p>
                        <span class="stat-label">En monitoreo</span>
                    </div>
                    <div class="stat-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" id="uptime-progress"></div>
                        </div>
                        <span class="progress-label">98.5% Uptime mensual</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filtros y Búsqueda -->
        <section class="filters-section fade-in">
            <div class="filters-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-servers" placeholder="Buscar por IP, nombre, ubicación o estado...">
                </div>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">
                        <i class="fas fa-th"></i>
                        Todos
                    </button>
                    <button class="filter-btn" data-filter="online">
                        <i class="fas fa-check"></i>
                        En línea
                    </button>
                    <button class="filter-btn" data-filter="offline">
                        <i class="fas fa-times"></i>
                        Fuera de línea
                    </button>
                    <button class="filter-btn" data-filter="warning">
                        <i class="fas fa-exclamation"></i>
                        Advertencias
                    </button>
                </div>
                <div class="view-options">
                    <button class="view-btn active tooltip" data-view="grid" data-tooltip="Vista de cuadrícula">
                        <i class="fas fa-th-large"></i>
                    </button>
       
                </div>
            </div>
        </section>

        <!-- Grid de Servidores -->
        <section class="servers-grid" id="servers-container">
            <!-- Los servidores se cargarán dinámicamente aquí -->
        </section>

        <!-- Modal de Detalles del Servidor -->
        <div class="modal" id="server-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Información Detallada del Servidor</h2>
                    <button class="modal-close" onclick="closeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-tabs">
                        <button class="modal-tab active" data-tab="general">General</button>
                        <button class="modal-tab" data-tab="performance">Rendimiento</button>
                        <button class="modal-tab" data-tab="database">Base de Datos</button>
                        <button class="modal-tab" data-tab="logs">Registros</button>
                        <button class="modal-tab" data-tab="config">Configuración</button>
                    </div>
                    <div class="modal-content-area" id="modal-content-area">
                        <!-- Contenido dinámico del modal -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Alertas Flotante -->
        <div class="alerts-panel collapsed" id="alerts-panel">
            <div class="alerts-header" onclick="toggleAlerts()">
                <h3><i class="fas fa-bell"></i> Centro de Alertas</h3>
                <button class="alerts-close">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="alerts-content">
                <div class="alert-item error">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">Servidor Crítico Sin Respuesta</div>
                        <div class="alert-message">El servidor 192.168.1.105 no responde hace 15 minutos</div>
                        <div class="alert-time">Hace 2 minutos</div>
                    </div>
                </div>
                <div class="alert-item warning">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">Alto Uso de CPU</div>
                        <div class="alert-message">Servidor 192.168.1.203 con 89% de uso de CPU</div>
                        <div class="alert-time">Hace 5 minutos</div>
                    </div>
                </div>
                <div class="alert-item info">
                    <div class="alert-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="alert-content">
                        <div class="alert-title">Mantenimiento Programado</div>
                        <div class="alert-message">Actualización de seguridad programada para esta noche</div>
                        <div class="alert-time">Hace 1 hora</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>Sistema SIAIS</h4>
                <p>Plataforma integral de monitoreo y gestión de infraestructura tecnológica del Instituto Mexicano del Seguro Social.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Enlaces Rápidos</h4>
                <ul>
                    <li><a href="#"><i class="fas fa-angle-right"></i> Manual de Usuario</a></li>
                    <li><a href="#"><i class="fas fa-angle-right"></i> Guía de Administración</a></li>
                    <li><a href="#"><i class="fas fa-angle-right"></i> Soporte Técnico</a></li>
                    <li><a href="#"><i class="fas fa-angle-right"></i> Reportar Problema</a></li>
                    <li><a href="#"><i class="fas fa-angle-right"></i> Actualizaciones del Sistema</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contacto de Emergencia</h4>
                <p><i class="fas fa-phone"></i> Línea directa: 01 800 623 2323</p>
                <p><i class="fas fa-mobile-alt"></i> Móvil: +52 55 1234 5678</p>
                <p><i class="fas fa-envelope"></i> soporte.siais@imss.gob.mx</p>
                <p><i class="fas fa-clock"></i> Disponible 24/7</p>
            </div>
            <div class="footer-section">
                <h4>Estado del Sistema</h4>
                <div class="system-status">
                    <span class="status-indicator online"></span>
                    <span>Todos los sistemas operativos</span>
                </div>
                <p class="last-check">Última verificación: <span id="last-check-time">Hace 30 segundos</span></p>
                <button class="btn-check-now">Verificar Ahora</button>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2024 Instituto Mexicano del Seguro Social. Todos los derechos reservados. | Desarrollado por el Departamento de Sistemas</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuración de servidores
        const servers = [
            { id: 1, name: 'SIAIS-MX-01', ip: '192.168.1.101', location: 'Ciudad de México', status: 'online', cpu: 45, memory: 60, disk: 72 },
            { id: 2, name: 'SIAIS-GDL-01', ip: '192.168.1.102', location: 'Guadalajara', status: 'online', cpu: 32, memory: 55, disk: 68 },
            { id: 3, name: 'SIAIS-MTY-01', ip: '192.168.1.103', location: 'Monterrey', status: 'warning', cpu: 78, memory: 82, disk: 45 },
            { id: 4, name: 'SIAIS-PUE-01', ip: '192.168.1.104', location: 'Puebla', status: 'offline', cpu: 0, memory: 0, disk: 0 },
            { id: 5, name: 'SIAIS-TIJ-01', ip: '192.168.1.105', location: 'Tijuana', status: 'online', cpu: 23, memory: 38, disk: 54 },
            { id: 6, name: 'SIAIS-MER-01', ip: '192.168.1.106', location: 'Mérida', status: 'online', cpu: 56, memory: 71, disk: 83 },
            { id: 7, name: 'SIAIS-OAX-01', ip: '192.168.1.107', location: 'Oaxaca', status: 'warning', cpu: 89, memory: 76, disk: 91 },
            { id: 8, name: 'SIAIS-VER-01', ip: '192.168.1.108', location: 'Veracruz', status: 'online', cpu: 41, memory: 52, disk: 67 },
            { id: 9, name: 'SIAIS-QRO-01', ip: '192.168.1.109', location: 'Querétaro', status: 'online', cpu: 38, memory: 45, disk: 59 },
            { id: 10, name: 'SIAIS-CHH-01', ip: '192.168.1.110', location: 'Chihuahua', status: 'offline', cpu: 0, memory: 0, disk: 0 }
        ];

        // Variables globales
        let currentView = 'grid';
        let currentFilter = 'all';
        let selectedServer = null;

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            createParticles();
            startRealTimeUpdates();
        });

        // Función principal de inicialización
        function initializeApp() {
            updateStats();
            renderServers();
            initializeEventListeners();
            initializeCharts();
            updateLastCheckTime();
        }

        // Crear partículas de fondo
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.width = Math.random() * 10 + 5 + 'px';
                particle.style.height = particle.style.width;
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 20 + 's';
                particle.style.animationDuration = (Math.random() * 20 + 20) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Actualizar estadísticas
        function updateStats() {
            const online = servers.filter(s => s.status === 'online').length;
            const offline = servers.filter(s => s.status === 'offline').length;
            const warning = servers.filter(s => s.status === 'warning').length;
            const total = servers.length;

            animateNumber('servers-online', online);
            animateNumber('servers-offline', offline);
            animateNumber('servers-warning', warning);
            animateNumber('servers-total', total);

            // Actualizar barra de progreso
            const uptimePercentage = (online / total) * 100;
            document.getElementById('uptime-progress').style.width = uptimePercentage + '%';
        }

        // Animar números
        function animateNumber(elementId, targetValue) {
            const element = document.getElementById(elementId);
            const startValue = parseInt(element.textContent) || 0;
            const duration = 1000;
            const increment = (targetValue - startValue) / (duration / 16);
            let currentValue = startValue;

            const animation = setInterval(() => {
                currentValue += increment;
                if ((increment > 0 && currentValue >= targetValue) || 
                    (increment < 0 && currentValue <= targetValue)) {
                    currentValue = targetValue;
                    clearInterval(animation);
                }
                element.textContent = Math.round(currentValue);
            }, 16);
        }

        // Renderizar servidores
        function renderServers() {
            const container = document.getElementById('servers-container');
            container.innerHTML = '';

            const filteredServers = filterServers(servers);

            if (currentView === 'grid') {
                renderGridView(filteredServers, container);
            } else {
                renderListView(filteredServers, container);
            }

            // Agregar animaciones de entrada
            container.querySelectorAll('.server-card, .list-item').forEach((item, index) => {
                item.style.animationDelay = (index * 0.1) + 's';
                item.classList.add('slide-up');
            });
        }

        // Filtrar servidores
        function filterServers(servers) {
            if (currentFilter === 'all') return servers;
            return servers.filter(server => server.status === currentFilter);
        }

        // Vista de cuadrícula
        function renderGridView(servers, container) {
            servers.forEach(server => {
                const card = createServerCard(server);
                container.appendChild(card);
            });
        }

        // Crear tarjeta de servidor
        function createServerCard(server) {
            const card = document.createElement('div');
            card.className = `server-card ${server.status} hover-lift interactive`;
            card.onclick = () => showServerDetails(server);

            card.innerHTML = `
                <div class="server-header">
                    <div class="server-info">
                        <h3>${server.name}</h3>
                        <p class="ip">${server.ip}</p>
                    </div>
                    <div class="server-status ${server.status}">
                        <span class="status-dot"></span>
                        ${getStatusText(server.status)}
                    </div>
                </div>
                <div class="server-details">
                    <div class="detail-item">
                        <span class="detail-label">Ubicación</span>
                        <span class="detail-value">${server.location}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Base de Datos</span>
                        <span class="detail-value">DBSIAIS</span>
                    </div>
                </div>
                <div class="server-metrics">
                    <div class="metric">
                        <div class="metric-value">${server.cpu}%</div>
                        <div class="metric-label">CPU</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value">${server.memory}%</div>
                        <div class="metric-label">RAM</div>
                    </div>
                    <div class="metric">
                        <div class="metric-value">${server.disk}%</div>
                        <div class="metric-label">Disco</div>
                    </div>
                </div>
                <div class="server-actions">
                    <button class="action-btn primary" onclick="event.stopPropagation(); checkConnection(${server.id})">
                        <i class="fas fa-sync"></i> Verificar
                    </button>
                    <button class="action-btn secondary" onclick="event.stopPropagation(); showServerConfig(${server.id})">
                        <i class="fas fa-cog"></i> Config
                    </button>
                </div>
            `;

            return card;
        }

        // Obtener texto de estado
        function getStatusText(status) {
            const statusMap = {
                online: 'En línea',
                offline: 'Sin conexión',
                warning: 'Advertencia'
            };
            return statusMap[status] || status;
        }

        // Mostrar detalles del servidor
        function showServerDetails(server) {
            selectedServer = server;
            const modal = document.getElementById('server-modal');
            modal.classList.add('active');
            
            // Actualizar contenido del modal
            updateModalContent('general');
        }

        // Actualizar contenido del modal
        function updateModalContent(tab) {
            const contentArea = document.getElementById('modal-content-area');
            
            switch(tab) {
                case 'general':
                    contentArea.innerHTML = `
                        <div class="modal-info-grid">
                            <div class="info-section">
                                <h3>Información General</h3>
                                <div class="info-row">
                                    <span class="info-label">Nombre del Servidor:</span>
                                    <span class="info-value">${selectedServer.name}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Dirección IP:</span>
                                    <span class="info-value">${selectedServer.ip}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Ubicación:</span>
                                    <span class="info-value">${selectedServer.location}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Estado:</span>
                                    <span class="info-value status-badge ${selectedServer.status}">${getStatusText(selectedServer.status)}</span>
                                </div>
                            </div>
                            <div class="info-section">
                                <h3>Configuración de Base de Datos</h3>
                                <div class="info-row">
                                    <span class="info-label">Motor:</span>
                                    <span class="info-value">Microsoft SQL Server 2019</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Base de Datos:</span>
                                    <span class="info-value">DBSIAIS</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Usuario:</span>
                                    <span class="info-value">simf_siais</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Puerto:</span>
                                    <span class="info-value">1433</span>
                                </div>
                            </div>
                        </div>
                    `;
                    break;
                    
                case 'performance':
                    contentArea.innerHTML = `
                        <div class="performance-charts">
                            <canvas id="performance-chart"></canvas>
                        </div>
                        <div class="performance-metrics">
                            <div class="metric-card">
                                <h4>CPU</h4>
                                <div class="metric-display">${selectedServer.cpu}%</div>
                                <div class="metric-bar">
                                    <div class="metric-fill" style="width: ${selectedServer.cpu}%; background: ${getMetricColor(selectedServer.cpu)}"></div>
                                </div>
                            </div>
                            <div class="metric-card">
                                <h4>Memoria RAM</h4>
                                <div class="metric-display">${selectedServer.memory}%</div>
                                <div class="metric-bar">
                                    <div class="metric-fill" style="width: ${selectedServer.memory}%; background: ${getMetricColor(selectedServer.memory)}"></div>
                                </div>
                            </div>
                            <div class="metric-card">
                                <h4>Disco Duro</h4>
                                <div class="metric-display">${selectedServer.disk}%</div>
                                <div class="metric-bar">
                                    <div class="metric-fill" style="width: ${selectedServer.disk}%; background: ${getMetricColor(selectedServer.disk)}"></div>
                                </div>
                            </div>
                        </div>
                    `;
                    // Inicializar gráfico de rendimiento
                    setTimeout(() => initPerformanceChart(), 100);
                    break;
                    
                case 'database':
                    contentArea.innerHTML = `
                        <div class="database-info">
                            <h3>Tablas de la Base de Datos</h3>
                            <div class="loading-spinner">
                                <div class="loading"></div>
                                <p>Consultando base de datos...</p>
                            </div>
                        </div>
                    `;
                    // Simular carga de tablas
                    setTimeout(() => loadDatabaseTables(), 1000);
                    break;
                    
                case 'logs':
                    contentArea.innerHTML = `
                        <div class="logs-container">
                            <h3>Registros del Sistema</h3>
                            <div class="log-filters">
                                <select class="log-level">
                                    <option value="all">Todos los niveles</option>
                                    <option value="error">Errores</option>
                                    <option value="warning">Advertencias</option>
                                    <option value="info">Información</option>
                                </select>
                                <input type="date" class="log-date" value="${new Date().toISOString().split('T')[0]}">
                            </div>
                            <div class="logs-list">
                                ${generateLogs()}
                            </div>
                        </div>
                    `;
                    break;
                    
                case 'config':
                    contentArea.innerHTML = `
                        <div class="config-section">
                            <h3>Configuración del Servidor</h3>
                            <form class="config-form">
                                <div class="form-group">
                                    <label>Nombre del Servidor</label>
                                    <input type="text" value="${selectedServer.name}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Dirección IP</label>
                                    <input type="text" value="${selectedServer.ip}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Usuario de Base de Datos</label>
                                    <input type="text" value="simf_siais" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" value="********" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Intervalo de Monitoreo (minutos)</label>
                                    <input type="number" value="5" min="1" max="60" class="form-control">
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn-save">Guardar Cambios</button>
                                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                                </div>
                            </form>
                        </div>
                    `;
                    break;
            }
        }

        // Obtener color según métrica
        function getMetricColor(value) {
            if (value < 50) return 'var(--success)';
            if (value < 80) return 'var(--warning)';
            return 'var(--danger)';
        }

        // Inicializar gráficos pequeños
        function initializeCharts() {
            // Configuración común para los mini gráficos
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            };

            // Mini gráfico para servidores online
            const onlineCtx = document.getElementById('online-chart');
            if (onlineCtx) {
                new Chart(onlineCtx, {
                    type: 'line',
                    data: {
                        labels: ['', '', '', '', ''],
                        datasets: [{
                            data: [8, 9, 8, 10, 8],
                            borderColor: 'var(--success)',
                            tension: 0.4,
                            borderWidth: 2
                        }]
                    },
                    options: commonOptions
                });
            }
        }

        // Generar logs simulados
        function generateLogs() {
            const logTypes = [
                { level: 'info', icon: 'fa-info-circle', message: 'Conexión exitosa a la base de datos' },
                { level: 'warning', icon: 'fa-exclamation-triangle', message: 'Alto uso de memoria detectado' },
                { level: 'error', icon: 'fa-times-circle', message: 'Error al ejecutar consulta SQL' },
                { level: 'info', icon: 'fa-info-circle', message: 'Backup completado exitosamente' }
            ];

            let logsHtml = '';
            for (let i = 0; i < 10; i++) {
                const log = logTypes[Math.floor(Math.random() * logTypes.length)];
                const time = new Date(Date.now() - Math.random() * 3600000).toLocaleTimeString();
                logsHtml += `
                    <div class="log-entry ${log.level}">
                        <i class="fas ${log.icon}"></i>
                        <span class="log-time">${time}</span>
                        <span class="log-message">${log.message}</span>
                    </div>
                `;
            }
            return logsHtml;
        }

        // Cargar tablas de base de datos (simulado)
        function loadDatabaseTables() {
            const tables = [
                'dbo.Pacientes',
                'dbo.Medicos',
                'dbo.Citas',
                'dbo.Expedientes',
                'dbo.Medicamentos',
                'dbo.Hospitalizaciones',
                'dbo.Laboratorios',
                'dbo.Radiologia',
                'dbo.Urgencias',
                'dbo.Cirugias'
            ];

            const container = document.querySelector('.database-info');
            container.innerHTML = `
                <h3>Tablas de la Base de Datos SIAIS</h3>
                <div class="tables-grid">
                    ${tables.map(table => `
                        <div class="table-card">
                            <i class="fas fa-table"></i>
                            <span>${table}</span>
                            <div class="table-stats">
                                <span>${Math.floor(Math.random() * 100000)} registros</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Cerrar modal
        function closeModal() {
            const modal = document.getElementById('server-modal');
            modal.classList.remove('active');
        }

        // Toggle alertas
        function toggleAlerts() {
            const panel = document.getElementById('alerts-panel');
            panel.classList.toggle('collapsed');
        }

        // Verificar conexión
        async function checkConnection(serverId) {
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
            button.disabled = true;

            // Simular verificación
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check"></i> Conectado';
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }, 2000);
            }, 1500);
        }

        // Actualizar todos los servidores
        function refreshAllServers() {
            const button = document.querySelector('.btn-refresh');
            button.classList.add('refreshing');
            
            // Mostrar animación de actualización
            servers.forEach((server, index) => {
                setTimeout(() => {
                    // Simular actualización de estado
                    if (Math.random() > 0.8 && server.status === 'online') {
                        server.status = 'warning';
                    } else if (server.status === 'offline' && Math.random() > 0.7) {
                        server.status = 'online';
                    }
                    
                    // Actualizar métricas
                    if (server.status === 'online') {
                        server.cpu = Math.floor(Math.random() * 90) + 10;
                        server.memory = Math.floor(Math.random() * 90) + 10;
                        server.disk = Math.floor(Math.random() * 90) + 10;
                    }
                }, index * 100);
            });

            setTimeout(() => {
                updateStats();
                renderServers();
                button.classList.remove('refreshing');
                showNotification('Actualización completa', 'success');
            }, servers.length * 100 + 500);
        }

        // Mostrar notificación
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification ${type} show`;
            notification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Actualizar último tiempo de verificación
        function updateLastCheckTime() {
            const element = document.getElementById('last-check-time');
            let seconds = 0;
            
            setInterval(() => {
                seconds++;
                if (seconds < 60) {
                    element.textContent = `Hace ${seconds} segundos`;
                } else {
                    const minutes = Math.floor(seconds / 60);
                    element.textContent = `Hace ${minutes} minuto${minutes > 1 ? 's' : ''}`;
                }
            }, 1000);
        }

        // Actualizaciones en tiempo real
        function startRealTimeUpdates() {
            setInterval(() => {
                // Simular cambios aleatorios
                servers.forEach(server => {
                    if (server.status === 'online' && Math.random() > 0.95) {
                        server.cpu = Math.min(100, server.cpu + Math.floor(Math.random() * 10));
                        server.memory = Math.min(100, server.memory + Math.floor(Math.random() * 10));
                    }
                });
                
                // Actualizar vista si es necesario
                if (document.querySelector('.server-card')) {
                    renderServers();
                }
            }, 5000);
        }

        // Inicializar event listeners
        function initializeEventListeners() {
            // Búsqueda
            document.getElementById('search-servers').addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.server-card, .list-item');
                
                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            // Filtros
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentFilter = btn.dataset.filter;
                    renderServers();
                });
            });

            // Cambio de vista
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentView = btn.dataset.view;
                    renderServers();
                });
            });

            // Tabs del modal
            document.querySelectorAll('.modal-tab').forEach(tab => {
                tab.addEventListener('click', (e) => {
                    document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    updateModalContent(tab.dataset.tab);
                });
            });

            // Cerrar modal al hacer clic fuera
            document.getElementById('server-modal').addEventListener('click', (e) => {
                if (e.target.classList.contains('modal')) {
                    closeModal();
                }
            });

            // Navegación
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', (e) => {
                    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                });
            });
        }

    </script>
</body>
</html>

