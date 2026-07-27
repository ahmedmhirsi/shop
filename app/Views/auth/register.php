<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Stock & Sales Management</title>
    <link rel="stylesheet" href="/shop_v2/public/css/style.css">
    <style>
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .register-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-title {
            font-size: 24px;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 8px;
        }
        
        .register-subtitle {
            font-size: 14px;
            color: #64748B;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <h1 class="register-title">Create User Account</h1>
                <p class="register-subtitle">Add new team member</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo SecurityHelper::escapeHtml($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo SecurityHelper::escapeHtml($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" placeholder="Enter full name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="employee">Employee</option>
                        <option value="boss">Boss (Admin)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
                <a href="/shop_v2/index.php?url=dashboard" class="btn btn-secondary btn-block" style="margin-top: 10px;">Back</a>
            </form>
        </div>
    </div>
</body>
</html>
