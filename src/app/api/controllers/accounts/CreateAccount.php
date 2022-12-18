<?php
use app\shared\Context;
use app\api\presenters\accounts\CreateAccountPresenter;
use app\account_management\interactors\CreateAccountInteractor;


class CreateAccount
{
    public function run()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method not allowed';
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $accoutGateway = Context::getAccountGateway();
        $presenter = new CreateAccountPresenter();
        $interactor = new CreateAccountInteractor($accoutGateway);
        $interactor->run($data, $presenter);
    }
}