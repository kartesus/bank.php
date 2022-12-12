<?php
namespace app\api\presenters\accounts;

class CreateAccountPresenter
{
    public function accountCreated($account)
    {
        http_response_code(201);
        echo json_encode($account);
    }

    public function missingRequiredFields($fields)
    {
        http_response_code(400);
        echo json_encode([
            'error' => 'Missing required fields',
            'data' => $fields,
        ]);
    }
}