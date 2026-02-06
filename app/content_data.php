<?php
/**
 * Contenido por defecto del sitio (fallback cuando la tabla site_content está vacía).
 * No se usa si la BD ya tiene datos en site_content.
 * Cambiado para pruebas/test.
 */
return [
    'navbar' => [
        'brand' => 'Textil TEST',
        'logo' => 'site/logo-navbar-test.png',
    ],
    'footer' => [
        'brand' => 'PRENDAS TÍPICAS TEST OAXACA',
        'description' => 'Prendas artesanales test de la costa chica test de Oaxaca.',
        'contact' => [
            'title' => 'Contacto de Test',
            'address' => ['street' => 'Calle Test', 'city' => 'Ciudad Test'],
            'phone' => '555-TEST',
            'email' => 'test@email.com',
        ],
        'schedule' => [
            'title' => 'Horario Test',
            'weekdays' => ['days' => 'Lun-Vie', 'hours' => '09:00-17:00'],
            'extra' => ['days' => 'Sábado', 'hours' => '10:00-14:00'],
        ],
        'social' => [
            'facebook' => 'https://facebook.com/test',
            'instagram' => 'https://instagram.com/test',
            'whatsapp' => '555-TEST-WHATSAPP'
        ],
        'copyright' => [
            'text' => '© 2024 Test S.A.',
            'made_with' => 'Hecho con ❤ en Testlandia'
        ],
    ],
    'meta' => [
        'site' => [
            'name' => 'Oaxaca Textiles Test',
            'icon' => 'site/icon-test.png',
            'description' => 'Sitio de prueba para textiles de Oaxaca.',
            'location' => 'Testla, OAX',
        ],
    ],
    'home' => [
        'hero' => [
            'location' => 'Test City',
            'title' => 'Tradición Textil Oaxaqueña de Test',
            'description' => 'Bienvenidos a nuestro sitio test.',
            'image' => 'site/banner-test.jpg',
        ],
        'collection' => [
            'title' => 'Prendas Artesanales Test',
            'description' => 'Descripción de test para la colección.',
            'no_products' => 'No hay productos test disponibles actualmente.'
        ],
        'about' => [
            'badge' => 'Test Badge',
            'title' => 'Sobre Nosotros TEST',
            'description1' => 'Somos una empresa test dedicada a las prendas.',
            'description2' => 'Prueba de segunda descripción en test.',
            'image' => 'site/about-test.jpg',
            'stats' => [
                'years' => ['value' => '10', 'label' => 'Años de experiencia test'],
                'countrys' => ['value' => '3', 'label' => 'Países atendidos test'],
                'products' => ['value' => '50', 'label' => 'Productos test'],
            ],
        ],
    ],
];
