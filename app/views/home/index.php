<?php
$homeContent = loadContent('home');
?>
<div class="js-page-zone d-none" data-page="home"></div>
<style>
    /* Solo estilos que no se pueden reemplazar con Bootstrap */
    body.home-page {
        font-family: 'Inter', sans-serif;
        color: #3d3529;
    }

    body.home-page h1,
    body.home-page h2,
    body.home-page h3,
    body.home-page h4,
    body.home-page h5,
    body.home-page h6 {
        font-family: 'Cormorant Garamond', serif;
        font-weight: 500;
    }

    body.home-page .hero {
        position: relative;
        min-height: 55vh;
        margin-top: 8vh;
    }

    body.home-page .hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -2;
    }

    body.home-page .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, rgba(61, 53, 41, 0.85), rgba(61, 53, 41, 0.4));
        z-index: -1;
    }

    body.home-page .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    body.home-page .product-card:hover {
        transform: translateY(-5px);
    }
</style>

<!-- Hero -->
<section id="inicio" class="hero d-flex align-items-center overflow-hidden">
    <?php $heroImage = trim(getContent($homeContent, 'hero.image', '')); $heroSrc = $heroImage !== '' ? ASSETS_URL . '/images/' . htmlspecialchars($heroImage) : ASSETS_URL . '/images/page/banner.jpg'; ?>
    <img src="<?php echo $heroSrc; ?>" alt="Banner principal" class="hero-bg">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="text-white" style="max-width: 700px;">
            <h1 class="display-2 fw-light mb-4 lh-sm mt-5"><?php echo htmlspecialchars(getContent($homeContent, 'hero.title', 'Tradición Textil Oaxaqueña')); ?></h1>
            <p class="lead mb-4 opacity-75 lh-lg">
                <?php echo htmlspecialchars(getContent($homeContent, 'hero.description', '')); ?>
            </p>
            <a href="#coleccion" class="btn btn-warning px-4 py-2 small mb-4">Explorar Colección</a>
        </div>
    </div>
</section>

<!-- Collection -->
<section id="coleccion" class="py-5 overflow-hidden">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-6">
                <p class="small text-uppercase mb-2 text-success">Nuestra Colección</p>
                <h2 class="display-4 fw-normal mb-3"><?php echo htmlspecialchars(getContent($homeContent, 'collection.title', 'Prendas Artesanales')); ?></h2>
                <p class="text-muted" style="max-width: 500px;">
                    <?php echo htmlspecialchars(getContent($homeContent, 'collection.description', '')); ?>
                </p>
            </div>
            <div class="col-lg-6 text-lg-end d-flex align-items-center justify-content-lg-end">
                <a href="<?php echo BASE_URL; ?>/categorias" class="btn btn-success px-4 py-2">
                    <i class="bi bi-grid me-2"></i>Ver Todas las Categorías
                </a>
            </div>
        </div>

        <?php if (!empty($products)): ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4 ">
                        <div class="bg-light rounded card product-card h-100 border shadow-sm cursor-pointer" onclick="window.location.href='<?php echo BASE_URL; ?>/producto/<?php echo $product['id']; ?>'">
                            <img src="<?php echo htmlspecialchars(productImageUrl($product['imagen_url'] ?? '')); ?>"
                                alt="<?php echo htmlspecialchars($product['nombre']); ?>"
                                class="card-img-top"
                                style="height: 320px; object-fit: cover;"
                                loading="lazy">
                            <div class="card-body">
                                <span class="small text-uppercase d-block mb-2 text-muted"><?php echo htmlspecialchars($product['categoria']); ?></span>
                                <h3 class="h5 mb-2"><?php echo htmlspecialchars($product['nombre']); ?></h3>
                                <p class="fw-semibold mb-2 text-success">$<?php echo number_format($product['precio'], 2); ?> MXN</p>
                                <?php if ($product['stock'] > 0): ?>
                                    <small class="text-muted">Stock: <?php echo $product['stock']; ?></small>
                                <?php else: ?>
                                    <small class="text-danger">Agotado</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <p class="mb-0"><?php echo htmlspecialchars(getContent($homeContent, 'collection.no_products', 'No hay productos disponibles en este momento.')); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- About -->
<section id="nosotros" class="py-5 overflow-hidden">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <?php $aboutImage = trim(getContent($homeContent, 'about.image', '')); $aboutSrc = $aboutImage !== '' ? ASSETS_URL . '/images/' . htmlspecialchars($aboutImage) : ASSETS_URL . '/images/page/empresarial.jpg'; ?>
                <img src="<?php echo $aboutSrc; ?>" alt="<?php echo htmlspecialchars(getContent($homeContent, 'about.title', 'Nuestra Historia')); ?>" class="img-fluid rounded" style="height: 400px; object-fit: cover;">
            </div>
            <div class="col-lg-6">
                <p class="small text-uppercase mb-2 text-success"><?php echo htmlspecialchars(getContent($homeContent, 'about.badge', 'Nuestra Historia')); ?></p>
                <h2 class="display-4 fw-normal mb-4"><?php echo htmlspecialchars(getContent($homeContent, 'about.title', 'Raíces que Visten')); ?></h2>
                <p class="mb-4 text-muted">
                    <?php echo htmlspecialchars(getContent($homeContent, 'about.description1', '')); ?>
                </p>
                <p class="mb-4 text-muted">
                    <?php echo htmlspecialchars(getContent($homeContent, 'about.description2', '')); ?>
                </p>

                <div class="row mt-5">
                    <div class="col-4 text-center">
                        <p class="display-5 fw-semibold mb-2 text-success"><?php echo htmlspecialchars(getContent($homeContent, 'about.stats.years.value', '15+')); ?></p>
                        <p class="small text-muted"><?php echo htmlspecialchars(getContent($homeContent, 'about.stats.years.label', 'Años de experiencia')); ?></p>
                    </div>
                    <div class="col-4 text-center">
                        <p class="display-5 fw-semibold mb-2 text-success"><?php echo htmlspecialchars(getContent($homeContent, 'about.stats.countrys.value', '50+')); ?></p>
                        <p class="small text-muted"><?php echo htmlspecialchars(getContent($homeContent, 'about.stats.countrys.label', 'Artesanas colaboradoras')); ?></p>
                    </div>
                    <div class="col-4 text-center">
                        <p class="display-5 fw-semibold mb-2 text-success"><?php echo htmlspecialchars(getContent($homeContent, 'about.stats.products.value', '8')); ?></p>
                        <p class="small text-muted"><?php echo htmlspecialchars(getContent($homeContent, 'about.stats.products.label', 'Comunidades')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>