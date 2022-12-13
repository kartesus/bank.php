<?php
namespace app\api\presenters\accounts;

class DepositFundsPresenter
{
    public function missingRequiredFields($fields)
    {
        http_response_code(400);
        echo json_encode([
            'error' => 'Missing required fields',
            'data' => $fields,
        ]);
    }

    public function accountNotFound()
    {
        http_response_code(404);
        echo json_encode(['error' => 'Account not found']);
    }

    public function depositAccepted($account)
    {
        http_response_code(201);
        echo json_encode($account);
    }
}