<?php
// --- INICIO DE VERIFICACIÓN DE SESIÓN ---
session_start(); // Iniciar sesión ANTES de cualquier salida HTML
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?required=1');
    exit;
}
// --- FIN DE VERIFICACIÓN DE SESIÓN ---

ini_set('display_errors', 0); // No mostrar errores en producción
error_reporting(0);

require_once 'db_config.php';
$logged_user_name = isset($_SESSION['full_name']) && !empty($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Denuncias Registradas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .header-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; flex-wrap: wrap; gap: 10px; }
        .header-info span { font-size: 0.95rem; color: #555; }
        .logout-link { text-decoration: none; color: #dc3545; font-weight: bold; padding: 5px 10px; border: 1px solid #dc3545; border-radius: 4px; transition: all 0.2s ease; }
        .logout-link:hover { background-color: #dc3545; color: white; }
        .edit-link { display: inline-block; padding: 3px 8px; background-color: #ffc107; color: #333; text-decoration: none; border-radius: 3px; font-size: 0.85rem; border: 1px solid #e0a800; }
        .edit-link:hover { background-color: #e0a800; color: #000; }
        th, td { vertical-align: middle; } /* Alinear verticalmente celdas */
    </style>
</head>
<body>
    <div class="container view-container">
        <div class="header-info">
             <span>Usuario conectado: <strong><?php echo $logged_user_name; ?></strong></span>
            <a href="logout.php" class="logout-link">Cerrar Sesión</a>
        </div>

        <h1>Listado de Denuncias Registradas</h1>
        <a href="index.php" class="back-link">&larr; Ir al Registro Público</a>

        <?php
        // Mostrar mensajes de estado de la ACTUALIZACIÓN
        if (isset($_GET['update_status'])) {
            if ($_GET['update_status'] == 'success') {
                echo '<div class="message success">Denuncia actualizada exitosamente.</div>';
            } elseif ($_GET['update_status'] == 'error') {
                echo '<div class="message error">Hubo un error al actualizar la denuncia.</div>';
            } elseif ($_GET['update_status'] == 'notfound') {
                 echo '<div class="message error">Error: No se encontró la denuncia a actualizar.</div>';
            }
        }

        // --- Consulta y muestra de tabla ---
        $sql = "SELECT id, timestamp, complainant_name, complainant_role, complaint_type, description, involved_parties, status FROM denuncias ORDER BY timestamp DESC";
        $table_content = ''; // Variable para construir la tabla

        try {
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $table_content .= '<table>';
                $table_content .= '<thead><tr><th>ID</th><th>Fecha</th><th>Denunciante</th><th>Rol</th><th>Tipo</th><th>Descripción</th><th>Involucrados</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>';
                while($row = $result->fetch_assoc()) {
                    $table_content .= '<tr>';
                    $table_content .= '<td>' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '</td>';
                    $table_content .= '<td>' . htmlspecialchars(date('d/m/Y H:i', strtotime($row['timestamp'])), ENT_QUOTES, 'UTF-8') . '</td>'; // Formatear fecha
                    $table_content .= '<td>' . htmlspecialchars($row['complainant_name'], ENT_QUOTES, 'UTF-8') . '</td>';
                    $table_content .= '<td>' . htmlspecialchars($row['complainant_role'], ENT_QUOTES, 'UTF-8') . '</td>';
                    $table_content .= '<td>' . htmlspecialchars($row['complaint_type'], ENT_QUOTES, 'UTF-8') . '</td>';
                    $table_content .= '<td class="description" title="' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</td>';
                    $table_content .= '<td>' . htmlspecialchars($row['involved_parties'], ENT_QUOTES, 'UTF-8') . '</td>';
                    $table_content .= '<td>' . htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') . '</td>';
                    $table_content .= '<td><a href="edit_complaint.php?id=' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '" class="edit-link">Editar</a></td>';
                    $table_content .= '</tr>';
                }
                $table_content .= '</tbody></table>';
                $result->free(); // Liberar memoria del resultado
            } else {
                $table_content = '<p class="no-complaints">Aún no hay denuncias registradas en la base de datos.</p>';
            }
        } catch (mysqli_sql_exception $e) {
            error_log("Error en query en view_complaints: " . $e->getMessage());
            $table_content = '<p class="message error">Error al consultar las denuncias. Intente más tarde.</p>';
        }

        $conn->close(); // Cerrar la conexión
        echo $table_content; // Mostrar la tabla o mensaje
        ?>
    </div> </body>
</html>
