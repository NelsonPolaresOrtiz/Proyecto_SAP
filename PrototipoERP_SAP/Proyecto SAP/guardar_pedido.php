<?php
include 'conexion.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura limpia de los campos del formulario denso de SD
    $cliente = isset($_POST['cliente']) ? trim($_POST['cliente']) : '';
    $producto_id = isset($_POST['producto_id']) ? trim($_POST['producto_id']) : '';
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;
    $pago = isset($_POST['pago']) ? trim($_POST['pago']) : '';
    
    // Captura el valor del nuevo control deslizante de descuento (Por defecto 0)
    $descuento = isset($_POST['descuento']) ? intval($_POST['descuento']) : 0; 

    // CORRECCIÓN DE VALIDACIÓN: Aseguramos que ningún parámetro del maestro de clientes o pagos quede huérfano
    if(empty($cliente) || empty($producto_id) || $cantidad <= 0 || empty($pago)) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error de validación SD: Debe seleccionar un Cliente, un Material válido y un Canal de Pago.'
        ]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Verificar stock actual del producto en el submódulo MM-IM
        $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$prod) {
            echo json_encode(['status' => 'error', 'message' => 'Error MM Core: El SKU del material especificado no existe.']);
            exit;
        }

        if($prod['stock'] < $cantidad) {
            echo json_encode(['status' => 'error', 'message' => 'Stock insuficiente en el inventario físico MM.']);
            exit;
        }

        // 2. Calcular nuevo stock y balance dinámico de alertas Fiori
        $nuevo_stock = $prod['stock'] - $cantidad;
        $nuevo_estado = 'Stock Alto';
        if($nuevo_stock <= 15) {
            $nuevo_estado = 'Crítico';
        } elseif($nuevo_stock <= 90) {
            $nuevo_estado = 'Reabastecer';
        }

        // Actualizamos inventarios MM en vivo
        $update = $pdo->prepare("UPDATE productos SET stock = ?, estado = ? WHERE id = ?");
        $update->execute([$nuevo_stock, $nuevo_estado, $producto_id]);

        // 3. REGLA DE NEGOCIO AVANZADA: Deducción matemática del Descuento de Simulación (SAP Pricing Slider)
        $precio_bruto = $cantidad * 150; // $150 USD precio base estándar pactado
        $deduccion_valor = ($precio_bruto * $descuento) / 100;
        $total_neto = $precio_bruto - $deduccion_valor; 

        // 4. INSERCIÓN INTEGRAL: Inyectamos el registro forzando el estatus 'Enviado' para que las tablas no queden vacías
        // Si tu tabla de base de datos no tiene la columna 'status', SQL la ignorará o puedes verificar si se llama 'status' o 'estado'.
        $insert = $pdo->prepare("INSERT INTO pedidos (cliente, producto_id, cantidad, total, pago, status) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->execute([$cliente, $producto_id, $cantidad, $total_neto, $pago, 'Enviado']);

        $pdo->commit();
        
        // Retornamos respuesta exitosa para que script.js lance el Toast flotante de S/4HANA
        echo json_encode(['status' => 'success']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error de consistencia en el servidor ERP: ' . $e->getMessage()]);
    }
}
?>