<?php
use app\shared\Database;

class CreateAccount
{
    public function run()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['fiscalNumber']) || empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields', 'data' => ['fiscalNumber', 'name']]);
            return;
        }

        $db = new Database();
        $db->execute('INSERT INTO accounts (fiscalNumber, customerName) VALUES (?, ?)', [
            $data['fiscalNumber'],
            $data['name'],
        ]);
        $account = $db->queryRow('SELECT * FROM accounts WHERE fiscalNumber = :fiscalNumber', [
            'fiscalNumber' => $data['fiscalNumber'],
        ]);

        http_response_code(201);
        echo json_encode($account);
    }
}