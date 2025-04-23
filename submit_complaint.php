<?php
// Incluir el archivo de configuración de la base de datos
require_once 'db_config.php'; // $conn estará disponible desde aquí

// --- Configuración de Redirección ---
$redirect_success = 'index.php?status=success';
$redirect_error = 'index.php?status=error_db'; // Cambiado para error de DB
$redirect_missing_data = 'index.php?status=missing_data';

// --- Validación Básica ---
// Solo procesar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Verificar campos obligatorios (Descripción y Tipo de Denuncia ahora)
if (empty(trim($_POST['description'])) || empty($_POST['complaint_type'])) {
     header("Location: " . $redirect_missing_data);
     exit;
}

// --- Recolección y Limpieza de Datos (similar a antes) ---
$is_anonymous = isset($_POST['anonymous']) && $_POST['anonymous'] == '1';
$complainant_name = $is_anonymous ? 'Anónimo' : trim($_POST['complainant_name'] ?? 'No proporcionado');
$complainant_role = $is_anonymous ? 'Anónimo' : ($_POST['complainant_role'] ?? 'No especificado');
$complaint_type = $_POST['complaint_type'] ?? 'No especificado'; // Ya validado que no está vacío
$description = trim($_POST['description']); // Ya validado que no está vacío
$involved_parties = trim($_POST['involved_parties'] ?? ''); // Puede estar vacío
$status = 'Recibida'; // Estado inicial por defecto

// Manejar caso de nombre vacío si no es anónimo
if (!$is_anonymous && empty($complainant_name)) {
    $complainant_name = 'No proporcionado';
}
// Sanitizar las entradas justo antes de usarlas en la consulta es una buena práctica, aunque las consultas preparadas son la defensa principal.
// Sin embargo, para este ejemplo, confiaremos en las consultas preparadas.

// --- Guardar en Base de Datos usando Consultas Preparadas (Seguridad) ---

// 1. Preparar la consulta SQL para insertar datos
// Los signos de interrogación (?) son marcadores de posición para los valores
$sql = "INSERT INTO denuncias (complainant_name, complainant_role, complaint_type, description, involved_parties, status) VALUES (?, ?, ?, ?, ?, ?)";

// 2. Preparar la sentencia usando la conexión $conn del archivo db_config.php
$stmt = $conn->prepare($sql);

// Verificar si la preparación fue exitosa
if ($stmt === false) {
    error_log("Error al preparar la consulta SQL: " . $conn->error);
    $conn->close(); // Cerrar conexión
    header("Location: " . $redirect_error);
    exit;
}

// 3. Vincular los parámetros a los marcadores de posición
// "ssssss" indica que los 6 parámetros son strings (cadenas de texto)
// ¡El orden y tipo DEBEN coincidir con los '?' en la consulta SQL!
$stmt->bind_param("ssssss",
    $complainant_name,
    $complainant_role,
    $complaint_type,
    $description,
    $involved_parties,
    $status
);

// 4. Ejecutar la sentencia preparada
if ($stmt->execute()) {
    // Éxito al insertar
    $stmt->close(); // Cerrar la sentencia
    $conn->close(); // Cerrar la conexión
    header("Location: " . $redirect_success); // Redirigir con mensaje de éxito
    exit;
} else {
    // Error al ejecutar la sentencia
    error_log("Error al ejecutar la consulta SQL: " . $stmt->error);
    $stmt->close(); // Cerrar la sentencia
    $conn->close(); // Cerrar la conexión
    header("Location: " . $redirect_error); // Redirigir con mensaje de error
    exit;
}

?>
