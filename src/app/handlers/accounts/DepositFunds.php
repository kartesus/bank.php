<?php
use app\shared\Database;

class DepositFunds
{
    public function run()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['fiscalNumber']) || empty($data['amount'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields', 'data' => ['fiscalNumber', 'amount']]);
            return;
        }

        $db = new Database();
        $account = $db->queryRow('SELECT * FROM accounts WHERE fiscalNumber = ?', [$data['fiscalNumber']]);

        if (empty($account)) {
            http_response_code(404);
            echo json_encode(['error' => 'Account not found']);
            return;
        }

        $bonus = intdiv($data['amount'], 200);

        $db->beginTransaction();
        $db->execute('INSERT INTO transactions (accountId, amount, operation) VALUES (?, ?, "deposit")', [$account['id'], $data['amount']]);
        $db->execute('INSERT INTO transactions (accountId, amount, operation) VALUES (?, ?, "bonus")', [$account['id'], $bonus]);
        $db->execute('UPDATE accounts SET balance = balance + ? + ? WHERE id = ?', [$data['amount'], $bonus, $account['id']]);
        $db->commit();

        http_response_code(201);
        $account = $db->queryRow('SELECT * FROM accounts WHERE fiscalNumber = ?', [$data['fiscalNumber']]);
        echo json_encode($account);
    }
}