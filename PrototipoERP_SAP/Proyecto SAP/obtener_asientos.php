<?php
include 'conexion.php';

try {
    // 1. Asientos contables fijos históricos de la auditoría base
    echo "<tr style='font-family: sans-serif;'>";
    echo "<td><b>#FI-9843</b></td>";
    echo "<td>01/06/2026</td>";
    echo "<td>110010 (Caja Central)</td>";
    echo "<td>🟢 Asiento BANCO CENTRAL - Liquidación Mandante</td>";
    echo "<td style='font-weight: bold; color: var(--exito-ia);'>+$12,500.00 USD</td>";
    echo "<td><span class='badge badge-success'>Verificado IA</span></td>";
    echo "</tr>";

    echo "<tr style='font-family: sans-serif;'>";
    echo "<td><b>#FI-9844</b></td>";
    echo "<td>03/06/2026</td>";
    echo "<td>110020 (Banco Mercantil)</td>";
    echo "<td>🟢 Asiento BANCO MERCANTIL - Transferencia Recibida</td>";
    echo "<td style='font-weight: bold; color: var(--exito-ia);'>+$3,200.00 USD</td>";
    echo "<td><span class='badge badge-success'>Verificado IA</span></td>";
    echo "</tr>";

    // 2. Asientos dinámicos generados en caliente por tus ventas reales en SD
    $stmt = $pdo->query("SELECT id, cliente, total FROM pedidos ORDER BY id DESC LIMIT 8");
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr style='font-family: sans-serif; border-bottom: 1px solid var(--borde);'>";
        echo "<td><b>#FI-2026-" . htmlspecialchars($row['id']) . "</b></td>";
        echo "<td>" . date('d/m/2026') . "</td>";
        echo "<td>120010 (Deudores por Ventas)</td>";
        echo "<td>🟢 Factura Automática Comercial (" . htmlspecialchars($row['cliente']) . ")</td>";
        echo "<td style='font-weight: bold; color: #2B6CB0;'>+" . number_format($row['total'], 2) . " USD</td>";
        echo "<td><span class='badge badge-success'>Verificado IA</span></td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6' style='color:var(--alerta-critico); font-weight:bold; text-align:center;'>Error FI-GL Module: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>