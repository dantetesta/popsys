<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="container-fluid">
    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-box text-primary me-2"></i>
            Produtos
        </h1>
        <a href="/products/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Novo Produto
        </a>
    </div>

    <!-- Filtros e Busca -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET">
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
                               placeholder="Nome do produto..."
                               value="<?= $_GET['search'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoria</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" 
                                    <?= isset($_GET['category']) && $_GET['category'] == $category['id'] ? 'selected' : '' ?>>
                                <?= $category['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="stock" class="form-label">Estoque</label>
                    <select class="form-select" id="stock" name="stock">
                        <option value="">Todos</option>
                        <option value="low" <?= isset($_GET['stock']) && $_GET['stock'] == 'low' ? 'selected' : '' ?>>
                            Baixo Estoque
                        </option>
                        <option value="out" <?= isset($_GET['stock']) && $_GET['stock'] == 'out' ? 'selected' : '' ?>>
                            Sem Estoque
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" width="5%">#</th>
                        <th scope="col" width="35%">Nome</th>
                        <th scope="col" width="15%">Categoria</th>
                        <th scope="col" width="15%">Preço</th>
                        <th scope="col" width="15%">Estoque</th>
                        <th scope="col" width="15%" class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">Nenhum produto encontrado</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="text-center"><?= $product['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?= $product['image'] ?>" 
                                                 class="rounded me-2" 
                                                 width="40" 
                                                 height="40" 
                                                 alt="<?= $product['name'] ?>">
                                        <?php else: ?>
                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?= $product['name'] ?></div>
                                            <?php if (!empty($product['description'])): ?>
                                                <small class="text-muted"><?= mb_strimwidth($product['description'], 0, 50, '...') ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= $product['category_name'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold">
                                        R$ <?= number_format($product['price'], 2, ',', '.') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($product['stock'] <= 0): ?>
                                        <span class="badge bg-danger">Sem estoque</span>
                                    <?php elseif ($product['stock'] <= 10): ?>
                                        <span class="badge bg-warning text-dark">Baixo: <?= $product['stock'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $product['stock'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#stockModal" 
                                                data-product-id="<?= $product['id'] ?>"
                                                data-product-name="<?= $product['name'] ?>"
                                                data-product-stock="<?= $product['stock'] ?>"
                                                title="Atualizar Estoque">
                                            <i class="fas fa-boxes"></i>
                                        </button>
                                        <a href="/products/<?= $product['id'] ?>" 
                                           class="btn btn-sm btn-outline-info"
                                           title="Visualizar Produto">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/products/<?= $product['id'] ?>/edit" 
                                           class="btn btn-sm btn-outline-warning"
                                           title="Editar Produto">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal" 
                                                data-product-id="<?= $product['id'] ?>"
                                                data-product-name="<?= $product['name'] ?>"
                                                title="Excluir Produto">
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

<!-- Modal de Atualização de Estoque -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-boxes text-primary me-2"></i>
                    Atualizar Estoque
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Atualizando estoque do produto: <strong id="stockProductName"></strong></p>
                <p>Estoque atual: <span id="currentStock" class="badge bg-secondary"></span></p>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantidade a adicionar/remover</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary" id="decreaseStock">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" 
                               class="form-control text-center" 
                               id="quantity" 
                               value="0">
                        <button type="button" class="btn btn-outline-secondary" id="increaseStock">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">
                        Use valores negativos para remover do estoque
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="updateStock">
                    <i class="fas fa-save me-2"></i>
                    Atualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o produto:</p>
                <p class="fw-bold" id="deleteProductName"></p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Esta ação não poderá ser desfeita!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal de Estoque
    const stockModal = document.getElementById('stockModal');
    const quantityInput = document.getElementById('quantity');
    
    stockModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const productId = button.dataset.productId;
        const productName = button.dataset.productName;
        const productStock = button.dataset.productStock;
        
        document.getElementById('stockProductName').textContent = productName;
        document.getElementById('currentStock').textContent = productStock;
        quantityInput.value = 0;
        
        // Atualizar action do formulário
        const updateButton = document.getElementById('updateStock');
        updateButton.onclick = function() {
            // Aqui você implementa a lógica de atualização do estoque
            // Pode ser uma chamada AJAX para a API
        };
    });
    
    // Botões de incremento/decremento
    document.getElementById('decreaseStock').onclick = function() {
        quantityInput.value = parseInt(quantityInput.value || 0) - 1;
    };
    
    document.getElementById('increaseStock').onclick = function() {
        quantityInput.value = parseInt(quantityInput.value || 0) + 1;
    };
    
    // Modal de Exclusão
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const productId = button.dataset.productId;
        const productName = button.dataset.productName;
        
        document.getElementById('deleteProductName').textContent = productName;
        document.getElementById('deleteForm').action = `/products/${productId}`;
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
