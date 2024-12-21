<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-box text-primary me-2"></i>
            Detalhes do Produto
        </h1>
        <div class="btn-group">
            <a href="/products" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
            <a href="/products/<?= $product['id'] ?>/edit" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                Editar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações Básicas -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Informações do Produto</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Nome</h6>
                            <p class="h5"><?= $product['name'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Categoria</h6>
                            <span class="badge bg-info fs-6">
                                <?= $product['category_name'] ?>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Preço</h6>
                            <p class="h5 text-primary">
                                R$ <?= number_format($product['price'], 2, ',', '.') ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Estoque</h6>
                            <?php if ($product['stock'] <= 0): ?>
                                <span class="badge bg-danger fs-6">Sem estoque</span>
                            <?php elseif ($product['stock'] <= 10): ?>
                                <span class="badge bg-warning fs-6">Baixo: <?= $product['stock'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-success fs-6"><?= $product['stock'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <h6 class="text-muted mb-1">Descrição</h6>
                            <p class="mb-0"><?= $product['description'] ?: 'Nenhuma descrição disponível.' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="col-md-4">
            <!-- Vendas -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Estatísticas de Vendas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-shopping-cart text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Total Vendido</h6>
                            <h4 class="mb-0">
                                <?= $totalSold ?? 0 ?> unidades
                            </h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-dollar-sign text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Faturamento</h6>
                            <h4 class="mb-0">
                                R$ <?= number_format($totalRevenue ?? 0, 2, ',', '.') ?>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Movimentações Recentes -->
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Movimentações Recentes</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($recentMovements)): ?>
                        <div class="list-group-item text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">Nenhuma movimentação recente</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentMovements as $movement): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <?php if ($movement['type'] === 'sale'): ?>
                                            <i class="fas fa-shopping-cart text-success me-2"></i>
                                            Venda
                                        <?php else: ?>
                                            <i class="fas fa-boxes text-primary me-2"></i>
                                            Estoque
                                        <?php endif; ?>
                                    </h6>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($movement['date'])) ?>
                                    </small>
                                </div>
                                <p class="mb-1">
                                    <?= $movement['description'] ?>
                                </p>
                                <small class="text-muted">
                                    Quantidade: <?= $movement['quantity'] ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
