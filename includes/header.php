<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME . ' - Toko Elektronik Komputer & Laptop'; ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if (isset($custom_css)) echo $custom_css; ?>
    <style>
        /* Force dark mode if saved preference is dark */
        body.dark-mode {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }
        body.dark-mode .header {
            background-color: #2d2d2d !important;
        }
        body.dark-mode .navbar {
            background-color: #2d2d2d !important;
        }
    </style>
    <script>
        // Apply dark mode immediately before page renders if saved
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-mode');
                document.body.classList.add('dark-mode');
            }
        })();
    </script>
</head>
<body>
