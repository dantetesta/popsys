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
    public function loginForm() {
        if ($this->isAuthenticated) {
            redirect('/');
        }
        
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
            unset($_SESSION['error']);
        }
        
        require VIEWS_PATH . '/auth/login.php';
    }

    /**
     * Processa o login
     */
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $errors = $this->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            redirect('/login');
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            flash('E-mail ou senha inválidos.', 'error');
            redirect('/login');
            return;
        }

        // Inicia a sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        // Regenera o ID da sessão por segurança
        session_regenerate_id(true);

        flash('Bem-vindo(a) de volta!', 'success');
        redirect('/');
    }

    /**
     * Realiza o logout
     */
    public function logout() {
        // Limpa todas as variáveis da sessão
        $_SESSION = [];

        // Destrói a sessão
        session_destroy();

        // Expira o cookie da sessão
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        redirect('/login');
    }

    /**
     * Exibe o formulário de alteração de senha
     */
    public function changePasswordForm() {
        if (!$this->isAuthenticated) {
            redirect('/login');
        }
        require VIEWS_PATH . '/auth/change-password.php';
    }

    /**
     * Processa a alteração de senha
     */
    public function changePassword() {
        if (!$this->isAuthenticated) {
            redirect('/login');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = $this->validate($_POST, [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|min:6'
        ]);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            redirect('/change-password');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            flash('As senhas não conferem.', 'error');
            redirect('/change-password');
            return;
        }

        $user = $this->userModel->find($_SESSION['user_id']);

        if (!password_verify($currentPassword, $user['password'])) {
            flash('Senha atual incorreta.', 'error');
            redirect('/change-password');
            return;
        }

        // Atualiza a senha
        $this->userModel->update($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        flash('Senha alterada com sucesso!', 'success');
        redirect('/profile');
    }
}
