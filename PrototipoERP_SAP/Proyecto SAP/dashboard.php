<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

include 'conexion.php';

// Aseguramos un rol por defecto si la sesión viene limpia para evitar quiebres
if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'MM_Operario'; 
}

// --- CONTROL DE TRANSACCIONES OPERATIVAS EN CALIENTE ---
$stmt_ingresos = $pdo->query("SELECT SUM(total) as nuevos_ingresos, COUNT(*) as total_nuevos FROM pedidos");
$res_ingresos = $stmt_ingresos->fetch(PDO::FETCH_ASSOC);

$nuevas_ventas_valor = $res_ingresos['nuevos_ingresos'] ?? 0;
$cantidad_nuevos_pedidos = $res_ingresos['total_nuevos'] ?? 0;

// KPIs consolidados cruzados
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
        <div class="logo">ERPSystem <span style="font-size:11px; color:var(--exito-ia); font-weight:bold;">S/4HANA Core</span></div>
        <input type="text" class="search-bar" id="global-search" placeholder="Buscar procesos, transacciones..." onkeyup="filtrarContenido()">
        <div class="user-area">
            <button onclick="toggleFullScreen()" class="btn-fullscreen">📺 Fullscreen</button>
            <span>🔔</span>
            <span>⚙️</span>
            <span class="user-profile">👤 <?php echo htmlspecialchars($_SESSION['usuario']); ?> 
                (<span style="color:var(--exito-ia); font-weight:bold;"><?php echo $_SESSION['rol'] === 'Admin' ? 'Consultor Senior / Admin' : ($_SESSION['rol'] === 'FICO_Analista' ? 'Analista FICO' : 'Operario MM'); ?></span>)
            </span>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </header>

    <div class="main-wrapper">

        <aside class="sidebar">
            <div class="menu-item active" onclick="switchView('inicio', this)">🏠 Inicio / Dashboard</div>
            
            <?php if ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'MM_Operario'): ?>
                <div class="menu-item" onclick="switchView('mm', this)">📦 MM - Inventario</div>
            <?php endif; ?>
            
            <?php if ($_SESSION['rol'] === 'Admin' || $_SESSION['rol'] === 'FICO_Analista'): ?>
                <div class="menu-item" onclick="switchView('fico', this)">💰 FI/CO - Finanzas</div>
            <?php endif; ?>
            
            <?php if ($_SESSION['rol'] === 'Admin'): ?>
                <div class="menu-item" onclick="switchView('sd', this)">🛒 SD - Ventas</div>
                <div class="menu-item" onclick="switchView('crm', this)">👥 CRM - Clientes</div>
                <div class="menu-item" onclick="switchView('pp', this)">⚙️ PP - Producción</div>
                <div class="menu-item" onclick="switchView('qm', this)">🛡️ QM - Calidad</div>
            <?php else: ?>
                <div class="menu-item" style="opacity: 0.35; cursor: not-allowed; background: none; color: var(--texto-secundario);" title="Módulo restringido por políticas de seguridad SAP">🔒 SD - Ventas (Bloqueado)</div>
                <div class="menu-item" style="opacity: 0.35; cursor: not-allowed; background: none; color: var(--texto-secundario);" title="Módulo restringido por políticas de seguridad SAP">🔒 CRM - Clientes (Bloqueado)</div>
                <div class="menu-item" style="opacity: 0.35; cursor: not-allowed; background: none; color: var(--texto-secundario);" title="Módulo restringido por políticas de seguridad SAP">🔒 PP - Producción (Bloqueado)</div>
                <div class="menu-item" style="opacity: 0.35; cursor: not-allowed; background: none; color: var(--texto-secundario);" title="Módulo restringido por políticas de seguridad SAP">🔒 QM - Calidad (Bloqueado)</div>
            <?php endif; ?>
        </aside>

        <main class="main-content">

            <div id="view-inicio" class="view-section active">
                <div class="welcome-banner">
                    <h2>Centro de Mando Principal - Monitoreo de Mandante en Vivo</h2>
                    <p>Auditoría y control analítico de procesos de negocio respaldados por motores predictivos.</p>
                </div>
                
                <div class="grid-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; width: 100%;">
                    <div class="card">
                        <h4>💰 Ingresos Consolidados</h4>
                        <div class="metric" style="font-size:26px;">$<?php echo number_format($ingresos_totales); ?></div>
                        <small style="color:var(--exito-ia); font-size:11px; font-weight:600;">▲ 14.2% Eficiencia Fiscal</small>
                    </div>
                    <div class="card">
                        <h4>📦 Ocupación Almacén</h4>
                        <div class="metric" style="font-size:26px;">82% Ocupado</div>
                        <small style="color:var(--texto-secundario); font-size:11px;">Capacidad Física Central</small>
                    </div>
                    <div class="card">
                        <h4>🛒 Pedidos Procesados</h4>
                        <div class="metric" style="font-size:26px;"><?php echo number_format($total_ordenes_sistema); ?> Ud.</div>
                        <small style="color:var(--sidebar-activo); font-size:11px;">Documentos comerciales SD</small>
                    </div>
                    <div class="card">
                        <h4>🛡️ Lotes en Inspección</h4>
                        <div class="metric" style="font-size:26px;">3 Órdenes</div>
                        <small style="color:#DD6B20; font-size:11px; font-weight:600;">En cola de laboratorio QM</small>
                    </div>
                </div>

                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>📊 Rendimiento Operativo de la Empresa (Ventas del Semestre)</h3><br>
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
                    <h3>💡 Asistente IA - Alertas de Control y Optimización S/4HANA</h3><br>
                    <div class="ia-box">
                        <div class="ia-item"><span class="dot green"></span> <strong>IA Audit:</strong> 0 intentos de fraude o desviaciones contables detectados hoy en el Libro Contable.</div>
                        <div class="ia-item"><span class="dot blue"></span> <strong>MM Optimization:</strong> Algoritmo de distribución de materiales calculated. Sugerencia: Reabastecer Pernos 3/4.</div>
                        <div class="ia-item"><span class="dot yellow"></span> <strong>FI/CO Simulation:</strong> Liquidez proyectada estable. Índice de solvencia corporativa en 98.4%.</div>
                    </div>
                </div>
            </div>

            <div id="view-mm" class="view-section">
                <div class="alert-danger-custom">
                    <strong>⚠️ Alerta de Suministros (MM-IM):</strong> Existen <span id="criticos-count" style="font-weight:bold;"><?php echo $productos_criticos; ?></span> materiales bajo el umbral mínimo de seguridad.
                </div>

                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>📋 Control de Stock Activo (Inventario Fiori MM-IM)</h3>
                        <table class="sap-table">
                            <thead>
                                <tr>
                                    <th>ID Material</th>
                                    <th>Descripción Técnica</th>
                                    <th>📍 Ubicación Almacén</th>
                                    <th>Lote Control QM</th>
                                    <th>Existencias</th>
                                    <th>Valor Activo (FICO)</th>
                                    <th>Estado Alerta</th>
                                </tr>
                            </thead>
                            <tbody id="inventario-dinamico-body"></tbody>
                        </table>
                    </div>
                    
                    <div style="display:flex; flex-direction:column; gap:20px;">
                        <div class="card">
                            <h3>🤝 Proveedores Homologados</h3><br>
                            <p><strong>🏢 Aceros Bolivia S.A.</strong><br><small style="color:var(--texto-secundario)">Contacto Directo: J. Pérez | Término: Net 30</small></p><br>
                            <p><strong>🏭 Distribuidora Industrial</strong><br><small style="color:var(--texto-secundario)">Contacto Directo: M. Gómez | Término: Contado</small></p>
                        </div>
                        
                        <div class="card" style="background:#FAFAFA; border:1px dashed var(--borde); padding: 15px; border-radius: 8px;">
                            <h4 style="color: var(--azul-corporativo); font-size: 15px; margin-bottom: 8px; font-weight:bold;">📦 Submódulo MM-PUR (Sugerencias de Compra por IA)</h4>
                            <p style="font-size:12px; color:var(--texto-secundario); margin-bottom: 12px; line-height: 1.4;">
                                El motor analítico detecta materiales bajo el stock de seguridad y genera propuestas de reabastecimiento automáticas:
                            </p>
                            
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <?php
                                $stmt_pur = $pdo->query("SELECT id, nombre, stock, estado FROM productos WHERE estado IN ('Crítico', 'Reabastecer') ORDER BY stock ASC LIMIT 3");
                                $compras_generadas = false;

                                while($pur = $stmt_pur->fetch(PDO::FETCH_ASSOC)) {
                                    $compras_generadas = true;
                                    $cantidad_sugerida = ($pur['estado'] == 'Crítico') ? 500 : 250;
                                    $color_alerta = ($pur['estado'] == 'Crítico') ? '#E53E3E' : '#DD6B20';
                                    
                                    echo "<div style='background: #FFF; border-left: 4px solid {$color_alerta}; padding: 10px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); font-family: sans-serif;'>";
                                    echo "<div style='display:flex; justify-content: space-between; align-items:center;'>";
                                    echo "<span style='font-size:12.5px; font-weight:bold; color:var(--texto-principal);'>" . htmlspecialchars($pur['id']) . "</span>";
                                    echo "<span style='font-size:11px; font-weight:bold; color:{$color_alerta};'>[" . htmlspecialchars($pur['estado']) . "]</span>";
                                    echo "</div>";
                                    echo "<p style='font-size:11.5px; color: var(--texto-secundario); margin-top: 4px; margin-bottom:0;'>";
                                    echo "Sugerencia MM-PUR: Emitir SolP de <span style='color:#2B6CB0; font-weight:bold;'>" . $cantidad_sugerida . " un.</span> para " . htmlspecialchars($pur['nombre']);
                                    echo "</p>";
                                    echo "</div>";
                                }

                                if(!$compras_generadas) {
                                    echo "<div style='background: #EBF8FF; border-left: 4px solid #3182CE; padding: 10px; border-radius: 4px; font-size:12px; color: #2B6CB0;'>";
                                    echo "✓ Nivel de servicio óptimo. No se requieren órdenes de compra urgentes.";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-container" style="margin-top: 10px;">
                    <h3>🔄 MM-IM: Registro de Movimiento de Mercancías / Documentos de Material (Últimas Horas)</h3>
                    <table class="sap-table" style="font-size: 13px; margin-top: 15px;">
                        <thead>
                            <tr><th>Doc. Material</th><th>Fecha/Hora</th><th>Clase Movimiento</th><th>Texto Material</th><th>Cantidad</th><th>Usuario Responsable</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><b>#100432</b></td><td>Hoy 14:32</td><td>101 (Entrada de Mercancías)</td><td>PROD001 - Planchas Alum.</td><td>+50 un</td><td><span>👤 <?php echo htmlspecialchars($_SESSION['usuario']); ?></span></td></tr>
                            <tr><td><b>#100431</b></td><td>Hoy 11:15</td><td>261 (Consumo para Orden PP)</td><td>PROD004 - Cable Cobre</td><td>-15 un</td><td>👤 Sistema Automatizado</td></tr>
                            <tr><td><b>#100430</b></td><td>Ayer 18:20</td><td>601 (Salida de Mercancías - SD)</td><td>PROD003 - Tubos PVC</td><td>-100 un</td><td><span>👤 <?php echo htmlspecialchars($_SESSION['usuario']); ?></span></td></tr>
                        </tbody>
                    </table>
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
                            <h4>💳 FI-AR: Cuentas por Cobrar (Sincronizado)</h4>
                            <div class="metric">$<?php echo number_format($cuentas_por_cobrar_base, 2); ?> USD</div>
                            <small style="color:var(--exito-ia); font-size:11px; font-weight:600;">🟢 Flujo de Entrada Asegurado</small>
                        </div>
                        <div class="card">
                            <h4>📉 FI-AP: Cuentas por Pagar (Pasivos)</h4>
                            <div class="metric">$45,120.00 USD</div>
                            <small style="color:var(--alerta-critico); font-size:11px; font-weight:600;">🔴 3 Facturas Próximas a Vencer</small>
                        </div>
                        <div class="card">
                            <h4>🛡️ Nivel de Riesgo Corporativo</h4>
                            <div class="metric" style="color: var(--exito-ia);">Bajo (0.02%)</div>
                            <small style="color:var(--texto-secundario); font-size:11px;">Auditoría de Basilea III aprobada</small>
                        </div>
                    </div>

                    <div class="grid-layout-split">
                        <div class="table-container">
                            <h3>🔄 FI-GL: Libro Mayor y Conciliación Contable en Vivo</h3><br>
                            <table class="sap-table">
                                <thead>
                                    <tr>
                                        <th>Nro. Documento FI</th>
                                        <th>Fecha Contab.</th>
                                        <th>Cuenta de Mayor</th>
                                        <th>Texto Transacción Documentada</th>
                                        <th>Monto Liquidado</th>
                                        <th>Auditoría Digital</th>
                                    </tr>
                                </thead>
                                <tbody id="finanzas-dinamicas-body"></tbody>
                            </table>
                        </div>
                        
                        <div style="display:flex; flex-direction:column; gap:20px;">
                            <div class="card">
                                <h3>📊 CO-OM: Centros de Costo</h3><br>
                                <p><b>Planta Industrial:</b> $88,000.00 USD<br><span class="badge badge-success" style="font-size:10px; padding:2px 6px;">Asignado</span></p><hr style="margin:8px 0; border:0; border-top:1px solid var(--borde);">
                                <p><b>Distribución / Logística:</b> $45,000.00 USD<br><span class="badge badge-success" style="font-size:10px; padding:2px 6px;">Asignado</span></p><hr style="margin:8px 0; border:0; border-top:1px solid var(--borde);">
                                <p><b>Administración General:</b> $22,400.00 USD<br><span class="badge badge-success" style="font-size:10px; padding:2px 6px;">Asignado</span></p>
                            </div>

                            <div class="card" style="background:#FAFAFA; border:1px dashed var(--borde);">
                                <h3>🏛️ FI-AA: Control de Activos Fijos</h3><br>
                                <div style="font-size:12.5px; color:var(--texto-principal);">
                                    <p>• <b>Maquinaria de Planta:</b> $250,000.00 USD <br><small style="color:var(--texto-secundario);">Depreciación Anual: 10% (Método Lineal)</small></p><hr style="margin:6px 0; border:0; border-top:1px solid var(--borde);">
                                    <p>• <b>Flota de Transporte:</b> $115,000.00 USD <br><small style="color:var(--texto-secundario);">Amortización Acumulada Activa</small></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-layout-split" style="margin-top:5px;">
                        <div class="table-container">
                            <h3>📊 FI-AP: Libro Auxiliar de Proveedores (Pasivos y Obligaciones)</h3>
                            <table class="sap-table" style="font-size: 13px; margin-top: 15px;">
                                <thead>
                                    <tr><th>Factura ID</th><th>Proveedor Ref.</th><th>Fecha Emisión</th><th>Vencimiento</th><th>Monto Bruto</th><th>Estado Pago</th><th>Vía de Pago</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td><b>#INV-2026-9941</b></td><td>Aceros Bolivia S.A.</td><td>01/06/2026</td><td>30/06/2026</td><td>$12,400.00 USD</td><td><span class="badge badge-warning">Pendiente</span></td><td>Transferencia ACH</td></tr>
                                    <tr><td><b>#INV-2026-9942</b></td><td>Distribuidora Industrial</td><td>02/06/2026</td><td>15/06/2026</td><td>$5,120.00 USD</td><td><span class="badge badge-warning">En Revisión</span></td><td>Cheque Corporativo</td></tr>
                                    <tr><td><b>#INV-2026-9810</b></td><td>Suministros Chuquisaca</td><td>20/05/2026</td><td>04/06/2026</td><td>$27,600.00 USD</td><td><span class="badge badge-success">Liquidada</span></td><td>Transferencia ACH</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card" style="background:#FFFFFF; display:flex; flex-direction:column; justify-content:space-between;">
                            <div>
                                <h3>🎯 CO-PC: Control de Costos de Producto</h3><br>
                                <p style="font-size:13px; color:var(--texto-secundario); line-height: 1.5;">
                                    Análisis de desviaciones de manufactura cruzado con las órdenes liberadas del módulo de producción (PP):
                                </p>
                            </div>
                            <div style="background:#F8FAFC; padding:12px; border-radius:6px; font-size:12.5px; border:1px solid var(--borde);">
                                <p style="margin-bottom:4px;">• <b>Costo Estándar Calc:</b> $150.00 / un</p>
                                <p style="margin-bottom:4px;">• <b>Costo Real Registrado:</b> $148.20 / un</p>
                                <p style="color:var(--exito-ia); font-weight:700;">• Desviación General: -1.2% (Eficiente)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="view-sd" class="view-section">
                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>🛒 Registrar Orden de Venta de Materiales (SD-SLS)</h3><br>
                        <form id="form-nuevo-pedido">
                            <div class="form-group">
                                <label>Cliente Solicitante (Maestro de Clientes):</label>
                                <select name="cliente" class="sap-select-input">
                                    <option value="Constructora del Oriente S.R.L.">Constructora del Oriente S.R.L.</option>
                                    <option value="Cliente ABC">Cliente ABC</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Material Demandado (Módulo MM SKU):</label>
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
                                    <span>🎯 Descuento Comercial / Margen (SAP Pricing):</span>
                                    <span id="valor-descuento-txt" style="color:var(--sidebar-activo); font-weight:bold; margin-left:auto;">0%</span>
                                </label>
                                <input type="range" name="descuento" id="input-descuento" min="0" max="30" value="0" step="5" style="width:100%; margin-top:8px; cursor:pointer;" oninput="actualizarPrevisualizacion()">
                            </div>

                            <div class="form-group">
                                <label>Condición Comercial / Canal de Pago:</label>
                                <select name="pago" class="sap-select-input">
                                    <option value="Transferencia Bancaria">Transferencia Bancaria Directa</option>
                                    <option value="Crédito Documentario">Crédito Documentario 30 Días</option>
                                </select>
                            </div>

                            <div id="preview-orden" class="ia-box" style="background:#F8FAFC; display:none; margin-bottom:15px;">
                                <strong id="preview-texto"></strong>
                            </div>

                            <button type="submit" class="btn-sap btn-primary" style="width:100%;">💾 Guardar y Despachar Pedido Comercial</button>
                        </form>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:20px;">
                        <div class="card">
                            <h3>📑 Historial Reciente de Órdenes (SD)</h3><br>
                            <div id="historial-pedidos-ajax"></div>
                        </div>
                        
                        <div class="table-container" style="padding:15px;">
                            <h4>📋 SD-BIL: Lista de Precios Base</h4>
                            <table class="sap-table" style="font-size:12px; margin-top:10px;">
                                <thead><tr><th>SKU</th><th>Tarifa Base</th><th>Margen Max</th></tr></thead>
                                <tbody>
                                    <tr><td>PROD001</td><td>$150.00 USD</td><td>30% Max</td></tr>
                                    <tr><td>PROD002</td><td>$150.00 USD</td><td>30% Max</td></tr>
                                    <tr><td>PROD003</td><td>$150.00 USD</td><td>30% Max</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="table-container" style="margin-top:10px;">
                    <h3>🚚 SD-LE: Monitor de Embarque y Logística de Distribución Exterior</h3>
                    <table class="sap-table" style="font-size:13px; margin-top:15px;">
                        <thead>
                            <tr><th>Entrega Outbound</th><th>Destinatario</th><th>Transportista</th><th>Ruta de Salida</th><th>Estado Carga</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><b>#8002341</b></td><td>Constructora del Oriente S.R.L.</td><td>TransBolivia S.A.</td><td>Troncal Central - Sucre Hub</td><td><span class="badge badge-success">En Tránsito</span></td></tr>
                            <tr><td><b>#8002342</b></td><td>Cliente ABC Comercial</td><td>Distribución Local</td><td>Anillo Interno Distribución</td><td><span class="badge badge-warning">Listo para Despacho</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="view-crm" class="view-section">
                <div class="welcome-banner" style="border-left: 5px solid #D69E2E; background: #FEFCBF;">
                    <span class="badge badge-warning">⭐ Cliente VIP</span>
                    <h2 style="margin-top:10px;">👤 Ficha Comercial: Constructora del Oriente S.R.L.</h2>
                    <p>Jurisdicción: Bolivia | Sincronización analítica basada en transacciones SQL activas</p>
                </div>

                <div class="grid-layout-split">
                    <div class="table-container" style="border-left: 5px solid #3182ce;">
                        <h3>✨ Recomendaciones Predictivas de Venta Cruzada (SAP HANA Engine)</h3><br>
                        <p style="color:var(--texto-secundario); font-size:14px; margin-bottom:15px;">
                            Basado en el comportamiento de compra detectado en XAMPP, la IA sugiere priorizar:
                        </p>
                        
                        <div class="grid-3">
                            <div class="card" style="text-align:center; transition: transform 0.2s; <?php echo $top_producto=='PROD1' ? 'background:#EBF8FF; border:2px solid #3182CE; transform:scale(1.03);':''; ?>">
                                <strong>Planchas Alum.</strong><br><br>
                                <span class="badge <?php echo $top_producto=='PROD1'?'badge-danger':'badge-success'; ?>">
                                    <?php echo $top_producto=='PROD1'?'🔥 Top Acumulado':'Probabilidad: 94%'; ?>
                                </span>
                            </div>
                            <div class="card" style="text-align:center; transition: transform 0.2s; <?php echo $top_producto=='PROD2' ? 'background:#EBF8FF; border:2px solid #3182CE; transform:scale(1.03);':''; ?>">
                                <strong>Pernos 3/4</strong><br><br>
                                <span class="badge <?php echo $top_producto=='PROD2'?'badge-danger':'badge-success'; ?>">
                                    <?php echo $top_producto=='PROD2'?'🔥 Top Acumulado':'Probabilidad: 88%'; ?>
                                </span>
                            </div>
                            <div class="card" style="text-align:center; transition: transform 0.2s; <?php echo $top_producto=='PROD3' ? 'background:#EBF8FF; border:2px solid #3182CE; transform:scale(1.03);':''; ?>">
                                <strong>Tubos PVC 2"</strong><br><br>
                                <span class="badge <?php echo $top_producto=='PROD3'?'badge-danger':'badge-success'; ?>">
                                    <?php echo $top_producto=='PROD3'?'🔥 Top Acumulado':'Probabilidad: 71%'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <h3>📈 Índice de Retención</h3><br>
                        <div class="metric" style="color:var(--exito-ia); font-size:40px;">98.6%</div><br>
                        <p style="font-size:12.5px; color:var(--texto-secundario);">Cliente estratégico de alta prioridad con cumplimiento estricto de obligaciones crediticias.</p>
                    </div>
                </div>

                <div class="grid-layout-split" style="margin-top:10px;">
                    <div class="table-container">
                        <h3>📈 CRM-PIP: Embudo de Conversión y Negociaciones Abiertas</h3>
                        <table class="sap-table" style="font-size:13px;">
                            <thead><tr><th>Oportunidad Comercial</th><th>Etapa Pipeline</th><th>Volumen Estimado</th><th>Cierre Estimado</th></tr></thead>
                            <tbody>
                                <tr><td>Suministro Completo Complejo Vial</td><td><span class="badge badge-warning">En Negociación</span></td><td>$85,000.00 USD</td><td>22/07/2026</td></tr>
                                <tr><td>Lote Estructuras Refinería Sucre</td><td><span class="badge badge-success">Propuesta Presentada</span></td><td>$140,000.00 USD</td><td>05/08/2026</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card">
                        <h4>🛡️ Retención Activa</h4><br>
                        <p style="font-size:12.5px; color:var(--texto-secundario);">Índice de Riesgo de Churn (Abandono): <b>1.4% (Críticamente Bajo)</b>. Sistema HANA detecta lealtad comercial robusta.</p>
                    </div>
                </div>
            </div>

            <div id="view-pp" class="view-section">
                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>⚙️ Orden de Proceso / Fabricación (Módulo PP-SFC)</h3><br>
                        <form id="form-nueva-produccion">
                            <div class="form-group">
                                <label>Lote / Producto Objetivo:</label>
                                <select name="lote_nombre" class="sap-select-input">
                                    <option value="Estructuras Alum. Serie A">Estructuras Alum. Serie A</option>
                                    <option value="Ensamblaje Conectores Cobre">Ensamblaje Conectores Cobre</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Materia Prima a Consumir (Descuenta de Stock MM):</label>
                                <select name="producto_id" class="sap-select-input" required>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM productos");
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$row['id']}'>Materia Prima: {$row['nombre']} (Disp: " . htmlspecialchars($row['stock']) . " un)</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cantidad de Material Base a Reducir:</label>
                                <input type="number" name="cantidad" value="5" min="1" class="sap-text-input" required>
                            </div>
                            <button type="submit" class="btn-sap btn-primary" style="width:100%;">⚡ Ejecutar Orden de Fabricación Planta</button>
                        </form>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:20px;">
                        <div class="card">
                            <h3>📑 Monitor de Órdenes PP Activas en Planta</h3><br>
                            <div id="historial-produccion-ajax"></div>
                        </div>
                        <div class="card" style="background:#FAFAFA;">
                            <h4>🏭 Capacidad de Planta</h4>
                            <p style="font-size:12px; color:var(--texto-secundario); margin-top:5px;">Línea de producción 01 trabajando a un ritmo nominal estable del 87.5% de rendimiento general.</p>
                        </div>
                    </div>
                </div>

                <div class="table-container" style="margin-top:10px;">
                    <h3>🔧 PP-BD: Maestro de Hojas de Ruta y Estaciones de Trabajo Activas</h3>
                    <table class="sap-table" style="font-size:13px; margin-top:10px;">
                        <thead><tr><th>Work Center</th><th>Tipo Máquina</th><th>Operario Asignado</th><th>Carga de Trabajo</th><th>Siguiente Mtto PM</th></tr></thead>
                        <tbody>
                            <tr><td><b>WC-PLANTA01</b></td><td>Prensa Extrusora de Aluminio</td><td>Téc. Principal</td><td>🟩 68% Estable</td><td>20/07/2026</td></tr>
                            <tr><td><b>WC-LINEA02</b></td><td>Cortadora de Alta Precisión CNC</td><td>Téc. Asistente</td><td>🟨 82% Alta</td><td>15/07/2026</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="view-qm" class="view-section">
                <div class="grid-layout-split">
                    <div class="table-container">
                        <h3>🛡️ Laboratorio de Auditoría y Ensayos Técnicos (QM-IM)</h3><br>
                        <p style="color:var(--texto-secundario); font-size:14px; margin-bottom:15px;">
                            Monitoreo de especificaciones críticas y decisiones de empleo para liberación de lotes:
                        </p>
                        <div id="inspecciones-calidad-ajax" style="display:flex; flex-direction:column; gap:10px;"></div>
                    </div>
                    
                    <div class="card">
                        <h3>📊 Estado de Certificaciones</h3><br>
                        <p><span class="dot green"></span> <b>ISO 9001:2015</b> - Conforme</p><hr style="margin:12px 0; border:0; border-top:1px solid var(--borde);">
                        <p><span class="dot green"></span> <b>Auditoría de Control Interno</b> - Aprobada sin no-conformidades técnicas</p>
                    </div>
                </div>

                <div class="table-container" style="margin-top:10px;">
                    <h3>📋 QM-QC: Bitácora General de Eventos de Calidad y Acciones Correctivas</h3>
                    <table class="sap-table" style="font-size:13px; margin-top:10px;">
                        <thead><tr><th>Notificación QM</th><th>Material</th><th>Fecha Hallazgo</th><th>Defecto Detectado</th><th>Acción Tomada</th><th>Resultado Final</th></tr></thead>
                        <tbody>
                            <tr><td><b>#QM-20042</b></td><td>PROD002 - Pernos 3/4</td><td>Hoy 08:12</td><td>Desviación roscado > 0.05mm</td><td>Bloqueo preventivo en lote</td><td><span class="badge badge-danger">Retenido</span></td></tr>
                            <tr><td><b>#QM-20041</b></td><td>PROD001 - Planchas Alum.</td><td>Ayer 15:40</td><td>Rugosidad superficial límite</td><td>Liberación condicional</td><td><span class="badge badge-success">Aprobado</span></td></tr>
                        </tbody>
                    </table>
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