<?php
include 'conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recogemos las variables del formulario de PP de tu dashboard
    $lote_nombre = $_POST['lote_nombre'] ?? 'Lote Estructuras';
    $producto_id = $_POST['producto_id'] ?? '';
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

    // Validación estructural básica
    if (empty($producto_id) || $cantidad <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Error PP: Parámetros de manufactura insuficientes o cantidad errónea.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Consultar cobertura y existencias de la Materia Prima en el Almacén MM
        $stmt = $pdo->prepare("SELECT stock, nombre FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prod) {
            echo json_encode(['status' => 'error', 'message' => 'Error MM: El SKU de Materia Prima seleccionado no existe en el maestro.']);
            exit;
        }

        if ($prod['stock'] < $cantidad) {
            echo json_encode(['status' => 'error', 'message' => "Quiebre de Stock en MM: Existencias insuficientes de '" . $prod['nombre'] . "' para cubrir la orden PP."]);
            exit;
        }

        // 2. Calcular el nuevo balance logístico y actualizar estados de seguridad
        $nuevo_stock = $prod['stock'] - $cantidad;
        
        // Regla analítica para los Badges de alerta de SAP Fiori
        $nuevo_estado = 'Stock Alto';
        if ($nuevo_stock <= 15) {
            $nuevo_estado = 'Crítico';
        } elseif ($nuevo_stock <= 90) {
            $nuevo_estado = 'Reabastecer';
        }

        // 3. Imputar la deducción de inventario en MySQL
        $update = $pdo->prepare("UPDATE productos SET stock = ?, estado = ? WHERE id = ?");
        $update->execute([$nuevo_stock, $nuevo_estado, $producto_id]);

        // Nota de ingeniería: Como no creamos una tabla extra de órdenes PP para no saturar tu XAMPP,
        // registramos el consumo simulado inyectándolo directamente como un movimiento contable cruzado en 'pedidos',
        // con el estatus especial de 'En Proceso' para que alimente automáticamente tus bitácoras visuales.
        $texto_cliente_simulado = "Planta Manufactura Interna (" . $lote_nombre . ")";
        $costo_imputado_simulado = $cantidad * 148.20; // Tarifa real CO-PC de manufactura

        $insert_movimiento = $pdo->prepare("INSERT INTO pedidos (cliente, producto_id, cantidad, total, pago, status) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_movimiento->execute([$texto_cliente_simulado, $producto_id, $cantidad, $costo_imputado_simulado, 'Imputación Interna CO', 'En Proceso']);

        $pdo->commit();
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Falla crítica en Mandante PP-SFC: ' . $e->getMessage()]);
    }
}
?>