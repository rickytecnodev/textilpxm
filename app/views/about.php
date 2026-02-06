<?php
$page_title = $page_title ?? 'Sobre Nosotros';
$homeContent = loadContent('home');
?>
<section class="py-5" style="margin-top: 6rem;">
    <div class="container">
        <h1 class="h3 fw-bold mb-3"><?php echo htmlspecialchars(getContent($homeContent, 'about.title', 'Sobre Nosotros')); ?></h1>
        <p class="lead text-muted"><?php echo htmlspecialchars(getContent($homeContent, 'about.description1', '')); ?></p>
        <p class="text-muted"><?php echo htmlspecialchars(getContent($homeContent, 'about.description2', '')); ?></p>
        <a href="<?php echo BASE_URL; ?>#nosotros" class="btn btn-dark">Ver más en la página principal</a>
    </div>
</section>
