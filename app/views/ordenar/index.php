<?php
$selectedProduct = $selectedProduct ?? null;
$selectedProductId = $selectedProductId ?? null;
$allProducts = $allProducts ?? [];
$footerContent = loadContent('footer');
$whatsappUrl = getContent($footerContent, 'social.whatsapp', '');
$whatsappNumber = class_exists('SiteContent') ? SiteContent::whatsappUrlToNumber($whatsappUrl) : '';
?>
<div class="js-page-zone d-none" data-page="ordenar"></div>

<style>
    /* Solo estilos que no se pueden reemplazar con Bootstrap - Colores personalizados */
    body.ordenar-page {
        font-family: 'Inter', sans-serif !important;
    }
    
    /* Ocultar elementos del home si existen */
    body.ordenar-page .hero,
    body.ordenar-page .order-section,
    body.ordenar-page .about-section {
        display: none !important;
    }
    
    .product-preview-image {
        width: 100%;
        max-width: 400px;
        height: 400px;
        object-fit: cover;
    }
</style>

<div class="mt-5 pt-5" style="min-height: 70vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Encabezado -->
                <div class="row mb-5">
                    <div class="col-12 text-center">
                        <p class="small text-uppercase text-success mb-2">Haz tu Pedido</p>
                        <h2 class="display-4 fw-normal mb-3">Solicita tu Producto</h2>
                        <p class="text-muted mx-auto" style="max-width: 600px;">
                            Completa el formulario y nos pondremos en contacto contigo para confirmar tu pedido. Realizamos envíos a todo México y al extranjero.
                        </p>
                    </div>
                </div>
                
                <div class="row g-4 align-items-stretch">
                    <!-- Vista previa del producto (si hay uno seleccionado) -->
                    <?php if ($selectedProduct): ?>
                        <div class="col-lg-4 d-flex">
                            <div class="card shadow-sm p-4 bg-light h-100 w-100">
<img src="<?php echo htmlspecialchars(productImageUrl($selectedProduct['imagen_url'] ?? '')); ?>"
                                     alt="<?php echo htmlspecialchars($selectedProduct['nombre']); ?>"
                                     class="product-preview-image rounded shadow mb-3">
                                <div class="small text-uppercase text-muted mb-2"><?php echo htmlspecialchars($selectedProduct['categoria']); ?></div>
                                <h3 class="h3 mb-2"><?php echo htmlspecialchars($selectedProduct['nombre']); ?></h3>
                                <p class="h4 fw-semibold text-success mb-3">$<?php echo number_format($selectedProduct['precio'], 2); ?> MXN</p>
                                <?php if (!empty($selectedProduct['descripcion'])): ?>
                                    <p class="text-muted small mb-0">
                                        <?php echo htmlspecialchars(mb_substr($selectedProduct['descripcion'], 0, 100)); ?>
                                        <?php if (mb_strlen($selectedProduct['descripcion']) > 100): ?>...<?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Formulario -->
                    <div class="<?php echo $selectedProduct ? 'col-lg-8' : 'col-lg-12'; ?> d-flex">
                        <div class="card shadow-sm p-4 p-lg-5 bg-light h-100 w-100">
                            <form id="orderForm" data-whatsapp-number="<?php echo htmlspecialchars($whatsappNumber); ?>" onsubmit="return enviarWhatsApp(event)">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre Completo *</label>
                                        <input type="text" class="form-control" id="inputName" name="name" placeholder="Ej: Juan Pérez" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Correo Electrónico *</label>
                                        <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Ej: ejemplo@gmail.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono *</label>
                                        <input type="tel" class="form-control" id="inputPhone" name="phone" placeholder="Ej: 954 123 4567" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Selecciona un Producto *</label>
                                        <select class="form-select" name="product" id="productSelect" required>
                                            <option value="" <?php echo !$selectedProductId ? 'selected disabled' : ''; ?>>Elige un producto...</option>
                                            <?php if (!empty($allProducts)): ?>
                                                <?php foreach ($allProducts as $prod): ?>
                                                    <option value="<?php echo $prod['id']; ?>" 
                                                            data-nombre="<?php echo htmlspecialchars($prod['nombre']); ?>"
                                                            data-precio="<?php echo number_format($prod['precio'], 2); ?>"
                                                            <?php echo ($selectedProductId && $prod['id'] == $selectedProductId) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($prod['nombre']); ?> - $<?php echo number_format($prod['precio'], 2); ?> MXN
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Talla</label>
                                        <select class="form-select" id="inputSize" name="size">
                                            <option value="" selected disabled>Selecciona una talla</option>
                                            <?php if ($selectedProduct && !empty($selectedProduct['tallas_disponibles'])): 
                                                $tallas = explode(',', $selectedProduct['tallas_disponibles']);
                                                foreach ($tallas as $talla): 
                                                    $talla = trim($talla);
                                                    if (!empty($talla)):
                                            ?>
                                                <option value="<?php echo htmlspecialchars($talla); ?>"><?php echo htmlspecialchars($talla); ?></option>
                                            <?php 
                                                    endif;
                                                endforeach;
                                            endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Estado / Ciudad</label>
                                        <input type="text" class="form-control" id="inputCity" name="city" placeholder="Ej: CDMX, Guadalajara">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notas Adicionales</label>
                                        <textarea class="form-control" id="inputMessage" name="message" rows="3" placeholder="Comentarios, instrucciones especiales, etc."></textarea>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <div class="d-flex flex-column flex-sm-row gap-2">
                                            <button type="submit" class="btn btn-success flex-sm-fill">
                                                <i class="bi bi-whatsapp me-2"></i>Solicitar Pedido
                                            </button>
                                            <a href="<?php echo $selectedProduct ? BASE_URL . '/producto/' . $selectedProduct['id'] : BASE_URL; ?>" class="btn btn-outline-secondary flex-sm-fill">
                                                <i class="bi bi-arrow-left me-2"></i>Volver
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Información adicional -->
                <div class="row m-5">
                    <div class="col-12">
                        <div class="text-center text-muted">
                            <p class="mb-2"><i class="bi bi-check-circle me-2"></i> Envío seguro a todo México</p>
                            <p class="mb-2"><i class="bi bi-check-circle me-2"></i> Pago contra entrega disponible</p>
                            <p class="mb-0"><i class="bi bi-check-circle me-2"></i> Garantía de autenticidad</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
