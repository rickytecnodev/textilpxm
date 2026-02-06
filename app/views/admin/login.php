<div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="card shadow-sm w-100" style="max-width: 400px;">
        <div class="card-body p-4">
            <h1 class="h4 fw-bold mb-1 text-center">Administración</h1>
            <p class="text-muted small mb-4 text-center">Acceso solo para administradores</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo BASE_URL; ?>/admin/login">
                <?php if (!empty($csrf_token)): ?>
                <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required
                           value="<?php echo htmlspecialchars(isset($email) ? $email : ($_POST['email'] ?? '')); ?>"
                           autocomplete="email" placeholder="admin@textilpxm.com">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required
                           autocomplete="current-password" placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-dark w-100">Entrar</button>
                <button type="button" class="btn btn-link w-100" onclick="window.location.href='<?php echo BASE_URL; ?>'">Volver a la página principal</button>
            </form>
        </div>
    </div>
</div>
