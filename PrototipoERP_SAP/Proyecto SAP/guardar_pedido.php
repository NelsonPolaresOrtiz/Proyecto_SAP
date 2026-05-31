<?php
include 'conexion.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente = $_POST['cliente'] ?? '';
    $producto_id = $_POST['producto_id'] ?? '';
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
    $pago = $_POST['pago'] ?? '';
    
    // Captura el valor del nuevo control deslizante de descuento (Por defecto 0)
    $descuento = isset($_POST['descuento']) ? intval($_POST['descuento']) : 0; 

    if(empty($producto_id) || $cantidad <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Datos de formulario incompletos o cantidad inválida.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Verificar stock actual del producto
        $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$prod || $prod['stock'] < $cantidad) {
            echo json_encode(['status' => 'error', 'message' => 'Stock insuficiente en el inventario MM.']);
            exit;
        }

        // 2. Calcular nuevo stock y alertas
        $nuevo_stock = $prod['stock'] - $cantidad;
        $nuevo_estado = 'Stock Alto';
        if($nuevo_stock <= 15) {
            $nuevo_estado = 'Crítico';
        } elseif($nuevo_stock <= 90) {
            $nuevo_estado = 'Reabastecer';
        }

        $update = $pdo->prepare("UPDATE productos SET stock = ?, estado = ? WHERE id = ?");
        $update->execute([$nuevo_stock, $nuevo_estado, $producto_id]);

        // 3. NUEVA REGLA DE NEGOCIO: Deducción matemática del Descuento de Simulación
        $precio_bruto = $cantidad * 150; // $150 USD precio base
        $deduccion_valor = ($precio_bruto * $descuento) / 100;
        $total_neto = $precio_bruto - $deduccion_valor; // Monto real final que se inserta

        $insert = $pdo->prepare("INSERT INTO pedidos (cliente, producto_id, cantidad, total, pago) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$cliente, $producto_id, $cantidad, $total_neto, $pago]);

        $pdo->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error en el servidor ERP: ' . $e->getMessage()]);
    }
}
?>