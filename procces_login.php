<?php
// Iniciar la sesión ANTES de cualquier salida HTML
session_start();

// Si ya está logueado, no necesita procesar de nuevo
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: view_complaints.php');
    exit;
}

ini_set('display_errors', 0); // No mostrar errores en producción
error_reporting(0);

// Incluir configuración de la base de datos
require_once 'db_config.php';

// Verificar si se enviaron datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validar que no estén vacíos
    if (empty($username) || empty($password)) {
        header('Location: login.php?error=1');
        exit;
    }

    // Preparar consulta para buscar al usuario
    $sql = "SELECT id, username, password, full_name, role FROM usuarios WHERE username = ?";

    try {
        $stmt = $conn->prepare($sql);
        // Vincular parámetro
        $stmt->bind_param("s", $username);
        // Ejecutar
        $stmt->execute();
        // Obtener resultado
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Usuario encontrado
            $user = $result->fetch_assoc();

            // Verificar la contraseña hasheada
            if (password_verify($password, $user['password'])) {
                // Contraseña correcta - Iniciar sesión
                session_regenerate_id(true); // Regenerar ID por seguridad
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                $stmt->close();
                $conn->close();
                header('Location: view_complaints.php'); // Redirigir a la página protegida
                exit;
            }
        }

        // Si el usuario no existe o la contraseña es incorrecta, llegamos aquí
        $stmt->close();
        $conn->close();
        header('Location: login.php?error=1');
        exit;

    } catch (mysqli_sql_exception $e) {
        error_log("Error en process_login: " . $e->getMessage());
         if ($conn && $conn->ping()) { $conn->close(); }
        header('Location: login.php?error=1'); // Error genérico
        exit;
    }

} else {
    // Si no es POST, redirigir al login
    header('Location: login.php');
    exit;
}
?>
