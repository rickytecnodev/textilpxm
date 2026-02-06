/**
 * Listado admin: filtros, orden y botones subir/bajar orden.
 * Solo se ejecuta si existe #products-tbody (página de productos).
 */
(function() {
    'use strict';

    var tbody = document.getElementById('products-tbody');
    if (!tbody) return;

    var BASE_URL = document.body.dataset.baseUrl || '';
    var countEl = document.getElementById('product-count');
    var filterNombre = document.getElementById('filter-nombre');
    var filterCategoria = document.getElementById('filter-categoria');
    var filterPrecio = document.getElementById('filter-precio');
    var filterStock = document.getElementById('filter-stock');
    var filterPortada = document.getElementById('filter-portada');
    var filterEstado = document.getElementById('filter-estado');

    function getRows() {
        return [].slice.call(tbody.querySelectorAll('tr'));
    }

    function updateCount() {
        if (!countEl) return;
        var visible = getRows().filter(function(r) { return r.style.display !== 'none'; });
        countEl.textContent = visible.length + ' producto(s)';
    }

    function applyAllFilters() {
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
            if (qNombre && nombre.indexOf(qNombre) === -1) show = false;
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
            if (filterPrecio) filterPrecio.value = '';
            if (filterStock) filterStock.value = '';
        }
        actualizarDeshabilitarOrden();
        refresh();
    }
    function onChangePrecio() {
        if (tieneOrdenPrecio()) {
            if (filterStock) filterStock.value = '';
            if (filterCategoria && (filterCategoria.value === '__sort_asc__' || filterCategoria.value === '__sort_desc__'))
                filterCategoria.value = '';
        }
        actualizarDeshabilitarOrden();
        refresh();
    }
    function onChangeStock() {
        if (tieneOrdenStock()) {
            if (filterPrecio) filterPrecio.value = '';
            if (filterCategoria && (filterCategoria.value === '__sort_asc__' || filterCategoria.value === '__sort_desc__'))
                filterCategoria.value = '';
        }
        actualizarDeshabilitarOrden();
        refresh();
    }

    actualizarDeshabilitarOrden();

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
        if (id1 && id2 && BASE_URL)
            window.location.href = BASE_URL + '/admin/ordenarProductosSwap/' + id1 + '/' + id2;
    });
})();
