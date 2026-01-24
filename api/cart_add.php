<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$product_id = (int)$data['product_id'];
$quantity = (int)($data['quantity'] ?? 1);

// Check if product exists and has stock
$product = fetchOne("SELECT * FROM products WHERE id = ? AND status = 'active'", [$product_id]);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

if ($product['stock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
    exit;
}

// Member cart (database)
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    
    // Check if already in cart
    $existing = fetchOne("SELECT * FROM cart WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
    
    if ($existing) {
        execute("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?", 
              [$quantity, $user_id, $product_id]);
    } else {
        execute("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)", 
              [$user_id, $product_id, $quantity]);
    }
} else {
    // Guest cart (session)
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $found = false;
    for ($i = 0; $i < count($_SESSION['cart']); $i++) {
        if ($_SESSION['cart'][$i]['product_id'] == $product_id) {
            $_SESSION['cart'][$i]['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $product_id,
            'name' => $product['name'],
            'brand' => $product['brand'],
            'price' => $product['price'],
            'discount_price' => $product['discount_price'],
            'image' => $product['image'],
            'stock' => $product['stock'],
            'quantity' => $quantity
        ];
    }
}

echo json_encode(['success' => true, 'message' => 'Product added to cart']);
