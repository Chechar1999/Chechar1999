<?php
// Iniciar sesión y verificar autenticación
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?required=1');
    exit;
}

require_once 'db_config.php';

// Variable para almacenar datos de la denuncia
$complaint = null;
$error_message = '';
$complaint_id = null;

// 1. Obtener y validar el ID de la denuncia desde la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $complaint_id = intval($_GET['id']); // Convertir a entero por seguridad
} else {
    $error_message = "ID de denuncia inválido o no proporcionado.";
    // Podríamos redirigir o simplemente mostrar el error
    // header('Location: view_complaints.php?update_status=error'); exit;
}

// 2. Si tenemos un ID válido, buscar la denuncia en la BD
if ($complaint_id && empty($error_message)) {
    $sql = "SELECT id, timestamp, complainant_name, complainant_role, complaint_type, description, involved_parties, status FROM denuncias WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $complaint_id); // 'i' para integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $complaint = $result->fetch_assoc();
        } else {
            $error_message = "Denuncia no encontrada con ID: " . htmlspecialchars($complaint_id);
        }
        $stmt->close();
    } else {
        $error_message = "Error al preparar la consulta para obtener la denuncia: " . htmlspecialchars($conn->error);
    }
}

$conn->close(); // Cerramos la conexión aquí, ya no la necesitamos en esta página

// Definir los posibles estados (para el dropdown)
$possible_statuses = ['Recibida', 'En Investigación', 'Requiere Información Adicional', 'Resuelta', 'Cerrada', 'Desestimada'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Denuncia - Sistema</title>
    <link rel="stylesheet" href="style.css">
     <style>
        /* Estilos adicionales para el formulario de edición */
        .edit-form-container { max-width: 700px; }
        .form-read-only { background-color: #e9ecef; border: 1px solid #ced4da; padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 0.95rem; }
        .form-read-only strong { display: block; margin-bottom: 5px; color: #495057; }
        .form-group label { margin-bottom: 8px; }
        .btn-update { background-color: #007bff; border-color: #007bff; color: white; }
        .btn-update:hover { background-color: #0056b3; }
        .btn-cancel { background-color: #6c757d; border-color: #6c757d; color: white; margin-left: 10px; text-decoration: none; padding: 12px 20px; border-radius: 4px; font-size: 1.1rem; }
        .btn-cancel:hover { background-color: #5a6268; }
    </style>
</head>
<body>
    <div class="container edit-form-container">
        <h1>Editar Denuncia ID: <?php echo htmlspecialchars($complaint_id ?? 'Inválido'); ?></h1>

        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
            <p><a href="view_complaints.php" class="back-link">&larr; Volver al listado</a></p>
        <?php elseif ($complaint): ?>
            <form action="update_complaint.php" method="POST">
                <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($complaint['id']); ?>">

                <div class="form-read-only">
                    <strong>Fecha y Hora de Registro:</strong> <?php echo htmlspecialchars($complaint['timestamp']); ?>
                </div>
                 <div class="form-read-only">
                    <strong>Denunciante:</strong> <?php echo htmlspecialchars($complaint['complainant_name']); ?>
                </div>
                <div class="form-read-only">
                    <strong>Rol Denunciante:</strong> <?php echo htmlspecialchars($complaint['complainant_role']); ?>
                </div>
                <div class="form-read-only">
                    <strong>Tipo Denuncia:</strong> <?php echo htmlspecialchars($complaint['complaint_type']); ?>
                </div>
                 <div class="form-read-only">
                    <strong>Descripción Original:</strong>
                    <p style="white-space: pre-wrap; margin: 5px 0 0 0;"><?php echo htmlspecialchars($complaint['description']); ?></p>
                </div>

                <div class="form-group">
                    <label for="involved_parties">Personas Involucradas (Editable):</label>
                    <input type="text" id="involved_parties" name="involved_parties" value="<?php echo htmlspecialchars($complaint['involved_parties']); ?>">
                </div>

                <div class="form-group">
                    <label for="status">Estado de la Denuncia (Editable):</label>
                    <select id="status" name="status" required>
                        <?php foreach ($possible_statuses as $status_option): ?>
                            <option value="<?php echo htmlspecialchars($status_option); ?>" <?php echo ($complaint['status'] == $status_option) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status_option); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                 <button type="submit" class="btn-update">Actualizar Denuncia</button>
                <a href="view_complaints.php" class="btn-cancel">Cancelar</a>

            </form>
        <?php else: ?>
             <p class="message error">No se pudo cargar la información de la denuncia.</p>
             <p><a href="view_complaints.php" class="back-link">&larr; Volver al listado</a></p>
        <?php endif; ?>

    </div>
</body>
</html>
