<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-box text-primary me-2"></i>
            Pedidos
        </h1>
        <a href="/orders/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Novo Pedido
        </a>
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
                               placeholder="Nome do cliente, email ou telefone..."
                               value="<?= $filters['search'] ?? '' ?>">
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>
                            Pendente
                        </option>
                        <option value="confirmed" <?= ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>
                            Confirmado
                        </option>
                        <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>
                            Concluído
                        </option>
                        <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>
                            Cancelado
                        </option>
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
                        <a href="/orders" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Pedidos -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Data Entrega</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end">Total</th>
                        <th scope="col" class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">Nenhum pedido encontrado</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= $order['id'] ?></td>
                                <td>
                                    <div>
                                        <div class="fw-bold"><?= $order['customer_name'] ?></div>
                                        <?php if ($order['customer_phone']): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>
                                                <?= $order['customer_phone'] ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d/m/Y', strtotime($order['delivery_date'])) ?>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('H:i', strtotime($order['delivery_time'])) ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $statusLabels = [
                                        'pending' => ['text' => 'Pendente', 'class' => 'warning'],
                                        'confirmed' => ['text' => 'Confirmado', 'class' => 'info'],
                                        'completed' => ['text' => 'Concluído', 'class' => 'success'],
                                        'cancelled' => ['text' => 'Cancelado', 'class' => 'danger']
                                    ];
                                    $status = $statusLabels[$order['status']];
                                    ?>
                                    <span class="badge bg-<?= $status['class'] ?>">
                                        <?= $status['text'] ?>
                                    </span>
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            <?= $order['total_items'] ?> itens
                                        </small>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold">
                                        R$ <?= number_format($order['total_amount'], 2, ',', '.') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/orders/<?= $order['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#statusModal"
                                                    data-order-id="<?= $order['id'] ?>"
                                                    data-status="confirmed"
                                                    title="Confirmar Pedido">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#statusModal"
                                                    data-order-id="<?= $order['id'] ?>"
                                                    data-status="cancelled"
                                                    title="Cancelar Pedido">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php elseif ($order['status'] === 'confirmed'): ?>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#statusModal"
                                                    data-order-id="<?= $order['id'] ?>"
                                                    data-status="completed"
                                                    title="Concluir Pedido">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        <?php endif; ?>
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

<!-- Modal de Alteração de Status -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alterar Status do Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja alterar o status deste pedido?</p>
                <p class="mb-0" id="statusMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmStatus">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal de Status
    const statusModal = document.getElementById('statusModal');
    let orderId, newStatus;

    statusModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        orderId = button.getAttribute('data-order-id');
        newStatus = button.getAttribute('data-status');
        
        const statusMessages = {
            'confirmed': 'O pedido será marcado como <strong>Confirmado</strong>.',
            'completed': 'O pedido será marcado como <strong>Concluído</strong>.',
            'cancelled': 'O pedido será <strong>Cancelado</strong>. Esta ação não pode ser desfeita!'
        };

        document.getElementById('statusMessage').innerHTML = statusMessages[newStatus];
        
        const confirmButton = document.getElementById('confirmStatus');
        confirmButton.className = 'btn btn-primary';
        
        if (newStatus === 'cancelled') {
            confirmButton.classList.remove('btn-primary');
            confirmButton.classList.add('btn-danger');
        }
    });

    // Atualização de status via AJAX
    document.getElementById('confirmStatus').addEventListener('click', function() {
        fetch(`/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `status=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao atualizar status: ' + data.error);
            }
        })
        .catch(error => {
            alert('Erro ao atualizar status');
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
