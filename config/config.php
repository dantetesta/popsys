<?php
/**
 * Arquivo de configuração principal
 * Carrega as variáveis de ambiente e define constantes globais
 */

// Configurações da Aplicação
define('APP_NAME', 'PopSys');
define('APP_ENV', 'development');
define('APP_DEBUG', true);
define('APP_URL', 'http://localhost:8000');

// Configurações do Banco de Dados
define('DB_HOST', '187.33.241.61');
define('DB_PORT', '3306');
define('DB_DATABASE', 'dantetesta_popsys');
define('DB_USERNAME', 'dantetesta_popsys');
define('DB_PASSWORD', 'eF=d35Kf8DDU');

// Configurações de Email
define('SMTP_HOST', 'mail.dantetesta.com.br');
define('SMTP_PORT', '465');
define('SMTP_USER', 'no-reply@dantetesta.com.br');
define('SMTP_PASS', 'ddtevy11@');
define('SMTP_SECURE', 'ssl');

// Diretórios da Aplicação
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/Views');

// Timezone
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

// Configurações de erro (desenvolvimento)
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Configuração de Sessão
session_start();
session_regenerate_id(true);

// Funções helpers globais
function redirect($path) {
    header("Location: " . APP_URL . $path);
    exit;
}

function asset($path) {
    return APP_URL . "/public/" . $path;
}

// Funções Auxiliares
require_once CONFIG_PATH . '/helpers.php';

// Autoload de Classes
spl_autoload_register(function ($class) {
    $file = ROOT_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
