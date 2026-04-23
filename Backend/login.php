<?php
// login.php
session_start();
require_once "conexion.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST["correo"]);
    $contrasena = $_POST["contrasena"];

    if (empty($correo) || empty($contrasena)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Buscar usuario por correo
        $stmt = $pdo->prepare("SELECT id_usuario, nombre, correo, contraseña FROM Usuario WHERE correo = ?");
        $stmt->execute([$correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($contrasena, $user["contraseña"])) {
            // Inicio de sesión exitoso
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
            padding: 40px;
            border: 1px solid #ccc;
            width: 320px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #666;
            font-size: 20px;
            margin-top: 0;
            text-transform: uppercase;
            border-bottom: 2px solid #e1f5fe;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #666;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #999;
            box-sizing: border-box;
            font-size: 16px;
            color: #555;
        }

        .error-msg {
            background-color: #fdecea;
            color: #c0392b;
            border: 1px solid #e74c3c;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #64b5f6;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            width: fit-content;
            margin-bottom: 20px;
        }

        .btn-primary:hover {
            background-color: #42a5f5;
        }

        .forgot-password {
            display: block;
            color: #7e99ff;
            text-decoration: underline;
            font-size: 14px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e1f5fe;
            padding-bottom: 15px;
        }

        .create-user-section {
            margin-top: 20px;
        }

        .create-user-label {
            color: #666;
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }

        .btn-register {
            background-color: #64b5f6;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            width: fit-content;
        }

        .btn-register:hover {
            background-color: #42a5f5;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
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
            <span class="create-user-label">Crear nuevo usuario</span>
            <button class="btn-register" onclick="window.location.href='Registrarse.php'">REGISTRARTE</button>
        </div>
    </div>

</body>
</html>
