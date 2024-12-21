<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $title ?? 'Login' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%);
            display: flex;
            align-items: center;
            padding: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
        }
        
        .app-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4ECDC4;
            box-shadow: 0 0 0 0.25rem rgba(78, 205, 196, 0.25);
        }
        
        .input-group-text {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            background-color: #f8f9fa;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #FF6B6B, #4ECDC4);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            background: linear-gradient(45deg, #ff5252, #45b7aa);
        }
        
        .btn-outline-secondary {
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        
        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        @media (max-width: 576px) {
            .login-card {
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>
    <?= $content ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
