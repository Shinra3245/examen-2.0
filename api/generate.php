<?php
// Cargar la clase y las dependencias
require_once __DIR__ . '/../src/QrGenerator.php';

/**
 * Envía una respuesta de error en formato JSON y termina el script.
 *
 * @param string $message El mensaje de error.
 * @param int    $code    El código de estado HTTP.
 */
function send_json_error($message, $code) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => ['message' => $message, 'code' => $code]]);
    exit;
}

// Envolver toda la lógica en un bloque try-catch para manejar errores de forma centralizada
try {
    // --- Recolección de Parámetros ---
    $tipo = $_POST['tipo'] ?? 'texto';
    $tamaño = $_POST['tamaño'] ?? 300;
    $nivel_correccion = $_POST['nivel_correccion'] ?? 'M';

    // Instanciar el generador con la configuración general
    $qrGenerator = new QrGenerator($tamaño, $nivel_correccion);

    // --- Enrutamiento por Tipo de QR ---
    switch ($tipo) {
        case 'texto':
            $contenido = $_POST['contenido'] ?? '';
            $qrGenerator->text($contenido);
            break;

        case 'url':
            $contenido = $_POST['contenido'] ?? '';
            $qrGenerator->url($contenido);
            break;

        case 'wifi':
            $ssid = $_POST['ssid'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';
            $tipo_red = $_POST['tipo_red'] ?? 'WPA'; // WPA, WEP, nopass
            $qrGenerator->wifi($ssid, $contrasena, $tipo_red);
            break;

        case 'geo':
            $latitud = $_POST['latitud'] ?? '';
            $longitud = $_POST['longitud'] ?? '';
            $qrGenerator->geo($latitud, $longitud);
            break;

        default:
            // Usar la función de error para un tipo no válido
            send_json_error("El tipo de QR '{$tipo}' no es válido.", 400);
            break;
    }

} catch (Exception $e) {
    // Capturar cualquier excepción lanzada desde el QrGenerator
    // Usar el código del error de la excepción si está disponible, si no, 500.
    send_json_error($e->getMessage(), $e->getCode() ?: 500);
}
