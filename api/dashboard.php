<?php include 'dashboard_backend.php'; ?>
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
            box-shadow: 4px 0 25px rgba(14, 116, 144, 0.15);
            z-index: 9999999;
            overflow-x: hidden;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            padding-top: 20px;
            box-sizing: border-box;
        }

        .sidebar-nav:hover {
            width: 280px;
        }

        body:has(.sidebar-nav:hover) {
            padding-left: 300px;
        }

        .hamburger-indicator {
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: 24px;
            margin: 10px 0 25px 23px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .hamburger-indicator span {
            display: block;
            height: 3px;
            width: 100%;
            background-color: #0284c7;
            border-radius: 2px;
            transition: background-color 0.2s ease;
        }
        .sidebar-nav:hover .hamburger-indicator span {
            background-color: #0369a1;
        }

        .sidebar-menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 280px;
        }

        .sidebar-menu-item {
            display: none; 
            align-items: center;
            padding: 14px 24px;
            color: #475569;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            white-space: nowrap;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-nav:hover .sidebar-menu-item {
            display: flex;
        }

        .sidebar-menu-item.main-node {
            padding-left: 24px;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        .sidebar-menu-item.sub-node {
            padding-left: 44px;
            font-size: 14px;
            font-weight: normal;
        }

        .sidebar-menu-item:hover {
            background: #f0f9ff;
            color: #0284c7;
            border-left-color: #0284c7;
        }

        /* --- NAVIGATION BAR --- */
        .header { 
            display: flex; justify-content: space-between; align-items: center; 
            background: #fff; padding: 20px 35px; border-radius: 24px; 
            box-shadow: 0 8px 25px rgba(14, 116, 144, 0.12); margin-bottom: 30px;
        }
        .header-title-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .header h2 { margin: 0; font-size: 20px; color: #1e293b; font-weight: 700; }
        .header span { color: #64748b; font-size: 15px; font-weight: 700; }

        .header-actions-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .network-status-badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .network-status-badge::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-online { background-color: #e6f7ed; color: #166534; }
        .status-online::before { background-color: #22c55e; box-shadow: 0 0 8px #22c55e; }
        .status-offline { background-color: #fee2e2; color: #991b1b; }
        .status-offline::before { background-color: #ef4444; box-shadow: 0 0 8px #ef4444; }

        /* --- DYNAMIC AUDIO STATUS ACTION BUTTON --- */
        .audio-permission-btn {
            background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa;
            padding: 12px 20px; border-radius: 12px; font-weight: bold; font-size: 13px;
            cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;
        }
        .audio-permission-btn:hover { background: #ffedd5; }
        .audio-active { background: #f0fdf4; color: #166534; border-color: #bbf7d0; cursor: default; }

        .logout-btn {
            background: #f0f9ff; color: #0284c7; text-decoration: none; padding: 12px 20px; 
            border-radius: 12px; font-weight: bold; font-size: 13px; transition: background 0.2s;
        }
        .logout-btn:hover { background: #e0f2fe; }

        /* --- METRIC COUNTERS --- */
        .summary-container { display: flex; gap: 25px; margin-bottom: 35px; }
        .metric-card { 
            background: white; padding: 25px 35px; border-radius: 20px; flex: 1; 
            box-shadow: 0 6px 20px rgba(0,0,0,0.02); text-align: center;
            cursor: pointer; transition: all 0.2s ease; 
            border: 2px solid transparent;
        }
        .metric-card:hover { border: 2px solid #0284c7; }
        .metric-card h4 { margin: 0; color: #1e293b; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .metric-card p { margin: 8px 0 0 0; font-size: 30px; font-weight: bold; color: #0f172a; }

        .panel-title-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .panel-title-bar h3 { margin: 0; color: #0f172a; font-size: 24px; font-weight: 700; }

        .add-product-btn {
            background: #0284c7; color: white; border: none; padding: 14px 28px; 
            border-radius: 14px; font-weight: bold; cursor: pointer; font-size: 14px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2); transition: 0.2s; font-family: inherit;
        }
        .add-product-btn:hover { background: #0369a1; }

        /* --- PRODUCTS CARD GRID --- */
        .cards-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 30px; 
            width: 100%;
        }

        .product-card {
            background: white; border-radius: 24px; padding: 22px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05); display: flex; flex-direction: column;
            position: relative; overflow: hidden; transition: 0.25s ease;
            cursor: pointer; border: 2px solid transparent;
        }
        .product-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 12px 30px rgba(14, 116, 144, 0.15); 
            border-color: #0284c7;
        }

        .cat-baked { background: linear-gradient(to bottom, #fffdf9, #ffffff); }
        .cat-meals { background: linear-gradient(to bottom, #fffaf9, #ffffff); }
        .cat-crafts { background: linear-gradient(to bottom, #f9faff, #ffffff); }
        .cat-jewelry { background: linear-gradient(to bottom, #f7fdfa, #ffffff); }
        .cat-prints { background: linear-gradient(to bottom, #fdf9ff, #ffffff); }
        .cat-default { background: white; }

        .card-title-text { margin: 0 0 4px 0; font-size: 19px; color: #0f172a; font-weight: 700; text-align: center;}
        .card-tag { font-size: 13px; color: #475569; font-weight: 700; display: block; text-align: center; margin-bottom: 15px; }

        .card-img-holder {
            width: 100%; height: 180px; border-radius: 16px; overflow: hidden;
            margin-bottom: 15px; background: #fafafa; border: 1px solid rgba(0,0,0,0.03);
        }
        .card-img-holder img { width: 100%; height: 100%; object-fit: cover; }

        .stats-infobar { display: flex; background: rgba(255,255,255,0.9); padding: 12px 0; border-radius: 12px; margin-bottom: 15px; border: 1px solid rgba(0,0,0,0.05); }
        .stat-block { flex: 1; text-align: center; }
        .stat-block:not(:last-child) { border-right: 1px solid #e2e8f0; }
        .lbl { font-size: 11px; color: #64748b; text-transform: uppercase; display: block; font-weight: 700; }
        .val { font-size: 14px; font-weight: bold; color: #0f172a; }

        .button-drawer { display: flex; flex-direction: column; gap: 8px; }
        .btn-action {
            border: none; padding: 12px; border-radius: 12px; font-family: inherit;
            font-size: 13px; font-weight: bold; text-align: center; cursor: pointer;
            text-decoration: none; transition: transform 0.1s, opacity 0.2s; display: block;
        }
        .btn-action:active { transform: scale(0.98); }

        .action-row-pair { display: flex; gap: 10px; width: 100%; }
        .action-row-pair .btn-action { flex: 1; }

        .btn-sell { background: #ffd1d1 !important; color: #c62828 !important; }
        .btn-sell:hover { background: #ffbaba !important; }
        .btn-restock { background: #bfeff5 !important; color: #006064 !important; }
        .btn-restock:hover { background: #a6e7f0 !important; }

        .utility-row { display: flex; gap: 8px; margin-top: 4px; padding-top: 12px; border-top: 1px dashed #cbd5e1; }
        .btn-edit { background: #f3e5f5; color: #6a1b9a; flex: 1; }
        .btn-edit:hover { background: #ebd3f8; }
        .btn-delete { background: #ffebee; color: #c62828; flex: 1; }
        .btn-delete:hover { background: #ffdcd0; }

        .status-dot { position: absolute; top: 18px; right: 18px; width: 10px; height: 10px; border-radius: 50%; }
        .dot-low { background: #ef5350; box-shadow: 0 0 8px #ef5350; }
        .dot-ok { background: #66bb6a; }

        .empty-view { grid-column: 1 / -1; background: white; text-align: center; padding: 60px; border-radius: 20px; color: #64748b; border: 1px dashed #cbd5e1;}

        /* --- POPUP WINDOW MODALS --- */
        .modal-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); 
            display: none; justify-content: center; align-items: center; backdrop-filter: blur(8px); z-index: 99999;
        }
        .modal-box { background: white; padding: 35px; border-radius: 24px; width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
        .wide-modal-box { width: 90%; max-width: 900px; max-height: 80vh; overflow-y: auto; }

        /* --- LOG BOOK BADGES FOR RECENT ACTIVITY PANEL --- */
        .log-action-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-sale-action { background: #fee2e2; color: #b91c1c; }
        .badge-restock-action { background: #e0f2fe; color: #0369a1; }

        /* --- CATEGORY OVERVIEW STRUCTURES --- */
        .category-group-section {
            margin-bottom: 25px;
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
        }
        .category-group-header {
            font-size: 16px;
            font-weight: bold;
            color: #0284c7;
            border-bottom: 2px dashed #cbd5e1;
            padding-bottom: 8px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- MULTI-ITEM CRITICAL STOCK WARNING PANEL --- */
        .giant-warning-box {
            width: 650px !important;
            padding: 40px !important;
            background: #fff5f5 !important;
            border: 4px solid #dc2626 !important;
            text-align: center;
            border-radius: 24px;
            animation: popPulse 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .giant-warning-title {
            font-size: 28px !important;
            color: #b91c1c !important;
            margin: 0 0 10px 0;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .giant-warning-desc {
            font-size: 16px !important;
            color: #7f1d1d !important;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .low-stock-list-container {
            max-height: 280px;
            overflow-y: auto;
            background: #ffffff;
            border: 2px solid #fca5a5;
            border-radius: 16px;
            margin-bottom: 25px;
            text-align: left;
            padding: 10px;
        }
        .low-stock-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 18px;
            border-bottom: 1px solid #fee2e2;
            background: #ffffff;
            border-radius: 14px;
            margin-bottom: 6px;
            border: 1px solid rgba(220, 38, 38, 0.15);
        }
        .low-stock-item-row:last-child { margin-bottom: 0; }
        .low-stock-item-info { display: flex; flex-direction: column; gap: 2px; }
        .low-stock-item-name { font-weight: bold; color: #1e293b; font-size: 16px; }
        .low-stock-item-meta { font-size: 13px; color: #dc2626; font-weight: 700; }
        .btn-warning-row-fix {
            background: #0284c7; color: white; padding: 10px 20px; border-radius: 12px;
            font-size: 14px; font-weight: bold; border: none; cursor: pointer; transition: 0.2s;
            font-family: inherit;
        }
        .btn-warning-row-fix:hover { background: #0369a1; }

        .giant-warning-actions-row {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 15px;
        }
        .btn-warning-ignore {
            background: #e2e8f0; color: #475569; padding: 14px 28px; border-radius: 14px;
            font-size: 15px; font-weight: bold; border: none; cursor: pointer; font-family: inherit;
        }
        .btn-warning-ignore:hover { background: #cbd5e1; }

        @keyframes popPulse {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* --- CONSOLIDATED FLOATING DRAWER OVERLAY --- */
        .stock-alerts-drawer {
            position: fixed;
            bottom: 20px;
            left: 90px; 
            z-index: 10000000; 
            display: flex;
            flex-direction: column-reverse;
            gap: 12px;
            width: 380px;
            background: transparent;
            transition: left 0.3s ease;
        }

        body:has(.sidebar-nav:hover) .stock-alerts-drawer {
            left: 300px;
        }

        .floating-danger-card {
            background: #7f1d1d;
            color: #fef2f2;
            padding: 18px 24px;
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(127, 29, 29, 0.35);
            border-left: 6px solid #f87171;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            border-right: 1px solid rgba(255,255,255,0.1);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .floating-danger-card:hover {
            transform: translateY(-3px);
            background: #991b1b;
            box-shadow: 0 16px 36px rgba(127, 29, 29, 0.45);
        }

        /* --- BIG REGISTER FOCUS SCREEN --- */
        .focus-modal-box { 
            width: 860px !important; 
            padding: 40px; 
            text-align: left;
        }
        .focus-split-container {
            display: flex; gap: 35px; align-items: stretch; margin-top: 20px;
        }
        .focus-left-side {
            flex: 1; display: flex; flex-direction: column; justify-content: center;
        }
        .focus-right-side {
            flex: 1.3; background: #f8fafc; padding: 25px; border-radius: 22px; border: 1px solid #e2e8f0;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .focus-modal-box .card-img-holder { height: 200px; margin-bottom: 15px; border-radius: 18px; }

        .modal-box h3 { margin-top: 0; color: #0f172a; margin-bottom: 20px; font-size: 24px; font-weight: 700; }
        
        input, select { 
            width: 100%; padding: 14px; margin: 8px 0; border: 2px solid #bae6fd; 
            border-radius: 14px; box-sizing: border-box; background: #f0f9ff; color: #0f172a; font-size: 16px; font-family: inherit; font-weight: bold;
        }
        input:focus, select:focus { outline: none; background: #fff; border-color: #0284c7; }
        
        .big-quantity-input {
            font-size: 28px !important; text-align: center; padding: 10px !important; color: #0284c7 !important; border-color: #7dd3fc !important;
        }

        .toggle-tab-row { display: flex; border-radius: 14px; background: #e2e8f0; padding: 4px; margin-bottom: 15px; }
        .tab-btn { 
            flex: 1; border: none; background: transparent; padding: 12px; font-family: inherit;
            font-size: 14px; font-weight: bold; border-radius: 10px; cursor: pointer; color: #475569; text-align: center;
        }
        .tab-btn.active-tab { background: white; color: #0f172a; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }

        .modal-close-btn { background: #f1f5f9; color: #475569; margin-top: 25px; font-weight: 700; width: 100%; border: none; padding: 14px; border-radius: 12px; cursor: pointer;}
        .modal-close-btn:hover { background: #e2e8f0; }

        .popup-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; text-align: left; }
        .popup-table th { background: #f0f9ff; color: #0284c7; padding: 14px; font-weight: bold; border-bottom: 2px solid #e0f2fe; }
        .popup-table td { padding: 14px; border-bottom: 1px solid #e0f2fe; color: #1e293b; font-weight: 700; }
        .popup-table tr:hover td { background: #f8fafc; }
    </style>
</head>
<body>

<audio id="lowStockAlertChime" src="https://actions.google.com/sounds/v1/alarms/digital_watch_alarm_long.ogg" preload="auto"></audio>

<div class="sidebar-nav">
    <div class="hamburger-indicator">
        <span></span>
        <span></span>
        <span></span>
    </div>
    
    <nav class="sidebar-menu-list">
        <a href="#dashboard" class="sidebar-menu-item main-node">Dashboard</a>
        <a href="#summary" class="sidebar-menu-item sub-node" onclick="openPopupModal('productsPopup')">Inventory summary</a>
        <a href="#alerts" class="sidebar-menu-item sub-node" onclick="executeImmediateStockScan(false)">Low-stock alerts</a>
        <a href="#activity" class="sidebar-menu-item sub-node" onclick="openPopupModal('recentActivityLogBookPopup')">Recent activity</a>
        
        <a href="#products" class="sidebar-menu-item main-node">Products / Items</a>
        <a href="#list" class="sidebar-menu-item sub-node" onclick="openPopupModal('productsPopup')">Product list</a>
        <a href="#add" class="sidebar-menu-item sub-node" onclick="openAddModal()">Add product</a>
        <a href="#categories" class="sidebar-menu-item sub-node" onclick="openCategoryModeModal()">Categories</a>
    </nav>
</div>

<div class="dashboard-wrapper">

    <div class="header">
        <div class="header-title-area">
            <h2>Stock Space <span>(<?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>)</span></h2>
            <div id="networkStatusBadge" class="network-status-badge status-online">Online</div>
        </div>
        
        <div class="header-actions-area">
            <button id="audioPermissionBtn" class="audio-permission-btn" onclick="manuallyUnlockAudioPermissions()">Targeting Alerts...</button>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="summary-container">
        <div class="metric-card clickable-card" onclick="openPopupModal('productsPopup')">
            <h4>Total Products</h4>
            <p id="totalProductsMetricCard"><?php echo is_array($products_array) ? count($products_array) : 0; ?></p>
        </div>
        <div class="metric-card clickable-card" onclick="openPopupModal('revenuePopup')">
            <h4>Total Revenue</h4>
            <p id="totalRevenueDisplayNode" data-raw-revenue="<?php echo $total_revenue ?? 0; ?>">₱<?php echo number_format($total_revenue ?? 0, 2); ?></p>
        </div>
    </div>

    <div class="panel-title-bar">
        <h3>Inventory Deck</h3>
        <button class="add-product-btn" onclick="openAddModal()">Add Product</button>
    </div>

    <div class="cards-grid" id="mainProductsCardsGridContainer">
        <?php if (empty($products_array)): ?>
            <div class="empty-view">Your inventory deck is empty. Tap add to start!</div>
        <?php else: ?>
            <?php foreach ($products_array as $row): ?>
                <?php 
                    $is_low = $row['quantity'] <= $row['alert_limit']; 
                    $cat_class = 'cat-default';
                    if($row['category'] == 'Baked Goods') $cat_class = 'cat-baked';
                    if($row['category'] == 'Meals / Appetizers') $cat_class = 'cat-meals';
                    if($row['category'] == 'Artisan Crafts') $cat_class = 'cat-crafts';
                    if($row['category'] == 'Handmade Jewelry') $cat_class = 'cat-jewelry';
                    if($row['category'] == 'Custom Prints') $cat_class = 'cat-prints';
                    
                    $clean_row = [
                        'id' => intval($row['id']),
                        'name' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
                        'category' => htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'),
                        'price' => floatval($row['price']),
                        'quantity' => intval($row['quantity']),
                        'alert_limit' => intval($row['alert_limit']),
                        'items_sold' => intval($row['items_sold']),
                        'image' => htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8')
                    ];
                ?>
                
                <div class="product-card <?php echo $cat_class; ?>" id="card-product-<?php echo $row['id']; ?>" onclick='handleCardSelection(<?php echo json_encode($clean_row); ?>)'>
                    <div class="status-dot <?php echo $is_low ? 'dot-low' : 'dot-ok'; ?>"></div>

                    <h4 class="card-title-text"><?php echo htmlspecialchars($row['name']); ?></h4>
                    <span class="card-tag"><?php echo htmlspecialchars($row['category']); ?></span>

                    <div class="card-img-holder">
                        <img src="uploads/<?php echo $row['image']; ?>" alt="Item Image">
                    </div>

                    <div class="stats-infobar">
                        <div class="stat-block"><span class="lbl">Price</span><span class="val">₱<?php echo number_format($row['price'], 2); ?></span></div>
                        <div class="stat-block"><span class="lbl">Stock</span><span class="val stock-display-value"><?php echo $row['quantity']; ?></span></div>
                        <div class="stat-block"><span class="lbl">Sold</span><span class="val sold-display-value"><?php echo $row['items_sold']; ?></span></div>
                    </div>

                    <div class="button-drawer">
                        <div class="action-row-pair">
                            <a href="#" class="btn-action btn-sell" onclick="event.stopPropagation(); event.preventDefault(); processSaleReduction(<?php echo $row['id']; ?>, 1);">Sold (-1)</a>
                            <a href="#" class="btn-action btn-restock" onclick="event.stopPropagation(); event.preventDefault(); processRestockIncrease(<?php echo $row['id']; ?>, 1);">Restock (+1)</a>
                        </div>

                        <div class="utility-row">
                            <button type="button" class="btn-action btn-edit" onclick='event.stopPropagation(); openEditModal(<?php echo json_encode($clean_row); ?>)'>Edit</button>
                            <a href="dashboard_backend.php?delete_id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="event.stopPropagation(); return confirm('Remove item permanently?');">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="stock-alerts-drawer" id="ignoredAlertsContainer"></div>

<div class="modal-overlay" id="warningModal">
    <div class="giant-warning-box modal-box">
        <div class="giant-warning-title">Stock Level Alerts</div>
        <div class="giant-warning-desc">The following products have hit or dropped below their critical alert threshold limits:</div>
        
        <div style="margin: 0; width: 100%;">
            <div class="low-stock-list-container" id="lowStockItemsListContainer"></div>
            <div class="giant-warning-actions-row">
                <button type="button" class="btn-warning-ignore" id="warningIgnoreBtn">Dismiss & View Dashboard</button>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="focusModal" onclick="closePopupModalOnBackground(event, 'focusModal')">
    <div class="focus-modal-box modal-box" id="focusCardWrapper">
        <div style="text-align: center; border-bottom: 2px dashed #cbd5e1; padding-bottom: 12px; margin-bottom: 5px;">
            <h3 id="focusTitle" style="font-size: 30px; margin: 0; color: #0f172a; font-weight: 700;">Product Name</h3>
            <span id="focusCategory" class="card-tag" style="font-size: 15px; margin: 6px 0 0 0;">Category</span>
        </div>
        
        <div class="focus-split-container">
            <div class="focus-left-side">
                <div class="card-img-holder">
                    <img id="focusImage" src="" alt="Zoom Preview">
                </div>
                <div class="stats-infobar" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 16px 0; margin: 0 0 15px 0;">
                    <div class="stat-block"><span class="lbl" style="font-size: 12px;">Unit Price</span><span class="val" id="focusPrice" style="color: #0284c7; font-size: 18px;">₱0.00</span></div>
                    <div class="stat-block"><span class="lbl" style="font-size: 12px;">In Stock</span><span class="val" id="focusStock" style="font-size: 18px;">0</span></div>
                </div>

                <div class="action-row-pair">
                    <a href="#" id="focus_modal_sell_link" class="btn-action btn-sell" onclick="handleFocusQuickSell(event)">Sold (-1)</a>
                    <a href="#" id="focus_modal_restock_link" class="btn-action btn-restock" onclick="handleFocusQuickRestock(event)">Restock (+1)</a>
                </div>
            </div>

            <div class="focus-right-side">
                <form id="bundleActionForm" action="dashboard_backend.php" method="GET" style="margin: 0; display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                    <input type="hidden" name="custom_sell_id" id="focus_product_id_sell">
                    <input type="hidden" name="custom_restock_id" id="focus_product_id_restock" disabled>
                    
                    <div>
                        <div class="toggle-tab-row">
                            <button type="button" class="tab-btn active-tab" id="tabSellModeBtn" onclick="switchFocusFormConsoleMode('SELL')">Log Sale</button>
                            <button type="button" class="tab-btn" id="tabRestockModeBtn" onclick="switchFocusFormConsoleMode('RESTOCK')">Log Restock</button>
                        </div>

                        <label id="inputFormBoxDynamicLabel" style="font-size: 14px; font-weight: bold; color: #334155; display: block; margin-bottom: 6px; text-align: center;">How many items are you distributing?</label>
                        <input type="number" name="quantity_sold" id="focus_quantity_input" value="1" min="1" class="big-quantity-input" oninput="updateLivePrice()" required>
                    </div>

                    <div id="valuationContainerCard" style="background: #ffffff; padding: 18px; border-radius: 16px; border: 2px solid #bae6fd; text-align: center; margin: 15px 0;">
                        <span id="valuationDynamicTitle" style="font-size: 12px; font-weight: bold; color: #64748b; text-transform: uppercase; display: block; letter-spacing: 0.5px;">Gross Projected Income</span>
                        <span id="bulkTotalDisplay" style="font-size: 32px; font-weight: 900; color: #22c55e; display: block; margin-top: 6px;">₱0.00</span>
                    </div>

                    <button type="submit" id="mainConsoleActionButton" class="add-product-btn" style="background: #ef4444; width: 100%; padding: 16px; font-size: 16px; box-shadow: 0 6px 16px rgba(239, 68, 68, 0.25); border:none; border-radius:14px; color:white; font-weight:bold; cursor:pointer;">Confirm Sale Bundle</button>
                </form>
            </div>
        </div>

        <button type="button" class="modal-close-btn" onclick="closePopupModal('focusModal')">Close View</button>
    </div>
</div>

<div class="modal-overlay" id="productsPopup" onclick="closePopupModalOnBackground(event, 'productsPopup')">
    <div class="modal-box wide-modal-box">
        <h3>Current Stock List Overview</h3>
        <table class="popup-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Unit Price</th>
                    <th>Available Stock</th>
                </tr>
            </thead>
            <tbody id="summaryModalTableBodyContainer">
                <?php if(empty($products_array)): ?>
                    <tr id="productsSummaryModalEmptyPlaceholder"><td colspan="4" style="text-align:center; color:#64748b;">No items registered.</td></tr>
                <?php else: ?>
                    <?php foreach ($products_array as $p_row): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p_row['name']); ?></strong></td>
                            <td><span style="color:#475569; font-size:13px;"><?php echo htmlspecialchars($p_row['category']); ?></span></td>
                            <td>₱<?php echo number_format($p_row['price'], 2); ?></td>
                            <td>
                                <span class="stock-badge <?php echo ($p_row['quantity'] <= $p_row['alert_limit']) ? 'badge-low' : 'badge-ok'; ?>">
                                    <?php echo $p_row['quantity']; ?> left
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="button" class="modal-close-btn" onclick="closePopupModal('productsPopup')">Close Screen</button>
    </div>
</div>

<div class="modal-overlay" id="revenuePopup" onclick="closePopupModalOnBackground(event, 'revenuePopup')">
    <div class="modal-box wide-modal-box">
        <h3>Sales History Log Ledger</h3>
        <table class="popup-table">
            <thead>
                <tr>
                    <th>Item Traded</th>
                    <th>Category Type</th>
                    <th>Volume</th>
                    <th>Revenue Capitalized</th>
                    <th>Timestamp Record</th>
                </tr>
            </thead>
            <tbody id="revenueModalTableBody">
                <?php if (empty($sales_history_array)): ?>
                    <tr id="revenueEmptyStatePlaceholderRow"><td colspan="5" style="text-align:center; color:#64748b;">No items sold on this profile yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($sales_history_array as $sale): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($sale['product_name']); ?></strong></td>
                            <td><span style="color:#475569; font-size:13px;"><?php echo htmlspecialchars($sale['category']); ?></span></td>
                            <td><?php echo $sale['quantity_sold']; ?> pcs</td>
                            <td><span class="revenue-tag">+₱<?php echo number_format($sale['price_sold'] * $sale['quantity_sold'], 2); ?></span></td>
                            <td><span style="color:#0f172a; font-size:13px;"><?php echo date("M d, Y - g:i A", strtotime($sale['sold_at'])); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="button" class="modal-close-btn" onclick="closePopupModal('revenuePopup')">Close Screen</button>
    </div>
</div>

<div class="modal-overlay" id="recentActivityLogBookPopup" onclick="closePopupModalOnBackground(event, 'recentActivityLogBookPopup')">
    <div class="modal-box wide-modal-box">
        <h3>Database Audit Log Book (Recent Activity)</h3>
        <table class="popup-table">
            <thead>
                <tr>
                    <th>Timestamp Record</th>
                    <th>Product Item</th>
                    <th>Action Executed</th>
                    <th>Quantity Affected</th>
                </tr>
            </thead>
            <tbody id="auditLogBookTableBody"></tbody>
        </table>
        <button type="button" class="modal-close-btn" onclick="closePopupModal('recentActivityLogBookPopup')">Close Log Book</button>
    </div>
</div>

<div class="modal-overlay" id="categoryModePopup" onclick="closePopupModalOnBackground(event, 'categoryModePopup')">
    <div class="modal-box wide-modal-box">
        <h3>Inventory Sorted by Categories</h3>
        <div id="categoryModeContainer" style="margin-top: 15px;"></div>
        <button type="button" class="modal-close-btn" onclick="closePopupModal('categoryModePopup')">Close Screen</button>
    </div>
</div>

<div class="modal-overlay" id="productModal">
    <div class="modal-box">
        <h3 id="modalTitle">Add Product</h3>
        <form id="modalForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="form_product_id">
            <input type="hidden" name="add_product" id="action_add_trigger" value="1">
            <input type="hidden" name="edit_product" id="action_edit_trigger" value="1" disabled>
            
            <input type="text" name="name" id="form_name" placeholder="Item Name" required>
            
            <input type="text" name="category" id="form_category" list="category_suggestions" placeholder="Choose or Type Category" required>
            <datalist id="category_suggestions">
                <option value="Baked Goods">Baked Goods / Pastries</option>
                <option value="Meals / Appetizers">Meals and Food Bowls</option>
                <option value="Artisan Crafts">Artisan Crafts / Pottery</option>
                <option value="Handmade Jewelry">Handmade Jewelry</option>
                <option value="Custom Prints">Custom Prints / Stickers</option>
            </datalist>
            
            <input type="number" name="price" id="form_price" placeholder="Price (₱)" step="0.01" min="0" required>
            <div id="qty_input_wrapper"><input type="number" name="quantity" id="form_quantity" placeholder="Starting Quantity" min="0"></div>
            <input type="number" name="alert_limit" id="form_alert_limit" placeholder="Alert Limit" min="0" required>
            
            <div style="margin-top:8px;"><input type="file" name="image" accept="image/*" style="border:none; background:none;"></div>
            
            <button type="submit" class="add-product-btn" style="width:100%; margin-top:15px; padding:12px; color:white; border:none; font-weight:bold; cursor:pointer; border-radius:12px;">Save Details</button>
            <button type="button" class="modal-close-btn" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
    let currentItemUnitPrice = 0;
    let focusFormConsoleMode = "SELL"; 
    let titleFlasherInterval = null;
    let chimeTimeoutId = null; 
    
    let absoluteInventoryDataset = <?php echo json_encode($products_array ?? []); ?> || [];
    let coreSalesHistoryDataset = <?php echo json_encode($sales_history_array ?? []); ?> || [];
    let selectedProductRef = null;

    let tracksPreviouslyAlertedIDs = [];
    let runtimeDatabaseAuditLogs = [];

    // Safety switch: blocks data refreshes while you're filling out a form
    let isEditingOrInteractingWithForm = false; 

    coreSalesHistoryDataset.forEach(sale => {
        runtimeDatabaseAuditLogs.push({
            timestamp: sale.sold_at,
            name: sale.product_name,
            action: 'SELL',
            volume: sale.quantity_sold
        });
    });

    function updateNetworkStatusIndicator() {
        const badge = document.getElementById('networkStatusBadge');
        if (navigator.onLine) {
            badge.textContent = "Online";
            badge.className = "network-status-badge status-online";
        } else {
            badge.textContent = "Offline Mode";
            badge.className = "network-status-badge status-offline";
        }
    }
    
    window.addEventListener('online', () => { updateNetworkStatusIndicator(); triggerSyncCycle(); });
    window.addEventListener('offline', updateNetworkStatusIndicator);
    window.addEventListener('DOMContentLoaded', updateNetworkStatusIndicator);

    let db;
    const dbRequest = indexedDB.open("OfflineSalesDB", 1);
    dbRequest.onupgradeneeded = function(e) {
        db = e.target.result;
        if (!db.objectStoreNames.contains("pending_sales")) {
            db.createObjectStore("pending_sales", { autoIncrement: true });
        }
    };
    dbRequest.onsuccess = function(e) { db = e.target.result; };

    window.addEventListener('DOMContentLoaded', function() {
        executeImmediateStockScan(true); 
        checkAndSyncAudioPermissionUI();

        // ⏱️ SET TO AUTO-REFRESH COLD DATA REFRESH EVERY 30 SECONDS (30000ms)
        setInterval(silentlyFetchLatestDataUpdates, 30000);

        const bundleActionForm = document.getElementById('bundleActionForm');
        if(bundleActionForm) {
            bundleActionForm.addEventListener('submit', function(event) {
                event.preventDefault();
                if(!selectedProductRef) return;

                const qtyInput = document.getElementById('focus_quantity_input').value;
                const volumeAmount = parseInt(qtyInput) || 1;

                closePopupModal('focusModal');

                if (focusFormConsoleMode === "SELL") {
                    processSaleReduction(selectedProductRef.id, volumeAmount);
                } else {
                    processRestockIncrease(selectedProductRef.id, volumeAmount);
                }
            });
        }

        const mainProductForm = document.getElementById('modalForm');
        if (mainProductForm) {
            mainProductForm.addEventListener('submit', function() {
                const isEdit = document.getElementById('action_edit_trigger').disabled === false;
                const itemName = document.getElementById('form_name').value;
                const itemQty = document.getElementById('form_quantity').value || 0;

                runtimeDatabaseAuditLogs.unshift({
                    timestamp: new Date().toISOString(),
                    name: itemName,
                    action: isEdit ? 'EDIT' : 'ADD',
                    volume: isEdit ? 'N/A' : itemQty
                });
                populateAuditLogBookUI();
            });
        }
    });

    /* --- BACKGROUND DATA SYNC ENGINE --- */
    function silentlyFetchLatestDataUpdates() {
        // Halt if user is typing or connection went dark
        if (isEditingOrInteractingWithForm || !navigator.onLine) return;

        fetch('dashboard_backend.php?get_latest_json_state=1')
            .then(response => response.json())
            .then(payload => {
                if (payload && payload.products) {
                    absoluteInventoryDataset = payload.products;
                    
                    // Update main cards count metrics safely
                    const totalProductsCounter = document.getElementById('totalProductsMetricCard');
                    if (totalProductsCounter) totalProductsCounter.textContent = absoluteInventoryDataset.length;

                    const revenueDisplayBox = document.getElementById('totalRevenueDisplayNode');
                    if (revenueDisplayBox && payload.total_revenue !== undefined) {
                        let totalRevNum = parseFloat(payload.total_revenue) || 0;
                        revenueDisplayBox.setAttribute('data-raw-revenue', totalRevNum);
                        revenueDisplayBox.textContent = "₱" + totalRevNum.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }

                    // Render grid loop content updates smoothly
                    const gridContainer = document.getElementById('mainProductsCardsGridContainer');
                    if (gridContainer && absoluteInventoryDataset.length === 0) {
                        gridContainer.innerHTML = '<div class="empty-view">Your inventory deck is empty. Tap add to start!</div>';
                    } else if (gridContainer) {
                        const existingCardCount = gridContainer.querySelectorAll('.product-card').length;
                        
                        if (existingCardCount !== absoluteInventoryDataset.length) {
                            let structuralHTMLBuffer = "";
                            absoluteInventoryDataset.forEach(row => {
                                let isLow = row.quantity <= row.alert_limit;
                                let catClass = 'cat-default';
                                if(row.category === 'Baked Goods') catClass = 'cat-baked';
                                if(row.category === 'Meals / Appetizers') catClass = 'cat-meals';
                                if(row.category === 'Artisan Crafts') catClass = 'cat-crafts';
                                if(row.category === 'Handmade Jewelry') catClass = 'cat-jewelry';
                                if(row.category === 'Custom Prints') catClass = 'cat-prints';

                                let cleanJSONRow = JSON.stringify({
                                    id: parseInt(row.id), name: row.name, category: row.category,
                                    price: parseFloat(row.price), quantity: parseInt(row.quantity),
                                    alert_limit: parseInt(row.alert_limit), items_sold: parseInt(row.items_sold), image: row.image
                                }).replace(/'/g, "&apos;");

                                structuralHTMLBuffer += `
                                <div class="product-card ${catClass}" id="card-product-${row.id}" onclick='handleCardSelection(${cleanJSONRow})'>
                                    <div class="status-dot ${isLow ? 'dot-low' : 'dot-ok'}"></div>
                                    <h4 class="card-title-text">${row.name}</h4>
                                    <span class="card-tag">${row.category}</span>
                                    <div class="card-img-holder"><img src="uploads/${row.image}" alt="Item Image"></div>
                                    <div class="stats-infobar">
                                        <div class="stat-block"><span class="lbl">Price</span><span class="val">₱${parseFloat(row.price).toFixed(2)}</span></div>
                                        <div class="stat-block"><span class="lbl">Stock</span><span class="val stock-display-value">${row.quantity}</span></div>
                                        <div class="stat-block"><span class="lbl">Sold</span><span class="val sold-display-value">${row.items_sold}</span></div>
                                    </div>
                                    <div class="button-drawer">
                                        <div class="action-row-pair">
                                            <a href="#" class="btn-action btn-sell" onclick="event.stopPropagation(); event.preventDefault(); processSaleReduction(${row.id}, 1);">Sold (-1)</a>
                                            <a href="#" class="btn-action btn-restock" onclick="event.stopPropagation(); event.preventDefault(); processRestockIncrease(${row.id}, 1);">Restock (+1)</a>
                                        </div>
                                        <div class="utility-row">
                                            <button type="button" class="btn-action btn-edit" onclick='event.stopPropagation(); openEditModal(${cleanJSONRow})'>Edit</button>
                                            <a href="dashboard_backend.php?delete_id=${row.id}" class="btn-action btn-delete" onclick="event.stopPropagation(); return confirm(\'Remove item permanently?\');">Delete</a>
                                        </div>
                                    </div>
                                </div>`;
                            });
                            gridContainer.innerHTML = structuralHTMLBuffer;
                        } else {
                            absoluteInventoryDataset.forEach(item => {
                                const card = document.getElementById("card-product-" + item.id);
                                if (card) {
                                    card.querySelector('.stock-display-value').textContent = item.quantity;
                                    card.querySelector('.sold-display-value').textContent = item.items_sold;
                                    const dot = card.querySelector('.status-dot');
                                    if (dot) {
                                        dot.className = parseInt(item.quantity) <= parseInt(item.alert_limit) ? "status-dot dot-low" : "status-dot dot-ok";
                                    }
                                }
                            });
                        }
                    }

                    updateFocusModalFields();
                    syncSummaryModalTableRows();
                    executeImmediateStockScan(false);
                }
            }).catch(err => console.log("Refresh skipped: " + err));
    }

    function syncSummaryModalTableRows() {
        const tableBody = document.getElementById('summaryModalTableBodyContainer');
        if (!tableBody) return;

        if (absoluteInventoryDataset.length === 0) {
            tableBody.innerHTML = '<tr id="productsSummaryModalEmptyPlaceholder"><td colspan="4" style="text-align:center; color:#64748b;">No items registered.</td></tr>';
            return;
        }

        let newRowsHTML = "";
        absoluteInventoryDataset.forEach(p_row => {
            let isLow = parseInt(p_row.quantity) <= parseInt(p_row.alert_limit);
            newRowsHTML += `
                <tr>
                    <td><strong>${p_row.name}</strong></td>
                    <td><span style="color:#475569; font-size:13px;">${p_row.category}</span></td>
                    <td>₱${parseFloat(p_row.price).toFixed(2)}</td>
                    <td>
                        <span class="stock-badge ${isLow ? 'badge-low' : 'badge-ok'}">
                            ${p_row.quantity} left
                        </span>
                    </td>
                </tr>`;
        });
        tableBody.innerHTML = newRowsHTML;
    }

    function checkAndSyncAudioPermissionUI() {
        const btn = document.getElementById('audioPermissionBtn');
        if(!btn) return;

        const chime = document.getElementById('lowStockAlertChime');
        if (chime) {
            chime.volume = 1.0; 
            let playPromise = chime.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    chime.pause(); chime.currentTime = 0;
                    btn.textContent = "🔊 Alerts Enabled";
                    btn.classList.add('audio-active');
                    btn.onclick = null;
                }).catch(() => {
                    btn.textContent = "🔈 Click to Unmute Alerts";
                    btn.classList.remove('audio-active');
                });
            }
        }
    }

    function manuallyUnlockAudioPermissions() {
        const chime = document.getElementById('lowStockAlertChime');
        if(chime) {
            chime.volume = 1.0; 
            chime.play().then(() => {
                chime.pause(); chime.currentTime = 0;
                const btn = document.getElementById('audioPermissionBtn');
                btn.textContent = "🔊 Alerts Enabled";
                btn.classList.add('audio-active');
                btn.onclick = null;
            }).catch(err => console.error(err));
        }
    }

    function executeImmediateStockScan(isInitialLoad) {
        let matchingLowItems = [];
        let itemsNeedingNewAlerts = [];

        for (let i = 0; i < absoluteInventoryDataset.length; i++) {
            let item = absoluteInventoryDataset[i];
            let stock = parseInt(item.quantity);
            let limit = parseInt(item.alert_limit);

            if (stock <= limit) {
                matchingLowItems.push(item);
                if (!tracksPreviouslyAlertedIDs.includes(item.id)) {
                    itemsNeedingNewAlerts.push(item);
                }
            } else {
                tracksPreviouslyAlertedIDs = tracksPreviouslyAlertedIDs.filter(id => id !== item.id);
            }
        }

        if (matchingLowItems.length > 0) {
            if (isInitialLoad || itemsNeedingNewAlerts.length > 0) {
                let itemsToWarn = isInitialLoad ? matchingLowItems : itemsNeedingNewAlerts;
                triggerChromeNotificationAlert(itemsToWarn.length, itemsToWarn.map(item => item.name));
                buildAndOpenWarningListModal(matchingLowItems);

                itemsToWarn.forEach(item => {
                    if (!tracksPreviouslyAlertedIDs.includes(item.id)) tracksPreviouslyAlertedIDs.push(item.id);
                });
            }
            renderConsolidatedFloatingAlert();
        } else {
            removeConsolidatedFloatingAlert();
            if (titleFlasherInterval) {
                clearInterval(titleFlasherInterval);
                document.title = "Dashboard - Inventory Deck";
            }
        }
    }

    function triggerChromeNotificationAlert(count, itemsList) {
        const chime = document.getElementById('lowStockAlertChime');
        if (chime) {
            if (chimeTimeoutId) clearTimeout(chimeTimeoutId);
            chime.volume = 1.0; chime.loop = true; chime.currentTime = 0;
            
            chime.play().then(() => {
                const btn = document.getElementById('audioPermissionBtn');
                if(btn) {
                    btn.textContent = "🔊 Alerts Enabled";
                    btn.classList.add('audio-active');
                }
                chimeTimeoutId = setTimeout(() => {
                    chime.pause(); chime.currentTime = 0; chime.loop = false;
                    chimeTimeoutId = null;
                }, 5000);
            }).catch(err => {
                if ('speechSynthesis' in window) {
                    const speech = new SpeechSynthesisUtterance(`Low stock warning. ${count} items are critical.`);
                    speech.volume = 1.0; window.speechSynthesis.cancel(); window.speechSynthesis.speak(speech);
                }
            });
        }

        if (titleFlasherInterval) clearInterval(titleFlasherInterval);
        let isAlertState = false;
        titleFlasherInterval = setInterval(() => {
            if (document.getElementById('warningModal').style.display === 'none' && 
                document.getElementById('ignoredAlertsContainer').children.length === 0) {
                clearInterval(titleFlasherInterval);
                document.title = "Dashboard - Inventory Deck";
                return;
            }
            document.title = isAlertState ? "⚠️ LOW STOCK ALERT!" : "Dashboard - Inventory Deck";
            isAlertState = !isAlertState;
        }, 1000);
    }

    function processSaleReduction(productId, amountToReduce = 1) {
        let targetItem = absoluteInventoryDataset.find(item => item.id == productId);
        
        if (targetItem && targetItem.quantity >= amountToReduce) {
            targetItem.quantity -= amountToReduce;
            targetItem.items_sold += amountToReduce;

            const revenueDisplayBox = document.getElementById('totalRevenueDisplayNode');
            if (revenueDisplayBox) {
                let currentTotalRevenue = parseFloat(revenueDisplayBox.getAttribute('data-raw-revenue')) || 0;
                let addedRevenueValue = parseFloat(targetItem.price) * amountToReduce;
                let updatedTotalRevenue = currentTotalRevenue + addedRevenueValue;
                
                revenueDisplayBox.setAttribute('data-raw-revenue', updatedTotalRevenue);
                revenueDisplayBox.textContent = "₱" + updatedTotalRevenue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            const revenueLedgerTableBody = document.getElementById('revenueModalTableBody');
            const emptyPlaceholderRow = document.getElementById('revenueEmptyStatePlaceholderRow');
            if (emptyPlaceholderRow) { emptyPlaceholderRow.remove(); }
            
            if (revenueLedgerTableBody) {
                const currentRecordTime = new Date();
                const timeStringOptions = { month: 'short', day: '2-digit', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true };
                const formattedTimeLabel = currentRecordTime.toLocaleString('en-US', timeStringOptions).replace(',', ' -');
                let singleAddedRevenue = parseFloat(targetItem.price) * amountToReduce;

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td><strong>${targetItem.name}</strong></td>
                    <td><span style="color:#475569; font-size:13px;">${targetItem.category}</span></td>
                    <td>${amountToReduce} pcs</td>
                    <td><span class="revenue-tag">+₱${singleAddedRevenue.toFixed(2)}</span></td>
                    <td><span style="color:#0f172a; font-size:13px;">${formattedTimeLabel}</span></td>
                `;
                revenueLedgerTableBody.insertBefore(newRow, revenueLedgerTableBody.firstChild);
            }

            runtimeDatabaseAuditLogs.unshift({
                timestamp: new Date().toISOString(),
                name: targetItem.name,
                action: 'SELL',
                volume: amountToReduce
            });

            updateUIRowValues(productId, targetItem);
            populateAuditLogBookUI(); 

            if (navigator.onLine) {
                fetch(`dashboard_backend.php?custom_sell_id=${productId}&quantity_sold=${amountToReduce}`);
            } else {
                const transaction = db.transaction(["pending_sales"], "readwrite");
                transaction.objectStore("pending_sales").add({ id: productId, actionType: "SELL", volume: amountToReduce });
            }
        }
    }

    function processRestockIncrease(productId, amountToAdd = 1) {
        let targetItem = absoluteInventoryDataset.find(item => item.id == productId);
        
        if (targetItem) {
            targetItem.quantity = parseInt(targetItem.quantity) + amountToAdd;

            runtimeDatabaseAuditLogs.unshift({
                timestamp: new Date().toISOString(),
                name: targetItem.name,
                action: 'RESTOCK',
                volume: amountToAdd
            });

            updateUIRowValues(productId, targetItem);
            populateAuditLogBookUI(); 

            if (navigator.onLine) {
                fetch(`dashboard_backend.php?custom_restock_id=${productId}&quantity_restocked=${amountToAdd}`);
            } else {
                const transaction = db.transaction(["pending_sales"], "readwrite");
                transaction.objectStore("pending_sales").add({ id: productId, actionType: "RESTOCK", volume: amountToAdd });
            }
        }
    }

    function updateUIRowValues(productId, targetItem) {
        const mainCard = document.getElementById("card-product-" + productId);
        if (mainCard) {
            mainCard.querySelector('.stock-display-value').textContent = targetItem.quantity;
            if(mainCard.querySelector('.sold-display-value')) {
                mainCard.querySelector('.sold-display-value').textContent = targetItem.items_sold;
            }
            const dot = mainCard.querySelector('.status-dot');
            if(dot) {
                dot.className = targetItem.quantity <= targetItem.alert_limit ? "status-dot dot-low" : "status-dot dot-ok";
            }
        }
        executeImmediateStockScan(false);
    }

    function populateAuditLogBookUI() {
        const body = document.getElementById('auditLogBookTableBody');
        if (!body) return; body.innerHTML = "";

        if (runtimeDatabaseAuditLogs.length === 0) {
            body.innerHTML = `<tr><td colspan="4" style="text-align:center; color:#64748b;">No logs collected yet.</td></tr>`;
            return;
        }

        runtimeDatabaseAuditLogs.forEach(log => {
            const dateFormatted = new Date(log.timestamp).toLocaleString();
            let actionBadge = '';

            if(log.action === 'SELL') actionBadge = `<span class="log-action-badge badge-sale-action">Sold</span>`;
            else if(log.action === 'RESTOCK') actionBadge = `<span class="log-action-badge badge-restock-action">Restocked</span>`;
            else if(log.action === 'ADD') actionBadge = `<span class="log-action-badge" style="background:#dcfce7; color:#15803d;">Added</span>`;
            else if(log.action === 'EDIT') actionBadge = `<span class="log-action-badge" style="background:#fef9c3; color:#a16207;">Edited</span>`;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td><span style="color:#64748b; font-size:13px;">${dateFormatted}</span></td>
                <td><strong>${log.name}</strong></td>
                <td>${actionBadge}</td>
                <td>${log.volume !== 'N/A' ? log.volume + ' pcs' : 'N/A'}</td>
            `;
            body.appendChild(row);
        });
    }

    function openCategoryModeModal() {
        const container = document.getElementById('categoryModeContainer');
        container.innerHTML = "";

        let groupedData = {};
        absoluteInventoryDataset.forEach(product => {
            if (!groupedData[product.category]) groupedData[product.category] = [];
            groupedData[product.category].push(product);
        });

        if (Object.keys(groupedData).length === 0) {
            container.innerHTML = `<div style="text-align:center; color:#64748b; padding:20px;">No registered products found.</div>`;
            openPopupModal('categoryModePopup'); return;
        }

        Object.keys(groupedData).forEach(catName => {
            const section = document.createElement('div');
            section.className = "category-group-section";
            
            let itemsRowsHTML = "";
            groupedData[catName].forEach(p => {
                itemsRowsHTML += `
                    <tr>
                        <td><strong>${p.name}</strong></td>
                        <td>₱${parseFloat(p.price).toFixed(2)}</td>
                        <td>${p.quantity} units left</td>
                    </tr>`;
            });

            section.innerHTML = `
                <div class="category-group-header">${catName} (${groupedData[catName].length} items)</div>
                <table class="popup-table" style="margin: 0;">
                    <thead><tr><th>Product Name</th><th>Unit Price</th><th>Stock Count</th></tr></thead>
                    <tbody>${itemsRowsHTML}</tbody>
                </table>`;
            container.appendChild(section);
        });

        openPopupModal('categoryModePopup');
    }

    function buildAndOpenWarningListModal(itemsArray) {
        const listContainer = document.getElementById('lowStockItemsListContainer');
        if(!listContainer) return; listContainer.innerHTML = ""; 

        itemsArray.forEach(product => {
            const row = document.createElement('div');
            row.className = "low-stock-item-row";
            row.innerHTML = `
                <div class="low-stock-item-info">
                    <span class="low-stock-item-name">${product.name}</span>
                    <span class="low-stock-item-meta">Current Stock: ${product.quantity} / Limit: ${product.alert_limit}</span>
                </div>
                <button type="button" class="btn-warning-row-fix">Restock Now</button>`;

            row.querySelector('.btn-warning-row-fix').onclick = function() {
                closePopupModal('warningModal');
                let currentLive = absoluteInventoryDataset.find(item => item.id == product.id);
                showFocusModal(currentLive || product, "RESTOCK");
            };
            listContainer.appendChild(row);
        });

        document.getElementById('warningIgnoreBtn').onclick = function() { closePopupModal('warningModal'); };
        openPopupModal('warningModal');
    }

    function triggerSyncCycle() {
        if (!db) return;
        const transaction = db.transaction(["pending_sales"], "readwrite");
        const store = transaction.objectStore("pending_sales");
        store.openCursor().onsuccess = function(event) {
            const cursor = event.target.result;
            if (cursor) {
                const item = cursor.value;
                let endpoint = item.actionType === "RESTOCK" 
                    ? `dashboard_backend.php?custom_restock_id=${item.id}&quantity_restocked=${item.volume}`
                    : `dashboard_backend.php?custom_sell_id=${item.id}&quantity_sold=${item.volume}`;
                fetch(endpoint).then(() => { store.delete(cursor.key); });
                cursor.continue();
            }
        };
    }

    function handleFocusQuickSell(event) {
        event.preventDefault();
        if (!selectedProductRef || selectedProductRef.quantity <= 0) return;
        processSaleReduction(selectedProductRef.id, 1);
        updateFocusModalFields();
    }

    function handleFocusQuickRestock(event) {
        event.preventDefault();
        if (!selectedProductRef) return;
        processRestockIncrease(selectedProductRef.id, 1);
        updateFocusModalFields();
    }

    function updateFocusModalFields() {
        if(!selectedProductRef) return;
        let liveItem = absoluteInventoryDataset.find(item => item.id == selectedProductRef.id);
        if(liveItem) {
            const stockDisplay = document.getElementById('focusStock');
            if (stockDisplay) stockDisplay.textContent = liveItem.quantity;
        }
    }

    function openPopupModal(id) {
        if (id === 'recentActivityLogBookPopup') populateAuditLogBookUI();
        var el = document.getElementById(id); if (el) el.style.display = 'flex';
    }
    function closePopupModal(id) { var el = document.getElementById(id); if (el) el.style.display = 'none'; }
    function closePopupModalOnBackground(event, id) { if (event.target === document.getElementById(id)) closePopupModal(id); }

    function handleCardSelection(product) {
        let currentLiveItem = absoluteInventoryDataset.find(item => item.id == product.id);
        selectedProductRef = currentLiveItem || product;
        showFocusModal(selectedProductRef, "SELL"); 
    }

    function renderConsolidatedFloatingAlert() {
        const container = document.getElementById('ignoredAlertsContainer');
        if(!container) return;
        
        let currentLowItems = absoluteInventoryDataset.filter(item => parseInt(item.quantity) <= parseInt(item.alert_limit));
        if (currentLowItems.length === 0) { removeConsolidatedFloatingAlert(); return; }

        container.innerHTML = ''; 
        let alertCard = document.createElement('div');
        alertCard.className = "floating-danger-card";
        alertCard.innerHTML = `<div>Low Stock Alert: ${currentLowItems.length} items need attention</div>`;
        alertCard.onclick = function() { buildAndOpenWarningListModal(currentLowItems); };
        container.appendChild(alertCard);
    }

    function removeConsolidatedFloatingAlert() { 
        const container = document.getElementById('ignoredAlertsContainer');
        if (container) container.innerHTML = '';
    }

    function showFocusModal(product, designatedMode = "SELL") {
        selectedProductRef = product;
        currentItemUnitPrice = parseFloat(product.price);
        
        document.getElementById('focusTitle').textContent = product.name;
        document.getElementById('focusCategory').textContent = product.category;
        document.getElementById('focusImage').src = "uploads/" + product.image;
        document.getElementById('focusPrice').textContent = "₱" + currentItemUnitPrice.toFixed(2);
        document.getElementById('focusStock').textContent = product.quantity;
        document.getElementById('focus_quantity_input').value = 1;
        
        switchFocusFormConsoleMode(designatedMode);
        openPopupModal('focusModal');
    }

    function switchFocusFormConsoleMode(targetMode) {
        focusFormConsoleMode = targetMode;
        const sellIdInput = document.getElementById('focus_product_id_sell');
        const restockIdInput = document.getElementById('focus_product_id_restock');
        const qtyInput = document.getElementById('focus_quantity_input');
        const mainSubmitBtn = document.getElementById('mainConsoleActionButton');

        if (targetMode === "SELL") {
            sellIdInput.value = selectedProductRef.id; sellIdInput.disabled = false; restockIdInput.disabled = true;
            qtyInput.name = "quantity_sold";
            document.getElementById('tabSellModeBtn').classList.add('active-tab');
            document.getElementById('tabRestockModeBtn').classList.remove('active-tab');
            document.getElementById('inputFormBoxDynamicLabel').textContent = "How many items are you distributing?";
            document.getElementById('valuationDynamicTitle').textContent = "Gross Projected Income";
            mainSubmitBtn.textContent = "Confirm Sale Bundle"; mainSubmitBtn.style.background = "#ef4444";
        } else {
            restockIdInput.value = selectedProductRef.id; restockIdInput.disabled = false; sellIdInput.disabled = true;
            qtyInput.name = "quantity_restocked";
            document.getElementById('tabRestockModeBtn').classList.add('active-tab');
            document.getElementById('tabSellModeBtn').classList.remove('active-tab');
            document.getElementById('inputFormBoxDynamicLabel').textContent = "How many items are you adding to stock?";
            document.getElementById('valuationDynamicTitle').textContent = "Incoming Stock Volume";
            mainSubmitBtn.textContent = "Confirm Restock Bundle"; mainSubmitBtn.style.background = "#0284c7";
        }
        updateLivePrice();
    }

    function updateLivePrice() {
        var inputVal = parseInt(document.getElementById('focus_quantity_input').value) || 0;
        var displayEl = document.getElementById('bulkTotalDisplay');
        if (focusFormConsoleMode === "SELL") {
            displayEl.textContent = "₱" + (inputVal * currentItemUnitPrice).toFixed(2); displayEl.style.color = "#22c55e";
        } else {
            displayEl.textContent = "+" + inputVal + " Units"; displayEl.style.color = "#0284c7";
        }
    }

    var modal = document.getElementById('productModal');
    function openAddModal() {
        isEditingOrInteractingWithForm = true; 
        document.getElementById('modalTitle').textContent = "Add Product";
        document.getElementById('action_add_trigger').disabled = false;
        document.getElementById('action_edit_trigger').disabled = true;
        document.getElementById('qty_input_wrapper').style.display = "block";
        document.getElementById('form_product_id').value = "";
        document.getElementById('form_name').value = "";
        document.getElementById('form_category').value = "";
        document.getElementById('form_price').value = "";
        document.getElementById('form_quantity').value = "";
        document.getElementById('form_alert_limit').value = "";
        if (modal) modal.style.display = 'flex';
    }
    function openEditModal(product) {
        isEditingOrInteractingWithForm = true; 
        document.getElementById('modalTitle').textContent = "Edit Details";
        document.getElementById('action_add_trigger').disabled = true;
        document.getElementById('action_edit_trigger').disabled = false;
        document.getElementById('qty_input_wrapper').style.display = "none";
        document.getElementById('form_product_id').value = product.id;
        document.getElementById('form_name').value = product.name;
        document.getElementById('form_category').value = product.category;
        document.getElementById('form_price').value = product.price;
        document.getElementById('form_alert_limit').value = product.alert_limit;
        if (modal) modal.style.display = 'flex';
    }
    function closeModal() { 
        isEditingOrInteractingWithForm = false; 
        if (modal) modal.style.display = 'none'; 
    }
</script>
</body>
</html>