-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS dantetesta_popsys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dantetesta_popsys;

-- Tabela de usuários
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de categorias de produtos
CREATE TABLE product_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de produtos
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    min_stock INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id)
) ENGINE=InnoDB;

-- Tabela de pedidos
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    customer_email VARCHAR(100),
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    delivery_date DATE NOT NULL,
    delivery_time TIME NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Tabela de itens do pedido
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- Tabela de vendas
CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    payment_method ENUM('cash', 'credit_card', 'debit_card', 'pix') NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    sale_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- Tabela de itens da venda
CREATE TABLE sale_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- Tabela de categorias de despesas
CREATE TABLE expense_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabela de despesas
CREATE TABLE expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    description TEXT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id)
) ENGINE=InnoDB;

-- Inserindo categorias de produtos
INSERT INTO product_categories (name, description) VALUES
('Pipoca Doce', 'Variedades de pipoca com sabores doces'),
('Pipoca Salgada', 'Variedades de pipoca com sabores salgados'),
('Pipoca Gourmet', 'Pipocas especiais com sabores exclusivos'),
('Bebidas', 'Refrigerantes e outras bebidas'),
('Combos', 'Combinações de produtos com preço especial');

-- Inserindo alguns produtos
INSERT INTO products (category_id, name, description, price, stock, min_stock) VALUES
(1, 'Pipoca Caramelizada', 'Pipoca doce tradicional com caramelo', 8.00, 100, 10),
(1, 'Pipoca com Chocolate', 'Pipoca doce com cobertura de chocolate', 10.00, 100, 10),
(2, 'Pipoca Salgada Tradicional', 'Pipoca salgada com manteiga', 6.00, 100, 10),
(2, 'Pipoca Queijo', 'Pipoca salgada com queijo', 8.00, 100, 10),
(3, 'Pipoca Gourmet Nutella', 'Pipoca especial com Nutella', 15.00, 50, 10),
(3, 'Pipoca Gourmet Bacon', 'Pipoca especial com bacon', 12.00, 50, 10),
(4, 'Refrigerante Lata', 'Refrigerante em lata 350ml', 5.00, 200, 10),
(4, 'Água Mineral', 'Água mineral sem gás 500ml', 3.00, 200, 10),
(5, 'Combo Família', 'Pipoca grande + 4 refrigerantes', 35.00, 50, 10),
(5, 'Combo Casal', 'Pipoca média + 2 refrigerantes', 25.00, 50, 10);

-- Inserindo categorias de despesas
INSERT INTO expense_categories (name, description) VALUES
('Ingredientes', 'Matéria prima para produção'),
('Embalagens', 'Materiais para embalagem dos produtos'),
('Marketing', 'Despesas com publicidade e propaganda'),
('Funcionários', 'Despesas com pessoal'),
('Equipamentos', 'Manutenção e compra de equipamentos'),
('Aluguel', 'Despesas com aluguel do espaço'),
('Utilities', 'Água, luz, internet, etc');

-- Inserindo usuário administrador padrão
-- Senha: Admin@123
INSERT INTO users (name, email, password) VALUES
('Administrador', 'admin@popsys.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Triggers para cálculo automático de subtotais
DELIMITER //

CREATE TRIGGER calculate_order_item_subtotal
BEFORE INSERT ON order_items
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.quantity * NEW.unit_price;
END//

CREATE TRIGGER calculate_sale_item_subtotal
BEFORE INSERT ON sale_items
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.quantity * NEW.unit_price;
END//

-- Trigger para atualização de estoque após venda
CREATE TRIGGER update_stock_after_sale
AFTER INSERT ON sale_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock = stock - NEW.quantity 
    WHERE id = NEW.product_id;
END//

DELIMITER ;
