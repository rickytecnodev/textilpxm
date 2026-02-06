/**
 * Tabs de categorías: mostrar/ocultar contenido y sincronizar con la URL.
 * Solo se ejecuta si existen .category-tab y .category-content.
 */
(function() {
    'use strict';

    var tabs = document.querySelectorAll('.category-tab');
    if (!tabs.length) return;

    function showCategory(category, element) {
        var allContents = document.querySelectorAll('.category-content');
        allContents.forEach(function(content) {
            content.classList.remove('d-block');
            content.classList.add('d-none');
        });

        var allTabs = document.querySelectorAll('.category-tab');
        allTabs.forEach(function(tab) {
            tab.classList.remove('active', 'fw-semibold');
            tab.classList.add('border-transparent');
        });

        var contentId = category === 'all' ? 'category-all' : 'category-' + category.toLowerCase().replace(/[^a-z0-9]/g, '-');
        var content = document.getElementById(contentId);
        if (content) {
            content.classList.remove('d-none');
            content.classList.add('d-block');
        }

        if (element) {
            element.classList.add('active', 'fw-semibold');
            element.classList.remove('border-transparent');
        }

        var url = new URL(window.location);
        if (category === 'all') {
            url.searchParams.delete('cat');
        } else {
            url.searchParams.set('cat', encodeURIComponent(category));
        }
        window.history.pushState({}, '', url);

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function initTabs() {
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                showCategory(this.getAttribute('data-category'), this);
            });
        });
    }

    function loadCategoryFromURL() {
        document.querySelectorAll('.category-content').forEach(function(content) {
            content.classList.remove('d-block');
            content.classList.add('d-none');
        });

        var urlParams = new URLSearchParams(window.location.search);
        var cat = urlParams.get('cat');

        if (cat) {
            var tabsList = document.querySelectorAll('.category-tab');
            for (var i = 0; i < tabsList.length; i++) {
                if (tabsList[i].getAttribute('data-category') === cat) {
                    showCategory(cat, tabsList[i]);
                    return;
                }
            }
        }

        var allTab = document.querySelector('.category-tab[data-category="all"]');
        if (allTab) {
            showCategory('all', allTab);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initTabs();
        loadCategoryFromURL();
    });
})();
