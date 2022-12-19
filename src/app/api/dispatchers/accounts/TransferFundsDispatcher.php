<?php
use app\shared\Context;
use app\api\presenters\accounts\TransferFundsPresenter;
use app\account_management\interactors\TransferFundsInteractor;

class TransferFundsDispatcher
{
    public function dispatch()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $accountGateway = Context::getAccountGateway();
        $presenter = new TransferFundsPresenter();
        $interactor = new TransferFundsInteractor($accountGateway);
        $interactor->run($data, $presenter);
    }
}