<?php
try {
    spl_autoload_register(function(string $className){
        require_once __DIR__.'/../'.str_replace('\\','/',$className.'.php');
    });

    $method = $_SERVER['REQUEST_METHOD'];
    $route = $_GET['route'] ?? '';
    $routes = require __DIR__ . '/../src/config/routes_api.php';

    $isRouteFound = false;
    foreach($routes as $pattern => $controllerAndAction){
        preg_match($pattern, $route, $matches);
        if(!empty($matches)){
            $isRouteFound = true;
            break;
        }
    }
    $controller = new \src\controllers\MainController();
    if(!$isRouteFound){
        throw new \src\exceptions\NotFoundException();
    }

    $controllerName = $controllerAndAction[0];
    unset($matches[0]);
    if(!isset($controllerAndAction[$method])){
        throw new \src\exceptions\InvalidMethodException('Method not support');
    }

    $actionName = $controllerAndAction[$method];

    $controller = new $controllerName;
    $controller->$actionName(...$matches);
} catch (\src\exceptions\DbException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} catch (\src\exceptions\NotFoundException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
} catch (\src\exceptions\UnauthorizedException $e) {
    header('Content-Type: application/json; charset=utf-8');
    header('WWW-Authenticate: Basic realm="API"');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
} catch (\src\exceptions\ForbiddenException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
} catch (\src\exceptions\InvalidMethodException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['error' => $e->getMessage()]);
}
