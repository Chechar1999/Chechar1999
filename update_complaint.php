<?php
// Iniciar sesión y verificar autenticación
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?required=1');
    exit;
}

require_once 'db_config.php';

// Verificar si la solicitud es POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Obtener y validar datos del formulario
    $complaint_id = filter_input(INPUT_POST, 'complaint_id', FILTER_VALIDATE_INT);
    $new_status = trim($_POST['status'] ?? ''); // Obtener el nuevo estado
    $new_involved = trim($_POST['involved_parties'] ?? ''); // Obtener involucrados actualizados
    // $new_admin_notes = trim($_POST['admin_notes'] ?? ''); // Si añades notas admin

    // Validar que el ID sea válido y el estado no esté vacío
    if ($complaint_id === false || $complaint_id <= 0) {
        header('Location: view_complaints.php?update_status=error'); // ID inválido
        exit;
    }
    if (empty($new_status)) {
         // Idealmente, validar contra la lista de estados posibles
         header('Location: edit_complaint.php?id=' . $complaint_id . '&error=status_empty'); // Redirigir de vuelta al form con error
         exit;
    }
     // Podrías añadir validación para asegurar que $new_status es uno de los permitidos

    // 2. Preparar la consulta UPDATE
    // Actualizaremos solo 'status' e 'involved_parties'
    // Si añades notas admin, inclúyelas aquí: SET status = ?, involved_parties = ?, admin_notes = ? WHERE id = ?
    $sql = "UPDATE denuncias SET status = ?, involved_parties = ? WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Error al preparar UPDATE: " . $conn->error);
        $conn->close();
        header('Location: view_complaints.php?update_status=error');
        exit;
    }

    // 3. Vincular parámetros (s=string, i=integer)
    // Si añades notas admin (string): "sssi"
    $stmt->bind_param("ssi", $new_status, $new_involved, $complaint_id);

    // 4. Ejecutar la consulta
    if ($stmt->execute()) {
        // Verificar si alguna fila fue afectada (significa que el ID existía)
        if ($stmt->affected_rows > 0) {
            $redirect_status = 'success';
        } else {
            // No se afectaron filas, puede que el ID no existiera o los datos eran iguales
            // Podríamos verificar si el ID existe primero, pero por simplicidad lo manejamos aquí
             error_log("UPDATE ejecutado pero 0 filas afectadas para ID: " . $complaint_id);
            $redirect_status = 'success'; // O podrías poner 'nochange' si quieres diferenciar
        }
    } else {
        // Error en la ejecución
        error_log("Error al ejecutar UPDATE para ID " . $complaint_id . ": " . $stmt->error);
        $redirect_status = 'error';
    }

    // 5. Cerrar y redirigir
    $stmt->close();
    $conn->close();
    header('Location: view_complaints.php?update_status=' . $redirect_status);
    exit;

} else {
    // Si no es POST, redirigir al listado
    header('Location: view_complaints.php');
    exit;
}
?>
