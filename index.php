<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Denuncias - Universidad</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <div class="container">
        <h1>Sistema de Registro de Denuncias</h1>
        <p>Utilice este formulario para registrar su denuncia. Asegúrese de proporcionar detalles claros.</p>

        <?php
        // Mostrar mensajes de éxito o error si existen (enviados desde submit_complaint.php)
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo '<div class="message success">Denuncia registrada exitosamente. Se ha asignado un ID único para referencia futura.</div>';
            } elseif ($_GET['status'] == 'error') {
                echo '<div class="message error">Hubo un error al registrar la denuncia. Por favor, intente de nuevo o contacte al administrador.</div>';
            } elseif ($_GET['status'] == 'missing_data') {
                 echo '<div class="message error">Error: La descripción de la denuncia es obligatoria. Por favor, complétela.</div>';
            }
        }
        ?>

        <form action="submit_complaint.php" method="POST">
            <label for="complainant_name">Su Nombre (Opcional):</label>
            <input type="text" id="complainant_name" name="complainant_name">

            <div class="checkbox-container">
                <input type="checkbox" id="anonymous" name="anonymous" value="1">
                <label for="anonymous" class="checkbox-label">Registrar como Anónimo</label>
            </div>
            <br>

            <label for="complainant_role">Su Rol en la Universidad:</label>
            <select id="complainant_role" name="complainant_role" required>
                <option value="">Seleccione...</option>
                <option value="Estudiante">Estudiante</option>
                <option value="Docente">Docente</option>
                <option value="Personal Administrativo">Personal Administrativo</option>
                <option value="Personal Obrero">Personal Obrero</option>
                <option value="Externo">Externo</option>
                <option value="No Aplica/Anónimo">No Aplica/Anónimo</option>
            </select>

            <label for="complaint_type">Tipo de Denuncia:</label>
            <select id="complaint_type" name="complaint_type" required>
                <option value="">Seleccione...</option>
                <option value="Académica">Académica (Notas, evaluaciones, etc.)</option>
                <option value="Acoso">Acoso (Sexual, laboral, etc.)</option>
                <option value="Discriminación">Discriminación</option>
                <option value="Administrativa">Administrativa (Trámites, servicios)</option>
                <option value="Infraestructura">Infraestructura (Instalaciones, equipos)</option>
                <option value="Seguridad">Seguridad</option>
                <option value="Violencia">Violencia</option>
                <option value="Otra">Otra</option>
            </select>

            <label for="description">Descripción Detallada de los Hechos:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="involved_parties">Personas Involucradas (si aplica y conoce):</label>
            <input type="text" id="involved_parties" name="involved_parties">

            <button type="submit">Enviar Denuncia</button>
        </form>

        <a href="view_complaints.php" class="view-link">Ver Denuncias Registradas</a>
    </div>
</body>
</html>

