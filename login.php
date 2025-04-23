<?php
// Iniciar sesión para manejar mensajes de error o éxito (logout)
session_start();

// Si el usuario ya está logueado, redirigir a la vista de denuncias
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: view_complaints.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Denuncias</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos específicos para el login */
        .login-container { max-width: 400px; margin-top: 50px;}
        .login-container h1 { font-size: 1.8rem; margin-bottom: 25px;}
        .login-container label { font-weight: normal; }
        .login-container .form-group { margin-bottom: 20px; }
        .login-container .btn-login { background-color: #28a745; border-color: #28a745; }
        .login-container .btn-login:hover { background-color: #218838; }
        .login-container .public-link { display: block; text-align: center; margin-top: 20px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container login-container">
        <h1>Acceso Autorizado</h1>
        <p>Ingrese sus credenciales para ver las denuncias.</p>

        <?php
        // Mostrar mensajes de error del login o mensaje de logout
        if (isset($_GET['error']) && $_GET['error'] == 1) {
            echo '<div class="message error">Usuario o contraseña incorrectos.</div>';
        }
        if (isset($_GET['logged_out']) && $_GET['logged_out'] == 1) {
            echo '<div class="message success">Sesión cerrada exitosamente.</div>';
        }
         if (isset($_GET['required']) && $_GET['required'] == 1) {
            echo '<div class="message error">Necesita iniciar sesión para acceder a esa página.</div>';
        }
        ?>

        <form action="process_login.php" method="POST">
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>

        <a href="index.php" class="public-link">Volver al registro público de denuncias</a>
    </div>
</body>
</html>
