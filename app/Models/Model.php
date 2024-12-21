<?php

namespace App\Models;

use App\Database\Database;
use PDO;

/**
 * Classe base para todos os models
 * Implementa operações básicas de CRUD
 */
abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getConnection() {
        return $this->db;
    }

    /**
     * Busca um registro pelo ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findBy($column, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
        $stmt->execute([$value]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca todos os registros da tabela
     */
    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo registro
     */
    public function create($data) {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($fields)) {
            return false;
        }

        $columns = implode(', ', array_keys($fields));
        $values = implode(', ', array_fill(0, count($fields), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute(array_values($fields))) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Atualiza um registro existente
     */
    public function update($id, $data) {
        $fields = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($fields)) {
            return false;
        }

        $set = implode(' = ?, ', array_keys($fields)) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?";
        
        $values = array_values($fields);
        $values[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Remove um registro
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Busca registros com base em uma condição
     */
    public function where($column, $operator, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} {$operator} ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca registros com base em uma lista de valores
     */
    public function whereIn($column, $values) {
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} IN ({$placeholders})");
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna a contagem de registros
     */
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table}");
        $stmt->execute();
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    /**
     * Retorna a soma de uma coluna
     */
    public function sum($column) {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM({$column}), 0) as total FROM {$this->table}");
        $stmt->execute();
        return (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Retorna uma página de registros
     */
    public function paginate($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        
        // Total de registros
        $total = $this->count();
        
        // Registros da página atual
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} LIMIT ? OFFSET ?");
        $stmt->execute([$perPage, $offset]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'items' => $items
        ];
    }

    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollBack() {
        return $this->db->rollBack();
    }
}
