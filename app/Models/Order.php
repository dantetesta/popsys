<?php
namespace App\Models;

/**
 * Model para gerenciamento de pedidos
 */
class Order extends Model {
    protected $table = 'orders';

    /**
     * Retorna o total de pedidos no período
     */
    public function getMonthlyTotal($startDate, $endDate) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE delivery_date BETWEEN :start AND :end";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start' => $startDate,
            'end' => $endDate
        ]);
        
        return $stmt->fetch()['total'];
    }

    /**
     * Retorna os pedidos pendentes
     */
    public function getPendingOrders($limit = null) {
        $sql = "SELECT o.*, 
                       GROUP_CONCAT(p.name) as products,
                       COUNT(DISTINCT oi.product_id) as total_items
                FROM {$this->table} o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE o.status = 'pending'
                AND o.delivery_date >= CURRENT_DATE
                GROUP BY o.id
                ORDER BY o.delivery_date ASC, o.delivery_time ASC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare($sql);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Retorna os pedidos com filtros
     */
    public function getFilteredOrders($filters = []) {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "o.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['start_date'])) {
            $where[] = "o.delivery_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where[] = "o.delivery_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(o.customer_name LIKE :search OR o.customer_email LIKE :search OR o.customer_phone LIKE :search)";
            $params['search'] = "%{$filters['search']}%";
        }

        $sql = "SELECT o.*, 
                       GROUP_CONCAT(p.name) as products,
                       COUNT(DISTINCT oi.product_id) as total_items
                FROM {$this->table} o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY o.id
                ORDER BY o.delivery_date DESC, o.delivery_time DESC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Retorna um pedido com seus itens
     */
    public function findWithItems($id) {
        // Busca o pedido
        $sql = "SELECT o.*, u.name as created_by
                FROM {$this->table} o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        // Busca os itens do pedido
        $sql = "SELECT oi.*, p.name as product_name, p.description as product_description
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['order_id' => $id]);
        $order['items'] = $stmt->fetchAll();

        return $order;
    }

    /**
     * Cria um novo pedido com seus itens
     */
    public function createOrder($orderData, $items) {
        try {
            $this->db->beginTransaction();

            // Calcula o total do pedido
            $total = 0;
            foreach ($items as $item) {
                $total += $item['quantity'] * $item['unit_price'];
            }
            $orderData['total_amount'] = $total;

            // Insere o pedido
            $orderId = $this->create($orderData);

            // Insere os itens do pedido
            $stmt = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal)
            ");

            foreach ($items as $item) {
                $item['order_id'] = $orderId;
                $item['subtotal'] = $item['quantity'] * $item['unit_price'];
                $stmt->execute($item);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Atualiza o status do pedido
     */
    public function updateStatus($id, $status) {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Verifica se existem pedidos para entrega hoje
     */
    public function getTodayOrders() {
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table}
                WHERE DATE(delivery_date) = CURRENT_DATE
                AND status = 'pending'";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }

    /**
     * Retorna os próximos pedidos
     */
    public function getUpcomingOrders($days = 7) {
        $sql = "SELECT o.*, 
                       GROUP_CONCAT(p.name) as products
                FROM {$this->table} o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE o.delivery_date BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL :days DAY)
                AND o.status = 'pending'
                GROUP BY o.id
                ORDER BY o.delivery_date ASC, o.delivery_time ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':days', $days, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
