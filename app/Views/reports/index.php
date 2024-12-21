<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-bar text-primary me-2"></i>
            Relatórios
        </h1>
    </div>

    <div class="row g-4">
        <?php foreach ($reports as $report): ?>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-<?= $report['color'] ?> bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-<?= $report['icon'] ?> text-<?= $report['color'] ?> fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1"><?= $report['title'] ?></h5>
                                <p class="card-text text-muted small mb-0">
                                    <?= $report['description'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="d-grid">
                            <a href="/reports/<?= $report['id'] ?>" class="btn btn-<?= $report['color'] ?>">
                                <i class="fas fa-eye me-2"></i>
                                Visualizar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
