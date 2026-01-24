-- Add status column to categories table
-- Run this SQL in your phpMyAdmin or MySQL client

USE techhub_db;

-- Add status column to categories table
ALTER TABLE categories 
ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER description;

-- Update existing categories to have active status
UPDATE categories SET status = 'active' WHERE status IS NULL;

-- Show result
SELECT * FROM categories;
