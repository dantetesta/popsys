<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-tachometer-alt text-primary me-2"></i>
            Dashboard
        </h1>
        <div class="text-muted">
            <?= date('F Y') ?>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row g-3 mb-4">
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
                            <p class="text-muted mb-0">Vendas do Mês</p>
                            <h3 class="mb-0">
                                R$ <?= number_format($salesData['total_amount'], 2, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                <?= $salesData['total_sales'] ?> vendas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itens Vendidos -->
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
                            <p class="text-muted mb-0">Itens Vendidos</p>
                            <h3 class="mb-0">
                                <?= $salesData['total_items'] ?>
                            </h3>
                            <small class="text-muted">
                                unidades
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Despesas -->
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="fas fa-receipt text-danger fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Despesas do Mês</p>
                            <h3 class="mb-0">
                                R$ <?= number_format($expensesTotal, 2, ',', '.') ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lucro -->
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
                            <p class="text-muted mb-0">Lucro do Mês</p>
                            <h3 class="mb-0">
                                R$ <?= number_format($salesData['total_amount'] - $expensesTotal, 2, ',', '.') ?>
                            </h3>
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

        <!-- Gráfico de Despesas -->
        <div class="col-xl-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Despesas por Categoria</h5>
                </div>
                <div class="card-body">
                    <canvas id="expensesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Produtos com Estoque Baixo -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Produtos com Estoque Baixo</h5>
                    <a href="/products" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th class="text-end">Estoque</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStockProducts as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                                    <td class="text-end">
                                        <span class="badge bg-warning">
                                            <?= $product['stock'] ?> un
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Pedidos -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Últimos Pedidos</h5>
                    <a href="/orders" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                                    <td>
                                        <?php
                                        $statusClasses = [
                                            'pending' => 'warning',
                                            'confirmed' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusNames = [
                                            'pending' => 'Pendente',
                                            'confirmed' => 'Confirmado',
                                            'completed' => 'Concluído',
                                            'cancelled' => 'Cancelado'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusClasses[$order['status']] ?>">
                                            <?= $statusNames[$order['status']] ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        R$ <?= number_format($order['total_amount'], 2, ',', '.') ?>
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

// Dados para o gráfico de despesas
const expensesData = {
    labels: <?= json_encode(array_column($expensesByCategory, 'category')) ?>,
    datasets: [{
        data: <?= json_encode(array_column($expensesByCategory, 'total_amount')) ?>,
        backgroundColor: [
            '#FF6B6B',
            '#4ECDC4',
            '#45B7D1',
            '#96CEB4',
            '#FFEEAD'
        ]
    }]
};

// Configuração do gráfico de despesas
const expensesConfig = {
    type: 'doughnut',
    data: expensesData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
};

// Inicializa os gráficos
const salesChart = new Chart(
    document.getElementById('salesChart'),
    salesConfig
);

const expensesChart = new Chart(
    document.getElementById('expensesChart'),
    expensesConfig
);
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
