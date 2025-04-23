<?php
/*
 * SCRIPT PARA CREAR UN USUARIO INICIAL - ¡SOLO PARA ADMINISTRACIÓN!
 * ¡Eliminar o proteger este archivo después de usarlo!
 */
require_once 'db_config.php'; // Incluir conexión a la BD

// --- Define aquí los datos del primer usuario ---
$new_username = 'admin'; // Cambia si quieres otro nombre de usuario
$plain_password = 'password123'; // ¡ELIGE UNA CONTRASEÑA SEGURA!
$full_name = 'Administrador del Sistema'; // Opcional
$role = 'admin'; // Rol inicial

// --- Hashing de la contraseña (¡MUY IMPORTANTE!) ---
// PASSWORD_DEFAULT usa el algoritmo de hashing más seguro disponible en tu versión de PHP
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

if ($hashed_password === false) {
    die('Error al hashear la contraseña.');
}

// --- Insertar en la base de datos ---
$sql = "INSERT INTO usuarios (username, password, full_name, role) VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error al preparar la consulta: " . htmlspecialchars($conn->error));
}

// Vincular parámetros: s = string
$stmt->bind_param("ssss", $new_username, $hashed_password, $full_name, $role);

// Ejecutar
if ($stmt->execute()) {
    echo "Usuario '" . htmlspecialchars($new_username) . "' creado exitosamente.";
    echo "<br>Recuerda eliminar o proteger este archivo (create_user.php).";
} else {
    // Manejar error de duplicado de username
    if ($conn->errno == 1062) { // 1062 es el código de error para entrada duplicada
         echo "Error: El nombre de usuario '" . htmlspecialchars($new_username) . "' ya existe.";
    } else {
        echo "Error al crear el usuario: " . htmlspecialchars($stmt->error);
    }
}

// Cerrar
$stmt->close();
$conn->close();
?>
