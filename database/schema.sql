-- Script SQL completo para crear la base de datos de TextilPXM
-- Este script elimina y recrea todo desde cero

-- Eliminar la base de datos si existe (CUIDADO: Esto borrará todos los datos)
DROP DATABASE IF EXISTS textilpxm_db;

-- Crear la base de datos
CREATE DATABASE textilpxm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleccionar la base de datos
USE textilpxm_db;

-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(50) DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar un usuario administrador por defecto (contraseña: admin123)
INSERT INTO users (nombre, email, password, rol) 
VALUES (
    'Administrador',
    'admin@textilpxm.com',
    '$2y$10$RFvz.hTVHPtBWtEJNvHVouKZqyKVG0a8q9nMrapPVqi6xvlL5AR.q',
    'admin'
);

-- Tabla de productos
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    categoria VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    imagen_url VARCHAR(500),
    activo TINYINT(1) DEFAULT 1,
    portada TINYINT(1) DEFAULT 0,
    tallas_disponibles VARCHAR(200) DEFAULT '',
    orden INT DEFAULT NULL COMMENT 'Orden de visualización; por defecto coincide con id',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_activo (activo),
    INDEX idx_portada (portada),
    INDEX idx_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar productos de ejemplo (6 categorías con 6 productos cada una)
-- Algunos productos tienen portada=1 para aparecer en el home
INSERT INTO products (nombre, descripcion, categoria, precio, stock, imagen_url, portada, tallas_disponibles) VALUES
('Monedero Bordado', 'Monedero pequeño con bordados tradicionales. Funcional y hermoso.', 'Accesorios', 380.00, 18, 'https://images.unsplash.com/photo-1624222247344-550fb60583fd?q=80&w=800', 0, 'Talla Única');


-- Tabla para contenido del sitio (editable desde admin)
-- Clave única por campo; valor en texto. WhatsApp se guarda como URL completa.

CREATE TABLE IF NOT EXISTS site_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(120) NOT NULL UNIQUE,
    value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Valores por defecto (misma estructura que content_data.php)
-- Imágenes: rutas relativas a public/images/ (ej. site/icon.png). Los archivos están en public/images/site/
INSERT INTO site_content (`key`, value) VALUES
('navbar_brand', 'Textil PXM'),
('navbar_logo', 'site/logo-navbar.png'),
('footer_brand', 'PRENDAS TÍPICAS OAXACA'),
('footer_description', 'Prendas artesanales de la costa chica de Oaxaca. Tejiendo tradición desde Puerto Escondido.'),
('footer_contact_title', 'Contacto'),
('footer_contact_street', 'Punta Zicatela'),
('footer_contact_city', 'Puerto Escondido, Oaxaca'),
('footer_contact_phone', '+52 954 181 78 23'),
('footer_contact_email', 'prendastipicasoaxaca@gmail.com'),
('footer_schedule_title', 'Horario'),
('footer_schedule_days', 'Fines de semana'),
('footer_schedule_hours', '10:00 AM - 7:00 PM'),
('footer_schedule_days_2', ''),
('footer_schedule_hours_2', ''),
('footer_social_facebook', ''),
('footer_social_instagram', ''),
('footer_social_whatsapp', 'https://wa.me/529541817823?text=Hola,%20me%20interesa%20información%20sobre%20sus%20productos'),
('footer_copyright_text', 'Oaxaca Textiles. Todos los derechos reservados.'),
('footer_copyright_made_with', 'Hecho con amor en Puerto Escondido, Oaxaca'),
('meta_site_name', 'Oaxaca Textiles'),
('meta_site_icon', 'site/icon.png'),
('meta_site_description', 'Descubre la belleza de la ropa típica oaxaqueña. Prendas artesanales hechas a mano en Puerto Escondido, Oaxaca.'),
('meta_site_location', 'Puerto Escondido, Oaxaca'),
('home_hero_location', 'Puerto Escondido, Oaxaca'),
('home_hero_title', 'Tradición Textil Oaxaqueña'),
('home_hero_description', 'Descubre prendas únicas tejidas a mano por artesanas de la costa chica de Oaxaca. Cada pieza cuenta una historia de tradición, color y amor por nuestras raíces.'),
('home_hero_image', 'site/banner.jpg'),
('home_collection_title', 'Prendas Artesanales'),
('home_collection_description', 'Cada pieza es elaborada con técnicas ancestrales transmitidas de generación en generación.'),
('home_collection_no_products', 'No hay productos disponibles en este momento.'),
('home_about_badge', 'Nuestra Historia'),
('home_about_title', 'Raíces que Visten'),
('home_about_description1', 'Desde el corazón de Puerto Escondido, trabajamos directamente con artesanas de la región, preservando técnicas milenarias de tejido en telar de cintura y bordado a mano.'),
('home_about_description2', 'Cada prenda que ofrecemos representa semanas de trabajo dedicado, usando tintes naturales extraídos de la grana cochinilla, el añil y otras plantas de la región.'),
('home_about_image', 'site/about.jpg'),
('home_about_stats_years_value', '15+'),
('home_about_stats_years_label', 'Años de tradición'),
('home_about_stats_countrys_value', '10+'),
('home_about_stats_countrys_label', 'Paises exportados'),
('home_about_stats_products_value', '120+'),
('home_about_stats_products_label', 'Productos tejidos')
ON DUPLICATE KEY UPDATE value = VALUES(value);