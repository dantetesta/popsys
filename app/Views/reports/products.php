<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-box text-primary me-2"></i>
            Relatório de Produtos
        </h1>
    </div>

    <!-- Cards de Resumo -->
    <div class="row g-4 mb-4">
        <!-- Total de Produtos -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-boxes text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Total de Produtos</p>
                            <h3 class="mb-0">
                                <?= count($products) ?>
                            </h3>
                            <small class="text-muted">
                                cadastrados
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total em Estoque -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-warehouse text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Total em Estoque</p>
                            <h3 class="mb-0">
                                <?= number_format(array_sum(array_column($products, 'stock')), 0, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                unidades
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de Vendas -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-shopping-cart text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Total de Vendas</p>
                            <h3 class="mb-0">
                                <?= number_format(array_sum(array_column($products, 'total_quantity')), 0, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                unidades vendidas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valor Total -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-dollar-sign text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Valor Total</p>
                            <h3 class="mb-0">
                                R$ <?= number_format(array_sum(array_column($products, 'total_amount')), 2, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                em vendas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Produtos -->
    <div class="card shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="card-title mb-0">Lista de Produtos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="productsTable">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th class="text-center">Estoque</th>
                            <th class="text-center">Vendas</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($product['name']) ?>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($product['stock'] <= 10): ?>
                                    <span class="badge bg-danger">
                                        <?= number_format($product['stock'], 0, ',', '.') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success">
                                        <?= number_format($product['stock'], 0, ',', '.') ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?= number_format($product['total_quantity'] ?? 0, 0, ',', '.') ?>
                            </td>
                            <td class="text-end">
                                R$ <?= number_format($product['total_amount'] ?? 0, 2, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        },
        order: [[3, 'desc']], // Ordena por vendas (descendente)
        pageLength: 25
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
