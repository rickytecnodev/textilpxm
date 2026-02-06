<?php
/**
 * Controlador de Administración
 * Login solo para rol admin. CRUD de productos. Sin enlace desde la página principal.
 * Acceso solo por URL: /admin, /admin/login, etc.
 */

class AdminController extends Controller {
    private $userModel;
    private $productModel;
    private $siteContentModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->siteContentModel = new SiteContent();
    }

    /** Extensiones y MIME permitidos para imágenes de productos */
    private static $productImageTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /**
     * Subir imagen de producto. Guarda en public/images/productos como {id}.{ext}
     * @param string $inputName Nombre del input file (ej: 'imagen')
     * @param int $productId ID del producto
     * @return string Ruta relativa para BD (ej: 'productos/1.jpg') o ''
     */
    private function uploadProductImage($inputName, $productId) {
        if (empty($_FILES[$inputName]['tmp_name']) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return '';
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES[$inputName]['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, self::$productImageTypes, true)) {
            return '';
        }
        $ext = (new SplFileInfo($_FILES[$inputName]['name']))->getExtension();
        $ext = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'jpg';
        $dir = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'productos';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $dest = $dir . DIRECTORY_SEPARATOR . $productId . '.' . $ext;
        if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $dest)) {
            return '';
        }
        return 'productos/' . $productId . '.' . $ext;
    }

    /**
     * Eliminar archivo físico de imagen de producto si es ruta local (productos/...)
     * @param string $imagenUrl Valor de imagen_url en BD
     */
    private function deleteProductImageFile($imagenUrl) {
        $imagenUrl = trim($imagenUrl ?? '');
        if ($imagenUrl === '' || strpos($imagenUrl, 'productos/') !== 0) {
            return;
        }
        // Evitar path traversal (.., barras invertidas, etc.)
        if (preg_match('/\.\.|[\\\\]/', $imagenUrl)) {
            return;
        }
        $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagenUrl);
        $realPath = realpath($path);
        $basePath = realpath(PUBLIC_PATH . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'productos');
        if ($realPath !== false && $basePath !== false && strpos($realPath, $basePath) === 0 && is_file($realPath)) {
            @unlink($realPath);
        }
    }

    /**
     * Comprobar que el usuario está logueado y es admin
     */
    private function requireAdmin() {
        if (empty($_SESSION['user_id']) || ($_SESSION['user_rol'] ?? '') !== 'admin') {
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            $uri = strtok($uri, '?');
            $basePath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
            if ($basePath !== '/' && $basePath !== '\\' && $basePath !== '.') {
                $uri = (strpos($uri, $basePath) === 0) ? substr($uri, strlen($basePath)) : '/admin';
            }
            $_SESSION['admin_redirect'] = $uri !== '' ? $uri : '/admin';
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
    }

    /**
     * Login solo para administradores.
     * GET /admin/login → redirige a /admin (el login se muestra allí).
     * POST /admin/login → procesa el formulario de login.
     */
    public function login() {
        if (($this->isAuthenticated()) && ($_SESSION['user_rol'] ?? '') === 'admin') {
            $this->redirect(BASE_URL . '/admin');
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        if (!csrf_verify()) {
            $_SESSION['flash_email'] = trim($_POST['email'] ?? '');
            $this->redirectWithMessage('/admin', 'Sesión inválida. Intenta de nuevo.', 'danger');
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['flash_email'] = $email;
            $this->redirectWithMessage('/admin', 'Completa email y contraseña.', 'danger');
            return;
        }

        $user = $this->userModel->login($email, $password);
        if (!$user) {
            $_SESSION['flash_email'] = $email;
            $this->redirectWithMessage('/admin', 'Credenciales incorrectas.', 'danger');
            return;
        }
        if (($user['rol'] ?? '') !== 'admin') {
            $_SESSION['flash_email'] = $email;
            $this->redirectWithMessage('/admin', 'Acceso denegado. Solo administradores.', 'danger');
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_rol'] = $user['rol'];

        $path = $_SESSION['admin_redirect'] ?? '/admin';
        unset($_SESSION['admin_redirect']);
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    /**
     * Mostrar formulario de login (GET /admin cuando no está autenticado)
     */
    private function showLoginForm($error = null, $email = '') {
        $this->render('admin/login', [
            'page_title' => 'Admin Login',
            'error' => $error,
            'email' => $email,
            'csrf_token' => csrf_token(),
        ], 'admin');
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }

    /**
     * GET /admin: si no está logueado como admin → mostrar login aquí (misma URL).
     * Si está logueado → listado de productos (sitio de administración).
     */
    public function index() {
        $isAdmin = !empty($_SESSION['user_id']) && ($_SESSION['user_rol'] ?? '') === 'admin';
        if (!$isAdmin) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $flash = $this->getFlashMessage();
                $error = ($flash && ($flash['type'] ?? '') === 'danger') ? ($flash['message'] ?? '') : null;
                $email = $_SESSION['flash_email'] ?? '';
                if (isset($_SESSION['flash_email'])) {
                    unset($_SESSION['flash_email']);
                }
                $this->showLoginForm($error, $email);
                return;
            }
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
        $products = $this->productModel->getAll();
        $flash = $this->getFlashMessage();
        $data = [
            'page_title' => 'Productos',
            'products' => $products ?: [],
            'flash_message' => $flash['message'] ?? null,
            'flash_type' => $flash['type'] ?? null,
        ];
        $this->render('admin/products/index', $data, 'admin');
    }

    /**
     * Página para ordenar productos: redirige al listado (el orden se cambia con subir/bajar en la tabla).
     * GET /admin/ordenarProductos
     */
    public function ordenarProductos() {
        $this->requireAdmin();
        $this->redirect(BASE_URL . '/admin');
    }

    /**
     * Subir producto una posición en el orden
     * GET /admin/ordenarProductosSubir/{id}
     */
    public function ordenarProductosSubir($id) {
        $this->requireAdmin();
        $id = (int) $id;
        if ($id <= 0) {
            $this->redirectWithMessage('/admin/ordenarProductos', 'ID no válido', 'danger');
            return;
        }
        if ($this->productModel->subirOrden($id)) {
            $this->redirectWithMessage('/admin', 'Orden actualizado', 'success');
        } else {
            $this->redirectWithMessage('/admin', 'No se pudo subir (quizá ya es el primero)', 'warning');
        }
    }

    /**
     * Bajar producto una posición en el orden
     * GET /admin/ordenarProductosBajar/{id}
     */
    public function ordenarProductosBajar($id) {
        $this->requireAdmin();
        $id = (int) $id;
        if ($id <= 0) {
            $this->redirectWithMessage('/admin/ordenarProductos', 'ID no válido', 'danger');
            return;
        }
        if ($this->productModel->bajarOrden($id)) {
            $this->redirectWithMessage('/admin', 'Orden actualizado', 'success');
        } else {
            $this->redirectWithMessage('/admin', 'No se pudo bajar (quizá ya es el último)', 'warning');
        }
    }

    /**
     * Intercambiar orden entre dos productos (por ID). Redirige a /admin.
     * GET /admin/ordenarProductosSwap/{id1}/{id2}
     */
    public function ordenarProductosSwap($id1, $id2) {
        $this->requireAdmin();
        $id1 = (int) $id1;
        $id2 = (int) $id2;
        if ($id1 <= 0 || $id2 <= 0 || $id1 === $id2) {
            $this->redirectWithMessage('/admin', 'IDs no válidos', 'danger');
            return;
        }
        if ($this->productModel->swapOrden($id1, $id2)) {
            $this->redirectWithMessage('/admin', 'Orden actualizado', 'success');
        } else {
            $this->redirectWithMessage('/admin', 'No se pudo cambiar el orden', 'warning');
        }
    }

    /**
     * Formulario nuevo producto
     */
    public function crear() {
        $this->requireAdmin();
        $categories = $this->productModel->getCategoriesAll();
        $flash = $this->getFlashMessage();
        $data = [
            'page_title' => 'Nuevo producto',
            'product' => null,
            'categories' => $categories ?: [],
            'flash_message' => $flash['message'] ?? null,
            'flash_type' => $flash['type'] ?? null,
            'csrf_token' => csrf_token(),
        ];
        $this->render('admin/products/form', $data, 'admin');
    }

    /**
     * Guardar nuevo producto
     */
    public function guardar() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/admin');
            return;
        }
        if (!csrf_verify()) {
            $this->redirectWithMessage('/admin/crear', 'Sesión inválida. Intenta de nuevo.', 'danger');
            return;
        }

        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
            'precio' => (float) (str_replace(',', '.', $_POST['precio'] ?? 0)),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'imagen_url' => '',
            'activo' => isset($_POST['activo']) ? 1 : 0,
            'portada' => isset($_POST['portada']) ? 1 : 0,
            'tallas_disponibles' => trim($_POST['tallas_disponibles'] ?? ''),
        ];

        if ($data['nombre'] === '') {
            $this->redirectWithMessage('/admin/crear', 'El nombre es obligatorio', 'danger');
            return;
        }
        if ($data['categoria'] === '') {
            $this->redirectWithMessage('/admin/crear', 'La categoría es obligatoria', 'danger');
            return;
        }
        if ($data['precio'] <= 0) {
            $this->redirectWithMessage('/admin/crear', 'El precio debe ser mayor que 0', 'danger');
            return;
        }

        try {
            $id = $this->productModel->create($data);
            if ($id) {
                $uploaded = $this->uploadProductImage('imagen', $id);
                if ($uploaded !== '') {
                    $this->productModel->update($id, array_merge($data, ['imagen_url' => $uploaded]));
                }
                $this->redirectWithMessage('/admin', 'Producto creado correctamente', 'success');
            } else {
                $this->redirectWithMessage('/admin/crear', 'Error al crear el producto', 'danger');
            }
        } catch (Exception $e) {
            $this->redirectWithMessage('/admin/crear', 'Error: ' . $e->getMessage(), 'danger');
        }
    }

    /**
     * Formulario editar producto
     */
    public function editar($id) {
        $this->requireAdmin();
        $id = (int) $id;
        if ($id <= 0) {
            $this->redirectWithMessage('/admin', 'ID no válido', 'danger');
            return;
        }
        $product = $this->productModel->getById($id);
        if (!$product) {
            $this->redirectWithMessage('/admin', 'Producto no encontrado', 'danger');
            return;
        }
        $categories = $this->productModel->getCategoriesAll();
        $flash = $this->getFlashMessage();
        $data = [
            'page_title' => 'Editar producto',
            'product' => $product,
            'categories' => $categories ?: [],
            'flash_message' => $flash['message'] ?? null,
            'flash_type' => $flash['type'] ?? null,
            'csrf_token' => csrf_token(),
        ];
        $this->render('admin/products/form', $data, 'admin');
    }

    /**
     * Actualizar producto
     */
    public function actualizar($id) {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/admin');
            return;
        }
        if (!csrf_verify()) {
            $this->redirectWithMessage('/admin/editar/' . (int)$id, 'Sesión inválida. Intenta de nuevo.', 'danger');
            return;
        }
        $id = (int) $id;
        if ($id <= 0) {
            $this->redirectWithMessage('/admin', 'ID no válido', 'danger');
            return;
        }

        $product = $this->productModel->getById($id);
        if (!$product) {
            $this->redirectWithMessage('/admin', 'Producto no encontrado', 'danger');
            return;
        }

        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? ''),
            'precio' => (float) (str_replace(',', '.', $_POST['precio'] ?? 0)),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'imagen_url' => $product['imagen_url'] ?? '',
            'activo' => isset($_POST['activo']) ? 1 : 0,
            'portada' => isset($_POST['portada']) ? 1 : 0,
            'tallas_disponibles' => trim($_POST['tallas_disponibles'] ?? ''),
        ];

        if ($data['nombre'] === '') {
            $this->redirectWithMessage('/admin/editar/' . $id, 'El nombre es obligatorio', 'danger');
            return;
        }
        if ($data['categoria'] === '') {
            $this->redirectWithMessage('/admin/editar/' . $id, 'La categoría es obligatoria', 'danger');
            return;
        }
        if ($data['precio'] <= 0) {
            $this->redirectWithMessage('/admin/editar/' . $id, 'El precio debe ser mayor que 0', 'danger');
            return;
        }

        $uploaded = $this->uploadProductImage('imagen', $id);
        if ($uploaded !== '') {
            $this->deleteProductImageFile($product['imagen_url'] ?? '');
            $data['imagen_url'] = $uploaded;
        }

        try {
            $result = $this->productModel->update($id, $data);
            if ($result) {
                $this->redirectWithMessage('/admin', 'Producto actualizado correctamente', 'success');
            } else {
                $this->redirectWithMessage('/admin/editar/' . $id, 'No se pudo actualizar', 'danger');
            }
        } catch (Exception $e) {
            $this->redirectWithMessage('/admin/editar/' . $id, 'Error: ' . $e->getMessage(), 'danger');
        }
    }

    /**
     * Formulario de contenido del sitio (navbar, footer, meta, home)
     * GET: mostrar formulario; POST a /admin/contenido/guardar: guardar
     */
    public function contenido($action = null) {
        $this->requireAdmin();

        if ($action === 'guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!csrf_verify()) {
                $this->redirectWithMessage('/admin/contenido', 'Sesión inválida. Intenta de nuevo.', 'danger');
                return;
            }
            $uploadDir = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'site';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/x-icon'];
            $navbarLogoPath = '';
            $siteIconPath = '';
            $heroImagePath = '';
            $aboutImagePath = '';
            $uploadFile = function($inputKey, $destName) use ($uploadDir, $allowedTypes) {
                if (empty($_FILES[$inputKey]['tmp_name']) || $_FILES[$inputKey]['error'] !== UPLOAD_ERR_OK) return '';
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES[$inputKey]['tmp_name']);
                finfo_close($finfo);
                if (!in_array($mime, $allowedTypes, true)) return '';
                $ext = (new SplFileInfo($_FILES[$inputKey]['name']))->getExtension() ?: 'png';
                $ext = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'png';
                $dest = $uploadDir . DIRECTORY_SEPARATOR . $destName . '.' . $ext;
                return move_uploaded_file($_FILES[$inputKey]['tmp_name'], $dest) ? ('site/' . $destName . '.' . $ext) : '';
            };
            $navbarLogoPath = $uploadFile('navbar_logo', 'logo-navbar');
            $siteIconPath = $uploadFile('meta_site_icon', 'icon');
            $heroImagePath = $uploadFile('home_hero_image', 'banner');
            $aboutImagePath = $uploadFile('home_about_image', 'about');
            $keys = [
                'navbar_brand', 'navbar_logo', 'footer_brand', 'footer_description',
                'footer_contact_title', 'footer_contact_street', 'footer_contact_city', 'footer_contact_phone', 'footer_contact_email',
                'footer_schedule_title', 'footer_schedule_days', 'footer_schedule_hours', 'footer_schedule_days_2', 'footer_schedule_hours_2',
                'footer_social_facebook', 'footer_social_instagram', 'footer_social_whatsapp',
                'footer_copyright_text', 'footer_copyright_made_with',
                'meta_site_name', 'meta_site_icon', 'meta_site_description', 'meta_site_location',
                'home_hero_location', 'home_hero_title', 'home_hero_description', 'home_hero_image',
                'home_collection_title', 'home_collection_description', 'home_collection_no_products',
                'home_about_badge', 'home_about_title', 'home_about_description1', 'home_about_description2', 'home_about_image',
                'home_about_stats_years_value', 'home_about_stats_years_label',
                'home_about_stats_countrys_value', 'home_about_stats_countrys_label',
                'home_about_stats_products_value', 'home_about_stats_products_label',
            ];
            try {
                $flat = $this->siteContentModel->getAll();
            } catch (Throwable $e) {
                $flat = [];
            }
            foreach ($keys as $key) {
                if ($key === 'footer_social_whatsapp') {
                    $number = trim($_POST['footer_social_whatsapp_number'] ?? '');
                    $value = SiteContent::numberToWhatsAppUrl($number);
                } elseif ($key === 'navbar_logo') {
                    $value = $navbarLogoPath !== '' ? $navbarLogoPath : ($flat[$key] ?? '');
                } elseif ($key === 'meta_site_icon') {
                    $value = $siteIconPath !== '' ? $siteIconPath : ($flat[$key] ?? '');
                } elseif ($key === 'home_hero_image') {
                    $value = $heroImagePath !== '' ? $heroImagePath : ($flat[$key] ?? '');
                } elseif ($key === 'home_about_image') {
                    $value = $aboutImagePath !== '' ? $aboutImagePath : ($flat[$key] ?? '');
                } else {
                    $value = trim($_POST[$key] ?? '');
                }
                $this->siteContentModel->set($key, $value);
            }
            $this->redirectWithMessage('/admin/contenido', 'Contenido guardado correctamente', 'success');
            return;
        }

        try {
            $flat = $this->siteContentModel->getAll();
        } catch (Throwable $e) {
            $flat = [];
        }
        if (empty($flat)) {
            $flat = $this->getDefaultContentFlat();
        }
        $whatsappUrl = $flat['footer_social_whatsapp'] ?? '';
        $flash = $this->getFlashMessage();
        $data = [
            'page_title' => 'Contenido del sitio',
            'content' => $flat,
            'whatsapp_number' => SiteContent::whatsappUrlToNumber($whatsappUrl),
            'flash_message' => $flash['message'] ?? null,
            'flash_type' => $flash['type'] ?? null,
            'csrf_token' => csrf_token(),
        ];
        $this->render('admin/contenido', $data, 'admin');
    }

    /**
     * Valores por defecto desde content_data.php (si la tabla está vacía)
     */
    private function getDefaultContentFlat() {
        $file = APP_PATH . '/content_data.php';
        if (!file_exists($file)) {
            return [];
        }
        $data = require $file;
        $flat = [];
        if (!empty($data['navbar'])) {
            $flat['navbar_brand'] = $data['navbar']['brand'] ?? '';
            $flat['navbar_logo'] = $data['navbar']['logo'] ?? '';
        }
        if (!empty($data['footer'])) {
            $f = $data['footer'];
            $flat['footer_brand'] = $f['brand'] ?? '';
            $flat['footer_description'] = $f['description'] ?? '';
            $flat['footer_contact_title'] = $f['contact']['title'] ?? '';
            $flat['footer_contact_street'] = $f['contact']['address']['street'] ?? '';
            $flat['footer_contact_city'] = $f['contact']['address']['city'] ?? '';
            $flat['footer_contact_phone'] = $f['contact']['phone'] ?? '';
            $flat['footer_contact_email'] = $f['contact']['email'] ?? '';
            $flat['footer_schedule_title'] = $f['schedule']['title'] ?? '';
            $flat['footer_schedule_days'] = $f['schedule']['weekdays']['days'] ?? '';
            $flat['footer_schedule_hours'] = $f['schedule']['weekdays']['hours'] ?? '';
            $flat['footer_schedule_days_2'] = $f['schedule']['extra']['days'] ?? '';
            $flat['footer_schedule_hours_2'] = $f['schedule']['extra']['hours'] ?? '';
            $flat['footer_social_facebook'] = $f['social']['facebook'] ?? '';
            $flat['footer_social_instagram'] = $f['social']['instagram'] ?? '';
            $flat['footer_social_whatsapp'] = $f['social']['whatsapp'] ?? '';
            $flat['footer_copyright_text'] = $f['copyright']['text'] ?? '';
            $flat['footer_copyright_made_with'] = $f['copyright']['made_with'] ?? '';
        }
        if (!empty($data['meta']['site'])) {
            $s = $data['meta']['site'];
            $flat['meta_site_name'] = $s['name'] ?? '';
            $flat['meta_site_icon'] = $s['icon'] ?? '';
            $flat['meta_site_description'] = $s['description'] ?? '';
            $flat['meta_site_location'] = $s['location'] ?? '';
        }
        if (!empty($data['home'])) {
            $h = $data['home'];
            $flat['home_hero_location'] = $h['hero']['location'] ?? '';
            $flat['home_hero_title'] = $h['hero']['title'] ?? '';
            $flat['home_hero_description'] = $h['hero']['description'] ?? '';
            $flat['home_hero_image'] = $h['hero']['image'] ?? '';
            $flat['home_collection_title'] = $h['collection']['title'] ?? '';
            $flat['home_collection_description'] = $h['collection']['description'] ?? '';
            $flat['home_collection_no_products'] = $h['collection']['no_products'] ?? '';
            $flat['home_about_badge'] = $h['about']['badge'] ?? '';
            $flat['home_about_title'] = $h['about']['title'] ?? '';
            $flat['home_about_description1'] = $h['about']['description1'] ?? '';
            $flat['home_about_description2'] = $h['about']['description2'] ?? '';
            $flat['home_about_image'] = $h['about']['image'] ?? '';
            $flat['home_about_stats_years_value'] = $h['about']['stats']['years']['value'] ?? '';
            $flat['home_about_stats_years_label'] = $h['about']['stats']['years']['label'] ?? '';
            $flat['home_about_stats_countrys_value'] = $h['about']['stats']['countrys']['value'] ?? '';
            $flat['home_about_stats_countrys_label'] = $h['about']['stats']['countrys']['label'] ?? '';
            $flat['home_about_stats_products_value'] = $h['about']['stats']['products']['value'] ?? '';
            $flat['home_about_stats_products_label'] = $h['about']['stats']['products']['label'] ?? '';
        }
        return $flat;
    }

    /**
     * Eliminar producto (borrado físico: se borra de la base de datos y su imagen)
     */
    public function eliminar($id) {
        $this->requireAdmin();
        $id = (int) $id;
        if ($id <= 0) {
            $this->redirectWithMessage('/admin', 'ID no válido', 'danger');
            return;
        }
        $product = $this->productModel->getById($id);
        if (!$product) {
            $this->redirectWithMessage('/admin', 'Producto no encontrado', 'danger');
            return;
        }
        $this->deleteProductImageFile($product['imagen_url'] ?? '');
        try {
            $this->productModel->deletePermanent($id);
            $this->redirectWithMessage('/admin', 'Producto eliminado correctamente', 'success');
        } catch (Exception $e) {
            $this->redirectWithMessage('/admin', 'Error: ' . $e->getMessage(), 'danger');
        }
    }
}
