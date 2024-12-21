<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-pie text-primary me-2"></i>
            Relatório de Despesas
        </h1>
        <div class="d-flex gap-2">
            <a href="/expenses/report?export=pdf&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" 
               class="btn btn-outline-primary"
               target="_blank">
                <i class="fas fa-file-pdf me-2"></i>
                Exportar PDF
            </a>
            <a href="/expenses" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <!-- Data Inicial -->
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Data Inicial</label>
                    <input type="date" 
                           class="form-control" 
                           id="start_date" 
                           name="start_date"
                           value="<?= $startDate ?>">
                </div>

                <!-- Data Final -->
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Data Final</label>
                    <input type="date" 
                           class="form-control" 
                           id="end_date" 
                           name="end_date"
                           value="<?= $endDate ?>">
                </div>

                <!-- Botões -->
                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>
                            Filtrar
                        </button>
                        <a href="/expenses/report" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-2"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cards de Resumo -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Resumo do Período</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Total -->
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-receipt text-primary fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-0">Total</p>
                                    <h3 class="mb-0">
                                        R$ <?= number_format($report['total'], 2, ',', '.') ?>
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <!-- Média Diária -->
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-chart-line text-success fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-0">Média Diária</p>
                                    <h3 class="mb-0">
                                        R$ <?= number_format($report['average'], 2, ',', '.') ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Despesas por Categoria</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabela Detalhada -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Detalhamento por Categoria</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Categoria</th>
                                <th class="text-end">Quantidade</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">% do Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['by_category'] as $category): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $category['category'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?= $category['total_expenses'] ?? 0 ?>
                                    </td>
                                    <td class="text-end">
                                        R$ <?= number_format($category['total_amount'] ?? 0, 2, ',', '.') ?>
                                    </td>
                                    <td class="text-end">
                                        <?php
                                        $total = $report['total'] ?? 0;
                                        $amount = $category['total_amount'] ?? 0;
                                        $percentage = $total > 0 ? ($amount / $total) * 100 : 0;
                                        echo number_format($percentage, 1) . '%';
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td class="text-end">
                                    <?= array_sum(array_column($report['by_category'], 'total_expenses')) ?>
                                </td>
                                <td class="text-end">
                                    R$ <?= number_format($report['total'], 2, ',', '.') ?>
                                </td>
                                <td class="text-end">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração de cores
    const colors = [
        '#0d6efd', // primary
        '#198754', // success
        '#ffc107', // warning
        '#dc3545', // danger
        '#0dcaf0', // info
        '#6610f2', // purple
        '#fd7e14', // orange
        '#20c997', // teal
        '#d63384', // pink
        '#6f42c1'  // indigo
    ];

    // Gráfico de Categorias
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($report['by_category'], 'category')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($report['by_category'], 'total_amount')) ?>,
                backgroundColor: colors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
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
