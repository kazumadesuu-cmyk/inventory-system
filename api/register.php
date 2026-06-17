<?php
// Securely link your Firebase db connection file
include __DIR__ . '/db.php';
$message = "";
$show_success_modal = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Check if the user already exists inside your Firebase database node
            $existingUser = firebase_request("users/" . urlencode($username));

            if ($existingUser !== null) {
                $message = "Username already taken!";
            } else {
                // package up your user packet for Firebase
                $newUserPacket = [
                    "password" => $hashed_password
                ];

                // Save data to Firebase using PUT under the username
                firebase_request("users/" . urlencode($username), "PUT", $newUserPacket);
                $show_success_modal = true;
            }
        } catch (Exception $e) {
            $message = "Database connection offline.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Us! - Inventory System</title>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Comfortaa', 'Segoe UI', Arial, sans-serif !important; 
            background: linear-gradient(135deg, #fff0f2 0%, #fbcfe8 100%) !important; 
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
            box-shadow: 0 10px 30px rgba(244, 143, 177, 0.15) !important; 
            width: 460px !important; 
            text-align: center !important;
            border: 1px solid #ffe4e6 !important;
            box-sizing: border-box !important;
        }
        h2 { color: #1e293b !important; margin-bottom: 12px !important; font-size: 28px !important; font-weight: 700 !important; margin-top: 0; }
        p.subtitle { color: #94a3b8 !important; margin-bottom: 35px !important; font-size: 15px !important; margin-top: 0 !important; }
        
        .password-wrapper {
            position: relative !important;
            width: 100% !important;
        }
        input { 
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
        input:focus {
            border-color: #f48fb1 !important;
            background: #ffffff !important;
            outline: none !important;
            box-shadow: 0 0 0 4px rgba(244, 143, 177, 0.15) !important;
        }
        .toggle-password {
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
        }
        .toggle-password:hover {
            background: #f48fb1 !important;
            color: #ffffff !important;
        }
        button { 
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
        }
        button:hover { background: #e91e63 !important; }
        .back-link { display: block !important; text-align: center !important; margin-top: 25px !important; color: #94a3b8 !important; text-decoration: none !important; font-size: 14px !important; }
        .back-link:hover { color: #f48fb1 !important; text-decoration: underline !important; }
        .error-msg { color: #e11d48 !important; background: #fff1f2 !important; padding: 12px !important; border-radius: 12px !important; font-size: 14px !important; margin-bottom: 20px !important; border: 1px solid #ffe4e6 !important; }

        /* --- SUCCESS MODAL BOX --- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.2); display: flex; justify-content: center;
            align-items: center; z-index: 1000; backdrop-filter: blur(4px);
        }
        .modal-box {
            background: white; padding: 45px 40px; border-radius: 24px;
            box-shadow: 0 12px 35px rgba(244, 143, 177, 0.15); text-align: center; width: 360px;
            animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        .modal-box h3 { color: #1e293b; margin-top: 0; font-size: 22px; font-weight: 700; }
        .modal-box p { color: #64748b; font-size: 15px; line-height: 1.6; margin-bottom: 25px; }
        .modal-btn {
            background: #f48fb1; color: white; padding: 12px 28px; border: none;
            border-radius: 12px; font-weight: bold; font-size: 14px; text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(244, 143, 177, 0.2);
            transition: all 0.2s ease;
        }
        .modal-btn:hover { background: #e91e63; }
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
        
        <?php if(!empty($message)) echo "<div class='error-msg'>$message</div>"; ?>
        
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Choose a Username" required>
            
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
