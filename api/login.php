<?php
include 'db.php';
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (isset($conn)) {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit;
            } else {
                $message = "Oops! Wrong password.";
            }
        } else {
            $message = "We couldn't find that user.";
        }
    } else {
        $message = "Database connection error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Inventory System</title>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body class="force-pink-body">
    <div class="custom-pink-wrapper">
        <div class="form-container">
            <h2>Welcome Back</h2>
            <p class="subtitle">Log in to manage your inventory space</p>
            
            <?php if(!empty($message)) echo "<div class='error-msg'>$message</div>"; ?>
            
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                
                <div class="password-wrapper">
                    <input type="password" id="login-pass" name="password" placeholder="Password" required>
                    <span class="toggle-password" onclick="togglePassword('login-pass', this)">Show</span>
                </div>
                
                <button type="submit" class="submit-btn">Log In</button>
            </form>
            
            <div class="hr-container">New to the platform?</div>
            
            <a href="register.php" class="reg-btn">Create an Account</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId, element) {
            var input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                element.textContent = "Hide";
            } else {
                input.type = "password";
                element.textContent = "Show";
            }
        }
    </script>
</body>
</html>
