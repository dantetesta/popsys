<?php require_once VIEWS_PATH . '/layouts/auth.php'; ?>

<?php ob_start(); ?>

<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-11 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="app-icon mb-3">
                            <i class="fas fa-store fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold"><?= APP_NAME ?></h4>
                        <p class="text-muted">Sistema de Gestão</p>
                    </div>

                    <?php if ($flash = get_flash()): ?>
                        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                            <?= $flash['message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="/login" method="POST" class="needs-validation" novalidate>
                        <div class="form-floating mb-3">
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   placeholder="nome@exemplo.com"
                                   value="<?= old('email') ?>"
                                   required>
                            <label for="email">E-mail</label>
                            <div class="invalid-feedback">
                                Por favor, informe um e-mail válido.
                            </div>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Sua senha"
                                   required>
                            <label for="password">Senha</label>
                            <div class="invalid-feedback">
                                Por favor, informe sua senha.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>

<?php $content = ob_get_clean(); ?>

<?php require VIEWS_PATH . '/layouts/auth.php'; ?>
