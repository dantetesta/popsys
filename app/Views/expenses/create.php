<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-receipt text-primary me-2"></i>
            Nova Despesa
        </h1>
        <a href="/expenses" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <!-- Formulário -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="/expenses" method="POST" id="expenseForm" class="row g-3">
                <!-- Categoria -->
                <div class="col-md-4">
                    <label for="category_id" class="form-label">Categoria *</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Selecione uma categoria</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>">
                                <?= $category['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Data -->
                <div class="col-md-4">
                    <label for="expense_date" class="form-label">Data *</label>
                    <input type="date" 
                           class="form-control" 
                           id="expense_date" 
                           name="expense_date"
                           required
                           max="<?= date('Y-m-d') ?>">
                </div>

                <!-- Valor -->
                <div class="col-md-4">
                    <label for="amount" class="form-label">Valor *</label>
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" 
                               class="form-control" 
                               id="amount" 
                               name="amount"
                               required
                               placeholder="0,00">
                    </div>
                </div>

                <!-- Descrição -->
                <div class="col-12">
                    <label for="description" class="form-label">Descrição *</label>
                    <input type="text" 
                           class="form-control" 
                           id="description" 
                           name="description"
                           required
                           maxlength="255"
                           placeholder="Ex: Material de escritório">
                </div>

                <!-- Observações -->
                <div class="col-12">
                    <label for="notes" class="form-label">
                        Observações
                        <small class="text-muted">(opcional)</small>
                    </label>
                    <textarea class="form-control" 
                              id="notes" 
                              name="notes"
                              rows="3"
                              maxlength="1000"
                              placeholder="Detalhes adicionais sobre a despesa..."></textarea>
                </div>

                <!-- Botões -->
                <div class="col-12">
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="/expenses" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- IMask.js -->
<script src="https://unpkg.com/imask"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para valor monetário
    IMask(document.getElementById('amount'), {
        mask: Number,
        scale: 2,
        thousandsSeparator: '.',
        padFractionalZeros: true,
        normalizeZeros: true,
        radix: ',',
        mapToRadix: ['.']
    });

    // Validação do formulário
    const form = document.getElementById('expenseForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Valida categoria
        const categoryId = document.getElementById('category_id').value;
        if (!categoryId) {
            alert('Por favor, selecione uma categoria.');
            return;
        }

        // Valida data
        const expenseDate = document.getElementById('expense_date').value;
        if (!expenseDate) {
            alert('Por favor, selecione uma data.');
            return;
        }

        // Valida valor
        const amount = document.getElementById('amount').value;
        if (!amount || parseFloat(amount.replace('.', '').replace(',', '.')) <= 0) {
            alert('Por favor, informe um valor válido.');
            return;
        }

        // Valida descrição
        const description = document.getElementById('description').value.trim();
        if (!description) {
            alert('Por favor, informe uma descrição.');
            return;
        }

        // Envia o formulário
        form.submit();
    });

    // Define a data máxima como hoje
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('expense_date').max = today;
    
    // Define a data padrão como hoje
    document.getElementById('expense_date').value = today;
});
</script>
