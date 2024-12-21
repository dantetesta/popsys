<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-user-circle text-primary me-2"></i>
            Meu Perfil
        </h1>
    </div>

    <div class="row">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="/profile" method="POST" class="needs-validation" novalidate>
                        <!-- Nome -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" 
                                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?= htmlspecialchars($user['name']) ?>" 
                                   required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['name'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" 
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($user['email']) ?>" 
                                   required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback">
                                    <?= $errors['email'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Alterar Senha</h5>
                        <p class="text-muted small mb-3">
                            Preencha apenas se desejar alterar sua senha. 
                            <strong class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Por segurança, você será desconectado após alterar a senha.
                            </strong>
                        </p>

                        <!-- Nova Senha -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nova Senha</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" 
                                       id="new_password" 
                                       name="new_password"
                                       minlength="6">
                                <button class="btn btn-outline-secondary toggle-password" 
                                        type="button" 
                                        data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <?php if (isset($errors['new_password'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $errors['new_password'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Confirmar Nova Senha -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                       id="confirm_password" 
                                       name="confirm_password"
                                       minlength="6">
                                <button class="btn btn-outline-secondary toggle-password" 
                                        type="button" 
                                        data-target="confirm_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback d-block">
                                    <?= $errors['confirm_password'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Dicas de Segurança -->
        <div class="col-md-4 col-lg-6">
            <div class="card shadow-sm bg-light border-0">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Dicas de Segurança
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Use uma senha forte com pelo menos 6 caracteres
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Combine letras maiúsculas e minúsculas
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Inclua números e caracteres especiais
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Evite usar informações pessoais na senha
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmPasswordChange" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirmar Alteração de Senha
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Você está prestes a alterar sua senha. Por motivos de segurança, você será desconectado do sistema e precisará fazer login novamente com sua nova senha.</p>
                <p class="mb-0">Deseja continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmChange">
                    <i class="fas fa-check me-2"></i>
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle de visibilidade das senhas
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });

    // Validação do formulário
    const form = document.querySelector('.needs-validation');
    const newPassword = document.getElementById('new_password');
    const modal = new bootstrap.Modal(document.getElementById('confirmPasswordChange'));
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        
        // Se estiver alterando a senha, mostra o modal de confirmação
        if (newPassword.value.trim() !== '') {
            event.preventDefault();
            modal.show();
            return;
        }
        
        form.classList.add('was-validated');
    }, false);
    
    // Confirmação de alteração de senha
    document.getElementById('confirmChange').addEventListener('click', function() {
        modal.hide();
        form.submit();
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
