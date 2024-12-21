<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-clipboard-list text-primary me-2"></i>
            Relatório de Pedidos
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
        <!-- Total de Pedidos -->
        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-shopping-bag text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-0">Total de Pedidos</p>
                            <h3 class="mb-0">
                                <?= number_format($ordersData['total_orders'], 0, ',', '.') ?>
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
        <div class="col-sm-6 col-xl-4">
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
                                R$ <?= number_format($ordersData['total_amount'], 2, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                em pedidos
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ticket Médio -->
        <div class="col-sm-6 col-xl-4">
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
                                R$ <?= number_format($ordersData['total_amount'] / $ordersData['total_orders'], 2, ',', '.') ?>
                            </h3>
                            <small class="text-muted">
                                por pedido
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Gráfico de Pedidos -->
        <div class="col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Pedidos por Dia</h5>
                </div>
                <div class="card-body">
                    <canvas id="ordersChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Status dos Pedidos -->
        <div class="col-xl-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Status dos Pedidos</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dados para o gráfico de pedidos por dia
const ordersData = {
    labels: <?= json_encode(array_column($ordersByDate, 'delivery_date')) ?>,
    datasets: [{
        label: 'Pedidos',
        data: <?= json_encode(array_column($ordersByDate, 'total_amount')) ?>,
        borderColor: '#4ECDC4',
        backgroundColor: 'rgba(78, 205, 196, 0.1)',
        fill: true,
        tension: 0.4
    }]
};

// Configuração do gráfico de pedidos
const ordersConfig = {
    type: 'line',
    data: ordersData,
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

// Dados para o gráfico de status
const statusLabels = <?= json_encode(array_column($ordersByStatus, 'status')) ?>;
const statusColors = {
    'Pendente': '#FFC107',
    'Em Preparo': '#17A2B8',
    'Pronto': '#28A745',
    'Entregue': '#6C757D',
    'Cancelado': '#DC3545'
};

const statusData = {
    labels: statusLabels,
    datasets: [{
        data: <?= json_encode(array_column($ordersByStatus, 'total_orders')) ?>,
        backgroundColor: statusLabels.map(status => statusColors[status] || '#6C757D')
    }]
};

// Configuração do gráfico de status
const statusConfig = {
    type: 'doughnut',
    data: statusData,
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
const ordersChart = new Chart(
    document.getElementById('ordersChart'),
    ordersConfig
);

const statusChart = new Chart(
    document.getElementById('statusChart'),
    statusConfig
);
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
