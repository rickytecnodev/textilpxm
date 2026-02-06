/**
 * Página Ordenar: envío del pedido por WhatsApp y redirección al cambiar producto.
 * El formulario debe tener id="orderForm", data-whatsapp-number (número sin +) y opcional data-base-url.
 */
(function() {
    'use strict';

    var form = document.getElementById('orderForm');
    if (!form) return;

    var baseUrl = document.body.dataset.baseUrl || '';
    var whatsappNumber = form.dataset.whatsappNumber || '';

    window.enviarWhatsApp = function(event) {
        if (event) event.preventDefault();

        var nombre = (document.getElementById('inputName') && document.getElementById('inputName').value) ? document.getElementById('inputName').value.trim() : '';
        var email = (document.getElementById('inputEmail') && document.getElementById('inputEmail').value) ? document.getElementById('inputEmail').value.trim() : '';
        var telefono = (document.getElementById('inputPhone') && document.getElementById('inputPhone').value) ? document.getElementById('inputPhone').value.trim() : '';
        var ciudad = (document.getElementById('inputCity') && document.getElementById('inputCity').value) ? document.getElementById('inputCity').value.trim() : '';
        var mensaje = (document.getElementById('inputMessage') && document.getElementById('inputMessage').value) ? document.getElementById('inputMessage').value.trim() : '';
        var tallaSelect = document.getElementById('inputSize');
        var talla = (tallaSelect && tallaSelect.options[tallaSelect.selectedIndex]) ? tallaSelect.options[tallaSelect.selectedIndex].value : 'No especificada';

        var productSelect = document.getElementById('productSelect');
        var selectedOption = productSelect && productSelect.options[productSelect.selectedIndex];
        var productoNombre = (selectedOption && selectedOption.dataset.nombre) ? selectedOption.dataset.nombre : 'No especificado';
        var productoPrecio = (selectedOption && selectedOption.dataset.precio) ? selectedOption.dataset.precio : 'Por definir';

        if (!nombre || !email || !telefono || !productSelect.value) {
            alert('Por favor completa todos los campos requeridos');
            return false;
        }

        var whatsappMessage = '🛒 *NUEVO PEDIDO - OAXACA TEXTILES*\n\n';
        whatsappMessage += '━━━━━━━━━━━━━━━━━━━━\n';
        whatsappMessage += '📦 *PRODUCTO*\n';
        whatsappMessage += '• Artículo: ' + productoNombre + '\n';
        whatsappMessage += '• Precio: $' + productoPrecio + ' MXN\n';
        whatsappMessage += '• Talla: ' + talla + '\n';
        whatsappMessage += '━━━━━━━━━━━━━━━━━━━━\n';
        whatsappMessage += '👤 *DATOS DEL CLIENTE*\n';
        whatsappMessage += '• Nombre: ' + nombre + '\n';
        whatsappMessage += '• Teléfono: ' + telefono + '\n';
        whatsappMessage += '• Email: ' + email + '\n';
        if (ciudad) {
            whatsappMessage += '• Ciudad: ' + ciudad + '\n';
        }
        if (mensaje) {
            whatsappMessage += '━━━━━━━━━━━━━━━━━━━━\n';
            whatsappMessage += '💬 *MENSAJE*\n';
            whatsappMessage += mensaje + '\n';
        }
        whatsappMessage += '━━━━━━━━━━━━━━━━━━━━\n';
        whatsappMessage += '📅 Fecha: ' + new Date().toLocaleDateString('es-MX') + '\n';
        whatsappMessage += '🌐 Enviado desde: oaxacatextiles.mx';

        var encodedMessage = encodeURIComponent(whatsappMessage);
        var num = whatsappNumber.replace(/\D/g, '');
        if (!num) {
            alert('No está configurado el número de WhatsApp para pedidos.');
            return false;
        }
        window.open('https://wa.me/' + num + '?text=' + encodedMessage, '_blank');
        return false;
    };

    document.addEventListener('DOMContentLoaded', function() {
        var productSelect = document.getElementById('productSelect');
        if (productSelect && baseUrl) {
            productSelect.addEventListener('change', function() {
                var productId = this.value;
                if (productId && productId !== 'otro' && productId !== '') {
                    window.location.href = baseUrl + '/ordenar?producto=' + productId;
                }
            });
        }
    });
})();
