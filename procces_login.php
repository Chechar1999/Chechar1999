<?php
// Iniciar la sesión ANTES de cualquier salida HTML
session_start();

// Si ya está logueado, no necesita procesar de nuevo
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: view_complaints.php');
    exit;
}

// Incluir configuración de la base de datos
require_once 'db_config.php';

// Verificar si se enviaron datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validar que no estén vacíos (aunque 'required' en HTML ayuda)
    if (empty($username) || empty($password)) {
        header('Location: login.php?error=1'); // Considerar un error específico para campos vacíos
        exit;
    }

    // Preparar consulta para buscar al usuario
    $sql = "SELECT id, username, password, full_name, role FROM usuarios WHERE username = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Error al preparar consulta de login: " . $conn->error);
        header('Location: login.php?error=1'); // Error genérico para el usuario
        exit;
    }

    // Vincular parámetro
    $stmt->bind_param("s", $username);

    // Ejecutar
    if ($stmt->execute()) {
        // Obtener resultado
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Usuario encontrado, obtener sus datos
            $user = $result->fetch_assoc();

            // ¡Verificar la contraseña hasheada!
            if (password_verify($password, $user['password'])) {
                // Contraseña correcta - Iniciar sesión

                // Regenerar ID de sesión por seguridad
                session_regenerate_id(true);

                // Almacenar datos en la sesión
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Redirigir a la página protegida
                header('Location: view_complaints.php');
                exit;

            } else {
                // Contraseña incorrecta
                header('Location: login.php?error=1');
                exit;
            }
        } else {
            // Usuario no encontrado
            header('Location: login.php?error=1');
            exit;
        }
    } else {
        // Error al ejecutar la consulta
        error_log("Error al ejecutar consulta de login: " . $stmt->error);
        header('Location: login.php?error=1'); // Error genérico
        exit;
    }

    // Cerrar sentencia
    $stmt->close();

} else {
    // Si no es POST, redirigir al login
    header('Location: login.php');
    exit;
}

// Cerrar conexión (aunque puede que no se alcance si hay redirecciones antes)
$conn->close();
?>
