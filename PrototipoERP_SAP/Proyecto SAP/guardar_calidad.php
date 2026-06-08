<?php
include 'conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura de datos asíncronos enviados por el laboratorio QM desde script.js
    $producto_id = isset($_POST['producto_id']) ? trim($_POST['producto_id']) : '';
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
    $decision = isset($_POST['decision']) ? trim($_POST['decision']) : '';

    if (empty($producto_id) || empty($decision)) {
        echo json_encode(['status' => 'error', 'message' => 'Error QM: Parámetros de inspección incompletos.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Consultamos el estado de inventario actual del material evaluado (MM-IM)
        $stmt = $pdo->prepare("SELECT stock, nombre FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prod) {
            echo json_encode(['status' => 'error', 'message' => 'Error MM: El SKU inspeccionado no existe en el maestro de materiales.']);
            exit;
        }

        $nuevo_stock = $prod['stock'];
        $nuevo_estado = 'Stock Alto';

        // 2. REGLA DE LOGÍSTICA S/4HANA: Procesamiento de la Decisión de Empleo
        if ($decision === 'Rechazado') {
            // Si el laboratorio técnico rechaza el lote por desviación, las unidades se descuentan del stock utilizable
            // y se bloquean (simulando transferencia a stock bloqueado)
            $nuevo_stock = max(0, $prod['stock'] - $cantidad);
            
            // Recalculamos umbrales de alerta crítica para MM de inmediato
            if ($nuevo_stock <= 15) {
                $nuevo_estado = 'Crítico';
            } elseif ($nuevo_stock <= 90) {
                $nuevo_estado = 'Reabastecer';
            }
            
            // Actualizamos el inventario aplicando la restricción por falla técnica
            $update = $pdo->prepare("UPDATE productos SET stock = ?, estado = ? WHERE id = ?");
            $update->execute([$nuevo_stock, $nuevo_estado, $producto_id]);
        } else {
            // Si es 'Aprobado', el stock ya fue deducido previamente al fabricar o se consolida de forma exitosa
            // Re-verificamos el estado actual por si acaso para no perder consistencia en Fiori
            if ($nuevo_stock <= 15) { $nuevo_estado = 'Crítico'; }
            elseif ($nuevo_stock <= 90) { $nuevo_estado = 'Reabastecer'; }
            
            $update = $pdo->prepare("UPDATE productos SET estado = ? WHERE id = ?");
            $update->execute([$nuevo_estado, $producto_id]);
        }

        $pdo->commit();

        // Retornamos un JSON estructurado para que el front-end dispare los Toasts de SAP
        echo json_encode([
            'status' => 'success',
            'decision' => $decision,
            'producto' => $prod['nombre'],
            'nuevo_stock' => $nuevo_stock
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Falla en el Mandante QM-IM: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de petición no permitido en el servidor.']);
}
?>