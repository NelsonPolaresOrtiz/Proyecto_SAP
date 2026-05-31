<?php
include 'conexion.php';

try {
    $stmt = $pdo->query("SELECT * FROM productos");

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $badgeClass = 'badge-success';
        if($row['estado'] == 'Crítico') $badgeClass = 'badge-danger';
        if($row['estado'] == 'Reabastecer') $badgeClass = 'badge-warning';

        echo "<tr>";
        echo "<td><b>" . htmlspecialchars($row['id']) . "</b></td>";
        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
        echo "<td>" . htmlspecialchars($row['stock']) . " un</td>";
        echo "<td><span class='badge {$badgeClass}'>" . htmlspecialchars($row['estado']) . "</span></td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='4' style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>