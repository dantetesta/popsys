<?php
namespace App\Models;

/**
 * Model para gerenciamento de vendas
 */
class Sale extends Model {
    protected $table = 'sales';

    /**
     * Retorna o total de vendas no período
     */
    public function getMonthlyTotal($startDate, $endDate) {
        $sql = "SELECT COALESCE(SUM(total_amount), 0) as total 
                FROM {$this->table} 
                WHERE sale_date BETWEEN :start AND :end";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start' => $startDate,
            'end' => $endDate
        ]);
        
        return $stmt->fetch()['total'];
    }

    /**
     * Retorna as vendas diárias no período
     */
    public function getDailySales($startDate, $endDate) {
        $sql = "SELECT DATE(sale_date) as date,
                       COUNT(*) as total_sales,
                       SUM(total_amount) as total_amount
                FROM {$this->table}
                WHERE sale_date BETWEEN :start AND :end
                GROUP BY DATE(sale_date)
                ORDER BY date";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start' => $startDate,
            'end' => $endDate
        ]);
        
        return $stmt->fetchAll();
    }

    /**
     * Retorna as vendas por categoria no período
     */
    public function getSalesByCategory($startDate, $endDate) {
        $sql = "SELECT pc.name as category,
                       COUNT(DISTINCT s.id) as total_sales,
                       SUM(si.quantity) as total_items,
                       SUM(si.subtotal) as total_amount
                FROM {$this->table} s
                JOIN sale_items si ON s.id = si.sale_id
                JOIN products p ON si.product_id = p.id
                JOIN product_categories pc ON p.category_id = pc.id
                WHERE s.sale_date BETWEEN :start AND :end
                GROUP BY pc.id, pc.name
                ORDER BY total_amount DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start' => $startDate,
            'end' => $endDate
        ]);
        
        return $stmt->fetchAll();
    }

    /**
     * Retorna as vendas mais recentes
     */
    public function getRecentSales($limit = 5) {
        $sql = "SELECT s.*, 
                       GROUP_CONCAT(p.name) as products,
                       COUNT(DISTINCT si.product_id) as total_items
                FROM {$this->table} s
                JOIN sale_items si ON s.id = si.sale_id
                JOIN products p ON si.product_id = p.id
                GROUP BY s.id
                ORDER BY s.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Cria uma nova venda com seus itens
     */
    public function createSale($saleData, $items) {
        try {
            $this->db->beginTransaction();

            // Insere a venda
            $saleId = $this->create($saleData);

            // Insere os itens da venda
            $stmt = $this->db->prepare("
                INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal)
                VALUES (:sale_id, :product_id, :quantity, :unit_price, :subtotal)
            ");

            foreach ($items as $item) {
                $item['sale_id'] = $saleId;
                $item['subtotal'] = $item['quantity'] * $item['unit_price'];
                $stmt->execute($item);
            }

            $this->db->commit();
            return $saleId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
