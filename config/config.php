<?php
/**
 * Arquivo de configuração principal
 * Carrega as variáveis de ambiente e define constantes globais
 */

// Tenta carregar o arquivo .env
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
} catch (Exception $e) {
    // Define valores padrão se o .env falhar
    $_ENV['APP_NAME'] = 'PopSys';
    $_ENV['APP_ENV'] = 'development';
    $_ENV['APP_DEBUG'] = true;
    $_ENV['APP_URL'] = 'http://localhost:8000';
    
    $_ENV['DB_HOST'] = 'localhost';
    $_ENV['DB_PORT'] = '3306';
    $_ENV['DB_DATABASE'] = 'popsys';
    $_ENV['DB_USERNAME'] = 'root';
    $_ENV['DB_PASSWORD'] = '';
    
    $_ENV['SMTP_HOST'] = 'smtp.mailtrap.io';
    $_ENV['SMTP_PORT'] = '2525';
    $_ENV['SMTP_USERNAME'] = 'your_username';
    $_ENV['SMTP_PASSWORD'] = 'your_password';
    $_ENV['SMTP_SECURE'] = 'tls';
    $_ENV['SMTP_FROM_ADDRESS'] = 'no-reply@popsys.com';
    $_ENV['SMTP_FROM_NAME'] = 'PopSys';
}

// Configurações da Aplicação
define('APP_NAME', $_ENV['APP_NAME']);
define('APP_ENV', $_ENV['APP_ENV']);
define('APP_DEBUG', $_ENV['APP_DEBUG']);
define('APP_URL', $_ENV['APP_URL']);

// Configurações do Banco de Dados
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_PORT', $_ENV['DB_PORT']);
define('DB_DATABASE', $_ENV['DB_DATABASE']);
define('DB_USERNAME', $_ENV['DB_USERNAME']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

// Configurações de Email
define('SMTP_HOST', $_ENV['SMTP_HOST']);
define('SMTP_PORT', $_ENV['SMTP_PORT']);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME']);
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD']);
define('SMTP_SECURE', $_ENV['SMTP_SECURE']);
define('SMTP_FROM_ADDRESS', $_ENV['SMTP_FROM_ADDRESS']);
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME']);

// Caminhos do Sistema
define('ROOT_PATH', realpath(__DIR__ . '/..'));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/Views');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Erro
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Funções Helpers
require_once CONFIG_PATH . '/helpers.php';

// Autoload de Classes
spl_autoload_register(function ($class) {
    $file = ROOT_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
