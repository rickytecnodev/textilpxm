<?php
/**
 * Configuración Principal del Sitio
 * Definiciones globales del proyecto
 */

// Definir constantes del sistema
define('PROJECT_NAME', 'TextilPXM');
define('PROJECT_VERSION', '1.0.0');

// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('DATA_PATH', ROOT_PATH . '/data');

// Configuración de URLs - Detección automática
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

$scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
// En XAMPP/Windows a veces SCRIPT_NAME es ruta de disco (C:/xampp/htdocs/...), lo que rompe BASE_URL
$isFilesystemPath = (strpos($scriptPath, ':') !== false || strpos($scriptPath, '\\') !== false);
if ($isFilesystemPath && !empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_FILENAME'])) {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
    $docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));
    $basePath = ($docRoot !== '' && strpos($scriptDir, $docRoot) === 0)
        ? substr($scriptDir, strlen($docRoot))
        : '/' . basename($scriptDir);
    $basePath = '/' . trim(str_replace('\\', '/', $basePath), '/');
    $baseUrl = $protocol . '://' . $host . $basePath;
} else {
    $baseUrl = $protocol . '://' . $host . $scriptPath;
}
$baseUrl = rtrim($baseUrl, '/');
// Quitar /public de la URL para que los enlaces sean sin "public" (ej. /textilpxm/admin)
define('BASE_URL', preg_replace('#/public$#', '', $baseUrl));

// ASSETS_URL: URL base para css, js, imágenes (carpeta public). Se calcula desde DOCUMENT_ROOT para que funcione con cualquier nivel de carpetas.
$docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\'));
$publicPath = str_replace('\\', '/', realpath(PUBLIC_PATH) ?: PUBLIC_PATH);
$docRootReal = str_replace('\\', '/', realpath($docRoot) ?: $docRoot);
$assetsPath = '';
if ($docRootReal !== '' && strpos($publicPath, $docRootReal) === 0) {
    $assetsPath = substr($publicPath, strlen($docRootReal));
    $assetsPath = '/' . trim(str_replace('\\', '/', $assetsPath), '/');
}
define('ASSETS_URL', ($assetsPath !== '' ? $protocol . '://' . $host . $assetsPath : BASE_URL . '/public'));

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'textilpxm_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración del sitio
define('SITE_NAME', 'TextilPXM');
define('SITE_DESCRIPTION', 'Sistema de gestión textil');
define('SITE_EMAIL', 'info@textilpxm.com');

// Configuración de la sesión
define('SESSION_NAME', 'TEXTILPXM_SESSION');
define('SESSION_LIFETIME', 86400); // 24 horas

// Configuración de errores: solo mostrar en desarrollo
$isDev = (getenv('APP_ENV') ?: 'production') === 'development';
ini_set('display_errors', $isDev ? '1' : '0');
ini_set('display_startup_errors', $isDev ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Zona horaria
date_default_timezone_set('America/Lima');

// Iniciar sesión
session_name(SESSION_NAME);
session_start();

// Incluir helpers
require_once APP_PATH . '/helpers.php';

// Autoload de clases
spl_autoload_register(function($class) {
    $paths = [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/views/',
        APP_PATH . '/', // Para Router y otras clases en la raíz de app
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});