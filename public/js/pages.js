/**
 * Aplica clase al body según data-page del primer .js-page-zone.
 * Las vistas que necesitan estilos por página incluyen: <div class="js-page-zone d-none" data-page="home"></div>
 */
(function() {
    'use strict';
    var zone = document.querySelector('.js-page-zone');
    if (zone && zone.dataset.page) {
        document.body.classList.add(zone.dataset.page + '-page');
    }
})();
