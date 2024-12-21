<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-box text-primary me-2"></i>
            Editar Produto
        </h1>
        <a href="/products" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <!-- Formulário -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="/products/<?= $product['id'] ?>" method="POST" class="needs-validation" novalidate>
                <div class="row g-3">
                    <!-- Nome do Produto -->
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nome do Produto *</label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               value="<?= $product['name'] ?>"
                               required 
                               autofocus>
                        <div class="invalid-feedback">
                            Por favor, informe o nome do produto.
                        </div>
                    </div>

                    <!-- Categoria -->
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Categoria *</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                        <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                    <?= $category['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecione uma categoria.
                        </div>
                    </div>

                    <!-- Preço -->
                    <div class="col-md-6">
                        <label for="price" class="form-label">Preço *</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" 
                                   class="form-control" 
                                   id="price" 
                                   name="price" 
                                   value="<?= number_format($product['price'], 2, ',', '.') ?>"
                                   required 
                                   data-mask="currency">
                        </div>
                        <div class="invalid-feedback">
                            Por favor, informe o preço do produto.
                        </div>
                    </div>

                    <!-- Estoque -->
                    <div class="col-md-6">
                        <label for="stock" class="form-label">Estoque</label>
                        <input type="number" 
                               class="form-control" 
                               id="stock" 
                               name="stock" 
                               value="<?= $product['stock'] ?>" 
                               min="0">
                    </div>

                    <!-- Descrição -->
                    <div class="col-12">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="3"><?= $product['description'] ?></textarea>
                    </div>

                    <!-- Botões -->
                    <div class="col-12">
                        <hr class="my-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/products" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.4.3/imask.min.js"></script>
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

    // Máscara para o campo de preço
    const priceElement = document.getElementById('price');
    IMask(priceElement, {
        mask: 'num',
        blocks: {
            num: {
                mask: Number,
                scale: 2,
                thousandsSeparator: '.',
                padFractionalZeros: true,
                radix: ',',
                mapToRadix: ['.']
            }
        }
    });
});
</script>
