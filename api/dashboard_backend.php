<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];

// --- NEW: PROCESS SINGLE ITEM BUNDLE RESTOCK ---
if (isset($_GET['custom_restock_id']) && isset($_GET['quantity_restocked'])) {
    $product_id = intval($_GET['custom_restock_id']);
    $qty_restocked = intval($_GET['quantity_restocked']);

    if ($qty_restocked > 0) {
        $stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $qty_restocked, $product_id, $current_user_id);
        $stmt->execute();
    }
    header("Location: dashboard.php");
    exit;
}

// --- PROCESS BULK RESTOCK FORM ARRAY FROM WARNING POPUP ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['batch_restock_submit'])) {
    if (isset($_POST['restock_amounts']) && is_array($_POST['restock_amounts'])) {
        foreach ($_POST['restock_amounts'] as $product_id => $amount) {
            $add_qty = intval($amount);
            if ($add_qty > 0) {
                $p_id = intval($product_id);
                $restock_stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ? AND user_id = ?");
                $restock_stmt->bind_param("iii", $add_qty, $p_id, $current_user_id);
                $restock_stmt->execute();
            }
        }
    }
    header("Location: dashboard.php");
    exit;
}

// --- PROCESS BUNDLE/CUSTOM QUANTITY SALES FROM POP-UP ---
if (isset($_GET['custom_sell_id']) && isset($_GET['quantity_sold'])) {
    $product_id = intval($_GET['custom_sell_id']);
    $qty_sold = intval($_GET['quantity_sold']);

    if ($qty_sold > 0) {
        $check_stmt = $conn->prepare("SELECT name, category, price, quantity FROM products WHERE id = ? AND user_id = ?");
        $check_stmt->bind_param("ii", $product_id, $current_user_id);
        $check_stmt->execute();
        $product_info = $check_stmt->get_result()->fetch_assoc();

        if ($product_info && $product_info['quantity'] >= $qty_sold) {
            $update_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ?, items_sold = items_sold + ? WHERE id = ? AND user_id = ?");
            $update_stmt->bind_param("iiii", $qty_sold, $qty_sold, $product_id, $current_user_id);
            $update_stmt->execute();

            $log_stmt = $conn->prepare("INSERT INTO sales_history (user_id, product_name, category, price_sold, quantity_sold) VALUES (?, ?, ?, ?, ?)");
            $log_stmt->bind_param("issdi", $current_user_id, $product_info['name'], $product_info['category'], $product_info['price'], $qty_sold);
            $log_stmt->execute();
        }
    }
    header("Location: dashboard.php");
    exit;
}

// 1. ADD NEW PRODUCT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $alert_limit = intval($_POST['alert_limit']);
    
    $image_name = 'default.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);
    }

    $stmt = $conn->prepare("INSERT INTO products (user_id, name, category, quantity, price, alert_limit, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdiis", $current_user_id, $name, $category, $quantity, $price, $alert_limit, $image_name);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

// 2. EDIT EXISTING PRODUCT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    $product_id = intval($_POST['product_id']);
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = floatval($_POST['price']);
    $alert_limit = intval($_POST['alert_limit']);
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);
        
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, alert_limit=?, image=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssdiiii", $name, $category, $price, $alert_limit, $image_name, $product_id, $current_user_id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, alert_limit=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssdiii", $name, $category, $price, $alert_limit, $product_id, $current_user_id);
    }
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

// 3. QUICK SINGLE SOLD BUTTON (-1)
if (isset($_GET['quick_sell'])) {
    $product_id = intval($_GET['quick_sell']);
    
    $p_stmt = $conn->prepare("SELECT name, category, price, quantity FROM products WHERE id = ? AND user_id = ?");
    $p_stmt->bind_param("ii", $product_id, $current_user_id);
    $p_stmt->execute();
    $product_info = $p_stmt->get_result()->fetch_assoc();

    if ($product_info && $product_info['quantity'] > 0) {
        $stmt = $conn->prepare("UPDATE products SET quantity = quantity - 1, items_sold = items_sold + 1 WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $product_id, $current_user_id);
        $stmt->execute();

        $log_stmt = $conn->prepare("INSERT INTO sales_history (user_id, product_name, category, price_sold, quantity_sold) VALUES (?, ?, ?, ?, 1)");
        $log_stmt->bind_param("issd", $current_user_id, $product_info['name'], $product_info['category'], $product_info['price']);
        $log_stmt->execute();
    }
    header("Location: dashboard.php");
    exit;
}

// 4. QUICK RESTOCK BUTTON (+1)
if (isset($_GET['quick_restock'])) {
    $product_id = intval($_GET['quick_restock']);
    $stmt = $conn->prepare("UPDATE products SET quantity = quantity + 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $product_id, $current_user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

// 5. DELETE PRODUCT PERMANENTLY
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $current_user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

// QUERY SYSTEM RECORDS FOR CARDS RENDER LOOP
$stmt = $conn->prepare("SELECT * FROM products WHERE user_id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$products = $stmt->get_result();

$total_products = 0;
$products_array = [];
while ($row = $products->fetch_assoc()) {
    $products_array[] = $row;
    $total_products += $row['quantity'];
}

// QUERY SALES TIMELINE RECORD HISTORY
$history_stmt = $conn->prepare("SELECT * FROM sales_history WHERE user_id = ? ORDER BY sold_at DESC");
$history_stmt->bind_param("i", $current_user_id);
$history_stmt->execute();
$history_res = $history_stmt->get_result();

$total_revenue = 0.00;
$sales_history_array = [];
while ($h_row = $history_res->fetch_assoc()) {
    $sales_history_array[] = $h_row;
    $total_revenue += ($h_row['price_sold'] * $h_row['quantity_sold']);
}
?>