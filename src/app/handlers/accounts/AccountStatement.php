<?php
use app\shared\Database;

class AccountStatement
{
    public function run()
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

        $transactions = $db->query('SELECT * FROM transactions WHERE accountId = ?', [$account['id']]);

        http_response_code(200);
        echo json_encode([
            'account' => $account,
            'transactions' => $transactions,
        ]);
    }
}