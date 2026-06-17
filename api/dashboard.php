<?php
// --- 1. DECLARE TIMELINE & MATH HELPERS FIRST SO THE CORE ENGINE AND HTML CAN CALL THEM SAFE ---
function number_style_render($val) {
    return number_format(floatval($val), 2, '.', ',');
}

function format_time_ago($timestamp_str) {
    if (empty($timestamp_str)) return 'Just now';
    $time = strtotime($timestamp_str);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    $mins = round($diff / 60);
    if ($mins < 60) return $mins . 'm ago';
    $hours = round($diff / 3600);
    if ($hours < 24) return $hours . 'h ago';
    $days = round($diff / 86400);
    if ($days < 7) return $days . 'd ago';
    
    return date('M d, g:i A', $time);
}

// --- 2. INCLUDE WORKING FIREBASE REWRITE ENGINE BACKEND ---
include 'dashboard_backend.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Inventory Deck</title>
    <link rel="manifest" href="manifest.json">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* --- GLOBAL GRAPHICS & BACKGROUND --- */
        body { 
            font-family: 'Comfortaa', sans-serif; 
            margin: 0; padding: 40px 20px 40px 90px;
            background: linear-gradient(135deg, #bae6fd 0%, #7dd3fc 100%); 
            min-height: 100vh; color: #3a4558;
            display: flex; flex-direction: column; align-items: center;
            transition: padding-left 0.3s ease;
        }

        .dashboard-wrapper { 
            width: 100%; 
            max-width: 1400px; 
        }

        /* --- SLIDING SIDE NAVIGATION PANEL --- */
        .sidebar-nav {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 70px;
            background: #ffffff;
            box-shadow: 4px 0 20px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 30px;
            z-index: 999;
            transition: width 0.3s ease;
            overflow: hidden;
        }

        body.sidebar-open {
            padding-left: 270px;
        }

        body.sidebar-open .sidebar-nav {
            width: 250px;
            align-items: flex-start;
            padding-left: 20px;
        }

        .nav-toggle-btn {
            background: #f0f9ff;
            border: none;
            cursor: pointer;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 40px;
            color: #0284c7;
            font-size: 18px;
            transition: background 0.2s;
            align-self: center;
        }
        body.sidebar-open .nav-toggle-btn {
            align-self: flex-end;
            margin-right: 20px;
        }

        .nav-toggle-btn:hover { background: #e0f2fe; }

        .sidebar-links {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 10px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #64748b;
            padding: 14px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
            width: calc(100% - 40px);
            white-space: nowrap;
        }

        .nav-item:hover, .nav-item.active {
            background: #f0f9ff;
            color: #0284c7;
        }

        .nav-item svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
            margin-right: 25px;
            flex-shrink: 0;
        }

        .nav-label {
            opacity: 0;
            transition: opacity 0.2s;
        }
        body.sidebar-open .nav-label {
            opacity: 1;
        }

        .logout-btn {
            margin-top: auto;
            margin-bottom: 40px;
            border: 2px solid #ef4444;
            color: #ef4444;
        }
        .logout-btn:hover {
            background: #fef2f2;
            color: #ef4444;
        }

        /* --- TOP PROFILE HEADER SECTION --- */
        .header-section {
            margin-bottom: 40px;
            animation: fadeIn 0.6s ease;
        }

        .header-section h1 {
            font-size: 32px;
            color: #1e293b;
            margin: 0 0 8px 0;
            font-weight: 700;
        }

        .header-section p {
            font-size: 16px;
            color: #64748b;
            margin: 0;
        }

        /* --- SCOREBOARD METRICS GRID --- */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
            width: 100%;
        }

        .metric-card {
            background: rgba(255, 255, 255, 0.85);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 25px rgba(2, 132, 199, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            gap: 20px;
            animation: slideUp 0.5s ease;
        }

        .metric-icon-box {
            padding: 15px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .blue-box { background: #e0f2fe; color: #0284c7; }
        .pink-box { background: #fce7f3; color: #db2777; }
        .green-box { background: #dcfce7; color: #16a34a; }

        .metric-icon-box svg {
            width: 28px;
            height: 28px;
            fill: currentColor;
        }

        .metric-info h3 {
            margin: 0 0 6px 0;
            font-size: 14px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-info p {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
        }

        /* --- CONTROL DISPATCH ACTION CORES --- */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            width: 100%;
        }

        .search-wrapper {
            position: relative;
            width: 350px;
        }

        .search-wrapper input {
            width: 100%;
            padding: 14px 20px 14px 45px;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .search-wrapper input:focus {
            outline: none;
            border-color: #0284c7;
            box-shadow: 0 0 0 4px #e0f2fe;
        }

        .search-wrapper svg {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            fill: #94a3b8;
        }

        .add-product-btn {
            background: #0284c7;
            color: #ffffff;
            border: none;
            padding: 14px 24px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
            transition: all 0.2s;
            font-family: inherit;
        }

        .add-product-btn:hover {
            background: #0369a1;
            transform: translateY(-2px);
        }

        /* --- CENTRAL WORKSPACE ARCHITECTURE --- */
        .workspace-layout {
            display: flex;
            gap: 30px;
            width: 100%;
            align-items: flex-start;
        }

        .cards-container {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .product-card {
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.08);
        }

        .card-img-frame {
            width: 100%;
            height: 180px;
            background: #f8fafc;
            position: relative;
            overflow: hidden;
        }

        .card-img-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.9);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            backdrop-filter: blur(4px);
        }

        .card-details {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .card-title {
            margin: 0 0 12px 0;
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            color: #64748b;
        }

        .price-val {
            color: #10b981;
            font-weight: 700;
            font-size: 16px;
        }

        .stock-status {
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 11px;
        }
        .status-ok { background: #dcfce7; color: #15803d; }
        .status-low { background: #fef2f2; color: #b91c1c; }

        .card-control-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
            gap: 8px;
        }

        .counter-pill {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .counter-pill-btn {
            background: transparent;
            border: none;
            padding: 8px 12px;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            transition: background 0.2s;
        }
        .counter-pill-btn:hover { background: #e2e8f0; color: #1e293b; }

        .counter-pill-val {
            padding: 0 4px;
            font-weight: 700;
            color: #1e293b;
            min-width: 24px;
            text-align: center;
            font-size: 14px;
        }

        /* --- LIVE TIMELINE SIDEBAR TRACKER --- */
        .timeline-sidebar {
            width: 380px;
            background: #ffffff;
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
            box-sizing: border-box;
        }

        .timeline-sidebar h2 {
            margin: 0 0 20px 0;
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }

        .timeline-track {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
            max-height: 520px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .node-content-box {
            position: relative;
            padding-left: 24px;
            border-left: 2px solid #e2e8f0;
        }

        .node-content-box::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #0284c7;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px #0284c7;
        }

        .node-content-box h5 {
            margin: 0 0 4px 0;
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        .node-content-box p {
            margin: 0 0 4px 0;
            font-size: 13px;
            color: #64748b;
        }

        .node-content-box span {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 700;
        }

        /* --- LIGHTBOX MODAL OVERLAYS --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.3);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-body-container {
            background: #ffffff;
            border-radius: 24px;
            width: 480px;
            padding: 35px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
            animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-body-container h3 {
            margin: 0 0 25px 0;
            font-size: 22px;
            color: #1e293b;
        }

        .form-input-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-input-group label {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
        }

        .form-input-group input, .form-input-group select {
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            font-family: inherit;
            transition: border 0.2s;
        }

        .form-input-group input:focus {
            outline: none;
            border-color: #0284c7;
        }

        .form-actions-row {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
        }

        .cancel-btn {
            background: #f1f5f9;
            color: #475569;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
        }

        /* --- BATCH RESTOCK CONTAINER WARNING PANEL --- */
        .batch-restock-panel {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 35px;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 15px;
            animation: slideUp 0.4s ease;
        }

        .batch-restock-header {
            color: #991b1b;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .batch-restock-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 12px;
        }

        .batch-restock-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 10px 15px;
            border-radius: 12px;
            border: 1px solid #fee2e2;
        }

        .batch-restock-row span {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .batch-restock-input {
            width: 70px;
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            text-align: center;
            font-family: inherit;
            font-size: 13px;
        }

        .batch-restock-submit-btn {
            background: #dc2626;
            color: #ffffff;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s;
            align-self: flex-end;
        }
        .batch-restock-submit-btn:hover { background: #b91c1c; }

        /* --- ANIMATION CORE INTERPOLATIONS --- */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes modalPop { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body>

    <div class="sidebar-nav">
        <button class="nav-toggle-btn" onclick="toggleSidebarLayout()">☰</button>
        <div class="sidebar-links">
            <a href="dashboard.php" class="nav-item active">
                <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                <span class="nav-label">Workspace</span>
            </a>
            <a href="logout.php" class="nav-item logout-btn">
                <svg viewBox="0 0 24 24"><path d="M13 3h-2v10h2V3zm4.41 2.59L16 7c1.86 1.86 3 4.43 3 7.25 0 5.52-4.48 10-10 10S2 19.77 2 14.25c0-2.82 1.14-5.39 3-7.25L3.59 5.59C1.39 7.79 0 10.86 0 14.25 0 21.01 5.52 26.5 12.25 26.5S24.5 21.01 24.5 14.25c0-3.39-1.39-6.46-3.59-8.66z"/></svg>
                <span class="nav-label">Log Out</span>
            </a>
        </div>
    </div>

    <div class="dashboard-wrapper">
        <div class="header-section">
            <h1>Workspace Terminal</h1>
            <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>!</p>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon-box blue-box">
                    <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5v-5l-10 5-10-5v5z"/></svg>
                </div>
                <div class="metric-info">
                    <h3>Unique SKUs</h3>
                    <p><?php echo count($products_array ?? []); ?></p>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon-box pink-box">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
                </div>
                <div class="metric-info">
                    <h3>In-Stock Items</h3>
                    <p><?php echo $total_products ?? 0; ?></p>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon-box green-box">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
                </div>
                <div class="metric-info">
                    <h3>Gross Revenue</h3>
                    <p>₱<?php echo number_style_render($total_revenue ?? 0); ?></p>
                </div>
            </div>
        </div>

        <?php
        $critical_shortage_items = [];
        if (isset($products_array) && is_array($products_array)) {
            foreach ($products_array as $p) {
                if (intval($p['quantity']) <= intval($p['alert_limit'])) {
                    $critical_shortage_items[] = $p;
                }
            }
        }
        if (!empty($critical_shortage_items)):
        ?>
        <form method="POST" action="dashboard_backend.php" class="batch-restock-panel">
            <div class="batch-restock-header">
                ⚠️ Stock Warning Alert: Critical Shortage Detected
            </div>
            <div class="batch-restock-list">
                <?php foreach ($critical_shortage_items as $warn_row): ?>
                <div class="batch-restock-row">
                    <span><?php echo htmlspecialchars($warn_row['name']); ?> (<?php echo $warn_row['quantity']; ?> left)</span>
                    <input type="number" name="restock_amounts[<?php echo $warn_row['id']; ?>]" class="batch-restock-input" min="0" placeholder="+ Qty">
                </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" name="batch_restock_submit" class="batch-restock-submit-btn">Process Bulk Restock</button>
        </form>
        <?php endif; ?>

        <div class="actions-bar">
            <div class="search-wrapper">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="catalog-search" placeholder="Filter item names..." onkeyup="filterCatalogSearch()">
            </div>
            <button class="add-product-btn" onclick="openAddProductModal()">+ Register New Stock</button>
        </div>

        <div class="workspace-layout">
            <div class="cards-container" id="catalog-grid">
                <?php if (empty($products_array)): ?>
                    <div style="background:white; padding:40px; text-align:center; grid-column:1/-1; border-radius:24px;">No items tracked yet.</div>
                <?php else: ?>
                    <?php foreach ($products_array as $row): 
                        $is_low = intval($row['quantity']) <= intval($row['alert_limit']);
                    ?>
                    <div class="product-card" data-title="<?php echo strtolower(htmlspecialchars($row['name'])); ?>">
                        <div class="card-img-frame">
                            <img src="<?php echo (!empty($row['image']) && file_exists('uploads/'.$row['image'])) ? 'uploads/'.htmlspecialchars($row['image']) : 'uploads/default.png'; ?>" alt="Product">
                            <span class="category-badge"><?php echo htmlspecialchars($row['category']); ?></span>
                        </div>
                        <div class="card-details">
                            <h4 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h4>
                            <div class="info-row">
                                <span>Unit Price</span>
                                <span class="price-val">₱<?php echo number_style_render($row['price']); ?></span>
                            </div>
                            <div class="info-row">
                                <span>Stock Balance</span>
                                <span class="stock-status <?php echo $is_low ? 'status-low' : 'status-ok'; ?>">
                                    <?php echo $row['quantity']; ?> units left
                                </span>
                            </div>
                            <div class="card-control-buttons">
                                <button onclick="confirmPurgeRecord('<?php echo $row['id']; ?>')" style="color:#ef4444; background:none; border:none; cursor:pointer; font-weight:700; font-family:inherit; font-size:13px;">Delete</button>
                                <div class="counter-pill">
                                    <button class="counter-pill-btn" onclick="triggerCustomPopInteraction('sell', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>')">-</button>
                                    <span class="counter-pill-val"><?php echo $row['quantity']; ?></span>
                                    <button class="counter-pill-btn" onclick="triggerCustomPopInteraction('restock', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>')">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="timeline-sidebar">
                <h2>Activity Log</h2>
                <div class="timeline-track">
                    <?php if (empty($sales_history_array)): ?>
                        <p style="font-size:12px; text-align:center; color:#94a3b8; margin-top:20px;">No activities captured yet.</p>
                    <?php else: ?>
                        <?php foreach ($sales_history_array as $h_row): ?>
                        <div class="node-content-box">
                            <h5><?php echo htmlspecialchars($h_row['product_name']); ?></h5>
                            <p>Dispatched: <?php echo $h_row['quantity_sold']; ?> units (@ ₱<?php echo number_style_render($h_row['price_sold'] ?? 0); ?>)</p>
                            <span><?php echo format_time_ago($h_row['sold_at'] ?? ''); ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="productModal">
        <div class="modal-body-container">
            <h3>Register Stock Profile</h3>
            <form id="modalForm" method="POST" action="dashboard_backend.php" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="form-product-id">
                
                <div class="form-input-group">
                    <label>Item Name</label>
                    <input type="text" name="name" id="form-name" required placeholder="e.g. Chocolate Croissant">
                </div>
                <div class="form-input-group">
                    <label>Category Group</label>
                    <select name="category" id="form-category">
                        <option value="Pastry / Pastries">Pastry / Baked Products</option>
                        <option value="Coffee Brews">Coffee / Beverage Corner</option>
                        <option value="Handmade Crafts / Arts">Handmade Crafts / Arts</option>
                        <option value="Boutique / Accessories">Boutique / Accessories</option>
                        <option value="Cosmetics / Beauty Shop">Cosmetics / Beauty Shop</option>
                    </select>
                </div>
                <div class="form-input-group">
                    <label>Price (PHP)</label>
                    <input type="number" step="0.01" name="price" id="form-price" required placeholder="0.00">
                </div>
                <div class="form-input-group" id="stock-input-wrapper">
                    <label>Starting Stock</label>
                    <input type="number" name="quantity" id="form-quantity" required placeholder="0">
                </div>
                <div class="form-input-group">
                    <label>Low Limit Alert Notification Threshold</label>
                    <input type="number" name="alert_limit" id="form-alert-limit" required placeholder="5">
                </div>
                <div class="form-input-group">
                    <label>Product Display Image File</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                
                <div class="form-actions-row">
                    <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_product" id="submit-action-btn" class="add-product-btn" style="box-shadow:none; padding:12px 24px; border-radius:12px;">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const modal = document.getElementById('productModal');

    function toggleSidebarLayout() {
        document.body.classList.toggle('sidebar-open');
    }

    function openAddProductModal() {
        document.getElementById('modalForm').action = 'dashboard_backend.php';
        document.getElementById('form-product-id').value = '';
        document.getElementById('form-name').value = '';
        document.getElementById('form-price').value = '';
        document.getElementById('form-quantity').value = '';
        document.getElementById('form-alert-limit').value = '';
        document.getElementById('stock-input-wrapper').style.display = 'flex';
        document.getElementById('submit-action-btn').name = 'add_product';
        document.getElementById('submit-action-btn').textContent = 'Save Profile';
        if(modal) modal.style.display = 'flex';
    }

    function closeModal() {
        if(modal) modal.style.display = 'none';
    }

    function filterCatalogSearch() {
        const query = document.getElementById('catalog-search').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            const title = card.getAttribute('data-title');
            if (title.includes(query)) {
                card.style.display = "flex";
            } else {
                card.style.display = "none";
            }
        });
    }

    function triggerCustomPopInteraction(type, id, name) {
        if (type === 'sell') {
            let qty = prompt(`How many units of "${name}" were just sold / checked out?`, "1");
            if (qty === null) return; 
            qty = parseInt(qty);
            if (!isNaN(qty) && qty > 0) {
                window.location.href = `dashboard_backend.php?custom_sell_id=${id}&quantity_sold=${qty}`;
            } else if (qty <= 0) {
                alert("Please insert a valid item quantity greater than 0.");
            }
        } else if (type === 'restock') {
            let qty = prompt(`How many incoming restock units of "${name}" are you receiving?`, "1");
            if (qty === null) return; 
            qty = parseInt(qty);
            if (!isNaN(qty) && qty > 0) {
                window.location.href = `dashboard_backend.php?custom_restock_id=${id}&quantity_restocked=${qty}`;
            } else if (qty <= 0) {
                alert("Restock requests require item entries greater than 0.");
            }
        }
    }

    function confirmPurgeRecord(id) {
        if (confirm("Are you entirely sure you want to permanently delete this catalog product file? This action is irreversible.")) {
            window.location.href = `dashboard_backend.php?delete_id=${id}`;
        }
    }
    </script>
</body>
</html>