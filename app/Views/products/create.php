<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-box text-primary me-2"></i>
            Novo Produto
        </h1>
        <a href="/products" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <!-- Formulário -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="/products" method="POST" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <!-- Nome do Produto -->
                            <div class="col-md-8">
                                <label for="name" class="form-label">Nome do Produto *</label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?= $_POST['name'] ?? '' ?>"
                                       required 
                                       autofocus>
                                <div class="invalid-feedback">
                                    <?= $errors['name'] ?? 'Por favor, informe o nome do produto.' ?>
                                </div>
                            </div>

                            <!-- Categoria -->
                            <div class="col-md-4">
                                <label for="category_id" class="form-label">Categoria *</label>
                                <select class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                                <?= (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                            <?= $category['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= $errors['category_id'] ?? 'Por favor, selecione uma categoria.' ?>
                                </div>
                            </div>

                            <!-- Preço -->
                            <div class="col-md-6">
                                <label for="price" class="form-label">Preço *</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="text" 
                                           class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                                           id="price" 
                                           name="price" 
                                           value="<?= $_POST['price'] ?? '' ?>"
                                           required>
                                    <div class="invalid-feedback">
                                        <?= $errors['price'] ?? 'Por favor, informe o preço do produto.' ?>
                                    </div>
                                </div>
                                <div class="form-text">Use ponto para decimais (ex: 10.99)</div>
                            </div>

                            <!-- Estoque -->
                            <div class="col-md-6">
                                <label for="stock" class="form-label">Estoque Inicial</label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control <?= isset($errors['stock']) ? 'is-invalid' : '' ?>" 
                                           id="stock" 
                                           name="stock" 
                                           value="<?= $_POST['stock'] ?? '0' ?>" 
                                           min="0">
                                    <span class="input-group-text">unidades</span>
                                    <div class="invalid-feedback">
                                        <?= $errors['stock'] ?? 'O estoque não pode ser negativo.' ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Descrição -->
                            <div class="col-12">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                          id="description" 
                                          name="description" 
                                          rows="3"
                                          placeholder="Descreva o produto..."><?= $_POST['description'] ?? '' ?></textarea>
                                <div class="invalid-feedback">
                                    <?= $errors['description'] ?? '' ?>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div class="col-12">
                                <hr class="my-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-2"></i>
                                        Limpar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        Salvar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card de Dicas -->
        <div class="col-lg-4">
            <div class="card shadow-sm bg-light border-0">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Dicas
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Escolha um nome claro e descritivo
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Selecione a categoria mais apropriada
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Use preços com até 2 casas decimais
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Adicione uma descrição detalhada
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Máscara para preço
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Remove tudo que não é número ou ponto
        value = value.replace(/[^\d.]/g, '');
        
        // Garante apenas um ponto decimal
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Limita a 2 casas decimais
        if (parts.length > 1) {
            value = parts[0] + '.' + parts[1].slice(0, 2);
        }
        
        e.target.value = value;
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
