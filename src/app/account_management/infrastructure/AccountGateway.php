<?php
namespace app\account_management\infrastructure;

use app\shared\Database;

class AccountGateway
{
    public function create($data)
    {
        $db = new Database();
        $db->execute('INSERT INTO accounts (fiscalNumber, customerName) VALUES (?, ?)', [
            $data['fiscalNumber'],
            $data['name'],
        ]);
    }

    public function get($fiscalNumber)
    {
        $db = new Database();
        return $db->queryRow('SELECT * FROM accounts WHERE fiscalNumber = :fiscalNumber', [
            'fiscalNumber' => $fiscalNumber,
        ]);
    }

    public function acceptDeposit($data)
    {
        $db = new Database();
        $account = $this->get($data['fiscalNumber']);
        $db->beginTransaction();
        $db->execute('INSERT INTO transactions (accountId, amount, operation) VALUES (?, ?, "deposit")', [$account['id'], $data['amount']]);
        $db->execute('INSERT INTO transactions (accountId, amount, operation) VALUES (?, ?, "bonus")', [$account['id'], $data['bonus']]);
        $db->execute('UPDATE accounts SET balance = balance + ? + ? WHERE id = ?', [$data['amount'], $data['bonus'], $account['id']]);
        $db->commit();
    }
}