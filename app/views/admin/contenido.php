<?php
$c = $content ?? [];
$whatsapp_number = $whatsapp_number ?? '';
$v = function($key) use ($c) { return htmlspecialchars($c[$key] ?? '', ENT_QUOTES, 'UTF-8'); };
?>

<div class="card shadow-sm mb-4" style="margin-top: 6rem;">
    <div class="card-header bg-dark text-white">
        <h1 class="h5 mb-0">Textos y datos del sitio</h1>
    </div>

    <?php $alert_class = 'm-1'; require APP_PATH . '/views/partials/_flash_alert.php'; ?>

    <div class="card-body p-4">
        <form method="post" action="<?php echo BASE_URL; ?>/admin/contenido/guardar" enctype="multipart/form-data">
            <?php if (!empty($csrf_token)): ?>
            <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <?php endif; ?>
            <ul class="nav nav-tabs mb-3" id="contenidoTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-3-btn" data-bs-toggle="tab" data-bs-target="#tab-3" type="button" role="tab" aria-selected="true">Título y descripción del sitio (pestaña y buscadores)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-1-btn" data-bs-toggle="tab" data-bs-target="#tab-1" type="button" role="tab">Encabezado</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-2-btn" data-bs-toggle="tab" data-bs-target="#tab-2" type="button" role="tab">Quiénes somos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-4-btn" data-bs-toggle="tab" data-bs-target="#tab-4" type="button" role="tab">Pie de página</button>
                </li>
            </ul>
            <div class="tab-content" id="contenidoTabsContent">
                <div class="tab-pane fade" id="tab-1" role="tabpanel">
                <div class="mb-3">
                    <label for="navbar_logo" class="form-label">Logo del encabezado (imagen)</label>
                    <?php
                    $navbar_logo = $c['navbar_logo'] ?? '';
                    if ($navbar_logo !== '' && preg_match('/^site\/[a-zA-Z0-9_.-]+$/', $navbar_logo)):
                    ?>
                    <div class="mb-2">
                        <img src="<?php echo ASSETS_URL; ?>/images/<?php echo htmlspecialchars($navbar_logo); ?>" alt="Logo actual" class="img-thumbnail" style="max-height: 60px;">
                        <p class="small text-muted mb-0">Imagen actual. Sube otra para reemplazarla.</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="navbar_logo" name="navbar_logo" accept="image/jpeg,image/png,image/webp,image/gif">
                    <small class="text-muted">Reemplaza el icono del encabezado. Formatos: JPG, PNG, WebP, GIF.</small>
                </div>
                <div class="mb-3">
                    <label for="navbar_brand" class="form-label">Nombre de la marca (texto, si no usas logo)</label>
                    <input type="text" class="form-control" id="navbar_brand" name="navbar_brand" value="<?php echo $v('navbar_brand'); ?>">
                </div>
                <h4 class="text-muted">Banner principal (lo primero que ve el visitante)</h4>
                <div class="mb-3">
                    <label for="home_hero_image" class="form-label">Imagen del banner</label>
                    <?php $hero_img = $c['home_hero_image'] ?? ''; if ($hero_img !== '' && preg_match('/^site\/[a-zA-Z0-9_.-]+$/', $hero_img)): ?>
                    <div class="mb-2"><img src="<?php echo ASSETS_URL; ?>/images/<?php echo htmlspecialchars($hero_img); ?>" alt="Banner actual" class="img-thumbnail" style="max-height: 80px;"></div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="home_hero_image" name="home_hero_image" accept="image/jpeg,image/png,image/webp,image/gif">
                    <small class="text-muted">Se muestra en la portada. JPG, PNG, WebP, GIF.</small>
                </div>
                <div class="mb-3">
                    <label for="home_hero_location" class="form-label">Lugar que se muestra</label>
                    <input type="text" class="form-control" id="home_hero_location" name="home_hero_location" value="<?php echo $v('home_hero_location'); ?>">
                </div>
                <div class="mb-3">
                    <label for="home_hero_title" class="form-label">Título principal del banner</label>
                    <input type="text" class="form-control" id="home_hero_title" name="home_hero_title" value="<?php echo $v('home_hero_title'); ?>">
                </div>
                <div class="mb-3">
                    <label for="home_hero_description" class="form-label">Texto descriptivo del banner</label>
                    <textarea class="form-control" id="home_hero_description" name="home_hero_description" rows="2"><?php echo $v('home_hero_description'); ?></textarea>
                </div>
                <h4 class="text-muted mt-3">Sección de productos</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="home_collection_title" class="form-label">Título de la sección de productos</label>
                        <input type="text" class="form-control" id="home_collection_title" name="home_collection_title" value="<?php echo $v('home_collection_title'); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="home_collection_description" class="form-label">Descripción de la sección de productos</label>
                    <textarea class="form-control" id="home_collection_description" name="home_collection_description" rows="2"><?php echo $v('home_collection_description'); ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="home_collection_no_products" class="form-label">Mensaje cuando no hay productos</label>
                        <input type="text" class="form-control" id="home_collection_no_products" name="home_collection_no_products" value="<?php echo $v('home_collection_no_products'); ?>">
                    </div>
                </div>
                </div>

                <div class="tab-pane fade" id="tab-2" role="tabpanel">
                <h4 class="text-muted mt-3">Sección «Nosotros» o «Quiénes somos»</h4>
                <div class="mb-3">
                    <label for="home_about_image" class="form-label">Imagen de Nuestra Historia</label>
                    <?php $about_img = $c['home_about_image'] ?? ''; if ($about_img !== '' && preg_match('/^site\/[a-zA-Z0-9_.-]+$/', $about_img)): ?>
                    <div class="mb-2"><img src="<?php echo ASSETS_URL; ?>/images/<?php echo htmlspecialchars($about_img); ?>" alt="Imagen actual" class="img-thumbnail" style="max-height: 80px;"></div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="home_about_image" name="home_about_image" accept="image/jpeg,image/png,image/webp,image/gif">
                    <small class="text-muted">Imagen que acompaña el texto. JPG, PNG, WebP, GIF.</small>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="home_about_badge" class="form-label">Texto pequeño arriba (ej. «Nuestra historia»)</label>
                        <input type="text" class="form-control" id="home_about_badge" name="home_about_badge" value="<?php echo $v('home_about_badge'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="home_about_title" class="form-label">Título de la sección</label>
                        <input type="text" class="form-control" id="home_about_title" name="home_about_title" value="<?php echo $v('home_about_title'); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="home_about_description1" class="form-label">Primer párrafo</label>
                    <textarea class="form-control" id="home_about_description1" name="home_about_description1" rows="2"><?php echo $v('home_about_description1'); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="home_about_description2" class="form-label">Segundo párrafo</label>
                    <textarea class="form-control" id="home_about_description2" name="home_about_description2" rows="2"><?php echo $v('home_about_description2'); ?></textarea>
                </div>
                <h4 class="text-muted mt-2">Números o datos destacados (ej. años de experiencia, países)</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="home_about_stats_years_value" class="form-label">Primer número (ej. 15+)</label>
                        <input type="text" class="form-control" id="home_about_stats_years_value" name="home_about_stats_years_value" value="<?php echo $v('home_about_stats_years_value'); ?>" placeholder="15+">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="home_about_stats_years_label" class="form-label">Texto debajo del primer número</label>
                        <input type="text" class="form-control" id="home_about_stats_years_label" name="home_about_stats_years_label" value="<?php echo $v('home_about_stats_years_label'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="home_about_stats_countrys_value" class="form-label">Segundo número</label>
                        <input type="text" class="form-control" id="home_about_stats_countrys_value" name="home_about_stats_countrys_value" value="<?php echo $v('home_about_stats_countrys_value'); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="home_about_stats_countrys_label" class="form-label">Texto debajo del segundo número</label>
                        <input type="text" class="form-control" id="home_about_stats_countrys_label" name="home_about_stats_countrys_label" value="<?php echo $v('home_about_stats_countrys_label'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="home_about_stats_products_value" class="form-label">Tercer número</label>
                        <input type="text" class="form-control" id="home_about_stats_products_value" name="home_about_stats_products_value" value="<?php echo $v('home_about_stats_products_value'); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="home_about_stats_products_label" class="form-label">Texto debajo del tercer número</label>
                        <input type="text" class="form-control" id="home_about_stats_products_label" name="home_about_stats_products_label" value="<?php echo $v('home_about_stats_products_label'); ?>">
                    </div>
                </div>
                </div>

                <div class="tab-pane fade show active" id="tab-3" role="tabpanel">
                <div class="mb-3">
                    <label for="meta_site_icon" class="form-label">Icono del sitio (favicon y logo en navegación)</label>
                    <?php $site_icon = $c['meta_site_icon'] ?? ''; if ($site_icon !== '' && preg_match('/^site\/[a-zA-Z0-9_.-]+$/', $site_icon)): ?>
                    <div class="mb-2"><img src="<?php echo ASSETS_URL; ?>/images/<?php echo htmlspecialchars($site_icon); ?>" alt="Icono actual" class="img-thumbnail" style="max-height: 48px;"></div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="meta_site_icon" name="meta_site_icon" accept="image/jpeg,image/png,image/webp,image/gif,image/x-icon">
                    <small class="text-muted">Aparece en la pestaña del navegador y en el encabezado. PNG, ICO, JPG, WebP, GIF.</small>
                </div>
                <div class="mb-3">
                    <label for="meta_site_name" class="form-label">Nombre de tu negocio o sitio</label>
                    <input type="text" class="form-control" id="meta_site_name" name="meta_site_name" value="<?php echo $v('meta_site_name'); ?>">
                </div>
                <div class="mb-3">
                    <label for="meta_site_description" class="form-label">Descripción breve (la usan Google y otros buscadores)</label>
                    <textarea class="form-control" id="meta_site_description" name="meta_site_description" rows="2"><?php echo $v('meta_site_description'); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="meta_site_location" class="form-label">Lugar donde están (ciudad, estado)</label>
                    <input type="text" class="form-control" id="meta_site_location" name="meta_site_location" value="<?php echo $v('meta_site_location'); ?>">
                </div>
                </div>

                <div class="tab-pane fade" id="tab-4" role="tabpanel">
                <div class="mb-3">
                    <label for="footer_brand" class="form-label">Nombre de la marca en el pie de página</label>
                    <input type="text" class="form-control" id="footer_brand" name="footer_brand" value="<?php echo $v('footer_brand'); ?>">
                </div>
                <div class="mb-3">
                    <label for="footer_description" class="form-label">Breve descripción de tu negocio</label>
                    <textarea class="form-control" id="footer_description" name="footer_description" rows="2"><?php echo $v('footer_description'); ?></textarea>
                </div>
                <h4 class="text-muted mt-3">Datos de contacto</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="footer_contact_title" class="form-label">Título de la sección (ej. «Contacto»)</label>
                        <input type="text" class="form-control" id="footer_contact_title" name="footer_contact_title" value="<?php echo $v('footer_contact_title'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="footer_contact_street" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="footer_contact_street" name="footer_contact_street" value="<?php echo $v('footer_contact_street'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="footer_contact_city" class="form-label">Ciudad y estado</label>
                        <input type="text" class="form-control" id="footer_contact_city" name="footer_contact_city" value="<?php echo $v('footer_contact_city'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="footer_contact_phone" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="footer_contact_phone" name="footer_contact_phone" value="<?php echo $v('footer_contact_phone'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="footer_contact_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="footer_contact_email" name="footer_contact_email" value="<?php echo $v('footer_contact_email'); ?>">
                    </div>
                </div>
                <h4 class="text-muted mt-3">Horario de atención</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="footer_schedule_title" class="form-label">Título (ej. «Horario»)</label>
                        <input type="text" class="form-control" id="footer_schedule_title" name="footer_schedule_title" value="<?php echo $v('footer_schedule_title'); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="footer_schedule_days" class="form-label">Días que abren</label>
                        <input type="text" class="form-control" id="footer_schedule_days" name="footer_schedule_days" value="<?php echo $v('footer_schedule_days'); ?>" placeholder="Ej: Fines de semana">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="footer_schedule_hours" class="form-label">Horario</label>
                        <input type="text" class="form-control" id="footer_schedule_hours" name="footer_schedule_hours" value="<?php echo $v('footer_schedule_hours'); ?>" placeholder="Ej: 10:00 AM - 7:00 PM">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="footer_schedule_days_2" class="form-label">Días adicionales (opcional)</label>
                        <input type="text" class="form-control" id="footer_schedule_days_2" name="footer_schedule_days_2" value="<?php echo $v('footer_schedule_days_2'); ?>" placeholder="Ej: Entre semana">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="footer_schedule_hours_2" class="form-label">Horario adicional (opcional)</label>
                        <input type="text" class="form-control" id="footer_schedule_hours_2" name="footer_schedule_hours_2" value="<?php echo $v('footer_schedule_hours_2'); ?>" placeholder="Ej: 9:00 AM - 2:00 PM">
                    </div>
                </div>
                <small class="text-muted d-block mb-2">Si dejas vacíos días y horario adicionales, no se mostrarán en el footer.</small>
                <h4 class="text-muted mt-3">Enlaces a redes sociales</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="footer_social_facebook" class="form-label">Enlace de Facebook</label>
                        <input type="url" class="form-control" id="footer_social_facebook" name="footer_social_facebook" value="<?php echo $v('footer_social_facebook'); ?>" placeholder="https://...">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="footer_social_instagram" class="form-label">Enlace de Instagram</label>
                        <input type="url" class="form-control" id="footer_social_instagram" name="footer_social_instagram" value="<?php echo $v('footer_social_instagram'); ?>" placeholder="https://...">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="footer_social_whatsapp_number" class="form-label">Número de WhatsApp</label>
                        <input type="text" class="form-control" id="footer_social_whatsapp_number" name="footer_social_whatsapp_number" value="<?php echo htmlspecialchars($whatsapp_number, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Ej: 529541817823">
                        <small class="text-muted">Solo el número, con código de país, sin espacios ni símbolos.</small>
                    </div>
                </div>
                <h4 class="text-muted mt-3">Texto al final del sitio</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="footer_copyright_text" class="form-label">Frase de derechos reservados</label>
                        <input type="text" class="form-control" id="footer_copyright_text" name="footer_copyright_text" value="<?php echo $v('footer_copyright_text'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="footer_copyright_made_with" class="form-label">Mensaje adicional (ej. «Hecho con amor en…»)</label>
                        <input type="text" class="form-control" id="footer_copyright_made_with" name="footer_copyright_made_with" value="<?php echo $v('footer_copyright_made_with'); ?>">
                    </div>
                </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-success">Guardar cambios</button>
                <a href="<?php echo BASE_URL; ?>/admin" class="btn btn-outline-secondary">Volver a productos</a>
            </div>
        </form>
    </div>
</div>
