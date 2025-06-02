<?php
session_start();

?>

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
<div class="name"><?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellidos']; ?></div>
<div class="role"><?php echo ucfirst($_SESSION['categoria']); ?></div>

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
                <p><i class="fas fa-phone"></i> Línea directa: 56 5913 9591</p>
                <p><i class="fas fa-mobile-alt"></i> Móvil: +52 229 601 8327</p>
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
            <p>© 2025 Instituto Mexicano del Seguro Social. Todos los derechos reservados. | Desarrollado por el Departamento de Sistemas</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
    // Variables globales
    let servers = [];
    let currentView = 'grid';
    let currentFilter = 'all';
    let selectedServer = null;
    let updateInterval = null;

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        initializeApp();
        createParticles();
        startRealTimeUpdates();
    });

    // Función principal de inicialización
    function initializeApp() {
        loadServersFromAPI(); // Cargar datos iniciales
        initializeEventListeners();
        initializeCharts();
        updateLastCheckTime();
    }

    // Cargar servidores desde verificar.php
// Cargar servidores desde verificar.php
async function loadServersFromAPI() {
    try {
        showLoadingState();
        
        const response = await fetch('verificar.php');
        if (response.ok) {
            const data = await response.json();
            
            // Transformar datos para nuestro formato
            servers = data.map((server, index) => ({
                id: server.id,
                name: server.ubicacion,
                fullName: server.nombre,
                ip: server.ip || server.server,
                location: server.ubicacion,
                status: server.status,
                tables: server.tables || 0,
                error: server.error || null,
                // Nuevos campos de la base de datos
                nombre_sv: server.nombre_sv || '',
                nombre_db: server.nombre_db || 'DBSIAIS',
                usuario: server.usuario || 'simf_siais',
                contrasena: server.contrasena || '',
                puerto: server.puerto || 1433,
                // Generar métricas aleatorias para demostración
                cpu: server.status === 'online' ? Math.floor(Math.random() * 60) + 20 : 0,
                memory: server.status === 'online' ? Math.floor(Math.random() * 70) + 20 : 0,
                disk: server.status === 'online' ? Math.floor(Math.random() * 80) + 10 : 0
            }));
            
            updateStats();
            renderServers();
            hideLoadingState();
        }
    } catch (error) {
        console.error('Error al cargar servidores:', error);
        showNotification('Error al cargar los servidores', 'error');
        hideLoadingState();
    }
}


    // Mostrar estado de carga
    function showLoadingState() {
        const container = document.getElementById('servers-container');
        if (container) {
            container.innerHTML = `
                <div class="loading-state">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <p>Verificando conexiones con los servidores...</p>
                </div>
            `;
        }
    }

    // Ocultar estado de carga
    function hideLoadingState() {
        const loadingState = document.querySelector('.loading-state');
        if (loadingState) {
            loadingState.remove();
        }
    }

    // Crear partículas de fondo
    function createParticles() {
        const particlesContainer = document.getElementById('particles');
        if (!particlesContainer) return;
        
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
        const offline = servers.filter(s => s.status === 'error' || s.status === 'offline').length;
        const warning = servers.filter(s => s.status === 'warning').length;
        const total = servers.length;

        animateNumber('servers-online', online);
        animateNumber('servers-offline', offline);
        animateNumber('servers-warning', warning);
        animateNumber('servers-total', total);

        // Actualizar barra de progreso
        const uptimePercentage = total > 0 ? (online / total) * 100 : 0;
        const progressBar = document.getElementById('uptime-progress');
        if (progressBar) {
            progressBar.style.width = uptimePercentage + '%';
        }
    }

    // Animar números
    function animateNumber(elementId, targetValue) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
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
        if (!container) return;
        
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
        if (currentFilter === 'online') return servers.filter(s => s.status === 'online');
        if (currentFilter === 'offline') return servers.filter(s => s.status === 'error' || s.status === 'offline');
        if (currentFilter === 'warning') return servers.filter(s => s.status === 'warning');
        return servers;
    }

    // Vista de cuadrícula
    function renderGridView(servers, container) {
        servers.forEach(server => {
            const card = createServerCard(server);
            container.appendChild(card);
        });
    }

    // Crear tarjeta de servidor
    // Crear tarjeta de servidor
function createServerCard(server) {
    const card = document.createElement('div');
    const statusClass = server.status === 'error' ? 'offline' : server.status;
    card.className = `server-card ${statusClass} hover-lift interactive`;
    card.onclick = () => showServerDetails(server);

    card.innerHTML = `
        <div class="server-header">
            <div class="server-info">
                <h3>${server.name}</h3>
                <p class="ip">${server.ip}</p>
            </div>
            <div class="server-status ${statusClass}">
                <span class="status-dot"></span>
                ${getStatusText(server.status)}
            </div>
        </div>
        <div class="server-details">
            <div class="detail-item">
                <span class="detail-label">Ubicación</span>
                <span class="detail-value">${server.fullName}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Base de Datos</span>
                <span class="detail-value">${server.nombre_db || 'DBSIAIS'}</span>
            </div>
            ${server.status === 'online' ? `
                <div class="detail-item">
                    <span class="detail-label">Tablas</span>
                    <span class="detail-value">${server.tables}</span>
                </div>
            ` : ''}
        </div>
        ${server.status === 'online' ? `
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
        ` : `
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Sin conexión</p>
            </div>
        `}
        <div class="server-actions">
            <button class="action-btn primary" data-server-id="${server.id}" data-server-ip="${server.ip}">
                <i class="fas fa-sync"></i> Verificar
            </button>
            <button class="action-btn secondary" data-server-id="${server.id}">
                <i class="fas fa-cog"></i> Config
            </button>
        </div>
    `;

    // Agregar event listeners a los botones después de crear el HTML
    const verifyBtn = card.querySelector('.action-btn.primary');
    const configBtn = card.querySelector('.action-btn.secondary');
    
    verifyBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        checkConnectionByButton(server.id, server.ip, e.currentTarget);
    });
    
    configBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        showServerConfig(server.id);
    });

    return card;
}

    // Vista de lista
    // Vista de lista
function renderListView(servers, container) {
    const table = document.createElement('table');
    table.className = 'servers-table';
    
    table.innerHTML = `
        <thead>
            <tr>
                <th>Servidor</th>
                <th>IP</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Tablas</th>
                <th>CPU</th>
                <th>RAM</th>
                <th>Disco</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="servers-table-body">
        </tbody>
    `;
    
    const tbody = table.querySelector('#servers-table-body');
    
    servers.forEach((server, index) => {
        const tr = document.createElement('tr');
        tr.className = `list-item ${server.status === 'error' ? 'offline' : server.status}`;
        
        tr.innerHTML = `
            <td>${server.name}</td>
            <td>${server.ip}</td>
            <td>${server.fullName}</td>
            <td>
                <span class="status-badge ${server.status === 'error' ? 'offline' : server.status}">
                    ${getStatusText(server.status)}
                </span>
            </td>
            <td>${server.status === 'online' ? server.tables : '-'}</td>
            <td>${server.status === 'online' ? server.cpu + '%' : '-'}</td>
            <td>${server.status === 'online' ? server.memory + '%' : '-'}</td>
            <td>${server.status === 'online' ? server.disk + '%' : '-'}</td>
            <td>
                <button class="action-btn small primary verify-btn" data-server-id="${server.id}" data-server-ip="${server.ip}">
                    <i class="fas fa-sync"></i>
                </button>
                <button class="action-btn small secondary info-btn" data-server-index="${index}">
                    <i class="fas fa-info-circle"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(tr);
        
        // Agregar event listeners a los botones
        const verifyBtn = tr.querySelector('.verify-btn');
        const infoBtn = tr.querySelector('.info-btn');
        
        verifyBtn.addEventListener('click', (e) => {
            checkConnectionByButton(server.id, server.ip, e.currentTarget);
        });
        
        infoBtn.addEventListener('click', () => {
            showServerDetails(server);
        });
    });
    
    container.appendChild(table);
}


    // Obtener texto de estado
    function getStatusText(status) {
        const statusMap = {
            online: 'En línea',
            offline: 'Sin conexión',
            error: 'Error',
            warning: 'Advertencia'
        };
        return statusMap[status] || status;
    }

    // Mostrar detalles del servidor
    function showServerDetails(server) {
        selectedServer = server;
        const modal = document.getElementById('server-modal');
        if (modal) {
            modal.classList.add('active');
            updateModalContent('general');
        }
    }

    // Actualizar contenido del modal
    function updateModalContent(tab) {
        const contentArea = document.getElementById('modal-content-area');
        if (!contentArea) return;
        
        switch(tab) {
            case 'general':
    contentArea.innerHTML = `
        <div class="modal-info-grid">
            <div class="info-section">
                <h3>Información General</h3>
                <div class="info-row">
                    <span class="info-label">Código:</span>
                    <span class="info-value">${selectedServer.name}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dirección IP:</span>
                    <span class="info-value">${selectedServer.ip}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ubicación:</span>
                    <span class="info-value">${selectedServer.fullName}</span>
                </div>
                ${selectedServer.nombre_sv ? `
                    <div class="info-row">
                        <span class="info-label">Nombre del Servidor:</span>
                        <span class="info-value">${selectedServer.nombre_sv}</span>
                    </div>
                ` : ''}
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="info-value status-badge ${selectedServer.status === 'error' ? 'offline' : selectedServer.status}">
                        ${getStatusText(selectedServer.status)}
                    </span>
                </div>
                ${selectedServer.status === 'online' ? `
                    <div class="info-row">
                        <span class="info-label">Tablas en BD:</span>
                        <span class="info-value">${selectedServer.tables}</span>
                    </div>
                ` : ''}
                ${selectedServer.error ? `
                    <div class="error-section">
                        <h4>Detalles del Error</h4>
                        <div class="error-box">
                            <i class="fas fa-exclamation-circle"></i>
                            <pre class="error-text">${formatErrorMessage(selectedServer.error)}</pre>
                        </div>
                    </div>
                ` : ''}
            </div>
            <div class="info-section">
                <h3>Configuración de Base de Datos</h3>
                                <div class="info-row">
                    <span class="info-label">Motor:</span>
                    <span class="info-value">Microsoft SQL Server 2019</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Base de Datos:</span>
                    <span class="info-value">${selectedServer.nombre_db || 'DBSIAIS'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Usuario:</span>
                    <span class="info-value">${selectedServer.usuario || 'simf_siais'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Puerto:</span>
                    <span class="info-value">${selectedServer.puerto || 1433}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tiempo de espera:</span>
                    <span class="info-value">30 segundos</span>
                </div>
            </div>
        </div>
    `;
    break;

                    
            case 'performance':
                if (selectedServer.status === 'online') {
                    contentArea.innerHTML = `
                        <div class="performance-container">
                            <h3>Rendimiento del Servidor</h3>
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
                                    <span class="metric-status">${getMetricStatus(selectedServer.cpu)}</span>
                                </div>
                                <div class="metric-card">
                                    <h4>Memoria RAM</h4>
                                    <div class="metric-display">${selectedServer.memory}%</div>
                                    <div class="metric-bar">
                                        <div class="metric-fill" style="width: ${selectedServer.memory}%; background: ${getMetricColor(selectedServer.memory)}"></div>
                                    </div>
                                    <span class="metric-status">${getMetricStatus(selectedServer.memory)}</span>
                                </div>
                                <div class="metric-card">
                                    <h4>Disco Duro</h4>
                                    <div class="metric-display">${selectedServer.disk}%</div>
                                    <div class="metric-bar">
                                        <div class="metric-fill" style="width: ${selectedServer.disk}%; background: ${getMetricColor(selectedServer.disk)}"></div>
                                    </div>
                                    <span class="metric-status">${getMetricStatus(selectedServer.disk)}</span>
                                </div>
                            </div>
                            <div class="performance-info">
                                <p><i class="fas fa-info-circle"></i> Última actualización: ${new Date().toLocaleString()}</p>
                            </div>
                        </div>
                    `;
                    // Inicializar gráfico de rendimiento
                    setTimeout(() => initPerformanceChart(), 100);
                } else {
                    contentArea.innerHTML = `
                        <div class="offline-state">
                            <i class="fas fa-chart-line"></i>
                            <h3>Rendimiento no disponible</h3>
                            <p>El servidor está sin conexión. No se pueden obtener métricas de rendimiento.</p>
                            <button class="btn-retry" onclick="checkConnection(${selectedServer.id})">
                                <i class="fas fa-sync"></i> Reintentar conexión
                            </button>
                        </div>
                    `;
                }
                break;
                
            case 'database':
                if (selectedServer.status === 'online') {
                    contentArea.innerHTML = `
                        <div class="database-info">
                            <h3>Base de Datos SIAIS</h3>
                            <div class="db-stats">
                                <div class="stat-card">
                                    <i class="fas fa-database"></i>
                                    <div class="stat-value">DBSIAIS</div>
                                    <div class="stat-label">Base de Datos</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-table"></i>
                                    <div class="stat-value">${selectedServer.tables}</div>
                                    <div class="stat-label">Total Tablas</div>
                                </div>
                                <div class="stat-card">
                                    <i class="fas fa-server"></i>
                                    <div class="stat-value">SQL Server</div>
                                    <div class="stat-label">Motor BD</div>
                                </div>
                            </div>
                            <div class="loading-spinner">
                                <div class="loading"></div>
                                <p>Consultando estructura de base de datos...</p>
                            </div>
                        </div>
                    `;
                    // Simular carga de tablas
                    setTimeout(() => loadDatabaseTables(), 1000);
                } else {
                    contentArea.innerHTML = `
                        <div class="offline-state">
                            <i class="fas fa-database"></i>
                            <h3>Base de datos no accesible</h3>
                            <p>No se puede conectar con la base de datos del servidor.</p>
                            ${selectedServer.error ? `
                                <div class="error-detail">
                                    <h4>Detalles del error:</h4>
                                    <code>${formatErrorMessage(selectedServer.error)}</code>
                                </div>
                            ` : ''}
                            <button class="btn-retry" onclick="checkConnection(${selectedServer.id})">
                                <i class="fas fa-sync"></i> Reintentar conexión
                            </button>
                        </div>
                    `;
                }
                break;
                
            case 'logs':
                contentArea.innerHTML = `
                    <div class="logs-container">
                        <h3>Registros del Sistema</h3>
                        <div class="log-filters">
                            <select class="log-level" id="log-level-filter">
                                <option value="all">Todos los niveles</option>
                                <option value="error">Errores</option>
                                <option value="warning">Advertencias</option>
                                <option value="info">Información</option>
                            </select>
                            <input type="date" class="log-date" id="log-date-filter" value="${new Date().toISOString().split('T')[0]}">
                            <button class="btn-filter" onclick="filterLogs()">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                        <div class="logs-list" id="logs-list">
                            ${generateLogs(selectedServer)}
                        </div>
                        <div class="logs-footer">
                            <button class="btn-export" onclick="exportLogs()">
                                <i class="fas fa-download"></i> Exportar logs
                            </button>
                        </div>
                    </div>
                `;
                break;
                
case 'config':
    contentArea.innerHTML = `
        <div class="config-section">
            <h3>Configuración del Servidor</h3>
            <form class="config-form" onsubmit="saveServerConfig(event)">
                <input type="hidden" id="config-server-id" value="${selectedServer.id}">
                
                <div class="form-section">
                    <h4>Información General</h4>
                    <div class="form-group">
                        <label>Código del Servidor</label>
                        <input type="text" value="${selectedServer.name}" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Dirección IP</label>
                        <input type="text" id="config-ip" value="${selectedServer.ip}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" id="config-fullname" value="${selectedServer.fullName}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nombre del Servidor</label>
                        <input type="text" id="config-server-name" value="${selectedServer.nombre_sv || ''}" class="form-control" placeholder="Ej: SERVIDOR-01">
                    </div>
                </div>
                
                <div class="form-section">
                    <h4>Configuración de Base de Datos</h4>
                    <div class="form-group">
                        <label>Nombre de Base de Datos</label>
                        <input type="text" id="config-db-name" value="${selectedServer.nombre_db || 'DBSIAIS'}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Usuario</label>
                        <input type="text" id="config-db-user" value="${selectedServer.usuario || 'simf_siais'}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" id="config-db-password" value="${selectedServer.contrasena ? '********' : ''}" class="form-control" placeholder="Dejar en blanco para no cambiar">
                    </div>
                    <div class="form-group">
                        <label>Puerto</label>
                        <input type="number" id="config-db-port" value="${selectedServer.puerto || 1433}" min="1" max="65535" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    <button type="button" class="btn-test" onclick="testConnection()">
                        <i class="fas fa-network-wired"></i> Probar Conexión
                    </button>
                    <button type="button" class="btn-cancel" onclick="closeModal()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    `;
    break;

        }
    }

    // Función auxiliar para formatear mensajes de error
    function formatErrorMessage(error) {
        // Limpiar y formatear el mensaje de error
        if (typeof error === 'string') {
            // Remover caracteres no deseados y formatear
            return error
                .replace(/\\n/g, '\n')
                .replace(/\\'/g, "'")
                .replace(/b'/g, '')
                .replace(/'/g, '')
                .trim();
        }
        return error;
    }

    // Función auxiliar para obtener el estado de una métrica
    function getMetricStatus(value) {
        if (value < 50) return 'Óptimo';
        if (value < 80) return 'Normal';
        return 'Crítico';
    }

    // Función para generar logs específicos del servidor
    function generateLogs(server) {
        const logTypes = [];
        
        if (server.status === 'online') {
            logTypes.push(
                { level: 'info', icon: 'fa-check-circle', message: `Conexión exitosa establecida con ${server.name}` },
                { level: 'info', icon: 'fa-database', message: `Base de datos DBSIAIS verificada - ${server.tables} tablas` },
                { level: 'info', icon: 'fa-sync', message: 'Sincronización de datos completada' }
            );
            
            if (server.cpu > 80) {
                logTypes.push({ level: 'warning', icon: 'fa-exclamation-triangle', message: 'Alto uso de CPU detectado' });
            }
            if (server.memory > 80) {
                logTypes.push({ level: 'warning', icon: 'fa-memory', message: 'Alto uso de memoria RAM' });
            }
        } else {
            logTypes.push(
                { level: 'error', icon: 'fa-times-circle', message: `Error de conexión con ${server.name}` },
                { level: 'error', icon: 'fa-database', message: 'No se pudo acceder a la base de datos' },
                { level: 'warning', icon: 'fa-exclamation-triangle', message: 'Servidor no responde a ping' }
            );
        }

        let logsHtml = '';
        const baseTime = Date.now();
        
        for (let i = 0; i < 15; i++) {
            const log = logTypes[Math.floor(Math.random() * logTypes.length)];
            const time = new Date(baseTime - (i * 300000)).toLocaleString(); // Cada 5 minutos
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

    // Funciones adicionales para los formularios
// Función para guardar la configuración del servidor
async function saveServerConfig(event) {
    event.preventDefault();
    
    const serverId = document.getElementById('config-server-id').value;
    const ip = document.getElementById('config-ip').value;
    const fullname = document.getElementById('config-fullname').value;
    const serverName = document.getElementById('config-server-name').value;
    const dbName = document.getElementById('config-db-name').value;
    const dbUser = document.getElementById('config-db-user').value;
    const dbPassword = document.getElementById('config-db-password').value;
    const dbPort = document.getElementById('config-db-port').value;
    
    const data = {
        id: serverId,
        ip: ip,
        nombre: fullname,
        nombre_sv: serverName,
        nombre_db: dbName,
        usuario: dbUser,
        contrasena: dbPassword,
        puerto: parseInt(dbPort)
    };
    
    try {
        const response = await fetch('update_server.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Configuración guardada correctamente', 'success');
            
            // Actualizar los datos locales
            const serverIndex = servers.findIndex(s => s.id == serverId);
            if (serverIndex !== -1) {
                servers[serverIndex] = {
                    ...servers[serverIndex],
                    ip: ip,
                    fullName: fullname,
                    nombre_sv: serverName,
                    nombre_db: dbName,
                    usuario: dbUser,
                    puerto: dbPort
                };
                
                // Si la contraseña no es ********, actualizarla
                if (dbPassword !== '********') {
                    servers[serverIndex].contrasena = dbPassword;
                }
            }
            
            // Recargar la vista
            renderServers();
            
            setTimeout(() => closeModal(), 1500);
        } else {
            showNotification('Error al guardar: ' + result.error, 'error');
        }
    } catch (error) {
        showNotification('Error al guardar la configuración', 'error');
        console.error('Error:', error);
    }
}


// Función para probar la conexión con los datos actuales del formulario
async function testConnection() {
    const button = event.target;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Probando...';
    button.disabled = true;
    
    // Obtener los valores actuales del formulario
    const ip = document.getElementById('config-ip').value;
    const dbName = document.getElementById('config-db-name').value;
    const dbUser = document.getElementById('config-db-user').value;
    const dbPassword = document.getElementById('config-db-password').value;
    const dbPort = document.getElementById('config-db-port').value;
    
    try {
        // Crear un objeto con los datos de prueba
        const testData = {
            ip: ip,
            nombre_db: dbName,
            usuario: dbUser,
            contrasena: dbPassword,
            puerto: dbPort
        };
        
        // Llamar a un endpoint para probar la conexión
        const response = await fetch('test_connection.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(testData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            button.innerHTML = '<i class="fas fa-check"></i> Conexión exitosa';
            button.classList.add('success');
            showNotification('Conexión exitosa con la base de datos', 'success');
        } else {
            button.innerHTML = '<i class="fas fa-times"></i> Error de conexión';
            button.classList.add('error');
            showNotification('Error: ' + result.error, 'error');
        }
    } catch (error) {
        button.innerHTML = '<i class="fas fa-times"></i> Error de conexión';
        button.classList.add('error');
        showNotification('Error al probar la conexión', 'error');
    }
    
    setTimeout(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
        button.classList.remove('success', 'error');
    }, 3000);
}


    function filterLogs() {
        const level = document.getElementById('log-level-filter').value;
        const date = document.getElementById('log-date-filter').value;
        showNotification('Filtros aplicados', 'info');
    }

    function exportLogs() {
        showNotification('Exportando logs...', 'info');
        setTimeout(() => {
            showNotification('Logs exportados correctamente', 'success');
        }, 1500);
    }

    // Obtener color según métrica
    function getMetricColor(value) {
        if (value < 50) return 'var(--success)';
        if (value < 80) return 'var(--warning)';
        return 'var(--danger)';
    }

    // Inicializar gráfico de rendimiento
    function initPerformanceChart() {
        const ctx = document.getElementById('performance-chart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['12:00', '12:05', '12:10', '12:15', '12:20', '12:25'],
                datasets: [
                    {
                        label: 'CPU',
                        data: [45, 52, 48, 55, 51, selectedServer.cpu],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'RAM',
                        data: [60, 58, 62, 65, 63, selectedServer.memory],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Disco',
                        data: [72, 72, 73, 73, 74, selectedServer.disk],
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
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
        if (!container) return;

        container.innerHTML = `
            <h3>Tablas de la Base de Datos SIAIS</h3>
            <div class="db-stats">
                <div class="stat-card">
                    <i class="fas fa-table"></i>
                    <div class="stat-value">${selectedServer.tables}</div>
                    <div class="stat-label">Total Tablas</div>
                </div>
            </div>
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
        if (modal) {
            modal.classList.remove('active');
        }
    }

    // Toggle alertas
    function toggleAlerts() {
        const panel = document.getElementById('alerts-panel');
        if (panel) {
            panel.classList.toggle('collapsed');
        }
    }

    // Verificar conexión individual

    // Verificar conexión individual

    // Verificar conexión individual mejorada
async function checkConnectionByButton(serverId, serverIp, button) {
    console.log('Verificando servidor:', serverId, serverIp); // Para debugging
    
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
    button.disabled = true;
    button.style.cursor = 'not-allowed';

    try {
        console.log('Llamando a verificar_individual.php con IP:', serverIp);
        
        const response = await fetch(`verificar_individual.php?ip=${encodeURIComponent(serverIp)}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Respuesta recibida:', result);
        
        // Buscar el servidor en el array y actualizar sus datos
        const serverIndex = servers.findIndex(s => s.id === serverId);
        if (serverIndex !== -1) {
            servers[serverIndex].status = result.status || 'error';
            servers[serverIndex].tables = result.tables || 0;
            servers[serverIndex].error = result.error || null;
            
            // Si está online, generar nuevas métricas aleatorias
            if (result.status === 'online') {
                servers[serverIndex].cpu = Math.floor(Math.random() * 60) + 20;
                servers[serverIndex].memory = Math.floor(Math.random() * 70) + 20;
                servers[serverIndex].disk = Math.floor(Math.random() * 80) + 10;
            } else {
                servers[serverIndex].cpu = 0;
                servers[serverIndex].memory = 0;
                servers[serverIndex].disk = 0;
            }
        }
        
        // Actualizar la interfaz
        updateStats();
        renderServers();
        
        // Mostrar éxito temporalmente
        button.innerHTML = '<i class="fas fa-check"></i> Actualizado';
        button.classList.add('success');
        showNotification(`Servidor ${serverIp} verificado`, 'success');
        
    } catch (error) {
        console.error('Error al verificar:', error);
        button.innerHTML = '<i class="fas fa-times"></i> Error';
        button.classList.add('error');
        showNotification(`Error al verificar servidor: ${error.message}`, 'error');
    } finally {
        // Restaurar el botón después de 2 segundos
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.style.cursor = 'pointer';
            button.classList.remove('success', 'error');
        }, 2000);
    }
}


    // Actualizar todos los servidores
    async function refreshAllServers() {
        const button = document.querySelector('.btn-refresh');
        if (button) {
            button.classList.add('refreshing');
        }
        
        showNotification('Actualizando todos los servidores...', 'info');
        
        // Recargar datos desde verificar.php
        await loadServersFromAPI();
        
        if (button) {
            button.classList.remove('refreshing');
        }
        
        showNotification('Actualización completa', 'success');
    }

    // Mostrar notificación
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type} show`;
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        notification.innerHTML = `
            <i class="fas ${icons[type]}"></i>
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
        if (!element) return;
        
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
        // Actualizar cada 5 minutos
        updateInterval = setInterval(() => {
            loadServersFromAPI();
        }, 300000); // 5 minutos
    }

    // Inicializar event listeners
    function initializeEventListeners() {
        // Búsqueda
        const searchInput = document.getElementById('search-servers');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.server-card, .list-item');
                
                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

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
        const modal = document.getElementById('server-modal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal')) {
                    closeModal();
                }
            });
        }

        // Navegación
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });

        // Botón de actualizar
        const refreshBtn = document.querySelector('.btn-refresh');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', refreshAllServers);
        }
    }

    // Configuración del servidor individual
    function showServerConfig(serverId) {
        const server = servers.find(s => s.id === serverId);
        if (server) {
            selectedServer = server;
            const modal = document.getElementById('server-modal');
            if (modal) {
                modal.classList.add('active');
                updateModalContent('config');
            }
        }
    }
</script>





</body>
</html>

