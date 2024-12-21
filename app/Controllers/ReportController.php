<?php

namespace App\Controllers;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Product;

class ReportController extends Controller {
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

    public function index() {
        $reports = [
            [
                'id' => 'sales',
                'title' => 'Relatório de Vendas',
                'description' => 'Análise detalhada das vendas por período, produtos e categorias',
                'icon' => 'shopping-cart',
                'color' => 'primary'
            ],
            [
                'id' => 'expenses',
                'title' => 'Relatório de Despesas',
                'description' => 'Controle de despesas por categoria e período',
                'icon' => 'receipt',
                'color' => 'danger'
            ],
            [
                'id' => 'products',
                'title' => 'Relatório de Produtos',
                'description' => 'Análise de estoque, produtos mais vendidos e menos vendidos',
                'icon' => 'box',
                'color' => 'success'
            ],
            [
                'id' => 'orders',
                'title' => 'Relatório de Pedidos',
                'description' => 'Acompanhamento de pedidos por status e período',
                'icon' => 'clipboard-list',
                'color' => 'warning'
            ]
        ];

        $data = [
            'reports' => $reports
        ];

        require VIEWS_PATH . '/reports/index.php';
    }

    public function sales() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        // Total de vendas do período
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

        // Vendas por dia
        $sql = "SELECT 
                    sale_date,
                    COUNT(*) as total_sales,
                    COALESCE(SUM(total_amount), 0) as total_amount,
                    COALESCE(SUM(
                        (SELECT SUM(quantity) 
                         FROM sale_items 
                         WHERE sale_id = sales.id)
                    ), 0) as total_items
                FROM sales
                WHERE sale_date BETWEEN ? AND ?
                GROUP BY sale_date
                ORDER BY sale_date";
        
        $stmt = $this->saleModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $salesByDate = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Produtos mais vendidos
        $sql = "SELECT 
                    p.id,
                    p.name,
                    pc.name as category_name,
                    COUNT(DISTINCT s.id) as total_sales,
                    SUM(si.quantity) as total_quantity,
                    SUM(si.quantity * si.unit_price) as total_amount
                FROM products p
                JOIN product_categories pc ON p.category_id = pc.id
                JOIN sale_items si ON si.product_id = p.id
                JOIN sales s ON s.id = si.sale_id
                WHERE s.sale_date BETWEEN ? AND ?
                GROUP BY p.id, p.name, pc.name
                ORDER BY total_quantity DESC
                LIMIT 10";
        
        $stmt = $this->saleModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $topProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $data = [
            'salesData' => $salesData,
            'salesByDate' => $salesByDate,
            'topProducts' => $topProducts,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        require VIEWS_PATH . '/reports/sales.php';
    }

    public function expenses() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        // Total de despesas do período
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM expenses 
                WHERE expense_date BETWEEN ? AND ?";
        $stmt = $this->expenseModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $expensesTotal = (float)$stmt->fetch(\PDO::FETCH_ASSOC)['total'];

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

        // Despesas por dia
        $sql = "SELECT 
                    expense_date,
                    COUNT(*) as total_expenses,
                    COALESCE(SUM(amount), 0) as total_amount
                FROM expenses
                WHERE expense_date BETWEEN ? AND ?
                GROUP BY expense_date
                ORDER BY expense_date";
        
        $stmt = $this->expenseModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $expensesByDate = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $data = [
            'expensesTotal' => $expensesTotal,
            'expensesByCategory' => $expensesByCategory,
            'expensesByDate' => $expensesByDate,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        require VIEWS_PATH . '/reports/expenses.php';
    }

    public function products() {
        // Produtos mais vendidos
        $sql = "SELECT 
                    p.id,
                    p.name,
                    pc.name as category_name,
                    p.stock,
                    COUNT(DISTINCT s.id) as total_sales,
                    SUM(si.quantity) as total_quantity,
                    SUM(si.quantity * si.unit_price) as total_amount
                FROM products p
                JOIN product_categories pc ON p.category_id = pc.id
                LEFT JOIN sale_items si ON si.product_id = p.id
                LEFT JOIN sales s ON s.id = si.sale_id
                GROUP BY p.id, p.name, pc.name, p.stock
                ORDER BY total_quantity DESC";
        
        $stmt = $this->productModel->getConnection()->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $data = [
            'products' => $products
        ];

        require VIEWS_PATH . '/reports/products.php';
    }

    public function orders() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        // Total de pedidos do período
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount), 0) as total_amount
                FROM orders 
                WHERE delivery_date BETWEEN ? AND ?";
        
        $stmt = $this->orderModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $ordersData = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Pedidos por status
        $sql = "SELECT 
                    status,
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount), 0) as total_amount
                FROM orders
                WHERE delivery_date BETWEEN ? AND ?
                GROUP BY status";
        
        $stmt = $this->orderModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $ordersByStatus = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Pedidos por dia
        $sql = "SELECT 
                    delivery_date,
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount), 0) as total_amount
                FROM orders
                WHERE delivery_date BETWEEN ? AND ?
                GROUP BY delivery_date
                ORDER BY delivery_date";
        
        $stmt = $this->orderModel->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $ordersByDate = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $data = [
            'ordersData' => $ordersData,
            'ordersByStatus' => $ordersByStatus,
            'ordersByDate' => $ordersByDate,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        require VIEWS_PATH . '/reports/orders.php';
    }
}
