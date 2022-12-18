<?php
use app\shared\Context;
use app\api\presenters\accounts\DepositFundsPresenter;
use app\account_management\interactors\DepositFundsInteractor;

class DepositFunds
{
    public function run()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $accountGateway = Context::getAccountGateway();
        $presenter = new DepositFundsPresenter();
        $interactor = new DepositFundsInteractor($accountGateway);
        $interactor->run($data, $presenter);
    }
}