<?php
include 'db.php';
$message = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $business_type = trim($_POST['business_type']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_slides > 0 || $checkStmt->num_rows > 0) {
            $message = "Username is already taken!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, business_type, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $business_type, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Account created successfully! Redirecting...";
                header("refresh:2;url=login.php");
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Account - Inventory System</title>
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
            padding: 45px; 
            border-radius: 28px; 
            box-shadow: 0 10px 30px rgba(244, 143, 177, 0.15); 
            width: 460px; 
            text-align: center;
            border: 1px solid #ffe4e6;
            box-sizing: border-box;
        }
        h2 { color: #4a5568; margin-bottom: 12px; font-size: 28px; font-weight: 700; margin-top: 0; }
        p.subtitle { color: #a0aec0; margin-bottom: 30px; font-size: 15px; margin-top: 0; }
        
        .password-wrapper {
            position: relative;
            width: 100%;
        }
        input { 
            width: 100%; 
            padding: 16px; 
            margin: 10px 0; 
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
        /* Extra padding right on password fields to ensure text never runs under the eye icon */
        input[type="password"], input[type="text"].pass-input {
            padding-right: 50px;
        }
        
        /* Modern positioning settings for the eyes */
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
            z-index: 10;
        }
        .toggle-password:hover {
            color: #f48fb1;
        }
        
        button.submit-btn { 
            width: 100%; 
            padding: 16px; 
            margin-top: 20px;
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
            margin: 25px 0;
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
        
        .login-link { 
            color: #f48fb1;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            transition: color 0.2s ease;
        }
        .login-link:hover { color: #f06292; }
        
        .error-msg { color: #e11d48; background: #fff1f2; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 15px; border: 1px solid #ffe4e6; }
        .success-msg { color: #15803d; background: #f0fdf4; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 15px; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Account</h2>
        <p class="subtitle">Set up your private inventory space</p>
        
        <?php 
            if(!empty($message)) echo "<div class='error-msg'>$message</div>"; 
            if(!empty($success)) echo "<div class='success-msg'>$success</div>"; 
        ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Choose a Username" required>
            <input type="text" name="business_type" placeholder="Business Type / Job" required>
            
            <div class="password-wrapper">
                <input type="password" id="reg-pass" name="password" class="pass-input" placeholder="Create Password" required>
                <i class="fa-regular fa-eye toggle-password" onclick="toggleRegisterPassword('reg-pass', this)"></i>
            </div>
            
            <div class="password-wrapper">
                <input type="password" id="confirm-pass" name="confirm_password" class="pass-input" placeholder="Confirm Password" required>
                <i class="fa-regular fa-eye toggle-password" onclick="toggleRegisterPassword('confirm-pass', this)"></i>
            </div>
            
            <button type="submit" class="submit-btn">Get Started</button>
        </form>
        
        <div class="hr-container">Already have an account?</div>
        
        <a href="login.php" class="login-link">Log In Here</a>
    </div>

    <script>
        function toggleRegisterPassword(inputId, iconElement) {
            var input = document.getElementById(inputId);
            
            if (input.type === "password") {
                input.type = "text";
                // Swaps classes to solid slashed eye when visible
                iconElement.classList.remove("fa-regular", "fa-eye");
                iconElement.classList.add("fa-solid", "fa-eye-slash");
            } else {
                input.type = "password";
                // Swaps back to clean outlined regular eye when hidden
                iconElement.classList.remove("fa-solid", "fa-eye-slash");
                iconElement.classList.add("fa-regular", "fa-eye");
            }
        }
    </script>
</body>
</html>
