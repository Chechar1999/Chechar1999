<?php
// Incluir el archivo de configuración para obtener la conexión $conn
require_once 'db_config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Denuncias Registradas</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <div class="container view-container">
        <h1>Listado de Denuncias Registradas</h1>
        <a href="index.php" class="back-link">&larr; Registrar Nueva Denuncia</a>

        <?php
        // Consulta SQL para seleccionar todas las denuncias, ordenadas por fecha descendente
        $sql = "SELECT id, timestamp, complainant_name, complainant_role, complaint_type, description, involved_parties, status FROM denuncias ORDER BY timestamp DESC";

        // Ejecutar la consulta usando la conexión $conn
        $result = $conn->query($sql);

        // Verificar si la consulta fue exitosa
        if ($result === false) {
            echo '<p class="message error">Error al realizar la consulta a la base de datos: ' . htmlspecialchars($conn->error, ENT_QUOTES, 'UTF-8') . '</p>';
        } elseif ($result->num_rows > 0) {
            // Hay denuncias, mostrar la tabla
            echo '<table>';
            echo '<thead><tr>';
            echo '<th>ID</th>';
            echo '<th>Fecha y Hora</th>';
            echo '<th>Denunciante</th>';
            echo '<th>Rol</th>';
            echo '<th>Tipo</th>';
            echo '<th>Descripción</th>';
            echo '<th>Involucrados</th>';
            echo '<th>Estado</th>';
            echo '</tr></thead><tbody>';

            // Recorrer cada fila del resultado
            while($row = $result->fetch_assoc()) {
                echo '<tr>';
                // Acceder a los datos por el nombre de la columna
                // ¡IMPORTANTE! Usar htmlspecialchars() en CADA dato que se muestra para prevenir XSS
                echo '<td>' . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['timestamp'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['complainant_name'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['complainant_role'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['complaint_type'], ENT_QUOTES, 'UTF-8') . '</td>';
                // Aplicar clase 'description' y 'title' para posible truncamiento CSS
                echo '<td class="description" title="' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '">'
                     . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['involved_parties'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '<td>' . htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>'; // Cerrar tabla

            // Liberar el conjunto de resultados
            $result->free();

        } else {
            // No se encontraron denuncias en la tabla
            echo '<p class="no-complaints">Aún no hay denuncias registradas en la base de datos.</p>';
        }

        // Cerrar la conexión a la base de datos
        $conn->close();
        ?>
    </div> </body>
</html>
