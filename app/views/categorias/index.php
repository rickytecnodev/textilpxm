<?php
/**
 * Vista de categorías con sistema de tabs
 * Muestra productos agrupados por categorías con navegación por tabs
 */

/**
 * Helper function para renderizar una tarjeta de producto
 */
if (!function_exists('renderProductCard')) {
    function renderProductCard($product) {
        $imageUrl = productImageUrl($product['imagen_url'] ?? '');
        
        $productUrl = BASE_URL . '/producto/' . (int)$product['id'];
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100" style="cursor: pointer;" onclick="window.location.href='<?php echo $productUrl; ?>'">
<img src="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>"
                     alt="<?php echo htmlspecialchars($product['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                     class="card-img-top"
                     style="height: 320px; object-fit: cover;"
                     loading="lazy">
                <div class="card-body">
                    <span class="small text-uppercase text-muted d-block mb-2"><?php echo htmlspecialchars($product['categoria'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <h3 class="h5 mb-2"><?php echo htmlspecialchars($product['nombre'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="fw-semibold text-success mb-2">$<?php echo number_format((float)$product['precio'], 2, '.', ','); ?> MXN</p>
                    <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                        <small class="text-muted">Stock: <?php echo (int)$product['stock']; ?></small>
                    <?php else: ?>
                        <small class="text-danger">Agotado</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
}

// Validar que las variables existen
if (!isset($categoryNames)) {
    $categoryNames = [];
}
if (!isset($productsByCategory)) {
    $productsByCategory = [];
}

// Verificar que tenemos datos
$hasCategories = !empty($categoryNames) && !empty($productsByCategory);
$selectedCategory = $selectedCategory ?? null;
?>
<div class="js-page-zone d-none" data-page="categorias"></div>
<style>
    /* Solo estilos que no se pueden reemplazar con Bootstrap - Colores personalizados */
    body.categorias-page {
        font-family: 'Inter', sans-serif !important;
    }
    
    /* Ocultar elementos del home si existen */
    body.categorias-page .hero,
    body.categorias-page .order-section,
    body.categorias-page .about-section {
        display: none !important;
    }
    
    /* Tabs personalizados con colores específicos del diseño */
    .category-tab:hover {
        color: #4a6741 !important;
        border-bottom-color: #4a6741 !important;
        background: rgba(74, 103, 65, 0.05) !important;
    }
    
    .category-tab.active {
        color: #4a6741 !important;
        border-bottom-color: #4a6741 !important;
    }
</style>

<!-- Vista de Categorías - Contenido Único -->
<div id="categorias-page-content" class="mt-5 pt-5" style="min-height: 60vh;">
    <div class="container">
        <!-- Encabezado -->
        <div class="row mb-2">
            <div class="col-12 text-center">
                <?php if (!empty($searchTerm)): ?>
                    <p class="small text-uppercase text-success mb-2">Resultados de Búsqueda</p>
                    <?php if (isset($totalProducts)): ?>
                        <div class="d-flex justify-content-center">
                            <strong class="text-muted">
                                <?php echo (int)$totalProducts; ?> <?php echo ($totalProducts == 1) ? 'producto encontrado' : 'productos encontrados'; ?>
                            </strong>
                        </div>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/categorias" class="btn btn-outline-secondary btn-sm mt-2 mb-2">
                        <i class="bi bi-arrow-left me-2"></i>Ver todas las categorías
                    </a>
                <?php else: ?>
                    <p class="small text-uppercase text-success mb-2">Nuestra Colección Completa</p>
                    <h2 class="display-4 fw-normal mb-3">Explora por Categorías</h2>
                    <?php if (isset($totalProducts)): ?>
                        <p class="text-muted"><?php echo (int)$totalProducts; ?> <?php echo ($totalProducts == 1) ? 'producto disponible' : 'productos disponibles'; ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($hasCategories && !empty($categoryNames) && empty($searchTerm)): ?>
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-5 pb-0 border-bottom border-2">
                    <button type="button" 
                            class="category-tab btn btn-link text-decoration-none px-4 py-3 border-bottom border-3 <?php echo empty($selectedCategory) ? 'active fw-semibold' : 'border-transparent'; ?>" 
                            data-category="all"
                            aria-label="Ver todas las categorías">
                        Todas
                    </button>
                <?php foreach ($categoryNames as $categoryName): 
                    $categoryId = preg_replace('/[^a-zA-Z0-9]/', '-', strtolower($categoryName));
                ?>
                    <button type="button" 
                            class="category-tab btn btn-link text-decoration-none px-4 py-3 text-capitalize border-bottom border-3 <?php echo ($selectedCategory === $categoryName) ? 'active fw-semibold' : 'border-transparent'; ?>" 
                            data-category="<?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>"
                            data-category-id="category-<?php echo $categoryId; ?>"
                            aria-label="Ver productos de <?php echo htmlspecialchars($categoryName); ?>">
                        <?php echo htmlspecialchars($categoryName); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php elseif (!empty($searchTerm) && !$hasCategories): ?>
            <div class="alert alert-info text-center">
                <p class="mb-0">
                    No se encontraron productos que coincidan con "<?php echo htmlspecialchars($searchTerm); ?>".
                </p>
            </div>
        <?php elseif (empty($searchTerm) && !$hasCategories): ?>
            <div class="alert alert-warning text-center">
                <p class="mb-0">
                    No se encontraron categorías. 
                </p>
            </div>
        <?php endif; ?>
        
        <!-- Contenido de categorías -->
        <?php if ($hasCategories): ?>
            <?php if (!empty($searchTerm)): ?>
                <!-- Resultados de búsqueda: mostrar todos los productos en una sola lista -->
                <div class="row g-4 mb-5">
                    <?php foreach ($productsByCategory as $categoria => $productosCategoria): ?>
                        <?php foreach ($productosCategoria as $product): ?>
                            <?php renderProductCard($product); ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Vista "Todas las categorías" normal -->
                <div id="category-all" 
                     class="category-content mb-5 <?php echo empty($selectedCategory) ? 'd-block' : 'd-none'; ?>">
                    <?php foreach ($productsByCategory as $categoria => $productosCategoria): ?>
                        <?php 
                        // Asegurar que $categoria sea un string válido
                        $categoriaNombre = is_string($categoria) ? trim($categoria) : '';
                        if (empty($categoriaNombre)) continue;
                        ?>
                        <div class="pb-4">
                            <h3 class="display-5 fw-normal mb-4 pb-2 border-bottom border-success border-2">
                                <?php echo htmlspecialchars($categoriaNombre); ?>
                            </h3>
                            <div class="row g-4">
                                <?php foreach ($productosCategoria as $product): ?>
                                    <?php renderProductCard($product); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Vistas individuales por categoría (solo cuando no hay búsqueda) -->
            <?php if (empty($searchTerm)): ?>
                <?php foreach ($productsByCategory as $categoria => $productosCategoria): 
                    $categoryId = preg_replace('/[^a-zA-Z0-9]/', '-', strtolower($categoria));
                ?>
                    <div id="category-<?php echo $categoryId; ?>" 
                         class="category-content <?php echo ($selectedCategory === $categoria) ? 'd-block' : 'd-none'; ?>"
                         data-category-name="<?php echo htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="row g-4 mb-5">
                            <?php foreach ($productosCategoria as $product): ?>
                                <?php renderProductCard($product); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php elseif (empty($searchTerm) && !$hasCategories): ?>
            <!-- Solo mostrar este mensaje si no hay búsqueda activa -->
            <div class="alert alert-info text-center">
                <p class="mb-0">No hay productos disponibles en este momento.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
