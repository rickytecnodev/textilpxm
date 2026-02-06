<?php
/**
 * Controlador Home
 * Gestiona la página principal del sitio
 */

class HomeController extends Controller {
    private $userModel;
    private $productModel;
    private $page_title = 'Inicio';

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->productModel = new Product();
    }

    /**
     * Página principal - Vista Home
     */
    public function index() {
        // Obtener solo productos de portada (sin agrupar por categoría)
        $products = $this->productModel->getPortada();
        
        // Obtener categorías para el formulario
        $categories = $this->productModel->getCategories();
        
        // Obtener todos los productos activos para el formulario de pedidos
        $allProducts = $this->productModel->getActive();
        
        $metaContent = loadContent('meta');
        $data = $this->mergeFlashIntoData([
            'page_title' => getContent($metaContent, 'site.name', 'Oaxaca Textiles | Ropa Típica de Puerto Escondido'),
            'products' => $products ?: [],
            'allProducts' => $allProducts ?: [],
            'categories' => $categories ?: [],
        ]);
        $this->render('home/index', $data);
    }

    /**
     * Página de categorías con tabs
     */
    public function categorias() {
        // Obtener término de búsqueda desde GET
        $searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        // Obtener categoría seleccionada desde GET
        $categoriaSeleccionada = $this->getSelectedCategory();
        
        // Si hay búsqueda, buscar productos
        if (!empty($searchTerm)) {
            $allProducts = $this->productModel->search($searchTerm);
        } else {
            // Obtener todos los productos activos
            $allProducts = $this->productModel->getActive();
        }
        
        // Agrupar productos por categoría usando método del modelo
        $productsByCategory = $this->productModel->groupByCategory($allProducts);
        
        // Obtener lista de nombres de categorías
        $categoryNames = array_keys($productsByCategory);
        
        // Títulos: site.name desde content_data, página desde HTML
        $metaContent = loadContent('meta');
        $siteName = getContent($metaContent, 'site.name', 'Oaxaca Textiles');
        $baseTitle = 'Categorías | ' . $siteName;

        $data = $this->mergeFlashIntoData([
            'page_title' => !empty($searchTerm) ? 'Búsqueda: ' . htmlspecialchars($searchTerm) . ' | ' . $siteName : $baseTitle,
            'categoryNames' => $categoryNames,
            'productsByCategory' => $productsByCategory,
            'selectedCategory' => $categoriaSeleccionada,
            'totalProducts' => count($allProducts),
            'searchTerm' => $searchTerm,
        ]);
        $this->render('categorias/index', $data);
    }

    /**
     * Obtener la categoría seleccionada desde GET
     * @return string|null Nombre de la categoría o null
     */
    private function getSelectedCategory() {
        if (!isset($_GET['cat']) || empty($_GET['cat'])) {
            return null;
        }
        
        return trim($_GET['cat']);
    }

    /**
     * Ver detalles de un producto
     * @param int|string $id ID del producto
     */
    public function producto($id) {
        // Validar y sanitizar el ID
        $productId = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$productId || $productId <= 0) {
            $this->redirectWithMessage('/', 'ID de producto inválido', 'danger');
            return;
        }

        // Obtener el producto
        $product = $this->productModel->getById($productId);
        
        if (!$product) {
            $this->redirectWithMessage('/', 'Producto no encontrado', 'danger');
            return;
        }
        
        if (!isset($product['activo']) || !$product['activo']) {
            $this->redirectWithMessage('/', 'Este producto no está disponible', 'danger');
            return;
        }

        // Obtener productos relacionados (misma categoría, excluyendo el actual)
        $relatedProducts = $this->getRelatedProducts($product['categoria'], $productId);

        $data = $this->mergeFlashIntoData([
            'page_title' => htmlspecialchars($product['nombre']) . ' | Oaxaca Textiles',
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
        $this->render('producto/show', $data);
    }

    /**
     * Obtener productos relacionados de la misma categoría
     * @param string $categoria Nombre de la categoría
     * @param int $excludeId ID del producto a excluir
     * @param int $limit Límite de productos a retornar
     * @return array Array de productos relacionados
     */
    private function getRelatedProducts($categoria, $excludeId, $limit = 4) {
        $products = $this->productModel->getByCategory($categoria, 1);
        
        // Filtrar el producto actual
        $related = array_filter($products, function($p) use ($excludeId) {
            return isset($p['id']) && (int)$p['id'] !== (int)$excludeId;
        });
        
        // Limitar resultados
        return array_slice($related, 0, $limit);
    }

    /**
     * Página de inicio de sesión
     */
    public function login() {
        // Si ya está autenticado, redirigir al home
        if ($this->isAuthenticated()) {
            $this->redirect('/');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $this->redirectWithMessage('/login', 'Por favor completa todos los campos', 'danger');
                return;
            }

            // Autenticar usuario
            $user = $this->userModel->login($email, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_rol'] = $user['rol'];
                $this->redirectWithMessage('/', '¡Bienvenido! Has iniciado sesión correctamente', 'success');
            } else {
                $this->redirectWithMessage('/login', 'Email o contraseña incorrectos', 'danger');
            }
            return;
        }

        $this->render('login', $this->mergeFlashIntoData(['page_title' => 'Iniciar Sesión']));
    }

    /**
     * Página de registro
     */
    public function register() {
        // Si ya está autenticado, redirigir al home
        if ($this->isAuthenticated()) {
            $this->redirect('/');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? ''
            ];

            // Validar datos
            if (empty($data['nombre']) || empty($data['email']) || empty($data['password'])) {
                $this->redirectWithMessage('/register', 'Por favor completa todos los campos', 'danger');
                return;
            }

            // Verificar si el email ya existe
            $existingUser = $this->userModel->getByEmail($data['email']);
            if ($existingUser) {
                $this->redirectWithMessage('/register', 'El email ya está registrado', 'danger');
                return;
            }

            // Crear usuario
            try {
                $userId = $this->userModel->create($data);
                if ($userId) {
                    $this->redirectWithMessage('/login', '¡Cuenta creada exitosamente! Ahora puedes iniciar sesión', 'success');
                    return;
                }
            } catch (Exception $e) {
                $this->redirectWithMessage('/register', 'Error al crear la cuenta. Por favor intenta nuevamente', 'danger');
                return;
            }
        }

        $this->render('register', $this->mergeFlashIntoData(['page_title' => 'Crear Cuenta']));
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        session_unset();
        session_destroy();
        $this->redirectWithMessage('/', 'Has cerrado sesión correctamente', 'success');
    }

    /**
     * Página "Sobre Nosotros"
     */
    public function about() {
        $this->render('about', ['page_title' => 'Sobre Nosotros']);
    }

    /**
     * Página de formulario de pedido con producto pre-seleccionado
     */
    public function ordenar() {
        // Obtener ID del producto desde GET
        $productId = isset($_GET['producto']) ? (int)$_GET['producto'] : null;
        $selectedProduct = null;
        
        // Si hay un ID de producto, obtener su información
        if ($productId) {
            $selectedProduct = $this->productModel->getById($productId);
            // Si el producto no existe o no está activo, limpiar la variable
            if (!$selectedProduct || !isset($selectedProduct['activo']) || $selectedProduct['activo'] != 1) {
                $selectedProduct = null;
                $productId = null;
            }
        }
        
        // Obtener todos los productos activos para el select
        $allProducts = $this->productModel->getActive();
        
        $data = $this->mergeFlashIntoData([
            'page_title' => 'Solicitar Producto | Oaxaca Textiles',
            'selectedProduct' => $selectedProduct,
            'selectedProductId' => $productId,
            'allProducts' => $allProducts ?: [],
        ]);
        $this->render('ordenar/index', $data);
    }

    /**
     * Página de Contacto / Procesar Pedidos
     */
    public function contact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $productId = $_POST['product'] ?? '';
            $size = $_POST['size'] ?? '';
            $city = $_POST['city'] ?? '';
            $message = $_POST['message'] ?? '';

            if (empty($name) || empty($email) || empty($phone) || empty($productId)) {
                // Redirigir a /ordenar con el producto si existe
                $redirectUrl = '/ordenar';
                if ($productId && $productId !== 'otro' && is_numeric($productId)) {
                    $redirectUrl .= '?producto=' . (int)$productId;
                }
                $this->redirectWithMessage($redirectUrl, 'Por favor completa todos los campos requeridos', 'danger');
                return;
            }

            // Obtener información del producto si no es "otro"
            $productInfo = '';
            if ($productId !== 'otro' && is_numeric($productId)) {
                $product = $this->productModel->getById($productId);
                if ($product) {
                    $productInfo = $product['nombre'] . ' ($' . number_format($product['precio'], 2) . ')';
                }
            } else {
                $productInfo = 'Producto personalizado';
            }

            // Aquí podrías guardar el pedido en base de datos o enviar email
            // Por ahora solo mostramos mensaje de éxito
            $redirectUrl = '/ordenar';
            if ($productId && $productId !== 'otro' && is_numeric($productId)) {
                $redirectUrl .= '?producto=' . (int)$productId;
            }
            $this->redirectWithMessage($redirectUrl, '¡Gracias por tu pedido! Nos pondremos en contacto contigo pronto por WhatsApp o correo electrónico.', 'success');
            return;
        }

        $this->render('contact', ['page_title' => 'Contacto']);
    }
}