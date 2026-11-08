<?php
// login.php
require_once 'includes/auth.php';
require_once 'config/database.php';

// Kung naka-login na, i-redirect sa ilang dashboard
if (is_logged_in()) {
    header('Location: ' . (is_admin() ? '/admin/dashboard.php' : '/resident/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, full_name, password_hash, role FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful! Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            // Regenerate session ID for security
            session_regenerate_id(true);

            header('Location: ' . ($user['role'] === 'admin' ? '/admin/dashboard.php' : '/resident/dashboard.php'));
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-box">
    <h2>Login</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        
        <button type="submit" class="btn-primary">Log In</button>
    </form>
    <p>Don't have an account? <a href="/register.php">Register here</a></p>
</div>

<?php include 'includes/footer.php'; ?>