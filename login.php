<?php
/**
 * Modern Login Page - Professional Design
 * User authentication with premium UI/UX
 */

session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/models/User.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!validate_csrf($csrf_token)) {
        $error = 'Requête invalide. Veuillez réessayer.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Veuillez entrer le nom d\'utilisateur et le mot de passe.';
    } else {
        $userModel = new User();
        $user = $userModel->authenticate($username, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] === 'boss') {
                header('Location: index.php?page=dashboard');
            } else {
                header('Location: index.php?page=pos');
            }
            exit;
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe invalide.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/modern-design.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-wrapper {
            width: 100%;
            max-width: 450px;
        }
        
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        
        .login-logo {
            font-size: 40px;
            margin-bottom: 16px;
            animation: slideInDown 0.6s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .login-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s ease-in-out;
            background: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #f0f9ff;
            background: white;
        }
        
        .form-input::placeholder {
            color: #9ca3af;
        }
        
        .login-submit {
            width: 100%;
            padding: 12px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .login-submit:hover {
            background: #1e40af;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .login-submit:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 12px 16px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            color: #7f1d1d;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideInDown 0.3s ease-out;
        }
        
        .alert-icon {
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .login-footer {
            padding: 20px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        
        .login-credentials {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }
        
        .login-credential-item {
            margin: 4px 0;
            font-weight: 500;
            color: #374151;
        }
        
        .login-credential-label {
            color: #6b7280;
            font-weight: 400;
        }
        
        .form-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .form-checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #2563eb;
        }
        
        @media (max-width: 480px) {
            .login-card {
                border-radius: 12px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-logo {
                font-size: 32px;
            }
            
            .login-title {
                font-size: 20px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .login-footer {
                padding: 16px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-wrapper">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">🏪</div>
                    <h1 class="login-title"><?php echo APP_NAME; ?></h1>
                    <p class="login-subtitle">Gestion de Stock & Caisse</p>
                </div>
                
                <div class="login-body">
                    <?php if ($error): ?>
                        <div class="alert">
                            <span class="alert-icon">⚠️</span>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="login-form">
                        <?php echo csrf_field(); ?>
                        
                        <div class="form-group">
                            <label class="form-label" for="username">Nom d'utilisateur</label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="form-input" 
                                placeholder="Entrez votre nom d'utilisateur"
                                required 
                                autofocus
                            >
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="password">Mot de passe</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="Entrez votre mot de passe"
                                required
                            >
                        </div>
                        
                        <div class="form-remember">
                            <input type="checkbox" id="remember" name="remember" class="form-checkbox">
                            <label for="remember">Se souvenir de moi</label>
                        </div>
                        
                        <button type="submit" class="login-submit">Connexion</button>
                    </form>
                </div>
                
                <div class="login-footer">
                    <div class="login-credentials">
                        <div class="login-credential-label">Identifiants de test :</div>
                        <div class="login-credential-item">
                            👨‍💼 Admin: <strong>admin</strong> / <strong>password</strong>
                        </div>
                        <div class="login-credential-item">
                            👤 Employé: <strong>employee</strong> / <strong>password</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
