<?php

namespace src\controllers\api;

use src\controllers\Controller;
use src\models\UsersAuthService;

class ApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->user = UsersAuthService::getUserByAuthBasic() ?? UsersAuthService::getUserByApiToken();
    }
}
