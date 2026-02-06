/**
 * Formulario admin producto: chips de tallas y (en edición) deshabilitar enviar si no hay cambios.
 * Solo se ejecuta si existe #form-producto.
 */
(function() {
    'use strict';

    var form = document.getElementById('form-producto');
    if (!form) return;

    var hidden = document.getElementById('tallas_disponibles');
    var inputNueva = document.getElementById('talla-nueva');
    var btnAgregar = document.getElementById('btn-agregar-talla');
    var container = document.getElementById('tallas-chips');

    if (hidden && container) {
        function getTallas() {
            var v = (hidden.value || '').trim();
            return v ? v.split(',').map(function(s) { return s.trim(); }).filter(Boolean) : [];
        }

        function setTallas(arr) {
            hidden.value = arr.join(',');
            if (form) {
                var e = new Event('change', { bubbles: true });
                form.dispatchEvent(e);
            }
        }

        function agregarTalla() {
            var val = (inputNueva.value || '').trim();
            if (!val) return;
            var tallas = getTallas();
            if (tallas.indexOf(val) !== -1) return;
            tallas.push(val);
            setTallas(tallas);
            var span = document.createElement('span');
            span.className = 'badge bg-secondary d-inline-flex align-items-center gap-1 py-2 px-2';
            span.innerHTML = val + ' <button type="button" class="btn btn-link p-0 text-white text-decoration-none" style="font-size: 0.9rem; line-height: 1;" data-talla="' + val.replace(/"/g, '&quot;') + '" aria-label="Quitar">×</button>';
            container.appendChild(span);
            span.querySelector('button').addEventListener('click', quitarTalla);
            inputNueva.value = '';
            if (inputNueva.focus) inputNueva.focus();
        }

        function quitarTalla(ev) {
            var btn = ev.target;
            var t = btn.getAttribute('data-talla');
            if (!t) return;
            var tallas = getTallas().filter(function(x) { return x !== t; });
            setTallas(tallas);
            var badge = btn.closest('.badge');
            if (badge) badge.remove();
        }

        if (btnAgregar) btnAgregar.addEventListener('click', agregarTalla);
        if (inputNueva) {
            inputNueva.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') { e.preventDefault(); agregarTalla(); }
            });
        }
        container.querySelectorAll('button[data-talla]').forEach(function(btn) {
            btn.addEventListener('click', quitarTalla);
        });
    }

    var btn = document.getElementById('btn-submit');
    if (btn && form.getAttribute('action') && form.getAttribute('action').indexOf('/actualizar/') !== -1) {
        function val(el) {
            if (!el) return '';
            if (el.type === 'checkbox') return el.checked ? '1' : '0';
            return (el.value || '').trim();
        }
        function numVal(el) {
            var v = val(el).replace(',', '.');
            return isNaN(parseFloat(v)) ? '' : parseFloat(v).toString();
        }

        var initial = {
            nombre: val(form.nombre),
            descripcion: val(form.descripcion),
            categoria: val(form.categoria),
            precio: numVal(form.precio) || val(form.precio),
            stock: val(form.stock),
            imagen: form.imagen && form.imagen.files ? form.imagen.files.length : 0,
            tallas_disponibles: val(form.tallas_disponibles),
            activo: val(form.activo),
            portada: val(form.portada)
        };

        function checkChanges() {
            var current = {
                nombre: val(form.nombre),
                descripcion: val(form.descripcion),
                categoria: val(form.categoria),
                precio: numVal(form.precio) || val(form.precio),
                stock: val(form.stock),
                imagen: form.imagen && form.imagen.files ? form.imagen.files.length : 0,
                tallas_disponibles: val(form.tallas_disponibles),
                activo: val(form.activo),
                portada: val(form.portada)
            };
            var changed = false;
            for (var k in initial) {
                if (initial[k] !== current[k]) { changed = true; break; }
            }
            btn.disabled = !changed;
        }

        form.addEventListener('input', checkChanges);
        form.addEventListener('change', checkChanges);
    }
})();
