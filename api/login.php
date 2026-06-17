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
        /* Force background to bypass any active cache structures */
        html, body, body.force-pink-body { 
            font-family: 'Comfortaa', 'Segoe UI', Arial, sans-serif !important; 
            background: linear-gradient(135deg, #fff0f2 0%, #fbcfe8 100%) !important; 
            display: flex !important; 
            justify-content: center !important; 
            align-items: center !important; 
            height: 100vh !important; 
            margin: 0 !important; 
            width: 100vw !important;
        }

        /* Highly-specific nested selectors to override external style.css */
        body.force-pink-body .custom-pink-wrapper .form-container { 
            background: #ffffff !important; 
            padding: 50px 45px !important; 
            border-radius: 28px !important; 
            box-shadow: 0 10px 30px rgba(244, 143, 177, 0.15) !important; 
            width: 460px !important; 
            text-align: center !important;
            border: 1px solid #ffe4e6 !important;
            box-sizing: border-box !important;
            display: block !important;
        }
        
        body.force-pink-body .custom-pink-wrapper h2 { color: #1e293b !important; margin-bottom: 12px !important; font-size: 28px !important; font-weight: 700 !important; margin-top: 0 !important;}
        body.force-pink-body .custom-pink-wrapper p.subtitle { color: #94a3b8 !important; margin-bottom: 35px !important; font-size: 15px !important; margin-top: 0 !important; }
        
        body.force-pink-body .custom-pink-wrapper .password-wrapper {
            position: relative !important;
            width: 100% !important;
        }
        
        body.force-pink-body .custom-pink-wrapper input { 
            width: 100% !important; 
            padding: 16px !important; 
            margin: 12px 0 !important; 
            border: 2px solid #fff1f2 !important; 
            border-radius: 16px !important; 
            box-sizing: border-box !important; 
            font-size: 15px !important; 
            background: #fff8f9 !important;
            transition: all 0.2s ease !important;
            color: #334155 !important;
            font-family: inherit !important;
        }
        body.force-pink-body .custom-pink-wrapper input:focus {
            border-color: #f48fb1 !important;
            background: #ffffff !important;
            outline: none !important;
            box-shadow: 0 0 0 4px rgba(244, 143, 177, 0.15) !important;
        }
        
        body.force-pink-body .custom-pink-wrapper .toggle-password {
            position: absolute !important;
            right: 15px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            cursor: pointer !important;
            color: #f48fb1 !important;
            font-size: 12px !important;
            user-select: none !important;
            background: #fff0f2 !important;
            padding: 6px 12px !important;
            border-radius: 10px !important;
            font-weight: bold !important;
            transition: all 0.2s ease !important;
            display: inline-block !important;
        }
        
        /* Direct button targeting using body class reference */
        body.force-pink-body .custom-pink-wrapper button.submit-btn { 
            width: 100% !important; 
            padding: 16px !important; 
            margin-top: 25px !important;
            background: #f48fb1 !important; 
            color: white !important; 
            border: none !important; 
            border-radius: 16px !important; 
            cursor: pointer !important; 
            font-weight: bold !important; 
            font-size: 16px !important;
            box-shadow: 0 6px 16px rgba(244, 143, 177, 0.25) !important;
            transition: transform 0.1s ease, background 0.2s ease !important;
            font-family: inherit !important;
            display: block !important;
        }
        body.force-pink-body .custom-pink-wrapper button.submit-btn:hover { background: #e91e63 !important; }
        
        body.force-pink-body .custom-pink-wrapper .hr-container {
            display: flex !important;
            align-items: center !important;
            text-align: center !important;
            margin: 35px 0 !important;
            color: #94a3b8 !important;
            font-size: 13px !important;
        }
        body.force-pink-body .custom-pink-wrapper .hr-container::before, 
        body.force-pink-body .custom-pink-wrapper .hr-container::after {
            content: '' !important;
            flex: 1 !important;
            border-bottom: 2px solid #fff1f2 !important;
        }
        
        body.force-pink-body .custom-pink-wrapper .reg-btn { 
            display: inline-block !important;
            width: 100% !important;
            padding: 15px !important;
            background: transparent !important;
            color: #f48fb1 !important;
            border: 2px solid #f48fb1 !important;
            border-radius: 16px !important;
            text-decoration: none !important;
            box-sizing: border-box !important;
            font-weight: bold !important;
            font-size: 15px !important;
            transition: all 0.2s ease !important;
            text-align: center !important;
        }
        body.force-pink-body .custom-pink-wrapper .reg-btn:hover { background: #f48fb1 !important; color: white !important; }
        body.force-pink-body .custom-pink-wrapper .error-msg { color: #e11d48 !important; background: #fff1f2 !important; padding: 12px !important; border-radius: 12px !important; font-size: 14px !important; margin-bottom: 20px !important; border: 1px solid #ffe4e6 !important; }
    </style>
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
