<?php
ini_set('display_errors', 0); // No mostrar errores en producción
error_reporting(0); // No reportar errores en producción
// En desarrollo, puedes habilitarlos:
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Incluir el archivo de configuración de la base de datos
require_once 'db_config.php'; // $conn estará disponible desde aquí

// --- Configuración de Redirección ---
$redirect_success = 'index.php?status=success';
$redirect_error = 'index.php?status=error_db';
$redirect_missing_data = 'index.php?status=missing_data';

// --- Validación Básica ---
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Verificar campos obligatorios
if (empty(trim($_POST['description'])) || empty($_POST['complaint_type'])) {
     header("Location: " . $redirect_missing_data);
     exit;
}

// --- Recolección y Limpieza de Datos ---
$is_anonymous = isset($_POST['anonymous']) && $_POST['anonymous'] == '1';
$complainant_name = $is_anonymous ? 'Anónimo' : trim($_POST['complainant_name'] ?? 'No proporcionado');
$complainant_role = $is_anonymous ? 'Anónimo' : ($_POST['complainant_role'] ?? 'No especificado');
$complaint_type = $_POST['complaint_type'] ?? 'No especificado';
$description = trim($_POST['description']);
$involved_parties = trim($_POST['involved_parties'] ?? '');
$status = 'Recibida'; // Estado inicial por defecto

if (!$is_anonymous && empty($complainant_name)) {
    $complainant_name = 'No proporcionado';
}

// --- Guardar en Base de Datos usando Consultas Preparadas ---
$sql = "INSERT INTO denuncias (complainant_name, complainant_role, complaint_type, description, involved_parties, status) VALUES (?, ?, ?, ?, ?, ?)";

// Usar try-catch para manejar errores de BD de forma más robusta
try {
    $stmt = $conn->prepare($sql);
    // Vincular parámetros
    $stmt->bind_param("ssssss",
        $complainant_name,
        $complainant_role,
        $complaint_type,
        $description,
        $involved_parties,
        $status
    );

    // Ejecutar
    $stmt->execute();

    // Cerrar sentencia
    $stmt->close();
    // Cerrar conexión
    $conn->close();
    // Redirigir con mensaje de éxito
    header("Location: " . $redirect_success);
    exit;

} catch (mysqli_sql_exception $e) {
    // Registrar el error detallado para el administrador
    error_log("Error al ejecutar INSERT en submit_complaint: " . $e->getMessage());
    // Cerrar conexión si aún está abierta
    if ($conn && $conn->ping()) { $conn->close(); }
    // Redirigir con mensaje de error genérico para el usuario
    header("Location: " . $redirect_error);
    exit;
}
?>
