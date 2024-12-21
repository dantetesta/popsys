<?php

namespace App\Controllers;

abstract class Controller {
    protected $user;
    protected $isAuthenticated = false;

    public function __construct() {
        // Inicia a sessão se ainda não foi iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se o usuário está autenticado
        $this->isAuthenticated = isset($_SESSION['user_id']);
        
        // Se estiver autenticado, carrega os dados do usuário
        if ($this->isAuthenticated) {
            $this->user = [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? 'Usuário',
                'email' => $_SESSION['user_email'] ?? '',
                'role' => $_SESSION['user_role'] ?? 'user'
            ];
        }

        // Verifica se precisa estar autenticado
        $publicRoutes = [
            '/login',
            '/register',
            '/forgot-password',
            '/reset-password'
        ];

        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!in_array($currentPath, $publicRoutes) && !$this->isAuthenticated) {
            flash('Você precisa fazer login para acessar esta página.', 'warning');
            redirect('/login');
        }
    }

    protected function view($view, $data = []) {
        // Adiciona os dados do usuário às variáveis da view
        $data['user'] = $this->user;
        $data['isAuthenticated'] = $this->isAuthenticated;
        
        // Extrai as variáveis para a view
        extract($data);
        
        // Carrega a view
        require VIEWS_PATH . '/' . $view . '.php';
    }

    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function back() {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    protected function validate($data, $rules) {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $ruleArray = explode('|', $rule);

            foreach ($ruleArray as $singleRule) {
                if ($singleRule === 'required' && empty($value)) {
                    $errors[$field][] = 'O campo é obrigatório.';
                }

                if (strpos($singleRule, 'min:') === 0) {
                    $min = substr($singleRule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = "O campo deve ter no mínimo {$min} caracteres.";
                    }
                }

                if (strpos($singleRule, 'max:') === 0) {
                    $max = substr($singleRule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = "O campo deve ter no máximo {$max} caracteres.";
                    }
                }

                if ($singleRule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'O campo deve ser um e-mail válido.';
                }

                if ($singleRule === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = 'O campo deve ser um número.';
                }
            }
        }

        return $errors;
    }
}
