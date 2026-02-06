<?php $page_title = $page_title ?? 'Iniciar Sesión'; ?>
<section class="py-5" style="margin-top: 6rem;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h4 fw-bold mb-3 text-center">Iniciar sesión</h1>
                        <?php require APP_PATH . '/views/partials/_flash_alert.php'; ?>
                        <form method="post" action="<?php echo BASE_URL; ?>/login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" autocomplete="email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-dark w-100">Entrar</button>
                        </form>
                        <p class="mt-3 mb-0 text-center small text-muted">
                            ¿No tienes cuenta? <a href="<?php echo BASE_URL; ?>/register">Regístrate</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
