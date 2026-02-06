# TextilPXM

Sitio Web Dinámico desarrollado con PHP, Bootstrap y Arquitectura MVC

## Características

- Framework MVC (Modelo-Vista-Controlador) personalizado
- Diseño responsivo con Bootstrap 5
- Conexión PDO a base de datos MySQL/MariaDB
- Sistema de enrutamiento dinámico
- Autenticación de usuarios
- **CRUD completo de productos** (Crear, Leer, Actualizar, Eliminar)
- **Catálogo público de productos** con diseño adaptado
- **Panel de administración** para gestionar productos
- Control de stock y disponibilidad
- Gestión de categorías de productos
- Vistas con layouts reutilizables
- URLs amigables
- Sin dependencias externas (solo CDN para Bootstrap)

## Estructura del Proyecto

```
textilpxm/
├── app/
│   ├── controllers/        # Controladores
│   │   ├── Controller.php   # Controlador base
│   │   ├── HomeController.php
│   │   └── AdminController.php  # Panel admin (productos, contenido, login)
│   ├── models/
│   │   ├── Model.php
│   │   ├── User.php
│   │   ├── Product.php
│   │   └── SiteContent.php
│   ├── views/
│   │   ├── View.php
│   │   ├── layouts/ (main.php, admin.php)
│   │   ├── home/, categorias/, ordenar/, producto/
│   │   ├── admin/ (login, products, contenido)
│   │   ├── login.php, register.php, contact.php, about.php
│   │   └── ...
│   ├── helpers.php
│   ├── content_data.php    # Fallback de contenido (si BD vacía)
│   └── router.php
├── config/
│   └── Config.php          # Configuración y constantes (BD, rutas)
├── database/
│   └── schema.sql
├── public/
│   ├── css/, js/, images/
│   ├── index.php           # Punto de entrada
│   └── .htaccess
└── README.md
```

## Requisitos Previos

- PHP 7.4 o superior
- MySQL/MariaDB
- Servidor web Apache con mod_rewrite habilitado
- XAMPP (opcional, pero recomendado)

## Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/081120RickyPeralta/textilpxm.git
cd textilpxm
```

2. Importante: Los archivos están en la subcarpeta `textilpxm/`, puedes moverlos al directorio raíz del proyecto.

3. Crear la base de datos:
   - Ejecuta el script SQL: `database/schema.sql`
   - O importa la base de datos desde phpMyAdmin
   - El script incluye:
     - Tabla de usuarios
     - Tabla de productos
     - Usuario administrador por defecto (email: admin@textilpxm.com, contraseña: admin123)
     - Productos de ejemplo

4. Configurar el servidor web (solo necesario si mod_rewrite no está habilitado):

   **Nota:** El proyecto detecta automáticamente las rutas, por lo que funciona en cualquier subdirectorio sin configuración adicional.
   
   **Habilitar mod_rewrite en Apache (XAMPP) - Solo si no está habilitado:**
   
   En la mayoría de instalaciones de XAMPP, mod_rewrite ya viene habilitado. Si las URLs amigables no funcionan:
   
   a. Abre el archivo `httpd.conf` ubicado en `C:\xampp\apache\conf\httpd.conf`
   
   b. Busca la línea: `#LoadModule rewrite_module modules/mod_rewrite.so`
   
   c. Elimina el símbolo `#` al inicio: `LoadModule rewrite_module modules/mod_rewrite.so`
   
   d. Busca `<Directory "C:/xampp/htdocs">` y cambia `AllowOverride None` a `AllowOverride All`
   
   e. Guarda y reinicia Apache desde el Panel de Control de XAMPP
   
   **Acceso al sitio:**
   
   Simplemente accede a: `http://localhost/textilpxm/public/`
   
   El proyecto detecta automáticamente la ruta base, por lo que funcionará en cualquier ubicación sin configuración adicional.

5. Ajustar las configuraciones (opcional):
   - `config/Config.php`: host, nombre y usuario de la base de datos (DB_HOST, DB_NAME, DB_USER, DB_PASS)
   - En producción, define `APP_ENV=production` (o en el servidor) para no mostrar errores PHP
   - Las URLs se detectan automáticamente

6. Permisos:
   - Asegúrate de que PHP tenga permisos de escritura en las carpetas necesarias

## Uso

### Configuración del Servidor

Asegúrate de que el DocumentRoot de tu servidor web apunte al directorio `public/` por razones de seguridad.

### Configuración de XAMPP

**¡Configuración simplificada!** El proyecto detecta automáticamente las rutas, por lo que solo necesitas:

1. **Asegúrate de que Apache esté corriendo** en XAMPP
2. **Accede directamente a:** `http://localhost/textilpxm/public/`

**Si las URLs amigables no funcionan:**
- Verifica que mod_rewrite esté habilitado (ver paso 4 de Instalación)
- En la mayoría de instalaciones de XAMPP ya viene habilitado por defecto

**Nota:** El proyecto funciona automáticamente en cualquier subdirectorio sin necesidad de configurar Virtual Hosts o modificar rutas.

### Accediendo al sitio

Una vez que Apache esté corriendo, accede a:

**Páginas Públicas:**
- Inicio: `http://localhost/textilpxm/public/` (o sin `/public/` si usas el .htaccess raíz)
- Categorías: `/categorias` (con búsqueda `?q=`)
- Producto: `/producto/{id}`
- Ordenar: `/ordenar` (formulario de pedido)
- Contacto: `/contact` — Nosotros: `/about`
- Login/Registro (usuarios): `/login`, `/register`

**Panel de Administración (solo rol admin):**
- Login admin: `http://localhost/textilpxm/public/admin` o `/admin/login`
- Productos: `/admin` (listado, orden, crear, editar, eliminar)
- Contenido del sitio: `/admin/contenido`

**Credenciales admin por defecto:**
- Email: `admin@textilpxm.com`
- Contraseña: `admin123`

**Nota:** Si colocas el proyecto en otra ubicación, simplemente ajusta la ruta en la URL. El proyecto detectará automáticamente su ubicación.

### URLs Amigables

Las URLs están configuradas para ser amigables gracias al archivo `.htaccess`. Esto convierte URLs como:
- `index.php?path=about` → `/about`

Si las URLs no funcionan, verifica que:
1. Apache tenga mod_rewrite habilitado
2. El archivo `.htaccess` esté en la carpeta `public/`
3. `AllowOverride` esté configurado en `All` en tu configuración de Apache

### Gestión de Productos (Admin)

En `/admin` (tras iniciar sesión como admin):

1. **Listado:** Ver todos los productos con filtros y orden
2. **Orden:** Subir/bajar orden con los botones en la tabla
3. **Nuevo producto:** `/admin/crear`
4. **Editar / Eliminar:** Desde el listado (eliminar es borrado permanente)
5. **Portada:** Marcar productos para la sección de portada en inicio
6. **Contenido del sitio:** Navbar, footer, meta, textos del home en `/admin/contenido`

### Catálogo Público

- **Inicio:** Productos en portada y enlace a categorías
- **Categorías:** `/categorias` con pestañas por categoría y búsqueda
- **Detalle:** `/producto/{id}` con productos relacionados
- **Ordenar:** Formulario de pedido en `/ordenar`

## Descripción de Componentes

### MVC

**Modelo (Model):**
- `Model.php`: Clase base con funcionalidades comunes de acceso a datos
- `User.php`: Ejemplo de modelo para gestión de usuarios

**Vista (View):**
- `View.php`: Clase que renderiza las vistas con layouts
- `layouts/main.php`: Layout principal con Bootstrap
- Todas las vistas usan este layout automáticamente

**Controlador (Controller):**
- `Controller.php`: Clase base con funcionalidades comunes
- `HomeController.php`: Controlador principal que gestiona las páginas principales

### Enrutamiento

El sistema de enrutamiento automáticamente:
- Parsea la URL del navegador
- Determina qué controlador y método invocar
- Pasa parámetros si existen

### Bootstrap 5

El diseño usa Bootstrap 5 a través de CDN para:
- Diseño responsivo
- Componentes predefinidos
- Iconos de Bootstrap Icons
- Sin necesidad de descargas locales

## Personalización

### Cambiar colores y estilos
- Edita `public/css/style.css`
- El CSS usa variables para fácil personalización

### Agregar nuevas páginas
1. Crea una vista en `app/views/`
2. Agrega un método en el controlador correspondiente en `app/controllers/`
3. Accede a través de la URL: `/nombre-controlador/nombre-metodo`

### Agregar nuevos modelos
1. Crea un nuevo archivo en `app/models/`
2. Extiende de la clase `Model`
3. Usa los métodos protegidos para consultas a la base de datos

### Modificar base de datos
- Cambia las credenciales en `config/Config.php`

## Seguridad

- Contraseñas hasheadas con `password_hash` (PHP)
- Prepared statements en todos los modelos (SQL injection)
- Tokens CSRF en formularios de admin (login, productos, contenido)
- Regeneración de sesión al iniciar sesión admin
- Validación de tipo MIME en subida de imágenes; path traversal evitado al borrar imágenes
- Errores PHP ocultos en producción (`APP_ENV=production` por defecto)
- .htaccess: bloqueo de acceso a `config/`, `.env`, `.git`; cabeceras X-Content-Type-Options, X-Frame-Options, X-XSS-Protection

## Desarrollo

### Agregar dependencias (si fuera necesario)

Si deseas agregar gestión de dependencias con Composer:

```bash
composer init
composer install
```

### Depuración

Para ver errores PHP en desarrollo, define la variable de entorno `APP_ENV=development` (o en tu servidor/virtualhost). Por defecto el entorno se considera producción y no se muestran errores en pantalla.

## Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/NuevaFeature`)
3. Commit tus cambios (`git commit -m 'Agrega la nueva feature'`)
4. Push a la rama (`git push origin feature/NuevaFeature`)
5. Abre un Pull Request

## Soporte

Para reportar problemas o sugerencias, por favor abre un issue en el repositorio.

## Licencia

Este proyecto está bajo la Licencia MIT.

## Créditos

Desarrollado por Ricky Peralta
Framework MVC: PHP puro, sin dependencias externas
Frontend: Bootstrap 5