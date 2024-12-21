<?php
namespace App\Models;

/**
 * Model para gerenciamento de despesas
 */
class Expense extends Model {
    protected $table = 'expenses';

    /**
     * Retorna o total de despesas no período
     */
    public function getMonthlyTotal($startDate, $endDate) {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE expense_date BETWEEN :start AND :end";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start' => $startDate,
            'end' => $endDate
        ]);
        
        return $stmt->fetch()['total'];
    }

    /**
     * Retorna as despesas por categoria no período
     */
    public function getExpensesByCategory($startDate, $endDate) {
        $sql = "SELECT ec.name as category,
                       COUNT(*) as total_expenses,
                       SUM(e.amount) as total_amount
                FROM {$this->table} e
                JOIN expense_categories ec ON e.category_id = ec.id
                WHERE e.expense_date BETWEEN :start AND :end
                GROUP BY ec.id, ec.name
                ORDER BY total_amount DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start' => $startDate,
            'end' => $endDate
        ]);
        
        return $stmt->fetchAll();
    }

    /**
     * Retorna as despesas com filtros
     */
    public function getFilteredExpenses($filters = []) {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = "e.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['start_date'])) {
            $where[] = "e.expense_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where[] = "e.expense_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(e.description LIKE :search OR ec.name LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }

        $sql = "SELECT e.*, ec.name as category_name
                FROM {$this->table} e
                JOIN expense_categories ec ON e.category_id = ec.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY e.expense_date DESC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Retorna as categorias de despesas
     */
    public function getCategories() {
        $sql = "SELECT * FROM expense_categories ORDER BY name";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Retorna as despesas recentes
     */
    public function getRecentExpenses($limit = 5) {
        $sql = "SELECT e.*, ec.name as category_name
                FROM {$this->table} e
                JOIN expense_categories ec ON e.category_id = ec.id
                ORDER BY e.expense_date DESC, e.id DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Retorna o relatório de despesas por período
     */
    public function getExpenseReport($startDate, $endDate) {
        // Total por categoria
        $byCategory = $this->getExpensesByCategory($startDate, $endDate);
        
        // Total geral
        $total = array_reduce($byCategory, function($sum, $item) {
            return $sum + $item['total_amount'];
        }, 0);
        
        // Média diária
        $sql = "SELECT AVG(daily_total) as average
                FROM (
                    SELECT DATE(expense_date) as date, SUM(amount) as daily_total
                    FROM {$this->table}
                    WHERE expense_date BETWEEN :start AND :end
                    GROUP BY DATE(expense_date)
                ) as daily";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start' => $startDate,
            'end' => $endDate
        ]);
        
        $average = $stmt->fetch()['average'];
        
        return [
            'total' => $total,
            'average' => $average,
            'by_category' => $byCategory
        ];
    }

    /**
     * Cria uma nova despesa
     */
    public function createExpense($data) {
        try {
            $this->db->beginTransaction();
            
            // Valida a categoria
            $stmt = $this->db->prepare("SELECT id FROM expense_categories WHERE id = ?");
            $stmt->execute([$data['category_id']]);
            if (!$stmt->fetch()) {
                throw new \Exception('Categoria inválida');
            }
            
            // Insere a despesa
            $id = $this->create($data);
            
            $this->db->commit();
            return $id;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
