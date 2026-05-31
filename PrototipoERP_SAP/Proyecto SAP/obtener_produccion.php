<?php
include 'conexion.php';

// Renderizar de forma interactiva estados de fabricación para simular las órdenes de PP
echo "<div style='margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--borde); font-family: sans-serif;'>";
echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;'>";
echo "<strong style='color: var(--azul-corporativo); font-size: 14px;'>Orden PP-#2026-01</strong>";
echo "<span class='badge badge-success' style='font-size: 11px;'>🟢 Liberada</span>";
echo "</div>";
echo "<p style='font-size: 13px; color: var(--texto-principal); margin-bottom: 4px;'><b>Lote:</b> Estructuras Alum. Serie A</p>";
echo "<p style='font-size: 12px; color: var(--texto-secundario);'>Consumo interno asociado a PROD1</p>";
echo "</div>";

echo "<div style='margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--borde); font-family: sans-serif;'>";
echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;'>";
echo "<strong style='color: var(--azul-corporativo); font-size: 14px;'>Orden PP-#2026-02</strong>";
echo "<span class='badge badge-warning' style='font-size: 11px;'>🔵 En Proceso</span>";
echo "</div>";
echo "<p style='font-size: 13px; color: var(--texto-principal); margin-bottom: 4px;'><b>Lote:</b> Ensamblaje Conectores Cobre</p>";
echo "<p style='font-size: 12px; color: var(--texto-secundario);'>Consumo interno asociado a PROD4</p>";
echo "</div>";
?>