<?php
namespace App\Controllers;

use App\Models\Product;

/**
 * Controller para gerenciamento de produtos
 */
class ProductController extends Controller {
    private $productModel;

    public function __construct() {
        parent::__construct(); // Chamando o construtor pai primeiro
        
        if (!$this->isAuthenticated) {
            redirect('/login');
        }
        
        $this->productModel = new Product();
    }

    /**
     * Lista todos os produtos
     */
    public function index() {
        $products = $this->productModel->getAllWithCategories();
        require VIEWS_PATH . '/products/index.php';
    }

    /**
     * Exibe formulário de criação
     */
    public function create() {
        $categories = $this->productModel->getAllCategories();
        require VIEWS_PATH . '/products/create.php';
    }

    /**
     * Salva novo produto
     */
    public function store() {
        // Validação básica
        $name = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = str_replace(',', '.', $_POST['price'] ?? '');
        $stock = (int)($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'O nome do produto é obrigatório.';
        }

        if ($category_id <= 0) {
            $errors['category_id'] = 'Selecione uma categoria válida.';
        }

        if (!is_numeric($price) || $price <= 0) {
            $errors['price'] = 'Informe um preço válido.';
        }

        if ($stock < 0) {
            $errors['stock'] = 'O estoque não pode ser negativo.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            redirect('/products/create');
            return;
        }

        $data = [
            'name' => $name,
            'category_id' => $category_id,
            'price' => (float)$price,
            'stock' => $stock,
            'description' => $description
        ];

        try {
            $this->productModel->create($data);
            flash('Produto criado com sucesso!', 'success');
            redirect('/products');
        } catch (\Exception $e) {
            flash('Erro ao criar produto. Tente novamente.', 'error');
            redirect('/products/create');
        }
    }

    /**
     * Exibe detalhes do produto
     */
    public function show($id) {
        $product = $this->productModel->findWithCategory($id);
        if (!$product) {
            flash('Produto não encontrado.', 'error');
            redirect('/products');
        }
        require VIEWS_PATH . '/products/show.php';
    }

    /**
     * Exibe formulário de edição
     */
    public function edit($id) {
        $product = $this->productModel->find($id);
        if (!$product) {
            flash('Produto não encontrado.', 'error');
            redirect('/products');
        }
        
        $categories = $this->productModel->getAllCategories();
        require VIEWS_PATH . '/products/edit.php';
    }

    /**
     * Atualiza produto existente
     */
    public function update($id) {
        // Validação básica
        $name = trim($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = str_replace(',', '.', $_POST['price'] ?? '');
        $stock = (int)($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'O nome do produto é obrigatório.';
        }

        if ($category_id <= 0) {
            $errors['category_id'] = 'Selecione uma categoria válida.';
        }

        if (!is_numeric($price) || $price <= 0) {
            $errors['price'] = 'Informe um preço válido.';
        }

        if ($stock < 0) {
            $errors['stock'] = 'O estoque não pode ser negativo.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            redirect("/products/$id/edit");
            return;
        }

        $data = [
            'name' => $name,
            'category_id' => $category_id,
            'price' => (float)$price,
            'stock' => $stock,
            'description' => $description
        ];

        try {
            $this->productModel->update($id, $data);
            flash('Produto atualizado com sucesso!', 'success');
            redirect('/products');
        } catch (\Exception $e) {
            flash('Erro ao atualizar produto. Tente novamente.', 'error');
            redirect("/products/$id/edit");
        }
    }

    /**
     * Remove produto
     */
    public function destroy($id) {
        try {
            $this->productModel->delete($id);
            flash('Produto removido com sucesso!', 'success');
        } catch (\Exception $e) {
            flash('Erro ao remover produto. Tente novamente.', 'error');
        }
        redirect('/products');
    }

    /**
     * Atualiza estoque do produto
     */
    public function updateStock($id) {
        $quantity = (int)($_POST['quantity'] ?? 0);
        
        try {
            $product = $this->productModel->find($id);
            if (!$product) {
                throw new \Exception('Produto não encontrado.');
            }

            $newStock = $product['stock'] + $quantity;
            if ($newStock < 0) {
                throw new \Exception('Estoque não pode ficar negativo.');
            }

            $this->productModel->update($id, ['stock' => $newStock]);
            flash('Estoque atualizado com sucesso!', 'success');
        } catch (\Exception $e) {
            flash($e->getMessage(), 'error');
        }

        redirect('/products');
    }
}
