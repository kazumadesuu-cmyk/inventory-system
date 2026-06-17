<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_id = $_SESSION['user_id'];

// --- NEW: PROCESS SINGLE ITEM BUNDLE RESTOCK ---
if (isset($_GET['custom_restock_id']) && isset($_GET['quantity_restocked'])) {
    $product_id = $_GET['custom_restock_id'];
    $qty_restocked = intval($_GET['quantity_restocked']);

    if ($qty_restocked > 0) {
        $product = firebase_request("products/$product_id");
        if ($product && $product['user_id'] === $current_user_id) {
            $new_qty = intval($product['quantity'] ?? 0) + $qty_restocked;
            firebase_request("products/$product_id", 'PATCH', ['quantity' => $new_qty]);
        }
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
                $product = firebase_request("products/$product_id");
                if ($product && $product['user_id'] === $current_user_id) {
                    $new_qty = intval($product['quantity'] ?? 0) + $add_qty;
                    firebase_request("products/$product_id", 'PATCH', ['quantity' => $new_qty]);
                }
            }
        }
    }
    header("Location: dashboard.php");
    exit;
}

// --- PROCESS BUNDLE/CUSTOM QUANTITY SALES FROM POP-UP ---
if (isset($_GET['custom_sell_id']) && isset($_GET['quantity_sold'])) {
    $product_id = $_GET['custom_sell_id'];
    $qty_sold = intval($_GET['quantity_sold']);

    if ($qty_sold > 0) {
        $product_info = firebase_request("products/$product_id");

        if ($product_info && $product_info['user_id'] === $current_user_id && ($product_info['quantity'] ?? 0) >= $qty_sold) {
            $new_qty = intval($product_info['quantity']) - $qty_sold;
            $new_sold = intval($product_info['items_sold'] ?? 0) + $qty_sold;
            
            firebase_request("products/$product_id", 'PATCH', [
                'quantity' => $new_qty,
                'items_sold' => $new_sold
            ]);

            $log_data = [
                'user_id' => $current_user_id,
                'product_name' => $product_info['name'],
                'category' => $product_info['category'],
                'price_sold' => floatval($product_info['price']),
                'quantity_sold' => $qty_sold,
                'sold_at' => date('Y-m-d H:i:s')
            ];
            firebase_request('sales_history', 'POST', $log_data);
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

    $new_product = [
        'user_id' => $current_user_id,
        'name' => $name,
        'category' => $category,
        'quantity' => $quantity,
        'price' => $price,
        'alert_limit' => $alert_limit,
        'image' => $image_name,
        'items_sold' => 0
    ];

    firebase_request('products', 'POST', $new_product);
    header("Location: dashboard.php");
    exit;
}

// 2. EDIT EXISTING PRODUCT
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = floatval($_POST['price']);
    $alert_limit = intval($_POST['alert_limit']);
    
    $update_data = [
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'alert_limit' => $alert_limit
    ];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);
        $update_data['image'] = $image_name;
    }

    firebase_request("products/$product_id", 'PATCH', $update_data);
    header("Location: dashboard.php");
    exit;
}

// 3. QUICK SINGLE SOLD BUTTON (-1)
if (isset($_GET['quick_sell'])) {
    $product_id = $_GET['quick_sell'];
    $product_info = firebase_request("products/$product_id");

    if ($product_info && $product_info['user_id'] === $current_user_id && ($product_info['quantity'] ?? 0) > 0) {
        $new_qty = intval($product_info['quantity']) - 1;
        $new_sold = intval($product_info['items_sold'] ?? 0) + 1;

        firebase_request("products/$product_id", 'PATCH', [
            'quantity' => $new_qty,
            'items_sold' => $new_sold
        ]);

        $log_data = [
            'user_id' => $current_user_id,
            'product_name' => $product_info['name'],
            'category' => $product_info['category'],
            'price_sold' => floatval($product_info['price']),
            'quantity_sold' => 1,
            'sold_at' => date('Y-m-d H:i:s')
        ];
        firebase_request('sales_history', 'POST', $log_data);
    }
    header("Location: dashboard.php");
    exit;
}

// 4. QUICK RESTOCK BUTTON (+1)
if (isset($_GET['quick_restock'])) {
    $product_id = $_GET['quick_restock'];
    $product_info = firebase_request("products/$product_id");

    if ($product_info && $product_info['user_id'] === $current_user_id) {
        $new_qty = intval($product_info['quantity'] ?? 0) + 1;
        firebase_request("products/$product_id", 'PATCH', ['quantity' => $new_qty]);
    }
    header("Location: dashboard.php");
    exit;
}

// 5. DELETE PRODUCT PERMANENTLY
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $product_info = firebase_request("products/$delete_id");

    if ($product_info && $product_info['user_id'] === $current_user_id) {
        firebase_request("products/$delete_id", 'DELETE');
    }
    header("Location: dashboard.php");
    exit;
}

// --- RENDERING READ-QUERIES CONVERTED FOR LOOPS IN DASHBOARD.PHP ---
$all_products = firebase_request('products') ?: [];
$total_products = 0;
$products_array = [];

foreach ($all_products as $id => $row) {
    if (isset($row['user_id']) && $row['user_id'] === $current_user_id) {
        $row['id'] = $id; // Inject the alphanumeric Firebase Key as ID field 
        $products_array[] = $row;
        $total_products += intval($row['quantity'] ?? 0);
    }
}

$all_history = firebase_request('sales_history') ?: [];
$total_revenue = 0.00;
$sales_history_array = [];

foreach ($all_history as $id => $h_row) {
    if (isset($h_row['user_id']) && $h_row['user_id'] === $current_user_id) {
        $h_row['id'] = $id;
        $sales_history_array[] = $h_row;
        $total_revenue += (floatval($h_row['price_sold'] ?? 0) * intval($h_row['quantity_sold'] ?? 0));
    }
}

// Sort sales timeline history by date string descending
usort($sales_history_array, function($a, $b) {
    return strcmp($b['sold_at'] ?? '', $a['sold_at'] ?? '');
});
?>