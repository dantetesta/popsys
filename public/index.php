<?php

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Carrega as configurações
require_once __DIR__ . '/../config/config.php';

// Inicializa o router
$router = new \AltoRouter();
$router->setBasePath('');

// Rotas de Autenticação
$router->map('GET', '/login', ['App\Controllers\AuthController', 'loginForm'], 'auth.login');
$router->map('POST', '/login', ['App\Controllers\AuthController', 'login'], 'auth.login.post');
$router->map('GET', '/logout', ['App\Controllers\AuthController', 'logout'], 'auth.logout');

// Rotas do Dashboard
$router->map('GET', '/', ['App\Controllers\DashboardController', 'index'], 'home');
$router->map('GET', '/dashboard', ['App\Controllers\DashboardController', 'index'], 'dashboard');

// Rotas de Produtos
$router->map('GET', '/products', ['App\Controllers\ProductController', 'index'], 'products.index');
$router->map('GET', '/products/create', ['App\Controllers\ProductController', 'create'], 'products.create');
$router->map('POST', '/products', ['App\Controllers\ProductController', 'store'], 'products.store');
$router->map('GET', '/products/[i:id]', ['App\Controllers\ProductController', 'show'], 'products.show');
$router->map('GET', '/products/[i:id]/edit', ['App\Controllers\ProductController', 'edit'], 'products.edit');
$router->map('POST', '/products/[i:id]', ['App\Controllers\ProductController', 'update'], 'products.update');
$router->map('DELETE', '/products/[i:id]', ['App\Controllers\ProductController', 'destroy'], 'products.destroy');

// Rotas de Pedidos
$router->map('GET', '/orders', ['App\Controllers\OrderController', 'index'], 'orders.index');
$router->map('GET', '/orders/create', ['App\Controllers\OrderController', 'create'], 'orders.create');
$router->map('POST', '/orders', ['App\Controllers\OrderController', 'store'], 'orders.store');
$router->map('GET', '/orders/[i:id]', ['App\Controllers\OrderController', 'show'], 'orders.show');
$router->map('POST', '/orders/[i:id]/status', ['App\Controllers\OrderController', 'updateStatus'], 'orders.status');

// Rotas de Vendas
$router->map('GET', '/sales', ['App\Controllers\SaleController', 'index'], 'sales.index');
$router->map('GET', '/sales/create', ['App\Controllers\SaleController', 'create'], 'sales.create');
$router->map('POST', '/sales', ['App\Controllers\SaleController', 'store'], 'sales.store');
$router->map('GET', '/sales/[i:id]', ['App\Controllers\SaleController', 'show'], 'sales.show');

// Rotas de Despesas
$router->map('GET', '/expenses', ['App\Controllers\ExpenseController', 'index'], 'expenses.index');
$router->map('GET', '/expenses/create', ['App\Controllers\ExpenseController', 'create'], 'expenses.create');
$router->map('POST', '/expenses', ['App\Controllers\ExpenseController', 'store'], 'expenses.store');
$router->map('GET', '/expenses/[i:id]', ['App\Controllers\ExpenseController', 'show'], 'expenses.show');
$router->map('GET', '/expenses/[i:id]/edit', ['App\Controllers\ExpenseController', 'edit'], 'expenses.edit');
$router->map('POST', '/expenses/[i:id]', ['App\Controllers\ExpenseController', 'update'], 'expenses.update');
$router->map('DELETE', '/expenses/[i:id]', ['App\Controllers\ExpenseController', 'destroy'], 'expenses.destroy');
$router->map('GET', '/expenses/report', ['App\Controllers\ExpenseController', 'report'], 'expenses.report');

// Rotas de Perfil
$router->map('GET', '/profile', ['App\Controllers\ProfileController', 'edit'], 'profile.edit');
$router->map('POST', '/profile', ['App\Controllers\ProfileController', 'update'], 'profile.update');

// Rotas de Relatórios
$router->map('GET', '/reports', ['App\Controllers\ReportController', 'index'], 'reports.index');
$router->map('GET', '/reports/sales', ['App\Controllers\ReportController', 'sales'], 'reports.sales');
$router->map('GET', '/reports/expenses', ['App\Controllers\ReportController', 'expenses'], 'reports.expenses');
$router->map('GET', '/reports/export/[*:type]', ['App\Controllers\ReportController', 'export'], 'reports.export');

// Match da rota atual
$match = $router->match();

if ($match === false) {
    // Página não encontrada
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    require VIEWS_PATH . '/errors/404.php';
    exit;
}

// Executa o controller
list($controller, $method) = $match['target'];
$params = $match['params'];

// Instancia o controller
$controller = new $controller();

// Chama o método com os parâmetros
call_user_func_array([$controller, $method], $params);
