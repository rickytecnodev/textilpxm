<?php
/**
 * Partial: alerta de mensaje flash.
 * Variables: $flash_message, $flash_type (default 'success'), $dismissible (default true), $alert_class (opcional, ej. 'm-1')
 */
$flash_message = $flash_message ?? null;
$flash_type = $flash_type ?? 'success';
$dismissible = $dismissible ?? true;
$alert_class = $alert_class ?? '';
if (empty($flash_message)) {
    return;
}
$typeClass = ($flash_type === 'danger') ? 'danger' : 'success';
?>
<div class="alert alert-<?php echo $typeClass; ?> alert-dismissible fade show <?php echo htmlspecialchars($alert_class); ?>" role="alert">
    <?php echo htmlspecialchars($flash_message); ?>
    <?php if ($dismissible): ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    <?php endif; ?>
</div>
