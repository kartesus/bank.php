<?php
namespace app\api\presenters\accounts;

class TransferFundsPresenter
{
    public function missingRequiredFields($fields)
    {
        http_response_code(400);
        echo json_encode([
            'error' => 'Missing required fields',
            'data' => $fields,
        ]);
    }

    public function sourceAccountNotFound()
    {
        http_response_code(404);
        echo json_encode(['error' => 'Source account not found']);
    }

    public function destinationAccountNotFound()
    {
        http_response_code(404);
        echo json_encode(['error' => 'Destination account not found']);
    }

    public function insufficientFunds()
    {
        http_response_code(400);
        echo json_encode(['error' => 'Insufficient funds']);
    }

    public function transferAccepted($transfer)
    {
        http_response_code(200);
        echo json_encode($transfer);
    }
}