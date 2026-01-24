<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../config/database.php';

$cart_count = 0;

// Get cart count based on user status
if (isLoggedIn() && isset($_SESSION['user_id'])) {
    // Member cart (database)
    $cart_result = fetchOne("SELECT COUNT(*) as count FROM cart WHERE user_id = ?", [$_SESSION['user_id']]);
    $cart_count = $cart_result['count'] ?? 0;
} elseif (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    // Guest cart (session)
    $cart_count = count($_SESSION['cart']);
}

echo json_encode(['success' => true, 'count' => $cart_count]);
