<?php
namespace app\account_management\use_cases;


class CreateAccount
{
    private $accountGateway;

    public function __construct($accountGateway)
    {
        $this->accountGateway = $accountGateway;
    }

    public function run($input, $presenter)
    {
        if (empty($input['fiscalNumber']) || empty($input['name'])) {
            $presenter->missingRequiredFields(['fiscalNumber', 'name']);
            return;
        }

        $this->accountGateway->create($input);
        $account = $this->accountGateway->get($input['fiscalNumber']);

        $presenter->accountCreated($account);
    }
}