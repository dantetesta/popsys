<?php
namespace App\Models;

/**
 * Model para gerenciamento de usuários
 */
class User extends Model {
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];

    /**
     * Busca usuário por email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Cria um novo usuário com senha criptografada
     */
    public function createUser(array $data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->create($data);
    }

    /**
     * Atualiza a senha do usuário
     */
    public function updatePassword($id, $password) {
        return $this->update($id, [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Verifica se as credenciais são válidas
     */
    public function validateCredentials($email, $password) {
        $user = $this->findByEmail($email);
        if (!$user) {
            return false;
        }
        return password_verify($password, $user['password']);
    }
}
