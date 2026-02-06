<?php
$navbarContent = loadContent('navbar');
$footerContent = loadContent('footer');
$metaContent = loadContent('meta');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $page_title ?? getContent($metaContent, 'site.name', 'Oaxaca Textiles'); ?></title>
    <?php $siteIcon = trim(getContent($metaContent, 'site.icon', '')); $faviconHref = $siteIcon !== '' ? ASSETS_URL . '/images/' . htmlspecialchars($siteIcon) : ASSETS_URL . '/images/icon.ico'; ?>
    <link rel="icon" type="image/x-icon" href="<?php echo $faviconHref; ?>">
    <meta name="description" content="<?php echo htmlspecialchars(getContent($metaContent, 'site.description', '')); ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo ASSETS_URL; ?>/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top bg-light border-bottom py-3">
        <div class="container">
            <a class="navbar-brand fs-4 fw-semibold text-dark d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>">
                <?php
                $navbarLogo = trim(getContent($navbarContent, 'logo', ''));
                $navIcon = $siteIcon !== '' ? ASSETS_URL . '/images/' . htmlspecialchars($siteIcon) : ASSETS_URL . '/images/icon.png';
                if ($navbarLogo !== ''): ?>
                <img src="<?php echo ASSETS_URL; ?>/images/<?php echo htmlspecialchars($navbarLogo); ?>" alt="<?php echo htmlspecialchars(getContent($navbarContent, 'brand', '')); ?>" height="40" class="d-inline-block align-middle">
                <?php else: ?>
                <img src="<?php echo $navIcon; ?>" alt="" width="32" height="32" class="d-inline-block">
                <?php endif; ?>
                <?php echo htmlspecialchars(getContent($navbarContent, 'brand', 'OAXACA TEXTILES')); ?>
            </a>
            <button class="navbar-toggler border-2 px-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="bi bi-list fs-4 text-dark"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                
                <ul class="navbar-nav mx-auto">
                    <?php if (!empty($_SESSION['user_id']) && ($_SESSION['user_rol'] ?? '') === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-dark small" href="<?php echo BASE_URL; ?>/admin"><i class="bi bi-gear me-1"></i>Admin</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link text-dark small" href="<?php echo BASE_URL; ?>#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark small" href="<?php echo BASE_URL; ?>#coleccion">Colección</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark small" href="<?php echo BASE_URL; ?>#nosotros">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark small" href="<?php echo BASE_URL; ?>#contacto">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark small" href="<?php echo BASE_URL; ?>/ordenar">Ordenar</a>
                    </li>
                </ul>
                <form class="d-flex me-3 mb-2 mb-lg-0" role="search" action="<?php echo BASE_URL; ?>/categorias" method="GET" onsubmit="var q = this.q.value.trim(); if (!q) { this.q.value = ''; return false; } this.q.value = q;">
                    <input class="form-control form-control-sm" type="search" name="q" placeholder="Buscar..." aria-label="Buscar producto" value="<?php echo isset($_GET['q']) ? htmlspecialchars(trim($_GET['q'])) : ''; ?>" style="min-width: 150px;">
                    <button class="btn btn-outline-success btn-sm" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer id="contacto" class="bg-dark text-white pt-5 pb-2">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <p class="h4 fw-semibold mb-3"><?php echo htmlspecialchars(getContent($footerContent, 'brand', 'OAXACA TEXTILES')); ?></p>
                    <p class="small text-white-50">
                        <?php echo htmlspecialchars(getContent($footerContent, 'description', '')); ?>
                    </p>
                    <?php
                    $fb = trim(getContent($footerContent, 'social.facebook', ''));
                    $ig = trim(getContent($footerContent, 'social.instagram', ''));
                    $wa = trim(getContent($footerContent, 'social.whatsapp', ''));
                    $hasSocial = ($fb !== '' && $fb !== '#') || ($ig !== '' && $ig !== '#') || ($wa !== '' && $wa !== '#');
                    if ($hasSocial):
                    ?>
                    <div class="mt-3">
                        <?php if ($fb !== '' && $fb !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($fb); ?>" class="d-inline-flex align-items-center justify-content-center rounded-circle border border-white border-opacity-25 text-white-50 text-decoration-none me-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($ig !== '' && $ig !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($ig); ?>" class="d-inline-flex align-items-center justify-content-center rounded-circle border border-white border-opacity-25 text-white-50 text-decoration-none me-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <?php endif; ?>
                        <?php if ($wa !== '' && $wa !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($wa); ?>" class="d-inline-flex align-items-center justify-content-center rounded-circle border border-white border-opacity-25 text-white-50 text-decoration-none me-2" style="width: 40px; height: 40px;">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="h6 fw-semibold mb-4">Navegación</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>#inicio" class="text-decoration-none small text-white-50">Inicio</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>#coleccion" class="text-decoration-none small text-white-50">Colección</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>#nosotros" class="text-decoration-none small text-white-50">Nosotros</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>#ordenar" class="text-decoration-none small text-white-50">Ordenar</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="h6 fw-semibold mb-4"><?php echo htmlspecialchars(getContent($footerContent, 'contact.title', 'Contacto')); ?></h5>
                    <ul class="list-unstyled">
                        <?php if (trim(getContent($footerContent, 'contact.address.street', '')) !== ''): ?>
                        <li class="mb-2 small text-white-50"><i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars(getContent($footerContent, 'contact.address.street', '')); ?></li>
                        <?php endif; ?>
                        <?php if (trim(getContent($footerContent, 'contact.address.city', '')) !== ''): ?>
                        <li class="mb-2 small text-white-50"><?php echo htmlspecialchars(getContent($footerContent, 'contact.address.city', '')); ?></li>
                        <?php endif; ?>
                        <?php if (trim(getContent($footerContent, 'contact.phone', '')) !== ''): ?>
                        <li class="mb-2 small text-white-50"><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars(getContent($footerContent, 'contact.phone', '')); ?></li>
                        <?php endif; ?>
                        <?php if (trim(getContent($footerContent, 'contact.email', '')) !== ''): ?>
                        <li class="mb-2 small text-white-50"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars(getContent($footerContent, 'contact.email', '')); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php
                $scheduleTitle = trim(getContent($footerContent, 'schedule.title', ''));
                $scheduleDays = trim(getContent($footerContent, 'schedule.weekdays.days', ''));
                $scheduleHours = trim(getContent($footerContent, 'schedule.weekdays.hours', ''));
                $scheduleDays2 = trim(getContent($footerContent, 'schedule.extra.days', ''));
                $scheduleHours2 = trim(getContent($footerContent, 'schedule.extra.hours', ''));
                $hasSchedule = $scheduleTitle !== '' || $scheduleDays !== '' || $scheduleHours !== '' || $scheduleDays2 !== '' || $scheduleHours2 !== '';
                if ($hasSchedule):
                ?>
                <div class="col-lg-3 col-md-4">
                    <h5 class="h6 fw-semibold mb-4"><?php echo htmlspecialchars($scheduleTitle !== '' ? $scheduleTitle : 'Horario'); ?></h5>
                    <ul class="list-unstyled">
                        <?php if ($scheduleDays !== '' || $scheduleHours !== ''): ?>
                        <li class="mb-2 small text-white-50"><?php echo htmlspecialchars($scheduleDays); ?></li>
                        <li class="mb-2 small text-white-50"><?php echo htmlspecialchars($scheduleHours); ?></li>
                        <?php endif; ?>
                        <?php if ($scheduleDays2 !== '' || $scheduleHours2 !== ''): ?>
                        <li class="mb-2 small text-white-50"><?php echo htmlspecialchars($scheduleDays2); ?></li>
                        <li class="mb-2 small text-white-50"><?php echo htmlspecialchars($scheduleHours2); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="border-top border-white border-opacity-10 pt-4 mt-5 text-center">
                <p class="mb-0 small text-white-50">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(getContent($footerContent, 'copyright.text', 'Oaxaca Textiles. Todos los derechos reservados.')); ?></p>
                <p class="mt-1 small text-white-50"><?php echo htmlspecialchars(getContent($footerContent, 'copyright.made_with', 'Hecho con amor en Puerto Escondido, Oaxaca')); ?></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
</body>
</html>