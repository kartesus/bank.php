<?php

namespace app\account_management\use_cases;

class WithdrawResultVisitor
{
    private $output;

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function withdrawRejected($reason)
    {
        if ($reason === 'insufficientFunds')
            $this->output->insufficientFunds();
        else
            $this->output->unknownError();
    }

    public function withdrawAccepted($account)
    {
        $this->output->withdrawAccepted($account);
    }
}

class WithdrawFunds
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

        $fee = intdiv($input['amount'], 100);
        $withdraw = ['fiscalNumber' => $input['fiscalNumber'], 'amount' => $input['amount'], 'fee' => $fee];
        $this->accountGateway->acceptWithdraw($withdraw, new WithdrawResultVisitor($output));
    }
}