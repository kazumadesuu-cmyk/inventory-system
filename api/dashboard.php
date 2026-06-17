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
            box-shadow: 4px 0 20px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 30px;
            z-index: 999;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            transition: background 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            align-self: center;
        }
        body.sidebar-open .nav-toggle-btn {
            align-self: flex-end;
            margin-right: 20px;
        }
        .nav-toggle-btn:hover { background: #e0f2fe; }
        .nav-toggle-btn svg { width: 22px; height: 22px; fill: #0284c7; }

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
            transition: all 0.2s ease;
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
            width: calc(100% - 40px);
        }
        body.sidebar-open .nav-item { width: calc(100% - 30px); }
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
            transition: opacity 0.2s ease;
        }
        body.sidebar-open .nav-label { opacity: 1; }

        .logout-btn { margin-top: auto; margin-bottom: 40px; border: 2px solid #ef4444; color: #ef4444; background: transparent; }
        .logout-btn:hover { background: #fef2f2; color: #ef4444; }

        /* --- METRIC COUNTERS OVERVIEW CARDS --- */
        .header-section { margin-bottom: 35px; text-align: left; width: 100%; }
        .header-section h1 { margin: 0; font-size: 32px; color: #1e293b; font-weight: 700; }
        .header-section p { margin: 5px 0 0 0; color: #64748b; font-size: 15px; }

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
            border: 1px solid rgba(255,255,255,0.6);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .metric-icon-box {
            padding: 15px; border-radius: 18px; display: flex; align-items: center; justify-content: center;
        }
        .blue-box { background: #e0f2fe; color: #0284c7; }
        .pink-box { background: #fce7f3; color: #db2777; }
        .green-box { background: #dcfce7; color: #16a34a; }
        
        .metric-icon-box svg { width: 28px; height: 28px; fill: currentColor; }
        .metric-info h3 { margin: 0; font-size: 14px; color: #64748b; font-weight: 400; text-transform: uppercase; letter-spacing: 0.5px; }
        .metric-info p { margin: 5px 0 0 0; font-size: 28px; font-weight: 700; color: #1e293b; }

        /* --- ACTIONS BAR & MAIN FLEX WORKSPACE --- */
        .actions-bar {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; width: 100%;
        }
        .search-wrapper { position: relative; width: 350px; }
        .search-wrapper input {
            width: 100%; padding: 14px 20px 14px 45px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.7);
            background: rgba(255,255,255,0.8); box-sizing: border-box; font-family: inherit; font-size: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02); transition: all 0.2s;
        }
        .search-wrapper input:focus { outline: none; border-color: #38bdf8; background: #fff; }
        .search-wrapper svg { position: absolute; left: 16px; top: 15px; width: 16px; height: 16px; fill: #94a3b8; }

        .add-product-btn {
            background: #0284c7; color: white; padding: 14px 24px; border: none; border-radius: 16px;
            font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 10px;
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.25); transition: all 0.2s; font-family: inherit;
        }
        .add-product-btn:hover { background: #0369a1; transform: translateY(-1px); }
        .add-product-btn svg { width: 16px; height: 16px; fill: white; }

        /* --- DECK CARDS INTERFACE VIEWLAYOUT --- */
        .workspace-layout {
            display: flex; gap: 30px; align-items: flex-start; width: 100%; margin-bottom: 50px;
        }
        .cards-container { 
            flex: 1; display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; 
        }
        .product-card { 
            background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9; display: flex; flex-direction: column; position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 15px 35px rgba(0,0,0,0.06); }
        
        .card-img-frame { width: 100%; height: 180px; background: #f8fafc; overflow: hidden; position: relative; }
        .card-img-frame img { width: 100%; height: 100%; object-fit: cover; }
        
        .alert-badge {
            position: absolute; top: 15px; left: 15px; background: #ef4444; color: white;
            padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3); animation: pulseAlert 2s infinite;
        }
        @keyframes pulseAlert { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }

        .card-details { padding: 20px; display: flex; flex-direction: column; flex: 1; }
        .card-category { font-size: 11px; color: #0284c7; background: #f0f9ff; padding: 4px 10px; border-radius: 6px; display: inline-block; width: fit-content; font-weight: 700; margin-bottom: 8px; }
        .card-title { margin: 0 0 12px 0; font-size: 18px; color: #1e293b; font-weight: 700; }
        
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: #64748b; }
        .info-row span.val { font-weight: 700; color: #334155; }
        .info-row span.price-val { color: #10b981; font-size: 16px; font-weight: 700; }

        .card-actions-divider { height: 1px; background: #f1f5f9; margin: 15px 0; }
        .card-control-buttons { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
        
        .action-icon-btn {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 8px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: all 0.2s; color: #64748b;
        }
        .action-icon-btn:hover { background: #f1f5f9; color: #1e293b; }
        .action-icon-btn svg { width: 16px; height: 16px; fill: currentColor; }
        
        .delete-btn-card:hover { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }

        .counter-pill {
            display: flex; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-left: auto;
        }
        .counter-pill-btn {
            background: transparent; border: none; padding: 8px 12px; font-weight: 700; cursor: pointer; color: #64748b; transition: background 0.2s;
        }
        .counter-pill-btn:hover { background: #e2e8f0; color: #1e293b; }
        .counter-pill-val { padding: 0 4px; font-size: 13px; font-weight: 700; color: #1e293b; min-width: 24px; text-align: center; }

        /* --- SALES LOG ENGINE TIMELINE GRAPHICS --- */
        .timeline-sidebar {
            width: 380px; background: rgba(255, 255, 255, 0.85); border-radius: 24px; padding: 25px;
            box-shadow: 0 10px 25px rgba(2, 132, 199, 0.05); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.6); box-sizing: border-box;
        }
        .timeline-sidebar h2 { margin: 0 0 20px 0; font-size: 18px; color: #1e293b; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .timeline-sidebar h2 svg { width: 18px; height: 18px; fill: #0284c7; }
        
        .timeline-track {
            display: flex; flex-direction: column; gap: 20px; max-height: 520px; overflow-y: auto; padding-right: 5px;
        }
        .timeline-track::-webkit-scrollbar { width: 4px; }
        .timeline-track::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        
        .timeline-node { display: flex; gap: 15px; position: relative; }
        .timeline-node::before {
            content: ''; position: absolute; left: 15px; top: 32px; bottom: -24px; width: 2px; background: #e2e8f0;
        }
        .timeline-node:last-child::before { display: none; }
        
        .node-icon-status {
            width: 32px; height: 32px; border-radius: 50%; background: #dcfce7; color: #16a34a;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0; z-index: 2;
        }
        .node-icon-status svg { width: 14px; height: 14px; fill: currentColor; }
        
        .node-content-box { flex: 1; background: white; padding: 12px 16px; border-radius: 14px; box-shadow: 0 4px 10px rgba(0,0,0,0.01); border: 1px solid #f1f5f9; }
        .node-meta { display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; margin-bottom: 4px; }
        .node-item-name { margin: 0 0 2px 0; font-size: 13px; color: #1e293b; font-weight: 700; }
        .node-summary-details { margin: 0; font-size: 12px; color: #64748b; }
        .node-summary-details span.green-rev { color: #16a34a; font-weight: 700; }

        /* --- SYSTEM CONTROL MODAL DIALOGS --- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.3);
            backdrop-filter: blur(4px); display: none; justify-content: center; align-items: center; z-index: 1000;
        }
        .modal-body-container {
            background: white; border-radius: 24px; width: 480px; padding: 35px; box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes modalPop { 0% { transform: scale(0.9); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        
        .modal-title-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .modal-title-row h3 { margin: 0; font-size: 20px; color: #1e293b; font-weight: 700; }
        .close-modal-x { background: #f1f5f9; border: none; border-radius: 50%; width: 32px; height: 32px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #64748b; }
        .close-modal-x:hover { background: #e2e8f0; color: #1e293b; }

        .form-input-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 6px; }
        .form-input-group label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; }
        .form-input-group input, .form-input-group select {
            padding: 12px 16px; border-radius: 12px; border: 1px solid #cbd5e1; font-family: inherit; font-size: 14px; color: #1e293b;
        }
        .form-input-group input:focus, .form-input-group select:focus { outline: none; border-color: #0284c7; }
        
        .form-row-split { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        .modal-footer-action-buttons { display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; }
        .modal-btn-cancel { background: #f1f5f9; color: #64748b; padding: 12px 20px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; font-family: inherit; font-size: 14px; }
        .modal-btn-cancel:hover { background: #e2e8f0; }
        .modal-btn-confirm { background: #0284c7; color: white; padding: 12px 24px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; font-family: inherit; font-size: 14px; }
        .modal-btn-confirm:hover { background: #0369a1; }

        /* Custom Dropdown Dialog / Stock Warning Indicator CSS */
        .batch-restock-panel {
            background: #fef2f2; border: 1px solid #fee2e2; border-radius: 20px; padding: 20px; margin-bottom: 35px; width: 100%; box-sizing: border-box; display: flex; flex-direction: column; gap: 15px;
        }
        .batch-restock-header { display: flex; align-items: center; gap: 10px; color: #991b1b; font-weight: 700; font-size: 15px; }
        .batch-restock-header svg { width: 20px; height: 20px; fill: currentColor; }
        .batch-restock-list { display: flex; flex-direction: column; gap: 10px; }
        .batch-restock-row { display: flex; justify-content: space-between; align-items: center; background: white; padding: 10px 15px; border-radius: 12px; border: 1px solid #fca5a5; }
        .batch-item-meta { font-size: 13px; color: #1e293b; font-weight: 700; }
        .batch-item-meta span.warn-count { color: #dc2626; font-weight: 700; }
        .batch-input-box { width: 70px; padding: 6px; border-radius: 8px; border: 1px solid #cbd5e1; text-align: center; font-family: inherit; font-size: 13px; }
        .batch-submit-trigger { background: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: bold; cursor: pointer; font-family: inherit; font-size: 13px; align-self: flex-end; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2); }
        .batch-submit-trigger:hover { background: #b91c1c; }
    </style>
</head>
<body>

    <div class="sidebar-nav">
        <button class="nav-toggle-btn" onclick="toggleSidebarLayout()">
            <svg viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
        </button>
        <div class="sidebar-links">
            <a href="dashboard.php" class="nav-item active">
                <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                <span class="nav-label">Main Workspace</span>
            </a>
            <a href="logout.php" class="nav-item logout-btn">
                <svg viewBox="0 0 24 24"><path d="M13 3h-2v10h2V3zm4.41 2.19l-1.42 1.42C17.99 7.86 19 9.81 19 12c0 3.87-3.13 7-7 7s-7-3.13-7-7c0-2.19 1.01-4.14 2.01-5.38L5.59 5.19C3.99 6.93 3 9.35 3 12c0 4.97 4.03 9 9 9s9-4.03 9-9c0-2.65-1.01-5.07-2.59-6.81z"/></svg>
                <span class="nav-label">Log Out</span>
            </a>
        </div>
    </div>

    <div class="dashboard-wrapper">
        <div class="header-section">
            <h1>Workspace Terminal</h1>
            <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>! Monitor, catalog, and fulfill item orders.</p>
        </div>

        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon-box blue-box">
                    <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z"/></svg>
                </div>
                <div class="metric-info">
                    <h3>Unique SKUs</h3>
                    <p><?php echo count($products_array); ?></p>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon-box pink-box">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 12 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
                <div class="metric-info">
                    <h3>Total In-Stock Items</h3>
                    <p><?php echo $total_products; ?></p>
                </div>
            </div>
            <div class="metric-card">
                <div class="metric-icon-box green-box">
                    <svg viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                </div>
                <div class="metric-info">
                    <h3>Accumulated Gross revenue</h3>
                    <p>₱<?php echo number_style_render($total_revenue); ?></p>
                </div>
            </div>
        </div>

        <?php
        $critical_shortage_items = [];
        foreach ($products_array as $p) {
            if (intval($p['quantity']) <= intval($p['alert_limit'])) {
                $critical_shortage_items[] = $p;
            }
        }
        if (!empty($critical_shortage_items)):
        ?>
        <form method="POST" action="dashboard_backend.php" class="batch-restock-panel">
            <div class="batch-restock-header">
                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                <span>Stock Warning Alert: Certain catalog records are dropping beneath target safety counts.</span>
            </div>
            <div class="batch-restock-list">
                <?php foreach ($critical_shortage_items as $warn_row): ?>
                <div class="batch-restock-row">
                    <div class="batch-item-meta">
                        <?php echo htmlspecialchars($warn_row['name']); ?> 
                        (<span class="warn-count"><?php echo $warn_row['quantity']; ?> remaining</span> / Limit: <?php echo $warn_row['alert_limit']; ?>)
                    </div>
                    <input type="number" name="restock_amounts[<?php echo $warn_row['id']; ?>]" class="batch-input-box" min="0" placeholder="+ Qty">
                </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" name="batch_restock_submit" class="batch-submit-trigger">Process Bulk Restock Order</button>
        </form>
        <?php endif; ?>

        <div class="actions-bar">
            <div class="search-wrapper">
                <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <input type="text" id="catalog-search" placeholder="Filter through names or labels..." onkeyup="filterCatalogSearch()">
            </div>
            <button class="add-product-btn" onclick="openAddProductModal()">
                <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                Register New Stock Item
            </button>
        </div>

        <div class="workspace-layout">
            
            <div class="cards-container" id="catalog-grid">
                <?php if (empty($products_array)): ?>
                    <div style="grid-column: 1/-1; background: white; padding: 40px; border-radius:24px; text-align:center; color:#94a3b8; font-weight:700;">
                        No items tracked yet. Click "Register New Stock Item" above to create records.
                    </div>
                <?php else: ?>
                    <?php foreach ($products_array as $row): 
                        $is_low = intval($row['quantity']) <= intval($row['alert_limit']);
                    ?>
                    <div class="product-card" data-title="<?php echo strtolower(htmlspecialchars($row['name'])); ?>" data-category="<?php echo strtolower(htmlspecialchars($row['category'])); ?>">
                        <?php if($is_low): ?>
                            <div class="alert-badge">Critical Count</div>
                        <?php endif; ?>
                        
                        <div class="card-img-frame">
                            <img src="uploads/<?php echo htmlspecialchars($row['image'] ?: 'default.png'); ?>" alt="Product image">
                        </div>
                        
                        <div class="card-details">
                            <span class="card-category"><?php echo htmlspecialchars($row['category']); ?></span>
                            <h4 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h4>
                            
                            <div class="info-row">
                                <span>Unit Price</span>
                                <span class="price-val">₱<?php echo number_style_render($row['price']); ?></span>
                            </div>
                            <div class="info-row">
                                <span>Alert Safety Threshold</span>
                                <span class="val"><?php echo $row['alert_limit']; ?> units</span>
                            </div>
                            <div class="info-row">
                                <span>Items Fullfilled (Lifetime)</span>
                                <span class="val" style="color: #64748b; font-weight:bold;"><?php echo $row['items_sold'] ?? 0; ?> sold</span>
                            </div>
                            
                            <div class="card-actions-divider"></div>
                            
                            <div class="card-control-buttons">
                                <button class="action-icon-btn" title="Edit Metadata Profile" onclick='openEditModal(<?php echo json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                    <svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                </button>
                                
                                <button class="action-icon-btn delete-btn-card" title="Erase Record Permanently" onclick="confirmPurgeRecord('<?php echo $row['id']; ?>')">
                                    <svg viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                </button>
                                
                                <div class="counter-pill">
                                    <button class="counter-pill-btn" title="Fulfill Custom Order / Deduct Bundle" onclick="triggerCustomPopInteraction('sell', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>')">-</button>
                                    <span class="counter-pill-val" title="Current Stock Balance"><?php echo $row['quantity']; ?></span>
                                    <button class="counter-pill-btn" title="Log Quick Bundle Influx / Restock" onclick="triggerCustomPopInteraction('restock', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>')">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="timeline-sidebar">
                <h2>
                    <svg viewBox="0 0 24 24"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                    Fulfillment Activity
                </h2>
                <div class="timeline-track">
                    <?php if (empty($sales_history_array)): ?>
                        <p style="font-size:12px; color:#94a3b8; text-align:center; padding: 20px;">No transaction activities captured yet.</p>
                    <?php else: ?>
                        <?php foreach ($sales_history_array as $h_row): ?>
                        <div class="timeline-node">
                            <div class="node-icon-status">
                                <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            </div>
                            <div class="node-content-box">
                                <div class="node-meta">
                                    <span><?php echo htmlspecialchars($h_row['category'] ?? 'General'); ?></span>
                                    <span><?php echo format_time_ago($h_row['sold_at'] ?? ''); ?></span>
                                </div>
                                <h5 class="node-item-name"><?php echo htmlspecialchars($h_row['product_name']); ?></h5>
                                <p class="node-summary-details">
                                    Dispatched: <strong><?php echo $h_row['quantity_sold']; ?> units</strong> <br>
                                    Yielded: <span class="green-rev">+₱<?php echo number_style_render(floatval($h_row['price_sold'] ?? 0) * intval($h_row['quantity_sold'] ?? 0)); ?></span>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <div class="modal-overlay" id="productModal">
        <div class="modal-body-container">
            <div class="modal-title-row">
                <h3 id="modalTitle">Register Stock Profile</h3>
                <button class="close-modal-x" onclick="closeModal()">✕</button>
            </div>
            
            <form id="modalForm" method="POST" action="dashboard_backend.php" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="form_product_id">
                
                <div class="form-input-group">
                    <label>Item Display Name</label>
                    <input type="text" name="name" id="form_name" placeholder="e.g. Sourdough Loaf Bread" required>
                </div>
                
                <div class="form-input-group">
                    <label>Storage Category / Tag</label>
                    <select name="category" id="form_category" required>
                        <option value="Pastry / Pastries">Pastry / Pastries</option>
                        <option value="Artisan Crafts">Artisan Crafts</option>
                        <option value="Coffee Brews">Coffee Brews</option>
                        <option value="Apparels & Garments">Apparels & Garments</option>
                        <option value="Cosmetics & Skincare">Cosmetics & Skincare</option>
                    </select>
                </div>
                
                <div class="form-row-split">
                    <div class="form-input-group">
                        <label>Unit Selling Price (₱)</label>
                        <input type="number" step="0.01" name="price" id="form_price" placeholder="0.00" required>
                    </div>
                    <div class="form-input-group" id="qty_input_wrapper">
                        <label>Starting Volume Count</label>
                        <input type="number" name="quantity" id="form_quantity" placeholder="0">
                    </div>
                </div>
                
                <div class="form-input-group">
                    <label>Safety Threshold Limit (Low Alert)</label>
                    <input type="number" name="alert_limit" id="form_alert_limit" placeholder="Notify when inventory falls to this count" required>
                </div>
                
                <div class="form-input-group">
                    <label>Product Display Image Banner</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                
                <div class="modal-footer-action-buttons">
                    <button type="button" class="modal-btn-cancel" onclick="closeModal()">Discard</button>
                    <button type="submit" name="add_product" id="action_add_trigger" class="modal-btn-confirm">Save Profile</button>
                    <button type="submit" name="edit_product" id="action_edit_trigger" class="modal-btn-confirm" style="display:none;">Apply Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const modal = document.getElementById('productModal');
    let isEditingOrInteractingWithForm = false; 

    // Handle Sliding Side Navigation Responsive States
    function toggleSidebarLayout() {
        document.body.classList.toggle('sidebar-open');
        localStorage.setItem('deckSidebarState', document.body.classList.contains('sidebar-open') ? 'open' : 'closed');
    }
    
    window.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('deckSidebarState') === 'open') {
            document.body.classList.add('sidebar-open');
        }
    });

    // Real-time Text Filter Search Feature
    function filterCatalogSearch() {
        const query = document.getElementById('catalog-search').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.product-card');
        
        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            const cat = card.getAttribute('data-category') || '';
            if (title.includes(query) || cat.includes(query)) {
                card.style.display = "flex";
            } else {
                card.style.display = "none";
            }
        });
    }

    // Modal Visibility Switches
    function openAddProductModal() {
        isEditingOrInteractingWithForm = true; 
        document.getElementById('modalTitle').textContent = "Register Stock Profile";
        document.getElementById('action_add_trigger').style.display = 'block';
        document.getElementById('action_add_trigger').disabled = false;
        document.getElementById('action_edit_trigger').style.display = 'none';
        document.getElementById('qty_input_wrapper').style.display = "block";
        
        // Wipe existing form cache fields
        document.getElementById('form_product_id').value = "";
        document.getElementById('form_name').value = "";
        document.getElementById('form_price').value = "";
        document.getElementById('form_quantity').value = "";
        document.getElementById('form_alert_limit').value = "";
        
        if (modal) modal.style.display = 'flex';
    }

    function openEditModal(product) {
        isEditingOrInteractingWithForm = true; 
        document.getElementById('modalTitle').textContent = "Edit Product Information";
        document.getElementById('action_add_trigger').style.display = 'none';
        document.getElementById('action_edit_trigger').style.display = 'block';
        document.getElementById('action_edit_trigger').disabled = false;
        document.getElementById('qty_input_wrapper').style.display = "none"; // Core balance edited via custom pill functions
        
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

    // Process Pill Counters Interaction (Prompting for custom numbers)
    function triggerCustomPopInteraction(type, id, name) {
        if (type === 'sell') {
            let qty = prompt(`How many units of "${name}" were dispatched/sold?`, "1");
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

<?php
// HELPER FORMATTING MATHEMATICS FUNCTIONS
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
    
    return date('M d, g:i A', $time);
}
?>