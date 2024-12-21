<?php

/**
 * Redireciona para uma URL específica
 */
function redirect($url) {
    header("Location: $url");
    exit;
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
