<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Denuncias Registradas</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <div class="container view-container"> <h1>Listado de Denuncias Registradas</h1>
        <a href="index.php" class="back-link">&larr; Registrar Nueva Denuncia</a>

        <?php
        $csv_file = 'complaints.csv'; // Nombre del archivo de datos

        // Verificar si el archivo existe y si se puede leer
        if (!file_exists($csv_file) || !is_readable($csv_file)) {
            echo '<p class="no-complaints">No hay denuncias registradas o el archivo no se puede leer. Verifique los permisos.</p>';
        } else {
            // Intentar abrir el archivo CSV para lectura ('r')
            $handle = @fopen($csv_file, 'r');

            // Verificar si se pudo abrir
            if ($handle === false) {
                echo '<p class="no-complaints">Error crítico: No se pudo abrir el archivo de denuncias.</p>';
            } else {
                echo '<table>';
                $is_header_row = true; // Bandera para saber si estamos en la primera fila (encabezados)
                $row_count = 0; // Contador de filas de datos (sin encabezados)

                // Leer el archivo línea por línea (cada línea es una fila CSV)
                // fgetcsv parsea la línea CSV en un array
                while (($data = fgetcsv($handle)) !== false) {
                    // Si es la primera fila, tratarla como encabezados
                    if ($is_header_row) {
                        echo '<thead><tr>';
                        foreach ($data as $header) {
                            // Sanitizar encabezados por seguridad antes de mostrarlos
                            echo '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
                        }
                        echo '</tr></thead><tbody>'; // Cerrar thead y abrir tbody
                        $is_header_row = false; // Las siguientes filas ya no son encabezados
                    } else {
                        // Es una fila de datos
                        $row_count++;
                        echo '<tr>';
                        // Iterar sobre cada celda ($cell) de la fila ($data)
                        foreach ($data as $index => $cell) {
                             // Columna 5 es la descripción (índice basado en 0)
                             $td_class = ($index == 5) ? ' class="description"' : '';
                             // Añadir 'title' para ver el texto completo al pasar el mouse sobre celdas truncadas
                             $title_attr = ($index == 5) ? ' title="' . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . '"' : '';

                            // Sanitizar cada celda antes de mostrarla en HTML
                            echo '<td' . $td_class . $title_attr . '>' . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . '</td>';
                        }
                        echo '</tr>';
                    }
                } // Fin del while

                echo '</tbody></table>'; // Cerrar tbody y table

                // Cerrar el manejador del archivo
                fclose($handle);

                // Mensaje si solo se encontraron encabezados (o el archivo estaba vacío)
                if ($row_count === 0 && !$is_header_row) { // !$is_header_row asegura que se procesó al menos el encabezado
                     echo '<p class="no-complaints">Aún no hay denuncias registradas.</p>';
                } elseif ($is_header_row) { // Si $is_header_row sigue true, el archivo estaba completamente vacío
                     echo '<p class="no-complaints">El archivo de denuncias está vacío.</p>';
                }
            } // Fin del else (handle !== false)
        } // Fin del else (file_exists)
        ?>
    </div> </body>
</html>
