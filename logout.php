<?php
// Iniciar la sesión para poder acceder a ella
session_start();

// Destruir todas las variables de sesión.
$_SESSION = array();

// Borrar la cookie de sesión si existe.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();

// Redirigir a la página de login con un mensaje
header("Location: login.php?logged_out=1");
exit;
?>
