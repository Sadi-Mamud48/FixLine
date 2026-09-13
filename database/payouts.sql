CREATE TABLE payouts (
    payout_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider_id INT UNSIGNED NOT NULL,
    provider_name VARCHAR(150) NOT NULL,
    job_id VARCHAR(50) NOT NULL,
    invoice_id VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payout_method VARCHAR(50) NOT NULL,
    payout_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Paid', 'Failed') NOT NULL DEFAULT 'Pending',
    INDEX idx_payouts_provider (provider_id),
    INDEX idx_payouts_date (payout_date),
    INDEX idx_payouts_status (status)
);
