<?php
session_start();
require_once __DIR__ . "/conexion.php";

$error = "";
$exito = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario   = trim($_POST["usuario"]);
    $correo    = trim($_POST["correo"]);
    $contrasena = $_POST["contrasena"];
    $confirmar  = $_POST["confirmar"];

    if (empty($usuario) || empty($correo) || empty($contrasena) || empty($confirmar)) {
        $error = "Por favor, completa todos los campos.";
    } elseif (strlen($contrasena) < 6) {
        $error = "La contraseña debe tener mínimo 6 caracteres.";
    } elseif ($contrasena !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $stmt = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE correo = ?");
        $stmt->execute([$correo]);
        if ($stmt->fetch()) {
            $error = "Este correo ya está registrado.";
        } else {
            $stmt2 = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE usuario = ?");
            $stmt2->execute([$usuario]);
            if ($stmt2->fetch()) {
                $error = "Este nombre de usuario ya está en uso.";
            } else {
                $hash  = password_hash($contrasena, PASSWORD_DEFAULT);
                $fecha = date("Y-m-d H:i:s");
                $insert = $pdo->prepare("INSERT INTO Usuario (nombre, usuario, correo, contraseña, fecha_registro) VALUES (?, ?, ?, ?, ?)");
                $insert->execute([$usuario, $usuario, $correo, $hash, $fecha]);
                $exito = "¡Cuenta creada exitosamente! Ya puedes iniciar sesión.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoopBook - Crear Cuenta</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f4f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-card {
            background: white;
            padding: 35px 30px 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo-img {
            width: 160px;
            margin-bottom: 20px;
        }
        .subtitle {
            color: #888;
            font-size: 13px;
            margin: 0 0 25px 0;
            border-bottom: 2px solid #e1f5fe;
            padding-bottom: 15px;
        }
        .form-group { text-align: left; margin-bottom: 15px; }
        .form-group label {
            display: block;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 7px;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 11px 14px;
            border: none;
            border-radius: 10px;
            background-color: #f1f3f5;
            font-size: 14px;
            color: #555;
            box-sizing: border-box;
        }
        .form-group input::placeholder { color: #adb5bd; }
        .error-msg {
            background-color: #fdecea;
            color: #c0392b;
            border: 1px solid #e74c3c;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: left;
        }
        .exito-msg {
            background-color: #eafaf1;
            color: #1e8449;
            border: 1px solid #27ae60;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: left;
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background-color: #080a1a;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }
        .btn-register:hover { background-color: #1a1d2e; }
        .back-link {
            text-decoration: none;
            color: #4a5568;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .back-link:hover { color: #000; }
    </style>
</head>
<body>

    <div class="register-card">
        <img src="loopbook_logo.png" alt="LoopBook" class="logo-img">

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="exito-msg"><?= htmlspecialchars($exito) ?></div>
        <?php endif; ?>

        <form method="POST" action="Registrarse.php">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" placeholder="Elige un nombre de usuario"
                       value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="correo" placeholder="tu@email.com"
                       value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="contrasena" placeholder="Mínimo 6 caracteres" required>
            </div>
            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirmar" placeholder="Repite tu contraseña" required>
            </div>
            <button type="submit" class="btn-register">Registrarse</button>
        </form>

        <a href="iniciarSesion.php" class="back-link">
            <span>←</span> Volver al inicio de sesión
        </a>
    </div>

</body>
</html>