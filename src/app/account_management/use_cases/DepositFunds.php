<?php
namespace app\account_management\use_cases;

class DepositFunds
{
    private $accountGateway;

    public function __construct($accountGateway)
    {
        $this->accountGateway = $accountGateway;
    }

    public function run($input, $output)
    {
        if (empty($input['fiscalNumber']) || empty($input['amount'])) {
            $output->missingRequiredFields(['fiscalNumber', 'amount']);
            return;
        }

        $account = $this->accountGateway->get($input['fiscalNumber']);
        if (empty($account)) {
            $output->accountNotFound();
            return;
        }

        $bonus = intdiv($input['amount'], 200);
        $this->accountGateway->acceptDeposit([
            'fiscalNumber' => $input['fiscalNumber'],
            'amount' => $input['amount'],
            'bonus' => $bonus,
        ]);

        $account = $this->accountGateway->get($input['fiscalNumber']);
        $output->depositAccepted($account);
    }
}