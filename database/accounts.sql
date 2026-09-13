CREATE TABLE accounts (
    account_id VARCHAR(50) PRIMARY KEY,
    account_type ENUM('Customer', 'Service Provider') NOT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    status ENUM('Active', 'Pending', 'Suspended') NOT NULL DEFAULT 'Pending',
    last_payment DECIMAL(10, 2) NULL,
    last_payout DECIMAL(10, 2) NULL,
    last_login DATETIME NULL,
    INDEX idx_accounts_type (account_type),
    INDEX idx_accounts_status (status)
);
