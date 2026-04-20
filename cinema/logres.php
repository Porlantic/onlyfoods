<?php
// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_email'])) {
    session_start();
    require_once 'config.php';
    
    $email = $_POST['login_email'] ?? '';
    $password = $_POST['login_password'] ?? '';
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email and password are required.';
        header('Location: index.php');
        exit();
    }
    
    // Check database for user
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] ?? 'user';
            
            // Redirect based on role
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $_SESSION['login_error'] = 'Invalid credentials';
            header('Location: index.php');
            exit();
        }
    } else {
        $_SESSION['login_error'] = 'Invalid credentials';
        header('Location: index.php');
        exit();
    }
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_name'])) {
    session_start();
    $name = $_POST['register_name'] ?? '';
    $email = $_POST['register_email'] ?? '';
    $password = $_POST['register_password'] ?? '';
    $confirm_password = $_POST['register_confirm_password'] ?? '';
    
    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['register_error'] = 'All fields are required.';
        header('Location: index.php');
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = 'Passwords do not match.';
        header('Location: index.php');
        exit();
    }
    
    // For now, just show a success message
    $_SESSION['register_success'] = 'Registration successful! You can now login.';
    header('Location: index.php');
    exit();
}
?>
<link rel="stylesheet" href="logres.css">

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <h2>Login</h2>
        <form id="loginForm" method="POST">
            <div id="loginError" class="login-error" style="display: none;"></div>
            <div class="form-group">
                <label for="login-email">Email</label>
                <input type="email" id="login-email" name="login_email" required>
            </div>
            <div class="form-group">
                <label for="login-password">Password</label>
                <div class="password-input-container">
                    <input type="password" id="login-password" name="login_password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('login-password')">View</button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>

<!-- Register Modal -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeRegisterModal()">&times;</span>
        <h2>Register</h2>
        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="register-name">Full Name</label>
                <input type="text" id="register-name" name="register_name" required>
            </div>
            <div class="form-group">
                <label for="register-email">Email</label>
                <input type="email" id="register-email" name="register_email" required>
            </div>
            <div class="form-group">
                <label for="register-password">Password</label>
                <div class="password-input-container">
                    <input type="password" id="register-password" name="register_password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('register-password')">View</button>
                </div>
            </div>
            <div class="form-group">
                <label for="register-confirm-password">Confirm Password</label>
                <div class="password-input-container">
                    <input type="password" id="register-confirm-password" name="register_confirm_password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('register-confirm-password')">View</button>
                </div>
            </div>
            <button type="submit" class="btn btn-secondary">Register</button>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const toggleButton = passwordInput.nextElementSibling;
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.textContent = 'Hide';
        } else {
            passwordInput.type = 'password';
            toggleButton.textContent = 'View';
        }
    }

    function showLoginModal() {
        document.getElementById('loginModal').style.display = 'block';
    }
    
    function closeLoginModal() {
        document.getElementById('loginModal').style.display = 'none';
        document.getElementById('loginError').style.display = 'none';
        document.getElementById('loginForm').reset();
    }
    
    function showRegisterModal() {
        document.getElementById('registerModal').style.display = 'block';
    }
    
    function closeRegisterModal() {
        document.getElementById('registerModal').style.display = 'none';
    }
    
    // AJAX Login Handler
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const errorDiv = document.getElementById('loginError');
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // Disable submit button during request
        submitBtn.disabled = true;
        submitBtn.textContent = 'Logging in...';
        
        fetch('login_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            try {
                const data = JSON.parse(text);
                console.log('Parsed JSON:', data);
                if (data.success) {
                    console.log('Redirecting to:', data.redirect);
                    window.location.href = data.redirect;
                } else {
                    // Show error message
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                    
                    // Clear password field for security
                    document.getElementById('login-password').value = '';
                    
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Login';
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response was:', text);
                errorDiv.textContent = 'Invalid credential. Please try again.';
                errorDiv.style.display = 'block';
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Login';
            }
        })
        .catch(error => {
            console.error('Login error:', error);
            errorDiv.textContent = 'Invalid credential. Please try again.';
            errorDiv.style.display = 'block';
            
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Login';
        });
    });
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        const loginModal = document.getElementById('loginModal');
        const registerModal = document.getElementById('registerModal');
        
        if (event.target == loginModal) {
            closeLoginModal();
        }
        if (event.target == registerModal) {
            closeRegisterModal();
        }
    }
</script>
