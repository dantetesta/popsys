<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-line text-primary me-2"></i>
            Relatório de Vendas
        </h1>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-12 col-md-5">
                    <label for="start_date" class="form-label">Data Inicial</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?= htmlspecialchars($startDate) ?>">
                </div>
                <div class="col-12 col-md-5">
                    <label for="end_date" class="form-label">Data Final</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?= htmlspecialchars($endDate) ?>">
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row g-4 mb-4">
        <!-- Vendas -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-shopping-cart text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Total de Vendas</p>
                            <h3 class="mb-0">
                                <?= number_format($salesData['total_sales'], 0, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                no período
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
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-dollar-sign text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Valor Total</p>
                            <h3 class="mb-0">
                                R$ <?= number_format($salesData['total_amount'], 2, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                em vendas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Médio -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-receipt text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Ticket Médio</p>
                            <h3 class="mb-0">
                                R$ <?= number_format($salesData['total_amount'] / $salesData['total_sales'], 2, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                por venda
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de Itens -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-box text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Total de Itens</p>
                            <h3 class="mb-0">
                                <?= number_format($salesData['total_items'], 0, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                vendidos
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Gráfico de Vendas -->
        <div class="col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Vendas por Dia</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Produtos Mais Vendidos -->
        <div class="col-xl-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Top 10 Produtos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Qtd</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($product['name']) ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($product['category_name']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= number_format($product['total_quantity'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        R$ <?= number_format($product['total_amount'], 2, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dados para o gráfico de vendas
const salesData = {
    labels: <?= json_encode(array_column($salesByDate, 'sale_date')) ?>,
    datasets: [{
        label: 'Vendas',
        data: <?= json_encode(array_column($salesByDate, 'total_amount')) ?>,
        borderColor: '#FF6B6B',
        backgroundColor: 'rgba(255, 107, 107, 0.1)',
        fill: true,
        tension: 0.4
    }]
};

// Configuração do gráfico de vendas
const salesConfig = {
    type: 'line',
    data: salesData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toFixed(2);
                    }
                }
            }
        }
    }
};

// Inicializa o gráfico
const salesChart = new Chart(
    document.getElementById('salesChart'),
    salesConfig
);
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
