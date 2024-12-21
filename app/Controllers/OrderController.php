<?php
namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Controller para gerenciamento de pedidos
 */
class OrderController extends Controller {
    private $orderModel;
    private $productModel;

    public function __construct() {
        $this->requireAuth();
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    /**
     * Lista todos os pedidos
     */
    public function index() {
        $filters = [
            'status' => $_GET['status'] ?? null,
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        $orders = $this->orderModel->getFilteredOrders($filters);
        $this->view('orders/index', [
            'orders' => $orders,
            'filters' => $filters
        ]);
    }

    /**
     * Exibe formulário de criação
     */
    public function create() {
        $products = $this->productModel->getAllWithCategories();
        $this->view('orders/create', ['products' => $products]);
    }

    /**
     * Salva novo pedido
     */
    public function store() {
        // Valida dados do pedido
        $orderData = [
            'user_id' => $_SESSION['user_id'],
            'customer_name' => filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_STRING),
            'customer_phone' => filter_input(INPUT_POST, 'customer_phone', FILTER_SANITIZE_STRING),
            'customer_email' => filter_input(INPUT_POST, 'customer_email', FILTER_SANITIZE_EMAIL),
            'delivery_date' => filter_input(INPUT_POST, 'delivery_date'),
            'delivery_time' => filter_input(INPUT_POST, 'delivery_time'),
            'notes' => filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING),
            'status' => 'pending'
        ];

        if (!$orderData['customer_name'] || !$orderData['delivery_date'] || !$orderData['delivery_time']) {
            $_SESSION['error'] = 'Por favor, preencha todos os campos obrigatórios.';
            $this->redirect('/orders/create');
        }

        // Valida itens do pedido
        $items = [];
        $products = $_POST['products'] ?? [];
        $quantities = $_POST['quantities'] ?? [];
        
        if (empty($products)) {
            $_SESSION['error'] = 'Por favor, adicione pelo menos um produto ao pedido.';
            $this->redirect('/orders/create');
        }

        foreach ($products as $index => $productId) {
            $product = $this->productModel->find($productId);
            if (!$product) {
                $_SESSION['error'] = 'Produto inválido selecionado.';
                $this->redirect('/orders/create');
            }

            $quantity = (int)($quantities[$index] ?? 0);
            if ($quantity <= 0) {
                $_SESSION['error'] = 'Quantidade inválida para o produto ' . $product['name'];
                $this->redirect('/orders/create');
            }

            $items[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $product['price']
            ];
        }

        try {
            // Cria o pedido
            $orderId = $this->orderModel->createOrder($orderData, $items);
            
            // Envia e-mail de confirmação
            if ($orderData['customer_email']) {
                $this->sendOrderConfirmation($orderId);
            }

            $_SESSION['success'] = 'Pedido criado com sucesso!';
            $this->redirect('/orders/' . $orderId);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao criar pedido. Tente novamente.';
            $this->redirect('/orders/create');
        }
    }

    /**
     * Exibe detalhes do pedido
     */
    public function show($id) {
        $order = $this->orderModel->findWithItems($id);
        if (!$order) {
            $_SESSION['error'] = 'Pedido não encontrado.';
            $this->redirect('/orders');
        }

        $this->view('orders/show', ['order' => $order]);
    }

    /**
     * Atualiza status do pedido
     */
    public function updateStatus($id) {
        $status = filter_input(INPUT_POST, 'status');
        $validStatus = ['pending', 'confirmed', 'completed', 'cancelled'];

        if (!in_array($status, $validStatus)) {
            $this->json(['error' => 'Status inválido'], 400);
        }

        try {
            $this->orderModel->updateStatus($id, $status);
            
            // Se o pedido foi cancelado, envia e-mail
            if ($status === 'cancelled') {
                $order = $this->orderModel->findWithItems($id);
                if ($order['customer_email']) {
                    $this->sendOrderCancellation($order);
                }
            }

            $this->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            $this->json(['error' => 'Erro ao atualizar status'], 500);
        }
    }

    /**
     * Envia e-mail de confirmação do pedido
     */
    private function sendOrderConfirmation($orderId) {
        $order = $this->orderModel->findWithItems($orderId);
        if (!$order) return;

        $mail = new PHPMailer(true);

        try {
            // Configurações do servidor
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->CharSet = 'UTF-8';

            // Destinatários
            $mail->setFrom($_ENV['SMTP_USER'], APP_NAME);
            $mail->addAddress($order['customer_email'], $order['customer_name']);

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = 'Confirmação de Pedido #' . $orderId;
            
            // Corpo do e-mail
            $body = "<h2>Olá, {$order['customer_name']}!</h2>";
            $body .= "<p>Seu pedido #{$orderId} foi recebido com sucesso.</p>";
            $body .= "<p><strong>Data de Entrega:</strong> " . date('d/m/Y', strtotime($order['delivery_date']));
            $body .= " às " . date('H:i', strtotime($order['delivery_time'])) . "</p>";
            
            $body .= "<h3>Itens do Pedido:</h3>";
            $body .= "<ul>";
            foreach ($order['items'] as $item) {
                $body .= "<li>{$item['quantity']}x {$item['product_name']} - R$ " . 
                        number_format($item['subtotal'], 2, ',', '.') . "</li>";
            }
            $body .= "</ul>";
            
            $body .= "<p><strong>Total:</strong> R$ " . number_format($order['total_amount'], 2, ',', '.') . "</p>";
            
            if ($order['notes']) {
                $body .= "<p><strong>Observações:</strong> {$order['notes']}</p>";
            }

            $mail->Body = $body;

            $mail->send();
        } catch (Exception $e) {
            // Log do erro, mas não interrompe o fluxo
            error_log("Erro ao enviar e-mail de confirmação: {$mail->ErrorInfo}");
        }
    }

    /**
     * Envia e-mail de cancelamento do pedido
     */
    private function sendOrderCancellation($order) {
        $mail = new PHPMailer(true);

        try {
            // Configurações do servidor
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port = $_ENV['SMTP_PORT'];
            $mail->CharSet = 'UTF-8';

            // Destinatários
            $mail->setFrom($_ENV['SMTP_USER'], APP_NAME);
            $mail->addAddress($order['customer_email'], $order['customer_name']);

            // Conteúdo
            $mail->isHTML(true);
            $mail->Subject = 'Pedido #' . $order['id'] . ' Cancelado';
            
            // Corpo do e-mail
            $body = "<h2>Olá, {$order['customer_name']}!</h2>";
            $body .= "<p>Seu pedido #{$order['id']} foi cancelado.</p>";
            $body .= "<p>Se você tiver alguma dúvida, por favor entre em contato conosco.</p>";

            $mail->Body = $body;

            $mail->send();
        } catch (Exception $e) {
            // Log do erro, mas não interrompe o fluxo
            error_log("Erro ao enviar e-mail de cancelamento: {$mail->ErrorInfo}");
        }
    }
}
