<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stock & Sales Management</title>
    <link rel="stylesheet" href="/shop_v2/public/css/style.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .login-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo {
            font-size: 32px;
            font-weight: 700;
            color: #4F46E5;
            margin-bottom: 10px;
        }
        
        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 8px;
        }
        
        .login-subtitle {
            font-size: 14px;
            color: #64748B;
        }
        
        .login-form {
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="login-logo">📦</div>
                <h1 class="login-title">Stock & Sales</h1>
                <p class="login-subtitle">Management System v1.0.0</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo SecurityHelper::escapeHtml($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">Sign In</button>
            </form>

            <div style="text-align: center; font-size: 12px; color: #64748B;">
                <p>Demo Credentials:</p>
                <p><strong>Admin:</strong> admin / password</p>
                <p><strong>Employee:</strong> employee / password</p>
            </div>
        </div>
    </div>
</body>
</html>
