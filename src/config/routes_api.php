<?php

return [
    '~^articles$~' => [
        \src\controllers\api\ArticlesApiController::class, 'GET' => 'all', 'POST' => 'add' ],
    '~^articles/(\d+)$~' => [
        \src\controllers\api\ArticlesApiController::class, 'GET' => 'view', 'PUT' => 'edit', 'PATCH' => 'edit', 'DELETE' => 'delete' ],
    '~^user$~' => [
        \src\controllers\api\UsersApiController::class, 'GET' => 'user' ],
    '~^user/login$~' => [  \src\controllers\api\UsersApiController::class, 'POST' => 'login' ],
];
