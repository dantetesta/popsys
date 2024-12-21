<?php
namespace App\Controllers;

use App\Models\User;

class ProfileController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Exibe o formulário de edição do perfil
     */
    public function edit() {
        if (!$this->isAuthenticated) {
            redirect('/login');
        }

        $user = $this->userModel->find($_SESSION['user_id']);
        require VIEWS_PATH . '/profile/edit.php';
    }

    /**
     * Atualiza os dados do perfil
     */
    public function update() {
        if (!$this->isAuthenticated) {
            redirect('/login');
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'new_password' => $_POST['new_password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ];

        $errors = $this->validate($data, [
            'name' => 'required|min:3',
            'email' => 'required|email'
        ]);

        $passwordChanged = false;

        // Se informou nova senha, valida
        if (!empty($data['new_password'])) {
            if ($data['new_password'] !== $data['confirm_password']) {
                $errors['confirm_password'] = 'As senhas não conferem';
            } elseif (strlen($data['new_password']) < 6) {
                $errors['new_password'] = 'A senha deve ter pelo menos 6 caracteres';
            } else {
                $passwordChanged = true;
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            redirect('/profile');
            return;
        }

        try {
            // Atualiza os dados básicos
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email']
            ];

            // Se informou senha, atualiza também
            if ($passwordChanged) {
                $updateData['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
            }

            $this->userModel->update($_SESSION['user_id'], $updateData);
            
            // Atualiza a sessão
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['user_email'] = $data['email'];
            
            if ($passwordChanged) {
                flash('Senha alterada com sucesso! Por segurança, você será desconectado para fazer login com sua nova senha.', 'success');
                redirect('/logout');
                return;
            }
            
            flash('Perfil atualizado com sucesso!', 'success');
            redirect('/profile');
            
        } catch (\Exception $e) {
            flash('Erro ao atualizar perfil. Tente novamente.', 'error');
            redirect('/profile');
        }
    }
}
