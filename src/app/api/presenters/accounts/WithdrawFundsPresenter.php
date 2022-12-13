<?php
namespace app\api\presenters\accounts;

class WithdrawFundsPresenter
{
    public function insufficientFunds()
    {
        http_response_code(400);
        echo json_encode(['error' => 'Insufficient funds']);
    }

    public function unknownError()
    {
        http_response_code(500);
        echo json_encode(['error' => 'Unknown error']);
    }

    public function withdrawAccepted($account)
    {
        http_response_code(201);
        echo json_encode($account);
    }
}