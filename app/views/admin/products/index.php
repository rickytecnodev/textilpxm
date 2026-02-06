<h1 class="h4 fw-bold text-center" style="margin-top: 6rem;">Productos</h1>

<?php
$categorias_disponibles = [];
if (!empty($products)) {
    $cats = array_unique(array_column($products, 'categoria'));
    sort($cats, SORT_STRING);
    $categorias_disponibles = array_values($cats);
}
?>
<?php require APP_PATH . '/views/partials/_flash_alert.php'; ?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <span class="text-muted small" id="product-count"><?php echo count($products); ?> producto(s)</span>
    <a href="<?php echo BASE_URL; ?>/admin/crear" class="btn btn-dark btn-sm">
        <i class="bi bi-plus-lg"></i> Nuevo producto
    </a>
</div>

<style>
    a.swap-up.disabled, a.swap-down.disabled { pointer-events: none; opacity: 0.5; cursor: not-allowed; }
</style>
<div class="card shadow-sm mb-4">
    <?php if (empty($products)): ?>
        <div class="card-body text-center py-5 text-muted">
            <p class="fw-bold mb-2">No hay productos</p>
            <p class="mb-3">Crea el primero desde el botón «Nuevo producto».</p>
            <a href="<?php echo BASE_URL; ?>/admin/crear" class="btn btn-dark">Nuevo producto</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Orden</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Portada</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    <tr class="table-secondary align-middle">
                        <th class="p-1"></th>
                        <th class="p-1">
                            <input type="text" class="form-control form-control-sm" id="filter-nombre" placeholder="Nombre...">
                        </th>
                        <th class="p-1">
                            <select class="form-select form-select-sm" id="filter-categoria">
                                <option value="">-</option>
                                <optgroup label="Orden">
                                    <option value="__sort_asc__">A → Z</option>
                                    <option value="__sort_desc__">Z → A</option>
                                </optgroup>
                                <?php if (!empty($categorias_disponibles)): ?>
                                <optgroup label="Categorías">
                                    <?php foreach ($categorias_disponibles as $cat): ?>
                                        <option value="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endif; ?>
                            </select>
                        </th>
                        <th class="p-1">
                            <select class="form-select form-select-sm" id="filter-precio">
                                <option value="">—</option>
                                <option value="asc">Menor a mayor</option>
                                <option value="desc">Mayor a menor</option>
                            </select>
                        </th>
                        <th class="p-1">
                            <select class="form-select form-select-sm" id="filter-stock">
                                <option value="">—</option>
                                <option value="asc">Menor a mayor</option>
                                <option value="desc">Mayor a menor</option>
                            </select>
                        </th>
                        <th class="p-1">
                            <select class="form-select form-select-sm" id="filter-portada">
                                <option value="">-</option>
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </th>
                        <th class="p-1">
                            <select class="form-select form-select-sm" id="filter-estado">
                                <option value="">-</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </th>
                        <th class="p-1 text-end">
                            <a href="<?php echo BASE_URL; ?>/admin" class="btn btn-outline-secondary btn-sm" title="Recargar">
                                <i class="bi bi-arrow-counterclockwise"></i> Restablecer
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody id="products-tbody">
                    <?php foreach ($products as $p): ?>
                        <tr data-id="<?php echo (int)$p['id']; ?>" data-nombre="<?php echo htmlspecialchars($p['nombre'], ENT_QUOTES, 'UTF-8'); ?>" data-categoria="<?php echo htmlspecialchars($p['categoria'], ENT_QUOTES, 'UTF-8'); ?>" data-precio="<?php echo (float)$p['precio']; ?>" data-stock="<?php echo (int)($p['stock'] ?? 0); ?>" data-portada="<?php echo !empty($p['portada']) ? '1' : '0'; ?>" data-activo="<?php echo !empty($p['activo']) ? '1' : '0'; ?>">
                            <td>
                                <a href="#" class="swap-up btn btn-sm btn-outline-secondary me-1" title="Subir en el orden"><i class="bi bi-chevron-up"></i></a>
                                <a href="#" class="swap-down btn btn-sm btn-outline-secondary" title="Bajar en el orden"><i class="bi bi-chevron-down"></i></a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo htmlspecialchars(productImageUrl($p['imagen_url'] ?? '')); ?>" alt="" class="rounded object-fit-cover" width="48" height="48" loading="lazy">
                                    <div>
                                        <span class="fw-medium"><?php echo htmlspecialchars($p['nombre']); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="text-muted small"><?php echo htmlspecialchars($p['categoria']); ?></span></td>
                            <td>$<?php echo number_format((float)$p['precio'], 2); ?></td>
                            <td><?php echo (int)($p['stock'] ?? 0); ?></td>
                            <td>
                                <?php if (!empty($p['portada'])): ?>
                                    <span class="badge bg-primary">Sí</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($p['activo'])): ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="<?php echo BASE_URL; ?>/admin/editar/<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i></a>
                                <a href="<?php echo BASE_URL; ?>/admin/eliminar/<?php echo (int)$p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmarEliminar(this.href, 'eliminar');"><i class="bi bi-trash me-1"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

