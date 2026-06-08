<?php
include 'conexion.php';

try {
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id ASC");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- MEJORA: Lógica de respaldo si la tabla está vacía ---
    if (empty($registros)) {
        $registros = [
            ['id' => 'MAT-001', 'nombre' => 'Láminas de Acero A36', 'stock' => 450, 'estado' => 'Estable'],
            ['id' => 'MAT-002', 'nombre' => 'Electrodos E7018', 'stock' => 8, 'estado' => 'Crítico'],
            ['id' => 'MAT-003', 'nombre' => 'Pernos Hex. 1/2"', 'stock' => 95, 'estado' => 'Estable'],
            ['id' => 'MAT-004', 'nombre' => 'Discos de Corte 7"', 'stock' => 22, 'estado' => 'Reabastecer']
        ];
    }

    foreach ($registros as $index => $row) {
        $badgeClass = ($row['estado'] == 'Crítico') ? 'badge-danger' : (($row['estado'] == 'Reabastecer') ? 'badge-warning' : 'badge-success');
        
        $precio_simulado = 150;
        $valor_total = $row['stock'] * $precio_simulado;
        
        // Simulación dinámica de ubicaciones
        $id_num = (isset($row['id'])) ? preg_replace('/[^0-9]/', '', $row['id']) : ($index + 1);
        $ubicacion = "📍 Pasillo " . ($id_num % 2 == 0 ? "Norte" : "Sur") . " - Rack " . chr(65 + ($id_num % 3));
        $lote = "LT-2026-" . str_pad($id_num, 3, "0", STR_PAD_LEFT);

        // Estilo Zebra (fondo ligeramente gris en pares)
        $bg = ($index % 2 == 0) ? "#FFFFFF" : "#F7FAFC";

        echo "<tr style='background-color: {$bg}; transition: background 0.3s;' onmouseover='this.style.background=\"#EDF2F7\"' onmouseout='this.style.background=\"{$bg}\"'>";
        echo "<td style='padding: 12px 14px;'><b>" . htmlspecialchars($row['id']) . "</b></td>";
        echo "<td style='padding: 12px 14px; color: var(--azul-corporativo); font-weight: 500;'>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td style='padding: 12px 14px; color: #718096; font-size: 13px;'>" . $ubicacion . "</td>";
        echo "<td style='padding: 12px 14px; font-family: monospace; font-size: 12px; color: #4A5568;'>" . $lote . "</td>";
        echo "<td style='padding: 12px 14px;'><strong>" . htmlspecialchars($row['stock']) . " un</strong></td>";
        echo "<td style='padding: 12px 14px; font-weight: 600; color: #2B6CB0;'>$" . number_format($valor_total, 2) . " USD</td>";
        echo "<td style='padding: 12px 14px;'><span class='badge {$badgeClass}' style='padding: 4px 8px; font-size: 11px;'>" . htmlspecialchars($row['estado']) . "</span></td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='7' style='text-align:center; padding:20px; color:red;'>Error de conexión: " . $e->getMessage() . "</td></tr>";
}
?>