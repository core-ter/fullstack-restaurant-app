<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/middleware.php';

header('Content-Type: application/json');
requireAuth();

try {
    $period = $_GET['period'] ?? 'today';
    $db = getDBConnection();
    
    // Build date filter based on period
    $dateFilter = match($period) {
        'today' => "DATE(o.created_at) = CURDATE()",
        'week' => "YEARWEEK(o.created_at, 1) = YEARWEEK(NOW(), 1)",
        'month' => "MONTH(o.created_at) = MONTH(NOW()) AND YEAR(o.created_at) = YEAR(NOW())",
        'all' => "1=1",
        default => "DATE(o.created_at) = CURDATE()"
    };
    
    // 1. Total orders and revenue
    $statsQuery = "
        SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(total_amount), 0) as total_revenue,
            COALESCE(AVG(total_amount), 0) as avg_order_value,
            COUNT(CASE WHEN status_id IN (SELECT id FROM order_statuses WHERE status_code IN ('pending', 'confirmed', 'preparing')) THEN 1 END) as active_orders,
            COUNT(CASE WHEN status_id = (SELECT id FROM order_statuses WHERE status_code = 'delivered') THEN 1 END) as completed_orders
        FROM orders o
        WHERE {$dateFilter}
    ";
    
    $statsStmt = $db->query($statsQuery);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    // 2. Order status breakdown
    $statusQuery = "
        SELECT 
            os.display_name,
            os.color_hex,
            COUNT(o.id) as count
        FROM order_statuses os
        LEFT JOIN orders o ON o.status_id = os.id AND {$dateFilter}
        GROUP BY os.id, os.display_name, os.color_hex
        ORDER BY os.display_order
    ";
    
    $statusStmt = $db->query($statusQuery);
    $statusBreakdown = $statusStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Top 5 popular items
    $popularQuery = "
        SELECT 
            mi.name,
            SUM(oi.quantity) as total_quantity,
            COUNT(DISTINCT oi.order_id) as order_count
        FROM order_items oi
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        JOIN orders o ON oi.order_id = o.id
        WHERE {$dateFilter}
        GROUP BY mi.id, mi.name
        ORDER BY total_quantity DESC
        LIMIT 5
    ";
    
    $popularStmt = $db->query($popularQuery);
    $popularItems = $popularStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. Trend data (daily breakdown)
    $trendDays = match($period) {
        'today' => 1,
        'week' => 7,
        'month' => 30,
        'all' => 30,
        default => 7
    };
    
    $trendQuery = "
        SELECT 
            DATE(created_at) as date,
            COUNT(*) as order_count,
            COALESCE(SUM(total_amount), 0) as revenue
        FROM orders
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL {$trendDays} DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ";
    
    $trendStmt = $db->query($trendQuery);
    $trendData = $trendStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'period' => $period,
        'stats' => [
            'total_orders' => (int)$stats['total_orders'],
            'total_revenue' => (float)$stats['total_revenue'],
            'avg_order_value' => (float)$stats['avg_order_value'],
            'active_orders' => (int)$stats['active_orders'],
            'completed_orders' => (int)$stats['completed_orders']
        ],
        'status_breakdown' => $statusBreakdown,
        'popular_items' => $popularItems,
        'trend' => $trendData
    ]);
    
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Hiba: ' . $e->getMessage()
    ]);
}
