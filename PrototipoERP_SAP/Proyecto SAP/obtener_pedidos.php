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
        
        // Formateamos el estado visualmente igual que las especificaciones
        $badgeClass = 'badge-success';
        if($row['status'] == 'Enviado') {
            $badgeClass = 'badge-warning'; // Azul o amarillo según tus bocetos
        }

        echo "<div style='margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--borde); font-family: sans-serif;'>";
        echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;'>";
        echo "<strong style='color: var(--azul-corporativo); font-size: 14px;'>Pedido #55" . htmlspecialchars($row['id']) . "</strong>";
        echo "<span class='badge {$badgeClass}' style='font-size: 11px;'>🔵 " . htmlspecialchars($row['status']) . "</span>";
        echo "</div>";
        echo "<p style='font-size: 13px; color: var(--texto-principal); margin-bottom: 4px;'><b>Cliente:</b> " . htmlspecialchars($row['cliente']) . "</p>";
        echo "<p style='font-size: 12px; color: var(--texto-secundario);'>Material: " . htmlspecialchars($row['prod_nombre']) . " (" . htmlspecialchars($row['cantidad']) . " un)</p>";
        echo "</div>";
    }

    if (!$hay_pedidos) {
        echo "<p style='color: var(--texto-secundario); font-size: 13px; text-align: center; padding: 20px;'>No hay órdenes registradas en esta sesión.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: var(--alerta-critico); font-size: 12px;'>Error al cargar historial: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>