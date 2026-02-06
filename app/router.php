<?php
/**
 * Router
 * Sistema de enrutamiento para mapear URLs a controladores y métodos
 */

class Router {
    private $controllerName = 'HomeController';
    private $methodName = 'index';
    private $params = [];

    /**
     * Constructor
     */
    public function __construct() {
        // Obtener la URL de la petición
        $url = $this->getUrl();

        // Procesar la URI
        $urlParts = [];
        if (!empty($url) && $url !== '/') {
            $urlParts = $this->processUrl($url);
        }
        
        // Rutas especiales que mapean a métodos del HomeController
        $homeRoutes = [
            'categorias' => 'categorias',
            'producto' => 'producto',
            'ordenar' => 'ordenar',
            'login' => 'login',
            'register' => 'register',
            'logout' => 'logout',
            'contact' => 'contact',
            'about' => 'about',
        ];
        
        // Redirigir /products al panel de administración (gestión real está en /admin)
        if (!empty($urlParts[0]) && $urlParts[0] === 'products') {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        // Panel de administración: /admin, /admin/login, /admin/productos, etc.
        if (!empty($urlParts[0]) && $urlParts[0] === 'admin') {
            $this->controllerName = 'AdminController';
            unset($urlParts[0]);
            $this->methodName = !empty($urlParts[1]) ? $urlParts[1] : 'index';
            if (!empty($urlParts[1])) {
                unset($urlParts[1]);
            }
            $this->params = $urlParts ? array_values($urlParts) : [];
        } else {
            // Establecer el controlador (se convierte a formato PascalCase con primera letra mayúscula)
            if (!empty($urlParts[0])) {
                $firstPart = $urlParts[0];
                
                // Si es una ruta especial del HomeController, usar HomeController
                if (isset($homeRoutes[$firstPart])) {
                    $this->controllerName = 'HomeController';
                    $this->methodName = $homeRoutes[$firstPart];
                    unset($urlParts[0]);
                } else {
                    // Intentar buscar un controlador específico
                    $this->controllerName = ucfirst($firstPart) . 'Controller';
                    unset($urlParts[0]);
                }
            }
            
            // Establecer el método solo si no se estableció desde las rutas especiales
            if ($this->methodName === 'index' && !empty($urlParts[1])) {
                $this->methodName = $urlParts[1];
                unset($urlParts[1]);
            }
            
            // Obtener parámetros
            $this->params = $urlParts ? array_values($urlParts) : [];
        }

        // Verificar si el controlador existe
        $controllerFile = APP_PATH . '/controllers/' . $this->controllerName . '.php';
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            // Si no existe el controlador, usar HomeController
            $this->controllerName = 'HomeController';
            require_once APP_PATH . '/controllers/' . $this->controllerName . '.php';
        }

        // Instanciar el controlador
        $controller = new $this->controllerName();

        // Verificar si el método existe
        if (method_exists($controller, $this->methodName)) {
            // Llamar al controlador con los parámetros
            call_user_func_array([$controller, $this->methodName], $this->params);
        } else {
            // Si el método no existe, usar el método index
            $controller->index();
        }
    }

    /**
     * Obtener y procesar la URL
     */
    private function getUrl() {
        $url = '';
        
        // Prioridad: usar el parámetro path de .htaccess
        if (isset($_GET['path'])) {
            $url = '/' . trim($_GET['path'], '/');
            if ($url === '/') {
                $url = '/';
            }
            return $url;
        }
        
        // Si no hay parámetro path, usar REQUEST_URI
        if (isset($_SERVER['REQUEST_URI'])) {
            $url = $_SERVER['REQUEST_URI'];
            
            // Obtener la ruta base del script (directorio donde está index.php)
            $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
            
            // Si la ruta base no es '/', eliminarla de la URL
            if ($scriptPath !== '/' && $scriptPath !== '\\' && $scriptPath !== '.') {
                $url = str_replace($scriptPath, '', $url);
            }
            
            // Eliminar query parameters
            $url = strtok($url, '?');
            
            // Eliminar barras diagonales finales
            $url = rtrim($url, '/');
            
            // Si está vacío, usar '/' para el home
            $url = $url === '' ? '/' : $url;
        }

        return $url;
    }

    /**
     * Procesar y dividir la URL en partes
     */
    private function processUrl($url) {
        if ($url === '/') {
            return [];
        }
        // Eliminar la barra inicial si existe
        $url = ltrim($url, '/');
        // Dividir por /
        $parts = explode('/', filter_var($url, FILTER_SANITIZE_URL));
        // Eliminar elementos vacíos y reindexar
        return array_values(array_filter($parts, function($part) {
            return !empty($part);
        }));
    }
}