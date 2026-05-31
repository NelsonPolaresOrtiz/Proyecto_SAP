<?php
include 'conexion.php';

try {
    // Primero listamos las dos transacciones fijas exigidas por los bocetos
    echo "<tr><td>🟢 Transacción #9843 - BANCO CENTRAL</td><td>$12,500.00 USD</td><td><span class='badge badge-success'>Verificado IA</span></td></tr>";
    echo "<tr><td>🟢 Transacción #9844 - BANCO MERCANTIL</td><td>$3,200.00 USD</td><td><span class='badge badge-success'>Verificado IA</span></td></tr>";

    // Luego listamos dinámicamente las generadas por Nelson en el sistema
    $stmt = $pdo->query("SELECT p.id, p.cliente, p.total FROM pedidos p ORDER BY p.id DESC");
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>🟢 Factura Automática #FI-2026-" . htmlspecialchars($row['id']) . " (" . htmlspecialchars($row['cliente']) . ")</td>";
        echo "<td>$" . number_format($row['total'], 2) . " USD</td>";
        echo "<td><span class='badge badge-success'>Verificado IA</span></td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='3' style='color:red;'>Error al cargar el libro mayor: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>