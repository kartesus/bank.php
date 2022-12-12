<?php
use app\shared\Database;

class TransferFunds
{
    public function run()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['origin']) || empty($data['destination']) || empty($data['amount'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields', 'data' => ['origin', 'destination', 'amount']]);
            return;
        }

        $db = new Database();
        $origin = $db->queryRow('SELECT * FROM accounts WHERE fiscalNumber = ?', [$data['origin']]);
        $destination = $db->queryRow('SELECT * FROM accounts WHERE fiscalNumber = ?', [$data['destination']]);

        if (empty($origin)) {
            http_response_code(404);
            echo json_encode(['error' => 'Origin account not found']);
            return;
        }

        if (empty($destination)) {
            http_response_code(404);
            echo json_encode(['error' => 'Destination account not found']);
            return;
        }

        if ($origin['id'] === $destination['id']) {
            http_response_code(400);
            echo json_encode(['error' => 'Origin and destination accounts must be different']);
            return;
        }

        if ($data['amount'] <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Amount must be greater than zero']);
            return;
        }

        $db->beginTransaction();
        $db->execute('INSERT INTO transactions (accountId, amount, operation, source) VALUES (?, ?, "transferOut", ?)', [$origin['id'], $data['amount'], $destination['id']]);
        $db->execute('INSERT INTO transactions (accountId, amount, operation, source) VALUES (?, ?, "transferIn", ?)', [$destination['id'], $data['amount'], $origin['id']]);
        $db->execute('UPDATE accounts SET balance = balance - ? WHERE id = ?', [$data['amount'], $origin['id']]);
        $account = $db->queryRow('SELECT balance FROM accounts WHERE id = ?', [$origin['id']]);
        if ($account['balance'] < 0) {
            $db->rollback();
            http_response_code(400);
            echo json_encode(['error' => 'Insufficient funds']);
            return;
        }
        $db->execute('UPDATE accounts SET balance = balance + ? WHERE id = ?', [$data['amount'], $destination['id']]);
        $db->commit();

        http_response_code(201);
    }
}