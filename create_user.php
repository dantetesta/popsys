<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

use App\Database\Database;
use PDO;

try {
    $db = Database::getInstance()->getConnection();
    
    // Primeiro, vamos verificar se o usuário já existe
    $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $checkStmt->execute(['admin@popsys.com']);
    $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "Usuário já existe!\n";
    } else {
        // Criar o usuário
        $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([
            'Administrador',
            'admin@popsys.com',
            password_hash('password123', PASSWORD_DEFAULT)
        ]);
        
        echo "Usuário criado com sucesso!\n";
        echo "Email: admin@popsys.com\n";
        echo "Senha: password123\n";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
