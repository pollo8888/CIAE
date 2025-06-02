<?php
session_start();

// Configuración
$pythonPath = "C:\\Users\\Soporte\\AppData\\Local\\Programs\\Python\\Python39\\python.exe";
$scriptPath = "C:\\xampp\\htdocs\\CIAE\\DM\\dm01.py";

// Función para ejecutar el script Python
function ejecutarAnalisisDiabetes($periodo) {
    global $pythonPath, $scriptPath;
    
    $comando = "\"$pythonPath\" \"$scriptPath\" $periodo 2>&1";
    $output = shell_exec($comando);
    return $output;
}

// Función para obtener el nombre del mes
function getNombreMes($mes) {
    $meses = [
        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
        '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
        '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
    ];
    return $meses[$mes] ?? '';
}

// Procesar solicitud AJAX para cargar datos específicos
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'cargar_datos_periodo') {
        $year = $_POST['year'] ?? date('Y');
        $month = $_POST['month'] ?? null;
        
        $datos_periodo = [];
        
        if ($month) {
            // Cargar datos de un mes específico
            $periodo = sprintf("%04d%02d", $year, $month);
            $resultado = ejecutarAnalisisDiabetes($periodo);
            $resultado_datos = json_decode($resultado, true);
            
            if (isset($resultado_datos['resultados_individuales'])) {
                $datos_periodo = $resultado_datos;
            }
        } else {
            // Cargar datos de todo el año
            $datos_periodo = [
                'resultados_individuales' => [],
                'total_global' => [
                    'numerador' => 0,
                    'denominador' => 0,
                    'porcentaje' => 0
                ],
                'datos_mensuales' => []
            ];
            
            for ($mes = 1; $mes <= 12; $mes++) {
                $periodo = sprintf("%04d%02d", $year, $mes);
                $resultado = ejecutarAnalisisDiabetes($periodo);
                $resultado_datos = json_decode($resultado, true);
                
                if (isset($resultado_datos['resultados_individuales'])) {
                    $datos_periodo['datos_mensuales'][sprintf("%02d", $mes)] = $resultado_datos;
                    $datos_periodo['total_global']['numerador'] += $resultado_datos['total_global']['numerador'];
                    $datos_periodo['total_global']['denominador'] += $resultado_datos['total_global']['denominador'];
                }
            }
            
            if ($datos_periodo['total_global']['denominador'] > 0) {
                $datos_periodo['total_global']['porcentaje'] = 
                    ($datos_periodo['total_global']['numerador'] / $datos_periodo['total_global']['denominador']) * 100;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'datos' => $datos_periodo]);
        exit;
    }
}

// Cargar datos iniciales (año actual)
$año_actual = date('Y');
$datos_por_año = [];

// Cargar datos de 2024 y 2025
for ($year = 2020; $year <= 2025; $year++) {
    $datos_por_año[$year] = [
        'resultados_individuales' => [],
        'total_global' => [
            'numerador' => 0,
            'denominador' => 0,
            'porcentaje' => 0
        ],
        'datos_mensuales' => []
    ];
    
    for ($month = 1; $month <= 12; $month++) {
        $periodo = sprintf("%04d%02d", $year, $month);
        $resultado = ejecutarAnalisisDiabetes($periodo);
        $resultado_datos = json_decode($resultado, true);
        
        if (isset($resultado_datos['resultados_individuales']) && !empty($resultado_datos['resultados_individuales'])) {
            // Guardar datos mensuales
            $datos_por_año[$year]['datos_mensuales'][sprintf("%02d", $month)] = $resultado_datos;
            
            // Acumular totales globales del año
            $datos_por_año[$year]['total_global']['numerador'] += $resultado_datos['total_global']['numerador'];
            $datos_por_año[$year]['total_global']['denominador'] += $resultado_datos['total_global']['denominador'];
            
            // Procesar resultados individuales
            foreach ($resultado_datos['resultados_individuales'] as $unidad) {
                $clave = $unidad['clave_presup'];
                
                if (!isset($datos_por_año[$year]['resultados_individuales'][$clave])) {
                    $datos_por_año[$year]['resultados_individuales'][$clave] = [
                        'clave_presup' => $clave,
                        'unidad' => $unidad['unidad'],
                        'numerador' => 0,
                        'denominador' => 0,
                        'resultado_final' => 0,
                        'meses' => []
                    ];
                }
                
                // Acumular valores anuales
                $datos_por_año[$year]['resultados_individuales'][$clave]['numerador'] += $unidad['numerador'];
                $datos_por_año[$year]['resultados_individuales'][$clave]['denominador'] += $unidad['denominador'];
                
                // Guardar datos mensuales
                $datos_por_año[$year]['resultados_individuales'][$clave]['meses'][sprintf("%02d", $month)] = [
                    'numerador' => $unidad['numerador'],
                    'denominador' => $unidad['denominador'],
                    'resultado_final' => $unidad['resultado_final']
                ];
            }
        }
    }
    
    // Calcular porcentaje global del año
    if ($datos_por_año[$year]['total_global']['denominador'] > 0) {
        $datos_por_año[$year]['total_global']['porcentaje'] = 
            ($datos_por_año[$year]['total_global']['numerador'] / $datos_por_año[$year]['total_global']['denominador']) * 100;
    }
    
    // Calcular resultado_final para cada unidad (promedio anual)
    foreach ($datos_por_año[$year]['resultados_individuales'] as &$unidad) {
        if ($unidad['denominador'] > 0) {
            $unidad['resultado_final'] = ($unidad['numerador'] / $unidad['denominador']) * 100;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Avanzado de Indicadores de Diabetes - IMSS</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/daterangepicker/3.1/daterangepicker.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="loader-wrapper" id="loader">
    <div class="loader-content">
        <div class="loader"></div>
        <div class="loader-text">Cargando Dashboard...</div>
    </div>
</div>

<div class="header">
    <div class="container text-center">
        <h1 class="animate__animated animate__fadeInDown">
            <i class="fas fa-chart-line"></i> Dashboard Avanzado de Indicadores de Diabetes
        </h1>
        <p class="subtitle animate__animated animate__fadeInUp">
            Sistema de Análisis y Monitoreo en Tiempo Real - IMSS
        </p>
    </div>
</div>

<div class="container-fluid mt-4">
    <!-- Control Panel -->
    <div class="control-panel animate__animated animate__fadeIn">
        <div class="row">
            <div class="col-md-12">
                <h2 class="control-title">
                    <i class="fas fa-filter"></i> Panel de Control y Filtros
                </h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="filter-item">
                    <label class="filter-label">Seleccionar Año</label>
                    <select class="filter-select" id="yearFilter">
                        <option value="2024">2024</option>
                        <option value="2025" selected>2025</option>
                        <option value="all">Todos los años</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="filter-item">
                    <label class="filter-label">Seleccionar Mes</label>
                    <select class="filter-select" id="monthFilter">
                        <option value="all">Todos los meses</option>
                        <option value="01">Enero</option>
                        <option value="02">Febrero</option>
                        <option value="03">Marzo</option>
                        <option value="04">Abril</option>
                        <option value="05">Mayo</option>
                        <option value="06">Junio</option>
                        <option value="07">Julio</option>
                        <option value="08">Agosto</option>
                        <option value="09">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="filter-item">
                    <label class="filter-label">Tipo de Vista</label>
                    <select class="filter-select" id="viewType">
                        <option value="monthly">Vista Mensual</option>
                        <option value="quarterly">Vista Trimestral</option>
                        <option value="yearly">Vista Anual</option>
                        <option value="comparison">Comparativa</option>
                    </select>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="filter-item">
                    <label class="filter-label">Acciones</label>
                    <div class="d-flex gap-2">
                        <button class="btn-custom btn-primary-custom" id="applyFilters">
                            <i class="fas fa-search"></i> Aplicar Filtros
                        </button>
                        <button class="btn-custom btn-secondary-custom" id="resetFilters">
                            <i class="fas fa-redo"></i> Resetear
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Selector de Meses Rápido -->
        <div class="row mt-3">
            <div class="col-md-12">
                <h4 class="control-title">
                    <i class="fas fa-calendar-alt"></i> Selección Rápida de Meses
                </h4>
                <div class="month-selector-grid">
                    <?php for($i = 1; $i <= 12; $i++): ?>
                        <button class="month-btn" data-month="<?php echo sprintf('%02d', $i); ?>">
                            <?php echo getNombreMes(sprintf('%02d', $i)); ?>
                        </button>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Year Tabs -->
    <div class="year-tabs animate__animated animate__fadeIn">
        <button class="year-tab" data-year="2024">
            <i class="fas fa-calendar"></i> 2024
        </button>
        <button class="year-tab active" data-year="2025">
            <i class="fas fa-calendar"></i> 2025
        </button>
        <button class="year-tab" data-year="comparison">
            <i class="fas fa-chart-bar"></i> Comparación
        </button>
    </div>

    <!-- Summary Section -->
    <div class="analysis-summary animate__animated animate__fadeIn">
        <h3 class="summary-title">
            <i class="fas fa-chart-pie"></i> Resumen del Análisis
        </h3>
        <div class="summary-content">
            <div class="summary-item">
                <div class="summary-value" id="summaryYear">2025</div>
                <div class="summary-label">AÑO SELECCIONADO</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" id="summaryMonth">Todos</div>
                <div class="summary-label">MES SELECCIONADO</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" id="summaryUnits">0</div>
                <div class="summary-label">UNIDADES ANALIZADAS</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" id="summaryGlobal">0%</div>
                <div class="summary-label">PORCENTAJE GLOBAL</div>
            </div>
        </div>
    </div>

    <!-- Stats Cards para Año Seleccionado -->
    <div class="stats-container" id="statsContainer">
        <div class="stat-card primary scale-in">
            <i class="fas fa-user-md stat-icon"></i>
            <div class="stat-value" id="totalNumerador">0</div>
            <div class="stat-label">Total Numerador</div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> +5.2% vs mes anterior
            </div>
        </div>
        
        <div class="stat-card secondary scale-in" style="animation-delay: 0.1s">
            <i class="fas fa-users stat-icon"></i>
            <div class="stat-value" id="totalDenominador">0</div>
            <div class="stat-label">Total Denominador</div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> +3.1% vs mes anterior
            </div>
        </div>
        
        <div class="stat-card success scale-in" style="animation-delay: 0.2s">
            <i class="fas fa-percentage stat-icon"></i>
            <div class="stat-value" id="porcentajeGlobal">0%</div>
            <div class="stat-label">Porcentaje Global</div>
            <div class="stat-change negative">
                <i class="fas fa-arrow-down"></i> -0.5% vs mes anterior
            </div>
        </div>
        
        <div class="stat-card info scale-in" style="animation-delay: 0.3s">
            <i class="fas fa-hospital stat-icon"></i>
            <div class="stat-value" id="unidadesTotales">0</div>
            <div class="stat-label">Unidades Médicas</div>
            <div class="stat-change">
                <i class="fas fa-minus"></i> Sin cambios
            </div>
        </div>
    </div>

    <!-- Main Charts Section -->
    <div class="row">
        <!-- Chart Principal -->
        <div class="col-lg-8">
            <div class="chart-container slide-in-right">
                <div class="chart-header">
                    <h2 class="chart-title">
                        <i class="fas fa-chart-bar"></i> 
                        <span id="mainChartTitle">Análisis Comparativo por Unidad</span>
                    </h2>
                    <div class="chart-options">
                        <button class="chart-option-btn active" data-chart="bar">
                            <i class="fas fa-chart-bar"></i> Barras
                        </button>
                        <button class="chart-option-btn" data-chart="line">
                            <i class="fas fa-chart-line"></i> Líneas
                        </button>
                        <button class="chart-option-btn" data-chart="radar">
                            <i class="fas fa-chart-radar"></i> Radar
                        </button>
                    </div>
                </div>
                <div style="position: relative; height: 400px;">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Chart Secundario -->
        <div class="col-lg-4">
            <div class="chart-container slide-in-right" style="animation-delay: 0.1s">
                <div class="chart-header">
                    <h2 class="chart-title">
                        <i class="fas fa-chart-pie"></i> Distribución por Unidad
                    </h2>
                </div>
                <div style="position: relative; height: 400px;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica de Tendencia Temporal -->
    <div class="chart-container fade-in">
        <div class="chart-header">
            <h2 class="chart-title">
                <i class="fas fa-chart-line"></i> Tendencia Temporal (2024-2025)
            </h2>
        </div>
               <div style="position: relative; height: 400px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Comparación entre años -->
    <div class="comparison-container">
        <div class="chart-container fade-in">
            <div class="chart-header">
                <h2 class="chart-title">
                    <i class="fas fa-balance-scale"></i> Comparación 2024 vs 2025
                </h2>
            </div>
            <div style="position: relative; height: 350px;">
                <canvas id="comparison2024vs2025"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabla de Datos Detallados -->
    <div class="table-container fade-in">
        <div class="table-header">
            <h2 class="table-title">
                <i class="fas fa-table"></i> Datos Detallados por Unidad
            </h2>
            <div class="table-actions">
                <input type="text" class="filter-input" id="tableSearch" placeholder="Buscar...">
                <select class="filter-select" id="tablePageSize">
                    <option value="10">10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                    <option value="100">100 registros</option>
                </select>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="custom-table" id="dataTable">
                <thead>
                    <tr>
                        <th>Clave Presupuestal</th>
                        <th>Unidad</th>
                        <th>Año</th>
                        <th>Mes</th>
                        <th>Numerador</th>
                        <th>Denominador</th>
                        <th>Porcentaje</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    <!-- Los datos se cargarán dinámicamente -->
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div id="tableInfo">Mostrando 0 de 0 registros</div>
            <nav>
                <ul class="pagination" id="tablePagination">
                    <!-- Se generará dinámicamente -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Sistema de Notificaciones -->
<div id="notificationContainer"></div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
// Variables globales
let currentYear = 2025;
let currentMonth = 'all';
let currentView = 'monthly';
let chartsInstances = {};
let currentData = <?php echo json_encode($datos_por_año); ?>;

// Función para obtener el nombre del mes
function getNombreMes(mes) {
    const meses = {
        '01': 'Enero', '02': 'Febrero', '03': 'Marzo', '04': 'Abril',
        '05': 'Mayo', '06': 'Junio', '07': 'Julio', '08': 'Agosto',
        '09': 'Septiembre', '10': 'Octubre', '11': 'Noviembre', '12': 'Diciembre'
    };
    return meses[mes] || mes;
}

// Funciones para el sistema de notificaciones
function showNotification(message, type = 'info') {
    const notification = $(`
        <div class="notification ${type} animate__animated animate__slideInRight">
            <i class="fas ${getNotificationIcon(type)} me-2"></i>
            <span>${message}</span>
        </div>
    `);
    
    $('#notificationContainer').append(notification);
    
    setTimeout(() => {
        notification.addClass('show');
    }, 100);
    
    setTimeout(() => {
        notification.removeClass('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function getNotificationIcon(type) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-times-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    return icons[type] || 'fa-info-circle';
}

// Inicialización
$(document).ready(function() {
    // Ocultar loader
    setTimeout(() => {
        $('#loader').fadeOut();
        showNotification('Dashboard cargado exitosamente', 'success');
    }, 1000);
    
    // Inicializar todos los componentes
    initializeEventListeners();
    initializeCharts();
    updateDashboard();
});

// Event Listeners
function initializeEventListeners() {
    // Year tabs
    $('.year-tab').on('click', function() {
        $('.year-tab').removeClass('active');
        $(this).addClass('active');
        const yearValue = $(this).data('year');
        
        if (yearValue === 'comparison') {
            currentView = 'comparison';
            showComparisonView();
        } else {
            currentYear = yearValue;
            currentView = 'yearly';
            updateDashboard();
        }
    });
    
    // Month buttons
    $('.month-btn').on('click', function() {
        $('.month-btn').removeClass('active');
        $(this).addClass('active');
        currentMonth = $(this).data('month');
        $('#monthFilter').val(currentMonth);
        updateDashboard();
    });
    
    // Apply filters
    $('#applyFilters').on('click', function() {
        currentYear = $('#yearFilter').val();
        currentMonth = $('#monthFilter').val();
        currentView = $('#viewType').val();
        
        if (currentYear === 'all') {
            showAllYearsView();
        } else {
            updateDashboard();
        }
        
        showNotification('Filtros aplicados correctamente', 'success');
    });
    
    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#yearFilter').val('2025');
        $('#monthFilter').val('all');
        $('#viewType').val('monthly');
        $('.month-btn').removeClass('active');
        
        currentYear = 2025;
        currentMonth = 'all';
        currentView = 'monthly';
        
        updateDashboard();
        showNotification('Filtros reseteados', 'info');
    });
    
    // Chart type buttons
    $('.chart-option-btn').on('click', function() {
        const chartType = $(this).data('chart');
        $('.chart-option-btn').removeClass('active');
        $(this).addClass('active');
        updateMainChart(chartType);
    });
}

// Inicializar gráficos
function initializeCharts() {
    // Inicializar gráficos vacíos para evitar errores
    const mainCtx = document.getElementById('mainChart').getContext('2d');
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const comparisonCtx = document.getElementById('comparison2024vs2025').getContext('2d');
    
    // Crear gráficos vacíos
    chartsInstances.mainChart = new Chart(mainCtx, {
        type: 'bar',
        data: { labels: [], datasets: [] },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    chartsInstances.pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: { labels: [], datasets: [] },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    chartsInstances.trendChart = new Chart(trendCtx, {
        type: 'line',
        data: { labels: [], datasets: [] },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    chartsInstances.comparisonChart = new Chart(comparisonCtx, {
        type: 'bar',
        data: { labels: [], datasets: [] },
        options: { responsive: true, maintainAspectRatio: false }
    });
}

// Función principal de actualización
function updateDashboard() {
    updateSummary();
    updateStats();
    updateCharts();
    updateDataTable();
}

// Actualizar resumen
function updateSummary() {
    $('#summaryYear').text(currentYear === 'all' ? 'Todos' : currentYear);
    $('#summaryMonth').text(currentMonth === 'all' ? 'Todos' : getNombreMes(currentMonth));
}

// Actualizar estadísticas
function updateStats() {
    let numerator = 0;
    let denominator = 0;
    let units = 0;
    
    if (currentYear === 'all') {
        // Sumar datos de todos los años
        Object.keys(currentData).forEach(year => {
            if (currentData[year] && currentData[year].total_global) {
                numerator += currentData[year].total_global.numerador || 0;
                denominator += currentData[year].total_global.denominador || 0;
                units += Object.keys(currentData[year].resultados_individuales || {}).length;
            }
        });
    } else {
        const yearData = currentData[currentYear];
        if (yearData) {
            if (currentMonth === 'all') {
                numerator = yearData.total_global.numerador || 0;
                denominator = yearData.total_global.denominador || 0;
                units = Object.keys(yearData.resultados_individuales || {}).length;
            } else {
                const monthData = yearData.datos_mensuales?.[currentMonth];
                if (monthData) {
                    numerator = monthData.total_global.numerador || 0;
                    denominator = monthData.total_global.denominador || 0;
                    units = monthData.resultados_individuales?.length || 0;
                }
            }
        }
    }
    
    const percentage = denominator > 0 ? (numerator / denominator * 100).toFixed(2) : 0;
    
    // Actualizar valores con animación
    animateValue('totalNumerador', 0, numerator, 1000);
    animateValue('totalDenominador', 0, denominator, 1000);
    animateValue('porcentajeGlobal', 0, percentage, 1000, true);
    animateValue('unidadesTotales', 0, units, 1000);
    
    $('#summaryUnits').text(units);
    $('#summaryGlobal').text(percentage + '%');
}

// Función para animar números
function animateValue(id, start, end, duration, isPercentage = false) {
    const element = document.getElementById(id);
    if (!element) return;
    
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            current = end;
            clearInterval(timer);
        }
        
        if (isPercentage) {
            element.textContent = current.toFixed(2) + '%';
        } else {
            element.textContent = Math.round(current).toLocaleString();
        }
    }, 16);
}

// Actualizar gráficos
function updateCharts() {
    if (currentYear === 'all') {
        updateAllYearsCharts();
    } else if (currentView === 'comparison') {
        updateComparisonCharts();
    } else {
        updateMainChart('bar');
        updatePieChart();
        updateTrendChart();
        updateComparisonCharts();
    }
}

// Actualizar gráfico principal
function updateMainChart(type = 'bar') {
    const ctx = document.getElementById('mainChart').getContext('2d');
    
    if (chartsInstances.mainChart) {
        chartsInstances.mainChart.destroy();
    }
    
    const labels = [];
    const numeradores = [];
    const denominadores = [];
    const porcentajes = [];
    
    if (currentYear === 'all') {
        // Agregar datos de todos los años
        Object.keys(currentData).forEach(year => {
            const yearData = currentData[year];
            if (yearData && yearData.resultados_individuales) {
                Object.values(yearData.resultados_individuales).forEach(unit => {
                    const index = labels.indexOf(unit.unidad);
                    if (index === -1) {
                        labels.push(unit.unidad);
                        numeradores.push(unit.numerador || 0);
                        denominadores.push(unit.denominador || 0);
                    } else {
                        numeradores[index] += unit.numerador || 0;
                        denominadores[index] += unit.denominador || 0;
                    }
                });
            }
        });
    } else {
        const yearData = currentData[currentYear];
        if (yearData) {
            if (currentMonth === 'all') {
                // Datos anuales
                Object.values(yearData.resultados_individuales || {}).forEach(unit => {
                    labels.push(unit.unidad);
                    numeradores.push(unit.numerador || 0);
                    denominadores.push(unit.denominador || 0);
                    const percentage = unit.denominador > 0 ? (unit.numerador / unit.denominador * 100).toFixed(2) : 0;
                    porcentajes.push(percentage);
                });
            } else {
                // Datos mensuales
                const monthData = yearData.datos_mensuales?.[currentMonth];
                if (monthData && monthData.resultados_individuales) {
                    monthData.resultados_individuales.forEach(unit => {
                        labels.push(unit.unidad);
                        numeradores.push(unit.numerador || 0);
                        denominadores.push(unit.denominador || 0);
                        porcentajes.push(unit.resultado_final || 0);
                    });
                }
            }
        }
    }
    
    // Calcular porcentajes si no están calculados
    if (porcentajes.length === 0) {
        for (let i = 0; i < numeradores.length; i++) {
            const percentage = denominadores[i] > 0 ? (numeradores[i] / denominadores[i] * 100).toFixed(2) : 0;
            porcentajes.push(percentage);
        }
    }
    
    const datasets = type === 'radar' ? [{
        label: 'Porcentaje',
        data: porcentajes,
        backgroundColor: 'rgba(0, 99, 65, 0.2)',
        borderColor: 'rgba(0, 99, 65, 1)',
        pointBackgroundColor: 'rgba(0, 99, 65, 1)',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: 'rgba(0, 99, 65, 1)'
    }] : [
        {
            label: 'Numerador',
            data: numeradores,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2
        },
        {
            label: 'Denominador',
            data: denominadores,
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2
        }
    ];
    
    chartsInstances.mainChart = new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            if (type !== 'radar') {
                                const index = context.dataIndex;
                                return 'Porcentaje: ' + porcentajes[index] + '%';
                            }
                            return '';
                        }
                    }
                }
            },
            scales: type !== 'radar' ? {
                y: {
                    beginAtZero: true
                }
            } : {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

// Actualizar gráfico de pastel
function updatePieChart() {
    const ctx = document.getElementById('pieChart').getContext('2d');
    
    if (chartsInstances.pieChart) {
        chartsInstances.pieChart.destroy();
    }
    
    const labels = [];
    const data = [];
    const backgroundColors = [];
    
    const colors = [
        '#006341', '#8B0000', '#28a745', '#ffc107', 
        '#17a2b8', '#6610f2', '#e83e8c', '#fd7e14',
        '#20c997', '#6c757d', '#343a40', '#007bff'
    ];
    
    if (currentYear === 'all') {
        // Datos de todos los años
        Object.keys(currentData).forEach(year => {
            const yearData = currentData[year];
            if (yearData && yearData.resultados_individuales) {
                Object.values(yearData.resultados_individuales).forEach((unit, index) => {
                    const unitIndex = labels.indexOf(unit.unidad);
                    if (unitIndex === -1) {
                        labels.push(unit.unidad);
                        data.push(unit.numerador || 0);
                    } else {
                        data[unitIndex] += unit.numerador || 0;
                                    }
                });
            }
        });
    } else {
        // Datos del año seleccionado
        const yearData = currentData[currentYear];
        if (yearData && yearData.resultados_individuales) {
            Object.values(yearData.resultados_individuales).forEach(unit => {
                labels.push(unit.unidad);
                data.push(unit.numerador || 0);
                backgroundColors.push(colors[labels.length % colors.length]);
            });
        }
    }
    
    chartsInstances.pieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(2);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Actualizar gráfico de tendencia
function updateTrendChart() {
    const ctx = document.getElementById('trendChart').getContext('2d');

    if (chartsInstances.trendChart) {
        chartsInstances.trendChart.destroy();
    }

    const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    const datasets = [];

    [2024, 2025].forEach(year => {
        const yearData = currentData[year];
        const data = [];

        for (let i = 1; i <= 12; i++) {
            const monthKey = String(i).padStart(2, '0');
            const monthData = yearData?.datos_mensuales?.[monthKey];
            if (monthData) {
                data.push(monthData.total_global.porcentaje.toFixed(2));
            } else {
                data.push(null);
            }
        }

        datasets.push({
            label: year.toString(),
            data: data,
            borderColor: year === 2024 ? 'rgba(0, 99, 65, 1)' : 'rgba(255, 99, 132, 1)',
            backgroundColor: year === 2024 ? 'rgba(0, 99, 65, 0.1)' : 'rgba(255, 99, 132, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true
        });
    });

    chartsInstances.trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Porcentaje (%)'
                    }
                }
            }
        }
    });
}

// Actualizar gráficos de comparación
function updateComparisonCharts() {
    const ctx = document.getElementById('comparison2024vs2025').getContext('2d');

    if (chartsInstances.comparisonChart) {
        chartsInstances.comparisonChart.destroy();
    }

    const labels = [];
    const data2024 = [];
    const data2025 = [];

    const yearData2024 = currentData[2024];
    const yearData2025 = currentData[2025];

    Object.values(yearData2024.resultados_individuales).forEach(unit => {
        labels.push(unit.unidad);
        data2024.push((unit.numerador / unit.denominador * 100).toFixed(2));
    });

    Object.values(yearData2025.resultados_individuales).forEach(unit => {
        const index = labels.indexOf(unit.unidad);
        if (index !== -1) {
            data2025[index] = (unit.numerador / unit.denominador * 100).toFixed(2);
        } else {
            labels.push(unit.unidad);
            data2025.push((unit.numerador / unit.denominador * 100).toFixed(2));
        }
    });

    chartsInstances.comparisonChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '2024',
                    data: data2024,
                    backgroundColor: 'rgba(0, 99, 65, 0.6)',
                    borderColor: 'rgba(0, 99, 65, 1)',
                    borderWidth: 2
                },
                {
                    label: '2025',
                    data: data2025,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Porcentaje (%)'
                    }
                }
            }
        }
    });
}

// Actualizar tabla de datos
function updateDataTable() {
    const yearData = currentData[currentYear];
    const dataTableBody = $('#dataTableBody');
    dataTableBody.empty();
    
    if (currentYear === 'all') {
        // Agregar datos de todos los años
        Object.keys(currentData).forEach(year => {
            const yearData = currentData[year];
            if (yearData && yearData.resultados_individuales) {
                Object.values(yearData.resultados_individuales).forEach(unit => {
                    const row = `
                        <tr>
                            <td>${unit.clave_presup}</td>
                            <td>${unit.unidad}</td>
                            <td>${year}</td>
                            <td>Todos</td>
                            <td>${unit.numerador}</td>
                            <td>${unit.denominador}</td>
                            <td>${(unit.numerador / unit.denominador * 100).toFixed(2)}%</td>
                        </tr>
                    `;
                    dataTableBody.append(row);
                });
            }
        });
    } else {
        // Agregar datos del año seleccionado
        if (yearData && yearData.resultados_individuales) {
            Object.values(yearData.resultados_individuales).forEach(unit => {
                const row = `
                    <tr>
                        <td>${unit.clave_presup}</td>
                        <td>${unit.unidad}</td>
                        <td>${currentYear}</td>
                        <td>${currentMonth === 'all' ? 'Todos' : getNombreMes(currentMonth)}</td>
                        <td>${unit.numerador}</td>
                        <td>${unit.denominador}</td>
                        <td>${(unit.numerador / unit.denominador * 100).toFixed(2)}%</td>
                    </tr>
                `;
                dataTableBody.append(row);
            });
        }
    }
    
    // Actualizar información de la tabla
    $('#tableInfo').text(`Mostrando ${dataTableBody.children().length} registros`);
}
</script>

</body>
</html>
