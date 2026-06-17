<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // 1. Fetch all users from your Asia-Southeast Firebase node
        $all_users = firebase_request('users');
        $user_found = false;

        if ($all_users) {
            // Firebase returns data as an associative array where $id is the alphanumeric unique key string
            foreach ($all_users as $id => $user) {
                if (isset($user['username']) && strtolower($user['username']) === strtolower($username)) {
                    // 2. Verify the hashed password securely
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $id; // Save the unique Firebase string ID as the session pointer
                        $_SESSION['username'] = $user['username'];
                        
                        $user_found = true;
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $message = "Oops! Wrong password.";
                        $user_found = true;
                        break;
                    }
                }
            }
        }

        if (!$user_found) {
            $message = "We couldn't find that user.";
        }

    } catch (Exception $e) {
        $message = "Login Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome - Inventory System</title>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Comfortaa', 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .form-container { 
            background: #fff; 
            padding: 50px 45px; \
            border-radius: 28px; 
            box-shadow: 0 10px 30px rgba(2, 132, 199, 0.1); 
            width: 460px; 
            text-align: center;
            border: 1px solid #e0f2fe;
            box-sizing: border-box;
        }
        h2 { color: #1e293b; margin-bottom: 12px; font-size: 28px; font-weight: 700; }
        p.subtitle { color: #64748b; margin-bottom: 35px; font-size: 15px; margin-top: 0; }
        
        .password-wrapper {
            position: relative;
            width: 100%;
        }
        input { 
            width: 100%; 
            padding: 16px; 
            margin: 12px 0; 
            border: 2px solid #f1f5f9; 
            border-radius: 16px; 
            box-sizing: border-box; 
            font-size: 15px; 
            background: #f8fafc;
            transition: all 0.2s ease;
            color: #1e293b;
            font-family: inherit;
        }
        input:focus {
            border-color: #38bdf8;
            background: #fff;
            outline: none;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #0284c7;
            font-size: 12px;
            user-select: none;
            background: #e0f2fe;
            padding: 6px 12px;
            border-radius: 10px;
            font-weight: bold;
        }
        .submit-btn { 
            width: 100%;
            padding: 16px;
            margin-top: 25px;
            background: #0284c7; 
            color: white; 
            border: none; \
            border-radius: 16px;
            cursor: pointer; 
            font-weight: bold; 
            font-size: 16px; 
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.2);
            transition: background 0.2s ease;
            font-family: inherit;
        }
        .submit-btn:hover { background: #0369a1; }
        
        .hr-container {
            margin: 30px 0 20px 0;
            color: #94a3b8;
            font-size: 13px;
            position: relative;
        }
        
        .reg-btn { 
            display: inline-block;
            width: 100%;
            padding: 14px;
            background: transparent; 
            color: #0284c7; 
            border: 2px solid #0284c7; 
            border-radius: 16px;
            cursor: pointer; 
            font-weight: bold; 
            font-size: 14px; 
            text-decoration: none;
            box-sizing: border-box;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .reg-btn:hover { background: #f0f9ff; }
        .error-msg { color: #ef4444; background: #fef2f2; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; border: 1px solid #fee2e2; text-align: left; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Welcome Back</h2>
        <p class="subtitle">Log in to manage your inventory space</p>
        
        <?php if($message) echo "<div class='error-msg'>$message</div>"; ?>
        
        <form method="POST">
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