<?php
/**
 * Vista de detalles del producto
 * Muestra información completa de un producto individual
 */

// Verificar que el producto existe
if (empty($product) || !isset($product['id'])) {
    // Si no hay producto, redirigir al home
    header('Location: ' . BASE_URL);
    exit;
}

?>
<div class="js-page-zone d-none" data-page="product-detail"></div>
<style>
    /* Solo estilos que no se pueden reemplazar con Bootstrap - Colores personalizados */
    body.product-detail-page {
        font-family: 'Inter', sans-serif !important;
    }
    
    /* Ocultar elementos del home si existen */
    body.product-detail-page .hero,
    body.product-detail-page .order-section,
    body.product-detail-page .about-section {
        display: none !important;
    }
    
    .product-detail-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
    }
    
    .product-detail-image-container {
        position: relative;
        overflow: hidden;
    }
    
    .product-detail-info p {
        line-height: 1.8;
    }
</style>

<div class="mt-5 pt-5" style="min-height: 70vh;">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="bg-transparent p-0">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-success text-decoration-none">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/categorias" class="text-success text-decoration-none">Categorías</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/categorias?cat=<?php echo urlencode($product['categoria']); ?>" class="text-success text-decoration-none"><?php echo htmlspecialchars($product['categoria']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['nombre']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="row g-5 mb-5">
            <!-- Imagen del producto -->
            <div class="col-lg-6">
                <div class="product-detail-image-container rounded shadow">
<img src="<?php echo htmlspecialchars(productImageUrl($product['imagen_url'] ?? '')); ?>"
                         alt="<?php echo htmlspecialchars($product['nombre']); ?>"
                         class="product-detail-image rounded">
                </div>
            </div>
            
            <!-- Información del producto -->
            <div class="col-lg-6">
                <span class="small text-uppercase text-muted d-block mb-2"><?php echo htmlspecialchars($product['categoria']); ?></span>
                <h1 class="display-4 fw-normal mb-3"><?php echo htmlspecialchars($product['nombre']); ?></h1>
                <p class="display-5 fw-semibold mb-4 text-success">$<?php echo number_format($product['precio'], 2); ?> MXN</p>
                
                <div class="mb-4">
                    <?php if (!empty($product['descripcion'])): ?>
                        <p class="text-muted product-detail-info"><?php echo nl2br(htmlspecialchars($product['descripcion'])); ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Tallas disponibles -->
                <?php if (!empty($product['tallas_disponibles'])): ?>
                    <div class="my-4">
                        <h5 class="mb-3">Tallas Disponibles:</h5>
                        <?php 
                        $tallas = explode(',', $product['tallas_disponibles']);
                        foreach ($tallas as $talla): 
                            $talla = trim($talla);
                            if (!empty($talla)):
                        ?>
                            <span class="badge border border-secondary text-dark me-2 mb-2"><?php echo htmlspecialchars($talla); ?></span>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                <?php endif; ?>
                
                <!-- Stock -->
                <div class="p-3 rounded border-start border-4 <?php echo ($product['stock'] <= 0) ? 'border-danger' : 'border-success'; ?> my-4">
                    <h5 class="mb-2">
                        <?php if ($product['stock'] > 0): ?>
                            <i class="bi bi-check-circle text-success"></i> Disponible
                        <?php else: ?>
                            <i class="bi bi-x-circle text-danger"></i> Agotado
                        <?php endif; ?>
                    </h5>
                    <p class="mb-0">
                        <?php if ($product['stock'] > 0): ?>
                            Stock disponible: <strong><?php echo $product['stock']; ?></strong> unidades
                        <?php else: ?>
                            Este producto está temporalmente agotado
                        <?php endif; ?>
                    </p>
                </div>
                
                <!-- Botón de pedido -->
                <div class="">
                    <a href="<?php echo BASE_URL; ?>/ordenar?producto=<?php echo $product['id']; ?>" class="btn btn-success mt-4">
                        <i class="bi bi-cart-plus me-2"></i>Solicitar este Producto
                    </a>
                    <a href="<?php echo BASE_URL; ?>/categorias?cat=<?php echo urlencode($product['categoria']); ?>" class="btn btn-outline-secondary mt-4">
                        <i class="bi bi-arrow-left me-2"></i>Ir a <?php echo htmlspecialchars($product['categoria']); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Productos relacionados -->
        <?php if (!empty($relatedProducts)): ?>
            <div class="mt-5 pt-4 border-top mb-5">
                <h3 class="display-5 fw-normal mb-4">Productos Relacionados</h3>
                <div class="row g-4">
                    <?php foreach ($relatedProducts as $related): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="card shadow-sm h-100" style="cursor: pointer;" onclick="window.location.href='<?php echo BASE_URL; ?>/producto/<?php echo $related['id']; ?>'">
<img src="<?php echo htmlspecialchars(productImageUrl($related['imagen_url'] ?? '')); ?>"
                                     alt="<?php echo htmlspecialchars($related['nombre']); ?>"
                                     class="card-img-top"
                                     style="height: 320px; object-fit: cover;">
                                <div class="card-body">
                                    <span class="small text-uppercase text-muted d-block mb-2"><?php echo htmlspecialchars($related['categoria']); ?></span>
                                    <h3 class="h5 mb-2"><?php echo htmlspecialchars($related['nombre']); ?></h3>
                                    <p class="fw-semibold text-success mb-0">$<?php echo number_format($related['precio'], 2); ?> MXN</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
