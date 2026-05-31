<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

include 'conexion.php';

// --- CONTROL DE TRANSACCIONES OPERATIVAS EN CALIENTE ---
$stmt_ingresos = $pdo->query("SELECT SUM(total) as nuevos_ingresos, COUNT(*) as total_nuevos FROM pedidos");
$res_ingresos = $stmt_ingresos->fetch(PDO::FETCH_ASSOC);

$nuevas_ventas_valor = $res_ingresos['nuevos_ingresos'] ?? 0;
$cantidad_nuevos_pedidos = $res_ingresos['total_nuevos'] ?? 0;

$ingresos_totales = 450000 + $nuevas_ventas_valor;
$total_ordenes_sistema = 1240 + $cantidad_nuevos_pedidos;
$cuentas_por_cobrar_base = 120400 + $nuevas_ventas_valor;

$stmt_critico = $pdo->query("SELECT COUNT(*) as criticos FROM productos WHERE estado = 'Crítico'");
$res_critico = $stmt_critico->fetch(PDO::FETCH_ASSOC);
$productos_criticos = $res_critico['criticos'] ?? 0;

$stmt_crm = $pdo->query("SELECT producto_id, SUM(cantidad) as total_cant FROM pedidos GROUP BY producto_id ORDER BY total_cant DESC LIMIT 1");
$res_crm = $stmt_crm->fetch(PDO::FETCH_ASSOC);
$top_producto = $res_crm['producto_id'] ?? 'PROD1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAP Lite ERP - Ecosistema Empresarial Integrado</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div id="toast-wrapper" class="toast-container"></div>

<div class="app-container">

    <header class="header">
        <div class="logo">ERPSystem</div>
        <input type="text" class="search-bar" id="global-search" placeholder="Buscar procesos, transacciones..." onkeyup="filtrarContenido()">
        <div class="user-area">
            <button onclick="toggleFullScreen()" class="btn-fullscreen">📺 Fullscreen</button>
            <span>🔔</span>
            <span>⚙️</span>
            <span class="user-profile">👤 <?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </header>

    <div class="main-wrapper">

        <aside class="sidebar">
            <div class="menu-item active" onclick="switchView('inicio', this)">🏠 Inicio / Dashboard</div>
            <div class="menu-item" onclick="switchView('mm', this)">📦 MM - Inventario</div>
            <div class="menu-item" onclick="switchView('fico', this)">💰 FI/CO - Finanzas</div>
            <div class="menu-item" onclick="switchView('sd', this)">🛒 SD - Ventas</div>
            <div class="menu-item" onclick="switchView('crm', this)">👥 CRM - Clientes</div>
            <div class="menu-item" onclick="switchView('pp', this)">⚙️ PP - Producción</div>
            <div class="menu-item" onclick="switchView('qm', this)">🛡️ QM - Calidad</div>
        </aside>

        <main class="main-content">

            <div id="view-inicio" class="view-section active">
                <div class="welcome-banner">
                    <h2>¡Bienvenido al Sistema de Gestión Integrado!</h2>
                    <p>Monitoreo empresarial en tiempo real impulsado por IA y analítica predictiva.</p>
                </div>
                
                <div class="grid-3">
                    <div class="card">
                        <h4>💰 Ingresos del Mes</h4>
                        <div class="metric">$<?php echo number_format($ingresos_totales); ?> USD</div>
                    </div>
                    <div class="card">
                        <h4>📦 Capacidad Almacén</h4>
                        <div class="metric">82% Ocupado</div>
                    </div>
                    <div class="card">
                        <h4>🛒 Pedidos Procesados</h4>
                        <div class="metric"><?php echo number_format($total_ordenes_sistema); ?> Órdenes</div>
                    </div>
                </div>

                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>📊 Rendimiento Operativo de la Empresa</h3><br>
                        <div style="position: relative; height:260px; width:100%;">
                            <canvas id="chartRendimiento"></canvas>
                        </div>
                    </div>
                    <div class="card">
                        <h3>📦 Distribución de Almacén</h3><br>
                        <div style="position: relative; height:200px; width:100%; display:flex; justify-content:center;">
                            <canvas id="chartAlmacen"></canvas>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <h3>💡 Asistente IA - Alertas de Control</h3>
                    <div class="ia-box">
                        <div class="ia-item"><span class="dot green"></span> <strong>IA Audit:</strong> 0 intentos de fraude detectados hoy.</div>
                        <div class="ia-item"><span class="dot blue"></span> <strong>MM Optimization:</strong> Ruta de distribución de materiales calculada con éxito.</div>
                        <div class="ia-item"><span class="dot yellow"></span> <strong>FI/CO Simulation:</strong> Liquidez proyectada estable con riesgo bajo.</div>
                    </div>
                </div>
            </div>

            <div id="view-mm" class="view-section">
                <div class="alert-danger-custom">
                    <strong>⚠️ Alerta de Suministros:</strong> Existen <span id="criticos-count"><?php echo $productos_criticos; ?></span> materiales bajo el umbral mínimo de seguridad.
                </div>

                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>📋 Control de Stock Activo (Inventario MM)</h3>
                        <table class="sap-table">
                            <thead>
                                <tr><th>ID Material</th><th>Descripción</th><th>Existencias</th><th>Estado Alerta</th></tr>
                            </thead>
                            <tbody id="inventario-dinamico-body">
                                </tbody>
                        </table>
                    </div>
                    <div class="card">
                        <h3>🤝 Proveedores Homologados</h3><br>
                        <p><strong>🏢 Aceros Bolivia S.A.</strong><br><small style="color:var(--texto-secundario)">Contacto Directo: J. Pérez</small></p><br>
                        <p><strong>🏭 Distribuidora Industrial</strong><br><small style="color:var(--texto-secundario)">Contacto Directo: M. Gómez</small></p>
                    </div>
                </div>
            </div>

            <div id="view-fico" class="view-section">
                <div class="view-header-actions" style="display:flex; justify-content:flex-end; margin-bottom: 5px;">
                    <button class="btn-sap btn-primary" onclick="exportarBalancePDF()">📊 Generar Reporte PDF Oficial</button>
                </div>

                <div id="reporte-financiero" style="display:flex; flex-direction:column; gap:20px; background:#F4F6F9; padding:10px; border-radius:8px;">
                    <div class="pdf-only-header" style="display:none; border-bottom:2px solid #0A2540; padding-bottom:15px; margin-bottom:15px;">
                        <h1 style="color:#0A2540; margin:0;">ERPSystem - Balance de Tesorería</h1>
                        <p style="color:#718096; margin:5px 0 0 0; font-size:13px;">Consultor Responsable: <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
                    </div>

                    <div class="grid-3">
                        <div class="card">
                            <h4>💳 Cuentas por Cobrar (Sincronizado)</h4>
                            <div class="metric">$<?php echo number_format($cuentas_por_cobrar_base, 2); ?> USD</div>
                        </div>
                        <div class="card">
                            <h4>📉 Cuentas por Pagar</h4>
                            <div class="metric">$45,120.00 USD</div>
                        </div>
                        <div class="card">
                            <h4>🛡️ Nivel de Riesgo Corporativo</h4>
                            <div class="metric" style="color: var(--exito-ia);">Bajo (0.02%)</div>
                        </div>
                    </div>

                    <div class="table-container">
                        <h3>🔄 Libro Mayor y Conciliación Contable en Vivo</h3><br>
                        <table class="sap-table">
                            <thead>
                                <tr><th>Transacción Documentada</th><th>Monto Liquidado</th><th>Auditoría Digital</th></tr>
                            </thead>
                            <tbody id="finanzas-dinamicas-body">
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="view-sd" class="view-section">
                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>🛒 Registrar Orden de Venta de Materiales (SD)</h3><br>
                        <form id="form-nuevo-pedido">
                            <div class="form-group">
                                <label>Cliente Solicitante:</label>
                                <select name="cliente" class="sap-select-input">
                                    <option value="Constructora del Oriente S.R.L.">Constructora del Oriente S.R.L.</option>
                                    <option value="Cliente ABC">Cliente ABC</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Material Demandado (SKU):</label>
                                <select name="producto_id" id="select-producto" class="sap-select-input" onchange="actualizarPrevisualizacion()">
                                    <option value="">-- Seleccionar Material --</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM productos");
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$row['id']}' data-stock='{$row['stock']}' data-nombre='{$row['nombre']}'>{$row['id']} - {$row['nombre']} (Stock: {$row['stock']})</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cantidad Solicitada:</label>
                                <input type="number" name="cantidad" id="input-cantidad" value="100" class="sap-text-input" oninput="actualizarPrevisualizacion()">
                            </div>

                            <div class="form-group" style="background:#F8FAFC; padding:15px; border-radius:6px; border:1px solid var(--borde);">
                                <label style="display:flex; justify-content:between; align-items:center;">
                                    <span>🎯 Descuento Comercial / Margen:</span>
                                    <span id="valor-descuento-txt" style="color:var(--sidebar-activo); font-weight:bold; margin-left:auto;">0%</span>
                                </label>
                                <input type="range" name="descuento" id="input-descuento" min="0" max="30" value="0" step="5" style="width:100%; margin-top:8px; cursor:pointer;" oninput="actualizarPrevisualizacion()">
                            </div>

                            <div class="form-group">
                                <label>Condición Comercial / Pago:</label>
                                <select name="pago" class="sap-select-input">
                                    <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                    <option value="Crédito Documentario">Crédito Documentario</option>
                                </select>
                            </div>

                            <div id="preview-orden" class="ia-box" style="background:#F8FAFC; display:none; margin-bottom:15px;">
                                <strong id="preview-texto"></strong>
                            </div>

                            <button type="submit" class="btn-sap btn-primary">💾 Guardar Pedido</button>
                        </form>
                    </div>

                    <div class="card">
                        <h3>📑 Historial Reciente de Órdenes</h3><br>
                        <div id="historial-pedidos-ajax">
                            </div>
                    </div>
                </div>
            </div>

            <div id="view-crm" class="view-section">
                <div class="welcome-banner" style="border-left: 5px solid #D69E2E; background: #FEFCBF;">
                    <span class="badge badge-warning">⭐ Cliente VIP</span>
                    <h2 style="margin-top:10px;">👤 Ficha Comercial: Constructora del Oriente S.R.L.</h2>
                    <p>Jurisdicción: Bolivia | Última interacción del Account Manager: Hace 2 horas</p>
                </div>

                <div class="table-container" style="border-left: 5px solid #3182ce;">
                    <h3>✨ Recomendaciones Predictivas de Venta Cruzada (SAP HANA Engine)</h3><br>
                    <p style="color:var(--texto-secundario); font-size:14px; margin-bottom:15px;">
                        Basado en el comportamiento de compra detectado en XAMPP, la IA sugiere priorizar:
                    </p>
                    
                    <div class="grid-3">
                        <div class="card" style="text-align:center; transition: transform 0.2s; <?php echo $top_producto=='PROD1' ? 'background:#EBF8FF; border:2px solid #3182CE; transform:scale(1.03);':''; ?>">
                            <strong>Planchas Alum.</strong><br><br>
                            <span class="badge <?php echo $top_producto=='PROD1'?'badge-danger':'badge-success'; ?>">
                                <?php echo $top_producto=='PROD1'?'🔥 Top Adquirido':'Probabilidad: 94%'; ?>
                            </span>
                        </div>
                        <div class="card" style="text-align:center; transition: transform 0.2s; <?php echo $top_producto=='PROD2' ? 'background:#EBF8FF; border:2px solid #3182CE; transform:scale(1.03);':''; ?>">
                            <strong>Pernos 3/4</strong><br><br>
                            <span class="badge <?php echo $top_producto=='PROD2'?'badge-danger':'badge-success'; ?>">
                                <?php echo $top_producto=='PROD2'?'🔥 Top Adquirido':'Probabilidad: 88%'; ?>
                            </span>
                        </div>
                        <div class="card" style="text-align:center; transition: transform 0.2s; <?php echo $top_producto=='PROD3' ? 'background:#EBF8FF; border:2px solid #3182CE; transform:scale(1.03);':''; ?>">
                            <strong>Tubos PVC 2"</strong><br><br>
                            <span class="badge <?php echo $top_producto=='PROD3'?'badge-danger':'badge-success'; ?>">
                                <?php echo $top_producto=='PROD3'?'🔥 Top Adquirido':'Probabilidad: 71%'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="view-pp" class="view-section">
                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>⚙️ Orden de Proceso / Fabricación (Módulo PP)</h3><br>
                        <form id="form-nueva-produccion">
                            <div class="form-group">
                                <label>Lote / Producto Objetivo:</label>
                                <select name="lote_nombre" class="sap-select-input">
                                    <option value="Estructuras Alum. Serie A">Estructuras Alum. Serie A</option>
                                    <option value="Ensamblaje Conectores Cobre">Ensamblaje Conectores Cobre</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Materia Prima a Consumir:</label>
                                <select name="producto_id" class="sap-select-input" required>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM productos");
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$row['id']}'>Materia Prima: {$row['nombre']} (Disp: {$row['stock']})</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cantidad de Material Base a Reducir:</label>
                                <input type="number" name="cantidad" value="5" min="1" class="sap-text-input" required>
                            </div>
                            <button type="submit" class="btn-sap btn-primary">⚡ Ejecutar Orden de Fabricación</button>
                        </form>
                    </div>

                    <div class="card">
                        <h3>📑 Monitor de Órdenes PP Activas</h3><br>
                        <div id="historial-produccion-ajax">
                            </div>
                    </div>
                </div>
            </div>

            <div id="view-qm" class="view-section">
                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>🛡️ Laboratorio de Auditoría y Ensayos Técnicos (QM)</h3><br>
                        <p style="color:var(--texto-secundario); font-size:14px; margin-bottom:15px;">
                            Monitoreo de especificaciones críticas y decisiones de empleo para liberación de lotes:
                        </p>
                        <div id="inspecciones-calidad-ajax" class="ia-box" style="background:#FFF;">
                            </div>
                    </div>
                    <div class="card">
                        <h3>📊 Estado de Certificaciones</h3><br>
                        <p>🟢 <b>ISO 9001:2015</b> - Conforme</p><hr style="margin:10px 0; border:0; border-top:1px solid var(--borde);">
                        <p>🟢 <b>Auditoría Interna</b> - Aprobada sin no-conformidades</p>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    const ERP_INGRESOS_TOTALES = <?php echo $ingresos_totales; ?>;
</script>
<script src="script.js"></script>
</body>
</html>