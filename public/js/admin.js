/**
 * Script del panel de administración
 * Usa SweetAlert2 para confirmaciones (https://sweetalert2.github.io/)
 */
function confirmarEliminar(url, accion) {
    var titulo, texto, icono;
    switch (accion) {
        case 'eliminar':
            titulo = '¿Eliminar producto?';
            texto = 'Si eliminas este producto no podras recuperarlo. Mejor recomendamos que lo desactives desde el modulo de edicion.';
            icono = 'warning';
            break;
        default:
            titulo = '¿Continuar?';
            texto = '';
            icono = 'question';
    }
    var editUrl = (accion === 'eliminar' && url) ? url.replace(/\/eliminar\//, '/editar/') : null;
    var opts = {
        title: titulo,
        text: texto,
        icon: icono,
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    };
    if (editUrl) {
        opts.showDenyButton = true;
        opts.denyButtonText = 'Ir a editar';
        opts.denyButtonColor = '#0d6efd';
    }
    Swal.fire(opts).then(function(result) {
        if (result.isConfirmed) {
            window.location.href = url;
        } else if (result.isDenied && editUrl) {
            window.location.href = editUrl;
        }
    });
    return false;
}
