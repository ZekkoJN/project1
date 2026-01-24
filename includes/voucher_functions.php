<?php
/**
 * Voucher & Member Benefits Functions
 */

/**
 * Generate welcome voucher untuk member baru
 */
function generateWelcomeVoucher($user_id, $user_email) {
    global $pdo;
    
    try {
        // Generate unique voucher code
        $voucher_code = 'WELCOME' . strtoupper(substr(md5($user_email . time()), 0, 8));
        
        // Create welcome voucher - discount 10%
        $sql = "INSERT INTO member_vouchers (user_id, voucher_code, voucher_type, amount, min_purchase, description, valid_until) 
                VALUES (?, ?, 'discount_percent', 10, 100000, 'Welcome Bonus - 10% Discount', DATE_ADD(NOW(), INTERVAL 30 DAY))";
        query($sql, [$user_id, $voucher_code]);
        
        // Initialize cashback balance
        $sql = "INSERT INTO cashback_balance (user_id, balance) VALUES (?, 0)";
        query($sql, [$user_id]);
        
        return true;
    } catch (Exception $e) {
        error_log("Error generating welcome voucher: " . $e->getMessage());
        return false;
    }
}

/**
 * Get active vouchers untuk user
 */
function getUserVouchers($user_id) {
    return fetchAll("SELECT * FROM member_vouchers 
                     WHERE user_id = ? AND is_used = FALSE AND valid_until >= NOW() 
                     ORDER BY created_at DESC", 
                     [$user_id]);
}

/**
 * Get member cashback balance
 */
function getCashbackBalance($user_id) {
    $result = fetchOne("SELECT balance FROM cashback_balance WHERE user_id = ?", [$user_id]);
    return $result ? $result['balance'] : 0;
}

/**
 * Apply voucher to order
 */
function applyVoucher($user_id, $voucher_code, $order_total) {
    global $pdo;
    
    try {
        $voucher = fetchOne("SELECT * FROM member_vouchers 
                            WHERE user_id = ? AND voucher_code = ? AND is_used = FALSE AND valid_until >= NOW()",
                            [$user_id, $voucher_code]);
        
        if (!$voucher) {
            return ['success' => false, 'message' => 'Voucher tidak ditemukan atau sudah kadaluarsa'];
        }
        
        if ($order_total < $voucher['min_purchase']) {
            return ['success' => false, 'message' => 'Minimum pembelian Rp' . number_format($voucher['min_purchase'], 0)];
        }
        
        // Calculate discount
        if ($voucher['voucher_type'] === 'discount_percent') {
            $discount = ($order_total * $voucher['amount']) / 100;
        } elseif ($voucher['voucher_type'] === 'discount_fixed') {
            $discount = $voucher['amount'];
        } else {
            $discount = 0;
        }
        
        // Mark as used
        query("UPDATE member_vouchers SET is_used = TRUE, used_date = NOW() WHERE id = ?", [$voucher['id']]);
        
        return [
            'success' => true,
            'discount' => $discount,
            'message' => 'Voucher berhasil digunakan'
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

/**
 * Apply cashback to order (member only)
 */
function applyCashback($user_id, $discount_amount) {
    global $pdo;
    
    try {
        // Add cashback to balance (2% dari discount amount)
        $cashback_earned = $discount_amount * 0.02;
        
        query("UPDATE cashback_balance 
               SET balance = balance + ?, total_earned = total_earned + ? 
               WHERE user_id = ?", 
              [$cashback_earned, $cashback_earned, $user_id]);
        
        return $cashback_earned;
    } catch (Exception $e) {
        error_log("Error applying cashback: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get member benefits summary
 */
function getMemberBenefits($user_id) {
    $active_vouchers = count(getUserVouchers($user_id));
    $cashback_balance = getCashbackBalance($user_id);
    
    $benefits = [
        'active_vouchers' => $active_vouchers,
        'cashback_balance' => $cashback_balance,
        'total_benefit' => ($active_vouchers * 50000) + $cashback_balance
    ];
    
    return $benefits;
}

/**
 * Generate guest order number
 */
function generateGuestOrderNumber() {
    return 'GUEST-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}
