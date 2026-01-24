-- Test Query untuk Verifikasi Order Management System
-- Jalankan di phpMyAdmin atau MySQL client

-- 1. Cek jumlah total orders (member + guest)
SELECT 
    'Member Orders' as type,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
FROM orders

UNION ALL

SELECT 
    'Guest Orders' as type,
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
FROM guest_orders;


-- 2. Gabungan semua orders dengan tipe (seperti yang dipakai di admin panel)
SELECT 
    o.id,
    o.order_number,
    o.total_amount,
    o.discount_amount,
    o.final_amount,
    o.status,
    o.payment_method,
    o.payment_status,
    o.created_at,
    u.username,
    u.email,
    u.full_name,
    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count,
    'member' as order_type
FROM orders o
JOIN users u ON o.user_id = u.id

UNION ALL

SELECT 
    go.id,
    go.order_number,
    go.total_amount,
    go.discount_amount,
    go.final_amount,
    go.status,
    go.payment_method,
    go.payment_status,
    go.created_at,
    go.guest_name as username,
    go.guest_email as email,
    go.guest_name as full_name,
    (SELECT COUNT(*) FROM guest_order_items WHERE guest_order_id = go.id) as item_count,
    'guest' as order_type
FROM guest_orders go
ORDER BY created_at DESC
LIMIT 20;


-- 3. Statistik detail per status
SELECT 
    'TOTAL' as category,
    'All Orders' as description,
    (SELECT COUNT(*) FROM orders) + (SELECT COUNT(*) FROM guest_orders) as count
    
UNION ALL

SELECT 
    'PENDING' as category,
    'Pending Orders' as description,
    (SELECT COUNT(*) FROM orders WHERE status = 'pending') + 
    (SELECT COUNT(*) FROM guest_orders WHERE status = 'pending') as count

UNION ALL

SELECT 
    'PROCESSING' as category,
    'Processing Orders' as description,
    (SELECT COUNT(*) FROM orders WHERE status = 'processing') + 
    (SELECT COUNT(*) FROM guest_orders WHERE status = 'processing') as count

UNION ALL

SELECT 
    'SHIPPED' as category,
    'Shipped Orders' as description,
    (SELECT COUNT(*) FROM orders WHERE status = 'shipped') + 
    (SELECT COUNT(*) FROM guest_orders WHERE status = 'shipped') as count

UNION ALL

SELECT 
    'COMPLETED' as category,
    'Completed Orders' as description,
    (SELECT COUNT(*) FROM orders WHERE status = 'completed') + 
    (SELECT COUNT(*) FROM guest_orders WHERE status = 'completed') as count

UNION ALL

SELECT 
    'CANCELLED' as category,
    'Cancelled Orders' as description,
    (SELECT COUNT(*) FROM orders WHERE status = 'cancelled') + 
    (SELECT COUNT(*) FROM guest_orders WHERE status = 'cancelled') as count;


-- 4. Top 10 customers (member + guest berdasarkan jumlah order)
SELECT 
    'Member' as customer_type,
    u.full_name as name,
    u.email,
    COUNT(o.id) as total_orders,
    SUM(o.final_amount) as total_spent
FROM orders o
JOIN users u ON o.user_id = u.id
GROUP BY o.user_id, u.full_name, u.email

UNION ALL

SELECT 
    'Guest' as customer_type,
    go.guest_name as name,
    go.guest_email as email,
    COUNT(go.id) as total_orders,
    SUM(go.final_amount) as total_spent
FROM guest_orders go
GROUP BY go.guest_email, go.guest_name
ORDER BY total_orders DESC, total_spent DESC
LIMIT 10;


-- 5. Revenue comparison (member vs guest)
SELECT 
    'Member Revenue' as category,
    COUNT(*) as order_count,
    SUM(total_amount) as gross_revenue,
    SUM(discount_amount) as total_discount,
    SUM(final_amount) as net_revenue,
    AVG(final_amount) as avg_order_value
FROM orders

UNION ALL

SELECT 
    'Guest Revenue' as category,
    COUNT(*) as order_count,
    SUM(total_amount) as gross_revenue,
    SUM(discount_amount) as total_discount,
    SUM(final_amount) as net_revenue,
    AVG(final_amount) as avg_order_value
FROM guest_orders

UNION ALL

SELECT 
    'TOTAL Revenue' as category,
    (SELECT COUNT(*) FROM orders) + (SELECT COUNT(*) FROM guest_orders) as order_count,
    (SELECT SUM(total_amount) FROM orders) + (SELECT SUM(total_amount) FROM guest_orders) as gross_revenue,
    (SELECT SUM(discount_amount) FROM orders) + (SELECT SUM(discount_amount) FROM guest_orders) as total_discount,
    (SELECT SUM(final_amount) FROM orders) + (SELECT SUM(final_amount) FROM guest_orders) as net_revenue,
    ((SELECT SUM(final_amount) FROM orders) + (SELECT SUM(final_amount) FROM guest_orders)) / 
    ((SELECT COUNT(*) FROM orders) + (SELECT COUNT(*) FROM guest_orders)) as avg_order_value;


-- 6. Recent orders (10 terakhir dari member dan guest)
SELECT 
    o.id,
    'MEMBER' as type,
    o.order_number,
    u.full_name as customer,
    u.email,
    o.final_amount,
    o.status,
    o.created_at
FROM orders o
JOIN users u ON o.user_id = u.id

UNION ALL

SELECT 
    go.id,
    'GUEST' as type,
    go.order_number,
    go.guest_name as customer,
    go.guest_email as email,
    go.final_amount,
    go.status,
    go.created_at
FROM guest_orders go
ORDER BY created_at DESC
LIMIT 10;


-- 7. Orders yang perlu diproses (pending orders dari kedua tipe)
SELECT 
    'MEMBER' as type,
    o.id,
    o.order_number,
    u.full_name as customer,
    u.email,
    o.final_amount,
    o.payment_method,
    o.created_at,
    TIMESTAMPDIFF(HOUR, o.created_at, NOW()) as hours_waiting
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.status = 'pending'

UNION ALL

SELECT 
    'GUEST' as type,
    go.id,
    go.order_number,
    go.guest_name as customer,
    go.guest_email as email,
    go.final_amount,
    go.payment_method,
    go.created_at,
    TIMESTAMPDIFF(HOUR, go.created_at, NOW()) as hours_waiting
FROM guest_orders go
WHERE go.status = 'pending'
ORDER BY created_at ASC;
