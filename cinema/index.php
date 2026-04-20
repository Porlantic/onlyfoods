<?php
session_start();

// Check for login/register messages
$login_error = $_SESSION['login_error'] ?? '';
$register_error = $_SESSION['register_error'] ?? '';
$register_success = $_SESSION['register_success'] ?? '';

// Clear session messages
unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['register_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema - Login & Register</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <?php if ($login_error): ?>
        <div class="message error-message"><?php echo htmlspecialchars($login_error); ?></div>
    <?php endif; ?>
    
    <?php if ($register_error): ?>
        <div class="message error-message"><?php echo htmlspecialchars($register_error); ?></div>
    <?php endif; ?>
    
    <?php if ($register_success): ?>
        <div class="message success-message"><?php echo htmlspecialchars($register_success); ?></div>
    <?php endif; ?>
    
    <header class="main-header">
        <div class="header-container">
            <div class="logo-section">
                <h1 class="logo">Cinema</h1>
            </div>
            
            <nav class="auth-nav">
                <button class="auth-btn login-btn" onclick="showLoginModal()">Login</button>
                <button class="auth-btn register-btn" onclick="showRegisterModal()">Register</button>
            </nav>
        </div>
    </header>
    
    <main class="main-content">
        <?php include 'nowshowing.php'; ?>
    </main>
    
    <?php include 'logres.php'; ?>
<script>
        function showLoginModal() {
            document.getElementById('loginModal').style.display = 'block';
        }
        
        function showRegisterModal() {
            document.getElementById('registerModal').style.display = 'block';
        }
        
        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }
        
        function closeRegisterModal() {
            document.getElementById('registerModal').style.display = 'none';
        }
    </script>
</body>
</html>
