<?php
require_once __DIR__ . '/shared/Database.php';
use app\shared\Database;

$db = Database::getInstance();
$db->exec('CREATE TABLE IF NOT EXISTS accounts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fiscalNumber VARCHAR(255) NOT NULL,
            customerName VARCHAR(255) NOT NULL,
            balance INTEGER NOT NULL DEFAULT 0,
            createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            accountId INTEGER NOT NULL,
            amount INTEGER NOT NULL,
            operation VARCHAR(255) NOT NULL,
            source INTEGER,
            createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (accountId) REFERENCES accounts(id)
        );
        CREATE INDEX IF NOT EXISTS transactions_accountId ON transactions (accountId);
        CREATE UNIQUE INDEX IF NOT EXISTS accounts_fiscalNumber ON accounts (fiscalNumber);');