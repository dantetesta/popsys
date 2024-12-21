<?php

/**
 * Redireciona para uma URL específica
 */
function redirect($url) {
    if (!defined('APP_URL')) {
        throw new Exception('APP_URL não está definida');
    }
    
    // Se a URL não começar com http ou https, assume que é uma rota interna
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = APP_URL . $url;
    }
    
    header("Location: $url");
    exit;
}

/**
 * Gera URL para assets
 */
function asset($path) {
    if (!defined('APP_URL')) {
        throw new Exception('APP_URL não está definida');
    }
    return APP_URL . '/public/' . ltrim($path, '/');
}

/**
 * Define uma mensagem flash na sessão
 */
function flash($message, $type = 'info') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Retorna e limpa a mensagem flash da sessão
 */
function get_flash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Retorna os dados antigos do formulário
 */
function old($field, $default = '') {
    return $_SESSION['old'][$field] ?? $default;
}

/**
 * Retorna os erros de validação
 */
function get_errors() {
    $errors = $_SESSION['errors'] ?? [];
    unset($_SESSION['errors']);
    return $errors;
}

/**
 * Formata um valor monetário
 */
function format_money($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/**
 * Formata uma data
 */
function format_date($date) {
    return date('d/m/Y', strtotime($date));
}

/**
 * Formata uma data e hora
 */
function format_datetime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Verifica se o usuário está autenticado
 */
function is_authenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Retorna o usuário autenticado
 */
function auth_user() {
    if (!is_authenticated()) {
        return null;
    }
    
    static $user = null;
    if ($user === null) {
        $userModel = new \App\Models\User();
        $user = $userModel->find($_SESSION['user_id']);
    }
    
    return $user;
}

// Polyfill para versões antigas do PHP
if (PHP_VERSION_ID < 80000) {
    /**
     * Verifica se uma string contém um valor
     */
    if (!function_exists('str_contains')) {
        function str_contains($haystack, $needle) {
            return $needle !== '' && mb_strpos($haystack, $needle) !== false;
        }
    }

    /**
     * Verifica se uma string começa com um valor
     */
    if (!function_exists('str_starts_with')) {
        function str_starts_with($haystack, $needle) {
            return $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
        }
    }

    /**
     * Verifica se uma string termina com um valor
     */
    if (!function_exists('str_ends_with')) {
        function str_ends_with($haystack, $needle) {
            return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
        }
    }
}
