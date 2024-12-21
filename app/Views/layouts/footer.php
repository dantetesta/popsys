    </div><!-- /.container -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Inicializa todos os tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Função para formatar números como moeda
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        }

        // Função para formatar datas
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return new Intl.DateTimeFormat('pt-BR').format(date);
        }

        // Atualiza formatação de valores monetários
        document.querySelectorAll('.money').forEach(element => {
            const value = parseFloat(element.textContent);
            element.textContent = formatCurrency(value);
        });

        // Atualiza formatação de datas
        document.querySelectorAll('.date').forEach(element => {
            element.textContent = formatDate(element.textContent);
        });
    </script>
</body>
</html>
