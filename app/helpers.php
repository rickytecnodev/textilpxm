<?php
/**
 * Funciones Helper
 * Funciones auxiliares para uso en vistas y controladores
 */

/**
 * Generar y guardar token CSRF en sesión. Debe llamarse al mostrar formularios.
 * @return string Token para usar en campo oculto
 */
function csrf_token() {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Comprobar token CSRF (en controladores, en POST).
 * @param string|null $token Valor del campo _csrf (por defecto $_POST['_csrf'])
 * @return bool
 */
function csrf_verify($token = null) {
    $token = $token ?? ($_POST['_csrf'] ?? '');
    return $token !== '' && isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Cargar contenido: primero desde BD (site_content), luego fallback a content_data.php
 * @param string $filename Clave del contenido: navbar, footer, meta, home
 * @return array|false Array con los datos o false si no existe
 */
function loadContent($filename) {
    static $fromDb = null;
    if ($fromDb === null) {
        try {
            $model = new SiteContent();
            $fromDb = $model->getNested();
        } catch (Throwable $e) {
            $fromDb = [];
        }
    }
    if (!empty($fromDb[$filename])) {
        return $fromDb[$filename];
    }
    $filePath = APP_PATH . '/content_data.php';
    if (!file_exists($filePath)) {
        return false;
    }
    $contentData = require $filePath;
    return isset($contentData[$filename]) ? $contentData[$filename] : false;
}

/**
 * Obtener un valor anidado de un array usando notación de punto
 * @param array $data Array de datos
 * @param string $key Clave en notación de punto (ej: "menu.inicio")
 * @param mixed $default Valor por defecto si no se encuentra
 * @return mixed
 */
function getContent($data, $key, $default = '') {
    if ($data === false) {
        return $default;
    }
    
    $keys = explode('.', $key);
    $value = $data;
    
    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }
    
    return $value;
}

/**
 * URL completa para la imagen de un producto.
 * Si imagen_url es ruta local (ej. productos/1.jpg), devuelve ASSETS_URL/images/...
 * Si es URL externa (http...), la devuelve tal cual.
 * @param string $imagen_url Valor de producto['imagen_url']
 * @param string $default URL por defecto si está vacío
 * @return string
 */
function productImageUrl($imagen_url, $default = '') {
    $imagen_url = trim($imagen_url ?? '');
    if ($imagen_url === '') {
        return $default !== '' ? $default : (ASSETS_URL . '/images/productos/placeholder.svg');
    }
    if (preg_match('#^https?://#i', $imagen_url)) {
        return $imagen_url;
    }
    return ASSETS_URL . '/images/' . $imagen_url;
}

/**
 * Obtener URLs de las imágenes de galería de un producto (archivos {id}_galeria_{n}.{ext}).
 * No se guardan en BD; se listan por convención desde public/images/productos.
 * @param int $productId ID del producto
 * @return array Lista de URLs completas, ordenadas por nombre de archivo
 */
function productGalleryUrls($productId) {
    $productId = (int) $productId;
    if ($productId <= 0 || !defined('PUBLIC_PATH')) {
        return [];
    }
    $dir = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'productos';
    if (!is_dir($dir)) {
        return [];
    }
    $pattern = str_replace('\\', '/', $dir) . '/' . $productId . '_galeria_*';
    $files = glob($pattern);
    if ($files === false) {
        return [];
    }
    sort($files);
    $urls = [];
    foreach ($files as $path) {
        if (is_file($path)) {
            $basename = basename($path);
            if (preg_match('/^' . preg_quote((string)$productId, '/') . '_galeria_\d+\.([a-z0-9]+)$/i', $basename)) {
                $urls[] = ASSETS_URL . '/images/productos/' . $basename;
            }
        }
    }
    return $urls;
}

/**
 * Obtener archivos de galería de un producto para admin (url + nombre de archivo para eliminar).
 * @param int $productId ID del producto
 * @return array Lista de ['url' => string, 'filename' => string]
 */
function productGalleryFiles($productId) {
    $productId = (int) $productId;
    if ($productId <= 0 || !defined('PUBLIC_PATH')) {
        return [];
    }
    $dir = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'productos';
    if (!is_dir($dir)) {
        return [];
    }
    $pattern = str_replace('\\', '/', $dir) . '/' . $productId . '_galeria_*';
    $files = glob($pattern);
    if ($files === false) {
        return [];
    }
    sort($files);
    $items = [];
    foreach ($files as $path) {
        if (is_file($path)) {
            $basename = basename($path);
            if (preg_match('/^' . preg_quote((string)$productId, '/') . '_galeria_\d+\.([a-z0-9]+)$/i', $basename)) {
                $items[] = [
                    'url' => ASSETS_URL . '/images/productos/' . $basename,
                    'filename' => $basename,
                ];
            }
        }
    }
    return $items;
}
