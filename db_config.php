<?php
/*
 * Archivo de Configuración de la Base de Datos
 * Modifica estos valores con los de tu entorno MySQL.
 */

define('DB_SERVER', 'localhost');    // Servidor de la DB (usualmente 'localhost')
define('DB_USERNAME', 'root');       // Tu nombre de usuario de MySQL (cambiar si es diferente)
define('DB_PASSWORD', '');           // Tu contraseña de MySQL (cambiar si tienes una)
define('DB_NAME', 'universidad_denuncias_db'); // Nombre de la base de datos que creaste

/* Intento de conexión a la base de datos MySQL */
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if($conn === false || $conn->connect_error){
    // No mostrar errores detallados en producción por seguridad.
    // En desarrollo, puedes descomentar: die("ERROR: No se pudo conectar. " . $conn->connect_error);
    error_log("Error de conexión a la base de datos: " . $conn->connect_error); // Registrar error
    die("ERROR: No se pudo establecer conexión con la base de datos. Por favor, contacte al administrador.");
}

// Establecer el conjunto de caracteres a UTF-8 (muy importante para acentos y ñ)
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error al establecer el conjunto de caracteres utf8mb4: " . $conn->error);
    // Podrías decidir si continuar o detener la ejecución aquí
}

// La variable $conn ahora está disponible para ser usada en otros scripts que incluyan este archivo.
?>
