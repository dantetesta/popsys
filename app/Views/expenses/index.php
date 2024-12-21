<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-receipt text-primary me-2"></i>
            Despesas
        </h1>
        <div class="d-flex gap-2">
            <a href="/expenses/report" class="btn btn-outline-primary">
                <i class="fas fa-chart-pie me-2"></i>
                Relatório
            </a>
            <a href="/expenses/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nova Despesa
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Busca -->
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               placeholder="Descrição ou categoria..."
                               value="<?= $filters['search'] ?? '' ?>">
                    </div>
                </div>

                <!-- Categoria -->
                <div class="col-md-2">
                    <label for="category_id" class="form-label">Categoria</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Todas</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" 
                                    <?= ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                <?= $category['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Data Inicial -->
                <div class="col-md-2">
                    <label for="start_date" class="form-label">Data Inicial</label>
                    <input type="date" 
                           class="form-control" 
                           id="start_date" 
                           name="start_date"
                           value="<?= $filters['start_date'] ?? '' ?>">
                </div>

                <!-- Data Final -->
                <div class="col-md-2">
                    <label for="end_date" class="form-label">Data Final</label>
                    <input type="date" 
                           class="form-control" 
                           id="end_date" 
                           name="end_date"
                           value="<?= $filters['end_date'] ?? '' ?>">
                </div>

                <!-- Botões -->
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>
                            Filtrar
                        </button>
                        <a href="/expenses" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Despesas -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Data</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Categoria</th>
                        <th scope="col" class="text-end">Valor</th>
                        <th scope="col" class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-receipt fa-2x mb-2"></i>
                                <p class="mb-0">Nenhuma despesa encontrada</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td>
                                    <?= date('d/m/Y', strtotime($expense['expense_date'])) ?>
                                </td>
                                <td>
                                    <div>
                                        <?= $expense['description'] ?>
                                        <?php if ($expense['notes']): ?>
                                            <i class="fas fa-info-circle text-muted ms-1" 
                                               title="<?= $expense['notes'] ?>"
                                               data-bs-toggle="tooltip"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= $expense['category_name'] ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold">
                                        R$ <?= number_format($expense['amount'], 2, ',', '.') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/expenses/<?= $expense['id'] ?>/edit" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-expense-id="<?= $expense['id'] ?>"
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Excluir Despesa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta despesa?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta ação não pode ser desfeita!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));

    // Modal de Exclusão
    const deleteModal = document.getElementById('deleteModal');
    let expenseId;

    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        expenseId = button.getAttribute('data-expense-id');
    });

    // Exclusão via AJAX
    document.getElementById('confirmDelete').addEventListener('click', function() {
        fetch(`/expenses/${expenseId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao excluir despesa: ' + data.message);
            }
        })
        .catch(error => {
            alert('Erro ao excluir despesa');
        });
    });

    // Validação das datas
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    startDate.addEventListener('change', function() {
        endDate.min = this.value;
    });

    endDate.addEventListener('change', function() {
        startDate.max = this.value;
    });
});
</script>
