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
}