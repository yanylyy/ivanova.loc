<?php

namespace src\controllers\api;

use src\models\User;
use src\exceptions\InvalidArgumentException;

class UsersApiController extends ApiController
{
    public function user()
    {
        if ($this->user !== null) {
            $this->view->displayJson([
                'user' => $this->user->getNickname(),
                'id' => $this->user->getId()
            ]);
        } else {
            $this->view->displayJson([
                'user' => 'No authenticated'
            ]);
        }
    }

    public function login()
    {
        $input = !empty($_POST) ? $_POST : ($this->getInputData() ?? []);

        try {
            $user = User::login($input);

            $this->view->displayJson([
                'user' => $user->getNickname(),
                'id' => $user->getId(),
                'token' => $user->getAuthToken()
            ]);
        } catch (InvalidArgumentException $e) {
            http_response_code(401);
            $this->view->displayJson([
                'user' => 'No authenticated',
                'error' => $e->getMessage()
            ]);
        }
    }
}
