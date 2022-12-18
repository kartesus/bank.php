<?php
use app\shared\Context;
use app\api\presenters\accounts\WithdrawFundsPresenter;
use app\account_management\interactors\WithdrawFundsInteractor;

class WithdrawFunds
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
        $presenter = new WithdrawFundsPresenter();
        $interactor = new WithdrawFundsInteractor($accountGateway);
        $interactor->run($data, $presenter);
    }
}