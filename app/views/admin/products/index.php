<h1 class="h4 fw-bold text-center" style="margin-top: 6rem;">Productos</h1>

<?php
$categorias_disponibles = [];
if (!empty($products)) {
    $cats = array_unique(array_column($products, 'categoria'));
    sort($cats, SORT_STRING);
    $categorias_disponibles = array_values($cats);
}
?>
<?php if (!empty($flash_message)): ?>
    <div class="alert alert-<?php echo $flash_type === 'danger' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($flash_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
<?php endif; ?>

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

<?php if (!empty($products)): ?>
<script>
(function() {
    var BASE_URL = <?php echo json_encode(BASE_URL); ?>;
    var tbody = document.getElementById('products-tbody');
    var filterInput = document.getElementById('filter-products');
    var sortSelect = document.getElementById('sort-products');
    var countEl = document.getElementById('product-count');
    var filterNombre = document.getElementById('filter-nombre');
    var filterCategoria = document.getElementById('filter-categoria');
    var filterPrecio = document.getElementById('filter-precio');
    var filterStock = document.getElementById('filter-stock');
    var filterPortada = document.getElementById('filter-portada');
    var filterEstado = document.getElementById('filter-estado');
    if (!tbody) return;

    function getRows() { return [].slice.call(tbody.querySelectorAll('tr')); }

    function updateCount() {
        var visible = getRows().filter(function(r) { return r.style.display !== 'none'; });
        countEl.textContent = visible.length + ' producto(s)';
    }

    function applyAllFilters() {
        var q = (filterInput && filterInput.value) ? filterInput.value.trim().toLowerCase() : '';
        var qNombre = (filterNombre && filterNombre.value) ? filterNombre.value.trim().toLowerCase() : '';
        var catRaw = filterCategoria ? filterCategoria.value : '';
        var esOrdenCategoria = (catRaw === '__sort_asc__' || catRaw === '__sort_desc__');
        var catFiltro = esOrdenCategoria ? '' : (catRaw || '');
        var portadaVal = (filterPortada && filterPortada.value !== '') ? filterPortada.value : '';
        var estadoVal = (filterEstado && filterEstado.value !== '') ? filterEstado.value : '';

        getRows().forEach(function(tr) {
            var nombre = (tr.getAttribute('data-nombre') || '').toLowerCase();
            var cat = (tr.getAttribute('data-categoria') || '');
            var portada = String(tr.getAttribute('data-portada') || '');
            var activo = String(tr.getAttribute('data-activo') || '');

            var show = true;
            if (q && nombre.indexOf(q) === -1 && cat.toLowerCase().indexOf(q) === -1) show = false;
            if (show && qNombre && nombre.indexOf(qNombre) === -1) show = false;
            if (show && catFiltro !== '' && cat !== catFiltro) show = false;
            if (show && portadaVal !== '' && portada !== portadaVal) show = false;
            if (show && estadoVal !== '' && activo !== estadoVal) show = false;
            tr.style.display = show ? '' : 'none';
        });
        updateCount();
    }

    function applySort() {
        var sortPrecio = filterPrecio && filterPrecio.value;
        var sortStock = filterStock && filterStock.value;
        var catRaw = filterCategoria ? filterCategoria.value : '';
        var val = 'nombre-asc';
        if (sortPrecio === 'asc' || sortPrecio === 'desc') val = 'precio-' + sortPrecio;
        else if (sortStock === 'asc' || sortStock === 'desc') val = 'stock-' + sortStock;
        else if (catRaw === '__sort_asc__') val = 'categoria-asc';
        else if (catRaw === '__sort_desc__') val = 'categoria-desc';
        else if (sortSelect && sortSelect.value) val = sortSelect.value;

        var parts = val.split('-');
        var key = parts[0];
        var dir = (parts[1] === 'desc') ? -1 : 1;
        var rows = getRows();
        rows.sort(function(a, b) {
            var va, vb;
            if (key === 'nombre' || key === 'categoria') {
                va = (a.getAttribute('data-' + key) || '').toLowerCase();
                vb = (b.getAttribute('data-' + key) || '').toLowerCase();
                return dir * (va < vb ? -1 : (va > vb ? 1 : 0));
            }
            if (key === 'precio' || key === 'stock' || key === 'portada') {
                va = parseFloat(a.getAttribute('data-' + key)) || 0;
                vb = parseFloat(b.getAttribute('data-' + key)) || 0;
                return dir * (va - vb);
            }
            return 0;
        });
        rows.forEach(function(r) { tbody.appendChild(r); });
    }

    function refresh() {
        applyAllFilters();
        applySort();
        updateSwapButtonsState();
    }

    function tieneAlgunFiltro() {
        if (filterNombre && filterNombre.value.trim() !== '') return true;
        if (filterCategoria && filterCategoria.value !== '') return true;
        if (filterPrecio && filterPrecio.value !== '') return true;
        if (filterStock && filterStock.value !== '') return true;
        if (filterPortada && filterPortada.value !== '') return true;
        if (filterEstado && filterEstado.value !== '') return true;
        return false;
    }

    function updateSwapButtonsState() {
        var conFiltro = tieneAlgunFiltro();
        var rows = getRows();
        var primero = rows[0];
        var ultimo = rows[rows.length - 1];
        rows.forEach(function(tr) {
            var upBtn = tr.querySelector('a.swap-up');
            var downBtn = tr.querySelector('a.swap-down');
            var deshabilitarUp = conFiltro || tr === primero;
            var deshabilitarDown = conFiltro || tr === ultimo;
            if (upBtn) {
                if (deshabilitarUp) {
                    upBtn.classList.add('disabled');
                    upBtn.setAttribute('aria-disabled', 'true');
                    upBtn.title = conFiltro ? 'Quita los filtros para cambiar el orden' : 'Es el primero';
                } else {
                    upBtn.classList.remove('disabled');
                    upBtn.removeAttribute('aria-disabled');
                    upBtn.title = 'Subir en el orden';
                }
            }
            if (downBtn) {
                if (deshabilitarDown) {
                    downBtn.classList.add('disabled');
                    downBtn.setAttribute('aria-disabled', 'true');
                    downBtn.title = conFiltro ? 'Quita los filtros para cambiar el orden' : 'Es el último';
                } else {
                    downBtn.classList.remove('disabled');
                    downBtn.removeAttribute('aria-disabled');
                    downBtn.title = 'Bajar en el orden';
                }
            }
        });
    }

    function tieneOrdenCategoria() {
        var v = filterCategoria ? filterCategoria.value : '';
        return (v === '__sort_asc__' || v === '__sort_desc__');
    }
    function tieneOrdenPrecio() {
        var v = filterPrecio ? filterPrecio.value : '';
        return (v === 'asc' || v === 'desc');
    }
    function tieneOrdenStock() {
        var v = filterStock ? filterStock.value : '';
        return (v === 'asc' || v === 'desc');
    }

    function actualizarDeshabilitarOrden() {
        var cat = tieneOrdenCategoria();
        var pre = tieneOrdenPrecio();
        var stk = tieneOrdenStock();
        if (filterCategoria) filterCategoria.disabled = (pre || stk);
        if (filterPrecio) filterPrecio.disabled = (cat || stk);
        if (filterStock) filterStock.disabled = (cat || pre);
    }

    function onChangeCategoria() {
        if (tieneOrdenCategoria()) {
            if (filterPrecio) { filterPrecio.value = ''; }
            if (filterStock) { filterStock.value = ''; }
        }
        actualizarDeshabilitarOrden();
        refresh();
    }
    function onChangePrecio() {
        if (tieneOrdenPrecio()) {
            if (filterStock) { filterStock.value = ''; }
            if (filterCategoria && (filterCategoria.value === '__sort_asc__' || filterCategoria.value === '__sort_desc__')) {
                filterCategoria.value = '';
            }
        }
        actualizarDeshabilitarOrden();
        refresh();
    }
    function onChangeStock() {
        if (tieneOrdenStock()) {
            if (filterPrecio) { filterPrecio.value = ''; }
            if (filterCategoria && (filterCategoria.value === '__sort_asc__' || filterCategoria.value === '__sort_desc__')) {
                filterCategoria.value = '';
            }
        }
        actualizarDeshabilitarOrden();
        refresh();
    }

    actualizarDeshabilitarOrden();

    if (filterInput) filterInput.addEventListener('input', refresh);
    if (sortSelect) sortSelect.addEventListener('change', refresh);
    if (filterNombre) filterNombre.addEventListener('input', refresh);
    if (filterCategoria) filterCategoria.addEventListener('change', onChangeCategoria);
    if (filterPrecio) filterPrecio.addEventListener('change', onChangePrecio);
    if (filterStock) filterStock.addEventListener('change', onChangeStock);
    if (filterPortada) filterPortada.addEventListener('change', refresh);
    if (filterEstado) filterEstado.addEventListener('change', refresh);

    updateSwapButtonsState();

    function prevRow(tr) { return tr.previousElementSibling; }
    function nextRow(tr) { return tr.nextElementSibling; }
    tbody.addEventListener('click', function(e) {
        var link = e.target.closest('a.swap-up, a.swap-down');
        if (!link || link.classList.contains('disabled')) return;
        e.preventDefault();
        var tr = link.closest('tr');
        var otherTr = link.classList.contains('swap-up') ? prevRow(tr) : nextRow(tr);
        if (!otherTr) return;
        var id1 = tr.getAttribute('data-id');
        var id2 = otherTr.getAttribute('data-id');
        if (id1 && id2) window.location.href = BASE_URL + '/admin/ordenarProductosSwap/' + id1 + '/' + id2;
    });
})();
</script>
<?php endif; ?>
