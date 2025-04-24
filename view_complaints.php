<?php
// --- INICIO DE VERIFICACIÓN DE SESIÓN ---
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php?required=1');
    exit;
}
// --- FIN DE VERIFICACIÓN DE SESIÓN ---

require_once 'db_config.php';
$logged_user_name = isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name'], ENT_QUOTES, 'UTF-8') : (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') : 'Usuario');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Denuncias Registradas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .header-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; flex-wrap: wrap; }
        .header-info span { font-size: 0.95rem; color: #555; }
        .logout-link { text-decoration: none; color: #dc3545; font-weight: bold; padding: 5px 10px; border: 1px solid #dc3545; border-radius: 4px; transition: all 0.2s ease; }
        .logout-link:hover { background-color: #dc3545; color: white; }
        /* Estilo para el enlace de editar */
        .edit-link {
            display: inline-block;
            padding: 3px 8px;
            background-color: #ffc107; /* Amarillo advertencia */
            color: #333;
            text-decoration: none;
            border-radius: 3px;
            font-size: 0.85rem;
            border: 1px solid #dda_yellow_color; /* Un borde sutil */
        }
        .edit-link:hover {
            background-color: #e0a800;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="container view-container">
        <div class="header-info">
             <span>Usuario conectado: <strong><?php echo $logged_user_name; ?></strong></span>
            <a href="logout.php" class="logout-link">Cerrar Sesión</a>
        </div>

        <h1>Listado de Denuncias Registradas</h1>
        <a href="index.php" class="back-link">&larr; Ir al Registro Público de Denuncias</a>

        <?php
        // Mostrar mensajes de éxito o error de la ACTUALIZACIÓN
        if (isset($_GET['update_status'])) {
            if ($_GET['update_status'] == 'success') {
                echo '<div class="message success">Denuncia actualizada exitosamente.</div>';
            } elseif ($_GET['update_status'] == 'error') {
                echo '<div class="message error">Hubo un error al actualizar la denuncia.</div>';
            } elseif ($_GET['update_status'] == 'notfound') {
                 echo '<div class="message error">Error: No se encontró la denuncia a actualizar.</div>';
            }
        }

        // --- Consulta y muestra de tabla (con nueva columna de acciones) ---
        $sql = "SELECT id, timestamp, complainant_name, complainant_role, complaint_type, description, involved_parties, status FROM denuncias ORDER BY timestamp DESC";
        $result = $conn->query($sql);

        if ($result === false) {
            echo '<p class="message error">Error al realizar la consulta a la base de datos: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($result->num_rows > 0) {
            echo '<table>';
            echo '<thead><tr>'; // Encabezados
            echo '<th>ID</th>';
            echo '<th>Fecha y Hora</th>';
            echo '<th>Denunciante</th>';
            echo '<th>Rol</th>';
            echo '<th>Tipo</th>';
            echo '<th>Descripción</th>';
            echo '<th>Involucrados</th>';
            echo '<th>Estado</th>';
            echo '<th>Acciones</th>'; // <-- NUEVA COLUMNA
            echo '</tr></thead><tbody>';

            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['timestamp'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['complainant_name'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['complainant_role'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['complaint_type'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td class="description" title="' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '">'
                     . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['involved_parties'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') . '</td>';
                // --- NUEVA CELDA CON ENLACE DE EDITAR ---
                echo '<td>';
                echo '<a href="edit_complaint.php?id=' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '" class="edit-link">Editar</a>';
                echo '</td>';
                // --- FIN NUEVA CELDA ---
                echo '</tr>';
            }
            echo '</tbody></table>';
            $result->free();
        } else {
            echo '<p class="no-complaints">Aún no hay denuncias registradas en la base de datos.</p>';
        }
        $conn->close();
        ?>
    </div> </body>
</html>
