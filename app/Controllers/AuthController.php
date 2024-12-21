<?php
namespace App\Controllers;

use App\Models\User;

/**
 * Controller responsável pela autenticação
 */
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Exibe o formulário de login
     */
    public function showLoginForm() {
        if (isset($_SESSION['user_id'])) {
            redirect('/');
        }
        
        require VIEWS_PATH . '/auth/login.php';
    }

    /**
     * Processa o login
     */
    public function login() {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            flash('Por favor, preencha todos os campos.', 'danger');
            redirect('/login');
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            flash('E-mail ou senha inválidos.', 'danger');
            redirect('/login');
            return;
        }

        // Login bem sucedido
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        flash('Bem-vindo de volta!', 'success');
        redirect('/');
    }

    /**
     * Faz logout do usuário
     */
    public function logout() {
        session_destroy();
        redirect('/login');
    }

    /**
     * Middleware de autenticação
     */
    public function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            flash('Por favor, faça login para continuar.', 'warning');
            redirect('/login');
            exit;
        }
    }

    /**
     * Middleware de guest
     */
    public function requireGuest() {
        if (isset($_SESSION['user_id'])) {
            redirect('/');
            exit;
        }
    }
}
