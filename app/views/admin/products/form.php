<?php
$isEdit = !empty($product['id']);
$formAction = $isEdit ? BASE_URL . '/admin/actualizar/' . (int)$product['id'] : BASE_URL . '/admin/guardar';
$nombre = $product['nombre'] ?? '';
$descripcion = $product['descripcion'] ?? '';
$categoria = $product['categoria'] ?? '';
$precio = $product['precio'] ?? '';
$stock = $product['stock'] ?? 0;
$imagen_url = $product['imagen_url'] ?? '';
$activo = (int)($product['activo'] ?? 1);
$portada = (int)($product['portada'] ?? 0);
$tallas_disponibles = $product['tallas_disponibles'] ?? '';
$tallas_array = array_filter(array_map('trim', explode(',', $tallas_disponibles)));
?>

<?php $alert_class = 'mb-3'; require APP_PATH . '/views/partials/_flash_alert.php'; ?>

<div class="card shadow-sm mx-auto mb-4" style="max-width: 720px; margin-top: 6rem;">
    <div class="card-body p-4">
    <h1 class="h4 fw-bold mb-3 text-center"><?php echo $isEdit ? 'Editar producto' : 'Nuevo producto'; ?></h1>
    <form method="post" action="<?php echo $formAction; ?>" id="form-producto" enctype="multipart/form-data">
        <?php if (!empty($csrf_token)): ?>
        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nombre" name="nombre" required
                   value="<?php echo htmlspecialchars($nombre); ?>">
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($descripcion); ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="categoria" name="categoria" required
                       list="categorias-list" value="<?php echo htmlspecialchars($categoria); ?>">
                <?php if (!empty($categories)): ?>
                    <datalist id="categorias-list">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>">
                        <?php endforeach; ?>
                    </datalist>
                <?php endif; ?>
            </div>
            <div class="col-md-3 mb-3">
                <label for="precio" class="form-label">Precio <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="precio" name="precio" required
                       value="<?php echo htmlspecialchars($precio); ?>" placeholder="0.00">
            </div>
            <div class="col-md-3 mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" min="0"
                       value="<?php echo (int)$stock; ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen del producto</label>
            <?php if ($isEdit): ?>
                <div class="mb-2">
                    <img id="preview-imagen" src="<?php echo htmlspecialchars(productImageUrl($imagen_url)); ?>" alt="Vista previa" class="img-thumbnail" style="max-height: 120px;">
                </div>
            <?php endif; ?>
            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp,image/gif">
            <small class="text-muted">Formatos: JPG, PNG, WebP, GIF. En edición, sube una nueva imagen solo si quieres reemplazarla.</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Tallas disponibles</label>
            <input type="hidden" id="tallas_disponibles" name="tallas_disponibles" value="<?php echo htmlspecialchars($tallas_disponibles); ?>">
            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                <input type="text" class="form-control form-control-sm" id="talla-nueva" placeholder="Escribe una talla y pulsa Agregar" style="max-width: 280px;" autocomplete="off">
                <button type="button" class="btn btn-sm btn-outline-dark" id="btn-agregar-talla">Agregar</button>
            </div>
            <div id="tallas-chips" class="d-flex flex-wrap gap-1">
                <?php foreach ($tallas_array as $t): ?>
                <span class="badge bg-secondary d-inline-flex align-items-center gap-1 py-2 px-2">
                    <?php echo htmlspecialchars($t); ?>
                    <button type="button" class="btn btn-link p-0 text-white text-decoration-none" style="font-size: 0.9rem; line-height: 1;" data-talla="<?php echo htmlspecialchars($t); ?>" aria-label="Quitar">×</button>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mb-4">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1" <?php echo $activo ? 'checked' : ''; ?>>
                <label class="form-check-label" for="activo">Activo (visible en tienda)</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="portada" id="portada" value="1" <?php echo $portada ? 'checked' : ''; ?>>
                <label class="form-check-label" for="portada">Mostrar en portada</label>
            </div>
        </div>
        <div class="gap-4 text-center">
            <button type="submit" class="btn btn-dark" id="btn-submit"<?php echo $isEdit ? ' disabled' : ''; ?>><?php echo $isEdit ? 'Guardar cambios' : 'Crear producto'; ?></button>
            <a href="<?php echo BASE_URL; ?>/admin" class="btn btn-dark">Cancelar</a>
        </div>
    </form>

    <?php if ($isEdit && isset($product['id'])): 
        $galeriaItems = productGalleryFiles($product['id']);
    ?>
    <div class="border rounded p-3 bg-light mt-4">
        <label class="form-label fw-semibold">Galería de fotos (fotos extras del producto)</label>
        <p class="small text-muted mb-2">Se muestran en la ficha del producto después de la imagen principal. Máximo 20 fotos.</p>
        <form method="post" action="<?php echo BASE_URL; ?>/admin/galeria/subir/<?php echo (int)$product['id']; ?>" enctype="multipart/form-data" class="d-flex flex-wrap align-items-end gap-2 mb-3">
            <?php if (!empty($csrf_token)): ?>
            <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <?php endif; ?>
            <div class="flex-grow-1" style="min-width: 200px;">
                <input type="file" class="form-control form-control-sm" name="galeria" accept="image/jpeg,image/png,image/webp,image/gif" required>
            </div>
            <button type="submit" class="btn btn-sm btn-dark">Agregar a galería</button>
        </form>
        <?php if (!empty($galeriaItems)): ?>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($galeriaItems as $item): ?>
            <div class="position-relative d-inline-block">
                <img src="<?php echo htmlspecialchars($item['url']); ?>" alt="" class="img-thumbnail" style="height: 70px; width: 70px; object-fit: cover;">
                <form method="post" action="<?php echo BASE_URL; ?>/admin/galeria/eliminar/<?php echo (int)$product['id']; ?>" class="position-absolute top-0 end-0 m-0" style="transform: translate(50%, -50%);">
                    <?php if (!empty($csrf_token)): ?>
                    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <?php endif; ?>
                    <input type="hidden" name="archivo" value="<?php echo htmlspecialchars($item['filename']); ?>">
                    <button type="submit" class="btn btn-danger btn-sm rounded-circle p-0" style="width: 22px; height: 22px; line-height: 1;" title="Eliminar foto" onclick="return confirm('¿Eliminar esta foto de la galería?');">×</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="small text-muted mb-0">Aún no hay fotos en la galería. Sube la primera arriba.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    </div>
</div>

