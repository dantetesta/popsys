<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
            Relatório de Despesas
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
        <!-- Total de Despesas -->
        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="fas fa-dollar-sign text-danger fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Total de Despesas</p>
                            <h3 class="mb-0">
                                R$ <?= number_format($expensesTotal, 2, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                no período
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Média por Dia -->
        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-calendar-day text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Média por Dia</p>
                            <h3 class="mb-0">
                                R$ <?= number_format($expensesTotal / max(1, count($expensesByDate)), 2, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                em despesas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categorias -->
        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-tags text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Categorias</p>
                            <h3 class="mb-0">
                                <?= count($expensesByCategory) ?>
                            </h3>
                            <small class="text-muted">
                                com despesas
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Gráfico de Despesas -->
        <div class="col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Despesas por Dia</h5>
                </div>
                <div class="card-body">
                    <canvas id="expensesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Despesas por Categoria -->
        <div class="col-xl-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Por Categoria</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Categoria</th>
                                    <th>Qtd</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expensesByCategory as $category): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($category['category']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= number_format($category['total_expenses'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        R$ <?= number_format($category['total_amount'], 2, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Categorias -->
        <div class="col-xl-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Distribuição por Categoria</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dados para o gráfico de despesas por dia
const expensesData = {
    labels: <?= json_encode(array_column($expensesByDate, 'expense_date')) ?>,
    datasets: [{
        label: 'Despesas',
        data: <?= json_encode(array_column($expensesByDate, 'total_amount')) ?>,
        borderColor: '#FF6B6B',
        backgroundColor: 'rgba(255, 107, 107, 0.1)',
        fill: true,
        tension: 0.4
    }]
};

// Configuração do gráfico de despesas
const expensesConfig = {
    type: 'line',
    data: expensesData,
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

// Dados para o gráfico de categorias
const categoryData = {
    labels: <?= json_encode(array_column($expensesByCategory, 'category')) ?>,
    datasets: [{
        data: <?= json_encode(array_column($expensesByCategory, 'total_amount')) ?>,
        backgroundColor: [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEEAD',
            '#D4A5A5', '#9FA8DA', '#90CAF9', '#A5D6A7', '#FFCC80'
        ]
    }]
};

// Configuração do gráfico de categorias
const categoryConfig = {
    type: 'doughnut',
    data: categoryData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
};

// Inicializa os gráficos
const expensesChart = new Chart(
    document.getElementById('expensesChart'),
    expensesConfig
);

const categoryChart = new Chart(
    document.getElementById('categoryChart'),
    categoryConfig
);
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
