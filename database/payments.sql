CREATE TABLE payments (
    payment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT UNSIGNED NOT NULL,
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150) NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Approved', 'Refunded', 'Rejected') NOT NULL DEFAULT 'Pending',
    INDEX idx_payments_customer (customer_id),
    INDEX idx_payments_date (payment_date),
    INDEX idx_payments_status (status)
);
