<?php
include 'conexion.php';

try {
    // Consultamos las últimas 5 órdenes registradas haciendo un JOIN para traer el nombre del producto
    $stmt = $pdo->query("SELECT p.*, prod.nombre as prod_nombre 
                         FROM pedidos p 
                         LEFT JOIN productos prod ON p.producto_id = prod.id 
                         ORDER BY p.id DESC 
                         LIMIT 5");

    $hay_pedidos = false;

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $hay_pedidos = true;
        
        // Formateamos las clases de Badges según el estatus real del flujo SD
        $badgeClass = 'badge-success';
        if($row['status'] == 'Enviado' || $row['status'] == 'Pendiente') {
            $badgeClass = 'badge-warning'; 
        }

        // Recuperamos el canal de pago guardado (o asignamos uno estándar si viene vacío)
        $metodo_pago = !empty($row['pago']) ? htmlspecialchars($row['pago']) : 'Transferencia Directa';

        echo "<div style='margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--borde); font-family: sans-serif;'>";
        
        // Encabezado del Bloque: ID de Documento Comercial y Badge Fiori
        echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;'>";
        echo "<strong style='color: var(--azul-corporativo); font-size: 13.5px;'>Documento SD-#55" . htmlspecialchars($row['id']) . "</strong>";
        echo "<span class='badge {$badgeClass}' style='font-size: 11px;'>🔵 " . htmlspecialchars($row['status']) . "</span>";
        echo "</div>";
        
        // Fila 1: Datos del Cliente Solicitante
        echo "<p style='font-size: 13px; color: var(--texto-principal); margin-bottom: 4px;'><b>Cliente:</b> " . htmlspecialchars($row['cliente']) . "</p>";
        
        // Fila 2: SKU Detallado y Unidades
        echo "<p style='font-size: 12px; color: var(--texto-secundario); margin-bottom: 4px;'><b>Material:</b> " . htmlspecialchars($row['prod_nombre']) . " (" . htmlspecialchars($row['producto_id']) . ")</p>";
        
        // Fila 3: Canal y Términos Comerciales (DENSIDAD EXTRA)
        echo "<p style='font-size: 11.5px; color: var(--texto-secundario); margin-bottom: 6px;'><b>Términos:</b> " . $metodo_pago . " | Vol: " . htmlspecialchars($row['cantidad']) . " un.</p>";
        
        // Fila 4: Valoración Financiera Comercial Cruzada con FI/CO (RELLENA EL ESPACIO PERFECTAMENTE)
        echo "<div style='text-align: right; font-size: 13px; color: var(--sidebar-activo); font-weight: bold;'>Valor Neto: $" . number_format($row['total'], 2) . " USD</div>";
        
        echo "</div>";
    }

    if (!$hay_pedidos) {
        echo "<p style='color: var(--texto-secundario); font-size: 13px; text-align: center; padding: 25px;'>No se registran órdenes comerciales en este mandante.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: var(--alerta-critico); font-size: 12px; padding:10px;'>Error SD-SLS Core: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>