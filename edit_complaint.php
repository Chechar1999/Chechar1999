<?php
// Iniciar sesión y verificar autenticación ANTES de cualquier salida
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?required=1');
    exit;
}

ini_set('display_errors', 0); // No mostrar errores en producción
error_reporting(0);

require_once 'db_config.php';

// Variable para almacenar datos de la denuncia y mensajes
$complaint = null;
$error_message = '';
$complaint_id = null;

// 1. Obtener y validar el ID de la denuncia desde la URL
if (isset($_GET['id'])) {
     // Usar filter_input para validar que sea un entero positivo
     $complaint_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ["options" => ["min_range"=>1]]);
     if ($complaint_id === false) {
         $error_message = "ID de denuncia inválido.";
         $complaint_id = null; // Asegurarse de que no se use un ID inválido
     }
} else {
    $error_message = "No se proporcionó ID de denuncia.";
}

// 2. Si tenemos un ID válido, buscar la denuncia en la BD
if ($complaint_id && empty($error_message)) {
    $sql = "SELECT id, timestamp, complainant_name, complainant_role, complaint_type, description, involved_parties, status FROM denuncias WHERE id = ?";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $complaint_id); // 'i' para integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $complaint = $result->fetch_assoc();
        } else {
            $error_message = "Denuncia no encontrada con ID: " . htmlspecialchars($complaint_id);
        }
        $stmt->close();

    } catch (mysqli_sql_exception $e) {
        error_log("Error al obtener denuncia para editar (ID: $complaint_id): " . $e->getMessage());
        $error_message = "Error al cargar los datos de la denuncia.";
    }
}

$conn->close(); // Cerramos la conexión aquí

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
        .form-read-only { background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 10px 15px; margin-bottom: 15px; border-radius: 4px; font-size: 0.95rem; color: #495057; }
        .form-read-only strong { display: block; margin-bottom: 5px; color: #343a40; font-weight: 600;}
        .form-read-only p { white-space: pre-wrap; margin: 5px 0 0 0; background-color: #fff; padding: 8px; border: 1px solid #eee; border-radius: 3px; }
        .form-group label { margin-bottom: 8px; font-weight: 600; }
        .btn-update { background-color: #007bff; border-color: #007bff; color: white; }
        .btn-update:hover { background-color: #0056b3; }
        .btn-cancel { background-color: #6c757d; border-color: #6c757d; color: white; margin-left: 10px; text-decoration: none; padding: 12px 20px; border-radius: 4px; font-size: 1.1rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; vertical-align: middle; } /* Asegurar alineación con botón */
        .btn-cancel:hover { background-color: #5a6268; }
        .button-group { margin-top: 25px; }
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
                    <strong>Fecha y Hora de Registro:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($complaint['timestamp']))); ?>
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
                    <p><?php echo htmlspecialchars($complaint['description']); ?></p>
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

                <div class="button-group">
                    <button type="submit" class="btn-update">Actualizar Denuncia</button>
                    <a href="view_complaints.php" class="btn-cancel">Cancelar</a>
                </div>

            </form>
        <?php else: ?>
             <p class="message error">No se pudo cargar la información de la denuncia solicitada.</p>
             <p><a href="view_complaints.php" class="back-link">&larr; Volver al listado</a></p>
        <?php endif; ?>

    </div>
</body>
</html>
