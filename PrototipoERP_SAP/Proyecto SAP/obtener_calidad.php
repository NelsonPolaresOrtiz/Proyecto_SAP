<?php
include 'conexion.php';

// Renderizado dinámico de lotes pendientes de inspección QM
echo "<div class='bank-row' style='display:flex; justify-content:space-between; align-items:center; padding:12px; border-bottom:1px solid var(--borde); font-family:sans-serif;'>";
echo "<div>";
echo "<strong style='color:var(--azul-corporativo); font-size:14px;'>Muestreo Lote #QM-2026-A</strong>";
echo "<p style='font-size:12px; color:var(--texto-secundario); margin-top:2px;'>Origen: PP - Estructuras Alum. Serie A</p>";
echo "</div>";
echo "<div style='display:flex; gap:10px;'>";
echo "<button class='btn-sap btn-primary' style='padding:6px 12px; font-size:12px; background:var(--exito-ia);' onclick='procesarLoteQM(\"QM-2026-A\", \"Aprobado\")'>✓ Liberar</button>";
echo "<button class='btn-sap btn-primary' style='padding:6px 12px; font-size:12px; background:var(--alerta-critico);' onclick='procesarLoteQM(\"QM-2026-A\", \"Rechazado\")'>✕ Bloquear</button>";
echo "</div>";
echo "</div>";

echo "<div class='bank-row' style='display:flex; justify-content:space-between; align-items:center; padding:12px; border-bottom:1px solid var(--borde); font-family:sans-serif;'>";
echo "<div>";
echo "<strong style='color:var(--azul-corporativo); font-size:14px;'>Muestreo Lote #QM-2026-B</strong>";
echo "<p style='font-size:12px; color:var(--texto-secundario); margin-top:2px;'>Origen: PP - Ensamblaje Conectores Cobre</p>";
echo "</div>";
echo "<div style='display:flex; gap:10px;'>";
echo "<button class='btn-sap btn-primary' style='padding:6px 12px; font-size:12px; background:var(--exito-ia);' onclick='procesarLoteQM(\"QM-2026-B\", \"Aprobado\")'>✓ Liberar</button>";
echo "<button class='btn-sap btn-primary' style='padding:6px 12px; font-size:12px; background:var(--alerta-critico);' onclick='procesarLoteQM(\"QM-2026-B\", \"Rechazado\")'>✕ Bloquear</button>";
echo "</div>";
echo "</div>";
?>