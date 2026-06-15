<?php
include 'db.php';
$message = "";
$show_success_modal = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // Capture the custom or recommended business type
    $business_type = trim($_POST['business_type']); 

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } elseif (empty($business_type)) {
        $message = "Please specify your business type / job.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Username already taken!";
        } else {
            // Note: Make sure your 'users' table has a 'business_type' column to save this data
            $stmt = $conn->prepare("INSERT INTO users (username, password, business_type) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $business_type);
            if ($stmt->execute()) {
                $show_success_modal = true;
            } else {
                $message = "Error creating account.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Join Us! - Inventory System</title>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">
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
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #718096;
            font-size: 12px;
            user-select: none;
            background: #ffe4e6;
            padding: 6px 12px;
            border-radius: 10px;
            font-weight: bold;
        }
        button { 
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
        button:hover { background: #f06292; }
        .back-link { display: block; text-align: center; margin-top: 25px; color: #a0aec0; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #718096; text-decoration: underline; }
        .error-msg { color: #e11d48; background: #fff1f2; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; border: 1px solid #ffe4e6; }

        /* --- SUCCESS MODAL BOX --- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.15); display: flex; justify-content: center;
            align-items: center; z-index: 1000; backdrop-filter: blur(3px);
        }
        .modal-box {
            background: white; padding: 45px 40px; border-radius: 24px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.06); text-align: center; width: 360px;
            animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        .modal-box h3 { color: #2d3748; margin-top: 0; font-size: 22px; font-weight: 700; }
        .modal-box p { color: #718096; font-size: 15px; line-height: 1.6; margin-bottom: 25px; }
        .modal-btn {
            background: #f48fb1; color: white; padding: 12px 28px; border: none;
            border-radius: 12px; font-weight: bold; font-size: 14px; text-decoration: none;
            display: inline-block;
        }
        @keyframes popIn {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Create Account</h2>
        <p class="subtitle">Set up your private inventory space</p>
        
        <?php if($message) echo "<div class='error-msg'>$message</div>"; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Choose a Username" required>
            
            <input type="text" name="business_type" list="business-options" placeholder="Type or Select Business Type / Job" required>
            <datalist id="business-options">
                <option value="Homemade Food / Bakery">
                <option value="Handmade Crafts / Arts">
                <option value="Cafe / Beverage Corner">
                <option value="Boutique / Accessories">
                <option value="Cosmetics / Beauty Shop">
            </datalist>

            <div class="password-wrapper">
                <input type="password" id="reg-pass" name="password" placeholder="Create Password" required>
                <span class="toggle-password" onclick="togglePassword('reg-pass', this)">Show</span>
            </div>

            <div class="password-wrapper">
                <input type="password" id="reg-confirm" name="confirm_password" placeholder="Confirm Password" required>
                <span class="toggle-password" onclick="togglePassword('reg-confirm', this)">Show</span>
            </div>
            
            <button type="submit">Get Started</button>
        </form>
        <a href="login.php" class="back-link">Already have an account? Log In</a>
    </div>

    <?php if ($show_success_modal): ?>
        <div class="modal-overlay">
            <div class="modal-box">
                <h3>Account Created!</h3>
                <p>Your private workspace is ready for setup. Let us head over to the dashboard to organize your details.</p>
                <a href="login.php" class="modal-btn">Go to Login</a>
            </div>
        </div>
    <?php endif; ?>

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