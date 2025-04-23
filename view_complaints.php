<?php
// --- INICIO DE VERIFICACIÓN DE SESIÓN ---
// Iniciar sesión ANTES de cualquier salida HTML
session_start();

// Verificar si el usuario está logueado. Si no, redirigir al login.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Guardar la URL a la que intentaba acceder para redirigir después del login (opcional)
    // $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];

    header('Location: login.php?required=1'); // Añadimos ?required=1 para un mensaje específico
    exit;
}
// --- FIN DE VERIFICACIÓN DE SESIÓN ---

// El resto del código solo se ejecuta si el usuario está logueado.
// Incluir configuración de la base de datos
require_once 'db_config.php';

// Obtener nombre del usuario logueado para mostrarlo (opcional)
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
        // --- (El resto del código PHP para mostrar la tabla es IGUAL que antes) ---
        // Consulta SQL para seleccionar todas las denuncias...
        $sql = "SELECT id, timestamp, complainant_name, complainant_role, complaint_type, description, involved_parties, status FROM denuncias ORDER BY timestamp DESC";
        $result = $conn->query($sql);

        if ($result === false) {
            echo '<p class="message error">Error al realizar la consulta a la base de datos: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($result->num_rows > 0) {
            echo '<table>';
            echo '<thead><tr><th>ID</th><th>Fecha y Hora</th><th>Denunciante</th><th>Rol</th><th>Tipo</th><th>Descripción</th><th>Involucrados</th><th>Estado</th></tr></thead><tbody>';
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

