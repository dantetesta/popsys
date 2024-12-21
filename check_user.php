<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

use App\Database\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@popsys.com']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "Usuário encontrado:\n";
        echo "ID: " . $user['id'] . "\n";
        echo "Nome: " . $user['name'] . "\n";
        echo "Email: " . $user['email'] . "\n";
    } else {
        echo "Usuário não encontrado!\n";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
