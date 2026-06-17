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
    <style>
        /* EXPERIMENT OVERRIDES */
        html, body, body[style] { 
            font-family: 'Comfortaa', 'Segoe UI', Arial, sans-serif !important; 
            background: #22c55e !important; /* Aggressive Neon Green */
            display: flex !important; 
            justify-content: center !important; 
            align-items: center !important; 
            height: 100vh !important; 
            margin: 0 !important; 
        }

        .form-container { 
            background: #ffffff !important; 
            padding: 50px 45px !important; 
            border-radius: 28px !important; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important; 
            width: 460px !important; 
            text-align: center !important;
            border: 1px solid #cbd5e1 !important;
            box-sizing: border-box !important;
        }
        
        h2 { color: #1e293b !important; margin-bottom: 12px !important; font-size: 28px !important; font-weight: 700 !important; margin-top: 0 !important;}
        p.subtitle { color: #94a3b8 !important; margin-bottom: 35px !important; font-size: 15px !important; margin-top: 0 !important; }
        
        .password-wrapper {
            position: relative !important;
            width: 100% !important;
        }
        
        input { 
            width: 100% !important; 
            padding: 16px !important; 
            margin: 12px 0 !important; 
            border: 2px solid #cbd5e1 !important; 
            border-radius: 16px !important; 
            box-sizing: border-box !important; 
            font-size: 15px !important; 
            background: #f8fafc !important;
            color: #334155 !important;
            font-family: inherit !important;
        }
        
        .toggle-password {
            position: absolute !important;
            right: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            cursor: pointer !important;
            color: #64748b !important;
            font-size: 12px !important;
            user-select: none !important;
            background: #e2e8f0 !important;
            padding: 6px 12px !important;
            border-radius: 10px !important;
            font-weight: bold !important;
        }
        
        /* Direct button targeting for testing red swap */
        button.submit-btn, button, button[style], input[type="submit"] { 
            width: 100% !important; 
            padding: 16px !important; 
            margin-top: 25px !important;
            background: #ef4444 !important; /* Bright Red */
            color: white !important; 
            border: none !important; 
            border-radius: 16px !important; 
            cursor: pointer !important; 
            font-weight: bold !important; 
            font-size: 16px !important;
            box-shadow: none !important;
            font-family: inherit !important;
        }
        
        .hr-container {
            display: flex !important;
            align-items: center !important;
            text-align: center !important;
            margin: 35px 0 !important;
            color: #94a3b8 !important;
            font-size: 13px !important;
        }
        .hr-container::before, .hr-container::after {
            content: '' !important;
            flex: 1 !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }
        .hr-container:not(:empty)::before { margin-right: .8em !important; }
        .hr-container:not(:empty)::after { margin-left: .8em !important; }
        
        .reg-btn, a { 
            display: inline-block !important;
            width: 100% !important;
            padding: 15px !important;
            background: #ef4444 !important; /* Bright Red */
            color: white !important; 
            border: none !important; 
            border-radius: 16px !important; 
            text-decoration: none !important;
            box-sizing: border-box !important;
            font-weight: bold !important;
            font-size: 15px !important;
            text-align: center !important;
        }
        .error-msg { color: #e11d48 !important; background: #fff1f2 !important; padding: 12px !important; border-radius: 12px !important; font-size: 14px !important; margin-bottom: 20px !important; border: 1px solid #ffe4e6 !important; }
    </style>
</head>
<body>
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
