<?php
namespace app\account_management\interactors;

class TransferFundsInteractor
{
    private $accountGateway;

    public function __construct($accountGateway)
    {
        $this->accountGateway = $accountGateway;
    }

    public function run($input, $output)
    {
        if (empty($input['source']) || empty($input['destination']) || empty($input['amount'])) {
            $output->missingRequiredFields(['source', 'destination', 'amount']);
            return;
        }

        $source = $this->accountGateway->get($input['source']);

        if (empty($source)) {
            $output->sourceAccountNotFound();
            return;
        }

        $destination = $this->accountGateway->get($input['destination']);
        if (empty($destination)) {
            $output->destinationAccountNotFound();
            return;
        }

        $this->accountGateway->acceptTransfer($input, new TransferResultVisitor($output));
    }
}

class TransferResultVisitor
{
    private $output;

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function transferRejected($reason)
    {

        if ($reason === 'insufficientFunds')
            $this->output->insufficientFunds();
        else
            $this->output->unknownError();
    }

    public function transferAccepted($tranfer)
    {
        $this->output->transferAccepted($tranfer);
    }
}