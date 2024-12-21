<?php

// Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Configurações de Sessão (antes de iniciar a sessão)
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);

// Inicia a sessão
session_start();

// Carrega as configurações
require_once __DIR__ . '/../config/config.php';

// Configura o router
$router = new AltoRouter();
$router->setBasePath('');

// Rotas de Autenticação
$router->map('GET', '/login', 'App\Controllers\AuthController@showLoginForm', 'login');
$router->map('POST', '/login', 'App\Controllers\AuthController@login', 'login.post');
$router->map('GET', '/logout', 'App\Controllers\AuthController@logout', 'logout');

// Rotas do Dashboard
$router->map('GET', '/', 'App\Controllers\DashboardController@index', 'dashboard');

// Rotas de Produtos
$router->map('GET', '/products', 'App\Controllers\ProductController@index', 'products');
$router->map('GET', '/products/create', 'App\Controllers\ProductController@create', 'products.create');
$router->map('POST', '/products', 'App\Controllers\ProductController@store', 'products.store');
$router->map('GET', '/products/[i:id]', 'App\Controllers\ProductController@show', 'products.show');
$router->map('GET', '/products/[i:id]/edit', 'App\Controllers\ProductController@edit', 'products.edit');
$router->map('POST', '/products/[i:id]', 'App\Controllers\ProductController@update', 'products.update');
$router->map('POST', '/products/[i:id]/delete', 'App\Controllers\ProductController@destroy', 'products.delete');
$router->map('POST', '/products/[i:id]/stock', 'App\Controllers\ProductController@updateStock', 'products.stock');

// Rotas de Pedidos
$router->map('GET', '/orders', 'App\Controllers\OrderController@index', 'orders');
$router->map('GET', '/orders/create', 'App\Controllers\OrderController@create', 'orders.create');
$router->map('POST', '/orders', 'App\Controllers\OrderController@store', 'orders.store');

// Rotas de Despesas
$router->map('GET', '/expenses', 'App\Controllers\ExpenseController@index', 'expenses');
$router->map('GET', '/expenses/create', 'App\Controllers\ExpenseController@create', 'expenses.create');
$router->map('POST', '/expenses', 'App\Controllers\ExpenseController@store', 'expenses.store');
$router->map('GET', '/expenses/[i:id]/edit', 'App\Controllers\ExpenseController@edit', 'expenses.edit');
$router->map('POST', '/expenses/[i:id]', 'App\Controllers\ExpenseController@update', 'expenses.update');
$router->map('POST', '/expenses/[i:id]/delete', 'App\Controllers\ExpenseController@destroy', 'expenses.delete');

// Rotas de Relatórios
$router->map('GET', '/reports', 'App\Controllers\ReportController@index', 'reports');
$router->map('GET', '/reports/sales', 'App\Controllers\ReportController@sales', 'reports.sales');
$router->map('GET', '/reports/expenses', 'App\Controllers\ReportController@expenses', 'reports.expenses');
$router->map('GET', '/reports/products', 'App\Controllers\ReportController@products', 'reports.products');

// Rotas de Perfil
$router->map('GET', '/profile', 'App\Controllers\ProfileController@edit', 'profile.edit');
$router->map('POST', '/profile', 'App\Controllers\ProfileController@update', 'profile.update');
$router->map('POST', '/profile/password', 'App\Controllers\ProfileController@updatePassword', 'profile.password');

// Match da rota atual
$match = $router->match();

if ($match) {
    list($controller, $method) = explode('@', $match['target']);
    
    if (class_exists($controller)) {
        $controllerInstance = new $controller();
        
        if (method_exists($controllerInstance, $method)) {
            call_user_func_array([$controllerInstance, $method], $match['params']);
        } else {
            require VIEWS_PATH . '/errors/404.php';
        }
    } else {
        require VIEWS_PATH . '/errors/404.php';
    }
} else {
    require VIEWS_PATH . '/errors/404.php';
}
