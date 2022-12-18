<?php
use app\shared\Database;

class AccountStatementDispatcher
{
    public function dispatch()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        $fiscalNumber = $_GET['fiscalNumber'] ?? null;

        if (empty($fiscalNumber)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields', 'data' => ['fiscalNumber']]);
            return;
        }

        $db = new Database();
        $account = $db->queryRow('SELECT * FROM accounts WHERE fiscalNumber = ?', [$fiscalNumber]);

        if (empty($account)) {
            http_response_code(404);
            echo json_encode(['error' => 'Account not found']);
            return;
        }

        $transactions = $db->query("SELECT transactions.*,
                (CASE
                    WHEN transactions.operation = 'transferIn' THEN
                        'Transfer from ' || (SELECT accounts.customerName FROM accounts WHERE accounts.id = transactions.source) 
                    WHEN transactions.operation = 'transferOut' THEN
                        'Transfer to ' || (SELECT accounts.customerName FROM accounts WHERE accounts.id = transactions.source)
                    ELSE transactions.operation
                END) AS description
        FROM transactions
        INNER JOIN accounts
            ON transactions.accountId = accounts.id
        WHERE accounts.id = ?", [$account['id']]);

        http_response_code(200);
        echo json_encode([
            'id' => $account['id'],
            'balance' => $account['balance'],
            'transactions' => array_map(function ($t) {
                return [
                    'amount' => $t['amount'],
                    'description' => ucfirst($t['description']),
                    'date' => $t['createdAt'],
                ];
            }, $transactions),
        ]);
    }
}