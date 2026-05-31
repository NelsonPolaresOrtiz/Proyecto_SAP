<?php
include 'conexion.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto_id = $_POST['producto_id'] ?? '';
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

    if(empty($producto_id) || $cantidad <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Parámetros de lote insuficientes.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Verificar existencias de materias primas directo en MM
        $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$prod || $prod['stock'] < $cantidad) {
            echo json_encode(['status' => 'error', 'message' => 'No hay suficiente stock de materia prima en almacén.']);
            exit;
        }

        // Descontar existencias y recalcular estados de alerta
        $nuevo_stock = $prod['stock'] - $cantidad;
        $nuevo_estado = 'Stock Alto';
        if($nuevo_stock <= 15) $nuevo_estado = 'Crítico';
        elseif($nuevo_stock <= 90) $nuevo_estado = 'Reabastecer';

        $update = $pdo->prepare("UPDATE productos SET stock = ?, estado = ? WHERE id = ?");
        $update->execute([$nuevo_stock, $nuevo_estado, $producto_id]);

        $pdo->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>