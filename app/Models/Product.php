<?php
namespace App\Models;

/**
 * Model para gerenciamento de produtos
 */
class Product extends Model {
    protected $table = 'products';
    protected $fillable = ['name', 'description', 'category_id', 'price', 'stock'];

    /**
     * Busca produtos com suas categorias
     */
    public function getAllWithCategories() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN product_categories c ON p.category_id = c.id 
                ORDER BY p.name";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca um produto com sua categoria
     */
    public function findWithCategory($id) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN product_categories c ON p.category_id = c.id 
                WHERE p.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca todas as categorias
     */
    public function getAllCategories() {
        $stmt = $this->db->query("SELECT * FROM product_categories ORDER BY name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca produtos com estoque baixo
     */
    public function getLowStock($limit = 5) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN product_categories c ON p.category_id = c.id 
                WHERE p.stock <= 10 
                ORDER BY p.stock ASC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Busca produtos mais vendidos
     */
    public function getTopSelling($limit = 5) {
        $sql = "SELECT p.*, c.name as category_name, 
                       SUM(si.quantity) as total_sold 
                FROM {$this->table} p 
                LEFT JOIN product_categories c ON p.category_id = c.id 
                LEFT JOIN sale_items si ON p.id = si.product_id 
                GROUP BY p.id 
                ORDER BY total_sold DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza o estoque do produto
     */
    public function updateStock($id, $quantity) {
        $sql = "UPDATE {$this->table} 
                SET stock = stock + :quantity 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'quantity' => $quantity
        ]);
    }
}
