<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: dashboard.php");
    exit();
}

include 'conexion.php'; 
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) ? trim($_POST['password']) : '';
    $mandante = isset($_POST['mandante']) ? $_POST['mandante'] : '300';

    if (!empty($user) && !empty($pass)) {
        try {
            // 1. Buscamos el consultor directo en la tabla de MariaDB
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? LIMIT 1");
            $stmt->execute([$user]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Si existe el registro en la base de datos, extraemos su Rol real
            if ($row && $pass === $row['password']) {
                $_SESSION['usuario'] = $row['usuario'];
                $_SESSION['rol'] = $row['rol']; // Asigna 'Admin', 'MM_Operario' o 'FICO_Analista'
                $_SESSION['mandante'] = $mandante;

                header("Location: dashboard.php");
                exit();
            } else {
                // 3. Mecanismo de respaldo unificado por redundancia de credenciales directas
                if (($user === "Nelson" || $user === "Nelson Polares Ortiz") && $pass === "sap123") {
                    $_SESSION['usuario'] = "Nelson";
                    $_SESSION['rol'] = "Admin";
                    $_SESSION['mandante'] = $mandante;
                    header("Location: dashboard.php");
                    exit;
                } elseif ($user === "Marcos_Logistica" && $pass === "sap123") {
                    $_SESSION['usuario'] = "Marcos_Logistica";
                    $_SESSION['rol'] = "MM_Operario"; 
                    $_SESSION['mandante'] = $mandante;
                    header("Location: dashboard.php");
                    exit;
                } elseif ($user === "Adriana_Finanzas" && $pass === "sap123") {
                    $_SESSION['usuario'] = "Adriana_Finanzas";
                    $_SESSION['rol'] = "FICO_Analista"; 
                    $_SESSION['mandante'] = $mandante;
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "Falla de autenticación en Mandante $mandante. Usuario o clave incorrecta.";
                }
            }
        } catch (PDOException $e) {
            $error = "Error en el Mandante de Seguridad: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, complete todos los campos de acceso.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAP Lite ERP - LogOn Principal Core</title>
    <style>
        :root {
            --azul-corporativo: #0A2540;
            --azul-fiori: #1E3A8A;
            --fondo-degradado: linear-gradient(135deg, #0A2540 0%, #1E3A8A 100%);
            --tarjetas-header: #FFFFFF;
            --texto-principal: #1A202C;
            --texto-secundario: #718096;
            --alerta-critico: #E53E3E;
            --borde: #E2E8F0;
            --exito-ia: #10B981;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        body { 
            display: flex; 
            flex-direction: column;
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background: var(--fondo-degradado);
            overflow: hidden;
        }

        .login-card { 
            background: var(--tarjetas-header); 
            padding: 40px; 
            border-radius: 12px; 
            width: 420px; 
            text-align: center; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4); 
            border: 1px solid rgba(255,255,255,0.1);
        }

        .login-card h2 { 
            color: var(--azul-corporativo); 
            font-size: 26px; 
            margin-bottom: 4px; 
            font-weight: 800; 
            letter-spacing: -0.5px;
        }

        .login-card p { 
            color: var(--texto-secundario); 
            margin-bottom: 24px; 
            font-size: 14px; 
            font-weight: 500;
        }

        .form-row-sap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 18px;
        }

        .form-group { text-align: left; margin-bottom: 18px; }
        .form-group label { display: block; font-size: 12.5px; margin-bottom: 6px; color: var(--texto-principal); font-weight: 600; }
        
        .sap-input { 
            width: 100%; 
            padding: 11px 14px; 
            border: 1px solid var(--borde); 
            border-radius: 6px; 
            font-size: 14px; 
            background: #F8FAFC; 
            color: var(--texto-principal);
            transition: all 0.2s;
        }
        
        .sap-input:focus { 
            background: #FFF; 
            border-color: var(--azul-fiori); 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
        }

        .btn-login { 
            width: 100%; 
            padding: 13px; 
            background: var(--azul-corporativo); 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: bold; 
            font-size: 14px; 
            transition: background 0.2s, transform 0.1s; 
            margin-top: 5px;
        }
        
        .btn-login:hover { background: var(--azul-fiori); }
        .btn-login:active { transform: scale(0.99); }

        .alert { 
            background: #FDE8E8; 
            color: var(--alerta-critico); 
            padding: 12px; 
            border-radius: 6px; 
            font-size: 13px; 
            margin-bottom: 18px; 
            font-weight: 600; 
            text-align: left; 
            border-left: 4px solid var(--alerta-critico); 
            border-left-width: 4px;
        }

        .sap-footer-info {
            position: absolute;
            bottom: 20px;
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            text-align: center;
            line-height: 1.6;
            font-family: monospace;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>ERPSystem <span style="font-size:12px; color:var(--exito-ia); font-weight:bold; vertical-align:middle; margin-left:3px;">S/4HANA Core</span></h2>
        <p>Entorno de Simulación Académica Integrada</p>
        
        <?php if (!empty($error)): ?>
            <div class="alert">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-row-sap">
                <div class="form-group">
                    <label>Mandante:</label>
                    <select name="mandante" class="sap-input" style="cursor: pointer; font-weight: bold; color: var(--azul-fiori);">
                        <option value="300">300 (Sandbox / Test)</option>
                        <option value="100">100 (QA Validation)</option>
                        <option value="200">200 (Prod Central)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Idioma Mandante:</label>
                    <input type="text" class="sap-input" value="ES (Español)" disabled style="background:#E2E8F0; font-weight:600; text-align:center;">
                </div>
            </div>

            <div class="form-group">
                <label>Usuario / Consultor Certificado Senior:</label>
                <input type="text" name="username" class="sap-input" placeholder="Nombre de usuario corporativo" required autocomplete="off" autofocus>
            </div>

            <div class="form-group">
                <label>Clave de Acceso Corporativa:</label>
                <input type="password" name="password" class="sap-input" placeholder="Clave de seguridad" required>
            </div>

            <button type="submit" class="btn-login">Acceder al Núcleo de Transacciones</button>
        </form>
    </div>

    <div class="sap-footer-info">
        Mandante Local de Pruebas Activo de Ingeniería de Sistemas<br>
        Servidor: Apache/XAMPP local | Base de datos: MariaDB (Puerto: 3307) | Kernel: PHP Core 8.x
    </div>

</body>
</html>