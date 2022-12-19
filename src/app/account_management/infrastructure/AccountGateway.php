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

    public function acceptWithdraw($data, $result)
    {
        $db = new Database();
        $account = $this->get($data['fiscalNumber']);
        $db->beginTransaction();
        $db->execute('INSERT INTO transactions (accountId, amount, operation) VALUES (?, ?, "withdraw")', [$account['id'], $data['amount']]);
        $db->execute('INSERT INTO transactions (accountId, amount, operation) VALUES (?, ?, "fee")', [$account['id'], $data['fee']]);
        $db->execute('UPDATE accounts SET balance = balance - ? - ? WHERE id = ?', [$data['amount'], $data['fee'], $account['id']]);

        $account = $this->get($data['fiscalNumber']);
        if ($account['balance'] < 0) {
            $db->rollback();
            $result->withdrawRejected('insufficientFunds');
            return;
        }

        $db->commit();
        $result->withdrawAccepted($account);
    }

    public function acceptTransfer($data, $result)
    {
        $db = new Database();
        $source = $this->get($data['source']);
        $destination = $this->get($data['destination']);
        $db->beginTransaction();
        $db->execute('INSERT INTO transactions (accountId, amount, operation, source) VALUES (?, ?, "transferOut", ?)', [$source['id'], $data['amount'], $destination['id']]);
        $db->execute('INSERT INTO transactions (accountId, amount, operation, source) VALUES (?, ?, "transferIn", ?)', [$destination['id'], $data['amount'], $source['id']]);
        $db->execute('UPDATE accounts SET balance = balance - ? WHERE id = ?', [$data['amount'], $source['id']]);
        $db->execute('UPDATE accounts SET balance = balance + ? WHERE id = ?', [$data['amount'], $destination['id']]);

        $source = $this->get($data['source']);
        if ($source['balance'] < 0) {
            $db->rollback();
            $result->transferRejected('insufficientFunds');
            return;
        }

        $db->commit();
        $result->transferAccepted($data);
    }
}