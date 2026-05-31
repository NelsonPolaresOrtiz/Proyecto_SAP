<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Permite tu nombre directo o d jesus como administrador alterno, clave corporativa obligatoria: "sap123"
    if (!empty($user) && $pass === "sap123") {
        $_SESSION['usuario'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Contraseña corporativa incorrecta. Use 'sap123'.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SAP Lite ERP - LogOn</title>
    <style>
        :root {
            --azul-corporativo: #0A2540;
            --fondo-general: #F4F6F9;
            --tarjetas-header: #FFFFFF;
            --texto-principal: #1A202C;
            --texto-secundario: #718096;
            --alerta-critico: #E53E3E;
            --borde: #E2E8F0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-color: var(--azul-corporativo); }
        .login-card { background: var(--tarjetas-header); padding: 40px; border-radius: 12px; width: 380px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .login-card h2 { color: var(--azul-corporativo); font-size: 24px; margin-bottom: 4px; font-weight: 700; }
        .login-card p { color: var(--texto-secundario); margin-bottom: 24px; font-size: 14px; }
        .form-group { text-align: left; margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; margin-bottom: 6px; color: var(--texto-principal); font-weight: 600; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid var(--borde); border-radius: 6px; font-size: 14px; background: #FAFAFA; }
        .form-group input:focus { background: #FFF; border-color: #1E3A8A; outline: none; }
        .btn-login { width: 100%; padding: 12px; background: var(--azul-corporativo); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 14px; transition: background 0.2s; }
        .btn-login:hover { background: #1E3A8A; }
        .alert { background: #FDE8E8; color: var(--alerta-critico); padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 18px; font-weight: 600; text-align: left; border-left: 4px solid var(--alerta-critico); }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>ERPSystem</h2>
        <p>Simulador Académico SAP Lite</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Usuario / Consultor Certificado:</label>
                <input type="text" name="username" value="UserName" placeholder="Nombre completo" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Clave de Acceso Mandante:</label>
                <input type="password" name="password" placeholder="Clave: sap123" required>
            </div>
            <button type="submit" class="btn-login">Iniciar Sesión de Sistema</button>
        </form>
    </div>
</body>
</html>