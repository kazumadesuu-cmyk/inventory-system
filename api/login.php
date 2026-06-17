<?php
include 'db.php';
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

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
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome - Inventory System</title>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Comfortaa', 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #fff5f5 0%, #fff0f3 100%); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .form-container { 
            background: #fff; 
            padding: 50px 45px; 
            border-radius: 28px; 
            box-shadow: 0 10px 30px rgba(244, 143, 177, 0.15); 
            width: 460px; 
            text-align: center;
            border: 1px solid #ffe4e6;
            box-sizing: border-box;
        }
        h2 { color: #4a5568; margin-bottom: 12px; font-size: 28px; font-weight: 700; }
        p.subtitle { color: #a0aec0; margin-bottom: 35px; font-size: 15px; margin-top: 0; }
        
        .password-wrapper {
            position: relative;
            width: 100%;
        }
        input { 
            width: 100%; 
            padding: 16px; 
            padding-right: 50px; /* Extends padding so text never overlaps the eye icon */
            margin: 12px 0; 
            border: 2px solid #fff1f2; 
            border-radius: 16px; 
            box-sizing: border-box; 
            font-size: 15px; 
            background: #fffafb;
            transition: all 0.2s ease;
            color: #4a5568;
            font-family: inherit;
        }
        input:focus {
            border-color: #fbcfe8;
            background: #fff;
            outline: none;
        }
        
        /* Modern icon placement settings */
        .toggle-password {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #b1bccc;
            font-size: 16px;
            user-select: none;
            transition: color 0.2s ease;
        }
        .toggle-password:hover {
            color: #f48fb1; /* Icon turns pink when hovered */
        }
        
        button.submit-btn { 
            width: 100%; 
            padding: 16px; 
            margin-top: 25px;
            background: #f48fb1; 
            color: white; 
            border: none; 
            border-radius: 16px; 
            cursor: pointer; 
            font-weight: bold; 
            font-size: 16px;
            box-shadow: 0 6px 16px rgba(244, 143, 177, 0.25);
            transition: transform 0.1s ease, background 0.2s ease;
            font-family: inherit;
        }
        button.submit-btn:hover { background: #f06292; }
        
        .hr-container {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 35px 0;
            color: #a0aec0;
            font-size: 13px;
        }
        .hr-container::before, .hr-container::after {
            content: '';
            flex: 1;
            border-bottom: 2px solid #fff1f2;
        }
        .hr-container:not(:empty)::before { margin-right: .8em; }
        .hr-container:not(:empty)::after { margin-left: .8em; }
        
        .reg-btn { 
            display: inline-block;
            width: 100%;
            padding: 15px;
            background: transparent;
            color: #f48fb1;
            border: 2px solid #f48fb1;
            border-radius: 16px;
            text-decoration: none;
            box-sizing: border-box;
            font-weight: bold;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        .reg-btn:hover {
            background: #f48fb1;
            color: white;
        }
        .error-msg { color: #e11d48; background: #fff1f2; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; border: 1px solid #ffe4e6; }
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
                <i class="fa-regular fa-eye toggle-password" id="eye-icon" onclick="togglePassword()"></i>
            </div>
            
            <button type="submit" class="submit-btn">Log In</button>
        </form>
        
        <div class="hr-container">New to the platform?</div>
        
        <a href="register.php" class="reg-btn">Create an Account</a>
    </div>

    <script>
        function togglePassword() {
            var input = document.getElementById("login-pass");
            var icon = document.getElementById("eye-icon");
            
            if (input.type === "password") {
                input.type = "text";
                // Changes the icon to a slashed eye when password is shown
                icon.classList.remove("fa-regular", "fa-eye");
                icon.classList.add("fa-solid", "fa-eye-slash");
            } else {
                input.type = "password";
                // Changes it back to a standard regular eye when hidden
                icon.classList.remove("fa-solid", "fa-eye-slash");
                icon.classList.add("fa-regular", "fa-eye");
            }
        }
    </script>
</body>
</html>
