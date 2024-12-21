<?php
namespace App\Controllers;

use App\Models\Expense;

/**
 * Controller para gerenciamento de despesas
 */
class ExpenseController extends Controller {
    private $expenseModel;

    public function __construct() {
        $this->requireAuth();
        $this->expenseModel = new Expense();
    }

    /**
     * Lista todas as despesas
     */
    public function index() {
        $filters = [
            'category_id' => $_GET['category_id'] ?? null,
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        $expenses = $this->expenseModel->getFilteredExpenses($filters);
        $categories = $this->expenseModel->getCategories();
        
        $this->view('expenses/index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'filters' => $filters
        ]);
    }

    /**
     * Exibe formulário de criação
     */
    public function create() {
        $categories = $this->expenseModel->getCategories();
        $this->view('expenses/create', ['categories' => $categories]);
    }

    /**
     * Salva nova despesa
     */
    public function store() {
        // Valida dados da despesa
        $expenseData = [
            'user_id' => $_SESSION['user_id'],
            'category_id' => filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT),
            'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING),
            'amount' => filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT),
            'expense_date' => filter_input(INPUT_POST, 'expense_date'),
            'notes' => filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING)
        ];

        // Validação básica
        if (!$expenseData['category_id'] || !$expenseData['description'] || 
            !$expenseData['amount'] || !$expenseData['expense_date']) {
            $_SESSION['error'] = 'Por favor, preencha todos os campos obrigatórios.';
            $this->redirect('/expenses/create');
        }

        try {
            $this->expenseModel->createExpense($expenseData);
            $_SESSION['success'] = 'Despesa registrada com sucesso!';
            $this->redirect('/expenses');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao registrar despesa. Tente novamente.';
            $this->redirect('/expenses/create');
        }
    }

    /**
     * Exibe detalhes da despesa
     */
    public function show($id) {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            $_SESSION['error'] = 'Despesa não encontrada.';
            $this->redirect('/expenses');
        }

        $this->view('expenses/show', ['expense' => $expense]);
    }

    /**
     * Exibe formulário de edição
     */
    public function edit($id) {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            $_SESSION['error'] = 'Despesa não encontrada.';
            $this->redirect('/expenses');
        }

        $categories = $this->expenseModel->getCategories();
        $this->view('expenses/edit', [
            'expense' => $expense,
            'categories' => $categories
        ]);
    }

    /**
     * Atualiza despesa
     */
    public function update($id) {
        $expense = $this->expenseModel->find($id);
        if (!$expense) {
            $_SESSION['error'] = 'Despesa não encontrada.';
            $this->redirect('/expenses');
        }

        // Valida dados da despesa
        $expenseData = [
            'category_id' => filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT),
            'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING),
            'amount' => filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT),
            'expense_date' => filter_input(INPUT_POST, 'expense_date'),
            'notes' => filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING)
        ];

        // Validação básica
        if (!$expenseData['category_id'] || !$expenseData['description'] || 
            !$expenseData['amount'] || !$expenseData['expense_date']) {
            $_SESSION['error'] = 'Por favor, preencha todos os campos obrigatórios.';
            $this->redirect("/expenses/{$id}/edit");
        }

        try {
            $this->expenseModel->update($id, $expenseData);
            $_SESSION['success'] = 'Despesa atualizada com sucesso!';
            $this->redirect('/expenses');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar despesa. Tente novamente.';
            $this->redirect("/expenses/{$id}/edit");
        }
    }

    /**
     * Remove despesa
     */
    public function destroy($id) {
        try {
            $this->expenseModel->delete($id);
            $this->json([
                'success' => true,
                'message' => 'Despesa removida com sucesso!'
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Erro ao remover despesa.'
            ], 500);
        }
    }

    /**
     * Exibe relatório de despesas
     */
    public function report() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        $expense = new Expense();
        
        $report = [
            'total' => 0,
            'average' => 0,
            'by_category' => [],
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        // Total de despesas no período
        $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM expenses 
                WHERE date BETWEEN ? AND ?";
        $stmt = $expense->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $report['total'] = (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Média diária
        $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
        $report['average'] = $report['total'] / $days;

        // Despesas por categoria
        $sql = "SELECT 
                    category,
                    COUNT(*) as total_expenses,
                    COALESCE(SUM(amount), 0) as total_amount
                FROM expenses 
                WHERE date BETWEEN ? AND ?
                GROUP BY category
                ORDER BY total_amount DESC";
        
        $stmt = $expense->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $report['by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Se não houver categorias, adiciona um array vazio
        if (empty($report['by_category'])) {
            $report['by_category'] = [];
        }

        require VIEWS_PATH . '/expenses/report.php';
    }

    /**
     * Exporta relatório em PDF
     */
    private function exportPDF($report, $startDate, $endDate) {
        require_once ROOT_PATH . '/vendor/autoload.php';
        
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20
        ]);

        // Cabeçalho
        $html = "
            <h1>Relatório de Despesas</h1>
            <p>Período: " . date('d/m/Y', strtotime($startDate)) . 
            " a " . date('d/m/Y', strtotime($endDate)) . "</p>
            
            <h2>Resumo</h2>
            <p>Total: R$ " . number_format($report['total'], 2, ',', '.') . "</p>
            <p>Média Diária: R$ " . number_format($report['average'], 2, ',', '.') . "</p>
            
            <h2>Despesas por Categoria</h2>
            <table border='1' cellpadding='5'>
                <tr>
                    <th>Categoria</th>
                    <th>Quantidade</th>
                    <th>Total</th>
                </tr>";

        foreach ($report['by_category'] as $category) {
            $html .= "<tr>
                        <td>{$category['category']}</td>
                        <td align='center'>{$category['total_expenses']}</td>
                        <td align='right'>R$ " . 
                        number_format($category['total_amount'], 2, ',', '.') . 
                        "</td>
                    </tr>";
        }

        $html .= "</table>";

        $mpdf->WriteHTML($html);
        $mpdf->Output('relatorio-despesas.pdf', 'D');
        exit;
    }
}
