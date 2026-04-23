<?php
session_start();
require_once "conexion.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo    = trim($_POST["correo"]);
    $contrasena = $_POST["contrasena"];

    if (empty($correo) || empty($contrasena)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        $stmt = $pdo->prepare("SELECT id_usuario, nombre, correo, contraseña FROM Usuario WHERE correo = ?");
        $stmt->execute([$correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($contrasena, $user["contraseña"])) {
            session_regenerate_id(true);
            $_SESSION["id_usuario"] = $user["id_usuario"];
            $_SESSION["nombre"]     = $user["nombre"];
            $_SESSION["correo"]     = $user["correo"];
            header("Location: Cursos.php");
            exit();
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Loopbook</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: white;
            padding: 35px 40px 40px;
            border: 1px solid #ccc;
            width: 320px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo-img {
            width: 160px;
            margin-bottom: 20px;
        }
        .login-subtitle {
            color: #888;
            font-size: 13px;
            margin: 0 0 20px 0;
            border-bottom: 2px solid #e1f5fe;
            padding-bottom: 15px;
        }
        .form-group { margin-bottom: 20px; text-align: left; }
        label {
            display: block;
            margin-bottom: 20px;
            color: #666;
            font-weight: bold;
            font-size: 14px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #999;
            box-sizing: border-box;
            font-size: 15px;
            color: #555;
            border-radius: 6px;
        }
        .error-msg {
            background-color: #fdecea;
            color: #c0392b;
            border: 1px solid #e74c3c;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: left;
        }
        .btn-primary {
            background-color: #64b5f6;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-bottom: 15px;
        }
        .btn-primary:hover { background-color: #42a5f5; }
        .forgot-password {
            display: block;
            color: #7e99ff;
            text-decoration: underline;
            font-size: 13px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e1f5fe;
            padding-bottom: 15px;
        }
        .create-user-section { margin-top: 10px; }
        .create-user-label {
            color: #666;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            display: block;
        }
        .btn-register {
            background-color: #64b5f6;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            width: 100%;
        }
        .btn-register:hover { background-color: #42a5f5; }
    </style>
</head>
<body>

    <div class="login-container">
        <img src="loopbook_logo.png" alt="LoopBook" class="logo-img">

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="iniciarSesion.php">
            <div class="form-group">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo"
                       placeholder="ejemplo@gmail.com"
                       value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                       required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <button type="submit" class="btn-primary">Iniciar sesión</button>
        </form>

        <a href="recuperar_contrasena.php" class="forgot-password">¿Olvidaste tu contraseña?</a>

        <div class="create-user-section">
            <span class="create-user-label">¿No tienes cuenta?</span>
            <button class="btn-register" onclick="window.location.href='Registrarse.php'">REGISTRARTE</button>
        </div>
    </div>

</body>
</html>