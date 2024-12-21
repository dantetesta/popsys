<?php

namespace App\Controllers;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Product;

/**
 * Controller responsável pelo dashboard
 */
class DashboardController extends Controller {
    private $saleModel;
    private $orderModel;
    private $expenseModel;
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->saleModel = new Sale();
        $this->orderModel = new Order();
        $this->expenseModel = new Expense();
        $this->productModel = new Product();
    }

    /**
     * Exibe o dashboard
     */
    public function index() {
        $startDate = date('Y-m-01'); // Primeiro dia do mês
        $endDate = date('Y-m-t'); // Último dia do mês
        
        // Total de vendas do mês
        $sql = "SELECT 
                    COUNT(*) as total_sales,
                    COALESCE(SUM(total_amount), 0) as total_amount,
                    COALESCE(SUM(
                        (SELECT SUM(quantity) 
                         FROM sale_items 
                         WHERE sale_id = sales.id)
                    ), 0) as total_items
                FROM sales 
                WHERE sale_date BETWEEN ? AND ?";
        
        $stmt = $this->saleModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $salesData = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Total de despesas do mês
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM expenses 
                WHERE expense_date BETWEEN ? AND ?";
        $stmt = $this->expenseModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $expensesTotal = (float)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Produtos com estoque baixo (menos de 20 unidades)
        $sql = "SELECT p.*, pc.name as category_name 
                FROM products p
                JOIN product_categories pc ON p.category_id = pc.id
                WHERE p.stock <= 20 
                ORDER BY p.stock ASC 
                LIMIT 5";
        $stmt = $this->productModel->getConnection()->prepare($sql);
        $stmt->execute();
        $lowStockProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Últimos pedidos
        $sql = "SELECT o.*, u.name as user_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC 
                LIMIT 5";
        $stmt = $this->orderModel->getConnection()->prepare($sql);
        $stmt->execute();
        $recentOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Vendas por dia do mês atual
        $sql = "SELECT 
                    sale_date,
                    COUNT(*) as total_sales,
                    COALESCE(SUM(total_amount), 0) as total_amount
                FROM sales
                WHERE sale_date BETWEEN ? AND ?
                GROUP BY sale_date
                ORDER BY sale_date";
        
        $stmt = $this->saleModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $salesByDate = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Despesas por categoria
        $sql = "SELECT 
                    ec.name as category,
                    COUNT(*) as total_expenses,
                    COALESCE(SUM(e.amount), 0) as total_amount
                FROM expenses e
                JOIN expense_categories ec ON e.category_id = ec.id
                WHERE e.expense_date BETWEEN ? AND ?
                GROUP BY ec.id, ec.name
                ORDER BY total_amount DESC";
        
        $stmt = $this->expenseModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $expensesByCategory = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $data = [
            'salesData' => $salesData,
            'expensesTotal' => $expensesTotal,
            'lowStockProducts' => $lowStockProducts,
            'recentOrders' => $recentOrders,
            'salesByDate' => $salesByDate,
            'expensesByCategory' => $expensesByCategory,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        require VIEWS_PATH . '/dashboard/index.php';
    }
}
