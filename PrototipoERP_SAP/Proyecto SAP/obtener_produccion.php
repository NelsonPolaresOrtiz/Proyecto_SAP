<?php
include 'conexion.php';

try {
    // 1. Registro histórico base para la simulación inicial requerida en planta
    echo "<div style='margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--borde); font-family: sans-serif;'>";
    echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;'>";
    echo "<strong style='color: var(--azul-corporativo); font-size: 14px;'>⚙️ Orden PP-#2026-001</strong>";
    echo "<span class='badge badge-success' style='font-size: 11px;'>🟢 Liberada y Cerrada</span>";
    echo "</div>";
    echo "<p style='font-size: 13px; color: var(--texto-principal); margin-bottom: 4px;'><b>Centro de Trabajo:</b> WC-PLANTA01 (Maquinaria Pesada)</p>";
    echo "<p style='font-size: 12px; color: var(--texto-secundario);'>Consumo: 5 unidades de PROD001 | <b>Tiempo de Máquina:</b> 42 min</p>";
    echo "</div>";

    // 2. Consulta dinámica cruzando las órdenes internas en caliente de MySQL
    $stmt = $pdo->query("SELECT p.id, p.producto_id, p.cantidad, prod.nombre as prod_nombre 
                         FROM pedidos p 
                         LEFT JOIN productos prod ON p.producto_id = prod.id 
                         WHERE p.pago = 'Imputación Interna CO' OR p.status = 'En Proceso'
                         ORDER BY p.id DESC 
                         LIMIT 4");

    $contador = 2;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $wc_asignado = ($contador % 2 == 0) ? "WC-LINEA02 (Extrusión CNC)" : "WC-LINEA03 (Ensamblaje Neumático)";
        $tiempo_estimado = ($row['cantidad'] * 3) + 8;

        echo "<div style='margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--borde); font-family: sans-serif;'>";
        echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;'>";
        echo "<strong style='color: var(--azul-corporativo); font-size: 14px;'>⚙️ Orden PP-#2026-00" . $contador . "</strong>";
        echo "<span class='badge' style='font-size: 11px; background:#EBF8FF; color:#2B6CB0;'>🔵 En Proceso</span>";
        echo "</div>";
        echo "<p style='font-size: 13px; color: var(--texto-principal); margin-bottom: 4px;'><b>Centro de Trabajo:</b> " . $wc_asignado . "</p>";
        echo "<p style='font-size: 12px; color: var(--texto-secundario);'><b>Deducción de Almacén:</b> " . htmlspecialchars($row['cantidad']) . " un. de " . htmlspecialchars($row['prod_nombre']) . " (" . htmlspecialchars($row['producto_id']) . ")</p>";
        echo "<small style='color: #DD6B20; font-size: 11px; font-weight:600;'>⏱️ Tiempo Corrida Est: " . $tiempo_estimado . " min | Pendiente de Aprobación Técnico</small>";
        echo "</div>";
        
        $contador++;
    }

} catch (PDOException $e) {
    echo "<div style='color: var(--alerta-critico); font-size: 13px; padding: 10px;'>Error PP-SFC Core: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>