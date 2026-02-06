<?php
/**
 * Clase Base para los Controladores
 * Proporciona funcionalidad común para todos los controladores
 */

class Controller {
    protected $view;
    protected $data = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->view = new View();
    }

    /**
     * Renderizar una vista con el layout
     * @param string $layout 'main', 'admin' o null para sin layout
     */
    protected function render($view, $data = [], $layout = 'main') {
        $this->data = array_merge($this->data, $data);
        $this->view->render($view, $this->data, $layout);
    }

    /**
     * Redireccionar a una URL
     */
    protected function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    /**
     * Redireccionar con mensaje flash
     */
    protected function redirectWithMessage($url, $message, $type = 'success') {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
        $this->redirect($url);
    }

    /**
     * Verificar si hay un mensaje flash (consume el mensaje de sesión)
     */
    public function getFlashMessage() {
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            $type = $_SESSION['flash_type'] ?? 'success';
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            return ['message' => $message, 'type' => $type];
        }
        return null;
    }

    /**
     * Fusionar mensaje flash en el array de datos para la vista.
     * Evita repetir getFlashMessage + array en cada acción.
     * @param array $data Datos a pasar a la vista
     * @return array $data con flash_message y flash_type añadidos (o null si no hay)
     */
    protected function mergeFlashIntoData(array $data = []) {
        $flash = $this->getFlashMessage();
        $data['flash_message'] = $flash['message'] ?? null;
        $data['flash_type'] = $flash['type'] ?? null;
        return $data;
    }

    /**
     * Verificar si el usuario está autenticado
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Requerir autenticación
     */
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
        }
    }

    /**
     * Obtener usuario autenticado
     */
    protected function getAuthUser() {
        if ($this->isAuthenticated()) {
            return $_SESSION['user_id'];
        }
        return null;
    }
}