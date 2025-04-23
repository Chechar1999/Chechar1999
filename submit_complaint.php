<?php
// --- Configuración ---
$csv_file = 'complaints.csv'; // Nombre del archivo donde se guardan las denuncias
$redirect_success = 'index.php?status=success'; // Página a la que redirigir en caso de éxito
$redirect_error = 'index.php?status=error';     // Página a la que redirigir en caso de error
$redirect_missing_data = 'index.php?status=missing_data'; // Página si faltan datos obligatorios

// --- Validación Básica ---
// Solo procesar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php"); // Redirigir si se accede directamente
    exit;
}

// Verificar que la descripción (campo obligatorio) no esté vacía después de quitar espacios
if (empty(trim($_POST['description']))) {
     header("Location: " . $redirect_missing_data);
     exit;
}

// --- Recolección y Sanitización de Datos ---
// Se usa htmlspecialchars para prevenir ataques XSS (Cross-Site Scripting)
// Se usa trim para quitar espacios en blanco al inicio y final
// Se usa ?? para proveer un valor por defecto si el campo no llega (aunque 'required' en HTML ayuda)

$is_anonymous = isset($_POST['anonymous']) && $_POST['anonymous'] == '1';
// Si es anónimo, nombre y rol son 'Anónimo', si no, toma el valor del POST o 'No proporcionado'/'No especificado'
$complainant_name = $is_anonymous ? 'Anónimo' : htmlspecialchars(trim($_POST['complainant_name'] ?? 'No proporcionado'), ENT_QUOTES, 'UTF-8');
$complainant_role = $is_anonymous ? 'Anónimo' : htmlspecialchars($_POST['complainant_role'] ?? 'No especificado', ENT_QUOTES, 'UTF-8');

$complaint_type = htmlspecialchars($_POST['complaint_type'] ?? 'No especificado', ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8'); // Obligatorio, ya validado que no está vacío
$involved_parties = htmlspecialchars(trim($_POST['involved_parties'] ?? 'No especificado'), ENT_QUOTES, 'UTF-8');
$timestamp = date('Y-m-d H:i:s'); // Fecha y hora actual del servidor
$complaint_id = uniqid('DEN-', true); // Generar un ID único con prefijo DEN- para la denuncia
$status = 'Recibida'; // Estado inicial de toda denuncia nueva

// --- Preparar Datos para CSV ---
// Array con los datos en el orden que tendrán las columnas en el CSV
$data_row = [
    $complaint_id,
    $timestamp,
    $complainant_name,
    $complainant_role,
    $complaint_type,
    $description,
    $involved_parties,
    $status // Añadir el estado inicial
];

// --- Guardar en Archivo CSV ---
$file_exists = file_exists($csv_file); // Comprobar si el archivo ya existe

// Intentar abrir archivo en modo 'append' (añadir al final).
// La '@' suprime errores de PHP si no se puede abrir, lo manejaremos explícitamente
$handle = @fopen($csv_file, 'a');

// Verificar si se pudo abrir el archivo (si $handle es false, hubo un error)
if ($handle === false) {
    // Podría ser un problema de permisos de escritura en la carpeta
    error_log("Error al abrir el archivo CSV para escritura: " . $csv_file); // Registrar el error para el admin
    header("Location: " . $redirect_error); // Informar al usuario del error
    exit;
}

// Si el archivo es nuevo (no existía antes) O está vacío, escribir los encabezados primero
if (!$file_exists || filesize($csv_file) === 0) {
    $headers = ['ID Denuncia', 'Fecha y Hora', 'Nombre Denunciante', 'Rol Denunciante', 'Tipo Denuncia', 'Descripción', 'Involucrados', 'Estado'];
    // fputcsv escribe un array como una línea CSV, manejando comas y comillas
    if (fputcsv($handle, $headers) === false) {
         // Error escribiendo encabezados
         error_log("Error al escribir encabezados en CSV: " . $csv_file);
         fclose($handle); // Cerrar el archivo antes de redirigir
         header("Location: " . $redirect_error);
         exit;
    }
}

// Escribir la fila de datos de la denuncia actual en el archivo CSV
if (fputcsv($handle, $data_row) === false) {
    // Error al escribir la línea de datos
    error_log("Error al escribir datos en CSV: " . $csv_file);
    fclose($handle); // Cerrar el archivo
    header("Location: " . $redirect_error);
    exit;
}

// Cerrar el manejador del archivo (importante para liberar recursos)
fclose($handle);

// --- Redirigir con mensaje de éxito ---
header("Location: " . $redirect_success);
exit; // Terminar el script después de redirigir

?>
