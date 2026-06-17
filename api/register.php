<?php
include 'db.php';
$message = "";
$show_success_modal = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $business_type = trim($_POST['business_type']); 

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } elseif (empty($business_type)) {
        $message = "Please specify your business type / job.";
    } else {
        try {
            $all_users = firebase_request('users');
            $user_exists = false;

            if ($all_users && is_array($all_users)) {
                foreach ($all_users as $user) {
                    if (isset($user['username']) && strtolower($user['username']) === strtolower($username)) {
                        $user_exists = true;
                        break;
                    }
                }
            }

            if ($user_exists) {
                $message = "Username already taken!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $new_user_data = [
                    'username' => $username,
                    'password' => $hashed_password,
                    'business_type' => $business_type
                ];

                firebase_request('users', 'POST', $new_user_data);
                $show_success_modal = true;
            }
        } catch (Exception $e) {
            $message = "Error creating account: " . $e->getMessage();
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
        body { font-family: 'Comfortaa', sans-serif; background: linear-gradient(135deg, #fff5f5 0%, #fff0f3 100%); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .form-container { background: #fff; padding: 50px 45px; border-radius: 28px; box-shadow: 0 10px 30px rgba(244, 143, 177, 0.15); width: 460px; text-align: center; border: 1px solid #ffe4e6; box-sizing: border-box; }
        h2 { color: #4a5568; font-size: 28px; font-weight: 700; }
        p.subtitle { color: #a0aec0; font-size: 15px; }
        input { width: 100%; padding: 16px; margin: 12px 0; border: 2px solid #fff1f2; border-radius: 16px; box-sizing: border-box; font-size: 15px; background: #fffafb; color: #4a5568; font-family: inherit; }
        button { width: 100%; padding: 16px; margin-top: 25px; background: #f48fb1; color: white; border: none; border-radius: 16px; cursor: pointer; font-weight: bold; font-size: 16px; }
        .error-msg { color: #e11d48; background: #fff1f2; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.15); display: flex; justify-content: center; align-items: center; backdrop-filter: blur(3px); }
        .modal-box { background: white; padding: 45px 40px; border-radius: 24px; text-align: center; width: 360px; }
        .modal-btn { background: #f48fb1; color: white; padding: 12px 28px; border: none; border-radius: 12px; font-weight: bold; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Account</h2>
        <p class="subtitle">Set up your private inventory space</p>
        <?php if($message) echo "<div class='error-msg'>$message</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Choose a Username" required>
            <input type="text" name="business_type" placeholder="Business Type / Job" required>
            <input type="password" name="password" placeholder="Create Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Get Started</button>
        </form>
    </div>
    <?php if ($show_success_modal): ?>
        <div class="modal-overlay">
            <div class="modal-box">
                <h3>Account Created!</h3>
                <p>Your private workspace is ready.</p>
                <a href="login.php" class="modal-btn">Go to Login</a>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>