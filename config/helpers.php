<?php

/**
 * Funções auxiliares globais
 */

if (!function_exists('dd')) {
    /**
     * Dump and die - Debug helper
     */
    function dd(...$args) {
        echo '<pre>';
        var_dump(...$args);
        echo '</pre>';
        die();
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities
     */
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to another page
     */
    function redirect($url) {
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate asset URL
     */
    function asset($path) {
        return APP_URL . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL
     */
    function url($path = '') {
        return APP_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('old')) {
    /**
     * Get old form input value
     */
    function old($key, $default = '') {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate CSRF token
     */
    function csrf_token() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF field
     */
    function csrf_field() {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('method_field')) {
    /**
     * Generate method field for forms
     */
    function method_field($method) {
        return '<input type="hidden" name="_method" value="' . $method . '">';
    }
}

if (!function_exists('flash')) {
    /**
     * Flash message helper
     */
    function flash($message = null, $type = 'success') {
        if ($message) {
            $_SESSION['flash'] = [
                'message' => $message,
                'type' => $type
            ];
            return null;
        }

        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }

        return null;
    }
}

if (!function_exists('format_money')) {
    /**
     * Format number to money
     */
    function format_money($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date
     */
    function format_date($date, $format = 'd/m/Y') {
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime
     */
    function format_datetime($date, $format = 'd/m/Y H:i') {
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_date_pt')) {
    /**
     * Formata uma data em português
     */
    function format_date_pt($date, $format = 'complete') {
        $meses = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];

        $diasSemana = [
            0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira',
            3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira',
            6 => 'Sábado'
        ];

        $timestamp = strtotime($date);
        $dia = date('d', $timestamp);
        $mes = (int)date('n', $timestamp);
        $ano = date('Y', $timestamp);
        $diaSemana = (int)date('w', $timestamp);

        switch ($format) {
            case 'complete':
                return $diasSemana[$diaSemana] . ', ' . $dia . ' de ' . $meses[$mes] . ' de ' . $ano;
            case 'month_year':
                return $meses[$mes] . ' de ' . $ano;
            case 'short':
                return $dia . '/' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '/' . $ano;
            default:
                return date($format, $timestamp);
        }
    }
}

if (!function_exists('is_active')) {
    /**
     * Check if current page is active
     */
    function is_active($path, $class = 'active') {
        $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $current = trim($current, '/');
        $path = trim($path, '/');
        
        return ($current === $path) ? $class : '';
    }
}

if (!function_exists('get_flash_alert')) {
    /**
     * Get flash message alert
     */
    function get_flash_alert() {
        $flash = flash();
        if (!$flash) return '';

        $type = $flash['type'];
        $message = $flash['message'];

        $icons = [
            'success' => 'check-circle',
            'error' => 'exclamation-circle',
            'warning' => 'exclamation-triangle',
            'info' => 'info-circle'
        ];

        $icon = $icons[$type] ?? 'info-circle';

        return "
            <div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                <i class='fas fa-{$icon} me-2'></i>
                {$message}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>
        ";
    }
}
