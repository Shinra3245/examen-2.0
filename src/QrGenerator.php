<?php

require_once __DIR__ . '/../phpqrcode/qrlib.php';

/**
 * Clase para encapsular la generación de códigos QR.
 */
class QrGenerator {
    private $size;
    private $level;

    /**
     * Constructor de la clase.
     *
     * @param int    $size  Dimensión del QR en píxeles.
     * @param string $level Nivel de corrección de errores (L, M, Q, H).
     */
    public function __construct($size = 300, $level = 'M') {
        // Validar y establecer el tamaño
        $this->size = (int)$size;
        if ($this->size < 100) $this->size = 100;
        if ($this->size > 1000) $this->size = 1000;

        // Validar y establecer el nivel de corrección
        $this->level = strtoupper($level);
        if (!in_array($this->level, ['L', 'M', 'Q', 'H'])) {
            $this->level = 'M';
        }
    }

    /**
     * Genera la imagen PNG del código QR y la envía al navegador.
     *
     * @param string $content El contenido a codificar.
     * @throws Exception Si el contenido está vacío.
     */
    private function generatePng(string $content) {
        if (empty($content)) {
            // Código 400: Bad Request
            throw new Exception("El contenido para el código QR no puede estar vacío.", 400);
        }
        
        // Generar y mostrar el código QR directamente como una imagen PNG
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="codigo_qr.png"');
        
        // QRcode::png($text, $outfile=false, $level=QR_ECLEVEL_L, $size=3, $margin=4, $saveandprint=false)
        QRcode::png($content, false, $this->level, floor($this->size / 38), 2); // 38 es aprox. el num de modulos en v1
    }

    /**
     * Genera un QR para texto plano.
     *
     * @param string $content El texto a codificar.
     * @throws Exception Si el contenido excede la capacidad máxima.
     */
    public function text(string $content) {
        if (strlen($content) > 2953) { // Límite para alfanuméricos en versión 40-H
            // Código 413: Payload Too Large
            throw new Exception("El contenido excede la capacidad máxima para un código QR.", 413);
        }
        $this->generatePng($content);
    }

    /**
     * Genera un QR para una URL.
     *
     * @param string $url La URL a codificar.
     * @throws Exception Si la URL no es válida.
     */
    public function url(string $url) {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            // Código 400: Bad Request
            throw new Exception("La URL proporcionada no es válida.", 400);
        }
        $this->generatePng($url);
    }

    /**
     * Genera un QR para credenciales WiFi.
     *
     * @param string $ssid         El SSID de la red.
     * @param string $password     La contraseña de la red.
     * @param string $securityType El tipo de seguridad (WPA/WPA2, WEP, nopass).
     * @throws Exception Si el SSID está vacío.
     */
    public function wifi(string $ssid, string $password, string $securityType) {
        if (empty($ssid)) {
            // Código 400: Bad Request
            throw new Exception("El SSID no puede estar vacío.", 400);
        }
        $content = "WIFI:S:{$ssid};T:{$securityType};P:{$password};;";
        $this->generatePng($content);
    }

    /**
     * Genera un QR para coordenadas de geolocalización.
     *
     * @param string $latitude  La latitud.
     * @param string $longitude La longitud.
     * @throws Exception Si las coordenadas no son válidas.
     */
    public function geo(string $latitude, string $longitude) {
        if (!is_numeric($latitude) || !is_numeric($longitude) || $latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            // Código 400: Bad Request
            throw new Exception("Las coordenadas de geolocalización no son válidas.", 400);
        }
        $content = "geo:{$latitude},{$longitude}";
        $this->generatePng($content);
    }
}
