<?php
$admin_page_title = $page_title ?? 'Admin';
$admin_user_name = $_SESSION['user_name'] ?? '';
$admin_is_logged = !empty($_SESSION['user_id']) && ($_SESSION['user_rol'] ?? '') === 'admin';
$metaContent = loadContent('meta');
$admin_site_name = getContent($metaContent, 'site.name', 'TextilPXM');
$navbarContent = loadContent('navbar');
$admin_site_name_navbar = getContent($navbarContent, 'brand', 'OAXACA TEXTILES'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo htmlspecialchars($admin_page_title); ?> · <?php echo htmlspecialchars($admin_site_name); ?> Admin</title>
    <?php $adminSiteIcon = trim(getContent($metaContent, 'site.icon', '')); $adminFavicon = $adminSiteIcon !== '' ? ASSETS_URL . '/images/' . htmlspecialchars($adminSiteIcon) : ASSETS_URL . '/images/icon.ico'; ?>
    <link rel="icon" type="image/x-icon" href="<?php echo $adminFavicon; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>/css/admin.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100" data-base-url="<?php echo htmlspecialchars(BASE_URL); ?>">
    <?php if ($admin_is_logged): ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom shadow-sm fixed-top py-3">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>/admin" class="navbar-brand fw-bold d-flex align-items-center gap-2">
                <?php
                $adminNavLogo = trim(getContent($navbarContent, 'logo', ''));
                $adminNavImgSrc = $adminNavLogo !== '' ? ASSETS_URL . '/images/' . htmlspecialchars($adminNavLogo) : ($adminSiteIcon !== '' ? ASSETS_URL . '/images/' . htmlspecialchars($adminSiteIcon) : ASSETS_URL . '/images/icon.png');
                ?>
                <img src="<?php echo $adminNavImgSrc; ?>" alt="" width="32" height="32" class="d-inline-block">
                <?php echo htmlspecialchars($admin_site_name_navbar); ?> Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>"><i class="bi bi-house-door me-1"></i>Página principal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin"><i class="bi bi-box-seam me-1"></i>Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/crear"><i class="bi bi-plus-lg me-1"></i>Nuevo producto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/contenido"><i class="bi bi-pencil-square me-1"></i>Contenido del sitio</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-dark small"><?php echo htmlspecialchars($admin_user_name); ?></span>
                    <a href="<?php echo BASE_URL; ?>/admin/logout" class="btn btn-dark btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Cerrar sesión</a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <main class="container">
        <?php echo $content; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/admin.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/admin-products.js"></script>
    <script src="<?php echo ASSETS_URL; ?>/js/admin-form.js"></script>
</body>
</html>
